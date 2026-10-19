<?php
/**
 * Smoke test Fase 6C (smoke6c.php) — revisi kandidat + ganti password
 * + privacy gate audit lama berskor.
 *
 * Cakupan:
 *   A. PUT apicareer/Auth/password            (ganti password kandidat).
 *   B. PUT apicareer/Lamaran_jawaban          (revisi revision_requested).
 *   C. POST apicareer/Lamaran_dokumen/upload  (revisi revision_requested).
 *   D. Modul career_audit: pemegang career_audit_list TANPA
 *      career_pelamar_score_view tidak melihat nilai/rubrik internal
 *      (mask server-side); superadmin tetap melihat.
 *
 * Pemakaian: php tests/api/smoke6c.php [BASE]
 *   BASE default: http://jacos.test:8080
 * Prasyarat: jacos_db LOKAL (fixture dibuat + dihapus sendiri via
 * register_shutdown_function). DILARANG dipakai di produksi. SQL hanya
 * untuk fixture/cleanup — semua perilaku endpoint diuji lewat HTTP.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080';

$H = parse_url($BASE, PHP_URL_HOST);
$HB = ($H === null) ? '' : explode(':', $H)[0];
if ($HB !== 'jacos.test' && $HB !== 'localhost' && $HB !== '127.0.0.1') {
	echo "FAIL  host tidak lokal: {$H}\n"; exit(1);
}

$pass = 0; $fail = 0;
function check($nama, $harap, $terima) {
	global $pass, $fail;
	if ((string) $harap === (string) $terima) {
		$pass++; echo "PASS  $nama\n";
	} else {
		$fail++; echo "FAIL  $nama (harap " . print_r($harap, true) . ", terima " . print_r($terima, true) . ")\n";
	}
}

function http($method, $url, $headers = array(), $body = null, $cookie = null) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	if (count($headers) > 0) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if ($cookie !== null) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	$raw = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($raw === false || $raw === '') return array((string) $code, '', '');
	$pos = strpos($raw, "\r\n\r\n");
	return array((string) $code, $pos === false ? '' : substr($raw, 0, $pos), $pos === false ? $raw : substr($raw, $pos + 4));
}

function http_json($method, $url, $body, $token = null) {
	$headers = array('Content-Type: application/json');
	if ($token !== null) $headers[] = 'Authorization: Bearer ' . $token;
	$send = (strtoupper($method) === 'GET') ? null : json_encode($body);
	list($code, $head, $raw) = http($method, $url, $headers, $send);
	$json = json_decode($raw, true);
	return array($code, is_array($json) ? $json : null, $head, $raw);
}

function upload_dok($url, $fields, $token, $field, $path, $sendname = 'ijazah-revisi.pdf') {
	$post = $fields;
	$post[$field] = new CURLFile($path, 'application/pdf', $sendname);
	$headers = array();
	if ($token !== null) $headers[] = 'Authorization: Bearer ' . $token;
	list($code, $head, $raw) = http('POST', $url, $headers, $post);
	$json = json_decode($raw, true);
	return array($code, is_array($json) ? $json : null, $head);
}

function header_val($head, $name) {
	if (preg_match('/^' . preg_quote($name, '/') . ':\s*(.+)$/mi', $head, $m)) return trim($m[1]);
	return null;
}

function api_url($path) { return $GLOBALS['BASE'] . '/apicareer/' . ltrim($path, '/'); }

$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
// F7: S6C_NOCLEAN=1 hanya melewatkan REGISTER cleanup — seluruh tes
// TETAP jalan (dulu: return di sini = semua tes dilewati = false-green).
$S6C_SKIP_CLEAN = (getenv('S6C_NOCLEAN') === '1');

// F7: hasil query fixture WAJIB dicek — gagal = keluar error, bukan data palsu.
function q($sql) {
	global $pdo;
	$r = $pdo->query($sql);
	if ($r === false) {
		echo "FAIL  SQL gagal: {$pdo->error} :: " . substr($sql, 0, 100) . "\n";
		exit(1);
	}
	return $r;
}
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }
$now_db = $pdo->query('SELECT DATABASE() d')->fetch_assoc()['d'];
if ($now_db !== 'jacos_db') { echo "FAIL  bukan jacos_db\n"; exit(1); }

$STAMP = date('ymdHis') . substr((string) mt_rand(), 0, 4);

/* kumpulan baris fixture utk cleanup terarah (FK-aware) */
$G_DOCS = array();     // id_application_document
$G_REQS = array();     // id_application_requirement
$G_APPS = array();     // id_application
$G_POSTS = array();    // id_job_posting
$G_CANDS = array();    // id_candidate
$G_AUDIT = array();    // id_audit_log
$G_ADMINS = array();   // id admin aauth

