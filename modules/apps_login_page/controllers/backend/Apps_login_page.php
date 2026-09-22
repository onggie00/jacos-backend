<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apps Login Page Controller
*| --------------------------------------------------------------------------
*| Apps Login Page site
*|
*/
class Apps_login_page extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_login_page');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apps Login Pages
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_login_page_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_login_pages'] = $this->model_apps_login_page->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_login_page_counts'] = $this->model_apps_login_page->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_login_page/index/',
			'total_rows'   => $this->model_apps_login_page->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Login Page List');
		$this->render('backend/standart/administrator/apps_login_page/apps_login_page_list', $this->data);
	}
	
	/**
	* Add new apps_login_pages
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_login_page_add');

		$this->template->title('Pengaturan Login Page New');
		$this->render('backend/standart/administrator/apps_login_page/apps_login_page_add', $this->data);
	}

	/**
	* Add New Apps Login Pages
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_login_page_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('apps_login_page_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('sub_title', 'Sub Title', 'trim');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		

		if ($this->form_validation->run()) {
			$apps_login_page_img_file_uuid = $this->input->post('apps_login_page_img_file_uuid');
			$apps_login_page_img_file_name = $this->input->post('apps_login_page_img_file_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'sub_title' => $this->input->post('sub_title'),
				'description' => $this->input->post('description'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => $this->input->post('tanggal_aktif'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			];

			if (!is_dir(FCPATH . '/uploads/apps_login_page/')) {
				mkdir(FCPATH . '/uploads/apps_login_page/');
			}

			if (!empty($apps_login_page_img_file_name)) {
				$apps_login_page_img_file_name_copy = date('YmdHis') . '-' . $apps_login_page_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_login_page_img_file_uuid . '/' . $apps_login_page_img_file_name, 
						FCPATH . 'uploads/apps_login_page/' . $apps_login_page_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_login_page/' . $apps_login_page_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_login_page_img_file_name_copy;
			}
		
			
			$save_apps_login_page = $this->model_apps_login_page->store($save_data);
            

			if ($save_apps_login_page) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_login_page;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_login_page/edit/' . $save_apps_login_page, 'Edit Apps Login Page'),
						anchor('administrator/apps_login_page', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_login_page/edit/' . $save_apps_login_page, 'Edit Apps Login Page')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_login_page');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_login_page');
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
	* Update view Apps Login Pages
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_login_page_update');

		$this->data['apps_login_page'] = $this->model_apps_login_page->find($id);

		$this->template->title('Pengaturan Login Page Update');
		$this->render('backend/standart/administrator/apps_login_page/apps_login_page_update', $this->data);
	}

	/**
	* Update Apps Login Pages
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_login_page_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('apps_login_page_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('sub_title', 'Sub Title', 'trim');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		
		if ($this->form_validation->run()) {
			$apps_login_page_img_file_uuid = $this->input->post('apps_login_page_img_file_uuid');
			$apps_login_page_img_file_name = $this->input->post('apps_login_page_img_file_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'sub_title' => $this->input->post('sub_title'),
				'description' => $this->input->post('description'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => $this->input->post('tanggal_aktif'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			];

			if (!is_dir(FCPATH . '/uploads/apps_login_page/')) {
				mkdir(FCPATH . '/uploads/apps_login_page/');
			}

			if (!empty($apps_login_page_img_file_uuid)) {
				$apps_login_page_img_file_name_copy = date('YmdHis') . '-' . $apps_login_page_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_login_page_img_file_uuid . '/' . $apps_login_page_img_file_name, 
						FCPATH . 'uploads/apps_login_page/' . $apps_login_page_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_login_page/' . $apps_login_page_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_login_page_img_file_name_copy;
			}
		
			
			$save_apps_login_page = $this->model_apps_login_page->change($id, $save_data);

			if ($save_apps_login_page) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_login_page', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_login_page');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_login_page');
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
	* delete Apps Login Pages
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_login_page_delete');

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
            set_message(cclang('has_been_deleted', 'apps_login_page'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_login_page'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apps Login Pages
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_login_page_view');

		$this->data['apps_login_page'] = $this->model_apps_login_page->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Login Page Detail');
		$this->render('backend/standart/administrator/apps_login_page/apps_login_page_view', $this->data);
	}
	
	/**
	* delete Apps Login Pages
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_login_page = $this->model_apps_login_page->find($id);

		if (!empty($apps_login_page->img_file)) {
			$path = FCPATH . '/uploads/apps_login_page/' . $apps_login_page->img_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_apps_login_page->remove($id);
	}
	
	/**
	* Upload Image Apps Login Page	* 
	* @return JSON
	*/
	public function upload_img_file_file()
	{
		if (!$this->is_allowed('apps_login_page_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'apps_login_page',
		]);
	}

	/**
	* Delete Image Apps Login Page	* 
	* @return JSON
	*/
	public function delete_img_file_file($uuid)
	{
		if (!$this->is_allowed('apps_login_page_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'img_file', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'apps_login_page',
            'primary_key'       => 'id_login_page',
            'upload_path'       => 'uploads/apps_login_page/'
        ]);
	}

	/**
	* Get Image Apps Login Page	* 
	* @return JSON
	*/
	public function get_img_file_file($id)
	{
		if (!$this->is_allowed('apps_login_page_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$apps_login_page = $this->model_apps_login_page->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_file', 
            'table_name'        => 'apps_login_page',
            'primary_key'       => 'id_login_page',
            'upload_path'       => 'uploads/apps_login_page/',
            'delete_endpoint'   => 'administrator/apps_login_page/delete_img_file_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_login_page_export');

		$this->model_apps_login_page->export('apps_login_page', 'apps_login_page');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_login_page_export');

		$this->model_apps_login_page->pdf('apps_login_page', 'apps_login_page');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_login_page_export');

		$table = $title = 'apps_login_page';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_login_page->find($id);
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


/* End of file apps_login_page.php */
/* Location: ./modules/apps_login_page/controllers/backend/Apps_login_page.php */
