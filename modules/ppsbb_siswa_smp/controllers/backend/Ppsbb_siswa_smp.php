<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ppsbb Siswa Smp Controller
*| --------------------------------------------------------------------------
*| Ppsbb Siswa Smp site
*|
*/
class Ppsbb_siswa_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ppsbb_siswa_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ppsbb Siswa Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ppsbb_siswa_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ppsbb_siswa_smps'] = $this->model_ppsbb_siswa_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['ppsbb_siswa_smp_counts'] = $this->model_ppsbb_siswa_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ppsbb_siswa_smp/index/',
			'total_rows'   => $this->model_ppsbb_siswa_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ppsbb Siswa SMP List');
		$this->render('backend/standart/administrator/ppsbb_siswa_smp/ppsbb_siswa_smp_list', $this->data);
	}
	
	/**
	* Add new ppsbb_siswa_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('ppsbb_siswa_smp_add');

		$this->template->title('Ppsbb Siswa SMP New');
		$this->render('backend/standart/administrator/ppsbb_siswa_smp/ppsbb_siswa_smp_add', $this->data);
	}

	/**
	* Add New Ppsbb Siswa Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ppsbb_siswa_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_smp', 'Id Siswa Smp', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_prestasi', 'Jenis Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('keterangan_prestasi', 'Keterangan Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan_prestasi_lainnya', 'Keterangan Prestasi (Lainnya)', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_prestasi', 'Tahun Prestasi', 'trim|required|max_length[4]');
		$this->form_validation->set_rules('ppsbb_siswa_smp_sertifikat_name', 'Sertifikat', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenis_jenjang', 'Jenis Jenjang', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_lomba', 'Jenis Lomba', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
			$ppsbb_siswa_smp_sertifikat_uuid = $this->input->post('ppsbb_siswa_smp_sertifikat_uuid');
			$ppsbb_siswa_smp_sertifikat_name = $this->input->post('ppsbb_siswa_smp_sertifikat_name');
		
			$save_data = [
				'id_siswa_smp' => $this->input->post('id_siswa_smp'),
				'jenis_prestasi' => $this->input->post('jenis_prestasi'),
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'keterangan_prestasi' => $this->input->post('keterangan_prestasi'),
				'keterangan_prestasi_lainnya' => $this->input->post('keterangan_prestasi_lainnya'),
				'tahun_prestasi' => $this->input->post('tahun_prestasi'),
				'jenis_jenjang' => $this->input->post('jenis_jenjang'),
				'jenis_lomba' => $this->input->post('jenis_lomba'),
			];

			if (!is_dir(FCPATH . '/uploads/ppsbb_siswa_smp/')) {
				mkdir(FCPATH . '/uploads/ppsbb_siswa_smp/');
			}

			if (!empty($ppsbb_siswa_smp_sertifikat_name)) {
				$ppsbb_siswa_smp_sertifikat_name_copy = date('YmdHis') . '-' . $ppsbb_siswa_smp_sertifikat_name;

				rename(FCPATH . 'uploads/tmp/' . $ppsbb_siswa_smp_sertifikat_uuid . '/' . $ppsbb_siswa_smp_sertifikat_name, 
						FCPATH . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp_sertifikat_name_copy);

				if (!is_file(FCPATH . '/uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp_sertifikat_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['sertifikat'] = $ppsbb_siswa_smp_sertifikat_name_copy;
			}
		
			
			$save_ppsbb_siswa_smp = $this->model_ppsbb_siswa_smp->store($save_data);
            

			if ($save_ppsbb_siswa_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ppsbb_siswa_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ppsbb_siswa_smp/edit/' . $save_ppsbb_siswa_smp, 'Edit Ppsbb Siswa Smp'),
						anchor('administrator/ppsbb_siswa_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ppsbb_siswa_smp/edit/' . $save_ppsbb_siswa_smp, 'Edit Ppsbb Siswa Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_smp');
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
	* Update view Ppsbb Siswa Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ppsbb_siswa_smp_update');

		$this->data['ppsbb_siswa_smp'] = $this->model_ppsbb_siswa_smp->find($id);

		$this->template->title('Ppsbb Siswa SMP Update');
		$this->render('backend/standart/administrator/ppsbb_siswa_smp/ppsbb_siswa_smp_update', $this->data);
	}

	/**
	* Update Ppsbb Siswa Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ppsbb_siswa_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_smp', 'Id Siswa Smp', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_prestasi', 'Jenis Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('keterangan_prestasi', 'Keterangan Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan_prestasi_lainnya', 'Keterangan Prestasi (Lainnya)', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_prestasi', 'Tahun Prestasi', 'trim|required|max_length[4]');
		$this->form_validation->set_rules('ppsbb_siswa_smp_sertifikat_name', 'Sertifikat', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenis_jenjang', 'Jenis Jenjang', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_lomba', 'Jenis Lomba', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
			$ppsbb_siswa_smp_sertifikat_uuid = $this->input->post('ppsbb_siswa_smp_sertifikat_uuid');
			$ppsbb_siswa_smp_sertifikat_name = $this->input->post('ppsbb_siswa_smp_sertifikat_name');
		
			$save_data = [
				'id_siswa_smp' => $this->input->post('id_siswa_smp'),
				'jenis_prestasi' => $this->input->post('jenis_prestasi'),
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'keterangan_prestasi' => $this->input->post('keterangan_prestasi'),
				'keterangan_prestasi_lainnya' => $this->input->post('keterangan_prestasi_lainnya'),
				'tahun_prestasi' => $this->input->post('tahun_prestasi'),
				'jenis_jenjang' => $this->input->post('jenis_jenjang'),
				'jenis_lomba' => $this->input->post('jenis_lomba'),
			];

			if (!is_dir(FCPATH . '/uploads/ppsbb_siswa_smp/')) {
				mkdir(FCPATH . '/uploads/ppsbb_siswa_smp/');
			}

			if (!empty($ppsbb_siswa_smp_sertifikat_uuid)) {
				$ppsbb_siswa_smp_sertifikat_name_copy = date('YmdHis') . '-' . $ppsbb_siswa_smp_sertifikat_name;

				rename(FCPATH . 'uploads/tmp/' . $ppsbb_siswa_smp_sertifikat_uuid . '/' . $ppsbb_siswa_smp_sertifikat_name, 
						FCPATH . 'uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp_sertifikat_name_copy);

				if (!is_file(FCPATH . '/uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp_sertifikat_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['sertifikat'] = $ppsbb_siswa_smp_sertifikat_name_copy;
			}
		
			
			$save_ppsbb_siswa_smp = $this->model_ppsbb_siswa_smp->change($id, $save_data);

			if ($save_ppsbb_siswa_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ppsbb_siswa_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_smp');
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
	* delete Ppsbb Siswa Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ppsbb_siswa_smp_delete');

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
            set_message(cclang('has_been_deleted', 'ppsbb_siswa_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'ppsbb_siswa_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ppsbb Siswa Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ppsbb_siswa_smp_view');

		$this->data['ppsbb_siswa_smp'] = $this->model_ppsbb_siswa_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ppsbb Siswa SMP Detail');
		$this->render('backend/standart/administrator/ppsbb_siswa_smp/ppsbb_siswa_smp_view', $this->data);
	}
	
	/**
	* delete Ppsbb Siswa Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ppsbb_siswa_smp = $this->model_ppsbb_siswa_smp->find($id);

		if (!empty($ppsbb_siswa_smp->sertifikat)) {
			$path = FCPATH . '/uploads/ppsbb_siswa_smp/' . $ppsbb_siswa_smp->sertifikat;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_ppsbb_siswa_smp->remove($id);
	}
	
	/**
	* Upload Image Ppsbb Siswa Smp	* 
	* @return JSON
	*/
	public function upload_sertifikat_file()
	{
		if (!$this->is_allowed('ppsbb_siswa_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'ppsbb_siswa_smp',
		]);
	}

	/**
	* Delete Image Ppsbb Siswa Smp	* 
	* @return JSON
	*/
	public function delete_sertifikat_file($uuid)
	{
		if (!$this->is_allowed('ppsbb_siswa_smp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'sertifikat', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'ppsbb_siswa_smp',
            'primary_key'       => 'id_ppsbb_siswa_smp',
            'upload_path'       => 'uploads/ppsbb_siswa_smp/'
        ]);
	}

	/**
	* Get Image Ppsbb Siswa Smp	* 
	* @return JSON
	*/
	public function get_sertifikat_file($id)
	{
		if (!$this->is_allowed('ppsbb_siswa_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$ppsbb_siswa_smp = $this->model_ppsbb_siswa_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'sertifikat', 
            'table_name'        => 'ppsbb_siswa_smp',
            'primary_key'       => 'id_ppsbb_siswa_smp',
            'upload_path'       => 'uploads/ppsbb_siswa_smp/',
            'delete_endpoint'   => 'administrator/ppsbb_siswa_smp/delete_sertifikat_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ppsbb_siswa_smp_export');

		$this->model_ppsbb_siswa_smp->export('ppsbb_siswa_smp', 'ppsbb_siswa_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ppsbb_siswa_smp_export');

		$this->model_ppsbb_siswa_smp->pdf('ppsbb_siswa_smp', 'ppsbb_siswa_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ppsbb_siswa_smp_export');

		$table = $title = 'ppsbb_siswa_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ppsbb_siswa_smp->find($id);
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


/* End of file ppsbb_siswa_smp.php */
/* Location: ./application/controllers/administrator/Ppsbb Siswa Smp.php */