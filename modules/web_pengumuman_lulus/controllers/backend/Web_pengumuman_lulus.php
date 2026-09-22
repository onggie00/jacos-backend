<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Web Pengumuman Lulus Controller
*| --------------------------------------------------------------------------
*| Web Pengumuman Lulus site
*|
*/
class Web_pengumuman_lulus extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_pengumuman_lulus');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Web Pengumuman Luluss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_pengumuman_lulus_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['web_pengumuman_luluss'] = $this->model_web_pengumuman_lulus->get($filter, $field, $this->limit_page, $offset);
		$this->data['web_pengumuman_lulus_counts'] = $this->model_web_pengumuman_lulus->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/web_pengumuman_lulus/index/',
			'total_rows'   => $this->model_web_pengumuman_lulus->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('File Pengumuman Lulus List');
		$this->render('backend/standart/administrator/web_pengumuman_lulus/web_pengumuman_lulus_list', $this->data);
	}
	
	
		/**
	* Update view Web Pengumuman Luluss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_pengumuman_lulus_update');

		$this->data['web_pengumuman_lulus'] = $this->model_web_pengumuman_lulus->find($id);

		$this->template->title('File Pengumuman Lulus Update');
		$this->render('backend/standart/administrator/web_pengumuman_lulus/web_pengumuman_lulus_update', $this->data);
	}

	/**
	* Update Web Pengumuman Luluss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_pengumuman_lulus_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('web_pengumuman_lulus_dokumen_name', 'Dokumen', 'trim|required');
		
		if ($this->form_validation->run()) {

			$web_pengumuman_lulus_dokumen_uuid = $this->input->post('web_pengumuman_lulus_dokumen_uuid');
			$web_pengumuman_lulus_dokumen_name = $this->input->post('web_pengumuman_lulus_dokumen_name');
		
			$save_data = [
			];

			if (!is_dir(FCPATH . '/uploads/web_pengumuman_lulus/')) {
				mkdir(FCPATH . '/uploads/web_pengumuman_lulus/');
			}

			if (!empty($web_pengumuman_lulus_dokumen_name)) {
				$web_pengumuman_lulus_dokumen_copy = date('YmdHis') . '-' . $web_pengumuman_lulus_dokumen_name;

				rename(FCPATH . 'uploads/tmp/' . $web_pengumuman_lulus_dokumen_uuid . '/' . $web_pengumuman_lulus_dokumen_name, 
						FCPATH . 'uploads/web_pengumuman_lulus/' . $web_pengumuman_lulus_dokumen_copy);

				if (!is_file(FCPATH . '/uploads/web_pengumuman_lulus/' . $web_pengumuman_lulus_dokumen_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['dokumen'] = $web_pengumuman_lulus_dokumen_copy;
			}
			
			$save_web_pengumuman_lulus = $this->model_web_pengumuman_lulus->change($id, $save_data);

			if ($save_web_pengumuman_lulus) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_pengumuman_lulus', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_pengumuman_lulus');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_pengumuman_lulus');
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
	* delete Web Pengumuman Luluss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_pengumuman_lulus_delete');

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
            set_message(cclang('has_been_deleted', 'web_pengumuman_lulus'), 'success');
        } else {
            set_message(cclang('error_delete', 'web_pengumuman_lulus'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Web Pengumuman Luluss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('web_pengumuman_lulus_view');

		$this->data['web_pengumuman_lulus'] = $this->model_web_pengumuman_lulus->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('File Pengumuman Lulus Detail');
		$this->render('backend/standart/administrator/web_pengumuman_lulus/web_pengumuman_lulus_view', $this->data);
	}
	
	/**
	* delete Web Pengumuman Luluss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$web_pengumuman_lulus = $this->model_web_pengumuman_lulus->find($id);

		if (!empty($web_pengumuman_lulus->dokumen)) {
			$path = FCPATH . '/uploads/web_pengumuman_lulus/' . $web_pengumuman_lulus->dokumen;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_web_pengumuman_lulus->remove($id);
	}
	
	/**
	* Upload Image Web Pengumuman Lulus	* 
	* @return JSON
	*/
	public function upload_dokumen_file()
	{
		if (!$this->is_allowed('web_pengumuman_lulus_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'web_pengumuman_lulus',
		]);
	}

	/**
	* Delete Image Web Pengumuman Lulus	* 
	* @return JSON
	*/
	public function delete_dokumen_file($uuid)
	{
		if (!$this->is_allowed('web_pengumuman_lulus_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'dokumen', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'web_pengumuman_lulus',
            'primary_key'       => 'id_web_pengumuman_lulus',
            'upload_path'       => 'uploads/web_pengumuman_lulus/'
        ]);
	}

	/**
	* Get Image Web Pengumuman Lulus	* 
	* @return JSON
	*/
	public function get_dokumen_file($id)
	{
		if (!$this->is_allowed('web_pengumuman_lulus_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$web_pengumuman_lulus = $this->model_web_pengumuman_lulus->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'dokumen', 
            'table_name'        => 'web_pengumuman_lulus',
            'primary_key'       => 'id_web_pengumuman_lulus',
            'upload_path'       => 'uploads/web_pengumuman_lulus/',
            'delete_endpoint'   => 'administrator/web_pengumuman_lulus/delete_dokumen_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('web_pengumuman_lulus_export');

		$this->model_web_pengumuman_lulus->export('web_pengumuman_lulus', 'web_pengumuman_lulus');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('web_pengumuman_lulus_export');

		$this->model_web_pengumuman_lulus->pdf('web_pengumuman_lulus', 'web_pengumuman_lulus');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('web_pengumuman_lulus_export');

		$table = $title = 'web_pengumuman_lulus';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_web_pengumuman_lulus->find($id);
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


/* End of file web_pengumuman_lulus.php */
/* Location: ./application/controllers/administrator/Web Pengumuman Lulus.php */