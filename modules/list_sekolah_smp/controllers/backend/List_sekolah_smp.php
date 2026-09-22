<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| List Sekolah Smp Controller
*| --------------------------------------------------------------------------
*| List Sekolah Smp site
*|
*/
class List_sekolah_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_list_sekolah_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all List Sekolah Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('list_sekolah_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['list_sekolah_smps'] = $this->model_list_sekolah_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['list_sekolah_smp_counts'] = $this->model_list_sekolah_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/list_sekolah_smp/index/',
			'total_rows'   => $this->model_list_sekolah_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Daftar Sekolah Asal SMP List');
		$this->render('backend/standart/administrator/list_sekolah_smp/list_sekolah_smp_list', $this->data);
	}
	
	/**
	* Add new list_sekolah_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('list_sekolah_smp_add');

		$this->template->title('Daftar Sekolah Asal SMP New');
		$this->render('backend/standart/administrator/list_sekolah_smp/list_sekolah_smp_add', $this->data);
	}

	/**
	* Add New List Sekolah Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('list_sekolah_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('no_sekolah', 'No Sekolah', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('negeri_swasta', 'Negeri Swasta', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('akreditasi', 'Akreditasi', 'trim|max_length[30]');
		

		if ($this->form_validation->run()) {
	
			$save_data = [
				'no_sekolah' => $this->input->post('no_sekolah'),
				'nama_sekolah' => $this->input->post('nama_sekolah'),
				'negeri_swasta' => $this->input->post('negeri_swasta'),
				'lokasi' => $this->input->post('lokasi'),
				'akreditasi' => $this->input->post('akreditasi'),
			];

			
			$save_list_sekolah_smp = $this->model_list_sekolah_smp->store($save_data);
            

			if ($save_list_sekolah_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_list_sekolah_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/list_sekolah_smp/edit/' . $save_list_sekolah_smp, 'Edit List Sekolah Smp'),
						anchor('administrator/list_sekolah_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/list_sekolah_smp/edit/' . $save_list_sekolah_smp, 'Edit List Sekolah Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/list_sekolah_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/list_sekolah_smp');
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
	* Update view List Sekolah Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('list_sekolah_smp_update');

		$this->data['list_sekolah_smp'] = $this->model_list_sekolah_smp->find($id);

		$this->template->title('Daftar Sekolah Asal SMP Update');
		$this->render('backend/standart/administrator/list_sekolah_smp/list_sekolah_smp_update', $this->data);
	}

	/**
	* Update List Sekolah Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('list_sekolah_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('no_sekolah', 'No Sekolah', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('negeri_swasta', 'Negeri Swasta', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('akreditasi', 'Akreditasi', 'trim|max_length[30]');
		
		if ($this->form_validation->run()) {
	
			$save_data = [
				'no_sekolah' => $this->input->post('no_sekolah'),
				'nama_sekolah' => $this->input->post('nama_sekolah'),
				'negeri_swasta' => $this->input->post('negeri_swasta'),
				'lokasi' => $this->input->post('lokasi'),
				'akreditasi' => $this->input->post('akreditasi'),
				'is_show' => $this->input->post('is_show'),
			];

			
			$save_list_sekolah_smp = $this->model_list_sekolah_smp->change($id, $save_data);

			if ($save_list_sekolah_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/list_sekolah_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/list_sekolah_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/list_sekolah_smp');
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
	* delete List Sekolah Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('list_sekolah_smp_delete');

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
            set_message(cclang('has_been_deleted', 'list_sekolah_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'list_sekolah_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view List Sekolah Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('list_sekolah_smp_view');

		$this->data['list_sekolah_smp'] = $this->model_list_sekolah_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Daftar Sekolah Asal SMP Detail');
		$this->render('backend/standart/administrator/list_sekolah_smp/list_sekolah_smp_view', $this->data);
	}
	
	/**
	* delete List Sekolah Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$list_sekolah_smp = $this->model_list_sekolah_smp->find($id);

		
		
		return $this->model_list_sekolah_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('list_sekolah_smp_export');

		$this->model_list_sekolah_smp->export('list_sekolah_smp', 'list_sekolah_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('list_sekolah_smp_export');

		$this->model_list_sekolah_smp->pdf('list_sekolah_smp', 'list_sekolah_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('list_sekolah_smp_export');

		$table = $title = 'list_sekolah_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_list_sekolah_smp->find($id);
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

	/**
	* Delete all List Sekolah Smps (truncate) for re-import workflow
	*
	* Requires ?confirm=yes double confirmation
	*/
	public function delete_all()
	{
		$this->is_allowed('list_sekolah_smp_delete');

		if ($this->input->get('confirm') !== 'yes') {
			set_message(cclang('error_delete', 'list_sekolah_smp'), 'error');
			redirect_back();
		}

		$this->db->truncate('list_sekolah_smp');

		set_message(cclang('has_been_deleted', 'all list_sekolah_smp'), 'success');
		redirect_back();
	}

	
}


/* End of file list_sekolah_smp.php */
/* Location: ./application/controllers/administrator/List Sekolah Smp.php */