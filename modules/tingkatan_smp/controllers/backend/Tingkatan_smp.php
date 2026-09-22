<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tingkatan Smp Controller
*| --------------------------------------------------------------------------
*| Tingkatan Smp site
*|
*/
class Tingkatan_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tingkatan_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tingkatan Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tingkatan_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tingkatan_smps'] = $this->model_tingkatan_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['tingkatan_smp_counts'] = $this->model_tingkatan_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tingkatan_smp/index/',
			'total_rows'   => $this->model_tingkatan_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tingkatan Smp List');
		$this->render('backend/standart/administrator/tingkatan_smp/tingkatan_smp_list', $this->data);
	}
	
	/**
	* Add new tingkatan_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('tingkatan_smp_add');

		$this->template->title('Tingkatan Smp New');
		$this->render('backend/standart/administrator/tingkatan_smp/tingkatan_smp_add', $this->data);
	}

	/**
	* Add New Tingkatan Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tingkatan_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya Spp', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'biaya_spp' => $this->input->post('biaya_spp'),
			];

			
			$save_tingkatan_smp = $this->model_tingkatan_smp->store($save_data);
            

			if ($save_tingkatan_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tingkatan_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tingkatan_smp/edit/' . $save_tingkatan_smp, 'Edit Tingkatan Smp'),
						anchor('administrator/tingkatan_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tingkatan_smp/edit/' . $save_tingkatan_smp, 'Edit Tingkatan Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tingkatan_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tingkatan_smp');
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
	* Update view Tingkatan Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tingkatan_smp_update');

		$this->data['tingkatan_smp'] = $this->model_tingkatan_smp->find($id);

		$this->template->title('Tingkatan Smp Update');
		$this->render('backend/standart/administrator/tingkatan_smp/tingkatan_smp_update', $this->data);
	}

	/**
	* Update Tingkatan Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tingkatan_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('biaya_spp', 'Biaya Spp', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'label' => $this->input->post('label'),
				'biaya_spp' => $this->input->post('biaya_spp'),
			];

			
			$save_tingkatan_smp = $this->model_tingkatan_smp->change($id, $save_data);

			if ($save_tingkatan_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tingkatan_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tingkatan_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tingkatan_smp');
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
	* delete Tingkatan Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tingkatan_smp_delete');

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
            set_message(cclang('has_been_deleted', 'tingkatan_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'tingkatan_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tingkatan Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tingkatan_smp_view');

		$this->data['tingkatan_smp'] = $this->model_tingkatan_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tingkatan Smp Detail');
		$this->render('backend/standart/administrator/tingkatan_smp/tingkatan_smp_view', $this->data);
	}
	
	/**
	* delete Tingkatan Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tingkatan_smp = $this->model_tingkatan_smp->find($id);

		
		
		return $this->model_tingkatan_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tingkatan_smp_export');

		$this->model_tingkatan_smp->export('tingkatan_smp', 'tingkatan_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tingkatan_smp_export');

		$this->model_tingkatan_smp->pdf('tingkatan_smp', 'tingkatan_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tingkatan_smp_export');

		$table = $title = 'tingkatan_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tingkatan_smp->find($id);
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


/* End of file tingkatan_smp.php */
/* Location: ./application/controllers/administrator/Tingkatan Smp.php */