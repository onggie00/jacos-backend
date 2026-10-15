<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Template Controller (Fase 6A)
*| --------------------------------------------------------------------------
*| Master template persyaratan karier (career_requirement_templates).
*| Aturan D12: template yang pernah dipakai lowongan TIDAK boleh dihapus
*| — hanya dinonaktifkan. Semua mutasi dicatat ke career_audit_logs.
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

		$q      = $this->input->get('q');
		$status = $this->input->get('status');

		$this->data['q']        = $q;
		$this->data['status']   = $status;
		$this->data['templates']      = $this->model_career_template->career_list($q, $status, $this->limit_page, $offset);
		$this->data['template_counts'] = $this->model_career_template->career_count($q, $status);

		$config = [
			'base_url'    => 'administrator/career_template/index/',
			'total_rows'  => $this->data['template_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Master Persyaratan');
		$this->render('backend/standart/administrator/career_template/career_template_list', $this->data);
	}

	/** Form tambah template. */
	public function add()
	{
		$this->is_allowed('career_template_add');

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

		$this->db->trans_start();
		$id = $this->model_career_template->store_template($save_data);
		career_audit('create_template', 'career_requirement_template', $id, [
			'name' => $save_data['name'],
			'is_active' => $save_data['is_active'],
		]);
		$this->db->trans_complete();

		return $this->_json_done($id !== NULL && $id > 0, (int) $id);
	}

	/** Form ubah template. */
	public function edit($id)
	{
		$this->is_allowed('career_template_update');

		$this->data['template'] = $this->model_career_template->find_template($id);
		if (!$this->data['template']) {
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
		$ok = $this->model_career_template->change_template($id, $save_data);
		career_audit('update_template', 'career_requirement_template', $id, [
			'before' => ['name' => $old->name, 'is_active' => (int) $old->is_active],
			'after'  => ['name' => $save_data['name'], 'is_active' => $save_data['is_active']],
		]);
		$this->db->trans_complete();

		return $this->_json_done($ok, (int) $id);
	}

	/**
	 * Hapus template (GET link dgn konfirmasi di list).
	 * Hanya yang BELUM pernah dipakai lowongan (D12).
	 */
	public function delete($id = null)
	{
		if (!$this->is_allowed('career_template_delete', false)) {
			set_message(cclang('sorry_you_do_not_have_permission_to_access'), 'error');
			redirect('administrator/career_template', 'refresh');
		}

		$old = $this->model_career_template->find_template($id);
		if (!$old) {
			set_message('Template tidak ditemukan.', 'error');
			redirect('administrator/career_template', 'refresh');
		}

		if ($this->model_career_template->delete_unused($id)) {
			career_audit('delete_template', 'career_requirement_template', $id, [
				'name' => $old->name,
			]);
			set_message('Template dihapus.', 'success');
		} else {
			// D12: template dipakai lowongan → nonaktifkan saja.
			set_message('Template sudah pernah dipakai lowongan dan tidak boleh dihapus — nonaktifkan saja.', 'error');
		}

		redirect('administrator/career_template', 'refresh');
	}

	private function _json_done($success, $id = NULL)
	{
		echo json_encode([
			'success' => (bool) $success,
			'id'      => $id,
			'message' => $success ? cclang('data_not_change') : 'Gagal menyimpan.',
			'redirect' => $success ? base_url('administrator/career_template') : NULL,
		]);
		exit;
	}

	private function _json_deny($message)
	{
		echo json_encode(['success' => false, 'message' => $message]);
		exit;
	}

	private function _json_invalid($errors)
	{
		echo json_encode([
			'success' => false,
			'message' => 'Opss validation failed',
			'errors'  => $errors,
		]);
		exit;
	}
}
