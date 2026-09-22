<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Peminatan Ft Controller
*| --------------------------------------------------------------------------
*| Peminatan Ft site
*|
*/
class Peminatan_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_peminatan_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Peminatan Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('peminatan_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['peminatan_fts'] = $this->model_peminatan_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['peminatan_ft_counts'] = $this->model_peminatan_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/peminatan_ft/index/',
			'total_rows'   => $this->model_peminatan_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Peminatan France Track List');
		$this->render('backend/standart/administrator/peminatan_ft/peminatan_ft_list', $this->data);
	}
	
	/**
	* Add new peminatan_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('peminatan_ft_add');

		$this->template->title('Peminatan France Track New');
		$this->render('backend/standart/administrator/peminatan_ft/peminatan_ft_add', $this->data);
	}

	/**
	* Add New Peminatan Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('peminatan_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('peminatan', 'Peminatan', 'trim|required|max_length[200]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'peminatan' => $this->input->post('peminatan'),
			];

			
			$save_peminatan_ft = $this->model_peminatan_ft->store($save_data);
            

			if ($save_peminatan_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_peminatan_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/peminatan_ft/edit/' . $save_peminatan_ft, 'Edit Peminatan Ft'),
						anchor('administrator/peminatan_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/peminatan_ft/edit/' . $save_peminatan_ft, 'Edit Peminatan Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/peminatan_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/peminatan_ft');
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
	* Update view Peminatan Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('peminatan_ft_update');

		$this->data['peminatan_ft'] = $this->model_peminatan_ft->find($id);

		$this->template->title('Peminatan France Track Update');
		$this->render('backend/standart/administrator/peminatan_ft/peminatan_ft_update', $this->data);
	}

	/**
	* Update Peminatan Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('peminatan_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('peminatan', 'Peminatan', 'trim|required|max_length[200]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'peminatan' => $this->input->post('peminatan'),
			];

			
			$save_peminatan_ft = $this->model_peminatan_ft->change($id, $save_data);

			if ($save_peminatan_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/peminatan_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/peminatan_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/peminatan_ft');
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
	* delete Peminatan Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('peminatan_ft_delete');

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
            set_message(cclang('has_been_deleted', 'peminatan_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'peminatan_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Peminatan Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('peminatan_ft_view');

		$this->data['peminatan_ft'] = $this->model_peminatan_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Peminatan France Track Detail');
		$this->render('backend/standart/administrator/peminatan_ft/peminatan_ft_view', $this->data);
	}
	
	/**
	* delete Peminatan Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$peminatan_ft = $this->model_peminatan_ft->find($id);

		
		
		return $this->model_peminatan_ft->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('peminatan_ft_export');

		$this->model_peminatan_ft->export('peminatan_ft', 'peminatan_ft');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('peminatan_ft_export');

		$this->model_peminatan_ft->pdf('peminatan_ft', 'peminatan_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('peminatan_ft_export');

		$table = $title = 'peminatan_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_peminatan_ft->find($id);
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


/* End of file peminatan_ft.php */
/* Location: ./application/controllers/administrator/Peminatan Ft.php */