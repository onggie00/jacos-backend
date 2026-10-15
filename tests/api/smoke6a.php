<?php
/**
 * Smoke test Fase 6A (revisi 6A.1) — modul admin karier CiCOOL.
 *
 * Pemakaian: php tests/api/smoke6a.php [BASE_ADMIN]
 *   BASE_ADMIN default: http://jacos.test:8080
 * Prasyarat: jacos_db LOKAL + migration 004 sudah dijalankan (perms + menu). Ketik DILARANG.
 * Membuat & menghapus data uji sendiri (register_shutdown_service). DILARANG dipakai di produksi.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080';

/* DILARANG jalankan ke host/db non-lokal. */
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
$PASS_A  = 'Rahasia6Aaa';
$hash = password_hash($PASS_A, PASSWORD_BCRYPT);
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_A}', '{$hash}', '" . $pdo->real_escape_string($EMAIL_A) . "', 'Admin Smoke6A', 0, '', '')");
$ADMIN_ID = (int) $pdo->query("SELECT id FROM aauth_users WHERE email = '${EMAIL_A}'")->fetch_assoc()['id'];
$pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$ADMIN_ID}, 1)");

/* fixture admin TANPA perm career (grup id 2 kalau ada; login tetap sah). */
$EMAIL_B = 'smoke6a-b-' . date('ymdHis') . '@example.test';
$UNAME_B = 's6b' . date('ymdHis');
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('${EMAIL_B}', '{$hash}', '${UNAME_B}', 'Admin TanpaIzin', 0, '', '')");
$res_b = $pdo->query("SELECT id FROM aauth_users WHERE email = '${EMAIL_B}' AND banned = 0");
if ($res_b === false) { echo 'FAIL select EmailB: ', $pdo->error, "\n"; exit(1); }
$NOID = (int) $res_b->fetch_assoc()['id'];
$g = $pdo->query("SELECT id FROM aauth_groups WHERE id <> 1 ORDER BY id LIMIT 1")->fetch_assoc();
$NOGR = $g ? (int) $g['id'] : 0;
if ($NOGR > 0) { $pdo->query("INSERT INTO aauth_user_to_group (user_id, group_id) VALUES ({$NOID}, {$NOGR})"); }

