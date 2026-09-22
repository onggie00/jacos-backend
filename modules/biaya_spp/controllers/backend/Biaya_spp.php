<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Biaya Spp Controller
*| --------------------------------------------------------------------------
*| Biaya Spp site
*|
*/
class Biaya_spp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_biaya_spp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Biaya Spps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('biaya_spp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['biaya_spps'] = $this->model_biaya_spp->get($filter, $field, $this->limit_page, $offset);
		$this->data['biaya_spp_counts'] = $this->model_biaya_spp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/biaya_spp/index/',
			'total_rows'   => $this->model_biaya_spp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Biaya Spp List');
		$this->render('backend/standart/administrator/biaya_spp/biaya_spp_list', $this->data);
	}
	
	/**
	* Add new biaya_spps
	*
	*/
	public function add()
	{
		$this->is_allowed('biaya_spp_add');

		$this->template->title('Biaya Spp New');
		$this->render('backend/standart/administrator/biaya_spp/biaya_spp_add', $this->data);
	}

	/**
	* Add New Biaya Spps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('biaya_spp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nominal', 'Nominal', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nominal' => $this->input->post('nominal'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_biaya_spp = $this->model_biaya_spp->store($save_data);
            

			if ($save_biaya_spp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_biaya_spp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/biaya_spp/edit/' . $save_biaya_spp, 'Edit Biaya Spp'),
						anchor('administrator/biaya_spp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/biaya_spp/edit/' . $save_biaya_spp, 'Edit Biaya Spp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/biaya_spp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/biaya_spp');
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
	* Update view Biaya Spps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('biaya_spp_update');

		$this->data['biaya_spp'] = $this->model_biaya_spp->find($id);

		$this->template->title('Biaya Spp Update');
		$this->render('backend/standart/administrator/biaya_spp/biaya_spp_update', $this->data);
	}

	/**
	* Update Biaya Spps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('biaya_spp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nominal', 'Nominal', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nominal' => $this->input->post('nominal'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_biaya_spp = $this->model_biaya_spp->change($id, $save_data);

			if ($save_biaya_spp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/biaya_spp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/biaya_spp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/biaya_spp');
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
	* delete Biaya Spps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('biaya_spp_delete');

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
            set_message(cclang('has_been_deleted', 'biaya_spp'), 'success');
        } else {
            set_message(cclang('error_delete', 'biaya_spp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Biaya Spps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('biaya_spp_view');

		$this->data['biaya_spp'] = $this->model_biaya_spp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Biaya Spp Detail');
		$this->render('backend/standart/administrator/biaya_spp/biaya_spp_view', $this->data);
	}
	
	/**
	* delete Biaya Spps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$biaya_spp = $this->model_biaya_spp->find($id);

		
		
		return $this->model_biaya_spp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('biaya_spp_export');

		$this->model_biaya_spp->export('biaya_spp', 'biaya_spp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('biaya_spp_export');

		$this->model_biaya_spp->pdf('biaya_spp', 'biaya_spp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('biaya_spp_export');

		$table = $title = 'biaya_spp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_biaya_spp->find($id);
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


/* End of file biaya_spp.php */
/* Location: ./application/controllers/administrator/Biaya Spp.php */