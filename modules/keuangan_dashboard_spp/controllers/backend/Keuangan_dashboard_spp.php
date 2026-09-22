<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard SPP — modul non-tabel.
 * Sumber data: transaksi_spp (dedup row terbaru per id_spp+bulan+TA).
 */
class Keuangan_dashboard_spp extends Admin
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_keuangan_dashboard_spp');
	}

	public function index()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}
		$data = array();
		$data['bulan_list'] = $this->model_keuangan_dashboard_spp->daftar_bulan();
		$tahun_awal = 2000;
		$tahun_akhir = intval(date('Y')) + 2;
		$tahun_list = array();
		for ($i = $tahun_akhir; $i >= $tahun_awal; $i--) {
			$tahun_list[] = $i;
		}
		$data['tahun_list'] = $tahun_list;
		$data['ta_options'] = $this->model_keuangan_dashboard_spp->get_tahun_ajaran_options();
		$data['ta_aktif'] = $this->model_keuangan_dashboard_spp->get_active_tahun_ajaran();
		$data['bulan_default'] = intval(date('n'));
		$data['tahun_default'] = intval(date('Y'));
		$this->template->title('Dashboard SPP');
		$this->render('backend/standart/administrator/keuangan_dashboard_spp/dashboard_spp', $data);
	}

	/**
	 * Endpoint core (POST AJAX): bulan, tahun, jenjang, kelas, status, page.
	 * Return JSON: summary (+vs bulan lalu), bar kategori, pie status, tabel detail.
	 */
	public function data()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$bulan = intval($this->input->post('bulan'));
		$tahun = intval($this->input->post('tahun'));
		if ($bulan < 1 || $bulan > 12) {
			$bulan = intval(date('n'));
		}
		if ($tahun < 2000) {
			$tahun = intval(date('Y'));
		}

		$filters = array(
			'jenjang' => trim($this->input->post('jenjang')),
			'kelas'   => trim($this->input->post('kelas')),
			'status'  => $this->input->post('status'),
			'tingkatan' => trim($this->input->post('tingkatan')),
		);

		// ---- summary bulan ini + bulan lalu ----
		// 1 scan utk 2 periode (conditional aggregate); dedup cukup materialize 1x
		$core2 = $this->model_keuangan_dashboard_spp->get_core_two_months($bulan, $tahun, $filters);
		$core = ($core2 === null) ? null : $core2['rows'];
		$summary = $this->_build_summary($core);
		$summary_prev = ($core2 !== null && $core2['prev_valid'])
			? $this->_build_summary($core, 'nominal_prev')
			: $this->_build_summary(null);

		$bar = array(
			'awal'   => 0, 'tepat' => 0, 'telat' => 0, 'dimuka' => 0,
			'awal_nominal' => 0, 'tepat_nominal' => 0, 'telat_nominal' => 0, 'dimuka_nominal' => 0,
		);
		$pie = array('belum' => 0, 'menunggu' => 0, 'lunas' => 0);
		if (is_array($core)) {
			foreach ($core as $row) {
				if (intval($row->status_transaksi) === 0) {
					$pie['belum'] += intval($row->c);
				} elseif (intval($row->status_transaksi) === 1) {
					$pie['menunggu'] += intval($row->c);
				} elseif (intval($row->status_transaksi) === 2) {
					$pie['lunas'] += intval($row->c);
					if ($row->kategori !== null) {
						$bar[$row->kategori] += intval($row->c);
						$bar[$row->kategori . '_nominal'] += intval($row->nominal);
					}
				}
			}
		}

		// ---- tabel detail (paginasi) — filter tanggal hanya utk tabel ini ----
		$page = max(1, intval($this->input->post('page')));
		$per_page = 25;
		$sort_by = trim($this->input->post('sort_by'));
		$sort_dir = trim($this->input->post('sort_dir'));
		$date_start = trim($this->input->post('start_date'));
		$date_end = trim($this->input->post('end_date'));
		$total = $this->model_keuangan_dashboard_spp->count_detail($bulan, $tahun, $filters, $date_start, $date_end);
		$rows = $this->model_keuangan_dashboard_spp->get_detail($bulan, $tahun, $filters, $per_page, ($page - 1) * $per_page, $sort_by, $sort_dir, $date_start, $date_end);

		$out = array(
			'summary' => array(
				'bulan_ini' => $summary,
				'bulan_lalu' => $summary_prev,
			),
			'bar' => $bar,
			'pie' => $pie,
			'detail' => array(
				'rows' => $rows,
				'total' => $total,
				'page' => $page,
				'per_page' => $per_page,
				'total_page' => ceil($total / $per_page),
			),
			'kelas_options' => $this->model_keuangan_dashboard_spp->get_kelas_options(),
			'tingkatan_options' => $this->model_keuangan_dashboard_spp->get_tingkatan_options(),
			'filter' => array('bulan' => $bulan, 'tahun' => $tahun),
		);
		header('Content-Type: application/json');
		echo json_encode($out);
	}

	/**
	 * Trend 12 bulan rolling (POST AJAX): bulan, tahun.
	 * Return JSON: labels + nominal per bulan (nol utk bulan tanpa data).
	 */
	public function trend()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$bulan = intval($this->input->post('bulan'));
		$tahun = intval($this->input->post('tahun'));
		if ($bulan < 1 || $bulan > 12) {
			$bulan = intval(date('n'));
		}
		if ($tahun < 2000) {
			$tahun = intval(date('Y'));
		}
		$filters = array(
			'jenjang' => trim($this->input->post('jenjang')),
			'kelas'   => trim($this->input->post('kelas')),
			'status'  => $this->input->post('status'),
			'tingkatan' => trim($this->input->post('tingkatan')),
		);
		$rows = $this->model_keuangan_dashboard_spp->get_trend($bulan, $tahun, $filters);

		// isi nol utk bulan tanpa data
		$by_ym = array();
		foreach ($rows as $r) {
			$by_ym[$r->ym] = $r;
		}
		$labels = array();
		$nominals = array();
		$counts = array();
		for ($i = 11; $i >= 0; $i--) {
			$ts = mktime(0, 0, 0, $bulan - $i, 1, $tahun);
			$ym = date('Y-m', $ts);
			$labels[] = $this->model_keuangan_dashboard_spp->nama_bulan(intval(date('n', $ts))) . ' ' . date('y', $ts);
			$nominals[] = isset($by_ym[$ym]) ? intval($by_ym[$ym]->nominal) : 0;
			$counts[] = isset($by_ym[$ym]) ? intval($by_ym[$ym]->c) : 0;
		}
		header('Content-Type: application/json');
		echo json_encode(array('labels' => $labels, 'nominals' => $nominals, 'counts' => $counts));
	}

	/**
	 * Aging report / daftar penunggak (POST AJAX). Global, tidak terikat filter bulan.
	 */
	public function aging()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$filters = array(
			'jenjang' => trim($this->input->post('jenjang')),
			'kelas'   => trim($this->input->post('kelas')),
			'status'  => $this->input->post('status'),
			'tingkatan' => trim($this->input->post('tingkatan')),
			'id_tahun_ajaran' => intval($this->input->post('id_tahun_ajaran')),
			'bulan'   => intval($this->input->post('bulan')),
			'tahun'   => intval($this->input->post('tahun')),
			'q'       => substr(trim($this->input->post('q')), 0, 50),
		);
		$per_page_choices = array(25, 50, 100);
		$per_page = intval($this->input->post('per_page'));
		$per_page = in_array($per_page, $per_page_choices) ? $per_page : 25;
		$page = max(1, intval($this->input->post('page')));
		$total = $this->model_keuangan_dashboard_spp->get_aging_count($filters);
		$rows = $this->model_keuangan_dashboard_spp->get_aging($per_page, $filters, ($page - 1) * $per_page);
		header('Content-Type: application/json');
		echo json_encode(array(
			'rows' => $rows,
			'total' => $total,
			'page' => $page,
			'per_page' => $per_page,
			'total_page' => ceil($total / $per_page),
		));
	}

	/**
	 * Detail tunggakan 1 siswa utk modal (POST AJAX): jenjang, nis, id_tahun_ajaran,
	 * bulan, tahun (filter aktif Daftar Penunggak saat tombol diklik).
	 * Return JSON: siswa, terakhir (rentang aging aktif), semua (tanpa rentang).
	 */
	public function aging_detail()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$jenjang = strtoupper(trim($this->input->post('jenjang')));
		$nis = trim($this->input->post('nis'));
		$filters = array(
			'jenjang' => $jenjang,
			'kelas'   => '',
			'status'  => $this->input->post('status'),
			'id_tahun_ajaran' => intval($this->input->post('id_tahun_ajaran')),
			'bulan'   => intval($this->input->post('bulan')),
			'tahun'   => intval($this->input->post('tahun')),
		);
		$terakhir_rows = $this->model_keuangan_dashboard_spp->get_aging_detail_terakhir($jenjang, $nis, $filters);
		$semua_rows = $this->model_keuangan_dashboard_spp->get_aging_detail_semua($jenjang, $nis);
		$siswa = array('jenjang' => $jenjang, 'nama' => '', 'nis' => $nis, 'kelas' => '');
		$src = !empty($terakhir_rows) ? $terakhir_rows : $semua_rows;
		if (!empty($src)) {
			$siswa['nama'] = $src[0]->nama;
			$siswa['kelas'] = $src[0]->kelas;
		}
		$out = array(
			'siswa' => $siswa,
			'terakhir' => array('rows' => $terakhir_rows, 'total_nominal' => 0),
			'semua' => array('rows' => $semua_rows, 'total_nominal' => 0),
		);
		foreach ($terakhir_rows as $r) { $out['terakhir']['total_nominal'] += intval($r->total_biaya); }
		foreach ($semua_rows as $r) { $out['semua']['total_nominal'] += intval($r->total_biaya); }
		header('Content-Type: application/json');
		echo json_encode($out);
	}

	/**
	 * Export PDF laporan tunggakan 1 siswa (GET dari modal List Tunggakan):
	 * jenjang, nis, scope=terakhir|semua, id_tahun_ajaran, bulan, tahun.
	 * Layout mengikuti contoh tagihan (kop + identitas + rincian per TA + Jumlah).
	 */
	public function export_tunggakan()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$jenjang = strtoupper(trim($this->input->get('jenjang')));
		$nis = trim($this->input->get('nis'));
		if ($nis === '' || !in_array($jenjang, array('SD', 'SMP', 'SMA', 'FT'), true)) {
			show_404();
		}
		$scope = ($this->input->get('scope') === 'terakhir') ? 'terakhir' : 'semua';
		$bulan = intval($this->input->get('bulan'));
		$tahun = intval($this->input->get('tahun'));
		if ($bulan < 1 || $bulan > 12) {
			$bulan = intval(date('n'));
		}
		if ($tahun < 2000) {
			$tahun = intval(date('Y'));
		}
		$filters = array(
			'jenjang' => $jenjang,
			'kelas'   => '',
			'status'  => '',
			'id_tahun_ajaran' => intval($this->input->get('id_tahun_ajaran')),
			'bulan'   => $bulan,
			'tahun'   => $tahun,
		);
		$data_pdf = $this->model_keuangan_dashboard_spp->build_tunggakan_pdf_data($jenjang, $nis, $filters, $scope);
		$data_pdf['periode'] = 'SAMPAI DENGAN ' . strtoupper($this->model_keuangan_dashboard_spp->nama_bulan($bulan)) . ' ' . $tahun;
		$data_pdf['dicetak'] = date('l, d F Y H:i:s');
		$filename = 'laporan_tunggakan_spp_' . strtolower($jenjang) . '_' . $nis . '_' . $scope;

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5),
		);
		$this->pdf = new HtmlPdf($config);
		$content = $this->pdf->loadHtmlPdf('backend/standart/administrator/keuangan_dashboard_spp/export_tunggakan_pdf', $data_pdf, TRUE);
		$this->pdf->initialize($config);
		$this->pdf->pdf->SetDisplayMode('fullpage');
		$this->pdf->writeHTML($content);
		$this->pdf->Output($filename . '.pdf', 'D');
	}

	/**
	 * Breakdown per jenjang (POST AJAX): bulan, tahun.
	 */
	public function breakdown_jenjang()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$bulan = intval($this->input->post('bulan'));
		$tahun = intval($this->input->post('tahun'));
		if ($bulan < 1 || $bulan > 12) {
			$bulan = intval(date('n'));
		}
		if ($tahun < 2000) {
			$tahun = intval(date('Y'));
		}
		$filters = array(
			'jenjang' => '', // breakdown memang menampilkan semua jenjang
			'kelas'   => trim($this->input->post('kelas')),
			'status'  => $this->input->post('status'),
			'tingkatan' => trim($this->input->post('tingkatan')),
		);
		$rows = $this->model_keuangan_dashboard_spp->get_breakdown_jenjang($bulan, $tahun, $filters);
		$out = array('SD' => array('tagihan' => 0, 'lunas_nominal' => 0, 'lunas_count' => 0),
			'SMP' => array('tagihan' => 0, 'lunas_nominal' => 0, 'lunas_count' => 0),
			'SMA' => array('tagihan' => 0, 'lunas_nominal' => 0, 'lunas_count' => 0),
			'FT'  => array('tagihan' => 0, 'lunas_nominal' => 0, 'lunas_count' => 0));
		foreach ($rows as $r) {
			if (isset($out[$r->jenjang])) {
				$out[$r->jenjang] = array(
					'tagihan' => intval($r->tagihan),
					'lunas_nominal' => intval($r->lunas_nominal),
					'lunas_count' => intval($r->lunas_count),
				);
			}
		}
		header('Content-Type: application/json');
		echo json_encode($out);
	}

	/**
	 * Opsi kelas terfilter tingkatan (POST AJAX): jenjang, tingkatan.
	 * Utk cascading dropdown Kelas.
	 */
	public function kelas_options()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$jenjang = trim($this->input->post('jenjang'));
		$tingkatan = trim($this->input->post('tingkatan'));
		$rows = $this->model_keuangan_dashboard_spp->get_kelas_by_tingkatan($jenjang, $tingkatan);
		header('Content-Type: application/json');
		echo json_encode(array('kelas' => $rows));
	}

	/**
	 * Export Excel Rekapitulasi Tunggakan SPP (GET): bulan, tahun.
	 * Per unit + tingkatan, 6 bulan semester (ala sample Rekap Tunggakan).
	 */
	public function export_rekap()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$bulan = intval($this->input->get('bulan'));
		$tahun = intval($this->input->get('tahun'));
		if ($bulan < 1 || $bulan > 12) {
			$bulan = intval(date('n'));
		}
		if ($tahun < 2000) {
			$tahun = intval(date('Y'));
		}
		$rekap = $this->model_keuangan_dashboard_spp->get_rekap_tunggakan($bulan, $tahun);
		if ($rekap === null) {
			show_error('Tahun ajaran untuk periode ini tidak ditemukan.');
			return;
		}
		// cut-off: akhir bulan filter, atau hari ini jika bulan/tahun berjalan
		if ($bulan === intval(date('n')) && $tahun === intval(date('Y'))) {
			$tgl = intval(date('j'));
		} else {
			$tgl = intval(date('t', mktime(0, 0, 0, $bulan, 1, $tahun)));
		}
		$periode = 'Per ' . $tgl . ' ' . $this->model_keuangan_dashboard_spp->nama_bulan($bulan) . ' ' . $tahun;
		$this->load->library('Keuangan_rekap_excel');
		$this->keuangan_rekap_excel->generate($rekap, $periode, 'rekap_tunggakan_spp_' . $bulan . '_' . $tahun);
	}

	/**
	 * Export laporan (GET): bulan, tahun, jenjang, kelas, status, format=xls|pdf.
	 * Sertakan filter di header file.
	 */
	public function export()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			show_404();
		}
		$bulan = intval($this->input->get('bulan'));
		$tahun = intval($this->input->get('tahun'));
		if ($bulan < 1 || $bulan > 12) {
			$bulan = intval(date('n'));
		}
		if ($tahun < 2000) {
			$tahun = intval(date('Y'));
		}
		$filters = array(
			'jenjang' => trim($this->input->get('jenjang')),
			'kelas'   => trim($this->input->get('kelas')),
			'status'  => $this->input->get('status'),
		);
		$format = ($this->input->get('format') === 'pdf') ? 'pdf' : 'xls';
		$sort_by = trim($this->input->get('sort_by'));
		$sort_dir = trim($this->input->get('sort_dir'));
		$date_start = trim($this->input->get('start_date'));
		$date_end = trim($this->input->get('end_date'));

		$summary = $this->_build_summary($this->model_keuangan_dashboard_spp->get_core_by_month($bulan, $tahun));
		$rows = $this->model_keuangan_dashboard_spp->get_detail($bulan, $tahun, $filters, 0, 0, $sort_by, $sort_dir, $date_start, $date_end);
		$filter_info = array(
			'bulan' => $this->model_keuangan_dashboard_spp->nama_bulan($bulan),
			'tahun' => $tahun,
			'jenjang' => ($filters['jenjang'] !== '' ? strtoupper($filters['jenjang']) : 'Semua Jenjang'),
			'kelas' => ($filters['kelas'] !== '' ? $filters['kelas'] : 'Semua Kelas'),
			'status' => $this->_status_label($filters['status']),
		);
		$filename = 'laporan_spp_' . strtolower($filter_info['bulan']) . '_' . $tahun;

		if ($format === 'pdf') {
			$this->_export_pdf($summary, $rows, $filter_info, $filename);
		} else {
			$this->_export_xls($summary, $rows, $filter_info, $filename);
		}
	}

	private function _status_label($status)
	{
		if ($status === '0' || $status === 0) return 'Belum Dibayar';
		if ($status === '1' || $status === 1) return 'Menunggu Pembayaran';
		if ($status === '2' || $status === 2) return 'Lunas';
		return 'Semua Status';
	}

	private function _export_xls($summary, $rows, $filter_info, $filename)
	{
		$this->load->library('Keuangan_dashboard_excel');
		$this->keuangan_dashboard_excel->generate($summary, $rows, $filter_info, $filename);
	}

	private function _export_pdf($summary, $rows, $filter_info, $filename)
	{
		$this->load->library('HtmlPdf');
		$config = array(
			'orientation' => 'l',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5),
		);
		$this->pdf = new HtmlPdf($config);
		$content = $this->pdf->loadHtmlPdf('backend/standart/administrator/keuangan_dashboard_spp/export_pdf', array(
			'summary' => $summary,
			'rows' => $rows,
			'filter_info' => $filter_info,
		), TRUE);
		$this->pdf->initialize($config);
		$this->pdf->pdf->SetDisplayMode('fullpage');
		$this->pdf->writeHTML($content);
		$this->pdf->Output($filename . '.pdf', 'D');
	}

	/** Susun summary dari hasil get_core_by_month / get_core_two_months. */
	private function _build_summary($core, $nominal_field = 'nominal')
	{
		$out = array(
			'total_tagihan' => 0,
			'total_diterima' => 0,
			'belum_dibayar' => 0,
			'kolektibilitas' => 0,
			'invalid' => false,
		);
		if ($core === null) {
			$out['invalid'] = true; // TA tidak ada di tabel
			return $out;
		}
		if (is_array($core)) {
			foreach ($core as $row) {
				$out['total_tagihan'] += intval($row->{$nominal_field});
				if (intval($row->status_transaksi) === 2) {
					$out['total_diterima'] += intval($row->{$nominal_field});
				}
			}
		}
		$out['belum_dibayar'] = $out['total_tagihan'] - $out['total_diterima'];
		if ($out['total_tagihan'] > 0) {
			$out['kolektibilitas'] = round($out['total_diterima'] / $out['total_tagihan'] * 100, 1);
		}
		return $out;
	}
}
