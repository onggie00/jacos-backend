<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Kelas Ft Controller
*| --------------------------------------------------------------------------
*| Kelas Ft site
*|
*/
class Kelas_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kelas_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	public function index($offset = 0)
	{
		$this->is_allowed('kelas_ft_list');

		// Build filter array
		$filter = array();
		$q = trim($this->input->get('q'));
		$field = trim($this->input->get('f'));
		$id_tingkatan = $this->input->get('id_tingkatan');
		
		if ($q !== '' && $q !== false) {
			$filter['q'] = $q;
			$filter['f'] = $field;
		}
		if ($id_tingkatan !== '' && $id_tingkatan !== false) {
			$filter['id_tingkatan'] = $id_tingkatan;
		}

		// Get list tingkatan for filter dropdown
		$this->data['list_tingkatan'] = $this->mymodel->getall('tingkatan_ft');

		$this->data['kelas_fts'] = $this->model_kelas_ft->get($filter, $this->limit_page, $offset);
		$this->data['kelas_ft_counts'] = $this->model_kelas_ft->count_all($filter);

		$config = array(
			'base_url'     => 'administrator/kelas_ft/index/',
			'total_rows'   => $this->data['kelas_ft_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);
		$this->data['filter'] = $filter;

		$this->template->title('Kelas Ft List');
		$this->render('backend/standart/administrator/kelas_ft/kelas_ft_list', $this->data);
	}
	
	public function add()
	{
		$this->is_allowed('kelas_ft_add');
		$this->template->title('Kelas Ft New');
		$this->render('backend/standart/administrator/kelas_ft/kelas_ft_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('kelas_ft_add', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|max_length[255]');

		if ($this->form_validation->run()) {
			$get_tingkatan = $this->mymodel->getbywhere("tingkatan_ft", "id_tingkatan_ft", $this->input->post('id_tingkatan'), "row");
			$save_data = [
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nama_kelas' => $this->input->post('nama_kelas'),
				'label' => $get_tingkatan->label." ".$this->input->post('nama_kelas'),
			];
			$save_kelas_ft = $this->model_kelas_ft->store($save_data);
			if ($save_kelas_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $save_kelas_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kelas_ft/edit/' . $save_kelas_ft, 'Edit Kelas Ft'),
						anchor('administrator/kelas_ft', ' Go back to list')
					]);
				} else {
					set_message(cclang('success_save_data_redirect', [anchor('administrator/kelas_ft/edit/' . $save_kelas_ft, 'Edit Kelas Ft')]), 'success');
					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kelas_ft');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}
		echo json_encode($this->data);
	}
	
	public function edit($id)
	{
		$this->is_allowed('kelas_ft_update');
		$this->data['kelas_ft'] = $this->model_kelas_ft->find($id);
		$this->template->title('Kelas Ft Update');
		$this->render('backend/standart/administrator/kelas_ft/kelas_ft_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('kelas_ft_update', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}
		
		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$get_tingkatan = $this->mymodel->getbywhere("tingkatan_ft", "id_tingkatan_ft", $this->input->post('id_tingkatan'), "row");
			$save_data = [
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nama_kelas' => $this->input->post('nama_kelas'),
				'label' => $get_tingkatan->label." ".$this->input->post('nama_kelas'),
			];
			$save_kelas_ft = $this->model_kelas_ft->change($id, $save_data);
			if ($save_kelas_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $id;
					$this->data['message'] = cclang('success_update_data_stay', [anchor('administrator/kelas_ft', ' Go back to list')]);
				} else {
					set_message(cclang('success_update_data_redirect', []), 'success');
					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kelas_ft');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}
		echo json_encode($this->data);
	}
	
	public function delete($id = null)
	{
		$this->is_allowed('kelas_ft_delete');
		$this->load->helper('file');
		$arr_id = $this->input->get('id');
		$remove = false;
		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}
		if ($remove) {
            set_message(cclang('has_been_deleted', 'kelas_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'kelas_ft'), 'error');
        }
		redirect_back();
	}

	public function view($id)
	{
		$this->is_allowed('kelas_ft_view');
		$this->data['kelas_ft'] = $this->model_kelas_ft->join_avaiable()->filter_avaiable()->find($id);
		$this->template->title('Kelas Ft Detail');
		$this->render('backend/standart/administrator/kelas_ft/kelas_ft_view', $this->data);
	}
	
	private function _remove($id)
	{
		$kelas_ft = $this->model_kelas_ft->find($id);
		return $this->model_kelas_ft->remove($id);
	}
	
	public function export()
	{
		$this->is_allowed('kelas_ft_export');
		$this->model_kelas_ft->export('kelas_ft', 'kelas_ft');
	}

	public function export_pdf()
	{
		$this->is_allowed('kelas_ft_export');
		$this->model_kelas_ft->pdf('kelas_ft', 'kelas_ft');
	}

	public function single_pdf($id = null)
	{
		$this->is_allowed('kelas_ft_export');
		$table = $title = 'kelas_ft';
		$this->load->library('HtmlPdf');
        $config = array('orientation' => 'p', 'format' => 'a4', 'marges' => array(5, 5, 5, 5));
        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight');
        $result = $this->db->get($table);
        $data = $this->model_kelas_ft->find($id);
        $fields = $result->list_fields();
        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', ['data' => $data, 'fields' => $fields, 'title' => $title], TRUE);
        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}
	
}

/* End of file kelas_ft.php */
/* Location: ./application/controllers/administrator/Kelas Ft.php */
