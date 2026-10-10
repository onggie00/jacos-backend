<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 — Halaman Tidak Ditemukan</title>
<style>
	body { font-family: Arial, Helvetica, sans-serif; background:#f4f6f9; margin:0; display:flex; align-items:center; justify-content:center; min-height:100vh; }
	.card { background:#fff; border:1px solid #e3e6f0; border-radius:6px; padding:36px 44px; max-width:480px; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,.06); }
	.code { font-size:64px; font-weight:bold; color:#3FA7E0; margin:0; line-height:1; }
	h1 { font-size:20px; color:#333; margin:12px 0 8px; }
	p { font-size:14px; color:#666; margin:0 0 22px; }
	a.btn { display:inline-block; background:#3FA7E0; color:#fff; text-decoration:none; padding:10px 22px; border-radius:4px; font-size:14px; }
	a.btn:hover { background:#2e8dc4; }
	small { display:block; margin-top:16px; color:#999; font-size:12px; }
</style>
</head>
<body>
<div class="card">
	<p class="code">404</p>
	<h1>Halaman tidak ditemukan</h1>
	<p>Halaman yang Anda tuju tidak tersedia atau telah dipindahkan.</p>
	<a class="btn" href="<?= site_url('administrator'); ?>">Kembali ke Dashboard</a>
	<small><?= date('Y') ?> &copy; Jakarta Cosmopolite Islamic School</small>
</div>
</body>
</html>
