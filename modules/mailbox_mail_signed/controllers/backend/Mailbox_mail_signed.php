<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Mailbox Mail Signed Controller
*| --------------------------------------------------------------------------
*| Mailbox Mail Signed site
*|
*/
class Mailbox_mail_signed extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mailbox_mail_signed');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Mailbox Mail Signeds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mailbox_mail_signed_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['mailbox_mail_signeds'] = $this->model_mailbox_mail_signed->get($filter, $field, $this->limit_page, $offset);
		$this->data['mailbox_mail_signed_counts'] = $this->model_mailbox_mail_signed->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/mailbox_mail_signed/index/',
			'total_rows'   => $this->model_mailbox_mail_signed->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Mailbox Mail Signed List');
		$this->render('backend/standart/administrator/mailbox_mail_signed/mailbox_mail_signed_list', $this->data);
	}
	
	/**
	* Add new mailbox_mail_signeds
	*
	*/
	public function add()
	{
		$this->is_allowed('mailbox_mail_signed_add');

		$this->template->title('Mailbox Mail Signed New');
		$this->render('backend/standart/administrator/mailbox_mail_signed/mailbox_mail_signed_add', $this->data);
	}

	/**
	* Add New Mailbox Mail Signeds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('mailbox_mail_signed_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_mail', 'Mail', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('sign_name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('sign_position', 'Position', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('mailbox_mail_signed_sign_signature_name', 'Signature File', 'trim|max_length[255]');
		$this->form_validation->set_rules('sign_order', 'Order', 'trim|required|max_length[2]');
		

		if ($this->form_validation->run()) {
			$mailbox_mail_signed_sign_signature_uuid = $this->input->post('mailbox_mail_signed_sign_signature_uuid');
			$mailbox_mail_signed_sign_signature_name = $this->input->post('mailbox_mail_signed_sign_signature_name');
		
			$save_data = [
				'id_mail' => $this->input->post('id_mail'),
				'sign_name' => $this->input->post('sign_name'),
				'sign_position' => $this->input->post('sign_position'),
				'sign_order' => $this->input->post('sign_order'),
			];

			if (!is_dir(FCPATH . '/uploads/mailbox_mail_signed/')) {
				mkdir(FCPATH . '/uploads/mailbox_mail_signed/');
			}

			if (!empty($mailbox_mail_signed_sign_signature_name)) {
				$mailbox_mail_signed_sign_signature_name_copy = date('YmdHis') . '-' . $mailbox_mail_signed_sign_signature_name;

				rename(FCPATH . 'uploads/tmp/' . $mailbox_mail_signed_sign_signature_uuid . '/' . $mailbox_mail_signed_sign_signature_name, 
						FCPATH . 'uploads/mailbox_mail_signed/' . $mailbox_mail_signed_sign_signature_name_copy);

				if (!is_file(FCPATH . '/uploads/mailbox_mail_signed/' . $mailbox_mail_signed_sign_signature_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['sign_signature'] = $mailbox_mail_signed_sign_signature_name_copy;
			}
		
			
			$save_mailbox_mail_signed = $this->model_mailbox_mail_signed->store($save_data);
            

			if ($save_mailbox_mail_signed) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_mailbox_mail_signed;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/mailbox_mail_signed/edit/' . $save_mailbox_mail_signed, 'Edit Mailbox Mail Signed'),
						anchor('administrator/mailbox_mail_signed', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/mailbox_mail_signed/edit/' . $save_mailbox_mail_signed, 'Edit Mailbox Mail Signed')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_mail_signed');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_mail_signed');
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
	* Update view Mailbox Mail Signeds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('mailbox_mail_signed_update');

		$this->data['mailbox_mail_signed'] = $this->model_mailbox_mail_signed->find($id);

		$this->template->title('Mailbox Mail Signed Update');
		$this->render('backend/standart/administrator/mailbox_mail_signed/mailbox_mail_signed_update', $this->data);
	}

	/**
	* Update Mailbox Mail Signeds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('mailbox_mail_signed_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_mail', 'Mail', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('sign_name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('sign_position', 'Position', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('mailbox_mail_signed_sign_signature_name', 'Signature File', 'trim|max_length[255]');
		$this->form_validation->set_rules('sign_order', 'Order', 'trim|required|max_length[2]');
		
		if ($this->form_validation->run()) {
			$mailbox_mail_signed_sign_signature_uuid = $this->input->post('mailbox_mail_signed_sign_signature_uuid');
			$mailbox_mail_signed_sign_signature_name = $this->input->post('mailbox_mail_signed_sign_signature_name');
		
			$save_data = [
				'id_mail' => $this->input->post('id_mail'),
				'sign_name' => $this->input->post('sign_name'),
				'sign_position' => $this->input->post('sign_position'),
				'sign_order' => $this->input->post('sign_order'),
			];

			if (!is_dir(FCPATH . '/uploads/mailbox_mail_signed/')) {
				mkdir(FCPATH . '/uploads/mailbox_mail_signed/');
			}

			if (!empty($mailbox_mail_signed_sign_signature_uuid)) {
				$mailbox_mail_signed_sign_signature_name_copy = date('YmdHis') . '-' . $mailbox_mail_signed_sign_signature_name;

				rename(FCPATH . 'uploads/tmp/' . $mailbox_mail_signed_sign_signature_uuid . '/' . $mailbox_mail_signed_sign_signature_name, 
						FCPATH . 'uploads/mailbox_mail_signed/' . $mailbox_mail_signed_sign_signature_name_copy);

				if (!is_file(FCPATH . '/uploads/mailbox_mail_signed/' . $mailbox_mail_signed_sign_signature_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['sign_signature'] = $mailbox_mail_signed_sign_signature_name_copy;
			}
		
			
			$save_mailbox_mail_signed = $this->model_mailbox_mail_signed->change($id, $save_data);

			if ($save_mailbox_mail_signed) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/mailbox_mail_signed', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_mail_signed');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_mail_signed');
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
	* delete Mailbox Mail Signeds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mailbox_mail_signed_delete');

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
            set_message(cclang('has_been_deleted', 'mailbox_mail_signed'), 'success');
        } else {
            set_message(cclang('error_delete', 'mailbox_mail_signed'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Mailbox Mail Signeds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('mailbox_mail_signed_view');

		$this->data['mailbox_mail_signed'] = $this->model_mailbox_mail_signed->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Mailbox Mail Signed Detail');
		$this->render('backend/standart/administrator/mailbox_mail_signed/mailbox_mail_signed_view', $this->data);
	}
	
	/**
	* delete Mailbox Mail Signeds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$mailbox_mail_signed = $this->model_mailbox_mail_signed->find($id);

		if (!empty($mailbox_mail_signed->sign_signature)) {
			$path = FCPATH . '/uploads/mailbox_mail_signed/' . $mailbox_mail_signed->sign_signature;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_mailbox_mail_signed->remove($id);
	}
	
	/**
	* Upload Image Mailbox Mail Signed	* 
	* @return JSON
	*/
	public function upload_sign_signature_file()
	{
		if (!$this->is_allowed('mailbox_mail_signed_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'mailbox_mail_signed',
		]);
	}

	/**
	* Delete Image Mailbox Mail Signed	* 
	* @return JSON
	*/
	public function delete_sign_signature_file($uuid)
	{
		if (!$this->is_allowed('mailbox_mail_signed_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'sign_signature', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'mailbox_mail_signed',
            'primary_key'       => 'id_mail_signed',
            'upload_path'       => 'uploads/mailbox_mail_signed/'
        ]);
	}

	/**
	* Get Image Mailbox Mail Signed	* 
	* @return JSON
	*/
	public function get_sign_signature_file($id)
	{
		if (!$this->is_allowed('mailbox_mail_signed_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$mailbox_mail_signed = $this->model_mailbox_mail_signed->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'sign_signature', 
            'table_name'        => 'mailbox_mail_signed',
            'primary_key'       => 'id_mail_signed',
            'upload_path'       => 'uploads/mailbox_mail_signed/',
            'delete_endpoint'   => 'administrator/mailbox_mail_signed/delete_sign_signature_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('mailbox_mail_signed_export');

		$this->model_mailbox_mail_signed->export('mailbox_mail_signed', 'mailbox_mail_signed');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('mailbox_mail_signed_export');

		$this->model_mailbox_mail_signed->pdf('mailbox_mail_signed', 'mailbox_mail_signed');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('mailbox_mail_signed_export');

		$table = $title = 'mailbox_mail_signed';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_mailbox_mail_signed->find($id);
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


/* End of file mailbox_mail_signed.php */
/* Location: ./application/controllers/administrator/Mailbox Mail Signed.php */