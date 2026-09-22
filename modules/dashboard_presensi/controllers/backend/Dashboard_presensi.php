<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard_presensi extends Admin	
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
		//$this->render('backend/standart/dashboard', $data);
		$this->template->title('Grafik Presensi');
		$this->render('backend/standart/administrator/grafik_presensi', $data);
	}

	public function grafik_presensi_unit()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}
		$data[] = [];
		$this->template->title('Grafik Presensi Unit');
		$this->render('backend/standart/administrator/grafik_presensi_unit', $data);
	}

	public function grafik_presensi_tingkatan()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}
		$data[] = [];
		$this->template->title('Grafik Presensi Tingkatan');
		$this->render('backend/standart/administrator/grafik_presensi_tingkatan', $data);
	}

	public function grafik_presensi_kelas()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}
		$data[] = [];
		$this->template->title('Grafik Presensi Kelas');
		$this->render('backend/standart/administrator/grafik_presensi_kelas', $data);
	}

	public function grafik_presensi_siswa()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}
		$data[] = [];
		$this->template->title('Grafik Presensi Siswa');
		$this->render('backend/standart/administrator/grafik_presensi_siswa', $data);
	}

	public function chart_presensi_unit()
	{
		$start_date = $this->input->post("start_date") ? $this->input->post("start_date") . " 00:00:00" : date("Y-m-d") . " 00:00:00";
		$end_date   = $this->input->post("end_date") ? $this->input->post("end_date") . " 23:59:59" : date("Y-m-d") . " 23:59:59";
		$unit       = $this->input->post("unit");

		if (!$unit) {
			echo json_encode([]);
			return;
		}

		// Ambil data siswa aktif
		$get_siswa = $this->mymodel->withquery("
			SELECT sa.id_siswa_{$unit} AS id_siswa
			FROM siswa_{$unit}_aktif sa
			INNER JOIN tahun_ajaran ta ON ta.id_tahun_ajaran = sa.id_tahun_ajaran
			WHERE ta.tanggal_mulai <= '{$start_date}'
			AND ta.tanggal_selesai >= '{$end_date}'
			AND sa.deleted_at IS NULL
		", "result");

		$total_siswa = count($get_siswa);

		// Ambil semua presensi dalam rentang waktu
		$get_presensi = $this->mymodel->withquery("
			SELECT 
				id_siswa_aktif,
				tanggal_absen,
				MIN(status_absen) AS status_absen
			FROM presensi_{$unit}
			WHERE tanggal_absen BETWEEN '{$start_date}' AND '{$end_date}'
			GROUP BY id_siswa_aktif, DATE(tanggal_absen)
		", "result");

		// Siapkan array hasil
		$data = [];

		// Iterasi per bulan
		$start = new DateTime($start_date);
		$end   = new DateTime($end_date);

		while ($start <= $end) {
			$bulan_key = $start->format('Y-m');

			$total_hadir = 0;
			$total_terlambat = 0;
			$total_alfa = 0;
			$total_sakit = 0;
			$total_izin = 0;
			$hari_aktif = [];
			$total_hari_aktif = 0;

			foreach ($get_presensi as $row) {
				$presensi_bulan = date('Y-m', strtotime($row->tanggal_absen));
				if ($presensi_bulan === $bulan_key) {
					$status = strtolower($row->status_absen);
					$tanggal = date('Y-m-d', strtotime($row->tanggal_absen));
					$hari_aktif[$tanggal] = true;

					switch ($status) {
						case 'hadir':
							$total_hadir++;
							break;
						case 'terlambat':
							$total_terlambat++;
							break;
						case 'alfa':
							$total_alfa++;
							break;
						case 'sakit':
							$total_sakit++;
							break;
						case 'izin':
							$total_izin++;
							break;
					}
				}
			}
			$total_hari_aktif = count($hari_aktif);
			//hitung tingkat persentase presensi digital
			$tingkat_presensi_digital = 0;
			if ($total_hari_aktif > 0) {
				$tingkat_presensi_digital = (($total_hadir + $total_terlambat + $total_izin + $total_sakit + $total_alfa) / ($total_hari_aktif * $total_siswa) ) * 100;
			}
			$data[] = [
				'bulan'            => formatBulan($bulan_key) . " " . date("Y", strtotime($bulan_key)),
				'total_siswa'      => $total_siswa,
				'total_hari_aktif' => count($hari_aktif),
				'total_hadir_dan_presensi'      => $total_hadir + $total_terlambat,
				'total_terlambat'  => $total_terlambat,
				'total_sakit'      => $total_sakit,
				'total_izin'       => $total_izin,
				'total_alfa'       => $total_alfa,
				'tingkat_presensi_digital' => number_format($tingkat_presensi_digital,2,",","")
			];

			// Lanjut ke bulan berikutnya
			$start->modify('first day of next month');
		}

		echo json_encode($data);
	}

	public function chart_presensi_tingkatan()
	{
		$start_date = $this->input->post("start_date") ? $this->input->post("start_date") . " 00:00:00" : date("Y-m-d") . " 00:00:00";
		$end_date   = $this->input->post("end_date") ? $this->input->post("end_date") . " 23:59:59" : date("Y-m-d") . " 23:59:59";
		$unit       = $this->input->post("unit");
		$id_tingkatan = $this->input->post("tingkatan");

		$kolom_id_siswa_aktif    = "id_siswa_{$unit}_aktif";
		$nama_tabel_siswa_aktif  = "siswa_{$unit}_aktif";
		$nama_tabel_presensi     = "presensi_{$unit}";
		$nama_tabel_kelas        = "kelas_{$unit}";
		$nama_tabel_tingkatan    = "tingkatan_{$unit}";

		// Ambil siswa berdasarkan tingkatan
		$get_siswa = $this->mymodel->withquery("
			SELECT sa.{$kolom_id_siswa_aktif} AS id_siswa_aktif
			FROM {$nama_tabel_siswa_aktif} sa
			INNER JOIN tahun_ajaran ta ON ta.id_tahun_ajaran = sa.id_tahun_ajaran
			JOIN {$nama_tabel_kelas} k ON k.id_kelas_{$unit} = sa.id_kelas
			JOIN {$nama_tabel_tingkatan} t ON t.id_tingkatan_{$unit} = k.id_tingkatan
			WHERE sa.deleted_at IS NULL
			AND ta.tanggal_mulai <= '{$start_date}'
			AND ta.tanggal_selesai >= '{$end_date}'
			AND t.id_tingkatan_{$unit} = '{$id_tingkatan}'
		", "result");

		if (empty($get_siswa)) {
			echo json_encode([]);
			return;
		}

		$id_siswa_aktif_list = array_column($get_siswa, 'id_siswa_aktif');
		$total_siswa = count($id_siswa_aktif_list);
		$id_list_string = implode(",", array_map('intval', $id_siswa_aktif_list));

		// Ambil presensi berdasarkan siswa & tanggal filter
		$get_presensi = $this->mymodel->withquery("
			SELECT id_siswa_aktif, status_absen, tanggal_absen
			FROM {$nama_tabel_presensi}
			WHERE tanggal_absen BETWEEN '{$start_date}' AND '{$end_date}'
			AND id_siswa_aktif IN ({$id_list_string}) 
			GROUP BY id_siswa_aktif, DATE(tanggal_absen)
		", "result");

		// Hitung presensi per bulan
		$presensi_per_bulan = [];

		foreach ($get_presensi as $row) {
			$bulan = date("Y-m", strtotime($row->tanggal_absen));
			$status = strtolower($row->status_absen);
			$tanggal = date("Y-m-d", strtotime($row->tanggal_absen));

			if (!isset($presensi_per_bulan[$bulan])) {
				$presensi_per_bulan[$bulan] = [
					'hadir' => 0,
					'terlambat' => 0,
					'alfa' => 0,
					'sakit' => 0,
					'izin' => 0,
					'hari_aktif' => []
				];
			}

			if (isset($presensi_per_bulan[$bulan][$status])) {
				$presensi_per_bulan[$bulan][$status]++;
			}

			$presensi_per_bulan[$bulan]['hari_aktif'][$tanggal] = true; // Hari unik
		}

		// Buat output data per bulan
		$start = new DateTime($start_date);
		$end   = new DateTime($end_date);
		$data  = [];

		while ($start <= $end) {
			$bulan_key = $start->format('Y-m');

			$total_hadir     = $presensi_per_bulan[$bulan_key]['hadir']     ?? 0;
			$total_terlambat = $presensi_per_bulan[$bulan_key]['terlambat'] ?? 0;
			$total_alfa      = $presensi_per_bulan[$bulan_key]['alfa']      ?? 0;
			$total_sakit     = $presensi_per_bulan[$bulan_key]['sakit']     ?? 0;
			$total_izin      = $presensi_per_bulan[$bulan_key]['izin']      ?? 0;
			$total_hari_aktif = isset($presensi_per_bulan[$bulan_key]['hari_aktif']) 
								? count($presensi_per_bulan[$bulan_key]['hari_aktif']) 
								: 0;

			//hitung tingkat persentase presensi digital
			$tingkat_presensi_digital = 0;
			if ($total_hari_aktif > 0) {
				$tingkat_presensi_digital = (($total_hadir + $total_terlambat + $total_izin + $total_sakit + $total_alfa) / ($total_hari_aktif * $total_siswa) ) * 100;
			}
			$data[] = [
				'bulan'            => formatBulan($bulan_key) . " " . date("Y", strtotime($bulan_key)),
				'total_siswa'      => $total_siswa,
				'total_hari_aktif' => $total_hari_aktif,
				'total_hadir_dan_presensi'      => $total_hadir + $total_terlambat,
				'total_terlambat'  => $total_terlambat,
				'total_sakit'      => $total_sakit,
				'total_izin'       => $total_izin,
				'total_alfa'       => $total_alfa,
				'tingkat_presensi_digital' => number_format($tingkat_presensi_digital,2,",","")
			];

			$start->modify('first day of next month');
		}

		echo json_encode($data);
	}

	public function chart_presensi_kelas()
	{
		$start_date   = $this->input->post("start_date") ? $this->input->post("start_date") . " 00:00:00" : date("Y-m-d") . " 00:00:00";
		$end_date     = $this->input->post("end_date") ? $this->input->post("end_date") . " 23:59:59" : date("Y-m-d") . " 23:59:59";
		$unit         = $this->input->post("unit");       // contoh: smp
		$id_tingkatan = $this->input->post("tingkatan");  // contoh: 8
		$id_kelas     = $this->input->post("kelas");      // contoh: ID kelas VII A

		$kolom_id_siswa_aktif   = "id_siswa_{$unit}_aktif";
		$nama_tabel_siswa_aktif = "siswa_{$unit}_aktif";
		$nama_tabel_presensi    = "presensi_{$unit}";
		$nama_tabel_kelas       = "kelas_{$unit}";
		$nama_tabel_tingkatan   = "tingkatan_{$unit}";

		// Ambil daftar siswa berdasarkan tingkatan dan kelas
		$get_siswa = $this->mymodel->withquery("
			SELECT sa.{$kolom_id_siswa_aktif} AS id_siswa_aktif
			FROM {$nama_tabel_siswa_aktif} sa
			INNER JOIN tahun_ajaran ta ON ta.id_tahun_ajaran = sa.id_tahun_ajaran
			JOIN {$nama_tabel_kelas} k ON k.id_kelas_{$unit} = sa.id_kelas
			JOIN {$nama_tabel_tingkatan} t ON t.id_tingkatan_{$unit} = k.id_tingkatan
			WHERE sa.deleted_at IS NULL
			AND ta.tanggal_mulai <= '{$start_date}'
			AND ta.tanggal_selesai >= '{$end_date}'
			AND t.id_tingkatan_{$unit} = '{$id_tingkatan}'
			AND k.id_kelas_{$unit} = '{$id_kelas}'
		", "result");

		if (empty($get_siswa)) {
			echo json_encode([]);
			return;
		}

		$id_siswa_aktif_list = array_column($get_siswa, 'id_siswa_aktif');
		$total_siswa = count($id_siswa_aktif_list);
		$id_list_string = implode(",", array_map('intval', $id_siswa_aktif_list));

		// Ambil presensi siswa di kelas
		$get_presensi = $this->mymodel->withquery("
			SELECT id_siswa_aktif, status_absen, tanggal_absen
			FROM {$nama_tabel_presensi}
			WHERE tanggal_absen BETWEEN '{$start_date}' AND '{$end_date}'
			AND id_siswa_aktif IN ({$id_list_string}) 
			GROUP BY id_siswa_aktif, DATE(tanggal_absen)
		", "result");

		// Hitung presensi per bulan
		$presensi_per_bulan = [];

		foreach ($get_presensi as $row) {
			$bulan   = date("Y-m", strtotime($row->tanggal_absen));
			$status  = strtolower($row->status_absen);
			$tanggal = date("Y-m-d", strtotime($row->tanggal_absen));

			if (!isset($presensi_per_bulan[$bulan])) {
				$presensi_per_bulan[$bulan] = [
					'hadir' => 0,
					'terlambat' => 0,
					'alfa' => 0,
					'sakit' => 0,
					'izin' => 0,
					'hari_aktif' => []
				];
			}

			if (isset($presensi_per_bulan[$bulan][$status])) {
				$presensi_per_bulan[$bulan][$status]++;
			}

			$presensi_per_bulan[$bulan]['hari_aktif'][$tanggal] = true; // Hari unik
		}

		// Siapkan data output per bulan
		$start = new DateTime($start_date);
		$end   = new DateTime($end_date);
		$data  = [];

		while ($start <= $end) {
			$bulan_key = $start->format('Y-m');

			$total_hadir     = $presensi_per_bulan[$bulan_key]['hadir']     ?? 0;
			$total_terlambat = $presensi_per_bulan[$bulan_key]['terlambat'] ?? 0;
			$total_alfa      = $presensi_per_bulan[$bulan_key]['alfa']      ?? 0;
			$total_sakit     = $presensi_per_bulan[$bulan_key]['sakit']     ?? 0;
			$total_izin      = $presensi_per_bulan[$bulan_key]['izin']      ?? 0;
			$total_hari_aktif = isset($presensi_per_bulan[$bulan_key]['hari_aktif'])
								? count($presensi_per_bulan[$bulan_key]['hari_aktif'])
								: 0;

			//hitung tingkat persentase presensi digital
			$tingkat_presensi_digital = 0;
			if ($total_hari_aktif > 0) {
				$tingkat_presensi_digital = (($total_hadir + $total_terlambat + $total_izin + $total_sakit + $total_alfa) / ($total_hari_aktif * $total_siswa) ) * 100;
			}
			$data[] = [
				'bulan'            => formatBulan($bulan_key) . " " . date("Y", strtotime($bulan_key)),
				'total_siswa'      => $total_siswa,
				'total_hari_aktif' => $total_hari_aktif,
				'total_hadir_dan_presensi'      => $total_hadir + $total_terlambat,
				'total_terlambat'  => $total_terlambat,
				'total_sakit'      => $total_sakit,
				'total_izin'       => $total_izin,
				'total_alfa'       => $total_alfa,
				'tingkat_presensi_digital' => number_format($tingkat_presensi_digital,2,",","")
			];

			$start->modify('first day of next month');
		}

		echo json_encode($data);
	}

	public function export_chart_presensi(){
		$id_tahun_ajaran = $this->input->get("id_tahun_ajaran");
		$id_tingkatan = $this->input->get("id_tingkatan");
		$id_kelas = $this->input->get("id_kelas");
	
		$tgl_start = $this->input->get("start_date") ? date("Y-m-d", strtotime($this->input->get("start_date"))) : date("Y-m-d");
		$tgl_end = $this->input->get("end_date") ? date("Y-m-d", strtotime($this->input->get("end_date"))) : date("Y-m-d");
	
		$kelas_query = '';
		if (!empty($id_tingkatan)) {
			$kelas_query = "SELECT * FROM kelas_smp WHERE id_tingkatan = '$id_tingkatan'";
		} elseif (!empty($id_kelas)) {
			$kelas_query = "SELECT * FROM kelas_smp WHERE id_kelas_smp = '$id_kelas'";
		}
		$get_kelas = $kelas_query ? $this->mymodel->withquery($kelas_query, "result") : [];
	
		$start = new DateTime($tgl_start);
		$end = (new DateTime($tgl_end))->modify('+1 day');
		$interval = new DateInterval('P1D');
		$period = new DatePeriod($start, $interval, $end);
	
		$field = ['nomor', 'nis', 'id_siswa', 'nama_kelas'];
		foreach ($period as $date) {
			$field[] = $date->format("d/m/Y");
		}
		$field = array_merge($field, ["total_hadir", "total_alfa", "total_terlambat", "total_sakit", "total_izin"]);
	
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
	
		if (!empty($get_kelas)) {
			foreach ($get_kelas as $key_kelas => $value_kelas) {
				$where = [];
				if (!empty($id_tahun_ajaran)) $where[] = "s.id_tahun_ajaran = '$id_tahun_ajaran'";
				if (!empty($value_kelas->id_kelas_smp)) $where[] = "s.id_kelas = '{$value_kelas->id_kelas_smp}'";
	
				$sql_where = $where ? "WHERE " . implode(" AND ", $where) : "";
				$get_siswa = $this->mymodel->withquery("SELECT s.id_siswa_smp_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas FROM siswa_smp_aktif s JOIN kelas_smp k ON s.id_kelas = k.id_kelas_smp $sql_where ORDER BY k.label ASC, s.nama_lengkap ASC", "result");
	
				$presensi_map = [];
				$presensi_rows = $this->mymodel->withquery("SELECT * FROM presensi_smp WHERE tanggal_absen BETWEEN '$tgl_start' AND '$tgl_end'", "result");
				foreach ($presensi_rows as $presensi) {
					$presensi_map[$presensi->id_siswa_aktif][$presensi->tanggal_absen] = $presensi;
				}
	
				foreach ($get_siswa as $key => $value) {
					$totals = ['Hadir' => 0, 'Tidak Hadir' => 0, 'Alfa' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0];
	
					foreach ($period as $date) {
						$tgl = $date->format("Y-m-d");
						$label = $date->format("d/m/Y");
						$presensi = $presensi_map[$value->id_siswa][$tgl] ?? null;
	
						if ($presensi) {
							$status = $presensi->status_absen;
							$waktu = date("H:i", strtotime($presensi->waktu_absen));
							$value->$label = substr($status, 0, 1) . " ($waktu) (" . $presensi->wifi_ssid . ")";
							if ($status === "Terlambat") {
								$value->$label .= $presensi->alasan_terlambat;
							}
							$totals[$status]++;
						} else {
							$value->$label = "-";
						}
					}
	
					$value->total_hadir = $totals['Hadir'];
					$value->total_alfa = $totals['Tidak Hadir'] + $totals['Alfa'];
					$value->total_terlambat = $totals['Terlambat'];
					$value->total_sakit = $totals['Sakit'];
					$value->total_izin = $totals['Izin'];
				}
	
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex($key_kelas)->setTitle($value_kelas->label);
	
				$rowCount = 3;
				$column = 'A';
				foreach ($field as $col) {
					$label = strtoupper(str_replace(["_", "id_siswa"], [" ", "SISWA"], $col));
					if ($col == "nomor") $label = "NO";
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $label);
					$column++;
				}
	
				$rowCount = 4;
				foreach ($get_siswa as $key => $value) {
					$column = 'A';
					foreach ($field as $col) {
						$val = isset($value->$col) ? strip_tags($value->$col) : '';
						if ($col == "nomor") $val = $key + 1;
						if ($col == "id_kelas") $val = $value->nama_kelas;
						if ($col == "id_siswa") $val = $value->nama_lengkap;
						$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $val);
						$column++;
					}
					$rowCount++;
				}
			}
		}
	
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Presensi SMP - ' . formatTanggal($tgl_start) . ' s/d ' . formatTanggal($tgl_end) . '.xls"'); 
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}

	public function get_tingkatan()
	{
		$jenjang = $this->input->post("unit");
		$result = $this->mymodel->withquery("SELECT id_tingkatan_".$jenjang." as id_tingkatan, label FROM tingkatan_".$jenjang." WHERE label != 'Mutasi' ORDER BY label ASC", "result");

		echo json_encode($result);
	}

	public function get_kelas()
	{
		$jenjang = $this->input->post("unit");
		$tingkatan = $this->input->post("tingkatan");
		$result = $this->mymodel->withquery("SELECT id_kelas_".$jenjang." as id_kelas, label FROM kelas_".$jenjang." WHERE id_tingkatan = '".$tingkatan."' and label != 'Mutasi' and label != 'Lulus' and label != 'Keluar' and label != 'Alumni' ORDER BY label ASC", "result");
		
		echo json_encode($result);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */