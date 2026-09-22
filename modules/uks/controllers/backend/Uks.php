<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Uks Controller
*| --------------------------------------------------------------------------
*| Uks site
*|
*/
class Uks extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_uks');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ukss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('uks_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ukss'] = $this->model_uks->get($filter, $field, $this->limit_page, $offset);
		$this->data['uks_counts'] = $this->model_uks->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/uks/index/',
			'total_rows'   => $this->model_uks->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Uks List');
		$this->render('backend/standart/administrator/uks/uks_list', $this->data);
	}
	
	/**
	* Add new ukss
	*
	*/
	public function add()
	{
		$this->is_allowed('uks_add');

		$this->template->title('Uks New');
		$this->render('backend/standart/administrator/uks/uks_add', $this->data);
	}

	/**
	* Add New Ukss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('uks_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[25]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('uks_foto_name', 'Foto', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		

		if ($this->form_validation->run()) {
			$uks_foto_uuid = $this->input->post('uks_foto_uuid');
			$uks_foto_name = $this->input->post('uks_foto_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
				'created_at' => $this->input->post('created_at'),
			];

			if (!is_dir(FCPATH . '/uploads/uks/')) {
				mkdir(FCPATH . '/uploads/uks/');
			}

			if (!empty($uks_foto_name)) {
				$uks_foto_name_copy = date('YmdHis') . '-' . $uks_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $uks_foto_uuid . '/' . $uks_foto_name, 
						FCPATH . 'uploads/uks/' . $uks_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/uks/' . $uks_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $uks_foto_name_copy;
			}
		
			
			$save_uks = $this->model_uks->store($save_data);
            

			if ($save_uks) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_uks;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/uks/edit/' . $save_uks, 'Edit Uks'),
						anchor('administrator/uks', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/uks/edit/' . $save_uks, 'Edit Uks')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/uks');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/uks');
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
	* Update view Ukss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('uks_update');

		$this->data['uks'] = $this->model_uks->find($id);

		$this->template->title('Uks Update');
		$this->render('backend/standart/administrator/uks/uks_update', $this->data);
	}

	/**
	* Update Ukss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('uks_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[25]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('uks_foto_name', 'Foto', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		
		if ($this->form_validation->run()) {
			$uks_foto_uuid = $this->input->post('uks_foto_uuid');
			$uks_foto_name = $this->input->post('uks_foto_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
				'created_at' => $this->input->post('created_at'),
			];

			if (!is_dir(FCPATH . '/uploads/uks/')) {
				mkdir(FCPATH . '/uploads/uks/');
			}

			if (!empty($uks_foto_uuid)) {
				$uks_foto_name_copy = date('YmdHis') . '-' . $uks_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $uks_foto_uuid . '/' . $uks_foto_name, 
						FCPATH . 'uploads/uks/' . $uks_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/uks/' . $uks_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $uks_foto_name_copy;
			}
		
			
			$save_uks = $this->model_uks->change($id, $save_data);

			if ($save_uks) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/uks', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/uks');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/uks');
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
	* delete Ukss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('uks_delete');

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
            set_message(cclang('has_been_deleted', 'uks'), 'success');
        } else {
            set_message(cclang('error_delete', 'uks'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ukss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('uks_view');

		$this->data['uks'] = $this->model_uks->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Uks Detail');
		$this->render('backend/standart/administrator/uks/uks_view', $this->data);
	}
	
	/**
	* delete Ukss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$uks = $this->model_uks->find($id);

		if (!empty($uks->foto)) {
			$path = FCPATH . '/uploads/uks/' . $uks->foto;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_uks->remove($id);
	}
	
	/**
	* Upload Image Uks	* 
	* @return JSON
	*/
	public function upload_foto_file()
	{
		if (!$this->is_allowed('uks_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'uks',
		]);
	}

	/**
	* Delete Image Uks	* 
	* @return JSON
	*/
	public function delete_foto_file($uuid)
	{
		if (!$this->is_allowed('uks_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'foto', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'uks',
            'primary_key'       => 'id_uks',
            'upload_path'       => 'uploads/uks/'
        ]);
	}

	/**
	* Get Image Uks	* 
	* @return JSON
	*/
	public function get_foto_file($id)
	{
		if (!$this->is_allowed('uks_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$uks = $this->model_uks->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto', 
            'table_name'        => 'uks',
            'primary_key'       => 'id_uks',
            'upload_path'       => 'uploads/uks/',
            'delete_endpoint'   => 'administrator/uks/delete_foto_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('uks_export');

		$this->model_uks->export('uks', 'uks');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('uks_export');

		$this->model_uks->pdf('uks', 'uks');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('uks_export');

		$table = $title = 'uks';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_uks->find($id);
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


/* End of file uks.php */
/* Location: ./application/controllers/administrator/Uks.php */