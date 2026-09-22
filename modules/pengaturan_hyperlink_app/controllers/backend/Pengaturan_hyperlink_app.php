<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Hyperlink App Controller
*| --------------------------------------------------------------------------
*| Pengaturan Hyperlink App site
*|
*/
class Pengaturan_hyperlink_app extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_hyperlink_app');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Hyperlink Apps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_hyperlink_app_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_hyperlink_apps'] = $this->model_pengaturan_hyperlink_app->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_hyperlink_app_counts'] = $this->model_pengaturan_hyperlink_app->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_hyperlink_app/index/',
			'total_rows'   => $this->model_pengaturan_hyperlink_app->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Hyperlink App List');
		$this->render('backend/standart/administrator/pengaturan_hyperlink_app/pengaturan_hyperlink_app_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Hyperlink Apps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_hyperlink_app_update');

		$this->data['pengaturan_hyperlink_app'] = $this->model_pengaturan_hyperlink_app->find($id);

		$this->template->title('Pengaturan Hyperlink App Update');
		$this->render('backend/standart/administrator/pengaturan_hyperlink_app/pengaturan_hyperlink_app_update', $this->data);
	}

	/**
	* Update Pengaturan Hyperlink Apps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_hyperlink_app_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('url', 'Url', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'url' => $this->input->post('url'),
			];

			
			$save_pengaturan_hyperlink_app = $this->model_pengaturan_hyperlink_app->change($id, $save_data);

			if ($save_pengaturan_hyperlink_app) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_hyperlink_app', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_hyperlink_app');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_hyperlink_app');
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
	* delete Pengaturan Hyperlink Apps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_hyperlink_app_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_hyperlink_app'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_hyperlink_app'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Hyperlink Apps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_hyperlink_app_view');

		$this->data['pengaturan_hyperlink_app'] = $this->model_pengaturan_hyperlink_app->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Hyperlink App Detail');
		$this->render('backend/standart/administrator/pengaturan_hyperlink_app/pengaturan_hyperlink_app_view', $this->data);
	}
	
	/**
	* delete Pengaturan Hyperlink Apps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_hyperlink_app = $this->model_pengaturan_hyperlink_app->find($id);

		
		
		return $this->model_pengaturan_hyperlink_app->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_hyperlink_app_export');

		$this->model_pengaturan_hyperlink_app->export('pengaturan_hyperlink_app', 'pengaturan_hyperlink_app');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_hyperlink_app_export');

		$this->model_pengaturan_hyperlink_app->pdf('pengaturan_hyperlink_app', 'pengaturan_hyperlink_app');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_hyperlink_app_export');

		$table = $title = 'pengaturan_hyperlink_app';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_hyperlink_app->find($id);
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


/* End of file pengaturan_hyperlink_app.php */
/* Location: ./application/controllers/administrator/Pengaturan Hyperlink App.php */