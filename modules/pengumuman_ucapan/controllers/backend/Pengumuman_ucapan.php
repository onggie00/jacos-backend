<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengumuman Ucapan Controller
*| --------------------------------------------------------------------------
*| Pengumuman Ucapan site
*|
*/
class Pengumuman_ucapan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengumuman_ucapan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengumuman Ucapans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengumuman_ucapan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengumuman_ucapans'] = $this->model_pengumuman_ucapan->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengumuman_ucapan_counts'] = $this->model_pengumuman_ucapan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengumuman_ucapan/index/',
			'total_rows'   => $this->model_pengumuman_ucapan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ucapan Pengumuman List');
		$this->render('backend/standart/administrator/pengumuman_ucapan/pengumuman_ucapan_list', $this->data);
	}
	
	
		/**
	* Update view Pengumuman Ucapans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengumuman_ucapan_update');

		$this->data['pengumuman_ucapan'] = $this->model_pengumuman_ucapan->find($id);

		$this->template->title('Ucapan Pengumuman Update');
		$this->render('backend/standart/administrator/pengumuman_ucapan/pengumuman_ucapan_update', $this->data);
	}

	/**
	* Update Pengumuman Ucapans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengumuman_ucapan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('ucapan_lulus', 'Ucapan Lulus', 'trim|required');
		$this->form_validation->set_rules('ucapan_tidak_lulus', 'Ucapan Tidak Lulus', 'trim|required');
		$this->form_validation->set_rules('ucapan_cadangan', 'Ucapan Cadangan', 'trim|required');
		$this->form_validation->set_rules('ucapan_belum_tersedia', 'Ucapan Belum Tersedia', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'ucapan_lulus' => $this->input->post('ucapan_lulus'),
				'ucapan_tidak_lulus' => $this->input->post('ucapan_tidak_lulus'),
				'ucapan_cadangan' => $this->input->post('ucapan_cadangan'),
				'ucapan_belum_tersedia' => $this->input->post('ucapan_belum_tersedia'),
			];

			
			$save_pengumuman_ucapan = $this->model_pengumuman_ucapan->change($id, $save_data);

			if ($save_pengumuman_ucapan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengumuman_ucapan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengumuman_ucapan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengumuman_ucapan');
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
	* delete Pengumuman Ucapans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengumuman_ucapan_delete');

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
            set_message(cclang('has_been_deleted', 'pengumuman_ucapan'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengumuman_ucapan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengumuman Ucapans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengumuman_ucapan_view');

		$this->data['pengumuman_ucapan'] = $this->model_pengumuman_ucapan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ucapan Pengumuman Detail');
		$this->render('backend/standart/administrator/pengumuman_ucapan/pengumuman_ucapan_view', $this->data);
	}
	
	/**
	* delete Pengumuman Ucapans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengumuman_ucapan = $this->model_pengumuman_ucapan->find($id);

		
		
		return $this->model_pengumuman_ucapan->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengumuman_ucapan_export');

		$this->model_pengumuman_ucapan->export('pengumuman_ucapan', 'pengumuman_ucapan');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengumuman_ucapan_export');

		$this->model_pengumuman_ucapan->pdf('pengumuman_ucapan', 'pengumuman_ucapan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengumuman_ucapan_export');

		$table = $title = 'pengumuman_ucapan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengumuman_ucapan->find($id);
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


/* End of file pengumuman_ucapan.php */
/* Location: ./application/controllers/administrator/Pengumuman Ucapan.php */