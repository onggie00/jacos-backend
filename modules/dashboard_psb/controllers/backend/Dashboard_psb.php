<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard_psb extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Mapping value jenjang dashboard ke tabel/kolom aktual.
	* ppsbbft: pendaftar PPSBB France Track, duduk di siswa_ft dengan flag ppsbb = 1.
	* ft: exclude ppsbb = 1 supaya dashboard FT & PPSBB FT terpisah.
	*/
	private function jenjang_map($jenjang)
	{
		$j = ($jenjang == 'ppsbbft') ? 'ft' : $jenjang;
		$extra = "";
		if ($jenjang == 'ppsbbft') {
			$extra = " and s.ppsbb = 1";
		}
		else if ($jenjang == 'ft') {
			$extra = " and (s.ppsbb is null or s.ppsbb = 0)";
		}
		return array('j' => $j, 'extra' => $extra);
	}

	public function index()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}
		$data = [];
		$this->render('backend/standart/dashboard', $data);
	}

	public function grafik_sd()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "sd";
		$this->template->title('Grafik PSB SD');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function grafik_kb()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "kb";
		$this->template->title('Grafik PSB KB');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function grafik_tk()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "tk";
		$this->template->title('Grafik PSB TK');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function grafik_ft()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "ft";
		$this->template->title('Grafik PSB FRANCE TRACK');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function grafik_ppsbbft()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "ppsbbft";
		$this->template->title('Grafik PPSBB FRANCE TRACK');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function grafik_smp()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "smp";
		$this->template->title('Grafik PSB SMP');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function grafik_sma()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$data['jenjang'] = "sma";
		$this->template->title('Grafik PSB SMA');
		$this->render('backend/standart/administrator/grafik_psb', $data);
	}

	public function chart_psb(){
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$jenjang = strtolower((string) $this->input->post("jenjang"));
		$allowed_jenjang = array('sd', 'smp', 'sma', 'ft', 'ppsbbft', 'kb', 'tk');
		if (!in_array($jenjang, $allowed_jenjang, true)) {
			echo json_encode(array());
			return;
		}

		$m = $this->jenjang_map($jenjang);
		$j = $m['j'];
		$extra_where = $m['extra'];
		$tr_extra = ($jenjang == 'ft') ? " and tr.no_transaksi not like '%PPSBB%'" : "";

		$start_date = (string) $this->input->post("start_date");
		if (!empty($start_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
			echo json_encode(array());
			return;
		}
		$start_date = (empty($start_date)) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $start_date." 00:00:00";

		$end_date = (string) $this->input->post("end_date");
		if (!empty($end_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
			echo json_encode(array());
			return;
		}
		$end_date = (empty($end_date)) ? date("Y-m-d")." 23:59:59" : $end_date." 23:59:59";

		$table_name = "siswa_".$j;
		$table_status = "status_daftar_ulang_".$j;
		$kelas = "kelas_".$jenjang;
		$where = "";
		$where_sdh_bayar = "";
		$data = array();
		$data_tahun_ajaran = array();

		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where s.created_at >= '".$start_date."' and s.created_at <= '".$end_date."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.created_at >= '".$start_date."' and s.created_at <= '".$end_date."'";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where s.created_at >= '".$start_date."' and s.created_at <= '".$end_date."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.created_at >= '".$start_date."' and s.created_at <= '".$end_date."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where s.created_at <= '".$end_date."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.created_at <= '".$end_date."'";
			}
		}
		if (strtotime($start_date) > strtotime($end_date)) {
			echo json_encode(array());
			return;
		}
		$where .= $extra_where;
		$where_sdh_bayar .= $extra_where;
		//$get_data = $this->mymodel->withquery("select (select count(distinct tr.no_transaksi) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%SD%' ) as total_siswa, (select count(distinct tr.no_transaksi) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%LI-SD%' and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_siswa_daftar, (select count(distinct tr.no_transaksi) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%LDUI-SD%' and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_siswa_daftar_ulang from transaksi t group by total_siswa","result");
		$get_data = $this->mymodel->withquery("select (select count(*) from ".$table_name." s ".$where." and s.tahun_ajaran is not null and s.gelombang is not null) as total_siswa, 
			(select count(*) from ".$table_name." s ".$where_sdh_bayar." and s.tahun_ajaran is not null and s.gelombang is not null and exists (select 1 from transaksi tr where trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) and tr.created_at >= s.created_at and tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1' and tr.is_show = '1')) as total_siswa_daftar, 
			(select count(*) from ".$table_name." s join ".$table_status." st on s.id_siswa_".strtolower($j)." = st.id_siswa_".strtolower($j)." ".$where." and s.tahun_ajaran is not null and s.gelombang is not null and exists (select 1 from transaksi tr where trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) and tr.created_at >= s.created_at and tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%' and tr.is_show = '1')) as total_siswa_daftar_ulang, 
			(select count(*) from ".$table_name." s ".$where_sdh_bayar." and s.tahun_ajaran is not null and s.gelombang is not null and exists (select 1 from transaksi tr where trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) and tr.created_at >= s.created_at and tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1' and tr.is_show = '1')) as total_sudah_daftar_ulang 
			from transaksi t group by total_siswa","result");
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					"keterangan" => "Total Pendaftar ".date("d/m/Y", strtotime($start_date))." - ".date("d/m/Y", strtotime($end_date)), //"Total Siswa Daftar", "Total Siswa Daftar Ulang",
					"total_siswa" => $value->total_siswa,
					"total_siswa_daftar" => $value->total_siswa_daftar,
					"total_siswa_daftar_ulang" => $value->total_siswa_daftar_ulang,
					"total_sudah_daftar_ulang" => $value->total_sudah_daftar_ulang,
				);
				array_push($data, $in_array);
			}
		}

		echo json_encode($data);
	}

	public function export_chart_psb(){
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$jenjang = strtolower((string) $this->input->get("jenjang"));
		$allowed_jenjang = array('sd', 'smp', 'sma', 'ft', 'ppsbbft', 'kb', 'tk');
		if (!in_array($jenjang, $allowed_jenjang, true)) {
			return;
		}

		$m = $this->jenjang_map($jenjang);
		$j = $m['j'];
		$extra_where = $m['extra'];
		$tr_extra = ($jenjang == 'ft') ? " and tr.no_transaksi not like '%PPSBB%'" : "";

		$start_date = (string) $this->input->get("start_date");
		if (!empty($start_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
			return;
		}
		$start_date = (empty($start_date)) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $start_date." 00:00:00";

		$end_date = (string) $this->input->get("end_date");
		if (!empty($end_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
			return;
		}
		$end_date = (empty($end_date)) ? date("Y-m-d")." 23:59:59" : $end_date." 23:59:59";

		$table_name = "siswa_".$j;
		$where = "";
		$where_sdh_bayar = "";
		if (!empty($start_date) && !empty($end_date)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where tr.updated_at >= '".$start_date."' and tr.updated_at <= '".$end_date."'";
			}
		}
		else if(!empty($start_date) && empty($end_date)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where tr.updated_at >= '".$start_date."' and tr.updated_at <= '".$end_date."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where tr.created_at <= '".$end_date."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where tr.updated_at <= '".$end_date."'";
			}
		}
		$where .= $tr_extra;
		$where_sdh_bayar .= $tr_extra;

		if (strtotime($start_date) > strtotime($end_date)) {
			return;
		}

		$field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'nisn', 'description', 'total_biaya', 'status_transaksi', 'no_peserta', 'tgl_lahir', 'tempat_lahir', 'jenis_kelamin', 'agama', 'nama_ayah', 'notelp_ayah', 'nama_ibu' , 'notelp_ibu', 'alamat', 'sekolah_asal', 'sumber_informasi', 'alasan_tertarik', 'expired_datetime', 'created_at', 'updated_at'];
		
		//export all data based on input where all field
			$iterasi = 1;
	        $get_data = $this->mymodel->withquery("select tr.*, s.no_peserta, s.tgl_lahir, s.tempat_lahir, s.jenis_kelamin, s.agama, s.nama_ayah, s.notelp_ayah, s.nama_ibu, s.notelp_ibu, s.alamat, s.sekolah_asal, s.sumber_informasi, s.alasan_tertarik, s.nisn from transaksi tr left join ".$table_name." s on trim(tr.user_name) = trim(s.nama_lengkap) and trim(tr.user_email) = trim(s.email) ".$where." and (tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' or tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.is_show = '1'  order by created_at ASC","result");
	        $get_data2 = $this->mymodel->withquery("select tr.*, s.no_peserta, s.tgl_lahir, s.tempat_lahir, s.jenis_kelamin, s.agama, s.nama_ayah, s.notelp_ayah, s.nama_ibu, s.notelp_ibu, s.alamat, s.sekolah_asal, s.sumber_informasi, s.alasan_tertarik, s.nisn from transaksi tr left join ".$table_name." s on trim(tr.user_name) = trim(s.nama_lengkap) and trim(tr.user_email) = trim(s.email) ".$where_sdh_bayar." and ((tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1') or tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.is_show = '1' group by tr.user_email, tr.user_name order by created_at ASC","result");
	        $get_data3 = $this->mymodel->withquery("select tr.*, s.no_peserta, s.tgl_lahir, s.tempat_lahir, s.jenis_kelamin, s.agama, s.nama_ayah, s.notelp_ayah, s.nama_ibu, s.notelp_ibu, s.alamat, s.sekolah_asal, s.sumber_informasi, s.alasan_tertarik, s.nisn from transaksi tr left join ".$table_name." s on trim(tr.user_name) = trim(s.nama_lengkap) and trim(tr.user_email) = trim(s.email) ".$where_sdh_bayar." and tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1' and tr.is_show = '1' order by created_at ASC","result");

		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("Total Siswa");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_search[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBAYAR'));
			}
			else if(strpos($field_search[$i], "expired_datetime") !== false){
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
		        if (strpos($kolom, "status_transaksi") !== false && !empty($value_data)) {
		        	$value_data = "Lunas";
		        }
		        else if(strpos($kolom, "status_transaksi") !== false && empty($value_data)){
		        	$value_data = "Belum Dibayar";
		        }
		        else if(strpos($kolom, "created_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "updated_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false && $value_data > 0){
		        	$value_data = $this->mymodel->getbywhere("list_sekolah_".strtolower($j), "id_list_sekolah_".strtolower($j), $value_data ,"row")->nama_sekolah;
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false){
		        	$value_data = $value_data;
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
		$objPHPExcel->getActiveSheet()->setTitle("Total Siswa Daftar");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_search[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBAYAR'));
			}
			else if(strpos($field_search[$i], "expired_datetime") !== false){
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
		        if (strpos($kolom, "status_transaksi") !== false && !empty($value_data)) {
		        	$value_data = "Lunas";
		        }
		        else if(strpos($kolom, "status_transaksi") !== false && empty($value_data)){
		        	$value_data = "Belum Dibayar";
		        }
		        else if(strpos($kolom, "created_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "updated_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false && $value_data > 0){
		        	$value_data = $this->mymodel->getbywhere("list_sekolah_".strtolower($j), "id_list_sekolah_".strtolower($j), $value_data ,"row")->nama_sekolah;
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false){
		        	$value_data = $value_data;
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
		$objPHPExcel->getActiveSheet()->setTitle("Total Siswa Daftar Ulang");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_search[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBAYAR'));
			}
			else if(strpos($field_search[$i], "expired_datetime") !== false){
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
		        if (strpos($kolom, "status_transaksi") !== false && !empty($value_data)) {
		        	$value_data = "Lunas";
		        }
		        else if(strpos($kolom, "status_transaksi") !== false && empty($value_data)){
		        	$value_data = "Belum Dibayar";
		        }
		        else if(strpos($kolom, "created_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "updated_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false && $value_data > 0){
		        	$value_data = $this->mymodel->getbywhere("list_sekolah_".strtolower($j), "id_list_sekolah_".strtolower($j), $value_data ,"row")->nama_sekolah;
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false){
		        	$value_data = $value_data;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}

		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Grafik PSB_'.$jenjang.'_'.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
		//echo json_encode("success");
	}

	public function chart_psb_gelombang(){
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$jenjang = strtolower((string) $this->input->post("jenjang"));
		$allowed_jenjang = array('sd', 'smp', 'sma', 'ft', 'ppsbbft', 'kb', 'tk');
		if (!in_array($jenjang, $allowed_jenjang, true)) {
			echo json_encode(array());
			return;
		}

		$m = $this->jenjang_map($jenjang);
		$j = $m['j'];
		$extra_where = $m['extra'];
		$tr_extra = ($jenjang == 'ft') ? " and tr.no_transaksi not like '%PPSBB%'" : "";

		$gelombang = (string) $this->input->post("gelombang");
		if (!in_array($gelombang, array('', '1', '2', '3'), true)) {
			echo json_encode(array());
			return;
		}
		$gelombang = (empty($gelombang)) ? null : $gelombang;

		$tahun_ajaran_raw = (string) $this->input->post("tahun_ajaran");
		$tahun_ajaran = str_replace("_", "/", $tahun_ajaran_raw);
		if (!empty($tahun_ajaran) && !preg_match('/^\d{4}\/\d{4}$/', $tahun_ajaran)) {
			echo json_encode(array());
			return;
		}
		$tahun_ajaran = (empty($tahun_ajaran)) ? null : $tahun_ajaran;

		$table_name = "siswa_".$j;
		$table_status = "status_daftar_ulang_".$j;
		$kelas = "kelas_".$jenjang;
		$where = "";
		$where_sdh_bayar = "";
		$data = array();
		$data_tahun_ajaran = array();

		if (!empty($gelombang) && !empty($tahun_ajaran)) {
			if($where == ""){
				$where .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
		}
		else if(!empty($gelombang) && empty($tahun_ajaran)){
			if($where == ""){
				$where .= "where s.gelombang = '".$gelombang."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.gelombang = '".$gelombang."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
		}
		$where .= $extra_where;
		$where_sdh_bayar .= $extra_where;
		//$get_data = $this->mymodel->withquery("select (select count(distinct tr.user_email, tr.user_name) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%SD%' ) as total_siswa, (select count(distinct tr.user_email, tr.user_name) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%LI-SD%' and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_siswa_daftar, (select count(distinct tr.user_email, tr.user_name) from transaksi tr join ".$table_name." s on tr.user_email = s.email ".$where." and tr.no_transaksi like '%LDUI-SD%' and tr.status_transaksi = '1' and tr.is_show = '1' ) as total_siswa_daftar_ulang from transaksi t group by total_siswa","result");
		$get_data = $this->mymodel->withquery("select (select count(*) from ".$table_name." s ".$where." and s.tahun_ajaran is not null and s.gelombang is not null) as total_siswa, 
		(select count(*) from ".$table_name." s ".$where_sdh_bayar." and s.tahun_ajaran is not null and s.gelombang is not null and exists (select 1 from transaksi tr where trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) and tr.created_at >= s.created_at and tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1' and tr.is_show = '1')) as total_siswa_daftar, 
		(select count(*) from ".$table_name." s join ".$table_status." st on s.id_siswa_".strtolower($j)." = st.id_siswa_".strtolower($j)." ".$where." and s.tahun_ajaran is not null and s.gelombang is not null and exists (select 1 from transaksi tr where trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) and tr.created_at >= s.created_at and tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%' and tr.is_show = '1')) as total_siswa_daftar_ulang, 
		(select count(*) from ".$table_name." s ".$where_sdh_bayar." and s.tahun_ajaran is not null and s.gelombang is not null and exists (select 1 from transaksi tr where trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) and tr.created_at >= s.created_at and tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1' and tr.is_show = '1')) as total_sudah_daftar_ulang 
		from transaksi t group by total_siswa","result");
		//echo $this->db->last_query();
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					"keterangan" => "Total Pendaftar ".strtoupper($jenjang)." Gelombang ".$gelombang." Tahun Ajaran ".$tahun_ajaran, //"Total Siswa Daftar", "Total Siswa Daftar Ulang",
					"total_siswa" => $value->total_siswa,
					"total_siswa_daftar" => $value->total_siswa_daftar,
					"total_siswa_daftar_ulang" => $value->total_siswa_daftar_ulang,
					"total_sudah_daftar_ulang" => $value->total_sudah_daftar_ulang,
				);
				array_push($data, $in_array);
			}
		}

		echo json_encode($data);
	}

	public function export_chart_psb_gelombang(){
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$jenjang = strtolower((string) $this->input->get("jenjang"));
		$allowed_jenjang = array('sd', 'smp', 'sma', 'ft', 'ppsbbft', 'kb', 'tk');
		if (!in_array($jenjang, $allowed_jenjang, true)) {
			return;
		}

		$m = $this->jenjang_map($jenjang);
		$j = $m['j'];
		$extra_where = $m['extra'];
		$tr_extra = ($jenjang == 'ft') ? " and tr.no_transaksi not like '%PPSBB%'" : "";

		$gelombang = (string) $this->input->get("gelombang");
		if (!in_array($gelombang, array('', '1', '2', '3'), true)) {
			return;
		}
		$gelombang = (empty($gelombang)) ? null : $gelombang;

		$tahun_ajaran_raw = (string) $this->input->get("tahun_ajaran");
		$tahun_ajaran = str_replace("_", "/", $tahun_ajaran_raw);
		if (!empty($tahun_ajaran) && !preg_match('/^\d{4}\/\d{4}$/', $tahun_ajaran)) {
			return;
		}
		$tahun_ajaran = (empty($tahun_ajaran)) ? null : $tahun_ajaran;

		$table_name = "siswa_".$j;
		$where = "";
		$where_sdh_bayar = "";
		if (!empty($gelombang) && !empty($tahun_ajaran)) {
			if($where == ""){
				$where .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
		}
		else if(!empty($gelombang) && empty($tahun_ajaran)){
			if($where == ""){
				$where .= "where s.gelombang = '".$gelombang."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.gelombang = '".$gelombang."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
			if($where_sdh_bayar == ""){
				$where_sdh_bayar .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
			}
		}
		$where .= $tr_extra;
		$where_sdh_bayar .= $tr_extra;

		$field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'nisn', 'description', 'total_biaya', 'status_transaksi', 'no_peserta', 'tgl_lahir', 'tempat_lahir', 'jenis_kelamin', 'agama', 'nama_ayah', 'notelp_ayah', 'nama_ibu', 'notelp_ibu', 'alamat', 'sekolah_asal', 'sumber_informasi', 'alasan_tertarik', 'expired_datetime', 'created_at', 'updated_at'];
		
		//export all data based on input where all field
			$iterasi = 1;
	        $get_data = $this->mymodel->withquery("select tr.*, s.no_peserta, s.tgl_lahir, s.tempat_lahir, s.jenis_kelamin, s.agama, s.nama_ayah, s.notelp_ayah, s.nama_ibu, s.notelp_ibu, s.alamat, s.sekolah_asal, s.sumber_informasi, s.alasan_tertarik, s.nisn from transaksi tr left join ".$table_name." s on trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) ".$where." and (tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' or tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.is_show = '1'  order by created_at ASC","result");
	        $get_data2 = $this->mymodel->withquery("select tr.*, s.no_peserta, s.tgl_lahir, s.tempat_lahir, s.jenis_kelamin, s.agama, s.nama_ayah, s.notelp_ayah, s.nama_ibu, s.notelp_ibu, s.alamat, s.sekolah_asal, s.sumber_informasi, s.alasan_tertarik, s.nisn from transaksi tr left join ".$table_name." s on trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) ".$where_sdh_bayar." and ((tr.no_transaksi like '%LI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1') or tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%') and tr.is_show = '1' group by tr.user_email, tr.user_name order by created_at ASC","result");
	        $get_data3 = $this->mymodel->withquery("select tr.*, s.no_peserta, s.tgl_lahir, s.tempat_lahir, s.jenis_kelamin, s.agama, s.nama_ayah, s.notelp_ayah, s.nama_ibu, s.notelp_ibu, s.alamat, s.sekolah_asal, s.sumber_informasi, s.alasan_tertarik, s.nisn from transaksi tr left join ".$table_name." s on trim(tr.user_email) = trim(s.email) and trim(tr.user_name) = trim(s.nama_lengkap) ".$where_sdh_bayar." and tr.no_transaksi like '%LDUI-".strtoupper($jenjang)."%' and tr.status_transaksi = '1' and tr.is_show = '1' order by created_at ASC","result");

		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("Total Siswa");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_search[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBAYAR'));
			}
			else if(strpos($field_search[$i], "expired_datetime") !== false){
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
		        if (strpos($kolom, "status_transaksi") !== false && !empty($value_data)) {
		        	$value_data = "Lunas";
		        }
		        else if(strpos($kolom, "status_transaksi") !== false && empty($value_data)){
		        	$value_data = "Belum Dibayar";
		        }
		        else if(strpos($kolom, "created_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "updated_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false && $value_data > 0){
		        	$value_data = $this->mymodel->getbywhere("list_sekolah_".strtolower($j), "id_list_sekolah_".strtolower($j), $value_data ,"row")->nama_sekolah;
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false){
		        	$value_data = $value_data;
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
		$objPHPExcel->getActiveSheet()->setTitle("Total Siswa Daftar");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_search[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBAYAR'));
			}
			else if(strpos($field_search[$i], "expired_datetime") !== false){
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
		        if (strpos($kolom, "status_transaksi") !== false && !empty($value_data)) {
		        	$value_data = "Lunas";
		        }
		        else if(strpos($kolom, "status_transaksi") !== false && empty($value_data)){
		        	$value_data = "Belum Dibayar";
		        }
		        else if(strpos($kolom, "created_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "updated_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false && $value_data > 0){
		        	$value_data = $this->mymodel->getbywhere("list_sekolah_".strtolower($j), "id_list_sekolah_".strtolower($j), $value_data ,"row")->nama_sekolah;
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false){
		        	$value_data = $value_data;
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
		$objPHPExcel->getActiveSheet()->setTitle("Total Siswa Daftar Ulang");
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created_at") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBUAT'));
			}
			else if(strpos($field_search[$i], "updated_at") !== false){
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL TRANSAKSI DIBAYAR'));
			}
			else if(strpos($field_search[$i], "expired_datetime") !== false){
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
		        if (strpos($kolom, "status_transaksi") !== false && !empty($value_data)) {
		        	$value_data = "Lunas";
		        }
		        else if(strpos($kolom, "status_transaksi") !== false && empty($value_data)){
		        	$value_data = "Belum Dibayar";
		        }
		        else if(strpos($kolom, "created_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "updated_at") !== false ){
		        	$value_data = date("d-m-Y H:i:s", strtotime($value_data));
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false && $value_data > 0){
		        	$value_data = $this->mymodel->getbywhere("list_sekolah_".strtolower($j), "id_list_sekolah_".strtolower($j), $value_data ,"row")->nama_sekolah;
		        }
		        else if(strpos($kolom, "sekolah_asal") !== false){
		        	$value_data = $value_data;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}

		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Grafik PSB_'.$jenjang.' '.$tahun_ajaran.' Gelombang '.$gelombang.'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
		//echo json_encode("success");
	}

	public function detail_table(){
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$post = $this->input->post();
		$jenjang = strtolower((string) $post['jenjang']);
		$allowed_jenjang = array('sd', 'smp', 'sma', 'ft', 'ppsbbft', 'kb', 'tk');
		if (!in_array($jenjang, $allowed_jenjang, true)) {
			echo json_encode(array("status" => false, "message" => "Jenjang tidak valid"));
			return;
		}
		$m = $this->jenjang_map($jenjang);
		$j = $m['j'];
		$extra_where = $m['extra'];
		$tr_extra = ($jenjang == 'ft') ? " and tr.no_transaksi not like '%PPSBB%'" : "";

		$tipe_grafik = (string) $post['tipe'];
		if (!in_array($tipe_grafik, array('tanggal', 'gelombang'), true)) {
			echo json_encode(array("status" => false, "message" => "Tipe grafik tidak valid"));
			return;
		}

		if($tipe_grafik == "tanggal"){
			$start_date = (string) $this->input->post("start_date");
			if (!empty($start_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
				echo json_encode(array("status" => false, "message" => "Start date tidak valid"));
				return;
			}
			$start_date = (empty($start_date)) ? date("Y-m-d", strtotime("-1 month"))." 00:00:00" : $start_date." 00:00:00";

			$end_date = (string) $this->input->post("end_date");
			if (!empty($end_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
				echo json_encode(array("status" => false, "message" => "End date tidak valid"));
				return;
			}
			$end_date = (empty($end_date)) ? date("Y-m-d")." 23:59:59" : $end_date." 23:59:59";

			if (strtotime($start_date) > strtotime($end_date)) {
				echo json_encode(array("status" => false, "message" => "Start date tidak boleh melebihi end date"));
				return;
			}

			$where = "";
			$where_transaksi = "";

			if (!empty($start_date) && !empty($end_date)) {
				//pakai filter start date dan end date
				if($where == ""){
					$where .= "where s.created_at >= '".$start_date."' and s.created_at <= '".$end_date."'";
				}
				if($where_transaksi == ""){
					$where_transaksi .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
				}
			}
			else if(!empty($start_date) && empty($end_date)){
				//pakai filter start date saja hingga tahun ini
				if($where == ""){
					$where .= "where s.created_at >= '".$start_date."' and s.created_at <= '".$end_date."'";
				}
				if($where_transaksi == ""){
					$where_transaksi .= "where tr.created_at >= '".$start_date."' and tr.created_at <= '".$end_date."'";
				}
			}
			else{
				//tanpa filter
				if($where == ""){
					$where .= "where s.created_at <= '".$end_date."'";
				}
				if($where_transaksi == ""){
					$where_transaksi .= "where tr.created_at <= '".$end_date."'";
				}
			}
			//samakan filter dengan chart_psb: exclude siswa tanpa tahun_ajaran/gelombang (mis. mutasi)
			$where .= " and s.tahun_ajaran is not null and s.gelombang is not null";
			$where .= $extra_where;
			//get transaksi by tanggal
			$get_transaksi = $this->mymodel->withquery("select * from transaksi tr ".$where_transaksi." and tr.no_transaksi like '%".strtoupper($jenjang)."%'".$tr_extra." and tr.is_show = '1' order by created_at DESC","result");
		}
		else if($tipe_grafik == "gelombang"){
			$tahun_ajaran = (string) $post['tahun_ajaran'];
			$tahun_ajaran = str_replace("_", "/", $tahun_ajaran);
			if (!empty($tahun_ajaran) && !preg_match('/^\d{4}\/\d{4}$/', $tahun_ajaran)) {
				echo json_encode(array("status" => false, "message" => "Tahun ajaran tidak valid"));
				return;
			}

			$gelombang = (string) $post['gelombang'];
			if (!in_array($gelombang, array('', '1', '2', '3'), true)) {
				echo json_encode(array("status" => false, "message" => "Gelombang tidak valid"));
				return;
			}
			$where = "";
			$where_sdh_bayar = "";
			if (!empty($gelombang) && !empty($tahun_ajaran)) {
				if($where == ""){
					$where .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
				}
				if($where_sdh_bayar == ""){
					$where_sdh_bayar .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
				}
			}
			else if(!empty($gelombang) && empty($tahun_ajaran)){
				if($where == ""){
					$where .= "where s.gelombang = '".$gelombang."'";
				}
				if($where_sdh_bayar == ""){
					$where_sdh_bayar .= "where s.gelombang = '".$gelombang."'";
				}
			}
			else{
				//tanpa filter
				/* if($where == ""){
					$where .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
				}
				if($where_sdh_bayar == ""){
					$where_sdh_bayar .= "where s.gelombang = '".$gelombang."' and s.tahun_ajaran = '".$tahun_ajaran."'";
				} */
			}
			$where .= ($where == "") ? "where 1=1" . $extra_where : $extra_where;
			//get transaksi: pool sama dengan mode tanggal (batas periode web_periode_daftar dihapus —
			//lookup kelas LIKE '%sd%' bisa salah match (mis. 'SD MUTASI') dan mengecualikan pembayar awal)
			$get_transaksi = $this->mymodel->withquery("select * from transaksi tr where tr.no_transaksi like '%".strtoupper($jenjang)."%'".$tr_extra." and tr.is_show = '1' order by created_at DESC","result");
		}
// echo $this->db->last_query();
		$table_name = "siswa_".$j;
		if($jenjang == "sd"){
			$get_data = $this->mymodel->withquery("select s.id_siswa_".strtolower($j)." as id_siswa, s.no_peserta, s.nama_lengkap, s.jenis_kelamin, s.notelp_ibu, s.notelp_ayah, s.email, s.sekolah_asal, s.status_lulus, s.tahun_ajaran, s.gelombang, is_mutasi 
		from ".$table_name." s ".$where." order by created_at DESC","result");
		}
		else{
			$get_data = $this->mymodel->withquery("select s.id_siswa_".strtolower($j)." as id_siswa, s.no_peserta, s.nama_lengkap, s.jenis_kelamin, s.notelp_ibu, s.notelp_ayah, s.email, s.sekolah_asal, s.status_lulus, s.tahun_ajaran, s.gelombang 
		from ".$table_name." s ".$where." order by created_at DESC","result");
		}

		if(!empty($get_transaksi) && !empty($get_data)){
			$no = 1;
			//print_r($get_transaksi);
			foreach ($get_data as $key => $value) {
				$value->no = $no;
				foreach ($get_transaksi as $key2 => $value2) {
					//status pembayaran pendaftaran
					$jenis_transaksi = substr($value2->no_transaksi, 0, strpos($value2->no_transaksi, strtoupper($jenjang)));
					if(strtoupper($value->nama_lengkap) == strtoupper(trim($value2->user_name)) && strtoupper($value->email) == strtoupper(trim($value2->user_email)) && ($jenis_transaksi == "LI-" || $jenis_transaksi == "LDUI-") ){
						if($value2->status_transaksi == 1){
							$value->status_pendaftaran = "Lunas";
						}
						else if($jenis_transaksi == "LDUI-"){
							$value->status_pendaftaran = "Lunas";
						}
						else{
							$value->status_pendaftaran = "Belum";
						}
						break;
					}
					else{
						$value->status_pendaftaran = "Belum";
					}
				}
				foreach ($get_transaksi as $key2 => $value2) {
					//status pembayaran daftar ulang
					$jenis_transaksi2 = substr($value2->no_transaksi, 0, strpos($value2->no_transaksi, strtoupper($jenjang)));
					if($value->nama_lengkap == $value2->user_name && $value->email == $value2->user_email && ($jenis_transaksi2 == "LDUI-") ){
						if($value2->status_transaksi == 1){
							$value->status_daftar_ulang = "Lunas";
						}
						else{
							$value->status_daftar_ulang = "Belum";
						}
						break;
					}
					else{
						$value->status_daftar_ulang = "Belum";
					}
				}
				if ($value->status_lulus == 2) {
					$value->status_lulus = "Lulus";
				}
				else if ($value->status_lulus == 4) {
					$value->status_lulus = "Cadangan";
				}
				else if ($value->status_lulus == 3) {
					$value->status_lulus = "Tidak Lulus";
				}
				else{
					$value->status_lulus = "Menunggu";
				}
				if ($value->is_mutasi == 1){
					$value->tahun_ajaran = "Mutasi";
					$value->gelombang = "Mutasi";
					$value->status_lulus = "Mutasi";
					$value->status_pendaftaran = "Mutasi";
				}
				else{
					// $value->status_lulus .= " PSB";
					// $value->status_pendaftaran .= " PSB";
				}
				$no++;
			}
		}
		
		$data = array(
			"status" => true,
			"message" => "Loading Data Success",
			// "recordsTotal" => count($get_data),
			// "recordsFiltered" => count($get_data),
			"data" => $get_data,
		);
		echo json_encode($data);
	}

	public function detail_siswa(){
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$jenjang = strtolower((string) $this->input->post('jenjang'));
		$allowed_jenjang = array('sd', 'smp', 'sma', 'ft', 'ppsbbft', 'kb', 'tk');
		if (!in_array($jenjang, $allowed_jenjang, true)) {
			echo json_encode(array("status" => false, "message" => "Jenjang tidak valid"));
			return;
		}

		$m = $this->jenjang_map($jenjang);
		$j = $m['j'];

		$id = (int) $this->input->post('id_siswa');
		if ($id <= 0) {
			echo json_encode(array("status" => false, "message" => "ID siswa tidak valid"));
			return;
		}

		$table_siswa = 'siswa_' . $j;
		$id_col = 'id_siswa_' . $j;

		$siswa = $this->mymodel->withquery(
			"SELECT * FROM {$table_siswa} WHERE {$id_col} = " . $id . " AND deleted_at IS NULL",
			'row'
		);

		if (empty($siswa)) {
			echo json_encode(array("status" => false, "message" => "Siswa tidak ditemukan"));
			return;
		}

		$sekolah_nama = '-';
		if (!empty($siswa->sekolah_asal)) {
			$tbl_sekolah = 'list_sekolah_' . $j;
			$id_sekolah_col = 'id_list_sekolah_' . $j;
			$sekolah = $this->mymodel->getbywhere($tbl_sekolah, $id_sekolah_col, $siswa->sekolah_asal, 'row');
			if (!empty($sekolah) && !empty($sekolah->nama_sekolah)) {
				$sekolah_nama = $sekolah->nama_sekolah;
			}
		}

		echo json_encode(array(
			"status" => true,
			"data" => $siswa,
			"sekolah_nama" => $sekolah_nama,
		));
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */