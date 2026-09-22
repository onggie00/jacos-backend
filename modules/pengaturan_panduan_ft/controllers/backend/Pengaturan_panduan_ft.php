<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Panduan Ft Controller
*| --------------------------------------------------------------------------
*| Pengaturan Panduan Ft site
*|
*/
class Pengaturan_panduan_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_panduan_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Panduan Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_panduan_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_panduan_fts'] = $this->model_pengaturan_panduan_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_panduan_ft_counts'] = $this->model_pengaturan_panduan_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_panduan_ft/index/',
			'total_rows'   => $this->model_pengaturan_panduan_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Panduan Ft List');
		$this->render('backend/standart/administrator/pengaturan_panduan_ft/pengaturan_panduan_ft_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Panduan Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_panduan_ft_update');

		$this->data['pengaturan_panduan_ft'] = $this->model_pengaturan_panduan_ft->find($id);

		$this->template->title('Pengaturan Panduan Ft Update');
		$this->render('backend/standart/administrator/pengaturan_panduan_ft/pengaturan_panduan_ft_update', $this->data);
	}

	/**
	* Update Pengaturan Panduan Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_panduan_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('pengaturan_panduan_ft_file_panduan_name', 'File Panduan', 'trim|required');
		
		if ($this->form_validation->run()) {
			$pengaturan_panduan_ft_file_panduan_uuid = $this->input->post('pengaturan_panduan_ft_file_panduan_uuid');
			$pengaturan_panduan_ft_file_panduan_name = $this->input->post('pengaturan_panduan_ft_file_panduan_name');
		
			$save_data = [
			];

			if (!is_dir(FCPATH . '/uploads/pengaturan_panduan_ft/')) {
				mkdir(FCPATH . '/uploads/pengaturan_panduan_ft/');
			}

			if (!empty($pengaturan_panduan_ft_file_panduan_uuid)) {
				$pengaturan_panduan_ft_file_panduan_name_copy = date('YmdHis') . '-' . $pengaturan_panduan_ft_file_panduan_name;

				rename(FCPATH . 'uploads/tmp/' . $pengaturan_panduan_ft_file_panduan_uuid . '/' . $pengaturan_panduan_ft_file_panduan_name, 
						FCPATH . 'uploads/pengaturan_panduan_ft/' . $pengaturan_panduan_ft_file_panduan_name_copy);

				if (!is_file(FCPATH . '/uploads/pengaturan_panduan_ft/' . $pengaturan_panduan_ft_file_panduan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_panduan'] = $pengaturan_panduan_ft_file_panduan_name_copy;
			}
		
			
			$save_pengaturan_panduan_ft = $this->model_pengaturan_panduan_ft->change($id, $save_data);

			if ($save_pengaturan_panduan_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_panduan_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_panduan_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_panduan_ft');
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
	* delete Pengaturan Panduan Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_panduan_ft_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_panduan_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_panduan_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Panduan Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_panduan_ft_view');

		$this->data['pengaturan_panduan_ft'] = $this->model_pengaturan_panduan_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Panduan Ft Detail');
		$this->render('backend/standart/administrator/pengaturan_panduan_ft/pengaturan_panduan_ft_view', $this->data);
	}
	
	/**
	* delete Pengaturan Panduan Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_panduan_ft = $this->model_pengaturan_panduan_ft->find($id);

		if (!empty($pengaturan_panduan_ft->file_panduan)) {
			$path = FCPATH . '/uploads/pengaturan_panduan_ft/' . $pengaturan_panduan_ft->file_panduan;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_pengaturan_panduan_ft->remove($id);
	}
	
	/**
	* Upload Image Pengaturan Panduan Ft	* 
	* @return JSON
	*/
	public function upload_file_panduan_file()
	{
		if (!$this->is_allowed('pengaturan_panduan_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pengaturan_panduan_ft',
		]);
	}

	/**
	* Delete Image Pengaturan Panduan Ft	* 
	* @return JSON
	*/
	public function delete_file_panduan_file($uuid)
	{
		if (!$this->is_allowed('pengaturan_panduan_ft_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_panduan', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'pengaturan_panduan_ft',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/pengaturan_panduan_ft/'
        ]);
	}

	/**
	* Get Image Pengaturan Panduan Ft	* 
	* @return JSON
	*/
	public function get_file_panduan_file($id)
	{
		if (!$this->is_allowed('pengaturan_panduan_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pengaturan_panduan_ft = $this->model_pengaturan_panduan_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_panduan', 
            'table_name'        => 'pengaturan_panduan_ft',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/pengaturan_panduan_ft/',
            'delete_endpoint'   => 'administrator/pengaturan_panduan_ft/delete_file_panduan_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_panduan_ft_export');

		$this->model_pengaturan_panduan_ft->export('pengaturan_panduan_ft', 'pengaturan_panduan_ft');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_panduan_ft_export');

		$this->model_pengaturan_panduan_ft->pdf('pengaturan_panduan_ft', 'pengaturan_panduan_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_panduan_ft_export');

		$table = $title = 'pengaturan_panduan_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_panduan_ft->find($id);
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


/* End of file pengaturan_panduan_ft.php */
/* Location: ./application/controllers/administrator/Pengaturan Panduan Ft.php */