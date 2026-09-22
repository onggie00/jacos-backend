<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Alumni Sd Controller
*| --------------------------------------------------------------------------
*| Alumni Sd site
*|
*/
class Alumni_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_alumni_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Alumni Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('alumni_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['alumni_sds'] = $this->model_alumni_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['alumni_sd_counts'] = $this->model_alumni_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/alumni_sd/index/',
			'total_rows'   => $this->model_alumni_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Alumni Sd List');
		$this->render('backend/standart/administrator/alumni_sd/alumni_sd_list', $this->data);
	}
	
	
		/**
	* Update view Alumni Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('alumni_sd_update');

		$this->data['alumni_sd'] = $this->model_alumni_sd->find($id);

		$this->template->title('Alumni Sd Update');
		$this->render('backend/standart/administrator/alumni_sd/alumni_sd_update', $this->data);
	}

	/**
	* Update Alumni Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('alumni_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		
		if ($this->form_validation->run()) {
			$alumni_sd_file_ijazah_uuid = $this->input->post('alumni_sd_file_ijazah_uuid');
			$alumni_sd_file_ijazah_name = $this->input->post('alumni_sd_file_ijazah_name');
		
			$save_data = [
			];

			if (!is_dir(FCPATH . '/uploads/alumni_sd/')) {
				mkdir(FCPATH . '/uploads/alumni_sd/');
			}

			if (!empty($alumni_sd_file_ijazah_uuid)) {
				$alumni_sd_file_ijazah_name_copy = date('YmdHis') . '-' . $alumni_sd_file_ijazah_name;

				rename(FCPATH . 'uploads/tmp/' . $alumni_sd_file_ijazah_uuid . '/' . $alumni_sd_file_ijazah_name, 
						FCPATH . 'uploads/alumni_sd/' . $alumni_sd_file_ijazah_name_copy);

				if (!is_file(FCPATH . '/uploads/alumni_sd/' . $alumni_sd_file_ijazah_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_ijazah'] = $alumni_sd_file_ijazah_name_copy;
			}
		
			
			$save_alumni_sd = $this->model_alumni_sd->change($id, $save_data);

			if ($save_alumni_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/alumni_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/alumni_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/alumni_sd');
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
	* delete Alumni Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('alumni_sd_delete');

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
            set_message(cclang('has_been_deleted', 'alumni_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'alumni_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Alumni Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('alumni_sd_view');

		$this->data['alumni_sd'] = $this->model_alumni_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Alumni Sd Detail');
		$this->render('backend/standart/administrator/alumni_sd/alumni_sd_view', $this->data);
	}
	
	/**
	* delete Alumni Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$alumni_sd = $this->model_alumni_sd->find($id);

		if (!empty($alumni_sd->file_ijazah)) {
			$path = FCPATH . '/uploads/alumni_sd/' . $alumni_sd->file_ijazah;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_alumni_sd->remove($id);
	}
	
	/**
	* Upload Image Alumni Sd	* 
	* @return JSON
	*/
	public function upload_file_ijazah_file()
	{
		if (!$this->is_allowed('alumni_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'alumni_sd',
		]);
	}

	/**
	* Delete Image Alumni Sd	* 
	* @return JSON
	*/
	public function delete_file_ijazah_file($uuid)
	{
		if (!$this->is_allowed('alumni_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_ijazah', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'alumni_sd',
            'primary_key'       => 'id_alumni',
            'upload_path'       => 'uploads/alumni_sd/'
        ]);
	}

	/**
	* Get Image Alumni Sd	* 
	* @return JSON
	*/
	public function get_file_ijazah_file($id)
	{
		if (!$this->is_allowed('alumni_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$alumni_sd = $this->model_alumni_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_ijazah', 
            'table_name'        => 'alumni_sd',
            'primary_key'       => 'id_alumni',
            'upload_path'       => 'uploads/alumni_sd/',
            'delete_endpoint'   => 'administrator/alumni_sd/delete_file_ijazah_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('alumni_sd_export');

		$this->model_alumni_sd->export('alumni_sd', 'alumni_sd');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('alumni_sd_export');

		$this->model_alumni_sd->pdf('alumni_sd', 'alumni_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('alumni_sd_export');

		$table = $title = 'alumni_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_alumni_sd->find($id);
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


/* End of file alumni_sd.php */
/* Location: ./application/controllers/administrator/Alumni Sd.php */