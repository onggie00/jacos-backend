<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 * Helper format date indonesian
 * 
 * Modul helper tanggal untuk PT. SOLUSI DIGITAL INDUSTRI
 * @link https://codenom.com/
 * @author Codenom Dev
 * @version 1.0
 * 
 * @access public
 */

if (!function_exists('formatTanggal')) :
    function formatTanggal($waktu)
    {
        $tanggal = date('j', strtotime($waktu));

        $bulan_array = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $bl = date('n', strtotime($waktu));
        $bulan = $bulan_array[$bl];
        $tahun = date('Y', strtotime($waktu));

        return "$tanggal $bulan $tahun";
    }
endif;

if (!function_exists('formatHari')) :
    function formatHari($waktu)
    {
        $hari = date('N', strtotime($waktu));

        $hari_array = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $hari = $hari_array[$hari];

        return "$hari";
    }
endif;

if (!function_exists('formatBulan')) :
    function formatBulan($waktu)
    {

        $bulan_array = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $bl = date('n', strtotime($waktu));
        $bulan = $bulan_array[$bl];

        return "$bulan";
    }
endif;

if (!function_exists('formatTanggalRange')) :
    function formatTanggalRange($date_start, $date_end) {
        if ($date_start == $date_end) {
            // Tanggal sama → tampilkan 1 tanggal saja
            return formatTanggal($date_start);
        }
    
        $start = new DateTime($date_start);
        $end   = new DateTime($date_end);
    
        if ($start->format('m') === $end->format('m') && $start->format('Y') === $end->format('Y')) {
            // Bulan & tahun sama → "14 - 16 Mei 2025"
            return $start->format('d') . ' - ' . formatTanggal($date_end);
        }
    
        // Beda bulan / tahun → "28 April - 02 Mei 2025"
        return formatTanggal($date_start) . ' - ' . formatTanggal($date_end);
    }
endif;
