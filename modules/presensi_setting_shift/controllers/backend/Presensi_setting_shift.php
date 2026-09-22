<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Setting Shift Controller
*| --------------------------------------------------------------------------
*| Presensi Setting Shift site
*|
*/
class Presensi_setting_shift extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_setting_shift');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Setting Shifts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_setting_shift_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_setting_shifts'] = $this->model_presensi_setting_shift->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_setting_shift_counts'] = $this->model_presensi_setting_shift->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_setting_shift/index/',
			'total_rows'   => $this->model_presensi_setting_shift->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Setting Time List');
		$this->render('backend/standart/administrator/presensi_setting_shift/presensi_setting_shift_list', $this->data);
	}
	
	/**
	* Add new presensi_setting_shifts
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_setting_shift_add');

		$this->template->title('Presensi Setting Time New');
		$this->render('backend/standart/administrator/presensi_setting_shift/presensi_setting_shift_add', $this->data);
	}

	/**
	* Add New Presensi Setting Shifts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_setting_shift_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_role', 'Role', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('hari[]', 'Hari', 'trim|max_length[255]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[150]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_role' => $this->input->post('id_role'),
				'hari' => implode(',', (array) $this->input->post('hari')),
				'start_checkin' => $this->input->post('start_checkin'),
				'limit_checkin' => $this->input->post('limit_checkin'),
				'start_checkout' => $this->input->post('start_checkout'),
				'limit_checkout' => $this->input->post('limit_checkout'),
				'updated_by' => $this->input->post('updated_by'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_presensi_setting_shift = $this->model_presensi_setting_shift->store($save_data);
            

			if ($save_presensi_setting_shift) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_setting_shift;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_setting_shift/edit/' . $save_presensi_setting_shift, 'Edit Presensi Setting Shift'),
						anchor('administrator/presensi_setting_shift', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_setting_shift/edit/' . $save_presensi_setting_shift, 'Edit Presensi Setting Shift')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_setting_shift');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_setting_shift');
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
	* Update view Presensi Setting Shifts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_setting_shift_update');

		$this->data['presensi_setting_shift'] = $this->model_presensi_setting_shift->find($id);

		$this->template->title('Presensi Setting Time Update');
		$this->render('backend/standart/administrator/presensi_setting_shift/presensi_setting_shift_update', $this->data);
	}

	/**
	* Update Presensi Setting Shifts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_setting_shift_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_role', 'Role', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('hari[]', 'Hari', 'trim|max_length[255]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[150]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_role' => $this->input->post('id_role'),
				'hari' => implode(',', (array) $this->input->post('hari')),
				'start_checkin' => $this->input->post('start_checkin'),
				'limit_checkin' => $this->input->post('limit_checkin'),
				'start_checkout' => $this->input->post('start_checkout'),
				'limit_checkout' => $this->input->post('limit_checkout'),
				'updated_by' => $this->input->post('updated_by'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_presensi_setting_shift = $this->model_presensi_setting_shift->change($id, $save_data);

			if ($save_presensi_setting_shift) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_setting_shift', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_setting_shift');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_setting_shift');
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
	* delete Presensi Setting Shifts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_setting_shift_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_setting_shift'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_setting_shift'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Setting Shifts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_setting_shift_view');

		$this->data['presensi_setting_shift'] = $this->model_presensi_setting_shift->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Setting Time Detail');
		$this->render('backend/standart/administrator/presensi_setting_shift/presensi_setting_shift_view', $this->data);
	}
	
	/**
	* delete Presensi Setting Shifts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_setting_shift = $this->model_presensi_setting_shift->find($id);

		
		
		return $this->model_presensi_setting_shift->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_setting_shift_export');

		$this->model_presensi_setting_shift->export('presensi_setting_shift', 'presensi_setting_shift');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_setting_shift_export');

		$this->model_presensi_setting_shift->pdf('presensi_setting_shift', 'presensi_setting_shift');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_setting_shift_export');

		$table = $title = 'presensi_setting_shift';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_setting_shift->find($id);
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


/* End of file presensi_setting_shift.php */
/* Location: ./application/controllers/administrator/Presensi Setting Shift.php */