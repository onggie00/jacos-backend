<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mhcu_skala extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_mhcu_skala');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('mhcu_skala_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort = $this->input->get('s');
		$sort_type = $this->input->get('d');

		$multi_filters = $this->_parse_filters();
		$has_multi_filter = !empty($multi_filters);

		if ($has_multi_filter) {
			$this->data['mhcu_skalas'] = $this->model_mhcu_skala->get(null, null, $this->limit_page, $offset, array(), $sort, $sort_type, $multi_filters);
			$this->data['mhcu_skala_counts'] = $this->model_mhcu_skala->count_all(null, null, $multi_filters);
		} else {
			$this->data['mhcu_skalas'] = $this->model_mhcu_skala->get($filter, $field, $this->limit_page, $offset, array(), $sort, $sort_type);
			$this->data['mhcu_skala_counts'] = $this->model_mhcu_skala->count_all($filter, $field);
		}

		$config = array(
			'base_url'     => 'administrator/mhcu_skala/index/',
			'total_rows'   => $has_multi_filter ? $this->model_mhcu_skala->count_all(null, null, $multi_filters) : $this->model_mhcu_skala->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);
		$this->data['multi_filters'] = $multi_filters;

		$this->template->title('Skala MHCU');
		$this->render('backend/standart/administrator/mhcu_skala/mhcu_skala_list', $this->data);
	}

	private function _parse_filters()
	{
		$fields    = $this->input->get('ff');
		$operators = $this->input->get('fo');
		$values    = $this->input->get('fv');

		if (!is_array($fields) || !is_array($operators) || !is_array($values)) {
			return array();
		}

		$filters = array();
		for ($i = 0; $i < count($fields); $i++) {
			if (!empty($fields[$i]) && !empty($values[$i])) {
				$filters[] = array(
					'field'    => $fields[$i],
					'operator' => isset($operators[$i]) ? $operators[$i] : 'contains',
					'value'    => $values[$i],
				);
			}
		}
		return $filters;
	}

	public function add_save()
	{
		if (!$this->is_allowed('mhcu_skala_add', false)) {
			echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
			exit;
		}

		$this->form_validation->set_rules('kode_skala', 'Kode Skala', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('nama_skala', 'Nama Skala', 'trim|required|max_length[50]');

		if ($this->form_validation->run()) {
			$save_data = array(
				'kode_skala' => $this->input->post('kode_skala'),
				'nama_skala' => $this->input->post('nama_skala'),
			);
		
			$save = $this->model_mhcu_skala->store($save_data);

			if ($save) {
				echo json_encode(array('success' => true, 'message' => 'Skala berhasil ditambahkan'));
			} else {
				echo json_encode(array('success' => false, 'message' => cclang('data_not_change')));
			}
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}
	
	public function edit($id)
	{
		$this->is_allowed('mhcu_skala_update');
		$this->data['mhcu_skala'] = $this->model_mhcu_skala->find($id);
		$this->data['skala_options'] = $this->model_mhcu_skala->get_options($id);
		$this->template->title('Edit Skala');
		$this->render('backend/standart/administrator/mhcu_skala/mhcu_skala_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('mhcu_skala_update', false)) {
			echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
			exit;
		}
		
		$this->form_validation->set_rules('kode_skala', 'Kode Skala', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('nama_skala', 'Nama Skala', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
			$save_data = array(
				'kode_skala' => $this->input->post('kode_skala'),
				'nama_skala' => $this->input->post('nama_skala'),
			);
		
			$save = $this->model_mhcu_skala->change($id, $save_data);

			if ($save) {
				set_message(cclang('success_update_data_redirect', array()), 'success');
				echo json_encode(array('success' => true, 'redirect' => base_url('administrator/mhcu_skala')));
			} else {
				echo json_encode(array('success' => false, 'message' => cclang('data_not_change')));
			}
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}

	// ---- Nested: Skala Option ----

	public function add_option($id_skala)
	{
		$this->is_allowed('mhcu_skala_update');
		$this->form_validation->set_rules('label_option', 'Label Opsi', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('skor_value', 'Skor', 'trim|required|integer');
		$this->form_validation->set_rules('no_urut_option', 'No Urut', 'trim|required|integer');

		if ($this->form_validation->run()) {
			$save_data = array(
				'id_skala' => $id_skala,
				'label_option' => $this->input->post('label_option'),
				'skor_value' => $this->input->post('skor_value'),
				'no_urut_option' => $this->input->post('no_urut_option'),
			);

			$save = $this->model_mhcu_skala->save_option($save_data);

			echo json_encode($save
				? array('success' => true, 'message' => 'Opsi skala berhasil ditambahkan')
				: array('success' => false, 'message' => 'Gagal menambahkan opsi'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}

	public function edit_option($id_skala, $id_option)
	{
		$this->is_allowed('mhcu_skala_update');
		$this->form_validation->set_rules('label_option', 'Label Opsi', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('skor_value', 'Skor', 'trim|required|integer');
		$this->form_validation->set_rules('no_urut_option', 'No Urut', 'trim|required|integer');

		if ($this->form_validation->run()) {
			$save_data = array(
				'label_option' => $this->input->post('label_option'),
				'skor_value' => $this->input->post('skor_value'),
				'no_urut_option' => $this->input->post('no_urut_option'),
			);

			$save = $this->model_mhcu_skala->update_option($id_option, $save_data);

			echo json_encode($save
				? array('success' => true, 'message' => 'Opsi skala berhasil diupdate')
				: array('success' => false, 'message' => 'Tidak ada perubahan'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}

	public function delete_option($id_option)
	{
		$this->is_allowed('mhcu_skala_update');
		$remove = $this->model_mhcu_skala->delete_option($id_option);
		echo json_encode($remove
			? array('success' => true, 'message' => 'Opsi berhasil dihapus')
			: array('success' => false, 'message' => 'Gagal menghapus opsi'));
	}

	public function get_detail($id)
	{
		if (!$this->is_allowed('mhcu_skala_view', false)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$skala = $this->model_mhcu_skala->find($id);

		if ($skala) {
			$skala->options = $this->model_mhcu_skala->get_options($id);
			$this->output->set_content_type('application/json')->set_output(json_encode($skala));
		} else {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Data tidak ditemukan')));
		}
	}
	
	public function delete($id = null)
	{
		$this->is_allowed('mhcu_skala_delete');
		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$this->model_mhcu_skala->delete_options_by_skala($id);
			$remove = $this->model_mhcu_skala->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$this->model_mhcu_skala->delete_options_by_skala($id);
				$remove = $this->model_mhcu_skala->remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'Skala MHCU'), 'success');
        } else {
            set_message(cclang('error_delete', 'Skala MHCU'), 'error');
        }

		redirect_back();
	}
	
}

/* End of file Mhcu_skala.php */