/* ---------- cleanup otomatis via shutdown (S6A_NOCLEAN=1 utk debug) ---------- */
if (getenv('S6A_NOCLEAN') !== '1') {
register_shutdown_function(function () use ($pdo, $ADMIN_ID, $NOID) {
	$pdo->query("DELETE d FROM career_application_documents d
	             JOIN career_application_requirements r ON r.id_application_requirement = d.id_application_requirement
	             JOIN career_applications a ON a.id_application = r.id_application
	             JOIN career_job_posting_requirements jr ON jr.id_job_posting_requirement = r.id_job_posting_requirement
	             WHERE (a.id_candidate IN (SELECT id_candidate FROM career_candidates WHERE full_name IN ('Kandidat Smoke6A','Kandidat Smoke6A2'))
	                OR jr.id_job_posting IN (SELECT id_job_posting FROM career_job_postings WHERE title LIKE '[DUMMY TEST6A]%'))");
	$pdo->query("DELETE r FROM career_application_requirements r
	             JOIN career_applications a ON a.id_application = r.id_application
	             WHERE a.id_candidate IN (SELECT id_candidate FROM career_candidates WHERE full_name IN ('Kandidat Smoke6A','Kandidat Smoke6A2'))
	                OR a.id_job_posting IN (SELECT id_job_posting FROM career_job_postings WHERE title LIKE '[DUMMY TEST6A]%')");
	$pdo->query("DELETE s FROM career_application_profile_snapshots s
	             WHERE s.id_application NOT IN (SELECT id_application FROM career_applications)");
	$pdo->query("DELETE x FROM career_application_stages x
	             WHERE x.id_application NOT IN (SELECT id_application FROM career_applications)");
	$pdo->query("DELETE a FROM career_applications a
	             JOIN career_candidates c ON c.id_candidate = a.id_candidate
	             WHERE c.full_name IN ('Kandidat Smoke6A','Kandidat Smoke6A2')");
	$pdo->query("DELETE r FROM career_job_posting_requirements r
	             JOIN career_job_postings p ON p.id_job_posting = r.id_job_posting
	             WHERE p.title LIKE '[DUMMY TEST6A]%'");
	$pdo->query("DELETE p FROM career_job_postings p
	             JOIN career_job_postings p2 ON p2.id_job_posting = p.id_job_posting
	             WHERE p2.title LIKE '[DUMMY TEST6A]%'");
	$pdo->query("DELETE FROM career_requirement_templates WHERE name LIKE '[DUMMY SMOKE6A]%'");
	$pdo->query("DELETE FROM aauth_user_to_group WHERE user_id IN ({$ADMIN_ID}, {$NOID})");
	$pdo->query("DELETE FROM aauth_login_attempts WHERE timestamp > DATE_SUB(NOW(), INTERVAL 1 DAY)");
	$pdo->query("DELETE FROM aauth_users WHERE id IN ({$ADMIN_ID}, {$NOID})");
});
}

/* ---------- 2. login backend + cookie sesi ---------- */
function post_login($fields, $email_type = 'ADMIN') {
	$headers = array('Content-Type: application/x-www-form-urlencoded');
	return http('POST', $GLOBALS['BASE'] . '/administrator/login', $headers, http_build_query($fields), null);
}
list($c, $head, $rawbody) = post_login(array('username' => $EMAIL_A, 'password' => $PASS_A));
check('login backend balik sesi (302/200)', 'ya', ($c === '302' || $c === '200') ? 'ya' : $c);
preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $head, $mm);
$sid_name = 'jacos_session'; $sid = null;
foreach ($mm[1] as $i => $nm) { if (trim(strtolower($nm)) === $sid_name) $sid = rawurldecode($mm[2][$i]); }
check('cookie sesi didapat', 1, $sid !== null ? 1 : 0);
$COOKIE = $sid_name . '=' . rawurlencode($sid);

/* anonim (tanpa cookie): unduh harus 403 + BUKAN isi dokumen. */
function csrf_get($path) {
	list($c, , $rawbody) = http('GET', $GLOBALS['BASE'] . $path, array(), null, $GLOBALS['COOKIE']);
	if (preg_match('/name="career_csrf"[^>]*value="([^"]+)"/', $rawbody, $m)) return $m[1];
	if (preg_match('/value="([^"]+)"\s*name="career_csrf"/', $rawbody, $m)) return $m[1];
	return null;
}
function post_admin($path, $fields, $override_cookie = null) {
	$headers = array('Content-Type: application/x-www-form-urlencoded', 'X-Requested-With: XMLHttpRequest');
	return http('POST', $GLOBALS['BASE'] . $path, $headers, http_build_query($fields), $override_cookie !== null ? $override_cookie : $GLOBALS['COOKIE']);
}
function json_ok($raw) { $j = json_decode($raw, true); return is_array($j) ? $j : null; }

/* ---------- 2b. otorisasi: anonim & admin tanpa izin ---------- */
list($c, $head, $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/unduh/1?app=1', array(), null, null);
check('unduh tanpa cookie 403 (bukan dokumen)', '403', $c);
check('unduh tanpa cookie body bukan PDF', 0, (strpos($rawbody, '%PDF') === 0) ? 1 : 0);

// fixture admin tanpa izin: login
list($c, $head2) = post_login(array('username' => $EMAIL_B, 'password' => $PASS_A));
preg_match_all('/Set-Cookie:\s*([^;]+)=([^;]*)/i', $head2, $mm2);
$sid2 = null;
foreach ($mm2[1] as $i => $nm) { if (trim(strtolower($nm)) === 'jacos_session') $sid2 = rawurldecode($mm2[2][$i]); }
$COOKIE_NO = $sid2 !== null ? 'jacos_session=' . rawurlencode($sid2) : null;
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/unduh/1?app=1', array(), null, $COOKIE_NO);
check('unduh admin tanpa izin 403', '403', $c);
check('unduh admin tanpa izin body bukan PDF', 0, (strpos($rawbody, '%PDF') === 0) ? 1 : 0);

/* ---------- 3. career_template: add/edit/delete + D12 ---------- */
list($c, $head, $jraw) = post_admin('/administrator/career_template/add_save', array(
	'career_csrf' => csrf_get('/administrator/career_template/add'),
	'name' => '[DUMMY SMOKE6A] Template Ijazah',
	'description' => 'ujicoba smoke6a',
	'is_active' => '1',
));
$j = json_ok($jraw);
check('template add_save success', 1, (isset($j['success']) && $j['success']) ? 1 : 0);
$TPL = (int) ($j['id'] ?? 0);
check('template id > 0', 1, $TPL > 0 ? 1 : 0);
check('template add CT application/json', 'application/json',
	(preg_match('/Content-Type:\s*application\/json/i', $head) ? 'application/json' : 'bukan'));

list($c, , $jraw) = post_admin('/administrator/career_template/edit_save/' . $TPL, array(
	'career_csrf' => csrf_get('/administrator/career_template/edit/' . $TPL),
	'name' => '[DUMMY SMOKE6A] Template Ijazah X',
	'description' => 'deskripsi-e',
	'is_active' => '1',
));
$j = json_ok($jraw);
check('template edit_save success', 1, (isset($j['success']) && $j['success']) ? 1 : 0);
$row = $pdo->query("SELECT name FROM career_requirement_templates WHERE id_requirement_template = {$TPL}")->fetch_assoc();
check('template tersimpan (nama berubah)', '[DUMMY SMOKE6A] Template Ijazah X', $row !== null ? $row['name'] : null);
$adaudit = (int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action = 'update_template'
             AND object_type = 'career_requirement_template' AND object_id = {$TPL}
             AND detail_json LIKE '%description%'")->fetch_assoc()['n'];
check('audit update_template sebelum/sesudah deskripsi', 1, ($adaudit >= 1) ? 1 : 0);

/* ---------- 3b. CSRF: tanpa token / salah token / POST-only ---------- */
list($c, , ) = post_admin('/administrator/career_template/add_save', array(
	'name' => 'no-token-x', 'is_active' => '1'));
check('template add_save tanpa CSRF 403', '403', $c);
list($c, , ) = post_admin('/administrator/career_template/add_save', array(
	'career_csrf' => 'salah-token', 'name' => 'bad-token-x', 'is_active' => '1'));
check('template add_save CSRF salah 403', '403', $c);
list($c, , ) = http('GET', $BASE . '/administrator/career_template/delete/' . $TPL, array(), null, $COOKIE);
check('template delete via GET ditolak', '403', $c);
list($c, , ) = http('GET', $BASE . '/administrator/career_lowongan/delete/1', array(), null, $COOKIE);
check('lowongan delete via GET ditolak', '403', $c);
list($c, , ) = http('GET', $BASE . '/administrator/career_lowongan/requirement_delete/1', array(), null, $COOKIE);
check('requirement_delete via GET ditolak', '403', $c);
$ada0 = (int) $pdo->query("SELECT COUNT(*) n FROM career_requirement_templates WHERE id_requirement_template = {$TPL}")->fetch_assoc()['n'];
check('template tdk terhapus oleh GET', 1, $ada0 === 1 ? 1 : 0);

/* ---------- 4. career_lowongan: add_save + edit + validasi ---------- */
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
	'allowed_formats' => array('PDF, JPG', 'PDF, JPG'),
	'max_file_size_kb' => array('5120', '5120'),
));
$j = json_ok($jraw);
check('lowongan add_save success', 1, (isset($j['success']) && $j['success']) ? 1 : 0);
$IDP = (int) ($j['id'] ?? 0);
check('lowongan id > 0', 1, $IDP > 0 ? 1 : 0);
$n = (int) $pdo->query("SELECT COUNT(*) n FROM career_job_posting_requirements WHERE id_job_posting = {$IDP}")->fetch_assoc()['n'];
check('lowongan ada 2 persyaratan', 2, $n);
list($AR1, $AR2) = array_values(array_map(function($r){return (int)$r['id_job_posting_requirement']; },
	$pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} ORDER BY display_order, id_job_posting_requirement")->fetch_all(MYSQLI_ASSOC)));

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

// terbit (add) tanpa persyaratan ditolak
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
check('lowongan terbit (add) tanpa persyr ditolak', 1, ($j !== null && isset($j['success']) && $j['success'] === false) ? 1 : 0);

// HIGH #3: draf->terbit via edit_save tanpa persyr ditolak
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, education_level, is_published, created_by)
             VALUES ('[DUMMY TEST6A] TanpaReq2', 'draf kosong', '2026-01-01', '2026-12-31', NULL, 0, {$ADMIN_ID})");
$IDP2 = (int) $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE title = '[DUMMY TEST6A] TanpaReq2' ORDER BY id_job_posting DESC LIMIT 1")->fetch_assoc()['id_job_posting'];
list($c, , $jraw) = post_admin('/administrator/career_lowongan/edit_save/' . $IDP2, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP2),
	'title' => '[DUMMY TEST6A] TanpaReq2',
	'description' => 'x',
	'opening_date' => '2026-01-01',
	'closing_date' => '2026-12-31',
	'is_published' => '1',
));
$j = json_ok($jraw);
check('draf->terbit edit_save tanpa persyr ditolak', 1, ($j !== null && isset($j['success']) && $j['success'] === false) ? 1 : 0);
list($c, ) = post_admin('/administrator/career_lowongan/terbitkan/' . $IDP2, array('career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP2)));
$j = json_ok($jraw ?? '');
check('terbitkan explicit tanpa persyr ditolak', 1, ($j !== null && $j['success'] === false) ? 1 : 0);

// select tipe ditolak dgn pesan seragam
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_add/' . $IDP, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP),
	'requirement_type' => 'required',
	'input_type' => 'select',
	'requirement_name' => 'Pilihan Warna',
));
$j = json_ok($jraw);
check('requirement_add tipe select ditolak (belum didukung)', 1, ($j !== null && isset($j['success']) && $j['success'] === false) ? 1 : 0);

// HIGH #4: form action add/edit mengarah ke *_save
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_lowongan/add', array(), null, $COOKIE);
check('form add->action add_save', 1, (strpos($rawbody, 'career_lowongan/add_save') !== false) ? 1 : 0);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_lowongan/edit/' . $IDP, array(), null, $COOKIE);
check('form ubah->action edit_save/$id', 1, (strpos($rawbody, 'career_lowongan/edit_save/' . $IDP) !== false) ? 1 : 0);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_template/add', array(), null, $COOKIE);
check('form template add->add_save', 1, (strpos($rawbody, 'career_template/add_save') !== false) ? 1 : 0);
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_template/edit/' . $TPL, array(), null, $COOKIE);
check('form template ubah->edit_save', 1, (strpos($rawbody, 'career_template/edit_save/' . $TPL) !== false) ? 1 : 0);

/* ---------- 5. D12 lowongan: hapus dgn lamaran -> ditolak ---------- */
$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version, failed_login_count)
             VALUES ('smoke6a-kandidat-" . date('ymdHis') . "@example.test', 'x', 'Kandidat Smoke6A', 1, 1, 0)");
$KANID = (int) $pdo->query("SELECT id_candidate FROM career_candidates WHERE full_name = 'Kandidat Smoke6A' ORDER BY id_candidate DESC LIMIT 1")->fetch_assoc()['id_candidate'];
$pdo->query("INSERT INTO career_applications (id_candidate, id_job_posting, status, created_at)
             VALUES ({$KANID}, {$IDP}, 'draft', NOW())");
$APP = (int) $pdo->query("SELECT id_application FROM career_applications WHERE id_job_posting = {$IDP} LIMIT 1")->fetch_assoc()['id_application'];
list($c, ) = post_admin('/administrator/career_lowongan/delete/' . $IDP, array('career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP)));
$j = json_ok($jraw ?? '');
check('lowongan berlamaran hapus POST ditolak (success=false)', 1, ($j !== null && $j['success'] === false) ? 1 : 0);
$ada = (int) $pdo->query("SELECT COUNT(*) n FROM career_job_postings WHERE id_job_posting = {$IDP}")->fetch_assoc()['n'];
check('lowongan masih ada setelah delete', 1, $ada === 1 ? 1 : 0);

/* D12 requirement: hapus + ubah input_type pd yang sudah dipakai ditolak. */
$pdo->query("INSERT INTO career_application_requirements (id_application, id_job_posting_requirement, status)
             VALUES ({$APP}, {$AR1}, 'pending_review')");
$ARAPP = (int) $pdo->query("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$APP} AND id_job_posting_requirement = {$AR1}")->fetch_assoc()['id_application_requirement'];
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_delete/' . $AR1, array('career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP)));
$j = json_ok($jraw);
check('D12 requirement terpakai hapus ditolak', 1, ($j !== null && $j['success'] === false) ? 1 : 0);
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_update/' . $AR1, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP),
	'requirement_name' => 'Ijazah X',
	'requirement_type' => 'required',
	'input_type' => 'text',
));
$j = json_ok($jraw);
check('D12 requirement terpakai ubah input_type ditolak', 1, ($j !== null && $j['success'] === false) ? 1 : 0);
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_update/' . $AR1, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP),
	'requirement_name' => 'Ijazah X',
	'requirement_type' => 'required',
	'input_type' => 'document',
	'allowed_formats' => 'PDF',
	'max_file_size_kb' => '5120',
));
$j = json_ok($jraw);
check('D12 requirement terpakai ubah nama/formats OK', 1, ($j !== null && $j['success']) ? 1 : 0);
$nm = $pdo->query("SELECT requirement_name FROM career_job_posting_requirements WHERE id_job_posting_requirement = {$AR1}")->fetch_assoc()['requirement_name'];
check('nama requirement tersimpan (ubah nama)', 'Ijazah X', $nm);
$ord = (int) $pdo->query("SELECT display_order FROM career_job_posting_requirements WHERE id_job_posting_requirement = {$AR1}")->fetch_assoc()['display_order'];
check('display_order dipertahankan saat ubah (#6)', $ord > 0 ? 1 : 0, 1);

