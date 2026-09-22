<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Library Controller
*| --------------------------------------------------------------------------
*| Library site
*|
*/
class Library extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_library');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Librarys
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('library_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['librarys'] = $this->model_library->get($filter, $field, $this->limit_page, $offset);
		$this->data['library_counts'] = $this->model_library->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/library/index/',
			'total_rows'   => $this->model_library->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Library List');
		$this->render('backend/standart/administrator/library/library_list', $this->data);
	}
	
	/**
	* Add new librarys
	*
	*/
	public function add()
	{
		$this->is_allowed('library_add');

		$this->template->title('Library New');
		$this->render('backend/standart/administrator/library/library_add', $this->data);
	}

	/**
	* Add New Librarys
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('library_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('library_foto_name', 'Foto', 'trim|required');
		

		if ($this->form_validation->run()) {
			$library_foto_uuid = $this->input->post('library_foto_uuid');
			$library_foto_name = $this->input->post('library_foto_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
			];

			if (!is_dir(FCPATH . '/uploads/library/')) {
				mkdir(FCPATH . '/uploads/library/');
			}

			if (!empty($library_foto_name)) {
				$library_foto_name_copy = date('YmdHis') . '-' . $library_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $library_foto_uuid . '/' . $library_foto_name, 
						FCPATH . 'uploads/library/' . $library_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/library/' . $library_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $library_foto_name_copy;
			}
		
			
			$save_library = $this->model_library->store($save_data);
            

			if ($save_library) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_library;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/library/edit/' . $save_library, 'Edit Library'),
						anchor('administrator/library', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/library/edit/' . $save_library, 'Edit Library')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/library');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/library');
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
	* Update view Librarys
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('library_update');

		$this->data['library'] = $this->model_library->find($id);

		$this->template->title('Library Update');
		$this->render('backend/standart/administrator/library/library_update', $this->data);
	}

	/**
	* Update Librarys
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('library_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('library_foto_name', 'Foto', 'trim|required');
		
		if ($this->form_validation->run()) {
			$library_foto_uuid = $this->input->post('library_foto_uuid');
			$library_foto_name = $this->input->post('library_foto_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
			];

			if (!is_dir(FCPATH . '/uploads/library/')) {
				mkdir(FCPATH . '/uploads/library/');
			}

			if (!empty($library_foto_uuid)) {
				$library_foto_name_copy = date('YmdHis') . '-' . $library_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $library_foto_uuid . '/' . $library_foto_name, 
						FCPATH . 'uploads/library/' . $library_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/library/' . $library_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $library_foto_name_copy;
			}
		
			
			$save_library = $this->model_library->change($id, $save_data);

			if ($save_library) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/library', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/library');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/library');
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
	* delete Librarys
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('library_delete');

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
            set_message(cclang('has_been_deleted', 'library'), 'success');
        } else {
            set_message(cclang('error_delete', 'library'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Librarys
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('library_view');

		$this->data['library'] = $this->model_library->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Library Detail');
		$this->render('backend/standart/administrator/library/library_view', $this->data);
	}
	
	/**
	* delete Librarys
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$library = $this->model_library->find($id);

		if (!empty($library->foto)) {
			$path = FCPATH . '/uploads/library/' . $library->foto;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_library->remove($id);
	}
	
	/**
	* Upload Image Library	* 
	* @return JSON
	*/
	public function upload_foto_file()
	{
		if (!$this->is_allowed('library_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'library',
		]);
	}

	/**
	* Delete Image Library	* 
	* @return JSON
	*/
	public function delete_foto_file($uuid)
	{
		if (!$this->is_allowed('library_delete', false)) {
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
            'table_name'        => 'library',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/library/'
        ]);
	}

	/**
	* Get Image Library	* 
	* @return JSON
	*/
	public function get_foto_file($id)
	{
		if (!$this->is_allowed('library_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$library = $this->model_library->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto', 
            'table_name'        => 'library',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/library/',
            'delete_endpoint'   => 'administrator/library/delete_foto_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('library_export');

		$this->model_library->export('library', 'library');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('library_export');

		$this->model_library->pdf('library', 'library');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('library_export');

		$table = $title = 'library';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_library->find($id);
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


/* End of file library.php */
/* Location: ./application/controllers/administrator/Library.php */