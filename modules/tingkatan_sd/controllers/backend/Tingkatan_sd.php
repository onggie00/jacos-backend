<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tingkatan Sd Controller
*| --------------------------------------------------------------------------
*| Tingkatan Sd site
*|
*/
class Tingkatan_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tingkatan_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tingkatan Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tingkatan_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tingkatan_sds'] = $this->model_tingkatan_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['tingkatan_sd_counts'] = $this->model_tingkatan_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tingkatan_sd/index/',
			'total_rows'   => $this->model_tingkatan_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tingkatan Sd List');
		$this->render('backend/standart/administrator/tingkatan_sd/tingkatan_sd_list', $this->data);
	}
	
	/**
	* Add new tingkatan_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('tingkatan_sd_add');

		$this->template->title('Tingkatan Sd New');
		$this->render('backend/standart/administrator/tingkatan_sd/tingkatan_sd_add', $this->data);
	}

	/**
	* Add New Tingkatan Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tingkatan_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya Spp', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'biaya_spp' => $this->input->post('biaya_spp'),
			];

			
			$save_tingkatan_sd = $this->model_tingkatan_sd->store($save_data);
            

			if ($save_tingkatan_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tingkatan_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tingkatan_sd/edit/' . $save_tingkatan_sd, 'Edit Tingkatan Sd'),
						anchor('administrator/tingkatan_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tingkatan_sd/edit/' . $save_tingkatan_sd, 'Edit Tingkatan Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tingkatan_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tingkatan_sd');
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
	* Update view Tingkatan Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tingkatan_sd_update');

		$this->data['tingkatan_sd'] = $this->model_tingkatan_sd->find($id);

		$this->template->title('Tingkatan Sd Update');
		$this->render('backend/standart/administrator/tingkatan_sd/tingkatan_sd_update', $this->data);
	}

	/**
	* Update Tingkatan Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tingkatan_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya Spp', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'biaya_spp' => $this->input->post('biaya_spp'),
			];

			
			$save_tingkatan_sd = $this->model_tingkatan_sd->change($id, $save_data);

			if ($save_tingkatan_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tingkatan_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tingkatan_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tingkatan_sd');
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
	* delete Tingkatan Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tingkatan_sd_delete');

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
            set_message(cclang('has_been_deleted', 'tingkatan_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'tingkatan_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tingkatan Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tingkatan_sd_view');

		$this->data['tingkatan_sd'] = $this->model_tingkatan_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tingkatan Sd Detail');
		$this->render('backend/standart/administrator/tingkatan_sd/tingkatan_sd_view', $this->data);
	}
	
	/**
	* delete Tingkatan Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tingkatan_sd = $this->model_tingkatan_sd->find($id);

		
		
		return $this->model_tingkatan_sd->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tingkatan_sd_export');

		$this->model_tingkatan_sd->export('tingkatan_sd', 'tingkatan_sd');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tingkatan_sd_export');

		$this->model_tingkatan_sd->pdf('tingkatan_sd', 'tingkatan_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tingkatan_sd_export');

		$table = $title = 'tingkatan_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tingkatan_sd->find($id);
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


/* End of file tingkatan_sd.php */
/* Location: ./application/controllers/administrator/Tingkatan Sd.php */