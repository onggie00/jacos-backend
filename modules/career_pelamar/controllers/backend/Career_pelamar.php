<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Pelamar Controller (Fase 6A)
*| --------------------------------------------------------------------------
*| BACA SAJA: daftar + detail lamaran + unduh dokumen via izin
*| career_pelamar_view. TAHAP 7: verifikasi dokumen/keputusan administrasi
*| di modul ini (di luar lingkup Fase 6A).
*| TIDAK menampilkan: password_hash, token_version, hasil psikologi/skor.
*/
class Career_pelamar extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_career_pelamar');
		$this->load->helper('career_core/career_core');
	}

	/** GET administrator/career_pelamar — filter &amp; paging server-side. */
	public function index($offset = 0)
	{
		$this->is_allowed('career_pelamar_list');

		$filter = array(
			'id_job_posting' => (int) $this->input->get('id_job_posting'),
			'status'         => career_q($this->input->get('status')),
			'stage_id'       => (int) $this->input->get('stage_id'),
			'q'              => career_q($this->input->get('q')),
		);
		$offset = max(0, (int) $offset);
		if ($filter['status'] === 'perlu_verifikasi') {
			// 6B #11: pintasan status → tandai perlu_verifikasi.
			$filter['perlu_verifikasi'] = 1;
			$filter['status'] = '';
		}

		$this->data['q']              = $filter['q'];
		$this->data['status']         = $filter['status'];
		$this->data['stages']         = $this->db->where('is_active', 1)
			->order_by('stage_order', 'ASC')->get('career_stages')->result();
		$this->data['stage_id']       = (int) $filter['stage_id'];
		$this->data['id_job_posting'] = (int) $filter['id_job_posting'];
		$this->data['lowongans']      = $this->db->select('id_job_posting, title')
			->order_by('id_job_posting', 'DESC')
			->get('career_job_postings')->result();
		// Ringkasan hitung per status (satu query) utk kartu tl.
		$this->data['pelamars']       = $this->model_career_pelamar->daftar($filter, $this->limit_page, $offset);
		$this->data['pelamar_counts'] = $this->model_career_pelamar->daftar_count($filter);

		$this->data['ringkasan'] = $this->model_career_pelamar->ringkasan_status();

		$config = [
			'base_url'    => 'administrator/career_pelamar/index/',
			'total_rows'  => $this->data['pelamar_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pelamar');
		$this->render('backend/standart/administrator/career_pelamar/career_pelamar_list', $this->data);
	}

	/** Detail satu lamaran. */
	public function view($id_application = NULL)
	{
		$this->is_allowed('career_pelamar_view');

		$app = $this->model_career_pelamar->find_lamaran($id_application);
		if (!$app) {
			show_error('Lamaran tidak ditemukan.', 404);
		}

		// #1: tanda izin aksi 6B untuk view ($this di view = Loader, bukan
		// controller — jadi is_allowed() dinilai di sini).
		$this->data['bisa_verifikasi'] = $this->is_allowed('career_pelamar_verify', false);
		$this->data['bisa_keputusan']  = $this->is_allowed('career_pelamar_decide', false);
		$this->data['bisa_jadwal']     = $this->is_allowed('career_pelamar_schedule', false);
		$this->data['bisa_nilai']      = $this->is_allowed('career_pelamar_score_edit', false);
		$this->data['app']         = $app;
		$this->data['snapshot']    = $this->model_career_pelamar->snapshot($id_application);
		$this->data['persyaratan'] = $this->model_career_pelamar->persyaratan($id_application);
		$this->data['tahapan']     = $this->model_career_pelamar->tahapan($id_application);
		$this->data['audits']      = $this->model_career_pelamar->audit_lamaran($id_application, 30, 0);
		$this->data['notifikasis'] = $this->model_career_pelamar->notifikasi($app->id_candidate);

		$this->template->title('Detail Pelamar');
		$this->render('backend/standart/administrator/career_pelamar/career_pelamar_view', $this->data);
	}

	/**
	 * Unduh dokumen (GET administrator/career_pelamar/unduh/<id_doc>?app=<id_app>)
	 * dipanggil dari tautan pada view detail. Cek izin career_pelamar_view +
	 * catat audit unduh_dokumen_admin.
	 */
	public function unduh($id_application_document = NULL)
	{
		// HIGH #1: hasil is_allowed HARUS dicek — tanpa ini anonim bisa
		// mengunduh dokumen kandidat (PII).
		if (! $this->is_allowed('career_pelamar_view', false)) {
			show_error('Forbidden', 403);
		}

		$id_app = (int) $this->input->get('app');
		$doc = $this->model_career_pelamar->dokumen_unduh((int) $id_application_document, $id_app);
		if (!$doc) {
			show_error('Dokumen tidak ditemukan.', 404);
		}

		if (!career_stream_admin_document($doc)) {
			show_error('Berkas tidak dapat ditemukan di server.', 404);
		}
		// MED #17: audit SETELAH berkas benar-benar terkirim, supaya log
		// tidak mencatat unduhan yang ternyata gagal 404.
		career_audit('unduh_dokumen_admin', 'career_application_document', (int) $id_application_document, [
			'id_application' => $id_app,
		]);
		exit;
	}

	// ================================================================
	// Fase 6B — aksi tulis. SEMUA: POST-only + career_csrf_ok() → 403;
	// is_allowed hasil dicek → 403; transaksi + kunci lamaran FOR UPDATE;
	// audit SETELAH commit sukses; JSON Content-Type application/json.
	// ================================================================

	private function _butuh($izin)
	{
		if (! $this->is_allowed($izin, false)) {
			$this->_deny403('Forbidden');
			return false;
		}
		return true;
	}

	private function _deny403($pesan = 'Forbidden')
	{
		$this->output->set_status_header(403);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array(
			'success' => false,
			'message' => $pesan,
		)));
	}

	private function _jawab($status, $success, $pesan, $data = null)
	{
		$this->output->set_status_header($status);
		$this->output->set_content_type('application/json');
		$out = array('success' => (bool) $success, 'message' => (string) $pesan);
		if ($data !== null) { $out['data'] = $data; }
		$this->output->set_output(json_encode($out));
	}

	/** Bukan POST atau CSRF gagal: balik TRUE dan 403 sudah terkirim. */
	private function _butuh_post_csrf()
	{
		if ($this->input->method() !== 'post') {
			$this->_deny403('Method harus POST (mutasi tidak lewat GET).');
			return false;
		}
		if (! career_csrf_ok()) {
			$this->_deny403('Sesi tidak sah (CSRF). Muat ulang halaman.');
			return false;
		}
		return true;
	}

	/** Pecah pesan format "422|pesan" → kirim [kode, pesan]. */
	private function _galat_model($pesan)
	{
		$pisah = explode('|', (string) $pesan, 2);
		$kode = ctype_digit($pisah[0]) ? (int) $pisah[0] : 422;
		return array($kode, isset($pisah[1]) ? $pisah[1] : $pesan);
	}

	/**
	 * Verifikasi dokumen/jawaban satu persyaratan (D17).
	 * POST administrator/career_pelamar/verifikasi/<id_application_requirement>
	 * param: keputusan=verified|revision_requested|rejected,
	 *        pesan_kandidat (wajib utk revisi/tolak, <=500),
	 *        review_note (INTERNAL, opsional, <=2000).
	 */
	public function verifikasi($id_application_requirement = null)
	{
		if (! $this->_butuh('career_pelamar_verify')) { return; }
		if (! $this->_butuh_post_csrf()) { return; }

		$idr = (int) $id_application_requirement;
		$keputusan = career_q($this->input->post('keputusan'));
		$pesan_k   = career_q($this->input->post('pesan_kandidat'));
		$catatan   = career_q($this->input->post('review_note'));
		if (career_panjang($pesan_k) > 500) {
			$this->_jawab(422, false, 'Pesan untuk kandidat maksimal 500 karakter.');
			return;
		}
		if (career_ada_emoji($pesan_k) || career_ada_emoji($catatan)) {
			$this->_jawab(422, false, 'Teks mengandung karakter unicode 4-byte yang tidak didukung.');
			return;
		}

		// #6 6B.1: id_application diturunkan lewat query ringan DI LUAR
		// transaksi; dalam transaksi kunci_lamaran PERTAMA, baru baca ulang
		// requirement & status lamaran (anti-TOCTOU).
		$pra = $this->model_career_pelamar->verifikasi_req($idr);
		if ($pra === null) {
			$this->_jawab(404, false, 'Persyaratan tidak ditemukan.');
			return;
		}

		$this->db->trans_start();
		$app = $this->model_career_pelamar->kunci_lamaran((int) $pra->id_application);
		if ($app === null) {
			$this->db->trans_complete();
			$this->_jawab(404, false, 'Lamaran tidak ditemukan.');
			return;
		}
		if (! in_array($app->status, array('submitted', 'in_process'), TRUE)) {
			$this->db->trans_complete();
			$this->_jawab(409, false, 'Verifikasi hanya untuk lamaran submitted/in_process.');
			return;
		}
		$baris = $this->model_career_pelamar->verifikasi_req($idr);
		if ($baris === null) {
			$this->db->trans_complete();
			$this->_jawab(404, false, 'Persyaratan tidak ditemukan.');
			return;
		}
		list($ok, $pesan) = $this->model_career_pelamar->set_verifikasi(
			$idr, $baris, $app, $keputusan, $pesan_k, $catatan);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi verifikasi.'); return; }
		if (! $ok) {
			list($kode, $teks) = $this->_galat_model($pesan);
			$this->_jawab($kode, false, $teks);
			return;
		}
		// Audit SETELAH commit — tanpa isi pesan kandidat (PII minim).
		// #9: object_type career_application agar muncul di linimasa audit
		// lamaran; rid persyaratan dicatat sebagai konteks.
		career_audit('verifikasi_persyaratan', 'career_application', (int) $app->id_application, array(
			'id_application_requirement' => $idr,
			'before' => (string) $baris->requirement_status,
			'after'  => $keputusan,
		));
		$this->_jawab(200, true, $pesan);
	}

	/**
	 * Keputusan tahap (D16.1-4): POST administrator/career_pelamar/keputusan
	 * param: id_application, n (1..5), keputusan=passed|failed.
	 */
	public function keputusan()
	{
		if (! $this->_butuh('career_pelamar_decide')) { return; }
		if (! $this->_butuh_post_csrf()) { return; }

		$id_app = (int) $this->input->post('id_application');
		$n      = (int) $this->input->post('n');
		$keputusan = career_q($this->input->post('keputusan'));
		if (! in_array($keputusan, array('passed', 'failed'), TRUE)) {
			$this->_jawab(422, false, 'Keputusan harus passed/failed.');
			return;
		}

		$this->db->trans_start();
		$app = $this->model_career_pelamar->kunci_lamaran($id_app);
		if ($app === null) { $this->db->trans_complete(); $this->_jawab(404, false, 'Lamaran tidak ditemukan.'); return; }
		if (! in_array($app->status, array('submitted', 'in_process'), TRUE)) {
			$this->db->trans_complete();
			$this->_jawab(409, false, 'Hanya lamaran submitted/in_process yang dapat diproses (D16.1).');
			return;
		}
		list($ok, $pesan) = $this->model_career_pelamar->putus_tahap($id_app, $n, $keputusan);
		$app_baru = $this->model_career_pelamar->find_lamaran($id_app);
		$before = (string) $app->status;
		$after  = $app_baru ? (string) $app_baru->status : $before;
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi keputusan.'); return; }
		if (! $ok) {
			list($kode, $teks) = $this->_galat_model($pesan);
			$this->_jawab($kode, false, $teks);
			return;
		}
		career_audit('keputusan_tahap', 'career_application', $id_app, array(
			'tahap'  => (int) $n,
			'before' => $before,
			'after'  => $keputusan . ' (lamaran: ' . $after . ')',
		));
		$this->_jawab(200, true, $pesan, array('app_status' => $after));
	}

	/**
	 * Koreksi keputusan (D16.5): POST administrator/career_pelamar/koreksi
	 * param: id_application, n, keputusan_baru=passed|failed|pending, alasan (wajib).
	 */
	public function koreksi()
	{
		if (! $this->_butuh('career_pelamar_decide')) { return; }
		if (! $this->_butuh_post_csrf()) { return; }

		$id_app = (int) $this->input->post('id_application');
		$n      = (int) $this->input->post('n');
		$oke    = career_q($this->input->post('keputusan_baru'));
		$alasan = career_q($this->input->post('alasan'));
		if ($n < 1 || $n > 5) {
			$this->_jawab(404, false, 'Tahap tidak ditemukan.');
			return;
		}
		if (! in_array($oke, array('passed', 'failed', 'pending'), TRUE)) {
			$this->_jawab(422, false, 'Keputusan baru harus passed/failed/pending.');
			return;
		}
		if (career_panjang($alasan) === 0) {
			$this->_jawab(422, false, 'Alasan koreksi wajib diisi (maksimal 500 karakter).');
			return;
		}
		if (career_panjang($alasan) > 500) {
			$this->_jawab(422, false, 'Alasan koreksi maksimal 500 karakter.');
			return;
		}
		if (career_ada_emoji($alasan)) {
			$this->_jawab(422, false, 'Teks mengandung karakter unicode 4-byte yang tidak didukung.');
			return;
		}

		$this->db->trans_start();
		$app = $this->model_career_pelamar->kunci_lamaran($id_app);
		if ($app === null) { $this->db->trans_complete(); $this->_jawab(404, false, 'Lamaran tidak ditemukan.'); return; }
		$before_app = (string) $app->status;
		$tahap_ada  = career_stages_rekap($id_app);
		$before_tahap = (isset($tahap_ada[$n - 1]) && $tahap_ada[$n - 1] !== null)
			? (string) $tahap_ada[$n - 1]->status : '';
		list($ok, $pesan) = $this->model_career_pelamar->koreksi_tahap($id_app, $n, $oke, $alasan);
		$app_baru = $this->model_career_pelamar->find_lamaran($id_app);
		$after = $app_baru ? (string) $app_baru->status : (string) $app->status;
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi koreksi.'); return; }
		if (! $ok) {
			list($kode, $teks) = $this->_galat_model($pesan);
			$this->_jawab($kode, false, $teks);
			return;
		}
		// D16.5: akuntabilitas koreksi — BUKAN data D13 (bukan skor/
		// rubrik): alasan koreksi tersimpan penuh (maks 500) di
		// detail_json + status tahap & lamaran sebelum/sesudah.
		career_audit('koreksi_keputusan_tahap', 'career_application', $id_app, array(
			'tahap'        => (int) $n,
			'before_tahap' => $before_tahap,
			'after_tahap'  => $oke,
			'before_app'   => $before_app,
			'after_app'    => $after,
			'alasan'       => $alasan,
		));
		$this->_jawab(200, true, $pesan, array('app_status' => $after));
	}

	/**
	 * Status akhir DITERIMA (eksplisit, D16.4):
	 * POST administrator/career_pelamar/terima dengan id_application.
	 */
	public function terima()
	{
		if (! $this->_butuh('career_pelamar_decide')) { return; }
		if (! $this->_butuh_post_csrf()) { return; }
		$id_app = (int) $this->input->post('id_application');

		$this->db->trans_start();
		$app = $this->model_career_pelamar->kunci_lamaran($id_app);
		if ($app === null) { $this->db->trans_complete(); $this->_jawab(404, false, 'Lamaran tidak ditemukan.'); return; }
		if ($app->status === 'accepted') { $this->db->trans_complete(); $this->_jawab(409, false, 'Lamaran sudah diterima.'); return; }
		if (! in_array($app->status, array('submitted', 'in_process'), TRUE)) {
			$this->db->trans_complete();
			$this->_jawab(409, false, 'Hanya lamaran submitted/in_process.');
			return;
		}
		$before = (string) $app->status;
		list($ok, $pesan) = $this->model_career_pelamar->terima_akhir($id_app);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi.'); return; }
		if (! $ok) {
			list($kode, $teks) = $this->_galat_model($pesan);
			$this->_jawab($kode, false, $teks);
			return;
		}
		career_audit('status_akhir_diterima', 'career_application', $id_app, array(
			'before' => $before, 'after' => 'accepted',
		));
		$this->_jawab(200, true, $pesan, array('app_status' => 'accepted'));
	}

	/**
	 * Jadwal tahap: POST administrator/career_pelamar/jadwal
	 * param: id_application_stage, aksi=simpan|hapus|publikasi|batal,
	 *        schedule_datetime (Y-m-d H:i), channel (<=200), note (<=500).
	 */
	public function jadwal()
	{
		if (! $this->_butuh('career_pelamar_schedule')) { return; }
		if (! $this->_butuh_post_csrf()) { return; }

		$ids  = (int) $this->input->post('id_application_stage');
		$aksi = career_q($this->input->post('aksi'));
		if (! in_array($aksi, array('simpan', 'hapus', 'publikasi', 'batal'), TRUE)) {
			$this->_jawab(422, false, 'Aksi jadwal tidak sah.');
			return;
		}
		$st = $this->model_career_pelamar->stage_untuk_jadwal($ids);
		if ($st === null) { $this->_jawab(404, false, 'Tahap tidak ditemukan.'); return; }
		// BENTUK input divalidasi di luar transaksi; SEMUA validasi
		// status/urutan dilakukan DI DALAM transaksi SETELAH lock (#7).
		$urut_tahap = (int) $st->stage_order;
		$tanggal  = career_q($this->input->post('schedule_datetime'));
		$kanal    = career_q($this->input->post('channel'));
		$catatann = career_q($this->input->post('note'));
		$tiga = null;
		if ($aksi === 'simpan') {
			if (career_panjang($kanal) > 200) { $this->_jawab(422, false, 'Kanal maksimal 200 karakter.'); return; }
			if (career_panjang($catatann) > 500) { $this->_jawab(422, false, 'Catatan maksimal 500 karakter.'); return; }
			$tgl = DateTime::createFromFormat('Y-m-d H:i', $tanggal);
			if ($tgl === false || $tgl->format('Y-m-d H:i') !== $tanggal) {
				$this->_jawab(422, false, 'Tanggal tidak sah (format Y-m-d H:i).');
				return;
			}
			$tiga = $tgl->format('Y-m-d H:i:s');
		}

		$this->db->trans_start();
		$app = $this->model_career_pelamar->kunci_lamaran((int) $st->id_application);
		if ($app === null) {
			$this->db->trans_complete();
			$this->_jawab(404, false, 'Lamaran tidak ditemukan.');
			return;
		}

		if ($aksi === 'batal') {
			// Pencabutan publikasi TIDAK terkunci D16.2 (#5): admin selalu
			// dapat menarik jadwal pada lamaran yang masih bisa diproses
			// maupun yang baru dikoreksi.
			if (! in_array($app->status,
					array('submitted', 'in_process', 'not_accepted', 'accepted'), TRUE)) {
				$this->db->trans_complete();
				$this->_jawab(409, false, 'Lamaran tidak dalam keadaan yang bisa diproses.');
				return;
			}
			$st = $this->model_career_pelamar->stage_untuk_jadwal($ids);
			if ($st === null) { $this->db->trans_complete(); $this->_jawab(404, false, 'Tahap tidak ditemukan.'); return; }
			list($ok, $pesan) = $this->model_career_pelamar->jadwal_publikasi($ids, 0);
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi.'); return; }
			if (! $ok) {
				list($kode, $teks) = $this->_galat_model($pesan);
				$this->_jawab($kode, false, $teks);
				return;
			}
			career_audit('batal_publikasi_jadwal', 'career_application', (int) $app->id_application, array('stage_order' => $urut_tahap));
			$this->_jawab(200, true, $pesan);
			return;
		}

		// simpan/publikasi/hapus: lamaran harus submitted/in_process.
		if (! in_array($app->status, array('submitted', 'in_process'), TRUE)) {
			$this->db->trans_complete();
			$this->_jawab(409, false, 'Jadwal hanya untuk lamaran submitted/in_process.');
			return;
		}
		$st = $this->model_career_pelamar->stage_untuk_jadwal($ids);
		if ($st === null) { $this->db->trans_complete(); $this->_jawab(404, false, 'Tahap tidak ditemukan.'); return; }
		// D16.2: seluruh tahap sebelumnya harus passed sebelum tahap
		// ini boleh dijadwalkan; tahap yang sudah diputuskan terkunci.
		$tahapan = $this->model_career_pelamar->tahapan($st->id_application);
		foreach ($tahapan as $t) {
			$urut = (int) $t->stage_order;
			if ($urut < $urut_tahap && $t->status !== 'passed') {
				$this->db->trans_complete();
				$this->_jawab(409, false, 'Tahap ' . $urut . ' belum lulus — jadwal tahap ini belum boleh diset (D16.2).');
				return;
			}
		}
		if (in_array($st->status, array('passed', 'failed'), TRUE)) {
			$this->db->trans_complete();
			$this->_jawab(409, false, 'Tahap sudah diputuskan — jadwal tidak dapat diubah.');
			return;
		}

		if ($aksi === 'hapus') {
			list($ok, $pesan) = $this->model_career_pelamar->jadwal_hapus($ids);
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi jadwal.'); return; }
			if (! $ok) {
				list($kode, $teks) = $this->_galat_model($pesan);
				$this->_jawab($kode, false, $teks);
				return;
			}
			career_audit('hapus_jadwal_tahap', 'career_application', (int) $app->id_application, array('stage_order' => $urut_tahap));
			$this->_jawab(200, true, $pesan);
			return;
		}

		if ($aksi === 'simpan') {
			list($ok, $hasil_simpan) = $this->model_career_pelamar->jadwal_simpan($ids, array(
				'schedule_datetime' => $tiga,
				'channel' => $kanal === '' ? null : $kanal,
				'note'    => $catatann === '' ? null : $catatann,
				'is_published' => 0,
				'created_by'   => (int) $this->session->userdata('id'),
			));
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi jadwal.'); return; }
			if (! $ok) {
				list($kode, $teks) = $this->_galat_model($hasil_simpan);
				$this->_jawab($kode, false, $teks);
				return;
			}
			career_audit('simpan_jadwal_tahap', 'career_application', (int) $app->id_application,
				array('stage_order' => $urut_tahap, 'jadwal' => $tiga));
			$this->_jawab(200, true, 'Jadwal disimpan (belum dipublikasikan).');
			return;
		}

		// aksi=publikasi
		list($ok, $pesan) = $this->model_career_pelamar->jadwal_publikasi($ids, 1);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi publikasi.'); return; }
		if (! $ok) {
			list($kode, $teks) = $this->_galat_model($pesan);
			$this->_jawab($kode, false, $teks);
			return;
		}
		career_audit('publikasi_jadwal_tahap', 'career_application', (int) $app->id_application,
			array('stage_order' => $urut_tahap));
		$this->_jawab(200, true, $pesan);
	}

	/**
	 * Nilai & catatan internal (D13): POST administrator/career_pelamar/nilai
	 * param: id_application_stage, score (0-100), rubric_note (<=2000).
	 * Data SENSITIF — tidak pernah ke kandidat (apicareer select tanpa
	 * kolom skor/catatan; diuji smoke6b).
	 */
	public function nilai()
	{
		if (! $this->_butuh('career_pelamar_score_edit')) { return; }
		if (! $this->_butuh_post_csrf()) { return; }
		$ids   = (int) $this->input->post('id_application_stage');
		$skor  = career_q($this->input->post('score'));
		$catatann = career_q($this->input->post('rubric_note'));
		if (career_panjang($catatann) > 2000) {
			$this->_jawab(422, false, 'Catatan rubrik maksimal 2000 karakter.');
			return;
		}
		if (career_ada_emoji($catatann)) {
			$this->_jawab(422, false, 'Teks mengandung karakter unicode 4-byte yang tidak didukung.');
			return;
		}
		$st = $this->model_career_pelamar->stage_untuk_jadwal($ids);
		if ($st === null) { $this->_jawab(404, false, 'Tahap tidak ditemukan.'); return; }

		$this->db->trans_start();
		$app = $this->model_career_pelamar->kunci_lamaran((int) $st->id_application);
		if ($app === null) { $this->db->trans_complete(); $this->_jawab(404, false, 'Lamaran tidak ditemukan.'); return; }
		// #16: nilai hanya untuk lamaran yang sudah diajukan; dicek
		// SETELAH lock (anti-TOCTOU).
		if (! in_array($app->status,
				array('submitted', 'in_process', 'accepted', 'not_accepted'), TRUE)) {
			$this->db->trans_complete();
			$this->_jawab(409, false, 'Nilai hanya untuk lamaran yang sudah diajukan (bukan draft/withdrawn).');
			return;
		}
		list($ok, $pesan) = $this->model_career_pelamar->nilai_simpan($ids, $skor, $catatann);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) { $this->db->trans_rollback(); $this->_jawab(500, false, 'Gagal transaksi nilai.'); return; }
		if (! $ok) {
			list($kode, $teks) = $this->_galat_model($pesan);
			$this->_jawab($kode, false, $teks);
			return;
		}
		// #4: TANPA angka skor & tanpa isi catatan di audit (D13 sensitif,
		// audiens audit lebih luas daripada pemegang score_view) — cukup
		// "nilai diubah" + tahap + panjang catatan.
		career_audit('simpan_nilai_tahap', 'career_application', (int) $st->id_application, array(
			'stage_order' => (int) $st->stage_order,
			'nilai'       => 'diubah',
			'rubric_len'  => career_panjang($catatann),
		));
		$this->_jawab(200, true, $pesan);
	}
}
