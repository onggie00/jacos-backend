<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Detail Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Detail site
*|
*/
class Ujian_ruang_detail extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_detail');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Ruang Details
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_detail_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$suffix = "";
		if (!empty($filter) && !empty($field)) {
			$suffix .= "?q=".$filter."&f=".$field;
		}
		//$this->load->library('pagination');
		$limit = 40;//$this->limit_page
		$this->data['ujian_ruang_details'] = $this->model_ujian_ruang_detail->get($filter, $field, $limit, $offset);
		$this->data['ujian_ruang_detail_counts'] = $this->model_ujian_ruang_detail->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang_detail/index/',
			'total_rows'   => $this->model_ujian_ruang_detail->count_all($filter, $field),
			'per_page'     => $limit,
			'uri_segment'  => 4,
		];
		$config['first_url'] = $config['base_url'].$suffix;

		if ($field == "jenjang") {
			$this->data['tanggal_terakhir_bayar'] = $this->mymodel->getbywhere("setting_sync_ujian","jenjang",strtolower($filter),"row")->tanggal_terakhir_bayar;
		}
		else{
			$this->data['tanggal_terakhir_bayar'] = date("Y-m-d");
		}
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ujian Ruang Detail List');
		$this->render('backend/standart/administrator/ujian_ruang_detail/ujian_ruang_detail_list', $this->data);
	}
	
	
		/**
	* Update view Ujian Ruang Details
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_detail_update');

		$this->data['ujian_ruang_detail'] = $this->model_ujian_ruang_detail->find($id);

		$this->template->title('Ujian Ruang Detail Update');
		$this->render('backend/standart/administrator/ujian_ruang_detail/ujian_ruang_detail_update', $this->data);
	}

	/**
	* Update Ujian Ruang Details
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_detail_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ruang', 'Ruang Ujian', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_ruang_kelas', 'Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nomor_peserta_ujian', 'Nomor Peserta Ujian', 'trim|max_length[50]');
		$this->form_validation->set_rules('boleh_ujian', 'Boleh Ujian?', 'trim|required');
		$this->form_validation->set_rules('nama_siswa', 'Nama Siswa', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ruang' => $this->input->post('id_ruang'),
				'jenjang' => $this->input->post('jenjang'),
				'id_ruang_kelas' => $this->input->post('id_ruang_kelas'),
				'nomor_peserta_ujian' => $this->input->post('nomor_peserta_ujian'),
				'boleh_ujian' => $this->input->post('boleh_ujian'),
				'nama_siswa' => $this->input->post('nama_siswa'),
				'password' => $this->input->post('password')
			];

			
			$save_ujian_ruang_detail = $this->model_ujian_ruang_detail->change($id, $save_data);

			if ($save_ujian_ruang_detail) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_detail', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_detail');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_detail');
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
	* delete Ujian Ruang Details
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_detail_delete');

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
            set_message(cclang('has_been_deleted', 'ujian_ruang_detail'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_detail'), 'error');
        }

		redirect_back();
	}

	public function delete_all(){
		$jenjang = strtoupper($this->input->post('jenjang'));
		$empty_nomor_ujian = $this->mymodel->update("siswa_".strtolower($jenjang)."_aktif", array("nomor_peserta_ujian" => null), "nomor_peserta_ujian is not null", '');
		$delete  = $this->mymodel->delete("ujian_ruang_detail", "jenjang", $jenjang);

		if ($delete || $empty_nomor_ujian) {
			$this->session->set_flashdata('success', 'Data berhasil dihapus, Data siswa sudah diupdate');
		} else {
			$this->session->set_flashdata('failed', 'Data gagal dihapus atau data sudah kosong');
		}

		redirect_back();
	}

		/**
	* View view Ujian Ruang Details
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_detail_view');

		$this->data['ujian_ruang_detail'] = $this->model_ujian_ruang_detail->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ujian Ruang Detail Detail');
		$this->render('backend/standart/administrator/ujian_ruang_detail/ujian_ruang_detail_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Details
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_detail = $this->model_ujian_ruang_detail->find($id);

		
		
		return $this->model_ujian_ruang_detail->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_detail_export');

		//$this->model_ujian_ruang_detail->export('ujian_ruang_detail', 'ujian_ruang_detail');
		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$where = "";
		$field = ['nomor', 'nis', 'nomor_peserta_ujian', 'nama_lengkap', 'nama_kelas', 'nama_ruang', 'no_urut', 'password', 'tgl_download_kartu'];
		$list_siswa = array();
		if (!empty($f) && !empty($q)) {
			if ($q == "FT" || $q == "SMA") {
				//get all siswa di jenjang tersebut
				$query = "select d.id_detail, s.nis, s.nomor_peserta_ujian, s.id_kelas, k.label as nama_kelas, k.id_tingkatan, r.nama_ruang, d.urutan_kursi, s.nama_lengkap, d.password, d.tgl_download_kartu from siswa_sma_aktif s 
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
						"tgl_download_kartu" => (!empty($value->tgl_download_kartu)) ? date("d-m-Y H:i", strtotime($value->tgl_download_kartu)) : NULL,
						"jenjang" => "SMA"
					);
					//update password siswa
					//$this->mymodel->update("ujian_ruang_detail", array("password" => $detail_sma['password']), "id_detail", $value->id_detail);
					array_push($list_siswa, $detail_sma);
				}
				$query = "select d.id_detail, s.nis, s.nomor_peserta_ujian, s.id_kelas, k.label as nama_kelas, k.id_tingkatan, r.nama_ruang, d.urutan_kursi, s.nama_lengkap, d.password, d.tgl_download_kartu from siswa_ft_aktif s 
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
						"tgl_download_kartu" => (!empty($value->tgl_download_kartu)) ? date("d-m-Y H:i", strtotime($value->tgl_download_kartu)) : NULL,
						"jenjang" => "FT"
					);
					//update password siswa
					//$this->mymodel->update("ujian_ruang_detail", array("password" => $detail_ft['password']), "id_detail", $value->id_detail);
					array_push($list_siswa, $detail_ft);
				}
			}
			else{
				$query = "select d.id_detail, s.nis, s.nomor_peserta_ujian, s.id_kelas, k.label as nama_kelas, k.id_tingkatan, r.nama_ruang, d.urutan_kursi, s.nama_lengkap, d.password, d.tgl_download_kartu from siswa_".strtolower($q)."_aktif s 
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
						"tgl_download_kartu" => (!empty($value->tgl_download_kartu)) ? date("d-m-Y H:i", strtotime($value->tgl_download_kartu)) : NULL,
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

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_detail_export');

		$this->model_ujian_ruang_detail->pdf('ujian_ruang_detail', 'ujian_ruang_detail');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_detail_export');

		$table = $title = 'ujian_ruang_detail';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_detail->find($id);
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

	public function import_peserta_ujian()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p=$_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				//redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);
			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				//$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					$jenjang = strtolower($this->input->post("jenjang"));
				/* --- MAP NAMA KOLOM UNTUK PESAN ERROR --- */
				$colHeaders = array(
					'id_ruang' => 'ID RUANG',
					'jenjang' => 'JENJANG',
					'id_ruang_kelas' => 'ID KELAS',
					'nomor_peserta_ujian' => 'NOMOR PESERTA UJIAN',
					'urutan_kursi' => 'URUTAN KURSI',
					'nis' => 'NIS',
					'nama_siswa' => 'NAMA SISWA',
					'password' => 'PASSWORD'
				);
					$nis = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$nomor_peserta_ujian = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$get_siswa = $this->mymodel->withquery("select id_siswa_".$jenjang."_aktif, id_siswa_".$jenjang.", nama_lengkap from siswa_".$jenjang."_aktif where nis = '".$nis."' order by id_siswa_".$jenjang."_aktif desc", "row");

					/* if (empty($get_siswa) && $jenjang == "sma") {
						$get_siswa = $this->mymodel->withquery("select id_siswa_ft_aktif, id_siswa_ft, nama_lengkap from siswa_ft_aktif where nis = ".$nis, "row");
						$jenjang = "ft";
					}
					else if (empty($get_siswa) && $jenjang == "ft") {
						$get_siswa = $this->mymodel->withquery("select id_siswa_sma_aktif, id_siswa_sma, nama_lengkap from siswa_sma_aktif where nis = ".$nis, "row");
						$jenjang = "sma";
					} */
					$kelas = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$get_kelas = $this->mymodel->withquery("select id_kelas_".$jenjang.", id_tingkatan, label from kelas_".$jenjang." where label like '%".$kelas."%'","row");
					/* if (empty($get_kelas) && $jenjang == "sma") {
						$get_kelas = $this->mymodel->withquery("select id_kelas_ft, id_tingkatan, label from kelas_ft where label like '%".$kelas."%'","row");
						$jenjang = "ft";
					}
					else if (empty($get_kelas) && $jenjang == "ft") {
						$get_kelas = $this->mymodel->withquery("select id_kelas_sma, id_tingkatan, label from kelas_sma where label like '%".$kelas."%'","row");
						$jenjang = "sma";
					} */
					$keterangan = $this->db->last_query();
					if(empty($get_kelas)){
						$this->session->set_flashdata('failed', 'Kelas '.$kelas.' tidak ditemukan '.$keterangan);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$get_ruang_ujian = $this->mymodel->withquery("select * from ujian_ruang where nama_ruang='".$worksheet->getCellByColumnAndRow(5, $row)->getValue()."'","row");
					//echo $this->db->last_query();
					if (empty($get_ruang_ujian) ) {
						$this->session->set_flashdata('failed', 'Ruang ujian '.$worksheet->getCellByColumnAndRow(3, $row)->getValue().' tidak ditemukan (baris '.$worksheet->getCellByColumnAndRow(0, $row)->getValue().')' );
						redirect($_SERVER['HTTP_REFERER']);
					}
					$id_ruang = $get_ruang_ujian->id_ruang;
					$urutan_kursi = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$password = (empty($worksheet->getCellByColumnAndRow(7, $row)->getValue())) ? rand(100000, 999999) : $worksheet->getCellByColumnAndRow(7, $row)->getValue();

					$data_siswa_aktif = array(
						"nomor_peserta_ujian" => $nomor_peserta_ujian,
					);
					$data_ruang_kelas = array(
						"id_ruang" => $id_ruang,
						"id_tingkatan" => $get_kelas->id_tingkatan,
						"kapasitas" => 0,
						"kelas" => $get_kelas->label,
						"jenjang_kelas" => strtoupper($jenjang),
						"nomor_peserta_awal" => "-",
						"nomor_peserta_akhir" => "-",
					);
					$data_ruang_detail = array(
						"id_ruang" => $id_ruang,
						"jenjang" => strtoupper($jenjang),
						"nomor_peserta_ujian" => $nomor_peserta_ujian,
						"urutan_kursi" => $urutan_kursi,
						"nis" => $nis,
						"nama_siswa" => $get_siswa->nama_lengkap,
						"password" => $password
					);
					//check null
					foreach ($data_siswa_aktif as $key => $item) {
						if(empty($item)){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('failed', "Data Siswa ada yang kosong ".($colHeaders[$key] ?? $key));
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					foreach ($data_ruang_detail as $key => $item) {
						if(empty($item)){
							if($key == "urutan_kursi"){
								continue;
							}
							else{
								$this->db->trans_rollback();
								//$this->load->library("session");
								$this->session->set_flashdata('failed', "Data Ruang Detail ada yang kosong ".($colHeaders[$key] ?? $key)." ".$worksheet->getCellByColumnAndRow(0, $row)->getValue()." ".$nis." ".$worksheet->getCellByColumnAndRow(3, $row)->getValue());
								//redirect($_SERVER['HTTP_REFERER']);
							}
						}
					}
					$update_siswa_aktif = $this->mymodel->update("siswa_".$jenjang."_aktif", $data_siswa_aktif, "nis", $nis);
					$keterangan_siswa = $this->db->last_query();
					//cek data kelas / tingkatan sudah ada / belum
					$cek_kelas = $this->mymodel->withquery("select * from  ujian_ruang_kelas where id_ruang = '".$id_ruang."' and id_tingkatan = '".$get_kelas->id_tingkatan."' and jenjang_kelas = '".strtoupper($jenjang)."' and kelas = '".$kelas."'","row");
					if (!empty($cek_kelas)) {
						$update_ruang_kelas = $this->mymodel->update("ujian_ruang_kelas", $data_ruang_kelas, "id_ruang_kelas", $cek_kelas->id_ruang_kelas);
						$id_ruang_kelas = $cek_kelas->id_ruang_kelas;
					}
					else{
						$update_ruang_kelas = $this->mymodel->insertid("ujian_ruang_kelas", $data_ruang_kelas);
						$id_ruang_kelas = $update_ruang_kelas;
					}
					$keterangan_kelas = $this->db->last_query();
					//cek data peserta detail sudah ada / belum
					$cek_detail = $this->mymodel->withquery("select * from  ujian_ruang_detail where nomor_peserta_ujian = '".$nomor_peserta_ujian."'","row");
					if (!empty($cek_detail)) {
						$data_ruang_detail["updated_at"] = date("Y-m-d H:i:s");
						$data_ruang_detail["id_ruang_kelas"] = $id_ruang_kelas;
						$update_data_ruang = $this->mymodel->update("ujian_ruang_detail", $data_ruang_detail, "nomor_peserta_ujian", $nomor_peserta_ujian);
					}
					else{
						$data_ruang_detail["id_ruang_kelas"] = $id_ruang_kelas;
						$update_data_ruang = $this->mymodel->insertid("ujian_ruang_detail", $data_ruang_detail);
					}
					$keterangan_ruang = $this->db->last_query();
					//update total kapasitas per kelas
						$kapasitas = $this->mymodel->withquery("select count(nomor_peserta_ujian) as total from ujian_ruang_detail where id_ruang_kelas = '".$id_ruang_kelas."'","row")->total;
						$update_kapasitas = $this->mymodel->update("ujian_ruang_kelas", array("kapasitas" => $kapasitas), "id_ruang_kelas", $id_ruang_kelas);
						$keterangan_kapasitas = $this->db->last_query();
						// dd($data_pendaftaran);
						if ($update_siswa_aktif === false) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('failed', 'Import data gagal (Data siswa belum dikosongkan) ' . $data_siswa_aktif["nomor_peserta_ujian"]. ' '.$keterangan_siswa);
							redirect($_SERVER['HTTP_REFERER']);
						}
						else if($update_data_ruang === false){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('failed', 'Import data gagal (Ruang Detail belum dikosongkan)' . $data_siswa_aktif["nomor_peserta_ujian"] . ' '.$keterangan_ruang);
							redirect($_SERVER['HTTP_REFERER']);
						}
						else if($update_ruang_kelas === false) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('failed', 'Import data gagal (Kelas tidak ditemukan)' . $data_siswa_aktif["nomor_peserta_ujian"]. ' '.$keterangan_kelas);
							redirect($_SERVER['HTTP_REFERER']);
						}
						else if($update_kapasitas === false) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('failed', 'Import data gagal (Kapasitas update gagal)' . $data_siswa_aktif["nomor_peserta_ujian"]. ' '.$keterangan_kapasitas);
							redirect($_SERVER['HTTP_REFERER']);
						}

					}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data siswa berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->session->set_flashdata('failed', 'Import data siswa gagal, File tidak valid');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function sync_data(){
		$get = $this->input->get();
		$tanggal = $get['tanggal_terakhir_bayar'];
		$ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '".date('Y-m-d')."' and tanggal_selesai >= '".date('Y-m-d')."'", "row");
		if ($get["f"] == "id_ruang_kelas") {
			$get_jenjang = $this->mymodel->withquery("select jenjang from ujian_ruang_detail where id_ruang_kelas = ".$get["q"],"row");
			$this->mymodel->update("setting_sync_ujian", array("tanggal_terakhir_bayar" => $tanggal), "jenjang",strtolower($get_jenjang->jenjang));
			$get_siswa = $this->mymodel->withquery("select id_siswa_".strtolower($get_jenjang->jenjang)."_aktif as id_siswa_aktif, nis, nomor_peserta_ujian, nama_lengkap, id_tahun_ajaran, acc_ujian from siswa_".strtolower($get_jenjang->jenjang)."_aktif where nomor_peserta_ujian != '' and id_tahun_ajaran = '".$ajaran_aktif->id_tahun_ajaran."' order by id_siswa_".strtolower($get_jenjang->jenjang)."_aktif DESC","result");
			foreach ($get_siswa as $key => $value) {
				$nama_aktif = "id_siswa_".strtolower($jenjang)."_aktif";
				$get_spp = $this->mymodel->withquery("select * from spp_".strtolower($get_jenjang->jenjang)." where id_siswa_aktif = '".$value->id_siswa_aktif."' and id_tahun_ajaran = '".$value->id_tahun_ajaran."'", "row");
				
				//$bulan_ini = date("Y-m-d", strtotime("-1 months"));
				$bulan_ini = $tanggal;
				$bulan_ini = formatBulan($bulan_ini);
				$get_transaksi = $this->mymodel->withquery("select status_transaksi,bulan from transaksi_spp where id_spp = '".$get_spp->id."' and no_transaksi like '%".$get_jenjang->jenjang."%' and status_transaksi = '2' and bulan = '".$bulan_ini."' and id_tahun_ajaran = '".$ajaran_aktif->id_tahun_ajaran."' order by id_transaksi DESC","row");
				
				//if ($get_transaksi->bulan == $bulan_ini) {
				if ($get_transaksi->status_transaksi == 2 || $value->acc_ujian == "1") {
					$this->mymodel->update("ujian_ruang_detail", array("nis" => $value->nis, "nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "YA"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
				}
				else if ($get_transaksi->status_transaksi == 1 || $get_transaksi->status_transaksi == 0) {
					$this->mymodel->update("ujian_ruang_detail", array("nis" => $value->nis, "nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "TIDAK"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
					//echo " YA <br/>";
				}
				else{
					$this->mymodel->update("ujian_ruang_detail", array("nis" => $value->nis, "nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "TIDAK"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
				}
			}
			$this->session->set_flashdata('success', 'Sinkronisasi data per Kelas sukses');
		}
		else if($get["f"] == "jenjang"){
			$this->mymodel->update("setting_sync_ujian", array("tanggal_terakhir_bayar" => $tanggal), "jenjang",strtolower($get['q']));
			$get_siswa = $this->mymodel->withquery("select id_siswa_".strtolower($get['q'])."_aktif as id_siswa_aktif, nomor_peserta_ujian, nama_lengkap, id_tahun_ajaran from siswa_".strtolower($get["q"])."_aktif where nomor_peserta_ujian != '' and id_tahun_ajaran = '".$ajaran_aktif->id_tahun_ajaran."' order by id_siswa_".strtolower($get["q"])."_aktif DESC","result");

			foreach ($get_siswa as $key => $value) {
				//echo $value->nama_lengkap." ";
				$nama_aktif = "id_siswa_".strtolower($get["q"])."_aktif";
				$get_spp = $this->mymodel->withquery("select * from spp_".strtolower($get["q"])." where id_siswa_aktif = '".$value->id_siswa_aktif."' and id_tahun_ajaran = '".$value->id_tahun_ajaran."'", "row");
				//$bulan_ini = date("Y-m-d", strtotime("-1 months"));//formatBulan(date("n", strtotime("-1 months")));
				$bulan_ini = $tanggal;
				$bulan_ini = formatBulan($bulan_ini);
				$get_transaksi = $this->mymodel->withquery("select status_transaksi, bulan from transaksi_spp where id_spp = '".$get_spp->id."' and no_transaksi like '%".strtoupper($get["q"])."%' and status_transaksi = '2' and bulan = '".$bulan_ini."' and id_tahun_ajaran = '".$ajaran_aktif->id_tahun_ajaran."' order by id_transaksi DESC","row");
				//echo $this->db->last_query();
				//echo $bulan_ini." - ".$get_transaksi->bulan." <br/>";
				if ($get_transaksi->status_transaksi == 2) {
					$this->mymodel->update("ujian_ruang_detail", array("nis" => $value->nis, "nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "YA"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
					//echo " YA <br/>";
				}
				else if ($get_transaksi->status_transaksi == 1 || $get_transaksi->status_transaksi == 0) {
					$this->mymodel->update("ujian_ruang_detail", array("nis" => $value->nis, "nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "TIDAK"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
					//echo " YA <br/>";
				}
				else{
					$this->mymodel->update("ujian_ruang_detail", array("nis" => $value->nis, "nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "TIDAK"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
				}
			}
			$this->session->set_flashdata('success', 'Sinkronisasi data per Jenjang sukses');
		}
		else{
			$this->session->set_flashdata('failed', 'Sync Data Failed, Please select class or education level');
		}
		redirect_back();
	}

	
}


/* End of file ujian_ruang_detail.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Detail.php */