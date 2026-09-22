<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Whitelist Ssid Masjid Controller
*| --------------------------------------------------------------------------
*| Pengaturan Whitelist Ssid Masjid site
*|
*/
class Pengaturan_whitelist_ssid_masjid extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_whitelist_ssid_masjid');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Whitelist Ssid Masjids
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_whitelist_ssid_masjids'] = $this->model_pengaturan_whitelist_ssid_masjid->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_whitelist_ssid_masjid_counts'] = $this->model_pengaturan_whitelist_ssid_masjid->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_whitelist_ssid_masjid/index/',
			'total_rows'   => $this->model_pengaturan_whitelist_ssid_masjid->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Whitelist Ssid Masjid List');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid_masjid/pengaturan_whitelist_ssid_masjid_list', $this->data);
	}
	
	/**
	* Add new pengaturan_whitelist_ssid_masjids
	*
	*/
	public function add()
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_add');

		$this->template->title('Pengaturan Whitelist Ssid Masjid New');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid_masjid/pengaturan_whitelist_ssid_masjid_add', $this->data);
	}

	/**
	* Add New Pengaturan Whitelist Ssid Masjids
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pengaturan_whitelist_ssid_masjid_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_ssid', 'SSID', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ssid' => $this->input->post('nama_ssid'),
			];

			
			$save_pengaturan_whitelist_ssid_masjid = $this->model_pengaturan_whitelist_ssid_masjid->store($save_data);
            

			if ($save_pengaturan_whitelist_ssid_masjid) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pengaturan_whitelist_ssid_masjid;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pengaturan_whitelist_ssid_masjid/edit/' . $save_pengaturan_whitelist_ssid_masjid, 'Edit Pengaturan Whitelist Ssid Masjid'),
						anchor('administrator/pengaturan_whitelist_ssid_masjid', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pengaturan_whitelist_ssid_masjid/edit/' . $save_pengaturan_whitelist_ssid_masjid, 'Edit Pengaturan Whitelist Ssid Masjid')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid_masjid');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid_masjid');
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
	* Update view Pengaturan Whitelist Ssid Masjids
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_update');

		$this->data['pengaturan_whitelist_ssid_masjid'] = $this->model_pengaturan_whitelist_ssid_masjid->find($id);

		$this->template->title('Pengaturan Whitelist Ssid Masjid Update');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid_masjid/pengaturan_whitelist_ssid_masjid_update', $this->data);
	}

	/**
	* Update Pengaturan Whitelist Ssid Masjids
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_whitelist_ssid_masjid_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_ssid', 'SSID', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ssid' => $this->input->post('nama_ssid'),
				'created_at' => $this->input->post('created_at'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_pengaturan_whitelist_ssid_masjid = $this->model_pengaturan_whitelist_ssid_masjid->change($id, $save_data);

			if ($save_pengaturan_whitelist_ssid_masjid) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_whitelist_ssid_masjid', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid_masjid');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_whitelist_ssid_masjid');
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
	* delete Pengaturan Whitelist Ssid Masjids
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_whitelist_ssid_masjid'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_whitelist_ssid_masjid'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Whitelist Ssid Masjids
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_view');

		$this->data['pengaturan_whitelist_ssid_masjid'] = $this->model_pengaturan_whitelist_ssid_masjid->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Whitelist Ssid Masjid Detail');
		$this->render('backend/standart/administrator/pengaturan_whitelist_ssid_masjid/pengaturan_whitelist_ssid_masjid_view', $this->data);
	}
	
	/**
	* delete Pengaturan Whitelist Ssid Masjids
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_whitelist_ssid_masjid = $this->model_pengaturan_whitelist_ssid_masjid->find($id);

		
		
		return $this->model_pengaturan_whitelist_ssid_masjid->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_export');

		$this->model_pengaturan_whitelist_ssid_masjid->export('pengaturan_whitelist_ssid_masjid', 'pengaturan_whitelist_ssid_masjid');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_export');

		$this->model_pengaturan_whitelist_ssid_masjid->pdf('pengaturan_whitelist_ssid_masjid', 'pengaturan_whitelist_ssid_masjid');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_whitelist_ssid_masjid_export');

		$table = $title = 'pengaturan_whitelist_ssid_masjid';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_whitelist_ssid_masjid->find($id);
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


/* End of file pengaturan_whitelist_ssid_masjid.php */
/* Location: ./application/controllers/administrator/Pengaturan Whitelist Ssid Masjid.php */