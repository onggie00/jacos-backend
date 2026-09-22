<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Mailbox Category Controller
*| --------------------------------------------------------------------------
*| Mailbox Category site
*|
*/
class Mailbox_category extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mailbox_category');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Mailbox Categorys
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mailbox_category_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['mailbox_categorys'] = $this->model_mailbox_category->get($filter, $field, $this->limit_page, $offset);
		$this->data['mailbox_category_counts'] = $this->model_mailbox_category->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/mailbox_category/index/',
			'total_rows'   => $this->model_mailbox_category->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Mailbox Category List');
		$this->render('backend/standart/administrator/mailbox_category/mailbox_category_list', $this->data);
	}
	
	/**
	* Add new mailbox_categorys
	*
	*/
	public function add()
	{
		$this->is_allowed('mailbox_category_add');

		$this->template->title('Mailbox Category New');
		$this->render('backend/standart/administrator/mailbox_category/mailbox_category_add', $this->data);
	}

	/**
	* Add New Mailbox Categorys
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('mailbox_category_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[30]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'category' => $this->input->post('category'),
			];

			
			$save_mailbox_category = $this->model_mailbox_category->store($save_data);
            

			if ($save_mailbox_category) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_mailbox_category;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/mailbox_category/edit/' . $save_mailbox_category, 'Edit Mailbox Category'),
						anchor('administrator/mailbox_category', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/mailbox_category/edit/' . $save_mailbox_category, 'Edit Mailbox Category')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_category');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_category');
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
	* Update view Mailbox Categorys
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('mailbox_category_update');

		$this->data['mailbox_category'] = $this->model_mailbox_category->find($id);

		$this->template->title('Mailbox Category Update');
		$this->render('backend/standart/administrator/mailbox_category/mailbox_category_update', $this->data);
	}

	/**
	* Update Mailbox Categorys
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('mailbox_category_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[30]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'category' => $this->input->post('category'),
			];

			
			$save_mailbox_category = $this->model_mailbox_category->change($id, $save_data);

			if ($save_mailbox_category) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/mailbox_category', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_category');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_category');
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
	* delete Mailbox Categorys
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mailbox_category_delete');

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
            set_message(cclang('has_been_deleted', 'mailbox_category'), 'success');
        } else {
            set_message(cclang('error_delete', 'mailbox_category'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Mailbox Categorys
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('mailbox_category_view');

		$this->data['mailbox_category'] = $this->model_mailbox_category->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Mailbox Category Detail');
		$this->render('backend/standart/administrator/mailbox_category/mailbox_category_view', $this->data);
	}
	
	/**
	* delete Mailbox Categorys
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$mailbox_category = $this->model_mailbox_category->find($id);

		
		
		return $this->model_mailbox_category->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('mailbox_category_export');

		$this->model_mailbox_category->export('mailbox_category', 'mailbox_category');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('mailbox_category_export');

		$this->model_mailbox_category->pdf('mailbox_category', 'mailbox_category');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('mailbox_category_export');

		$table = $title = 'mailbox_category';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_mailbox_category->find($id);
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


/* End of file mailbox_category.php */
/* Location: ./application/controllers/administrator/Mailbox Category.php */