/* MED #12: requirement_add WAJIB di lowongan berlamaran perlu konfirmasi */
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_add/' . $IDP, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP),
	'requirement_type' => 'required',
	'input_type' => 'text',
	'requirement_name' => '[DUMMY TEST6A] Kekonfirmasi',
));
$j = json_ok($jraw);
check('req tambah WAJIB di berlamaran tanpa konfirmasi ditolak', 1, ($j !== null && $j['success'] === false) ? 1 : 0);
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_add/' . $IDP, array(
	'career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP),
	'requirement_type' => 'required',
	'input_type' => 'text',
	'requirement_name' => '[DUMMY TEST6A] Kekonfirmasi',
	'konfirmasi' => '1',
));
$j = json_ok($jraw);
check('req tambah WAJIB dgn konfirmasi=1 diterima', 1, ($j !== null && $j['success']) ? 1 : 0);

/* requirement_move: naik/turun swap tetangga (POST + CSRF).
   Reset urutan dulu supaya asersi deterministik (run berulang tanpa cleanup
   bisa menggeser display_order dari test sebelumnya). */
$pdo->query("UPDATE career_job_posting_requirements SET display_order = IF(id_job_posting_requirement = {$AR1}, 1, IF(id_job_posting_requirement = {$AR2}, 2, display_order)) WHERE id_job_posting = {$IDP}");
list($c, , $jraw) = post_admin('/administrator/career_lowongan/requirement_move/' . $AR2, array('career_csrf' => csrf_get('/administrator/career_lowongan/edit/' . $IDP), 'dir' => 'up'));
$j = json_ok($jraw);
check('requirement_move POST success', 1, ($j !== null && $j['success']) ? 1 : 0);
list($o1, $o2) = array_map('intval', call_user_func_array('array_merge', $pdo->query("SELECT display_order FROM career_job_posting_requirements WHERE id_job_posting_requirement IN ({$AR1},{$AR2}) ORDER BY id_job_posting_requirement")->fetch_all(MYSQLI_NUM)));
check('move: persyr lebih tinggi di atas', $o2 < $o1 ? 1 : 0, 1);

