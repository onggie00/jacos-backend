<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ppsbb Siswa Sma Controller
*| --------------------------------------------------------------------------
*| Ppsbb Siswa Sma site
*|
*/
class Ppsbb_siswa_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ppsbb_siswa_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ppsbb Siswa Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ppsbb_siswa_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ppsbb_siswa_smas'] = $this->model_ppsbb_siswa_sma->get($filter, $field, $this->limit_page, $offset);
		$this->data['ppsbb_siswa_sma_counts'] = $this->model_ppsbb_siswa_sma->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ppsbb_siswa_sma/index/',
			'total_rows'   => $this->model_ppsbb_siswa_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('PPSBB Siswa SMA List');
		$this->render('backend/standart/administrator/ppsbb_siswa_sma/ppsbb_siswa_sma_list', $this->data);
	}
	
	/**
	* Add new ppsbb_siswa_smas
	*
	*/
	public function add()
	{
		$this->is_allowed('ppsbb_siswa_sma_add');

		$this->template->title('PPSBB Siswa SMA New');
		$this->render('backend/standart/administrator/ppsbb_siswa_sma/ppsbb_siswa_sma_add', $this->data);
	}

	/**
	* Add New Ppsbb Siswa Smas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ppsbb_siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_sma', 'Id Siswa Sma', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_prestasi', 'Jenis Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('keterangan_prestasi', 'Keterangan Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan_prestasi_lainnya', 'Keterangan Prestasi Lainnya', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_prestasi', 'Tahun Prestasi', 'trim|required|max_length[4]');
		$this->form_validation->set_rules('ppsbb_siswa_sma_sertifikat_name', 'Sertifikat', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenis_jenjang', 'Jenis Jenjang', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_lomba', 'Jenis Lomba', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
			$ppsbb_siswa_sma_sertifikat_uuid = $this->input->post('ppsbb_siswa_sma_sertifikat_uuid');
			$ppsbb_siswa_sma_sertifikat_name = $this->input->post('ppsbb_siswa_sma_sertifikat_name');
		
			$save_data = [
				'id_siswa_sma' => $this->input->post('id_siswa_sma'),
				'jenis_prestasi' => $this->input->post('jenis_prestasi'),
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'keterangan_prestasi' => $this->input->post('keterangan_prestasi'),
				'keterangan_prestasi_lainnya' => $this->input->post('keterangan_prestasi_lainnya'),
				'tahun_prestasi' => $this->input->post('tahun_prestasi'),
				'jenis_jenjang' => $this->input->post('jenis_jenjang'),
				'jenis_lomba' => $this->input->post('jenis_lomba'),
			];

			if (!is_dir(FCPATH . '/uploads/ppsbb_siswa_sma/')) {
				mkdir(FCPATH . '/uploads/ppsbb_siswa_sma/');
			}

			if (!empty($ppsbb_siswa_sma_sertifikat_name)) {
				$ppsbb_siswa_sma_sertifikat_name_copy = date('YmdHis') . '-' . $ppsbb_siswa_sma_sertifikat_name;

				rename(FCPATH . 'uploads/tmp/' . $ppsbb_siswa_sma_sertifikat_uuid . '/' . $ppsbb_siswa_sma_sertifikat_name, 
						FCPATH . 'uploads/ppsbb_siswa_sma/' . $ppsbb_siswa_sma_sertifikat_name_copy);

				if (!is_file(FCPATH . '/uploads/ppsbb_siswa_sma/' . $ppsbb_siswa_sma_sertifikat_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['sertifikat'] = $ppsbb_siswa_sma_sertifikat_name_copy;
			}
		
			
			$save_ppsbb_siswa_sma = $this->model_ppsbb_siswa_sma->store($save_data);
            

			if ($save_ppsbb_siswa_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ppsbb_siswa_sma;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ppsbb_siswa_sma/edit/' . $save_ppsbb_siswa_sma, 'Edit Ppsbb Siswa Sma'),
						anchor('administrator/ppsbb_siswa_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ppsbb_siswa_sma/edit/' . $save_ppsbb_siswa_sma, 'Edit Ppsbb Siswa Sma')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_sma');
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
	* Update view Ppsbb Siswa Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ppsbb_siswa_sma_update');

		$this->data['ppsbb_siswa_sma'] = $this->model_ppsbb_siswa_sma->find($id);

		$this->template->title('PPSBB Siswa SMA Update');
		$this->render('backend/standart/administrator/ppsbb_siswa_sma/ppsbb_siswa_sma_update', $this->data);
	}

	/**
	* Update Ppsbb Siswa Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ppsbb_siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_sma', 'Id Siswa Sma', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_prestasi', 'Jenis Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('keterangan_prestasi', 'Keterangan Prestasi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('keterangan_prestasi_lainnya', 'Keterangan Prestasi Lainnya', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_prestasi', 'Tahun Prestasi', 'trim|required|max_length[4]');
		$this->form_validation->set_rules('ppsbb_siswa_sma_sertifikat_name', 'Sertifikat', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenis_jenjang', 'Jenis Jenjang', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenis_lomba', 'Jenis Lomba', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
			$ppsbb_siswa_sma_sertifikat_uuid = $this->input->post('ppsbb_siswa_sma_sertifikat_uuid');
			$ppsbb_siswa_sma_sertifikat_name = $this->input->post('ppsbb_siswa_sma_sertifikat_name');
		
			$save_data = [
				'id_siswa_sma' => $this->input->post('id_siswa_sma'),
				'jenis_prestasi' => $this->input->post('jenis_prestasi'),
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'keterangan_prestasi' => $this->input->post('keterangan_prestasi'),
				'keterangan_prestasi_lainnya' => $this->input->post('keterangan_prestasi_lainnya'),
				'tahun_prestasi' => $this->input->post('tahun_prestasi'),
				'jenis_jenjang' => $this->input->post('jenis_jenjang'),
				'jenis_lomba' => $this->input->post('jenis_lomba'),
			];

			if (!is_dir(FCPATH . '/uploads/ppsbb_siswa_sma/')) {
				mkdir(FCPATH . '/uploads/ppsbb_siswa_sma/');
			}

			if (!empty($ppsbb_siswa_sma_sertifikat_uuid)) {
				$ppsbb_siswa_sma_sertifikat_name_copy = date('YmdHis') . '-' . $ppsbb_siswa_sma_sertifikat_name;

				rename(FCPATH . 'uploads/tmp/' . $ppsbb_siswa_sma_sertifikat_uuid . '/' . $ppsbb_siswa_sma_sertifikat_name, 
						FCPATH . 'uploads/ppsbb_siswa_sma/' . $ppsbb_siswa_sma_sertifikat_name_copy);

				if (!is_file(FCPATH . '/uploads/ppsbb_siswa_sma/' . $ppsbb_siswa_sma_sertifikat_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['sertifikat'] = $ppsbb_siswa_sma_sertifikat_name_copy;
			}
		
			
			$save_ppsbb_siswa_sma = $this->model_ppsbb_siswa_sma->change($id, $save_data);

			if ($save_ppsbb_siswa_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ppsbb_siswa_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ppsbb_siswa_sma');
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
	* delete Ppsbb Siswa Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ppsbb_siswa_sma_delete');

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
            set_message(cclang('has_been_deleted', 'ppsbb_siswa_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'ppsbb_siswa_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ppsbb Siswa Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ppsbb_siswa_sma_view');

		$this->data['ppsbb_siswa_sma'] = $this->model_ppsbb_siswa_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('PPSBB Siswa SMA Detail');
		$this->render('backend/standart/administrator/ppsbb_siswa_sma/ppsbb_siswa_sma_view', $this->data);
	}
	
	/**
	* delete Ppsbb Siswa Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ppsbb_siswa_sma = $this->model_ppsbb_siswa_sma->find($id);

		if (!empty($ppsbb_siswa_sma->sertifikat)) {
			$path = FCPATH . '/uploads/ppsbb_siswa_sma/' . $ppsbb_siswa_sma->sertifikat;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_ppsbb_siswa_sma->remove($id);
	}
	
	/**
	* Upload Image Ppsbb Siswa Sma	* 
	* @return JSON
	*/
	public function upload_sertifikat_file()
	{
		if (!$this->is_allowed('ppsbb_siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'ppsbb_siswa_sma',
		]);
	}

	/**
	* Delete Image Ppsbb Siswa Sma	* 
	* @return JSON
	*/
	public function delete_sertifikat_file($uuid)
	{
		if (!$this->is_allowed('ppsbb_siswa_sma_delete', false)) {
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
            'table_name'        => 'ppsbb_siswa_sma',
            'primary_key'       => 'id_ppsbb_siswa_sma',
            'upload_path'       => 'uploads/ppsbb_siswa_sma/'
        ]);
	}

	/**
	* Get Image Ppsbb Siswa Sma	* 
	* @return JSON
	*/
	public function get_sertifikat_file($id)
	{
		if (!$this->is_allowed('ppsbb_siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$ppsbb_siswa_sma = $this->model_ppsbb_siswa_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'sertifikat', 
            'table_name'        => 'ppsbb_siswa_sma',
            'primary_key'       => 'id_ppsbb_siswa_sma',
            'upload_path'       => 'uploads/ppsbb_siswa_sma/',
            'delete_endpoint'   => 'administrator/ppsbb_siswa_sma/delete_sertifikat_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ppsbb_siswa_sma_export');

		$this->model_ppsbb_siswa_sma->export('ppsbb_siswa_sma', 'ppsbb_siswa_sma');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ppsbb_siswa_sma_export');

		$this->model_ppsbb_siswa_sma->pdf('ppsbb_siswa_sma', 'ppsbb_siswa_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ppsbb_siswa_sma_export');

		$table = $title = 'ppsbb_siswa_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ppsbb_siswa_sma->find($id);
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


/* End of file ppsbb_siswa_sma.php */
/* Location: ./application/controllers/administrator/Ppsbb Siswa Sma.php */