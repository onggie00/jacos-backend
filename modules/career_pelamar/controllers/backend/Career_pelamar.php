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

		$this->data['q']              = $filter['q'];
		$this->data['status']         = $filter['status'];
		$this->data['stages']         = $this->db->where('is_active', 1)
			->order_by('stage_order', 'ASC')->get('career_stages')->result();
		$this->data['stage_id']       = (int) $filter['stage_id'];
		$this->data['id_job_posting'] = (int) $filter['id_job_posting'];
		$this->data['lowongans']      = $this->db->select('id_job_posting, title')
			->order_by('id_job_posting', 'DESC')
			->get('career_job_postings')->result();
		$this->data['pelamars']       = $this->model_career_pelamar->daftar($filter, $this->limit_page, $offset);
		$this->data['pelamar_counts'] = $this->model_career_pelamar->daftar_count($filter);

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
}