/* ---------- 6. career_pelamar: list + detail + unduh + kebersihan ---------- */
$snap = json_encode(array(
	'profile' => array('phone' => '0812', 'address' => 'Jl. Test'),
	'candidate' => array('full_name' => 'Kandidat Smoke6A', 'email' => 'smoke6a-kandidat@example.test'),
	'items' => array(),
));
$pdo->query("INSERT INTO career_application_profile_snapshots (id_application, profile_json, snapshot_at)
             VALUES ({$APP}, '" . $pdo->real_escape_string($snap) . "', NOW())");

$req = $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$IDP} AND input_type = 'document' ORDER BY id_job_posting_requirement LIMIT 1")->fetch_assoc();
$REQJ = (int) $req['id_job_posting_requirement'];

$DIR = __DIR__ . '/../../application/storage/career';
@mkdir($DIR, 0777, true);
$PID = getmypid();
$FILEPATH = 'application/storage/career/smoke6a-' . $PID . '.pdf';
file_put_contents(__DIR__ . '/../../' . $FILEPATH, "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Size 1/Root 1 0 R>>\n%%EOF\n");
$pdo->query("INSERT INTO career_application_documents (id_application_requirement, id_application, version, file_path, original_name, mime_type, file_size, checksum_sha256, uploaded_at)
             VALUES ({$ARAPP}, {$APP}, 1, '{$FILEPATH}', 'smoke6a.pdf', 'application/pdf', 60, SHA2('smoke6a', 256), NOW())");
$DOC = (int) $pdo->query("SELECT id_application_document FROM career_application_documents WHERE id_application_requirement = {$ARAPP} ORDER BY id_application_document DESC LIMIT 1")->fetch_assoc()['id_application_document'];

/* path traversal: file_path dgn .. DILATI */
$pdo->query("INSERT INTO career_application_documents (id_application_requirement, id_application, version, file_path, original_name, mime_type, file_size, checksum_sha256, uploaded_at)
             VALUES ({$ARAPP}, {$APP}, 1, 'application/../careers/dbg.pdf', 'trav.pdf', 'application/pdf', 60, SHA2('x',256), NOW())");
$DOCTRAV = (int) $pdo->query("SELECT id_application_document FROM career_application_documents WHERE file_path LIKE 'application/../%' LIMIT 1")->fetch_assoc()['id_application_document'];
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/unduh/' . $DOCTRAV . '?app=' . $APP, array(), null, $COOKIE);
check('unduh path traversal ditolak (bukan 200 dokumen)', 1, ($c !== '200' || strpos($rawbody, '%PDF-1.4') === 0) ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar', array(), null, $COOKIE);
check('pelamar list 200', 200, $c);
check('pelamar list tampil kandidat uji', 1, (strpos($rawbody, 'Kandidat Smoke6A') !== false) ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar?status=submitted&q=Blah', array(), null, $COOKIE);
check('pelamar list filter status bukan kandidat uji', 0, (strpos($rawbody, 'Kandidat Smoke6A') !== false) ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/view/' . $APP, array(), null, $COOKIE);
check('pelamar view 200', 200, $c);
check('pelamar view punya nama kandidat', 1, (strpos($rawbody, 'Kandidat Smoke6A') !== false) ? 1 : 0);
check('pelamar view tautan unduh dokumen', 1, (strpos($rawbody, 'career_pelamar/unduh/' . $DOC) !== false) ? 1 : 0);
// Debugbar dev (ENVIRONMENT != production) melekat config aauth "use_password_hash"
// di dalam tag <script> — strip script+style dulu supaya asersi melihat markup nyata.
$stripped = preg_replace('#<(script|style)[\s\S]*?</(script|style)>#i', '', $rawbody);
check('pelamar view TANPA password_hash', 0, (strpos($stripped, 'password_hash') !== false) ? 1 : 0);
# Hapus buffer untuk pastikan body unduh SELALU milik respons terakhir */
$rawbody = '';

list($c, $head, $rawbody) = http('GET', $BASE . '/administrator/career_pelamar/unduh/' . $DOC . '?app=' . $APP, array(), null, $COOKIE);
check('pelamar unduh 200', 200, $c);
$ct = null; $nosniff = null; $cdn = null; $cl = null;
if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $head, $mm)) $ct = trim($mm[1]);
if (preg_match('/X-Content-Type-Options:\s*([^\r\n]+)/i', $head, $mm)) $nosniff = trim($mm[1]);
if (preg_match('/Content-Disposition:\s*([^\r\n]+)/i', $head, $mm)) $cdn = trim($mm[1]);
if (preg_match('/Content-Length:\s*([^\r\n]+)/i', $head, $mm)) $cl = trim($mm[1]);
check('pelamar unduh Content-Type application/pdf', 'application/pdf', preg_replace('/\s*;.*$/', '', (string) $ct));
check('pelamar unduh nosniff', 'nosniff', $nosniff);
check('pelamar unduh Content-Disposition attachment', 1, (strpos((string) $cdn, 'attachment') === 0) ? 1 : 0);
check('pelamar unduh Content-Length > 0', 1, ((int) $cl > 0) ? 1 : 0);
check('pelamar unduh body persis pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Size 1/Root 1 0 R>>\n%%EOF\n", $rawbody);

/* ---------- 7. career_audit: list + filter + isi actor ---------- */
list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_audit', array(), null, $COOKIE);
check('audit list 200', 200, $c);
check('audit list ada aksi create_template', 1, (strpos($rawbody, 'create_template') !== false) ? 1 : 0);

$sqlfilter = "SELECT action, detail_json FROM career_audit_logs
              WHERE object_type = 'career_job_posting' AND object_id = {$IDP}
              ORDER BY id_audit_log";
check('audit log lowongan create ada', 1, ($pdo->query($sqlfilter)->fetch_assoc()['action'] === 'create_lowongan') ? 1 : 0);

list($c, , $rawbody) = http('GET', $BASE . '/administrator/career_audit?object_type=career_job_posting&object_id=' . $IDP, array(), null, $COOKIE);
check('audit filter object_id tampilkan', 1, (strpos($rawbody, '[DUMMY TEST6A] Posting Smoke') !== false) ? 1 : 0);

$aactor = (int) $pdo->query("SELECT actor_id FROM career_audit_logs WHERE action='create_lowongan' AND object_id={$IDP} LIMIT 1")->fetch_assoc()['actor_id'];
check('audit actor_id isi id admin', $ADMIN_ID, $aactor);

/* ---------- 8. render semua halaman bebas PHP notice ---------- */
foreach (array(
	'career_lowongan', 'career_lowongan/add', 'career_lowongan/edit/' . $IDP,
	'career_template', 'career_template/add', 'career_template/edit/' . $TPL,
	'career_pelamar', 'career_pelamar/view/' . $APP, 'career_audit',
) as $path) {
	list($c, , $rawbody) = http('GET', $BASE . '/administrator/' . $path, array(), null, $COOKIE);
	$bad = (preg_match('/(Fatal error|Warning\(|Notice:|Undefined (index|variable|property))/i', $rawbody)) ? 1 : 0;
	check('render ' . $path . ' 200 bersih', '200:0', $c . ':' . $bad);
}

echo "Ringkasan smoke6a: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
