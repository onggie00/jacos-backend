<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Keterangan Prestasi Controller
*| --------------------------------------------------------------------------
*| Keterangan Prestasi site
*|
*/
class Keterangan_prestasi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_keterangan_prestasi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Keterangan Prestasis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('keterangan_prestasi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['keterangan_prestasis'] = $this->model_keterangan_prestasi->get($filter, $field, $this->limit_page, $offset);
		$this->data['keterangan_prestasi_counts'] = $this->model_keterangan_prestasi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/keterangan_prestasi/index/',
			'total_rows'   => $this->model_keterangan_prestasi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Keterangan Prestasi List');
		$this->render('backend/standart/administrator/keterangan_prestasi/keterangan_prestasi_list', $this->data);
	}
	
	/**
	* Add new keterangan_prestasis
	*
	*/
	public function add()
	{
		$this->is_allowed('keterangan_prestasi_add');

		$this->template->title('Keterangan Prestasi New');
		$this->render('backend/standart/administrator/keterangan_prestasi/keterangan_prestasi_add', $this->data);
	}

	/**
	* Add New Keterangan Prestasis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('keterangan_prestasi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('keterangan_prestasi', 'Keterangan Prestasi', 'trim|required|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'keterangan_prestasi' => $this->input->post('keterangan_prestasi'),
			];

			
			$save_keterangan_prestasi = $this->model_keterangan_prestasi->store($save_data);
            

			if ($save_keterangan_prestasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_keterangan_prestasi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/keterangan_prestasi/edit/' . $save_keterangan_prestasi, 'Edit Keterangan Prestasi'),
						anchor('administrator/keterangan_prestasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/keterangan_prestasi/edit/' . $save_keterangan_prestasi, 'Edit Keterangan Prestasi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/keterangan_prestasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/keterangan_prestasi');
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
	* Update view Keterangan Prestasis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('keterangan_prestasi_update');

		$this->data['keterangan_prestasi'] = $this->model_keterangan_prestasi->find($id);

		$this->template->title('Keterangan Prestasi Update');
		$this->render('backend/standart/administrator/keterangan_prestasi/keterangan_prestasi_update', $this->data);
	}

	/**
	* Update Keterangan Prestasis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('keterangan_prestasi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('keterangan_prestasi', 'Keterangan Prestasi', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'keterangan_prestasi' => $this->input->post('keterangan_prestasi'),
			];

			
			$save_keterangan_prestasi = $this->model_keterangan_prestasi->change($id, $save_data);

			if ($save_keterangan_prestasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/keterangan_prestasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/keterangan_prestasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/keterangan_prestasi');
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
	* delete Keterangan Prestasis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('keterangan_prestasi_delete');

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
            set_message(cclang('has_been_deleted', 'keterangan_prestasi'), 'success');
        } else {
            set_message(cclang('error_delete', 'keterangan_prestasi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Keterangan Prestasis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('keterangan_prestasi_view');

		$this->data['keterangan_prestasi'] = $this->model_keterangan_prestasi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Keterangan Prestasi Detail');
		$this->render('backend/standart/administrator/keterangan_prestasi/keterangan_prestasi_view', $this->data);
	}
	
	/**
	* delete Keterangan Prestasis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$keterangan_prestasi = $this->model_keterangan_prestasi->find($id);

		
		
		return $this->model_keterangan_prestasi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('keterangan_prestasi_export');

		$this->model_keterangan_prestasi->export('keterangan_prestasi', 'keterangan_prestasi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('keterangan_prestasi_export');

		$this->model_keterangan_prestasi->pdf('keterangan_prestasi', 'keterangan_prestasi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('keterangan_prestasi_export');

		$table = $title = 'keterangan_prestasi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_keterangan_prestasi->find($id);
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


/* End of file keterangan_prestasi.php */
/* Location: ./application/controllers/administrator/Keterangan Prestasi.php */