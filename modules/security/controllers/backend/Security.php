<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Security Controller
*| --------------------------------------------------------------------------
*| Security site
*|
*/
class Security extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_security');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Securitys
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('security_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['securitys'] = $this->model_security->get($filter, $field, $this->limit_page, $offset);
		$this->data['security_counts'] = $this->model_security->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/security/index/',
			'total_rows'   => $this->model_security->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Security List');
		$this->render('backend/standart/administrator/security/security_list', $this->data);
	}
	
	/**
	* Add new securitys
	*
	*/
	public function add()
	{
		$this->is_allowed('security_add');

		$this->template->title('Security New');
		$this->render('backend/standart/administrator/security/security_add', $this->data);
	}

	/**
	* Add New Securitys
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('security_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nik', 'NIK', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'NUPTK', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|max_length[255]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('id_posisi', 'Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('token', 'Token', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim|max_length[50]');
		$this->form_validation->set_rules('no_kk', 'No Kk', 'trim|max_length[50]');
		$this->form_validation->set_rules('presensi_role', 'Role', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('device_id', 'Device Id', 'trim|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nik' => $this->input->post('nik'),
				'nuptk' => $this->input->post('nuptk'),
				'npp' => $this->input->post('npp'),
				'no_telp' => $this->input->post('no_telp'),
				'id_posisi' => $this->input->post('id_posisi'),
				'unit' => $this->input->post('unit'),
				'status_kepegawaian' => $this->input->post('status_kepegawaian'),
				'emp_code' => $this->input->post('emp_code'),
				'token' => $this->input->post('token'),
				'token_expired' => $this->input->post('token_expired'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'no_kk' => $this->input->post('no_kk'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'foto_profil' => $this->input->post('foto_profil'),
				'presensi_role' => $this->input->post('presensi_role'),
				'device_id' => $this->input->post('device_id'),
				'created_at' => $this->input->post('created_at'),
				'deleted_at' => $this->input->post('deleted_at'),
			];

			
			$save_security = $this->model_security->store($save_data);
            

			if ($save_security) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_security;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/security/edit/' . $save_security, 'Edit Security'),
						anchor('administrator/security', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/security/edit/' . $save_security, 'Edit Security')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/security');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/security');
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
	* Update view Securitys
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('security_update');

		$this->data['security'] = $this->model_security->find($id);

		$this->template->title('Security Update');
		$this->render('backend/standart/administrator/security/security_update', $this->data);
	}

	/**
	* Update Securitys
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('security_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nik', 'NIK', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'NUPTK', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|max_length[255]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('id_posisi', 'Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('token', 'Token', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim|max_length[50]');
		$this->form_validation->set_rules('no_kk', 'No Kk', 'trim|max_length[50]');
		$this->form_validation->set_rules('presensi_role', 'Role', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('device_id', 'Device Id', 'trim|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nik' => $this->input->post('nik'),
				'nuptk' => $this->input->post('nuptk'),
				'npp' => $this->input->post('npp'),
				'no_telp' => $this->input->post('no_telp'),
				'id_posisi' => $this->input->post('id_posisi'),
				'unit' => $this->input->post('unit'),
				'status_kepegawaian' => $this->input->post('status_kepegawaian'),
				'emp_code' => $this->input->post('emp_code'),
				'token' => $this->input->post('token'),
				'token_expired' => $this->input->post('token_expired'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'no_kk' => $this->input->post('no_kk'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'foto_profil' => $this->input->post('foto_profil'),
				'presensi_role' => $this->input->post('presensi_role'),
				'device_id' => $this->input->post('device_id'),
				'created_at' => $this->input->post('created_at'),
				'deleted_at' => $this->input->post('deleted_at'),
			];

			
			$save_security = $this->model_security->change($id, $save_data);

			if ($save_security) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/security', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/security');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/security');
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
	* delete Securitys
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('security_delete');

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
            set_message(cclang('has_been_deleted', 'security'), 'success');
        } else {
            set_message(cclang('error_delete', 'security'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Securitys
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('security_view');

		$this->data['security'] = $this->model_security->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Security Detail');
		$this->render('backend/standart/administrator/security/security_view', $this->data);
	}
	
	/**
	* delete Securitys
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$security = $this->model_security->find($id);

		
		
		return $this->model_security->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('security_export');

		$this->model_security->export('security', 'security');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('security_export');

		$this->model_security->pdf('security', 'security');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('security_export');

		$table = $title = 'security';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_security->find($id);
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


/* End of file security.php */
/* Location: ./application/controllers/administrator/Security.php */