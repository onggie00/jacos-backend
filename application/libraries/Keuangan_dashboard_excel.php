<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . "/libraries/Excel/PHPExcel.php";
require_once APPPATH . "/libraries/Excel/PHPExcel/IOFactory.php";

/**
 * Export Excel laporan Dashboard SPP.
 * Header file berisi filter aktif (bulan/tahun/jenjang/kelas/status) + ringkasan.
 */
class Keuangan_dashboard_excel {

	private $excel;
	private $CI;

	private $header_bg   = 'C2410C';
	private $header_text = 'FFFFFF';
	private $info_bg     = 'FFF7ED';

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->excel = new PHPExcel();
	}

	public function generate($summary, $rows, $filter_info, $filename)
	{
		$this->excel->getProperties()->setCreator('LabSchool')
			->setTitle('Laporan SPP ' . $filter_info['bulan'] . ' ' . $filter_info['tahun']);

		$sheet = $this->excel->getActiveSheet();
		$sheet->setTitle('Laporan SPP');

		// ---- header filter ----
		$sheet->setCellValue('A1', 'LAPORAN DASHBOARD SPP');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->setCellValue('A2', 'Periode: ' . $filter_info['bulan'] . ' ' . $filter_info['tahun']);
		$sheet->setCellValue('A3', 'Jenjang: ' . $filter_info['jenjang'] . '   |   Kelas: ' . $filter_info['kelas'] . '   |   Status: ' . $filter_info['status']);
		$sheet->getStyle('A2:A3')->getFont()->setSize(11);

		// ---- ringkasan ----
		$sheet->setCellValue('A5', 'Total Tagihan');
		$sheet->setCellValue('B5', $summary['total_tagihan']);
		$sheet->setCellValue('A6', 'Total Diterima');
		$sheet->setCellValue('B6', $summary['total_diterima']);
		$sheet->setCellValue('A7', 'Total Belum Dibayar');
		$sheet->setCellValue('B7', $summary['belum_dibayar']);
		$sheet->setCellValue('A8', 'Kolektibilitas');
		$sheet->setCellValue('B8', $summary['kolektibilitas'] . '%');
		$sheet->getStyle('A5:A8')->getFont()->setBold(true);
		$sheet->getStyle('B5:B7')->getNumberFormat()->setFormatCode('#,##0');

		// ---- tabel detail ----
		$headers = array('No Transaksi', 'Siswa', 'Jenjang', 'Kelas', 'Bulan', 'Jumlah Tagihan', 'Detail Bulan', 'Nominal', 'Total Dibayar', 'Status', 'Kategori', 'Tgl Bayar');
		$row = 10;
		$col = 0;
		foreach ($headers as $h) {
			$sheet->setCellValueByColumnAndRow($col, $row, $h);
			$col++;
		}
		$sheet->getStyle('A' . $row . ':L' . $row)->getFont()->setBold(true)->getColor()->setRGB($this->header_text);
		$sheet->getStyle('A' . $row . ':L' . $row)->getFill()->getStartColor()->setRGB($this->header_bg);

		$r = $row + 1;
		$no = 1;
		foreach ($rows as $rowdata) {
			$sheet->setCellValueByColumnAndRow(0, $r, $rowdata->no_transaksi);
			$sheet->setCellValueByColumnAndRow(1, $r, $rowdata->user_name);
			$sheet->setCellValueByColumnAndRow(2, $r, $rowdata->jenjang);
			$sheet->setCellValueByColumnAndRow(3, $r, $rowdata->kelas);
			$sheet->setCellValueByColumnAndRow(4, $r, $rowdata->bulan);
			$sheet->setCellValueByColumnAndRow(5, $r, $rowdata->count_bill ? intval($rowdata->count_bill) : 1);
			$sheet->setCellValueByColumnAndRow(6, $r, $rowdata->detail_bulan);
			$sheet->setCellValueByColumnAndRow(7, $r, intval($rowdata->total_biaya));
			$sheet->setCellValueByColumnAndRow(8, $r, intval($rowdata->total_biaya) * max(1, intval($rowdata->count_bill)));
			$sheet->setCellValueByColumnAndRow(9, $r, $this->_status_text($rowdata->status_transaksi));
			$sheet->setCellValueByColumnAndRow(10, $r, $this->_kategori_text($rowdata->kategori));
			$sheet->setCellValueByColumnAndRow(11, $r, $rowdata->updated_at);
			$sheet->getStyle('H' . $r . ':I' . $r)->getNumberFormat()->setFormatCode('#,##0');
			$r++;
			$no++;
		}

		// lebar kolom
		$widths = array(28, 28, 9, 12, 12, 14, 40, 14, 14, 12, 18, 18);
		foreach ($widths as $i => $w) {
			$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($w);
		}
		$sheet->getStyle('A' . $row . ':L' . ($r - 1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		// tulis ke file temp dulu: save('php://output') + exit membuat header CI
		// (content-type/disposition) tidak pernah terkirim → browser menampilkan
		// binary xlsx mentah sebagai teks.
		$tmp = tempnam(sys_get_temp_dir(), 'rekap_xlsx_');
		$objWriter->save($tmp);
		$xlsx = file_get_contents($tmp);
		unlink($tmp);
		$this->CI->output->set_content_type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$this->CI->output->set_header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		$this->CI->output->set_header('Cache-Control: max-age=0');
		$this->CI->output->set_output($xlsx);
	}

	private function _status_text($s)
	{
		$s = intval($s);
		if ($s === 0) return 'Belum Dibayar';
		if ($s === 1) return 'Menunggu';
		if ($s === 2) return 'Lunas';
		return '-';
	}

	private function _kategori_text($k)
	{
		$map = array('awal' => 'Lebih Awal', 'tepat' => 'Tepat Waktu', 'telat' => 'Telat Bayar', 'dimuka' => 'Pembayaran Dimuka');
		return isset($map[$k]) ? $map[$k] : '-';
	}
}
