<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pekerjaan Ortu Controller
*| --------------------------------------------------------------------------
*| Pekerjaan Ortu site
*|
*/
class Pekerjaan_ortu extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pekerjaan_ortu');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pekerjaan Ortus
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pekerjaan_ortu_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pekerjaan_ortus'] = $this->model_pekerjaan_ortu->get($filter, $field, $this->limit_page, $offset);
		$this->data['pekerjaan_ortu_counts'] = $this->model_pekerjaan_ortu->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pekerjaan_ortu/index/',
			'total_rows'   => $this->model_pekerjaan_ortu->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pekerjaan Ortu List');
		$this->render('backend/standart/administrator/pekerjaan_ortu/pekerjaan_ortu_list', $this->data);
	}
	
	/**
	* Add new pekerjaan_ortus
	*
	*/
	public function add()
	{
		$this->is_allowed('pekerjaan_ortu_add');

		$this->template->title('Pekerjaan Ortu New');
		$this->render('backend/standart/administrator/pekerjaan_ortu/pekerjaan_ortu_add', $this->data);
	}

	/**
	* Add New Pekerjaan Ortus
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pekerjaan_ortu_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_pekerjaan', 'Nama Pekerjaan', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_pekerjaan' => $this->input->post('nama_pekerjaan'),
			];

			
			$save_pekerjaan_ortu = $this->model_pekerjaan_ortu->store($save_data);
            

			if ($save_pekerjaan_ortu) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pekerjaan_ortu;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pekerjaan_ortu/edit/' . $save_pekerjaan_ortu, 'Edit Pekerjaan Ortu'),
						anchor('administrator/pekerjaan_ortu', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pekerjaan_ortu/edit/' . $save_pekerjaan_ortu, 'Edit Pekerjaan Ortu')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pekerjaan_ortu');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pekerjaan_ortu');
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
	* Update view Pekerjaan Ortus
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pekerjaan_ortu_update');

		$this->data['pekerjaan_ortu'] = $this->model_pekerjaan_ortu->find($id);

		$this->template->title('Pekerjaan Ortu Update');
		$this->render('backend/standart/administrator/pekerjaan_ortu/pekerjaan_ortu_update', $this->data);
	}

	/**
	* Update Pekerjaan Ortus
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pekerjaan_ortu_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_pekerjaan', 'Nama Pekerjaan', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_pekerjaan' => $this->input->post('nama_pekerjaan'),
			];

			
			$save_pekerjaan_ortu = $this->model_pekerjaan_ortu->change($id, $save_data);

			if ($save_pekerjaan_ortu) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pekerjaan_ortu', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pekerjaan_ortu');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pekerjaan_ortu');
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
	* delete Pekerjaan Ortus
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pekerjaan_ortu_delete');

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
            set_message(cclang('has_been_deleted', 'pekerjaan_ortu'), 'success');
        } else {
            set_message(cclang('error_delete', 'pekerjaan_ortu'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pekerjaan Ortus
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pekerjaan_ortu_view');

		$this->data['pekerjaan_ortu'] = $this->model_pekerjaan_ortu->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pekerjaan Ortu Detail');
		$this->render('backend/standart/administrator/pekerjaan_ortu/pekerjaan_ortu_view', $this->data);
	}
	
	/**
	* delete Pekerjaan Ortus
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pekerjaan_ortu = $this->model_pekerjaan_ortu->find($id);

		
		
		return $this->model_pekerjaan_ortu->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pekerjaan_ortu_export');

		$this->model_pekerjaan_ortu->export('pekerjaan_ortu', 'pekerjaan_ortu');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pekerjaan_ortu_export');

		$this->model_pekerjaan_ortu->pdf('pekerjaan_ortu', 'pekerjaan_ortu');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pekerjaan_ortu_export');

		$table = $title = 'pekerjaan_ortu';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pekerjaan_ortu->find($id);
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


/* End of file pekerjaan_ortu.php */
/* Location: ./application/controllers/administrator/Pekerjaan Ortu.php */