<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Catatan Kartu Peserta Controller
*| --------------------------------------------------------------------------
*| Catatan Kartu Peserta site
*|
*/
class Catatan_kartu_peserta extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_catatan_kartu_peserta');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Catatan Kartu Pesertas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('catatan_kartu_peserta_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['catatan_kartu_pesertas'] = $this->model_catatan_kartu_peserta->get($filter, $field, $this->limit_page, $offset);
		$this->data['catatan_kartu_peserta_counts'] = $this->model_catatan_kartu_peserta->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/catatan_kartu_peserta/index/',
			'total_rows'   => $this->model_catatan_kartu_peserta->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Catatan Kartu Peserta List');
		$this->render('backend/standart/administrator/catatan_kartu_peserta/catatan_kartu_peserta_list', $this->data);
	}
	
	
		/**
	* Update view Catatan Kartu Pesertas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('catatan_kartu_peserta_update');

		$this->data['catatan_kartu_peserta'] = $this->model_catatan_kartu_peserta->find($id);

		$this->template->title('Catatan Kartu Peserta Update');
		$this->render('backend/standart/administrator/catatan_kartu_peserta/catatan_kartu_peserta_update', $this->data);
	}

	/**
	* Update Catatan Kartu Pesertas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('catatan_kartu_peserta_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('catatan_persiapan', 'Catatan Persiapan', 'trim|required');
		$this->form_validation->set_rules('catatan_perhatikan', 'Catatan Perhatikan', 'trim|required');
		$this->form_validation->set_rules('keterangan_ujian', 'Keterangan Ujian', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'catatan_persiapan' => $this->input->post('catatan_persiapan'),
				'catatan_perhatikan' => $this->input->post('catatan_perhatikan'),
				'keterangan_ujian' => $this->input->post('keterangan_ujian'),
			];

			
			$save_catatan_kartu_peserta = $this->model_catatan_kartu_peserta->change($id, $save_data);

			if ($save_catatan_kartu_peserta) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/catatan_kartu_peserta', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/catatan_kartu_peserta');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/catatan_kartu_peserta');
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
	* delete Catatan Kartu Pesertas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('catatan_kartu_peserta_delete');

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
            set_message(cclang('has_been_deleted', 'catatan_kartu_peserta'), 'success');
        } else {
            set_message(cclang('error_delete', 'catatan_kartu_peserta'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Catatan Kartu Pesertas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('catatan_kartu_peserta_view');

		$this->data['catatan_kartu_peserta'] = $this->model_catatan_kartu_peserta->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Catatan Kartu Peserta Detail');
		$this->render('backend/standart/administrator/catatan_kartu_peserta/catatan_kartu_peserta_view', $this->data);
	}
	
	/**
	* delete Catatan Kartu Pesertas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$catatan_kartu_peserta = $this->model_catatan_kartu_peserta->find($id);

		
		
		return $this->model_catatan_kartu_peserta->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('catatan_kartu_peserta_export');

		$this->model_catatan_kartu_peserta->export('catatan_kartu_peserta', 'catatan_kartu_peserta');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('catatan_kartu_peserta_export');

		$this->model_catatan_kartu_peserta->pdf('catatan_kartu_peserta', 'catatan_kartu_peserta');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('catatan_kartu_peserta_export');

		$table = $title = 'catatan_kartu_peserta';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_catatan_kartu_peserta->find($id);
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


/* End of file catatan_kartu_peserta.php */
/* Location: ./application/controllers/administrator/Catatan Kartu Peserta.php */