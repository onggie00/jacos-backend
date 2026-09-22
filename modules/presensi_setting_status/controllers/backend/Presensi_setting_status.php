<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Setting Status Controller
*| --------------------------------------------------------------------------
*| Presensi Setting Status site
*|
*/
class Presensi_setting_status extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_setting_status');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Setting Statuss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_setting_status_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_setting_statuss'] = $this->model_presensi_setting_status->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_setting_status_counts'] = $this->model_presensi_setting_status->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_setting_status/index/',
			'total_rows'   => $this->model_presensi_setting_status->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Setting Status List');
		$this->render('backend/standart/administrator/presensi_setting_status/presensi_setting_status_list', $this->data);
	}
	
	/**
	* Add new presensi_setting_statuss
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_setting_status_add');

		$this->template->title('Presensi Setting Status New');
		$this->render('backend/standart/administrator/presensi_setting_status/presensi_setting_status_add', $this->data);
	}

	/**
	* Add New Presensi Setting Statuss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_setting_status_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_status', 'Status', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('code', 'Kode', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_status' => $this->input->post('nama_status'),
				'code' => $this->input->post('code'),
				'updated_by' => $this->input->post('updated_by'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_presensi_setting_status = $this->model_presensi_setting_status->store($save_data);
            

			if ($save_presensi_setting_status) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_setting_status;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_setting_status/edit/' . $save_presensi_setting_status, 'Edit Presensi Setting Status'),
						anchor('administrator/presensi_setting_status', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_setting_status/edit/' . $save_presensi_setting_status, 'Edit Presensi Setting Status')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_setting_status');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_setting_status');
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
	* Update view Presensi Setting Statuss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_setting_status_update');

		$this->data['presensi_setting_status'] = $this->model_presensi_setting_status->find($id);

		$this->template->title('Presensi Setting Status Update');
		$this->render('backend/standart/administrator/presensi_setting_status/presensi_setting_status_update', $this->data);
	}

	/**
	* Update Presensi Setting Statuss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_setting_status_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_status', 'Status', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('code', 'Kode', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_status' => $this->input->post('nama_status'),
				'code' => $this->input->post('code'),
				'updated_by' => $this->input->post('updated_by'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_presensi_setting_status = $this->model_presensi_setting_status->change($id, $save_data);

			if ($save_presensi_setting_status) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_setting_status', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_setting_status');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_setting_status');
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
	* delete Presensi Setting Statuss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_setting_status_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_setting_status'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_setting_status'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Setting Statuss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_setting_status_view');

		$this->data['presensi_setting_status'] = $this->model_presensi_setting_status->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Setting Status Detail');
		$this->render('backend/standart/administrator/presensi_setting_status/presensi_setting_status_view', $this->data);
	}
	
	/**
	* delete Presensi Setting Statuss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_setting_status = $this->model_presensi_setting_status->find($id);

		
		
		return $this->model_presensi_setting_status->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_setting_status_export');

		$this->model_presensi_setting_status->export('presensi_setting_status', 'presensi_setting_status');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_setting_status_export');

		$this->model_presensi_setting_status->pdf('presensi_setting_status', 'presensi_setting_status');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_setting_status_export');

		$table = $title = 'presensi_setting_status';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_setting_status->find($id);
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


/* End of file presensi_setting_status.php */
/* Location: ./application/controllers/administrator/Presensi Setting Status.php */