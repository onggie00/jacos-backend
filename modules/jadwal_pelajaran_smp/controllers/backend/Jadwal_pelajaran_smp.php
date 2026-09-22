<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Jadwal Pelajaran Smp Controller
*| --------------------------------------------------------------------------
*| View generated jadwal pelajaran
*|
*/
class Jadwal_pelajaran_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jadwal_pelajaran_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* Show all jadwal
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jadwal_pelajaran_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		$this->data['jadwals'] = $this->model_jadwal_pelajaran_smp->get($filter, $field, $this->limit_page, $offset, array(), $sort, $sort_type);
		$this->data['jadwal_counts'] = $this->model_jadwal_pelajaran_smp->count_all($filter, $field);
		$this->data['stats'] = $this->model_jadwal_pelajaran_smp->get_stats();

		$config = array(
			'base_url'     => 'administrator/jadwal_pelajaran_smp/index/',
			'total_rows'   => $this->model_jadwal_pelajaran_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		// Get kelas list for matrix view
		$this->data['kelas_list'] = $this->db->query("
			SELECT id_kelas_smp, label, id_tingkatan 
			FROM kelas_smp 
			WHERE id_tingkatan != 4 
			ORDER BY id_tingkatan, label
		")->result();

		$this->template->title('Jadwal Pelajaran SMP');
		$this->render('backend/standart/administrator/jadwal_pelajaran_smp/jadwal_pelajaran_smp_list', $this->data);
	}

	/**
	* Matrix view per kelas
	*/
	public function matrix($id_kelas_smp = null)
	{
		$this->is_allowed('jadwal_pelajaran_smp_list');

		if (empty($id_kelas_smp)) {
			$id_kelas_smp = $this->input->get('kelas');
		}

		if (empty($id_kelas_smp)) {
			redirect('administrator/jadwal_pelajaran_smp');
		}

		$this->data['kelas'] = $this->db->get_where('kelas_smp', array('id_kelas_smp' => $id_kelas_smp))->row();
		$this->data['jadwal_matrix'] = $this->model_jadwal_pelajaran_smp->get_matrix_by_kelas($id_kelas_smp);

		// Get all kelas for dropdown
		$this->data['kelas_list'] = $this->db->query("
			SELECT id_kelas_smp, label, id_tingkatan 
			FROM kelas_smp 
			WHERE id_tingkatan != 4 
			ORDER BY id_tingkatan, label
		")->result();

		// Get all hari and jam for matrix structure
		$this->data['hari_list'] = $this->db->query("SELECT * FROM pelajaran_hari ORDER BY id_hari")->result();
		$this->data['jam_list'] = $this->db->query("
			SELECT pj.* FROM pelajaran_jam pj 
			WHERE pj.keterangan LIKE 'MENGAJAR%' 
			ORDER BY pj.id_jam
		")->result();

		$this->template->title('Matrix Jadwal - ' . $this->data['kelas']->label);
		$this->render('backend/standart/administrator/jadwal_pelajaran_smp/jadwal_pelajaran_smp_matrix', $this->data);
	}

	/**
	* View detail
	*/
	public function view($id)
	{
		$this->is_allowed('jadwal_pelajaran_smp_view');

		$this->data['jadwal'] = $this->model_jadwal_pelajaran_smp->join_avaiable()->find($id);

		$this->template->title('Detail Jadwal');
		$this->render('backend/standart/administrator/jadwal_pelajaran_smp/jadwal_pelajaran_smp_view', $this->data);
	}

	/**
	* Ringkasan guru per hari
	*/
	public function ringkasan_guru()
	{
		$this->is_allowed('jadwal_pelajaran_smp_list');

		// Get guru teaching load per day
		$this->data['guru_per_hari'] = $this->db->query("
			SELECT 
				g.id_guru,
				g.nama_lengkap,
				g.kode_mapel,
				g.jam_ajar,
				pelajaran_hari.id_hari,
				pelajaran_hari.hari as nama_hari,
				COUNT(*) as jam_per_hari
			FROM jadwal_pelajaran_smp j
			JOIN guru_smp g ON g.id_guru = j.id_guru
			JOIN pelajaran_setting_waktu psw ON psw.id_pelajaran_waktu = j.id_pelajaran_waktu
			JOIN pelajaran_hari ON pelajaran_hari.id_hari = psw.id_hari
			JOIN pelajaran_jam pj ON pj.id_jam = psw.id_jam
			WHERE pj.keterangan LIKE 'MENGAJAR%'
			GROUP BY g.id_guru, pelajaran_hari.id_hari
			ORDER BY g.nama_lengkap, pelajaran_hari.id_hari
		")->result();

		// Get total per guru
		$this->data['guru_total'] = $this->db->query("
			SELECT 
				g.id_guru,
				g.nama_lengkap,
				g.kode_mapel,
				g.jam_ajar,
				COUNT(*) as total_jam
			FROM jadwal_pelajaran_smp j
			JOIN guru_smp g ON g.id_guru = j.id_guru
			JOIN pelajaran_setting_waktu psw ON psw.id_pelajaran_waktu = j.id_pelajaran_waktu
			JOIN pelajaran_jam pj ON pj.id_jam = psw.id_jam
			WHERE pj.keterangan LIKE 'MENGAJAR%'
			GROUP BY g.id_guru
			ORDER BY g.nama_lengkap
		")->result();

		// Get hari list
		$this->data['hari_list'] = $this->db->query("SELECT * FROM pelajaran_hari ORDER BY id_hari")->result();

		$this->template->title('Ringkasan Guru');
		$this->render('backend/standart/administrator/jadwal_pelajaran_smp/jadwal_pelajaran_smp_ringkasan', $this->data);
	}

	/**
	* Delete jadwal
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jadwal_pelajaran_smp_delete');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_jadwal_pelajaran_smp->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->model_jadwal_pelajaran_smp->remove($id);
			}
		}

		if ($remove) {
            set_message('Jadwal berhasil dihapus', 'success');
        } else {
            set_message('Gagal menghapus jadwal', 'error');
        }

		redirect_back();
	}

	/**
	* Export to Excel - Matrix format per kelas
	*/
	public function export()
	{
		$this->is_allowed('jadwal_pelajaran_smp_export');

		// Load PHPExcel
		require_once APPPATH . 'libraries/Excel/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setTitle('Jadwal Pelajaran SMP Labschool')
			->setSubject('Jadwal Pelajaran')
			->setDescription('Export Jadwal Pelajaran SMP Labschool Cibubur');

		// Get all kelas
		$kelas_list = $this->db->query("
			SELECT id_kelas_smp, label, id_tingkatan 
			FROM kelas_smp 
			WHERE id_tingkatan != 4 
			ORDER BY id_tingkatan, label
		")->result();

		// Get hari and jam
		$hari_list = $this->db->query("SELECT * FROM pelajaran_hari ORDER BY id_hari")->result();
		$jam_list = $this->db->query("SELECT * FROM pelajaran_jam WHERE keterangan LIKE 'MENGAJAR%' ORDER BY id_jam")->result();

		// Build unique jam_ke list
		$jam_ke_list = array();
		foreach ($jam_list as $jl) {
			if (!isset($jam_ke_list[$jl->jam_ke])) {
				$jam_ke_list[$jl->jam_ke] = $jl;
			}
		}
		ksort($jam_ke_list);

		// Sheet 1: Matrix Semua Kelas
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Jadwal Lengkap');

		// Header
		$sheet->setCellValue('A1', 'JADWAL PELAJARAN SMP LABSCHOOL CIBUBUR');
		$sheet->mergeCells('A1:F1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

		$sheet->setCellValue('A2', 'Semester Ganjil Tahun Ajaran 2026/2027');
		$sheet->mergeCells('A2:F2');

		// For each kelas, create a matrix
		$current_row = 4;
		$tingkatan_label = array(1 => 'Kelas 7', 2 => 'Kelas 8', 3 => 'Kelas 9');

		foreach ($kelas_list as $kelas) {
			// Get jadwal for this kelas
			$jadwal = $this->model_jadwal_pelajaran_smp->get_matrix_by_kelas($kelas->id_kelas_smp);

			// Build matrix
			$matrix = array();
			foreach ($jadwal as $j) {
				$matrix[$j->id_hari][$j->jam_ke] = $j->mapel_kode . ' (' . $j->guru_nama . ')';
			}

			// Kelas header
			$sheet->setCellValue('A' . $current_row, $kelas->label);
			$sheet->getStyle('A' . $current_row)->getFont()->setBold(true)->setSize(12);
			$current_row++;

			// Column headers
			$col = 'B';
			foreach ($jam_ke_list as $jk => $jl) {
				$sheet->setCellValue($col . $current_row, 'Jam ' . $jk);
				$sheet->getStyle($col . $current_row)->getFont()->setBold(true);
				$col++;
			}

			// Hari rows
			foreach ($hari_list as $hari) {
				$current_row++;
				$sheet->setCellValue('A' . $current_row, $hari->hari);
				$sheet->getStyle('A' . $current_row)->getFont()->setBold(true);

				$col = 'B';
				foreach ($jam_ke_list as $jk => $jl) {
					if (isset($matrix[$hari->id_hari][$jk])) {
						$sheet->setCellValue($col . $current_row, $matrix[$hari->id_hari][$jk]);
					}
					$col++;
				}
			}

			$current_row += 2;
		}

		// Sheet 2: List Detail
		$sheet2 = $objPHPExcel->createSheet();
		$sheet2->setTitle('Detail List');

		// Headers
		$headers = array('Hari', 'Jam Ke', 'Waktu', 'Kelas', 'Mapel', 'Kode Mapel', 'Guru');
		$col = 'A';
		foreach ($headers as $h) {
			$sheet2->setCellValue($col . '1', $h);
			$sheet2->getStyle($col . '1')->getFont()->setBold(true);
			$col++;
		}

		// Data
		$jadwal_list = $this->model_jadwal_pelajaran_smp->get(null, null, 0, 0);
		$row = 2;
		foreach ($jadwal_list as $j) {
			$sheet2->setCellValue('A' . $row, $j->nama_hari);
			$sheet2->setCellValue('B' . $row, $j->jam_ke);
			$sheet2->setCellValue('C' . $row, $j->jam_pelajaran);
			$sheet2->setCellValue('D' . $row, $j->kelas_label);
			$sheet2->setCellValue('E' . $row, $j->mapel_nama);
			$sheet2->setCellValue('F' . $row, $j->mapel_kode);
			$sheet2->setCellValue('G' . $row, $j->guru_nama);
			$row++;
		}

		// Auto width
		foreach (range('A', 'G') as $col) {
			$sheet2->getColumnDimension($col)->setAutoSize(true);
		}

		// Sheet 3: Ringkasan Guru
		$sheet3 = $objPHPExcel->createSheet();
		$sheet3->setTitle('Ringkasan Guru');

		// Headers
		$headers3 = array('Nama Guru', 'Kode Mapel', 'Kuota', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Total', 'Sisa');
		$col = 'A';
		foreach ($headers3 as $h) {
			$sheet3->setCellValue($col . '1', $h);
			$sheet3->getStyle($col . '1')->getFont()->setBold(true);
			$col++;
		}

		// Get guru data
		$guru_total = $this->db->query("
			SELECT g.id_guru, g.nama_lengkap, g.kode_mapel, g.jam_ajar, COUNT(*) as total_jam
			FROM jadwal_pelajaran_smp j
			JOIN guru_smp g ON g.id_guru = j.id_guru
			JOIN pelajaran_setting_waktu psw ON psw.id_pelajaran_waktu = j.id_pelajaran_waktu
			JOIN pelajaran_jam pj ON pj.id_jam = psw.id_jam
			WHERE pj.keterangan LIKE 'MENGAJAR%'
			GROUP BY g.id_guru
			ORDER BY g.nama_lengkap
		")->result();

		$guru_per_hari = $this->db->query("
			SELECT g.id_guru, psw.id_hari, COUNT(*) as jam_per_hari
			FROM jadwal_pelajaran_smp j
			JOIN guru_smp g ON g.id_guru = j.id_guru
			JOIN pelajaran_setting_waktu psw ON psw.id_pelajaran_waktu = j.id_pelajaran_waktu
			JOIN pelajaran_jam pj ON pj.id_jam = psw.id_jam
			WHERE pj.keterangan LIKE 'MENGAJAR%'
			GROUP BY g.id_guru, psw.id_hari
		")->result();

		$guru_hari_map = array();
		foreach ($guru_per_hari as $gph) {
			if (!isset($guru_hari_map[$gph->id_guru])) {
				$guru_hari_map[$gph->id_guru] = array();
			}
			$guru_hari_map[$gph->id_guru][$gph->id_hari] = $gph->jam_per_hari;
		}

		$row = 2;
		foreach ($guru_total as $gt) {
			$sisa = $gt->jam_ajar - $gt->total_jam;
			$sheet3->setCellValue('A' . $row, $gt->nama_lengkap);
			$sheet3->setCellValue('B' . $row, $gt->kode_mapel);
			$sheet3->setCellValue('C' . $row, $gt->jam_ajar);
			$sheet3->setCellValue('D' . $row, isset($guru_hari_map[$gt->id_guru][1]) ? $guru_hari_map[$gt->id_guru][1] : 0);
			$sheet3->setCellValue('E' . $row, isset($guru_hari_map[$gt->id_guru][2]) ? $guru_hari_map[$gt->id_guru][2] : 0);
			$sheet3->setCellValue('F' . $row, isset($guru_hari_map[$gt->id_guru][3]) ? $guru_hari_map[$gt->id_guru][3] : 0);
			$sheet3->setCellValue('G' . $row, isset($guru_hari_map[$gt->id_guru][4]) ? $guru_hari_map[$gt->id_guru][4] : 0);
			$sheet3->setCellValue('H' . $row, isset($guru_hari_map[$gt->id_guru][5]) ? $guru_hari_map[$gt->id_guru][5] : 0);
			$sheet3->setCellValue('I' . $row, $gt->total_jam);
			$sheet3->setCellValue('J' . $row, $sisa);

			// Highlight overload
			if ($sisa < 0) {
				$sheet3->getStyle('J' . $row)->getFont()->getColor()->setRGB('FF0000');
			}

			$row++;
		}

		// Auto width for sheet 3
		foreach (range('A', 'J') as $col) {
			$sheet3->getColumnDimension($col)->setAutoSize(true);
		}

		// Output
		$filename = 'Jadwal_Pelajaran_SMP_' . date('YmdHis') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	/**
	* Export single kelas matrix
	*/
	public function export_kelas($id_kelas_smp)
	{
		$this->is_allowed('jadwal_pelajaran_smp_export');

		require_once APPPATH . 'libraries/Excel/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setTitle('Jadwal Kelas');

		$kelas = $this->db->get_where('kelas_smp', array('id_kelas_smp' => $id_kelas_smp))->row();
		$jadwal = $this->model_jadwal_pelajaran_smp->get_matrix_by_kelas($id_kelas_smp);

		$hari_list = $this->db->query("SELECT * FROM pelajaran_hari ORDER BY id_hari")->result();
		$jam_list = $this->db->query("SELECT * FROM pelajaran_jam WHERE keterangan LIKE 'MENGAJAR%' ORDER BY id_jam")->result();

		$jam_ke_list = array();
		foreach ($jam_list as $jl) {
			if (!isset($jam_ke_list[$jl->jam_ke])) {
				$jam_ke_list[$jl->jam_ke] = $jl;
			}
		}
		ksort($jam_ke_list);

		$matrix = array();
		foreach ($jadwal as $j) {
			$matrix[$j->id_hari][$j->jam_ke] = $j->mapel_kode . '\n' . $j->guru_nama;
		}

		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Jadwal ' . $kelas->label);

		// Title
		$sheet->setCellValue('A1', 'JADWAL PELAJARAN - ' . $kelas->label);
		$sheet->mergeCells('A1:F1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

		// Headers
		$sheet->setCellValue('A3', 'Hari');
		$sheet->getStyle('A3')->getFont()->setBold(true);
		$col = 'B';
		foreach ($jam_ke_list as $jk => $jl) {
			$sheet->setCellValue($col . '3', 'Jam ' . $jk);
			$sheet->setCellValue($col . '4', $jl->jam_pelajaran);
			$sheet->getStyle($col . '3')->getFont()->setBold(true);
			$col++;
		}

		// Data
		$row = 5;
		foreach ($hari_list as $hari) {
			$sheet->setCellValue('A' . $row, $hari->hari);
			$sheet->getStyle('A' . $row)->getFont()->setBold(true);

			$col = 'B';
			foreach ($jam_ke_list as $jk => $jl) {
				if (isset($matrix[$hari->id_hari][$jk])) {
					$sheet->setCellValue($col . $row, $matrix[$hari->id_hari][$jk]);
					$sheet->getStyle($col . $row)->getAlignment()->setWrapText(true);
				}
				$col++;
			}
			$row++;
		}

		// Auto width
		foreach (range('A', $col) as $c) {
			$sheet->getColumnDimension($c)->setWidth(15);
		}

		// Output
		$filename = 'Jadwal_' . $kelas->label . '_' . date('YmdHis') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	/**
	* Export to PDF
	*/
	public function export_pdf()
	{
		$this->is_allowed('jadwal_pelajaran_smp_export');
		$this->model_jadwal_pelajaran_smp->pdf('jadwal_pelajaran_smp', 'jadwal_pelajaran_smp');
	}
}

/* End of file Jadwal_pelajaran_smp.php */
/* Location: ./modules/jadwal_pelajaran_smp/controllers/backend/Jadwal_pelajaran_smp.php */
