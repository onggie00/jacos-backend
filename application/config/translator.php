<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * config/translator.php — Semua parameter service translator switch bahasa.
 * Dipakai libraries/Translator.php dan sub-class di libraries/Translator/.
 * Tidak ada kredensial di sini — key dibaca dari .env (DEEPL_API_KEY,
 * GOOGLE_TRANSLATE_API_KEY) lewat loader mini (lihat helper translator).
 */

// ---- KILL SWITCH (Fase 5) ----
// translator_enabled FALSE di SEMUA environment. Aktifkan hanya lokal untuk pilot.
$config['translator_enabled'] = FALSE; // default aman; pilot diaktifkan via config/development/translator.php
// Endpoint pilot: kunci "<role>/<controller>/<HTTP>" (sama dengan kunci whitelist).
// Menambah endpoint baru cukup tambah kunci di sini, tanpa ubah kode.
$config['translator_enabled_endpoints'] = array(); // endpoint pilot diaktifkan per environment
// Header debug X-Translate-* (angka saja) — FALSE di produksi.
$config['translator_debug_headers'] = TRUE; // lokal only
// Perlindungan kuota: string BARU (tidak ketemu kamus/override/cache) per pemanggilan
// melebihi angka ini -> dikembalikan asli, tidak dikirim ke DeepL.
$config['translator_max_uncached_per_request'] = 150;

// ---- sumber kredensial ----
$config['translator_env_file'] = APPPATH . '../.env';

// ---- DeepL ----
$config['translator_deepl_url_free']  = 'https://api-free.deepl.com/v2/translate'; // key ":fx"
$config['translator_deepl_url_pro']   = 'https://api.deepl.com/v2/translate';
$config['translator_deepl_source_lang'] = 'ID';
$config['translator_deepl_connect_timeout'] = 2;   // detik
$config['translator_deepl_total_timeout']   = 3;   // detik per request cURL
$config['translator_time_budget'] = 3.0;           // detik; anggaran total per pemanggilan translate_strings()
$config['translator_batch_max_texts'] = 50;        // maks teks per request DeepL
$config['translator_batch_max_body_bytes'] = 28000; // batas kasar ukuran body per request
$config['translator_retry_on'] = array(429, 500, 502, 503, 504); // status yg boleh retry 1x
$config['translator_max_retries'] = 1;

// ---- lookup bahasa di hook (hanya memilih bahasa tampilan; BUKAN otorisasi) ----
// FALSE: token cukup DIKENAL (ada di DB) walau kedaluwarsa -> tetap dapat
//        bahasa pilihan user (banyak endpoint legacy tidak cek expiry; kalau
//        tidak, terjemahan akan diam-diam mati ke Indonesia).
$config['translator_language_check_expiry'] = FALSE;

// ---- circuit breaker (status di CI cache file) ----
$config['translator_breaker_fail_threshold'] = 3;   // kegagalan beruntun sebelum open
$config['translator_breaker_open_seconds'] = 60;    // durasi open utk timeout/5xx
$config['translator_breaker_open_seconds_quota'] = 3600; // 456 (kuota habis) / 403 (key salah)
$config['translator_breaker_cache_key'] = 'translator_breaker_v1';

// ---- Google fallback (opsional; key dari .env GOOGLE_TRANSLATE_API_KEY) ----
$config['translator_google_url'] = 'https://translation.googleapis.com/language/translate/v2';
$config['translator_google_source'] = 'id';
$config['translator_google_timeout'] = 3;
// translation_cache tidak punya kolom penanda sumber => hasil fallback JANGAN disimpan.
$config['translator_google_store_fallback'] = false;

// ---- filter pengaman ----
$config['translator_filter_digit_run'] = 8;   // deretan digit >= N (NIS/NISN/VA) -> jangan kirim
$config['translator_filter_phone_min_digits'] = 8;
// filter email & no telp & URL aktif utk tipe 'teks' pada SEMUA baris; digit-run
// tidak berlaku utk bersama=true (konten admin); filter non-digit tetap utk teks.
$config['translator_filter_apply_digit_on_bersama'] = false;

// ---- cache translation_cache ----
$config['translator_cache_table'] = 'translation_cache';
$config['translator_cache_short_chars'] = 500; // batas teks pendek; bersama=true selalu di-cache
$config['translator_cache_hit_refresh_hours'] = 24; // last_used_at di-update hanya jika lebih lama dari ini (atau NULL)

// ---- override (opsional; tabel mungkin belum ada) ----
$config['translator_override_table'] = 'translation_override';

// ---- mapping kode bahasa -> folder CI3 language + target Google ----
$config['translator_language_folder'] = array(
    'en-GB' => 'english',
    // 'id' tidak perlu folder: kode id = identitas, service langsung melewati
);
$config['translator_google_target'] = array(
    'en-GB' => 'en',
);

// ---- HTML tag_handling untuk tipe html ----
$config['translator_deepl_tag_handling_html'] = 'html';
