<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Kelas Kb Controller
*| --------------------------------------------------------------------------
*| Kelas Kb site
*|
*/
class Kelas_kb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kelas_kb');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kelas Kbs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kelas_kb_list');

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
		$this->data['list_tingkatan'] = $this->mymodel->getall('tingkatan_kb');

		$this->data['kelas_kbs'] = $this->model_kelas_kb->get($filter, $this->limit_page, $offset);
		$this->data['kelas_kb_counts'] = $this->model_kelas_kb->count_all($filter);

		$config = array(
			'base_url'     => 'administrator/kelas_kb/index/',
			'total_rows'   => $this->data['kelas_kb_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);
		$this->data['filter'] = $filter;

		$this->template->title('Kelas Kb List');
		$this->render('backend/standart/administrator/kelas_kb/kelas_kb_list', $this->data);
	}
	
	/**
	* Add new kelas_kbs
	*
	*/
	public function add()
	{
		$this->is_allowed('kelas_kb_add');

		$this->template->title('Kelas Kb New');
		$this->render('backend/standart/administrator/kelas_kb/kelas_kb_add', $this->data);
	}

	/**
	* Add New Kelas Kbs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kelas_kb_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$get_tingkatan = $this->mymodel->getbywhere("tingkatan_kb", "id_tingkatan_kb", $this->input->post('id_tingkatan'), "row");

			$save_data = [
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nama_kelas' => $this->input->post('nama_kelas'),
				'label' => $get_tingkatan->label." ".$this->input->post('nama_kelas'),

			];

			
			$save_kelas_kb = $this->model_kelas_kb->store($save_data);
            

			if ($save_kelas_kb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kelas_kb;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kelas_kb/edit/' . $save_kelas_kb, 'Edit Kelas Kb'),
						anchor('administrator/kelas_kb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kelas_kb/edit/' . $save_kelas_kb, 'Edit Kelas Kb')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kelas_kb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kelas_kb');
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
	* Update view Kelas Kbs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kelas_kb_update');

		$this->data['kelas_kb'] = $this->model_kelas_kb->find($id);

		$this->template->title('Kelas Kb Update');
		$this->render('backend/standart/administrator/kelas_kb/kelas_kb_update', $this->data);
	}

	/**
	* Update Kelas Kbs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kelas_kb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$get_tingkatan = $this->mymodel->getbywhere("tingkatan_kb", "id_tingkatan_kb", $this->input->post('id_tingkatan'), "row");

			$old = $this->model_kelas_kb->find($id);
			$old_label = ($old && isset($old->label)) ? $old->label : '';

			$save_data = [
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nama_kelas' => $this->input->post('nama_kelas'),
				'label' => $get_tingkatan->label." ".$this->input->post('nama_kelas'),

			];

			
			$save_kelas_kb = $this->model_kelas_kb->change($id, $save_data);

			if ($save_kelas_kb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kelas_kb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kelas_kb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kelas_kb');
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
	* delete Kelas Kbs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kelas_kb_delete');

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
            set_message(cclang('has_been_deleted', 'kelas_kb'), 'success');
        } else {
            set_message(cclang('error_delete', 'kelas_kb'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kelas Kbs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kelas_kb_view');

		$this->data['kelas_kb'] = $this->model_kelas_kb->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kelas Kb Detail');
		$this->render('backend/standart/administrator/kelas_kb/kelas_kb_view', $this->data);
	}
	
	/**
	* delete Kelas Kbs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kelas_kb = $this->model_kelas_kb->find($id);

		
		
		
		return $this->model_kelas_kb->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kelas_kb_export');

		$this->model_kelas_kb->export('kelas_kb', 'kelas_kb');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kelas_kb_export');

		$this->model_kelas_kb->pdf('kelas_kb', 'kelas_kb');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kelas_kb_export');

		$table = $title = 'kelas_kb';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kelas_kb->find($id);
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


/* End of file kelas_kb.php */
/* Location: ./application/controllers/administrator/Kelas Kb.php */
