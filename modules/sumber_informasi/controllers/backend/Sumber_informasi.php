<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Sumber Informasi Controller
*| --------------------------------------------------------------------------
*| Sumber Informasi site
*|
*/
class Sumber_informasi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_sumber_informasi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Sumber Informasis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('sumber_informasi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['sumber_informasis'] = $this->model_sumber_informasi->get($filter, $field, $this->limit_page, $offset);
		$this->data['sumber_informasi_counts'] = $this->model_sumber_informasi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/sumber_informasi/index/',
			'total_rows'   => $this->model_sumber_informasi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Sumber Informasi Data List');
		$this->render('backend/standart/administrator/sumber_informasi/sumber_informasi_list', $this->data);
	}
	
	/**
	* Add new sumber_informasis
	*
	*/
	public function add()
	{
		$this->is_allowed('sumber_informasi_add');

		$this->template->title('Sumber Informasi Data New');
		$this->render('backend/standart/administrator/sumber_informasi/sumber_informasi_add', $this->data);
	}

	/**
	* Add New Sumber Informasis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('sumber_informasi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'sumber_informasi' => $this->input->post('sumber_informasi'),
			];

			
			$save_sumber_informasi = $this->model_sumber_informasi->store($save_data);
            

			if ($save_sumber_informasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_sumber_informasi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/sumber_informasi/edit/' . $save_sumber_informasi, 'Edit Sumber Informasi'),
						anchor('administrator/sumber_informasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/sumber_informasi/edit/' . $save_sumber_informasi, 'Edit Sumber Informasi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sumber_informasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sumber_informasi');
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
	* Update view Sumber Informasis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('sumber_informasi_update');

		$this->data['sumber_informasi'] = $this->model_sumber_informasi->find($id);

		$this->template->title('Sumber Informasi Data Update');
		$this->render('backend/standart/administrator/sumber_informasi/sumber_informasi_update', $this->data);
	}

	/**
	* Update Sumber Informasis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('sumber_informasi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'sumber_informasi' => $this->input->post('sumber_informasi'),
			];

			
			$save_sumber_informasi = $this->model_sumber_informasi->change($id, $save_data);

			if ($save_sumber_informasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/sumber_informasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sumber_informasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sumber_informasi');
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
	* delete Sumber Informasis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('sumber_informasi_delete');

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
            set_message(cclang('has_been_deleted', 'sumber_informasi'), 'success');
        } else {
            set_message(cclang('error_delete', 'sumber_informasi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Sumber Informasis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('sumber_informasi_view');

		$this->data['sumber_informasi'] = $this->model_sumber_informasi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Sumber Informasi Data Detail');
		$this->render('backend/standart/administrator/sumber_informasi/sumber_informasi_view', $this->data);
	}
	
	/**
	* delete Sumber Informasis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$sumber_informasi = $this->model_sumber_informasi->find($id);

		
		
		return $this->model_sumber_informasi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('sumber_informasi_export');

		$this->model_sumber_informasi->export('sumber_informasi', 'sumber_informasi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('sumber_informasi_export');

		$this->model_sumber_informasi->pdf('sumber_informasi', 'sumber_informasi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('sumber_informasi_export');

		$table = $title = 'sumber_informasi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_sumber_informasi->find($id);
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


/* End of file sumber_informasi.php */
/* Location: ./application/controllers/administrator/Sumber Informasi.php */