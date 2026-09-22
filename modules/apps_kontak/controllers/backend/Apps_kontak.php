<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kontak Apps Controller
*| --------------------------------------------------------------------------
*| Kontak Apps site
*|
*/
class Apps_kontak extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_kontak');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kontak Appss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_kontak_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_kontaks'] = $this->model_apps_kontak->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_kontak_counts'] = $this->model_apps_kontak->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_kontak/index/',
			'total_rows'   => $this->model_apps_kontak->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kontak Apps List');
		$this->render('backend/standart/administrator/apps_kontak/apps_kontak_list', $this->data);
	}
	
	/**
	* Add new apps_kontaks
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_kontak_add');

		$this->template->title('Kontak Apps New');
		$this->render('backend/standart/administrator/apps_kontak/apps_kontak_add', $this->data);
	}

	/**
	* Add New Kontak Appss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_kontak_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_kontak', 'Nama Kontak', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('tipe_kontak', 'Tipe Kontak', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kontak', 'Kontak', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
	
			$save_data = [
				'nama_kontak' => $this->input->post('nama_kontak'),
				'tipe_kontak' => $this->input->post('tipe_kontak'),
				'kontak' => $this->input->post('kontak'),
			];

			
			$save_apps_kontak = $this->model_apps_kontak->store($save_data);
            

			if ($save_apps_kontak) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_kontak;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_kontak/edit/' . $save_apps_kontak, 'Edit Kontak Apps'),
						anchor('administrator/apps_kontak', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_kontak/edit/' . $save_apps_kontak, 'Edit Kontak Apps')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_kontak');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_kontak');
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
	* Update view Kontak Appss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_kontak_update');

		$this->data['apps_kontak'] = $this->model_apps_kontak->find($id);

		$this->template->title('Kontak Apps Update');
		$this->render('backend/standart/administrator/apps_kontak/apps_kontak_update', $this->data);
	}

	/**
	* Update Kontak Appss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_kontak_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_kontak', 'Nama Kontak', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('tipe_kontak', 'Tipe Kontak', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kontak', 'Kontak', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
	
			$save_data = [
				'nama_kontak' => $this->input->post('nama_kontak'),
				'tipe_kontak' => $this->input->post('tipe_kontak'),
				'kontak' => $this->input->post('kontak'),
			];

			
			$save_apps_kontak = $this->model_apps_kontak->change($id, $save_data);

			if ($save_apps_kontak) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_kontak', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_kontak');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_kontak');
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
	* delete Kontak Appss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_kontak_delete');

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
            set_message(cclang('has_been_deleted', 'apps_kontak'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_kontak'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kontak Appss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_kontak_view');

		$this->data['apps_kontak'] = $this->model_apps_kontak->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kontak Apps Detail');
		$this->render('backend/standart/administrator/apps_kontak/apps_kontak_view', $this->data);
	}
	
	/**
	* delete Kontak Appss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_kontak = $this->model_apps_kontak->find($id);

		
		
		return $this->model_apps_kontak->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_kontak_export');

		$this->model_apps_kontak->export('apps_kontak', 'apps_kontak');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_kontak_export');

		$this->model_apps_kontak->pdf('apps_kontak', 'apps_kontak');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_kontak_export');

		$table = $title = 'apps_kontak';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_kontak->find($id);
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


/* End of file apps_kontak.php */
/* Location: ./application/controllers/administrator/Kontak Apps.php */