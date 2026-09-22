<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Data Kepala Sekolah Controller
*| --------------------------------------------------------------------------
*| Data Kepala Sekolah site
*|
*/
class Data_kepala_sekolah extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_data_kepala_sekolah');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Data Kepala Sekolahs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('data_kepala_sekolah_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['data_kepala_sekolahs'] = $this->model_data_kepala_sekolah->get($filter, $field, $this->limit_page, $offset);
		$this->data['data_kepala_sekolah_counts'] = $this->model_data_kepala_sekolah->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/data_kepala_sekolah/index/',
			'total_rows'   => $this->model_data_kepala_sekolah->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Data Kepala Sekolah List');
		$this->render('backend/standart/administrator/data_kepala_sekolah/data_kepala_sekolah_list', $this->data);
	}
	
	
		/**
	* Update view Data Kepala Sekolahs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('data_kepala_sekolah_update');

		$this->data['data_kepala_sekolah'] = $this->model_data_kepala_sekolah->find($id);

		$this->template->title('Data Kepala Sekolah Update');
		$this->render('backend/standart/administrator/data_kepala_sekolah/data_kepala_sekolah_update', $this->data);
	}

	/**
	* Update Data Kepala Sekolahs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('data_kepala_sekolah_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_kepsek', 'Nama Kepsek', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('data_kepala_sekolah_file_ttd_name', 'File Ttd', 'trim|required');
		$this->form_validation->set_rules('nrks', 'Nrks', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		
		if ($this->form_validation->run()) {
			$data_kepala_sekolah_file_ttd_uuid = $this->input->post('data_kepala_sekolah_file_ttd_uuid');
			$data_kepala_sekolah_file_ttd_name = $this->input->post('data_kepala_sekolah_file_ttd_name');
		
			$save_data = [
				'nama_kepsek' => $this->input->post('nama_kepsek'),
				'nrks' => $this->input->post('nrks'),
				'jenjang' => $this->input->post('jenjang'),
			];

			if (!is_dir(FCPATH . '/uploads/data_kepala_sekolah/')) {
				mkdir(FCPATH . '/uploads/data_kepala_sekolah/');
			}

			if (!empty($data_kepala_sekolah_file_ttd_uuid)) {
				$data_kepala_sekolah_file_ttd_name_copy = date('YmdHis') . '-' . $data_kepala_sekolah_file_ttd_name;

				rename(FCPATH . 'uploads/tmp/' . $data_kepala_sekolah_file_ttd_uuid . '/' . $data_kepala_sekolah_file_ttd_name, 
						FCPATH . 'uploads/data_kepala_sekolah/' . $data_kepala_sekolah_file_ttd_name_copy);

				if (!is_file(FCPATH . '/uploads/data_kepala_sekolah/' . $data_kepala_sekolah_file_ttd_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_ttd'] = $data_kepala_sekolah_file_ttd_name_copy;
			}
		
			
			$save_data_kepala_sekolah = $this->model_data_kepala_sekolah->change($id, $save_data);

			if ($save_data_kepala_sekolah) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/data_kepala_sekolah', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/data_kepala_sekolah');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/data_kepala_sekolah');
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
	* delete Data Kepala Sekolahs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('data_kepala_sekolah_delete');

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
            set_message(cclang('has_been_deleted', 'data_kepala_sekolah'), 'success');
        } else {
            set_message(cclang('error_delete', 'data_kepala_sekolah'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Data Kepala Sekolahs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('data_kepala_sekolah_view');

		$this->data['data_kepala_sekolah'] = $this->model_data_kepala_sekolah->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Data Kepala Sekolah Detail');
		$this->render('backend/standart/administrator/data_kepala_sekolah/data_kepala_sekolah_view', $this->data);
	}
	
	/**
	* delete Data Kepala Sekolahs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$data_kepala_sekolah = $this->model_data_kepala_sekolah->find($id);

		if (!empty($data_kepala_sekolah->file_ttd)) {
			$path = FCPATH . '/uploads/data_kepala_sekolah/' . $data_kepala_sekolah->file_ttd;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_data_kepala_sekolah->remove($id);
	}
	
	/**
	* Upload Image Data Kepala Sekolah	* 
	* @return JSON
	*/
	public function upload_file_ttd_file()
	{
		if (!$this->is_allowed('data_kepala_sekolah_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'data_kepala_sekolah',
		]);
	}

	/**
	* Delete Image Data Kepala Sekolah	* 
	* @return JSON
	*/
	public function delete_file_ttd_file($uuid)
	{
		if (!$this->is_allowed('data_kepala_sekolah_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_ttd', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'data_kepala_sekolah',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/data_kepala_sekolah/'
        ]);
	}

	/**
	* Get Image Data Kepala Sekolah	* 
	* @return JSON
	*/
	public function get_file_ttd_file($id)
	{
		if (!$this->is_allowed('data_kepala_sekolah_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$data_kepala_sekolah = $this->model_data_kepala_sekolah->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_ttd', 
            'table_name'        => 'data_kepala_sekolah',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/data_kepala_sekolah/',
            'delete_endpoint'   => 'administrator/data_kepala_sekolah/delete_file_ttd_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('data_kepala_sekolah_export');

		$this->model_data_kepala_sekolah->export('data_kepala_sekolah', 'data_kepala_sekolah');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('data_kepala_sekolah_export');

		$this->model_data_kepala_sekolah->pdf('data_kepala_sekolah', 'data_kepala_sekolah');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('data_kepala_sekolah_export');

		$table = $title = 'data_kepala_sekolah';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_data_kepala_sekolah->find($id);
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


/* End of file data_kepala_sekolah.php */
/* Location: ./application/controllers/administrator/Data Kepala Sekolah.php */