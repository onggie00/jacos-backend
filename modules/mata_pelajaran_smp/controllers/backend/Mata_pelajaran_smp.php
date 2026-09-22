<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Mata Pelajaran Smp Controller
*| --------------------------------------------------------------------------
*| Mata Pelajaran Smp site
*|
*/
class Mata_pelajaran_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mata_pelajaran_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Mata Pelajaran Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mata_pelajaran_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['mata_pelajaran_smps'] = $this->model_mata_pelajaran_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['mata_pelajaran_smp_counts'] = $this->model_mata_pelajaran_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/mata_pelajaran_smp/index/',
			'total_rows'   => $this->model_mata_pelajaran_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Mata Pelajaran Smp List');
		$this->render('backend/standart/administrator/mata_pelajaran_smp/mata_pelajaran_smp_list', $this->data);
	}
	
	/**
	* Add new mata_pelajaran_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('mata_pelajaran_smp_add');

		$this->template->title('Mata Pelajaran Smp New');
		$this->render('backend/standart/administrator/mata_pelajaran_smp/mata_pelajaran_smp_add', $this->data);
	}

	/**
	* Add New Mata Pelajaran Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('mata_pelajaran_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_mapel', 'Nama Mapel', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kode_mapel', 'Kode Mapel', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_mapel' => $this->input->post('nama_mapel'),
				'kode_mapel' => $this->input->post('kode_mapel'),
			];

			
			$save_mata_pelajaran_smp = $this->model_mata_pelajaran_smp->store($save_data);
            

			if ($save_mata_pelajaran_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_mata_pelajaran_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/mata_pelajaran_smp/edit/' . $save_mata_pelajaran_smp, 'Edit Mata Pelajaran Smp'),
						anchor('administrator/mata_pelajaran_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/mata_pelajaran_smp/edit/' . $save_mata_pelajaran_smp, 'Edit Mata Pelajaran Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mata_pelajaran_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mata_pelajaran_smp');
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
	* Update view Mata Pelajaran Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('mata_pelajaran_smp_update');

		$this->data['mata_pelajaran_smp'] = $this->model_mata_pelajaran_smp->find($id);

		$this->template->title('Mata Pelajaran Smp Update');
		$this->render('backend/standart/administrator/mata_pelajaran_smp/mata_pelajaran_smp_update', $this->data);
	}

	/**
	* Update Mata Pelajaran Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('mata_pelajaran_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_mapel', 'Nama Mapel', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kode_mapel', 'Kode Mapel', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_mapel' => $this->input->post('nama_mapel'),
				'kode_mapel' => $this->input->post('kode_mapel'),
			];

			
			$save_mata_pelajaran_smp = $this->model_mata_pelajaran_smp->change($id, $save_data);

			if ($save_mata_pelajaran_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/mata_pelajaran_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mata_pelajaran_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/mata_pelajaran_smp');
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
	* delete Mata Pelajaran Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mata_pelajaran_smp_delete');

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
            set_message(cclang('has_been_deleted', 'mata_pelajaran_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'mata_pelajaran_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Mata Pelajaran Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('mata_pelajaran_smp_view');

		$this->data['mata_pelajaran_smp'] = $this->model_mata_pelajaran_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Mata Pelajaran Smp Detail');
		$this->render('backend/standart/administrator/mata_pelajaran_smp/mata_pelajaran_smp_view', $this->data);
	}
	
	/**
	* delete Mata Pelajaran Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$mata_pelajaran_smp = $this->model_mata_pelajaran_smp->find($id);

		
		
		return $this->model_mata_pelajaran_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('mata_pelajaran_smp_export');

		$this->model_mata_pelajaran_smp->export('mata_pelajaran_smp', 'mata_pelajaran_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('mata_pelajaran_smp_export');

		$this->model_mata_pelajaran_smp->pdf('mata_pelajaran_smp', 'mata_pelajaran_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('mata_pelajaran_smp_export');

		$table = $title = 'mata_pelajaran_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_mata_pelajaran_smp->find($id);
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


/* End of file mata_pelajaran_smp.php */
/* Location: ./application/controllers/administrator/Mata Pelajaran Smp.php */