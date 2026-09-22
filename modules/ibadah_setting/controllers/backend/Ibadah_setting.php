<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ibadah Setting Controller
*| --------------------------------------------------------------------------
*| Ibadah Setting site
*|
*/
class Ibadah_setting extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ibadah_setting');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ibadah Settings
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ibadah_setting_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ibadah_settings'] = $this->model_ibadah_setting->get($filter, $field, $this->limit_page, $offset);
		$this->data['ibadah_setting_counts'] = $this->model_ibadah_setting->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ibadah_setting/index/',
			'total_rows'   => $this->model_ibadah_setting->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Setting Ibadah List');
		$this->render('backend/standart/administrator/ibadah_setting/ibadah_setting_list', $this->data);
	}
	
	/**
	* Add new ibadah_settings
	*
	*/
	public function add()
	{
		$this->is_allowed('ibadah_setting_add');

		$this->template->title('Setting Ibadah New');
		$this->render('backend/standart/administrator/ibadah_setting/ibadah_setting_add', $this->data);
	}

	/**
	* Add New Ibadah Settings
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ibadah_setting_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('ibadah', 'Jenis Ibadah', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('start_ibadah', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('longitude', 'Longitude', 'trim');
		$this->form_validation->set_rules('latitude', 'Waktu Latitude', 'trim');
		$this->form_validation->set_rules('radius', 'Radius', 'trim');

		if ($this->form_validation->run()) {
		
			$save_data = [
				'ibadah' => $this->input->post('ibadah'),
				'start_ibadah' => $this->input->post('start_ibadah'),
				'end_ibadah' => $this->input->post('end_ibadah'),
				'longitude' => (!empty($this->input->post('longitude'))) ? $this->input->post('longitude') : null,
				'latitude' => (!empty($this->input->post('latitude'))) ? $this->input->post('latitude') : null,
				'radius' => (!empty($this->input->post('radius'))) ? $this->input->post('radius') : null,
			];

			
			$save_ibadah_setting = $this->model_ibadah_setting->store($save_data);
            

			if ($save_ibadah_setting) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ibadah_setting;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ibadah_setting/edit/' . $save_ibadah_setting, 'Edit Ibadah Setting'),
						anchor('administrator/ibadah_setting', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ibadah_setting/edit/' . $save_ibadah_setting, 'Edit Ibadah Setting')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ibadah_setting');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ibadah_setting');
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
	* Update view Ibadah Settings
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ibadah_setting_update');

		$this->data['ibadah_setting'] = $this->model_ibadah_setting->find($id);

		$this->template->title('Setting Ibadah Update');
		$this->render('backend/standart/administrator/ibadah_setting/ibadah_setting_update', $this->data);
	}

	/**
	* Update Ibadah Settings
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ibadah_setting_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('ibadah', 'Jenis Ibadah', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('start_ibadah', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('longitude', 'Waktu Longitude', 'trim');
		$this->form_validation->set_rules('latitude', 'Waktu Latitude', 'trim');
		$this->form_validation->set_rules('radius', 'Radius', 'trim');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'ibadah' => $this->input->post('ibadah'),
				'start_ibadah' => $this->input->post('start_ibadah'),
				'end_ibadah' => $this->input->post('end_ibadah'),
				'longitude' => (!empty($this->input->post('longitude'))) ? $this->input->post('longitude') : null,
				'latitude' => (!empty($this->input->post('latitude'))) ? $this->input->post('latitude') : null,
				'radius' => (!empty($this->input->post('radius'))) ? $this->input->post('radius') : null,
			];

			
			$save_ibadah_setting = $this->model_ibadah_setting->change($id, $save_data);

			if ($save_ibadah_setting) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ibadah_setting', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ibadah_setting');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ibadah_setting');
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
	* delete Ibadah Settings
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ibadah_setting_delete');

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
            set_message(cclang('has_been_deleted', 'ibadah_setting'), 'success');
        } else {
            set_message(cclang('error_delete', 'ibadah_setting'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ibadah Settings
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ibadah_setting_view');

		$this->data['ibadah_setting'] = $this->model_ibadah_setting->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Setting Ibadah Detail');
		$this->render('backend/standart/administrator/ibadah_setting/ibadah_setting_view', $this->data);
	}
	
	/**
	* delete Ibadah Settings
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ibadah_setting = $this->model_ibadah_setting->find($id);

		
		
		return $this->model_ibadah_setting->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ibadah_setting_export');

		$this->model_ibadah_setting->export('ibadah_setting', 'ibadah_setting');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ibadah_setting_export');

		$this->model_ibadah_setting->pdf('ibadah_setting', 'ibadah_setting');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ibadah_setting_export');

		$table = $title = 'ibadah_setting';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ibadah_setting->find($id);
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


/* End of file ibadah_setting.php */
/* Location: ./application/controllers/administrator/Ibadah Setting.php */