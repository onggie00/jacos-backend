<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Prestasi Guru Ft Controller
*| --------------------------------------------------------------------------
*| Prestasi Guru Ft site
*|
*/
class Prestasi_guru_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_prestasi_guru_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Prestasi Guru Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('prestasi_guru_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['prestasi_guru_fts'] = $this->model_prestasi_guru_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['prestasi_guru_ft_counts'] = $this->model_prestasi_guru_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/prestasi_guru_ft/index/',
			'total_rows'   => $this->model_prestasi_guru_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Prestasi Guru Ft List');
		$this->render('backend/standart/administrator/prestasi_guru_ft/prestasi_guru_ft_list', $this->data);
	}
	
	/**
	* Add new prestasi_guru_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('prestasi_guru_ft_add');

		$this->template->title('Prestasi Guru Ft New');
		$this->render('backend/standart/administrator/prestasi_guru_ft/prestasi_guru_ft_add', $this->data);
	}

	/**
	* Add New Prestasi Guru Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('prestasi_guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_guru', 'Id Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('prestasi_guru_ft_file_prestasi_name', 'File Prestasi', 'trim|required');
		$this->form_validation->set_rules('prestasi_guru_ft_foto_prestasi_name', 'Foto Prestasi', 'trim|required');
		$this->form_validation->set_rules('tgl_raih', 'Tgl Raih', 'trim|required');
		$this->form_validation->set_rules('is_approve', 'Is Approve', 'trim|required');
		

		if ($this->form_validation->run()) {
			$prestasi_guru_ft_file_prestasi_uuid = $this->input->post('prestasi_guru_ft_file_prestasi_uuid');
			$prestasi_guru_ft_file_prestasi_name = $this->input->post('prestasi_guru_ft_file_prestasi_name');
			$prestasi_guru_ft_foto_prestasi_uuid = $this->input->post('prestasi_guru_ft_foto_prestasi_uuid');
			$prestasi_guru_ft_foto_prestasi_name = $this->input->post('prestasi_guru_ft_foto_prestasi_name');
		
			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'id_guru' => $this->input->post('id_guru'),
				'keterangan' => $this->input->post('keterangan'),
				'tgl_raih' => $this->input->post('tgl_raih'),
				'is_approve' => $this->input->post('is_approve'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_guru_ft/')) {
				mkdir(FCPATH . '/uploads/prestasi_guru_ft/');
			}

			if (!empty($prestasi_guru_ft_file_prestasi_name)) {
				$prestasi_guru_ft_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_guru_ft_file_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_guru_ft_file_prestasi_uuid . '/' . $prestasi_guru_ft_file_prestasi_name, 
						FCPATH . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft_file_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_guru_ft/' . $prestasi_guru_ft_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_guru_ft_file_prestasi_name_copy;
			}
		
			if (!empty($prestasi_guru_ft_foto_prestasi_name)) {
				$prestasi_guru_ft_foto_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_guru_ft_foto_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_guru_ft_foto_prestasi_uuid . '/' . $prestasi_guru_ft_foto_prestasi_name, 
						FCPATH . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft_foto_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_guru_ft/' . $prestasi_guru_ft_foto_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_prestasi'] = $prestasi_guru_ft_foto_prestasi_name_copy;
			}
		
			
			$save_prestasi_guru_ft = $this->model_prestasi_guru_ft->store($save_data);
            

			if ($save_prestasi_guru_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_prestasi_guru_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/prestasi_guru_ft/edit/' . $save_prestasi_guru_ft, 'Edit Prestasi Guru Ft'),
						anchor('administrator/prestasi_guru_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/prestasi_guru_ft/edit/' . $save_prestasi_guru_ft, 'Edit Prestasi Guru Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_guru_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_guru_ft');
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
	* Update view Prestasi Guru Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('prestasi_guru_ft_update');

		$this->data['prestasi_guru_ft'] = $this->model_prestasi_guru_ft->find($id);

		$this->template->title('Prestasi Guru Ft Update');
		$this->render('backend/standart/administrator/prestasi_guru_ft/prestasi_guru_ft_update', $this->data);
	}

	/**
	* Update Prestasi Guru Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('prestasi_guru_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_guru', 'Id Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('prestasi_guru_ft_file_prestasi_name', 'File Prestasi', 'trim|required');
		$this->form_validation->set_rules('prestasi_guru_ft_foto_prestasi_name', 'Foto Prestasi', 'trim|required');
		$this->form_validation->set_rules('tgl_raih', 'Tgl Raih', 'trim|required');
		$this->form_validation->set_rules('is_approve', 'Is Approve', 'trim|required');
		
		if ($this->form_validation->run()) {
			$prestasi_guru_ft_file_prestasi_uuid = $this->input->post('prestasi_guru_ft_file_prestasi_uuid');
			$prestasi_guru_ft_file_prestasi_name = $this->input->post('prestasi_guru_ft_file_prestasi_name');
			$prestasi_guru_ft_foto_prestasi_uuid = $this->input->post('prestasi_guru_ft_foto_prestasi_uuid');
			$prestasi_guru_ft_foto_prestasi_name = $this->input->post('prestasi_guru_ft_foto_prestasi_name');
		
			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'id_guru' => $this->input->post('id_guru'),
				'keterangan' => $this->input->post('keterangan'),
				'tgl_raih' => $this->input->post('tgl_raih'),
				'is_approve' => $this->input->post('is_approve'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_guru_ft/')) {
				mkdir(FCPATH . '/uploads/prestasi_guru_ft/');
			}

			if (!empty($prestasi_guru_ft_file_prestasi_uuid)) {
				$prestasi_guru_ft_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_guru_ft_file_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_guru_ft_file_prestasi_uuid . '/' . $prestasi_guru_ft_file_prestasi_name, 
						FCPATH . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft_file_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_guru_ft/' . $prestasi_guru_ft_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_guru_ft_file_prestasi_name_copy;
			}
		
			if (!empty($prestasi_guru_ft_foto_prestasi_uuid)) {
				$prestasi_guru_ft_foto_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_guru_ft_foto_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_guru_ft_foto_prestasi_uuid . '/' . $prestasi_guru_ft_foto_prestasi_name, 
						FCPATH . 'uploads/prestasi_guru_ft/' . $prestasi_guru_ft_foto_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_guru_ft/' . $prestasi_guru_ft_foto_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_prestasi'] = $prestasi_guru_ft_foto_prestasi_name_copy;
			}
		
			
			$save_prestasi_guru_ft = $this->model_prestasi_guru_ft->change($id, $save_data);

			if ($save_prestasi_guru_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/prestasi_guru_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_guru_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_guru_ft');
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
	* delete Prestasi Guru Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('prestasi_guru_ft_delete');

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
            set_message(cclang('has_been_deleted', 'prestasi_guru_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'prestasi_guru_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Prestasi Guru Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('prestasi_guru_ft_view');

		$this->data['prestasi_guru_ft'] = $this->model_prestasi_guru_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Prestasi Guru Ft Detail');
		$this->render('backend/standart/administrator/prestasi_guru_ft/prestasi_guru_ft_view', $this->data);
	}
	
	/**
	* delete Prestasi Guru Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$prestasi_guru_ft = $this->model_prestasi_guru_ft->find($id);

		if (!empty($prestasi_guru_ft->file_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_guru_ft/' . $prestasi_guru_ft->file_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($prestasi_guru_ft->foto_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_guru_ft/' . $prestasi_guru_ft->foto_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_prestasi_guru_ft->remove($id);
	}
	
	/**
	* Upload Image Prestasi Guru Ft	* 
	* @return JSON
	*/
	public function upload_file_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_guru_ft',
		]);
	}

	/**
	* Delete Image Prestasi Guru Ft	* 
	* @return JSON
	*/
	public function delete_file_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_guru_ft_delete', false)) {
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
            'table_name'        => 'prestasi_guru_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_guru_ft/'
        ]);
	}

	/**
	* Get Image Prestasi Guru Ft	* 
	* @return JSON
	*/
	public function get_file_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_guru_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$prestasi_guru_ft = $this->model_prestasi_guru_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_prestasi', 
            'table_name'        => 'prestasi_guru_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_guru_ft/',
            'delete_endpoint'   => 'administrator/prestasi_guru_ft/delete_file_prestasi_file'
        ]);
	}
	
	/**
	* Upload Image Prestasi Guru Ft	* 
	* @return JSON
	*/
	public function upload_foto_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_guru_ft',
		]);
	}

	/**
	* Delete Image Prestasi Guru Ft	* 
	* @return JSON
	*/
	public function delete_foto_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_guru_ft_delete', false)) {
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
            'table_name'        => 'prestasi_guru_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_guru_ft/'
        ]);
	}

	/**
	* Get Image Prestasi Guru Ft	* 
	* @return JSON
	*/
	public function get_foto_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_guru_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$prestasi_guru_ft = $this->model_prestasi_guru_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_prestasi', 
            'table_name'        => 'prestasi_guru_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_guru_ft/',
            'delete_endpoint'   => 'administrator/prestasi_guru_ft/delete_foto_prestasi_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('prestasi_guru_ft_export');

		$this->model_prestasi_guru_ft->export('prestasi_guru_ft', 'prestasi_guru_ft');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('prestasi_guru_ft_export');

		$this->model_prestasi_guru_ft->pdf('prestasi_guru_ft', 'prestasi_guru_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('prestasi_guru_ft_export');

		$table = $title = 'prestasi_guru_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_prestasi_guru_ft->find($id);
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


/* End of file prestasi_guru_ft.php */
/* Location: ./application/controllers/administrator/Prestasi Guru Ft.php */