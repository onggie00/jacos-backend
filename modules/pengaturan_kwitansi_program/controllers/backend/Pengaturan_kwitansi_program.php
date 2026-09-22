<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Kwitansi Program Controller
*| --------------------------------------------------------------------------
*| Pengaturan Kwitansi Program site
*|
*/
class Pengaturan_kwitansi_program extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_kwitansi_program');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Kwitansi Programs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_kwitansi_program_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_kwitansi_programs'] = $this->model_pengaturan_kwitansi_program->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_kwitansi_program_counts'] = $this->model_pengaturan_kwitansi_program->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_kwitansi_program/index/',
			'total_rows'   => $this->model_pengaturan_kwitansi_program->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Kwitansi Program List');
		$this->render('backend/standart/administrator/pengaturan_kwitansi_program/pengaturan_kwitansi_program_list', $this->data);
	}
	
	
		/**
	* Update view Pengaturan Kwitansi Programs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_kwitansi_program_update');

		$this->data['pengaturan_kwitansi_program'] = $this->model_pengaturan_kwitansi_program->find($id);

		$this->template->title('Pengaturan Kwitansi Program Update');
		$this->render('backend/standart/administrator/pengaturan_kwitansi_program/pengaturan_kwitansi_program_update', $this->data);
	}

	/**
	* Update Pengaturan Kwitansi Programs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_kwitansi_program_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('kepala_sekolah_nama', 'Kepala Sekolah (Nama)', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kepala_sekolah_npp', 'Kepala Sekolah (NPP)', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('kepala_tu_nama', 'Kepala TU (Nama)', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kepala_tu_npp', 'Kepala TU (NPP)', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('kepala_sekretariat_nama', 'Kepala Sekretariat (Nama)', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kepala_sekretariat_npp', 'Kepala Sekretariat (NPP)', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'kepala_sekolah_nama' => $this->input->post('kepala_sekolah_nama'),
				'kepala_sekolah_npp' => $this->input->post('kepala_sekolah_npp'),
				'kepala_tu_nama' => $this->input->post('kepala_tu_nama'),
				'kepala_tu_npp' => $this->input->post('kepala_tu_npp'),
				'kepala_sekretariat_nama' => $this->input->post('kepala_sekretariat_nama'),
				'kepala_sekretariat_npp' => $this->input->post('kepala_sekretariat_npp'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_pengaturan_kwitansi_program = $this->model_pengaturan_kwitansi_program->change($id, $save_data);

			if ($save_pengaturan_kwitansi_program) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_kwitansi_program', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_kwitansi_program');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_kwitansi_program');
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
	* delete Pengaturan Kwitansi Programs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_kwitansi_program_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_kwitansi_program'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_kwitansi_program'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Kwitansi Programs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_kwitansi_program_view');

		$this->data['pengaturan_kwitansi_program'] = $this->model_pengaturan_kwitansi_program->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Kwitansi Program Detail');
		$this->render('backend/standart/administrator/pengaturan_kwitansi_program/pengaturan_kwitansi_program_view', $this->data);
	}
	
	/**
	* delete Pengaturan Kwitansi Programs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_kwitansi_program = $this->model_pengaturan_kwitansi_program->find($id);

		
		
		return $this->model_pengaturan_kwitansi_program->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_kwitansi_program_export');

		$this->model_pengaturan_kwitansi_program->export('pengaturan_kwitansi_program', 'pengaturan_kwitansi_program');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_kwitansi_program_export');

		$this->model_pengaturan_kwitansi_program->pdf('pengaturan_kwitansi_program', 'pengaturan_kwitansi_program');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_kwitansi_program_export');

		$table = $title = 'pengaturan_kwitansi_program';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_kwitansi_program->find($id);
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


/* End of file pengaturan_kwitansi_program.php */
/* Location: ./application/controllers/administrator/Pengaturan Kwitansi Program.php */