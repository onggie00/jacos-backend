<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kelas Sma Controller
*| --------------------------------------------------------------------------
*| Kelas Sma site
*|
*/
class Kelas_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kelas_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kelas Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kelas_sma_list');

		// Build filter array
		$filter = array();
		$q = trim($this->input->get('q'));
		$field = trim($this->input->get('f'));
		$id_tingkatan = $this->input->get('id_tingkatan');
		
		if ($q !== '' && $q !== false) {
			$filter['q'] = $q;
			$filter['f'] = $field;
		}
		if ($id_tingkatan !== '' && $id_tingkatan !== false) {
			$filter['id_tingkatan'] = $id_tingkatan;
		}

		// Get list tingkatan for filter dropdown
		$this->data['list_tingkatan'] = $this->mymodel->getall('tingkatan_sma');

		$this->data['kelas_smas'] = $this->model_kelas_sma->get($filter, $this->limit_page, $offset);
		$this->data['kelas_sma_counts'] = $this->model_kelas_sma->count_all($filter);

		$config = array(
			'base_url'     => 'administrator/kelas_sma/index/',
			'total_rows'   => $this->data['kelas_sma_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);
		$this->data['filter'] = $filter;

		$this->template->title('Kelas Sma List');
		$this->render('backend/standart/administrator/kelas_sma/kelas_sma_list', $this->data);
	}
	
	/**
	* Add new kelas_smas
	*
	*/
	public function add()
	{
		$this->is_allowed('kelas_sma_add');

		$this->template->title('Kelas Sma New');
		$this->render('backend/standart/administrator/kelas_sma/kelas_sma_add', $this->data);
	}

	/**
	* Add New Kelas Smas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kelas_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$get_tingkatan = $this->mymodel->getbywhere("tingkatan_sma", "id_tingkatan_sma", $this->input->post('id_tingkatan'), "row");

			$save_data = [
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nama_kelas' => $this->input->post('nama_kelas'),
				'label' => $get_tingkatan->label." ".$this->input->post('nama_kelas'),

			];

			
			$save_kelas_sma = $this->model_kelas_sma->store($save_data);
            

			if ($save_kelas_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kelas_sma;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kelas_sma/edit/' . $save_kelas_sma, 'Edit Kelas Sma'),
						anchor('administrator/kelas_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kelas_sma/edit/' . $save_kelas_sma, 'Edit Kelas Sma')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kelas_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kelas_sma');
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
	* Update view Kelas Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kelas_sma_update');

		$this->data['kelas_sma'] = $this->model_kelas_sma->find($id);

		$this->template->title('Kelas Sma Update');
		$this->render('backend/standart/administrator/kelas_sma/kelas_sma_update', $this->data);
	}

	/**
	* Update Kelas Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kelas_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$get_tingkatan = $this->mymodel->getbywhere("tingkatan_sma", "id_tingkatan_sma", $this->input->post('id_tingkatan'), "row");

			$save_data = [
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nama_kelas' => $this->input->post('nama_kelas'),
				'label' => $get_tingkatan->label." ".$this->input->post('nama_kelas'),

			];

			
			$save_kelas_sma = $this->model_kelas_sma->change($id, $save_data);

			if ($save_kelas_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kelas_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kelas_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kelas_sma');
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
	* delete Kelas Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kelas_sma_delete');

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
            set_message(cclang('has_been_deleted', 'kelas_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'kelas_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kelas Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kelas_sma_view');

		$this->data['kelas_sma'] = $this->model_kelas_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kelas Sma Detail');
		$this->render('backend/standart/administrator/kelas_sma/kelas_sma_view', $this->data);
	}
	
	/**
	* delete Kelas Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kelas_sma = $this->model_kelas_sma->find($id);

		
		
		return $this->model_kelas_sma->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kelas_sma_export');

		$this->model_kelas_sma->export('kelas_sma', 'kelas_sma');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kelas_sma_export');

		$this->model_kelas_sma->pdf('kelas_sma', 'kelas_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kelas_sma_export');

		$table = $title = 'kelas_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kelas_sma->find($id);
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


/* End of file kelas_sma.php */
/* Location: ./application/controllers/administrator/Kelas Sma.php */