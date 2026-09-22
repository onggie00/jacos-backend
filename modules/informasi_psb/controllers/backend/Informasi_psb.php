<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

/**
*| --------------------------------------------------------------------------
*| Informasi Psb Controller
*| --------------------------------------------------------------------------
*| Informasi Psb site
*|
*/
class Informasi_psb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_informasi_psb');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Informasi Psbs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('informasi_psb_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['informasi_psbs'] = $this->model_informasi_psb->get($filter, $field, $this->limit_page, $offset);
		$this->data['informasi_psb_counts'] = $this->model_informasi_psb->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/informasi_psb/index/',
			'total_rows'   => $this->model_informasi_psb->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Informasi Psb List');
		$this->render('backend/standart/administrator/informasi_psb/informasi_psb_list', $this->data);
	}
	
	/**
	* Add new informasi_psbs
	*
	*/
	public function add()
	{
		$this->is_allowed('informasi_psb_add');

		$this->template->title('Informasi Psb New');
		$this->render('backend/standart/administrator/informasi_psb/informasi_psb_add', $this->data);
	}

	/**
	* Add New Informasi Psbs
	*
	* @return JSON
	*/
	public function add_save()
	{
		date_default_timezone_set('Asia/Jakarta');
		
		if (!$this->is_allowed('informasi_psb_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('thumbnail', 'Thumbnail', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('foto_informasi', 'Foto Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'deskripsi' => $this->input->post('deskripsi'),
				'thumbnail' => $this->input->post('thumbnail'),
				'foto_informasi' => $this->input->post('foto_informasi'),
				'created_at' => date("Y-m-d H:i:s"),
			];

			
			$save_informasi_psb = $this->model_informasi_psb->store($save_data);
            

			if ($save_informasi_psb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_informasi_psb;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/informasi_psb/edit/' . $save_informasi_psb, 'Edit Informasi Psb'),
						anchor('administrator/informasi_psb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/informasi_psb/edit/' . $save_informasi_psb, 'Edit Informasi Psb')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/informasi_psb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/informasi_psb');
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
	* Update view Informasi Psbs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('informasi_psb_update');

		$this->data['informasi_psb'] = $this->model_informasi_psb->find($id);

		$this->template->title('Informasi Psb Update');
		$this->render('backend/standart/administrator/informasi_psb/informasi_psb_update', $this->data);
	}

	/**
	* Update Informasi Psbs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('informasi_psb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('thumbnail', 'Thumbnail', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('foto_informasi', 'Foto Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'deskripsi' => $this->input->post('deskripsi'),
				'thumbnail' => $this->input->post('thumbnail'),
				'foto_informasi' => $this->input->post('foto_informasi'),
				'created_at' => $this->input->post('created_at'),
			];

			
			$save_informasi_psb = $this->model_informasi_psb->change($id, $save_data);

			if ($save_informasi_psb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/informasi_psb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/informasi_psb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/informasi_psb');
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
	* delete Informasi Psbs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('informasi_psb_delete');

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
            set_message(cclang('has_been_deleted', 'informasi_psb'), 'success');
        } else {
            set_message(cclang('error_delete', 'informasi_psb'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Informasi Psbs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('informasi_psb_view');

		$this->data['informasi_psb'] = $this->model_informasi_psb->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Informasi Psb Detail');
		$this->render('backend/standart/administrator/informasi_psb/informasi_psb_view', $this->data);
	}
	
	/**
	* delete Informasi Psbs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$informasi_psb = $this->model_informasi_psb->find($id);

		
		
		return $this->model_informasi_psb->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('informasi_psb_export');

		$this->model_informasi_psb->export('informasi_psb', 'informasi_psb');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('informasi_psb_export');

		$this->model_informasi_psb->pdf('informasi_psb', 'informasi_psb');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('informasi_psb_export');

		$table = $title = 'informasi_psb';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_informasi_psb->find($id);
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


/* End of file informasi_psb.php */
/* Location: ./application/controllers/administrator/Informasi Psb.php */