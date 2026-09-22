<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Foto Pengajar Controller
*| --------------------------------------------------------------------------
*| Foto Pengajar site
*|
*/
class Foto_pengajar extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_foto_pengajar');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Foto Pengajars
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('foto_pengajar_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['foto_pengajars'] = $this->model_foto_pengajar->get($filter, $field, $this->limit_page, $offset);
		$this->data['foto_pengajar_counts'] = $this->model_foto_pengajar->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/foto_pengajar/index/',
			'total_rows'   => $this->model_foto_pengajar->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Foto Pengajar List');
		$this->render('backend/standart/administrator/foto_pengajar/foto_pengajar_list', $this->data);
	}
	
	/**
	* Add new foto_pengajars
	*
	*/
	public function add()
	{
		$this->is_allowed('foto_pengajar_add');

		$this->template->title('Foto Pengajar New');
		$this->render('backend/standart/administrator/foto_pengajar/foto_pengajar_add', $this->data);
	}

	/**
	* Add New Foto Pengajars
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('foto_pengajar_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama', 'Nama', 'trim|required');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$foto_pengajar_foto_uuid = $this->input->post('foto_pengajar_foto_uuid');
			$foto_pengajar_foto_name = $this->input->post('foto_pengajar_foto_name');
		
			$save_data = [
				'nama' => $this->input->post('nama'),
				'jabatan' => $this->input->post('jabatan'),
				'jenjang' => 'ft',
			];

			if (!is_dir(FCPATH . '/uploads/foto_pengajar/')) {
				mkdir(FCPATH . '/uploads/foto_pengajar/');
			}

			if (!empty($foto_pengajar_foto_name)) {
				$foto_pengajar_foto_name_copy = date('YmdHis') . '-' . $foto_pengajar_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $foto_pengajar_foto_uuid . '/' . $foto_pengajar_foto_name, 
						FCPATH . 'uploads/foto_pengajar/' . $foto_pengajar_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/foto_pengajar/' . $foto_pengajar_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $foto_pengajar_foto_name_copy;
			}
		
			
			$save_foto_pengajar = $this->model_foto_pengajar->store($save_data);
            

			if ($save_foto_pengajar) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_foto_pengajar;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/foto_pengajar/edit/' . $save_foto_pengajar, 'Edit Foto Pengajar'),
						anchor('administrator/foto_pengajar', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/foto_pengajar/edit/' . $save_foto_pengajar, 'Edit Foto Pengajar')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/foto_pengajar');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/foto_pengajar');
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
	* Update view Foto Pengajars
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('foto_pengajar_update');

		$this->data['foto_pengajar'] = $this->model_foto_pengajar->find($id);

		$this->template->title('Foto Pengajar Update');
		$this->render('backend/standart/administrator/foto_pengajar/foto_pengajar_update', $this->data);
	}

	/**
	* Update Foto Pengajars
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('foto_pengajar_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama', 'Nama', 'trim|required');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$foto_pengajar_foto_uuid = $this->input->post('foto_pengajar_foto_uuid');
			$foto_pengajar_foto_name = $this->input->post('foto_pengajar_foto_name');
		
			$save_data = [
				'nama' => $this->input->post('nama'),
				'jabatan' => $this->input->post('jabatan'),
				'jenjang' => 'ft',
			];

			if (!is_dir(FCPATH . '/uploads/foto_pengajar/')) {
				mkdir(FCPATH . '/uploads/foto_pengajar/');
			}

			if (!empty($foto_pengajar_foto_uuid)) {
				$foto_pengajar_foto_name_copy = date('YmdHis') . '-' . $foto_pengajar_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $foto_pengajar_foto_uuid . '/' . $foto_pengajar_foto_name, 
						FCPATH . 'uploads/foto_pengajar/' . $foto_pengajar_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/foto_pengajar/' . $foto_pengajar_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $foto_pengajar_foto_name_copy;
			}
		
			
			$save_foto_pengajar = $this->model_foto_pengajar->change($id, $save_data);

			if ($save_foto_pengajar) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/foto_pengajar', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/foto_pengajar');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/foto_pengajar');
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
	* delete Foto Pengajars
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('foto_pengajar_delete');

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
            set_message(cclang('has_been_deleted', 'foto_pengajar'), 'success');
        } else {
            set_message(cclang('error_delete', 'foto_pengajar'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Foto Pengajars
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('foto_pengajar_view');

		$this->data['foto_pengajar'] = $this->model_foto_pengajar->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Foto Pengajar Detail');
		$this->render('backend/standart/administrator/foto_pengajar/foto_pengajar_view', $this->data);
	}
	
	/**
	* delete Foto Pengajars
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$foto_pengajar = $this->model_foto_pengajar->find($id);

		if (!empty($foto_pengajar->foto)) {
			$path = FCPATH . '/uploads/foto_pengajar/' . $foto_pengajar->foto;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_foto_pengajar->remove($id);
	}
	
	/**
	* Upload Image Foto Pengajar	* 
	* @return JSON
	*/
	public function upload_foto_file()
	{
		if (!$this->is_allowed('foto_pengajar_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'foto_pengajar',
		]);
	}

	/**
	* Delete Image Foto Pengajar	* 
	* @return JSON
	*/
	public function delete_foto_file($uuid)
	{
		if (!$this->is_allowed('foto_pengajar_delete', false)) {
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
            'table_name'        => 'foto_pengajar',
            'primary_key'       => 'id_foto_pengajar',
            'upload_path'       => 'uploads/foto_pengajar/'
        ]);
	}

	/**
	* Get Image Foto Pengajar	* 
	* @return JSON
	*/
	public function get_foto_file($id)
	{
		if (!$this->is_allowed('foto_pengajar_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$foto_pengajar = $this->model_foto_pengajar->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto', 
            'table_name'        => 'foto_pengajar',
            'primary_key'       => 'id_foto_pengajar',
            'upload_path'       => 'uploads/foto_pengajar/',
            'delete_endpoint'   => 'administrator/foto_pengajar/delete_foto_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('foto_pengajar_export');

		$this->model_foto_pengajar->export('foto_pengajar', 'foto_pengajar');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('foto_pengajar_export');

		$this->model_foto_pengajar->pdf('foto_pengajar', 'foto_pengajar');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('foto_pengajar_export');

		$table = $title = 'foto_pengajar';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_foto_pengajar->find($id);
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


/* End of file foto_pengajar.php */
/* Location: ./application/controllers/administrator/Foto Pengajar.php */