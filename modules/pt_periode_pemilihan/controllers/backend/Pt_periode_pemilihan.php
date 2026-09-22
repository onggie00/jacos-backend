<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pt Periode Pemilihan Controller
*| --------------------------------------------------------------------------
*| Pt Periode Pemilihan site
*|
*/
class Pt_periode_pemilihan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pt_periode_pemilihan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pt Periode Pemilihans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pt_periode_pemilihan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pt_periode_pemilihans'] = $this->model_pt_periode_pemilihan->get($filter, $field, $this->limit_page, $offset);
		$this->data['pt_periode_pemilihan_counts'] = $this->model_pt_periode_pemilihan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pt_periode_pemilihan/index/',
			'total_rows'   => $this->model_pt_periode_pemilihan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Periode Pemilihan PTN List');
		$this->render('backend/standart/administrator/pt_periode_pemilihan/pt_periode_pemilihan_list', $this->data);
	}
	
	/**
	* Add new pt_periode_pemilihans
	*
	*/
	public function add()
	{
		$this->is_allowed('pt_periode_pemilihan_add');

		$this->template->title('Periode Pemilihan PTN New');
		$this->render('backend/standart/administrator/pt_periode_pemilihan/pt_periode_pemilihan_add', $this->data);
	}

	/**
	* Add New Pt Periode Pemilihans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pt_periode_pemilihan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('end_date', 'Tanggal Selesai', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
			];

			
			$save_pt_periode_pemilihan = $this->model_pt_periode_pemilihan->store($save_data);
            

			if ($save_pt_periode_pemilihan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pt_periode_pemilihan;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pt_periode_pemilihan/edit/' . $save_pt_periode_pemilihan, 'Edit Pt Periode Pemilihan'),
						anchor('administrator/pt_periode_pemilihan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pt_periode_pemilihan/edit/' . $save_pt_periode_pemilihan, 'Edit Pt Periode Pemilihan')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_periode_pemilihan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_periode_pemilihan');
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
	* Update view Pt Periode Pemilihans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pt_periode_pemilihan_update');

		$this->data['pt_periode_pemilihan'] = $this->model_pt_periode_pemilihan->find($id);

		$this->template->title('Periode Pemilihan PTN Update');
		$this->render('backend/standart/administrator/pt_periode_pemilihan/pt_periode_pemilihan_update', $this->data);
	}

	/**
	* Update Pt Periode Pemilihans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pt_periode_pemilihan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('end_date', 'Tanggal Selesai', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
			];

			
			$save_pt_periode_pemilihan = $this->model_pt_periode_pemilihan->change($id, $save_data);

			if ($save_pt_periode_pemilihan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pt_periode_pemilihan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_periode_pemilihan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_periode_pemilihan');
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
	* delete Pt Periode Pemilihans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pt_periode_pemilihan_delete');

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
            set_message(cclang('has_been_deleted', 'pt_periode_pemilihan'), 'success');
        } else {
            set_message(cclang('error_delete', 'pt_periode_pemilihan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pt Periode Pemilihans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pt_periode_pemilihan_view');

		$this->data['pt_periode_pemilihan'] = $this->model_pt_periode_pemilihan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Periode Pemilihan PTN Detail');
		$this->render('backend/standart/administrator/pt_periode_pemilihan/pt_periode_pemilihan_view', $this->data);
	}
	
	/**
	* delete Pt Periode Pemilihans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pt_periode_pemilihan = $this->model_pt_periode_pemilihan->find($id);

		
		
		return $this->model_pt_periode_pemilihan->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pt_periode_pemilihan_export');

		$this->model_pt_periode_pemilihan->export('pt_periode_pemilihan', 'pt_periode_pemilihan');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pt_periode_pemilihan_export');

		$this->model_pt_periode_pemilihan->pdf('pt_periode_pemilihan', 'pt_periode_pemilihan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pt_periode_pemilihan_export');

		$table = $title = 'pt_periode_pemilihan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pt_periode_pemilihan->find($id);
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


/* End of file pt_periode_pemilihan.php */
/* Location: ./application/controllers/administrator/Pt Periode Pemilihan.php */