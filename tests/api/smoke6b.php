<?php
/**
 * Smoke test Fase 6B (smoke6b.php) — modul aksi tulis admin karier.
 *
 * Pemakaian: php tests/api/smoke6b.php [BASE_ADMIN]
 *   BASE_ADMIN default: http://jacos.test:8080
 * Prasyarat: jacos_db LOKAL + migration 004 & 005 sudah dijalankan.
 * Membuat & menghapus data uji sendiri (register_shutdown_function).
 * DILARANG dipakai di produksi.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080';

$H = parse_url($BASE, PHP_URL_HOST);
$HB = ($H === null) ? '' : explode(':', $H)[0];
if ($HB !== 'jacos.test' && $HB !== 'localhost' && $HB !== '127.0.0.1') {
	echo "FAIL  host tidak lokal: {$H}\n"; exit(1);
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

$pass = 0; $fail = 0;
function career_sandi_kuatsu($s) { return strlen($s) >= 8 && preg_match('/[A-Za-z]/', $s) === 1; }

function check($nama, $harap, $terima) {
	global $pass, $fail;
	if ((string) $harap === (string) $terima) {
		$pass++; echo "PASS  $nama\n";
	} else {
		$fail++; echo "FAIL  $nama (harap " . print_r($harap, true) . ", terima " . print_r($terima, true) . ")\n";
	}
}
function json_ok($raw) { $j = json_decode($raw, true); return is_array($j) ? $j : null; }

$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }

/* Database wajib jacos_db — smoke TIDAK BOLEH jalan di DB lain */
$DBN = ''; $rd = $pdo->query('SELECT DATABASE() AS d');
if ($rd) { $DBN = (string) $rd->fetch_assoc()['d']; }
if ($DBN !== 'jacos_db') {
	fwrite(STDERR, "FAIL  database aktif '{$DBN}' bukan jacos_db — dibatalkan.\n"); exit(1);
}
$RUN_T0 = date('Y-m-d H:i:s');

$STAMP = date('ymdHis');

/* ---------- 1. fixture admin uji ---------- */
$EMAIL_A = 'smoke6b-' . $STAMP . '@example.test';
$PASS_A  = 'Rahasia6Bbb';
$hash = password_hash($PASS_A, PASSWORD_BCRYPT);
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_A}', '{$hash}', '{$EMAIL_A}', 'Admin Smoke6B', 0, '', '')");
$ADMIN_ID = (int) $pdo->query("SELECT id FROM aauth_users WHERE email = '{$EMAIL_A}'")->fetch_assoc()['id'];
$pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$ADMIN_ID}, 1)");

$EMAIL_B = 'smoke6b-b-' . $STAMP . '@example.test';
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_B}', '{$hash}', '{$EMAIL_B}', 'Admin TanpaIzin 6B', 0, '', '')");
$NOID = (int) $pdo->query("SELECT id FROM aauth_users WHERE email = '{$EMAIL_B}'")->fetch_assoc()['id'];
/* admin C: career_pelamar_list + career_pelamar_view saja (tanpa score_view) */
$EMAIL_C = 'smoke6b-c-' . $STAMP . '@example.test';
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_C}', '{$hash}', '{$EMAIL_C}', 'Admin ViewOnly 6B', 0, '', '')");
$CID = (int) $pdo->insert_id;
$pdo->query("INSERT INTO aauth_groups (name, definition) VALUES ('Smoke6B ViewOnly {$STAMP}', 'fixture')");
$GID_C = (int) $pdo->insert_id;
foreach (array('career_pelamar_list', 'career_pelamar_view') as $pnama) {
	$pid = $pdo->query("SELECT id FROM aauth_perms WHERE name = '" . $pnama . "'")->fetch_assoc();
	if ($pid) { $pdo->query("INSERT INTO aauth_perm_to_group (perm_id, group_id) VALUES (" . (int) $pid['id'] . ", {$GID_C})"); }
}
$pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$CID}, {$GID_C})");

$g = $pdo->query("SELECT id FROM aauth_groups WHERE id <> 1 ORDER BY id LIMIT 1")->fetch_assoc();
$NOGR = $g ? (int) $g['id'] : 0;
if ($NOGR > 0) { $pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$NOID}, {$NOGR})"); }

/* ---------- cleanup shutdown ---------- */
register_shutdown_function(function () {
	global $pdo, $ADMIN_ID, $NOID, $CID, $GID_C, $G_APPS, $G_POSTS, $G_CANDS, $RUN_T0;

	/* #22: pembersihan berbasis ID yang TERCATAT saat membuat fixture
	   (tanpa pencocokan nama/deskripsi); urutan DELETE anak dulu baru induk (FK). */
	$apps   = implode(',', array_map('intval', $G_APPS));
	$posts  = implode(',', array_map('intval', $G_POSTS));
	$cands  = implode(',', array_map('intval', $G_CANDS));
	$users  = implode(',', array_map('intval', array_merge(array($ADMIN_ID, $NOID), (int) $CID > 0 ? array($CID) : array())));
	$pdo->query("DELETE n FROM career_notifications n WHERE n.id_candidate IN ({$cands})");
	$pdo->query("DELETE e FROM career_evaluation_scores e
	             JOIN career_application_stages s ON s.id_application_stage = e.id_application_stage
	             WHERE s.id_application IN ({$apps})");
	$pdo->query("DELETE FROM career_selection_schedules WHERE id_application_stage IN
	             (SELECT id_application_stage FROM career_application_stages WHERE id_application IN ({$apps}) UNION SELECT 0)");
	$pdo->query("DELETE FROM career_application_stages WHERE id_application IN ({$apps})");
	$pdo->query("DELETE FROM career_application_documents WHERE id_application IN ({$apps})");
	$pdo->query("DELETE FROM career_application_requirements WHERE id_application IN ({$apps})");
	$pdo->query("DELETE FROM career_application_profile_snapshots WHERE id_application IN ({$apps})");
	$pdo->query("DELETE FROM career_applications WHERE id_application IN ({$apps})");
	$pdo->query("DELETE FROM career_job_posting_requirements WHERE id_job_posting IN ({$posts})");
	$pdo->query("DELETE FROM career_job_postings WHERE id_job_posting IN ({$posts})");
	$pdo->query("DELETE FROM career_candidates WHERE id_candidate IN ({$cands})");
	$pdo->query("DELETE FROM aauth_user_to_group WHERE user_id IN ({$users})");
	if ((int) $GID_C > 0) {
		/* grup uji admin view-only: hapus bersama permnya;
		   nama grup diverifikasi supaya id yang tak sengaja sama tak ikut terhapus */
		$gclean = (int) $GID_C;
		$gnama = $pdo->query("SELECT name FROM aauth_groups WHERE id = {$gclean}")->fetch_assoc();
		if (is_array($gnama) && strpos((string) $gnama['name'], 'Smoke6B ViewOnly') === 0) {
			$pdo->query("DELETE FROM aauth_perm_to_group WHERE group_id = {$gclean}");
			$pdo->query("DELETE FROM aauth_groups WHERE id = {$gclean}");
		}
	}
	/* #22: login_attempts HANYA baris jendela run ini + 127.0.0.1 (IP fixture)
	   — bukan DELETE global seluruh tabel */
	$t0 = (string) $RUN_T0;
	$pdo->query("DELETE FROM aauth_login_attempts WHERE timestamp >= '" . $pdo->real_escape_string($t0) . "' AND ip_address = '127.0.0.1'");
	$pdo->query("DELETE FROM aauth_users WHERE id IN ({$users})");

	/* #22: bereskan berkas uji smoke6b sendiri */
	foreach ((array) @glob(__DIR__ . '/../../application/storage/career/smoke6b-*.pdf') as $f) { @unlink($f); }
});

/* ---------- 2. login + cookie ---------- */
function post_login($fields) {
	$headers = array('Content-Type: application/x-www-form-urlencoded');
	return http('POST', $GLOBALS['BASE'] . '/administrator/login', $headers, http_build_query($fields), null);
}
list($c, $head, $rawbody) = post_login(array('username' => $EMAIL_A, 'password' => $PASS_A));
check('login backend 302/200', 'ya', ($c === '302' || $c === '200') ? 'ya' : $c);
preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $head, $mm);
$sid = null;
foreach ($mm[1] as $i => $nm) { if (trim(strtolower($nm)) === 'jacos_session') $sid = rawurldecode($mm[2][$i]); }
check('cookie sesi didapat (admin super)', 1, $sid !== null ? 1 : 0);
$COOKIE = 'jacos_session=' . rawurlencode((string) $sid);

/* admin B tanpa izin */
list($c, $head, $rawbody) = post_login(array('username' => $EMAIL_B, 'password' => $PASS_A));
preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $head, $mm2);
$sid2 = null;
foreach ($mm2[1] as $i => $nm) { if (trim(strtolower($nm)) === 'jacos_session') $sid2 = rawurldecode($mm2[2][$i]); }
$COOKIE_NO = $sid2 !== null ? 'jacos_session=' . rawurlencode($sid2) : null;

function csrf_get($path) {
	list($c, , $rawbody) = http('GET', $GLOBALS['BASE'] . $path, array(), null, $GLOBALS['COOKIE']);
	if (preg_match('/name="career_csrf"[^>]*value="([^"]+)"/', $rawbody, $m)) return $m[1];
	if (preg_match('/value="([^"]+)"\s*name="career_csrf"/', $rawbody, $m)) return $m[1];
	return null;
}
function post_admin($path, $fields, $cookie = null) {
	$headers = array('Content-Type: application/x-www-form-urlencoded', 'X-Requested-With: XMLHttpRequest');
	return http('POST', $GLOBALS['BASE'] . $path, $headers, http_build_query($fields), $cookie !== null ? $cookie : $GLOBALS['COOKIE']);
}

/* ---------- 3. fixture lowongan + persyaratan + kandidat + lamaran ---------- */
$NM = '[DUMMY TEST6B] Guru Kelas SD';
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('{$NM}', 'post smoke6b', '2026-01-01', '2026-12-31', 'S1', 1, {$ADMIN_ID})");
$IDP = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE title = '{$NM}' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
#22 6B.2: id fixture TERCATAT — cleanup pakai id, bukan match nama
$G_POSTS = array($IDP);
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Ijazah', 'required', 'document', 'PDF, JPG', 5120, 1)");
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Nama', 'required', 'text', NULL, NULL, 2)");
$REQ1 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 1")->fetch_assoc()['id_job_posting_requirement'];
$REQ2 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 2")->fetch_assoc()['id_job_posting_requirement'];

$EMAIL_KAN = 'smoke6b-kandidat-' . $STAMP . '@example.test';
$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
             VALUES ('{$EMAIL_KAN}', '" . password_hash('SandiLama123', PASSWORD_BCRYPT) . "', 'Kandidat Smoke6B', 1, 1)");
$KANID = (int) $pdo->query("SELECT id_candidate FROM career_candidates WHERE full_name = 'Kandidat Smoke6B' ORDER BY id_candidate DESC LIMIT 1")->fetch_assoc()['id_candidate'];
$G_CANDS = array($KANID);

$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP}, 'submitted', 1, NOW())");
$APP = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_candidate = {$KANID} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
$G_APPS = array($APP);

$snap = json_encode(array('items' => array(), 'profile' => array(), 'candidate' => array('full_name' => 'Kandidat Smoke6B', 'email' => '')));
$pdo->query("INSERT INTO career_application_profile_snapshots (id_application, profile_json, snapshot_at)
             VALUES ({$APP}, '" . $pdo->real_escape_string($snap) . "', NOW())");

/* persyaratan milik lamaran: ijazah pending+doc, nama pending+answer */
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP}, {$REQ1}, 'pending_review', NULL)");
$AR1 = (int) $pdo->query("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP} AND id_job_posting_requirement = {$REQ1}")->fetch_assoc()['id_application_requirement'];
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP}, {$REQ2}, 'pending_review', 'Guru Kelas Aktif')");
$AR2 = (int) $pdo->query("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP} AND id_job_posting_requirement = {$REQ2}")->fetch_assoc()['id_application_requirement'];

$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Scan KTP', 'required', 'document', 'PDF, JPG', 2048, 3)");
$REQ3 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 3")->fetch_assoc()['id_job_posting_requirement'];
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status, answer_text)
             VALUES ({$APP}, {$REQ3}, 'pending_review', NULL)");
$AR3 = (int) $pdo->query("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP} AND id_job_posting_requirement = {$REQ3}")->fetch_assoc()['id_application_requirement'];

