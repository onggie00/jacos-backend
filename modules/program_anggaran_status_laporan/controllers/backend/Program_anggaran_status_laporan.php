<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Program Anggaran Status Laporan Controller
*| --------------------------------------------------------------------------
*| Program Anggaran Status Laporan site
*|
*/
class Program_anggaran_status_laporan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_program_anggaran_status_laporan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Program Anggaran Status Laporans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('program_anggaran_status_laporan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['program_anggaran_status_laporans'] = $this->model_program_anggaran_status_laporan->get($filter, $field, $this->limit_page, $offset);
		$this->data['program_anggaran_status_laporan_counts'] = $this->model_program_anggaran_status_laporan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/program_anggaran_status_laporan/index/',
			'total_rows'   => $this->model_program_anggaran_status_laporan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Status Laporan List');
		$this->render('backend/standart/administrator/program_anggaran_status_laporan/program_anggaran_status_laporan_list', $this->data);
	}
	
	/**
	* Add new program_anggaran_status_laporans
	*
	*/
	public function add()
	{
		$this->is_allowed('program_anggaran_status_laporan_add');

		$this->template->title('Status Laporan New');
		$this->render('backend/standart/administrator/program_anggaran_status_laporan/program_anggaran_status_laporan_add', $this->data);
	}

	/**
	* Add New Program Anggaran Status Laporans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('program_anggaran_status_laporan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'status' => $this->input->post('status'),
			];

			
			$save_program_anggaran_status_laporan = $this->model_program_anggaran_status_laporan->store($save_data);
            

			if ($save_program_anggaran_status_laporan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_program_anggaran_status_laporan;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/program_anggaran_status_laporan/edit/' . $save_program_anggaran_status_laporan, 'Edit Program Anggaran Status Laporan'),
						anchor('administrator/program_anggaran_status_laporan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/program_anggaran_status_laporan/edit/' . $save_program_anggaran_status_laporan, 'Edit Program Anggaran Status Laporan')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/program_anggaran_status_laporan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/program_anggaran_status_laporan');
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
	* Update view Program Anggaran Status Laporans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('program_anggaran_status_laporan_update');

		$this->data['program_anggaran_status_laporan'] = $this->model_program_anggaran_status_laporan->find($id);

		$this->template->title('Status Laporan Update');
		$this->render('backend/standart/administrator/program_anggaran_status_laporan/program_anggaran_status_laporan_update', $this->data);
	}

	/**
	* Update Program Anggaran Status Laporans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('program_anggaran_status_laporan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'status' => $this->input->post('status'),
			];

			
			$save_program_anggaran_status_laporan = $this->model_program_anggaran_status_laporan->change($id, $save_data);

			if ($save_program_anggaran_status_laporan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/program_anggaran_status_laporan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/program_anggaran_status_laporan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/program_anggaran_status_laporan');
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
	* delete Program Anggaran Status Laporans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('program_anggaran_status_laporan_delete');

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
            set_message(cclang('has_been_deleted', 'program_anggaran_status_laporan'), 'success');
        } else {
            set_message(cclang('error_delete', 'program_anggaran_status_laporan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Program Anggaran Status Laporans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('program_anggaran_status_laporan_view');

		$this->data['program_anggaran_status_laporan'] = $this->model_program_anggaran_status_laporan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Status Laporan Detail');
		$this->render('backend/standart/administrator/program_anggaran_status_laporan/program_anggaran_status_laporan_view', $this->data);
	}
	
	/**
	* delete Program Anggaran Status Laporans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$program_anggaran_status_laporan = $this->model_program_anggaran_status_laporan->find($id);

		
		
		return $this->model_program_anggaran_status_laporan->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('program_anggaran_status_laporan_export');

		$this->model_program_anggaran_status_laporan->export('program_anggaran_status_laporan', 'program_anggaran_status_laporan');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('program_anggaran_status_laporan_export');

		$this->model_program_anggaran_status_laporan->pdf('program_anggaran_status_laporan', 'program_anggaran_status_laporan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('program_anggaran_status_laporan_export');

		$table = $title = 'program_anggaran_status_laporan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_program_anggaran_status_laporan->find($id);
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


/* End of file program_anggaran_status_laporan.php */
/* Location: ./application/controllers/administrator/Program Anggaran Status Laporan.php */