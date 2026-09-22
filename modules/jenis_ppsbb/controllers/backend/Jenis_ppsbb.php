<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jenis Ppsbb Controller
*| --------------------------------------------------------------------------
*| Jenis Ppsbb site
*|
*/
class Jenis_ppsbb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jenis_ppsbb');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jenis Ppsbbs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jenis_ppsbb_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jenis_ppsbbs'] = $this->model_jenis_ppsbb->get($filter, $field, $this->limit_page, $offset);
		$this->data['jenis_ppsbb_counts'] = $this->model_jenis_ppsbb->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jenis_ppsbb/index/',
			'total_rows'   => $this->model_jenis_ppsbb->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jenis PPSBB List');
		$this->render('backend/standart/administrator/jenis_ppsbb/jenis_ppsbb_list', $this->data);
	}
	
	/**
	* Add new jenis_ppsbbs
	*
	*/
	public function add()
	{
		$this->is_allowed('jenis_ppsbb_add');

		$this->template->title('Jenis PPSBB New');
		$this->render('backend/standart/administrator/jenis_ppsbb/jenis_ppsbb_add', $this->data);
	}

	/**
	* Add New Jenis Ppsbbs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jenis_ppsbb_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenis_ppsbb', 'Jenis Ppsbb', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_ppsbb' => $this->input->post('jenis_ppsbb'),
			];

			
			$save_jenis_ppsbb = $this->model_jenis_ppsbb->store($save_data);
            

			if ($save_jenis_ppsbb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jenis_ppsbb;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jenis_ppsbb/edit/' . $save_jenis_ppsbb, 'Edit Jenis Ppsbb'),
						anchor('administrator/jenis_ppsbb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jenis_ppsbb/edit/' . $save_jenis_ppsbb, 'Edit Jenis Ppsbb')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_ppsbb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_ppsbb');
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
	* Update view Jenis Ppsbbs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jenis_ppsbb_update');

		$this->data['jenis_ppsbb'] = $this->model_jenis_ppsbb->find($id);

		$this->template->title('Jenis PPSBB Update');
		$this->render('backend/standart/administrator/jenis_ppsbb/jenis_ppsbb_update', $this->data);
	}

	/**
	* Update Jenis Ppsbbs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jenis_ppsbb_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenis_ppsbb', 'Jenis Ppsbb', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenis_ppsbb' => $this->input->post('jenis_ppsbb'),
			];

			
			$save_jenis_ppsbb = $this->model_jenis_ppsbb->change($id, $save_data);

			if ($save_jenis_ppsbb) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jenis_ppsbb', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jenis_ppsbb');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jenis_ppsbb');
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
	* delete Jenis Ppsbbs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jenis_ppsbb_delete');

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
            set_message(cclang('has_been_deleted', 'jenis_ppsbb'), 'success');
        } else {
            set_message(cclang('error_delete', 'jenis_ppsbb'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jenis Ppsbbs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jenis_ppsbb_view');

		$this->data['jenis_ppsbb'] = $this->model_jenis_ppsbb->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jenis PPSBB Detail');
		$this->render('backend/standart/administrator/jenis_ppsbb/jenis_ppsbb_view', $this->data);
	}
	
	/**
	* delete Jenis Ppsbbs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jenis_ppsbb = $this->model_jenis_ppsbb->find($id);

		
		
		return $this->model_jenis_ppsbb->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jenis_ppsbb_export');

		$this->model_jenis_ppsbb->export('jenis_ppsbb', 'jenis_ppsbb');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jenis_ppsbb_export');

		$this->model_jenis_ppsbb->pdf('jenis_ppsbb', 'jenis_ppsbb');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jenis_ppsbb_export');

		$table = $title = 'jenis_ppsbb';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jenis_ppsbb->find($id);
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


/* End of file jenis_ppsbb.php */
/* Location: ./application/controllers/administrator/Jenis Ppsbb.php */