register_shutdown_function(function () use ($pdo, $STAMP, &$G_DOCS, &$G_REQS, &$G_APPS, &$G_POSTS, &$G_CANDS, &$G_AUDIT, &$G_ADMINS) {
	// F7: cleanup hanya dilewati bila S6C_NOCLEAN=1 (tes tetap jalan).
	if (!empty($GLOBALS['S6C_SKIP_CLEAN'])) {
		echo "[S6C] S6C_NOCLEAN=1 — cleanup dilewati (sisa pakai STAMP {$STAMP}).\n";
		return;
	}
	// hapus file fisik dokumen fixture
	foreach ($G_DOCS as $id) {
		$r = $pdo->query('SELECT file_path FROM career_application_documents WHERE id_application_document = ' . (int) $id);
		while ($row = $r->fetch_assoc()) {
			@unlink('D:/myprojects/jacos/' . $row['file_path']);
		}
	}
	foreach (glob(__DIR__ . '/tmp_smoke6c_*') as $f) { @unlink($f); }

	$in = function ($arr) { return count($arr) ? implode(',', array_map('intval', $arr)) : '0'; };

	$pdo->query('DELETE FROM career_evaluation_scores WHERE id_application_stage IN (SELECT id_application_stage FROM career_application_stages WHERE id_application IN (' . $in($G_APPS) . '))');
	$pdo->query('DELETE FROM career_selection_schedules WHERE id_application_stage IN (SELECT id_application_stage FROM career_application_stages WHERE id_application IN (' . $in($G_APPS) . '))');
	$pdo->query('DELETE FROM career_notifications WHERE id_candidate IN (' . $in($G_CANDS) . ')');
	$pdo->query('DELETE FROM career_audit_logs WHERE id_audit_log IN (' . $in($G_AUDIT) . ')');
	$pdo->query('DELETE FROM career_application_documents WHERE id_application IN (' . $in($G_APPS) . ')');
	$pdo->query('DELETE FROM career_application_requirements WHERE id_application IN (' . $in($G_APPS) . ')');
	$pdo->query('DELETE FROM career_application_stages WHERE id_application IN (' . $in($G_APPS) . ')');
	$pdo->query('DELETE FROM career_application_profile_snapshots WHERE id_application IN (' . $in($G_APPS) . ')');
	$pdo->query('DELETE FROM career_applications WHERE id_application IN (' . $in($G_APPS) . ')');
	$pdo->query('DELETE FROM career_job_posting_requirements WHERE id_job_posting IN (' . $in($G_POSTS) . ')');
	$pdo->query('DELETE FROM career_job_postings WHERE id_job_posting IN (' . $in($G_POSTS) . ')');
	$pdo->query("DELETE FROM career_login_attempts WHERE email LIKE 'smoke6c-{$STAMP}%'");
	$pdo->query('DELETE FROM career_candidate_profiles WHERE id_candidate IN (' . $in($G_CANDS) . ')');
	$pdo->query('DELETE FROM career_candidates WHERE id_candidate IN (' . $in($G_CANDS) . ')');
	foreach ($G_ADMINS as $aid) {
		$pdo->query('DELETE FROM aauth_perm_to_user WHERE user_id = ' . (int) $aid);
		$pdo->query('DELETE FROM aauth_user_to_group WHERE user_id = ' . (int) $aid);
		$pdo->query('DELETE FROM aauth_users WHERE id = ' . (int) $aid);
	}
	$gn = $pdo->query("SELECT id FROM aauth_groups WHERE name LIKE 'Smoke6C AuditOnly {$STAMP}'")->fetch_assoc();
	if ($gn !== NULL) {
		$pdo->query('DELETE FROM aauth_perm_to_group WHERE group_id = ' . (int) $gn['id']);
		$pdo->query('DELETE FROM aauth_groups WHERE id = ' . (int) $gn['id']);
	}
});

/* ---------------- fixture dasar ---------------- */

