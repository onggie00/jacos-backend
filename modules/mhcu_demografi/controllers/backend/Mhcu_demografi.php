<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mhcu_demografi extends Admin	
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_mhcu_demografi');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('mhcu_demografi_list');
		$filter = $this->input->get('q'); $field = $this->input->get('f');
		$sort = $this->input->get('s'); $sort_type = $this->input->get('d');
		$multi_filters = $this->_parse_filters();
		$has_multi_filter = !empty($multi_filters);

		if ($has_multi_filter) {
			$this->data['mhcu_demografis'] = $this->model_mhcu_demografi->get(null, null, $this->limit_page, $offset, array(), $sort, $sort_type, $multi_filters);
			$this->data['mhcu_demografi_counts'] = $this->model_mhcu_demografi->count_all(null, null, $multi_filters);
		} else {
			$this->data['mhcu_demografis'] = $this->model_mhcu_demografi->get($filter, $field, $this->limit_page, $offset, array(), $sort, $sort_type);
			$this->data['mhcu_demografi_counts'] = $this->model_mhcu_demografi->count_all($filter, $field);
		}

		$this->data['pagination'] = $this->pagination(array(
			'base_url' => 'administrator/mhcu_demografi/index/',
			'total_rows' => $has_multi_filter ? $this->model_mhcu_demografi->count_all(null, null, $multi_filters) : $this->model_mhcu_demografi->count_all($filter, $field),
			'per_page' => $this->limit_page, 'uri_segment' => 4,
		));
		$this->data['multi_filters'] = $multi_filters;
		$this->template->title('Demografi MHCU');
		$this->render('backend/standart/administrator/mhcu_demografi/mhcu_demografi_list', $this->data);
	}

	private function _parse_filters()
	{
		$fields = $this->input->get('ff'); $operators = $this->input->get('fo'); $values = $this->input->get('fv');
		if (!is_array($fields) || !is_array($operators) || !is_array($values)) return array();
		$filters = array();
		for ($i = 0; $i < count($fields); $i++) {
			if (!empty($fields[$i]) && !empty($values[$i])) {
				$filters[] = array('field' => $fields[$i], 'operator' => isset($operators[$i]) ? $operators[$i] : 'contains', 'value' => $values[$i]);
			}
		}
		return $filters;
	}

	public function add_save()
	{
		if (!$this->is_allowed('mhcu_demografi_add', false)) { echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access'))); exit; }
		$this->form_validation->set_rules('demografi_kode', 'Kode', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('demografi_pertanyaan', 'Pertanyaan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('input_type', 'Tipe Input', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|integer');

		if ($this->form_validation->run()) {
			$save = $this->model_mhcu_demografi->store(array(
				'demografi_kode' => $this->input->post('demografi_kode'),
				'demografi_pertanyaan' => $this->input->post('demografi_pertanyaan'),
				'input_type' => $this->input->post('input_type'),
				'no_urut' => $this->input->post('no_urut'),
			));
			echo json_encode($save ? array('success' => true, 'message' => 'Pertanyaan berhasil ditambahkan') : array('success' => false, 'message' => cclang('data_not_change')));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}
	
	public function edit($id)
	{
		$this->is_allowed('mhcu_demografi_update');
		$this->data['mhcu_demografi'] = $this->model_mhcu_demografi->find($id);
		$this->data['demografi_options'] = $this->model_mhcu_demografi->get_options($id);
		$this->template->title('Edit Demografi');
		$this->render('backend/standart/administrator/mhcu_demografi/mhcu_demografi_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('mhcu_demografi_update', false)) { echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access'))); exit; }
		$this->form_validation->set_rules('demografi_kode', 'Kode', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('demografi_pertanyaan', 'Pertanyaan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('input_type', 'Tipe Input', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|integer');

		if ($this->form_validation->run()) {
			$save = $this->model_mhcu_demografi->change($id, array(
				'demografi_kode' => $this->input->post('demografi_kode'),
				'demografi_pertanyaan' => $this->input->post('demografi_pertanyaan'),
				'input_type' => $this->input->post('input_type'),
				'no_urut' => $this->input->post('no_urut'),
			));
			if ($save) { set_message(cclang('success_update_data_redirect', array()), 'success'); echo json_encode(array('success' => true, 'redirect' => base_url('administrator/mhcu_demografi'))); }
			else { echo json_encode(array('success' => false, 'message' => cclang('data_not_change'))); }
		} else { echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array())); }
	}

	// ---- Nested: Option ----
	public function add_option($id_pertanyaan)
	{
		$this->is_allowed('mhcu_demografi_update');
		$this->form_validation->set_rules('label_option', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('skor', 'Skor', 'trim|max_length[200]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|integer');
		if ($this->form_validation->run()) {
			$save = $this->model_mhcu_demografi->save_option(array('id_demografi_pertanyaan' => $id_pertanyaan, 'label_option' => $this->input->post('label_option'), 'skor' => $this->input->post('skor'), 'no_urut' => $this->input->post('no_urut')));
			echo json_encode($save ? array('success' => true, 'message' => 'Opsi berhasil ditambahkan') : array('success' => false, 'message' => 'Gagal'));
		} else { echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array())); }
	}

	public function edit_option($id_pertanyaan, $id_option)
	{
		$this->is_allowed('mhcu_demografi_update');
		$this->form_validation->set_rules('label_option', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('skor', 'Skor', 'trim|max_length[200]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|integer');
		if ($this->form_validation->run()) {
			$save = $this->model_mhcu_demografi->update_option($id_option, array('label_option' => $this->input->post('label_option'), 'skor' => $this->input->post('skor'), 'no_urut' => $this->input->post('no_urut')));
			echo json_encode($save ? array('success' => true, 'message' => 'Opsi berhasil diupdate') : array('success' => false, 'message' => 'Tidak ada perubahan'));
		} else { echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array())); }
	}

	public function delete_option($id_option)
	{
		$this->is_allowed('mhcu_demografi_update');
		$remove = $this->model_mhcu_demografi->delete_option($id_option);
		echo json_encode($remove ? array('success' => true, 'message' => 'Opsi berhasil dihapus') : array('success' => false, 'message' => 'Gagal'));
	}

	public function get_detail($id)
	{
		if (!$this->is_allowed('mhcu_demografi_view', false)) { $this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized'))); return; }
		$d = $this->model_mhcu_demografi->find($id);
		if ($d) { $d->options = $this->model_mhcu_demografi->get_options($id); $this->output->set_content_type('application/json')->set_output(json_encode($d)); }
		else { $this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Data tidak ditemukan'))); }
	}

	public function delete($id = null)
	{
		$this->is_allowed('mhcu_demografi_delete');
		$arr_id = $this->input->get('id'); $remove = false;
		if (!empty($id)) { $this->model_mhcu_demografi->delete_options_by_pertanyaan($id); $remove = $this->model_mhcu_demografi->remove($id); }
		elseif (count($arr_id) > 0) { foreach ($arr_id as $id) { $this->model_mhcu_demografi->delete_options_by_pertanyaan($id); $remove = $this->model_mhcu_demografi->remove($id); } }
		if ($remove) { set_message(cclang('has_been_deleted', 'Demografi MHCU'), 'success'); } else { set_message(cclang('error_delete', 'Demografi MHCU'), 'error'); }
		redirect_back();
	}
}
