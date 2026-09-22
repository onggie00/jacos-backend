<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Nilai Raport Smp Controller
*| --------------------------------------------------------------------------
*| Nilai Raport Smp site
*|
*/
class Nilai_raport_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_nilai_raport_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Nilai Raport Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('nilai_raport_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['nilai_raport_smps'] = $this->model_nilai_raport_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['nilai_raport_smp_counts'] = $this->model_nilai_raport_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/nilai_raport_smp/index/',
			'total_rows'   => $this->model_nilai_raport_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Nilai Raport Smp List');
		$this->render('backend/standart/administrator/nilai_raport_smp/nilai_raport_smp_list', $this->data);
	}
	
	
		/**
	* Update view Nilai Raport Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('nilai_raport_smp_update');

		$this->data['nilai_raport_smp'] = $this->model_nilai_raport_smp->find($id);

		$this->template->title('Nilai Raport Smp Update');
		$this->render('backend/standart/administrator/nilai_raport_smp/nilai_raport_smp_update', $this->data);
	}

	/**
	* Update Nilai Raport Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('nilai_raport_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('b_inggris', 'B Inggris', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_inggris2', 'B Inggris2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_inggris3', 'B Inggris3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_inggris4', 'B Inggris4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_indonesia', 'B Indonesia', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_indonesia2', 'B Indonesia2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_indonesia3', 'B Indonesia3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('b_indonesia4', 'B Indonesia4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ipa', 'Ipa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ipa2', 'Ipa2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ipa3', 'Ipa3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ipa4', 'Ipa4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ips', 'Ips', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ips2', 'Ips2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ips3', 'Ips3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ips4', 'Ips4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('matematika', 'Matematika', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('matematika2', 'Matematika2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('matematika3', 'Matematika3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('matematika4', 'Matematika4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_raport_smp_file_raport_name', 'File Raport', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nilai_raport_smp_file_raport2_name', 'File Raport2', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nilai_raport_smp_file_raport3_name', 'File Raport3', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nilai_raport_smp_file_raport4_name', 'File Raport4', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$nilai_raport_smp_file_raport_uuid = $this->input->post('nilai_raport_smp_file_raport_uuid');
			$nilai_raport_smp_file_raport_name = $this->input->post('nilai_raport_smp_file_raport_name');
			$nilai_raport_smp_file_raport2_uuid = $this->input->post('nilai_raport_smp_file_raport2_uuid');
			$nilai_raport_smp_file_raport2_name = $this->input->post('nilai_raport_smp_file_raport2_name');
			$nilai_raport_smp_file_raport3_uuid = $this->input->post('nilai_raport_smp_file_raport3_uuid');
			$nilai_raport_smp_file_raport3_name = $this->input->post('nilai_raport_smp_file_raport3_name');
			$nilai_raport_smp_file_raport4_uuid = $this->input->post('nilai_raport_smp_file_raport4_uuid');
			$nilai_raport_smp_file_raport4_name = $this->input->post('nilai_raport_smp_file_raport4_name');
		
			$save_data = [
				'b_inggris' => $this->input->post('b_inggris'),
				'b_inggris2' => $this->input->post('b_inggris2'),
				'b_inggris3' => $this->input->post('b_inggris3'),
				'b_inggris4' => $this->input->post('b_inggris4'),
				'b_indonesia' => $this->input->post('b_indonesia'),
				'b_indonesia2' => $this->input->post('b_indonesia2'),
				'b_indonesia3' => $this->input->post('b_indonesia3'),
				'b_indonesia4' => $this->input->post('b_indonesia4'),
				'ipa' => $this->input->post('ipa'),
				'ipa2' => $this->input->post('ipa2'),
				'ipa3' => $this->input->post('ipa3'),
				'ipa4' => $this->input->post('ipa4'),
				'ips' => $this->input->post('ips'),
				'ips2' => $this->input->post('ips2'),
				'ips3' => $this->input->post('ips3'),
				'ips4' => $this->input->post('ips4'),
				'matematika' => $this->input->post('matematika'),
				'matematika2' => $this->input->post('matematika2'),
				'matematika3' => $this->input->post('matematika3'),
				'matematika4' => $this->input->post('matematika4'),
			];

			if (!is_dir(FCPATH . '/uploads/nilai_raport_smp/')) {
				mkdir(FCPATH . '/uploads/nilai_raport_smp/');
			}

			if (!empty($nilai_raport_smp_file_raport_uuid)) {
				$nilai_raport_smp_file_raport_name_copy = date('YmdHis') . '-' . $nilai_raport_smp_file_raport_name;

				rename(FCPATH . 'uploads/tmp/' . $nilai_raport_smp_file_raport_uuid . '/' . $nilai_raport_smp_file_raport_name, 
						FCPATH . 'uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport_name_copy);

				if (!is_file(FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_raport'] = $nilai_raport_smp_file_raport_name_copy;
			}
		
			if (!empty($nilai_raport_smp_file_raport2_uuid)) {
				$nilai_raport_smp_file_raport2_name_copy = date('YmdHis') . '-' . $nilai_raport_smp_file_raport2_name;

				rename(FCPATH . 'uploads/tmp/' . $nilai_raport_smp_file_raport2_uuid . '/' . $nilai_raport_smp_file_raport2_name, 
						FCPATH . 'uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport2_name_copy);

				if (!is_file(FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport2_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_raport2'] = $nilai_raport_smp_file_raport2_name_copy;
			}
		
			if (!empty($nilai_raport_smp_file_raport3_uuid)) {
				$nilai_raport_smp_file_raport3_name_copy = date('YmdHis') . '-' . $nilai_raport_smp_file_raport3_name;

				rename(FCPATH . 'uploads/tmp/' . $nilai_raport_smp_file_raport3_uuid . '/' . $nilai_raport_smp_file_raport3_name, 
						FCPATH . 'uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport3_name_copy);

				if (!is_file(FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport3_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_raport3'] = $nilai_raport_smp_file_raport3_name_copy;
			}
		
			if (!empty($nilai_raport_smp_file_raport4_uuid)) {
				$nilai_raport_smp_file_raport4_name_copy = date('YmdHis') . '-' . $nilai_raport_smp_file_raport4_name;

				rename(FCPATH . 'uploads/tmp/' . $nilai_raport_smp_file_raport4_uuid . '/' . $nilai_raport_smp_file_raport4_name, 
						FCPATH . 'uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport4_name_copy);

				if (!is_file(FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp_file_raport4_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_raport4'] = $nilai_raport_smp_file_raport4_name_copy;
			}
		
			
			$save_nilai_raport_smp = $this->model_nilai_raport_smp->change($id, $save_data);

			if ($save_nilai_raport_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/nilai_raport_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/nilai_raport_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/nilai_raport_smp');
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
	* delete Nilai Raport Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('nilai_raport_smp_delete');

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
            set_message(cclang('has_been_deleted', 'nilai_raport_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'nilai_raport_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Nilai Raport Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('nilai_raport_smp_view');

		$this->data['nilai_raport_smp'] = $this->model_nilai_raport_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Nilai Raport Smp Detail');
		$this->render('backend/standart/administrator/nilai_raport_smp/nilai_raport_smp_view', $this->data);
	}
	
	/**
	* delete Nilai Raport Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$nilai_raport_smp = $this->model_nilai_raport_smp->find($id);

		if (!empty($nilai_raport_smp->file_raport)) {
			$path = FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp->file_raport;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($nilai_raport_smp->file_raport2)) {
			$path = FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp->file_raport2;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($nilai_raport_smp->file_raport3)) {
			$path = FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp->file_raport3;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($nilai_raport_smp->file_raport4)) {
			$path = FCPATH . '/uploads/nilai_raport_smp/' . $nilai_raport_smp->file_raport4;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_nilai_raport_smp->remove($id);
	}
	
	/**
	* Upload Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function upload_file_raport_file()
	{
		if (!$this->is_allowed('nilai_raport_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'nilai_raport_smp',
		]);
	}

	/**
	* Delete Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function delete_file_raport_file($uuid)
	{
		if (!$this->is_allowed('nilai_raport_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_raport', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/'
        ]);
	}

	/**
	* Get Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function get_file_raport_file($id)
	{
		if (!$this->is_allowed('nilai_raport_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$nilai_raport_smp = $this->model_nilai_raport_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_raport', 
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/',
            'delete_endpoint'   => 'administrator/nilai_raport_smp/delete_file_raport_file'
        ]);
	}
	
	/**
	* Upload Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function upload_file_raport2_file()
	{
		if (!$this->is_allowed('nilai_raport_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'nilai_raport_smp',
		]);
	}

	/**
	* Delete Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function delete_file_raport2_file($uuid)
	{
		if (!$this->is_allowed('nilai_raport_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_raport2', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/'
        ]);
	}

	/**
	* Get Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function get_file_raport2_file($id)
	{
		if (!$this->is_allowed('nilai_raport_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$nilai_raport_smp = $this->model_nilai_raport_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_raport2', 
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/',
            'delete_endpoint'   => 'administrator/nilai_raport_smp/delete_file_raport2_file'
        ]);
	}
	
	/**
	* Upload Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function upload_file_raport3_file()
	{
		if (!$this->is_allowed('nilai_raport_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'nilai_raport_smp',
		]);
	}

	/**
	* Delete Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function delete_file_raport3_file($uuid)
	{
		if (!$this->is_allowed('nilai_raport_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_raport3', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/'
        ]);
	}

	/**
	* Get Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function get_file_raport3_file($id)
	{
		if (!$this->is_allowed('nilai_raport_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$nilai_raport_smp = $this->model_nilai_raport_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_raport3', 
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/',
            'delete_endpoint'   => 'administrator/nilai_raport_smp/delete_file_raport3_file'
        ]);
	}
	
	/**
	* Upload Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function upload_file_raport4_file()
	{
		if (!$this->is_allowed('nilai_raport_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'nilai_raport_smp',
		]);
	}

	/**
	* Delete Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function delete_file_raport4_file($uuid)
	{
		if (!$this->is_allowed('nilai_raport_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_raport4', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/'
        ]);
	}

	/**
	* Get Image Nilai Raport Smp	* 
	* @return JSON
	*/
	public function get_file_raport4_file($id)
	{
		if (!$this->is_allowed('nilai_raport_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$nilai_raport_smp = $this->model_nilai_raport_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_raport4', 
            'table_name'        => 'nilai_raport_smp',
            'primary_key'       => 'id_nilai_raport_smp',
            'upload_path'       => 'uploads/nilai_raport_smp/',
            'delete_endpoint'   => 'administrator/nilai_raport_smp/delete_file_raport4_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('nilai_raport_smp_export');

		$this->model_nilai_raport_smp->join_avaiable();
		$data = $this->db->get('nilai_raport_smp');
		
		$this->model_nilai_raport_smp->exportExcel($data);	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('nilai_raport_smp_export');

		$this->model_nilai_raport_smp->pdf('nilai_raport_smp', 'nilai_raport_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('nilai_raport_smp_export');

		$table = $title = 'nilai_raport_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_nilai_raport_smp->find($id);
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


/* End of file nilai_raport_smp.php */
/* Location: ./application/controllers/administrator/Nilai Raport Smp.php */