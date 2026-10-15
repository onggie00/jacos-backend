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
if ($H !== 'localhost' && $H !== '127.0.0.1' && $H !== null && stripos($H, 'jacos.test') === false) {
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
function check($nama, $harap, $terima) {
	global $pass, $fail;
	if (getenv('S6B_TRACE') === '1') { echo "TRACE check '$nama' harap=" . var_export($harap, true) . " terima=" . var_export($terima, true) . "
"; }
	if ((string) $harap === (string) $terima) {
		$pass++; echo "PASS  $nama\n";
	} else {
		$fail++; echo "FAIL  $nama (harap " . print_r($harap, true) . ", terima " . print_r($terima, true) . ")\n";
	}
}
function json_ok($raw) { $j = json_decode($raw, true); return is_array($j) ? $j : null; }

$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }

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
$g = $pdo->query("SELECT id FROM aauth_groups WHERE id <> 1 ORDER BY id LIMIT 1")->fetch_assoc();
$NOGR = $g ? (int) $g['id'] : 0;
if ($NOGR > 0) { $pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$NOID}, {$NOGR})"); }

/* ---------- cleanup shutdown ---------- */
if (getenv('S6B_NOCLEAN') !== '1') {
register_shutdown_function(function () {
	global $pdo, $ADMIN_ID, $NOID;
	// Kandidat dibuat SETIAP RUN (stempel waktu + email unik) — hapus SEMUA
	// baris career bagi fixture smoke6b; jangan tunda id per-run.
	$root = "SELECT id_application FROM career_applications WHERE id_candidate IN
	         (SELECT id_candidate FROM career_candidates WHERE full_name = 'Kandidat Smoke6B')";
	$root2 = "SELECT id_application FROM career_applications WHERE id_job_posting IN
	          (SELECT id_job_posting FROM career_job_postings WHERE title LIKE '[DUMMY TEST6B]%')";
	$pdo->query("DELETE n FROM career_notifications n WHERE n.id_candidate IN
	             (SELECT id_candidate FROM career_candidates WHERE full_name = 'Kandidat Smoke6B')");
	$pdo->query("DELETE e FROM career_evaluation_scores e
	             JOIN career_application_stages s ON s.id_application_stage = e.id_application_stage
	             WHERE s.id_application IN ({$root}) OR s.id_application IN ({$root2})");
	$pdo->query("DELETE FROM career_selection_schedules WHERE id_application_stage IN
	             (SELECT id_application_stage FROM career_application_stages WHERE id_application IN ({$root}) UNION SELECT 0)");
	$pdo->query("DELETE s FROM career_application_stages s WHERE s.id_application IN ({$root}) OR s.id_application IN ({$root2})");
	$pdo->query("DELETE d FROM career_application_documents d WHERE d.id_application IN ({$root}) OR d.id_application IN ({$root2})");
	$pdo->query("DELETE r FROM career_application_requirements r WHERE r.id_application IN ({$root}) OR r.id_application IN ({$root2})");
	$pdo->query("DELETE p FROM career_application_profile_snapshots p WHERE p.id_application IN ({$root}) OR p.id_application IN ({$root2})");
	// MySQL melarang DELETE target yang mereferensi tabel yang sama di
	// FROM-subquery — tulis ulang sebagai JOIN eksplisit.
	$r = $pdo->query("DELETE a FROM career_applications a
	                  JOIN (SELECT c.id_candidate FROM career_candidates c WHERE c.full_name = 'Kandidat Smoke6B') kx ON kx.id_candidate = a.id_candidate
	                  LEFT JOIN career_job_postings px ON px.id_job_posting = a.id_job_posting
	                  WHERE px.id_job_posting IS NULL OR px.title NOT LIKE '[DUMMY TEST6B]%'");
	if ($r === false) { echo "SMOKE6B CLEANUP ERR apps: {$pdo->error}
"; }
	$r = $pdo->query("DELETE a FROM career_applications a JOIN career_job_postings p ON p.id_job_posting = a.id_job_posting
	                  WHERE p.title LIKE '[DUMMY TEST6B]%' OR p.description IN ('post smoke6b jadwal','post smoke6b filter')");
	if ($r === false) { echo "SMOKE6B CLEANUP ERR apps2: {$pdo->error}
"; }
	$pdo->query("DELETE FROM career_job_posting_requirements WHERE id_job_posting IN
	             (SELECT id_job_posting FROM career_job_postings WHERE title LIKE '[DUMMY TEST6B]%')");
	$r = $pdo->query("DELETE FROM career_job_postings WHERE title LIKE '[DUMMY TEST6B]%' OR description IN ('post smoke6b jadwal','post smoke6b filter')");
	if ($r === false) { echo "SMOKE6B CLEANUP ERR postings: {$pdo->error}
"; }
	$pdo->query("DELETE FROM career_candidates WHERE full_name = 'Kandidat Smoke6B'");
	$pdo->query("DELETE FROM aauth_user_to_group WHERE user_id IN ({$ADMIN_ID}, {$NOID})");
	$pdo->query("DELETE FROM aauth_login_attempts WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY)");
	$pdo->query("DELETE FROM aauth_users WHERE id IN ({$ADMIN_ID}, {$NOID})");
});
}

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
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Ijazah', 'required', 'document', 'PDF, JPG', 5120, 1)");
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP}, 'Nama', 'required', 'text', NULL, NULL, 2)");
$REQ1 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 1")->fetch_assoc()['id_job_posting_requirement'];
$REQ2 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND display_order = 2")->fetch_assoc()['id_job_posting_requirement'];

$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
             VALUES ('smoke6b-kandidat-{$STAMP}@example.test', '" . password_hash('SandiLama123', PASSWORD_BCRYPT) . "', 'Kandidat Smoke6B', 1, 1)");
$KANID = (int) $pdo->query("SELECT id_candidate FROM career_candidates WHERE full_name = 'Kandidat Smoke6B' ORDER BY id_candidate DESC LIMIT 1")->fetch_assoc()['id_candidate'];

$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP}, 'submitted', 1, NOW())");
$APP = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_candidate = {$KANID} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];

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
list($c, , $jraw) = post_admin('/administrator/career_kandidat/reset_password/' . $KANID, array(
	'career_csrf' => csrf_get('/administrator/career_kandidat'),
	'password_baru' => 'SandiBaru999',
));
$j = json_ok($jraw);
check('reset password 200 success', 1, ($j !== null && isset($j['success']) && $j['success']) ? 1 : 0);
$HASH_NEW = $pdo->query("SELECT password_hash FROM career_candidates WHERE id_candidate = {$KANID}")->fetch_assoc()['password_hash'];
check('password_hash berubah di DB', 1, ($HASH_NEW !== $HASH_OLD && password_verify('SandiBaru999', $HASH_NEW)) ? 1 : 0);
check('audit reset_password ada', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action='reset_password_kandidat' AND object_id={$KANID}")->fetch_assoc()['n'] === 1) ? 1 : 0);
check('audit reset TIDAK menyimpan sandi', 0,
	(strpos((string) $pdo->query("SELECT detail_json FROM career_audit_logs WHERE action='reset_password_kandidat' AND object_id={$KANID} LIMIT 1")->fetch_assoc()['detail_json'], 'SandiBaru') !== false) ? 1 : 0);

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

/* rejected ulang pada status sama → 422 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR2, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'rejected', 'pesan_kandidat' => 'Jawaban tidak sesuai.'));
check('verifikasi rejected ulang 422', '422', $c);

/* rejected ulang pada status sama → 422 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/verifikasi/' . $AR2, array(
	'career_csrf' => $CSRF_P, 'keputusan' => 'rejected', 'pesan_kandidat' => 'Jawaban tidak sesuai.'));
check('verifikasi rejected ulang 422', '422', $c);

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
check('audit koreksi tidak plaintext alasan', 0, (strpos((string) $kr, 'salah pilih') !== false) ? 1 : 0);
check('audit koreksi ada alasan_len', 1, (strpos((string) $kr, 'alasan_len') !== false) ? 1 : 0);

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
check('koreksi failing (tahap 1) → tahap lain reset pending', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_application_stages WHERE id_application={$APP} AND status IN ('passed','failed') AND id_stage <> {$STG1}")->fetch_assoc()['n'] === 0) ? 1 : 0);

/* pulihkan kembali: tahap 1..5 semua passed lagi (untuk test terima) */
$pdo->query("UPDATE career_application_stages SET status='passed', decided_at=NOW()
             WHERE id_application={$APP} AND id_stage IN ({$STG1}, {$STG2}, {$STG3}, {$stg[3][0]}, {$STG5})");
$pdo->query("UPDATE career_applications SET status='in_process', current_stage_id=NULL WHERE id_application={$APP}");

/* terima sebelum lengkap? bukan — tahap sudah semua passed. Terima valid: */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array('id_application' => $APP));
check('terima tanpa CSRF 403', '403', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array(
	'career_csrf' => $CSRF_P, 'id_application' => 999999));
check('terima lamaran tidak ada 404', '404', $c);
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP));
$j = json_ok($jraw);
check('terima 200 (semua tahap lulus)', 1, ($j !== null && $j['success']) ? 1 : 0);
check('lamaran status accepted', 'accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status']);
check('audit status_akhir_diterima ada', 1, ((int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action='status_akhir_diterima' AND object_id={$APP}")->fetch_assoc()['n'] === 1) ? 1 : 0);
/* keputusan pada lamaran accepted → 409 */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/keputusan', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan' => 'passed'));
check('keputusan pada lamaran accepted 409', '409', $c);
/* koreksi pada lamaran accepted sesuai D16.5: boleh membatalkan status
   akhir → 200, lamaran kembali terkonsistensi (stage1 failed → lamaran
   not_accepted, tahap 2..5 kembali pending) */
list($c, , $jraw) = post_admin('/administrator/career_pelamar/koreksi', array(
	'career_csrf' => $CSRF_P, 'id_application' => $APP, 'n' => 1, 'keputusan_baru' => 'failed', 'alasan' => 'Ulang koreksi.'));
check('koreksi pada lamaran accepted → batalkan status akhir 200', '200', $c);
check('koreksi batalkan accepted → tahap2..5 pending', 1,
	((int) $pdo->query("SELECT COUNT(*) n FROM career_application_stages WHERE id_application={$APP} AND status IN ('passed','failed') AND id_stage <> {$STG1}")->fetch_assoc()['n'] === 0) ? 1 : 0);
check('koreksi batalkan accepted → lamaran not_accepted', 'not_accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status'] ?? '');
/* pulihkan: tahap 1..5 passed lagi + in_process supaya blok berikutnya
   (terima-ulang) tetap menguji jalur yang benar */
$pdo->query("UPDATE career_application_stages SET status='passed', decided_at=NOW() WHERE id_application={$APP} AND id_stage IN ({$STG1}, {$STG2}, {$STG3}, {$stg[3][0]}, {$STG5})");
$pdo->query("UPDATE career_applications SET status='in_process', current_stage_id=NULL WHERE id_application={$APP}");
list($c, , $jraw) = post_admin('/administrator/career_pelamar/terima', array('career_csrf' => $CSRF_P, 'id_application' => $APP));
$j = json_ok($jraw);
check('terima ulang 200 (idempoten pada accepted)', 1, ($j !== null && $j['success']) ? 1 : 0);
check('lamaran tetap accepted setelah terima ulang', 'accepted',
	$pdo->query("SELECT status FROM career_applications WHERE id_application = {$APP}")->fetch_assoc()['status']);

/* ---------- 10. jadwal & nilai (career_pelamar/jadwal + nilai) — lamaran#2 ---------- */
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP}, 'submitted', 1, NOW())");
/* lamaran#2 untuk uji jadwal/nilai: kandidat (satu lamaran per lowongan),
   job_posting lain nutup far-future agar tetap 'submitted' */
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('{$NM}', 'post smoke6b jadwal', '2026-01-01', '2030-12-31', 'S1', 1, {$ADMIN_ID})");
$IDP2 = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE description = 'post smoke6b jadwal' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP2}, 'submitted', 1, NOW())");
$APP2 = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_candidate={$KANID} AND id_job_posting={$IDP2} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
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

