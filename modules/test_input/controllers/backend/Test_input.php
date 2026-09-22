<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Test Input Controller
*| --------------------------------------------------------------------------
*| Test Input site
*|
*/
class Test_input extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_test_input');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Test Inputs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('test_input_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['test_inputs'] = $this->model_test_input->get($filter, $field, $this->limit_page, $offset);
		$this->data['test_input_counts'] = $this->model_test_input->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/test_input/index/',
			'total_rows'   => $this->model_test_input->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Test Input List');
		$this->render('backend/standart/administrator/test_input/test_input_list', $this->data);
	}
	
	/**
	* Add new test_inputs
	*
	*/
	public function add()
	{
		$this->is_allowed('test_input_add');

		$this->template->title('Test Input New');
		$this->render('backend/standart/administrator/test_input/test_input_add', $this->data);
	}

	/**
	* Add New Test Inputs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('test_input_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('multiple_select[]', 'Multiple Select', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'multiple_select' => implode(',', (array) $this->input->post('multiple_select')),
			];

			
			$save_test_input = $this->model_test_input->store($save_data);
            

			if ($save_test_input) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_test_input;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/test_input/edit/' . $save_test_input, 'Edit Test Input'),
						anchor('administrator/test_input', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/test_input/edit/' . $save_test_input, 'Edit Test Input')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/test_input');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/test_input');
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
	* Update view Test Inputs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('test_input_update');

		$this->data['test_input'] = $this->model_test_input->find($id);

		$this->template->title('Test Input Update');
		$this->render('backend/standart/administrator/test_input/test_input_update', $this->data);
	}

	/**
	* Update Test Inputs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('test_input_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('multiple_select[]', 'Multiple Select', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'multiple_select' => implode(',', (array) $this->input->post('multiple_select')),
			];

			
			$save_test_input = $this->model_test_input->change($id, $save_data);

			if ($save_test_input) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/test_input', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/test_input');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/test_input');
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
	* delete Test Inputs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('test_input_delete');

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
            set_message(cclang('has_been_deleted', 'test_input'), 'success');
        } else {
            set_message(cclang('error_delete', 'test_input'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Test Inputs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('test_input_view');

		$this->data['test_input'] = $this->model_test_input->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Test Input Detail');
		$this->render('backend/standart/administrator/test_input/test_input_view', $this->data);
	}
	
	/**
	* delete Test Inputs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$test_input = $this->model_test_input->find($id);

		
		
		return $this->model_test_input->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('test_input_export');

		$this->model_test_input->export('test_input', 'test_input');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('test_input_export');

		$this->model_test_input->pdf('test_input', 'test_input');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('test_input_export');

		$table = $title = 'test_input';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_test_input->find($id);
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


/* End of file test_input.php */
/* Location: ./application/controllers/administrator/Test Input.php */