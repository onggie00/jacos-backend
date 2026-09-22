<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pimpinan Controller
*| --------------------------------------------------------------------------
*| Pimpinan site
*|
*/
class Pimpinan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pimpinan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pimpinans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pimpinan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pimpinans'] = $this->model_pimpinan->get($filter, $field, $this->limit_page, $offset);
		$this->data['pimpinan_counts'] = $this->model_pimpinan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pimpinan/index/',
			'total_rows'   => $this->model_pimpinan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Struktur Pimpinan List');
		$this->render('backend/standart/administrator/pimpinan/pimpinan_list', $this->data);
	}
	
	/**
	* Add new pimpinans
	*
	*/
	public function add()
	{
		$this->is_allowed('pimpinan_add');

		$this->template->title('Struktur Pimpinan New');
		$this->render('backend/standart/administrator/pimpinan/pimpinan_add', $this->data);
	}

	/**
	* Add New Pimpinans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pimpinan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_pimpinan', 'Nama Pimpinan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('pimpinan_foto_pimpinan_name', 'Foto Pimpinan', 'trim|required');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$pimpinan_foto_pimpinan_uuid = $this->input->post('pimpinan_foto_pimpinan_uuid');
			$pimpinan_foto_pimpinan_name = $this->input->post('pimpinan_foto_pimpinan_name');
		
			$save_data = [
				'nama_pimpinan' => $this->input->post('nama_pimpinan'),
				'jabatan' => $this->input->post('jabatan'),
				'jenjang' => $this->input->post('jenjang'),
			];

			if (!is_dir(FCPATH . '/uploads/pimpinan/')) {
				mkdir(FCPATH . '/uploads/pimpinan/');
			}

			if (!empty($pimpinan_foto_pimpinan_name)) {
				$pimpinan_foto_pimpinan_name_copy = date('YmdHis') . '-' . $pimpinan_foto_pimpinan_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_foto_pimpinan_uuid . '/' . $pimpinan_foto_pimpinan_name, 
						FCPATH . 'uploads/pimpinan/' . $pimpinan_foto_pimpinan_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan/' . $pimpinan_foto_pimpinan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_pimpinan'] = $pimpinan_foto_pimpinan_name_copy;
			}
		
			
			$save_pimpinan = $this->model_pimpinan->store($save_data);
            

			if ($save_pimpinan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pimpinan;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pimpinan/edit/' . $save_pimpinan, 'Edit Pimpinan'),
						anchor('administrator/pimpinan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pimpinan/edit/' . $save_pimpinan, 'Edit Pimpinan')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pimpinan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pimpinan');
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
	* Update view Pimpinans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pimpinan_update');

		$this->data['pimpinan'] = $this->model_pimpinan->find($id);

		$this->template->title('Struktur Pimpinan Update');
		$this->render('backend/standart/administrator/pimpinan/pimpinan_update', $this->data);
	}

	/**
	* Update Pimpinans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pimpinan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_pimpinan', 'Nama Pimpinan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('pimpinan_foto_pimpinan_name', 'Foto Pimpinan', 'trim|required');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$pimpinan_foto_pimpinan_uuid = $this->input->post('pimpinan_foto_pimpinan_uuid');
			$pimpinan_foto_pimpinan_name = $this->input->post('pimpinan_foto_pimpinan_name');
		
			$save_data = [
				'nama_pimpinan' => $this->input->post('nama_pimpinan'),
				'jabatan' => $this->input->post('jabatan'),
				'jenjang' => $this->input->post('jenjang'),
			];

			if (!is_dir(FCPATH . '/uploads/pimpinan/')) {
				mkdir(FCPATH . '/uploads/pimpinan/');
			}

			if (!empty($pimpinan_foto_pimpinan_uuid)) {
				$pimpinan_foto_pimpinan_name_copy = date('YmdHis') . '-' . $pimpinan_foto_pimpinan_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_foto_pimpinan_uuid . '/' . $pimpinan_foto_pimpinan_name, 
						FCPATH . 'uploads/pimpinan/' . $pimpinan_foto_pimpinan_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan/' . $pimpinan_foto_pimpinan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_pimpinan'] = $pimpinan_foto_pimpinan_name_copy;
			}
		
			
			$save_pimpinan = $this->model_pimpinan->change($id, $save_data);

			if ($save_pimpinan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pimpinan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pimpinan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pimpinan');
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
	* delete Pimpinans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pimpinan_delete');

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
            set_message(cclang('has_been_deleted', 'pimpinan'), 'success');
        } else {
            set_message(cclang('error_delete', 'pimpinan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pimpinans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pimpinan_view');

		$this->data['pimpinan'] = $this->model_pimpinan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Struktur Pimpinan Detail');
		$this->render('backend/standart/administrator/pimpinan/pimpinan_view', $this->data);
	}
	
	/**
	* delete Pimpinans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pimpinan = $this->model_pimpinan->find($id);

		if (!empty($pimpinan->foto_pimpinan)) {
			$path = FCPATH . '/uploads/pimpinan/' . $pimpinan->foto_pimpinan;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_pimpinan->remove($id);
	}
	
	/**
	* Upload Image Pimpinan	* 
	* @return JSON
	*/
	public function upload_foto_pimpinan_file()
	{
		if (!$this->is_allowed('pimpinan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pimpinan',
		]);
	}

	/**
	* Delete Image Pimpinan	* 
	* @return JSON
	*/
	public function delete_foto_pimpinan_file($uuid)
	{
		if (!$this->is_allowed('pimpinan_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'foto_pimpinan', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'pimpinan',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan/'
        ]);
	}

	/**
	* Get Image Pimpinan	* 
	* @return JSON
	*/
	public function get_foto_pimpinan_file($id)
	{
		if (!$this->is_allowed('pimpinan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pimpinan = $this->model_pimpinan->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_pimpinan', 
            'table_name'        => 'pimpinan',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan/',
            'delete_endpoint'   => 'administrator/pimpinan/delete_foto_pimpinan_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pimpinan_export');

		$this->model_pimpinan->export('pimpinan', 'pimpinan');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pimpinan_export');

		$this->model_pimpinan->pdf('pimpinan', 'pimpinan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pimpinan_export');

		$table = $title = 'pimpinan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pimpinan->find($id);
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


/* End of file pimpinan.php */
/* Location: ./application/controllers/administrator/Pimpinan.php */