$DIR = __DIR__ . '/../../application/storage/career';
@mkdir($DIR, 0777, true);
$FILEPATH = 'application/storage/career/smoke6b-' . $STAMP . '.pdf';
file_put_contents(__DIR__ . '/../../' . $FILEPATH, "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Size 1/Root 1 0 R>>\n%%EOF\n");
$pdo->query("INSERT INTO career_application_documents (id_application_requirement, id_application, version, file_path, original_name, mime_type, file_size, checksum_sha256, uploaded_at)
             VALUES ({$AR1}, {$APP}, 1, '{$FILEPATH}', 'smoke6b.pdf', 'application/pdf', 60, SHA2('smoke6b', 256), NOW())");
$DOC = (int) $pdo->query("SELECT id_application_document FROM career_application_documents WHERE id_application_requirement = {$AR1} ORDER BY id_application_document DESC LIMIT 1")->fetch_assoc()['id_application_document'];

/* 5 tahap milik lamaran */
$stg = $pdo->query("SELECT id_stage FROM career_stages WHERE is_active = 1 ORDER BY id_stage ASC")->fetch_all(MYSQLI_NUM);
$STG1 = (int) $stg[0][0];
$STG2 = (int) $stg[1][0];
$STG3 = (int) $stg[2][0];
$STG5 = (int) $stg[4][0];
foreach ($stg as $i => $row) {
	$pdo->query("INSERT INTO career_application_stages (id_application, id_stage, status)
	             VALUES ({$APP}, {$row[0]}, 'pending')");
}
$STGROW = array();
foreach ($pdo->query("SELECT id_application_stage, id_stage FROM career_application_stages WHERE id_application = {$APP}")->fetch_all(MYSQLI_ASSOC) as $r) {
	$STGROW[(int) $r['id_stage']] = (int) $r['id_application_stage'];
}

/* ---------- 4. kandidat karier: list + izin ---------- */
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_kandidat', array(), null, $COOKIE);
check('kandidat list 200', '200', $c);
check('kandidat list tampil kandidat uji', 1, (strpos($rawbody, 'Kandidat Smoke6B') !== false) ? 1 : 0);
$stripped = preg_replace('#<(script|style)[\s\S]*?</(script|style)>#i', '', $rawbody);
check('kandidat list TANPA password_hash', 0, (strpos($stripped, 'password_hash') !== false) ? 1 : 0);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_kandidat?q=Blah', array(), null, $COOKIE);
check('kandidat list filter q kosong hasil', 0, (strpos($rawbody, 'Kandidat Smoke6B') !== false) ? 1 : 0);
list($c, ) = http('GET', $BASE . '/administrator/career_kandidat', array(), null, $COOKIE_NO);
check('kandidat list tanpa izin 403', '403', $c);

/* ---------- 5. reset password D2 (career_kandidat) ---------- */
list($c, , ) = http('GET', $BASE . '/administrator/career_kandidat/reset_password/' . $KANID, array(), null, $COOKIE);
check('reset password via GET ditolak', '403', $c);
list($c, , ) = post_admin('/administrator/career_kandidat/reset_password/' . $KANID, array('password_baru' => 'SandiBaru999'));
check('reset password tanpa CSRF 403', '403', $c);
$HASH_OLD = $pdo->query("SELECT password_hash FROM career_candidates WHERE id_candidate = {$KANID}")->fetch_assoc()['password_hash'];
/* 6B.2: rantai reset — login dgn sandi lama dulu, pegang token lama */
list($c, , $jraw) = http_json6b('POST', $BASE . '/apicareer/Auth/login', array('email' => $EMAIL_KAN, 'password' => 'SandiLama123'), null);
$jlogin_oldu = json_ok($jraw);
$TOK_OLD = ($jlogin_oldu && isset($jlogin_oldu['data']['token'])) ? (string) $jlogin_oldu['data']['token'] : '';
check('login kandidat dgn sandi lama 200', 1, ($TOK_OLD !== '' ? 1 : 0));
$VER_OLD = (int) $pdo->query("SELECT token_version FROM career_candidates WHERE id_candidate = {$KANID}")->fetch_assoc()['token_version'];
/* #2 6B.1: sandi BARU dibangkitkan SERVER (bukan dari Input) —
   smoke men-verifikasi dari data.password respons. */
list($c, , $jraw) = post_admin('/administrator/career_kandidat/reset_password/' . $KANID, array(
	'career_csrf' => csrf_get('/administrator/career_kandidat'),
));
$j = json_ok($jraw);
check('reset password 200 success', 1, ($j !== null && isset($j['success']) && $j['success']) ? 1 : 0);
$SANDI_BARU = isset($j['data']['password']) ? (string) $j['data']['password'] : '';
check('reset password kembalikan sandi server-generate', 1, (career_sandi_kuatsu($SANDI_BARU) ? 1 : 0));
$HASH_NEW = $pdo->query("SELECT password_hash FROM career_candidates WHERE id_candidate = {$KANID}")->fetch_assoc()['password_hash'];
check('password_hash berubah di DB', 1, ($HASH_NEW !== $HASH_OLD && $SANDI_BARU !== '' && password_verify($SANDI_BARU, $HASH_NEW)) ? 1 : 0);
check('audit reset_password ada', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action='reset_password_kandidat' AND object_id={$KANID}")->fetch_assoc()['n'] === 1) ? 1 : 0);
check('audit reset TIDAK menyimpan sandi', 0,
	(strpos((string) $pdo->query("SELECT detail_json FROM career_audit_logs WHERE action='reset_password_kandidat' AND object_id={$KANID} LIMIT 1")->fetch_assoc()['detail_json'], $SANDI_BARU) !== false) ? 1 : 0);
/* 6B.2: sandi lama GAGAL login; token lama 401; token_version naik tepat 1 */
list($c, , $jraw) = http_json6b('POST', $BASE . '/apicareer/Auth/login', array('email' => $EMAIL_KAN, 'password' => 'SandiLama123'), null);
$jgagal = json_ok($jraw);
check('login dgn sandi lama DITOLAK', 1, ($jgagal !== null && empty($jgagal['success'])) ? 1 : 0);
list($c, , $jraw) = http_json6b('GET', $BASE . '/apicareer/Auth/me', null, $TOK_OLD);
check('token lama setelah reset 401', '401', $c);
$VER_NEW = (int) $pdo->query("SELECT token_version FROM career_candidates WHERE id_candidate = {$KANID}")->fetch_assoc()['token_version'];
check('token_version naik tepat 1', 1, ($VER_NEW === $VER_OLD + 1) ? 1 : 0);

/* ---------- 6. verifikasi persyaratan (career_pelamar/verifikasi) ---------- */
list($c, ) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR1, array(
	'keputusan' => 'verified', 'pesan_kandidat' => 'OK'), $COOKIE_NO);
check('verifikasi tanpa izin 403', '403', $c);
list($c, , ) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR1, array('keputusan' => 'verified'));
check('verifikasi tanpa CSRF 403', '403', $c);
$CSRF_P = csrf_get('/administrator/career_kandidat');

list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/999999', array('career_csrf' => $CSRF_P, 'keputusan' => 'verified'));
check('verifikasi id tidak ada 404', '404', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR1, array(
	'career_csrf' => $CSRF_P, 'keputusan' => ' verified '));
check('verifikasi keputusan tidak sah 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR1, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'revision_requested'));
$j = json_ok($jraw);
check('revisi tanpa pesan kandidat 422', '422', $c);

/* revision_requested + pesan kandidat */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR1, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'revision_requested',
	'pesan_kandidat' => 'Ijazah buram, mohon unggah ulang bermutu.', 'review_note' => 'internal: scan buram'));
