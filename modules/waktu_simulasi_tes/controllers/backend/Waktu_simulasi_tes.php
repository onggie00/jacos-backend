<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Waktu Simulasi Tes Controller
*| --------------------------------------------------------------------------
*| Waktu Simulasi Tes site
*|
*/
class Waktu_simulasi_tes extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_waktu_simulasi_tes');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Waktu Simulasi Tess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('waktu_simulasi_tes_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['waktu_simulasi_tess'] = $this->model_waktu_simulasi_tes->get($filter, $field, $this->limit_page, $offset);
		$this->data['waktu_simulasi_tes_counts'] = $this->model_waktu_simulasi_tes->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/waktu_simulasi_tes/index/',
			'total_rows'   => $this->model_waktu_simulasi_tes->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Waktu Simulasi Tes List');
		$this->render('backend/standart/administrator/waktu_simulasi_tes/waktu_simulasi_tes_list', $this->data);
	}
	
	/**
	* Add new waktu_simulasi_tess
	*
	*/
	public function add()
	{
		$this->is_allowed('waktu_simulasi_tes_add');

		$this->template->title('Waktu Simulasi Tes New');
		$this->render('backend/standart/administrator/waktu_simulasi_tes/waktu_simulasi_tes_add', $this->data);
	}

	/**
	* Add New Waktu Simulasi Tess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('waktu_simulasi_tes_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('tgl_ujian', 'Tanggal Ujian', 'trim|required');
		$this->form_validation->set_rules('waktu_mulai', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('waktu_selesai', 'Waktu Selesai', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('simulasi', 'Keterangan Simulasi', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'tgl_ujian' => $this->input->post('tgl_ujian'),
				'waktu_mulai' => $this->input->post('waktu_mulai'),
				'waktu_selesai' => $this->input->post('waktu_selesai'),
				'jenjang' => $this->input->post('jenjang'),
				'simulasi' => $this->input->post('simulasi'),
			];

			
			$save_waktu_simulasi_tes = $this->model_waktu_simulasi_tes->store($save_data);
            

			if ($save_waktu_simulasi_tes) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_waktu_simulasi_tes;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/waktu_simulasi_tes/edit/' . $save_waktu_simulasi_tes, 'Edit Waktu Simulasi Tes'),
						anchor('administrator/waktu_simulasi_tes', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/waktu_simulasi_tes/edit/' . $save_waktu_simulasi_tes, 'Edit Waktu Simulasi Tes')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/waktu_simulasi_tes');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/waktu_simulasi_tes');
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
	* Update view Waktu Simulasi Tess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('waktu_simulasi_tes_update');

		$this->data['waktu_simulasi_tes'] = $this->model_waktu_simulasi_tes->find($id);

		$this->template->title('Waktu Simulasi Tes Update');
		$this->render('backend/standart/administrator/waktu_simulasi_tes/waktu_simulasi_tes_update', $this->data);
	}

	/**
	* Update Waktu Simulasi Tess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('waktu_simulasi_tes_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('tgl_ujian', 'Tanggal Ujian', 'trim|required');
		$this->form_validation->set_rules('waktu_mulai', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('waktu_selesai', 'Waktu Selesai', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('simulasi', 'Keterangan Simulasi', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'tgl_ujian' => $this->input->post('tgl_ujian'),
				'waktu_mulai' => $this->input->post('waktu_mulai'),
				'waktu_selesai' => $this->input->post('waktu_selesai'),
				'jenjang' => $this->input->post('jenjang'),
				'simulasi' => $this->input->post('simulasi'),
			];

			
			$save_waktu_simulasi_tes = $this->model_waktu_simulasi_tes->change($id, $save_data);

			if ($save_waktu_simulasi_tes) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/waktu_simulasi_tes', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/waktu_simulasi_tes');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/waktu_simulasi_tes');
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
	* delete Waktu Simulasi Tess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('waktu_simulasi_tes_delete');

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
            set_message(cclang('has_been_deleted', 'waktu_simulasi_tes'), 'success');
        } else {
            set_message(cclang('error_delete', 'waktu_simulasi_tes'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Waktu Simulasi Tess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('waktu_simulasi_tes_view');

		$this->data['waktu_simulasi_tes'] = $this->model_waktu_simulasi_tes->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Waktu Simulasi Tes Detail');
		$this->render('backend/standart/administrator/waktu_simulasi_tes/waktu_simulasi_tes_view', $this->data);
	}
	
	/**
	* delete Waktu Simulasi Tess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$waktu_simulasi_tes = $this->model_waktu_simulasi_tes->find($id);

		
		
		return $this->model_waktu_simulasi_tes->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('waktu_simulasi_tes_export');

		$this->model_waktu_simulasi_tes->export('waktu_simulasi_tes', 'waktu_simulasi_tes');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('waktu_simulasi_tes_export');

		$this->model_waktu_simulasi_tes->pdf('waktu_simulasi_tes', 'waktu_simulasi_tes');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('waktu_simulasi_tes_export');

		$table = $title = 'waktu_simulasi_tes';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_waktu_simulasi_tes->find($id);
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


/* End of file waktu_simulasi_tes.php */
/* Location: ./application/controllers/administrator/Waktu Simulasi Tes.php */