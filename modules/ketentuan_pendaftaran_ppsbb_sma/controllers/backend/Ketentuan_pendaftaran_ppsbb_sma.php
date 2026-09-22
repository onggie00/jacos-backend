<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ketentuan Pendaftaran Ppsbb Sma Controller
*| --------------------------------------------------------------------------
*| Ketentuan Pendaftaran Ppsbb Sma site
*|
*/
class Ketentuan_pendaftaran_ppsbb_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ketentuan_pendaftaran_ppsbb_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ketentuan Pendaftaran Ppsbb Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ketentuan_pendaftaran_ppsbb_smas'] = $this->model_ketentuan_pendaftaran_ppsbb_sma->get($filter, $field, $this->limit_page, $offset);
		$this->data['ketentuan_pendaftaran_ppsbb_sma_counts'] = $this->model_ketentuan_pendaftaran_ppsbb_sma->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ketentuan_pendaftaran_ppsbb_sma/index/',
			'total_rows'   => $this->model_ketentuan_pendaftaran_ppsbb_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ketentuan Pendaftaran PPSBB SMA List');
		$this->render('backend/standart/administrator/ketentuan_pendaftaran_ppsbb_sma/ketentuan_pendaftaran_ppsbb_sma_list', $this->data);
	}
	
	
		/**
	* Update view Ketentuan Pendaftaran Ppsbb Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_update');

		$this->data['ketentuan_pendaftaran_ppsbb_sma'] = $this->model_ketentuan_pendaftaran_ppsbb_sma->find($id);

		$this->template->title('Ketentuan Pendaftaran PPSBB SMA Update');
		$this->render('backend/standart/administrator/ketentuan_pendaftaran_ppsbb_sma/ketentuan_pendaftaran_ppsbb_sma_update', $this->data);
	}

	/**
	* Update Ketentuan Pendaftaran Ppsbb Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sub_judul', 'Sub Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('ketentuan_pendaftaran', 'Deskripsi', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'sub_judul' => $this->input->post('sub_judul'),
				'ketentuan_pendaftaran' => $this->input->post('ketentuan_pendaftaran'),
			];

			
			$save_ketentuan_pendaftaran_ppsbb_sma = $this->model_ketentuan_pendaftaran_ppsbb_sma->change($id, $save_data);

			if ($save_ketentuan_pendaftaran_ppsbb_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ketentuan_pendaftaran_ppsbb_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ketentuan_pendaftaran_ppsbb_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ketentuan_pendaftaran_ppsbb_sma');
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
	* delete Ketentuan Pendaftaran Ppsbb Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_delete');

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
            set_message(cclang('has_been_deleted', 'ketentuan_pendaftaran_ppsbb_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'ketentuan_pendaftaran_ppsbb_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ketentuan Pendaftaran Ppsbb Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_view');

		$this->data['ketentuan_pendaftaran_ppsbb_sma'] = $this->model_ketentuan_pendaftaran_ppsbb_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ketentuan Pendaftaran PPSBB SMA Detail');
		$this->render('backend/standart/administrator/ketentuan_pendaftaran_ppsbb_sma/ketentuan_pendaftaran_ppsbb_sma_view', $this->data);
	}
	
	/**
	* delete Ketentuan Pendaftaran Ppsbb Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ketentuan_pendaftaran_ppsbb_sma = $this->model_ketentuan_pendaftaran_ppsbb_sma->find($id);

		
		
		return $this->model_ketentuan_pendaftaran_ppsbb_sma->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_export');

		$this->model_ketentuan_pendaftaran_ppsbb_sma->export('ketentuan_pendaftaran_ppsbb_sma', 'ketentuan_pendaftaran_ppsbb_sma');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_export');

		$this->model_ketentuan_pendaftaran_ppsbb_sma->pdf('ketentuan_pendaftaran_ppsbb_sma', 'ketentuan_pendaftaran_ppsbb_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ketentuan_pendaftaran_ppsbb_sma_export');

		$table = $title = 'ketentuan_pendaftaran_ppsbb_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ketentuan_pendaftaran_ppsbb_sma->find($id);
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


/* End of file ketentuan_pendaftaran_ppsbb_sma.php */
/* Location: ./application/controllers/administrator/Ketentuan Pendaftaran Ppsbb Sma.php */