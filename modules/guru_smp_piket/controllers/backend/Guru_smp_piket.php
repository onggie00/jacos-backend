<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Guru Smp Piket Controller
*| --------------------------------------------------------------------------
*| Guru Smp Piket site
*|
*/
class Guru_smp_piket extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_guru_smp_piket');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Guru Smp Pikets
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('guru_smp_piket_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['guru_smp_pikets'] = $this->model_guru_smp_piket->get($filter, $field, $this->limit_page, $offset);
		$this->data['guru_smp_piket_counts'] = $this->model_guru_smp_piket->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/guru_smp_piket/index/',
			'total_rows'   => $this->model_guru_smp_piket->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Guru Piket SMP List');
		$this->render('backend/standart/administrator/guru_smp_piket/guru_smp_piket_list', $this->data);
	}
	
	/**
	* Add new guru_smp_pikets
	*
	*/
	public function add()
	{
		$this->is_allowed('guru_smp_piket_add');

		$this->template->title('Guru Piket SMP New');
		$this->render('backend/standart/administrator/guru_smp_piket/guru_smp_piket_add', $this->data);
	}

	/**
	* Add New Guru Smp Pikets
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('guru_smp_piket_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'id_kelas' => $this->input->post('id_kelas'),
			];

			
			$save_guru_smp_piket = $this->model_guru_smp_piket->store($save_data);
            

			if ($save_guru_smp_piket) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_guru_smp_piket;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/guru_smp_piket/edit/' . $save_guru_smp_piket, 'Edit Guru Smp Piket'),
						anchor('administrator/guru_smp_piket', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/guru_smp_piket/edit/' . $save_guru_smp_piket, 'Edit Guru Smp Piket')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/guru_smp_piket');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/guru_smp_piket');
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
	* Update view Guru Smp Pikets
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('guru_smp_piket_update');

		$this->data['guru_smp_piket'] = $this->model_guru_smp_piket->find($id);

		$this->template->title('Guru Piket SMP Update');
		$this->render('backend/standart/administrator/guru_smp_piket/guru_smp_piket_update', $this->data);
	}

	/**
	* Update Guru Smp Pikets
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('guru_smp_piket_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'id_kelas' => $this->input->post('id_kelas'),
			];

			
			$save_guru_smp_piket = $this->model_guru_smp_piket->change($id, $save_data);

			if ($save_guru_smp_piket) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/guru_smp_piket', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/guru_smp_piket');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/guru_smp_piket');
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
	* delete Guru Smp Pikets
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('guru_smp_piket_delete');

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
            set_message(cclang('has_been_deleted', 'guru_smp_piket'), 'success');
        } else {
            set_message(cclang('error_delete', 'guru_smp_piket'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Guru Smp Pikets
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('guru_smp_piket_view');

		$this->data['guru_smp_piket'] = $this->model_guru_smp_piket->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Guru Piket SMP Detail');
		$this->render('backend/standart/administrator/guru_smp_piket/guru_smp_piket_view', $this->data);
	}
	
	/**
	* delete Guru Smp Pikets
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$guru_smp_piket = $this->model_guru_smp_piket->find($id);

		
		
		return $this->model_guru_smp_piket->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('guru_smp_piket_export');

		$this->model_guru_smp_piket->export('guru_smp_piket', 'guru_smp_piket');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('guru_smp_piket_export');

		$this->model_guru_smp_piket->pdf('guru_smp_piket', 'guru_smp_piket');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('guru_smp_piket_export');

		$table = $title = 'guru_smp_piket';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_guru_smp_piket->find($id);
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


/* End of file guru_smp_piket.php */
/* Location: ./application/controllers/administrator/Guru Smp Piket.php */