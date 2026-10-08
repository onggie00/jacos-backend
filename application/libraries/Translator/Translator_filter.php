<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Filter pengaman string sebelum dikirim ke penyedia terjemahan.
 * Ambang semua di config/translator.php.
 */
class Translator_filter {

    

    /**
     * TRUE bila string BOLEH dikirim ke penyedia; FALSE bila harus asli.
     *
     * @param string $teks     teks TERNORMALISASI
     * @param string $tipe     teks|label|html
     * @param bool   $bersama
     * @return bool
     */
    public function boleh_kirim($teks, $tipe, $bersama)
    {
        if ($teks === '' || !is_string($teks)) {
            return false;
        }

        $digit_run = (int) translator_cfg('translator_filter_digit_run', 8);
        $phone_min = (int) translator_cfg('translator_filter_phone_min_digits', 8);

        // email: selalu blokir (semua tipe, semua baris) — data pribadi
        if (preg_match('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/', $teks)) {
            return false;
        }

        // URL: blokir utk tipe teks (html boleh — tag/atribut ditangani DeepL)
        if ($tipe !== 'html' && preg_match('#https?://|www\.#i', $teks)) {
            return false;
        }

        // no telepon: blokir utk tipe teks — pola tanda+angka berjarak/tercetak
        if ($tipe !== 'html' && preg_match('/(\+?\d[\d\s\-().]{7,}\d)/', $teks)) {
            $digits = preg_replace('/\D/', '', $teks);
            if (strlen($digits) >= $phone_min) {
                return false;
            }
        }

        // deretan digit >= N (NIS/NISN/VA): tidak berlaku utk bersama=true
        if (!($bersama && !translator_cfg('translator_filter_apply_digit_on_bersama', false))) {
            if (preg_match('/\d{' . $digit_run . ',}/', $teks)) {
                return false;
            }
        }

        return true;
    }

    /**
     * String seluruhnya angka/tanggal/nominal — cepat dilewati tanpa jaringan.
     */
    public function murni_angka_tanggal($teks)
    {
        $t = trim($teks);
        if ($t === '') {
            return true;
        }
        if (preg_match('/^[\d\s.,:\/\-]+$/', $t) && preg_match('/\d/', $t)) {
            return true; // 1.234.567 | 2026-10-07 | 09:30 | 12/05/2026 09:30 | 0
        }
        return false;
    }
}
