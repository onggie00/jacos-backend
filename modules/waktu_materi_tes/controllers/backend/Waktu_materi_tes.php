<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Waktu Materi Tes Controller
*| --------------------------------------------------------------------------
*| Waktu Materi Tes site
*|
*/
class Waktu_materi_tes extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_waktu_materi_tes');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Waktu Materi Tess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('waktu_materi_tes_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['waktu_materi_tess'] = $this->model_waktu_materi_tes->get($filter, $field, $this->limit_page, $offset);
		$this->data['waktu_materi_tes_counts'] = $this->model_waktu_materi_tes->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/waktu_materi_tes/index/',
			'total_rows'   => $this->model_waktu_materi_tes->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Waktu & Materi Ujian List');
		$this->render('backend/standart/administrator/waktu_materi_tes/waktu_materi_tes_list', $this->data);
	}
	
	
		/**
	* Update view Waktu Materi Tess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('waktu_materi_tes_update');

		$this->data['waktu_materi_tes'] = $this->model_waktu_materi_tes->find($id);

		$this->template->title('Waktu & Materi Ujian Update');
		$this->render('backend/standart/administrator/waktu_materi_tes/waktu_materi_tes_update', $this->data);
	}

	/**
	* Update Waktu Materi Tess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('waktu_materi_tes_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('tgl_ujian', 'Tanggal Ujian', 'trim|required');
		$this->form_validation->set_rules('waktu_mulai', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('waktu_selesai', 'Waktu Selesai', 'trim|required');
		$this->form_validation->set_rules('materi', 'Materi / Agenda', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'tgl_ujian' => $this->input->post('tgl_ujian'),
				'waktu_mulai' => $this->input->post('waktu_mulai'),
				'waktu_selesai' => $this->input->post('waktu_selesai'),
				'materi' => $this->input->post('materi')
			];

			
			$save_waktu_materi_tes = $this->model_waktu_materi_tes->change($id, $save_data);

			if ($save_waktu_materi_tes) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/waktu_materi_tes', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/waktu_materi_tes');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/waktu_materi_tes');
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
	* delete Waktu Materi Tess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('waktu_materi_tes_delete');

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
            set_message(cclang('has_been_deleted', 'waktu_materi_tes'), 'success');
        } else {
            set_message(cclang('error_delete', 'waktu_materi_tes'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Waktu Materi Tess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('waktu_materi_tes_view');

		$this->data['waktu_materi_tes'] = $this->model_waktu_materi_tes->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Waktu & Materi Ujian Detail');
		$this->render('backend/standart/administrator/waktu_materi_tes/waktu_materi_tes_view', $this->data);
	}
	
	/**
	* delete Waktu Materi Tess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$waktu_materi_tes = $this->model_waktu_materi_tes->find($id);

		
		
		return $this->model_waktu_materi_tes->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('waktu_materi_tes_export');

		$this->model_waktu_materi_tes->export('waktu_materi_tes', 'waktu_materi_tes');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('waktu_materi_tes_export');

		$this->model_waktu_materi_tes->pdf('waktu_materi_tes', 'waktu_materi_tes');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('waktu_materi_tes_export');

		$table = $title = 'waktu_materi_tes';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_waktu_materi_tes->find($id);
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


/* End of file waktu_materi_tes.php */
/* Location: ./application/controllers/administrator/Waktu Materi Tes.php */