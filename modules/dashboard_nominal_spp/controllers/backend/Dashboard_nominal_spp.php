<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard_nominal_spp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}
		$data = [];
		$this->render('backend/standart/dashboard', $data);
	}

	public function nominal_spp()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$this->template->title('Grafik SPP');
		$this->render('backend/standart/administrator/grafik_nominal_spp', $data);
	}

	public function chart_nominal_spp(){
		$start_date = (empty($this->input->post("start_date"))) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $this->input->post("start_date") ;
		$end_date = (empty($this->input->post("end_date"))) ? date("Y-m-d")." 23:59:59" : $this->input->post("end_date") ;
		$start_date_text = formatBulan($start_date)." ".date("Y", strtotime($start_date));
		$end_date_text = formatBulan($end_date)." ".date("Y", strtotime($end_date));
		$jenjang = $this->input->post("jenjang");
		$table_name = "siswa_".strtolower($jenjang)."_aktif";
		$kelas = "kelas_".strtolower($jenjang);
		$where = "";
		$data = array();
		$data_tahun_ajaran = array();

		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%' ";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%' ";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%'";
			}
		}
		$get_data = $this->mymodel->withquery("select (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1' or tr.status_transaksi = '2') ) as total_nominal_spp, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1') ) as total_nominal_spp_belum_dibayar, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and tr.status_transaksi = '2' ) as total_nominal_spp_lunas from siswa_".strtolower($jenjang)."_aktif s group by total_nominal_spp","result");
		//echo $this->db->last_query();
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					"keterangan" => "Total SPP periode ".date("d/m/Y", strtotime($start_date))." - ".date("d/m/Y", strtotime($end_date)), //"Total Siswa Daftar", "Total Siswa Daftar Ulang",
					"total_nominal_spp" => $value->total_nominal_spp,
					"total_nominal_spp_belum_bayar" => $value->total_nominal_spp_belum_dibayar,
					"total_nominal_spp_lunas" => $value->total_nominal_spp_lunas,
				);
				array_push($data, $in_array);
			}
		}

		echo json_encode($data);
	}

	public function export_chart_spp(){
		$start_date = (empty($this->input->get("start_date"))) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $this->input->get("start_date") ;
		$end_date = (empty($this->input->get("end_date"))) ? date("Y-m-d")." 23:59:59" : $this->input->get("end_date") ;
		$start_date_text = formatBulan($start_date)." ".date("Y", strtotime($start_date));
		$end_date_text = formatBulan($end_date)." ".date("Y", strtotime($end_date));
		$jenjang = $this->input->get("jenjang");
		$table_name = "siswa_".strtolower($jenjang)."_aktif";
		$where = "";
		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%' ";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%' ";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%'";
			}
		}

		$field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'nis', 'kelas', 'tahun_ajaran', 'description', 'total_biaya', 'status_transaksi', 'expired_datetime', 'created_at', 'updated_at'];
		
		//export all data based on input where all field
			$iterasi = 1;
	        $get_data = $this->mymodel->withquery("select tr.*, s.nis, spp.kelas, spp.tahun_ajaran from transaksi_spp tr left join spp_".strtolower($jenjang)." spp on tr.id_spp = spp.id left join siswa_".strtolower($jenjang)."_aktif s on spp.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif  ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1' or tr.status_transaksi = '2')","result");
	        $get_data2 = $this->mymodel->withquery("select tr.*, s.nis, spp.kelas, spp.tahun_ajaran from transaksi_spp tr left join spp_".strtolower($jenjang)." spp on tr.id_spp = spp.id left join siswa_".strtolower($jenjang)."_aktif s on spp.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif  ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1')","result");
	        $get_data3 = $this->mymodel->withquery("select tr.*, s.nis, spp.kelas, spp.tahun_ajaran from transaksi_spp tr left join spp_".strtolower($jenjang)." spp on tr.id_spp = spp.id left join siswa_".strtolower($jenjang)."_aktif s on spp.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif  ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '2')","result");

		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("Total Nominal SPP");  
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_exists[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIUBAH'));
			}
			else if(strpos($field_exists[$i], "expired_datetime") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TRANSAKSI KADALUARSA'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "file") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/program_anggaran_sd/").$value_data;
		        }
		        if (strpos($kolom, "status_transaksi") !== false) {
		        	if ($value_data == "2") {
		        		$value_data = "Lunas";
		        	}
		        	else if($value_data == "1"){
		        		$value_data = "Belum Dibayar";
		        	}
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}

		$objPHPExcel->createSheet();
		// Set the active Excel worksheet to sheet 1
		$objPHPExcel->setActiveSheetIndex(1);
		$objPHPExcel->getActiveSheet()->setTitle("Total SPP Belum Dibayar");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_exists[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIUBAH'));
			}
			else if(strpos($field_exists[$i], "expired_datetime") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TRANSAKSI KADALUARSA'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data2 as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "file") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/program_anggaran_sd/").$value_data;
		        }
		        if (strpos($kolom, "status_transaksi") !== false) {
		        	if ($value_data == "2") {
		        		$value_data = "Lunas";
		        	}
		        	else if($value_data == "1"){
		        		$value_data = "Belum Dibayar";
		        	}
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}

		$objPHPExcel->createSheet();
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(2);  
		$objPHPExcel->getActiveSheet()->setTitle("Total SPP Lunas");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_exists[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIUBAH'));
			}
			else if(strpos($field_exists[$i], "expired_datetime") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TRANSAKSI KADALUARSA'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data3 as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "file") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/program_anggaran_sd/").$value_data;
		        }
		        if (strpos($kolom, "status_transaksi") !== false) {
		        	if ($value_data == "2") {
		        		$value_data = "Lunas";
		        	}
		        	else if($value_data == "1"){
		        		$value_data = "Belum Dibayar";
		        	}
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}

		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Grafik Nominal spp_'.$jenjang.'_'.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
		//echo json_encode("success");
	}

	public function chart_nominal_spp_overall(){
		$start_date = (empty($this->input->post("start_date"))) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $this->input->post("start_date") ;
		$end_date = (empty($this->input->post("end_date"))) ? date("Y-m-d")." 23:59:59" : $this->input->post("end_date") ;
		$start_date_text = formatBulan($start_date)." ".date("Y", strtotime($start_date));
		$end_date_text = formatBulan($end_date)." ".date("Y", strtotime($end_date));
		//$jenjang = $this->input->post("jenjang");
		$table_name = "siswa_".strtolower($jenjang)."_aktif";
		$kelas = "kelas_".strtolower($jenjang);
		$where = "";
		$data = array();
		$data_tahun_ajaran = array();

		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%' ";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%' ";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where tr.description like '%".$start_date_text."%'";
			}
		}
		$total_lunas = 0;
		$total_belum_lunas = 0;
		$jenjang = "sd";
		$get_data = $this->mymodel->withquery("select (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1' or tr.status_transaksi = '2') ) as total_nominal_spp, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1') ) as total_nominal_spp_belum_dibayar, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and tr.status_transaksi = '2' ) as total_nominal_spp_lunas from siswa_".strtolower($jenjang)."_aktif s group by total_nominal_spp","row");
		$total_lunas = $total_lunas + $get_data->total_nominal_spp_lunas;
		$total_belum_lunas = $total_belum_lunas + $get_data->total_nominal_spp_belum_dibayar;
		$jenjang = "smp";
		$get_data = $this->mymodel->withquery("select (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1' or tr.status_transaksi = '2') ) as total_nominal_spp, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1') ) as total_nominal_spp_belum_dibayar, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and tr.status_transaksi = '2' ) as total_nominal_spp_lunas from siswa_".strtolower($jenjang)."_aktif s group by total_nominal_spp","row");
		$total_lunas = $total_lunas + $get_data->total_nominal_spp_lunas;
		$total_belum_lunas = $total_belum_lunas + $get_data->total_nominal_spp_belum_dibayar;
		$jenjang = "sma";
		$get_data = $this->mymodel->withquery("select (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1' or tr.status_transaksi = '2') ) as total_nominal_spp, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1') ) as total_nominal_spp_belum_dibayar, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and tr.status_transaksi = '2' ) as total_nominal_spp_lunas from siswa_".strtolower($jenjang)."_aktif s group by total_nominal_spp","row");
		$total_lunas = $total_lunas + $get_data->total_nominal_spp_lunas;
		$total_belum_lunas = $total_belum_lunas + $get_data->total_nominal_spp_belum_dibayar;
		$jenjang = "ft";
		$get_data = $this->mymodel->withquery("select (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1' or tr.status_transaksi = '2') ) as total_nominal_spp, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and (tr.status_transaksi = '0' or tr.status_transaksi = '1') ) as total_nominal_spp_belum_dibayar, (select sum(tr.total_biaya) from transaksi_spp tr ".$where." and tr.no_transaksi like '%LISPP-".strtoupper($jenjang)."%' and tr.status_transaksi = '2' ) as total_nominal_spp_lunas from siswa_".strtolower($jenjang)."_aktif s group by total_nominal_spp","row");
		$total_lunas = $total_lunas + $get_data->total_nominal_spp_lunas;
		$total_belum_lunas = $total_belum_lunas + $get_data->total_nominal_spp_belum_dibayar;
		
		//echo $this->db->last_query();
			$in_array = array(
				"label" => "Sudah Dibayar",
				"value" => $total_lunas
			);
			array_push($data, $in_array);
			$in_array = array(
				"label" => "Belum Dibayar",
				"value" => $total_belum_lunas
			);
			array_push($data, $in_array);

		echo json_encode($data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */