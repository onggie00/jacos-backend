<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Fasilitas Controller
*| --------------------------------------------------------------------------
*| Fasilitas site
*|
*/
class Fasilitas extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_fasilitas');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Fasilitass
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('fasilitas_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['fasilitass'] = $this->model_fasilitas->get($filter, $field, $this->limit_page, $offset);
		$this->data['fasilitas_counts'] = $this->model_fasilitas->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/fasilitas/index/',
			'total_rows'   => $this->model_fasilitas->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Fasilitas List');
		$this->render('backend/standart/administrator/fasilitas/fasilitas_list', $this->data);
	}
	
	/**
	* Add new fasilitass
	*
	*/
	public function add()
	{
		$this->is_allowed('fasilitas_add');

		$this->template->title('Fasilitas New');
		$this->render('backend/standart/administrator/fasilitas/fasilitas_add', $this->data);
	}

	/**
	* Add New Fasilitass
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('fasilitas_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_fasilitas', 'Nama Fasilitas', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('fasilitas_img_fasilitas_name', 'Img Fasilitas', 'trim|required');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		

		if ($this->form_validation->run()) {
			$fasilitas_img_fasilitas_uuid = $this->input->post('fasilitas_img_fasilitas_uuid');
			$fasilitas_img_fasilitas_name = $this->input->post('fasilitas_img_fasilitas_name');
		
			$save_data = [
				'nama_fasilitas' => $this->input->post('nama_fasilitas'),
				'deskripsi' => $this->input->post('deskripsi'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
			];

			if (!is_dir(FCPATH . '/uploads/fasilitas/')) {
				mkdir(FCPATH . '/uploads/fasilitas/');
			}

			if (!empty($fasilitas_img_fasilitas_name)) {
				$fasilitas_img_fasilitas_name_copy = date('YmdHis') . '-' . $fasilitas_img_fasilitas_name;

				rename(FCPATH . 'uploads/tmp/' . $fasilitas_img_fasilitas_uuid . '/' . $fasilitas_img_fasilitas_name, 
						FCPATH . 'uploads/fasilitas/' . $fasilitas_img_fasilitas_name_copy);

				if (!is_file(FCPATH . '/uploads/fasilitas/' . $fasilitas_img_fasilitas_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_fasilitas'] = $fasilitas_img_fasilitas_name_copy;
			}
		
			
			$save_fasilitas = $this->model_fasilitas->store($save_data);
            

			if ($save_fasilitas) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_fasilitas;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/fasilitas/edit/' . $save_fasilitas, 'Edit Fasilitas'),
						anchor('administrator/fasilitas', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/fasilitas/edit/' . $save_fasilitas, 'Edit Fasilitas')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/fasilitas');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/fasilitas');
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
	* Update view Fasilitass
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('fasilitas_update');

		$this->data['fasilitas'] = $this->model_fasilitas->find($id);

		$this->template->title('Fasilitas Update');
		$this->render('backend/standart/administrator/fasilitas/fasilitas_update', $this->data);
	}

	/**
	* Update Fasilitass
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('fasilitas_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_fasilitas', 'Nama Fasilitas', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('fasilitas_img_fasilitas_name', 'Img Fasilitas', 'trim|required');
		$this->form_validation->set_rules('jenjang[]', 'Jenjang', 'trim|required');
		
		if ($this->form_validation->run()) {
			$fasilitas_img_fasilitas_uuid = $this->input->post('fasilitas_img_fasilitas_uuid');
			$fasilitas_img_fasilitas_name = $this->input->post('fasilitas_img_fasilitas_name');
		
			$save_data = [
				'nama_fasilitas' => $this->input->post('nama_fasilitas'),
				'deskripsi' => $this->input->post('deskripsi'),
				'jenjang' => implode(',', (array) $this->input->post('jenjang')),
			];

			if (!is_dir(FCPATH . '/uploads/fasilitas/')) {
				mkdir(FCPATH . '/uploads/fasilitas/');
			}

			if (!empty($fasilitas_img_fasilitas_uuid)) {
				$fasilitas_img_fasilitas_name_copy = date('YmdHis') . '-' . $fasilitas_img_fasilitas_name;

				rename(FCPATH . 'uploads/tmp/' . $fasilitas_img_fasilitas_uuid . '/' . $fasilitas_img_fasilitas_name, 
						FCPATH . 'uploads/fasilitas/' . $fasilitas_img_fasilitas_name_copy);

				if (!is_file(FCPATH . '/uploads/fasilitas/' . $fasilitas_img_fasilitas_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_fasilitas'] = $fasilitas_img_fasilitas_name_copy;
			}
		
			
			$save_fasilitas = $this->model_fasilitas->change($id, $save_data);

			if ($save_fasilitas) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/fasilitas', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/fasilitas');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/fasilitas');
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
	* delete Fasilitass
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('fasilitas_delete');

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
            set_message(cclang('has_been_deleted', 'fasilitas'), 'success');
        } else {
            set_message(cclang('error_delete', 'fasilitas'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Fasilitass
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('fasilitas_view');

		$this->data['fasilitas'] = $this->model_fasilitas->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Fasilitas Detail');
		$this->render('backend/standart/administrator/fasilitas/fasilitas_view', $this->data);
	}
	
	/**
	* delete Fasilitass
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$fasilitas = $this->model_fasilitas->find($id);

		if (!empty($fasilitas->img_fasilitas)) {
			$path = FCPATH . '/uploads/fasilitas/' . $fasilitas->img_fasilitas;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_fasilitas->remove($id);
	}
	
	/**
	* Upload Image Fasilitas	* 
	* @return JSON
	*/
	public function upload_img_fasilitas_file()
	{
		if (!$this->is_allowed('fasilitas_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'fasilitas',
		]);
	}

	/**
	* Delete Image Fasilitas	* 
	* @return JSON
	*/
	public function delete_img_fasilitas_file($uuid)
	{
		if (!$this->is_allowed('fasilitas_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'img_fasilitas', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'fasilitas',
            'primary_key'       => 'id_fasilitas',
            'upload_path'       => 'uploads/fasilitas/'
        ]);
	}

	/**
	* Get Image Fasilitas	* 
	* @return JSON
	*/
	public function get_img_fasilitas_file($id)
	{
		if (!$this->is_allowed('fasilitas_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$fasilitas = $this->model_fasilitas->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_fasilitas', 
            'table_name'        => 'fasilitas',
            'primary_key'       => 'id_fasilitas',
            'upload_path'       => 'uploads/fasilitas/',
            'delete_endpoint'   => 'administrator/fasilitas/delete_img_fasilitas_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('fasilitas_export');

		$this->model_fasilitas->export('fasilitas', 'fasilitas');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('fasilitas_export');

		$this->model_fasilitas->pdf('fasilitas', 'fasilitas');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('fasilitas_export');

		$table = $title = 'fasilitas';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_fasilitas->find($id);
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


/* End of file fasilitas.php */
/* Location: ./application/controllers/administrator/Fasilitas.php */