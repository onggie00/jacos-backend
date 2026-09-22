<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Izin Siswa Sma Controller
*| --------------------------------------------------------------------------
*| Izin Siswa Sma site
*|
*/
class Izin_siswa_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_izin_siswa_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Izin Siswa Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('izin_siswa_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['izin_siswa_smas'] = $this->model_izin_siswa_sma->get($filter, $field, $this->limit_page, $offset);
		$this->data['izin_siswa_sma_counts'] = $this->model_izin_siswa_sma->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/izin_siswa_sma/index/',
			'total_rows'   => $this->model_izin_siswa_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Izin SMA List');
		$this->render('backend/standart/administrator/izin_siswa_sma/izin_siswa_sma_list', $this->data);
	}
	
	/**
	* Add new izin_siswa_smas
	*
	*/
	public function add()
	{
		$this->is_allowed('izin_siswa_sma_add');

		$this->template->title('Izin SMA New');
		$this->render('backend/standart/administrator/izin_siswa_sma/izin_siswa_sma_add', $this->data);
	}

	/**
	* Add New Izin Siswa Smas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('izin_siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('izin_siswa_sma_file_izin_name', 'File Izin', 'trim|required');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'trim|required');
		$this->form_validation->set_rules('status', 'Satus', 'trim|required');
		$this->form_validation->set_rules('id_approver', 'Approver', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
			$izin_siswa_sma_file_izin_uuid = $this->input->post('izin_siswa_sma_file_izin_uuid');
			$izin_siswa_sma_file_izin_name = $this->input->post('izin_siswa_sma_file_izin_name');
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'tanggal_mulai' => $this->input->post('tanggal_mulai'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
				'keterangan' => $this->input->post('keterangan'),
				'jenis_izin' => $this->input->post('jenis_izin'),
				'status' => $this->input->post('status'),
				'id_approver' => $this->input->post('id_approver'),
			];

			if (!is_dir(FCPATH . '/uploads/izin_siswa_sma/')) {
				mkdir(FCPATH . '/uploads/izin_siswa_sma/');
			}

			if (!empty($izin_siswa_sma_file_izin_name)) {
				$izin_siswa_sma_file_izin_name_copy = date('YmdHis') . '-' . $izin_siswa_sma_file_izin_name;

				rename(FCPATH . 'uploads/tmp/' . $izin_siswa_sma_file_izin_uuid . '/' . $izin_siswa_sma_file_izin_name, 
						FCPATH . 'uploads/izin_siswa_sma/' . $izin_siswa_sma_file_izin_name_copy);

				if (!is_file(FCPATH . '/uploads/izin_siswa_sma/' . $izin_siswa_sma_file_izin_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_izin'] = $izin_siswa_sma_file_izin_name_copy;
			}
		
			
			$save_izin_siswa_sma = $this->model_izin_siswa_sma->store($save_data);
            

			if ($save_izin_siswa_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_izin_siswa_sma;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/izin_siswa_sma/edit/' . $save_izin_siswa_sma, 'Edit Izin Siswa Sma'),
						anchor('administrator/izin_siswa_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/izin_siswa_sma/edit/' . $save_izin_siswa_sma, 'Edit Izin Siswa Sma')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/izin_siswa_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/izin_siswa_sma');
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
	* Update view Izin Siswa Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('izin_siswa_sma_update');

		$this->data['izin_siswa_sma'] = $this->model_izin_siswa_sma->find($id);

		$this->template->title('Izin SMA Update');
		$this->render('backend/standart/administrator/izin_siswa_sma/izin_siswa_sma_update', $this->data);
	}

	/**
	* Update Izin Siswa Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('izin_siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('izin_siswa_sma_file_izin_name', 'File Izin', 'trim|max_length[255]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'trim|required');
		$this->form_validation->set_rules('status', 'Satus', 'trim|required');
		$this->form_validation->set_rules('id_approver', 'Approver', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
			$izin_siswa_sma_file_izin_uuid = $this->input->post('izin_siswa_sma_file_izin_uuid');
			$izin_siswa_sma_file_izin_name = $this->input->post('izin_siswa_sma_file_izin_name');
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'tanggal_mulai' => $this->input->post('tanggal_mulai'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
				'keterangan' => $this->input->post('keterangan'),
				'jenis_izin' => $this->input->post('jenis_izin'),
				'status' => $this->input->post('status'),
				'id_approver' => $this->input->post('id_approver'),
			];

			if (!is_dir(FCPATH . '/uploads/izin_siswa_sma/')) {
				mkdir(FCPATH . '/uploads/izin_siswa_sma/');
			}

			if (!empty($izin_siswa_sma_file_izin_uuid)) {
				$izin_siswa_sma_file_izin_name_copy = date('YmdHis') . '-' . $izin_siswa_sma_file_izin_name;

				rename(FCPATH . 'uploads/tmp/' . $izin_siswa_sma_file_izin_uuid . '/' . $izin_siswa_sma_file_izin_name, 
						FCPATH . 'uploads/izin_siswa_sma/' . $izin_siswa_sma_file_izin_name_copy);

				if (!is_file(FCPATH . '/uploads/izin_siswa_sma/' . $izin_siswa_sma_file_izin_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_izin'] = $izin_siswa_sma_file_izin_name_copy;
			}
		
			
			$save_izin_siswa_sma = $this->model_izin_siswa_sma->change($id, $save_data);

			if ($save_izin_siswa_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/izin_siswa_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/izin_siswa_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/izin_siswa_sma');
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
	* delete Izin Siswa Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('izin_siswa_sma_delete');

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
            set_message(cclang('has_been_deleted', 'izin_siswa_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'izin_siswa_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Izin Siswa Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('izin_siswa_sma_view');

		$this->data['izin_siswa_sma'] = $this->model_izin_siswa_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Izin SMA Detail');
		$this->render('backend/standart/administrator/izin_siswa_sma/izin_siswa_sma_view', $this->data);
	}
	
	/**
	* delete Izin Siswa Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$izin_siswa_sma = $this->model_izin_siswa_sma->find($id);

		if (!empty($izin_siswa_sma->file_izin)) {
			$path = FCPATH . '/uploads/izin_siswa_sma/' . $izin_siswa_sma->file_izin;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_izin_siswa_sma->remove($id);
	}
	
	/**
	* Upload Image Izin Siswa Sma	* 
	* @return JSON
	*/
	public function upload_file_izin_file()
	{
		if (!$this->is_allowed('izin_siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'izin_siswa_sma',
		]);
	}

	/**
	* Delete Image Izin Siswa Sma	* 
	* @return JSON
	*/
	public function delete_file_izin_file($uuid)
	{
		if (!$this->is_allowed('izin_siswa_sma_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_izin', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'izin_siswa_sma',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/izin_siswa_sma/'
        ]);
	}

	/**
	* Get Image Izin Siswa Sma	* 
	* @return JSON
	*/
	public function get_file_izin_file($id)
	{
		if (!$this->is_allowed('izin_siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$izin_siswa_sma = $this->model_izin_siswa_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_izin', 
            'table_name'        => 'izin_siswa_sma',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/izin_siswa_sma/',
            'delete_endpoint'   => 'administrator/izin_siswa_sma/delete_file_izin_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('izin_siswa_sma_export');

		$this->model_izin_siswa_sma->export('izin_siswa_sma', 'izin_siswa_sma');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('izin_siswa_sma_export');

		$this->model_izin_siswa_sma->pdf('izin_siswa_sma', 'izin_siswa_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('izin_siswa_sma_export');

		$table = $title = 'izin_siswa_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_izin_siswa_sma->find($id);
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


/* End of file izin_siswa_sma.php */
/* Location: ./application/controllers/administrator/Izin Siswa Sma.php */