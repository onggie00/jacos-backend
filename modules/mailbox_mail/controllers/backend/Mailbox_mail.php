<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Mailbox Mail Controller
*| --------------------------------------------------------------------------
*| Mailbox Mail site
*|
*/
class Mailbox_mail extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mailbox_mail');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Mailbox Mails
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mailbox_mail_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['mailbox_mails'] = $this->model_mailbox_mail->get($filter, $field, $this->limit_page, $offset);
		$this->data['mailbox_mail_counts'] = $this->model_mailbox_mail->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/mailbox_mail/index/',
			'total_rows'   => $this->model_mailbox_mail->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Mailbox Mail List');
		$this->render('backend/standart/administrator/mailbox_mail/mailbox_mail_list', $this->data);
	}
	
	/**
	* Add new mailbox_mails
	*
	*/
	public function add()
	{
		$this->is_allowed('mailbox_mail_add');

		$this->template->title('Mailbox Mail New');
		$this->render('backend/standart/administrator/mailbox_mail/mailbox_mail_add', $this->data);
	}

	/**
	* Add New Mailbox Mails
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('mailbox_mail_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('mailbox_category', 'Category', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('mailbox_title', 'Title', 'trim|max_length[255]');
		$this->form_validation->set_rules('is_confidential', 'Is Confidential?', 'trim|max_length[1]');
		$this->form_validation->set_rules('mail_number', 'Mail Number', 'trim|max_length[150]');
		$this->form_validation->set_rules('mail_address', 'Mail Address', 'trim|max_length[130]');
		$this->form_validation->set_rules('mailbox_mail_mail_header_file_name', 'Mail Header (KOP)', 'trim|max_length[255]');
		$this->form_validation->set_rules('mail_show_date', 'Show Date', 'trim');
		$this->form_validation->set_rules('mail_show_time', 'Show Time', 'trim');
		

		if ($this->form_validation->run()) {
			$mailbox_mail_mail_header_file_uuid = $this->input->post('mailbox_mail_mail_header_file_uuid');
			$mailbox_mail_mail_header_file_name = $this->input->post('mailbox_mail_mail_header_file_name');
		
			$save_data = [
				'mailbox_category' => $this->input->post('mailbox_category'),
				'mailbox_title' => $this->input->post('mailbox_title'),
				'mailbox_description' => $this->input->post('mailbox_description'),
				'is_confidential' => $this->input->post('is_confidential'),
				'mail_number' => $this->input->post('mail_number'),
				'mail_content' => $this->input->post('mail_content'),
				'mail_address' => $this->input->post('mail_address'),
				'mail_date' => $this->input->post('mail_date'),
				'mail_time' => $this->input->post('mail_time'),
			];

			if ($this->input->post('mail_show_date')) {
				$save_data['mail_show_date'] = $this->input->post('mail_show_date');
			}

			if ($this->input->post('mail_show_time')) {
				$save_data['mail_show_time'] = $this->input->post('mail_show_time');
			}

			if (!is_dir(FCPATH . '/uploads/mailbox_mail/')) {
				mkdir(FCPATH . '/uploads/mailbox_mail/');
			}

			if (!empty($mailbox_mail_mail_header_file_name)) {
				$mailbox_mail_mail_header_file_name_copy = date('YmdHis') . '-' . $mailbox_mail_mail_header_file_name;

				rename(FCPATH . 'uploads/tmp/' . $mailbox_mail_mail_header_file_uuid . '/' . $mailbox_mail_mail_header_file_name, 
						FCPATH . 'uploads/mailbox_mail/' . $mailbox_mail_mail_header_file_name_copy);

				if (!is_file(FCPATH . '/uploads/mailbox_mail/' . $mailbox_mail_mail_header_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['mail_header_file'] = $mailbox_mail_mail_header_file_name_copy;
			}
		
			
			$save_mailbox_mail = $this->model_mailbox_mail->store($save_data);
            

			if ($save_mailbox_mail) {
				//get all recipient
				$list_recipient = $this->input->post('list_recipient');
				for($i = 0; $i < count($list_recipient); $i++) {
					$recipient = explode('|', $list_recipient[$i]);
					$recipient_npp = $recipient[0];
					$recipient_name = $recipient[1];
					$recipient_role = $recipient[2];
					$recipient_table = $recipient[3];

					$id_recipient = $this->mymodel->insertid('mailbox_recipient', [
						'id_mail' => $id,
						'npp' => $recipient_npp,
						'nama_lengkap' => $recipient_name,
						'role' => $recipient_role,
						'nama_tabel' => $recipient_table,
					]);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_mailbox_mail;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/mailbox_mail/edit/' . $save_mailbox_mail, 'Edit Mailbox Mail'),
						anchor('administrator/mailbox_mail', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/mailbox_mail/edit/' . $save_mailbox_mail, 'Edit Mailbox Mail')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_mail');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_mail');
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
	* Update view Mailbox Mails
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('mailbox_mail_update');

		$this->data['mailbox_mail'] = $this->model_mailbox_mail->find($id);

		$this->template->title('Mailbox Mail Update');
		$this->render('backend/standart/administrator/mailbox_mail/mailbox_mail_update', $this->data);
	}

	/**
	* Update Mailbox Mails
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('mailbox_mail_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('mailbox_category', 'Category', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('mailbox_title', 'Title', 'trim|max_length[255]');
		$this->form_validation->set_rules('is_confidential', 'Is Confidential?', 'trim|max_length[1]');
		$this->form_validation->set_rules('mail_number', 'Mail Number', 'trim|max_length[150]');
		$this->form_validation->set_rules('mail_address', 'Mail Address', 'trim|max_length[130]');
		$this->form_validation->set_rules('mailbox_mail_mail_header_file_name', 'Mail Header (KOP)', 'trim|max_length[255]');
		$this->form_validation->set_rules('list_recipient', 'Recipients', 'trim');
		
		if ($this->form_validation->run()) {
			$mailbox_mail_mail_header_file_uuid = $this->input->post('mailbox_mail_mail_header_file_uuid');
			$mailbox_mail_mail_header_file_name = $this->input->post('mailbox_mail_mail_header_file_name');
		
			$save_data = [
				'mailbox_category' => $this->input->post('mailbox_category'),
				'mailbox_title' => $this->input->post('mailbox_title'),
				'mailbox_description' => $this->input->post('mailbox_description'),
				'is_confidential' => $this->input->post('is_confidential'),
				'mail_number' => $this->input->post('mail_number'),
				'mail_content' => $this->input->post('mail_content'),
				'mail_address' => $this->input->post('mail_address'),
				'mail_date' => $this->input->post('mail_date'),
				'mail_time' => $this->input->post('mail_time'),
			];

			if (!empty($this->input->post('mail_show_date'))) {
				$save_data['mail_show_date'] = $this->input->post('mail_show_date');
			}

			if (!empty($this->input->post('mail_show_time'))) {
				$save_data['mail_show_time'] = $this->input->post('mail_show_time');
			}

			if (!is_dir(FCPATH . '/uploads/mailbox_mail/')) {
				mkdir(FCPATH . '/uploads/mailbox_mail/');
			}

			if (!empty($mailbox_mail_mail_header_file_uuid)) {
				$mailbox_mail_mail_header_file_name_copy = date('YmdHis') . '-' . $mailbox_mail_mail_header_file_name;

				rename(FCPATH . 'uploads/tmp/' . $mailbox_mail_mail_header_file_uuid . '/' . $mailbox_mail_mail_header_file_name, 
						FCPATH . 'uploads/mailbox_mail/' . $mailbox_mail_mail_header_file_name_copy);

				if (!is_file(FCPATH . '/uploads/mailbox_mail/' . $mailbox_mail_mail_header_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['mail_header_file'] = $mailbox_mail_mail_header_file_name_copy;
			}
		
			
			$save_mailbox_mail = $this->model_mailbox_mail->change($id, $save_data);
			//get all recipient
			$list_recipient = $this->input->post('list_recipient');
			// print_r($list_recipient);
			//get data recipient
			$get_recipient = $this->mymodel->getbywhere('mailbox_recipient', ['id_mail' => $id]);
			if (!empty($get_recipient)) {
				//delete all recipient
				$this->mymodel->delete('mailbox_recipient', '(is_read = 0 or is_important is null) and id_mail=', $id);
			}
			$get_recipient = $this->mymodel->getbywhere('mailbox_recipient', ['id_mail' => $id]);

			for($i = 0; $i < count($list_recipient); $i++) {
				$recipient = explode('|', $list_recipient[$i]);
				$recipient_npp = $recipient[0];
				$recipient_name = $recipient[1];
				$recipient_role = $recipient[2];
				$recipient_table = $recipient[3];

				$data_update = array(
					'id_mail' => $id,
					'npp' => $recipient_npp,
					'nama_lengkap' => $recipient_name,
					'role' => $recipient_role,
					'nama_tabel' => $recipient_table,
					'created_at' => date('Y-m-d H:i:s'),
				);
				// print_r($data_update);
				$already_inserted = false;
				foreach ($get_recipient as $key => $value) {
					if ($value->npp == $recipient_npp) {
						$already_inserted = true;
					}
				}
				if($already_inserted == false) {
					$id_recipient = $this->mymodel->insertid('mailbox_recipient', $data_update);
				}
			}

			if ($save_mailbox_mail) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/mailbox_mail', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_mail');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mailbox_mail');
				} else {
            		$this->data['success'] = true;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mailbox_mail');
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
	* delete Mailbox Mails
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mailbox_mail_delete');

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
            set_message(cclang('has_been_deleted', 'mailbox_mail'), 'success');
        } else {
            set_message(cclang('error_delete', 'mailbox_mail'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Mailbox Mails
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('mailbox_mail_view');

		$this->data['mailbox_mail'] = $this->model_mailbox_mail->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Mailbox Mail Detail');
		$this->render('backend/standart/administrator/mailbox_mail/mailbox_mail_view', $this->data);
	}
	
	/**
	* delete Mailbox Mails
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$mailbox_mail = $this->model_mailbox_mail->find($id);

		if (!empty($mailbox_mail->mail_header_file)) {
			$path = FCPATH . '/uploads/mailbox_mail/' . $mailbox_mail->mail_header_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_mailbox_mail->remove($id);
	}
	
	/**
	* Upload Image Mailbox Mail	* 
	* @return JSON
	*/
	public function upload_mail_header_file_file()
	{
		if (!$this->is_allowed('mailbox_mail_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'mailbox_mail',
		]);
	}

	/**
	* Delete Image Mailbox Mail	* 
	* @return JSON
	*/
	public function delete_mail_header_file_file($uuid)
	{
		if (!$this->is_allowed('mailbox_mail_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'mail_header_file', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'mailbox_mail',
            'primary_key'       => 'id_mail',
            'upload_path'       => 'uploads/mailbox_mail/'
        ]);
	}

	/**
	* Get Image Mailbox Mail	* 
	* @return JSON
	*/
	public function get_mail_header_file_file($id)
	{
		if (!$this->is_allowed('mailbox_mail_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$mailbox_mail = $this->model_mailbox_mail->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'mail_header_file', 
            'table_name'        => 'mailbox_mail',
            'primary_key'       => 'id_mail',
            'upload_path'       => 'uploads/mailbox_mail/',
            'delete_endpoint'   => 'administrator/mailbox_mail/delete_mail_header_file_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('mailbox_mail_export');

		$this->model_mailbox_mail->export('mailbox_mail', 'mailbox_mail');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('mailbox_mail_export');

		$this->model_mailbox_mail->pdf('mailbox_mail', 'mailbox_mail');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('mailbox_mail_export');

		$table = $title = 'mailbox_mail';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_mailbox_mail->find($id);
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

	public function copy_mail($id = null){
		$get_mail = $this->mymodel->withquery("select * from mailbox_mail where id_mail = '$id'","row");
		if(!empty($get_mail)){
			$get_mail->id_mail = "";
			$this->mymodel->insert("mailbox_mail", $get_mail);
			set_message(cclang('copied', 'Mailbox Mail'), 'success');
		}
		else{
			set_message(cclang('not found', 'Mailbox Mail'), 'warning');
		}
		redirect('administrator/mailbox_mail');
	}

	public function get_user(){
		$filter = $this->input->get('filter');
		$data = array();
		if($filter == "all" || empty($filter)){
			//pimpinan
			$get_data = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sd') as role, u.npp, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SD|', 'pimpinan_sd' ) as text_name from pimpinan_sd u ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMP|', 'pimpinan_smp' ) as text_name from pimpinan_smp u ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMA|', 'pimpinan_sma' ) as text_name from pimpinan_sma u ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			//guru
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_ft') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|FT|', 'guru_ft' ) as text_name from guru_ft u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMA|', 'guru_sma' ) as text_name from guru_sma u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMP|', 'guru_smp' ) as text_name from guru_smp u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SD|', 'guru_sd' ) as text_name from guru_sd u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			//pegawai
			$get_data = $this->mymodel->withquery("select u.id_pegawai as id_user, concat('pegawai') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|pegawai|', 'pegawai' ) as text_name from pegawai u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
		}
		else if($filter == "sd"){
			$get_data = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sd') as role, u.npp, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SD|', 'pimpinan_sd' ) as text_name from pimpinan_sd u ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sd') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SD|', 'guru_sd' ) as text_name from guru_sd u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
		}
		else if($filter == "smp"){
			$get_data = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_smp') as role, u.npp, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMP|', 'pimpinan_smp' ) as text_name from pimpinan_smp u ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_smp') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMP|', 'guru_smp' ) as text_name from guru_smp u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
		}
		else if($filter == "sma"){
			$get_data = $this->mymodel->withquery("select u.id_pimpinan as id_user, concat('pimpinan_sma') as role, u.npp, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMA|', 'pimpinan_sma' ) as text_name from pimpinan_sma u ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
			$get_data = $this->mymodel->withquery("select u.id_guru as id_user, concat('guru_sma') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|SMA|', 'guru_sma' ) as text_name from guru_sma u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
		}
		else if($filter == "pegawai"){
			$get_data = $this->mymodel->withquery("select u.id_pegawai as id_user, concat('pegawai') as role, u.npp, u.emp_code, u.presensi_role, u.nama_lengkap, concat(u.npp, '|', u.nama_lengkap, '|pegawai|', 'pegawai' ) as text_name from pegawai u where deleted_at is null ","result");
			if(!empty($get_data)){
				foreach ($get_data as $key => $value) {
					if(!in_array($value->text_name, $data)){
						array_push($data, $value->text_name);
					}
				}
			}
		}
		else{
			$get_data = null;
		}

		// $data = $get_data;
		echo json_encode($data);
	}
}


/* End of file mailbox_mail.php */
/* Location: ./application/controllers/administrator/Mailbox Mail.php */