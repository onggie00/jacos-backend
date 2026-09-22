<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Judul Kartu Sementara Controller
*| --------------------------------------------------------------------------
*| Judul Kartu Sementara site
*|
*/
class Judul_kartu_sementara extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_judul_kartu_sementara');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Judul Kartu Sementaras
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('judul_kartu_sementara_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['judul_kartu_sementaras'] = $this->model_judul_kartu_sementara->get($filter, $field, $this->limit_page, $offset);
		$this->data['judul_kartu_sementara_counts'] = $this->model_judul_kartu_sementara->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/judul_kartu_sementara/index/',
			'total_rows'   => $this->model_judul_kartu_sementara->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Judul Kartu Sementara List');
		$this->render('backend/standart/administrator/judul_kartu_sementara/judul_kartu_sementara_list', $this->data);
	}
	
	/**
	* Add new judul_kartu_sementaras
	*
	*/
	public function add()
	{
		$this->is_allowed('judul_kartu_sementara_add');

		$this->template->title('Judul Kartu Sementara New');
		$this->render('backend/standart/administrator/judul_kartu_sementara/judul_kartu_sementara_add', $this->data);
	}

	/**
	* Add New Judul Kartu Sementaras
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('judul_kartu_sementara_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_judul_kartu_sementara = $this->model_judul_kartu_sementara->store($save_data);
            

			if ($save_judul_kartu_sementara) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_judul_kartu_sementara;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/judul_kartu_sementara/edit/' . $save_judul_kartu_sementara, 'Edit Judul Kartu Sementara'),
						anchor('administrator/judul_kartu_sementara', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/judul_kartu_sementara/edit/' . $save_judul_kartu_sementara, 'Edit Judul Kartu Sementara')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/judul_kartu_sementara');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/judul_kartu_sementara');
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
	* Update view Judul Kartu Sementaras
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('judul_kartu_sementara_update');

		$this->data['judul_kartu_sementara'] = $this->model_judul_kartu_sementara->find($id);

		$this->template->title('Judul Kartu Sementara Update');
		$this->render('backend/standart/administrator/judul_kartu_sementara/judul_kartu_sementara_update', $this->data);
	}

	/**
	* Update Judul Kartu Sementaras
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('judul_kartu_sementara_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_judul_kartu_sementara = $this->model_judul_kartu_sementara->change($id, $save_data);

			if ($save_judul_kartu_sementara) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/judul_kartu_sementara', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/judul_kartu_sementara');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/judul_kartu_sementara');
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
	* delete Judul Kartu Sementaras
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('judul_kartu_sementara_delete');

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
            set_message(cclang('has_been_deleted', 'judul_kartu_sementara'), 'success');
        } else {
            set_message(cclang('error_delete', 'judul_kartu_sementara'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Judul Kartu Sementaras
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('judul_kartu_sementara_view');

		$this->data['judul_kartu_sementara'] = $this->model_judul_kartu_sementara->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Judul Kartu Sementara Detail');
		$this->render('backend/standart/administrator/judul_kartu_sementara/judul_kartu_sementara_view', $this->data);
	}
	
	/**
	* delete Judul Kartu Sementaras
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$judul_kartu_sementara = $this->model_judul_kartu_sementara->find($id);

		
		
		return $this->model_judul_kartu_sementara->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('judul_kartu_sementara_export');

		$this->model_judul_kartu_sementara->export('judul_kartu_sementara', 'judul_kartu_sementara');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('judul_kartu_sementara_export');

		$this->model_judul_kartu_sementara->pdf('judul_kartu_sementara', 'judul_kartu_sementara');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('judul_kartu_sementara_export');

		$table = $title = 'judul_kartu_sementara';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_judul_kartu_sementara->find($id);
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


/* End of file judul_kartu_sementara.php */
/* Location: ./application/controllers/administrator/Judul Kartu Sementara.php */