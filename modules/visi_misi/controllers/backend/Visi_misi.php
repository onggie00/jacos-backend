<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Visi Misi Controller
*| --------------------------------------------------------------------------
*| Visi Misi site
*|
*/
class Visi_misi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_visi_misi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Visi Misis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('visi_misi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['visi_misis'] = $this->model_visi_misi->get($filter, $field, $this->limit_page, $offset);
		$this->data['visi_misi_counts'] = $this->model_visi_misi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/visi_misi/index/',
			'total_rows'   => $this->model_visi_misi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Visi Misi List');
		$this->render('backend/standart/administrator/visi_misi/visi_misi_list', $this->data);
	}
	
	
		/**
	* Update view Visi Misis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('visi_misi_update');

		$this->data['visi_misi'] = $this->model_visi_misi->find($id);

		$this->template->title('Visi Misi Update');
		$this->render('backend/standart/administrator/visi_misi/visi_misi_update', $this->data);
	}

	/**
	* Update Visi Misis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('visi_misi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('isi', 'Isi', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'isi' => $this->input->post('isi'),
			];

			
			$save_visi_misi = $this->model_visi_misi->change($id, $save_data);

			if ($save_visi_misi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/visi_misi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/visi_misi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/visi_misi');
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
	* delete Visi Misis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('visi_misi_delete');

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
            set_message(cclang('has_been_deleted', 'visi_misi'), 'success');
        } else {
            set_message(cclang('error_delete', 'visi_misi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Visi Misis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('visi_misi_view');

		$this->data['visi_misi'] = $this->model_visi_misi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Visi Misi Detail');
		$this->render('backend/standart/administrator/visi_misi/visi_misi_view', $this->data);
	}
	
	/**
	* delete Visi Misis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$visi_misi = $this->model_visi_misi->find($id);

		
		
		return $this->model_visi_misi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('visi_misi_export');

		$this->model_visi_misi->export('visi_misi', 'visi_misi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('visi_misi_export');

		$this->model_visi_misi->pdf('visi_misi', 'visi_misi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('visi_misi_export');

		$table = $title = 'visi_misi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_visi_misi->find($id);
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


/* End of file visi_misi.php */
/* Location: ./application/controllers/administrator/Visi Misi.php */