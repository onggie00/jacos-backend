<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tipe Kalender Controller
*| --------------------------------------------------------------------------
*| Tipe Kalender site
*|
*/
class Tipe_kalender extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tipe_kalender');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tipe Kalenders
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tipe_kalender_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tipe_kalenders'] = $this->model_tipe_kalender->get($filter, $field, $this->limit_page, $offset);
		$this->data['tipe_kalender_counts'] = $this->model_tipe_kalender->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tipe_kalender/index/',
			'total_rows'   => $this->model_tipe_kalender->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tipe Agenda List');
		$this->render('backend/standart/administrator/tipe_kalender/tipe_kalender_list', $this->data);
	}
	
	/**
	* Add new tipe_kalenders
	*
	*/
	public function add()
	{
		$this->is_allowed('tipe_kalender_add');

		$this->template->title('Tipe Agenda New');
		$this->render('backend/standart/administrator/tipe_kalender/tipe_kalender_add', $this->data);
	}

	/**
	* Add New Tipe Kalenders
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tipe_kalender_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_tipe', 'Nama Tipe', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('warna_hexcode', 'Warna Hexcode', 'trim|required|max_length[8]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_tipe' => $this->input->post('nama_tipe'),
				'warna_hexcode' => $this->input->post('warna_hexcode'),
			];

			
			$save_tipe_kalender = $this->model_tipe_kalender->store($save_data);
            

			if ($save_tipe_kalender) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tipe_kalender;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tipe_kalender/edit/' . $save_tipe_kalender, 'Edit Tipe Kalender'),
						anchor('administrator/tipe_kalender', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tipe_kalender/edit/' . $save_tipe_kalender, 'Edit Tipe Kalender')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tipe_kalender');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tipe_kalender');
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
	* Update view Tipe Kalenders
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tipe_kalender_update');

		$this->data['tipe_kalender'] = $this->model_tipe_kalender->find($id);

		$this->template->title('Tipe Agenda Update');
		$this->render('backend/standart/administrator/tipe_kalender/tipe_kalender_update', $this->data);
	}

	/**
	* Update Tipe Kalenders
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tipe_kalender_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_tipe', 'Nama Tipe', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('warna_hexcode', 'Warna Hexcode', 'trim|required|max_length[8]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_tipe' => $this->input->post('nama_tipe'),
				'warna_hexcode' => $this->input->post('warna_hexcode'),
			];

			
			$save_tipe_kalender = $this->model_tipe_kalender->change($id, $save_data);

			if ($save_tipe_kalender) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tipe_kalender', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tipe_kalender');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tipe_kalender');
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
	* delete Tipe Kalenders
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tipe_kalender_delete');

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
            set_message(cclang('has_been_deleted', 'tipe_kalender'), 'success');
        } else {
            set_message(cclang('error_delete', 'tipe_kalender'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tipe Kalenders
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tipe_kalender_view');

		$this->data['tipe_kalender'] = $this->model_tipe_kalender->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tipe Agenda Detail');
		$this->render('backend/standart/administrator/tipe_kalender/tipe_kalender_view', $this->data);
	}
	
	/**
	* delete Tipe Kalenders
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tipe_kalender = $this->model_tipe_kalender->find($id);

		
		
		return $this->model_tipe_kalender->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tipe_kalender_export');

		$this->model_tipe_kalender->export('tipe_kalender', 'tipe_kalender');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tipe_kalender_export');

		$this->model_tipe_kalender->pdf('tipe_kalender', 'tipe_kalender');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tipe_kalender_export');

		$table = $title = 'tipe_kalender';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tipe_kalender->find($id);
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


/* End of file tipe_kalender.php */
/* Location: ./application/controllers/administrator/Tipe Kalender.php */