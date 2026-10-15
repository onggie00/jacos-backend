<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Config fitur career (JACOS Careers) — AMAN di-commit, TANPA secret.
 * Secret JWT career: application/config/credentials.php (gitignored),
 * simbol CAREER_JWT_KEY — lihat credentials.example.php.
 */

// CORS allowlist kandidat (origin FE) — Access-Control-Allow-Origin hanya
// dibalas untuk origin ini; origin lain tidak dapat header ACAO.
// Produksi TANPA localhost (keputusan Fase 4.5 / L2).
$config['career_allowed_origins'] = (ENVIRONMENT === 'production')
	? array('https://careers.jacos.id')
	: array('https://careers.jacos.id', 'http://localhost:3000');

// Masa berlaku token JWT kandidat (detik) — default 8 jam.
$config['career_jwt_expire'] = 28800;

// Rate limit LOGIN (keputusan Fase 4.5, H3/H4 — TANPA lock total akun):
// - per (email+IP): N login gagal dalam jendela sekunder -> 429
// - per-IP lintas email : M login gagal dalam jendela sekunder -> 429
// - password benar dari limit tercapai TETAP BISA login (lock hanya memblokir
//   percobaan gagal; anti-DoS terhadap korban — locked_until tidak dipakai).
$config['career_login_email_max_attempts'] = 5;   // gagal per (email+IP)/jendela
$config['career_login_email_window']       = 900; // jendela per-email (detik)
$config['career_login_ip_max_attempts']    = 20;  // gagal per-IP/jendela
$config['career_login_ip_window']          = 900; // jendela per-IP (detik)

// Rate limit register per-IP (H3: hitung SEMUA percobaan — sukses+gagal+validasi).
$config['career_register_max_attempts'] = 5;
$config['career_register_rate_window']  = 3600;

// Purge career_login_attempts lebih tua dari N hari (query cron — lihat
// docs/PHASES/PHASE4_5_REPORT.md).
$config['career_attempt_purge_days'] = 30;

// Folder upload privat (relatif FCPATH, TIDAK di webroot docs).
$config['career_upload_dir'] = 'application/storage/career/';

// Nilai maksimum ukuran file (fallback bila persyaratan tidak set).
$config['career_upload_max_kb'] = 5120; // 5 MB

// Kuota dokumen (M4, Fase 4.5).
$config['career_doc_max_versions']   = 5;     // versi per persyaratan
$config['career_app_max_total_kb']   = 25600; // total per lamaran (25 MB)
