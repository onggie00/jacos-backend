<?php
/**
 * Smoke test Fase 6A — modul admin karier (career_template, career_lowongan,
 * career_pelamar, career_audit) di CiCOOL.
 *
 * Pemakaian: php tests/api/smoke6a.php [BASE_ADMIN]
 *   BASE_ADMIN default: http://jacos.test:8080
 * Prasyarat: jacos_db lokal + migration 004 sudah dijalankan (perms + menu).
 * Membuat & menghapus data uji sendiri di jacos_db LOKAL. JANGAN di produksi.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080';

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

function post_form($fields, $cookie, $extra_headers = array()) {
	$headers = array_merge(array('Content-Type: application/x-www-form-urlencoded'), $extra_headers);
	return http('POST', $GLOBALS['BASE'] . '/administrator/login', $headers, http_build_query($fields), $cookie);
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

$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }

/* ---------- 1. fixture admin uji (grup superadmin id=1) ---------- */
$EMAIL_A = 'smoke6a-' . date('ymdHis') . '@example.test';
$PASS_A = 'Rahasia6Aaa';
$hash = password_hash($PASS_A, PASSWORD_BCRYPT);
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_A}', '{$hash}', '" . $pdo->real_escape_string($EMAIL_A) . "', 'Admin Smoke6A', 0, '', '')");
$ADMIN_ID = (int) $pdo->query("SELECT id FROM aauth_users WHERE email = '{$EMAIL_A}'")->fetch_assoc()['id'];
$pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$ADMIN_ID}, 1)");
$pdo->query("DELETE FROM aauth_login_attempts");

/* ---------- 2. login backend + cookie sesi ---------- */
list($c, $head) = post_form(array('username' => $EMAIL_A, 'password' => $PASS_A), null);
check('login backend balik sesi (302/200)', 'ya', ($c === '302' || $c === '200') ? 'ya' : $c);
preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $head, $mm);
$sid_name = 'jacos_session'; $sid = null;
foreach ($mm[1] as $i => $nm) { if (trim(strtolower($nm)) === $sid_name) $sid = rawurldecode($mm[2][$i]); }
check('cookie sesi didapat', 1, $sid !== null ? 1 : 0);
$COOKIE = $sid_name . '=' . rawurlencode($sid);

function csrf_get($path) {
	list($c, , $rawbody) = http('GET', $GLOBALS['BASE'] . $path, array(), null, $GLOBALS['COOKIE']);
	if (preg_match('/name="career_csrf"[^>]*value="([^"]+)"/', $rawbody, $m)) return $m[1];
	if (preg_match('/value="([^"]+)"\s*name="career_csrf"/', $rawbody, $m)) return $m[1];
	return null;
}
function post_admin($path, $fields) {
	$headers = array('Content-Type: application/x-www-form-urlencoded', 'X-Requested-With: XMLHttpRequest');
	return http('POST', $GLOBALS['BASE'] . $path, $headers, http_build_query($fields), $GLOBALS['COOKIE']);
}
function json_ok($raw) { $j = json_decode($raw, true); return is_array($j) ? $j : null; }

/* ---------- 3. career_template: add/edit/delete + D12 ---------- */
list($c, , $jraw) = post_admin('/administrator/career_template/add_save', array(
	'career_csrf' => csrf_get('/administrator/career_template/add'),
	'name' => '[DUMMY SMOKE6A] Template Ijazah',
	'description' => 'ujicoba smoke6a',
	'is_active' => '1',
));
$j = json_ok($jraw);
check('template add_save success', 1, (isset($j['success']) && $j['success']) ? 1 : 0);
$TPL = (int) ($j['id'] ?? 0);
check('template id > 0', 1, $TPL > 0 ? 1 : 0);

list($c, , $jraw) = post_admin('/administrator/career_template/edit_save/' . $TPL, array(
	'career_csrf' => csrf_get('/administrator/career_template/edit/' . $TPL),
	'name' => '[DUMMY SMOKE6A] Template Ijazah X',
	'description' => 'deskripsi e',
	'is_active' => '1',
));
$j = json_ok($jraw);
check('template edit_save success', 1, (isset($j['success']) && $j['success']) ? 1 : 0);
$row = $pdo->query("SELECT name FROM career_requirement_templates WHERE id_requirement_template = {$TPL}")->fetch_assoc();
check('template tersimpan (nama berubah)', '[DUMMY SMOKE6A] Template Ijazah X', $row !== null ? $row['name'] : null);

// D12: lowongan + requirement pakai template ini, lalu delete harus ditolak
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('[DUMMY TEST6A] Skeletal', 'ujicoba', '2026-01-01', '2026-12-31', NULL, 0, {$ADMIN_ID})");
$POST = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE title = '[DUMMY TEST6A] Skeletal' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
$pdo->query("INSERT INTO career_job_posting_requirements (id_job_posting, id_requirement_template, requirement_type, requirement_name, input_type, display_order)
             VALUES ({$POST}, {$TPL}, 'required', 'Ijazah fisik', 'document', 1)");
list($c, $head, $rawbody) = http('GET', $BASE . '/administrator/career_template/delete/' . $TPL, array(), null, $COOKIE);
check('template delete redirect kosong (bukan json success)', '', trim($rawbody));
$ada = (int) $pdo->query("SELECT COUNT(*) n FROM career_requirement_templates WHERE id_requirement_template = {$TPL}")->fetch_assoc()['n'];
check('template masih ada (tidak dihapus)', 1, $ada === 1 ? 1 : 0);

/* ---------- 4. career_lowongan: add_save + variasi ---------- */
list($c, , $jraw) = post_admin('/administrator/career_lowongan/add_save', array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/add'),
	'title' => '[DUMMY TEST6A] Posting Smoke',
	'description' => 'deskripsi lowongan uji',
	'opening_date' => '2026-01-01',
	'closing_date' => '2026-12-31',
	'education_level' => 'S1',
	'is_published' => '0',
	'requirement_name' => array('Ijazah', 'Alasan'),
	'requirement_type' => array('required', 'optional'),
	'input_type' => array('document', 'text'),
	'allowed_formats' => array('PDF, JPG', ''),
	'max_file_size_kb' => array('5120', ''),
));
$j = json_ok($jraw);
check('lowongan add_save success', 1, (isset($j['success']) && $j['success']) ? 1 : 0);
$IDP = (int) ($j['id'] ?? 0);
check('lowongan id > 0', 1, $IDP > 0 ? 1 : 0);
$n = (int) $pdo->query("SELECT COUNT(*) n FROM career_job_posting_requirements WHERE id_job_posting = {$IDP}")->fetch_assoc()['n'];
check('lowongan ada 2 persyaratan', 2, $n);

// validasi gagal: tutup sebelum buka
list($c, , $jraw) = post_admin('/administrator/career_lowongan/add_save', array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/add'),
	'title' => '[DUMMY TEST6A] Salaha',
	'opening_date' => '2026-12-01',
	'closing_date' => '2026-01-01',
	'education_level' => 'S1',
	'is_published' => '1',
	'requirement_name' => array('Ijazah'),
	'input_type' => array('text'),
));
$j = json_ok($jraw);
check('lowongan add_save tanggal terbalik ditolak', 1, ($j !== null && isset($j['success']) && $j['success'] === false) ? 1 : 0);

// terbit tanpa persyaratan -> ditolak
list($c, , $jraw) = post_admin('/administrator/career_lowongan/add_save', array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/add'),
	'title' => '[DUMMY TEST6A] Tanpa Req',
	'opening_date' => '2026-01-01',
	'closing_date' => '2026-12-31',
	'education_level' => 'S1',
	'is_published' => '1',
	'requirement_name' => array(),
));
$j = json_ok($jraw);
check('lowongan terbit tanpa persyaratan ditolak', 1, ($j !== null && isset($j['success']) && $j['success'] === false) ? 1 : 0);

// select tipe ditolak
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_add/' . $IDP, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP),
	'requirement_type' => 'required',
	'input_type' => 'select',
	'requirement_name' => 'Pilihan Warna',
));
$j = json_ok($jraw);
check('requirement_add tipe select ditolak (belum didukung)', 1, ($j !== null && isset($j['success']) && $j['success'] === false) ? 1 : 0);

/* ---------- 5. D12 lowongan: hapus dgn lamaran -> ditolak ---------- */
// kandidat uji (dibuat sebelum lamaran: kolom id_candidate wajib)
$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version, failed_login_count)
             VALUES ('smoke6a-kandidat-" . date('ymdHis') . "@example.test', 'x', 'Kandidat Smoke6A', 1, 1, 0)");
$KANID = (int) $pdo->query("SELECT id_candidate FROM career_candidates WHERE full_name = 'Kandidat Smoke6A' ORDER BY id_candidate DESC LIMIT 1")->fetch_assoc()['id_candidate'];
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, created_at)
             VALUES ({$KANID}, {$IDP}, 'draft', NOW())");
$APP = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_job_posting = {$IDP} LIMIT 1")->fetch_assoc()['id_application'];
list($c, $head, $rawbody) = http('GET', $BASE . '/administrator/career_lowongan/delete/' . $IDP, array(), null, $COOKIE);
check('lowongan punya lamaran tidak boleh dihapus (redirect)', '', trim($rawbody));
$ada = (int) $pdo->query("SELECT COUNT(*) n FROM career_job_postings WHERE id_job_posting = {$IDP}")->fetch_assoc()['n'];
check('lowongan masih ada setelah delete', 1, $ada === 1 ? 1 : 0);

/* ---------- 6. career_pelamar: list + detail + unduh ---------- */

// snap_update: buat snapshot profil (kolom application_profile_snapshots)
$snap = json_encode(array(
	'profile' => array('phone' => '0812', 'address' => 'Jl. Test'),
	'candidate' => array('full_name' => 'Kandidat Smoke6A', 'email' => 'smoke6a-kandidat@example.test'),
	'items' => array(),
));
$pdo->query("INSERT INTO career_application_profile_snapshots (id_application, profile_json, snapshot_at)
             VALUES ({$APP}, '{$snap}', NOW())");

// requirements dgn dokumen (file uji di storage).
$req = $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND input_type = 'document'")->fetch_assoc();
$REQJ = (int) $req['id_job_posting_requirement'];
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status)
             VALUES ({$APP}, {$REQJ}, 'pending_review')");
