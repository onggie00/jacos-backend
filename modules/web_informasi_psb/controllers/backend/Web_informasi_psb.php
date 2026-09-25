<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Web Informasi Psb Controller
*| --------------------------------------------------------------------------
*| Web Informasi Psb site
*|
*/
class Web_informasi_psb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_informasi_psb');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Web Informasi Psbs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_informasi_psb_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['web_informasi_psbs'] = $this->model_web_informasi_psb->get($filter, $field, $this->limit_page, $offset);
		$this->data['web_informasi_psb_counts'] = $this->model_web_informasi_psb->count_all($filter, $field);

		// statistik utk info-box dashboard modul
		$this->data['stat_total'] = (int) $this->mymodel->withquery("SELECT COUNT(*) AS c FROM web_informasi_psb", "row")->c;
		$this->data['stat_gambar'] = (int) $this->mymodel->withquery("SELECT COUNT(*) AS c FROM web_informasi_psb WHERE img_file IS NOT NULL AND img_file <> ''", "row")->c;
		$this->data['stat_bulan_ini'] = (int) $this->mymodel->withquery("SELECT COUNT(*) AS c FROM web_informasi_psb WHERE DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'", "row")->c;

		$config = [
			'base_url'     => 'administrator/web_informasi_psb/index/',
			'total_rows'   => $this->model_web_informasi_psb->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Informasi Psb List');
		$this->render('backend/standart/administrator/web_informasi_psb/web_informasi_psb_list', $this->data);
	}
	
	/**
	* Add new web_informasi_psbs
	*
	*/
	public function add()
	{
		$this->is_allowed('web_informasi_psb_add');

		$this->template->title('Informasi Psb New');
		$this->render('backend/standart/administrator/web_informasi_psb/web_informasi_psb_add', $this->data);
	}

	/**
	* Add New Web Informasi Psbs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('web_informasi_psb_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('web_informasi_psb_img_file_name', 'Img File', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$web_informasi_psb_img_file_uuid = $this->input->post('web_informasi_psb_img_file_uuid');
			$web_informasi_psb_img_file_name = $this->input->post('web_informasi_psb_img_file_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'deskripsi' => $this->input->post('deskripsi'),
				'created_at' => date("Y-m-d H:i:s"),
			];

			if (!is_dir(FCPATH . '/uploads/web_informasi_psb/')) {
				mkdir(FCPATH . '/uploads/web_informasi_psb/');
			}

			if (!empty($web_informasi_psb_img_file_name)) {
				$web_informasi_psb_img_file_name_copy = date('YmdHis') . '-' . $web_informasi_psb_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $web_informasi_psb_img_file_uuid . '/' . $web_informasi_psb_img_file_name, 
						FCPATH . 'uploads/web_informasi_psb/' . $web_informasi_psb_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/web_informasi_psb/' . $web_informasi_psb_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $web_informasi_psb_img_file_name_copy;
			}
		
			
			$save_web_informasi_psb = $this->model_web_informasi_psb->store($save_data);
            

			if ($save_web_informasi_psb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_web_informasi_psb;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/web_informasi_psb/edit/' . $save_web_informasi_psb, 'Edit Web Informasi Psb'),
						anchor('administrator/web_informasi_psb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/web_informasi_psb/edit/' . $save_web_informasi_psb, 'Edit Web Informasi Psb')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_informasi_psb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_informasi_psb');
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
	* Update view Web Informasi Psbs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_informasi_psb_update');

		$this->data['web_informasi_psb'] = $this->model_web_informasi_psb->find($id);

		$this->template->title('Informasi Psb Update');
		$this->render('backend/standart/administrator/web_informasi_psb/web_informasi_psb_update', $this->data);
	}

	/**
	* Update Web Informasi Psbs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_informasi_psb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('web_informasi_psb_img_file_name', 'Img File', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$web_informasi_psb_img_file_uuid = $this->input->post('web_informasi_psb_img_file_uuid');
			$web_informasi_psb_img_file_name = $this->input->post('web_informasi_psb_img_file_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'deskripsi' => $this->input->post('deskripsi'),
				'created_at' => date("Y-m-d H:i:s"),
			];

			if (!is_dir(FCPATH . '/uploads/web_informasi_psb/')) {
				mkdir(FCPATH . '/uploads/web_informasi_psb/');
			}

			if (!empty($web_informasi_psb_img_file_uuid)) {
				$web_informasi_psb_img_file_name_copy = date('YmdHis') . '-' . $web_informasi_psb_img_file_name;

				rename(FCPATH . 'uploads/tmp/' . $web_informasi_psb_img_file_uuid . '/' . $web_informasi_psb_img_file_name, 
						FCPATH . 'uploads/web_informasi_psb/' . $web_informasi_psb_img_file_name_copy);

				if (!is_file(FCPATH . '/uploads/web_informasi_psb/' . $web_informasi_psb_img_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_file'] = $web_informasi_psb_img_file_name_copy;
			}
		
			
			$save_web_informasi_psb = $this->model_web_informasi_psb->change($id, $save_data);

			if ($save_web_informasi_psb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_informasi_psb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_informasi_psb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_informasi_psb');
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
	* delete Web Informasi Psbs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_informasi_psb_delete');

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
            set_message(cclang('has_been_deleted', 'web_informasi_psb'), 'success');
        } else {
            set_message(cclang('error_delete', 'web_informasi_psb'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Web Informasi Psbs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('web_informasi_psb_view');

		$this->data['web_informasi_psb'] = $this->model_web_informasi_psb->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Informasi Psb Detail');
		$this->render('backend/standart/administrator/web_informasi_psb/web_informasi_psb_view', $this->data);
	}
	
	/**
	* delete Web Informasi Psbs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$web_informasi_psb = $this->model_web_informasi_psb->find($id);

		if (!empty($web_informasi_psb->img_file)) {
			$path = FCPATH . '/uploads/web_informasi_psb/' . $web_informasi_psb->img_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_web_informasi_psb->remove($id);
	}
	
	/**
	* Upload Image Web Informasi Psb	* 
	* @return JSON
	*/
	public function upload_img_file_file()
	{
		if (!$this->is_allowed('web_informasi_psb_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'web_informasi_psb',
		]);
	}

	/**
	* Delete Image Web Informasi Psb	* 
	* @return JSON
	*/
	public function delete_img_file_file($uuid)
	{
		if (!$this->is_allowed('web_informasi_psb_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'img_file', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'web_informasi_psb',
            'primary_key'       => 'id_web_informasi_psb',
            'upload_path'       => 'uploads/web_informasi_psb/'
        ]);
	}

	/**
	* Get Image Web Informasi Psb	* 
	* @return JSON
	*/
	public function get_img_file_file($id)
	{
		if (!$this->is_allowed('web_informasi_psb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$web_informasi_psb = $this->model_web_informasi_psb->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_file', 
            'table_name'        => 'web_informasi_psb',
            'primary_key'       => 'id_web_informasi_psb',
            'upload_path'       => 'uploads/web_informasi_psb/',
            'delete_endpoint'   => 'administrator/web_informasi_psb/delete_img_file_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('web_informasi_psb_export');

		$this->model_web_informasi_psb->export('web_informasi_psb', 'web_informasi_psb');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('web_informasi_psb_export');

		$this->model_web_informasi_psb->pdf('web_informasi_psb', 'web_informasi_psb');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('web_informasi_psb_export');

		$table = $title = 'web_informasi_psb';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_web_informasi_psb->find($id);
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


/* End of file web_informasi_psb.php */
/* Location: ./application/controllers/administrator/Web Informasi Psb.php */