<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . '/vendor/autoload.php';
define( 'API_ACCESS_KEY', 'AAAAX38-FW8:APA91bGoy4cJtX9jf4kfphdyh-1EZ3VFU8GlbVzXmka4-x-c2q6-oAvoltIKeSzoW4Pz8hbUL_MT0EW6NTUctWryTgsAlAmakleTaC-QzwLocy8OaVbswc_RuCC-tUaqPKta3TiYdoJ-' );
define( 'PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');

/**
*| --------------------------------------------------------------------------
*| Pegawai Slip Controller
*| --------------------------------------------------------------------------
*| Pegawai Slip site
*|
*/
class Pegawai_slip extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pegawai_slip');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pegawai Slips
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pegawai_slip_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pegawai_slips'] = $this->model_pegawai_slip->get($filter, $field, $this->limit_page, $offset);
		$this->data['pegawai_slip_counts'] = $this->model_pegawai_slip->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pegawai_slip/index/',
			'total_rows'   => $this->model_pegawai_slip->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Slip Gaji & Tunjangan Pegawai List');
		$this->render('backend/standart/administrator/pegawai_slip/pegawai_slip_list', $this->data);
	}
	
	/**
	* Add new pegawai_slips
	*
	*/
	public function add()
	{
		$this->is_allowed('pegawai_slip_add');

		$this->template->title('Slip Gaji & Tunjangan Pegawai New');
		$this->render('backend/standart/administrator/pegawai_slip/pegawai_slip_add', $this->data);
	}

	/**
	* Add New Pegawai Slips
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pegawai_slip_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_slip', 'Id Slip', 'trim|required|max_length[11]');
		//$this->form_validation->set_rules('id_pegawai', 'Pegawai', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_istri', 'Tunjangan Istri', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_anak', 'Tunjangan Anak', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pengelolaan', 'Tunjangan Pengelolaan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_jabatan', 'Tunjangan Jabatan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_kesejahteraan', 'Tunjangan Kesejahteraan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_masa_kerja', 'Tunjangan Masa Kerja', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_fungsional', 'Tunjangan Fungsional', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_kehadiran', 'Tunjangan Kehadiran', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_mengajar', 'Tunjangan Mengajar', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_piket', 'Tunjangan Piket', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_wali_kelas', 'Tunjangan Wali Kelas', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pembina', 'Tunjangan Pembina', 'trim|max_length[20]');
		$this->form_validation->set_rules('insentif_ft', 'Insentif France Track', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_insentif', 'Tunjangan Insentif', 'trim|max_length[20]');
		$this->form_validation->set_rules('bonus', 'Bonus', 'trim|max_length[20]');
		$this->form_validation->set_rules('honor', 'Honor', 'trim|max_length[20]');
		$this->form_validation->set_rules('periode_mulai', 'Periode Mulai', 'trim|max_length[20]');
		$this->form_validation->set_rules('periode_selesai', 'Periode Selesai', 'trim|max_length[20]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_slip' => $this->input->post('id_slip'),
				'id_pegawai' => "0",
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'npp' => $this->input->post('npp'),
				'golongan' => $this->input->post('golongan'),
				'jabatan' => $this->input->post('jabatan'),
				'gaji_pokok' => $this->input->post('gaji_pokok'),
				'tunjangan_istri' => $this->input->post('tunjangan_istri'),
				'tunjangan_anak' => $this->input->post('tunjangan_anak'),
				'tunjangan_pengelolaan' => $this->input->post('tunjangan_pengelolaan'),
				'tunjangan_jabatan' => $this->input->post('tunjangan_jabatan'),
				'tunjangan_kesejahteraan' => $this->input->post('tunjangan_kesejahteraan'),
				'tunjangan_masa_kerja' => $this->input->post('tunjangan_masa_kerja'),
				'tunjangan_fungsional' => $this->input->post('tunjangan_fungsional'),
				'total_kehadiran' => $this->input->post('total_kehadiran'),
				'rupiah_per_kehadiran' => $this->input->post('rupiah_per_kehadiran'),
				'tunjangan_kehadiran' => $this->input->post('tunjangan_kehadiran'),
				'total_mengajar' => $this->input->post('total_mengajar'),
				'rupiah_per_mengajar' => $this->input->post('rupiah_per_mengajar'),
				'tunjangan_mengajar' => $this->input->post('tunjangan_mengajar'),
				'total_piket' => $this->input->post('total_piket'),
				'rupiah_per_piket' => $this->input->post('rupiah_per_piket'),
				'tunjangan_piket' => $this->input->post('tunjangan_piket'),
				'tunjangan_wali_kelas' => $this->input->post('tunjangan_wali_kelas'),
				'tunjangan_pembina' => $this->input->post('tunjangan_pembina'),
				'insentif_ft' => $this->input->post('insentif_ft'),
				'tunjangan_insentif' => $this->input->post('tunjangan_insentif'),
				'bonus' => $this->input->post('bonus'),
				'honor' => $this->input->post('honor'),
				'periode_mulai' => $this->input->post('periode_mulai'),
				'periode_selesai' => $this->input->post('periode_selesai'),
			];

			
			$save_pegawai_slip = $this->model_pegawai_slip->store($save_data);
                        $save_pegawai_slip = true;
            

			if ($save_pegawai_slip) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pegawai_slip;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pegawai_slip/edit/' . $save_pegawai_slip, 'Edit Pegawai Slip'),
						anchor('administrator/pegawai_slip', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pegawai_slip/edit/' . $save_pegawai_slip, 'Edit Pegawai Slip')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pegawai_slip');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pegawai_slip');
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
	* Update view Pegawai Slips
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pegawai_slip_update');

		$this->data['pegawai_slip'] = $this->model_pegawai_slip->find($id);

		$this->template->title('Slip Gaji & Tunjangan Pegawai Update');
		$this->render('backend/standart/administrator/pegawai_slip/pegawai_slip_update', $this->data);
	}

	/**
	* Update Pegawai Slips
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pegawai_slip_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_slip', 'Id Slip', 'trim|required|max_length[11]');
		//$this->form_validation->set_rules('id_pegawai', 'Pegawai', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_istri', 'Tunjangan Istri', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_anak', 'Tunjangan Anak', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pengelolaan', 'Tunjangan Pengelolaan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_jabatan', 'Tunjangan Jabatan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_kesejahteraan', 'Tunjangan Kesejahteraan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_masa_kerja', 'Tunjangan Masa Kerja', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_fungsional', 'Tunjangan Fungsional', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_kehadiran', 'Tunjangan Kehadiran', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_mengajar', 'Tunjangan Mengajar', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_piket', 'Tunjangan Piket', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_wali_kelas', 'Tunjangan Wali Kelas', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pembina', 'Tunjangan Pembina', 'trim|max_length[20]');
		$this->form_validation->set_rules('insentif_ft', 'Insentif France Track', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_insentif', 'Tunjangan Insentif', 'trim|max_length[20]');
		$this->form_validation->set_rules('bonus', 'Bonus', 'trim|max_length[20]');
		$this->form_validation->set_rules('honor', 'Honor', 'trim|max_length[20]');
		$this->form_validation->set_rules('periode_mulai', 'periode mulai', 'trim|max_length[20]');
		$this->form_validation->set_rules('periode_selesai', 'periode selesai', 'trim|max_length[20]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_slip' => $this->input->post('id_slip'),
				'id_pegawai' => "0",
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'npp' => $this->input->post('npp'),
				'golongan' => $this->input->post('golongan'),
				'jabatan' => $this->input->post('jabatan'),
				'gaji_pokok' => $this->input->post('gaji_pokok'),
				'tunjangan_istri' => $this->input->post('tunjangan_istri'),
				'tunjangan_anak' => $this->input->post('tunjangan_anak'),
				'tunjangan_pengelolaan' => $this->input->post('tunjangan_pengelolaan'),
				'tunjangan_jabatan' => $this->input->post('tunjangan_jabatan'),
				'tunjangan_kesejahteraan' => $this->input->post('tunjangan_kesejahteraan'),
				'tunjangan_masa_kerja' => $this->input->post('tunjangan_masa_kerja'),
				'tunjangan_fungsional' => $this->input->post('tunjangan_fungsional'),
				'total_kehadiran' => $this->input->post('total_kehadiran'),
				'rupiah_per_kehadiran' => $this->input->post('rupiah_per_kehadiran'),
				'tunjangan_kehadiran' => $this->input->post('tunjangan_kehadiran'),
				'total_mengajar' => $this->input->post('total_mengajar'),
				'rupiah_per_mengajar' => $this->input->post('rupiah_per_mengajar'),
				'tunjangan_mengajar' => $this->input->post('tunjangan_mengajar'),
				'total_piket' => $this->input->post('total_piket'),
				'rupiah_per_piket' => $this->input->post('rupiah_per_piket'),
				'tunjangan_piket' => $this->input->post('tunjangan_piket'),
				'tunjangan_wali_kelas' => $this->input->post('tunjangan_wali_kelas'),
				'tunjangan_pembina' => $this->input->post('tunjangan_pembina'),
				'insentif_ft' => $this->input->post('insentif_ft'),
				'tunjangan_insentif' => $this->input->post('tunjangan_insentif'),
				'bonus' => $this->input->post('bonus'),
				'honor' => $this->input->post('honor'),
				'periode_mulai' => $this->input->post('periode_mulai'),
				'periode_selesai' => $this->input->post('periode_selesai'),
			];

			
			$save_pegawai_slip = $this->model_pegawai_slip->change($id, $save_data);

			if ($save_pegawai_slip) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pegawai_slip', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pegawai_slip');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pegawai_slip');
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
	* delete Pegawai Slips
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pegawai_slip_delete');

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
            set_message(cclang('has_been_deleted', 'pegawai_slip'), 'success');
        } else {
            set_message(cclang('error_delete', 'pegawai_slip'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pegawai Slips
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pegawai_slip_view');

		$this->data['pegawai_slip'] = $this->model_pegawai_slip->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Slip Gaji & Tunjangan Pegawai Detail');
		$this->render('backend/standart/administrator/pegawai_slip/pegawai_slip_view', $this->data);
	}
	
	/**
	* delete Pegawai Slips
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pegawai_slip = $this->model_pegawai_slip->find($id);

		
		
		return $this->model_pegawai_slip->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export_gaji()
	{
		$this->is_allowed('pegawai_slip_export');

		//$this->model_pegawai_slip->export('pegawai_slip', 'pegawai_slip');
		$periode_mulai = $this->input->post('periode_mulai');
		$periode_selesai = $this->input->post('periode_selesai');
		$where = "";
		$field = ['nomor', 'nama_lengkap', 'npp', 'golongan', 'jabatan', 'gaji_pokok', 'tunjangan_istri', 'tunjangan_anak', 'potongan_gaji1', 'nominal_potongan_gaji1', 'potongan_gaji2', 'nominal_potongan_gaji2', 'potongan_gaji3', 'nominal_potongan_gaji3', 'potongan_gaji4', 'nominal_potongan_gaji4', 'potongan_gaji5', 'nominal_potongan_gaji5', 'potongan_gaji6', 'nominal_potongan_gaji6', 'total_penghasilan_gaji', 'total_potongan_gaji', 'total_diterima_gaji', 'kwitansi_gaji'];
		$list_slip = array();
		if (!empty($periode_mulai) && !empty($periode_selesai)) {
			$query = "select s.id_slip, s.id_pegawai, s.nama_lengkap, s.npp, s.golongan, s.jabatan, s.gaji_pokok, s.tunjangan_istri, s.tunjangan_anak, s.potongan_gaji1, s.nominal_potongan_gaji1, s.potongan_gaji2, s.nominal_potongan_gaji2, s.potongan_gaji3, s.nominal_potongan_gaji3, s.potongan_gaji4, s.nominal_potongan_gaji4, s.potongan_gaji5, s.nominal_potongan_gaji5, s.potongan_gaji6, s.nominal_potongan_gaji6 , s.total_penghasilan_gaji, s.total_potongan_gaji, s.total_diterima_gaji, s.kwitansi_gaji from pegawai_slip s 
			where s.periode_mulai = '".$periode_mulai."' and s.periode_selesai = '".$periode_selesai."'
			order by s.periode_selesai DESC, s.periode_mulai DESC, s.nama_lengkap ASC";
			$list_slip = $this->mymodel->withquery($query,"result");
		}
		else if(!empty($periode_selesai)) {
			$query = "select s.id_slip, s.id_pegawai, s.nama_lengkap, s.npp, s.golongan, s.jabatan, s.gaji_pokok, s.tunjangan_istri, s.tunjangan_anak, s.potongan_gaji1, s.nominal_potongan_gaji1, s.potongan_gaji2, s.nominal_potongan_gaji2, s.potongan_gaji3, s.nominal_potongan_gaji3, s.potongan_gaji4, s.nominal_potongan_gaji4, s.potongan_gaji5, s.nominal_potongan_gaji5, s.potongan_gaji6, s.nominal_potongan_gaji6, s.total_penghasilan_gaji, s.total_potongan_gaji, s.total_diterima_gaji, s.kwitansi_gaji from pegawai_slip s 
			where s.periode_selesai = '".$periode_selesai."'
			order by s.periode_selesai DESC, s.periode_mulai DESC, s.nama_lengkap ASC";
			$list_slip = $this->mymodel->withquery($query,"result");
			//$this->session->set_flashdata('failed', 'Data tidak valid');
		}
		else{
			/* $query = "select s.id_slip, s.id_pegawai, s.nama_lengkap, s.npp, s.golongan, s.jabatan, s.gaji_pokok, s.tunjangan_istri, s.tunjangan_anak from pegawai_slip s 
			order by s.periode_selesai DESC, s.periode_mulai DESC, s.nama_lengkap ASC"; */
			//$list_slip = $this->mymodel->withquery($query,"result");
			$this->session->set_flashdata('failed', 'Wajib memilih filter periode_mulai & periode_selesai');
			redirect('administrator/pegawai_slip');
		}
		
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
		);
		$style_bg = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
				'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '92D050')
				),
		);
		/* for($col = 'A'; $col !== 'K'; $col++) {
			$objPHPExcel->getActiveSheet()
				->getColumnDimension($col)
				->setAutoSize(true);
		} */
		$column = 'A';
		for ($i = 0; $i < count($field); $i++)  
		{
			if($field[$i] == "nomor") {
				$objPHPExcel->getActiveSheet()->mergeCells('A1:A2')->getStyle('A1:A2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if($field[$i] == "nama_lengkap") {
				$objPHPExcel->getActiveSheet()->mergeCells('B1:B2')->getStyle('B1:B2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA LENGKAP'));
			}
			else if($field[$i] == "npp") {
				$objPHPExcel->getActiveSheet()->mergeCells('C1:C2')->getStyle('C1:C2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NPP'));
			}
			else if($field[$i] == "golongan") {
				$objPHPExcel->getActiveSheet()->mergeCells('D1:D2')->getStyle('D1:D2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('GOLONGAN'));
			}
			else if($field[$i] == "jabatan") {
				$objPHPExcel->getActiveSheet()->mergeCells('E1:E2')->getStyle('E1:E2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('JABATAN'));
			}
			else if($field[$i] == "gaji_pokok") {
				$objPHPExcel->getActiveSheet()->mergeCells('F1:F2')->getStyle('F1:F2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('GAJI POKOK'));
			}
			else if($field[$i] == "tunjangan_istri") {
				$objPHPExcel->getActiveSheet()->mergeCells('G1:G2')->getStyle('G1:G2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN ISTRI'));
			}
			else if($field[$i] == "tunjangan_anak") {
				$objPHPExcel->getActiveSheet()->mergeCells('H1:H2')->getStyle('H1:H2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN ANAK'));
			}
			else if($field[$i] == "potongan_gaji1") {
				$objPHPExcel->getActiveSheet()->mergeCells('I1:I2')->getStyle('I1:I2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('POTONGAN GAJI 1'));
			}
			else if($field[$i] == "nominal_potongan_gaji1") {
				$objPHPExcel->getActiveSheet()->mergeCells('J1:J2')->getStyle('J1:J2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL POTONGAN GAJI 1'));
			}
			else if($field[$i] == "potongan_gaji2") {
				$objPHPExcel->getActiveSheet()->mergeCells('K1:K2')->getStyle('K1:K2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('POTONGAN GAJI 2'));
			}
			else if($field[$i] == "nominal_potongan_gaji2") {
				$objPHPExcel->getActiveSheet()->mergeCells('L1:L2')->getStyle('L1:L2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL POTONGAN GAJI 2'));
			}
			else if($field[$i] == "potongan_gaji3") {
				$objPHPExcel->getActiveSheet()->mergeCells('M1:M2')->getStyle('M1:M2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('POTONGAN GAJI 3'));
			}
			else if($field[$i] == "nominal_potongan_gaji3") {
				$objPHPExcel->getActiveSheet()->mergeCells('N1:N2')->getStyle('N1:N2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL POTONGAN GAJI 3'));
			}
			else if($field[$i] == "potongan_gaji4") {
				$objPHPExcel->getActiveSheet()->mergeCells('O1:O2')->getStyle('O1:O2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('POTONGAN GAJI 4'));
			}
			else if($field[$i] == "nominal_potongan_gaji4") {
				$objPHPExcel->getActiveSheet()->mergeCells('P1:P2')->getStyle('P1:P2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL POTONGAN GAJI 4'));
			}
			else if($field[$i] == "potongan_gaji5") {
				$objPHPExcel->getActiveSheet()->mergeCells('Q1:Q2')->getStyle('Q1:Q2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('POTONGAN GAJI 5'));
			}
			else if($field[$i] == "nominal_potongan_gaji5") {
				$objPHPExcel->getActiveSheet()->mergeCells('R1:R2')->getStyle('R1:R2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL POTONGAN GAJI 5'));
			}
			else if($field[$i] == "potongan_gaji6") {
				$objPHPExcel->getActiveSheet()->mergeCells('S1:S2')->getStyle('S1:S2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('POTONGAN GAJI 6'));
			}
			else if($field[$i] == "nominal_potongan_gaji6") {
				$objPHPExcel->getActiveSheet()->mergeCells('T1:T2')->getStyle('T1:T2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL POTONGAN GAJI 6'));
			}
			else if($field[$i] == "total_penghasilan_gaji") {
				$objPHPExcel->getActiveSheet()->mergeCells('U1:U2')->getStyle('U1:U2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TOTAL PENGHASILAN'));
			}
			else if($field[$i] == "total_potongan_gaji") {
				$objPHPExcel->getActiveSheet()->mergeCells('V1:V2')->getStyle('V1:V2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TOTAL POTONGAN'));
			}
			else if($field[$i] == "total_diterima_gaji") {
				$objPHPExcel->getActiveSheet()->mergeCells('W1:W2')->getStyle('W1:W2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TOTAL DITERIMA'));
			}
			else if($field[$i] == "kwitansi_gaji") {
				$objPHPExcel->getActiveSheet()->mergeCells('X1:X2')->getStyle('X1:X2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO. KWITANSI'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
			}
			$column++;
		}
		//end of adding column names

		//start while loop to get data  
		$rowCount = 3;
		foreach ($list_slip as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field); $j++) {
				$kolom = $field[$j];
					if ($kolom == "nomor")  {
						$value_data = ($key+1);
					}
					elseif(!isset($value->$kolom)) { 
						$value_data = NULL;  
					}
					elseif ($value->$kolom != "")  {
						$value_data = strip_tags($value->$kolom);  
					}
					else  {
						$value_data = "";  
					}
					$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->applyFromArray($style);
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Gaji Pegawai - '.formatTanggal($periode_mulai).' - '.formatTanggal($periode_selesai).'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function export_tunjangan()
	{
		$this->is_allowed('pegawai_slip_export');

		//$this->model_pegawai_slip->export('pegawai_slip', 'pegawai_slip');
		$periode_mulai = $this->input->post('periode_mulai');
		$periode_selesai = $this->input->post('periode_selesai');
		$where = "";
		$field = ['nomor', 'nama_lengkap', 'npp', 'golongan', 'jabatan', 'tunjangan_pengelolaan', 'tunjangan_jabatan', 'tunjangan_kesejahteraan', 'tunjangan_masa_kerja', 'tunjangan_fungsional', 'total_kehadiran', 'rupiah_per_kehadiran','tunjangan_kehadiran', 'total_mengajar', 'rupiah_per_mengajar', 'tunjangan_mengajar', 'total_piket', 'rupiah_per_piket', 'tunjangan_piket', 'tunjangan_wali_kelas', 'tunjangan_pembina', 'insentif_ft', 'tunjangan_insentif', 'bonus', 'honor', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'bpjs_pensiun', 'iuran_dplk_bni', 'simpanan_wajib_koperasi', 'pinjaman_uang_koperasi', 'pinjaman_barang_koperasi', 'pph21', 'keterangan_lain1', 'nominal_lain1', 'keterangan_lain2', 'nominal_lain2', 'keterangan_lain3', 'nominal_lain3', 'keterangan_lain4', 'nominal_lain4', 'keterangan_lain5', 'nominal_lain5', 'total_penghasilan_tunjangan', 'total_potongan_tunjangan', 'total_diterima_tunjangan', 'kwitansi_tunjangan'];
		$list_slip = array();
		if (!empty($periode_mulai) && !empty($periode_selesai)) {
			$query = "select s.id_slip, s.id_pegawai, s.nama_lengkap, s.npp, s.golongan, s.jabatan, s.tunjangan_pengelolaan, s.tunjangan_jabatan, s.tunjangan_kesejahteraan, s.tunjangan_masa_kerja, s.tunjangan_fungsional, s.total_kehadiran, s.rupiah_per_kehadiran, s.tunjangan_kehadiran, s.total_mengajar, s.rupiah_per_mengajar, s.tunjangan_mengajar, s.total_piket, s.rupiah_per_piket, s.tunjangan_piket, s.tunjangan_wali_kelas, s.tunjangan_pembina, s.insentif_ft, s.tunjangan_insentif, s.bonus, s.honor, s.bpjs_kesehatan, s.bpjs_ketenagakerjaan, s.bpjs_pensiun, s.iuran_dplk_bni, s.simpanan_wajib_koperasi, s.pinjaman_uang_koperasi, s.pinjaman_barang_koperasi, s.pph21, s.keterangan_lain1, s.nominal_lain1, s.keterangan_lain2, s.nominal_lain2, s.keterangan_lain3, s.nominal_lain3, s.keterangan_lain4, s.nominal_lain4, s.keterangan_lain5, s.nominal_lain5, s.total_penghasilan_tunjangan, s.total_potongan_tunjangan, s.total_diterima_tunjangan, s.kwitansi_tunjangan from pegawai_slip s 
			where s.periode_selesai = '$periode_selesai' and s.periode_mulai = '$periode_mulai'
			order by s.periode_selesai DESC, s.periode_mulai DESC, s.nama_lengkap ASC";
			$list_slip = $this->mymodel->withquery($query,"result");
		}
		else if(!empty($periode_selesai)) {
			$query = "select s.id_slip, s.id_pegawai, s.nama_lengkap, s.npp, s.golongan, s.jabatan, s.tunjangan_pengelolaan, s.tunjangan_jabatan, s.tunjangan_kesejahteraan, s.tunjangan_masa_kerja, s.tunjangan_fungsional, s.total_kehadiran, s.rupiah_per_kehadiran, s.tunjangan_kehadiran, s.total_mengajar, s.rupiah_per_mengajar, s.tunjangan_mengajar, s.total_piket, s.rupiah_per_piket, s.tunjangan_piket, s.tunjangan_wali_kelas, s.tunjangan_pembina, s.insentif_ft, s.tunjangan_insentif, s.bonus, s.honor, s.bpjs_kesehatan, s.bpjs_ketenagakerjaan, s.bpjs_pensiun, s.iuran_dplk_bni, s.simpanan_wajib_koperasi, s.pinjaman_uang_koperasi, s.pinjaman_barang_koperasi, s.pph21, s.keterangan_lain1, s.nominal_lain1, s.keterangan_lain2, s.nominal_lain2, s.keterangan_lain3, s.nominal_lain3, s.keterangan_lain4, s.nominal_lain4, s.keterangan_lain5, s.nominal_lain5, s.total_penghasilan_tunjangan, s.total_potongan_tunjangan, s.total_diterima_tunjangan, s.kwitansi_tunjangan from pegawai_slip s 
			where s.periode_selesai = '$periode_selesai'
			order by s.periode_selesai DESC, s.periode_mulai DESC, s.nama_lengkap ASC";
			$list_slip = $this->mymodel->withquery($query,"result");
			//$this->session->set_flashdata('failed', 'Data tidak valid');
		}
		else{
			/* $query = "select s.id_slip, s.id_pegawai, s.nama_lengkap, s.npp, s.golongan, s.jabatan, s.tunjangan_pengelolaan, s.tunjangan_jabatan, s.tunjangan_kesejahteraan, s.tunjangan_masa_kerja, s.tunjangan_fungsional, s.total_kehadiran, s.rupiah_per_kehadiran, s.tunjangan_kehadiran, s.total_mengajar, s.rupiah_per_mengajar, s.tunjangan_mengajar, s.total_piket, s.rupiah_per_piket, s.tunjangan_piket, s.tunjangan_wali_kelas, s.tunjangan_pembina, s.insentif_ft, s.tunjangan_insentif, s.bonus, s.honor, bpjs_kesehatan, bpjs_ketenagakerjaan, bpjs_pensiun, iuran_dplk_bni, simpanan_wajib_koperasi, pinjaman_uang_koperasi, pinjaman_barang_koperasi, pph21, keterangan_lain1, nominal_lain1, keterangan_lain2, nominal_lain2, keterangan_lain3, nominal_lain3, keterangan_lain4, nominal_lain4, keterangan_lain5, nominal_lain5 from pegawai_slip s 
			order by s.periode_selesai DESC, s.periode_mulai DESC, s.nama_lengkap ASC";
			$list_slip = $this->mymodel->withquery($query,"result"); */
			$this->session->set_flashdata('failed', 'Wajib memilih periode_mulai & periode_selesai');
			redirect('administrator/pegawai_slip');
		}
		
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
		);
		$style_bg = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
				'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFFF00')
				),
		);
		/* for($col = 'A'; $col !== 'K'; $col++) {
			$objPHPExcel->getActiveSheet()
				->getColumnDimension($col)
				->setAutoSize(true);
		} */
		$column = 'A';
		for ($i = 0; $i < count($field); $i++)  
		{
			if($field[$i] == "nomor") {
				$objPHPExcel->getActiveSheet()->mergeCells('A1:A2')->getStyle('A1:A2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if($field[$i] == "nama_lengkap") {
				$objPHPExcel->getActiveSheet()->mergeCells('B1:B2')->getStyle('B1:B2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA LENGKAP'));
			}
			else if($field[$i] == "npp") {
				$objPHPExcel->getActiveSheet()->mergeCells('C1:C2')->getStyle('C1:C2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NPP'));
			}
			else if($field[$i] == "golongan") {
				$objPHPExcel->getActiveSheet()->mergeCells('D1:D2')->getStyle('D1:D2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('GOLONGAN'));
			}
			else if($field[$i] == "jabatan") {
				$objPHPExcel->getActiveSheet()->mergeCells('E1:E2')->getStyle('E1:E2')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('JABATAN'));
			}
			else if($field[$i] == "tunjangan_pengelolaan") {
				$objPHPExcel->getActiveSheet()->mergeCells('F1:F2')->getStyle('F1:F2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN PENGELOLAAN'));
			}
			else if($field[$i] == "tunjangan_jabatan") {
				$objPHPExcel->getActiveSheet()->mergeCells('G1:G2')->getStyle('G1:G2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN JABATAN'));
			}
			else if($field[$i] == "tunjangan_kesejahteraan") {
				$objPHPExcel->getActiveSheet()->mergeCells('H1:H2')->getStyle('H1:H2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN KESEJAHTERAAN'));
			}
			else if($field[$i] == "tunjangan_masa_kerja") {
				$objPHPExcel->getActiveSheet()->mergeCells('I1:I2')->getStyle('I1:I2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN MASA KERJA'));
			}
			else if($field[$i] == "tunjangan_fungsional") {
				$objPHPExcel->getActiveSheet()->mergeCells('J1:J2')->getStyle('J1:J2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN FUNGSIONAL'));
			}
			else if($field[$i] == "tunjangan_kehadiran") {
				$column = 'K';
				$objPHPExcel->getActiveSheet()->mergeCells('K1:M1')->getStyle('K1:M1')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->getStyle('K'.($rowCount+1).':M'.($rowCount+1))->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN KEHADIRAN'));
				$objPHPExcel->getActiveSheet()->setCellValue("K".($rowCount+1), strtoupper('Jumlah Hadir'));
				$objPHPExcel->getActiveSheet()->setCellValue("L".($rowCount+1), strtoupper('Nominal'));
				$objPHPExcel->getActiveSheet()->setCellValue("M".($rowCount+1), strtoupper('Total Tunjangan'));
			}
			else if($field[$i] == "tunjangan_mengajar") {
				$column = 'N';
				$objPHPExcel->getActiveSheet()->mergeCells('N1:P1')->getStyle('N1:P1')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->getStyle('N'.($rowCount+1).':P'.($rowCount+1))->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN MENGAJAR'));
				$objPHPExcel->getActiveSheet()->setCellValue("N".($rowCount+1), strtoupper('Jumlah'));
				$objPHPExcel->getActiveSheet()->setCellValue("O".($rowCount+1), strtoupper('Nominal'));
				$objPHPExcel->getActiveSheet()->setCellValue("P".($rowCount+1), strtoupper('Total Tunjangan'));
			}
			else if($field[$i] == "tunjangan_piket") {
				$column = 'Q';
				$objPHPExcel->getActiveSheet()->mergeCells('Q1:S1')->getStyle('Q1:S1')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->getStyle('Q'.($rowCount+1).':S'.($rowCount+1))->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN PIKET'));
				$objPHPExcel->getActiveSheet()->setCellValue("Q".($rowCount+1), strtoupper('Jumlah'));
				$objPHPExcel->getActiveSheet()->setCellValue("R".($rowCount+1), strtoupper('Nominal'));
				$objPHPExcel->getActiveSheet()->setCellValue("S".($rowCount+1), strtoupper('Total Tunjangan'));
				$column = 'S';
			}
			else if($field[$i] == "tunjangan_wali_kelas") {
				$objPHPExcel->getActiveSheet()->mergeCells('T1:T2')->getStyle('T1:T2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN WALI KELAS'));
			}
			else if($field[$i] == "tunjangan_pembina") {
				$objPHPExcel->getActiveSheet()->mergeCells('U1:U2')->getStyle('U1:U2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN PEMBINA'));
			}
			else if($field[$i] == "insentif_ft") {
				$objPHPExcel->getActiveSheet()->mergeCells('V1:V2')->getStyle('V1:V2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('INSENTIF FT'));
			}
			else if($field[$i] == "tunjangan_insentif") {
				$objPHPExcel->getActiveSheet()->mergeCells('W1:W2')->getStyle('W1:W2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TUNJANGAN INSENTIF'));
			}
			else if($field[$i] == "bonus") {
				$objPHPExcel->getActiveSheet()->mergeCells('X1:X2')->getStyle('X1:X2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('BONUS'));
			}
			else if($field[$i] == "honor") {
				$objPHPExcel->getActiveSheet()->mergeCells('Y1:Y2')->getStyle('Y1:Y2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('HONOR'));
			}
			else if($field[$i] == "bpjs_kesehatan") {
				$objPHPExcel->getActiveSheet()->mergeCells('Z1:Z2')->getStyle('Z1:Z2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('BPJS KESEHATAN'));
			}
			else if($field[$i] == "bpjs_ketenagakerjaan") {
				$objPHPExcel->getActiveSheet()->mergeCells('AA1:AA2')->getStyle('AA1:AA2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('BPJS KETENAGAKERJAAN'));
			}
			else if($field[$i] == "bpjs_pensiun") {
				$objPHPExcel->getActiveSheet()->mergeCells('AB1:AB2')->getStyle('AB1:AB2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('BPJS PENSIUN'));
			}
			else if($field[$i] == "iuran_dplk_bni") {
				$objPHPExcel->getActiveSheet()->mergeCells('AC1:AC2')->getStyle('AC1:AC2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('IURAN DPLK BNI'));
			}
			else if($field[$i] == "simpanan_wajib_koperasi") {
				$objPHPExcel->getActiveSheet()->mergeCells('AD1:AD2')->getStyle('AD1:AD2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SIMPANAN WAJIB KOPERASI'));
			}
			else if($field[$i] == "pinjaman_uang_koperasi") {
				$objPHPExcel->getActiveSheet()->mergeCells('AE1:AE2')->getStyle('AE1:AE2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('PINJAMAN UANG KOPERASI'));
			}
			else if($field[$i] == "pinjaman_barang_koperasi") {
				$objPHPExcel->getActiveSheet()->mergeCells('AF1:AF2')->getStyle('AF1:AF2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('PINJAMAN BARANG KOPERASI'));
			}
			else if($field[$i] == "pph21") {
				$objPHPExcel->getActiveSheet()->mergeCells('AG1:AG2')->getStyle('AG1:AG2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('PPH 21'));
			}
			else if($field[$i] == "keterangan_lain1") {
				$objPHPExcel->getActiveSheet()->mergeCells('AH1:AH2')->getStyle('AH1:AH2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KETERANGAN LAIN 1'));
			}
			else if($field[$i] == "nominal_lain1") {
				$objPHPExcel->getActiveSheet()->mergeCells('AI1:AI2')->getStyle('AI1:AI2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL LAIN 1'));
			}
			else if($field[$i] == "keterangan_lain2") {
				$objPHPExcel->getActiveSheet()->mergeCells('AJ1:AJ2')->getStyle('AJ1:AJ2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KETERANGAN LAIN 2'));
			}
			else if($field[$i] == "nominal_lain2") {
				$objPHPExcel->getActiveSheet()->mergeCells('AK1:AK2')->getStyle('AK1:AK2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL LAIN 2'));
			}
			else if($field[$i] == "keterangan_lain3") {
				$objPHPExcel->getActiveSheet()->mergeCells('AL1:AL2')->getStyle('AL1:AL2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KETERANGAN LAIN 3'));
			}
			else if($field[$i] == "nominal_lain3") {
				$objPHPExcel->getActiveSheet()->mergeCells('AM1:AM2')->getStyle('AM1:AM2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL LAIN 3'));
			}
			else if($field[$i] == "keterangan_lain4") {
				$objPHPExcel->getActiveSheet()->mergeCells('AN1:AN2')->getStyle('AN1:AN2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KETERANGAN LAIN 4'));
			}
			else if($field[$i] == "nominal_lain4") {
				$objPHPExcel->getActiveSheet()->mergeCells('AO1:AO2')->getStyle('AO1:AO2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL LAIN 4'));
			}
			else if($field[$i] == "keterangan_lain5") {
				$objPHPExcel->getActiveSheet()->mergeCells('AP1:AP2')->getStyle('AP1:AP2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KETERANGAN LAIN 5'));
			}
			else if($field[$i] == "nominal_lain5") {
				$objPHPExcel->getActiveSheet()->mergeCells('AQ1:AQ2')->getStyle('AQ1:AQ2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL LAIN 5'));
			}
			else if($field[$i] == "total_penghasilan_tunjangan") {
				$objPHPExcel->getActiveSheet()->mergeCells('AR1:AR2')->getStyle('AR1:AR2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TOTAL PENGHASILAN'));
			}
			else if($field[$i] == "total_potongan_tunjangan") {
				$objPHPExcel->getActiveSheet()->mergeCells('AS1:AS2')->getStyle('AS1:AS2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TOTAL POTONGAN'));
			}
			else if($field[$i] == "total_diterima_tunjangan") {
				$objPHPExcel->getActiveSheet()->mergeCells('AT1:AT2')->getStyle('AT1:AT2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TOTAL DITERIMA'));
			}
			else if($field[$i] == "kwitansi_tunjangan") {
				$objPHPExcel->getActiveSheet()->mergeCells('AU1:AU2')->getStyle('AU1:AU2')->applyFromArray($style_bg);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO. KWITANSI'));
			}
			else{
				//$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
			}
			$column++;
		}
		//end of adding column names

		//start while loop to get data  
		$rowCount = 3;
		foreach ($list_slip as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field); $j++) {
				$kolom = $field[$j];
					if ($kolom == "nomor")  {
						$value_data = ($key+1);
					}
					elseif(!isset($value->$kolom)) { 
						$value_data = NULL;  
					}
					elseif ($value->$kolom != "")  {
						$value_data = strip_tags($value->$kolom);  
					}
					else  {
						$value_data = "";  
					}
					$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->applyFromArray($style);
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Tunjangan Pegawai - '.formatTanggal($periode_mulai).' - '.formatTanggal($periode_selesai).'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pegawai_slip_export');

		$this->model_pegawai_slip->pdf('pegawai_slip', 'pegawai_slip');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pegawai_slip_export');

		$table = $title = 'pegawai_slip';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pegawai_slip->find($id);
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

	public function import_gaji()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		// dd(isset($_FILES["file_siswa"]["name"]));
		$this->db->trans_begin();

		// dd($this->input->post('kelas_sd'));

		$periode_mulai = $this->input->post('periode_mulai');
		$periode_selesai = $this->input->post('periode_selesai');
		if (isset($_FILES["file_upload"]["name"])) {
			$path = $_FILES["file_upload"]["tmp_name"];
			$p=$_FILES["file_upload"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->load->library("session");
				$this->session->set_flashdata('failed', 'Format harus .xlsx atau .xls');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 3; $row <= $highestRow; $row++) {
					
					if (empty($worksheet->getCellByColumnAndRow(1, $row)->getValue()) ) {
						break;
					}
					// inisial variabel
					$nama = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$npp = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$golongan = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$jabatan = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$gaji_pokok = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$tunjangan_istri = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$tunjangan_anak = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$potongan_gaji1 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$nominal_potongan_gaji1 = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$potongan_gaji2 = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$nominal_potongan_gaji2 = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$potongan_gaji3 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$nominal_potongan_gaji3 = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$potongan_gaji4 = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$nominal_potongan_gaji4 = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					$potongan_gaji5 = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
					$nominal_potongan_gaji5 = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
					$potongan_gaji6 = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
					$nominal_potongan_gaji6 = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
					$total_penghasilan= $worksheet->getCellByColumnAndRow(20, $row)->getValue();
					$total_potongan = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
					$total_diterima = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
					$kwitansi = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
					/* $tunjangan_pengelolaan = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$tunjangan_jabatan = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$tunjangan_kesejahteraan = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$tunjangan_masa_kerja = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$tunjangan_fungsional = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$total_kehadiran = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$rupiah_per_kehadiran = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$tunjangan_kehadiran = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					$total_mengajar = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
					$rupiah_per_mengajar = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
					$tunjangan_mengajar = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
					$total_piket = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
					$rupiah_per_piket = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
					$tunjangan_piket = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
					$tunjangan_wali_kelas = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
					$tunjangan_pembina = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
					$insentif_ft = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
					$tunjangan_insentif = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
					$bonus = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
					$honor = $worksheet->getCellByColumnAndRow(27, $row)->getValue(); */
				
					// check npp staff
					/* $check=$this->mymodel->withquery("select * from pegawai where npp = '".$npp."'", 'row');
					
					if(empty($check)){
						//check nama staff yang mirip
						$check2=$this->mymodel->withquery("select * from pegawai where nama_lengkap like '%".$nama."%'", 'row');
						if(empty($check2)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Input NPP pegawai ".$check2->nama_lengkap." salah. (NPP : ".$check2->npp.")");
							redirect($_SERVER['HTTP_REFERER']);
						}
						else{
							$check=$check2;
						} */
						/* $this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', "NPP tidak terdaftar / salah. (NPP : ".$npp.")");
						redirect($_SERVER['HTTP_REFERER']);
					} */
				
					$data_slip = array(
						'id_pegawai' => "0",
						'nama_lengkap' => $nama,
						'npp' => $npp,
						'golongan' => $golongan,
						'jabatan' => $jabatan,
						'gaji_pokok' => $gaji_pokok,
						'tunjangan_istri' => $tunjangan_istri,
						'tunjangan_anak' => $tunjangan_anak,
						'total_penghasilan_gaji' => $total_penghasilan,
						'total_potongan_gaji' => $total_potongan,
						'total_diterima_gaji' => $total_diterima,
						'kwitansi_gaji' => $kwitansi,
						'periode_mulai' => $periode_mulai,
						'periode_selesai' => $periode_selesai
					);
					if (!empty($potongan_gaji1)){
						$data_slip['potongan_gaji1'] = $potongan_gaji1;
						$data_slip['nominal_potongan_gaji1'] = $nominal_potongan_gaji1;
					}
					if (!empty($potongan_gaji2)){
						$data_slip['potongan_gaji2'] = $potongan_gaji2;
						$data_slip['nominal_potongan_gaji2'] = $nominal_potongan_gaji2;
					}
					if (!empty($potongan_gaji3)){
						$data_slip['potongan_gaji3'] = $potongan_gaji3;
						$data_slip['nominal_potongan_gaji3'] = $nominal_potongan_gaji3;
					}
					if (!empty($potongan_gaji4)){
						$data_slip['potongan_gaji4'] = $potongan_gaji4;
						$data_slip['nominal_potongan_gaji4'] = $nominal_potongan_gaji4;
					}
					if (!empty($potongan_gaji5)){
						$data_slip['potongan_gaji5'] = $potongan_gaji5;
						$data_slip['nominal_potongan_gaji5'] = $nominal_potongan_gaji5;
					}
					if (!empty($potongan_gaji6)){
						$data_slip['potongan_gaji6'] = $potongan_gaji6;
						$data_slip['nominal_potongan_gaji6'] = $nominal_potongan_gaji6;
					}
					//print_r($data_slip);
					//echo "<br/>";
					
					//check null
					/* foreach ($data_slip as $key => $item) {
						if(!$item && ($key != 'tunjangan_anak' || $key != 'tunjangan_istri')){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Data ada yang kosong");
							redirect($_SERVER['HTTP_REFERER']);
						}
					} */
				
					//check apakah npp / pegawai tersebut di periode_selesai & periode_mulai yg sama sudah ada / belum
					// $check_slip = $this->mymodel->withquery("select * from pegawai_slip where (npp = '".$npp."' and nama_lengkap = '".str_replace("'", "\'", $nama)."') and periode_mulai = '".$periode_mulai."' and periode_selesai = '".$periode_selesai."'", 'row');
					//check apakah npp / pegawai tersebut di periode_selesai & periode_mulai yg sama sudah ada / belum
					if ($npp == "-" || empty($npp)){
						$check_slip = $this->mymodel->withquery("select * from pegawai_slip where (nama_lengkap = '".str_replace("'", "''", $nama)."') and periode_selesai = '".$periode_selesai."' and periode_mulai = '".$periode_mulai."'", 'row');
					}
					else{
						$check_slip = $this->mymodel->withquery("select * from pegawai_slip where (npp = '".$npp."' or nama_lengkap = '".str_replace("'", "''", $nama)."') and periode_selesai = '".$periode_selesai."' and periode_mulai = '".$periode_mulai."'", 'row');
					}
					
					if(!empty($check_slip)){
						$data_slip['updated_at'] = date("Y-m-d H:i:s");
						$insertId = $this->mymodel->update("pegawai_slip", $data_slip, 'id_slip', $check_slip->id_slip);
						$insertId = $check_slip->id_slip;
					}else{
						$data_slip['created_at'] = date("Y-m-d H:i:s");
						$insertId = $this->mymodel->insertid("pegawai_slip", $data_slip);
					}
					
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import gagal atas nama ' . $nama);
						redirect($_SERVER['HTTP_REFERER']);
					}
					else if(!empty($npp) ){
						//send notif
						$get_device_sd = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sd where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_smp = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_smp where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_sma = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sma where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_ft = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_ft where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_pegawai = $this->mymodel->withquery("select npp, device_id, nama_lengkap from pegawai where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$device_id = "";
						$bulan = formatBulan($periode_selesai);

						if (!empty($get_device_sd)) {
							$device_id = $get_device_sd->device_id;
							$nama_lengkap = $get_device_sd->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_sd->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_smp)) {
							$device_id = $get_device_smp->device_id;
							$nama_lengkap = $get_device_smp->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_smp->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_sma)) {
							$device_id = $get_device_sma->device_id;
							$nama_lengkap = $get_device_sma->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_sma->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_ft)) {
							$device_id = $get_device_ft->device_id;
							$nama_lengkap = $get_device_ft->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_ft->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_pegawai)) {
							$device_id = $get_device_pegawai->device_id;
							$nama_lengkap = $get_device_pegawai->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_pegawai->nama_lengkap), "id_slip", $insertId);
						}
						$pesan = "Slip Gaji bulan ".$bulan." sudah dapat dilihat di aplikasi.";
						if (!empty($device_id) && !empty($npp) && $npp == "682.25.254") {
							$this->send_notif("Slip Gaji ".strtoupper($nama_lengkap), $pesan, $device_id, array("npp" => $npp, "id_slip" => $insertId) );
						}
					}
					
				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import data berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function import_tunjangan()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		// dd(isset($_FILES["file_siswa"]["name"]));
		$this->db->trans_begin();

		// dd($this->input->post('kelas_sd'));

		$periode_mulai = $this->input->post('periode_mulai');
		$periode_selesai = $this->input->post('periode_selesai');
		if (isset($_FILES["file_upload"]["name"])) {
			$path = $_FILES["file_upload"]["tmp_name"];
			$p=$_FILES["file_upload"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->load->library("session");
				$this->session->set_flashdata('failed', 'Format harus .xlsx atau .xls');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 3; $row <= $highestRow; $row++) {
					
					if (empty($worksheet->getCellByColumnAndRow(1, $row)->getValue()) ) {
						break;
					}
					// inisial variabel
					$nama = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$npp = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$golongan = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$jabatan = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					/* $gaji_pokok = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$tunjangan_istri = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$tunjangan_anak = $worksheet->getCellByColumnAndRow(7, $row)->getValue(); */
					$tunjangan_pengelolaan = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$tunjangan_jabatan = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$tunjangan_kesejahteraan = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$tunjangan_masa_kerja = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$tunjangan_fungsional = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$total_kehadiran = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$rupiah_per_kehadiran = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$tunjangan_kehadiran = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$total_mengajar = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$rupiah_per_mengajar = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$tunjangan_mengajar = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					$total_piket = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
					$rupiah_per_piket = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
					$tunjangan_piket = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
					$tunjangan_wali_kelas = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
					$tunjangan_pembina = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
					$insentif_ft = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
					$tunjangan_insentif = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
					$bonus = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
					$honor = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
					$bpjs_kesehatan = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
					$bpjs_ketenagakerjaan = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
					$bpjs_pensiun = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
					$iuran_dplk_bni = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
					$simpanan_wajib_koperasi = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
					$pinjaman_uang_koperasi = $worksheet->getCellByColumnAndRow(30, $row)->getValue();
					$pinjaman_barang_koperasi = $worksheet->getCellByColumnAndRow(31, $row)->getValue();
					$pph21 = $worksheet->getCellByColumnAndRow(32, $row)->getValue();
					$keterangan1 = $worksheet->getCellByColumnAndRow(33, $row)->getValue();
					$nominal1 = $worksheet->getCellByColumnAndRow(34, $row)->getValue();
					$keterangan2 = $worksheet->getCellByColumnAndRow(35, $row)->getValue();
					$nominal2 = $worksheet->getCellByColumnAndRow(36, $row)->getValue();
					$keterangan3 = $worksheet->getCellByColumnAndRow(37, $row)->getValue();
					$nominal3 = $worksheet->getCellByColumnAndRow(38, $row)->getValue();
					$keterangan4 = $worksheet->getCellByColumnAndRow(39, $row)->getValue();
					$nominal4 = $worksheet->getCellByColumnAndRow(40, $row)->getValue();
					$keterangan5 = $worksheet->getCellByColumnAndRow(41, $row)->getValue();
					$nominal5 = $worksheet->getCellByColumnAndRow(42, $row)->getValue();

					$total_penghasilan = $worksheet->getCellByColumnAndRow(43, $row)->getValue();
					$total_potongan = $worksheet->getCellByColumnAndRow(44, $row)->getValue();
					$total_diterima = $worksheet->getCellByColumnAndRow(45, $row)->getValue();
					$kwitansi = $worksheet->getCellByColumnAndRow(46, $row)->getValue();
				
					// check npp staff
					//$check=$this->mymodel->withquery("select * from pegawai where npp = '".$npp."'", 'row');
					
					//if(empty($check)){
						//check nama staff yang mirip
						/* $check2=$this->mymodel->withquery("select * from pegawai where nama_lengkap like '%".$nama."%'", 'row');
						if(empty($check2)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Input NPP pegawai ".$check2->nama_lengkap." salah. (NPP : ".$check2->npp.")");
							redirect($_SERVER['HTTP_REFERER']);
						}
						else{
							$check=$check2;
						} */
						/* $this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', "NPP tidak terdaftar / salah. (NPP : ".$npp.")");
						redirect($_SERVER['HTTP_REFERER']); */
					//}
				
					$data_slip = array(
						'id_pegawai' => "0",
						'nama_lengkap' => $nama,
						'npp' => $npp,
						'golongan' => $golongan,
						'jabatan' => $jabatan,
						'tunjangan_pengelolaan' => (empty($tunjangan_pengelolaan)) ? 0 : $tunjangan_pengelolaan,
						'tunjangan_jabatan' => (empty($tunjangan_jabatan)) ? 0 : $tunjangan_jabatan,
						'tunjangan_kesejahteraan' => (empty($tunjangan_kesejahteraan)) ? 0 : $tunjangan_kesejahteraan,
						'tunjangan_masa_kerja' => (empty($tunjangan_masa_kerja)) ? 0 : $tunjangan_masa_kerja,
						'tunjangan_fungsional' => (empty($tunjangan_fungsional)) ? 0 : $tunjangan_fungsional,
						'tunjangan_kehadiran' => (empty($tunjangan_kehadiran)) ? 0 : $tunjangan_kehadiran,
						'tunjangan_mengajar' => (empty($tunjangan_mengajar)) ? 0 : $tunjangan_mengajar,
						'tunjangan_piket' => (empty($tunjangan_piket)) ? 0 : $tunjangan_piket,
						'tunjangan_wali_kelas' => (empty($tunjangan_wali_kelas)) ? 0 : $tunjangan_wali_kelas,
						'tunjangan_pembina' => (empty($tunjangan_pembina)) ? 0 : $tunjangan_pembina,
						'tunjangan_insentif' => (empty($tunjangan_insentif)) ? 0 : $tunjangan_insentif,
						'insentif_ft' => (empty($insentif_ft)) ? 0 : $insentif_ft,
						'bonus' => (empty($bonus)) ? 0 : $bonus,
						'honor' => (empty($honor)) ? 0 : $honor,
						'total_penghasilan_tunjangan' => (empty($total_penghasilan)) ? 0 : $total_penghasilan,
						'total_potongan_tunjangan' => (empty($total_potongan)) ? 0 : $total_potongan,
						'total_diterima_tunjangan' => (empty($total_diterima)) ? 0 : $total_diterima,
						'kwitansi_tunjangan' => $kwitansi,
						'periode_mulai' => $periode_mulai,
						'periode_selesai' => $periode_selesai
					);
					//print_r($data_slip);
					
					//check null
					/* foreach ($data_slip as $key => $item) {
						if(!$item){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Data ada yang kosong ".$key);
							redirect($_SERVER['HTTP_REFERER']);
						}
					} */
				
					if(!empty($bpjs_kesehatan)){
						$data_slip['bpjs_kesehatan'] = $bpjs_kesehatan;
					}
					if(!empty($bpjs_ketenagakerjaan)){
						$data_slip['bpjs_ketenagakerjaan'] = $bpjs_ketenagakerjaan;
					}
					if(!empty($bpjs_pensiun)){
						$data_slip['bpjs_pensiun'] = $bpjs_pensiun;
					}
					if(!empty($iuran_dplk_bni)){
						$data_slip['iuran_dplk_bni'] = $iuran_dplk_bni;
					}
					if(!empty($simpanan_wajib_koperasi)){
						$data_slip['simpanan_wajib_koperasi'] = $simpanan_wajib_koperasi;
					}
					if(!empty($pinjaman_uang_koperasi)){
						$data_slip['pinjaman_uang_koperasi'] = $pinjaman_uang_koperasi;
					}
					if(!empty($pinjaman_barang_koperasi)){
						$data_slip['pinjaman_barang_koperasi'] = $pinjaman_barang_koperasi;
					}
					if(!empty($pph21)){
						$data_slip['pph21'] = $pph21;
					}
					//set keterangan dan nominal
					if(!empty($keterangan1)){
						$data_slip['keterangan_lain1'] = $keterangan1;
						$data_slip['nominal_lain1'] = $nominal1;
					}
					if(!empty($keterangan2)){
						$data_slip['keterangan_lain2'] = $keterangan2;
						$data_slip['nominal_lain2'] = $nominal2;
					}
					if(!empty($keterangan3)){
						$data_slip['keterangan_lain3'] = $keterangan3;
						$data_slip['nominal_lain3'] = $nominal3;
					}
					if(!empty($keterangan4)){
						$data_slip['keterangan_lain4'] = $keterangan4;
						$data_slip['nominal_lain4'] = $nominal4;
					}
					if(!empty($keterangan5)){
						$data_slip['keterangan_lain5'] = $keterangan5;
						$data_slip['nominal_lain5'] = $nominal5;
					}
				
					//check apakah npp / pegawai tersebut di periode_selesai & periode_mulai yg sama sudah ada / belum
					if ($npp == "-" || empty($npp)){
						$check_slip = $this->mymodel->withquery("select * from pegawai_slip where (nama_lengkap = '".str_replace("'", "''", $nama)."') and periode_selesai = '".$periode_selesai."' and periode_mulai = '".$periode_mulai."'", 'row');
					}
					else{
						$check_slip = $this->mymodel->withquery("select * from pegawai_slip where (npp = '".$npp."' or nama_lengkap = '".str_replace("'", "''", $nama)."') and periode_selesai = '".$periode_selesai."' and periode_mulai = '".$periode_mulai."'", 'row');
					}
					/* echo $this->db->last_query();
					echo "<br/>"; */
					
					if(!empty($check_slip)){
						$data_slip['updated_at'] = date("Y-m-d H:i:s");
						$insertId = $this->mymodel->update("pegawai_slip", $data_slip, 'id_slip', $check_slip->id_slip);
						$insertId = $check_slip->id_slip;
					}else{
						$data_slip['created_at'] = date("Y-m-d H:i:s");
						$insertId = $this->mymodel->insertid("pegawai_slip", $data_slip);
					}
					/* echo $this->db->last_query();
					echo "<br/>"; */

					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import gagal atas nama ' . $nama.' NPP '.$npp);
						redirect($_SERVER['HTTP_REFERER']);
					}
					else if(!empty($npp)){
						//send notif
						$get_device_sd = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sd where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_smp = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_smp where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_sma = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sma where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_ft = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_ft where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_pegawai = $this->mymodel->withquery("select npp, device_id, nama_lengkap from pegawai where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$device_id = "";
						$bulan = formatBulan($periode_selesai);

						if (!empty($get_device_sd)) {
							$device_id = $get_device_sd->device_id;
							$nama_lengkap = $get_device_sd->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_sd->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_smp)) {
							$device_id = $get_device_smp->device_id;
							$nama_lengkap = $get_device_smp->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_smp->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_sma)) {
							$device_id = $get_device_sma->device_id;
							$nama_lengkap = $get_device_sma->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_sma->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_ft)) {
							$device_id = $get_device_ft->device_id;
							$nama_lengkap = $get_device_ft->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_ft->nama_lengkap), "id_slip", $insertId);
						}
						if (!empty($get_device_pegawai)) {
							$device_id = $get_device_pegawai->device_id;
							$nama_lengkap = $get_device_pegawai->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip", array("nama_lengkap" => $get_device_pegawai->nama_lengkap), "id_slip", $insertId);
						}
						$pesan = "Slip Tunjangan bulan ".$bulan." sudah dapat dilihat di aplikasi.";
						if (!empty($device_id) && !empty($npp) && $npp == "682.25.254") {
							$this->send_notif("Slip Tunjangan ".strtoupper($nama_lengkap), $pesan, $device_id, array("npp" => $npp, "id_slip" => $insertId) );
						}
					}
					
				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import data berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
        $id_siswa_aktif = $data['id_siswa_aktif'];
        $id_presensi = $data['id_presensi'];
        $jenjang = $data['jenjang'];
        $token = $this->getAccessToken();
        $data = [
            'token' => $fcm_id,
            'title' => $title,
            'body' => $desc
        ];
        $data_notification = array(
            'message' => array(
                'token' => $fcm_id,
                'notification' => array(
                    'title' => $data['title'],
                    'body' => $data['body']
                ),
                'data' => array(
                    'id_siswa_aktif' => $id_siswa_aktif,
                    'id_presensi' => $id_presensi,
                    'jenjang' => $jenjang
                )
                //'data' => array('route' => 'detailAnnouncement?idAnnouncement='.$data['id_agenda'])
            )
        );
        $send_notif = $this->firebase_send_notif($data);
        //$result = $firebaseService->sendMessage($fcm_id, $data['title'] ?? '', $data['body'] ?? '', array('route' => 'detailAnnouncement?idAnnouncement='.$data['id_agenda']));
        //print_r($token);
    }

    function getAccessToken(){

        //require "google-api-php-client/vendor/autoload.php";
        $client= new \Google_Client();
        //$client= new Google\Client();
        $client->setAuthConfig(PRIVATE_FIREBASE_KEY);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        //$client->fetchAccessTokenWithAssertion();
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        //result array(access_token, expires_in, token_type, created)
        $result=$token['access_token'];
                
        return $result;
    
    }
    function firebase_send_notif($data){
        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json'
        ];
    
        $fields = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body']
                ]
            ]
        ];
    
        $fields = json_encode($fields);
        $url = 'https://fcm.googleapis.com/v1/projects/labscib-app/messages:send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $result = curl_exec($ch);
        curl_close($ch);
        /*print_r($result);
        echo "<br/><br/>";*/
    }
	
}


/* End of file pegawai_slip.php */
/* Location: ./application/controllers/administrator/Pegawai Slip.php */