$SANDI1 = 'Rahasia6Caa';
$EMAIL1 = 'smoke6c-' . $STAMP . '-a@example.test';
$EMAIL2 = 'smoke6c-' . $STAMP . '-b@example.test';
q("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version, failed_login_count)
             VALUES ('{$EMAIL1}', '" . password_hash($SANDI1, PASSWORD_BCRYPT) . "', 'Kandidat Smoke6C A', 1, 1, 0)");
$CAND1 = (int) q("SELECT id_candidate FROM career_candidates WHERE email = '{$EMAIL1}'")->fetch_assoc()['id_candidate'];
$G_CANDS[] = $CAND1;
q("INSERT INTO career_candidate_profiles (id_candidate, summary) VALUES ({$CAND1}, 'profil smoke6c')");
q("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version, failed_login_count)
             VALUES ('{$EMAIL2}', '" . password_hash($SANDI1, PASSWORD_BCRYPT) . "', 'Kandidat Smoke6C B', 1, 1, 0)");
$CAND2 = (int) q("SELECT id_candidate FROM career_candidates WHERE email = '{$EMAIL2}'")->fetch_assoc()['id_candidate'];
$G_CANDS[] = $CAND2;

$NM = '[DUMMY TEST6C ' . $STAMP . '] Guru Kelas SD Revisi';
q("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('{$NM}', 'post smoke6c', '2026-01-01', '2026-12-31', 'S1', 1, 1)");
$IDP = (int) q("SELECT id_job_posting FROM career_job_postings WHERE title = '{$NM}'")->fetch_assoc()['id_job_posting'];
$G_POSTS[] = $IDP;
q("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Ijazah', 'required', 'document', 'PDF', 5120, 1)");
q("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Alasan Mengajar', 'required', 'text', NULL, NULL, 2)");
q("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Alasan Lain', 'required', 'text', NULL, NULL, 3)");
$REQ_D = (int) q("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 1")->fetch_assoc()['id_job_posting_requirement'];
$REQ_T = (int) q("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 2")->fetch_assoc()['id_job_posting_requirement'];
$REQ_T2 = (int) q("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 3")->fetch_assoc()['id_job_posting_requirement'];

/* dua lamaran submitted (SQL fixture) + 5 tahap */
foreach (array($CAND1, $CAND2) as $cc) {
	q("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
	             VALUES ({$cc}, {$IDP}, 'submitted', 1, NOW())");
	$APPX = (int) q("SELECT id_application FROM career_applications WHERE id_candidate = {$cc} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
	$G_APPS[] = $APPX;
	$snap = json_encode(array('items' => array(), 'profile' => array(), 'candidate' => array('full_name' => 'Kandidat Smoke6C', 'email' => 'x@example.test')));
	q("INSERT INTO career_application_profile_snapshots (id_application, profile_json, snapshot_at)
	             VALUES ({$APPX}, '" . $pdo->real_escape_string($snap) . "', NOW())");
	$q = q('SELECT id_stage FROM career_stages WHERE is_active = 1 ORDER BY stage_order ASC LIMIT 5');
	while ($stg = $q->fetch_assoc()) {
		q("INSERT INTO career_application_stages (id_application, id_stage, status) VALUES ({$APPX}, " . (int) $stg['id_stage'] . ", 'pending')");
	}
}
$APP1 = (int) q("SELECT id_application FROM career_applications WHERE id_candidate = {$CAND1} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
$APP2 = (int) q("SELECT id_application FROM career_applications WHERE id_candidate = {$CAND2} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];

/* requirement milik lamaran: A revisi utk teks+berkas; B direvisi (IDOR target) */
q("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP1}, {$REQ_T}, 'revision_requested', 'Jawaban lama revisi')");
$AR_T = (int) q("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP1} AND id_job_posting_requirement = {$REQ_T}")->fetch_assoc()['id_application_requirement'];
$G_REQS[] = $AR_T;
q("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP1}, {$REQ_T2}, 'pending_review', 'Jawaban non-revisi')");
$AR_T2 = (int) q("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP1} AND id_job_posting_requirement = {$REQ_T2}")->fetch_assoc()['id_application_requirement'];
$G_REQS[] = $AR_T2;
q("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP1}, {$REQ_D}, 'revision_requested', NULL)");
$AR_D = (int) q("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP1} AND id_job_posting_requirement = {$REQ_D}")->fetch_assoc()['id_application_requirement'];
$G_REQS[] = $AR_D;
q("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP2}, {$REQ_T}, 'revision_requested', NULL)");
$AR_T_STYLE = (int) q("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP2} AND id_job_posting_requirement = {$REQ_T}")->fetch_assoc()['id_application_requirement'];
$G_REQS[] = $AR_T_STYLE;

/* login kandidat A (HTTP nyata) */
list($c, $j, , ) = http_json('POST', api_url('Auth/login'), array('email' => $EMAIL1, 'password' => $SANDI1));
check('A login kandidat 200', '200', $c);
$TOKEN1 = (isset($j['data']['token']) && is_string($j['data']['token'])) ? $j['data']['token'] : null;
check('A token login ada', 'ya', $TOKEN1 ? 'ya' : 'bukan');

/* ---------------- SEC 1: PUT /Auth/password ---------------- */

list($c, , , ) = http_json('PUT', api_url('Auth/password'), array('password_lama' => $SANDI1, 'password_baru' => 'PasswordBaru9x'));
check('password TANPA auth 401', '401', $c);

list($c, $j, , ) = http_json('PUT', api_url('Auth/password'), array('password_lama' => 'SALAHBETULnot', 'password_baru' => 'PasswordBaru9x'), $TOKEN1);
// F3: sandi lama salah = 422 (bukan 401), token TETAP valid.
check('password lama salah 422', '422', $c);
check('password lama salah pesan tepat', 'ya', (isset($j['message']) && strpos($j['message'], 'Password lama salah') !== false) ? 'ya' : 'bukan');

list($c, , , ) = http_json('PUT', api_url('Auth/password'), array('password_baru' => 'PasswordBaru9x'), $TOKEN1);
check('password lama kosong 400', '400', $c);

list($c, , , ) = http_json('PUT', api_url('Auth/password'), array('password_lama' => $SANDI1, 'password_baru' => 'pendek'), $TOKEN1);
check('password baru <8 422', '422', $c);
list($c, , , ) = http_json('PUT', api_url('Auth/password'), array('password_lama' => $SANDI1, 'password_baru' => str_repeat('x', 73)), $TOKEN1);
check('password baru >72 422', '422', $c);

// F8: sandi baru sama dgn lama ditolak 422; header no-store tetap.
list($c, , $head_422, ) = http_json('PUT', api_url('Auth/password'), array('password_lama' => $SANDI1, 'password_baru' => $SANDI1), $TOKEN1);
check('password baru==lama 422', '422', $c);
check('password 422 no-store', 'ya', (strpos(strtolower((string) header_val($head_422, 'Cache-Control')), 'no-store') !== false) ? 'ya' : 'bukan');

// F3: pasca lama-salah, token LAMA tetap valid (sesi tidak hangus).
list($c_ok, , , ) = http_json('GET', api_url('Auth/me'), null, $TOKEN1);
check('password lama salah: token lama TETAP valid', '200', $c_ok);

list($c, $j, $head, ) = http_json('PUT', api_url('Auth/password'), array('password_lama' => $SANDI1, 'password_baru' => 'BaruBener99x'), $TOKEN1);
check('password benar 200', '200', $c);
$cc_hdr = strtolower((string) header_val($head, 'Cache-Control'));
check('password no-store', 'no-store', strpos($cc_hdr, 'no-store') !== false ? 'no-store' : $cc_hdr);
$pr_hdr = strtolower((string) header_val($head, 'Pragma'));
check('password pragma no-cache', 'no-cache', strpos($pr_hdr, 'no-cache') !== false ? 'no-cache' : $pr_hdr);
check('password respons TIDAK berisi sandi', 'ya', (isset($j) && strpos(json_encode($j), 'BaruBener99x') === false) ? 'ya' : 'bukan');

$row = q("SELECT token_version FROM career_candidates WHERE id_candidate = {$CAND1}")->fetch_assoc();
check('token_version naik tepat satu', '2', (string) $row['token_version']);

list($c, , , ) = http_json('GET', api_url('Auth/me'), null, $TOKEN1);
check('token LAMA mati 401 pasca-ganti', '401', $c);

list($c, , , ) = http_json('POST', api_url('Auth/login'), array('email' => $EMAIL1, 'password' => $SANDI1));
check('login sandi LAMA 401', '401', $c);

list($c, $j, , ) = http_json('POST', api_url('Auth/login'), array('email' => $EMAIL1, 'password' => 'BaruBener99x'));
check('login sandi BARU 200', '200', $c);
$TOKEN1B = (isset($j['data']['token']) && is_string($j['data']['token'])) ? $j['data']['token'] : null;
check('token BARU didapat', 'ya', $TOKEN1B ? 'ya' : 'bukan');
list($c, , , ) = http_json('GET', api_url('Auth/me'), null, $TOKEN1B);
check('token BARU valid (me 200)', '200', $c);
/* ---------------- SEC 2: revisi jawaban ---------------- */

list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'x'), null);
check('jawaban revisi TANPA auth 401', '401', $c);

// IDOR: requirement milik lamaran kandidat lain → 404
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T_STYLE, 'answer' => 'smuggling'), $TOKEN1B);
check('jawaban revisi IDOR 404', '404', $c);

list($c, $j, , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T2, 'answer' => 'coba ubah'), $TOKEN1B);
check('jawaban requirement NON-revisi (submitted) 409', '409', $c);

list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'a' . "\xF0\x9F\x98\x80" . 'b'), $TOKEN1B);
check('jawaban revisi emoji 422', '422', $c);

list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => str_repeat('x', 2001)), $TOKEN1B);
check('jawaban revisi >2000 422', '422', $c);

list($c, $j, , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'Alasan revisi baru, lebih jelas.'), $TOKEN1B);
check('jawaban revisi diterima 200', '200', $c);

$row = q("SELECT status, answer_text, reviewed_by FROM career_application_requirements WHERE id_application_requirement = {$AR_T}")->fetch_assoc();
check('jawaban revisi: answer_text terbaru', 'Alasan revisi baru, lebih jelas.', $row['answer_text']);
check('jawaban revisi: status pending_review', 'pending_review', $row['status']);
check('jawaban revisi: reviewed_by dibersihkan', 'ya', $row['reviewed_by'] === NULL ? 'ya' : 'bukan');

// pasca pending_review, ubah lagi ditolak (harus minta revisi dulu)
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'ubah lagi'), $TOKEN1B);
check('jawaban ulang tanpa revisi baru 409', '409', $c);

/* ---------------- SEC 3: revisi unggah dokumen ---------------- */

$f1 = __DIR__ . '/tmp_smoke6c_' . $STAMP . '_v1.pdf';
file_put_contents($f1, "%PDF-1.4\n% smoke6c dummy v1\n");
$f2 = __DIR__ . '/tmp_smoke6c_' . $STAMP . '_v2.pdf';
file_put_contents($f2, "%PDF-1.4\n% smoke6c dummy v2\n");

list($c, , , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), null, 'document', $f1);
check('upload revisi TANPA auth 401', '401', $c);

list($c, $j, , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_T_STYLE), $TOKEN1B, 'document', $f1);
check('upload revisi IDOR 404', '404', $c);

// requirement non-berkas ditolak
list($c, , , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_T), $TOKEN1B, 'document', $f1);
check('upload persyaratan non-berkas 422', '422', $c);

list($c, $j, , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $f1);
check('upload revisi (PDF sah) 201', '201', $c);
$DOC_ID = (int) ($j['data']['id_application_document'] ?? 0);
$G_DOCS[] = $DOC_ID;
check('upload revisi: version 1', '1', (string) ($j['data']['version'] ?? 'x'));

$docrow = q("SELECT ar.status, ar.reviewed_by FROM career_application_documents d JOIN career_application_requirements ar ON ar.id_application_requirement = d.id_application_requirement WHERE d.id_application_document = {$DOC_ID}")->fetch_assoc();
check('upload revisi: status pending_review', 'pending_review', $docrow['status']);
check('upload revisi: reviewed_by dibersihkan', 'ya', $docrow['reviewed_by'] === NULL ? 'ya' : 'bukan');

list($c, $j, , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $f1);
// Pasca-unggah pertama status requirement sudah pending_review — unggah
// ulang TANPA revisi baru ditolak (tidak ada duplikat versi).
check('upload ulang tanpa revisi baru 409', '409', $c);
$row = q("SELECT COUNT(*) n FROM career_application_documents WHERE id_application_requirement = {$AR_D}")->fetch_assoc();
check('upload ulang: TIDAK buat versi baru', '1', (string) $row['n']);

// Revisi kedua: kembalikan requirement ke revision_requested via SQL (fixture), lalu unggah.
q("UPDATE career_job_posting_requirements SET input_type='document' WHERE id_job_posting_requirement = (SELECT id_job_posting_requirement FROM career_application_requirements WHERE id_application_requirement = {$AR_D})");
q("UPDATE career_application_requirements SET status='revision_requested', review_note='perbaiki naskah' WHERE id_application_requirement = {$AR_D}");
list($c, $j, , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $f2);
check('upload revisi kedua 201', '201', $c);
check('upload revisi kedua: version 2', '2', (string) ($j['data']['version'] ?? 'x'));
$DOC_ID2 = (int) ($j['data']['id_application_document'] ?? 0);
$G_DOCS[] = $DOC_ID2;

// MIME fail-closed tetap berlaku utk revisi: PDF ber-ekstensi .jpg → 422
$fjpg = __DIR__ . '/tmp_smoke6c_' . $STAMP . '_fake.jpg';
copy($f1, $fjpg);
list($c, $j, , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $fjpg, 'palsu.jpg');
check('upload revisi PDF-dinama-.jpg 422', '422', $c);

// ukuran melebihi max_file_size_kb requirement (5120 KB)
$fbig = __DIR__ . '/tmp_smoke6c_' . $STAMP . '_big.pdf';
$fh = fopen($fbig, 'wb'); fseek($fh, 5 * 1024 * 1024 + 300); fwrite($fh, 'x'); fclose($fh);
list($c, , , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $fbig);
check('upload revisi oversize 422', '422', $c);

// unduh versi terbaru (owner) — streaming tetap jalan pasca-revisi
list($c, , $hd2, $rawbody) = http_json('GET', api_url('Lamaran_dokumen/detail/' . $DOC_ID2), null, $TOKEN1B);
if (getenv('S6C_TRACE') === '1') {
	echo "[TRACE] SEC3 DOC_ID={$DOC_ID} DOC_ID2={$DOC_ID2} AR_D={$AR_D} unduh_ct=" . header_val($hd2, 'Content-Type') . " body_head=" . substr((string) $rawbody, 0, 10) . "\n";
}
check('unduh dokumen revisi 200', '200', $c);
check('unduh dokumen revisi: body PDF', 'ya', (strpos((string) $rawbody, '%PDF-') === 0) ? 'ya' : 'bukan');
$ctk = strtolower((string) header_val($hd2, 'Content-Type'));
check('unduh dokumen revisi: Content-Type berisi pdf', 'ya', (strpos($ctk, 'pdf') !== false) ? 'ya' : 'bukan');

list($c, , , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_T), $TOKEN1B, 'document', $f2);
check('upload ke requirement teks 422 (bukan berkas)', '422', $c);

/* ---------------- SEC 4: career_audit mask (D13) ---------------- */

$AEMAIL = 'smoke6c-' . $STAMP . '-aud@example.test';
q("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret) VALUES ('{$AEMAIL}', '" . password_hash('Admin6cba12', PASSWORD_BCRYPT) . "', 'smoke6c_aud_{$STAMP}', 'Admin Audit Smoke6C', 0, '', '')");
$AID = (int) $pdo->insert_id;
$G_ADMINS[] = $AID;
/* HATI-HATI: grup 'Admin' MEMEGANG career_pelamar_score_view (SQL 005) —
   admin uji TANPA skor harus grup sendiri; perm hanya user/group fixture. */
q("INSERT INTO aauth_groups (name, definition) VALUES ('Smoke6C AuditOnly {$STAMP}', 'fixture')");
$GID_A = (int) $pdo->insert_id;
$pd2 = q("SELECT id FROM aauth_perms WHERE name = 'career_audit_list'")->fetch_assoc();
if ($pd2 !== NULL) {
	q("INSERT INTO aauth_perm_to_group (perm_id, group_id) VALUES (" . (int) $pd2['id'] . ", {$GID_A})");
}
q("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$AID}, {$GID_A})");

$SEMAIL = 'smoke6c-' . $STAMP . '-super@example.test';
q("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret) VALUES ('{$SEMAIL}', '" . password_hash('Super6cba12', PASSWORD_BCRYPT) . "', 'smoke6c_sup_{$STAMP}', 'Admin Super Smoke6C', 0, '', '')");
$SID = (int) $pdo->insert_id;
$G_ADMINS[] = $SID;
/* grup 'Admin' = admin_group AAuth → is_admin() TRUE (AAuth.php:1459/88). */
q("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$SID}, 1)");

/* baris audit sintetis LAMA berskor (fixture) — objek = tahap milik APP1 */
$STAGE1A = (int) q("SELECT id_application_stage FROM career_application_stages WHERE id_application = {$APP1} ORDER BY id_application_stage ASC LIMIT 1")->fetch_assoc()['id_application_stage'];
$detail = json_encode(array('score' => 75.00, 'skor' => '75', 'rubric_note' => 'psikotes menyusul', 'stage_order' => 1));
q("INSERT INTO career_audit_logs (actor_type, actor_id, object_type, object_id, action, detail_json, ip_address, created_at)
             VALUES ('admin', 1, 'career_application_stage', {$STAGE1A}, 'simpan_nilai_tahap', '" . $pdo->real_escape_string($detail) . "', '127.0.0.1', NOW())");
$AUID = (int) $pdo->insert_id;
$G_AUDIT[] = $AUID;
check('fixture audit skor terpasang', 'ya', $AUID > 0 ? 'ya' : 'bukan');

/* izin admin AUD: hanya career_audit_list (user-layer perm fixture) */
$pd = q("SELECT id FROM aauth_perms WHERE name = 'career_audit_list'")->fetch_assoc();
if ($pd !== NULL) {
	q('INSERT INTO aauth_perm_to_user (user_id, perm_id) VALUES (' . $AID . ', ' . (int) $pd['id'] . ')');
}

function admin_login_session($email, $pass) {
	list($c, $head, ) = http('POST', $GLOBALS['BASE'] . '/administrator/login', array('Content-Type: application/x-www-form-urlencoded'), http_build_query(array('username' => $email, 'password' => $pass)), null);
	$mm = array();
	preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $head, $mm);
	foreach ($mm[1] as $i => $nm) {
		if (trim(strtolower($nm)) === 'jacos_session') return rawurldecode($mm[2][$i]);
	}
	return null;
}
function admin_get($path, $cookie) {
	return http('GET', $GLOBALS['BASE'] . $path, array(), null, $cookie !== null ? 'jacos_session=' . rawurlencode($cookie) : null);
}

