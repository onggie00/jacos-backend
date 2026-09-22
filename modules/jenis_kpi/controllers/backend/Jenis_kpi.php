<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jenis Kpi Controller
*| --------------------------------------------------------------------------
*| Jenis Kpi site
*|
*/
class Jenis_kpi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jenis_kpi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jenis Kpis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jenis_kpi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jenis_kpis'] = $this->model_jenis_kpi->get($filter, $field, $this->limit_page, $offset);
		$this->data['jenis_kpi_counts'] = $this->model_jenis_kpi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jenis_kpi/index/',
			'total_rows'   => $this->model_jenis_kpi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jenis Kpi List');
		$this->render('backend/standart/administrator/jenis_kpi/jenis_kpi_list', $this->data);
	}
	
	/**
	* Add new jenis_kpis
	*
	*/
	public function add()
	{
		$this->is_allowed('jenis_kpi_add');

		$this->template->title('Jenis Kpi New');
		$this->render('backend/standart/administrator/jenis_kpi/jenis_kpi_add', $this->data);
	}

	/**
	* Add New Jenis Kpis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jenis_kpi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_jenis', 'Nama Jenis', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_jenis' => $this->input->post('nama_jenis'),
			];

			
			$save_jenis_kpi = $this->model_jenis_kpi->store($save_data);
            

			if ($save_jenis_kpi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jenis_kpi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jenis_kpi/edit/' . $save_jenis_kpi, 'Edit Jenis Kpi'),
						anchor('administrator/jenis_kpi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jenis_kpi/edit/' . $save_jenis_kpi, 'Edit Jenis Kpi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_kpi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_kpi');
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
	* Update view Jenis Kpis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jenis_kpi_update');

		$this->data['jenis_kpi'] = $this->model_jenis_kpi->find($id);

		$this->template->title('Jenis Kpi Update');
		$this->render('backend/standart/administrator/jenis_kpi/jenis_kpi_update', $this->data);
	}

	/**
	* Update Jenis Kpis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jenis_kpi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_jenis', 'Nama Jenis', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_jenis' => $this->input->post('nama_jenis'),
			];

			
			$save_jenis_kpi = $this->model_jenis_kpi->change($id, $save_data);

			if ($save_jenis_kpi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jenis_kpi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_kpi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_kpi');
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
	* delete Jenis Kpis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jenis_kpi_delete');

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
            set_message(cclang('has_been_deleted', 'jenis_kpi'), 'success');
        } else {
            set_message(cclang('error_delete', 'jenis_kpi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jenis Kpis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jenis_kpi_view');

		$this->data['jenis_kpi'] = $this->model_jenis_kpi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jenis Kpi Detail');
		$this->render('backend/standart/administrator/jenis_kpi/jenis_kpi_view', $this->data);
	}
	
	/**
	* delete Jenis Kpis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jenis_kpi = $this->model_jenis_kpi->find($id);

		
		
		return $this->model_jenis_kpi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jenis_kpi_export');

		$this->model_jenis_kpi->export('jenis_kpi', 'jenis_kpi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jenis_kpi_export');

		$this->model_jenis_kpi->pdf('jenis_kpi', 'jenis_kpi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jenis_kpi_export');

		$table = $title = 'jenis_kpi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jenis_kpi->find($id);
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


/* End of file jenis_kpi.php */
/* Location: ./application/controllers/administrator/Jenis Kpi.php */