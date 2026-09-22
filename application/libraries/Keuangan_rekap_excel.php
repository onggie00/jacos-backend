<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . "/libraries/Excel/PHPExcel.php";
require_once APPPATH . "/libraries/Excel/PHPExcel/IOFactory.php";

/**
 * Export Excel Rekapitulasi Tunggakan SPP per unit + tingkatan
 * (format acuan: sample "Rekap Tunggakan per 20 Agustus 2026").
 */
class Keuangan_rekap_excel {

	private $excel;
	private $CI;

	private $header_bg   = 'C2410C';
	private $header_text = 'FFFFFF';

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->excel = new PHPExcel();
	}

	public function generate($rekap, $periode, $filename)
	{
		$months = $rekap['months'];
		$sheet = $this->excel->getActiveSheet();
		$sheet->setTitle('Rekap Tunggakan');

		$sheet->setCellValue('A1', 'Rekapitulasi Tunggakan SPP');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->setCellValue('A2', $periode);
		$sheet->getStyle('A2')->getFont()->setSize(11);

		$r = 4;
		foreach ($rekap['units'] as $unit) {
			$r = $this->_write_unit($sheet, $r, $unit, $months);
			$r++;
		}

		// grand total
		$g = $rekap['grand'];
		$sheet->setCellValue('B' . $r, 'Total');
		$sheet->setCellValue('C' . $r, $g['jml_siswa']);
		for ($i = 0; $i < 6; $i++) {
			$c1 = PHPExcel_Cell::stringFromColumnIndex(4 + $i * 2);
			$c2 = PHPExcel_Cell::stringFromColumnIndex(5 + $i * 2);
			$sheet->setCellValue($c1 . $r, $g['pg'][$i]);
			$sheet->setCellValue($c2 . $r, $g['jml_siswa'] > 0 ? $g['pg'][$i] / $g['jml_siswa'] : 0);
			$sheet->getStyle($c2 . $r)->getNumberFormat()->setFormatCode('0.0%');
		}
		$sheet->setCellValue('Q' . $r, $g['tot_siswa']);
		$sheet->setCellValue('R' . $r, $g['tot_nominal']);
		$sheet->getStyle('A' . $r . ':R' . $r)->getFont()->setBold(true);
		$this->_format_row($sheet, $r);
		$sheet->getStyle('A' . $r . ':R' . $r)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		// lebar kolom
		$widths = array(5, 9, 10, 12, 7, 8, 7, 8, 7, 8, 7, 8, 7, 8, 7, 8, 9, 15);
		foreach ($widths as $i => $w) {
			$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($w);
		}

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

	/** Tulis 1 blok unit; return baris berikutnya. */
	private function _write_unit($sheet, $r, $unit, $months)
	{
		$sheet->setCellValue('A' . $r, 'Unit : ' . $unit['label']);
		$sheet->getStyle('A' . $r)->getFont()->setBold(true);
		$r++;

		// header baris 1
		$sheet->setCellValue('A' . $r, 'No');
		$sheet->setCellValue('B' . $r, 'Kelas');
		$sheet->setCellValue('C' . $r, 'Jml Siswa');
		$sheet->setCellValue('D' . $r, 'Nominal');
		$sheet->setCellValue('E' . $r, 'Jumlah Penunggak SPP');
		$sheet->mergeCells('E' . $r . ':P' . $r);
		$sheet->setCellValue('Q' . $r, 'Total Tunggakan');
		$sheet->mergeCells('Q' . $r . ':R' . ($r + 1));
		// header baris 2: nama bulan (masing-masing merge 2 kolom)
		for ($i = 0; $i < 6; $i++) {
			$c1 = PHPExcel_Cell::stringFromColumnIndex(4 + $i * 2);
			$c2 = PHPExcel_Cell::stringFromColumnIndex(5 + $i * 2);
			$sheet->setCellValue($c1 . ($r + 1), $months[$i]);
			$sheet->mergeCells($c1 . ($r + 1) . ':' . $c2 . ($r + 1));
		}
		for ($ci = 0; $ci < 18; $ci++) {
			$col = PHPExcel_Cell::stringFromColumnIndex($ci);
			$sheet->getStyle($col . $r)->getFont()->setBold(true);
			$sheet->getStyle($col . $r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setRGB($this->header_bg);
			$sheet->getStyle($col . $r)->getFont()->setColor(new PHPExcel_Style_Color($this->header_text));
			$sheet->getStyle($col . ($r + 1))->getFont()->setBold(true);
			$sheet->getStyle($col . ($r + 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setRGB($this->header_bg);
			$sheet->getStyle($col . ($r + 1))->getFont()->setColor(new PHPExcel_Style_Color($this->header_text));
		}
		$sheet->getStyle('A' . $r . ':R' . ($r + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$r += 2;

		// baris tingkatan
		$no = 1;
		foreach ($unit['rows'] as $row) {
			$sheet->setCellValue('A' . $r, $no++);
			$sheet->setCellValue('B' . $r, $row->tingkatan);
			$sheet->setCellValue('C' . $r, intval($row->jml_siswa));
			$sheet->setCellValue('D' . $r, intval($row->nominal));
			$sheet->getStyle('D' . $r)->getNumberFormat()->setFormatCode('#,##0');
			for ($i = 0; $i < 6; $i++) {
				$c1 = PHPExcel_Cell::stringFromColumnIndex(4 + $i * 2);
				$c2 = PHPExcel_Cell::stringFromColumnIndex(5 + $i * 2);
				$pg = intval($row->{'pg_' . $i});
				$sheet->setCellValue($c1 . $r, $pg);
				$sheet->setCellValue($c2 . $r, intval($row->jml_siswa) > 0 ? $pg / intval($row->jml_siswa) : 0);
				$sheet->getStyle($c2 . $r)->getNumberFormat()->setFormatCode('0.0%');
			}
			$sheet->setCellValue('Q' . $r, intval($row->tot_siswa));
			$sheet->setCellValue('R' . $r, intval($row->tot_nominal));
			$this->_format_row($sheet, $r);
			$sheet->getStyle('A' . $r . ':R' . $r)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$r++;
		}

		// subtotal unit
		$sub = $unit['subtotal'];
		$sheet->setCellValue('B' . $r, 'Jumlah');
		$sheet->setCellValue('C' . $r, $sub['jml_siswa']);
		for ($i = 0; $i < 6; $i++) {
			$c1 = PHPExcel_Cell::stringFromColumnIndex(4 + $i * 2);
			$c2 = PHPExcel_Cell::stringFromColumnIndex(5 + $i * 2);
			$sheet->setCellValue($c1 . $r, $sub['pg'][$i]);
			$sheet->setCellValue($c2 . $r, $sub['jml_siswa'] > 0 ? $sub['pg'][$i] / $sub['jml_siswa'] : 0);
			$sheet->getStyle($c2 . $r)->getNumberFormat()->setFormatCode('0.0%');
		}
		$sheet->setCellValue('Q' . $r, $sub['tot_siswa']);
		$sheet->setCellValue('R' . $r, $sub['tot_nominal']);
		$sheet->getStyle('A' . $r . ':R' . $r)->getFont()->setBold(true);
		$this->_format_row($sheet, $r);
		$sheet->getStyle('A' . $r . ':R' . $r)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		return $r + 1;
	}

	/** Format angka kolom nominal/total + alignment tengah utk kolom kecil. */
	private function _format_row($sheet, $r)
	{
		$sheet->getStyle('D' . $r)->getNumberFormat()->setFormatCode('#,##0');
		$sheet->getStyle('R' . $r)->getNumberFormat()->setFormatCode('#,##0');
		$sheet->getStyle('A' . $r . ':B' . $r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('E' . $r . ':Q' . $r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
}
