<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tingkatan Kb Controller
*| --------------------------------------------------------------------------
*| Tingkatan Kb site
*|
*/
class Tingkatan_Kb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tingkatan_kb');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tingkatan Kbs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tingkatan_kb_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tingkatan_kbs'] = $this->model_tingkatan_kb->get($filter, $field, $this->limit_page, $offset);
		$this->data['tingkatan_kb_counts'] = $this->model_tingkatan_kb->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tingkatan_kb/index/',
			'total_rows'   => $this->model_tingkatan_kb->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tingkatan Kb List');
		$this->render('backend/standart/administrator/tingkatan_kb/tingkatan_kb_list', $this->data);
	}
	
	/**
	* Add new tingkatan_kbs
	*
	*/
	public function add()
	{
		$this->is_allowed('tingkatan_kb_add');

		$this->template->title('Tingkatan Kb New');
		$this->render('backend/standart/administrator/tingkatan_kb/tingkatan_kb_add', $this->data);
	}

	/**
	* Add New Tingkatan Kbs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tingkatan_kb_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya Spp', 'trim|required');
		$this->form_validation->set_rules('usia_min', 'Usia Minimal', 'trim|numeric');
		$this->form_validation->set_rules('usia_max', 'Usia Maksimal', 'trim|numeric');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'biaya_spp' => $this->input->post('biaya_spp'),
			'usia_min' => $this->input->post('usia_min') !== '' ? $this->input->post('usia_min') : null,
			'usia_max' => $this->input->post('usia_max') !== '' ? $this->input->post('usia_max') : null,
			];

			
			$save_tingkatan_kb = $this->model_tingkatan_kb->store($save_data);
            

			if ($save_tingkatan_kb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tingkatan_kb;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tingkatan_kb/edit/' . $save_tingkatan_kb, 'Edit Tingkatan Kb'),
						anchor('administrator/tingkatan_kb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tingkatan_kb/edit/' . $save_tingkatan_kb, 'Edit Tingkatan Kb')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tingkatan_kb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tingkatan_kb');
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
	* Update view Tingkatan Kbs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tingkatan_kb_update');

		$this->data['tingkatan_kb'] = $this->model_tingkatan_kb->find($id);

		$this->template->title('Tingkatan Kb Update');
		$this->render('backend/standart/administrator/tingkatan_kb/tingkatan_kb_update', $this->data);
	}

	/**
	* Update Tingkatan Kbs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tingkatan_kb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya Spp', 'trim|required');
		$this->form_validation->set_rules('usia_min', 'Usia Minimal', 'trim|numeric');
		$this->form_validation->set_rules('usia_max', 'Usia Maksimal', 'trim|numeric');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'biaya_spp' => $this->input->post('biaya_spp'),
			'usia_min' => $this->input->post('usia_min') !== '' ? $this->input->post('usia_min') : null,
			'usia_max' => $this->input->post('usia_max') !== '' ? $this->input->post('usia_max') : null,
			];

			
			$save_tingkatan_kb = $this->model_tingkatan_kb->change($id, $save_data);

			if ($save_tingkatan_kb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tingkatan_kb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tingkatan_kb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tingkatan_kb');
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
	* delete Tingkatan Kbs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tingkatan_kb_delete');

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
            set_message(cclang('has_been_deleted', 'tingkatan_kb'), 'success');
        } else {
            set_message(cclang('error_delete', 'tingkatan_kb'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tingkatan Kbs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tingkatan_kb_view');

		$this->data['tingkatan_kb'] = $this->model_tingkatan_kb->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tingkatan Kb Detail');
		$this->render('backend/standart/administrator/tingkatan_kb/tingkatan_kb_view', $this->data);
	}
	
	/**
	* delete Tingkatan Kbs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tingkatan_kb = $this->model_tingkatan_kb->find($id);

		
		
		return $this->model_tingkatan_kb->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tingkatan_kb_export');

		$this->model_tingkatan_kb->export('tingkatan_kb', 'tingkatan_kb');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tingkatan_kb_export');

		$this->model_tingkatan_kb->pdf('tingkatan_kb', 'tingkatan_kb');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tingkatan_kb_export');

		$table = $title = 'tingkatan_kb';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tingkatan_kb->find($id);
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


/* End of file tingkatan_kb.php */
/* Location: ./application/controllers/administrator/Tingkatan Kb.php */