<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mhcu_peringatan extends Admin
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_mhcu_peringatan');
		require FCPATH . '/vendor/autoload.php';
		define('PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('mhcu_peringatan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$filters = array(
			'status_alert' => $this->input->get('status'),
			'jenis_alert' => $this->input->get('jenis'),
		);

		$this->data['peringatans'] = $this->model_mhcu_peringatan->get($filter, $field, $this->limit_page, $offset, $filters);
		$this->data['peringatan_counts'] = $this->model_mhcu_peringatan->count_all($filter, $field, $filters);
		// Stat card dari seluruh data (bukan hasil paginate)
		$this->data['status_stats'] = $this->model_mhcu_peringatan->status_counts();

		$config = array(
			'base_url'     => 'administrator/mhcu_peringatan/index/',
			'total_rows'   => $this->model_mhcu_peringatan->count_all($filter, $field, $filters),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Peringatan MHCU');
		$this->render('backend/standart/administrator/mhcu_peringatan/mhcu_peringatan_list', $this->data);
	}

	public function update_status($id)
	{
		$this->is_allowed('mhcu_peringatan_update');

		$status_alert = $this->input->post('status_alert');
		$diproses_oleh = $this->input->post('diproses_oleh');

		if (empty($status_alert)) {
			echo json_encode(array('success' => false, 'message' => 'Status wajib diisi'));
			exit;
		}

		$save_data = array(
			'status_alert' => $status_alert,
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => isset($this->session->userdata['username']) ? $this->session->userdata['username'] : 'admin',
		);

		if (!empty($diproses_oleh)) {
			$save_data['diproses_oleh'] = $diproses_oleh;
		}

		// Keterangan manual admin (boleh dikosongkan)
		if ($this->input->post('keterangan') !== null) {
			$save_data['keterangan'] = $this->input->post('keterangan');
		}

		$save = $this->model_mhcu_peringatan->change($id, $save_data);

		if ($save) {
			echo json_encode(array('success' => true, 'message' => 'Status berhasil diupdate'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Gagal update'));
		}
	}

	/**
	 * Kirim reminder FCM (bubble notifikasi) ke peserta peringatan.
	 * Hanya untuk status selain 'finished'. Log ke list_notifikasi.
	 */
	public function send_reminder($id)
	{
		$this->is_allowed('mhcu_peringatan_update');

		$judul     = $this->input->post('judul');
		$deskripsi = $this->input->post('deskripsi');

		if (empty($judul) || empty($deskripsi)) {
			echo json_encode(array('success' => false, 'message' => 'Judul dan deskripsi wajib diisi'));
			exit;
		}

		$peringatan = $this->db
			->select('mhcu_peringatan.*, mhcu_sesi.npp, mhcu_sesi.presensi_role')
			->join('mhcu_sesi', 'mhcu_sesi.id_mhcu_sesi = mhcu_peringatan.id_sesi', 'LEFT')
			->where('mhcu_peringatan.id_peringatan', $id)
			->get('mhcu_peringatan')->row();

		if (empty($peringatan)) {
			echo json_encode(array('success' => false, 'message' => 'Data peringatan tidak ditemukan'));
			exit;
		}

		if ($peringatan->status_alert == 'finished') {
			echo json_encode(array('success' => false, 'message' => 'Tiket sudah Finished, tidak bisa dikirim reminder'));
			exit;
		}

		$resolve = $this->_resolve_device($peringatan->npp, $peringatan->presensi_role);
		if ($resolve === false) {
			echo json_encode(array('success' => false, 'message' => 'User / device_id tidak ditemukan untuk NPP ' . $peringatan->npp . ' (role ' . $peringatan->presensi_role . ')'));
			exit;
		}

		// Kirim FCM
		$fcm_result = $this->_firebase_send_notif($resolve['device_id'], $judul, $deskripsi);

		// Log ke list_notifikasi
		$this->db->insert('list_notifikasi', array(
			'title'        => $judul,
			'content'      => $deskripsi,
			'role'         => $resolve['table_used'],
			'user_id'      => $resolve['user_id'],
			'email'        => $resolve['email'],
			'nama_lengkap' => $resolve['nama_lengkap'],
			'action'       => json_encode(array(
				'fcm_response' => $fcm_result,
				'route'        => 'mhcu_peringatan/send_reminder'
			))
		));

		echo json_encode(array(
			'success' => true,
			'message' => 'Reminder terkirim ke ' . $resolve['nama_lengkap'],
			'data'    => array(
				'table_used' => $resolve['table_used'],
				'email'      => $resolve['email'],
				'fcm_result' => $fcm_result
			)
		));
	}

	/**
	 * Cari device_id user by npp lewat presensi_role -> presensi_setting_role.nama_tabel.
	 * Fallback untuk pimpinan: turun ke pimpinan lain -> guru -> pegawai (sama dengan apiapp/dummy_test/Send_notification).
	 * Return array(table_used, user_id, email, nama_lengkap, device_id) atau false.
	 */
	private function _resolve_device($npp, $presensi_role)
	{
		$role_info = $this->db->where('nama_role', $presensi_role)->get('presensi_setting_role')->row();
		if (empty($role_info) || empty($role_info->nama_tabel)) {
			return false;
		}

		$chain = array($role_info->nama_tabel);
		if (substr($role_info->nama_tabel, 0, 8) == 'pimpinan') {
			if ($role_info->nama_tabel == 'pimpinan_sma') {
				$chain = array('pimpinan_sma', 'pimpinan_smp', 'pimpinan_sd', 'guru_sma', 'guru_smp', 'guru_sd', 'pegawai');
			} else if ($role_info->nama_tabel == 'pimpinan_smp') {
				$chain = array('pimpinan_smp', 'pimpinan_sd', 'guru_sma', 'guru_smp', 'guru_sd', 'pegawai');
			} else {
				$chain = array('pimpinan_sd', 'guru_sma', 'guru_smp', 'guru_sd', 'pegawai');
			}
		}

		$id_cols = array(
			'guru_sd' => 'id_guru', 'guru_smp' => 'id_guru', 'guru_sma' => 'id_guru', 'guru_ft' => 'id_guru',
			'pegawai' => 'id_pegawai',
			'pimpinan_sd' => 'id_pimpinan', 'pimpinan_smp' => 'id_pimpinan', 'pimpinan_sma' => 'id_pimpinan',
			'pramubhakti' => 'id_pramubhakti', 'security' => 'id_security'
		);

		foreach ($chain as $table) {
			if (!isset($id_cols[$table])) {
				continue;
			}
			$user = $this->db->query(
				"select " . $id_cols[$table] . " as id_user, email_ms_office, nama_lengkap, device_id
				 from " . $table . "
				 where npp = ? and deleted_at is null
				   and device_id is not null and device_id != ''",
				array($npp)
			)->row();
			if (!empty($user)) {
				return array(
					'table_used'   => $table,
					'user_id'      => $user->id_user,
					'email'        => $user->email_ms_office,
					'nama_lengkap' => $user->nama_lengkap,
					'device_id'    => $user->device_id
				);
			}
		}
		return false;
	}

	private function _firebase_send_notif($fcm_id, $title, $body)
	{
		$client = new \Google_Client();
		$client->setAuthConfig(PRIVATE_FIREBASE_KEY);
		$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
		$client->refreshTokenWithAssertion();
		$token = $client->getAccessToken();
		$access_token = $token['access_token'];

		$headers = array(
			'Authorization: Bearer ' . $access_token,
			'Content-Type: application/json'
		);

		$fields = array(
			'message' => array(
				'token' => $fcm_id,
				'notification' => array(
					'title' => $title,
					'body'  => $body
				)
			)
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/labscib-app/messages:send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		curl_close($ch);

		return json_decode($result, true);
	}

	public function view_sesi($id_sesi)
	{
		$this->is_allowed('mhcu_peringatan_view');
		redirect('administrator/mhcu_sesi/view/' . $id_sesi);
	}

	public function get_detail($id)
	{
		if (!$this->is_allowed('mhcu_peringatan_view', false)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$peringatan = $this->db
			->select('mhcu_peringatan.*, mhcu_sesi.npp, mhcu_sesi.nama_lengkap, mhcu_sesi.presensi_role, pk.label_profil AS kategori_keseluruhan, pk.saran_kategori AS saran_rekomendasi,
				cb.id_jadwal AS care_id_jadwal, cj.tanggal AS care_tanggal, cj.sesi_ke AS care_sesi_ke,
				cj.jam_mulai AS care_jam_mulai, cj.jam_selesai AS care_jam_selesai, cj.nama_psikolog AS care_nama_psikolog, cb.status AS care_status')
			->join('mhcu_sesi', 'mhcu_sesi.id_mhcu_sesi = mhcu_peringatan.id_sesi', 'LEFT')
			->join('mhcu_hasil_individu', 'mhcu_hasil_individu.id_sesi = mhcu_peringatan.id_sesi', 'LEFT')
			->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT')
			->join("mhcu_care_booking cb", "cb.id_sesi = mhcu_peringatan.id_sesi AND cb.status IN ('scheduled','selesai')", 'LEFT')
			->join('mhcu_care_jadwal cj', 'cj.id_jadwal = cb.id_jadwal', 'LEFT')
			->where('mhcu_peringatan.id_peringatan', $id)
			->get('mhcu_peringatan')->row();

		if ($peringatan) {
			$peringatan->created_at_formatted = formatTanggal($peringatan->created_at) . ' ' . date('H:i', strtotime($peringatan->created_at));
			if (!empty($peringatan->updated_at)) {
				$peringatan->updated_at_formatted = formatTanggal($peringatan->updated_at) . ' ' . date('H:i', strtotime($peringatan->updated_at));
			}

			// Ambil foto peserta dari tabel presensi_setting_role
			$peringatan->foto_url = '';
			if (!empty($peringatan->presensi_role) && !empty($peringatan->npp)) {
				$role_info = $this->db->where('nama_role', $peringatan->presensi_role)->get('presensi_setting_role')->row();
				if (!empty($role_info->nama_tabel)) {
					$nama_tabel = $role_info->nama_tabel;
					// Cek tabel ada
					$table_check = $this->db->query("SHOW TABLES LIKE '" . $nama_tabel . "'")->row();
					if ($table_check) {
						$user_data = $this->db->where('npp', $peringatan->npp)->get($nama_tabel)->row();
						if (!empty($user_data->foto_profil)) {
							$peringatan->foto_url = base_url('uploads/' . $nama_tabel . '/' . $user_data->foto_profil);
						}
					}
				}
			}

			$this->output->set_content_type('application/json')->set_output(json_encode($peringatan));
		} else {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Data tidak ditemukan')));
		}
	}
	
}

/* End of file Mhcu_peringatan.php */
