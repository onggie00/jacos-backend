<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Alasan Tertarik Controller
*| --------------------------------------------------------------------------
*| Alasan Tertarik site
*|
*/
class Alasan_tertarik extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_alasan_tertarik');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Alasan Tertariks
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('alasan_tertarik_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['alasan_tertariks'] = $this->model_alasan_tertarik->get($filter, $field, $this->limit_page, $offset);
		$this->data['alasan_tertarik_counts'] = $this->model_alasan_tertarik->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/alasan_tertarik/index/',
			'total_rows'   => $this->model_alasan_tertarik->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Alasan Tertarik Data List');
		$this->render('backend/standart/administrator/alasan_tertarik/alasan_tertarik_list', $this->data);
	}
	
	/**
	* Add new alasan_tertariks
	*
	*/
	public function add()
	{
		$this->is_allowed('alasan_tertarik_add');

		$this->template->title('Alasan Tertarik Data New');
		$this->render('backend/standart/administrator/alasan_tertarik/alasan_tertarik_add', $this->data);
	}

	/**
	* Add New Alasan Tertariks
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('alasan_tertarik_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('alasan', 'Alasan', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'alasan' => $this->input->post('alasan'),
			];

			
			$save_alasan_tertarik = $this->model_alasan_tertarik->store($save_data);
            

			if ($save_alasan_tertarik) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_alasan_tertarik;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/alasan_tertarik/edit/' . $save_alasan_tertarik, 'Edit Alasan Tertarik'),
						anchor('administrator/alasan_tertarik', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/alasan_tertarik/edit/' . $save_alasan_tertarik, 'Edit Alasan Tertarik')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/alasan_tertarik');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/alasan_tertarik');
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
	* Update view Alasan Tertariks
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('alasan_tertarik_update');

		$this->data['alasan_tertarik'] = $this->model_alasan_tertarik->find($id);

		$this->template->title('Alasan Tertarik Data Update');
		$this->render('backend/standart/administrator/alasan_tertarik/alasan_tertarik_update', $this->data);
	}

	/**
	* Update Alasan Tertariks
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('alasan_tertarik_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('alasan', 'Alasan', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'alasan' => $this->input->post('alasan'),
			];

			
			$save_alasan_tertarik = $this->model_alasan_tertarik->change($id, $save_data);

			if ($save_alasan_tertarik) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/alasan_tertarik', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/alasan_tertarik');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/alasan_tertarik');
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
	* delete Alasan Tertariks
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('alasan_tertarik_delete');

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
            set_message(cclang('has_been_deleted', 'alasan_tertarik'), 'success');
        } else {
            set_message(cclang('error_delete', 'alasan_tertarik'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Alasan Tertariks
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('alasan_tertarik_view');

		$this->data['alasan_tertarik'] = $this->model_alasan_tertarik->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Alasan Tertarik Data Detail');
		$this->render('backend/standart/administrator/alasan_tertarik/alasan_tertarik_view', $this->data);
	}
	
	/**
	* delete Alasan Tertariks
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$alasan_tertarik = $this->model_alasan_tertarik->find($id);

		
		
		return $this->model_alasan_tertarik->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('alasan_tertarik_export');

		$this->model_alasan_tertarik->export('alasan_tertarik', 'alasan_tertarik');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('alasan_tertarik_export');

		$this->model_alasan_tertarik->pdf('alasan_tertarik', 'alasan_tertarik');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('alasan_tertarik_export');

		$table = $title = 'alasan_tertarik';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_alasan_tertarik->find($id);
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


/* End of file alasan_tertarik.php */
/* Location: ./application/controllers/administrator/Alasan Tertarik.php */