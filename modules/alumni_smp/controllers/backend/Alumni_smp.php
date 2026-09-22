<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Alumni Smp Controller
*| --------------------------------------------------------------------------
*| Alumni Smp site
*|
*/
class Alumni_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_alumni_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Alumni Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('alumni_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['alumni_smps'] = $this->model_alumni_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['alumni_smp_counts'] = $this->model_alumni_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/alumni_smp/index/',
			'total_rows'   => $this->model_alumni_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Alumni Smp List');
		$this->render('backend/standart/administrator/alumni_smp/alumni_smp_list', $this->data);
	}
	
	
		/**
	* Update view Alumni Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('alumni_smp_update');

		$this->data['alumni_smp'] = $this->model_alumni_smp->find($id);

		$this->template->title('Alumni Smp Update');
		$this->render('backend/standart/administrator/alumni_smp/alumni_smp_update', $this->data);
	}

	/**
	* Update Alumni Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('alumni_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		
		if ($this->form_validation->run()) {
			$alumni_smp_file_ijazah_uuid = $this->input->post('alumni_smp_file_ijazah_uuid');
			$alumni_smp_file_ijazah_name = $this->input->post('alumni_smp_file_ijazah_name');
		
			$save_data = [
			];

			if (!is_dir(FCPATH . '/uploads/alumni_smp/')) {
				mkdir(FCPATH . '/uploads/alumni_smp/');
			}

			if (!empty($alumni_smp_file_ijazah_uuid)) {
				$alumni_smp_file_ijazah_name_copy = date('YmdHis') . '-' . $alumni_smp_file_ijazah_name;

				rename(FCPATH . 'uploads/tmp/' . $alumni_smp_file_ijazah_uuid . '/' . $alumni_smp_file_ijazah_name, 
						FCPATH . 'uploads/alumni_smp/' . $alumni_smp_file_ijazah_name_copy);

				if (!is_file(FCPATH . '/uploads/alumni_smp/' . $alumni_smp_file_ijazah_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_ijazah'] = $alumni_smp_file_ijazah_name_copy;
			}
		
			
			$save_alumni_smp = $this->model_alumni_smp->change($id, $save_data);

			if ($save_alumni_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/alumni_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/alumni_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/alumni_smp');
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
	* delete Alumni Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('alumni_smp_delete');

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
            set_message(cclang('has_been_deleted', 'alumni_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'alumni_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Alumni Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('alumni_smp_view');

		$this->data['alumni_smp'] = $this->model_alumni_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Alumni Smp Detail');
		$this->render('backend/standart/administrator/alumni_smp/alumni_smp_view', $this->data);
	}
	
	/**
	* delete Alumni Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$alumni_smp = $this->model_alumni_smp->find($id);

		if (!empty($alumni_smp->file_ijazah)) {
			$path = FCPATH . '/uploads/alumni_smp/' . $alumni_smp->file_ijazah;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_alumni_smp->remove($id);
	}
	
	/**
	* Upload Image Alumni Smp	* 
	* @return JSON
	*/
	public function upload_file_ijazah_file()
	{
		if (!$this->is_allowed('alumni_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'alumni_smp',
		]);
	}

	/**
	* Delete Image Alumni Smp	* 
	* @return JSON
	*/
	public function delete_file_ijazah_file($uuid)
	{
		if (!$this->is_allowed('alumni_smp_delete', false)) {
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
            'table_name'        => 'alumni_smp',
            'primary_key'       => 'id_alumni',
            'upload_path'       => 'uploads/alumni_smp/'
        ]);
	}

	/**
	* Get Image Alumni Smp	* 
	* @return JSON
	*/
	public function get_file_ijazah_file($id)
	{
		if (!$this->is_allowed('alumni_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$alumni_smp = $this->model_alumni_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_ijazah', 
            'table_name'        => 'alumni_smp',
            'primary_key'       => 'id_alumni',
            'upload_path'       => 'uploads/alumni_smp/',
            'delete_endpoint'   => 'administrator/alumni_smp/delete_file_ijazah_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('alumni_smp_export');

		$this->model_alumni_smp->export('alumni_smp', 'alumni_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('alumni_smp_export');

		$this->model_alumni_smp->pdf('alumni_smp', 'alumni_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('alumni_smp_export');

		$table = $title = 'alumni_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_alumni_smp->find($id);
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


/* End of file alumni_smp.php */
/* Location: ./application/controllers/administrator/Alumni Smp.php */