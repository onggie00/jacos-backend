<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard_nominal_psb extends Admin	
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

	public function nominal_psb()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "sd";
		$this->template->title('Grafik Nominal PSB');
		$this->render('backend/standart/administrator/grafik_nominal_psb', $data);
	}

	public function chart_nominal_psb(){
		$start_date = (empty($this->input->post("start_date"))) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $this->input->post("start_date") ;
		$end_date = (empty($this->input->post("end_date"))) ? date("Y-m-d")." 23:59:59" : $this->input->post("end_date") ;
		$jenjang = $this->input->post("jenjang");
		$table_name = "siswa_".strtolower($jenjang);
		$kelas = "kelas_".strtolower($jenjang);
		$where = "";
		$data = array();
		$data_tahun_ajaran = array();

		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where tr.created_at <= '".$end_date."'";
			}
		}
		$get_data = $this->mymodel->withquery("select (select sum(tr.total_biaya) from transaksi tr left join ".$table_name." s on tr.no_transaksi = s.no_transaksi ".$where." and (tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' or tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_nominal_siswa, (select sum(tr.total_biaya) from transaksi tr left join ".$table_name." s on tr.no_transaksi = s.no_transaksi ".$where." and (tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' ) and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_nominal_siswa_daftar, (select sum(tr.total_biaya) from transaksi tr left join ".$table_name." s on tr.no_transaksi = s.no_transaksi ".$where." and ( tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_nominal_siswa_daftar_ulang from transaksi t group by total_nominal_siswa","result");
		//echo $this->db->last_query();
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					"keterangan" => "Total Nominal Biaya", //"Total Siswa Daftar", "Total Siswa Daftar Ulang",
					"total_nominal_siswa" => $value->total_nominal_siswa,
					"total_nominal_siswa_daftar" => $value->total_nominal_siswa_daftar,
					"total_nominal_siswa_daftar_ulang" => $value->total_nominal_siswa_daftar_ulang,
				);
				array_push($data, $in_array);
			}
		}

		echo json_encode($data);
	}

	public function export_chart_psb(){
		$start_date = (empty($this->input->get("start_date"))) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $this->input->get("start_date") ;
		$end_date = (empty($this->input->get("end_date"))) ? date("Y-m-d")." 23:59:59" : $this->input->get("end_date");
		$jenjang = $this->input->get("jenjang");
		$table_name = "siswa_".strtolower($jenjang);
		$where = "";
		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where tr.created_at <= '".$end_date."'";
			}
		}

		$field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'description', 'total_biaya', 'status_transaksi', 'expired_datetime', 'created_at', 'updated_at'];
		
		//export all data based on input where all field
			$iterasi = 1;
	        $get_data = $this->mymodel->withquery("select * from transaksi tr left join ".$table_name." s on tr.no_transaksi = s.no_transaksi ".$where." and (tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' or tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.status_transaksi = '1' and tr.is_show = '1' ","result");
	        $get_data2 = $this->mymodel->withquery("select * from transaksi tr left join ".$table_name." s on tr.no_transaksi = s.no_transaksi ".$where." and (tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' ) and tr.status_transaksi = '1' and tr.is_show = '1'","result");
	        $get_data3 = $this->mymodel->withquery("select * from transaksi tr left join ".$table_name." s on tr.no_transaksi = s.no_transaksi ".$where." and (tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.status_transaksi = '1' and tr.is_show = '1'","result");

		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("Total Nominal Pembayaran");  
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
		        	if ($value_data == "1") {
		        		$value_data = "Lunas";
		        	}
		        	else if($value_data == "0"){
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
		$objPHPExcel->getActiveSheet()->setTitle("Total Nominal Pendaftaran");
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
		        	if ($value_data == "1") {
		        		$value_data = "Lunas";
		        	}
		        	else if($value_data == "0"){
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
		$objPHPExcel->getActiveSheet()->setTitle("Total Nominal Daftar Ulang");
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
		        	if ($value_data == "1") {
		        		$value_data = "Lunas";
		        	}
		        	else if($value_data == "0"){
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
		header('Content-Disposition: attachment;filename="Grafik Nominal PSB_'.$jenjang.'_'.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
		//echo json_encode("success");
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */