<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jenis Lomba Controller
*| --------------------------------------------------------------------------
*| Jenis Lomba site
*|
*/
class Jenis_lomba extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jenis_lomba');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jenis Lombas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jenis_lomba_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jenis_lombas'] = $this->model_jenis_lomba->get($filter, $field, $this->limit_page, $offset);
		$this->data['jenis_lomba_counts'] = $this->model_jenis_lomba->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jenis_lomba/index/',
			'total_rows'   => $this->model_jenis_lomba->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jenis Lomba List');
		$this->render('backend/standart/administrator/jenis_lomba/jenis_lomba_list', $this->data);
	}
	
	/**
	* Add new jenis_lombas
	*
	*/
	public function add()
	{
		$this->is_allowed('jenis_lomba_add');

		$this->template->title('Jenis Lomba New');
		$this->render('backend/standart/administrator/jenis_lomba/jenis_lomba_add', $this->data);
	}

	/**
	* Add New Jenis Lombas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jenis_lomba_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenis_lomba', 'Jenis Lomba', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_lomba' => $this->input->post('jenis_lomba'),
			];

			
			$save_jenis_lomba = $this->model_jenis_lomba->store($save_data);
            

			if ($save_jenis_lomba) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jenis_lomba;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jenis_lomba/edit/' . $save_jenis_lomba, 'Edit Jenis Lomba'),
						anchor('administrator/jenis_lomba', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jenis_lomba/edit/' . $save_jenis_lomba, 'Edit Jenis Lomba')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_lomba');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_lomba');
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
	* Update view Jenis Lombas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jenis_lomba_update');

		$this->data['jenis_lomba'] = $this->model_jenis_lomba->find($id);

		$this->template->title('Jenis Lomba Update');
		$this->render('backend/standart/administrator/jenis_lomba/jenis_lomba_update', $this->data);
	}

	/**
	* Update Jenis Lombas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jenis_lomba_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenis_lomba', 'Jenis Lomba', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_lomba' => $this->input->post('jenis_lomba'),
			];

			
			$save_jenis_lomba = $this->model_jenis_lomba->change($id, $save_data);

			if ($save_jenis_lomba) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jenis_lomba', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_lomba');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_lomba');
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
	* delete Jenis Lombas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jenis_lomba_delete');

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
            set_message(cclang('has_been_deleted', 'jenis_lomba'), 'success');
        } else {
            set_message(cclang('error_delete', 'jenis_lomba'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jenis Lombas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jenis_lomba_view');

		$this->data['jenis_lomba'] = $this->model_jenis_lomba->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jenis Lomba Detail');
		$this->render('backend/standart/administrator/jenis_lomba/jenis_lomba_view', $this->data);
	}
	
	/**
	* delete Jenis Lombas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jenis_lomba = $this->model_jenis_lomba->find($id);

		
		
		return $this->model_jenis_lomba->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jenis_lomba_export');

		$this->model_jenis_lomba->export('jenis_lomba', 'jenis_lomba');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jenis_lomba_export');

		$this->model_jenis_lomba->pdf('jenis_lomba', 'jenis_lomba');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jenis_lomba_export');

		$table = $title = 'jenis_lomba';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jenis_lomba->find($id);
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


/* End of file jenis_lomba.php */
/* Location: ./application/controllers/administrator/Jenis Lomba.php */