<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jenis Kpi Pegawai Controller
*| --------------------------------------------------------------------------
*| Jenis Kpi Pegawai site
*|
*/
class Jenis_kpi_pegawai extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jenis_kpi_pegawai');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jenis Kpi Pegawais
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jenis_kpi_pegawai_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jenis_kpi_pegawais'] = $this->model_jenis_kpi_pegawai->get($filter, $field, $this->limit_page, $offset);
		$this->data['jenis_kpi_pegawai_counts'] = $this->model_jenis_kpi_pegawai->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jenis_kpi_pegawai/index/',
			'total_rows'   => $this->model_jenis_kpi_pegawai->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jenis Kpi Pegawai List');
		$this->render('backend/standart/administrator/jenis_kpi_pegawai/jenis_kpi_pegawai_list', $this->data);
	}
	
	/**
	* Add new jenis_kpi_pegawais
	*
	*/
	public function add()
	{
		$this->is_allowed('jenis_kpi_pegawai_add');

		$this->template->title('Jenis Kpi Pegawai New');
		$this->render('backend/standart/administrator/jenis_kpi_pegawai/jenis_kpi_pegawai_add', $this->data);
	}

	/**
	* Add New Jenis Kpi Pegawais
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jenis_kpi_pegawai_add', false)) {
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

			
			$save_jenis_kpi_pegawai = $this->model_jenis_kpi_pegawai->store($save_data);
            

			if ($save_jenis_kpi_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jenis_kpi_pegawai;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jenis_kpi_pegawai/edit/' . $save_jenis_kpi_pegawai, 'Edit Jenis Kpi Pegawai'),
						anchor('administrator/jenis_kpi_pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jenis_kpi_pegawai/edit/' . $save_jenis_kpi_pegawai, 'Edit Jenis Kpi Pegawai')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_kpi_pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_kpi_pegawai');
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
	* Update view Jenis Kpi Pegawais
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jenis_kpi_pegawai_update');

		$this->data['jenis_kpi_pegawai'] = $this->model_jenis_kpi_pegawai->find($id);

		$this->template->title('Jenis Kpi Pegawai Update');
		$this->render('backend/standart/administrator/jenis_kpi_pegawai/jenis_kpi_pegawai_update', $this->data);
	}

	/**
	* Update Jenis Kpi Pegawais
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jenis_kpi_pegawai_update', false)) {
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

			
			$save_jenis_kpi_pegawai = $this->model_jenis_kpi_pegawai->change($id, $save_data);

			if ($save_jenis_kpi_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jenis_kpi_pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_kpi_pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_kpi_pegawai');
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
	* delete Jenis Kpi Pegawais
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jenis_kpi_pegawai_delete');

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
            set_message(cclang('has_been_deleted', 'jenis_kpi_pegawai'), 'success');
        } else {
            set_message(cclang('error_delete', 'jenis_kpi_pegawai'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jenis Kpi Pegawais
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jenis_kpi_pegawai_view');

		$this->data['jenis_kpi_pegawai'] = $this->model_jenis_kpi_pegawai->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jenis Kpi Pegawai Detail');
		$this->render('backend/standart/administrator/jenis_kpi_pegawai/jenis_kpi_pegawai_view', $this->data);
	}
	
	/**
	* delete Jenis Kpi Pegawais
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jenis_kpi_pegawai = $this->model_jenis_kpi_pegawai->find($id);

		
		
		return $this->model_jenis_kpi_pegawai->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jenis_kpi_pegawai_export');

		$this->model_jenis_kpi_pegawai->export('jenis_kpi_pegawai', 'jenis_kpi_pegawai');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jenis_kpi_pegawai_export');

		$this->model_jenis_kpi_pegawai->pdf('jenis_kpi_pegawai', 'jenis_kpi_pegawai');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jenis_kpi_pegawai_export');

		$table = $title = 'jenis_kpi_pegawai';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jenis_kpi_pegawai->find($id);
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


/* End of file jenis_kpi_pegawai.php */
/* Location: ./application/controllers/administrator/Jenis Kpi Pegawai.php */