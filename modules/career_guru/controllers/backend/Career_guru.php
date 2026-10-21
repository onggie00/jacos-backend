<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Guru Controller (Fase GURU.1 Bagian B)
*| --------------------------------------------------------------------------
*| Konversi pelamar diterima (status=accepted) menjadi guru SD/KB/TK.
*| SALIN (BUKAN memindah) data kandidat ke tabel guru_<jenjang>; data karier
*| + dokumen lamaran tetap UTUH di modul karier.
*|
*| Izin: career_pelamar_to_guru (dimiliki pemegang career_pelamar_decide).
*| Tombol dihalaman detail pelamar hanya tampil bila admin punya izin.
*|
*| TIDAK dalam lingkup fase ini (open decision):
*|  - akun/token aplikasi guru & notifikasi kandidat
*|  - penonaktifan akun karier / pemindahan berkas
*|  - konversi massal & pembalikan otomatis (rollback manual via SQL bila
*|    guru belum dipakai presensi — lihat laporan GURU.1)
*/
class Career_guru extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_career_guru');
		$this->load->helper('guru_core/guru_core');
		$this->load->helper('career_core/career_core');
	}

	/** GET administrator/career_guru/konversi/{id_application} */
	public function konversi($id_application)
	{
		$this->is_allowed('career_pelamar_to_guru');

		$id_application = (int) $id_application;
		$app = $this->db->select('ap.id_application, ap.status, ap.id_candidate, ap.submitted_at, c.full_name, c.email')
			->from('career_applications ap')
			->join('career_candidates c', 'c.id_candidate = ap.id_candidate', 'INNER')
			->where('ap.id_application', $id_application)
			->limit(1)
			->get()
			->row();

		if ( ! $app) {
			set_message('Lamaran tidak ditemukan.', 'error');
			redirect('administrator/career_pelamar');
		}

		if ($app->status !== 'accepted') {
			set_message('Hanya lamaran dengan status Diterima yang dapat dikonversi.', 'error');
			redirect('administrator/career_pelamar/view/' . $id_application);
		}

		if ($this->model_career_guru->link_lamaran($id_application)) {
			set_message('Lamaran ini sudah dikonversi menjadi guru.', 'error');
			redirect('administrator/career_pelamar/view/' . $id_application);
		}

		// Jenjang tujuan dipakai admin di form (satu halaman, satu jenjang):
		// jeung via ?t=KB/TK/SD, default SD karena wajib dipilih.
		$jenjang = (string) $this->input->get('t');
		if ( ! in_array($jenjang, array('SD', 'KB', 'TK'), TRUE)) { $jenjang = 'SD'; }

		$this->data['app']                = $app;
		$this->data['jenjang_pilihan']    = $jenjang;
		$this->data['ps_default']         = $this->model_career_guru->id_posisi_default($jenjang);
		$this->data['profil']             = $this->model_career_guru->profil($app->id_candidate);
		$this->data['pendidikan_terbaru'] = $this->model_career_guru->pendidikan_terbaru($app->id_candidate);
		$this->data['posisi']             = $this->db->order_by('id_posisi', 'ASC')->get('posisi_guru')->result();
		$this->data['mapel']              = $this->db->order_by('nama_mapel', 'ASC')->get('mata_pelajaran_sd')->result();

		$this->template->title('Konversi Pelamar Ke Guru');
		$this->render('backend/standart/administrator/career_guru/konversi_form', $this->data);
	}

	/**
	 * POST administrator/career_guru/konversi_simpan/{id_application}
	 * Race dua admin: tepat satu 200, yang lain 409 "sudah dikonversi".
	 */
	public function konversi_simpan_post($id_application)
	{
		if (!$this->is_allowed('career_pelamar_to_guru', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access'),
			));
			exit;
		}

		/* B-2: POST + CSRF (token per-sesi helper career_core). */
		if ((string) $this->input->method() !== 'post' || ! career_csrf_ok()) {
			echo json_encode(array('success' => false, 'message' => 'Permintaan tidak sah (CSRF).'));
			exit;
		}

		$id_application = (int) $id_application;

		/* ---- Validasi bentuk di LUAR transaksi -------------------------------- */
		$post = (object) array(
			'jenjang'             => (string) $this->input->post('jenjang'),
			'emp_code'            => trim(strip_tags((string) $this->input->post('emp_code'))),
			'npp'                 => trim(strip_tags((string) $this->input->post('npp'))),
			'email'               => trim(strip_tags((string) $this->input->post('email'))),
			'email_ms_office'     => trim(strip_tags((string) $this->input->post('email_ms_office'))),
			'id_posisi'           => (int) $this->input->post('id_posisi'),
			'id_mapel'            => (int) $this->input->post('id_mapel'),
			'status_kepegawaian'  => trim(strip_tags((string) $this->input->post('status_kepegawaian'))),
			'keterangan_jabatan'  => trim(strip_tags((string) $this->input->post('keterangan_jabatan'))),
			'unit'                => trim(strip_tags((string) $this->input->post('unit'))),
			'pendidikan_terakhir' => trim(strip_tags((string) $this->input->post('pendidikan_terakhir'))),
			'universitas'         => trim(strip_tags((string) $this->input->post('universitas'))),
			'jurusan'             => trim(strip_tags((string) $this->input->post('jurusan'))),
			'tahun_lulus'         => (int) $this->input->post('tahun_lulus'),
			'periksa_dipastikan'  => $this->input->post('periksa_dipastikan') === '1',
		);

		$galat = array();

		if ( ! in_array($post->jenjang, array('SD', 'KB', 'TK'), TRUE)) {
			$galat[] = 'Jenjang tujuan wajib dipilih (SD/KB/TK).';
		}

		$tabel = ($post->jenjang) ? $this->model_career_guru->tabel_jenjang($post->jenjang) : NULL;
		if ($tabel === NULL && $galat === array()) {
			$galat[] = 'Jenjang tidak dikenali.';
		}

		if ($post->emp_code === '') {
			$galat[] = 'Emp code wajib diisi.';
		}
		if ($post->email !== '' && ! filter_var($post->email, FILTER_VALIDATE_EMAIL)) {
			$galat[] = 'Email tidak valid.';
		}
		if ($post->email_ms_office !== '' && ! filter_var($post->email_ms_office, FILTER_VALIDATE_EMAIL)) {
			$galat[] = 'Email MS Office tidak valid.';
		}

		$id_posisi_default = ($tabel) ? $this->model_career_guru->id_posisi_default($post->jenjang) : NULL;
		$id_posisi = ($post->id_posisi > 0) ? $post->id_posisi : $id_posisi_default;
		if ($id_posisi === NULL || ! $this->db->where('id_posisi', $id_posisi)
			->limit(1)->get('posisi_guru')->row()) {
			$galat[] = 'Posisi tidak valid.';
		}

		/* id_mapel hanya untuk jenjang SD; kosong atau tidak id mapel yang valid
		   di mata_pelajaran_sd; KB/TK NULL. */
		$id_mapel = ($post->id_mapel > 0) ? $post->id_mapel : NULL;
		if ($id_mapel !== NULL && ! $this->db->where('id_mapel', $id_mapel)
			->limit(1)->get('mata_pelajaran_sd')->row()) {
			$galat[] = 'Mapel tidak valid di tabel mata_pelajaran_sd.';
		}
		if ($post->jenjang === 'SD' && $id_mapel === NULL) {
			$galat[] = 'Mapel wajib dipilih bila jenjang tujuan SD.';
		}

		if ($galat) {
			echo json_encode(array('success' => false, 'message' => implode(' ', $galat)));
			exit;
		}

		/* ---- Mulai trans — kunci lamaran utk race dua admin ------------------- */
		$this->db->trans_begin();

		$app = $this->model_career_guru->kunci_lamaran($id_application);
		if ( ! $app) {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false, 'message' => 'Lamaran tidak ditemukan.'));
			exit;
		}

		if ($app->status !== 'accepted') {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false,
				'message' => 'Hanya lamaran dengan status Diterima yang dapat dikonversi.'));
			exit;
		}

		$link_baru = $this->model_career_guru->link_lamaran($id_application);
		if ($link_baru) {
			$this->db->trans_complete();
			echo json_encode(array('success' => false,
				'message' => 'Lamaran ini sudah dikonversi menjadi guru (oleh admin lain).',
				'id_guru' => (int) $link_baru->id_guru));
			exit;
		}

		if ($this->model_career_guru->link_kandidat($app->id_candidate, $id_application)) {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false,
				'message' => 'Kandidat yang sama punya lamaran lain yang sudah dikonversi — satu guru per kandidat.'));
			exit;
		}

		/* ---- Pembekal guru ---------------------------------------------------- */
		$profil = $this->model_career_guru->profil($app->id_candidate);
		$pendidikan = $this->model_career_guru->pendidikan_terbaru($app->id_candidate);

		$data_guru = array(
			'nama_lengkap'       => trim($app->full_name),
			'nik'                => NULL,   // TIDAK disalin (open decision GURU.1)
			'no_kk'              => NULL,   // TIDAK disalin
			'npwp'               => NULL,   // TIDAK disalin
			'npp'                => $post->npp,
			'nuptk'              => NULL,
			'email'              => $post->email,
			'email_ms_office'    => $post->email_ms_office,
			'emp_code'           => $post->emp_code,
			'id_posisi'          => $id_posisi,
			'id_mapel'           => ($post->jenjang === 'SD') ? $id_mapel : NULL,
			'status_kepegawaian' => $post->status_kepegawaian,
			'informasi_kepala_pimpinan' => NULL,
			'keterangan_jabatan' => $post->keterangan_jabatan,
			'jam_ajar'           => NULL,
			'unit'               => ($post->unit !== '') ? $post->unit
				: ($post->jenjang === 'KB' ? 'KB' : ($post->jenjang === 'TK' ? 'TK' : 'SD')),
			'pendidikan_terakhir'=> $post->pendidikan_terakhir,
			'universitas'        => $post->universitas,
			'jurusan'            => $post->jurusan,
			'tahun_lulus'        => $post->tahun_lulus ?: NULL,
			// Pemetaan dari profil kandidat (server-side):
			'tempat_lahir'       => $profil ? (string) $profil->place_of_birth : NULL,
			'tgl_lahir'          => $profil && $profil->date_of_birth ? (string) $profil->date_of_birth : NULL,
			'jenis_kelamin'      => $profil ? ($profil->gender === 'male' ? 'L' : ($profil->gender === 'female' ? 'P' : NULL)) : NULL,
			'alamat'             => $profil ? (string) $profil->address : NULL,
			'agama'              => NULL,
			'status_menikah'     => NULL,
			'jumlah_anak'        => NULL,
			'no_telp'            => $profil ? substr((string) $profil->phone, 0, 25) : NULL,
			'token'              => NULL,
			'token_expired'      => NULL,
			'login_token'        => '',
		);

		/* ---- Cek tabrakan lintas jenjang (B-2) --------------------------------- */
		$tabrakan = $this->model_career_guru->tabrakan_guru(
			$data_guru['nama_lengkap'], $data_guru['tgl_lahir'],
			$data_guru['emp_code'], $data_guru['email'], $data_guru['email_ms_office'],
			$data_guru['nik']
		);

		foreach ($tabrakan as $t) {
			if ($t['jenis'] === 'blok') {
				$this->db->trans_rollback();
				echo json_encode(array('success' => false,
					'message' => $t['field'] . ' sudah dipakai guru lain (' . $t['tabel'] . ' #' . $t['id'] . ') — perbaiki dulu di form konversi.'));
				exit;
			}
		}

		$perlu_konfirmasi = false;
		foreach ($tabrakan as $t) {
			if ($t['jenis'] === 'periksa') { $perlu_konfirmasi = true; break; }
		}
		if ($perlu_konfirmasi && ! $post->periksa_dipastikan) {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false, 'tahan' => true,
				'message' => 'Ada guru lama dengan nama + tanggal lahir yang sama. Centang "Saya sudah memeriksa..." di form untuk melanjutkan.'));
			exit;
		}

		/* ---- Sisip guru + link ------------------------------------------------- */
		$id_guru = $this->model_career_guru->insert_guru($post->jenjang, $data_guru);

		if ($id_guru <= 0) {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false,
				'message' => 'Gagal menyimpan data guru — coba lagi.'));
			exit;
		}

		$snapshot = array(
			'jenjang'       => $post->jenjang,
			'id_guru'       => $id_guru,
			'tabel_guru'    => $tabel,
			'emp_code'      => $post->emp_code,
			'id_posisi'     => $id_posisi,
			'nama_lengkap'  => $data_guru['nama_lengkap'],
			'email'         => $post->email,
			'tanggal_kirim' => $app->submitted_at,
		);

		$id_link = $this->model_career_guru->insert_link(array(
			'id_application' => $id_application,
			'id_candidate'   => $app->id_candidate,
			'jenjang'        => $post->jenjang,
			'id_guru'        => $id_guru,
			'converted_by'   => $this->session->userdata('id'),
			'snapshot_json'  => json_encode($snapshot, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE),
		));

		if ($id_link <= 0) {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false,
				'message' => 'Gagal menyimpan link konversi — data guru dibatalkan (rollback).'));
			exit;
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(array('success' => false,
				'message' => 'Gagal transaksi konversi — dibatalkan.'));
			exit;
		}

		/* ---- Audit SETELAH trans sukses ---------------------------------------- */
		career_audit('application.convert_to_guru', 'career_application', $id_application, array(
			'jenjang'    => $post->jenjang,
			'id_guru'    => $id_guru,
			'tabel_guru' => $tabel,
			'id_link'    => $id_link,
		));

		$ctl = ($post->jenjang === 'SD') ? 'guru_sd' : ($post->jenjang === 'KB' ? 'guru_kb' : 'guru_tk');
		echo json_encode(array(
			'success'  => true,
			'message'  => 'Lamaran dikonversi menjadi guru ' . $post->jenjang . ' #' . $id_guru . '.',
			'redirect' => base_url('administrator/' . $ctl . '/view/' . $id_guru),
		));
		exit;
	}

	/** GET administrator/career_guru/cek_link/{id_application} — baca status link. */
	public function cek_link($id_application)
	{
		$this->is_allowed('career_pelamar_list');

		$link = $this->model_career_guru->link_lamaran((int) $id_application);
		if ( ! $link) {
			echo json_encode(array('success' => false, 'message' => 'Belum dikonversi.'));
			exit;
		}
		echo json_encode(array(
			'success'      => true,
			'jenjang'      => $link->jenjang,
			'id_guru'      => (int) $link->id_guru,
			'id_guru_tabel'=> $this->model_career_guru->tabel_jenjang($link->jenjang),
			'converted_at' => $link->converted_at,
		));
		exit;
	}
}
