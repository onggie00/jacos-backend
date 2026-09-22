<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jenis Prestasi Controller
*| --------------------------------------------------------------------------
*| Jenis Prestasi site
*|
*/
class Jenis_prestasi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jenis_prestasi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jenis Prestasis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jenis_prestasi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jenis_prestasis'] = $this->model_jenis_prestasi->get($filter, $field, $this->limit_page, $offset);
		$this->data['jenis_prestasi_counts'] = $this->model_jenis_prestasi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jenis_prestasi/index/',
			'total_rows'   => $this->model_jenis_prestasi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jenis Prestasi List');
		$this->render('backend/standart/administrator/jenis_prestasi/jenis_prestasi_list', $this->data);
	}
	
	/**
	* Add new jenis_prestasis
	*
	*/
	public function add()
	{
		$this->is_allowed('jenis_prestasi_add');

		$this->template->title('Jenis Prestasi New');
		$this->render('backend/standart/administrator/jenis_prestasi/jenis_prestasi_add', $this->data);
	}

	/**
	* Add New Jenis Prestasis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jenis_prestasi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenis_prestasi', 'Jenis Prestasi', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_prestasi' => $this->input->post('jenis_prestasi'),
			];

			
			$save_jenis_prestasi = $this->model_jenis_prestasi->store($save_data);
            

			if ($save_jenis_prestasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jenis_prestasi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jenis_prestasi/edit/' . $save_jenis_prestasi, 'Edit Jenis Prestasi'),
						anchor('administrator/jenis_prestasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jenis_prestasi/edit/' . $save_jenis_prestasi, 'Edit Jenis Prestasi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_prestasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_prestasi');
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
	* Update view Jenis Prestasis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jenis_prestasi_update');

		$this->data['jenis_prestasi'] = $this->model_jenis_prestasi->find($id);

		$this->template->title('Jenis Prestasi Update');
		$this->render('backend/standart/administrator/jenis_prestasi/jenis_prestasi_update', $this->data);
	}

	/**
	* Update Jenis Prestasis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jenis_prestasi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenis_prestasi', 'Jenis Prestasi', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_prestasi' => $this->input->post('jenis_prestasi'),
			];

			
			$save_jenis_prestasi = $this->model_jenis_prestasi->change($id, $save_data);

			if ($save_jenis_prestasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jenis_prestasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_prestasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_prestasi');
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
	* delete Jenis Prestasis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jenis_prestasi_delete');

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
            set_message(cclang('has_been_deleted', 'jenis_prestasi'), 'success');
        } else {
            set_message(cclang('error_delete', 'jenis_prestasi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jenis Prestasis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jenis_prestasi_view');

		$this->data['jenis_prestasi'] = $this->model_jenis_prestasi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jenis Prestasi Detail');
		$this->render('backend/standart/administrator/jenis_prestasi/jenis_prestasi_view', $this->data);
	}
	
	/**
	* delete Jenis Prestasis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jenis_prestasi = $this->model_jenis_prestasi->find($id);

		
		
		return $this->model_jenis_prestasi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jenis_prestasi_export');

		$this->model_jenis_prestasi->export('jenis_prestasi', 'jenis_prestasi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jenis_prestasi_export');

		$this->model_jenis_prestasi->pdf('jenis_prestasi', 'jenis_prestasi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jenis_prestasi_export');

		$table = $title = 'jenis_prestasi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jenis_prestasi->find($id);
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


/* End of file jenis_prestasi.php */
/* Location: ./application/controllers/administrator/Jenis Prestasi.php */