$cookie_aud = admin_login_session($AEMAIL, 'Admin6cba12');
$cookie_sup = admin_login_session($SEMAIL, 'Super6cba12');
check('admin audit login', 'ya', $cookie_aud !== null ? 'ya' : 'bukan');
check('admin super login', 'ya', $cookie_sup !== null ? 'ya' : 'bukan');

$qs = http_build_query(array('action' => 'simpan_nilai_tahap', 'object_id' => $STAGE1A));
list($c, , $rawaudA) = admin_get('/administrator/career_audit/index/0?' . $qs, $cookie_aud);
if (getenv('S6C_TRACE') === '1') {
	echo "[TRACE] audit_a len=" , strlen((string) $rawaudA), " mask_pos=" , (int) strpos((string) $rawaudA, 'memerlukan izin'), " score_pos=" , (int) strpos((string) $rawaudA, 'psikotes menyusul'), " head_snip=" , substr((string) preg_replace('/\s+/', ' ', strip_tags((string) $rawaudA)), 0, 400), "\n";
}
check('halaman career_audit (admin tanpa skor) 200', '200', $c);
check('audit tanpa score_view: nilai/rubrik TERSEMBUNYI', 'ya', (strpos($rawaudA, 'psikotes menyusul') === false AND strpos($rawaudA, '"skor"') === false) ? 'ya' : 'bukan');
check('audit tanpa score_view: mask terlihat', 'ya', (strpos($rawaudA, 'memerlukan izin career_pelamar_score_view') !== false) ? 'ya' : 'bukan');
check('audit tanpa score_view: metadata tetap', 'ya', (strpos($rawaudA, 'career_application_stage') !== false) ? 'ya' : 'bukan');

list($c, , $rawaudS) = admin_get('/administrator/career_audit/index/0?' . $qs, $cookie_sup);
check('career_audit superadmin 200', '200', $c);
check('audit superadmin: nilai tampil', 'ya', (strpos($rawaudS, 'psikotes menyusul') !== false) ? 'ya' : 'bukan');

/* ---------------- SEC 5: M1 negatif wildcard LIKE (kandidat filter) ---------------- */
/* M1 6B.3: like() dari Query Builder harus meng-escape %, _, ! —
   wildcard HARUS dicocokkan HARFIAH, bukan sebagai karakter wildcard. */
