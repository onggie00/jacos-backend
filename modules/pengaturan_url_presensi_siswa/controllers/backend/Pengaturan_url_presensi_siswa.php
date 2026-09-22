<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Url Presensi Siswa Controller
*| --------------------------------------------------------------------------
*| Pengaturan Url Presensi Siswa site
*|
*/
class Pengaturan_url_presensi_siswa extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_url_presensi_siswa');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Url Presensi Siswas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_url_presensi_siswas'] = $this->model_pengaturan_url_presensi_siswa->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_url_presensi_siswa_counts'] = $this->model_pengaturan_url_presensi_siswa->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_url_presensi_siswa/index/',
			'total_rows'   => $this->model_pengaturan_url_presensi_siswa->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Url Presensi Siswa List');
		$this->render('backend/standart/administrator/pengaturan_url_presensi_siswa/pengaturan_url_presensi_siswa_list', $this->data);
	}
	
	/**
	* Add new pengaturan_url_presensi_siswas
	*
	*/
	public function add()
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_add');

		$this->template->title('Pengaturan Url Presensi Siswa New');
		$this->render('backend/standart/administrator/pengaturan_url_presensi_siswa/pengaturan_url_presensi_siswa_add', $this->data);
	}

	/**
	* Add New Pengaturan Url Presensi Siswas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pengaturan_url_presensi_siswa_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('url', 'Url', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'url' => $this->input->post('url'),
				'jenjang' => $this->input->post('jenjang'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
			];

			
			$save_pengaturan_url_presensi_siswa = $this->model_pengaturan_url_presensi_siswa->store($save_data);
            

			if ($save_pengaturan_url_presensi_siswa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pengaturan_url_presensi_siswa;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pengaturan_url_presensi_siswa/edit/' . $save_pengaturan_url_presensi_siswa, 'Edit Pengaturan Url Presensi Siswa'),
						anchor('administrator/pengaturan_url_presensi_siswa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pengaturan_url_presensi_siswa/edit/' . $save_pengaturan_url_presensi_siswa, 'Edit Pengaturan Url Presensi Siswa')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_url_presensi_siswa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_url_presensi_siswa');
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
	* Update view Pengaturan Url Presensi Siswas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_update');

		$this->data['pengaturan_url_presensi_siswa'] = $this->model_pengaturan_url_presensi_siswa->find($id);

		$this->template->title('Pengaturan Url Presensi Siswa Update');
		$this->render('backend/standart/administrator/pengaturan_url_presensi_siswa/pengaturan_url_presensi_siswa_update', $this->data);
	}

	/**
	* Update Pengaturan Url Presensi Siswas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_url_presensi_siswa_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('url', 'Url', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'url' => $this->input->post('url'),
				'jenjang' => $this->input->post('jenjang'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
			];

			
			$save_pengaturan_url_presensi_siswa = $this->model_pengaturan_url_presensi_siswa->change($id, $save_data);

			if ($save_pengaturan_url_presensi_siswa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_url_presensi_siswa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_url_presensi_siswa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_url_presensi_siswa');
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
	* delete Pengaturan Url Presensi Siswas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_delete');

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
            set_message(cclang('has_been_deleted', 'pengaturan_url_presensi_siswa'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_url_presensi_siswa'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pengaturan Url Presensi Siswas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_view');

		$this->data['pengaturan_url_presensi_siswa'] = $this->model_pengaturan_url_presensi_siswa->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Url Presensi Siswa Detail');
		$this->render('backend/standart/administrator/pengaturan_url_presensi_siswa/pengaturan_url_presensi_siswa_view', $this->data);
	}
	
	/**
	* delete Pengaturan Url Presensi Siswas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_url_presensi_siswa = $this->model_pengaturan_url_presensi_siswa->find($id);

		
		
		return $this->model_pengaturan_url_presensi_siswa->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_export');

		$this->model_pengaturan_url_presensi_siswa->export('pengaturan_url_presensi_siswa', 'pengaturan_url_presensi_siswa');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_export');

		$this->model_pengaturan_url_presensi_siswa->pdf('pengaturan_url_presensi_siswa', 'pengaturan_url_presensi_siswa');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pengaturan_url_presensi_siswa_export');

		$table = $title = 'pengaturan_url_presensi_siswa';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pengaturan_url_presensi_siswa->find($id);
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


/* End of file pengaturan_url_presensi_siswa.php */
/* Location: ./application/controllers/administrator/Pengaturan Url Presensi Siswa.php */