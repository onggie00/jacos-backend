<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kpi Pegawai Controller
*| --------------------------------------------------------------------------
*| Kpi Pegawai site
*|
*/
class Kpi_pegawai extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kpi_pegawai');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kpi Pegawais
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kpi_pegawai_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kpi_pegawais'] = $this->model_kpi_pegawai->get($filter, $field, $this->limit_page, $offset);
		$this->data['kpi_pegawai_counts'] = $this->model_kpi_pegawai->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kpi_pegawai/index/',
			'total_rows'   => $this->model_kpi_pegawai->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kpi Pegawai List');
		$this->render('backend/standart/administrator/kpi_pegawai/kpi_pegawai_list', $this->data);
	}
	
	/**
	* Add new kpi_pegawais
	*
	*/
	public function add()
	{
		$this->is_allowed('kpi_pegawai_add');

		$this->template->title('Kpi Pegawai New');
		$this->render('backend/standart/administrator/kpi_pegawai/kpi_pegawai_add', $this->data);
	}

	/**
	* Add New Kpi Pegawais
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kpi_pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_kpi', 'Nama Kpi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_pegawai', 'Id Pegawai', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('judul_kpi', 'Judul Kpi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_jenis_kpi', 'Id Jenis Kpi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('kpi_pegawai_file_piagam_name', 'File Piagam', 'trim|required');
		

		if ($this->form_validation->run()) {
			$kpi_pegawai_file_piagam_uuid = $this->input->post('kpi_pegawai_file_piagam_uuid');
			$kpi_pegawai_file_piagam_name = $this->input->post('kpi_pegawai_file_piagam_name');
		
			$save_data = [
				'nama_kpi' => $this->input->post('nama_kpi'),
				'id_pegawai' => $this->input->post('id_pegawai'),
				'judul_kpi' => $this->input->post('judul_kpi'),
				'id_jenis_kpi' => $this->input->post('id_jenis_kpi'),
				'tanggal' => $this->input->post('tanggal'),
				'keterangan' => $this->input->post('keterangan'),
				'is_approved' => $this->input->post('is_approved'),
			];

			if (!is_dir(FCPATH . '/uploads/kpi_pegawai/')) {
				mkdir(FCPATH . '/uploads/kpi_pegawai/');
			}

			if (!empty($kpi_pegawai_file_piagam_name)) {
				$kpi_pegawai_file_piagam_name_copy = date('YmdHis') . '-' . $kpi_pegawai_file_piagam_name;

				rename(FCPATH . 'uploads/tmp/' . $kpi_pegawai_file_piagam_uuid . '/' . $kpi_pegawai_file_piagam_name, 
						FCPATH . 'uploads/kpi_pegawai/' . $kpi_pegawai_file_piagam_name_copy);

				if (!is_file(FCPATH . '/uploads/kpi_pegawai/' . $kpi_pegawai_file_piagam_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_piagam'] = $kpi_pegawai_file_piagam_name_copy;
			}
		
			
			$save_kpi_pegawai = $this->model_kpi_pegawai->store($save_data);
            

			if ($save_kpi_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kpi_pegawai;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kpi_pegawai/edit/' . $save_kpi_pegawai, 'Edit Kpi Pegawai'),
						anchor('administrator/kpi_pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kpi_pegawai/edit/' . $save_kpi_pegawai, 'Edit Kpi Pegawai')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kpi_pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kpi_pegawai');
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
	* Update view Kpi Pegawais
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kpi_pegawai_update');

		$this->data['kpi_pegawai'] = $this->model_kpi_pegawai->find($id);

		$this->template->title('Kpi Pegawai Update');
		$this->render('backend/standart/administrator/kpi_pegawai/kpi_pegawai_update', $this->data);
	}

	/**
	* Update Kpi Pegawais
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kpi_pegawai_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_kpi', 'Nama Kpi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_pegawai', 'Id Pegawai', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('judul_kpi', 'Judul Kpi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_jenis_kpi', 'Id Jenis Kpi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('kpi_pegawai_file_piagam_name', 'File Piagam', 'trim|required');
		
		if ($this->form_validation->run()) {
			$kpi_pegawai_file_piagam_uuid = $this->input->post('kpi_pegawai_file_piagam_uuid');
			$kpi_pegawai_file_piagam_name = $this->input->post('kpi_pegawai_file_piagam_name');
		
			$save_data = [
				'nama_kpi' => $this->input->post('nama_kpi'),
				'id_pegawai' => $this->input->post('id_pegawai'),
				'judul_kpi' => $this->input->post('judul_kpi'),
				'id_jenis_kpi' => $this->input->post('id_jenis_kpi'),
				'tanggal' => $this->input->post('tanggal'),
				'keterangan' => $this->input->post('keterangan'),
				'is_approved' => $this->input->post('is_approved'),
			];

			if (!is_dir(FCPATH . '/uploads/kpi_pegawai/')) {
				mkdir(FCPATH . '/uploads/kpi_pegawai/');
			}

			if (!empty($kpi_pegawai_file_piagam_uuid)) {
				$kpi_pegawai_file_piagam_name_copy = date('YmdHis') . '-' . $kpi_pegawai_file_piagam_name;

				rename(FCPATH . 'uploads/tmp/' . $kpi_pegawai_file_piagam_uuid . '/' . $kpi_pegawai_file_piagam_name, 
						FCPATH . 'uploads/kpi_pegawai/' . $kpi_pegawai_file_piagam_name_copy);

				if (!is_file(FCPATH . '/uploads/kpi_pegawai/' . $kpi_pegawai_file_piagam_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_piagam'] = $kpi_pegawai_file_piagam_name_copy;
			}
		
			
			$save_kpi_pegawai = $this->model_kpi_pegawai->change($id, $save_data);

			if ($save_kpi_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kpi_pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kpi_pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kpi_pegawai');
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
	* delete Kpi Pegawais
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kpi_pegawai_delete');

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
            set_message(cclang('has_been_deleted', 'kpi_pegawai'), 'success');
        } else {
            set_message(cclang('error_delete', 'kpi_pegawai'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kpi Pegawais
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kpi_pegawai_view');

		$this->data['kpi_pegawai'] = $this->model_kpi_pegawai->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kpi Pegawai Detail');
		$this->render('backend/standart/administrator/kpi_pegawai/kpi_pegawai_view', $this->data);
	}
	
	/**
	* delete Kpi Pegawais
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kpi_pegawai = $this->model_kpi_pegawai->find($id);

		if (!empty($kpi_pegawai->file_piagam)) {
			$path = FCPATH . '/uploads/kpi_pegawai/' . $kpi_pegawai->file_piagam;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_kpi_pegawai->remove($id);
	}
	
	/**
	* Upload Image Kpi Pegawai	* 
	* @return JSON
	*/
	public function upload_file_piagam_file()
	{
		if (!$this->is_allowed('kpi_pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'kpi_pegawai',
		]);
	}

	/**
	* Delete Image Kpi Pegawai	* 
	* @return JSON
	*/
	public function delete_file_piagam_file($uuid)
	{
		if (!$this->is_allowed('kpi_pegawai_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_piagam', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'kpi_pegawai',
            'primary_key'       => 'id_kpi',
            'upload_path'       => 'uploads/kpi_pegawai/'
        ]);
	}

	/**
	* Get Image Kpi Pegawai	* 
	* @return JSON
	*/
	public function get_file_piagam_file($id)
	{
		if (!$this->is_allowed('kpi_pegawai_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$kpi_pegawai = $this->model_kpi_pegawai->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_piagam', 
            'table_name'        => 'kpi_pegawai',
            'primary_key'       => 'id_kpi',
            'upload_path'       => 'uploads/kpi_pegawai/',
            'delete_endpoint'   => 'administrator/kpi_pegawai/delete_file_piagam_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kpi_pegawai_export');

		$this->model_kpi_pegawai->export('kpi_pegawai', 'kpi_pegawai');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kpi_pegawai_export');

		$this->model_kpi_pegawai->pdf('kpi_pegawai', 'kpi_pegawai');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kpi_pegawai_export');

		$table = $title = 'kpi_pegawai';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kpi_pegawai->find($id);
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


/* End of file kpi_pegawai.php */
/* Location: ./application/controllers/administrator/Kpi Pegawai.php */