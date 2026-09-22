<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Catatan Kartu Siswa Sementara Controller
*| --------------------------------------------------------------------------
*| Catatan Kartu Siswa Sementara site
*|
*/
class Catatan_kartu_siswa_sementara extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_catatan_kartu_siswa_sementara');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Catatan Kartu Siswa Sementaras
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['catatan_kartu_siswa_sementaras'] = $this->model_catatan_kartu_siswa_sementara->get($filter, $field, $this->limit_page, $offset);
		$this->data['catatan_kartu_siswa_sementara_counts'] = $this->model_catatan_kartu_siswa_sementara->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/catatan_kartu_siswa_sementara/index/',
			'total_rows'   => $this->model_catatan_kartu_siswa_sementara->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Catatan Kartu Siswa Sementara List');
		$this->render('backend/standart/administrator/catatan_kartu_siswa_sementara/catatan_kartu_siswa_sementara_list', $this->data);
	}
	
	
		/**
	* Update view Catatan Kartu Siswa Sementaras
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_update');

		$this->data['catatan_kartu_siswa_sementara'] = $this->model_catatan_kartu_siswa_sementara->find($id);

		$this->template->title('Catatan Kartu Siswa Sementara Update');
		$this->render('backend/standart/administrator/catatan_kartu_siswa_sementara/catatan_kartu_siswa_sementara_update', $this->data);
	}

	/**
	* Update Catatan Kartu Siswa Sementaras
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('catatan_kartu_siswa_sementara_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('catatan', 'Catatan', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'catatan' => $this->input->post('catatan'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_catatan_kartu_siswa_sementara = $this->model_catatan_kartu_siswa_sementara->change($id, $save_data);

			if ($save_catatan_kartu_siswa_sementara) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/catatan_kartu_siswa_sementara', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/catatan_kartu_siswa_sementara');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/catatan_kartu_siswa_sementara');
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
	* delete Catatan Kartu Siswa Sementaras
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_delete');

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
            set_message(cclang('has_been_deleted', 'catatan_kartu_siswa_sementara'), 'success');
        } else {
            set_message(cclang('error_delete', 'catatan_kartu_siswa_sementara'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Catatan Kartu Siswa Sementaras
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_view');

		$this->data['catatan_kartu_siswa_sementara'] = $this->model_catatan_kartu_siswa_sementara->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Catatan Kartu Siswa Sementara Detail');
		$this->render('backend/standart/administrator/catatan_kartu_siswa_sementara/catatan_kartu_siswa_sementara_view', $this->data);
	}
	
	/**
	* delete Catatan Kartu Siswa Sementaras
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$catatan_kartu_siswa_sementara = $this->model_catatan_kartu_siswa_sementara->find($id);

		
		
		return $this->model_catatan_kartu_siswa_sementara->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_export');

		$this->model_catatan_kartu_siswa_sementara->export('catatan_kartu_siswa_sementara', 'catatan_kartu_siswa_sementara');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_export');

		$this->model_catatan_kartu_siswa_sementara->pdf('catatan_kartu_siswa_sementara', 'catatan_kartu_siswa_sementara');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('catatan_kartu_siswa_sementara_export');

		$table = $title = 'catatan_kartu_siswa_sementara';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_catatan_kartu_siswa_sementara->find($id);
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


/* End of file catatan_kartu_siswa_sementara.php */
/* Location: ./application/controllers/administrator/Catatan Kartu Siswa Sementara.php */