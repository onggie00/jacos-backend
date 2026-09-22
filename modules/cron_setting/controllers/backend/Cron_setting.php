<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Cron Setting Controller
*| --------------------------------------------------------------------------
*| Cron Setting site
*|
*/
class Cron_setting extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_cron_setting');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Cron Settings
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('cron_setting_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['cron_settings'] = $this->model_cron_setting->get($filter, $field, $this->limit_page, $offset);
		$this->data['cron_setting_counts'] = $this->model_cron_setting->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/cron_setting/index/',
			'total_rows'   => $this->model_cron_setting->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Cron Setting List');
		$this->render('backend/standart/administrator/cron_setting/cron_setting_list', $this->data);
	}
	
	/**
	* Add new cron_settings
	*
	*/
	public function add()
	{
		$this->is_allowed('cron_setting_add');

		$this->template->title('Cron Setting New');
		$this->render('backend/standart/administrator/cron_setting/cron_setting_add', $this->data);
	}

	/**
	* Add New Cron Settings
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('cron_setting_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_cron', 'Nama CRON', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_active', 'Aktif?', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('updated_by', 'Diupdate Oleh?', 'trim|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_cron' => $this->input->post('nama_cron'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'jenjang' => $this->input->post('jenjang'),
				'id_kelas' => $this->input->post('id_kelas'),
				'is_active' => $this->input->post('is_active'),
				'date_deactivate' => $this->input->post('date_deactivate'),
				'date_reactivate' => $this->input->post('date_reactivate'),
				'updated_at' => $this->input->post('updated_at'),
				'updated_by' => $this->input->post('updated_by'),
			];

			
			$save_cron_setting = $this->model_cron_setting->store($save_data);
            

			if ($save_cron_setting) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_cron_setting;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/cron_setting/edit/' . $save_cron_setting, 'Edit Cron Setting'),
						anchor('administrator/cron_setting', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/cron_setting/edit/' . $save_cron_setting, 'Edit Cron Setting')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/cron_setting');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/cron_setting');
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
	* Update view Cron Settings
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('cron_setting_update');

		$this->data['cron_setting'] = $this->model_cron_setting->find($id);

		$this->template->title('Cron Setting Update');
		$this->render('backend/standart/administrator/cron_setting/cron_setting_update', $this->data);
	}

	/**
	* Update Cron Settings
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('cron_setting_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_cron', 'Nama CRON', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_active', 'Aktif?', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('updated_by', 'Diupdate Oleh?', 'trim|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_cron' => $this->input->post('nama_cron'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'jenjang' => $this->input->post('jenjang'),
				'id_kelas' => $this->input->post('id_kelas'),
				'is_active' => $this->input->post('is_active'),
				'date_deactivate' => $this->input->post('date_deactivate'),
				'date_reactivate' => $this->input->post('date_reactivate'),
				'updated_at' => $this->input->post('updated_at'),
				'updated_by' => $this->input->post('updated_by'),
			];

			
			$save_cron_setting = $this->model_cron_setting->change($id, $save_data);

			if ($save_cron_setting) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/cron_setting', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/cron_setting');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/cron_setting');
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
	* delete Cron Settings
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('cron_setting_delete');

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
            set_message(cclang('has_been_deleted', 'cron_setting'), 'success');
        } else {
            set_message(cclang('error_delete', 'cron_setting'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Cron Settings
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('cron_setting_view');

		$this->data['cron_setting'] = $this->model_cron_setting->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Cron Setting Detail');
		$this->render('backend/standart/administrator/cron_setting/cron_setting_view', $this->data);
	}
	
	/**
	* delete Cron Settings
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$cron_setting = $this->model_cron_setting->find($id);

		
		
		return $this->model_cron_setting->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('cron_setting_export');

		$this->model_cron_setting->export('cron_setting', 'cron_setting');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('cron_setting_export');

		$this->model_cron_setting->pdf('cron_setting', 'cron_setting');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('cron_setting_export');

		$table = $title = 'cron_setting';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_cron_setting->find($id);
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

	public function set_cron(){
		$post = $this->input->post();
		//print_r($post);
		$kelas_sd = (empty($post['id_kelas_sd'])) ? null : $post['id_kelas_sd'];
		$kelas_smp = (empty($post['id_kelas_smp'])) ? null : $post['id_kelas_smp'];
		$kelas_sma = (empty($post['id_kelas_sma'])) ? null : $post['id_kelas_sma'];
		$kelas_ft = (empty($post['id_kelas_ft'])) ? null : $post['id_kelas_ft'];

		$jenjang = $post['jenjang'];
		$where_sd = "";
		$where_smp = "";
		$where_sma = "";
		$where_ft = "";
		if (!empty($kelas_sd)) {
			for ($i=0; $i < count($kelas_sd); $i++) {
				if($where_sd == "" && $kelas_sd[$i] != null){
					$where_sd = " and  id_kelas = '".$kelas_sd[$i]."'";
				}
				else{
					$where_sd .= " or id_kelas = '".$kelas_sd[$i]."'";
				}
			}
		}
		if (!empty($kelas_smp)) {
			for ($i=0; $i < count($kelas_smp); $i++) {
				if($where_smp == "" && $kelas_smp[$i] != null){
					$where_smp = " and  id_kelas = '".$kelas_smp[$i]."'";
				}
				else{
					$where_smp .= " or id_kelas = '".$kelas_smp[$i]."'";
				}
			}
		}
		
		if (!empty($kelas_sma)) {
			for ($i=0; $i < count($kelas_sma); $i++) {
				if($where_sma == "" && $kelas_sma[$i] != null){
					$where_sma = " and  id_kelas = '".$kelas_sma[$i]."'";
				}
				else{
					$where_sma .= " or id_kelas = '".$kelas_sma[$i]."'";
				}
			}
		}
		
		if (!empty($kelas_ft)) {
			for ($i=0; $i < count($kelas_ft); $i++) {
				if($where_ft == "" && $kelas_ft[$i] != null){
					$where_ft = " and  id_kelas = '".$kelas_ft[$i]."'";
				}
				else{
					$where_ft .= " or id_kelas = '".$kelas_ft[$i]."'";
				}
			}
		}
		
		$data = array(
			"nama_cron" => "cron_presensi_siswa",
		);
		//sd
		if ($where_sd != "") {
			$get_siswa_sd = $this->mymodel->withquery("select id_siswa_sd_aktif, id_kelas from siswa_sd_aktif sa 
			join kelas_sd k on sa.id_kelas = k.id_kelas_sd 
			where (k.label not like '%alumni%' and k.label not like '%mutasi%') 
			".$where_sd."","result");
			//delete all setting based on setting before
			//$delete_sd = $this->mymodel->withquery("delete from cron_setting where jenjang = 'sd' ".$where_sd." ","result");
			$delete = $this->mymodel->delete("cron_setting", str_replace("and", "", $where_sd)." and jenjang=", "sd");

			if (!empty($get_siswa_sd)){
				foreach ($get_siswa_sd as $key => $value) {
					//insert cron setting sd
					$data['jenjang'] = "sd";
					$data['id_kelas'] = $value->id_kelas;
					$data['id_siswa_aktif'] = $value->id_siswa_sd_aktif;
					if (!empty($post['is_active'])) {
						$data['is_active'] = $post['is_active'];
					}
					if (!empty($post['date_deactivate'])) {
						$data['date_deactivate'] = $post['date_deactivate'];
					}
					if (!empty($post['date_reactivate'])) {
						$data['date_reactivate'] = $post['date_reactivate'];
					}
					$this->mymodel->insert("cron_setting",$data);
				}
				$this->session->set_flashdata('message', 'Data berhasil disimpan');
			}
			else{
				$this->session->set_flashdata('message', 'Data tidak ditemukan');
			}
		}

		//smp
		if ($where_smp != ""){
			$get_siswa_smp = $this->mymodel->withquery("select id_siswa_smp_aktif, id_kelas from siswa_smp_aktif sa 
			join kelas_smp k on sa.id_kelas = k.id_kelas_smp 
			where (k.label not like '%alumni%' and k.label not like '%mutasi%') 
			".$where_smp."","result");
			//delete all setting based on setting before
			//$delete_smp = $this->mymodel->withquery("delete from cron_setting where jenjang = 'smp' ".$where_smp." ","result");
			$delete = $this->mymodel->delete("cron_setting", str_replace("and", "", $where_smp)." and jenjang=", "smp");

			if (!empty($get_siswa_smp)){
				foreach ($get_siswa_smp as $key => $value) {
					//insert cron setting smp
					$data['jenjang'] = "smp";
					$data['id_kelas'] = $value->id_kelas;
					$data['id_siswa_aktif'] = $value->id_siswa_smp_aktif;
					if (!empty($post['is_active'])) {
						$data['is_active'] = $post['is_active'];
					}
					if (!empty($post['date_deactivate'])) {
						$data['date_deactivate'] = $post['date_deactivate'];
					}
					if (!empty($post['date_reactivate'])) {
						$data['date_reactivate'] = $post['date_reactivate'];
					}
					$this->mymodel->insert("cron_setting",$data);
				}
				$this->session->set_flashdata('message', 'Data berhasil disimpan');
			}
			else{
				$this->session->set_flashdata('message', 'Data tidak ditemukan');
			}
		}

		//sma
		if ($where_sma != ""){
			$get_siswa_sma = $this->mymodel->withquery("select id_siswa_sma_aktif, id_kelas from siswa_sma_aktif sa 
			join kelas_sma k on sa.id_kelas = k.id_kelas_sma 
			where (k.label not like '%alumni%' and k.label not like '%mutasi%') 
			".$where_sma."","result");
			//delete all setting based on setting before
			//$delete_sma = $this->mymodel->withquery("delete from cron_setting where jenjang = 'sma' ".$where_sma." ","result");
			$delete = $this->mymodel->delete("cron_setting", str_replace("and", "", $where_sma)." and jenjang=", "sma");

			if (!empty($get_siswa_sma)){
				foreach ($get_siswa_sma as $key => $value) {
					//insert cron setting sma
					$data['jenjang'] = "sma";
					$data['id_kelas'] = $value->id_kelas;
					$data['id_siswa_aktif'] = $value->id_siswa_sma_aktif;
					if (!empty($post['is_active'])) {
						$data['is_active'] = $post['is_active'];
					}
					if (!empty($post['date_deactivate'])) {
						$data['date_deactivate'] = $post['date_deactivate'];
					}
					if (!empty($post['date_reactivate'])) {
						$data['date_reactivate'] = $post['date_reactivate'];
					}
					$this->mymodel->insert("cron_setting",$data);
				}
				$this->session->set_flashdata('message', 'Data berhasil disimpan');
			}
			else{
				$this->session->set_flashdata('message', 'Data tidak ditemukan');
			}
		}

		//ft
		if ($where_ft != ""){
			$get_siswa_ft = $this->mymodel->withquery("select id_siswa_ft_aktif, id_kelas from siswa_ft_aktif sa 
			join kelas_ft k on sa.id_kelas = k.id_kelas_ft 
			where (k.label not like '%alumni%' and k.label not like '%mutasi%') 
			".$where_ft."","result");
			//delete all setting based on setting before
			//$delete_ft = $this->mymodel->withquery("delete from cron_setting where jenjang = 'ft' ".$where_ft." ","result");
			$delete = $this->mymodel->delete("cron_setting", str_replace("and", "", $where_ft)." and jenjang=", "ft");

			if (!empty($get_siswa_ft)){
				foreach ($get_siswa_ft as $key => $value) {
					//insert cron setting ft
					$data['jenjang'] = "ft";
					$data['id_kelas'] = $value->id_kelas;
					$data['id_siswa_aktif'] = $value->id_siswa_ft_aktif;
					if (!empty($post['is_active'])) {
						$data['is_active'] = $post['is_active'];
					}
					if (!empty($post['date_deactivate'])) {
						$data['date_deactivate'] = $post['date_deactivate'];
					}
					if (!empty($post['date_reactivate'])) {
						$data['date_reactivate'] = $post['date_reactivate'];
					}
					$this->mymodel->insert("cron_setting",$data);
				}
				$this->session->set_flashdata('message', 'Data berhasil disimpan');
			}
			else{
				$this->session->set_flashdata('message', 'Data tidak ditemukan');
			}
		}
		
		redirect('administrator/cron_setting');
	}
}


/* End of file cron_setting.php */
/* Location: ./application/controllers/administrator/Cron Setting.php */