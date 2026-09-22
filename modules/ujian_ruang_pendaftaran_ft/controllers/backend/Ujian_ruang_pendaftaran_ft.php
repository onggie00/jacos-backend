<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Ft Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Ft site
*|
*/
class Ujian_ruang_pendaftaran_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_pendaftaran_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Ruang Pendaftaran Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_pendaftaran_fts'] = $this->model_ujian_ruang_pendaftaran_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_pendaftaran_ft_counts'] = $this->model_ujian_ruang_pendaftaran_ft->count_all($filter, $field);

		$this->limit_page = 20;
		$config = [
			'base_url'     => 'administrator/ujian_ruang_pendaftaran_ft/index/',
			'total_rows'   => $this->model_ujian_ruang_pendaftaran_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);
		$this->session->set_userdata('url_back', $config['base_url']."?f=".$field."&q=".urlencode($filter));

		$this->template->title('Ruang Ujian PSB FT List');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_ft/ujian_ruang_pendaftaran_ft_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_pendaftaran_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_add');

		$this->template->title('Ruang Ujian PSB FT New');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_ft/ujian_ruang_pendaftaran_ft_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Pendaftaran Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_ruang', 'Nama Ruang', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('judul_ujian', 'Ujian', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kepala_sekolah', 'Kepala Sekolah', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('lokasi_ujian', 'Lokasi Ujian', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ruang' => $this->input->post('nama_ruang'),
				'judul_ujian' => $this->input->post('judul_ujian'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'kepala_sekolah' => $this->input->post('kepala_sekolah'),
				'lokasi_ujian' => $this->input->post('lokasi_ujian'),
				'meeting_id' => $this->input->post('meeting_id'),
				'meeting_url' => $this->input->post('meeting_url'),
				'meeting_password' => $this->input->post('meeting_password'),
				'maks_peserta' => $this->input->post('maks_peserta'),
			];

			
			$save_ujian_ruang_pendaftaran_ft = $this->model_ujian_ruang_pendaftaran_ft->store($save_data);
            

			if ($save_ujian_ruang_pendaftaran_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_pendaftaran_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_ft/edit/' . $save_ujian_ruang_pendaftaran_ft, 'Edit Ujian Ruang Pendaftaran Ft'),
						anchor('administrator/ujian_ruang_pendaftaran_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_pendaftaran_ft/edit/' . $save_ujian_ruang_pendaftaran_ft, 'Edit Ujian Ruang Pendaftaran Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_ft');
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
	* Update view Ujian Ruang Pendaftaran Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_update');

		$this->data['ujian_ruang_pendaftaran_ft'] = $this->model_ujian_ruang_pendaftaran_ft->find($id);

		$this->template->title('Ruang Ujian PSB FT Update');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_ft/ujian_ruang_pendaftaran_ft_update', $this->data);
	}

	/**
	* Update Ujian Ruang Pendaftaran Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_ruang', 'Nama Ruang', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('judul_ujian', 'Ujian', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kepala_sekolah', 'Kepala Sekolah', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('lokasi_ujian', 'Lokasi Ujian', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ruang' => $this->input->post('nama_ruang'),
				'judul_ujian' => $this->input->post('judul_ujian'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'kepala_sekolah' => $this->input->post('kepala_sekolah'),
				'lokasi_ujian' => $this->input->post('lokasi_ujian'),
				'meeting_id' => $this->input->post('meeting_id'),
				'meeting_url' => $this->input->post('meeting_url'),
				'meeting_password' => $this->input->post('meeting_password'),
				'maks_peserta' => $this->input->post('maks_peserta'),
			];

			
			$save_ujian_ruang_pendaftaran_ft = $this->model_ujian_ruang_pendaftaran_ft->change($id, $save_data);

			if ($save_ujian_ruang_pendaftaran_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_ft');
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
	* delete Ujian Ruang Pendaftaran Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$this->mymodel->delete("ujian_ruang_pendaftaran_detail_ft", "id_ruang_pendaftaran", $id);
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$this->mymodel->delete("ujian_ruang_pendaftaran_detail_ft", "id_ruang_pendaftaran", $id);
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'ujian_ruang_pendaftaran_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_pendaftaran_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Pendaftaran Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_view');

		$this->data['ujian_ruang_pendaftaran_ft'] = $this->model_ujian_ruang_pendaftaran_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ruang Ujian PSB FT Detail');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_ft/ujian_ruang_pendaftaran_ft_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Pendaftaran Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_pendaftaran_ft = $this->model_ujian_ruang_pendaftaran_ft->find($id);
		
		return $this->model_ujian_ruang_pendaftaran_ft->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_export');

		$this->model_ujian_ruang_pendaftaran_ft->export('ujian_ruang_pendaftaran_ft', 'ujian_ruang_pendaftaran_ft');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_export');

		// $this->model_ujian_ruang_pendaftaran_ft->pdf('ujian_ruang_pendaftaran_ft', 'ujian_ruang_pendaftaran_ft');
		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$where = "";
		$field = ['nomor', 'nomor_peserta', 'nama_lengkap', 'nama_kelas', 'nama_ruang', 'password'];
		$list_siswa = array();
		if (!empty($f) && !empty($q)) {
			if ($q == "FT" || $q == "SMA") {
				//get all siswa di jenjang tersebut
				$query = "select d.id_detail, s.nis, s.nomor_peserta_ujian, s.id_kelas, k.label as nama_kelas, k.id_tingkatan, r.nama_ruang, d.urutan_kursi, s.nama_lengkap, d.password from siswa_sma_aktif s 
				join kelas_sma k on s.id_kelas = k.id_kelas_sma and (k.label != 'Mutasi Keluar' and k.label != 'Alumni Lulus') 
				left join ujian_ruang_detail d on s.nomor_peserta_ujian = d.nomor_peserta_ujian 
				left join ujian_ruang r on d.id_ruang = r.id_ruang 
				order by k.id_kelas_sma ASC, s.nama_lengkap ASC ";
				$q_siswa = $this->mymodel->withquery($query,"result");
				foreach ($q_siswa as $key => $value) {
					$detail_sma = array(
						"nis" => $value->nis,
						"nomor_peserta_ujian" => $value->nomor_peserta_ujian,
						"id_kelas" => $value->id_kelas,
						"nama_lengkap" => $value->nama_lengkap,
						"nama_kelas" => $value->nama_kelas,
						"id_tingkatan" => $value->id_tingkatan,
						"nama_ruang" => $value->nama_ruang,
						"no_urut" => $value->urutan_kursi,
						"password" => (empty($value->password)) ? rand(100000, 999999) : $value->password,
						"jenjang" => "SMA"
					);
					//update password siswa
					//$this->mymodel->update("ujian_ruang_detail", array("password" => $detail_sma['password']), "id_detail", $value->id_detail);
					array_push($list_siswa, $detail_sma);
				}
				$query = "select d.id_detail, s.nis, s.nomor_peserta_ujian, s.id_kelas, k.label as nama_kelas, k.id_tingkatan, r.nama_ruang, d.urutan_kursi, s.nama_lengkap, d.password from siswa_ft_aktif s 
				join kelas_ft k on s.id_kelas = k.id_kelas_ft and (k.label != 'Mutasi Keluar' or k.label != 'Alumni Lulus') 
				left join ujian_ruang_detail d on s.nomor_peserta_ujian = d.nomor_peserta_ujian left join ujian_ruang r on d.id_ruang = r.id_ruang order by k.id_kelas_ft ASC, s.nama_lengkap ASC ";
				$q_siswa = $this->mymodel->withquery($query,"result");
				foreach ($q_siswa as $key => $value) {
					$detail_ft = array(
						"nis" => $value->nis,
						"nomor_peserta_ujian" => $value->nomor_peserta_ujian,
						"id_kelas" => $value->id_kelas,
						"nama_lengkap" => $value->nama_lengkap,
						"nama_kelas" => $value->nama_kelas,
						"id_tingkatan" => $value->id_tingkatan,
						"nama_ruang" => $value->nama_ruang,
						"no_urut" => $value->urutan_kursi,
						"password" => (empty($value->password)) ? rand(100000, 999999) : $value->password,
						"jenjang" => "FT"
					);
					//update password siswa
					//$this->mymodel->update("ujian_ruang_detail", array("password" => $detail_ft['password']), "id_detail", $value->id_detail);
					array_push($list_siswa, $detail_ft);
				}
			}
			else{
				$query = "select d.id_detail, s.nis, s.nomor_peserta_ujian, s.id_kelas, k.label as nama_kelas, k.id_tingkatan, r.nama_ruang, d.urutan_kursi, s.nama_lengkap, d.password from siswa_".strtolower($q)."_aktif s 
				join kelas_".strtolower($q)." k on s.id_kelas = k.id_kelas_".strtolower($q)." and (k.label != 'Mutasi Keluar' or k.label != 'Alumni Lulus') 
				left join ujian_ruang_detail d on s.nomor_peserta_ujian = d.nomor_peserta_ujian 
				left join ujian_ruang r on d.id_ruang = r.id_ruang order by k.id_kelas_".strtolower($q)." ASC, s.nama_lengkap ASC ";
				$q_siswa = $this->mymodel->withquery($query,"result");
				foreach ($q_siswa as $key => $value) {
					$detail = array(
						"nis" => $value->nis,
						"nomor_peserta_ujian" => $value->nomor_peserta_ujian,
						"id_kelas" => $value->id_kelas,
						"nama_lengkap" => $value->nama_lengkap,
						"nama_kelas" => $value->nama_kelas,
						"id_tingkatan" => $value->id_tingkatan,
						"nama_ruang" => $value->nama_ruang,
						"no_urut" => $value->urutan_kursi,
						"password" => (empty($value->password)) ? rand(100000, 999999) : $value->password,
						"jenjang" => strtoupper($q)
					);
					//update password siswa
					//$this->mymodel->update("ujian_ruang_detail", array("password" => $detail['password']), "id_detail", $value->id_detail);
					array_push($list_siswa, $detail);
				}
			}
		}
		else{
			//$this->session->set_flashdata('failed', 'Data tidak valid');
			echo "Gagal! Input tidak valid";
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
		$column = 'A';
		for ($i = 0; $i < count($field); $i++)  
		{
			//if (strpos($field_search[$i], "id") !== false) {
			if($field[$i] == "nomor") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if($field[$i] == "no_urut") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMOR URUT BANGKU'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names

		//start while loop to get data  
		$rowCount = 2;
		foreach ($list_siswa as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field); $j++) {
				$kolom = $field[$j];
		        if ($kolom == "nomor")  {
		            $value_data = ($key+1);
		        }
				elseif(!isset($value[$kolom])) { 
		            $value_data = NULL;  
		        }
		        elseif ($value[$kolom] != "")  {
		            $value_data = strip_tags($value[$kolom]);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Daftar Peserta Ujian '.strtoupper($q).' - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');

		//echo "<script>window.close()</script>";
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_ft_export');

		$table = $title = 'ujian_ruang_pendaftaran_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
    
        $data = $this->model_ujian_ruang_pendaftaran_ft->find($id);
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

	public function cetak_album($id_ruangan = null)
	{
		$id_ruangan = (int) $id_ruangan;
		$maks_peserta = 20; // bisa diubah dinamis

		if ($id_ruangan <= 0) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">ID Ruangan tidak valid</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftaran_ft');
		}

		$get_ruangan = $this->mymodel->getbywhere(
			'ujian_ruang_pendaftaran_ft',
			'id_ruang_pendaftaran',
			$id_ruangan,
			"row"
		);

		if (!$get_ruangan) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">Ruangan tidak ditemukan</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftaran_ft');
		}

		if (!empty($get_ruangan->maks_peserta)){
			$maks_peserta = $get_ruangan->maks_peserta;
		}

		$get_peserta = $this->mymodel->withquery("
			SELECT d.nomor_peserta, s.nama_lengkap, s.foto_peserta 
			FROM ujian_ruang_pendaftaran_detail_ft d
			LEFT JOIN siswa_ft s ON s.no_peserta = d.nomor_peserta
			WHERE d.id_ruang_pendaftaran = {$id_ruangan}
			ORDER BY d.nomor_peserta ASC limit {$maks_peserta}
		", "result");
		$peserta_awal = "";
		$peserta_akhir = "";

		foreach ($get_peserta as $key => $value) {
			if(empty($peserta_awal)){
				$peserta_awal = $value->nomor_peserta;
			}
			if(!empty($value->nomor_peserta)){
				$peserta_akhir = $value->nomor_peserta;
			}
		}

		if (empty($get_peserta)) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">Tidak ada data peserta</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftaran_ft');
		}

		// Tambah peserta kosong jika kurang
		$get_peserta = array_pad($get_peserta, $maks_peserta, (object)[
			'nomor_peserta' => '&nbsp;',
			'nama_lengkap'  => 'kosong',
			'foto_peserta'  => '0.jpg'
		]);

		// Hitung jumlah baris otomatis (4 kolom)
		$kolom_per_baris = 4;
		$jml_baris = ceil($maks_peserta / $kolom_per_baris);
$tableData = [];
$total = count($get_peserta);
$half = ceil($total / 2);

$left = array_slice($get_peserta, 0, $half);
$right = array_slice($get_peserta, $half);

$kolom_per_baris = 4;
$jml_baris = ceil($half / 2);

for ($i = 0; $i < $jml_baris; $i++) {

    $row = [
        $left[$i * 2] ?? null,
        $left[$i * 2 + 1] ?? null,
        $right[$i * 2] ?? null,
        $right[$i * 2 + 1] ?? null,
    ];

    // 🔥 INI KUNCINYA — taruh di depan array
    array_unshift($tableData, $row);
}

		$datas = [
			"ruangan" => $get_ruangan,
			"peserta" => $tableData,
			"jenjang" => "FT",
			"peserta_awal" => $peserta_awal,
			"peserta_akhir" => $peserta_akhir
		];

		$this->load->library('HtmlPdf');
		$pdf = new HTML2PDF('P', 'A4', 'en');

		ob_start();
		$this->load->view('template_album', $datas);
		$html = ob_get_clean();

		$pdf->WriteHTML($html);
		$nama_file = $get_ruangan->nama_ruang . '_' . str_replace('/', '_', $get_ruangan->tahun_ajaran) . '.pdf';
		$path_file = FCPATH . 'uploads/album/' . $nama_file;

		$pdf->Output($path_file, 'F');
		redirect('uploads/album/' . $nama_file);
	}

	public function kosongkan_ruang($id = null){
		$id_ruang = $id;
		$this->mymodel->delete("ujian_ruang_pendaftaran_detail_ft", "id_ruang_pendaftaran", $id_ruang);

		set_message(
			cclang('has_been_deleted', [
		]), 'success');
		redirect('administrator/ujian_ruang_pendaftaran_ft');
	}

}


/* End of file ujian_ruang_pendaftaran_ft.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Pendaftaran Ft.php */