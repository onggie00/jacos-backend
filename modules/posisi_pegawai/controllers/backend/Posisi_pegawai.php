<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Posisi Pegawai Controller
*| --------------------------------------------------------------------------
*| Posisi Pegawai site
*|
*/
class Posisi_pegawai extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_posisi_pegawai');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Posisi Pegawais
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('posisi_pegawai_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['posisi_pegawais'] = $this->model_posisi_pegawai->get($filter, $field, $this->limit_page, $offset);
		$this->data['posisi_pegawai_counts'] = $this->model_posisi_pegawai->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/posisi_pegawai/index/',
			'total_rows'   => $this->model_posisi_pegawai->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Posisi Pegawai List');
		$this->render('backend/standart/administrator/posisi_pegawai/posisi_pegawai_list', $this->data);
	}
	
	/**
	* Add new posisi_pegawais
	*
	*/
	public function add()
	{
		$this->is_allowed('posisi_pegawai_add');

		$this->template->title('Posisi Pegawai New');
		$this->render('backend/standart/administrator/posisi_pegawai/posisi_pegawai_add', $this->data);
	}

	/**
	* Add New Posisi Pegawais
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('posisi_pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama' => $this->input->post('nama'),
			];

			
			$save_posisi_pegawai = $this->model_posisi_pegawai->store($save_data);
            

			if ($save_posisi_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_posisi_pegawai;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/posisi_pegawai/edit/' . $save_posisi_pegawai, 'Edit Posisi Pegawai'),
						anchor('administrator/posisi_pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/posisi_pegawai/edit/' . $save_posisi_pegawai, 'Edit Posisi Pegawai')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/posisi_pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/posisi_pegawai');
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
	* Update view Posisi Pegawais
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('posisi_pegawai_update');

		$this->data['posisi_pegawai'] = $this->model_posisi_pegawai->find($id);

		$this->template->title('Posisi Pegawai Update');
		$this->render('backend/standart/administrator/posisi_pegawai/posisi_pegawai_update', $this->data);
	}

	/**
	* Update Posisi Pegawais
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('posisi_pegawai_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama' => $this->input->post('nama'),
			];

			
			$save_posisi_pegawai = $this->model_posisi_pegawai->change($id, $save_data);

			if ($save_posisi_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/posisi_pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/posisi_pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/posisi_pegawai');
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
	* delete Posisi Pegawais
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('posisi_pegawai_delete');

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
            set_message(cclang('has_been_deleted', 'posisi_pegawai'), 'success');
        } else {
            set_message(cclang('error_delete', 'posisi_pegawai'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Posisi Pegawais
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('posisi_pegawai_view');

		$this->data['posisi_pegawai'] = $this->model_posisi_pegawai->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Posisi Pegawai Detail');
		$this->render('backend/standart/administrator/posisi_pegawai/posisi_pegawai_view', $this->data);
	}
	
	/**
	* delete Posisi Pegawais
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$posisi_pegawai = $this->model_posisi_pegawai->find($id);

		
		
		return $this->model_posisi_pegawai->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('posisi_pegawai_export');

		$this->model_posisi_pegawai->export('posisi_pegawai', 'posisi_pegawai');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('posisi_pegawai_export');

		$this->model_posisi_pegawai->pdf('posisi_pegawai', 'posisi_pegawai');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('posisi_pegawai_export');

		$table = $title = 'posisi_pegawai';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_posisi_pegawai->find($id);
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


/* End of file posisi_pegawai.php */
/* Location: ./application/controllers/administrator/Posisi Pegawai.php */