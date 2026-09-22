<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MHCU Care — halaman publik tindak lanjut konseling psikologis.
 * Lokasi: application/controllers/Mhcu_care.php (root, MY_Controller).
 * Pattern: Mhcu_tracker / Forget_password (halaman publik tanpa login manual).
 *
 * Auto-login: token deterministik dari id_sesi (tanpa tabel token).
 *   token = md5(id_sesi . MHCU_CARE_TOKEN_SALT)
 * URL: mhcu_care?token=<md5>&id_sesi=<id_mhcu_sesi>
 *
 * Alur peserta: Step 1 persetujuan konseling -> Step 2 consent to release
 * information -> Step 3 pilih slot jadwal -> konfirmasi -> submit.
 * Submit: insert mhcu_care_booking (uq id_jadwal = 1 slot 1 peserta)
 * + update mhcu_peringatan.status_alert = 'scheduled'.
 */
class Mhcu_care extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Halaman utama. State final ditentukan di sini: error / info (sudah jadwal) / flow.
	 */
	public function index()
	{
		$token   = (string) $this->input->get('token', true);
		$id_sesi = intval($this->input->get('id_sesi', true));

		$ctx = $this->_resolve($token, $id_sesi);
		if ($ctx['error'] !== '') {
			$this->_render_state('error', array('error_message' => $ctx['error']));
			return;
		}

		$data = $ctx['data'];
		if ($data['already_booked']) {
			$this->_render_state('info', $data);
			return;
		}
		$this->_render_state('flow', $data);
	}

	/**
	 * GET slots — JSON daftar slot + status terisi (refresh tanpa reload).
	 */
	public function slots()
	{
		$token   = (string) $this->input->get('token', true);
		$id_sesi = intval($this->input->get('id_sesi', true));

		$ctx = $this->_resolve($token, $id_sesi);
		header('Content-Type: application/json');
		if ($ctx['error'] !== '') {
			echo json_encode(array('status' => 0, 'message' => $ctx['error']));
			return;
		}
		echo json_encode(array(
			'status' => 1,
			'data'   => array('jadwal' => $ctx['data']['jadwal']),
		));
	}

	/**
	 * POST submit — final booking.
	 * Body: token, id_sesi, id_jadwal.
	 * Transaksi + SELECT ... FOR UPDATE + UNIQUE(id_jadwal) sebagai race guard.
	 */
	public function submit()
	{
		header('Content-Type: application/json');

		$token    = (string) $this->input->post('token', true);
		$id_sesi  = intval($this->input->post('id_sesi', true));
		$id_jadwal = intval($this->input->post('id_jadwal', true));

		if ($id_jadwal <= 0) {
			echo json_encode(array('status' => 0, 'message' => 'Slot jadwal belum dipilih.'));
			return;
		}

		$ctx = $this->_resolve($token, $id_sesi);
		if ($ctx['error'] !== '') {
			echo json_encode(array('status' => 0, 'message' => $ctx['error']));
			return;
		}
		$data = $ctx['data'];
		if ($data['already_booked']) {
			echo json_encode(array('status' => 0, 'message' => 'Anda sudah menjadwalkan sesi.'));
			return;
		}

		$this->db->trans_begin();

		// Lock slot
		$jadwal = $this->db->query(
			"SELECT * FROM mhcu_care_jadwal WHERE id_jadwal = ? FOR UPDATE",
			array($id_jadwal)
		)->row();
		if (empty($jadwal)) {
			$this->db->trans_rollback();
			echo json_encode(array('status' => 0, 'message' => 'Slot jadwal tidak ditemukan.'));
			return;
		}

		// Slot sudah diambil peserta lain?
		$taken = $this->db->query(
			"SELECT id_care FROM mhcu_care_booking WHERE id_jadwal = ? AND status IN ('scheduled','selesai') FOR UPDATE",
			array($id_jadwal)
		)->row();
		if (!empty($taken)) {
			$this->db->trans_rollback();
			echo json_encode(array('status' => 0, 'message' => 'Maaf, slot tersebut baru saja terisi. Silakan pilih slot lain.'));
			return;
		}

		// Peserta ini sudah punya booking aktif?
		$mine = $this->db->query(
			"SELECT id_care FROM mhcu_care_booking WHERE id_sesi = ? AND status IN ('scheduled','selesai') FOR UPDATE",
			array($id_sesi)
		)->row();
		if (!empty($mine)) {
			$this->db->trans_rollback();
			echo json_encode(array('status' => 0, 'message' => 'Anda sudah menjadwalkan sesi.'));
			return;
		}

		$this->db->insert('mhcu_care_booking', array(
			'id_sesi'       => $id_sesi,
			'id_peringatan' => $data['peringatan']->id_peringatan,
			'id_jadwal'     => $id_jadwal,
			'status'        => 'scheduled',
			'created_at'    => date('Y-m-d H:i:s'),
		));

		$this->db->set('status_alert', 'scheduled')
			->set('updated_at', date('Y-m-d H:i:s'))
			->set('updated_by', 'mhcu_care')
			->where('id_peringatan', $data['peringatan']->id_peringatan)
			->update('mhcu_peringatan');

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			echo json_encode(array('status' => 0, 'message' => 'Gagal menyimpan. Silakan coba lagi.'));
			return;
		}
		$this->db->trans_commit();

		echo json_encode(array(
			'status'  => 1,
			'message' => 'Jadwal berhasil disimpan.',
			'data'    => array(
				'tanggal'  => $jadwal->tanggal,
				'nama_psikolog' => $jadwal->nama_psikolog,
				'sesi_ke'  => intval($jadwal->sesi_ke),
				'jam_mulai' => $jadwal->jam_mulai,
				'jam_selesai' => $jadwal->jam_selesai,
			),
		));
	}

	/**
	 * Validasi token + kumpulkan state peserta.
	 * Return array('error' => string, 'data' => array) — error kosong = valid.
	 */
	private function _resolve($token, $id_sesi)
	{
		$err = '';
		$data = array();

		if ($id_sesi <= 0 || $token === '') {
			$err = 'Tautan tidak valid.';
		} elseif (!hash_equals(md5($id_sesi . MHCU_CARE_TOKEN_SALT), $token)) {
			$err = 'Tautan tidak valid. Silakan gunakan tautan yang dikirimkan kepada Anda.';
		}

		if ($err === '') {
			$sesi = $this->db->where('id_mhcu_sesi', $id_sesi)
				->where('deleted_at IS NULL', null, false)
				->get('mhcu_sesi')->row();
			if (empty($sesi)) {
				$err = 'Data peserta tidak ditemukan.';
			}
		}

		// Jabatan peserta dari demografi (id_demografi_pertanyaan = 5 "Posisi saat ini")
		$jabatan = '-';
		if ($err === '') {
			$jabatan_row = $this->db->select('demografi_text_option')
				->where('id_sesi', $id_sesi)
				->where('id_demografi_pertanyaan', 5)
				->limit(1)
				->get('mhcu_sesi_demografi')->row();
			if ($jabatan_row && !empty($jabatan_row->demografi_text_option)) {
				$jabatan = $jabatan_row->demografi_text_option;
			}
		}

		if ($err === '') {
			$peringatan = $this->db->where('id_sesi', $id_sesi)
				->order_by('id_peringatan', 'DESC')
				->limit(1)
				->get('mhcu_peringatan')->row();
			if (empty($peringatan)) {
				$err = 'Data tindak lanjut MHCU tidak ditemukan.';
			}
		}

		if ($err === '') {
			$booking = $this->db->query(
				"SELECT b.*, j.tanggal, j.nama_psikolog, j.sesi_ke, j.jam_mulai, j.jam_selesai
				 FROM mhcu_care_booking b
				 JOIN mhcu_care_jadwal j ON j.id_jadwal = b.id_jadwal
				 WHERE b.id_sesi = ? AND b.status IN ('scheduled','selesai')
				 ORDER BY b.id_care DESC LIMIT 1",
				array($id_sesi)
			)->row();

			$already = !empty($booking)
				|| in_array($peringatan->status_alert, array('scheduled', 'process', 'finished'), true);

			$jadwal = $this->db->query(
				"SELECT j.id_jadwal, j.tanggal, j.nama_psikolog, j.sesi_ke, j.jam_mulai, j.jam_selesai,
				        (b.id_care IS NOT NULL) AS taken
				 FROM mhcu_care_jadwal j
				 LEFT JOIN mhcu_care_booking b
				        ON b.id_jadwal = j.id_jadwal AND b.status IN ('scheduled','selesai')
				 ORDER BY j.tanggal ASC, j.sesi_ke ASC"
			)->result();

			$data = array(
				'sesi'           => $sesi,
				'jabatan'        => $jabatan,
				'peringatan'     => $peringatan,
				'booking'        => $booking,
				'already_booked' => $already,
				'jadwal'         => $jadwal,
				'token'          => $token,
			);
		}

		return array('error' => $err, 'data' => $data);
	}

	/**
	 * Render state halaman (error / info / flow) ke satu view.
	 */
	private function _render_state($state, $data)
	{
		$data['page_state'] = $state;
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->load->view('mhcu_care_view', $data);
	}
}
