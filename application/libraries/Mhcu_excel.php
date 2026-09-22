<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . "/libraries/Excel/PHPExcel.php";
require_once APPPATH . "/libraries/Excel/PHPExcel/IOFactory.php";

/**
 * Mhcu_excel
 *
 * Helper untuk generate Excel multi-sheet dari data sesi MHCU.
 * Dipakai oleh mhcu_sesi (export by filter) dan mhcu_periode (export per periode).
 *
 * Struktur tiap sheet:
 *   - Header info peserta (NPP, Nama, Role, Periode, dsb)
 *   - Tabel item: No | Instrument | Dimensi/Aspek | Pertanyaan | Jawaban
 *   - Rekomendasi (dari mhcu_hasil_individu)
 *
 * Skor TIDAK ditampilkan (sesuai requirement).
 */
class Mhcu_excel {

    private $CI;
    private $excel;

    // Warna
    private $primary_dark   = 'C2410C';  // Warm Professional primary
    private $primary_light  = 'FED7AA';
    private $header_bg      = 'C2410C';
    private $header_text    = 'FFFFFF';
    private $border_color   = '9A3412';
    private $alt_row        = 'FAFAF9';
    private $info_bg        = 'FFF7ED';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->excel = new PHPExcel();
    }

    /**
     * Generate Excel multi-sheet untuk list sesi.
     * 1 sheet per sesi (1 user).
     *
     * @param array $sesi_list Array of sesi objects (dengan relasi sudah di-load)
     * @param object $periode_info Object mhcu_periode (untuk header file)
     * @param string $filename Nama file output (tanpa ekstensi)
     */
    public function generate_sesi_export($sesi_list, $periode_info, $filename = 'monitoring_mhcu')
    {
        $this->excel->getProperties()->setCreator('LabSchool MHCU')
            ->setTitle('Export Monitoring Sesi MHCU')
            ->setSubject('Data Skrining MHCU');

        if (empty($sesi_list)) {
            // Buat 1 sheet kosong dengan pesan
            $sheet = $this->excel->getActiveSheet();
            $sheet->setTitle('Info');
            $sheet->setCellValue('A1', 'Tidak ada data sesi selesai pada filter ini.');
            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
        } else {
            // ponytail: sort sheet by nama_lengkap biar gampang cari sheet — sumber query model bukan alfabetis
            usort($sesi_list, array($this, '_cmp_nama_lengkap'));
            foreach ($sesi_list as $idx => $sesi) {
                if ($idx === 0) {
                    $sheet = $this->excel->getActiveSheet();
                } else {
                    $sheet = $this->excel->createSheet();
                }
                $sheet_title = $this->_build_sheet_title($sesi, $idx);
                $sheet->setTitle($sheet_title);
                $this->_write_sesi_sheet($sheet, $sesi);
            }
        }

        // Set sheet pertama aktif
        $this->excel->setActiveSheetIndex(0);

        // Output ke browser
        $filename_full = $filename . '_' . date('dmY Hi') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename_full . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Sort by nama_lengkap (case-insensitive).
     */
    private function _cmp_nama_lengkap($a, $b)
    {
        $na = isset($a->nama_lengkap) ? $a->nama_lengkap : '';
        $nb = isset($b->nama_lengkap) ? $b->nama_lengkap : '';
        return strcasecmp($na, $nb);
    }

    /**
     * Tulis 1 worksheet untuk 1 sesi.
     */
    private function _write_sesi_sheet(PHPExcel_Worksheet $sheet, $sesi)
    {
        // Lebar kolom default (A-F: No, Kode, Instrument, Pertanyaan, Jawaban, Skor)
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(45);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(10);

        $row = 1;

        // === HEADER FILE ===
        $sheet->setCellValue('A' . $row, 'LAPORAN SKRINING MHCU');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->primary_light);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row += 2;

        // === INFO PESERTA (kolom A-B) ===
        $info_items = array(
            'NPP' => $sesi->npp,
            'Nama' => $sesi->nama_lengkap,
            'Unit' => isset($sesi->unit) ? $sesi->unit : '-',
            'Jabatan' => isset($sesi->jabatan) ? $sesi->jabatan : '-',
            'Role' => $sesi->presensi_role,
            'Periode' => isset($sesi->nama_periode) ? $sesi->nama_periode : '-',
            'Status' => isset($sesi->status) ? $sesi->status : '-',
            'Waktu Submit' => (!empty($sesi->submitted_at)) ? $sesi->submitted_at : '-',
        );
        if (!empty($sesi->kategori_keseluruhan)) {
            $info_items['Kategori'] = $sesi->kategori_keseluruhan;
        }
        if (isset($sesi->is_krisis) && $sesi->is_krisis == 1) {
            $info_items['Status Krisis'] = 'YA';
        }

        $info_start_row = $row;
        foreach ($info_items as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            if ($label === 'Status Krisis') {
                $sheet->getStyle('B' . $row)->getFont()->setBold(true)->getColor()->setRGB('B91C1C');
            }
            $row++;
        }
        $info_end_row = $row - 1;

        // === DEMOGRAFI PESERTA (kolom D-E, sejajar info peserta) ===
        $demografi = isset($sesi->demografi) ? $sesi->demografi : array();
        $demo_row = $info_start_row;
        if (!empty($demografi)) {
            foreach ($demografi as $demo) {
                $sheet->setCellValue('D' . $demo_row, $demo->demografi_pertanyaan);
                $sheet->setCellValue('E' . $demo_row, !empty($demo->jawaban) ? $demo->jawaban : '-');
                $sheet->getStyle('D' . $demo_row)->getFont()->setBold(true);
                $sheet->getStyle('D' . $demo_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($this->info_bg);
                $demo_row++;
            }
        }

        // Style label info peserta (kolom A)
        $sheet->getStyle('A' . $info_start_row . ':A' . $info_end_row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $info_start_row . ':A' . $info_end_row)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->info_bg);

        // Ambil baris terakhir dari info+demografi
        $row = max($row, $demo_row) + 2;

        // === TABEL PER INSTRUMENT ===
        $items = isset($sesi->items) ? $sesi->items : array();
        if (empty($items)) {
            $sheet->setCellValue('A' . $row, 'Tidak ada data jawaban instrument.');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->getColor()->setRGB('A8A29E');
        } else {
            // Skor total processed hasil Mhcu_scoring, untuk baris rata-rata per instrumen.
            $export_scores = array();
            if (!empty($sesi->export_scores)) {
                foreach ($sesi->export_scores as $score_row) {
                    $export_scores[$score_row->kode_instrument] = $score_row;
                }
            }

            // Group items by instrument
            $grouped = array();
            foreach ($items as $item) {
                $key = isset($item->kode_instrument) ? $item->kode_instrument : 'LAIN';
                if (!isset($grouped[$key])) {
                    $grouped[$key] = array(
                        'kode' => $key,
                        'nama' => isset($item->nama_instrument) ? $item->nama_instrument : $key,
                        'items' => array(),
                    );
                }
                $grouped[$key]['items'][] = $item;
            }

            foreach ($grouped as $inst) {
                // Header instrument
                $sheet->setCellValue('A' . $row, $inst['nama']);
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11)->getColor()->setRGB($this->header_text);
                $sheet->getStyle('A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($this->primary_light);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;

                // Header kolom
                $headers = array('No', 'Kode', 'Instrument', 'Pertanyaan', 'Jawaban', 'Skor');
                $col = 'A';
                foreach ($headers as $h) {
                    $sheet->setCellValue($col . $row, $h);
                    $col++;
                }
                $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true)->getColor()->setRGB($this->header_text);
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($this->header_bg);
                $sheet->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()->setRGB($this->border_color);
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;

                // Data items
                $no = 1;
                $total_skor = 0;
                $data_start_row = $row;
                foreach ($inst['items'] as $item) {
                    $pertanyaan = isset($item->text_item) ? $item->text_item : (isset($item->dimensi_aspek) ? $item->dimensi_aspek : '-');
                    $jawaban = isset($item->jawaban_text) ? $item->jawaban_text : '-';
                    $has_skor = (isset($item->skor_value) && is_numeric($item->skor_value));
                    $skor = $has_skor ? (int)$item->skor_value : 0;

                    // Item reversed CBI: skor kontribusi = 100 - skor_value, samakan
                    // dengan Mhcu_scoring::_score_cbi() supaya total & rata-rata konsisten.
                    // Hanya kalau skor_value memang ada, supaya item tanpa skor tidak
                    // ikut jadi 100.
                    $is_reversed = (isset($item->is_reversed) && $item->is_reversed == 1);
                    if ($has_skor && $is_reversed && $inst['kode'] === 'CBI') {
                        $skor = 100 - $skor;
                        $pertanyaan = $pertanyaan . ' (R)';
                    }
                    $total_skor += $skor;

                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $inst['kode']);
                    $sheet->setCellValue('C' . $row, $inst['nama']);
                    $sheet->setCellValue('D' . $row, $pertanyaan);
                    $sheet->setCellValue('E' . $row, $jawaban);
                    $sheet->setCellValue('F' . $row, $skor);

                    // Style
                    $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                        ->getColor()->setRGB($this->border_color);
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    // Alternating row color
                    if ($no % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($this->alt_row);
                    }

                    // Wrap text
                    $sheet->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

                    $sheet->getRowDimension($row)->setRowHeight(28);
                    $row++;
                    $no++;
                }

                // Baris Total Skor
                $sheet->setCellValue('A' . $row, 'TOTAL SKOR ' . $inst['nama']);
                $sheet->mergeCells('A' . $row . ':E' . $row);
                $sheet->setCellValue('F' . $row, $total_skor);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($this->header_bg);
                $sheet->getStyle('F' . $row)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()->setRGB($this->border_color);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;

                // Skor processed existing, dilabeli rata-rata sesuai kebutuhan laporan.
                $score_key = $inst['kode'];
                if ($score_key === 'PSIKOSOSIAL') {
                    $score_key = 'PSIKOSOSIAL';
                } elseif ($score_key === 'PSIKOSOSIAL_PIMPINAN') {
                    $score_key = 'PSIKOSOSIAL_PIMPINAN';
                }
                if (isset($export_scores[$score_key])) {
                    $average_score = $export_scores[$score_key];
                    $sheet->setCellValue('A' . $row, 'RATA-RATA SKOR ' . $inst['nama']);
                    $sheet->mergeCells('A' . $row . ':E' . $row);
                    $sheet->setCellValue('F' . $row, $average_score->skor);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($this->info_bg);
                    $sheet->getStyle('F' . $row)->getFont()->setBold(true)->setSize(12);
                    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $score_hex = $this->_get_rekap_color_hex($average_score->warna);
                    if (!empty($score_hex)) {
                        $sheet->getStyle('F' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($score_hex);
                    }
                    $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                        ->getColor()->setRGB($this->border_color);
                    $sheet->getRowDimension($row)->setRowHeight(24);
                    $row++;
                }
                $row++; // spasi antar instrument
            }
        }

        // Freeze pane di baris pertama header tabel
        $sheet->freezePane('A' . ($info_end_row + 4));

        $row += 1; // spasi

        // === DESKRIPSI PROFIL ===
        if (!empty($sesi->narasi_kategori)) {
            $sheet->setCellValue('A' . $row, 'DESKRIPSI PROFIL');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setRGB($this->primary_light);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
            $sheet->setCellValue('A' . $row, $sesi->narasi_kategori);
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $sheet->getStyle('A' . $row . ':E' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()->setRGB($this->border_color);
            $sheet->getRowDimension($row)->setRowHeight(80);
            $row += 2;
        }

        // === REKOMENDASI ===
        $sheet->setCellValue('A' . $row, 'REKOMENDASI');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->primary_light);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;

        $rekomendasi = (isset($sesi->saran_rekomendasi) && !empty($sesi->saran_rekomendasi))
            ? $sesi->saran_rekomendasi
            : 'Tidak ada rekomendasi tersedia untuk sesi ini.';
        $sheet->setCellValue('A' . $row, $rekomendasi);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $sheet->getStyle('A' . $row . ':E' . $row)->getBorders()->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
            ->getColor()->setRGB($this->border_color);
        $sheet->getRowDimension($row)->setRowHeight(100);
    }

    /**
     * Generate Excel single-sheet untuk daftar peserta tidak hadir.
     */
    public function generate_tidak_hadir_export($rows, $nama_periode = '')
    {
        $this->excel->getProperties()->setCreator('LabSchool MHCU')
            ->setTitle('Daftar Peserta Tidak Hadir MHCU')
            ->setSubject('Peserta Tidak Hadir - ' . $nama_periode);

        $sheet = $this->excel->getActiveSheet();
        $sheet->setTitle('Tidak Hadir MHCU');

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);

        $row = 1;

        // Header file
        $file_title = 'Tidak Hadir MHCU';
        if (!empty($nama_periode)) {
            $file_title = 'Tidak Hadir MHCU - ' . $nama_periode;
        }
        $sheet->setCellValue('A' . $row, $file_title);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->primary_light);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row += 2;

        // Info periode
        $sheet->setCellValue('A' . $row, 'Periode');
        $sheet->setCellValue('B' . $row, !empty($nama_periode) ? $nama_periode : '-');
        $row++;
        $sheet->setCellValue('A' . $row, 'Tanggal Cetak');
        $sheet->setCellValue('B' . $row, date('d-m-Y H:i'));
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Peserta Tidak Hadir');
        $sheet->setCellValue('B' . $row, count($rows));
        $sheet->getStyle('A3:A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A3:A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->info_bg);
        $row += 2;

        // Header tabel
        $headers = array('No', 'NPP', 'Nama Lengkap', 'Role');
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . $row, $h);
            $col++;
        }
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true)->getColor()->setRGB($this->header_text);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->header_bg);
        $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
            ->getColor()->setRGB($this->border_color);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;

        // Data rows
        if (empty($rows)) {
            $sheet->setCellValue('A' . $row, '-');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->getColor()->setRGB('A8A29E');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        } else {
            // Sort ulang di PHP untuk konsistensi dengan modal: by role, nama_lengkap, npp
            $sorted = $rows;
            usort($sorted, function($a, $b) {
                $r = strcmp(strtolower($a->presensi_role), strtolower($b->presensi_role));
                if ($r !== 0) return $r;
                $n = strcmp(strtolower($a->nama_lengkap), strtolower($b->nama_lengkap));
                if ($n !== 0) return $n;
                return strcmp($a->npp, $b->npp);
            });
            $no = 1;
            foreach ($sorted as $r) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $r->npp);
                $sheet->setCellValue('C' . $row, $r->nama_lengkap);
                $sheet->setCellValue('D' . $row, $r->presensi_role);

                $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()->setRGB($this->border_color);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                if ($no % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($this->alt_row);
                }

                $row++;
                $no++;
            }
        }

        $sheet->freezePane('A' . ($row - count($rows)));

        $this->excel->setActiveSheetIndex(0);

        $filename = 'Tidak_Hadir_MHCU';
        if (!empty($nama_periode)) {
            $slug = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nama_periode);
            $filename = 'Tidak_Hadir_MHCU_' . strtolower($slug);
        }
        $filename_full = $filename . '_' . date('dmY Hi') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename_full . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Generate Excel single-sheet rekap skor instrumen.
     * 1 baris per peserta, kolom: No, Nama, NPP, [Demografi...], WHO-5, PHQ-9, GAD-7, CBI, Psikososial, Instrumen 6, Kategori, Krisis
     *
     * @param array $data Array of rows (nama_lengkap, npp, demografi[], who5, phq9, gad7, cbi, psikososial, instrumen6, kategori, is_krisis)
     * @param array $pertanyaan Array of mhcu_demografi_pertanyaan objects (untuk header kolom dinamis)
     * @param object $periode_info Object mhcu_periode
     * @param string $filename Nama file output (tanpa ekstensi)
     */
    public function generate_rekap_instrumen_export($data, $pertanyaan, $periode_info, $filename = 'rekap_instrumen_mhcu')
    {
        $this->excel->getProperties()->setCreator('LabSchool MHCU')
            ->setTitle('Rekap Skor Instrumen MHCU')
            ->setSubject('Rekap Skor Instrumen');

        $sheet = $this->excel->getActiveSheet();
        $sheet->setTitle('Rekap Instrumen');

        // Urutan aspek psikososial (guru_karyawan + pimpinan, union)
        $psiko_aspek_order = array(
            'Job Demand', 'Meaning & Engagement', 'Leadership Support',
            'Team Support', 'Psychological Safety', 'Resources & Role Clarity',
            'Leadership Capacity & Workload', 'Leadership Meaning',
            'Organizational support', 'Psychological safety', 'Work life balance',
        );
        $psiko_aspek_count = count($psiko_aspek_order);

        // Lebar kolom dasar
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(30);  // Nama
        $sheet->getColumnDimension('C')->setWidth(14);  // NPP

        // Kolom demografi (mulai dari D)
        $demo_count = count($pertanyaan);
        $demo_start_col = 3; // 0-indexed: 0=A,1=B,2=C, 3=D
        for ($i = 0; $i < $demo_count; $i++) {
            $col_letter = $this->_col($demo_start_col + $i);
            $sheet->getColumnDimension($col_letter)->setWidth(18);
        }

        // Kolom skor: WHO-5, PHQ-9, GAD-7, CBI Personal, CBI Work, CBI Client, CBI Keseluruhan, [psikososial aspek...], Psikososial Total,
        // [6 kolom tambahan: dukungan_pilihan, dukungan_lainnya, t1, t2, t3, kategori_tambahan], Kategori, Krisis
        $skor_start_col = $demo_start_col + $demo_count;
        $skor_count = 7 + $psiko_aspek_count + 1 + 6 + 2; // 7 dasar (WHO5,PHQ9,GAD7, 4 CBI) + N aspek + total + 6 tambahan + 2 (Kategori+Krisis)
        // Lebar tambahan (kolom teks panjang)
        $text_wide_cols = array('dukungan_pilihan', 'dukungan_lainnya', 'tambahan_t2', 'tambahan_t3');
        for ($i = 0; $i < $skor_count; $i++) {
            $col_letter = $this->_col($skor_start_col + $i);
            // Lebar khusus untuk kolom teks panjang (tambahan)
            // Posisi tambahan dalam skor_count: setelah 7 dasar + N aspek + 1 total.
            $extra_idx = $i - (7 + $psiko_aspek_count + 1);
            if ($extra_idx >= 0 && $extra_idx < 6) {
                $extra_keys = array('dukungan_pilihan', 'dukungan_lainnya', 'tambahan_t1', 'tambahan_t2', 'tambahan_t3', 'kategori_tambahan');
                $extra_key = $extra_keys[$extra_idx];
                if (in_array($extra_key, $text_wide_cols, true)) {
                    $sheet->getColumnDimension($col_letter)->setWidth(40);
                } else {
                    $sheet->getColumnDimension($col_letter)->setWidth(20);
                }
            } else {
                $sheet->getColumnDimension($col_letter)->setWidth(14);
            }
        }
        // Kategori + Krisis (2 kolom terakhir dari skor_count)
        $kat_col = $skor_start_col + $skor_count - 2;
        $krisis_col = $kat_col + 1;
        $sheet->getColumnDimension($this->_col($kat_col))->setWidth(30);
        $sheet->getColumnDimension($this->_col($krisis_col))->setWidth(10);

        $last_col_letter = $this->_col($krisis_col);
        $row = 1;

        // === JUDUL ===
        $periode_nama = ($periode_info && !empty($periode_info->nama_periode)) ? $periode_info->nama_periode : '';
        $sheet->setCellValue('A' . $row, 'REKAP SKOR INSTRUMEN MHCU');
        $sheet->mergeCells('A' . $row . ':' . $last_col_letter . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->primary_light);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row++;

        // Info periode + tanggal cetak
        $sheet->setCellValue('A' . $row, 'Periode');
        $sheet->setCellValue('B' . $row, !empty($periode_nama) ? $periode_nama : '-');
        $row++;
        $sheet->setCellValue('A' . $row, 'Tanggal Cetak');
        $sheet->setCellValue('B' . $row, date('d-m-Y H:i'));
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Peserta');
        $sheet->setCellValue('B' . $row, count($data));
        $sheet->getStyle('A2:A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A2:A' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->info_bg);
        $row += 2;

        // === HEADER KOLOM ===
        $header_row = $row;
        $headers = array_merge(
            array('No', 'Nama Lengkap', 'NPP'),
            array_map(function($p) { return $p->demografi_pertanyaan; }, $pertanyaan),
            array('WHO-5', 'PHQ-9', 'GAD-7', 'Personal Burnout', 'Work-Related Burnout', 'Client-Related Burnout', 'CBI Keseluruhan'),
            $psiko_aspek_order,
            array('Psikososial Total'),
            array(
                'Dukungan Dipilih',     // dukungan_pilihan
                'Dukungan Lainnya',     // dukungan_lainnya (teks utk Lainnya)
                'Tantangan Jabatan',    // tambahan_t1 (pim)
                'Tantangan Hidup',      // tambahan_t2 (gk: 6 bln, pim: hidup)
                'Harapan Sekolah',      // tambahan_t3 (gk: dukungan, pim: perbaikan 1 hal)
                'Kategori Tambahan',    // kategori_tambahan (gk saja, keyword-match)
            ),
            array('Kategori', 'Krisis')
        );

        $col_idx = 0;
        foreach ($headers as $h) {
            $sheet->setCellValue($this->_col($col_idx) . $row, $h);
            $col_idx++;
        }

        $sheet->getStyle('A' . $row . ':' . $last_col_letter . $row)->getFont()->setBold(true)->getColor()->setRGB($this->header_text);
        $sheet->getStyle('A' . $row . ':' . $last_col_letter . $row)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($this->header_bg);
        $sheet->getStyle('A' . $row . ':' . $last_col_letter . $row)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('A' . $row . ':' . $last_col_letter . $row)->getBorders()->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
            ->getColor()->setRGB($this->border_color);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        // === DATA ROWS ===
        if (empty($data)) {
            $sheet->setCellValue('A' . $row, 'Tidak ada data sesi selesai pada filter ini.');
            $sheet->mergeCells('A' . $row . ':' . $last_col_letter . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->getColor()->setRGB('A8A29E');
        } else {
            $no = 1;
            foreach ($data as $d) {
                $col_idx = 0;

                // No
                $sheet->setCellValue($this->_col($col_idx) . $row, $no);
                $sheet->getStyle($this->_col($col_idx) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $col_idx++;

                // Nama
                $sheet->setCellValue($this->_col($col_idx) . $row, $d['nama_lengkap']);
                $col_idx++;

                // NPP
                $sheet->setCellValue($this->_col($col_idx) . $row, $d['npp']);
                $col_idx++;

                // Demografi
                foreach ($d['demografi'] as $jawaban) {
                    $sheet->setCellValue($this->_col($col_idx) . $row, !empty($jawaban) ? $jawaban : '');
                    $col_idx++;
                }

                // Skor instrumen dasar: WHO-5, PHQ-9, GAD-7, CBI (Personal, Work, Client, Keseluruhan)
                foreach (array('who5', 'phq9', 'gad7', 'cbi_personal', 'cbi_work', 'cbi_client', 'cbi') as $sk) {
                    $val = isset($d[$sk]) ? $d[$sk] : '';
                    $sheet->setCellValue($this->_col($col_idx) . $row, $val);
                    $sheet->getStyle($this->_col($col_idx) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $col_idx++;
                }

                // Skor psikososial per-aspek
                $aspek_data = isset($d['psikososial_aspek']) ? $d['psikososial_aspek'] : array();
                foreach ($psiko_aspek_order as $aspek_nama) {
                    $val = '';
                    if (isset($aspek_data[$aspek_nama])) {
                        $val = $aspek_data[$aspek_nama]['skor'];
                    }
                    $sheet->setCellValue($this->_col($col_idx) . $row, $val);
                    $sheet->getStyle($this->_col($col_idx) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $col_idx++;
                }

                // Psikososial Total
                $sheet->setCellValue($this->_col($col_idx) . $row, isset($d['psikososial']) ? $d['psikososial'] : '');
                $sheet->getStyle($this->_col($col_idx) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $col_idx++;

                // 6 kolom Instrumen Tambahan (6=gk, 8=pim)
                $tambahan_keys = array('dukungan_pilihan', 'dukungan_lainnya', 'tambahan_t1', 'tambahan_t2', 'tambahan_t3', 'kategori_tambahan');
                foreach ($tambahan_keys as $tk) {
                    $val = isset($d[$tk]) ? $d[$tk] : '';
                    $cell = $this->_col($col_idx) . $row;
                    $sheet->setCellValue($cell, $val);
                    // Teks panjang di-align kiri + wrap text
                    if (in_array($tk, $text_wide_cols, true)) {
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
                    } else {
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $col_idx++;
                }

                // Kategori
                $sheet->setCellValue($this->_col($col_idx) . $row, $d['kategori']);
                $col_idx++;

                // Krisis
                $sheet->setCellValue($this->_col($col_idx) . $row, $d['is_krisis'] ? 'YA' : '-');
                if ($d['is_krisis']) {
                    $sheet->getStyle($this->_col($col_idx) . $row)->getFont()->setBold(true)->getColor()->setRGB('B91C1C');
                }
                $sheet->getStyle($this->_col($col_idx) . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // Border + alternating row
                $sheet->getStyle('A' . $row . ':' . $last_col_letter . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()->setRGB($this->border_color);
                if ($no % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':' . $last_col_letter . $row)->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($this->alt_row);
                }

                // Warna skor: WHO-5, PHQ-9, GAD-7, CBI Aspek (Personal, Work, Client), CBI Keseluruhan (kolom 0-6)
                $warna_instrument = array('who5_warna', 'phq9_warna', 'gad7_warna', 'cbi_personal_warna', 'cbi_work_warna', 'cbi_client_warna', 'cbi_warna');
                for ($warna_idx = 0; $warna_idx < count($warna_instrument); $warna_idx++) {
                    $hex = $this->_get_rekap_color_hex(isset($d[$warna_instrument[$warna_idx]]) ? $d[$warna_instrument[$warna_idx]] : '');
                    if (!empty($hex)) {
                        $cell = $this->_col($skor_start_col + $warna_idx) . $row;
                        $sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($hex);
                    }
                }

                // Warna psikososial per-aspek (kolom 7 sampai 7+N-1)
                $psiko_col_offset = 7; // kolom ke-7 dari skor_start_col
                foreach ($psiko_aspek_order as $aspek_idx => $aspek_nama) {
                    if (isset($aspek_data[$aspek_nama]) && !empty($aspek_data[$aspek_nama]['warna'])) {
                        $hex = $this->_get_rekap_color_hex($aspek_data[$aspek_nama]['warna']);
                        if (!empty($hex)) {
                            $cell = $this->_col($skor_start_col + $psiko_col_offset + $aspek_idx) . $row;
                            $sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()->setRGB($hex);
                        }
                    }
                }

                // Warna psikososial total (kolom setelah semua aspek)
                $psiko_total_col_offset = 7 + $psiko_aspek_count;
                $hex = $this->_get_rekap_color_hex(isset($d['psikososial_warna']) ? $d['psikososial_warna'] : '');
                if (!empty($hex)) {
                    $cell = $this->_col($skor_start_col + $psiko_total_col_offset) . $row;
                    $sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($hex);
                }

                // Warna kategori profil keseluruhan (mapping 9 profil, beda dari band instrument).
                $kategori_hex = $this->_get_profil_color_hex(isset($d['kategori_warna']) ? $d['kategori_warna'] : '');
                if (!empty($kategori_hex)) {
                    $kategori_cell = $this->_col($kat_col) . $row;
                    $sheet->getStyle($kategori_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($kategori_hex);
                }

                $sheet->getRowDimension($row)->setRowHeight(22);

                $row++;
                $no++;
            }
        }

        // Freeze pane di bawah header
        $sheet->freezePane('A' . ($header_row + 1));

        $this->excel->setActiveSheetIndex(0);

        $filename_full = $filename . '_' . date('dmY_Hi') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename_full . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Konversi nama warna band instrumen ke warna pastel Excel.
     * 4 band: hijau/kuning/orange/merah (dari mhcu_band_kategori.warna).
     */
    private function _get_rekap_color_hex($warna)
    {
        $colors = array(
            'hijau'  => 'C6EFCE',
            'kuning' => 'FFEB9C',
            'orange' => 'FCE4D6',
            'merah'  => 'FFC7CE',
        );
        $warna = strtolower(trim((string) $warna));
        return isset($colors[$warna]) ? $colors[$warna] : '';
    }

    /**
     * Konversi nama warna profil ke hex Excel (9 profil, konsisten dg apiapp & template).
     * Sumber: mhcu_profil_kategori.warna.
     */
    private function _get_profil_color_hex($warna)
    {
        $colors = array(
            'hijau_tua'     => '538135',
            'hijau_muda'    => 'A8D08D',
            'hijau_pudar'   => 'E2EFD9',
            'kuning'        => 'FFC000',
            'coklat_orange' => '806000',
            'orange_tua'    => 'C45911',
            'orange_muda'   => 'F4B083',
            'merah_muda'    => 'BD5163',
            'merah_tua'     => 'C00000',
        );
        $warna = strtolower(trim((string) $warna));
        return isset($colors[$warna]) ? $colors[$warna] : '';
    }

    /**
     * Konversi 0-based column index ke huruf Excel (0=A, 25=Z, 26=AA).
     */
    private function _col($index)
    {
        $col = '';
        $index = intval($index);
        while ($index >= 0) {
            $col = chr(65 + ($index % 26)) . $col;
            $index = intdiv($index, 26) - 1;
        }
        return $col;
    }

    /**
     * Build judul sheet dari nama peserta (max 31 char sesuai Excel).
     */
    private function _build_sheet_title($sesi, $idx)
    {
        $nama = isset($sesi->nama_lengkap) ? $sesi->nama_lengkap : 'Sesi ' . ($idx + 1);
        $clean = preg_replace('/[\\\\\/\?\*\[\]:]/', '_', $nama);
        $clean = mb_substr($clean, 0, 28);
        if (mb_strlen($nama) > 28) {
            $clean .= '...';
        }
        return $clean;
    }
}