$AR = (int) $pdo->query("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP} LIMIT 1")->fetch_assoc()['id_application_requirement'];

$DIR = __DIR__ . '/../../application/storage/career';
@mkdir($DIR, 0777, true);
$FILEPATH = 'application/storage/career/smoke6a-' . getmypid() . '.pdf';
file_put_contents(__DIR__ . '/../../application/storage/career/smoke6a-' . getmypid() . '.pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Size 1/Root 1 0 R>>\n%%EOF\n");
$pdo->query("INSERT INTO career_application_documents (id_application_requirement, id_application, version, file_path, original_name, mime_type, file_size, checksum_sha256, uploaded_at)
             VALUES ({$AR}, {$APP}, 1, '{$FILEPATH}', 'smoke6a.pdf', 'application/pdf', 60, SHA2('smoke6a', 256), NOW())");
$DOC = (int) $pdo->query("SELECT id_application_document FROM career_application_documents WHERE id_application = {$APP} ORDER BY id_application_document DESC LIMIT 1")->fetch_assoc()['id_application_document'];

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar', array(), null, $COOKIE);
check('pelamar list 200', 200, $c);
check('pelamar list tampil kandidat uji', 1, (strpos($rawbody, 'Kandidat Smoke6A') !== false) ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar?status=submitted&q=Blah', array(), null, $COOKIE);
check('pelamar list filter status bukan kandidat uji', 0, (strpos($rawbody, 'Kandidat Smoke6A') !== false) ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/view/' . $APP, array(), null, $COOKIE);
check('pelamar view 200', 200, $c);
check('pelamar view punya nama kandidat', 1, (strpos($rawbody, 'Kandidat Smoke6A') !== false) ? 1 : 0);
check('pelamar view tautan unduh dokumen', 1, (strpos($rawbody, 'career_pelamar/unduh/' . $DOC) !== false) ? 1 : 0);

list($c, $head, $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/unduh/' . $DOC . '?app=' . $APP, array(), null, $COOKIE);
check('pelamar unduh 200', 200, $c);
$ct = null; $nosniff = null;
if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $head, $mm)) $ct = trim($mm[1]);
if (preg_match('/X-Content-Type-Options:\s*([^\r\n]+)/i', $head, $mm)) $nosniff = trim($mm[1]);
check('pelamar unduh Content-Type application/pdf', 'application/pdf', preg_replace('/\s*;.*$/', '', (string) $ct));
check('pelamar unduh nosniff', 'nosniff', $nosniff);
check('pelamar unduh body persis pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Size 1/Root 1 0 R>>\n%%EOF\n", $rawbody);

/* ---------- 7. career_audit: list + filter ---------- */
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_audit', array(), null, $COOKIE);
check('audit list 200', 200, $c);
check('audit list ada aksi create_template', 1, (strpos($rawbody, 'create_template') !== false) ? 1 : 0);

$sqlfilter = "SELECT action, detail_json FROM career_audit_logs
              WHERE object_type = 'career_job_posting' AND object_id = {$IDP}
              ORDER BY id_audit_log";
check('audit log lowongan create ada', 1, ($pdo->query($sqlfilter)->fetch_assoc()['action'] === 'create_lowongan') ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_audit?object_type=career_job_posting&object_id=' . $IDP, array(), null, $COOKIE);
check('audit filter object_id tampilkan', 1, (strpos($rawbody, '[DUMMY TEST6A] Posting Smoke') !== false) ? 1 : 0);

/* ---------- cleanup ---------- */
$pdo->query("DELETE d FROM career_application_documents d WHERE d.id_application = {$APP}");
$pdo->query("DELETE r FROM career_application_requirements r WHERE r.id_application = {$APP}");
$pdo->query("DELETE s FROM career_application_profile_snapshots s WHERE s.id_application = {$APP}");
$pdo->query("DELETE FROM career_applications WHERE id_application = {$APP}");
$pdo->query("DELETE FROM career_job_posting_requirements WHERE id_job_posting = {$IDP}");
$pdo->query("DELETE FROM career_job_postings WHERE id_job_posting = {$IDP} OR id_job_posting = {$POST}");
$pdo->query("DELETE FROM career_requirement_templates WHERE id_requirement_template = {$TPL}");
$pdo->query("DELETE FROM career_candidates WHERE id_candidate = {$KANID}");
$pdo->query("DELETE FROM aauth_user_to_group WHERE user_id = {$ADMIN_ID}");
$pdo->query("DELETE FROM aauth_login_attempts");
$pdo->query("DELETE FROM aauth_users WHERE id = {$ADMIN_ID}");
@unlink(__DIR__ . '/../../application/storage/career/smoke6a-' . getmypid() . '.pdf');

echo "Ringkasan smoke6a: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
