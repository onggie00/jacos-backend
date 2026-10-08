<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * config/bahasa.php — Daftar bahasa didukung fitur switch bahasa.
 *
 * Semua validasi kode bahasa memakai daftar ini (Setting_bahasa_model).
 * Menambah bahasa baru cukup tambah entri di bawah, tanpa ubah kode.
 *
 * Field:
 *   'kode'   => kode bahasa yang disimpan di setting_bahasa.kode_bahasa
 *   'nama'   => nama tampilan (disimpan ke kolom setting_bahasa.bahasa)
 *   'deepl'  => kode target DeepL (untuk fase translate)
 *   'google' => kode target Google Translate (untuk fase translate)
 *   'default'=> TRUE hanya pada satu entri (default user baru)
 */
$config['bahasa'] = array(
    'id' => array(
        'kode'    => 'id',
        'nama'    => 'Bahasa Indonesia',
        'deepl'   => 'ID',
        'google'  => 'id',
        'default' => TRUE,
    ),
    'en-GB' => array(
        'kode'    => 'en-GB',
        'nama'    => 'English (United Kingdom)',
        'deepl'   => 'EN-GB',
        'google'  => 'en',
        'default' => FALSE,
    ),
);
