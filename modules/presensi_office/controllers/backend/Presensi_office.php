<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Office Controller
*| --------------------------------------------------------------------------
*| Presensi Office site
*|
*/
class Presensi_office extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_office');
		$this->load->library('Presensi_office_rules');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Offices
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}

	public function pegawai($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;
		$this->data['list_user'] = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from pegawai where presensi_role = 'OFFICE' order by nama_lengkap ASC ","result");

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}

	public function sd($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;
		$list_user = array();
		$get_role = $this->mymodel->withquery("select * from presensi_setting_role where nama_role like '%SD%'","result");
		foreach($get_role as $key => $value){
			$get_user = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from $value->nama_tabel where presensi_role = '$value->nama_role' order by nama_lengkap ASC","result");
			$list_user = array_merge($list_user, $get_user);
		}
		$this->data['list_user'] = $list_user;

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}

	public function smp($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;
		$list_user = array();
		$get_role = $this->mymodel->withquery("select * from presensi_setting_role where nama_role like '%SMP%'","result");
		foreach($get_role as $key => $value){
			$get_user = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from $value->nama_tabel where presensi_role = '$value->nama_role' or presensi_role = 'GURU UMUM' order by nama_lengkap ASC","result");
			$list_user = array_merge($list_user, $get_user);
		}
		$this->data['list_user'] = $list_user;

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}

	public function sma($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;
		$list_user = array();
		$get_role = $this->mymodel->withquery("select * from presensi_setting_role where nama_role like '%SMA%'","result");
		foreach($get_role as $key => $value){
			$get_user = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from $value->nama_tabel where presensi_role = '$value->nama_role' or presensi_role = 'GURU UMUM' order by nama_lengkap ASC","result");
			$list_user = array_merge($list_user, $get_user);
		}
		$this->data['list_user'] = $list_user;

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}

	public function pramubhakti($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;
		$this->data['list_user'] = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from pramubhakti where presensi_role = 'PRAMUBHAKTI' order by nama_lengkap ASC ","result");

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}

	public function security($offset = 0)
	{
		$this->is_allowed('presensi_office_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_offices'] = $this->model_presensi_office->get($filter, $field, $this->limit_page, $offset);
		$total_rows = $this->model_presensi_office->count_all($filter, $field);
		$this->data['presensi_office_counts'] = $total_rows;
		$list_user = array();
		$get_role = $this->mymodel->withquery("select * from presensi_setting_role where nama_role like '%SECURITY%' or nama_role like '%SCR%'","result");
		foreach($get_role as $key => $value){
			$get_user = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from $value->nama_tabel where presensi_role = '$value->nama_role' order by nama_lengkap ASC","result");
			$list_user = array_merge($list_user, $get_user);
		}
		$this->data['list_user'] = $list_user;

		$config = [
			'base_url'     => 'administrator/presensi_office/index/',
			'total_rows'   => $total_rows,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office List');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_list', $this->data);
	}
	
	/**
	* Add new presensi_offices
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_office_add');

		$this->template->title('Presensi Office New');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_add', $this->data);
	}

	/**
	* Add New Presensi Offices
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_office_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('presensi_date', 'Date', 'trim|required');
		$this->form_validation->set_rules('presensi_hari', 'Hari', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('check_in', 'Check In', 'trim|required');
		$this->form_validation->set_rules('check_out', 'Check Out', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('presensi_device', 'Device', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('status_presensi', 'Status', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('status_presensi_selesai', 'Status Presensi Pulang', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('presensi_office_file_report_name', 'File Report', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('report_status', 'Report Status', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('id_izin', 'Izin', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		$this->form_validation->set_rules('updated_at', 'Updated At', 'trim|required');
		$this->form_validation->set_rules('updated_by', 'Updated By', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('deleted_at', 'Deleted At', 'trim|required');
		

		if ($this->form_validation->run()) {
			$presensi_office_file_report_uuid = $this->input->post('presensi_office_file_report_uuid');
			$presensi_office_file_report_name = $this->input->post('presensi_office_file_report_name');
		
			$save_data = [
				'npp' => $this->input->post('npp'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'presensi_date' => $this->input->post('presensi_date'),
				'presensi_hari' => $this->input->post('presensi_hari'),
				'check_in' => $this->input->post('check_in'),
				'check_out' => $this->input->post('check_out'),
				'role' => $this->input->post('role'),
				'presensi_device' => $this->input->post('presensi_device'),
				'status_presensi' => $this->input->post('status_presensi'),
				'status_presensi_selesai' => $this->input->post('status_presensi_selesai'),
				'keterangan' => $this->input->post('keterangan'),
				'report_status' => $this->input->post('report_status'),
				'id_izin' => $this->input->post('id_izin'),
				'created_at' => $this->input->post('created_at'),
				'updated_at' => $this->input->post('updated_at'),
				'updated_by' => $this->input->post('updated_by'),
				'deleted_at' => $this->input->post('deleted_at'),
			];

			if (!is_dir(FCPATH . '/uploads/presensi_office/')) {
				mkdir(FCPATH . '/uploads/presensi_office/');
			}

			if (!empty($presensi_office_file_report_name)) {
				$presensi_office_file_report_name_copy = date('YmdHis') . '-' . $presensi_office_file_report_name;

				rename(FCPATH . 'uploads/tmp/' . $presensi_office_file_report_uuid . '/' . $presensi_office_file_report_name, 
						FCPATH . 'uploads/presensi_office/' . $presensi_office_file_report_name_copy);

				if (!is_file(FCPATH . '/uploads/presensi_office/' . $presensi_office_file_report_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_report'] = $presensi_office_file_report_name_copy;
			}
		
			
			$save_presensi_office = $this->model_presensi_office->store($save_data);
            

			if ($save_presensi_office) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_office;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_office/edit/' . $save_presensi_office, 'Edit Presensi Office'),
						anchor('administrator/presensi_office', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_office/edit/' . $save_presensi_office, 'Edit Presensi Office')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_office');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_office');
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
	* Update view Presensi Offices
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_office_update');

		$this->data['presensi_office'] = $this->model_presensi_office->find($id);

		$this->template->title('Presensi Office Update');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_update', $this->data);
	}

	/**
	* Update Presensi Offices
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_office_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('presensi_date', 'Date', 'trim|required');
		$this->form_validation->set_rules('presensi_hari', 'Hari', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('check_in', 'Check In', 'trim|required');
		$this->form_validation->set_rules('check_out', 'Check Out', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('presensi_device', 'Device', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('status_presensi', 'Status', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('status_presensi_selesai', 'Status Presensi Pulang', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('presensi_office_file_report_name', 'File Report', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('report_status', 'Report Status', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('id_izin', 'Izin', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		$this->form_validation->set_rules('updated_at', 'Updated At', 'trim|required');
		$this->form_validation->set_rules('updated_by', 'Updated By', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('deleted_at', 'Deleted At', 'trim|required');
		
		if ($this->form_validation->run()) {
			$presensi_office_file_report_uuid = $this->input->post('presensi_office_file_report_uuid');
			$presensi_office_file_report_name = $this->input->post('presensi_office_file_report_name');
		
			$save_data = [
				'npp' => $this->input->post('npp'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'presensi_date' => $this->input->post('presensi_date'),
				'presensi_hari' => $this->input->post('presensi_hari'),
				'check_in' => $this->input->post('check_in'),
				'check_out' => $this->input->post('check_out'),
				'role' => $this->input->post('role'),
				'presensi_device' => $this->input->post('presensi_device'),
				'status_presensi' => $this->input->post('status_presensi'),
				'status_presensi_selesai' => $this->input->post('status_presensi_selesai'),
				'keterangan' => $this->input->post('keterangan'),
				'report_status' => $this->input->post('report_status'),
				'id_izin' => $this->input->post('id_izin'),
				'created_at' => $this->input->post('created_at'),
				'updated_at' => $this->input->post('updated_at'),
				'updated_by' => $this->input->post('updated_by'),
				'deleted_at' => $this->input->post('deleted_at'),
			];

			if (!is_dir(FCPATH . '/uploads/presensi_office/')) {
				mkdir(FCPATH . '/uploads/presensi_office/');
			}

			if (!empty($presensi_office_file_report_uuid)) {
				$presensi_office_file_report_name_copy = date('YmdHis') . '-' . $presensi_office_file_report_name;

				rename(FCPATH . 'uploads/tmp/' . $presensi_office_file_report_uuid . '/' . $presensi_office_file_report_name, 
						FCPATH . 'uploads/presensi_office/' . $presensi_office_file_report_name_copy);

				if (!is_file(FCPATH . '/uploads/presensi_office/' . $presensi_office_file_report_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_report'] = $presensi_office_file_report_name_copy;
			}
		
			
			$save_presensi_office = $this->model_presensi_office->change($id, $save_data);

			if ($save_presensi_office) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_office', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_office');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_office');
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
	* delete Presensi Offices
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_office_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_office'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_office'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Offices
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_office_view');

		$this->data['presensi_office'] = $this->model_presensi_office->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Office Detail');
		$this->render('backend/standart/administrator/presensi_office/presensi_office_view', $this->data);
	}
	
	/**
	* delete Presensi Offices
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_office = $this->model_presensi_office->find($id);

		if (!empty($presensi_office->file_report)) {
			$path = FCPATH . '/uploads/presensi_office/' . $presensi_office->file_report;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_presensi_office->remove($id);
	}
	
	/**
	* Upload Image Presensi Office	* 
	* @return JSON
	*/
	public function upload_file_report_file()
	{
		if (!$this->is_allowed('presensi_office_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'presensi_office',
		]);
	}

	/**
	* Delete Image Presensi Office	* 
	* @return JSON
	*/
	public function delete_file_report_file($uuid)
	{
		if (!$this->is_allowed('presensi_office_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_report', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'presensi_office',
            'primary_key'       => 'id_presensi',
            'upload_path'       => 'uploads/presensi_office/'
        ]);
	}

	/**
	* Get Image Presensi Office	* 
	* @return JSON
	*/
	public function get_file_report_file($id)
	{
		if (!$this->is_allowed('presensi_office_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$presensi_office = $this->model_presensi_office->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_report', 
            'table_name'        => 'presensi_office',
            'primary_key'       => 'id_presensi',
            'upload_path'       => 'uploads/presensi_office/',
            'delete_endpoint'   => 'administrator/presensi_office/delete_file_report_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_office_export');

		// $this->model_presensi_office->export('presensi_office', 'presensi_office');
	}

	public function export_harian()
	{
		$this->is_allowed('presensi_office_export');
	
		$role = $this->input->get('role');
		$tgl_start = $this->input->get("start_date") ? date("Y-m-d", strtotime($this->input->get("start_date"))) : date("Y-m-d");
		$tgl_end = $this->input->get("end_date") ? date("Y-m-d", strtotime($this->input->get("end_date"))) : date("Y-m-d");

		$start = new DateTime($tgl_start);
		$end = (new DateTime($tgl_end))->modify('+1 day');
		$interval = new DateInterval('P1D');
		$period = new DatePeriod($start, $interval, $end);
	
		$field = ['nomor', 'npp', 'nama_lengkap', 'role'];
		foreach ($period as $date) {
			$field[] = $date->format("d-m-Y");
		}
		$field = array_merge($field, ["total_hadir", "total_alfa", "total_terlambat", "total_sakit", "total_izin", "total_pulang_cepat", "total_selesai"]);
	
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
	
		if (!empty($role)) {
			if($role == "pegawai"){
				$role = "office";
			}
			$get_nama_tabel = $this->mymodel->withquery("select nama_role, nama_tabel from presensi_setting_role where nama_role like '%".$role."%'","result");
			foreach ($get_nama_tabel as $key_tabel => $value_tabel) {
				$nama_tabel = $value_tabel->nama_tabel;
				
				$where = [];
				if($role == "sd" || $role == "smp" || $role == "sma" || $role == "ft" || $role == "umum"){
					if (!empty($role)) $where[] = "u.presensi_role like '%".$role."%' or u.presensi_role like '%GURU UMUM%'";
				}
				else{
					if (!empty($role)) $where[] = "u.presensi_role like '%".$role."%'";
				}
	
				$sql_where = $where ? "WHERE " . implode(" AND ", $where) : "";
				$get_user = $this->mymodel->withquery("SELECT u.nama_lengkap, u.npp, u.presensi_role FROM $nama_tabel u $sql_where ORDER BY u.presensi_role ASC, u.nama_lengkap ASC", "result");
	
				$presensi_map = [];
				if($role == "sd" || $role == "smp" || $role == "sma" || $role == "ft" || $role == "umum"){
					$presensi_rows = $this->mymodel->withquery("SELECT * FROM presensi_office WHERE presensi_date >= '".$tgl_start."' AND presensi_date <= '".$tgl_end."' AND (role like '%".$role."%' OR role like '%GURU UMUM') ORDER BY presensi_date ASC", "result");
				}
				else{
					$presensi_rows = $this->mymodel->withquery("SELECT * FROM presensi_office WHERE presensi_date >= '".$tgl_start."' AND presensi_date <= '".$tgl_end."' AND role like '%".$role."%' ORDER BY presensi_date ASC", "result");
				}
				foreach ($presensi_rows as $presensi) {
					$presensi_map[$presensi->npp][$presensi->presensi_date] = $presensi;
				}
	
				foreach ($get_user as $key => $value) {
					$totals = ['H' => 0, 'T' => 0, 'TH' => 0, 'Alfa' => 0, 'SAKIT' => 0, 'I' => 0, 'E' => 0, 'PC' => 0, 'SH' => 0, 'E_no_status' => 0, 'SH_no_status' => 0];
	
					foreach ($period as $date) {
						$tgl = $date->format("Y-m-d");
						$label = $date->format("d-m-Y");
						$presensi = $presensi_map[$value->npp][$tgl] ?? null;
	
						if ($presensi) {
							$status = $presensi->status_presensi;
							$status_selesai = $presensi->status_presensi_selesai;
							// Jika status TROUBLE, kosongkan waktu
							$waktu = (!empty($presensi->check_in) && $status !== 'TROUBLE') 
								? date("H:i", strtotime($presensi->check_in)) 
								: "-";
								
							$waktu_selesai = (!empty($presensi->check_out) && $status_selesai !== 'TROUBLE') 
								? date("H:i", strtotime($presensi->check_out)) 
								: "-";

							$value->$label = substr($status, 0, 2) . " ($waktu) <br/>";
							$value->$label .= substr($status_selesai, 0, 2) . " ($waktu_selesai) ";
							if ($status === "T") {
								// $value->$label .= $presensi->alasan_terlambat;
							}
							$totals[$status]++;
							$totals[$status_selesai]++;
							// Hitung SH dan E yang status_presensi-nya null/kosong
							if (empty($status) || $status === 'TROUBLE') {
								if ($status_selesai === 'SH') $totals['SH_no_status']++;
								if ($status_selesai === 'E') $totals['E_no_status']++;
							}
						} else {
							$value->$label = "-";
						}
					}
	
					$value->total_hadir = (int)($totals['H'] ?? 0) + (int)($totals['T'] ?? 0) + (int)($totals['SH_no_status'] ?? 0) + (int)($totals['E_no_status'] ?? 0);
					$value->total_alfa = $totals['TH'];
					$value->total_terlambat = $totals['T'];
					$value->total_sakit = $totals['SAKIT'];
					$value->total_izin = $totals['I'];
					$value->total_pulang_cepat = $totals['E'];
					$value->total_selesai = $totals['SH'];
				}
	
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex($key_tabel)->setTitle($value_tabel->nama_role);
				
				$rowCount = 3;
				$column = 'A';
				foreach ($field as $col) {
					$label = strtoupper(str_replace(["_", "id_presensi"], [" ", "NAMA LENGKAP"], $col));
					if ($col == "nomor") $label = "NO";
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $label);
					$column++;
				}
				$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$column.'1');
				$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A3:'.$column.'3')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue("A1", "Monthly Check-in & out");
				$objPHPExcel->getActiveSheet()->setCellValue("A2", "Periode : ".$tgl_start." s/d ".$tgl_end);
	
				$rowCount = 4;
				foreach ($get_user as $key => $value) {
					$column = 'A';
					foreach ($field as $col) {
						$val = isset($value->$col) ? strip_tags($value->$col) : '';
						if ($col == "nomor") $val = $key + 1;
						if ($col == "role") $val = $value->presensi_role;
						// if ($col == "id_presensi") $val = $value->nama_lengkap;
						$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper($val));
						$column++;
					}
					$rowCount++;
				}
			}
			header('Content-Type: application/vnd.ms-excel'); 
			header('Content-Disposition: attachment;filename="Presensi '.strtoupper($role).' - ' . formatTanggal($tgl_start) . ' s/d ' . formatTanggal($tgl_end) . '.xls"'); 
			header('Cache-Control: max-age=0');
			PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
		}
		else{
			redirect_back();
		}
	}

	public function export_personal()
	{
		$this->is_allowed('presensi_office_export');

		$role = $this->input->get('role');
		$npp_input = $this->input->get('npp'); // bisa "123" atau "123,124,125" atau array

		// normalisasi NPP jadi array
		if (is_array($npp_input)) {
			$npp_list = $npp_input;
		} else {
			$npp_list = array_filter(array_map('trim', explode(',', $npp_input)));
		}

		$tgl_start = $this->input->get("start_date")
			? date("Y-m-d", strtotime($this->input->get("start_date")))
			: date("Y-m-d");

		$tgl_end = $this->input->get("end_date")
			? date("Y-m-d", strtotime($this->input->get("end_date")))
			: date("Y-m-d");

		$start    = new DateTime($tgl_start);
		$end      = (new DateTime($tgl_end))->modify('+1 day');
		$interval = new DateInterval('P1D');
		$period   = new DatePeriod($start, $interval, $end);

		$field = [
			'nomor',
			'npp',
			'nama_lengkap',
			'role',
			'presensi_date',
			'presensi_hari',
			'check_in',
			'check_out',
			'total_jam',
			'hadir',
			'terlambat',
			'pulang_cepat',
			'tidak_hadir'
		];

		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$sheetIndex = 0;

		if ($role == "pegawai") {
			$role = "office";
		}

		foreach ($npp_list as $npp) {

			$get_user = $this->mymodel->withquery("
				SELECT 
					p.nama_lengkap,
					p.npp,
					p.role,
					p.presensi_date,
					p.presensi_hari,
					p.check_in,
					p.check_out
				FROM presensi_office p
				WHERE p.npp = '$npp'
				AND p.presensi_date >= '$tgl_start' AND p.presensi_date <= '$tgl_end'
				ORDER BY p.presensi_date ASC
			", "result");

			if (empty($get_user)) {
				continue;
			}

			/* ===============================
			* MAP PRESENSI PER TANGGAL
			* =============================== */
			$presensi_map = [];
			foreach ($get_user as $row) {
				$presensi_map[$row->presensi_date] = $row;
			}

			$nama_lengkap = $get_user[0]->nama_lengkap;

			/* ===============================
			* CREATE SHEET
			* =============================== */
			if ($sheetIndex > 0) {
				$objPHPExcel->createSheet();
			}

			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$objPHPExcel->getActiveSheet()
				->setTitle(substr($npp.'-'.$nama_lengkap, 0, 31)); // max 31 char

			/* ===============================
			* HEADER
			* =============================== */
			$rowCount = 4;
			$column = 'A';

			foreach ($field as $col) {
				$label = strtoupper(str_replace("_", " ", $col));
				if ($col == "nomor") $label = "NO";
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $label);
				$column++;
			}

			$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$column.'1');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal('center');
			$objPHPExcel->getActiveSheet()->getStyle('A1:A3')->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->setCellValue("A1", "Monthly Check-in & Check-out");
			$objPHPExcel->getActiveSheet()->setCellValue("A2", "NPP : $npp | $nama_lengkap");
			$objPHPExcel->getActiveSheet()->setCellValue("A3", "Periode : $tgl_start s/d $tgl_end");

			/* ===============================
			* ISI DATA SEMUA TANGGAL
			* =============================== */
			$rowCount = 5;
			$no = 1;

			foreach ($period as $date) {

				$tgl = $date->format('Y-m-d');
				$value = $presensi_map[$tgl] ?? null;

				// default ALFA
				$check_in = '';
				$check_out = '';
				$total_jam = 0;
				$hadir = 0;
				$terlambat = 0;
				$pulang_cepat = 0;
				$tidak_hadir = 1;

				if ($value && ($value->check_in || $value->check_out)) {
					$check_in  = !empty($value->check_in) ? $value->check_in : '';
					$check_out = !empty($value->check_out) ? $value->check_out : '';

					$total_jam = ($check_in && $check_out)
						? max(0, (strtotime($check_out) - strtotime($check_in)) / 3600)
						: 0;

					$hadir       = ($check_in || $check_out) ? 1 : 0;
					$tidak_hadir = $hadir ? 0 : 1;
				}

				$column = 'A';
				foreach ($field as $col) {

					switch ($col) {
						case 'nomor': $val = $no; break;
						case 'npp': $val = $npp; break;
						case 'nama_lengkap': $val = $nama_lengkap; break;
						case 'role': $val = $role; break;
						case 'presensi_date': $val = $tgl; break;
						case 'presensi_hari': $val = $date->format('l'); break;
						case 'check_in': $val = $check_in; break;
						case 'check_out': $val = $check_out; break;
						case 'total_jam': $val = round($total_jam, 2); break;
						case 'hadir': $val = $hadir; break;
						case 'terlambat': $val = $terlambat; break;
						case 'pulang_cepat': $val = $pulang_cepat; break;
						case 'tidak_hadir': $val = $tidak_hadir; break;
						default: $val = '';
					}

					$objPHPExcel->getActiveSheet()
						->setCellValue($column.$rowCount, $val);
					$column++;
				}

				$rowCount++;
				$no++;
			}

			$sheetIndex++;
		}

		/* ===============================
		* DOWNLOAD
		* =============================== */
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Presensi-'.str_replace("'","",$nama_lengkap).'-'.$tgl_start.'-'.$tgl_end.'.xls"');
		header('Cache-Control: max-age=0');

		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_office_export');

		$this->model_presensi_office->pdf('presensi_office', 'presensi_office');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_office_export');

		$table = $title = 'presensi_office';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_office->find($id);
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

	public function chart_presensi(){
		$nama_tabel = $this->input->post('nama_tabel');
		$start_date = (empty($this->input->post("start_date"))) ? date("Y-m-d")."" : $this->input->post("start_date")."";
		$end_date = (empty($this->input->post("end_date"))) ? date("Y-m-d")."" : $this->input->post("end_date")."";

		$where = "";
		$data = array();

		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where p.presensi_date >= '".$start_date."' and p.presensi_date <= '".$end_date."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where p.presensi_date >= '".date("Y-m-d", strtotime("-1 months"))."' and p.presensi_date <= '".$end_date."'";
			}
		}
		//$get_data = $this->mymodel->withquery("select (select count(distinct tr.no_transaksi) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%SD%' ) as total_siswa, (select count(distinct tr.no_transaksi) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%LI-SD%' and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_siswa_daftar, (select count(distinct tr.no_transaksi) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%LDUI-SD%' and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_siswa_daftar_ulang from transaksi t group by total_siswa","result");
		$list_tanggal = array();
		$startDate = new DateTime($start_date);
		$endDate   = new DateTime($end_date);
		$endDate->modify('+1 day'); // agar tanggal akhir ikut masuk

		$period = new DatePeriod(
			$startDate,
			new DateInterval('P1D'),
			$endDate
		);

		foreach ($period as $date) {
			$dates[] = $date->format('Y-m-d');
		}

		if($nama_tabel == "pegawai"){
			$nama_tabel = "office";
		}
		$list_role = $this->mymodel->withquery("select * from presensi_setting_role where nama_role like '%".$nama_tabel."%' order by nama_role asc","result");
		foreach ($dates as $key => $value) {
			$data_date = array();
			$in_array = array(
				"tanggal" => formatTanggal($value),
			);
			foreach ($list_role as $key => $value_role) {
				$total = $this->mymodel->withquery("select count(id_presensi) as total from presensi_office p where p.presensi_date = '".$value."' and p.role = '".$value_role->nama_role."'","row")->total;
				$in_array[$value_role->nama_role] = $total;
			}
			array_push($data, $in_array);
		}

		echo json_encode($data);
	}

	public function approve($id_presensi, $type = ""){
		$id_presensi = (!empty($id_presensi)) ? $id_presensi : $this->input->get('id_presensi');
		$type = (!empty($type)) ? $type : $this->input->get('type');
		$get_role_shift = $this->mymodel->withquery("select p.check_in, p.check_out, p.status_presensi, p.status_presensi_selesai, p.file_report, p.file_report_end, r.nama_role, s.hari, s.start_checkin, s.limit_checkin, s.start_checkout, s.limit_checkout from presensi_setting_role r
		join presensi_setting_shift s on r.id = s.id_role
		join presensi_office p on p.role = r.nama_role
		where p.id_presensi = ".$id_presensi." ","result");
		//cek status — proses hanya laporan sesuai $type (check_in/check_out) yang masih pending
		if(!empty($get_role_shift) && ($type == "check_in" || $type == "check_out")){
			$data_update = array(
				'updated_at' => date("Y-m-d H:i:s"),
			);
			foreach ($get_role_shift as $key => $value) {
				//check_in: proses status TROUBLE atau file report pending, termasuk tanpa file
				if($type == "check_in" && $this->presensi_office_rules->isPendingCheckin($value)){
					if(!empty($value->check_in) && $value->check_in >= $value->start_checkin && $value->check_in <= $value->limit_checkin){
						$data_update['status_presensi'] = "H";
					}
					else if(!empty($value->check_in) && $value->check_in > $value->limit_checkin && $value->check_in < $value->start_checkout){
						$data_update['status_presensi'] = "T";
					}
					else{
						$data_update['status_presensi'] = "-";
					}
					$data_update['report_status'] = "1";
				}

				if($type == "check_out" && $this->presensi_office_rules->isPendingCheckout($value)){
					$data_update['status_presensi_selesai'] = $this->presensi_office_rules->checkoutStatus($value->check_out, $value->start_checkout);
					$data_update['report_status_end'] = "1";
				}
			}

			$this->mymodel->update("presensi_office", $data_update, array("id_presensi" => $id_presensi));
		}
		if ($this->input->is_ajax_request()) {
			echo json_encode(array("status" => !empty($get_role_shift) && !empty($data_update), "message" => empty($get_role_shift) ? "Data tidak ditemukan" : "Laporan berhasil disetujui"));
			return;
		}
		redirect($this->input->server('HTTP_REFERER', TRUE));
	}
	
	public function decline($id_presensi, $type = ""){
		$id_presensi = (!empty($id_presensi)) ? $id_presensi : $this->input->get('id_presensi');
		$type = (!empty($type)) ? $type : $this->input->get('type');
		$get_role_shift = $this->mymodel->withquery("select p.check_in, p.check_out, p.status_presensi, p.status_presensi_selesai, p.file_report, p.file_report_end, r.nama_role, s.hari, s.start_checkin, s.limit_checkin, s.start_checkout, s.limit_checkout from presensi_setting_role r
		join presensi_setting_shift s on r.id = s.id_role
		join presensi_office p on p.role = r.nama_role
		where p.id_presensi = ".$id_presensi." ","result");
		//cek status — proses hanya laporan sesuai $type (check_in/check_out) yang masih pending
		if(!empty($get_role_shift) && ($type == "check_in" || $type == "check_out")){
			$data_update = array(
				'updated_at' => date("Y-m-d H:i:s"),
			);
			foreach ($get_role_shift as $key => $value) {
				if($type == "check_in" && $this->presensi_office_rules->isPendingCheckin($value)){
					$data_update['status_presensi'] = "DITOLAK";
					$data_update['report_status'] = "2";
				}

				if($type == "check_out" && $this->presensi_office_rules->isPendingCheckout($value)){
					$data_update['status_presensi_selesai'] = "DITOLAK";
					$data_update['report_status_end'] = "2";
				}
			}

			$this->mymodel->update("presensi_office", $data_update, array("id_presensi" => $id_presensi));
		}
		if ($this->input->is_ajax_request()) {
			echo json_encode(array("status" => !empty($get_role_shift) && !empty($data_update), "message" => empty($get_role_shift) ? "Data tidak ditemukan" : "Laporan berhasil ditolak"));
			return;
		}
		redirect($this->input->server('HTTP_REFERER', TRUE));
	}

	public function persentase_data()
	{
		$nama_role = $this->input->post('nama_tabel');
		$start_date = $this->input->post("start_date") ?: date("Y-m-d");
		$end_date   = $this->input->post("end_date") ?: date("Y-m-d");

		if($nama_role == "pegawai"){
			$nama_role = "OFFICE";
		}

		// mapping role → tabel user
		$get_table = $this->mymodel->withquery("
			SELECT nama_role, nama_tabel 
			FROM presensi_setting_role 
			WHERE nama_role LIKE '%".$this->db->escape_like_str($nama_role)."%'
		", "result");

		if (empty($get_table)) {
			echo json_encode(["status"=>false,"data"=>[]]);
			return;
		}

		// UNION USER
		$union_user = [];
		foreach ($get_table as $t) {
			$union_user[] = "
				SELECT npp, nama_lengkap 
				FROM {$t->nama_tabel}
				WHERE deleted_at IS NULL and npp != '' and (presensi_role = '{$t->nama_role}' or presensi_role = 'GURU UMUM')
			";
		}
		$sql_user = implode(" UNION ", $union_user);

		// total user
		$total_user = $this->mymodel->withquery("
			SELECT COUNT(*) as total FROM ( $sql_user ) x
		", "row")->total;

		$threshold = ceil($total_user * 0.5);

		// ✅ ambil hari valid + bulan
		$hari_valid = $this->mymodel->withquery("
			SELECT 
				presensi_date,
				DATE_FORMAT(presensi_date, '%Y-%m') as bulan
			FROM presensi_office
			WHERE presensi_date BETWEEN '$start_date' AND '$end_date'
			AND DAYOFWEEK(presensi_date) NOT IN (1,7)
			GROUP BY presensi_date
			HAVING COUNT(DISTINCT npp) >= $threshold
		", "result");

		if (empty($hari_valid)) {
			echo json_encode(["status"=>true,"data"=>[]]);
			return;
		}

		// grouping tanggal per bulan
		$bulan_map = [];
		foreach ($hari_valid as $row) {
			$bulan_map[$row->bulan][] = "'".$row->presensi_date."'";
		}

		$data = [];

		foreach ($bulan_map as $bulan => $tanggal_list) {

			$tanggal_valid = implode(",", $tanggal_list);
			$total_hari_valid = count($tanggal_list);

			$sql = "
				SELECT 
					u.npp,
					u.nama_lengkap,

					COUNT(DISTINCT p.presensi_date) AS total_kehadiran,
					SUM(CASE WHEN p.status_presensi = 'T' THEN 1 ELSE 0 END) AS total_terlambat,
					SUM(CASE WHEN p.status_presensi_selesai = 'E' THEN 1 ELSE 0 END) AS total_pulang_cepat

				FROM ( $sql_user ) u

				LEFT JOIN presensi_office p 
					ON p.npp = u.npp
					AND p.presensi_date IN ($tanggal_valid)

				GROUP BY u.npp
			";

			$result = $this->mymodel->withquery($sql, "result");

			foreach ($result as $row) {

				$hadir = (int)$row->total_kehadiran;
				$terlambat = (int)$row->total_terlambat;
				$pulang_cepat = (int)$row->total_pulang_cepat;

				$tidak_hadir = $total_hari_valid - $hadir;

				$persen_hadir = $total_hari_valid > 0 ? ($hadir / $total_hari_valid) * 100 : 0;
				$persen_tidak_hadir = $total_hari_valid > 0 ? ($tidak_hadir / $total_hari_valid) * 100 : 0;
				$persen_terlambat = $hadir > 0 ? ($terlambat / $hadir) * 100 : 0;
				$persen_pulang_cepat = $hadir > 0 ? ($pulang_cepat / $hadir) * 100 : 0;

				if($persen_hadir > 80) {
					$persen_hadir = "<span class='label label-success'>".round($persen_hadir, 2)."%</span>";
				}
				else if($persen_hadir > 50) {
					$persen_hadir = "<span class='label label-warning'>".round($persen_hadir, 2)."%</span>";
				}
				else {
					$persen_hadir = "<span class='label label-danger'>".round($persen_hadir, 2)."%</span>";
				}
	
				if($persen_tidak_hadir > 80) {
					$persen_tidak_hadir = "<span class='label label-danger'>".round($persen_tidak_hadir, 2)."%</span>";
				}
				else if($persen_tidak_hadir > 50) {
					$persen_tidak_hadir = "<span class='label label-warning'>".round($persen_tidak_hadir, 2)."%</span>";
				}
				else {
					$persen_tidak_hadir = "<span class='label label-success'>".round($persen_tidak_hadir, 2)."%</span>";
				}
	
				if($persen_pulang_cepat > 80) {
					$persen_pulang_cepat = "<span class='label label-danger'>".round($persen_pulang_cepat, 2)."%</span>";
				}
				else if($persen_pulang_cepat > 50) {
					$persen_pulang_cepat = "<span class='label label-warning'>".round($persen_pulang_cepat, 2)."%</span>";
				}
				else {
					$persen_pulang_cepat = "<span class='label label-success'>".round($persen_pulang_cepat, 2)."%</span>";
				}
	
				if($persen_terlambat > 80) {
					$persen_terlambat = "<span class='label label-danger'>".round($persen_terlambat, 2)."%</span>";
				}
				else if($persen_terlambat > 50) {
					$persen_terlambat = "<span class='label label-warning'>".round($persen_terlambat, 2)."%</span>";
				}
				else {
					$persen_terlambat = "<span class='label label-success'>".round($persen_terlambat, 2)."%</span>";
				}

				$data[] = [
					'bulan' => formatBulan($bulan)." ".date("Y", strtotime($bulan)),
					'npp' => $row->npp,
					'nama_lengkap' => $row->nama_lengkap,
					'total_kehadiran' => $hadir,
					'total_tidak_hadir' => $tidak_hadir,
					'total_terlambat' => $terlambat,
					'total_pulang_cepat' => $pulang_cepat,
					'persentase_kehadiran' => $persen_hadir,
					'persentase_tidak_hadir' => $persen_tidak_hadir,
					'persentase_terlambat' => $persen_terlambat,
					'persentase_pulang_cepat' => $persen_pulang_cepat,
				];
			}
		}

		// ranking per bulan
		usort($data, function($a, $b){
			if ($a['bulan'] == $b['bulan']) {
				return $b['total_terlambat'] <=> $a['total_terlambat'];
			}
			return strcmp($a['bulan'], $b['bulan']);
		});

		$rank_per_bulan = [];
		foreach ($data as $i => $row) {
			$bulan = $row['bulan'];
			if (!isset($rank_per_bulan[$bulan])) {
				$rank_per_bulan[$bulan] = 1;
			}
			$data[$i]['ranking_terlambat'] = $rank_per_bulan[$bulan]++;
		}

		echo json_encode([
			"status" => true,
			"message" => "OK",
			"total_user" => $total_user,
			"data" => $data,
		]);
	}

	public function table_data() {
		$nama_tabel = $this->input->post('nama_tabel');
		$start_date = (empty($this->input->post("start_date"))) ? date("Y-m-d")."" : $this->input->post("start_date")."";
		$end_date = (empty($this->input->post("end_date"))) ? date("Y-m-d")."" : $this->input->post("end_date")."";
		
		$hadir = array();
		$tidak_hadir = array();
		// $list_user = $this->mymodel->withquery("select npp, nama_lengkap, presensi_role from $nama_tabel order by nama_lengkap ASC","result");
		if($nama_tabel == "pegawai"){
			$nama_tabel = "OFFICE";
		}
		//ambil daftar role dulu (tabel kecil), lalu query presensi dengan IN — pakai index presensi_date, tanpa join/group
		$roles = $this->mymodel->withquery("select nama_role from presensi_setting_role where nama_role like '%".$this->db->escape_like_str($nama_tabel)."%' or nama_role like '%GURU UMUM%'","result");
		$role_names = array();
		foreach ($roles as $r) { $role_names[] = $r->nama_role; }

		$data = array();
		if (!empty($role_names)) {
			$escaped = array();
			foreach ($role_names as $rn) { $escaped[] = $this->db->escape($rn); }
			$data = $this->mymodel->withquery("select p.* from presensi_office p
			where p.presensi_date >= '".$this->db->escape_str($start_date)."' and p.presensi_date <= '".$this->db->escape_str($end_date)."'
			and p.role in (".implode(",", $escaped).")
			order by p.presensi_date DESC","result");
		}

		if(!empty($data)){
			foreach($data as $key => $value){
				$action = "<a href='".base_url('administrator/presensi_office/edit/'.$value->id_presensi)."' class='btn btn-success btn-sm' style='width:50px;padding:5px;margin:2px;'>Edit</a> 
				<a href='javascript:void(0);' data-href='".site_url('administrator/presensi_office/delete/' . $value->id_presensi)."' class='btn btn-danger btn-sm remove-data' style='width:50px;padding:5px;margin:2px;'> Delete</a>";
				$status_start = "";
				$status_end = "";
				//status presensi
				if ($value->status_presensi == "H"){
					$status_start = "<button class='btn btn-sm btn-success'>Hadir</button>";
				}
				else if ($value->status_presensi == "T"){
					$status_start = "<button class='btn btn-sm btn-warning'>Terlambat</button>";
				}
				else if ($value->status_presensi == "TH"){
					$status_start = "<button class='btn btn-sm btn-danger'>Tidak Hadir</button>";
				}
				else if ($value->status_presensi == "TROUBLE"){
					$status_start = "<button class='btn btn-sm btn-info'>Presensi App</button>";
				}
				else if ($value->status_presensi == "DITOLAK"){
					$status_start = "<button class='btn btn-sm btn-danger'>Pengajuan Ditolak</button>";
				}
				else if ($value->status_presensi == "DISETUJUI"){
					$status_start = "<button class='btn btn-sm btn-success'>Pengajuan Disetujui</button>";
				}
				else if ($value->status_presensi == "HAMIL"){
					$status_start = "<button class='btn btn-sm btn-success'>Maternity Leave</button>";
				}
				else if ($value->status_presensi == "DINAS"){
					$status_start = "<button class='btn btn-sm btn-success'>Business Trip (Dinas)</button>";
				}
				else if ($value->status_presensi == "CUTI"){
					$status_start = "<button class='btn btn-sm btn-info'>Annual Leave</button>";
				}
				else if ($value->status_presensi == "SAKIT"){
					$status_start = "<button class='btn btn-sm btn-info'>Sick Leave</button>";
				}
				//status end
				if ($value->status_presensi_selesai == "E"){
					$status_end = "<button class='btn btn-sm btn-warning'>Pulang Cepat</button>";
				}
				else if ($value->status_presensi_selesai == "SH"){
					$status_end = "<button class='btn btn-sm btn-success'>Selesai</button>";
				}
				else if ($value->status_presensi_selesai == "TROUBLE"){
					$status_end = "<button class='btn btn-sm btn-info'>Presensi App</button>";
				}
				else if ($value->status_presensi == "DITOLAK"){
					$status_start = "<button class='btn btn-sm btn-danger'>Pengajuan Ditolak</button>";
				}
				else if ($value->status_presensi == "DISETUJUI"){
					$status_start = "<button class='btn btn-sm btn-success'>Pengajuan Disetujui</button>";
				}
				else if ($value->status_presensi_selesai == "DINAS"){
					$status_end = "<button class='btn btn-sm btn-success'>Business Trip (Dinas)</button>";
				}
				else if ($value->status_presensi_selesai == "CUTI"){
					$status_end = "<button class='btn btn-sm btn-info'>Annual Leave</button>";
				}
				else if ($value->status_presensi_selesai == "SAKIT"){
					$status_end = "<button class='btn btn-sm btn-info'>Sick Leave</button>";
				}
				else if ($value->status_presensi_selesai == "HAMIL"){
					$status_end = "<button class='btn btn-sm btn-success'>Maternity Leave</button>";
				}

				//approve / decline per laporan (datang = check_in, pulang = check_out), tampil selama laporan belum diproses — dijalankan via AJAX
				//datang: tampil jika status TROUBLE atau ada file laporan pending
				if ($this->presensi_office_rules->isPendingCheckin($value)) {
					$action .= "<a href='javascript:void(0);' class='btn btn-info btn-action' data-url='".base_url('administrator/presensi_office/approve/'.$value->id_presensi).'?type=check_in'."' style='padding:5px;margin:2px;white-space:nowrap;'>Approve Datang</a> 
					<a href='javascript:void(0);' class='btn btn-warning btn-action' data-url='".base_url('administrator/presensi_office/decline/'.$value->id_presensi).'?type=check_in'."' style='padding:5px;margin:2px;white-space:nowrap;'>Decline Datang</a>";
				}
				if ($this->presensi_office_rules->isPendingCheckout($value)) {
					$action .= "<a href='javascript:void(0);' class='btn btn-info btn-action' data-url='".base_url('administrator/presensi_office/approve/'.$value->id_presensi).'?type=check_out'."' style='padding:5px;margin:2px;white-space:nowrap;'>Approve Pulang</a> 
					<a href='javascript:void(0);' class='btn btn-warning btn-action' data-url='".base_url('administrator/presensi_office/decline/'.$value->id_presensi).'?type=check_out'."' style='padding:5px;margin:2px;white-space:nowrap;'>Decline Pulang</a>";
				}

				$file_report = "";
				if (!empty($value->file_report)) {
					$file_report = "<a href='".base_url('uploads/presensi_office/'.$value->file_report)."' target='_blank' class='btn btn-info btn-sm' style='width:50px;padding:5px;margin:2px;'>Lihat</a>";
				}

				$add = array(
					'id_presensi' => $value->id_presensi,
					'npp' => $value->npp,
					'nama_lengkap' => $value->nama_lengkap,
					'presensi_date' => formatTanggal($value->presensi_date),
					'check_in' => (empty($value->check_in)) ? "-" : date("H:i", strtotime($value->check_in))."<br/>".$status_start,
					'check_out' => (empty($value->check_out)) ? "&nbsp;": date("H:i", strtotime($value->check_out))."<br/>".$status_end,
					'presensi_hari' => $value->presensi_hari,
					'status_presensi' => $value->status_presensi,
					'status_presensi_selesai' => $value->status_presensi_selesai,
					'presensi_device' => $value->presensi_device,
					'keterangan' => $value->keterangan,
					'file_report' => $file_report,
					'action' => $action
				);
				$data[$key] = $add;
			}

		}

		$data_response = array(
			"status" => true,
			"message" => "Loading Data Success",
			// "recordsTotal" => count($get_data),
			// "recordsFiltered" => count($get_data),
			"data" => $data,
		);
		echo json_encode($data_response);
	}

	public function persentase_data_raw()
	{
		$nama_role = $this->input->get('nama_tabel');
		$start_date = $this->input->get("start_date") ?: date("Y-m-d");
		$end_date   = $this->input->get("end_date") ?: date("Y-m-d");

		if($nama_role == "pegawai"){
			$nama_role = "OFFICE";
		}

		// mapping role → tabel user
		$get_table = $this->mymodel->withquery("
			SELECT nama_role, nama_tabel 
			FROM presensi_setting_role 
			WHERE nama_tabel LIKE '%".$this->db->escape_like_str($nama_role)."%'
		", "result");

		if (empty($get_table)) {
			echo json_encode(["status"=>false,"data"=>[]]);
			return;
		}

		// UNION USER
		$union_user = [];
		foreach ($get_table as $t) {
			$union_user[] = "
				SELECT npp, nama_lengkap 
				FROM {$t->nama_tabel}
				WHERE deleted_at IS NULL and npp != '' and (presensi_role = '{$t->nama_role}' or presensi_role = 'GURU UMUM')
			";
		}
		$sql_user = implode(" UNION ", $union_user);

		// total user
		$total_user = $this->mymodel->withquery("
			SELECT COUNT(*) as total FROM ( $sql_user ) x
		", "row")->total;

		$threshold = ceil($total_user * 0.5);

		// ✅ ambil hari valid + bulan
		$hari_valid = $this->mymodel->withquery("
			SELECT 
				presensi_date,
				DATE_FORMAT(presensi_date, '%Y-%m') as bulan
			FROM presensi_office
			WHERE presensi_date >= '$start_date'
			AND presensi_date <= '$end_date'
			AND DAYOFWEEK(presensi_date) NOT IN (1,7)
			GROUP BY presensi_date
			HAVING COUNT(DISTINCT npp) >= $threshold
		", "result");

		if (empty($hari_valid)) {
			echo json_encode(["status"=>true,"data"=>[]]);
			return;
		}

		// grouping tanggal per bulan
		$bulan_map = [];
		foreach ($hari_valid as $row) {
			$bulan_map[$row->bulan][] = "'".$row->presensi_date."'";
		}

		$data = [];

		foreach ($bulan_map as $bulan => $tanggal_list) {

			$tanggal_valid = implode(",", $tanggal_list);
			$total_hari_valid = count($tanggal_list);

			$sql = "
				SELECT 
					u.npp,
					u.nama_lengkap,

					COUNT(DISTINCT p.presensi_date) AS total_kehadiran,
					SUM(CASE WHEN p.status_presensi = 'T' THEN 1 ELSE 0 END) AS total_terlambat,
					SUM(CASE WHEN p.status_presensi_selesai = 'E' THEN 1 ELSE 0 END) AS total_pulang_cepat

				FROM ( $sql_user ) u

				LEFT JOIN presensi_office p 
					ON p.npp = u.npp
					AND p.presensi_date IN ($tanggal_valid)

				GROUP BY u.npp
			";

			$result = $this->mymodel->withquery($sql, "result");

			foreach ($result as $row) {

				$hadir = (int)$row->total_kehadiran;
				$terlambat = (int)$row->total_terlambat;
				$pulang_cepat = (int)$row->total_pulang_cepat;

				$tidak_hadir = $total_hari_valid - $hadir;

				$persen_hadir = $total_hari_valid > 0 ? ($hadir / $total_hari_valid) * 100 : 0;
				$persen_tidak_hadir = $total_hari_valid > 0 ? ($tidak_hadir / $total_hari_valid) * 100 : 0;
				$persen_terlambat = $hadir > 0 ? ($terlambat / $hadir) * 100 : 0;
				$persen_pulang_cepat = $hadir > 0 ? ($pulang_cepat / $hadir) * 100 : 0;

				$data[] = [
					'bulan' => formatBulan($bulan)." ".date("Y", strtotime($bulan)),
					'npp' => $row->npp,
					'nama_lengkap' => $row->nama_lengkap,
					'total_kehadiran' => $hadir,
					'total_tidak_hadir' => $tidak_hadir,
					'total_terlambat' => $terlambat,
					'total_pulang_cepat' => $pulang_cepat,
					'persentase_kehadiran' => round($persen_hadir,2),
					'persentase_tidak_hadir' => round($persen_tidak_hadir,2),
					'persentase_terlambat' => round($persen_terlambat,2),
					'persentase_pulang_cepat' => round($persen_pulang_cepat,2),
				];
			}
		}

		// ranking per bulan
		usort($data, function($a, $b){
			if ($a['bulan'] == $b['bulan']) {
				return $b['total_terlambat'] <=> $a['total_terlambat'];
			}
			return strcmp($a['bulan'], $b['bulan']);
		});

		$rank_per_bulan = [];
		foreach ($data as $i => $row) {
			$bulan = $row['bulan'];
			if (!isset($rank_per_bulan[$bulan])) {
				$rank_per_bulan[$bulan] = 1;
			}
			$data[$i]['ranking_terlambat'] = $rank_per_bulan[$bulan]++;
		}

		return $data;
	}

	public function export_excel_persentase()
	{
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
	
		$result = $this->persentase_data_raw();
	
		if (empty($result)) {
			echo "Tidak ada data";
			return;
		}
	
		// group by bulan
		$group_bulan = [];
		foreach ($result as $row) {
			$group_bulan[$row['bulan']][] = $row;
		}
	
		$sheetIndex = 0;
	
		foreach ($group_bulan as $bulan => $rows) {
	
			// create sheet baru
			if ($sheetIndex == 0) {
				$sheet = $objPHPExcel->getActiveSheet();
			} else {
				$sheet = $objPHPExcel->createSheet($sheetIndex);
			}
	
			$sheet->setTitle($bulan);
			$sheet->getStyle('A1:K1')->getFont()->setBold(true);
			$sheet->freezePane('A2');
	
			// header
			$headers = [
				'A1' => 'NPP',
				'B1' => 'Nama',
				'C1' => 'Hadir',
				'D1' => 'Tidak Hadir',
				'E1' => 'Terlambat',
				'F1' => 'Pulang Cepat',
				'G1' => '% Hadir',
				'H1' => '% Tidak Hadir',
				'I1' => '% Terlambat',
				'J1' => '% Pulang Cepat',
				'K1' => 'Ranking Terlambat',
			];
	
			foreach ($headers as $cell => $val) {
				$sheet->setCellValue($cell, $val);
			}
	
			// isi data
			$rowNum = 2;
			foreach ($rows as $r) {
	
				$sheet->setCellValue("A$rowNum", $r['npp']);
				$sheet->setCellValue("B$rowNum", $r['nama_lengkap']);
				$sheet->setCellValue("C$rowNum", $r['total_kehadiran']);
				$sheet->setCellValue("D$rowNum", $r['total_tidak_hadir']);
				$sheet->setCellValue("E$rowNum", $r['total_terlambat']);
				$sheet->setCellValue("F$rowNum", $r['total_pulang_cepat']);
				$sheet->setCellValue("G$rowNum", $r['persentase_kehadiran']);
				$sheet->setCellValue("H$rowNum", $r['persentase_tidak_hadir']);
				$sheet->setCellValue("I$rowNum", $r['persentase_terlambat']);
				$sheet->setCellValue("J$rowNum", $r['persentase_pulang_cepat']);
				$sheet->setCellValue("K$rowNum", $r['ranking_terlambat']);
	
				$rowNum++;
			}
	
			// auto width
			foreach (range('A','K') as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			}
	
			$sheetIndex++;
		}
	
		// set active sheet pertama
		$objPHPExcel->setActiveSheetIndex(0);
	
		// output
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Laporan_Presensi_Per_Bulan.xls"');
		header('Cache-Control: max-age=0');
	
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$writer->save('php://output');
	}
	
}


/* End of file presensi_office.php */
/* Location: ./application/controllers/administrator/Presensi Office.php */