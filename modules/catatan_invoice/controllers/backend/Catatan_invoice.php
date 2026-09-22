<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Catatan Invoice Controller
*| --------------------------------------------------------------------------
*| Catatan Invoice site
*|
*/
class Catatan_invoice extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_catatan_invoice');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Catatan Invoices
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('catatan_invoice_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['catatan_invoices'] = $this->model_catatan_invoice->get($filter, $field, $this->limit_page, $offset);
		$this->data['catatan_invoice_counts'] = $this->model_catatan_invoice->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/catatan_invoice/index/',
			'total_rows'   => $this->model_catatan_invoice->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Catatan Invoice List');
		$this->render('backend/standart/administrator/catatan_invoice/catatan_invoice_list', $this->data);
	}
	
	
		/**
	* Update view Catatan Invoices
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('catatan_invoice_update');

		$this->data['catatan_invoice'] = $this->model_catatan_invoice->find($id);

		$this->template->title('Catatan Invoice Update');
		$this->render('backend/standart/administrator/catatan_invoice/catatan_invoice_update', $this->data);
	}

	/**
	* Update Catatan Invoices
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('catatan_invoice_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('catatan_mohon_dibaca', 'Catatan Mohon Dibaca', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'catatan_mohon_dibaca' => $this->input->post('catatan_mohon_dibaca'),
			];

			
			$save_catatan_invoice = $this->model_catatan_invoice->change($id, $save_data);

			if ($save_catatan_invoice) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/catatan_invoice', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/catatan_invoice');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/catatan_invoice');
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
	* delete Catatan Invoices
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('catatan_invoice_delete');

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
            set_message(cclang('has_been_deleted', 'catatan_invoice'), 'success');
        } else {
            set_message(cclang('error_delete', 'catatan_invoice'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Catatan Invoices
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('catatan_invoice_view');

		$this->data['catatan_invoice'] = $this->model_catatan_invoice->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Catatan Invoice Detail');
		$this->render('backend/standart/administrator/catatan_invoice/catatan_invoice_view', $this->data);
	}
	
	/**
	* delete Catatan Invoices
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$catatan_invoice = $this->model_catatan_invoice->find($id);

		
		
		return $this->model_catatan_invoice->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('catatan_invoice_export');

		$this->model_catatan_invoice->export('catatan_invoice', 'catatan_invoice');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('catatan_invoice_export');

		$this->model_catatan_invoice->pdf('catatan_invoice', 'catatan_invoice');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('catatan_invoice_export');

		$table = $title = 'catatan_invoice';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_catatan_invoice->find($id);
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


/* End of file catatan_invoice.php */
/* Location: ./application/controllers/administrator/Catatan Invoice.php */