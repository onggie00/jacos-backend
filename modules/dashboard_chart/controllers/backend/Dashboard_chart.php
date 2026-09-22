<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard_chart extends Admin	
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

	public function chart()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$this->template->title('Rekap Siswa Aktif, Guru, & Pegawai');
		$this->render('backend/standart/administrator/rekap_data', $data);
	}

	public function chart_data_siswa(){
		$start_year = (empty($this->input->post("start_year"))) ? null : $this->input->post("start_year") ;
		$end_year = (empty($this->input->post("end_year"))) ? date("Y", strtotime("+1 year")) : $this->input->post("end_year") ;
		$jenjang = "sd";
		$table_name = "siswa_".strtolower($jenjang)."_aktif";
		$kelas = "kelas_".strtolower($jenjang);
		$where = "";
		$data = array();
		$data_tahun_ajaran = array();

		if (!empty($start_year) && !empty($end_year)) {
			//pakai filter start date dan end date
			if($where == ""){
				$where .= "where year(t.tanggal_mulai) >= '".$start_year."' and year(t.tanggal_selesai) <= '".$end_year."'";
			}
		}
		else if(!empty($start_year) && empty($end_year)){
			//pakai filter start date saja hingga tahun ini
			if($where == ""){
				$where .= "where year(t.tanggal_mulai) >= '".$start_year."' and year(t.tanggal_selesai) <= '".$end_year."'";
			}
		}
		else{
			//tanpa filter
			if($where == ""){
				$where .= "where year(t.tanggal_selesai) <= '".$end_year."'";
			}
		}
		$get_data = $this->mymodel->withquery("select
		(select count(sa.id_siswa_sd_aktif) from siswa_sd_aktif sa join kelas_sd k on sa.id_kelas = k.id_kelas_sd where (k.label not like '%mutasi%' and k.label not like '%alumni%') and sa.id_tahun_ajaran = t.id_tahun_ajaran) as total_siswa_aktif_sd, 
  		(select count(sa.id_siswa_smp_aktif) from siswa_smp_aktif sa join kelas_smp k on sa.id_kelas = k.id_kelas_smp where (k.label not like '%mutasi%' and k.label not like '%alumni%') and sa.id_tahun_ajaran = t.id_tahun_ajaran ) as total_siswa_aktif_smp,
  		(select count(sa.id_siswa_sma_aktif) from siswa_sma_aktif sa join kelas_sma k on sa.id_kelas = k.id_kelas_sma where (k.label not like '%mutasi%' and k.label not like '%alumni%') and sa.id_tahun_ajaran = t.id_tahun_ajaran) as total_siswa_aktif_sma,
  		(select count(sa.id_siswa_ft_aktif) from siswa_ft_aktif sa join kelas_ft k on sa.id_kelas = k.id_kelas_ft where (k.label not like '%mutasi%' and k.label not like '%alumni%') and sa.id_tahun_ajaran = t.id_tahun_ajaran) as total_siswa_aktif_ft,
    	t.label as tahun_ajaran from tahun_ajaran t ".$where." ","result");
		//$get_data = $this->mymodel->withquery("select count(id_".$table_name.") as total_siswa_aktif, s.id_kelas, k.label as label_kelas, t.label as tahun_ajaran from ".$table_name." as s left join tahun_ajaran t on s.id_tahun_ajaran = t.id_tahun_ajaran join ".$kelas." k on s.id_kelas = k.id_".$kelas." ".$where." group by s.id_kelas order by s.id_tahun_ajaran ASC","result");
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					"tahun_ajaran" => $value->tahun_ajaran,
					"total_siswa_aktif_sd" => $value->total_siswa_aktif_sd,
					"total_siswa_aktif_smp" => $value->total_siswa_aktif_smp,
					"total_siswa_aktif_sma" => $value->total_siswa_aktif_sma,
					"total_siswa_aktif_ft" => $value->total_siswa_aktif_ft,
				);
				array_push($data, $in_array);
			}
		}

		echo json_encode($data);
	}

	public function chart_data_guru(){
		$start_year = (empty($this->input->post("start_year"))) ? null : $this->input->post("start_year") ;
		$end_year = (empty($this->input->post("end_year"))) ? date("Y", strtotime("+1 year")) : $this->input->post("end_year") ;
		$jenjang = $this->input->post("jenjang");
		$table_name = "guru_".strtolower($jenjang);
		$kelas = "kelas_".strtolower($jenjang);
		$where = "";
		$data = array();
		$data_tahun_ajaran = array();

		$get_data = $this->mymodel->withquery(" 
		select count(id_guru) as total_guru_sd, 
  		(select count(id_guru) from guru_smp where satuan_pendidikan = 'SMP' ) as total_guru_smp,
  		(select count(id_guru) from guru_sma where satuan_pendidikan = 'SMA' ) as total_guru_sma,
  		(select count(id_guru) from guru_ft where satuan_pendidikan = 'FT' ) as total_guru_ft  
		from guru_sd where satuan_pendidikan = 'SD' ".$where." ","result");
		
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					"y" => "Jenjang",
					"total_guru_sd" => $value->total_guru_sd,
					"total_guru_smp" => $value->total_guru_smp,
					"total_guru_sma" => $value->total_guru_sma,
					"total_guru_ft" => $value->total_guru_ft,
				);
				array_push($data, $in_array);
			}
		}
		echo json_encode($data);
	}

	public function chart_data_pegawai(){
		$start_year = (empty($this->input->post("start_year"))) ? null : $this->input->post("start_year") ;
		$end_year = (empty($this->input->post("end_year"))) ? date("Y", strtotime("+1 year")) : $this->input->post("end_year") ;
		/*$jenjang = $this->input->post("jenjang");
		$table_name = "guru_".strtolower($jenjang);
		$kelas = "kelas_".strtolower($jenjang);*/
		$where = "";
		$data = array();

		$get_data = $this->mymodel->withquery("select count(p.id_pegawai) as total_pegawai, p.status_kepegawaian, pp.nama as nama_posisi from pegawai as p join posisi_pegawai pp on p.id_posisi = pp.id_posisi ".$where." group by p.id_posisi order by p.id_pegawai ASC ","result");
		
		if (!empty($get_data)) {
			foreach ($get_data as $key => $value) {
				$in_array = array(
					//"status_kepegawaian" => $value->status_kepegawaian,
					"total_pegawai" => $value->total_pegawai,
					"posisi" => $value->nama_posisi,
				);
				array_push($data, $in_array);
			}
		}

		echo json_encode($data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */