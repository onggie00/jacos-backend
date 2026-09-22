<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Whitelist Ssid Controller
*| --------------------------------------------------------------------------
*| Pengaturan Whitelist Ssid site
*|
*/
class Pengaturan_whitelist_ssid extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_whitelist_ssid');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Whitelist Ssids
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_whitelist_ssids'] = $this->model_pengaturan_whitelist_ssid->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_whitelist_ssid_counts'] = $this->model_pengaturan_whitelist_ssid->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_whitelist_ssid/index/',
			'total_rows'   => $this->model_pengaturan_whitelist_ssid->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan SSID List');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid/pengaturan_whitelist_ssid_list', $this->data);
	}
	
	/**
	* Add new pengaturan_whitelist_ssids
	*
	*/
	public function add()
	{
		$this->is_allowed('pengaturan_whitelist_ssid_add');

		$this->template->title('Pengaturan SSID New');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid/pengaturan_whitelist_ssid_add', $this->data);
	}

	/**
	* Add New Pengaturan Whitelist Ssids
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pengaturan_whitelist_ssid_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_ssid', 'Nama SSID', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ssid' => $this->input->post('nama_ssid'),
			];

			
			$save_pengaturan_whitelist_ssid = $this->model_pengaturan_whitelist_ssid->store($save_data);
            

			if ($save_pengaturan_whitelist_ssid) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pengaturan_whitelist_ssid;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pengaturan_whitelist_ssid/edit/' . $save_pengaturan_whitelist_ssid, 'Edit Pengaturan Whitelist Ssid'),
						anchor('administrator/pengaturan_whitelist_ssid', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pengaturan_whitelist_ssid/edit/' . $save_pengaturan_whitelist_ssid, 'Edit Pengaturan Whitelist Ssid')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid');
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
	* Update view Pengaturan Whitelist Ssids
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_update');

		$this->data['pengaturan_whitelist_ssid'] = $this->model_pengaturan_whitelist_ssid->find($id);

		$this->template->title('Pengaturan SSID Update');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid/pengaturan_whitelist_ssid_update', $this->data);
	}

	/**
	* Update Pengaturan Whitelist Ssids
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_whitelist_ssid_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_ssid', 'Nama SSID', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ssid' => $this->input->post('nama_ssid'),
			];

			
			$save_pengaturan_whitelist_ssid = $this->model_pengaturan_whitelist_ssid->change($id, $save_data);

			if ($save_pengaturan_whitelist_ssid) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_whitelist_ssid', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid');
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
	* delete Pengaturan Whitelist Ssids
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_whitelist_ssid'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_whitelist_ssid'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Whitelist Ssids
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_view');

		$this->data['pengaturan_whitelist_ssid'] = $this->model_pengaturan_whitelist_ssid->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan SSID Detail');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid/pengaturan_whitelist_ssid_view', $this->data);
	}
	
	/**
	* delete Pengaturan Whitelist Ssids
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_whitelist_ssid = $this->model_pengaturan_whitelist_ssid->find($id);

		
		
		return $this->model_pengaturan_whitelist_ssid->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_whitelist_ssid_export');

		$this->model_pengaturan_whitelist_ssid->export('pengaturan_whitelist_ssid', 'pengaturan_whitelist_ssid');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_whitelist_ssid_export');

		$this->model_pengaturan_whitelist_ssid->pdf('pengaturan_whitelist_ssid', 'pengaturan_whitelist_ssid');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_export');

		$table = $title = 'pengaturan_whitelist_ssid';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_whitelist_ssid->find($id);
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


/* End of file pengaturan_whitelist_ssid.php */
/* Location: ./application/controllers/administrator/Pengaturan Whitelist Ssid.php */