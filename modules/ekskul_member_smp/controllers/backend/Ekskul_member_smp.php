<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ekskul Member Smp Controller
*| --------------------------------------------------------------------------
*| Ekskul Member Smp site
*|
*/
class Ekskul_member_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ekskul_member_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ekskul Member Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ekskul_member_smp_list');
		$this->limit_page = 20;
		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['data_list'] = $this->model_ekskul_member_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['value_counts'] = $this->model_ekskul_member_smp->count_all($filter, $field);

		// Stats
		$this->data['stats'] = new stdClass();
		$this->data['stats']->total = $this->model_ekskul_member_smp->count_all();
		$this->data['stats']->aktif = $this->db->where('status_member', 1)->where('jenjang', 'smp')->count_all_results('ekskul_member_smp');
		$this->data['stats']->tidak_aktif = $this->db->where('status_member', 0)->where('jenjang', 'smp')->count_all_results('ekskul_member_smp');
		$this->data['stats']->ekskul_count = $this->db->where('jenjang', 'smp')->count_all_results('ekskul');
		$this->data['stats']->sudah_bayar = $this->db->where('jenjang', 'smp')->where('file_pembayaran !=', '')->count_all_results('ekskul_member_smp');

		$config = array(
			'base_url'     => 'administrator/ekskul_member_smp/index/',
			'total_rows'   => $this->data['value_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Anggota Ekskul SMP');
		$this->render('backend/standart/administrator/ekskul_member_smp/ekskul_member_smp_list', $this->data);
	}

	public function filter($offset = 0)
	{
		$this->is_allowed('ekskul_member_smp_list');
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
			$get_all_ekskul = $this->mymodel->withquery("select id_ekskul, nama from ekskul where jenjang = 'smp' order by nama ASC","result");
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
				$where .= "where k.id_kelas_smp = '".$get['id_kelas']."'";
			}
			else{
				$where .= " and k.id_kelas_smp = '".$get['id_kelas']."'";
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
			if ($where == "") {
				$where .= "where (".$file_condition.")";
			} else {
				$where .= " and (".$file_condition.")";
			}
		}
		if (!empty($get['nama_lengkap'])) {
			if ($where == "") {
				$where .= "where s.nama_lengkap like '%".$get['nama_lengkap']."%'";
			}
			else{
				$where .= " and s.nama_lengkap like '%".$get['nama_lengkap']."%'";
			}
		}
		
		$get_data = $this->mymodel->withquery("select m.*, s.nama_lengkap, s.nis, k.id_kelas_smp as id_kelas, k.label as nama_kelas, e.nama as nama_ekskul, 
			case when m.status_member = '0' then 'Tidak Aktif' 
			when m.status_member = '1' then 'Aktif' end as status_member_text
			from ekskul_member_smp m join ekskul e on m.id_ekskul = e.id_ekskul 
			join siswa_smp_aktif s on m.id_siswa = s.id_siswa_smp_aktif 
			join kelas_smp k on s.id_kelas = k.id_kelas_smp ".$where." order by s.nama_lengkap ASC limit ".$offset.",".$limit." ","result");
		//echo $this->db->last_query();
		$get_data_count = $this->mymodel->withquery("select m.id_member from ekskul_member_smp m join ekskul e on m.id_ekskul = e.id_ekskul join siswa_smp_aktif s on m.id_siswa = s.id_siswa_smp_aktif join kelas_smp k on s.id_kelas = k.id_kelas_smp ".$where." order by s.nama_lengkap ASC ","result");
		$total_rows = count($get_data_count);

		$config = array(
			'base_url'     => 'administrator/ekskul_member_smp/filter/',
			'total_rows'   => $total_rows,
			'per_page'     => $limit,
			'uri_segment'  => 4,
		);

		$this->data['data_list'] = $get_data;
		$this->data['value_counts'] = $total_rows;
		$this->data['pagination'] = $this->pagination($config);

		// Stats
		$this->data['stats'] = new stdClass();
		$this->data['stats']->total = $this->model_ekskul_member_smp->count_all();
		$this->data['stats']->aktif = $this->db->where('status_member', 1)->where('jenjang', 'smp')->count_all_results('ekskul_member_smp');
		$this->data['stats']->tidak_aktif = $this->db->where('status_member', 0)->where('jenjang', 'smp')->count_all_results('ekskul_member_smp');
		$this->data['stats']->ekskul_count = $this->db->where('jenjang', 'smp')->count_all_results('ekskul');
		$this->data['stats']->sudah_bayar = $this->db->where('jenjang', 'smp')->where('file_pembayaran !=', '')->count_all_results('ekskul_member_smp');

		$this->template->title('Anggota Ekskul SMP');
		$this->render('backend/standart/administrator/ekskul_member_smp/ekskul_member_smp_list', $this->data);
	}
	
	/**
	* Add new ekskul_member_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('ekskul_member_smp_add');

		$this->template->title('Anggota Ekskul SMP New');
		$this->render('backend/standart/administrator/ekskul_member_smp/ekskul_member_smp_add', $this->data);
	}

	/**
	* Add New Ekskul Member Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ekskul_member_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_ekskul', 'Ektrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('status_member', 'Status', 'trim|required');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required');
		

		if ($this->form_validation->run()) {
			$ekskul_member_smp_file_pembayaran_uuid = $this->input->post('ekskul_member_smp_file_pembayaran_uuid');
			$ekskul_member_smp_file_pembayaran_name = $this->input->post('ekskul_member_smp_file_pembayaran_name');
		
			$save_data = [
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_siswa' => $this->input->post('id_siswa'),
				'status_member' => $this->input->post('status_member'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			if (!is_dir(FCPATH . '/uploads/ekskul_member_smp/')) {
				mkdir(FCPATH . '/uploads/ekskul_member_smp/');
			}

			if (!empty($ekskul_member_smp_file_pembayaran_name)) {
				$ekskul_member_smp_file_pembayaran_name_copy = date('YmdHis') . '-' . $ekskul_member_smp_file_pembayaran_name;

				rename(FCPATH . 'uploads/tmp/' . $ekskul_member_smp_file_pembayaran_uuid . '/' . $ekskul_member_smp_file_pembayaran_name, 
						FCPATH . 'uploads/ekskul_member_smp/' . $ekskul_member_smp_file_pembayaran_name_copy);

				if (!is_file(FCPATH . '/uploads/ekskul_member_smp/' . $ekskul_member_smp_file_pembayaran_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_pembayaran'] = $ekskul_member_smp_file_pembayaran_name_copy;
			}
		
			
			$save_ekskul_member_smp = $this->model_ekskul_member_smp->store($save_data);
            

			if ($save_ekskul_member_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ekskul_member_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ekskul_member_smp/edit/' . $save_ekskul_member_smp, 'Edit Ekskul Member Smp'),
						anchor('administrator/ekskul_member_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ekskul_member_smp/edit/' . $save_ekskul_member_smp, 'Edit Ekskul Member Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_member_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_member_smp');
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
	* Update view Ekskul Member Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ekskul_member_smp_update');

		$this->data['ekskul_member_smp'] = $this->model_ekskul_member_smp->find($id);

		$this->template->title('Anggota Ekskul SMP Update');
		$this->render('backend/standart/administrator/ekskul_member_smp/ekskul_member_smp_update', $this->data);
	}

	/**
	* Update Ekskul Member Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ekskul_member_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ekskul', 'Ektrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('status_member', 'Status', 'trim|required');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required');
		
		if ($this->form_validation->run()) {
			$ekskul_member_smp_file_pembayaran_uuid = $this->input->post('ekskul_member_smp_file_pembayaran_uuid');
			$ekskul_member_smp_file_pembayaran_name = $this->input->post('ekskul_member_smp_file_pembayaran_name');
		
			$save_data = [
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_siswa' => $this->input->post('id_siswa'),
				'status_member' => $this->input->post('status_member'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			if (!is_dir(FCPATH . '/uploads/ekskul_member_smp/')) {
				mkdir(FCPATH . '/uploads/ekskul_member_smp/');
			}

			if (!empty($ekskul_member_smp_file_pembayaran_uuid)) {
				$ekskul_member_smp_file_pembayaran_name_copy = date('YmdHis') . '-' . $ekskul_member_smp_file_pembayaran_name;

				rename(FCPATH . 'uploads/tmp/' . $ekskul_member_smp_file_pembayaran_uuid . '/' . $ekskul_member_smp_file_pembayaran_name, 
						FCPATH . 'uploads/ekskul_member_smp/' . $ekskul_member_smp_file_pembayaran_name_copy);

				if (!is_file(FCPATH . '/uploads/ekskul_member_smp/' . $ekskul_member_smp_file_pembayaran_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_pembayaran'] = $ekskul_member_smp_file_pembayaran_name_copy;
			}
		
			
			$save_ekskul_member_smp = $this->model_ekskul_member_smp->change($id, $save_data);

			if ($save_ekskul_member_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ekskul_member_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_member_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_member_smp');
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
	* delete Ekskul Member Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ekskul_member_smp_delete');

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
            set_message(cclang('has_been_deleted', 'ekskul_member_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'ekskul_member_smp'), 'error');
        }

		redirect_back();
	}

	public function activate_member($id = null)
	{
		$arr_id = $this->input->get('id');
		$activate = false;

		if (!empty($id)) {
			$activate = $this->mymodel->update("ekskul_member_smp", array("status_member" => 1), "id_member", $id);
		} elseif (is_array($arr_id) && count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$activate = $this->mymodel->update("ekskul_member_smp", array("status_member" => 1), "id_member", $id);
			}
		}

		if ($activate) {
            set_message("Aktivasi anggota ekstrakurikuler berhasil", 'success');
        } else {
            set_message("Aktivasi anggota ekstrakurikuler gagal", 'error');
        }

		redirect_back();
	}

	public function deactivate_member($id = null)
	{
		$arr_id = $this->input->get('id');
		$deactivate = false;

		if (!empty($id)) {
			$deactivate = $this->mymodel->update("ekskul_member_smp", array("status_member" => 0), "id_member", $id);
		} elseif (is_array($arr_id) && count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$deactivate = $this->mymodel->update("ekskul_member_smp", array("status_member" => 0), "id_member", $id);
			}
		}

		if ($deactivate) {
            set_message("Deaktivasi anggota ekstrakurikuler berhasil", 'success');
        } else {
            set_message("Deaktivasi anggota ekstrakurikuler gagal", 'error');
        }

		redirect_back();
	}

		/**
	* View view Ekskul Member Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ekskul_member_smp_view');

		$this->data['ekskul_member_smp'] = $this->model_ekskul_member_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Anggota Ekskul SMP Detail');
		$this->render('backend/standart/administrator/ekskul_member_smp/ekskul_member_smp_view', $this->data);
	}
	
	/**
	* delete Ekskul Member Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ekskul_member_smp = $this->model_ekskul_member_smp->find($id);

		if (!empty($ekskul_member_smp->file_pembayaran)) {
			$path = FCPATH . '/uploads/ekskul_member_smp/' . $ekskul_member_smp->file_pembayaran;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_ekskul_member_smp->remove($id);
	}
	
	/**
	* Upload Image Ekskul Member Smp	* 
	* @return JSON
	*/
	public function upload_file_pembayaran_file()
	{
		if (!$this->is_allowed('ekskul_member_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'ekskul_member_smp',
		]);
	}

	/**
	* Delete Image Ekskul Member Smp	* 
	* @return JSON
	*/
	public function delete_file_pembayaran_file($uuid)
	{
		if (!$this->is_allowed('ekskul_member_smp_delete', false)) {
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
            'table_name'        => 'ekskul_member_smp',
            'primary_key'       => 'id_member',
            'upload_path'       => 'uploads/ekskul_member_smp/'
        ]);
	}

	/**
	* Get Image Ekskul Member Smp	* 
	* @return JSON
	*/
	public function get_file_pembayaran_file($id)
	{
		if (!$this->is_allowed('ekskul_member_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$ekskul_member_smp = $this->model_ekskul_member_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_pembayaran', 
            'table_name'        => 'ekskul_member_smp',
            'primary_key'       => 'id_member',
            'upload_path'       => 'uploads/ekskul_member_smp/',
            'delete_endpoint'   => 'administrator/ekskul_member_smp/delete_file_pembayaran_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ekskul_member_smp_export');

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
		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$whereClauses[] = $get['file_pembayaran'] === 'sudah_upload'
				? "(m.file_pembayaran IS NOT NULL AND m.file_pembayaran != '')"
				: "(m.file_pembayaran IS NULL OR m.file_pembayaran = '')";
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
			$student_status_filter = " AND EXISTS (SELECT 1 FROM ekskul_member_smp em WHERE em.id_siswa = s.id_siswa_smp_aktif AND em.status_member = '".$get['status_member']."')";
		}
		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$student_status_filter .= $get['file_pembayaran'] === 'sudah_upload'
				? " AND EXISTS (SELECT 1 FROM ekskul_member_smp em WHERE em.id_siswa = s.id_siswa_smp_aktif AND em.file_pembayaran IS NOT NULL AND em.file_pembayaran != '')"
				: " AND EXISTS (SELECT 1 FROM ekskul_member_smp em WHERE em.id_siswa = s.id_siswa_smp_aktif AND (em.file_pembayaran IS NULL OR em.file_pembayaran = ''))";
		}
		if (!empty($get['id_kelas'])) {
			$get_siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_smp_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				FROM siswa_smp_aktif s
				JOIN kelas_smp k ON s.id_kelas = k.id_kelas_smp
				WHERE s.id_kelas = '".$get['id_kelas']."'{$student_status_filter}
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");
		} elseif (!empty($get['id_tingkatan'])) {
			$get_siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_smp_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				FROM siswa_smp_aktif s
				JOIN kelas_smp k ON s.id_kelas = k.id_kelas_smp
				WHERE k.id_tingkatan = '".$get['id_tingkatan']."'{$student_status_filter}
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");
		} else {
			$get_siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_smp_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				FROM ekskul_member_smp m
				JOIN ekskul e ON m.id_ekskul = e.id_ekskul AND e.jenjang = 'smp'
				JOIN siswa_smp_aktif s ON m.id_siswa = s.id_siswa_smp_aktif
				JOIN kelas_smp k ON s.id_kelas = k.id_kelas_smp
				$where
				GROUP BY m.id_siswa
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");
		}
		
		// Ambil semua ekskul siswa sekaligus
		$idList = array_column($get_siswa, 'id_siswa');
		$listEkskul = [];
		if ($idList) {
			$ekskul_status_where = (isset($get['status_member']) && $get['status_member'] !== '')
				? " AND m.status_member = '".$get['status_member']."'"
				: "";
			$allEkskul = $this->mymodel->withquery("
				SELECT m.id_siswa, e.nama, m.status_member
				FROM ekskul_member_smp m
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
		header('Content-Disposition: attachment;filename="Data Pilihan Ekstrakurikuler Siswa SMP - '.date("Y-m-d").'.xls"'); 
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}


	public function export_list_anggota()
	{
		$this->is_allowed('ekskul_member_smp_export');

		$get = $this->input->get();
		$field = ['nomor', 'id_siswa', 'nis', 'nama_kelas', 'nama_ekskul', 'tahun_ajaran', 'semester', 'status_member'];

		$conditions = [];

		if (!empty($get['id_ekskul'])) {
			$ids = (array)$get['id_ekskul'];
			$ids = array_map('intval', $ids);
			$conditions[] = "m.id_ekskul IN (" . implode(',', $ids) . ")";
		} else {
			$conditions[] = "m.id_ekskul IN (SELECT id_ekskul FROM ekskul WHERE jenjang = 'smp')";
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

		if (isset($get['status_member']) && $get['status_member'] !== '') {
			$conditions[] = "m.status_member = " . $this->db->escape($get['status_member']);
		} else {
			$conditions[] = "(m.status_member IN ('1','0'))";
		}
		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$conditions[] = $get['file_pembayaran'] === 'sudah_upload'
				? "(m.file_pembayaran IS NOT NULL AND m.file_pembayaran != '')"
				: "(m.file_pembayaran IS NULL OR m.file_pembayaran = '')";
		}

		$where = "WHERE " . implode(' AND ', $conditions);

		$all_data = $this->mymodel->withquery("
			SELECT 
				e.id_ekskul, e.nama AS nama_ekskul, 
				m.status_member, m.semester, m.tahun_ajaran,
				s.id_siswa_smp_aktif, s.nis, s.nama_lengkap, 
				k.label AS nama_kelas
			FROM ekskul_member_smp m
			JOIN ekskul e ON m.id_ekskul = e.id_ekskul
			JOIN siswa_smp_aktif s ON m.id_siswa = s.id_siswa_smp_aktif
			JOIN kelas_smp k ON s.id_kelas = k.id_kelas_smp
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
		header('Content-Disposition: attachment;filename="Daftar Anggota Ekstrakurikuler Siswa SMP - ' . date("Y-m-d") . '.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	/**
	* Export to Excel - Lembar Tanda Tangan Kehadiran per ekskul (1 sheet per ekskul)
	* Kolom minggu dibiarkan kosong untuk tanda tangan siswa saat dicetak
	*/
	public function export_tanda_tangan()
	{
		$this->is_allowed('ekskul_member_smp_export');

		$get = $this->input->get();
		$bulan = !empty($get['bulan']) ? (int)$get['bulan'] : date('n');
		$tahun = !empty($get['tahun']) ? (int)$get['tahun'] : date('Y');
		$id_ekskul_list = isset($get['id_ekskul']) ? array_map('intval', (array)$get['id_ekskul']) : [];

		if (empty($id_ekskul_list)) {
			set_message('Silahkan pilih ekskul terlebih dahulu', 'error');
			redirect_back();
		}

		// Semester & tahun pelajaran dari bulan terpilih: Ganjil (Jul-Des) = TA Y/Y+1, Genap (Jan-Jun) = TA Y-1/Y
		$semester = ($bulan >= 7) ? '1' : '2';
		$tahun_ajaran = ($bulan >= 7) ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);

		// ponytail: minggu ISO (Senin-Minggu) yang menyentuh bulan, max 6
		$first = mktime(0, 0, 0, $bulan, 1, $tahun);
		$last = mktime(0, 0, 0, $bulan, date('t', $first), $tahun);
		$iso_weeks = [];
		for ($d = $first; $d <= $last; $d = strtotime('+1 day', $d)) {
			$iso_weeks[date('oW', $d)] = true;
		}
		$total_weeks = min(count($iso_weeks), 6);

		$nama_bulan = formatBulan(date('Y-m-d', $first));
		$judul = 'PESERTA EKSTRAKURIKULER SMP LABSCHOOL CIBUBUR SEMESTER ' . $semester . ' TAHUN PELAJARAN ' . $tahun_ajaran;
		$header_bulan = strtoupper($nama_bulan . ' ' . $tahun);

		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$sheetIndex = 0;
		$first_col_letter = 'E'; // kolom minggu pertama

		foreach ($id_ekskul_list as $id_ekskul) {
			$rows = $this->mymodel->withquery("
				SELECT e.nama AS nama_ekskul, s.nama_lengkap, k.label AS nama_kelas
				FROM ekskul_member_smp m
				JOIN ekskul e ON m.id_ekskul = e.id_ekskul
				JOIN siswa_smp_aktif s ON m.id_siswa = s.id_siswa_smp_aktif
				JOIN kelas_smp k ON s.id_kelas = k.id_kelas_smp
				WHERE m.id_ekskul = '" . $id_ekskul . "'
				AND m.semester = '" . $semester . "'
				AND m.tahun_ajaran = '" . urldecode($tahun_ajaran) . "'
				AND m.status_member IN ('1','0')
				ORDER BY k.label ASC, s.nama_lengkap ASC
			", "result");

			if (empty($rows)) {
				continue;
			}

			if ($sheetIndex > 0) {
				$objPHPExcel->createSheet();
			}
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$sheet = $objPHPExcel->getActiveSheet();
			$sheet->setTitle(strtoupper(str_replace('/', '_', substr($rows[0]->nama_ekskul, 0, 28))));

			$last_week_col = chr(ord($first_col_letter) + $total_weeks - 1);

			// Row 1: judul merge
			$sheet->setCellValue('A1', $judul);
			$sheet->mergeCells('A1:' . $last_week_col . '1');
			$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
			$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			// Row 2-3: header 2 tingkat
			$sheet->setCellValue('A2', 'NO');
			$sheet->setCellValue('B2', 'NAMA PESERTA');
			$sheet->setCellValue('C2', 'KELAS');
			$sheet->setCellValue('D2', 'EKSKUL');
			$sheet->mergeCells('A2:A3');
			$sheet->mergeCells('B2:B3');
			$sheet->mergeCells('C2:C3');
			$sheet->mergeCells('D2:D3');

			$sheet->setCellValue('E2', $header_bulan);
			$sheet->mergeCells('E2:' . $last_week_col . '2');
			for ($w = 1; $w <= $total_weeks; $w++) {
				$col = chr(ord($first_col_letter) + $w - 1);
				$sheet->setCellValue($col . '3', 'Minggu ' . $w);
			}
			$sheet->getStyle('A2:' . $last_week_col . '3')->getFont()->setBold(true);
			$sheet->getStyle('A2:' . $last_week_col . '3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			// Data rows - kolom minggu kosong untuk tanda tangan
			$rowNum = 4;
			foreach ($rows as $i => $r) {
				$sheet->setCellValue('A' . $rowNum, $i + 1);
				$sheet->setCellValue('B' . $rowNum, $r->nama_lengkap);
				$sheet->setCellValue('C' . $rowNum, $r->nama_kelas);
				$sheet->setCellValue('D' . $rowNum, $r->nama_ekskul);
				$sheet->getRowDimension($rowNum)->setRowHeight(30);
				$rowNum++;
			}

			// Lebar kolom: cukup untuk tanda tangan saat dicetak
			$sheet->getColumnDimension('A')->setWidth(5);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(10);
			$sheet->getColumnDimension('D')->setWidth(25);
			for ($w = 1; $w <= $total_weeks; $w++) {
				$col = chr(ord($first_col_letter) + $w - 1);
				$sheet->getColumnDimension($col)->setWidth(9);
			}

			// Border semua sel terpakai
			$sheet->getStyle('A1:' . $last_week_col . ($rowNum - 1))->applyFromArray([
				'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
			]);
			$sheet->getStyle('A2:' . $last_week_col . ($rowNum - 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			$sheetIndex++;
			}

			if ($sheetIndex == 0) {
				set_message('Tidak ada anggota pada ekskul/periode yang dipilih', 'error');
				redirect_back();
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Tanda Tangan Kehadiran Ekskul SMP ' . $nama_bulan . ' ' . $tahun . ' - ' . date("Y-m-d") . '.xls"');
			header('Cache-Control: max-age=0');
			PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
		}

	public function export_rekap_pembayaran()
	{
		$this->is_allowed('ekskul_member_smp_export');

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
			$conditions[] = "m.id_ekskul IN (SELECT id_ekskul FROM ekskul WHERE jenjang = 'smp')";
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

		if (isset($get['status_member']) && $get['status_member'] !== '') {
			$conditions[] = "m.status_member = " . $this->db->escape($get['status_member']);
		} else {
			$conditions[] = "(m.status_member IN ('1','0'))";
		}
		if (isset($get['file_pembayaran']) && $get['file_pembayaran'] !== '') {
			$conditions[] = $get['file_pembayaran'] === 'sudah_upload'
				? "(m.file_pembayaran IS NOT NULL AND m.file_pembayaran != '')"
				: "(m.file_pembayaran IS NULL OR m.file_pembayaran = '')";
		}
		$where = "WHERE " . implode(' AND ', $conditions);

		$all_data = $this->mymodel->withquery("
			SELECT 
				e.id_ekskul, e.nama AS nama_ekskul, e.nominal_biaya, m.semester, m.tahun_ajaran,
				(SELECT COUNT(id_member) 
				FROM ekskul_member_smp 
				WHERE id_ekskul = e.id_ekskul 
				AND tahun_ajaran = m.tahun_ajaran 
				AND semester = m.semester 
				AND file_pembayaran != '') AS total_member_bayar,
				(SELECT COUNT(id_member) 
				FROM ekskul_member_smp 
				WHERE id_ekskul = e.id_ekskul 
				AND tahun_ajaran = m.tahun_ajaran 
				AND semester = m.semester 
				AND file_pembayaran = '') AS total_member_belum_bayar
			FROM ekskul e
			JOIN ekskul_member_smp m ON e.id_ekskul = m.id_ekskul 
			JOIN siswa_smp_aktif s ON m.id_siswa = s.id_siswa_smp_aktif
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
		header('Content-Disposition: attachment;filename="Rekap Pembayaran Ekstrakurikuler SMP - ' . date("Y-m-d") . '.xls"');
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
		$this->is_allowed('ekskul_member_smp_export');

		$this->model_ekskul_member_smp->pdf('ekskul_member_smp', 'ekskul_member_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ekskul_member_smp_export');

		$table = $title = 'ekskul_member_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ekskul_member_smp->find($id);
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
	 * Called via AJAX when admin previews HEIC file.
	 * Returns JSON: { success: bool, url: string, error: string }
	 */
	public function convert_heic()
	{
		$file = $this->input->get('file');
		if (empty($file)) {
			echo json_encode(array('success' => false, 'error' => 'Parameter file kosong'));
			return;
		}

		$file = basename($file);
		$original_path = FCPATH . 'uploads/ekskul_member_smp/' . $file;

		if (!is_file($original_path)) {
			echo json_encode(array('success' => false, 'error' => 'File tidak ditemukan: ' . $file));
			return;
		}

		$jpeg_path = preg_replace('/\.(heic|heif)$/i', '.jpg', $original_path);

		// Return cached if exists
		if (is_file($jpeg_path) && filemtime($jpeg_path) >= filemtime($original_path)) {
			echo json_encode(array('success' => true, 'url' => base_url('uploads/ekskul_member_smp/' . basename($jpeg_path))));
			return;
		}

		$debug = array();

		// Method 1: heif-convert
		$heif_bin = trim(shell_exec('which heif-convert 2>/dev/null'));
		$debug['heif_convert_path'] = $heif_bin ?: 'NOT FOUND';
		if ($heif_bin) {
			$cmd = sprintf('%s %s %s 2>&1', escapeshellarg($heif_bin), escapeshellarg($original_path), escapeshellarg($jpeg_path));
			exec($cmd, $output, $status);
			$debug['heif_convert_status'] = $status;
			$debug['heif_convert_output'] = implode(' ', $output);
			if ($status === 0 && is_file($jpeg_path)) {
				echo json_encode(array('success' => true, 'url' => base_url('uploads/ekskul_member_smp/' . basename($jpeg_path)), 'method' => 'heif-convert'));
				return;
			}
		}

		// Method 2: python3 pillow-heif
		$py_bin = trim(shell_exec('which python3 2>/dev/null'));
		$debug['python3_path'] = $py_bin ?: 'NOT FOUND';
		if ($py_bin) {
			$py_cmd = sprintf(
				'from PIL import Image; from pillow_heif import register_heif_opener; register_heif_opener(); Image.open(%s).save(%s, "JPEG", quality=85)',
				escapeshellarg($original_path),
				escapeshellarg($jpeg_path)
			);
			$cmd = sprintf('%s -c %s 2>&1', escapeshellarg($py_bin), escapeshellarg($py_cmd));
			exec($cmd, $output, $status);
			$debug['python_status'] = $status;
			$debug['python_output'] = implode(' ', $output);
			if ($status === 0 && is_file($jpeg_path)) {
				echo json_encode(array('success' => true, 'url' => base_url('uploads/ekskul_member_smp/' . basename($jpeg_path)), 'method' => 'python'));
				return;
			}
		}

		// All failed — return debug info
		echo json_encode(array('success' => false, 'error' => 'Konversi gagal', 'debug' => $debug));
	}

}


/* End of file ekskul_member_smp.php */
/* Location: ./application/controllers/administrator/Ekskul Member Smp.php */