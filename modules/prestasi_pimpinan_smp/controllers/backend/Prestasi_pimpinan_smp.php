<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Prestasi Pimpinan Smp Controller
*| --------------------------------------------------------------------------
*| Prestasi Pimpinan Smp site
*|
*/
class Prestasi_pimpinan_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_prestasi_pimpinan_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Prestasi Pimpinan Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('prestasi_pimpinan_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['prestasi_pimpinan_smps'] = $this->model_prestasi_pimpinan_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['prestasi_pimpinan_smp_counts'] = $this->model_prestasi_pimpinan_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/prestasi_pimpinan_smp/index/',
			'total_rows'   => $this->model_prestasi_pimpinan_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Prestasi Pimpinan Smp List');
		$this->render('backend/standart/administrator/prestasi_pimpinan_smp/prestasi_pimpinan_smp_list', $this->data);
	}
	
	/**
	* Add new prestasi_pimpinan_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('prestasi_pimpinan_smp_add');

		$this->template->title('Prestasi Pimpinan Smp New');
		$this->render('backend/standart/administrator/prestasi_pimpinan_smp/prestasi_pimpinan_smp_add', $this->data);
	}

	/**
	* Add New Prestasi Pimpinan Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_pimpinan', 'Id Pimpinan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('prestasi_pimpinan_smp_file_prestasi_name', 'File Prestasi', 'trim|required');
		$this->form_validation->set_rules('prestasi_pimpinan_smp_foto_prestasi_name', 'Foto Prestasi', 'trim|required');
		$this->form_validation->set_rules('tgl_raih', 'Tgl Raih', 'trim|required');
		

		if ($this->form_validation->run()) {
			$prestasi_pimpinan_smp_file_prestasi_uuid = $this->input->post('prestasi_pimpinan_smp_file_prestasi_uuid');
			$prestasi_pimpinan_smp_file_prestasi_name = $this->input->post('prestasi_pimpinan_smp_file_prestasi_name');
			$prestasi_pimpinan_smp_foto_prestasi_uuid = $this->input->post('prestasi_pimpinan_smp_foto_prestasi_uuid');
			$prestasi_pimpinan_smp_foto_prestasi_name = $this->input->post('prestasi_pimpinan_smp_foto_prestasi_name');
		
			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'id_pimpinan' => $this->input->post('id_pimpinan'),
				'keterangan' => $this->input->post('keterangan'),
				'tgl_raih' => $this->input->post('tgl_raih'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_pimpinan_smp/')) {
				mkdir(FCPATH . '/uploads/prestasi_pimpinan_smp/');
			}

			if (!empty($prestasi_pimpinan_smp_file_prestasi_name)) {
				$prestasi_pimpinan_smp_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_pimpinan_smp_file_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_pimpinan_smp_file_prestasi_uuid . '/' . $prestasi_pimpinan_smp_file_prestasi_name, 
						FCPATH . 'uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_file_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_pimpinan_smp_file_prestasi_name_copy;
			}
		
			if (!empty($prestasi_pimpinan_smp_foto_prestasi_name)) {
				$prestasi_pimpinan_smp_foto_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_pimpinan_smp_foto_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_pimpinan_smp_foto_prestasi_uuid . '/' . $prestasi_pimpinan_smp_foto_prestasi_name, 
						FCPATH . 'uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_foto_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_foto_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_prestasi'] = $prestasi_pimpinan_smp_foto_prestasi_name_copy;
			}
		
			
			$save_prestasi_pimpinan_smp = $this->model_prestasi_pimpinan_smp->store($save_data);
            

			if ($save_prestasi_pimpinan_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_prestasi_pimpinan_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/prestasi_pimpinan_smp/edit/' . $save_prestasi_pimpinan_smp, 'Edit Prestasi Pimpinan Smp'),
						anchor('administrator/prestasi_pimpinan_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/prestasi_pimpinan_smp/edit/' . $save_prestasi_pimpinan_smp, 'Edit Prestasi Pimpinan Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_pimpinan_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_pimpinan_smp');
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
	* Update view Prestasi Pimpinan Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('prestasi_pimpinan_smp_update');

		$this->data['prestasi_pimpinan_smp'] = $this->model_prestasi_pimpinan_smp->find($id);

		$this->template->title('Prestasi Pimpinan Smp Update');
		$this->render('backend/standart/administrator/prestasi_pimpinan_smp/prestasi_pimpinan_smp_update', $this->data);
	}

	/**
	* Update Prestasi Pimpinan Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_pimpinan', 'Id Pimpinan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('prestasi_pimpinan_smp_file_prestasi_name', 'File Prestasi', 'trim|required');
		$this->form_validation->set_rules('prestasi_pimpinan_smp_foto_prestasi_name', 'Foto Prestasi', 'trim|required');
		$this->form_validation->set_rules('tgl_raih', 'Tgl Raih', 'trim|required');
		
		if ($this->form_validation->run()) {
			$prestasi_pimpinan_smp_file_prestasi_uuid = $this->input->post('prestasi_pimpinan_smp_file_prestasi_uuid');
			$prestasi_pimpinan_smp_file_prestasi_name = $this->input->post('prestasi_pimpinan_smp_file_prestasi_name');
			$prestasi_pimpinan_smp_foto_prestasi_uuid = $this->input->post('prestasi_pimpinan_smp_foto_prestasi_uuid');
			$prestasi_pimpinan_smp_foto_prestasi_name = $this->input->post('prestasi_pimpinan_smp_foto_prestasi_name');
		
			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'id_pimpinan' => $this->input->post('id_pimpinan'),
				'keterangan' => $this->input->post('keterangan'),
				'tgl_raih' => $this->input->post('tgl_raih'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_pimpinan_smp/')) {
				mkdir(FCPATH . '/uploads/prestasi_pimpinan_smp/');
			}

			if (!empty($prestasi_pimpinan_smp_file_prestasi_uuid)) {
				$prestasi_pimpinan_smp_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_pimpinan_smp_file_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_pimpinan_smp_file_prestasi_uuid . '/' . $prestasi_pimpinan_smp_file_prestasi_name, 
						FCPATH . 'uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_file_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_pimpinan_smp_file_prestasi_name_copy;
			}
		
			if (!empty($prestasi_pimpinan_smp_foto_prestasi_uuid)) {
				$prestasi_pimpinan_smp_foto_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_pimpinan_smp_foto_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_pimpinan_smp_foto_prestasi_uuid . '/' . $prestasi_pimpinan_smp_foto_prestasi_name, 
						FCPATH . 'uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_foto_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp_foto_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_prestasi'] = $prestasi_pimpinan_smp_foto_prestasi_name_copy;
			}
		
			
			$save_prestasi_pimpinan_smp = $this->model_prestasi_pimpinan_smp->change($id, $save_data);

			if ($save_prestasi_pimpinan_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/prestasi_pimpinan_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_pimpinan_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_pimpinan_smp');
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
	* delete Prestasi Pimpinan Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('prestasi_pimpinan_smp_delete');

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
            set_message(cclang('has_been_deleted', 'prestasi_pimpinan_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'prestasi_pimpinan_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Prestasi Pimpinan Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('prestasi_pimpinan_smp_view');

		$this->data['prestasi_pimpinan_smp'] = $this->model_prestasi_pimpinan_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Prestasi Pimpinan Smp Detail');
		$this->render('backend/standart/administrator/prestasi_pimpinan_smp/prestasi_pimpinan_smp_view', $this->data);
	}
	
	/**
	* delete Prestasi Pimpinan Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$prestasi_pimpinan_smp = $this->model_prestasi_pimpinan_smp->find($id);

		if (!empty($prestasi_pimpinan_smp->file_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp->file_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($prestasi_pimpinan_smp->foto_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_pimpinan_smp/' . $prestasi_pimpinan_smp->foto_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_prestasi_pimpinan_smp->remove($id);
	}
	
	/**
	* Upload Image Prestasi Pimpinan Smp	* 
	* @return JSON
	*/
	public function upload_file_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_pimpinan_smp',
		]);
	}

	/**
	* Delete Image Prestasi Pimpinan Smp	* 
	* @return JSON
	*/
	public function delete_file_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_prestasi', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'prestasi_pimpinan_smp',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_pimpinan_smp/'
        ]);
	}

	/**
	* Get Image Prestasi Pimpinan Smp	* 
	* @return JSON
	*/
	public function get_file_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$prestasi_pimpinan_smp = $this->model_prestasi_pimpinan_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_prestasi', 
            'table_name'        => 'prestasi_pimpinan_smp',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_pimpinan_smp/',
            'delete_endpoint'   => 'administrator/prestasi_pimpinan_smp/delete_file_prestasi_file'
        ]);
	}
	
	/**
	* Upload Image Prestasi Pimpinan Smp	* 
	* @return JSON
	*/
	public function upload_foto_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_pimpinan_smp',
		]);
	}

	/**
	* Delete Image Prestasi Pimpinan Smp	* 
	* @return JSON
	*/
	public function delete_foto_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'foto_prestasi', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'prestasi_pimpinan_smp',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_pimpinan_smp/'
        ]);
	}

	/**
	* Get Image Prestasi Pimpinan Smp	* 
	* @return JSON
	*/
	public function get_foto_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_pimpinan_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$prestasi_pimpinan_smp = $this->model_prestasi_pimpinan_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_prestasi', 
            'table_name'        => 'prestasi_pimpinan_smp',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_pimpinan_smp/',
            'delete_endpoint'   => 'administrator/prestasi_pimpinan_smp/delete_foto_prestasi_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('prestasi_pimpinan_smp_export');

		$this->model_prestasi_pimpinan_smp->export('prestasi_pimpinan_smp', 'prestasi_pimpinan_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('prestasi_pimpinan_smp_export');

		$this->model_prestasi_pimpinan_smp->pdf('prestasi_pimpinan_smp', 'prestasi_pimpinan_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('prestasi_pimpinan_smp_export');

		$table = $title = 'prestasi_pimpinan_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_prestasi_pimpinan_smp->find($id);
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


/* End of file prestasi_pimpinan_smp.php */
/* Location: ./application/controllers/administrator/Prestasi Pimpinan Smp.php */