$NAMA_LIT = 'KandidatLit A_%x_' . $STAMP;
$NAMA_WILD = 'KandidatWild Aaaxx_' . $STAMP;
q("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
             VALUES ('smoke6c-{$STAMP}-lit@example.test', '" . password_hash('Sandi6Ccc', PASSWORD_BCRYPT) . "', '" . $pdo->real_escape_string($NAMA_LIT) . "', 1, 1)");
$LIT_ID = (int) $pdo->insert_id;
$G_CANDS[] = $LIT_ID;
q("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
             VALUES ('smoke6c-{$STAMP}-wild@example.test', '" . password_hash('Sandi6Ccc', PASSWORD_BCRYPT) . "', '" . $pdo->real_escape_string($NAMA_WILD) . "', 1, 1)");
$WILD_ID = (int) $pdo->insert_id;
$G_CANDS[] = $WILD_ID;

$LEMAIL = 'smoke6c-' . $STAMP . '-list@example.test';
q("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret) VALUES ('{$LEMAIL}', '" . password_hash('List6cdd11', PASSWORD_BCRYPT) . "', 'smoke6c_list_{$STAMP}', 'Admin Kandidat 6C', 0, '', '')");
$LID = (int) $pdo->insert_id;
$G_ADMINS[] = $LID;
q("INSERT INTO aauth_groups (name, definition) VALUES ('Smoke6C Kandidat {$STAMP}', 'fixture')");
$GID_L = (int) $pdo->insert_id;
$pl2 = q("SELECT id FROM aauth_perms WHERE name = 'career_kandidat_list'")->fetch_assoc();
if ($pl2 !== NULL) {
	q("INSERT INTO aauth_perm_to_group (perm_id, group_id) VALUES (" . (int) $pl2['id'] . ", {$GID_L})");
}
q("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$LID}, {$GID_L})");
$cookie_list = admin_login_session($LEMAIL, 'List6cdd11');
check('admin kandidat login', 'ya', $cookie_list !== null ? 'ya' : 'bukan');

list($c, , $rawq) = admin_get('/administrator/career_kandidat?q=' . rawurlencode($NAMA_LIT), $cookie_list);
$htmlq = preg_replace('/\s+/', ' ', strip_tags((string) $rawq));
check('halaman filter kandidat 200', '200', $c);
check('LIKE literal: nama literal ditemukan', 'ya', (strpos($htmlq, $NAMA_LIT) !== false) ? 'ya' : 'bukan');
check('LIKE literal: wildcard TIDAK ekspansi — nama lain TIDAK ikut', 'ya',
	(strpos($htmlq, $NAMA_WILD) === false) ? 'ya' : 'bukan');

list($c, , $rawq2) = admin_get('/administrator/career_kandidat?q=' . rawurlencode($NAMA_WILD), $cookie_list);
$htmlq2 = preg_replace('/\s+/', ' ', strip_tags((string) $rawq2));
check('LIKE wildcard: nama lain dicocokkan literal', 'ya', (strpos($htmlq2, $NAMA_WILD) !== false) ? 'ya' : 'bukan');


/* ------------- SEC 6: F5b/F5c jadwal rollback disiplin (admin) ------------- */


function jadwal_post($fields, $cookie, $csrf) {
	$post = array_merge($fields, array('career_csrf' => $csrf));
	list($c, $head, $raw) = http('POST', $GLOBALS['BASE'] . '/administrator/career_pelamar/jadwal', array('Content-Type: application/x-www-form-urlencoded', 'X-Requested-With: XMLHttpRequest'), http_build_query($post), $cookie !== null ? 'jacos_session=' . rawurlencode($cookie) : null);
	$j = json_decode($raw, true);
	return array((string) $c, is_array($j) ? $j : null, $raw);
}
/* admin dengan izin career_pelamar_schedule */
$JEMAIL = 'smoke6c-' . $STAMP . '-jad@example.test';
q("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret) VALUES ('{$JEMAIL}', '" . password_hash('Jadwal6ca11', PASSWORD_BCRYPT) . "', 'smoke6c_jad_{$STAMP}', 'Admin Jadwal 6C', 0, '', '')");
$JID = (int) $pdo->insert_id;
$G_ADMINS[] = $JID;
q("INSERT INTO aauth_groups (name, definition) VALUES ('Smoke6C Jadwal {$STAMP}', 'fixture')");
$GID_J = (int) $pdo->insert_id;
/* admin jadwal = career_pelamar_schedule + view (halaman view untuk
   CSRF token PER-SESI: token dari sesi yang SAMA dengan yang POST). */
foreach (array('career_pelamar_schedule', 'career_pelamar_view', 'career_pelamar_list') as $npm) {
	$plj = q("SELECT id FROM aauth_perms WHERE name = '{$npm}'")->fetch_assoc();
	if ($plj !== NULL) {
		q("INSERT INTO aauth_perm_to_group (perm_id, group_id) VALUES (" . (int) $plj['id'] . ", {$GID_J})");
	}
}
q("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$JID}, {$GID_J})");
$cookie_jad = admin_login_session($JEMAIL, 'Jadwal6ca11');
check('admin jadwal login', 'ya', $cookie_jad !== null ? 'ya' : 'bukan');

/* ---------- CSRF token PER-SESI dari halaman view pelamar ---------- */

/* tahap-1 APP2 (submitted kandidat B) */
$ST2 = (int) q("SELECT id_application_stage FROM career_application_stages WHERE id_application = {$APP2} ORDER BY id_application_stage ASC LIMIT 1")->fetch_assoc()['id_application_stage'];

list($c, , $rawpage) = admin_get('/administrator/career_pelamar/view/' . $APP1, $cookie_jad);
check('view pelamar dilihat admin jadwal', '200', $c);

/* tahap-1 APP2 (submitted kandidat B) */
$ST2 = (int) q("SELECT id_application_stage FROM career_application_stages WHERE id_application = {$APP2} ORDER BY id_application_stage ASC LIMIT 1")->fetch_assoc()['id_application_stage'];

$CSRF_J = null;
if (preg_match('/name="career_csrf"[^>]*value="([^"]+)"/', $rawpage, $mjr) OR preg_match('/value="([^"]+)"\s*name="career_csrf"/', $rawpage, $mjr)) {
	$CSRF_J = $mjr[1];
}
check('csrf token admin jadwal terbaca', 'ya', $CSRF_J !== null ? 'ya' : 'bukan');

/* F5b: set tahap 'scheduled' + baris jadwal is_published=0 (state
   inkonsisten — modifikasi direct SQL utk pengujian, bukan API). */
q("UPDATE career_application_stages SET status='scheduled', scheduled_at='2026-12-01 09:00:00' WHERE id_application_stage = {$ST2}");
q("INSERT INTO career_selection_schedules (id_application_stage, schedule_datetime, channel, note, is_published, created_by)
             VALUES ({$ST2}, '2026-12-01 09:00:00', 'Zoom [DUMMY TEST6C]', 'fixture', 0, {$JID})");
list($c, , ) = jadwal_post(array('id_application_stage' => $ST2, 'aksi' => 'batal'), $cookie_jad, $CSRF_J);
check('F5b jadwal batal state inkonsisten 409', '409', $c);
$rb = q("SELECT status, scheduled_at FROM career_application_stages WHERE id_application_stage = {$ST2}")->fetch_assoc();
check('F5b: tahap tetap scheduled', 'scheduled', (string) $rb['status']);
check('F5b: baris jadwal tetap (tidak terhapus, tetap unpublished)', '0', (string) q("SELECT is_published FROM career_selection_schedules WHERE id_application_stage = {$ST2}")->fetch_assoc()['is_published']);

/* F5c: simpan yang GAGAL 409 tidak meninggalkan in_process (autokoreksi
   ikut ter-rollback; status kembali/submitted tetap). */
q("UPDATE career_selection_schedules SET is_published=1 WHERE id_application_stage = {$ST2}");
list($c, $jxs) = jadwal_post(array('id_application_stage' => $ST2, 'aksi' => 'simpan', 'schedule_datetime' => '2026-12-05 10:00', 'channel' => 'Zoom', 'note' => 'x'), $cookie_jad, $CSRF_J);
check('F5c: simpan direjeksi jadwal terpublikasi 409', '409', $c);
$sc = (string) q("SELECT status FROM career_applications WHERE id_application = {$APP2}")->fetch_assoc()['status'];
check('F5c simpan-gagal tidak merobah status (submitted tetap)', 'submitted', $sc);

/* F5c lanjutan: simpan sah → app naik in_process */
q("DELETE FROM career_selection_schedules WHERE id_application_stage = {$ST2}");
q("UPDATE career_application_stages SET status='pending', scheduled_at=NULL WHERE id_application_stage = {$ST2}");
list($c, $jxs2) = jadwal_post(array('id_application_stage' => $ST2, 'aksi' => 'simpan', 'schedule_datetime' => '2026-12-05 10:00', 'channel' => 'Zoom', 'note' => 'sah'), $cookie_jad, $CSRF_J);
check('F5c jadwal simpan sah 200', '200', $c);
check('F5c: lamaran naik in_process', 'in_process', (string) q("SELECT status FROM career_applications WHERE id_application = {$APP2}")->fetch_assoc()['status']);

/* aksi salah nama → 422, tanpa mengubah status */
$sc_before = (string) q("SELECT status FROM career_applications WHERE id_application = {$APP2}")->fetch_assoc()['status'];
list($c, , ) = jadwal_post(array('id_application_stage' => $ST2, 'aksi' => 'pecah'), $cookie_jad, $CSRF_J);
check('F5c aksi pecah ditolak 422', '422', $c);
check('F5c aksi pecah: status tetap', $sc_before, (string) q("SELECT status FROM career_applications WHERE id_application = {$APP2}")->fetch_assoc()['status']);

/* ------------- SEC 7: F5d resubmit & F4-F2 rebalans (kandidat A) ------------- */

/* draft aplikasi utk kandidat A (posting kedua baru, tidak bentrok UNIQUE) */
q("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_at)
             VALUES ('[DUMMY TEST6C] Rebut Judul {$STAMP}', 'x', '2026-01-01', '2030-12-31', 'SD', 1, NOW())");
$IDP_F = (int) $pdo->insert_id;
$G_POSTS[] = $IDP_F;
q("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP_F}, 'Alasan reimajinasi', 'required', 'text', NULL, NULL, 1)");
$REQ_F = (int) q("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP_F} AND display_order = 1")->fetch_assoc()['id_job_posting_requirement'];
q("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$CAND1}, {$IDP_F}, 'draft', 1, NOW())");
$APP_F = (int) q("SELECT id_application FROM career_applications WHERE id_candidate = {$CAND1} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
$G_APPS[] = $APP_F;
q("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status)
             VALUES ({$APP_F}, {$REQ_F}, 'not_uploaded')");
$AR_F = (int) q("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP_F}")->fetch_assoc()['id_application_requirement'];
$G_REQS[] = $AR_F;

list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_F, 'answer' => 'isi draft'), $TOKEN1B);
check('F5d jawaban requirement draft-lama 200', '200', $c);

/* verified & rejected → 409 (bukan auth/401) */
q("UPDATE career_application_requirements SET status='verified' WHERE id_application_requirement = {$AR_T2}");
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T2, 'answer' => 'ubah'), $TOKEN1B);
check('F5d jawaban saat verified 409', '409', $c);
q("UPDATE career_application_requirements SET status='rejected' WHERE id_application_requirement = {$AR_T2}");
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T2, 'answer' => 'ubah'), $TOKEN1B);
check('F5d jawaban saat rejected 409', '409', $c);

