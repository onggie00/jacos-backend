<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper parsing tanggal dari cell PHPExcel/PhpSpreadsheet menjadi format Y-m-d.
 *
 * Digunakan oleh modul-modul yang mengimpor data siswa dari file Excel, dimana
 * cell berformat Date bisa dibaca oleh PHPExcel sebagai:
 *   - float (Excel serial date, mis. 45292 = 2024-01-15)
 *   - object PHPExcel_DateTime
 *   - string dengan format dd/mm/YYYY, dd-mm-YYYY, YYYY-MM-DD, dll
 *
 * Tanpa parsing yang benar, nilai mentah (float / string tidak valid) akan
 * disimpan ke kolom DATE MySQL dan menjadi 1970-01-01 (epoch) atau 0000-00-00.
 *
 * @param PHPExcel_Cell|string|float|null $cell  Cell PHPExcel atau nilai mentah
 * @param string|null                     $value Nilai mentah (opsional, jika bukan cell)
 * @return string|null Tanggal dalam format Y-m-d, atau null jika kosong/gagal
 */
if (!function_exists('parse_excel_date')) :
    function parse_excel_date($cell = null, $value = null)
    {
        // Tentukan sumber nilai
        if ($cell instanceof PHPExcel_Cell) {
            $rawValue = $cell->getValue();
            $isDateCell = PHPExcel_Shared_Date::isDateTime($cell);
        } else {
            $rawValue = ($value !== null) ? $value : $cell;
            $isDateCell = false;
        }

        if ($rawValue === null || $rawValue === '' || $rawValue === false) return null;

        // 1. PHPExcel DateTime cell → pakai helper bawaan
        if ($isDateCell) {
            $ts = PHPExcel_Shared_Date::ExcelToPHP($rawValue);
            if ($ts > 0) return date('Y-m-d', $ts);
        }

        // 2. Object DateTime (kasus tertentu)
        if (is_object($rawValue) && method_exists($rawValue, 'format')) {
            return $rawValue->format('Y-m-d');
        }

        // 3. Numeric (Excel serial date tanpa deteksi DateTime)
        if (is_numeric($rawValue) && $rawValue > 0 && $rawValue < 100000) {
            $ts = PHPExcel_Shared_Date::ExcelToPHP($rawValue);
            if ($ts > 0) return date('Y-m-d', $ts);
        }

        // 4. String — coba beberapa format umum
        $str = trim((string) $rawValue);
        if ($str === '') return null;

        $formats = array('Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'Y/m/d', 'd M Y', 'd F Y');
        foreach ($formats as $f) {
            $d = DateTime::createFromFormat($f, $str);
            if ($d !== false) {
                $errors = DateTime::getLastErrors();
                if (!$errors || ($errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    return $d->format('Y-m-d');
                }
            }
        }

        // 5. Fallback strtotime
        $ts = strtotime($str);
        return $ts ? date('Y-m-d', $ts) : null;
    }
endif;
