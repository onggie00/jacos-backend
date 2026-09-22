<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . '/vendor/autoload.php';
define( 'API_ACCESS_KEY', 'AAAAX38-FW8:APA91bGoy4cJtX9jf4kfphdyh-1EZ3VFU8GlbVzXmka4-x-c2q6-oAvoltIKeSzoW4Pz8hbUL_MT0EW6NTUctWryTgsAlAmakleTaC-QzwLocy8OaVbswc_RuCC-tUaqPKta3TiYdoJ-' );
define( 'PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');


/**
*| --------------------------------------------------------------------------
*| Pegawai Slip Custom Controller
*| --------------------------------------------------------------------------
*| Pegawai Slip Custom site
*|
*/
class Pegawai_slip_custom extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pegawai_slip_custom');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pegawai Slip Customs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pegawai_slip_custom_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pegawai_slip_customs'] = $this->model_pegawai_slip_custom->get($filter, $field, $this->limit_page, $offset);
		$this->data['pegawai_slip_custom_counts'] = $this->model_pegawai_slip_custom->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pegawai_slip_custom/index/',
			'total_rows'   => $this->model_pegawai_slip_custom->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pegawai Slip Lain List');
		$this->render('backend/standart/administrator/pegawai_slip_custom/pegawai_slip_custom_list', $this->data);
	}
	
	/**
	* Add new pegawai_slip_customs
	*
	*/
	public function add()
	{
		$this->is_allowed('pegawai_slip_custom_add');

		$this->template->title('Pegawai Slip Lain New');
		$this->render('backend/standart/administrator/pegawai_slip_custom/pegawai_slip_custom_add', $this->data);
	}

	/**
	* Add New Pegawai Slip Customs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pegawai_slip_custom_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_slip', 'Nama Slip', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tunjangan_istri', 'Tunjangan Istri', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_anak', 'Tunjangan Anak', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pengelolaan', 'Tunjangan Pengelolaan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_jabatan', 'Tunjangan Jabatan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_kesejahteraan', 'Tunjangan Kesejahteraan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_masa_kerja', 'Tunjangan Masa Kerja', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_fungsional', 'Tunjangan Fungsional', 'trim|max_length[20]');
		$this->form_validation->set_rules('total_kehadiran', 'Total Kehadiran', 'trim|max_length[11]');
		$this->form_validation->set_rules('tunjangan_kehadiran', 'Tunjangan Kehadiran', 'trim|max_length[20]');
		$this->form_validation->set_rules('total_mengajar', 'Total Mengajar', 'trim|max_length[11]');
		$this->form_validation->set_rules('tunjangan_mengajar', 'Tunjangan Mengajar', 'trim|max_length[20]');
		$this->form_validation->set_rules('total_piket', 'Total Piket', 'trim|max_length[11]');
		$this->form_validation->set_rules('rupiah_per_piket', 'Rupiah Per Piket', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_piket', 'Tunjangan Piket', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_wali_kelas', 'Tunjangan Wali Kelas', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pembina', 'Tunjangan Pembina', 'trim|max_length[20]');
		$this->form_validation->set_rules('insentif_ft', 'Insentif FT', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_insentif', 'Tunjangan Insentif', 'trim|max_length[20]');
		$this->form_validation->set_rules('bonus', 'Bonus', 'trim|max_length[20]');
		$this->form_validation->set_rules('honor', 'Honor', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('thr', 'THR', 'trim|max_length[20]');
		$this->form_validation->set_rules('gaji14', 'Gaji Ke 14', 'trim|max_length[20]');
		$this->form_validation->set_rules('pph21', 'PPH21', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('total_penghasilan', 'Total Penghasilan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('total_potongan', 'Total Potongan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('total_diterima', 'Total Diterima', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kwitansi', 'Kwitansi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('periode_mulai', 'Periode Mulai', 'trim|required');
		$this->form_validation->set_rules('periode_selesai', 'Periode Selesai', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_slip' => $this->input->post('nama_slip'),
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
				'tunjangan_kehadiran' => $this->input->post('tunjangan_kehadiran'),
				'total_mengajar' => $this->input->post('total_mengajar'),
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
				'thr' => $this->input->post('thr'),
				'gaji14' => $this->input->post('gaji14'),
				'pph21' => $this->input->post('pph21'),
				'total_penghasilan' => $this->input->post('total_penghasilan'),
				'total_potongan' => $this->input->post('total_potongan'),
				'total_diterima' => $this->input->post('total_diterima'),
				'kwitansi' => $this->input->post('kwitansi'),
				'periode_mulai' => $this->input->post('periode_mulai'),
				'periode_selesai' => $this->input->post('periode_selesai'),
			];

			
			$save_pegawai_slip_custom = $this->model_pegawai_slip_custom->store($save_data);
            

			if ($save_pegawai_slip_custom) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pegawai_slip_custom;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pegawai_slip_custom/edit/' . $save_pegawai_slip_custom, 'Edit Pegawai Slip Custom'),
						anchor('administrator/pegawai_slip_custom', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pegawai_slip_custom/edit/' . $save_pegawai_slip_custom, 'Edit Pegawai Slip Custom')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pegawai_slip_custom');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pegawai_slip_custom');
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
	* Update view Pegawai Slip Customs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pegawai_slip_custom_update');

		$this->data['pegawai_slip_custom'] = $this->model_pegawai_slip_custom->find($id);

		$this->template->title('Pegawai Slip Lain Update');
		$this->render('backend/standart/administrator/pegawai_slip_custom/pegawai_slip_custom_update', $this->data);
	}

	/**
	* Update Pegawai Slip Customs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pegawai_slip_custom_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_slip', 'Nama Slip', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tunjangan_istri', 'Tunjangan Istri', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_anak', 'Tunjangan Anak', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pengelolaan', 'Tunjangan Pengelolaan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_jabatan', 'Tunjangan Jabatan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_kesejahteraan', 'Tunjangan Kesejahteraan', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_masa_kerja', 'Tunjangan Masa Kerja', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_fungsional', 'Tunjangan Fungsional', 'trim|max_length[20]');
		$this->form_validation->set_rules('total_kehadiran', 'Total Kehadiran', 'trim|max_length[11]');
		$this->form_validation->set_rules('tunjangan_kehadiran', 'Tunjangan Kehadiran', 'trim|max_length[20]');
		$this->form_validation->set_rules('total_mengajar', 'Total Mengajar', 'trim|max_length[11]');
		$this->form_validation->set_rules('tunjangan_mengajar', 'Tunjangan Mengajar', 'trim|max_length[20]');
		$this->form_validation->set_rules('total_piket', 'Total Piket', 'trim|max_length[11]');
		$this->form_validation->set_rules('rupiah_per_piket', 'Rupiah Per Piket', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_piket', 'Tunjangan Piket', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_wali_kelas', 'Tunjangan Wali Kelas', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_pembina', 'Tunjangan Pembina', 'trim|max_length[20]');
		$this->form_validation->set_rules('insentif_ft', 'Insentif FT', 'trim|max_length[20]');
		$this->form_validation->set_rules('tunjangan_insentif', 'Tunjangan Insentif', 'trim|max_length[20]');
		$this->form_validation->set_rules('bonus', 'Bonus', 'trim|max_length[20]');
		$this->form_validation->set_rules('honor', 'Honor', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('thr', 'THR', 'trim|max_length[20]');
		$this->form_validation->set_rules('gaji14', 'Gaji Ke 14', 'trim|max_length[20]');
		$this->form_validation->set_rules('pph21', 'PPH21', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('total_penghasilan', 'Total Penghasilan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('total_potongan', 'Total Potongan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('total_diterima', 'Total Diterima', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kwitansi', 'Kwitansi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('periode_mulai', 'Periode Mulai', 'trim|required');
		$this->form_validation->set_rules('periode_selesai', 'Periode Selesai', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_slip' => $this->input->post('nama_slip'),
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
				'tunjangan_kehadiran' => $this->input->post('tunjangan_kehadiran'),
				'total_mengajar' => $this->input->post('total_mengajar'),
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
				'thr' => $this->input->post('thr'),
				'gaji14' => $this->input->post('gaji14'),
				'pph21' => $this->input->post('pph21'),
				'total_penghasilan' => $this->input->post('total_penghasilan'),
				'total_potongan' => $this->input->post('total_potongan'),
				'total_diterima' => $this->input->post('total_diterima'),
				'kwitansi' => $this->input->post('kwitansi'),
				'periode_mulai' => $this->input->post('periode_mulai'),
				'periode_selesai' => $this->input->post('periode_selesai'),
			];

			
			$save_pegawai_slip_custom = $this->model_pegawai_slip_custom->change($id, $save_data);

			if ($save_pegawai_slip_custom) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pegawai_slip_custom', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pegawai_slip_custom');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pegawai_slip_custom');
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
	* delete Pegawai Slip Customs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pegawai_slip_custom_delete');

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
            set_message(cclang('has_been_deleted', 'pegawai_slip_custom'), 'success');
        } else {
            set_message(cclang('error_delete', 'pegawai_slip_custom'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pegawai Slip Customs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pegawai_slip_custom_view');

		$this->data['pegawai_slip_custom'] = $this->model_pegawai_slip_custom->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pegawai Slip Lain Detail');
		$this->render('backend/standart/administrator/pegawai_slip_custom/pegawai_slip_custom_view', $this->data);
	}
	
	/**
	* delete Pegawai Slip Customs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pegawai_slip_custom = $this->model_pegawai_slip_custom->find($id);

		
		
		return $this->model_pegawai_slip_custom->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pegawai_slip_custom_export');

		$this->model_pegawai_slip_custom->export('pegawai_slip_custom', 'pegawai_slip_custom');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pegawai_slip_custom_export');

		$this->model_pegawai_slip_custom->pdf('pegawai_slip_custom', 'pegawai_slip_custom');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pegawai_slip_custom_export');

		$table = $title = 'pegawai_slip_custom';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pegawai_slip_custom->find($id);
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

	public function import_slip()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		// dd(isset($_FILES["file_siswa"]["name"]));
		$this->db->trans_begin();

		// dd($this->input->post('kelas_sd'));

		$periode_mulai = $this->input->post('periode_mulai');
		$periode_selesai = $this->input->post('periode_selesai');
		$nama_slip = $this->input->post('nama_slip');
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
					
					$tunjangan_pengelolaan = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
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
					$honor = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
					$thr = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
					$gaji14 = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
					$pph21 = $worksheet->getCellByColumnAndRow(30, $row)->getValue();
					$total_penghasilan= $worksheet->getCellByColumnAndRow(31, $row)->getValue();
					$total_potongan = $worksheet->getCellByColumnAndRow(32, $row)->getValue();
					$total_diterima = $worksheet->getCellByColumnAndRow(33, $row)->getValue();
					$kwitansi = $worksheet->getCellByColumnAndRow(34, $row)->getValue();
				
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
						'nama_slip' => $nama_slip,
						'nama_lengkap' => $nama,
						'npp' => $npp,
						'golongan' => $golongan,
						'jabatan' => $jabatan,
						'gaji_pokok' => $gaji_pokok,
						'tunjangan_istri' => $tunjangan_istri,
						'tunjangan_anak' => $tunjangan_anak,
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
						'total_penghasilan' => $total_penghasilan,
						'total_potongan' => $total_potongan,
						'total_diterima' => $total_diterima,
						'kwitansi' => $kwitansi,
						'periode_mulai' => $periode_mulai,
						'periode_selesai' => $periode_selesai,
						'pph21' => $pph21,
					);
					if (!empty($thr)){
						$data_slip['thr'] = $thr;
					}
					if (!empty($gaji14)){
						$data_slip['gaji14'] = $gaji14;
					}
				
					//check apakah npp / pegawai tersebut di periode_selesai & periode_mulai yg sama sudah ada / belum
					// $check_slip = $this->mymodel->withquery("select * from pegawai_slip_custom where (npp = '".$npp."' and nama_lengkap = '".str_replace("'", "\'", $nama)."') and periode_mulai = '".$periode_mulai."' and periode_selesai = '".$periode_selesai."'", 'row');
					//check apakah npp / pegawai tersebut di periode_selesai & periode_mulai yg sama sudah ada / belum
					if ($npp == "-" || empty($npp)){
						$check_slip = $this->mymodel->withquery("select * from pegawai_slip_custom where nama_slip = '".$nama_slip."' and (nama_lengkap = '".str_replace("'", "''", $nama)."') and periode_selesai = '".$periode_selesai."' and periode_mulai = '".$periode_mulai."'", 'row');
					}
					else{
						$check_slip = $this->mymodel->withquery("select * from pegawai_slip_custom where nama_slip = '".$nama_slip."' and (nama_lengkap = '".str_replace("'", "''", $nama)."') and periode_selesai = '".$periode_selesai."' and periode_mulai = '".$periode_mulai."'", 'row');
					}
					
					if(!empty($check_slip)){
						$data_slip['updated_at'] = date("Y-m-d H:i:s");
						$insertId = $this->mymodel->update("pegawai_slip_custom", $data_slip, 'id_slip', $check_slip->id_slip);
					}else{
						$data_slip['created_at'] = date("Y-m-d H:i:s");
						$insertId = $this->mymodel->insertid("pegawai_slip_custom", $data_slip);
					}
					
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import gagal atas nama ' . $nama .' '. $this->db->last_query());
						redirect($_SERVER['HTTP_REFERER']);
					}
					else if(!empty($npp) ){
						//send notif
						$get_device_sd = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sd where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_smp = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_smp where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_sma = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sma where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_ft = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_ft where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						$get_device_pegawai = $this->mymodel->withquery("select npp, device_id, nama_lengkap from pegawai where npp = ".$this->db->escape($npp)." and deleted_at is null","row");
						/* $get_device_sd = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sd where nama_lengkap = '".str_replace("'", "''", $nama)."' and deleted_at is null","row");
						$get_device_smp = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_smp where nama_lengkap = '".str_replace("'", "''", $nama)."' and deleted_at is null","row");
						$get_device_sma = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_sma where nama_lengkap = '".str_replace("'", "''", $nama)."' and deleted_at is null","row");
						$get_device_ft = $this->mymodel->withquery("select npp, device_id, nama_lengkap from guru_ft where nama_lengkap = '".str_replace("'", "''", $nama)."' and deleted_at is null","row");
						$get_device_pegawai = $this->mymodel->withquery("select npp, device_id, nama_lengkap from pegawai where nama_lengkap = '".str_replace("'", "''", $nama)."' and deleted_at is null","row"); */
						$device_id = "";
						$bulan = formatBulan($periode_selesai);

						if (!empty($get_device_sd)) {
							$device_id = $get_device_sd->device_id;
							$nama_lengkap = $get_device_sd->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip_custom", array("nama_lengkap" => $get_device_sd->nama_lengkap, "npp" =>$get_device_sd->npp ), "id_slip", $insertId);
						}
						if (!empty($get_device_smp)) {
							$device_id = $get_device_smp->device_id;
							$nama_lengkap = $get_device_smp->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip_custom", array("nama_lengkap" => $get_device_smp->nama_lengkap, "npp" =>$get_device_smp->npp ), "id_slip", $insertId);
						}
						if (!empty($get_device_sma)) {
							$device_id = $get_device_sma->device_id;
							$nama_lengkap = $get_device_sma->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip_custom", array("nama_lengkap" => $get_device_sma->nama_lengkap, "npp" =>$get_device_sma->npp ), "id_slip", $insertId);
						}
						if (!empty($get_device_ft)) {
							$device_id = $get_device_ft->device_id;
							$nama_lengkap = $get_device_ft->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip_custom", array("nama_lengkap" => $get_device_ft->nama_lengkap, "npp" =>$get_device_ft->npp ), "id_slip", $insertId);
						}
						if (!empty($get_device_pegawai)) {
							$device_id = $get_device_pegawai->device_id;
							$nama_lengkap = $get_device_pegawai->nama_lengkap;
							//update nama sesuai di server
							$this->mymodel->update("pegawai_slip_custom", array("nama_lengkap" => $get_device_pegawai->nama_lengkap, "npp" =>$get_device_pegawai->npp ), "id_slip", $insertId);
						}
						$pesan = "Slip ".$nama_slip." sudah dapat dilihat di aplikasi.";
						if (!empty($device_id) && !empty($npp) && $npp == "682.25.254") {
							$this->send_notif("Slip  ".$nama_slip.'('.formatBulan($periode_selesai).')' .' '.strtoupper($nama), $pesan, $device_id, array("npp" => $npp, "id_slip" => $insertId) );
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


/* End of file pegawai_slip_custom.php */
/* Location: ./application/controllers/administrator/Pegawai Slip Custom.php */