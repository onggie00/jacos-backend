<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apps Position Controller
*| --------------------------------------------------------------------------
*| Apps Position site
*|
*/
class Apps_position extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_position');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apps Positions
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_position_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_positions'] = $this->model_apps_position->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_position_counts'] = $this->model_apps_position->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_position/index/',
			'total_rows'   => $this->model_apps_position->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Apps Position List');
		$this->render('backend/standart/administrator/apps_position/apps_position_list', $this->data);
	}
	
	/**
	* Add new apps_positions
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_position_add');

		$this->template->title('Apps Position New');
		$this->render('backend/standart/administrator/apps_position/apps_position_add', $this->data);
	}

	/**
	* Add New Apps Positions
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_position_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('position', 'Position', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('horizontal_vertical', 'Horizontal Vertical', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'position' => $this->input->post('position'),
				'horizontal_vertical' => $this->input->post('horizontal_vertical'),
				'keterangan' => $this->input->post('keterangan'),
			];

			
			$save_apps_position = $this->model_apps_position->store($save_data);
            

			if ($save_apps_position) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_position;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_position/edit/' . $save_apps_position, 'Edit Apps Position'),
						anchor('administrator/apps_position', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_position/edit/' . $save_apps_position, 'Edit Apps Position')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_position');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_position');
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
	* Update view Apps Positions
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_position_update');

		$this->data['apps_position'] = $this->model_apps_position->find($id);

		$this->template->title('Apps Position Update');
		$this->render('backend/standart/administrator/apps_position/apps_position_update', $this->data);
	}

	/**
	* Update Apps Positions
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_position_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('position', 'Position', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('horizontal_vertical', 'Horizontal Vertical', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'position' => $this->input->post('position'),
				'horizontal_vertical' => $this->input->post('horizontal_vertical'),
				'keterangan' => $this->input->post('keterangan'),
			];

			
			$save_apps_position = $this->model_apps_position->change($id, $save_data);

			if ($save_apps_position) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_position', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_position');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_position');
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
	* delete Apps Positions
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_position_delete');

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
            set_message(cclang('has_been_deleted', 'apps_position'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_position'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apps Positions
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_position_view');

		$this->data['apps_position'] = $this->model_apps_position->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Apps Position Detail');
		$this->render('backend/standart/administrator/apps_position/apps_position_view', $this->data);
	}
	
	/**
	* delete Apps Positions
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_position = $this->model_apps_position->find($id);

		
		
		return $this->model_apps_position->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_position_export');

		$this->model_apps_position->export('apps_position', 'apps_position');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_position_export');

		$this->model_apps_position->pdf('apps_position', 'apps_position');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_position_export');

		$table = $title = 'apps_position';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_position->find($id);
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


/* End of file apps_position.php */
/* Location: ./application/controllers/administrator/Apps Position.php */