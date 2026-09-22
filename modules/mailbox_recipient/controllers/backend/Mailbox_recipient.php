<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Mailbox Recipient Controller
*| --------------------------------------------------------------------------
*| Mailbox Recipient site
*|
*/
class Mailbox_recipient extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mailbox_recipient');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Mailbox Recipients
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mailbox_recipient_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['mailbox_recipients'] = $this->model_mailbox_recipient->get($filter, $field, $this->limit_page, $offset);
		$this->data['mailbox_recipient_counts'] = $this->model_mailbox_recipient->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/mailbox_recipient/index/',
			'total_rows'   => $this->model_mailbox_recipient->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Mailbox Recipient List');
		$this->render('backend/standart/administrator/mailbox_recipient/mailbox_recipient_list', $this->data);
	}
	
	/**
	* Add new mailbox_recipients
	*
	*/
	public function add()
	{
		$this->is_allowed('mailbox_recipient_add');

		$this->template->title('Mailbox Recipient New');
		$this->render('backend/standart/administrator/mailbox_recipient/mailbox_recipient_add', $this->data);
	}

	/**
	* Add New Mailbox Recipients
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('mailbox_recipient_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_mail', 'Mail', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|max_length[30]');
		$this->form_validation->set_rules('nama_lengkap', 'Name', 'trim|max_length[255]');
		$this->form_validation->set_rules('role', 'Role', 'trim|max_length[30]');
		$this->form_validation->set_rules('is_read', 'Is Read?', 'trim|max_length[1]');
		$this->form_validation->set_rules('is_important', 'Marked As Important?', 'trim|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_mail' => $this->input->post('id_mail'),
				'npp' => $this->input->post('npp'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'role' => $this->input->post('role'),
				'is_read' => $this->input->post('is_read'),
				'is_important' => $this->input->post('is_important'),
			];

			
			$save_mailbox_recipient = $this->model_mailbox_recipient->store($save_data);
            

			if ($save_mailbox_recipient) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_mailbox_recipient;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/mailbox_recipient/edit/' . $save_mailbox_recipient, 'Edit Mailbox Recipient'),
						anchor('administrator/mailbox_recipient', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/mailbox_recipient/edit/' . $save_mailbox_recipient, 'Edit Mailbox Recipient')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_recipient');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_recipient');
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
	* Update view Mailbox Recipients
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('mailbox_recipient_update');

		$this->data['mailbox_recipient'] = $this->model_mailbox_recipient->find($id);

		$this->template->title('Mailbox Recipient Update');
		$this->render('backend/standart/administrator/mailbox_recipient/mailbox_recipient_update', $this->data);
	}

	/**
	* Update Mailbox Recipients
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('mailbox_recipient_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_mail', 'Mail', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|max_length[30]');
		$this->form_validation->set_rules('nama_lengkap', 'Name', 'trim|max_length[255]');
		$this->form_validation->set_rules('role', 'Role', 'trim|max_length[30]');
		$this->form_validation->set_rules('is_read', 'Is Read?', 'trim|max_length[1]');
		$this->form_validation->set_rules('is_important', 'Marked As Important?', 'trim|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_mail' => $this->input->post('id_mail'),
				'npp' => $this->input->post('npp'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'role' => $this->input->post('role'),
				'is_read' => $this->input->post('is_read'),
				'is_important' => $this->input->post('is_important'),
			];

			
			$save_mailbox_recipient = $this->model_mailbox_recipient->change($id, $save_data);

			if ($save_mailbox_recipient) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/mailbox_recipient', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_recipient');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_recipient');
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
	* delete Mailbox Recipients
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mailbox_recipient_delete');

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
            set_message(cclang('has_been_deleted', 'mailbox_recipient'), 'success');
        } else {
            set_message(cclang('error_delete', 'mailbox_recipient'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Mailbox Recipients
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('mailbox_recipient_view');

		$this->data['mailbox_recipient'] = $this->model_mailbox_recipient->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Mailbox Recipient Detail');
		$this->render('backend/standart/administrator/mailbox_recipient/mailbox_recipient_view', $this->data);
	}
	
	/**
	* delete Mailbox Recipients
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$mailbox_recipient = $this->model_mailbox_recipient->find($id);

		
		
		return $this->model_mailbox_recipient->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('mailbox_recipient_export');

		$this->model_mailbox_recipient->export('mailbox_recipient', 'mailbox_recipient');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('mailbox_recipient_export');

		$this->model_mailbox_recipient->pdf('mailbox_recipient', 'mailbox_recipient');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('mailbox_recipient_export');

		$table = $title = 'mailbox_recipient';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_mailbox_recipient->find($id);
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


/* End of file mailbox_recipient.php */
/* Location: ./application/controllers/administrator/Mailbox Recipient.php */