<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kontak Labschool Controller
*| --------------------------------------------------------------------------
*| Kontak Labschool site
*|
*/
class Kontak_labschool extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kontak_labschool');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kontak Labschools
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kontak_labschool_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kontak_labschools'] = $this->model_kontak_labschool->get($filter, $field, $this->limit_page, $offset);
		$this->data['kontak_labschool_counts'] = $this->model_kontak_labschool->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kontak_labschool/index/',
			'total_rows'   => $this->model_kontak_labschool->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kontak Labschool List');
		$this->render('backend/standart/administrator/kontak_labschool/kontak_labschool_list', $this->data);
	}
	
	/**
	* Add new kontak_labschools
	*
	*/
	public function add()
	{
		$this->is_allowed('kontak_labschool_add');

		$this->template->title('Kontak Labschool New');
		$this->render('backend/standart/administrator/kontak_labschool/kontak_labschool_add', $this->data);
	}

	/**
	* Add New Kontak Labschools
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kontak_labschool_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('tipe_kontak', 'Tipe Kontak', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kontak', 'Kontak', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'tipe_kontak' => $this->input->post('tipe_kontak'),
				'kontak' => $this->input->post('kontak'),
			];

			
			$save_kontak_labschool = $this->model_kontak_labschool->store($save_data);
            

			if ($save_kontak_labschool) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kontak_labschool;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kontak_labschool/edit/' . $save_kontak_labschool, 'Edit Kontak Labschool'),
						anchor('administrator/kontak_labschool', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kontak_labschool/edit/' . $save_kontak_labschool, 'Edit Kontak Labschool')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kontak_labschool');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kontak_labschool');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Kontak Labschools
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kontak_labschool_update');

		$this->data['kontak_labschool'] = $this->model_kontak_labschool->find($id);

		$this->template->title('Kontak Labschool Update');
		$this->render('backend/standart/administrator/kontak_labschool/kontak_labschool_update', $this->data);
	}

	/**
	* Update Kontak Labschools
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kontak_labschool_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('tipe_kontak', 'Tipe Kontak', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kontak', 'Kontak', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'tipe_kontak' => $this->input->post('tipe_kontak'),
				'kontak' => $this->input->post('kontak'),
			];

			
			$save_kontak_labschool = $this->model_kontak_labschool->change($id, $save_data);

			if ($save_kontak_labschool) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kontak_labschool', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kontak_labschool');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kontak_labschool');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Kontak Labschools
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kontak_labschool_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'kontak_labschool'), 'success');
        } else {
            set_message(cclang('error_delete', 'kontak_labschool'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kontak Labschools
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kontak_labschool_view');

		$this->data['kontak_labschool'] = $this->model_kontak_labschool->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kontak Labschool Detail');
		$this->render('backend/standart/administrator/kontak_labschool/kontak_labschool_view', $this->data);
	}
	
	/**
	* delete Kontak Labschools
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kontak_labschool = $this->model_kontak_labschool->find($id);

		
		
		return $this->model_kontak_labschool->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kontak_labschool_export');

		$this->model_kontak_labschool->export('kontak_labschool', 'kontak_labschool');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kontak_labschool_export');

		$this->model_kontak_labschool->pdf('kontak_labschool', 'kontak_labschool');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kontak_labschool_export');

		$table = $title = 'kontak_labschool';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kontak_labschool->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	
}


/* End of file kontak_labschool.php */
/* Location: ./application/controllers/administrator/Kontak Labschool.php */