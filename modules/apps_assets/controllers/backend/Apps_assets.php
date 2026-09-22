<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apps Assets Controller
*| --------------------------------------------------------------------------
*| Apps Assets site
*|
*/
class Apps_assets extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_assets');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apps Assetss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_assets_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_assetss'] = $this->model_apps_assets->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_assets_counts'] = $this->model_apps_assets->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_assets/index/',
			'total_rows'   => $this->model_apps_assets->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Asset List');
		$this->render('backend/standart/administrator/apps_assets/apps_assets_list', $this->data);
	}
	
	/**
	* Add new apps_assetss
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_assets_add');

		$this->data['positions'] = $this->db->select('position')
			->distinct()
			->order_by('position', 'ASC')
			->get('apps_position')
			->result();

		$this->template->title('Pengaturan Asset New');
		$this->render('backend/standart/administrator/apps_assets/apps_assets_add', $this->data);
	}

	/**
	* Add New Apps Assetss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_assets_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('position', 'Position', 'trim|required');
		$this->form_validation->set_rules('apps_assets_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]');
		$this->form_validation->set_rules('is_default', 'Default?', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		

		if ($this->form_validation->run()) {
			$apps_assets_img_file_uuid = $this->input->post('apps_assets_img_file_uuid');
			$apps_assets_img_file_name = $this->input->post('apps_assets_img_file_name');
		
			$save_data = [
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'position' => $this->input->post('position'),
				'description' => $this->input->post('description'),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => (!empty($this->input->post('tanggal_aktif')) ? date('Y-m-d', strtotime($this->input->post('tanggal_aktif'))) : null),
				'tanggal_selesai' => (!empty($this->input->post('tanggal_selesai')) ? date('Y-m-d', strtotime($this->input->post('tanggal_selesai'))) : null),
			];

			if (!is_dir(FCPATH . '/uploads/apps_assets/')) {
				mkdir(FCPATH . '/uploads/apps_assets/');
			}

			if (!empty($apps_assets_img_file_name)) {
				$apps_assets_img_file_name_copy = date('YmdHis') . '-' . $apps_assets_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_assets_img_file_uuid . '/' . $apps_assets_img_file_name, 
						FCPATH . 'uploads/apps_assets/' . $apps_assets_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_assets/' . $apps_assets_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_assets_img_file_name_copy;
			}
		
			
			$save_apps_assets = $this->model_apps_assets->store($save_data);
            

			if ($save_apps_assets) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_assets;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_assets/edit/' . $save_apps_assets, 'Edit Apps Assets'),
						anchor('administrator/apps_assets', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_assets/edit/' . $save_apps_assets, 'Edit Apps Assets')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_assets');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_assets');
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
	* Update view Apps Assetss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_assets_update');

		$this->data['apps_assets'] = $this->model_apps_assets->find($id);
		$this->data['positions'] = $this->db->select('position')
			->distinct()
			->order_by('position', 'ASC')
			->get('apps_position')
			->result();

		$this->template->title('Pengaturan Asset Update');
		$this->render('backend/standart/administrator/apps_assets/apps_assets_update', $this->data);
	}

	/**
	* Update Apps Assetss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_assets_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('role[]', 'Role', 'trim|required');
		$this->form_validation->set_rules('position', 'Position', 'trim|required');
		$this->form_validation->set_rules('apps_assets_img_file_name', 'File', 'trim|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]');
		$this->form_validation->set_rules('is_default', 'Default?', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Active?', 'trim|required');
		
		if ($this->form_validation->run()) {
			$apps_assets_img_file_uuid = $this->input->post('apps_assets_img_file_uuid');
			$apps_assets_img_file_name = $this->input->post('apps_assets_img_file_name');
		
			$save_data = [
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
				'role' => implode(',', (array) $this->input->post('role')),
				'position' => $this->input->post('position'),
				'description' => $this->input->post('description'),
				'is_default' => $this->input->post('is_default'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_aktif' => (!empty($this->input->post('tanggal_aktif')) ? date('Y-m-d', strtotime($this->input->post('tanggal_aktif'))) : null),
				'tanggal_selesai' => (!empty($this->input->post('tanggal_selesai')) ? date('Y-m-d', strtotime($this->input->post('tanggal_selesai'))) : null),
			];

			if (!is_dir(FCPATH . '/uploads/apps_assets/')) {
				mkdir(FCPATH . '/uploads/apps_assets/');
			}

			if (!empty($apps_assets_img_file_uuid)) {
				$apps_assets_img_file_name_copy = date('YmdHis') . '-' . $apps_assets_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $apps_assets_img_file_uuid . '/' . $apps_assets_img_file_name, 
						FCPATH . 'uploads/apps_assets/' . $apps_assets_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/apps_assets/' . $apps_assets_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $apps_assets_img_file_name_copy;
			}
		
			
			$save_apps_assets = $this->model_apps_assets->change($id, $save_data);

			if ($save_apps_assets) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_assets', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_assets');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_assets');
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
	* delete Apps Assetss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_assets_delete');

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
            set_message(cclang('has_been_deleted', 'apps_assets'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_assets'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apps Assetss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_assets_view');

		$this->data['apps_assets'] = $this->model_apps_assets->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Asset Detail');
		$this->render('backend/standart/administrator/apps_assets/apps_assets_view', $this->data);
	}
	
	/**
	* delete Apps Assetss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_assets = $this->model_apps_assets->find($id);

		if (!empty($apps_assets->img_file)) {
			$path = FCPATH . '/uploads/apps_assets/' . $apps_assets->img_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_apps_assets->remove($id);
	}
	
	/**
	* Upload Image Apps Assets	* 
	* @return JSON
	*/
	public function upload_img_file_file()
	{
		if (!$this->is_allowed('apps_assets_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'apps_assets',
		]);
	}

	/**
	* Delete Image Apps Assets	* 
	* @return JSON
	*/
	public function delete_img_file_file($uuid)
	{
		if (!$this->is_allowed('apps_assets_delete', false)) {
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
            'table_name'        => 'apps_assets',
            'primary_key'       => 'id_assets',
            'upload_path'       => 'uploads/apps_assets/'
        ]);
	}

	/**
	* Get Image Apps Assets	* 
	* @return JSON
	*/
	public function get_img_file_file($id)
	{
		if (!$this->is_allowed('apps_assets_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$apps_assets = $this->model_apps_assets->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_file', 
            'table_name'        => 'apps_assets',
            'primary_key'       => 'id_assets',
            'upload_path'       => 'uploads/apps_assets/',
            'delete_endpoint'   => 'administrator/apps_assets/delete_img_file_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_assets_export');

		$this->model_apps_assets->export('apps_assets', 'apps_assets');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_assets_export');

		$this->model_apps_assets->pdf('apps_assets', 'apps_assets');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_assets_export');

		$table = $title = 'apps_assets';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_assets->find($id);
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


/* End of file apps_assets.php */
/* Location: ./application/controllers/administrator/Apps Assets.php */