$j = json_ok($jraw);
check('verifikasi revision_requested 200', 1, ($j !== null && $j['success']) ? 1 : 0);
$stt = $pdo->query("SELECT status, review_note, reviewed_by FROM career_application_requirements WHERE id_application_requirement = {$AR1}")->fetch_assoc();
check('status revision_requested tersimpan', 'revision_requested', $stt['status']);
check('review_note internal tersimpan', 'internal: scan buram', $stt['review_note']);
check('reviewed_by = admin uji', $ADMIN_ID, (int) $stt['reviewed_by']);
check('notif document_revision ada', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_notifications WHERE id_candidate={$KANID} AND type IN ('document_revision','verifikasi','requirement_revision')")->fetch_assoc()['n'] >= 1) ? 1 : 0);

/* verified tanpa dokumen ditolak (AR3 = document tanpa berkas) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR3, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'verified'));
check('verified tanpa dokumen (document) ditolak', 1, (($j = json_ok($jraw)) !== null && $j['success'] === false) ? 1 : 0);

/* verified untuk answer text (req2 text) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR2, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'verified', 'review_note' => 'jawaban sesuai'));
$j = json_ok($jraw);
check('verifikasi jawaban text verified 200', 1, ($j !== null && $j['success']) ? 1 : 0);
check('status text req verified', 'verified', $pdo->query("SELECT status FROM career_application_requirements WHERE id_application_requirement = {$AR2}")->fetch_assoc()['status']);

/* rejected */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR2, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'rejected', 'pesan_kandidat' => 'Jawaban tidak sesuai.')
);
$j = json_ok($jraw);
check('verifikasi rejected 200', 1, ($j !== null && $j['success']) ? 1 : 0);

/* rejected ulang pada status sama → 409 (6B.2: kode 409, bukan 422) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR2, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'rejected', 'pesan_kandidat' => 'Jawaban tidak sesuai.'));
check('verifikasi rejected ulang 409', '409', $c);

/* check audit verifikasi */
check('audit verifikasi ada >=2', 1, (((int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action='verifikasi_persyaratan' AND actor_id={$ADMIN_ID}")->fetch_assoc()['n']) >= 2) ? 1 : 0);

/* ---------- 7. keputusan tahap (career_pelamar/keputusan) ---------- */
list($c, ) = post_admin('/administrator/career_pelamar/keputusan', array(
	'id_application' => $APP, 'n' => 1, 'keputusan' => 'passed'), $COOKIE_NO);
check('keputusan tanpa izin (job career_pelamar_decide) 403', '403', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array('id_application' => $APP, 'n' => 1, 'keputusan' => 'passed'));
check('keputusan tanpa CSRF 403', '403', $c);

list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan' => 'salah'));
check('keputusan nilai tidak sah 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 9, 'keputusan' => 'passed'));
check('keputusan tahap tidak ada 404', '404', $c);

/* submitted -> in_process saat stage1 passed (D16.3) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan' => 'passed'));
$j = json_ok($jraw);
check('keputusan tahap1 passed 200', 1, ($j !== null && $j['success']) ? 1 : 0);
check('lamaran status in_process setelah tahap1', 'in_process',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status']);
check('tahap1 status passed', 'passed', $pdo->query("SELECT status FROM career_application_stages WHERE id_application={$APP} AND id_stage={$STG1}")->fetch_assoc()['status']);

/* stage3 sebelum stage2 → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 3, 'keputusan' => 'passed'));
check('keputusan loncat tahap 409', '409', $c);

/* stage2 failed → not_accepted */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 2, 'keputusan' => 'failed'));
$j = json_ok($jraw);
check('keputusan tahap2 failed 200', 1, ($j !== null && $j['success']) ? 1 : 0);
check('lamaran status not_accepted', 'not_accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status']);
check('tahap2 status failed', 'failed', $pdo->query("SELECT status FROM career_application_stages WHERE id_application={$APP} AND id_stage={$STG2}")->fetch_assoc()['status']);

/* keputusan ulang tahap yang sudah diputuskan → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 2, 'keputusan' => 'passed'));
check('keputusan tahap sudah diputuskan 409', '409', $c);

/* ---------- 8. koreksi tahap (career_pelamar/koreksi) ---------- */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 2, 'keputusan_baru' => 'passed', 'alasan' => ''));
check('koreksi tanpa alasan 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan_baru' => 'passed', 'alasan' => str_repeat('x', 501)));
check('koreksi alasan 501 karakter 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan_baru' => 'passed', 'alasan' => 'Alasan 😀 uji'));
check('koreksi alasan emoji 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 2, 'keputusan_baru' => 'salah', 'alasan' => 'x'));
check('koreksi nilai tidak sah 422', '422', $c);
/* koreksi berganti status ke passed + alasan */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 2, 'keputusan_baru' => 'passed', 'alasan' => 'Admin salah pilih - betulan jadi passed.'));
$j = json_ok($jraw);
check('koreksi 200 success', 1, ($j !== null && $j['success']) ? 1 : 0);
check('tahap2 jadi passed (koreksi)', 'passed', $pdo->query("SELECT status FROM career_application_stages WHERE id_application={$APP} AND id_stage={$STG2}")->fetch_assoc()['status']);
check('lamaran status balik in_process', 'in_process',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status']);
$kr = $pdo->query("SELECT detail_json FROM career_audit_logs WHERE action='koreksi_keputusan_tahap' AND object_id={$APP} ORDER BY id_audit_log DESC LIMIT 1")->fetch_assoc()['detail_json'];
/* #3 review 6B.1: alasan koreksi WAJIB tersimpan penuh (bukan alasan_len)
   supaya audit dapat dipertanggungjawabkan; ini bukan data D13. */
check('audit koreksi simpan alasan penuh', 1,
	(strpos((string) $kr, 'Admin salah pilih - betulan jadi passed.') !== false) ? 1 : 0);
check('audit koreksi simpan before/after tahap & lamaran', 1,
	(strpos((string) $kr, 'before_tahap') !== false && strpos((string) $kr, 'after_app') !== false) ? 1 : 0);

/* ---------- 9. lulus 1..5 semua → termasuk terima (D16.4) ---------- */
foreach (array(3 => $STG3, 4 => $stg[3][0], 5 => $STG5) as $n => $sid_) {
	list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
		'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => $n, 'keputusan' => 'passed'));
}
check('tahap5 status passed', 'passed', $pdo->query("SELECT status FROM career_application_stages WHERE id_application={$APP} AND id_stage={$STG5}")->fetch_assoc()['status']);
check('lamaran masih in_process sebelum terima', 'in_process',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status']);

list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan_baru' => 'failed', 'alasan' => 'Tes koreksi akhir.'));
/* no-op koreksi (keputusan sama dgn sekarang) → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan_baru' => 'failed', 'alasan' => 'Ulang sama.'));
check('koreksi no-op (keputusan sama) 409', '409', $c);
check('koreksi failing (tahap 1) → tahap lain reset pending', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_application_stages WHERE id_application={$APP} AND status IN ('passed','failed') AND id_stage <> {$STG1}")->fetch_assoc()['n'] === 0) ? 1 : 0);

/* #14 6B.2: TIDAK memulihkan status lamaran $APP lewat SQL langsung —
   buat fixture baru kandidat K2 + lamaran APP5 (lima tahap sudah lulus)
   khusus menguji jalur 'terima' 200. */
$EMAIL_K2 = 'smoke6b-k2-' . $STAMP . '@example.test';
$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
             VALUES ('{$EMAIL_K2}', '" . password_hash('SandiLama123', PASSWORD_BCRYPT) . "', 'Kandidat Smoke6B', 1, 1)");
$K2 = (int) $pdo->insert_id;
$G_CANDS[] = $K2;
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$K2}, {$IDP}, 'submitted', 1, NOW())");
$APP5 = (int) $pdo->insert_id;
$G_APPS[] = $APP5;
foreach ($stg as $row) {
	$pdo->query("INSERT INTO career_application_stages (id_application, id_stage, status)
	             VALUES ({$APP5}, {$row[0]}, 'passed')");
}

/* terima sebelum lengkap? bukan — tahap sudah semua passed. Terima valid: */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array('id_application' => $APP5));
check('terima tanpa CSRF 403', '403', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array(
	'career_csrf' => $CSRF_P, 'id_application' => 999999));
