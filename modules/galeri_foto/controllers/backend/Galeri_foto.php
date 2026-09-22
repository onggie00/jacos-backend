<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Galeri Foto Controller
*| --------------------------------------------------------------------------
*| Galeri Foto site
*|
*/
class Galeri_foto extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_galeri_foto');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Galeri Fotos
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('galeri_foto_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['galeri_fotos'] = $this->model_galeri_foto->get($filter, $field, $this->limit_page, $offset);
		$this->data['galeri_foto_counts'] = $this->model_galeri_foto->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/galeri_foto/index/',
			'total_rows'   => $this->model_galeri_foto->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Galeri Foto List');
		$this->render('backend/standart/administrator/galeri_foto/galeri_foto_list', $this->data);
	}
	
	/**
	* Add new galeri_fotos
	*
	*/
	public function add()
	{
		$this->is_allowed('galeri_foto_add');

		$this->template->title('Galeri Foto New');
		$this->render('backend/standart/administrator/galeri_foto/galeri_foto_add', $this->data);
	}

	/**
	* Add New Galeri Fotos
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('galeri_foto_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('galeri_foto_foto_name', 'Foto', 'trim|required');
		

		if ($this->form_validation->run()) {
			$galeri_foto_foto_uuid = $this->input->post('galeri_foto_foto_uuid');
			$galeri_foto_foto_name = $this->input->post('galeri_foto_foto_name');
		
			$save_data = [
				'label' => $this->input->post('label'),
				'jenjang' => 'ft',
			];

			if (!is_dir(FCPATH . '/uploads/galeri_foto/')) {
				mkdir(FCPATH . '/uploads/galeri_foto/');
			}

			if (!empty($galeri_foto_foto_name)) {
				$galeri_foto_foto_name_copy = date('YmdHis') . '-' . $galeri_foto_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $galeri_foto_foto_uuid . '/' . $galeri_foto_foto_name, 
						FCPATH . 'uploads/galeri_foto/' . $galeri_foto_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/galeri_foto/' . $galeri_foto_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $galeri_foto_foto_name_copy;
			}
		
			
			$save_galeri_foto = $this->model_galeri_foto->store($save_data);
            

			if ($save_galeri_foto) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_galeri_foto;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/galeri_foto/edit/' . $save_galeri_foto, 'Edit Galeri Foto'),
						anchor('administrator/galeri_foto', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/galeri_foto/edit/' . $save_galeri_foto, 'Edit Galeri Foto')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/galeri_foto');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/galeri_foto');
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
	* Update view Galeri Fotos
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('galeri_foto_update');

		$this->data['galeri_foto'] = $this->model_galeri_foto->find($id);

		$this->template->title('Galeri Foto Update');
		$this->render('backend/standart/administrator/galeri_foto/galeri_foto_update', $this->data);
	}

	/**
	* Update Galeri Fotos
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('galeri_foto_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('galeri_foto_foto_name', 'Foto', 'trim|required');
		
		if ($this->form_validation->run()) {
			$galeri_foto_foto_uuid = $this->input->post('galeri_foto_foto_uuid');
			$galeri_foto_foto_name = $this->input->post('galeri_foto_foto_name');
		
			$save_data = [
				'label' => $this->input->post('label'),
				'jenjang' => 'ft',
			];

			if (!is_dir(FCPATH . '/uploads/galeri_foto/')) {
				mkdir(FCPATH . '/uploads/galeri_foto/');
			}

			if (!empty($galeri_foto_foto_uuid)) {
				$galeri_foto_foto_name_copy = date('YmdHis') . '-' . $galeri_foto_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $galeri_foto_foto_uuid . '/' . $galeri_foto_foto_name, 
						FCPATH . 'uploads/galeri_foto/' . $galeri_foto_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/galeri_foto/' . $galeri_foto_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $galeri_foto_foto_name_copy;
			}
		
			
			$save_galeri_foto = $this->model_galeri_foto->change($id, $save_data);

			if ($save_galeri_foto) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/galeri_foto', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/galeri_foto');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/galeri_foto');
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
	* delete Galeri Fotos
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('galeri_foto_delete');

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
            set_message(cclang('has_been_deleted', 'galeri_foto'), 'success');
        } else {
            set_message(cclang('error_delete', 'galeri_foto'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Galeri Fotos
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('galeri_foto_view');

		$this->data['galeri_foto'] = $this->model_galeri_foto->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Galeri Foto Detail');
		$this->render('backend/standart/administrator/galeri_foto/galeri_foto_view', $this->data);
	}
	
	/**
	* delete Galeri Fotos
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$galeri_foto = $this->model_galeri_foto->find($id);

		if (!empty($galeri_foto->foto)) {
			$path = FCPATH . '/uploads/galeri_foto/' . $galeri_foto->foto;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_galeri_foto->remove($id);
	}
	
	/**
	* Upload Image Galeri Foto	* 
	* @return JSON
	*/
	public function upload_foto_file()
	{
		if (!$this->is_allowed('galeri_foto_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'galeri_foto',
		]);
	}

	/**
	* Delete Image Galeri Foto	* 
	* @return JSON
	*/
	public function delete_foto_file($uuid)
	{
		if (!$this->is_allowed('galeri_foto_delete', false)) {
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
            'table_name'        => 'galeri_foto',
            'primary_key'       => 'id_galeri',
            'upload_path'       => 'uploads/galeri_foto/'
        ]);
	}

	/**
	* Get Image Galeri Foto	* 
	* @return JSON
	*/
	public function get_foto_file($id)
	{
		if (!$this->is_allowed('galeri_foto_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$galeri_foto = $this->model_galeri_foto->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto', 
            'table_name'        => 'galeri_foto',
            'primary_key'       => 'id_galeri',
            'upload_path'       => 'uploads/galeri_foto/',
            'delete_endpoint'   => 'administrator/galeri_foto/delete_foto_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('galeri_foto_export');

		$this->model_galeri_foto->export('galeri_foto', 'galeri_foto');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('galeri_foto_export');

		$this->model_galeri_foto->pdf('galeri_foto', 'galeri_foto');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('galeri_foto_export');

		$table = $title = 'galeri_foto';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_galeri_foto->find($id);
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


/* End of file galeri_foto.php */
/* Location: ./application/controllers/administrator/Galeri Foto.php */