/* ---------- 12. pelamar list tetap baca + perlu_verifikasi ---------- */
/* $APP pada titik ini accepted, APP2 sudah in_process (D16.3 setelah
   keputusan tahap 1) → buat lamaran#3 yang masih mentah submitted untuk uji
   filter status=submitted */
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('{$NM} Filter', 'post smoke6b filter', '2026-01-01', '2030-12-31', 'S1', 1, {$ADMIN_ID})");
$IDP3 = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE description = 'post smoke6b filter' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order)
             VALUES ({$IDP3}, 'Ijazah Filter', 'required', 'document', 'PDF', 5120, 1)");
$IJZ3 = (int) $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting={$IDP3} LIMIT 1")->fetch_assoc()['id_job_posting_requirement'];
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, current_stage_id, created_at)
             VALUES ({$KANID}, {$IDP3}, 'submitted', 1, NOW())");
$APP3 = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_candidate={$KANID} AND id_job_posting={$IDP3} ORDER BY id_application DESC LIMIT 1")->fetch_assoc()['id_application'];
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status)
             VALUES ({$APP3}, {$IJZ3}, 'pending_review')");
list($c, , $rawbody) = http('GET', $GLOBALS['BASE'] . '/administrator/career_pelamar?status=submitted&id_job_posting=' . $GLOBALS['IDP3'], array(), null, $COOKIE);
if (getenv('S6B_TRACE') === '1') { echo "TRACE pelamarlist c=$c len=" . strlen($rawbody) . "
"; }
check('pelamar list filter status submitted → kandidat smoke6b', 1, (strpos($rawbody, 'Kandidat Smoke6B') !== false) ? 1 : 0);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/view/' . $APP, array(), null, $COOKIE);
check('pelamar view tahap detail 200', '200', $c);
$stripped2 = preg_replace('#<(script|style)[\s\S]*?</(script|style)>#i', '', $rawbody);
check('pelamar view TIDAK ada note_internal/score mentah', 0,
	((strpos($stripped2, 'psikotes menyusul') !== false) || preg_match('/\b75\.00\b/', $stripped2)) ? 1 : 0);

/* ---------- 13. render halaman bersih notice ---------- */
foreach (array('career_kandidat') as $path) {
	list($c, , $rawbody) = http('GET', $BASE . '/administrator/' . $path, array(), null, $COOKIE);
	$bad = (preg_match('/(Fatal error|Warning\(|Notice:|Undefined (index|variable|property))/i', $rawbody)) ? 1 : 0;
	check('render ' . $path . ' 200 bersih', '200:0', $c . ':' . $bad);
}

echo "Ringkasan smoke6b: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
