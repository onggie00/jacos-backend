<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kategori Agenda Controller
*| --------------------------------------------------------------------------
*| Kategori Agenda site
*|
*/
class Kategori_agenda extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kategori_agenda');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kategori Agendas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kategori_agenda_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kategori_agendas'] = $this->model_kategori_agenda->get($filter, $field, $this->limit_page, $offset);
		$this->data['kategori_agenda_counts'] = $this->model_kategori_agenda->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kategori_agenda/index/',
			'total_rows'   => $this->model_kategori_agenda->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kategori Agenda List');
		$this->render('backend/standart/administrator/kategori_agenda/kategori_agenda_list', $this->data);
	}
	
	/**
	* Add new kategori_agendas
	*
	*/
	public function add()
	{
		$this->is_allowed('kategori_agenda_add');

		$this->template->title('Kategori Agenda New');
		$this->render('backend/standart/administrator/kategori_agenda/kategori_agenda_add', $this->data);
	}

	/**
	* Add New Kategori Agendas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kategori_agenda_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_kategori' => $this->input->post('nama_kategori'),
			];

			
			$save_kategori_agenda = $this->model_kategori_agenda->store($save_data);
            

			if ($save_kategori_agenda) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kategori_agenda;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kategori_agenda/edit/' . $save_kategori_agenda, 'Edit Kategori Agenda'),
						anchor('administrator/kategori_agenda', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kategori_agenda/edit/' . $save_kategori_agenda, 'Edit Kategori Agenda')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kategori_agenda');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kategori_agenda');
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
	* Update view Kategori Agendas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kategori_agenda_update');

		$this->data['kategori_agenda'] = $this->model_kategori_agenda->find($id);

		$this->template->title('Kategori Agenda Update');
		$this->render('backend/standart/administrator/kategori_agenda/kategori_agenda_update', $this->data);
	}

	/**
	* Update Kategori Agendas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kategori_agenda_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_kategori' => $this->input->post('nama_kategori'),
			];

			
			$save_kategori_agenda = $this->model_kategori_agenda->change($id, $save_data);

			if ($save_kategori_agenda) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kategori_agenda', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kategori_agenda');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kategori_agenda');
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
	* delete Kategori Agendas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kategori_agenda_delete');

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
            set_message(cclang('has_been_deleted', 'kategori_agenda'), 'success');
        } else {
            set_message(cclang('error_delete', 'kategori_agenda'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kategori Agendas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kategori_agenda_view');

		$this->data['kategori_agenda'] = $this->model_kategori_agenda->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kategori Agenda Detail');
		$this->render('backend/standart/administrator/kategori_agenda/kategori_agenda_view', $this->data);
	}
	
	/**
	* delete Kategori Agendas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kategori_agenda = $this->model_kategori_agenda->find($id);

		
		
		return $this->model_kategori_agenda->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kategori_agenda_export');

		$this->model_kategori_agenda->export('kategori_agenda', 'kategori_agenda');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kategori_agenda_export');

		$this->model_kategori_agenda->pdf('kategori_agenda', 'kategori_agenda');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kategori_agenda_export');

		$table = $title = 'kategori_agenda';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kategori_agenda->find($id);
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


/* End of file kategori_agenda.php */
/* Location: ./application/controllers/administrator/Kategori Agenda.php */