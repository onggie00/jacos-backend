<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Template Controller (Fase 6A, revisi 6A.1)
*| --------------------------------------------------------------------------
*| Master template persyaratan karier (career_requirement_templates).
*| Aturan D12: template yang pernah dipakai lowongan TIDAK boleh dihapus
*| — hanya dinonaktifkan. Semua mutasi POST + CSRF + audit career_audit_logs.
*/
class Career_template extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_career_template');
		$this->load->helper('career_core/career_core');
		$this->load->library('form_validation');
	}

	/** Daftar template — filter nama + status. GET administrator/career_template */
	public function index($offset = 0)
	{
		$this->is_allowed('career_template_list');

		$q      = career_q($this->input->get('q'));
		$status = career_q($this->input->get('status'));
		$offset = max(0, (int) $offset);

		$this->data['q']        = $q;
		$this->data['status']   = $status;
		$this->data['templates']      = $this->model_career_template->career_list($q, $status, $this->limit_page, $offset);
		$this->data['template_counts'] = $this->model_career_template->career_count($q, $status);

		// MED #5/#11: peta dipakai 1 query, bukan N+1 per baris.
		$ids = array();
		foreach ($this->data['templates'] as $t) { $ids[] = (int) $t->id_requirement_template; }
		$this->data['dipakai'] = $this->model_career_template->used_map($ids);

		$config = [
			'base_url'    => 'administrator/career_template/index/',
			'total_rows'  => $this->data['template_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		// token CSRF utk tombol hapus via AJAX POST (HIGH #2/#7)
		$this->data['csrf_field'] = career_csrf_field();

		$this->template->title('Master Persyaratan');
		$this->render('backend/standart/administrator/career_template/career_template_list', $this->data);
	}

	/** Form tambah template. */
	public function add()
	{
		if (!$this->is_allowed('career_template_add')) {
			return;
		}

		$this->template->title('Tambah Master Persyaratan');
		$this->render('backend/standart/administrator/career_template/career_template_add', $this->data);
	}

	/** Simpan template baru (POST JSON/AJAX). */
	public function add_save()
	{
		if (!$this->is_allowed('career_template_add', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}

		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}

		$this->form_validation->set_rules('name', 'Nama', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[2000]');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|in_list[0,1]');

		if (!$this->form_validation->run()) {
			return $this->_json_invalid($this->form_validation->error_array());
		}

		$save_data = [
			'name'        => $this->input->post('name'),
			'description' => $this->input->post('description') !== NULL ? $this->input->post('description') : '',
			'is_active'   => $this->input->post('is_active') == '1' ? 1 : 0,
		];

		// MED #9: transaksi + audit HANYA setelah commit sukses.
		$this->db->trans_start();
		$id = $this->model_career_template->store_template($save_data);
		$ok = $this->db->trans_complete();

		if (!$ok || $id <= 0) {
			return $this->_json_done(false, NULL, 'Gagal menyimpan template.');
		}

		career_audit('create_template', 'career_requirement_template', $id, [
			'name' => $save_data['name'],
			'is_active' => $save_data['is_active'],
		]);

		return $this->_json_done(true, $id);
	}

	/** Form ubah template. */
	public function edit($id)
	{
		if (!$this->is_allowed('career_template_update')) {
			return;
		}

		$this->data['tpl'] = $this->model_career_template->find_template($id);
		if (!$this->data['tpl']) {
			show_error('Template tidak ditemukan.', 404);
		}
		$this->data['used'] = $this->model_career_template->used_by_postings($id);

		$this->template->title('Ubah Master Persyaratan');
		$this->render('backend/standart/administrator/career_template/career_template_update', $this->data);
	}

	/** Simpan ubahan template (POST JSON/AJAX). */
	public function edit_save($id)
	{
		if (!$this->is_allowed('career_template_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}

		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}

		$old = $this->model_career_template->find_template($id);
		if (!$old) {
			return $this->_json_deny('Template tidak ditemukan.');
		}

		$this->form_validation->set_rules('name', 'Nama', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[2000]');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|in_list[0,1]');

		if (!$this->form_validation->run()) {
			return $this->_json_invalid($this->form_validation->error_array());
		}

		$save_data = [
			'name'        => $this->input->post('name'),
			'description' => $this->input->post('description') !== NULL ? $this->input->post('description') : '',
			'is_active'   => $this->input->post('is_active') == '1' ? 1 : 0,
		];

		$this->db->trans_start();
		$ubah = $this->model_career_template->change_template($id, $save_data);
		// 6C nit: transaksi selalu ditutup (auto-rollback bila gagal).
		$done = $this->db->trans_complete();
		$ok = ($ubah) && $this->db->trans_status();


		if (!$ok) {
			return $this->_json_done(false, (int) $id, 'Gagal menyimpan template.');
		}

		// MED #16: audit sebelum/setelah SEMUA field yang bisa berubah.
		career_audit('update_template', 'career_requirement_template', (int) $id, [
			'before' => [
				'name'        => $old->name,
				'description' => $old->description,
				'is_active'   => (int) $old->is_active,
			],
			'after' => [
				'name'        => $save_data['name'],
				'description' => $save_data['description'],
				'is_active'   => $save_data['is_active'],
			],
		]);

		return $this->_json_done(true, (int) $id);
	}

	/**
	 * Hapus template (POST dgn konfirmasi AJAX di list — HIGH #2: GET
	 * mutasi ditolak). Hanya yang BELUM pernah dipakai lowongan (D12).
	 */
	public function delete($id = null)
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Metode harus POST.', 403);
		}
		if (!$this->is_allowed('career_template_delete', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'), 403);
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.', 403);
		}

		$old = $this->model_career_template->find_template($id);
		if (!$old) {
			return $this->_json_done(false, (int) $id, 'Template tidak ditemukan.');
		}

		if ($this->model_career_template->delete_unused($id)) {
			career_audit('delete_template', 'career_requirement_template', (int) $id, [
				'name' => $old->name,
			]);
			return $this->_json_done(true, (int) $id, 'Template dihapus.');
		}

		// D12: template dipakai lowongan → nonaktifkan saja.
		return $this->_json_done(false, (int) $id,
			'Template sudah pernah dipakai lowongan dan tidak boleh dihapus — nonaktifkan saja.');
	}

	private function _json_done($success, $id = NULL, $message = NULL)
	{
		$this->output->set_content_type('application/json')->set_output(json_encode([
			'success' => (bool) $success,
			'id'      => $id,
			'message' => $message !== NULL
				? $message
				: ($success ? 'Tersimpan.' : 'Gagal menyimpan.'),
		]));
	}

	private function _json_deny($message, $status = 403)
	{
		$this->output->set_status_header($status);
		$this->output->set_content_type('application/json')->set_output(json_encode([
			'success' => false,
			'message' => $message,
		]));
	}

	private function _json_invalid($errors)
	{
		$this->output->set_content_type('application/json')->set_output(json_encode([
			'success' => false,
			'message' => 'Opss validation failed',
			'errors'  => $errors,
		]));
	}
}
