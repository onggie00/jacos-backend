<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Prestasi Siswa Ft Controller
*| --------------------------------------------------------------------------
*| Prestasi Siswa Ft site
*|
*/
class Prestasi_siswa_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_prestasi_siswa_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Prestasi Siswa Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('prestasi_siswa_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['prestasi_siswa_fts'] = $this->model_prestasi_siswa_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['prestasi_siswa_ft_counts'] = $this->model_prestasi_siswa_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/prestasi_siswa_ft/index/',
			'total_rows'   => $this->model_prestasi_siswa_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Prestasi Siswa FT List');
		$this->render('backend/standart/administrator/prestasi_siswa_ft/prestasi_siswa_ft_list', $this->data);
	}
	
	/**
	* Add new prestasi_siswa_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('prestasi_siswa_ft_add');

		$this->template->title('Prestasi Siswa FT New');
		$this->render('backend/standart/administrator/prestasi_siswa_ft/prestasi_siswa_ft_add', $this->data);
	}

	/**
	* Add New Prestasi Siswa Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('prestasi_siswa_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tgl_raih', 'Tanggal Raih', 'trim|required');
		$this->form_validation->set_rules('juara', 'Juara', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('prestasi_siswa_ft_file_prestasi_name', 'File Prestasi', 'trim|required');
		$this->form_validation->set_rules('prestasi_siswa_ft_foto_prestasi_name', 'Foto', 'trim|required');
		$this->form_validation->set_rules('id_siswa', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_prestasi_id', 'Tingkatan Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('is_approved', 'Disetujui?', 'trim|required');
		

		if ($this->form_validation->run()) {
			$prestasi_siswa_ft_file_prestasi_uuid = $this->input->post('prestasi_siswa_ft_file_prestasi_uuid');
			$prestasi_siswa_ft_file_prestasi_name = $this->input->post('prestasi_siswa_ft_file_prestasi_name');
			$prestasi_siswa_ft_foto_prestasi_uuid = $this->input->post('prestasi_siswa_ft_foto_prestasi_uuid');
			$prestasi_siswa_ft_foto_prestasi_name = $this->input->post('prestasi_siswa_ft_foto_prestasi_name');
		
			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'tgl_raih' => $this->input->post('tgl_raih'),
				'juara' => $this->input->post('juara'),
				'id_siswa' => $this->input->post('id_siswa'),
				'jenis_prestasi_id' => $this->input->post('jenis_prestasi_id'),
				'konten' => $this->input->post('konten'),
				'tanggal_posting' => $this->input->post('tanggal_posting'),
				'is_approved' => $this->input->post('is_approved'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_siswa_ft/')) {
				mkdir(FCPATH . '/uploads/prestasi_siswa_ft/');
			}

			if (!empty($prestasi_siswa_ft_file_prestasi_name)) {
				$prestasi_siswa_ft_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_siswa_ft_file_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_siswa_ft_file_prestasi_uuid . '/' . $prestasi_siswa_ft_file_prestasi_name, 
						FCPATH . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_file_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_siswa_ft_file_prestasi_name_copy;
			}
		
			if (!empty($prestasi_siswa_ft_foto_prestasi_name)) {
				$prestasi_siswa_ft_foto_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_siswa_ft_foto_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_siswa_ft_foto_prestasi_uuid . '/' . $prestasi_siswa_ft_foto_prestasi_name, 
						FCPATH . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_foto_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_foto_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_prestasi'] = $prestasi_siswa_ft_foto_prestasi_name_copy;
			}
		
			
			$save_prestasi_siswa_ft = $this->model_prestasi_siswa_ft->store($save_data);
            

			if ($save_prestasi_siswa_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_prestasi_siswa_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/prestasi_siswa_ft/edit/' . $save_prestasi_siswa_ft, 'Edit Prestasi Siswa Ft'),
						anchor('administrator/prestasi_siswa_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/prestasi_siswa_ft/edit/' . $save_prestasi_siswa_ft, 'Edit Prestasi Siswa Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_ft');
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
	* Update view Prestasi Siswa Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('prestasi_siswa_ft_update');

		$this->data['prestasi_siswa_ft'] = $this->model_prestasi_siswa_ft->find($id);

		$this->template->title('Prestasi Siswa FT Update');
		$this->render('backend/standart/administrator/prestasi_siswa_ft/prestasi_siswa_ft_update', $this->data);
	}

	/**
	* Update Prestasi Siswa Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('prestasi_siswa_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tgl_raih', 'Tanggal Raih', 'trim|required');
		$this->form_validation->set_rules('juara', 'Juara', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('prestasi_siswa_ft_file_prestasi_name', 'File Prestasi', 'trim|required');
		$this->form_validation->set_rules('prestasi_siswa_ft_foto_prestasi_name', 'Foto', 'trim|required');
		$this->form_validation->set_rules('id_siswa', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_prestasi_id', 'Tingkatan Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('is_approved', 'Disetujui?', 'trim|required');
		
		if ($this->form_validation->run()) {
			$prestasi_siswa_ft_file_prestasi_uuid = $this->input->post('prestasi_siswa_ft_file_prestasi_uuid');
			$prestasi_siswa_ft_file_prestasi_name = $this->input->post('prestasi_siswa_ft_file_prestasi_name');
			$prestasi_siswa_ft_foto_prestasi_uuid = $this->input->post('prestasi_siswa_ft_foto_prestasi_uuid');
			$prestasi_siswa_ft_foto_prestasi_name = $this->input->post('prestasi_siswa_ft_foto_prestasi_name');
		
			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'tgl_raih' => $this->input->post('tgl_raih'),
				'juara' => $this->input->post('juara'),
				'id_siswa' => $this->input->post('id_siswa'),
				'jenis_prestasi_id' => $this->input->post('jenis_prestasi_id'),
				'konten' => $this->input->post('konten'),
				'tanggal_posting' => $this->input->post('tanggal_posting'),
				'is_approved' => $this->input->post('is_approved'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_siswa_ft/')) {
				mkdir(FCPATH . '/uploads/prestasi_siswa_ft/');
			}

			if (!empty($prestasi_siswa_ft_file_prestasi_uuid)) {
				$prestasi_siswa_ft_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_siswa_ft_file_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_siswa_ft_file_prestasi_uuid . '/' . $prestasi_siswa_ft_file_prestasi_name, 
						FCPATH . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_file_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_siswa_ft_file_prestasi_name_copy;
			}
		
			if (!empty($prestasi_siswa_ft_foto_prestasi_uuid)) {
				$prestasi_siswa_ft_foto_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_siswa_ft_foto_prestasi_name;

				rename(FCPATH . 'uploads/tmp/' . $prestasi_siswa_ft_foto_prestasi_uuid . '/' . $prestasi_siswa_ft_foto_prestasi_name, 
						FCPATH . 'uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_foto_prestasi_name_copy);

				if (!is_file(FCPATH . '/uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft_foto_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_prestasi'] = $prestasi_siswa_ft_foto_prestasi_name_copy;
			}
		
			
			$save_prestasi_siswa_ft = $this->model_prestasi_siswa_ft->change($id, $save_data);

			if ($save_prestasi_siswa_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/prestasi_siswa_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_ft');
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
	* delete Prestasi Siswa Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('prestasi_siswa_ft_delete');

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
            set_message(cclang('has_been_deleted', 'prestasi_siswa_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'prestasi_siswa_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Prestasi Siswa Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('prestasi_siswa_ft_view');

		$this->data['prestasi_siswa_ft'] = $this->model_prestasi_siswa_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Prestasi Siswa FT Detail');
		$this->render('backend/standart/administrator/prestasi_siswa_ft/prestasi_siswa_ft_view', $this->data);
	}
	
	/**
	* delete Prestasi Siswa Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$prestasi_siswa_ft = $this->model_prestasi_siswa_ft->find($id);

		if (!empty($prestasi_siswa_ft->file_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft->file_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($prestasi_siswa_ft->foto_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_siswa_ft/' . $prestasi_siswa_ft->foto_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_prestasi_siswa_ft->remove($id);
	}
	
	/**
	* Upload Image Prestasi Siswa Ft	* 
	* @return JSON
	*/
	public function upload_file_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_siswa_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_siswa_ft',
		]);
	}

	/**
	* Delete Image Prestasi Siswa Ft	* 
	* @return JSON
	*/
	public function delete_file_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_siswa_ft_delete', false)) {
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
            'table_name'        => 'prestasi_siswa_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_siswa_ft/'
        ]);
	}

	/**
	* Get Image Prestasi Siswa Ft	* 
	* @return JSON
	*/
	public function get_file_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_siswa_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$prestasi_siswa_ft = $this->model_prestasi_siswa_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_prestasi', 
            'table_name'        => 'prestasi_siswa_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_siswa_ft/',
            'delete_endpoint'   => 'administrator/prestasi_siswa_ft/delete_file_prestasi_file'
        ]);
	}
	
	/**
	* Upload Image Prestasi Siswa Ft	* 
	* @return JSON
	*/
	public function upload_foto_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_siswa_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_siswa_ft',
		]);
	}

	/**
	* Delete Image Prestasi Siswa Ft	* 
	* @return JSON
	*/
	public function delete_foto_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_siswa_ft_delete', false)) {
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
            'table_name'        => 'prestasi_siswa_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_siswa_ft/'
        ]);
	}

	/**
	* Get Image Prestasi Siswa Ft	* 
	* @return JSON
	*/
	public function get_foto_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_siswa_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$prestasi_siswa_ft = $this->model_prestasi_siswa_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_prestasi', 
            'table_name'        => 'prestasi_siswa_ft',
            'primary_key'       => 'id_prestasi',
            'upload_path'       => 'uploads/prestasi_siswa_ft/',
            'delete_endpoint'   => 'administrator/prestasi_siswa_ft/delete_foto_prestasi_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('prestasi_siswa_ft_export');

		//$this->model_prestasi_siswa_ft->export('prestasi_siswa_ft', 'prestasi_siswa_ft');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id_prestasi', 'nama_lengkap', 'kelas', 'nama_prestasi', 'tgl_raih', 'juara', 'file_prestasi', 'foto_prestasi', 'jenis_prestasi', 'nama_bidang', 'tanggal_posting', 'is_approved'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select p.*, sa.nama_lengkap, k.label as kelas, j.jenis_prestasi, b.nama_bidang from prestasi_siswa_ft p 
				join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi 
				join siswa_ft_aktif sa on p.id_siswa = sa.id_siswa_ft_aktif 
				join kelas_ft k on sa.id_kelas = k.id_kelas_ft and k.label not like '%mutasi%' 
				left join prestasi_siswa_bidang b on p.id_prestasi_bidang = b.id_prestasi_bidang ","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "nama_lengkap"){
                $where .= "(" . "sa.nama_lengkap LIKE '%" . $inputan . "%' )";
            }
            else if($field == "kelas"){
                $where .= "(" . "k.label LIKE '%" . $inputan . "%' )";
            }
            else if($field == "nama_bidang"){
                $where .= "(" . "b.nama_bidang LIKE '%" . $inputan . "%' )";
            }
            else{
                $where .= "(" . "p.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select p.*, sa.nama_lengkap, k.label as kelas, j.jenis_prestasi, b.nama_bidang from prestasi_siswa_ft p 
				join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi 
				join siswa_ft_aktif sa on p.id_siswa = sa.id_siswa_ft_aktif 
				join kelas_ft k on sa.id_kelas = k.id_kelas_ft and k.label not like '%mutasi%' 
				left join prestasi_siswa_bidang b on p.id_prestasi_bidang = b.id_prestasi_bidang 
				where ".$where." 
				","result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "p.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "kelas" || $field == "nama_lengkap" || $field == "nama_bidang"){
	                	continue;
	                }
	                else {
	                    $where .= "OR " . "p.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "k.label LIKE '%" . $inputan . "%' ";
						$where .= "OR " . "sa.nama_lengkap LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select p.*, sa.nama_lengkap, k.label as kelas, j.jenis_prestasi, b.nama_bidang from prestasi_siswa_ft p 
				join jenis_prestasi j on p.jenis_prestasi_id = j.id_jenis_prestasi 
				join siswa_ft_aktif sa on p.id_siswa = sa.id_siswa_ft_aktif 
				join kelas_ft k on sa.id_kelas = k.id_kelas_ft and k.label not like '%mutasi%' 
				left join prestasi_siswa_bidang b on p.id_prestasi_bidang = b.id_prestasi_bidang 
				where ".$where." 
				","result");
		}
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);  
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "id_prestasi") !== false && $i == 0) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if (strpos($field_search[$i], "jenis_prestasi") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TINGKATAN'));
			}
			else if (strpos($field_search[$i], "is_approved") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('DISETUJUI?'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		$no = 1;
		foreach ($get_data as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";
		        }
		        if (strpos($kolom, "id_prestasi") !== false ) {
		        	$value_data = $no++;
		        }
				if (strpos($kolom, "tgl_raih") !== false && !empty($value_data)) {
		        	$value_data = date("d-m-Y",strtotime($value_data));
		        }
		        if (strpos($kolom, "tanggal_posting") !== false && !empty($value_data)) {
		        	$value_data = date("d-m-Y H:i",strtotime($value_data));
		        }
				if (strpos($kolom, "file_prestasi") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/prestasi_siswa_ft/").$value_data;
		        }
				if (strpos($kolom, "foto_prestasi") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/prestasi_siswa_ft/").$value_data;
		        }
		        if (strpos($kolom, "is_approved") !== false && empty($value_data)) {
		        	$value_data = "BELUM DISETUJUI";
		        }
		        else if (strpos($kolom, "is_approved") !== false && !empty($value_data)) {
		        	$value_data = "DISETUJUI";
		        }
		        
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data Prestasi Siswa FT - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('prestasi_siswa_ft_export');

		$this->model_prestasi_siswa_ft->pdf('prestasi_siswa_ft', 'prestasi_siswa_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('prestasi_siswa_ft_export');

		$table = $title = 'prestasi_siswa_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_prestasi_siswa_ft->find($id);
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

	public function update_status($id = null)
	{
		$arr_id = $this->input->get('id');
		$st     = $this->input->get('st');

		$arr_id = $this->input->get('id', TRUE);
		if (empty($arr_id)) {
			set_message('Pilih data dan status terlebih dahulu', 'warning');
			redirect_back();
		}

		$arr_id = array_map('intval', (array) $arr_id);
		$arr_id = array_values(array_filter($arr_id, function ($v) { return $v > 0; }));
		if (empty($arr_id)) {
			set_message('ID tidak valid', 'warning');
			redirect_back();
		}

		if (!in_array((string) $st, array('0', '1', '2'), TRUE)) {
			set_message('Status tidak valid', 'warning');
			redirect_back();
		}

		$this->db->trans_begin();
		$this->db->where_in('id_prestasi', $arr_id);
		$this->db->update('prestasi_siswa_ft', array('is_approved' => (int) $st));
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			set_message('Update status gagal', 'danger');
		} else {
			$this->db->trans_commit();
			set_message('Update status berhasil', 'success');
		}
		redirect_back();
	}
	/**
	 * Convert HEIC to JPEG via heif-convert.
	 */
	public function convert_heic()
	{
		$this->load->helper('heic');

		$file = $this->input->get('file');
		if (empty($file)) {
			echo json_encode(array('success' => false, 'error' => 'Parameter file kosong'));
			return;
		}

		$file = basename($file);
		$original_path = FCPATH . 'uploads/prestasi_siswa_ft/' . $file;

		if (!is_file($original_path)) {
			echo json_encode(array('success' => false, 'error' => 'File tidak ditemukan'));
			return;
		}

		$jpeg_path = convert_heic_to_jpeg($original_path);

		if ($jpeg_path && is_file($jpeg_path)) {
			$jpeg_name = basename($jpeg_path);
			echo json_encode(array(
				'success' => true,
				'url' => base_url('uploads/prestasi_siswa_ft/' . $jpeg_name)
			));
		} else {
			echo json_encode(array('success' => false, 'error' => 'Gagal mengkonversi HEIC'));
		}
	}

}


/* End of file prestasi_siswa_ft.php */
/* Location: ./application/controllers/administrator/Prestasi Siswa Ft.php */