check('terima lamaran tidak ada 404', '404', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP5));
$j = json_ok($jraw);
check('terima 200 (semua tahap lulus)', 1, ($j !== null && $j['success']) ? 1 : 0);
check('lamaran status accepted', 'accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP5}")->fetch_assoc()['status']);
check('audit status_akhir_diterima ada', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action='status_akhir_diterima' AND object_id={$APP5}")->fetch_assoc()['n'] === 1) ? 1 : 0);
/* keputusan pada lamaran accepted → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP5, 'n' => 1, 'keputusan' => 'passed'));
check('keputusan pada lamaran accepted 409', '409', $c);
/* koreksi pada lamaran accepted sesuai D16.5: boleh membatalkan status
   akhir → 200, lamaran kembali terkonsistensi (stage1 failed → lamaran
   not_accepted, tahap 2..5 kembali pending) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP5, 'n' => 1, 'keputusan_baru' => 'failed', 'alasan' => 'Ulang koreksi.'));
check('koreksi pada lamaran accepted → batalkan status akhir 200', '200', $c);
check('koreksi batalkan accepted → tahap2..5 pending', 1,
	((int) $pdo->query("SELECT COUNT(*) n FROM career_application_stages WHERE id_application={$APP5} AND status IN ('passed','failed') AND id_stage <> {$STG1}")->fetch_assoc()['n'] === 0) ? 1 : 0);
check('koreksi batalkan accepted → lamaran not_accepted', 'not_accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP5}")->fetch_assoc()['status'] ?? '');
/* 6B.2: TIDAK ADA pemulihan status via SQL langsung — lamaran $APP
   tetap not_accepted pasca-koreksi; terima ulang pada lamaran yang
   bukan submitted/in_process HARUS ditolak 409 (#9 review 6B.1). */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array('career_csrf' => $CSRF_P, 'id_application' => $APP5));
$j = json_ok($jraw);
check('terima ulang pada lamaran not_accepted 409', '409', $c);
check('lamaran tetap not_accepted setelah terima ditolak', 'not_accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP5}")->fetch_assoc()['status']);

/* ---------- 10. jadwal & nilai (career_pelamar/jadwal + nilai) — lamaran#2 ---------- */
/* lamaran#2 untuk uji jadwal/nilai: kandidat (satu lamaran per lowongan),
   job_posting lain nutup far-future agar tetap 'submitted' */
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('{$NM}', 'post smoke6b jadwal', '2026-01-01', '2030-12-31', 'S1', 1, {$ADMIN_ID})");
$IDP2 = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE description = 'post smoke6b jadwal' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
$G_POSTS[] = $IDP2;
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP2}, 'submitted', 1, NOW())");
$APP2 = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_candidate={$KANID} AND id_job_posting={$IDP2} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
$G_APPS[] = $APP2;
foreach ($stg as $row) {
	$pdo->query("INSERT INTO career_application_stages (id_application, id_stage, status)
	             VALUES ({$APP2}, {$row[0]}, 'pending')");
}
$STAGE1_2 = (int) $pdo->query("SELECT id_application_stage FROM career_application_stages WHERE id_application={$APP2} AND id_stage={$STG1}")->fetch_assoc()['id_application_stage'];
$STAGE2_2 = (int) $pdo->query("SELECT id_application_stage FROM career_application_stages WHERE id_application={$APP2} AND id_stage={$STG2}")->fetch_assoc()['id_application_stage'];

/* tanpa perm jadwal 403 */
list($c, ) = post_admin('/administrator/career_pelamar/jadwal', array(
	'id_application_stage' => $STAGE1_2, 'aksi' => 'simpan'), $COOKIE_NO);
check('jadwal tanpa izin 403', '403', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'id_application_stage' => $STAGE1_2, 'aksi' => 'simpan'));
check('jadwal tanpa CSRF 403', '403', $c);

