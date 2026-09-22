<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Masa Aktif Va Controller
*| --------------------------------------------------------------------------
*| Pengaturan Masa Aktif Va site
*|
*/
class Pengaturan_masa_aktif_va extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_masa_aktif_va');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Masa Aktif Vas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_masa_aktif_va_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_masa_aktif_vas'] = $this->model_pengaturan_masa_aktif_va->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_masa_aktif_va_counts'] = $this->model_pengaturan_masa_aktif_va->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_masa_aktif_va/index/',
			'total_rows'   => $this->model_pengaturan_masa_aktif_va->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Masa Aktif VA List');
		$this->render('backend/standart/administrator/pengaturan_masa_aktif_va/pengaturan_masa_aktif_va_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Masa Aktif Vas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_masa_aktif_va_update');

		$this->data['pengaturan_masa_aktif_va'] = $this->model_pengaturan_masa_aktif_va->find($id);

		$this->template->title('Pengaturan Masa Aktif VA Update');
		$this->render('backend/standart/administrator/pengaturan_masa_aktif_va/pengaturan_masa_aktif_va_update', $this->data);
	}

	/**
	* Update Pengaturan Masa Aktif Vas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_masa_aktif_va_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('value', 'Value', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('tipe_date', 'Tipe Date', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'value' => $this->input->post('value'),
				'tipe_date' => $this->input->post('tipe_date'),
			];

			
			$save_pengaturan_masa_aktif_va = $this->model_pengaturan_masa_aktif_va->change($id, $save_data);

			if ($save_pengaturan_masa_aktif_va) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_masa_aktif_va', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_masa_aktif_va');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_masa_aktif_va');
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
	* delete Pengaturan Masa Aktif Vas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_masa_aktif_va_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_masa_aktif_va'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_masa_aktif_va'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Masa Aktif Vas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_masa_aktif_va_view');

		$this->data['pengaturan_masa_aktif_va'] = $this->model_pengaturan_masa_aktif_va->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Masa Aktif VA Detail');
		$this->render('backend/standart/administrator/pengaturan_masa_aktif_va/pengaturan_masa_aktif_va_view', $this->data);
	}
	
	/**
	* delete Pengaturan Masa Aktif Vas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_masa_aktif_va = $this->model_pengaturan_masa_aktif_va->find($id);

		
		
		return $this->model_pengaturan_masa_aktif_va->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_masa_aktif_va_export');

		$this->model_pengaturan_masa_aktif_va->export('pengaturan_masa_aktif_va', 'pengaturan_masa_aktif_va');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_masa_aktif_va_export');

		$this->model_pengaturan_masa_aktif_va->pdf('pengaturan_masa_aktif_va', 'pengaturan_masa_aktif_va');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_masa_aktif_va_export');

		$table = $title = 'pengaturan_masa_aktif_va';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_masa_aktif_va->find($id);
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


/* End of file pengaturan_masa_aktif_va.php */
/* Location: ./application/controllers/administrator/Pengaturan Masa Aktif Va.php */