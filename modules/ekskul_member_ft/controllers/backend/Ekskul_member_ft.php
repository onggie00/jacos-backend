<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ekskul Member Ft Controller
*| --------------------------------------------------------------------------
*| Ekskul Member Ft site
*|
*/
class Ekskul_member_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ekskul_member_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ekskul Member Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ekskul_member_ft_list');
		$this->limit_page = 20;
		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['data_list'] = $this->model_ekskul_member_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['value_counts'] = $this->model_ekskul_member_ft->count_all($filter, $field);

		// Stats
		$this->data['stats'] = new stdClass();
		$this->data['stats']->total = $this->model_ekskul_member_ft->count_all();
		$this->data['stats']->aktif = $this->db->where('status_member', 1)->where('jenjang', 'ft')->count_all_results('ekskul_member_ft');
		$this->data['stats']->tidak_aktif = $this->db->where('status_member', 0)->where('jenjang', 'ft')->count_all_results('ekskul_member_ft');
		$this->data['stats']->ekskul_count = $this->db->where('jenjang', 'ft')->count_all_results('ekskul');
		$this->data['stats']->sudah_bayar = $this->db->where('jenjang', 'ft')->where('file_pembayaran !=', '')->count_all_results('ekskul_member_ft');

		$config = array(
			'base_url'	     => 'administrator/ekskul_member_ft/index/',
			'total_rows'	   => $this->data['value_counts'],
			'per_page'	     => $this->limit_page,
			'uri_segment'	  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Anggota Ekskul FT');
		$this->render('backend/standart/administrator/ekskul_member_ft/ekskul_member_ft_list', $this->data);
	}

	public function filter($offset = 0)
	{
		$this->is_allowed('ekskul_member_ft_list');
		$limit = 20;
		$get = $this->input->get();
		$where = "";
		if (!empty($get['id_ekskul']) && $get['id_ekskul'][0] !== '') {
			foreach ($get['id_ekskul'] as $key => $value) {
				if ($where == "") {
					$where = "where (m.id_ekskul = '".$value."'";
				}
				else{
					$where .= " or m.id_ekskul = '".$value."'";
				}
			}
		}
		else{
			$get_all_ekskul = $this->mymodel->withquery("select id_ekskul, nama from ekskul where jenjang = 'ft' or jenjang = 'sma' order by nama ASC","result");
			foreach ($get_all_ekskul as $key => $value) {
				if ($where == "") {
					$where = "where (m.id_ekskul = '".$value->id_ekskul."'";
				}
				else{
					$where .= " or m.id_ekskul = '".$value->id_ekskul."'";
				}
			}
		}
		if(!empty($where)){
			$where .= ")";
		}
		if (!empty($get['tahun_ajaran'])) {
			if ($where == "") {
				$where .= "where m.tahun_ajaran = '".urldecode($get['tahun_ajaran'])."'";
			}
			else{
				$where .= " and m.tahun_ajaran = '".urldecode($get['tahun_ajaran'])."'";
			}
		}
		if (!empty($get['semester'])) {
			if ($where == "") {
				$where .= "where m.semester = '".$get['semester']."'";
			}
			else{
				$where .= " and m.semester = '".$get['semester']."'";
			}
		}
		if (!empty($get['id_kelas'])) {
			if ($where == "") {
				$where .= "where k.id_kelas_ft = '".$get['id_kelas']."'";
			}
			else{
				$where .= " and k.id_kelas_ft = '".$get['id_kelas']."'";
			}
		}
		if (!empty($get['id_tingkatan'])) {
			if ($where == "") {
				$where .= "where k.id_tingkatan = '".$get['id_tingkatan']."'";
			}
			else{
				$where .= " and k.id_tingkatan = '".$get['id_tingkatan']."'";
			}
		}
		if ($get['status_member'] != "") {
			if ($where == "") {
				$where .= "where m.status_member = '".$get['status_member']."'";
			}
			else{
				$where .= " and m.status_member = '".$get['status_member']."'";
			}
		}
		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$file_condition = $get['file_pembayaran'] === 'sudah_upload'
				? "(m.file_pembayaran IS NOT NULL AND m.file_pembayaran != '')"
				: "(m.file_pembayaran IS NULL OR m.file_pembayaran = '')";
			$where = $where == "" ? "where ".$file_condition : $where." and ".$file_condition;
		}
		if (!empty($get['nama_lengkap'])) {
			if ($where == "") {
				$where .= "where s.nama_lengkap like '%".$get['nama_lengkap']."%'";
			}
			else{
				$where .= " and s.nama_lengkap like '%".$get['nama_lengkap']."%'";
			}
		}
		
		$get_data = $this->mymodel->withquery("select m.*, s.nama_lengkap, s.nis, k.id_kelas_ft as id_kelas, k.label as nama_kelas, e.nama as nama_ekskul, 
			case when m.status_member = '0' then 'Tidak Aktif' 
			when m.status_member = '1' then 'Aktif' end as status_member_text
			from ekskul_member_ft m join ekskul e on m.id_ekskul = e.id_ekskul 
			join siswa_ft_aktif s on m.id_siswa = s.id_siswa_ft_aktif 
			join kelas_ft k on s.id_kelas = k.id_kelas_ft ".$where." order by s.nama_lengkap ASC limit ".$offset.",".$limit." ","result");
		//echo $this->db->last_query();
		$get_data_count = $this->mymodel->withquery("select m.id_member from ekskul_member_ft m join ekskul e on m.id_ekskul = e.id_ekskul join siswa_ft_aktif s on m.id_siswa = s.id_siswa_ft_aktif join kelas_ft k on s.id_kelas = k.id_kelas_ft ".$where." order by s.nama_lengkap ASC ","result");
		$total_rows = count($get_data_count);

		$config = array(
			'base_url'     => 'administrator/ekskul_member_ft/filter/',
			'total_rows'   => $total_rows,
			'per_page'     => $limit,
			'uri_segment'  => 4,
		);

		$this->data['data_list'] = $get_data;
		$this->data['value_counts'] = $total_rows;
		$this->data['pagination'] = $this->pagination($config);

		// Stats
		$this->data['stats'] = new stdClass();
		$this->data['stats']->total = $this->model_ekskul_member_ft->count_all();
		$this->data['stats']->aktif = $this->db->where('status_member', 1)->where('jenjang', 'ft')->count_all_results('ekskul_member_ft');
		$this->data['stats']->tidak_aktif = $this->db->where('status_member', 0)->where('jenjang', 'ft')->count_all_results('ekskul_member_ft');
		$this->data['stats']->ekskul_count = $this->db->where('jenjang', 'ft')->count_all_results('ekskul');
		$this->data['stats']->sudah_bayar = $this->db->where('jenjang', 'ft')->where('file_pembayaran !=', '')->count_all_results('ekskul_member_ft');

		$this->template->title('Anggota Ekskul FT');
		$this->render('backend/standart/administrator/ekskul_member_ft/ekskul_member_ft_list', $this->data);
	}
	
	/**
	* Add new ekskul_member_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('ekskul_member_ft_add');

		$this->template->title('Anggota Ekskul FT New');
		$this->render('backend/standart/administrator/ekskul_member_ft/ekskul_member_ft_add', $this->data);
	}

	/**
	* Add New Ekskul Member Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ekskul_member_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('status_member', 'Status', 'trim|required');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required');
		

		if ($this->form_validation->run()) {
			$ekskul_member_ft_file_pembayaran_uuid = $this->input->post('ekskul_member_ft_file_pembayaran_uuid');
			$ekskul_member_ft_file_pembayaran_name = $this->input->post('ekskul_member_ft_file_pembayaran_name');
		
			$save_data = [
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_siswa' => $this->input->post('id_siswa'),
				'status_member' => $this->input->post('status_member'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			if (!is_dir(FCPATH . '/uploads/ekskul_member_ft/')) {
				mkdir(FCPATH . '/uploads/ekskul_member_ft/');
			}

			if (!empty($ekskul_member_ft_file_pembayaran_name)) {
				$ekskul_member_ft_file_pembayaran_name_copy = date('YmdHis') . '-' . $ekskul_member_ft_file_pembayaran_name;

				rename(FCPATH . 'uploads/tmp/' . $ekskul_member_ft_file_pembayaran_uuid . '/' . $ekskul_member_ft_file_pembayaran_name, 
						FCPATH . 'uploads/ekskul_member_ft/' . $ekskul_member_ft_file_pembayaran_name_copy);

				if (!is_file(FCPATH . '/uploads/ekskul_member_ft/' . $ekskul_member_ft_file_pembayaran_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_pembayaran'] = $ekskul_member_ft_file_pembayaran_name_copy;
			}
		
			
			$save_ekskul_member_ft = $this->model_ekskul_member_ft->store($save_data);
            

			if ($save_ekskul_member_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ekskul_member_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ekskul_member_ft/edit/' . $save_ekskul_member_ft, 'Edit Ekskul Member Ft'),
						anchor('administrator/ekskul_member_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ekskul_member_ft/edit/' . $save_ekskul_member_ft, 'Edit Ekskul Member Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_member_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_member_ft');
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
	* Update view Ekskul Member Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ekskul_member_ft_update');

		$this->data['ekskul_member_ft'] = $this->model_ekskul_member_ft->find($id);

		$this->template->title('Anggota Ekskul FT Update');
		$this->render('backend/standart/administrator/ekskul_member_ft/ekskul_member_ft_update', $this->data);
	}

	/**
	* Update Ekskul Member Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ekskul_member_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('status_member', 'Status', 'trim|required');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required');
		
		if ($this->form_validation->run()) {
			$ekskul_member_ft_file_pembayaran_uuid = $this->input->post('ekskul_member_ft_file_pembayaran_uuid');
			$ekskul_member_ft_file_pembayaran_name = $this->input->post('ekskul_member_ft_file_pembayaran_name');
		
			$save_data = [
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_siswa' => $this->input->post('id_siswa'),
				'status_member' => $this->input->post('status_member'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			if (!is_dir(FCPATH . '/uploads/ekskul_member_ft/')) {
				mkdir(FCPATH . '/uploads/ekskul_member_ft/');
			}

			if (!empty($ekskul_member_ft_file_pembayaran_uuid)) {
				$ekskul_member_ft_file_pembayaran_name_copy = date('YmdHis') . '-' . $ekskul_member_ft_file_pembayaran_name;

				rename(FCPATH . 'uploads/tmp/' . $ekskul_member_ft_file_pembayaran_uuid . '/' . $ekskul_member_ft_file_pembayaran_name, 
						FCPATH . 'uploads/ekskul_member_ft/' . $ekskul_member_ft_file_pembayaran_name_copy);

				if (!is_file(FCPATH . '/uploads/ekskul_member_ft/' . $ekskul_member_ft_file_pembayaran_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_pembayaran'] = $ekskul_member_ft_file_pembayaran_name_copy;
			}
		
			
			$save_ekskul_member_ft = $this->model_ekskul_member_ft->change($id, $save_data);

			if ($save_ekskul_member_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ekskul_member_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_member_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_member_ft');
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
	* delete Ekskul Member Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ekskul_member_ft_delete');

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
            set_message(cclang('has_been_deleted', 'ekskul_member_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'ekskul_member_ft'), 'error');
        }

		redirect_back();
	}

	public function activate_member($id = null)
	{
		$arr_id = $this->input->get('id');
		$activate = false;

		if (!empty($id)) {
			$activate = $this->mymodel->update("ekskul_member_ft", array("status_member" => 1), "id_member", $id);
		} elseif (is_array($arr_id) && count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$activate = $this->mymodel->update("ekskul_member_ft", array("status_member" => 1), "id_member", $id);
			}
		}

		if ($activate) {
            set_message("Aktivasi anggota ekstrakurikuler FT berhasil", 'success');
        } else {
            set_message("Aktivasi anggota ekstrakurikuler FT gagal", 'error');
        }

		redirect_back();
	}

	public function deactivate_member($id = null)
	{
		$arr_id = $this->input->get('id');
		$deactivate = false;

		if (!empty($id)) {
			$deactivate = $this->mymodel->update("ekskul_member_ft", array("status_member" => 0), "id_member", $id);
		} elseif (is_array($arr_id) && count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$deactivate = $this->mymodel->update("ekskul_member_ft", array("status_member" => 0), "id_member", $id);
			}
		}

		if ($deactivate) {
            set_message("Deaktivasi anggota ekstrakurikuler FT berhasil", 'success');
        } else {
            set_message("Deaktivasi anggota ekstrakurikuler FT gagal", 'error');
        }

		redirect_back();
	}

		/**
	* View view Ekskul Member Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ekskul_member_ft_view');

		$this->data['ekskul_member_ft'] = $this->model_ekskul_member_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Anggota Ekskul FT Detail');
		$this->render('backend/standart/administrator/ekskul_member_ft/ekskul_member_ft_view', $this->data);
	}
	
	/**
	* delete Ekskul Member Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ekskul_member_ft = $this->model_ekskul_member_ft->find($id);

		if (!empty($ekskul_member_ft->file_pembayaran)) {
			$path = FCPATH . '/uploads/ekskul_member_ft/' . $ekskul_member_ft->file_pembayaran;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_ekskul_member_ft->remove($id);
	}
	
	/**
	* Upload Image Ekskul Member Ft	* 
	* @return JSON
	*/
	public function upload_file_pembayaran_file()
	{
		if (!$this->is_allowed('ekskul_member_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'ekskul_member_ft',
		]);
	}

	/**
	* Delete Image Ekskul Member Ft	* 
	* @return JSON
	*/
	public function delete_file_pembayaran_file($uuid)
	{
		if (!$this->is_allowed('ekskul_member_ft_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_pembayaran', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'ekskul_member_ft',
            'primary_key'       => 'id_member',
            'upload_path'       => 'uploads/ekskul_member_ft/'
        ]);
	}

	/**
	* Get Image Ekskul Member Ft	* 
	* @return JSON
	*/
	public function get_file_pembayaran_file($id)
	{
		if (!$this->is_allowed('ekskul_member_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$ekskul_member_ft = $this->model_ekskul_member_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_pembayaran', 
            'table_name'        => 'ekskul_member_ft',
            'primary_key'       => 'id_member',
            'upload_path'       => 'uploads/ekskul_member_ft/',
            'delete_endpoint'   => 'administrator/ekskul_member_ft/delete_file_pembayaran_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ekskul_member_ft_export');

		$get = $this->input->get();
		$field = ['nomor', 'id_siswa', 'nis', 'nama_kelas', 'pilihan_1', 'pilihan_2', 'pilihan_3', 'pilihan_4', 'pilihan_5', 'tahun_ajaran', 'semester'];
		$whereClauses = [];
		//set default query
		$whereClauses[] = "m.tahun_ajaran != '' AND (m.semester != '0') ";

		// Build where
		if (!empty($get['id_ekskul'])) {
			$idList = array_map(function($v) { return "'".$v."'"; }, $get['id_ekskul']);
			$whereClauses[] = "m.id_ekskul IN (" . implode(",", $idList) . ")";
		}
		if (!empty($get['tahun_ajaran'])) {
			$whereClauses[] = "m.tahun_ajaran = '".urldecode($get['tahun_ajaran'])."'";
		}
		if (!empty($get['semester'])) {
			$whereClauses[] = "m.semester = '".$get['semester']."'";
		}
		if (!empty($get['id_kelas'])) {
			$whereClauses[] = "s.id_kelas = '".$get['id_kelas']."'";
		}
		if (!empty($get['id_tingkatan'])) {
			$whereClauses[] = "k.id_tingkatan = '".$get['id_tingkatan']."'";
		}
		if (!empty($get['nama_lengkap'])) {
			$whereClauses[] = "s.nama_lengkap LIKE '%".$get['nama_lengkap']."%'";
		}
		if (isset($get['status_member']) && $get['status_member'] !== "") {
			$whereClauses[] = "m.status_member = '".$get['status_member']."'";
		} else {
			$whereClauses[] = "(m.status_member = '1' OR m.status_member = '0')";
		}

		$where = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

		// Ambil semua siswa
		$student_status_filter = '';
		if (isset($get['status_member']) && $get['status_member'] !== '') {
			$student_status_filter = " AND EXISTS (SELECT 1 FROM ekskul_member_ft em WHERE em.id_siswa = s.id_siswa_ft_aktif AND em.status_member = '".$get['status_member']."')";
		}
		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$student_status_filter .= $get['file_pembayaran'] === 'sudah_upload'
				? " AND EXISTS (SELECT 1 FROM ekskul_member_ft em WHERE em.id_siswa = s.id_siswa_ft_aktif AND em.file_pembayaran IS NOT NULL AND em.file_pembayaran != '')"
				: " AND EXISTS (SELECT 1 FROM ekskul_member_ft em WHERE em.id_siswa = s.id_siswa_ft_aktif AND (em.file_pembayaran IS NULL OR em.file_pembayaran = ''))";
		}
		if (!empty($get['id_kelas'])) {
			$get_siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_ft_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				FROM siswa_ft_aktif s JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
				WHERE s.id_kelas = '".$get['id_kelas']."'{$student_status_filter}
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");
		} elseif (!empty($get['id_tingkatan'])) {
			$get_siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_ft_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				FROM siswa_ft_aktif s JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
				WHERE k.id_tingkatan = '".$get['id_tingkatan']."'{$student_status_filter}
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");
		} else {

			$get_siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_ft_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				FROM ekskul_member_ft m
				JOIN ekskul e ON m.id_ekskul = e.id_ekskul AND (e.jenjang = 'ft' OR e.jenjang = 'sma')
				JOIN siswa_ft_aktif s ON m.id_siswa = s.id_siswa_ft_aktif
				JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
				$where
				GROUP BY m.id_siswa
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");
		}
		
		// Ambil semua ekskul siswa sekaligus
		$idList = array_column($get_siswa, 'id_siswa');
		$listEkskul = [];
		if ($idList) {
			$ekskul_status_where = (isset($get['status_member']) && $get['status_member'] !== '') ? " AND m.status_member = '".$get['status_member']."'" : "";
			$allEkskul = $this->mymodel->withquery("
				SELECT m.id_siswa, e.nama, m.status_member
				FROM ekskul_member_ft m
				JOIN ekskul e ON m.id_ekskul = e.id_ekskul
				WHERE m.semester = '".$get['semester']."' and m.tahun_ajaran = '".urldecode($get['tahun_ajaran'])."' and m.id_siswa IN (".implode(",", $idList)."){$ekskul_status_where}
			", "result");

			foreach ($allEkskul as $ex) {
				$status = $ex->status_member == "1" ? "Aktif" : ($ex->status_member == "0" ? "Tidak Aktif" : "Belum Daftar");
				$listEkskul[$ex->id_siswa][] = "(".$status.") ".$ex->nama;
			}
		}

		// Isi pilihan dan pastikan pramuka di pilihan_1
		foreach ($get_siswa as $s) {
			$ekskulList = $listEkskul[$s->id_siswa] ?? [];
			
			// Pastikan Pramuka di depan
			usort($ekskulList, function($a, $b) {
				return (stripos($a, 'pramuka') !== false) ? -1 : ((stripos($b, 'pramuka') !== false) ? 1 : 0);
			});

			foreach ($ekskulList as $i => $ek) {
				$s->{"pilihan_".($i+1)} = $ek;
			}
		}

		// Export Excel
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		// Header
		$column = 'A';
		foreach ($field as $f) {
			$header = ($f == "nomor") ? "NO" : (($f == "id_siswa") ? "SISWA" : strtoupper(str_replace("_", " ", $f)));
			$objPHPExcel->getActiveSheet()->setCellValue($column.'1', $header);
			$column++;
		}

		// Data
		$row = 2;
		foreach ($get_siswa as $idx => $s) {
			$column = 'A';
			foreach ($field as $f) {
				$val = $f === "nomor" ? $idx+1 :
					($f === "id_kelas" ? $s->nama_kelas :
					($f === "id_siswa" ? $s->nama_lengkap : ($s->$f ?? "")));
				$objPHPExcel->getActiveSheet()->setCellValue($column.$row, $val);
				$column++;
			}
			$row++;
		}

		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Pilihan Ekstrakurikuler Siswa FT - '.date("Y-m-d").'.xls"'); 
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}


	public function export_list_anggota()
	{
		$this->is_allowed('ekskul_member_ft_export');

		$get = $this->input->get();
		$field = ['nomor', 'id_siswa', 'nis', 'nama_kelas', 'nama_ekskul', 'tahun_ajaran', 'semester', 'status_member'];

		$conditions = [];

		if (!empty($get['id_ekskul'])) {
			$ids = (array)$get['id_ekskul'];
			$ids = array_map('intval', $ids);
			$conditions[] = "m.id_ekskul IN (" . implode(',', $ids) . ")";
		} else {
			$conditions[] = "m.id_ekskul IN (SELECT id_ekskul FROM ekskul WHERE (jenjang = 'ft' OR jenjang = 'sma') )";
		}

		if (!empty($get['tahun_ajaran'])) {
			$conditions[] = "m.tahun_ajaran = '" . urldecode($get['tahun_ajaran']) . "'";
		}

		if (!empty($get['semester'])) {
			$conditions[] = "m.semester = '" . $get['semester'] . "'";
		}

		if (!empty($get['id_kelas'])) {
			$conditions[] = "s.id_kelas = '" . $get['id_kelas'] . "'";
		}

		if (!empty($get['id_tingkatan'])) {
			$conditions[] = "k.id_tingkatan = '" . $get['id_tingkatan'] . "'";
		}

		if (!empty($get['nama_lengkap'])) {
			$safe = $this->db->escape_like_str($get['nama_lengkap']);
			$conditions[] = "s.nama_lengkap LIKE '%{$safe}%'";
		}

		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$conditions[] = $get['file_pembayaran'] === 'sudah_upload'
				? "(m.file_pembayaran IS NOT NULL AND m.file_pembayaran != '')"
				: "(m.file_pembayaran IS NULL OR m.file_pembayaran = '')";
		}
		if (isset($get['status_member']) && $get['status_member'] !== '') {
			$conditions[] = "m.status_member = " . $this->db->escape($get['status_member']);
		} else {
			$conditions[] = "(m.status_member IN ('1','0'))";
		}

		$where = "WHERE " . implode(' AND ', $conditions);

		$all_data = $this->mymodel->withquery("
			SELECT 
				e.id_ekskul, e.nama AS nama_ekskul, 
				m.status_member, m.semester, m.tahun_ajaran,
				s.id_siswa_ft_aktif, s.nis, s.nama_lengkap, 
				k.label AS nama_kelas
			FROM ekskul_member_ft m
			JOIN ekskul e ON m.id_ekskul = e.id_ekskul
			JOIN siswa_ft_aktif s ON m.id_siswa = s.id_siswa_ft_aktif
			JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
			{$where}
			ORDER BY e.id_ekskul, k.label ASC, s.nama_lengkap ASC
		", "result");

		// Group hanya berdasarkan ekskul
		$grouped = [];
		foreach ($all_data as $row) {
			$grouped[$row->id_ekskul]['nama_ekskul'] = $row->nama_ekskul;
			$grouped[$row->id_ekskul]['siswa'][] = $row;
		}

		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();

		$sheetIndex = 0;
		foreach ($grouped as $g) {
			if ($sheetIndex > 0) {
				$objPHPExcel->createSheet();
			}
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$nama_sheet = strtoupper(str_replace('/', '_', $g['nama_ekskul']));
			// ponytail: PHPExcel max 31 char sheet title, truncate
			$objPHPExcel->getActiveSheet()->setTitle(substr($nama_sheet, 0, 31));

			// Header
			$headers = [
				'nomor' => 'NO',
				'id_siswa' => 'SISWA',
				'nama_ekskul' => 'EKSTRAKURIKULER',
				'status_member' => 'KEANGGOTAAN'
			];
			$col = 'A';
			foreach ($field as $colName) {
				$header = $headers[$colName] ?? strtoupper(str_replace("_", " ", $colName));
				$objPHPExcel->getActiveSheet()->setCellValue($col . '1', $header);
				$col++;
			}

			// Data
			$rowCount = 2;
			foreach ($g['siswa'] as $i => $siswa) {
				$col = 'A';
				foreach ($field as $colName) {
					switch ($colName) {
						case 'nomor':
							$value_data = $i + 1;
							break;
						case 'id_kelas':
							$value_data = $siswa->nama_kelas;
							break;
						case 'id_siswa':
							$value_data = $siswa->nama_lengkap;
							break;
						case 'status_member':
							$value_data = $siswa->status_member == 1 ? 'Aktif' : 'Tidak Aktif';
							break;
						default:
							$value_data = isset($siswa->$colName) ? strip_tags($siswa->$colName) : '';
					}
					$objPHPExcel->getActiveSheet()->setCellValue($col . $rowCount, $value_data);
					$col++;
				}
				$rowCount++;
			}

			$sheetIndex++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Daftar Anggota Ekstrakurikuler Siswa FT - ' . date("Y-m-d") . '.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function export_rekap_pembayaran()
	{
		$this->is_allowed('ekskul_member_ft_export');

		$get = $this->input->get();
		$field = [
			'nomor', 'nama_ekskul', 'tahun_ajaran', 'semester',
			'nominal_biaya', 'total_member_bayar', 'total_member_belum_bayar', 'total_diterima'
		];

		$conditions = [];

		if (!empty($get['id_ekskul'])) {
			$ids = array_map('intval', (array)$get['id_ekskul']);
			$conditions[] = "m.id_ekskul IN (" . implode(',', $ids) . ")";
		} else {
			$conditions[] = "m.id_ekskul IN (SELECT id_ekskul FROM ekskul WHERE jenjang = 'ft')";
		}

		if (!empty($get['tahun_ajaran'])) {
			$conditions[] = "m.tahun_ajaran = " . $this->db->escape(urldecode($get['tahun_ajaran']));
		}

		if (!empty($get['semester'])) {
			$conditions[] = "m.semester = " . $this->db->escape($get['semester']);
		}

		if (!empty($get['id_kelas'])) {
			$conditions[] = "s.id_kelas = " . $this->db->escape($get['id_kelas']);
		}

		if (!empty($get['id_tingkatan'])) {
			$conditions[] = "k.id_tingkatan = " . $this->db->escape($get['id_tingkatan']);
		}

		if (!empty($get['nama_lengkap'])) {
			$safe = $this->db->escape_like_str($get['nama_lengkap']);
			$conditions[] = "s.nama_lengkap LIKE '%{$safe}%'";
		}

		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$conditions[] = $get['file_pembayaran'] === 'sudah_upload'
				? "(m.file_pembayaran IS NOT NULL AND m.file_pembayaran != '')"
				: "(m.file_pembayaran IS NULL OR m.file_pembayaran = '')";
		}
		if (isset($get['status_member']) && $get['status_member'] !== '') {
			$conditions[] = "m.status_member = " . $this->db->escape($get['status_member']);
		} else {
			$conditions[] = "(m.status_member IN ('1','0'))";
		}
		$where = "WHERE " . implode(' AND ', $conditions);

		$all_data = $this->mymodel->withquery("
			SELECT 
				e.id_ekskul, e.nama AS nama_ekskul, e.nominal_biaya, m.semester, m.tahun_ajaran,
				(SELECT COUNT(id_member) 
				FROM ekskul_member_ft 
				WHERE id_ekskul = e.id_ekskul 
				AND tahun_ajaran = m.tahun_ajaran 
				AND semester = m.semester 
				AND file_pembayaran != '') AS total_member_bayar,
				(SELECT COUNT(id_member) 
				FROM ekskul_member_ft 
				WHERE id_ekskul = e.id_ekskul 
				AND tahun_ajaran = m.tahun_ajaran 
				AND semester = m.semester 
				AND file_pembayaran = '') AS total_member_belum_bayar
			FROM ekskul e
			JOIN ekskul_member_ft m ON e.id_ekskul = m.id_ekskul 
			JOIN siswa_ft_aktif s ON m.id_siswa = s.id_siswa_ft_aktif
			{$where}
			GROUP BY e.id_ekskul, m.tahun_ajaran, m.semester
			ORDER BY e.id_ekskul, e.nama ASC
		", "result");

		// Siapkan data rekap
		$rekap = [];
		$grand_total_bayar = 0;
		$grand_total_belum_bayar = 0;
		$grand_total_diterima = 0;

		foreach ($all_data as $row) {
			$total_diterima = $row->total_member_bayar * $row->nominal_biaya;
			$rekap[] = [
				'nama_ekskul'              => $row->nama_ekskul,
				'tahun_ajaran'             => $row->tahun_ajaran,
				'semester'                 => $row->semester,
				'nominal_biaya'            => $row->nominal_biaya,
				'total_member_bayar'       => $row->total_member_bayar,
				'total_member_belum_bayar' => $row->total_member_belum_bayar,
				'total_diterima'           => $total_diterima,
			];

			$grand_total_bayar       += $row->total_member_bayar;
			$grand_total_belum_bayar += $row->total_member_belum_bayar;
			$grand_total_diterima    += $total_diterima;
		}

		// Export Excel
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle("REKAP PEMBAYARAN");

		// Header
		$headers = [
			'nomor'                   => 'NO',
			'nama_ekskul'             => 'EKSTRAKURIKULER',
			'tahun_ajaran'            => 'TAHUN AJARAN',
			'semester'                => 'SEMESTER',
			'nominal_biaya'           => 'NOMINAL BIAYA',
			'total_member_bayar'      => 'TOTAL ANGGOTA BAYAR',
			'total_member_belum_bayar'=> 'TOTAL ANGGOTA BELUM BAYAR',
			'total_diterima'          => 'TOTAL DITERIMA',
		];

		$col = 'A';
		foreach ($field as $colName) {
			$sheet->setCellValue($col . '1', $headers[$colName] ?? strtoupper($colName));
			$col++;
		}

		// Data
		$rowCount = 2;
		foreach ($rekap as $i => $r) {
			$col = 'A';
			foreach ($field as $colName) {
				$value = ($colName === 'nomor') ? $i + 1 : $r[$colName];
				$sheet->setCellValue($col . $rowCount, $value);
				$col++;
			}
			$rowCount++;
		}

		// Tambahkan GRAND TOTAL
		$sheet->setCellValue('A' . $rowCount, 'TOTAL');
		$sheet->mergeCells('A' . $rowCount . ':E' . $rowCount);
		$sheet->setCellValue('F' . $rowCount, $grand_total_bayar);
		$sheet->setCellValue('G' . $rowCount, $grand_total_belum_bayar);
		$sheet->setCellValue('H' . $rowCount, $grand_total_diterima);

		// Bold untuk total
		$sheet->getStyle('A' . $rowCount . ':H' . $rowCount)->getFont()->setBold(true);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Rekap Pembayaran Ekstrakurikuler FT - ' . date("Y-m-d") . '.xls"');
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
		$this->is_allowed('ekskul_member_ft_export');

		$this->model_ekskul_member_ft->pdf('ekskul_member_ft', 'ekskul_member_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ekskul_member_ft_export');

		$table = $title = 'ekskul_member_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ekskul_member_ft->find($id);
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

	
	/**
	 * Convert HEIC to JPEG via server-side Imagick.
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
		$original_path = FCPATH . 'uploads/ekskul_member_ft/' . $file;

		if (!is_file($original_path)) {
			echo json_encode(array('success' => false, 'error' => 'File tidak ditemukan'));
			return;
		}

		$jpeg_path = convert_heic_to_jpeg($original_path);

		if ($jpeg_path && is_file($jpeg_path)) {
			$jpeg_name = basename($jpeg_path);
			echo json_encode(array(
				'success' => true,
				'url' => base_url('uploads/ekskul_member_ft/' . $jpeg_name)
			));
		} else {
			echo json_encode(array('success' => false, 'error' => 'Gagal mengkonversi HEIC'));
		}
	}

}


/* End of file ekskul_member_ft.php */
/* Location: ./application/controllers/administrator/Ekskul Member Ft.php */