/* D16.2: jadwal tahap 2 sebelum tahap1 lulus dijadwalkan → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'simpan',
	'schedule_datetime' => '2026-05-10 09:00'));
$j = json_ok($jraw);
check('jadwal tahap2 sebelum tahap1 lulus 409 (D16.2)', '409', $c);

/* lulus tahap 1 utk lanjut schedule tahap 2 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP2, 'n' => 1, 'keputusan' => 'passed'));

/* jadwal tahap1 tidak valid dt → 422 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'simpan',
	'schedule_datetime' => 'NOT_DATE', 'channel' => 'Online', 'note' => 'x'));
check('jadwal tanggal tidak sah 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'salah'));
check('jadwal aksi tidak sah 422', '422', $c);

/* jawal tahap2 simpan OK (belum dipublikasi) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'simpan',
	'schedule_datetime' => '2026-05-10 09:00', 'channel' => 'Online', 'note' => 'Bawa tes digital'));
$j = json_ok($jraw);
check('jadwal simpan 200 (belum dipublikasi)', 1, ($j !== null && $j['success']) ? 1 : 0);
$jrow = $pdo->query("SELECT schedule_datetime, channel, note, is_published FROM career_selection_schedules WHERE id_application_stage={$STAGE2_2}")->fetch_assoc();
check('jadwal tersimpan di DB (belum publikasi)', 0, isset($jrow['is_published']) ? (int) $jrow['is_published'] : -1);
check('jadwal channel/note tersimpan', 1, (isset($jrow['channel']) && $jrow['channel'] === 'Online' && $jrow['note'] === 'Bawa tes digital') ? 1 : 0);
check('tahap2 masih pending (belum publi)', 'pending', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE2_2}")->fetch_assoc()['status']);

/* publikasi */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'publikasi'));
$j = json_ok($jraw);
check('jadwul publikasi 200', 1, ($j !== null && $j['success']) ? 1 : 0);
check('jadwal jadi terpublikasi', 1, (int) $pdo->query("SELECT is_published FROM career_selection_schedules WHERE id_application_stage={$STAGE2_2}")->fetch_assoc()['is_published']);
check('tahap status scheduled setelah publikasi', 'scheduled', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE2_2}")->fetch_assoc()['status']);
check('notif stage_scheduled ada (D18)', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_notifications WHERE id_candidate={$KANID} AND type='stage_scheduled'")->fetch_assoc()['n'] >= 1) ? 1 : 0);

/* ubah jadwal terpublikasi → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'simpan',
	'schedule_datetime' => '2026-05-11 09:00', 'channel' => 'Online'));
$j = json_ok($jraw);
check('ubah jadwal terpublikasi ditolak', 1, ($j !== null && $j['success'] === false) ? 1 : 0);

/* batalkan publikasi */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'batal'));
$j = json_ok($jraw);
check('jadwal batal publikasi 200', 1, ($j !== null && $j['success']) ? 1 : 0);
check('tahap kembali pending (batal publikasi)', 'pending', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE2_2}")->fetch_assoc()['status']);

/* hapus jadwal */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'hapus'));
$j = json_ok($jraw);
check('jadwal hapus 200', 1, ($j !== null && $j['success']) ? 1 : 0);
check('jadwal hilang di DB', 0, (int) $pdo->query("SELECT COUNT(*) n FROM career_selection_schedules WHERE id_application_stage={$STAGE2_2}")->fetch_assoc()['n']);

/* 6B.2: emoji 4-byte ditolak di field teks jadwal & nilai */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'simpan',
	'schedule_datetime' => '2026-05-10 09:00', 'channel' => 'Online', 'note' => 'Catatan 😀'));
check('jadwal note emoji 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE1_2, 'score' => '80', 'rubric_note' => 'Nilai 😀'));
check('nilai rubric_note emoji 422', '422', $c);

/* ---------- 11. nilai & catatan internal (D13) ---------- */
list($c, ) = post_admin('/administrator/career_pelamar/nilai', array(
	'id_application_stage' => $STAGE1_2, 'score' => '75'), $COOKIE_NO);
check('nilai tanpa izin (score_edit) 403', '403', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'id_application_stage' => $STAGE1_2, 'score' => '75'));
check('nilai tanpa CSRF 403', '403', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE1_2, 'score' => '150'));
check('nilai 150 ditolak 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE1_2, 'score' => 'abc'));
check('nilai abc ditolak 422', '422', $c);
$LONGRUBRIK = str_repeat('k', 2001);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE1_2, 'score' => '75', 'rubric_note' => $LONGRUBRIK));
check('rubric_note > 2000 ditolak 422', '422', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE1_2, 'score' => '75',
	'rubric_note' => 'Tes dasar bagus; psikotes menyusul.'));
$j = json_ok($jraw);
check('nilai simpan 200', 1, ($j !== null && $j['success']) ? 1 : 0);
/* nilai D13 tersimpan di career_evaluation_scores (B14 satu sumber kebenaran) */
$row_nilai = $pdo->query("SELECT score, rubric_note FROM career_evaluation_scores WHERE id_application_stage={$STAGE1_2}")->fetch_assoc();
check('score tersimpan 75.00', '75.00', ($row_nilai === null) ? '' : (string) $row_nilai['score']);
check("rubric_note internal tersimpan", 'Tes dasar bagus; psikotes menyusul.', ($row_nilai === null) ? '' : (string) $row_nilai['rubric_note']);
check('audit simpan_nilai ada', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action='simpan_nilai_tahap' AND actor_id={$ADMIN_ID}")->fetch_assoc()['n'] >= 1) ? 1 : 0);
/* audit nilai tanpa isi rubrik */
$val_audit = (string) $pdo->query("SELECT detail_json FROM career_audit_logs WHERE action='simpan_nilai_tahap' AND actor_id={$ADMIN_ID} ORDER BY id_audit_log DESC LIMIT 1")->fetch_assoc()['detail_json'];
check('audit nilai tidak plaintext rubric', 0, (strpos($val_audit, 'psikotes menyusul') !== false) ? 1 : 0);

/* ---------- 11.5 6B.2: jadwal diputuskan terkunci + koreksi un-publish ---------- */
$STAGE3_2 = (int) $pdo->query("SELECT id_application_stage FROM career_application_stages WHERE id_application={$APP2} AND id_stage={$STG3}")->fetch_assoc()['id_application_stage'];
/* jadwal tahap3 dibuat + dipublikasi (tahap2 belum? tahap2 masih pending —
   D16.2 menuntut tahap2 lulus dulu: luluskan lewat keputusan) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'simpan',
	'schedule_datetime' => '2026-05-12 09:00', 'channel' => 'Online', 'note' => 'Tes tahap2'));
check('jadwal tahap2 (lagi) 200', '200', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'publikasi'));
check('jadwal tahap2 publikasi 200', '200', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP2, 'n' => 2, 'keputusan' => 'passed'));
check('keputusan tahap2 lulus (walau masih scheduled) 200', '200', $c);
/* batal/hapus pada tahap yang SUDAH diputuskan → 409 + data identik */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE2_2, 'aksi' => 'batal'));
check('batal jadwal pada tahap diputuskan 409', '409', $c);
$jd = $pdo->query("SELECT schedule_datetime, channel, note, is_published FROM career_selection_schedules WHERE id_application_stage={$STAGE2_2}")->fetch_assoc();
check('batal 409 → jadwal tetap ada & terpublikasi', 1, (is_array($jd) && (int) $jd['is_published'] === 1 && (string) $jd['schedule_datetime'] === '2026-05-12 09:00:00') ? 1 : 0);
check('batal 409 → tahap tetap passed', 'passed', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE2_2}")->fetch_assoc()['status']);

