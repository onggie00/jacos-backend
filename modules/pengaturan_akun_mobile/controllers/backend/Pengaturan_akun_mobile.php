<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Akun Mobile Controller
*| --------------------------------------------------------------------------
*| Pengaturan Akun Mobile site
*|
*/
class Pengaturan_akun_mobile extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_akun_mobile');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Akun Mobiles
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_akun_mobile_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_akun_mobiles'] = $this->model_pengaturan_akun_mobile->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_akun_mobile_counts'] = $this->model_pengaturan_akun_mobile->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_akun_mobile/index/',
			'total_rows'   => $this->model_pengaturan_akun_mobile->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Akun Mobile List');
		$this->render('backend/standart/administrator/pengaturan_akun_mobile/pengaturan_akun_mobile_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Akun Mobiles
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_akun_mobile_update');

		$this->data['pengaturan_akun_mobile'] = $this->model_pengaturan_akun_mobile->find($id);

		$this->template->title('Pengaturan Akun Mobile Update');
		$this->render('backend/standart/administrator/pengaturan_akun_mobile/pengaturan_akun_mobile_update', $this->data);
	}

	/**
	* Update Pengaturan Akun Mobiles
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_akun_mobile_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('secret_key', 'Secret Key', 'trim|required');
		$this->form_validation->set_rules('client_key', 'Client Key', 'trim|required');
		$this->form_validation->set_rules('value', 'Value', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'secret_key' => $this->input->post('secret_key'),
				'client_key' => $this->input->post('client_key'),
				'value' => $this->input->post('value'),
				'expired_date' => $this->input->post('expired_date'),
			];

			
			$save_pengaturan_akun_mobile = $this->model_pengaturan_akun_mobile->change($id, $save_data);

			if ($save_pengaturan_akun_mobile) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_akun_mobile', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_akun_mobile');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_akun_mobile');
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
	* delete Pengaturan Akun Mobiles
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_akun_mobile_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_akun_mobile'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_akun_mobile'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Akun Mobiles
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_akun_mobile_view');

		$this->data['pengaturan_akun_mobile'] = $this->model_pengaturan_akun_mobile->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Akun Mobile Detail');
		$this->render('backend/standart/administrator/pengaturan_akun_mobile/pengaturan_akun_mobile_view', $this->data);
	}
	
	/**
	* delete Pengaturan Akun Mobiles
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_akun_mobile = $this->model_pengaturan_akun_mobile->find($id);

		
		
		return $this->model_pengaturan_akun_mobile->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_akun_mobile_export');

		$this->model_pengaturan_akun_mobile->export('pengaturan_akun_mobile', 'pengaturan_akun_mobile');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_akun_mobile_export');

		$this->model_pengaturan_akun_mobile->pdf('pengaturan_akun_mobile', 'pengaturan_akun_mobile');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_akun_mobile_export');

		$table = $title = 'pengaturan_akun_mobile';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_akun_mobile->find($id);
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


/* End of file pengaturan_akun_mobile.php */
/* Location: ./application/controllers/administrator/Pengaturan Akun Mobile.php */