<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Audit Controller (Fase 6A)
*| --------------------------------------------------------------------------
*| BACA SAJA: riwayat career_audit_logs. Modul ini tidak pernah menulis log;
*| log muncul otomatis dari aksi admin/kandidat/system (Fase 5, 6, 7).
*/
class Career_audit extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_career_audit');
		$this->load->helper('career_core/career_core');
	}

	/** GET administrator/career_audit — filter + paging server-side. */
	public function index($offset = 0)
	{
		$this->is_allowed('career_audit_list');

		$filter = array(
			'action'      => career_q($this->input->get('action')),
			'object_type' => career_q($this->input->get('object_type')),
			'object_id'   => (int) $this->input->get('object_id'),
			'actor_type'  => career_q($this->input->get('actor_type')),
			'tgl_dari'    => $this->input->get('tgl_dari'),
			'tgl_sampai'  => $this->input->get('tgl_sampai'),
		);

		$this->data['filter']      = $filter;
		$this->data['aksis']       = $this->model_career_audit->aksi_unik();
		$this->data['tipe_objeks'] = $this->model_career_audit->tipe_objek_unik();
		// 6C: nilai internal (D13) hanya untuk pemegang izin skor — detail
		// audit 'simpan_nilai_tahap' disamarkan server-side di model.
		$this->data['bisa_skor'] = (bool) $this->is_allowed('career_pelamar_score_view', false);
		$this->data['audits']      = $this->model_career_audit->daftar($filter, $this->limit_page, $offset, $this->data['bisa_skor']);
		$this->data['audit_count'] = $this->model_career_audit->daftar_count($filter);

		$config = [
			'base_url'    => 'administrator/career_audit/index/',
			'total_rows'  => $this->data['audit_count'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		// Query string dipertahankan saat pindah halaman (paging + filter).
		$this->data['qs'] = http_build_query(array_filter(array(
			'action'      => $filter['action'],
			'object_type' => $filter['object_type'],
			'object_id'   => (int) $filter['object_id'],
			'actor_type'  => $filter['actor_type'],
			'tgl_dari'    => $filter['tgl_dari'],
			'tgl_sampai'  => $filter['tgl_sampai'],
		)));
		$this->data['cur_offset'] = (int) $offset;
		$this->data['per_page']   = $this->limit_page;
		$this->data['total_rows'] = $this->data['audit_count'];

		$this->template->title('Audit Karier');
		$this->render('backend/standart/administrator/career_audit/career_audit_list', $this->data);
	}
}