/* jadwal tahap3 + publikasi, lalu koreksi tahap3 → tahap3 un-publish &
   tahap berikutnya kembali pending (D16.5/D16.2) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE3_2, 'aksi' => 'simpan',
	'schedule_datetime' => '2026-05-13 09:00', 'channel' => 'Offline', 'note' => 'Tes tahap3'));
check('jadwal tahap3 simpan 200', '200', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STAGE3_2, 'aksi' => 'publikasi'));
check('jadwal tahap3 publikasi 200', '200', $c);
check('tahap3 scheduled', 'scheduled', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE3_2}")->fetch_assoc()['status']);
/* koreksi pada tahap yang MASIH scheduled → 409; data identik */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP2, 'n' => 3, 'keputusan_baru' => 'pending', 'alasan' => 'Jadwal salah admin - koreksi.'));
check('koreksi pada tahap scheduled ditolak 409', '409', $c);
check('koreksi 409 → jadwal tahap3 tetap terpublikasi', 1, (int) $pdo->query("SELECT is_published FROM career_selection_schedules WHERE id_application_stage={$STAGE3_2}")->fetch_assoc()['is_published']);
check('koreksi 409 → tahap3 tetap scheduled', 'scheduled', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE3_2}")->fetch_assoc()['status']);
/* un-publish jadwal lewat koreksi tahap2 yang SUDAH diputuskan */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP2, 'n' => 2, 'keputusan_baru' => 'pending', 'alasan' => 'Koreksi sesuai D16.5.'));
check('koreksi tahap2 ke pending 200', '200', $c);
check('koreksi tahap2 → jadwal tahap3 un-published', 0, (int) $pdo->query("SELECT is_published FROM career_selection_schedules WHERE id_application_stage={$STAGE3_2}")->fetch_assoc()['is_published']);
check('koreksi tahap2 → tahap3 pending', 'pending', $pdo->query("SELECT status FROM career_application_stages WHERE id_application_stage={$STAGE3_2}")->fetch_assoc()['status']);
check('koreksi tahap2 → tahap4 tetap pending', 'pending', $pdo->query("SELECT status FROM career_application_stages WHERE id_application={$APP2} AND id_stage={$stg[3][0]}")->fetch_assoc()['status']);
/* tahap2 gagal (via keputusan pada tahap pending) → lamaran not_accepted */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP2, 'n' => 2, 'keputusan' => 'failed'));
check('keputusan tahap2 failed 200', '200', $c);
check('tahap2 failed → lamaran not_accepted', 'not_accepted', $pdo->query("SELECT status FROM career_applications WHERE id_application={$APP2}")->fetch_assoc()['status']);

/* ---------- 12. pelamar list tetap baca + perlu_verifikasi ---------- */
/* $APP pada titik ini accepted, APP2 sudah in_process (D16.3 setelah
   keputusan tahap 1) → buat lamaran#3 yang masih mentah submitted untuk uji
   filter status=submitted */
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('{$NM} Filter', 'post smoke6b filter', '2026-01-01', '2030-12-31', 'S1', 1, {$ADMIN_ID})");
$IDP3 = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE description = 'post smoke6b filter' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
$G_POSTS[] = $IDP3;
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP3}, 'Ijazah Filter', 'required', 'document', 'PDF', 5120, 1)");
$IJZ3 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting={$IDP3} LIMIT 1")->fetch_assoc()['id_job_posting_requirement'];
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP3}, 'submitted', 1, NOW())");
$APP3 = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_candidate={$KANID} AND id_job_posting={$IDP3} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
$G_APPS[] = $APP3;
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status)
             VALUES ({$APP3}, {$IJZ3}, 'pending_review')");
list($c, , $rawbody) = http('GET', $GLOBALS['BASE'] . '/administrator/career_pelamar?status=submitted&id_job_posting=' . $GLOBALS['IDP3'], array(), null, $COOKIE);
check('pelamar list filter status submitted → kandidat smoke6b', 1, (strpos($rawbody, 'Kandidat Smoke6B') !== false) ? 1 : 0);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/view/' . $APP, array(), null, $COOKIE);
check('pelamar view tahap detail 200', '200', $c);
$stripped2 = preg_replace('#<(script|style)[\s\S]*?</(script|style)>#i', '', $rawbody);
check('pelamar view TIDAK ada note_internal/score mentah', 0,
	((strpos($stripped2, 'psikotes menyusul') !== false) || preg_match('/\b75\.00\b/', $stripped2)) ? 1 : 0);

/* ---------- 12.5 6B.2 HIGH-1: pencarian kandidat tahan SQLi ---------- */
$SQLI_SET = array(
	"' OR 1=1 -- ",
	"' UNION SELECT pass,pass FROM aauth_users -- ",
	"%' OR '1'='1\\",
	'50%_off',
);
foreach ($SQLI_SET as $si => $qs) {
	$url = $BASE . '/administrator/career_kandidat?q=' . rawurlencode($qs);
	list($c, , $rawq) = http('GET', $url, array(), null, $COOKIE);
	$bad = (preg_match('/(Fatal error|Warning\(|Notice:|Undefined (index|variable|property)|A Database Error Occurred|syntax error|SQLSTATE)/i', $rawq)) ? 1 : 0;
	check('SQLi q#' . ($si + 1) . ' 200 bersih', '200:0', $c . ':' . $bad);
}

/* ---------- 13. render halaman bersih notice ---------- */
foreach (array('career_kandidat') as $path) {
	list($c, , $rawbody) = http('GET', $BASE . '/administrator/' . $path, array(), null, $COOKIE);
	$bad = (preg_match('/(Fatal error|Warning\(|Notice:|Undefined (index|variable|property))/i', $rawbody)) ? 1 : 0;
	check('render ' . $path . ' 200 bersih', '200:0', $c . ':' . $bad);
}

/* ---------- 13.5 6B.2: admin TANPA score_view TIDAK melihat skor ---------- */
list($c, $headc, ) = post_login(array('username' => $EMAIL_C, 'password' => $PASS_A));
preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $headc, $mmc);
$sidc = null;
foreach ($mmc[1] as $i => $nmc) { if (trim(strtolower($nmc)) === 'jacos_session') $sidc = rawurldecode($mmc[2][$i]); }
check('login admin view-only 200', 'ya', ($sidc !== null) ? 'ya' : 'tidak');
$COOKIE_C = 'jacos_session=' . rawurlencode((string) $sidc);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/view/' . $APP, array(), null, $COOKIE_C);
check('view pelamar utk view-only 200', '200', $c);
$strippedc = preg_replace('#<(script|style)[\s\S]*?</(script|style)>#i', '', (string) $rawbody);
check('view-only TANPA string skor 75.00', 0, (preg_match('/\b75\.00\b/', $strippedc)) ? 1 : 0);
check('view-only TANPA isi rubric internal', 0, (strpos($strippedc, 'psikotes menyusul') !== false) ? 1 : 0);

/* ====================================================================
   14. Ekstensi 6B.1 (#14 review): GET-mutasi tolak, anonim 403, API
   kandidat tak bocor (D13), jadwal publik muncul, draft 409.
   ==================================================================== */

