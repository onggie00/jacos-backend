<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apps Banner Controller
*| --------------------------------------------------------------------------
*| Apps Banner site
*|
*/
class Apps_banner extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_banner');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apps Banners
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_banner_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_banners'] = $this->model_apps_banner->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_banner_counts'] = $this->model_apps_banner->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_banner/index/',
			'total_rows'   => $this->model_apps_banner->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Popup Banner List');
		$this->render('backend/standart/administrator/apps_banner/apps_banner_list', $this->data);
	}
	
	/**
	* Add new apps_banners
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_banner_add');

		$this->template->title('Pengaturan Popup Banner New');
		$this->render('backend/standart/administrator/apps_banner/apps_banner_add', $this->data);
	}

	/**
	* Add New Apps Banners
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_banner_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('apps_banner_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('no_urut', 'Urutan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		

		if ($this->form_validation->run()) {
			$apps_banner_img_file_uuid = $this->input->post('apps_banner_img_file_uuid');
			$apps_banner_img_file_name = $this->input->post('apps_banner_img_file_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'description' => $this->input->post('description'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'no_urut' => $this->input->post('no_urut'),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => $this->input->post('tanggal_aktif'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			];

			if (!is_dir(FCPATH . '/uploads/apps_banner/')) {
				mkdir(FCPATH . '/uploads/apps_banner/');
			}

			if (!empty($apps_banner_img_file_name)) {
				$apps_banner_img_file_name_copy = date('YmdHis') . '-' . $apps_banner_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_banner_img_file_uuid . '/' . $apps_banner_img_file_name, 
						FCPATH . 'uploads/apps_banner/' . $apps_banner_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_banner/' . $apps_banner_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_banner_img_file_name_copy;
			}
		
			
			$save_apps_banner = $this->model_apps_banner->store($save_data);
            

			if ($save_apps_banner) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_banner;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_banner/edit/' . $save_apps_banner, 'Edit Apps Banner'),
						anchor('administrator/apps_banner', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_banner/edit/' . $save_apps_banner, 'Edit Apps Banner')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_banner');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_banner');
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
	* Update view Apps Banners
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_banner_update');

		$this->data['apps_banner'] = $this->model_apps_banner->find($id);

		$this->template->title('Pengaturan Popup Banner Update');
		$this->render('backend/standart/administrator/apps_banner/apps_banner_update', $this->data);
	}

	/**
	* Update Apps Banners
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_banner_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('apps_banner_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('no_urut', 'Urutan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		
		if ($this->form_validation->run()) {
			$apps_banner_img_file_uuid = $this->input->post('apps_banner_img_file_uuid');
			$apps_banner_img_file_name = $this->input->post('apps_banner_img_file_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'description' => $this->input->post('description'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'no_urut' => $this->input->post('no_urut'),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => $this->input->post('tanggal_aktif'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			];

			if (!is_dir(FCPATH . '/uploads/apps_banner/')) {
				mkdir(FCPATH . '/uploads/apps_banner/');
			}

			if (!empty($apps_banner_img_file_uuid)) {
				$apps_banner_img_file_name_copy = date('YmdHis') . '-' . $apps_banner_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_banner_img_file_uuid . '/' . $apps_banner_img_file_name, 
						FCPATH . 'uploads/apps_banner/' . $apps_banner_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_banner/' . $apps_banner_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_banner_img_file_name_copy;
			}
		
			
			$save_apps_banner = $this->model_apps_banner->change($id, $save_data);

			if ($save_apps_banner) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_banner', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_banner');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_banner');
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
	* delete Apps Banners
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_banner_delete');

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
            set_message(cclang('has_been_deleted', 'apps_banner'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_banner'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apps Banners
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_banner_view');

		$this->data['apps_banner'] = $this->model_apps_banner->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Popup Banner Detail');
		$this->render('backend/standart/administrator/apps_banner/apps_banner_view', $this->data);
	}
	
	/**
	* delete Apps Banners
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_banner = $this->model_apps_banner->find($id);

		if (!empty($apps_banner->img_file)) {
			$path = FCPATH . '/uploads/apps_banner/' . $apps_banner->img_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_apps_banner->remove($id);
	}
	
	/**
	* Upload Image Apps Banner	* 
	* @return JSON
	*/
	public function upload_img_file_file()
	{
		if (!$this->is_allowed('apps_banner_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'apps_banner',
		]);
	}

	/**
	* Delete Image Apps Banner	* 
	* @return JSON
	*/
	public function delete_img_file_file($uuid)
	{
		if (!$this->is_allowed('apps_banner_delete', false)) {
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
            'table_name'        => 'apps_banner',
            'primary_key'       => 'id_banner',
            'upload_path'       => 'uploads/apps_banner/'
        ]);
	}

	/**
	* Get Image Apps Banner	* 
	* @return JSON
	*/
	public function get_img_file_file($id)
	{
		if (!$this->is_allowed('apps_banner_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$apps_banner = $this->model_apps_banner->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_file', 
            'table_name'        => 'apps_banner',
            'primary_key'       => 'id_banner',
            'upload_path'       => 'uploads/apps_banner/',
            'delete_endpoint'   => 'administrator/apps_banner/delete_img_file_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_banner_export');

		$this->model_apps_banner->export('apps_banner', 'apps_banner');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_banner_export');

		$this->model_apps_banner->pdf('apps_banner', 'apps_banner');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_banner_export');

		$table = $title = 'apps_banner';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_banner->find($id);
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


/* End of file apps_banner.php */
/* Location: ./application/controllers/administrator/Apps Banner.php */