/* app accepted & withdrawn → 409; pasca itu APP1 dipulihkan submitted */
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'ubah'), $TOKEN1B);
check('F5d baseline AR_T masih revision_requested 409 (B sudah final? A belum revisi)', '409', $c);
q("UPDATE career_applications SET status='accepted' WHERE id_application = {$APP1}");
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'ubah'), $TOKEN1B);
check('F5d jawaban saat app accepted 409', '409', $c);
q("UPDATE career_applications SET status='withdrawn' WHERE id_application = {$APP1}");
list($c, , , ) = http_json('PUT', api_url('Lamaran_jawaban'), array('id_application_requirement' => $AR_T, 'answer' => 'ubah'), $TOKEN1B);
check('F5d jawaban saat app withdrawn 409', '409', $c);
q("UPDATE career_applications SET status='submitted' WHERE id_application = {$APP1}");
q("UPDATE career_application_requirements SET status='revision_requested' WHERE id_application_requirement = {$AR_T}");

/* F4a: duplikat checksum saat revisi → 422 + NOL versi baru */
q("UPDATE career_application_requirements SET status='revision_requested' WHERE id_application_requirement = {$AR_D}");
$ndoc1 = (int) q("SELECT COUNT(*) n FROM career_application_documents WHERE id_application_requirement = {$AR_D}")->fetch_assoc()['n'];
list($c, $jxd, ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $f2);
check('F4a duplikat checksum saat revisi 422', '422', $c);
$ndoc2 = (int) q("SELECT COUNT(*) n FROM career_application_documents WHERE id_application_requirement = {$AR_D}")->fetch_assoc()['n'];
check('F4a: tidak ada versi baru', $ndoc1, $ndoc2);

/* F4b kuota versi: isi sampai 5 versi (rentetan fixture) → upload → 409 */
$vend = (int) q("SELECT COALESCE(MAX(version),0) v FROM career_application_documents WHERE id_application_requirement = {$AR_D}")->fetch_assoc()['v'];
for ($v = $vend + 1; $v <= 5; $v++) {
	q("INSERT INTO career_application_documents (id_application_requirement, id_application, version, file_path, original_name, mime_type, file_size, checksum_sha256, uploaded_at)
	             VALUES ({$AR_D}, {$APP1}, {$v}, 'career/doc_fixture_v{$v}.pdf', 'fix{$v}.pdf', 'application/pdf', 1024, SHA2('{$STAMP}-{$v}', 256), NOW())");
}
$fk = __DIR__ . '/tmp_smoke6c_' . $STAMP . '_kuota.pdf';
file_put_contents($fk, "%PDF-1.4\n% kuota jebakan\n");
list($c, , , ) = upload_dok(api_url('Lamaran_dokumen/upload'), array('id_application_requirement' => $AR_D), $TOKEN1B, 'document', $fk);
check('F4b kuota versi habis 409', '409', $c);
unlink($fk);

echo "-----\nRingkasan smoke6c: PASS={$pass} FAIL={$fail}\n";
exit($fail === 0 ? 0 : 1);
