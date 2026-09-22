<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tahun Ajaran Psb Controller
*| --------------------------------------------------------------------------
*| Tahun Ajaran Psb site
*|
*/
class Tahun_ajaran_psb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tahun_ajaran_psb');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tahun Ajaran Psbs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tahun_ajaran_psb_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tahun_ajaran_psbs'] = $this->model_tahun_ajaran_psb->get($filter, $field, $this->limit_page, $offset);
		$this->data['tahun_ajaran_psb_counts'] = $this->model_tahun_ajaran_psb->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tahun_ajaran_psb/index/',
			'total_rows'   => $this->model_tahun_ajaran_psb->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tahun Ajaran Psb List');
		$this->render('backend/standart/administrator/tahun_ajaran_psb/tahun_ajaran_psb_list', $this->data);
	}
	
	
		/**
	* Update view Tahun Ajaran Psbs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tahun_ajaran_psb_update');

		$this->data['tahun_ajaran_psb'] = $this->model_tahun_ajaran_psb->find($id);

		$this->template->title('Tahun Ajaran Psb Update');
		$this->render('backend/standart/administrator/tahun_ajaran_psb/tahun_ajaran_psb_update', $this->data);
	}

	/**
	* Update Tahun Ajaran Psbs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tahun_ajaran_psb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
			];

			
			$save_tahun_ajaran_psb = $this->model_tahun_ajaran_psb->change($id, $save_data);

			if ($save_tahun_ajaran_psb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tahun_ajaran_psb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tahun_ajaran_psb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tahun_ajaran_psb');
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
	* delete Tahun Ajaran Psbs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tahun_ajaran_psb_delete');

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
            set_message(cclang('has_been_deleted', 'tahun_ajaran_psb'), 'success');
        } else {
            set_message(cclang('error_delete', 'tahun_ajaran_psb'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tahun Ajaran Psbs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tahun_ajaran_psb_view');

		$this->data['tahun_ajaran_psb'] = $this->model_tahun_ajaran_psb->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tahun Ajaran Psb Detail');
		$this->render('backend/standart/administrator/tahun_ajaran_psb/tahun_ajaran_psb_view', $this->data);
	}
	
	/**
	* delete Tahun Ajaran Psbs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tahun_ajaran_psb = $this->model_tahun_ajaran_psb->find($id);

		
		
		return $this->model_tahun_ajaran_psb->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tahun_ajaran_psb_export');

		$this->model_tahun_ajaran_psb->export('tahun_ajaran_psb', 'tahun_ajaran_psb');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tahun_ajaran_psb_export');

		$this->model_tahun_ajaran_psb->pdf('tahun_ajaran_psb', 'tahun_ajaran_psb');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tahun_ajaran_psb_export');

		$table = $title = 'tahun_ajaran_psb';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tahun_ajaran_psb->find($id);
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


/* End of file tahun_ajaran_psb.php */
/* Location: ./application/controllers/administrator/Tahun Ajaran Psb.php */