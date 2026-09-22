<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 * Helper format currency indonesian
 * 
 * Modul helper tanggal untuk PT. SOLUSI DIGITAL INDUSTRI
 * @link https://codenom.com/
 * @author Codenom Dev
 * @version 1.0
 * 
 * @access public
 */

if (!function_exists('formatIDR')) :
    function formatIDR($str)
    {
        $format = number_format($str,0,"",".");

        return 'Rp ' . $format;
    }
endif;

if (!function_exists('format_nominal')) :
    function format_nominal($str)
    {
        $str = str_replace(".", "", $str);
        $format = number_format($str,0,"",".");

        return $format;
    }
endif;

if (!function_exists('angkaKeHuruf')) :
    function angkaKeHuruf($angka) {
        $angka = (int) abs($angka);
    
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
                   'Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
                   'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
    
        if ($angka === 0)   return 'Nol';
        if ($angka < 20)    return $satuan[$angka];
    
        if ($angka < 100) {
            $puluhan = (int)($angka / 10);
            $sisa    = $angka % 10;
            return $satuan[$puluhan] . ' Puluh' . ($sisa ? ' ' . $satuan[$sisa] : '');
        }
    
        if ($angka < 200) {
            $sisa = $angka % 100;
            return 'Seratus' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        if ($angka < 1000) {
            $ratus = (int)($angka / 100);
            $sisa  = $angka % 100;
            return $satuan[$ratus] . ' Ratus' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        if ($angka < 2000) {
            $sisa = $angka % 1000;
            return 'Seribu' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        if ($angka < 1000000) {
            $ribu = (int)($angka / 1000);
            $sisa = $angka % 1000;
            return angkaKeHuruf($ribu) . ' Ribu' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        if ($angka < 1000000000) {
            $juta = (int)($angka / 1000000);
            $sisa = $angka % 1000000;
            return angkaKeHuruf($juta) . ' Juta' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        if ($angka < 1000000000000) {
            $miliar = (int)($angka / 1000000000);
            $sisa   = $angka % 1000000000;
            return angkaKeHuruf($miliar) . ' Miliar' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        if ($angka < 1000000000000000) {
            $triliun = (int)($angka / 1000000000000);
            $sisa    = $angka % 1000000000000;
            return angkaKeHuruf($triliun) . ' Triliun' . ($sisa ? ' ' . angkaKeHuruf($sisa) : '');
        }
    
        return 'Angka terlalu besar';
    }
endif;
