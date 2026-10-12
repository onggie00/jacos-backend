<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/Setting_bahasa_audit_test — uji created_by/updated_by modul.
 * Dua skenario: (a) kolom ada (deteksi otomatis), (b) kolom dipaksa "tidak ada"
 * (seam audit_cols(array())) — tanpa DDL. CLI only.
 */
class Setting_bahasa_audit_test extends MX_Controller {

    public function __construct() { parent::__construct(); }

    public function index()
    {
        if (!$this->input->is_cli_request()) { show_404(); return; }
        $this->load->model('setting_bahasa/model_setting_bahasa_admin');
        $this->load->helper('translator_safe_db');

        $pass = 0; $fail = 0;
        $t = function($n, $ok, $d = '') use (&$pass, &$fail) {
            echo ($ok ? 'PASS' : 'FAIL') . " | {$n}" . ($d ? " | {$d}" : ''), PHP_EOL;
            $ok ? $pass++ : $fail++;
        };
        $fetch = function($hash) {
            return translator_safe_query(
                "SELECT created_by, updated_by FROM translation_override WHERE hash_sumber = ? AND kode_bahasa = 'en-GB' LIMIT 1",
                array($hash), 'row', null);
        };
        $uid = 73; // user uji admin (uji.translator)

        // deteksi otomatis (kolom ada di DB lokal — user sudah jalankan DDL)
        $cols = $this->model_setting_bahasa_admin->audit_cols();
        $t('deteksi kolom audit (kolom ada)', (count($cols) === 2), implode(',', $cols));

        // (a) DENGAN kolom: simpan -> created_by terisi
        $hash = md5('teks:uji audit fase 7');
        translator_safe_query("DELETE FROM translation_override WHERE hash_sumber = ?", array($hash), 'write');
        $ok = $this->model_setting_bahasa_admin->simpan_override(array(
            'hash_sumber' => $hash, 'kode_bahasa' => 'en-GB',
            'teks_asal' => 'uji audit fase 7', 'teks_hasil' => 'Audit test fase 7',
            'aktif' => 1, 'catatan' => 'tes',
        ), $uid);
        $row = $fetch($hash);
        $t('dengan kolom: created_by terisi', ($ok && $row && (int) $row->created_by === $uid), 'created_by=' . ($row ? $row->created_by : '-'));

        // (a) ubah -> updated_by terisi
        $ok = $this->model_setting_bahasa_admin->ubah_override(
            translator_safe_query("SELECT id FROM translation_override WHERE hash_sumber = ? AND kode_bahasa = 'en-GB' LIMIT 1", array($hash), 'row', null)->id,
            array('kode_bahasa' => 'en-GB', 'teks_asal' => 'uji audit fase 7', 'teks_hasil' => 'Audit EDITED', 'aktif' => 1, 'catatan' => 'tes'),
            $uid);
        $row = $fetch($hash);
        $t('dengan kolom: updated_by terisi', ($ok && $row && (int) $row->updated_by === $uid), 'updated_by=' . ($row ? $row->updated_by : '-'));

        // (b) TANPA kolom (seam): simpan tanpa referensi kolom audit -> tetap jalan
        $this->model_setting_bahasa_admin->audit_cols(array()); // paksa deteksi kosong
        $hash2 = md5('teks:uji audit tanpa kolom');
        translator_safe_query("DELETE FROM translation_override WHERE hash_sumber = ?", array($hash2), 'write');
        $ok = $this->model_setting_bahasa_admin->simpan_override(array(
            'hash_sumber' => $hash2, 'kode_bahasa' => 'en-GB',
            'teks_asal' => 'uji audit tanpa kolom', 'teks_hasil' => 'No audit columns',
            'aktif' => 1, 'catatan' => 'tes',
        ), $uid);
        $row2 = translator_safe_query("SELECT created_by FROM translation_override WHERE hash_sumber = ? AND kode_bahasa = 'en-GB' LIMIT 1", array($hash2), 'row', null);
        $t('tanpa kolom: simpan tetap jalan', ($ok && $row2), '');
        $t('tanpa kolom: created_by NULL', ($row2 && $row2->created_by === null), '');

        // daftar_override dgn deteksi kosong -> SQL tanpa kolom audit (tidak error)
        $rows = $this->model_setting_bahasa_admin->daftar_override(array(), 5, 0);
        $t('tanpa kolom: daftar tetap jalan', is_array($rows), '');

        // bersihkan
        translator_safe_query("DELETE FROM translation_override WHERE hash_sumber IN (?, ?)", array($hash, $hash2), 'write');
        $this->model_setting_bahasa_admin->audit_cols(null); // reset deteksi
        echo "TOTAL: {$pass} PASS, {$fail} FAIL\n";
    }
}
