<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tipe Kontak Controller
*| --------------------------------------------------------------------------
*| Tipe Kontak site
*|
*/
class Tipe_kontak extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tipe_kontak');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tipe Kontaks
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tipe_kontak_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tipe_kontaks'] = $this->model_tipe_kontak->get($filter, $field, $this->limit_page, $offset);
		$this->data['tipe_kontak_counts'] = $this->model_tipe_kontak->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tipe_kontak/index/',
			'total_rows'   => $this->model_tipe_kontak->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tipe Kontak List');
		$this->render('backend/standart/administrator/tipe_kontak/tipe_kontak_list', $this->data);
	}
	
	/**
	* Add new tipe_kontaks
	*
	*/
	public function add()
	{
		$this->is_allowed('tipe_kontak_add');

		$this->template->title('Tipe Kontak New');
		$this->render('backend/standart/administrator/tipe_kontak/tipe_kontak_add', $this->data);
	}

	/**
	* Add New Tipe Kontaks
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tipe_kontak_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('tipe', 'Tipe', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'tipe' => $this->input->post('tipe'),
			];

			
			$save_tipe_kontak = $this->model_tipe_kontak->store($save_data);
            

			if ($save_tipe_kontak) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tipe_kontak;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tipe_kontak/edit/' . $save_tipe_kontak, 'Edit Tipe Kontak'),
						anchor('administrator/tipe_kontak', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tipe_kontak/edit/' . $save_tipe_kontak, 'Edit Tipe Kontak')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tipe_kontak');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tipe_kontak');
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
	* Update view Tipe Kontaks
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tipe_kontak_update');

		$this->data['tipe_kontak'] = $this->model_tipe_kontak->find($id);

		$this->template->title('Tipe Kontak Update');
		$this->render('backend/standart/administrator/tipe_kontak/tipe_kontak_update', $this->data);
	}

	/**
	* Update Tipe Kontaks
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tipe_kontak_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('tipe', 'Tipe', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'tipe' => $this->input->post('tipe'),
			];

			
			$save_tipe_kontak = $this->model_tipe_kontak->change($id, $save_data);

			if ($save_tipe_kontak) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tipe_kontak', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tipe_kontak');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tipe_kontak');
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
	* delete Tipe Kontaks
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tipe_kontak_delete');

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
            set_message(cclang('has_been_deleted', 'tipe_kontak'), 'success');
        } else {
            set_message(cclang('error_delete', 'tipe_kontak'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tipe Kontaks
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tipe_kontak_view');

		$this->data['tipe_kontak'] = $this->model_tipe_kontak->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tipe Kontak Detail');
		$this->render('backend/standart/administrator/tipe_kontak/tipe_kontak_view', $this->data);
	}
	
	/**
	* delete Tipe Kontaks
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tipe_kontak = $this->model_tipe_kontak->find($id);

		
		
		return $this->model_tipe_kontak->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tipe_kontak_export');

		$this->model_tipe_kontak->export('tipe_kontak', 'tipe_kontak');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tipe_kontak_export');

		$this->model_tipe_kontak->pdf('tipe_kontak', 'tipe_kontak');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tipe_kontak_export');

		$table = $title = 'tipe_kontak';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tipe_kontak->find($id);
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


/* End of file tipe_kontak.php */
/* Location: ./application/controllers/administrator/Tipe Kontak.php */