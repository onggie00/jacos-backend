<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apps Splash Screen Controller
*| --------------------------------------------------------------------------
*| Apps Splash Screen site
*|
*/
class Apps_splash_screen extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_splash_screen');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apps Splash Screens
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_splash_screen_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_splash_screens'] = $this->model_apps_splash_screen->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_splash_screen_counts'] = $this->model_apps_splash_screen->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_splash_screen/index/',
			'total_rows'   => $this->model_apps_splash_screen->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Splash Screen List');
		$this->render('backend/standart/administrator/apps_splash_screen/apps_splash_screen_list', $this->data);
	}
	
	/**
	* Add new apps_splash_screens
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_splash_screen_add');

		$this->template->title('Pengaturan Splash Screen New');
		$this->render('backend/standart/administrator/apps_splash_screen/apps_splash_screen_add', $this->data);
	}

	/**
	* Add New Apps Splash Screens
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_splash_screen_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('apps_splash_screen_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		

		if ($this->form_validation->run()) {
			$apps_splash_screen_img_file_uuid = $this->input->post('apps_splash_screen_img_file_uuid');
			$apps_splash_screen_img_file_name = $this->input->post('apps_splash_screen_img_file_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'description' => $this->input->post('description'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => $this->input->post('tanggal_aktif'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			];

			if (!is_dir(FCPATH . '/uploads/apps_splash_screen/')) {
				mkdir(FCPATH . '/uploads/apps_splash_screen/');
			}

			if (!empty($apps_splash_screen_img_file_name)) {
				$apps_splash_screen_img_file_name_copy = date('YmdHis') . '-' . $apps_splash_screen_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_splash_screen_img_file_uuid . '/' . $apps_splash_screen_img_file_name, 
						FCPATH . 'uploads/apps_splash_screen/' . $apps_splash_screen_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_splash_screen/' . $apps_splash_screen_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_splash_screen_img_file_name_copy;
			}
		
			
			$save_apps_splash_screen = $this->model_apps_splash_screen->store($save_data);
            

			if ($save_apps_splash_screen) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_splash_screen;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_splash_screen/edit/' . $save_apps_splash_screen, 'Edit Apps Splash Screen'),
						anchor('administrator/apps_splash_screen', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_splash_screen/edit/' . $save_apps_splash_screen, 'Edit Apps Splash Screen')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_splash_screen');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_splash_screen');
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
	* Update view Apps Splash Screens
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_splash_screen_update');

		$this->data['apps_splash_screen'] = $this->model_apps_splash_screen->find($id);

		$this->template->title('Pengaturan Splash Screen Update');
		$this->render('backend/standart/administrator/apps_splash_screen/apps_splash_screen_update', $this->data);
	}

	/**
	* Update Apps Splash Screens
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_splash_screen_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('apps_splash_screen_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		
		if ($this->form_validation->run()) {
			$apps_splash_screen_img_file_uuid = $this->input->post('apps_splash_screen_img_file_uuid');
			$apps_splash_screen_img_file_name = $this->input->post('apps_splash_screen_img_file_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'description' => $this->input->post('description'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => $this->input->post('tanggal_aktif'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			];

			if (!is_dir(FCPATH . '/uploads/apps_splash_screen/')) {
				mkdir(FCPATH . '/uploads/apps_splash_screen/');
			}

			if (!empty($apps_splash_screen_img_file_uuid)) {
				$apps_splash_screen_img_file_name_copy = date('YmdHis') . '-' . $apps_splash_screen_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_splash_screen_img_file_uuid . '/' . $apps_splash_screen_img_file_name, 
						FCPATH . 'uploads/apps_splash_screen/' . $apps_splash_screen_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_splash_screen/' . $apps_splash_screen_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_splash_screen_img_file_name_copy;
			}
		
			
			$save_apps_splash_screen = $this->model_apps_splash_screen->change($id, $save_data);

			if ($save_apps_splash_screen) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_splash_screen', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_splash_screen');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_splash_screen');
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
	* delete Apps Splash Screens
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_splash_screen_delete');

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
            set_message(cclang('has_been_deleted', 'apps_splash_screen'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_splash_screen'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apps Splash Screens
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_splash_screen_view');

		$this->data['apps_splash_screen'] = $this->model_apps_splash_screen->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Splash Screen Detail');
		$this->render('backend/standart/administrator/apps_splash_screen/apps_splash_screen_view', $this->data);
	}
	
	/**
	* delete Apps Splash Screens
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_splash_screen = $this->model_apps_splash_screen->find($id);

		if (!empty($apps_splash_screen->img_file)) {
			$path = FCPATH . '/uploads/apps_splash_screen/' . $apps_splash_screen->img_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_apps_splash_screen->remove($id);
	}
	
	/**
	* Upload Image Apps Splash Screen	* 
	* @return JSON
	*/
	public function upload_img_file_file()
	{
		if (!$this->is_allowed('apps_splash_screen_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'apps_splash_screen',
		]);
	}

	/**
	* Delete Image Apps Splash Screen	* 
	* @return JSON
	*/
	public function delete_img_file_file($uuid)
	{
		if (!$this->is_allowed('apps_splash_screen_delete', false)) {
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
            'table_name'        => 'apps_splash_screen',
            'primary_key'       => 'id_splash_screen',
            'upload_path'       => 'uploads/apps_splash_screen/'
        ]);
	}

	/**
	* Get Image Apps Splash Screen	* 
	* @return JSON
	*/
	public function get_img_file_file($id)
	{
		if (!$this->is_allowed('apps_splash_screen_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$apps_splash_screen = $this->model_apps_splash_screen->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_file', 
            'table_name'        => 'apps_splash_screen',
            'primary_key'       => 'id_splash_screen',
            'upload_path'       => 'uploads/apps_splash_screen/',
            'delete_endpoint'   => 'administrator/apps_splash_screen/delete_img_file_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_splash_screen_export');

		$this->model_apps_splash_screen->export('apps_splash_screen', 'apps_splash_screen');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_splash_screen_export');

		$this->model_apps_splash_screen->pdf('apps_splash_screen', 'apps_splash_screen');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_splash_screen_export');

		$table = $title = 'apps_splash_screen';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_splash_screen->find($id);
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


/* End of file apps_splash_screen.php */
/* Location: ./application/controllers/administrator/Apps Splash Screen.php */