/* helper JSON (endpoint apicareer kandidat) */
function http_json6b($method, $url, $body, $token = null) {
	$headers = array();
	$payload = null;
	if ($body !== null) {
		$headers[] = 'Content-Type: application/json';
		$payload = json_encode($body);
	}
	if ($token !== null) { $headers[] = 'Authorization: Bearer ' . $token; }
	if (count($headers) === 0) { $headers[] = 'Accept: application/json'; }
	return http($method, $url, $headers, $payload);
}

/* 14a. GET-mutasi semua endpoint tulis ditolak 403 */
foreach (array(
	'/administrator/career_pelamar/verifikasi/' . $AR3,
	'/administrator/career_pelamar/keputusan',
	'/administrator/career_pelamar/koreksi',
	'/administrator/career_pelamar/terima',
	'/administrator/career_pelamar/jadwal',
	'/administrator/career_pelamar/nilai',
) as $getpath) {
	list($gc,) = http('GET', $BASE . $getpath, array(), null, $COOKIE);
	check('GET-mutasi ditolak 403 — ' . substr($getpath, -25), '403', $gc);
}

/* 14b. anonim POST (tanpa cookie) -> 403 */
foreach (array(
	array('/administrator/career_pelamar/verifikasi/' . $AR3, array('keputusan' => 'verified')),
	array('/administrator/career_pelamar/keputusan', array('id_application' => $APP, 'n' => 1, 'keputusan' => 'passed')),
	array('/administrator/career_pelamar/koreksi', array('id_application' => $APP, 'n' => 1, 'keputusan_baru' => 'pending', 'alasan' => 'x')),
	array('/administrator/career_pelamar/nilai', array('id_application_stage' => $STG5, 'score' => '70')),
	array('/administrator/career_kandidat/reset_password/' . $KANID, array()),
) as $anon) {
	list($tc, $thead,) = http('POST', $BASE . $anon[0],
		array('Content-Type: application/x-www-form-urlencoded'), http_build_query($anon[1]), null);
	check('anonim POST 403 — ' . substr($anon[0], -25), '403', $tc);
}

/* 14c. API kandidat tidak bocor (D13): review_note/score/rubric_note
   tidak pernah ada; jadwal terpublikasi muncul di tahap. Token pakai
   login asli dengan sandi hasil reset (server-generate). */
list($c, , $jraw) = http_json6b('POST', $BASE . '/apicareer/Auth/login',
	array('email' => $EMAIL_KAN, 'password' => $SANDI_BARU), null);
$jlogin = json_decode((string) $jraw, true);
$TOK = ($jlogin && isset($jlogin['data']['token'])) ? $jlogin['data']['token'] : '';
check('login kandidat pasca-reset (sandi server-generate)', 1, ($TOK !== '' ? 1 : 0));

list($c, , $jraw) = http_json6b('GET', $BASE . '/apicareer/Lamaran/detail/' . $APP, null, $TOK);
$stripped3 = str_replace('\\u00a0', ' ', (string) $jraw);
check('detail API kandidat 200', '200', $c);
check('detail API TIDAK berisi review_note internal', 0, (strpos($stripped3, 'internal: scan') !== false) ? 1 : 0);
check('detail API TIDAK berisi rubric/note_internal key', 0, (preg_match('/"(rubric_note|note_internal)"\s*:/', $stripped3) === 1) ? 1 : 0);
check('detail API TIDAK berisi konten sensitif', 0,
	((strpos($stripped3, 'psikotes menyusul') !== false) || (strpos($stripped3, '75.00') !== false)) ? 1 : 0);
/* pengulang non-regresi: tahapan kandidat punya jadwal = schedule objek */
check('jadwal terpublikasi ada di detail (D18; bentuk apa adanya)', 1,
	(preg_match('/(schedule|"schedule")[^}]{0,400}/', $stripped3) === 1 && (strpos($stripped3, 'schedule') !== false)) ? 1 : 0);

list($c, , $jraw) = http_json6b('GET', $BASE . '/apicareer/Lamaran/index', null, $TOK);
check('index API TIDAK berisi skor/internal', 0,
	(preg_match('/"(score|note_internal|rubric_note)"\s*:/', (string) $jraw) === 1) ? 1 : 0);
list($c, , $jraw) = http_json6b('GET', $BASE . '/apicareer/Notifikasi/index', null, $TOK);
check('notifikasi API TIDAK berisi skor/internal', 0,
	((strpos((string) $jraw, 'psikotes menyusul') !== false) || (strpos((string) $jraw, '75.00') !== false)) ? 1 : 0);

/* 14d. draft ditolak 409 (keputusan/koreksi/nilai/jadwal) */
$pdo->query("INSERT INTO career_job_postings (title, description, is_published, opening_date, closing_date, created_at, updated_at)
             VALUES ('[DUMMY TEST6B] Draft Guru', 'x', 0, '2025-01-01', '2030-12-31', NOW(), NOW())");
$IDP4 = (int) $pdo->insert_id;
$G_POSTS[] = $IDP4;
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status)
             VALUES ({$KANID}, {$IDP4}, 'draft')");
$APP4 = (int) $pdo->insert_id;
$G_APPS[] = $APP4;
$STG4 = 0;
$res_st = $pdo->query("SELECT id_stage FROM career_stages WHERE is_active = 1 ORDER BY stage_order ASC");
while ($st4 = $res_st->fetch_assoc()) {
	$pdo->query("INSERT INTO career_application_stages (id_application, id_stage, status)
	             VALUES ({$APP4}, " . (int) $st4['id_stage'] . ", 'pending')");
	$STG4 = (int) $pdo->insert_id;
}
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP4, 'n' => 1, 'keputusan' => 'passed'));
check('keputusan pada draft 409', '409', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP4, 'n' => 1, 'keputusan_baru' => 'pending', 'alasan' => 'uji'));
check('koreksi pada draft 409', '409', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/nilai', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STG4, 'score' => '70'));
check('nilai pada draft 409', '409', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/jadwal', array(
	'career_csrf' => $CSRF_P, 'id_application_stage' => $STG4, 'aksi' => 'simpan',
	'schedule_datetime' => '2027-01-01 09:00'));
check('jadwal simpan pada draft 409', '409', $c);

/* 14e. daftar pelamar: opsi Perlu verifikasi + ringkasan status */
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar', array(), null, $COOKIE);
check('opsi filter Perlu verifikasi tersedia', 1, (strpos($rawbody, 'Perlu verifikasi') !== false) ? 1 : 0);
$stripped4 = preg_replace('#<(script|style)[\s\S]*?</(script|style)>#i', '', $rawbody);
check('ringkasan status tampil', 1, (strpos($stripped4, 'Tidak Diterima') !== false) ? 1 : 0);

echo "Ringkasan smoke6b: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
