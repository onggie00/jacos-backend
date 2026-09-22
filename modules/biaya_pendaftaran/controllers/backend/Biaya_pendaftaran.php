<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Biaya Pendaftaran Controller
*| --------------------------------------------------------------------------
*| Biaya Pendaftaran site
*|
*/
class Biaya_pendaftaran extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_biaya_pendaftaran');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Biaya Pendaftarans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('biaya_pendaftaran_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['biaya_pendaftarans'] = $this->model_biaya_pendaftaran->get($filter, $field, $this->limit_page, $offset);
		$this->data['biaya_pendaftaran_counts'] = $this->model_biaya_pendaftaran->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/biaya_pendaftaran/index/',
			'total_rows'   => $this->model_biaya_pendaftaran->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Biaya Pendaftaran List');
		$this->render('backend/standart/administrator/biaya_pendaftaran/biaya_pendaftaran_list', $this->data);
	}
	
	
		/**
	* Update view Biaya Pendaftarans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('biaya_pendaftaran_update');

		$this->data['biaya_pendaftaran'] = $this->model_biaya_pendaftaran->find($id);

		$this->template->title('Biaya Pendaftaran Update');
		$this->render('backend/standart/administrator/biaya_pendaftaran/biaya_pendaftaran_update', $this->data);
	}

	/**
	* Update Biaya Pendaftarans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('biaya_pendaftaran_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nominal_pendaftaran', 'Nominal Pendaftaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('nominal_daftar_ulang', 'Nominal Daftar Ulang', 'trim|required|max_length[20]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nominal_pendaftaran' => $this->input->post('nominal_pendaftaran'),
				'nominal_daftar_ulang' => $this->input->post('nominal_daftar_ulang'),
			];

			
			$save_biaya_pendaftaran = $this->model_biaya_pendaftaran->change($id, $save_data);

			if ($save_biaya_pendaftaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/biaya_pendaftaran', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/biaya_pendaftaran');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/biaya_pendaftaran');
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
	* delete Biaya Pendaftarans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('biaya_pendaftaran_delete');

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
            set_message(cclang('has_been_deleted', 'biaya_pendaftaran'), 'success');
        } else {
            set_message(cclang('error_delete', 'biaya_pendaftaran'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Biaya Pendaftarans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('biaya_pendaftaran_view');

		$this->data['biaya_pendaftaran'] = $this->model_biaya_pendaftaran->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Biaya Pendaftaran Detail');
		$this->render('backend/standart/administrator/biaya_pendaftaran/biaya_pendaftaran_view', $this->data);
	}
	
	/**
	* delete Biaya Pendaftarans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$biaya_pendaftaran = $this->model_biaya_pendaftaran->find($id);

		
		
		return $this->model_biaya_pendaftaran->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('biaya_pendaftaran_export');

		$this->model_biaya_pendaftaran->export('biaya_pendaftaran', 'biaya_pendaftaran');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('biaya_pendaftaran_export');

		$this->model_biaya_pendaftaran->pdf('biaya_pendaftaran', 'biaya_pendaftaran');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('biaya_pendaftaran_export');

		$table = $title = 'biaya_pendaftaran';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_biaya_pendaftaran->find($id);
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


/* End of file biaya_pendaftaran.php */
/* Location: ./application/controllers/administrator/Biaya Pendaftaran.php */