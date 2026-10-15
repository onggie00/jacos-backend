<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Config fitur career (JACOS Careers) — AMAN di-commit, TANPA secret.
 * Secret JWT career: application/config/credentials.php (gitignored),
 * simbol CAREER_JWT_KEY — lihat credentials.example.php.
 */

// CORS allowlist kandidat (origin FE) — Access-Control-Allow-Origin hanya
// dibalas untuk origin ini; origin lain tidak dapat header ACAO.
$config['career_allowed_origins'] = array(
	'https://careers.jacos.id',
	'http://localhost:3000',
);

// Masa berlaku token JWT kandidat (detik) — default 8 jam.
$config['career_jwt_expire'] = 28800;

// Rate limit login (tabel career_login_attempts + kolom candidates).
$config['career_login_max_attempts'] = 5;   // percobaan gagal -> lock
$config['career_login_window']       = 900; // jendela hitung (detik)
$config['career_login_lockout']      = 900; // durasi lock (detik)

// Rate limit register per-IP.
$config['career_register_max_attempts'] = 5;
$config['career_register_rate_window']  = 3600;

// Folder upload privat (relatif FCPATH, TIDAK di webroot docs).
$config['career_upload_dir'] = 'application/storage/career/';

// Nilai maksimum ukuran file (fallback bila persyaratan tidak set).
$config['career_upload_max_kb'] = 5120; // 5 MB
