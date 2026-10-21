<?php
/**
 * Smoke test GURU.1 (smoke_guru1.php) — modul konversi pelamar accepted →
 * guru SD/KB/TK + helper guru_core lintas jenjang.
 *
 * Cakupan:
 *   A. Konversi sukses SD/KB/TK — baris guru berisi pemetaan profil TEPAT
 *      (banding field by field), presensi_role/satuan server-side,
 *      career_guru_link terisi + snapshot_json tanpa NIK/telepon, audit log
 *      baris 'application.convert_to_guru'.
 *   B. Tolak: draft, sudah dikonversi (409), lamaran bukan accepted, tanpa
 *      perm (career_pelamar_to_guru), GET-metode, tanpa CSRF, emp_code
 *      terpakai lintas jenjang, email terpakai lintas jenjang, nama + tgl
 *      lahir cocok → minta centang admin ("periksa dipastikan").
 *   C. Pemetaan gender male/female/NULL; telepon >25 dipotong; field kosong
 *      → NULL; id_mapel SD wajib & KB/TK NULL.
 *   D. cek_link read-only + tombol/lencana di career_pelamar_view.
 *
 * Pemakaian: php tests/api/smoke_guru1.php [BASE]
 * Prasyarat: jacos_db LOKAL; fixture dibuat + dihapus sendiri (STAMP).
 * DILARANG di produksi.
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

function post_admin($path, $fields, $cookie) {
	return http('POST', $GLOBALS['BASE'] . '/administrator/' . ltrim($path, '/'),
		array('Content-Type: application/x-www-form-urlencoded', 'X-Requested-With: XMLHttpRequest'),
		http_build_query($fields), $cookie);
}

function get_admin($path, $cookie) {
	return http('GET', $GLOBALS['BASE'] . '/administrator/' . ltrim($path, '/'), array(), null, $cookie);
}

$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }
if ((string) $pdo->query('SELECT DATABASE() d')->fetch_assoc()['d'] !== 'jacos_db') {
	echo "FAIL  bukan jacos_db\n"; exit(1);
}

$S1 = getenv('Guru1_SKIP_CLEAN') === '1'; // -- tes tetap jalan; pembersihan dilewati
$STAMP = date('ymdHis') . substr((string) mt_rand(), 0, 4);

$G_APPS = array(); $G_CANDS = array(); $G_LINKS = array(); $G_ADMINS = array();
$G_GURU = array('guru_sd' => array(), 'guru_kb' => array(), 'guru_tk' => array());
$G_POSTS = array();

$in = function ($a) { return count($a) ? implode(',', array_map('intval', $a)) : '0'; };
$prefix_like = 'Guru SmokeG1 ' . $STAMP;

register_shutdown_function(function () use ($pdo, $in, $prefix_like, $STAMP, $S1) {
	echo "\n[cleanup] mulai STAMP={$STAMP}\n";
	if (!empty($GLOBALS['S1'])) {
		echo "[GURU1] skip-cleanup diabaikan (GURU1_SKIP_CLEAN=1) — sisa pakai STAMP {$STAMP}\n";
		return;
	}
	$ids = $in($GLOBALS['G_APPS']);
	$pdo->query("DELETE c FROM career_guru_link c WHERE c.id_application IN ({$ids})");
	foreach (array('guru_sd', 'guru_kb', 'guru_tk') as $tbl) {
		$cond = array();
		if ($GLOBALS['G_GURU'][$tbl] !== array()) { $cond[] = 'id_guru IN (' . implode(',', array_map('intval', $GLOBALS['G_GURU'][$tbl])) . ')'; }
		$cond[] = "nama_lengkap IN ('Guru SmokeG1 {$STAMP} A','Guru SmokeG1 {$STAMP} B','Guru SmokeG1 {$STAMP} C','Guru SmokeG1 {$STAMP} D','Guru SmokeG1 {$STAMP} E','Guru SmokeG1 {$STAMP} F','Guru SmokeG1 {$STAMP} G','Guru Lama {$STAMP}')";
		$r = $pdo->query("DELETE FROM {$tbl} WHERE " . implode(' OR ', $cond));
		echo "[cleanup] {$tbl} affected=" . ($r ? $pdo->affected_rows : 'code ' . $pdo->errno) . "\n";
	}
	foreach ($GLOBALS['G_APPS'] as $aid) {
		$pdo->query("DELETE FROM career_audit_logs WHERE object_type = 'career_application' AND object_id = " . (int) $aid);
		$pdo->query("DELETE FROM career_application_stages WHERE id_application = " . (int) $aid);
		$pdo->query("DELETE FROM career_applications WHERE id_application = " . (int) $aid);
	}
	foreach ($GLOBALS['G_POSTS'] as $p) {
		$pdo->query('DELETE FROM career_job_posting_requirements WHERE id_job_posting = ' . (int) $p);
		$pdo->query('DELETE FROM career_job_postings WHERE id_job_posting = ' . (int) $p);
	}
	$pdo->query('DELETE FROM career_candidate_profiles WHERE id_candidate IN (' . $in($GLOBALS['G_CANDS']) . ')');
	$pdo->query('DELETE FROM career_candidate_items WHERE id_candidate IN (' . $in($GLOBALS['G_CANDS']) . ')');
	$pdo->query('DELETE FROM career_candidates WHERE id_candidate IN (' . $in($GLOBALS['G_CANDS']) . ')');
	foreach ($GLOBALS['G_ADMINS'] as $aid) {
		$pdo->query('DELETE FROM aauth_perm_to_user WHERE user_id = ' . (int) $aid);
		$pdo->query('DELETE FROM aauth_user_to_group WHERE user_id = ' . (int) $aid);
		$pdo->query('DELETE FROM aauth_users WHERE id = ' . (int) $aid);
	}
});

/* --------------- fixture admin ------------------------------------------- */
// Superadmin (grup Admin == 1) punya semua perm isi career, termasuk
// career_pelamar_to_guru (digrant via 007). Untuk uji "tanpa izin", tulis
// admin yang bukan grup 1/tanpa perm apa pun (tanpa grup apapun).
$SANDI = 'SandiGuru1x!';
$EMAIL_A = "guru1-{$STAMP}-adm@example.test";
$EMAIL_B = "guru1-{$STAMP}-tanpa@example.test";
$hash_a = password_hash($SANDI, PASSWORD_BCRYPT);
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_A}', '{$hash_a}', 'guru1adm', 'SmokeAdmin G1 a', 0, '', '')");
$AID = (int) $pdo->insert_id; $G_ADMINS[] = $AID;
$pdo->query('INSERT INTO aauth_user_to_group (user_id, group_id) VALUES (' . $AID . ', 1)');
$pdo->query("INSERT INTO aauth_users (email, pass, username, full_name, banned, avatar, top_secret)
             VALUES ('{$EMAIL_B}', '{$hash_a}', 'guru1tam', 'SmokeAdmin G1 tanpa izin', 0, '', '')");
$BID = (int) $pdo->insert_id; $G_ADMINS[] = $BID;

/* login admin A (grup Admin) */
$cookie_a = 'csid=' . md5($STAMP . 'A');
list($cA, $headA, ) = http('POST', $BASE . '/administrator/login', array(),
	http_build_query(array('username' => $EMAIL_A, 'password' => $SANDI)));
check('login admin A 200/302', 'ya', ($cA === '200' || $cA === '302') ? 'ya' : 'bukan');
// ambil set-cookie jacos session utk cookie_a
preg_match('/Set-Cookie: ([^;]+=[^;]+)/i', $headA, $m);
$cookie_a = isset($m[1]) ? $m[1] : $cookie_a;

/* login admin B (tanpa perm) */
list($cB, $headB, ) = http('POST', $BASE . '/administrator/login', array(),
	http_build_query(array('username' => $EMAIL_B, 'password' => $SANDI)));
preg_match('/Set-Cookie: ([^;]+=[^;]+)/i', $headB, $m2);
$cookie_b = isset($m2[1]) ? $m2[1] : ('csid=' . md5($STAMP . 'B'));
check('login admin B 200/302', 'ya', ($cB === '200' || $cB === '302') ? 'ya' : 'bukan');

/* ---------------- fixture kandidat + lamaran + stage -------------------- */
$EMAIL1 = "guru1-{$STAMP}-a@example.test";
$EMAIL2 = "guru1-{$STAMP}-b@example.test";
$EMAIL3 = "guru1-{$STAMP}-c@example.test";
$EMAIL4 = "guru1-{$STAMP}-d@example.test";
$q = function ($sql) use ($pdo) {
	$r = $pdo->query($sql);
	if ($r === false) { echo "FAIL  SQL gagal: {$pdo->error} :: " . substr($sql, 0, 120) . "\n"; exit(1); }
	return $r;
};
$first = function ($sql, $col) use ($q) {
	$r = $q($sql)->fetch_assoc();
	return $r ? $r[$col] : null;
};

$NAMA_GURU_BASE = 'Guru SmokeG1 ' . $STAMP;
$fixture_cands = array(
	array('email' => $EMAIL1, 'nama' => $NAMA_GURU_BASE . ' A'),
	array('email' => $EMAIL2, 'nama' => $NAMA_GURU_BASE . ' B'),
	array('email' => $EMAIL3, 'nama' => $NAMA_GURU_BASE . ' C'),
	array('email' => $EMAIL4, 'nama' => $NAMA_GURU_BASE . ' D'),
);
$CANDS = array();
foreach ($fixture_cands as $i => $c) {
	$q("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
	             VALUES ('{$c['email']}', '{$hash_a}', '{$c['nama']}', 1, 1)");
	$cid = (int) $first("SELECT id_candidate c FROM career_candidates WHERE email = '{$c['email']}'", 'c');
	$CANDS[$i + 1] = $cid; $G_CANDS[] = $cid;
}

$NM = '[DUMMY TEST GURU1 ' . $STAMP . '] Guru Kelas SD Konversi';
$q("INSERT INTO career_job_postings (title, description, opening_date, closing_date, is_published)
             VALUES ('{$NM}', 'post GURU1', CURDATE() - INTERVAL 1 DAY, CURDATE() + INTERVAL 30 DAY, 1)");
$IDP = (int) $first("SELECT id_job_posting p FROM career_job_postings WHERE title = '{$NM}'", 'p');
$G_POSTS[] = $IDP;

$kandidat_status = array(
	$CANDS[1] => 'accepted',   // A: konversi SD sukses
	$CANDS[2] => 'accepted',   // B: lamaran kedua (emp_code bentrok → 409)
	$CANDS[3] => 'accepted',   // C: tabrakan nama+tgl → periksa
	$CANDS[4] => 'draft',      // D: draft → tolak
);
$APPS = array();
foreach ($CANDS as $i => $cid) {
	$status = $kandidat_status[$cid];
	$q("INSERT INTO career_applications (id_candidate, id_job_posting, status, created_at)
	             VALUES ({$cid}, {$IDP}, '{$status}', NOW())");
	$app = (int) $first("SELECT id_application a FROM career_applications WHERE id_candidate = {$cid} ORDER BY id_application DESC LIMIT 1", 'a');
	$APPS[$i] = $app; $G_APPS[] = $app;
	$q("INSERT INTO career_application_stages (id_application, id_stage, status)
	             SELECT {$app}, id_stage, 'pending' FROM career_stages WHERE is_active = 1 ORDER BY stage_order ASC LIMIT 5");
}
$APP1 = $APPS[1]; $APP2 = $APPS[2]; $APP3 = $APPS[3]; $APPD = $APPS[4];

/* profil kandidat utk pemetaan */
$q("INSERT INTO career_candidate_profiles (id_candidate, phone, place_of_birth, date_of_birth, gender, address)
             VALUES ({$CANDS[1]}, '081212345678901234567890123456', 'Bandung', '1995-05-01', 'male', 'Jl Uji 1')");
$q("INSERT INTO career_candidate_profiles (id_candidate, phone, place_of_birth, date_of_birth, gender, address)
             VALUES ({$CANDS[2]}, '081223456789', 'Jakarta', '2000-01-31', 'female', 'Jl Uji 2')");
$q("INSERT INTO career_candidate_profiles (id_candidate, phone, place_of_birth, date_of_birth, gender, address)
             VALUES ({$CANDS[3]}, NULL, NULL, NULL, NULL, NULL)");
$q("INSERT INTO career_candidate_items (id_candidate, item_type, title, institution, start_date, end_date, display_order)
             VALUES ({$CANDS[1]}, 'education', 'S1 Matematika', 'Universitas Uji', '2016-08-01', '2020-06-30', 1)");
$q("INSERT INTO career_candidate_items (id_candidate, item_type, title, institution, start_date, end_date, display_order)
             VALUES ({$CANDS[1]}, 'education', 'SMA N 1', 'SMA 1', '2013-07-01', '2016-06-30', 0)");

/* Guru lama (lintas jenjang) utk cek tabrakan */
$q("INSERT INTO guru_kb (nama_lengkap, emp_code, email, email_ms_office, satuan_pendidikan, presensi_role, tgl_lahir, foto_profil, login_token)
             VALUES ('Guru Lama {$STAMP}', 'EMPDUP{$STAMP}', 'guru1-dup-{$STAMP}@example.test', 'guru1-dupms-{$STAMP}@example.test', 'KB', 'GURUKB', '1990-07-15', '', '')");
$DUP_KB_ID = (int) $pdo->insert_id;
$G_GURU['guru_kb'][] = $DUP_KB_ID;
$EMPDUP  = 'EMPDUP' . $STAMP;
$EMAILDUP = 'guru1-dup-' . $STAMP . '@example.test';
$EMAILDUP_MS = 'guru1-dupms-' . $STAMP . '@example.test';


/* Guru lama utk periksa: nama samakan INPUT nama_lengkap + tgl CAND5 */
$q("INSERT INTO guru_tk (nama_lengkap, emp_code, email, satuan_pendidikan, presensi_role, tgl_lahir, foto_profil, login_token)
             VALUES ('{$NAMA_GURU_BASE} E', 'EMPERIKSA{$STAMP}', 'emperiksa-{$STAMP}@example.test', 'TK', 'GURUTK', '2021-06-10', '', '')");
$PERIKSA_GURU_ID = (int) $pdo->insert_id;
$G_GURU['guru_tk'][] = $PERIKSA_GURU_ID;
$NAMA_PERIKSA = $NAMA_GURU_BASE . ' E'; // sama dgn nama kandidat C5 (controller memakai full_name kandidat)

/* kandidat tambahan: CAND5 periksa, CAND6 emp dup, CAND7 email dup */
$EMAIL5 = "guru1-{$STAMP}-e@example.test";
$EMAIL6 = "guru1-{$STAMP}-f@example.test";
$EMAIL7 = "guru1-{$STAMP}-g@example.test";
foreach (array(array('em' => $EMAIL5, 'nm' => 'Guru SmokeG1 '.$STAMP.' E'), array('em' => $EMAIL6, 'nm' => 'Guru SmokeG1 '.$STAMP.' F'), array('em' => $EMAIL7, 'nm' => 'Guru SmokeG1 '.$STAMP.' G')) as $cand_row) {
	$q("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version)
	             VALUES ('{$cand_row['em']}', '{$hash_a}', '{$cand_row['nm']}', 1, 1)");
	$cid = (int) $first("SELECT id_candidate c FROM career_candidates WHERE email = '{$cand_row['em']}'", 'c');
	$G_CANDS[] = $cid;
	$CANDS[] = $cid;
	$q("INSERT INTO career_candidate_profiles (id_candidate, phone, place_of_birth, date_of_birth, gender, address)
	             VALUES ({$cid}, '081234567890', 'Bandung', '2021-06-10', 'male', 'Jl Uji 3')");
	$q("INSERT INTO career_applications (id_candidate, id_job_posting, status, created_at)
	             VALUES ({$cid}, {$IDP}, 'accepted', NOW())");
	$app = (int) $first("SELECT id_application a FROM career_applications WHERE id_candidate = {$cid} ORDER BY id_application DESC LIMIT 1", 'a');
	$APPS[] = $app; $G_APPS[] = $app;
}
$APP5 = $APPS[5]; $APP6 = $APPS[6]; $APP7 = $APPS[7];

/** util post konversi */
function konversi($cookie, $app, $fields) {
	$fields = array_merge(array('kg_status' => 'stay', 'career_csrf' => $GLOBALS['CSRF'] ?? ''), $fields);
	$resp = post_admin("career_guru/konversi_simpan_post/{$app}", $fields, $cookie);
	if (getenv('GURU1_DEBUG') === '1') { echo "[debug konversi $app] " . $resp[2] . "
"; }
	return $resp;
}
function json_of($raw) {
	$j = json_decode($raw, true);
	return is_array($j) ? $j : array();
}

/* ---------------- SEC 1: GET konversi ------------------------------------ */
list($c, $head, $raw) = get_admin('career_guru/konversi/' . $APP1, $cookie_a);
check('halaman konversi 200', '200', $c);
$career_konversi_csrf = null;
if (preg_match('/name="career_csrf"[^>]*value="([^"]+)"/', $raw, $m)) {
	$career_konversi_csrf = $m[1];
}
/* sesi bisa di-refresh di response — pakai cookie terbaru bila ada */
function ambil_cookie($head, $saat) { 
	preg_match_all('/Set-Cookie:\s*([^;\s]+)=([^;\s]*)/i', $head, $cm);
	foreach ($cm[1] as $i => $nm) {
		if (trim(strtolower($nm)) === 'jacos_session' && $cm[2][$i] !== '') {
			return 'jacos_session=' . rawurldecode($cm[2][$i]);
		}
	}
	return $saat;
}
$cookie_a = ambil_cookie($head, $cookie_a);
/* DEBUG CSRF banding 2 GET + 1 POST */
list($c2, $h2, $raw2) = get_admin('career_guru/konversi/' . $APP1, $cookie_a);
preg_match('/name="career_csrf"[^>]*value="([^"]+)"/', $raw2, $m2);
if (getenv('GURU1_DEBUG') === '1') {
	echo "[debug csrf] tok1={$career_konversi_csrf}
[debug csrf] tok2={$m2[1]}
[debug csrf] c2={$c2} len2=" . strlen($raw2) . "
";
}
$career_konversi_csrf = ($m2[1] ?? $career_konversi_csrf);
check('token CSRF tersedia di halaman konversi', 'ya', $career_konversi_csrf !== null ? 'ya' : 'bukan');
$CSRF = (string) $career_konversi_csrf;

list($c, , $raw) = get_admin('career_guru/konversi/99999999', $cookie_a);
check('konversi lamaran tak ditemukan 307 redirect flash', '307', $c);

/* sesuaikan id_mapel/posisi dari DB dev (bukan hardcode) */
$row = $pdo->query("SELECT id_mapel FROM mata_pelajaran_sd ORDER BY id_mapel ASC LIMIT 1")->fetch_assoc();
$MAPEL_SD = $row !== NULL ? (int) $row['id_mapel'] : 0;
check('SD memiliki id_mapel fixture', 'ya', $MAPEL_SD > 0 ? 'ya' : 'bukan');
$row = $pdo->query("SELECT id_posisi FROM posisi_guru WHERE nama_posisi = 'Guru KB' LIMIT 1")->fetch_assoc();
$POS_KB = $row !== NULL ? (int) $row['id_posisi'] : 0;
check('Guru KB posisi tersedia', 'ya', $POS_KB > 0 ? 'ya' : 'bukan');

/* ---------------- SEC 2: tolak bermacam kondisi -------------------------- */
/* pesan biasanya error jadi dari set_flashdata + redirect — curl tanpa
 * followlocation balik 302; verifikasikan di body redirect.juries */
list($c, $head, $body) = konversi($cookie_a, $APP1, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '1', 'email' => $EMAIL1, 'jenjang' => 'SD',
	'id_mapel' => $MAPEL_SD, 'npp' => 'NPPGURU1', 'pendidikan_terakhir' => 'S1',
	'universitas' => 'Universitas Uji', 'jurusan' => 'Matematika', 'tahun_lulus' => 2020,
	'periksa_dipastikan' => '1',
));
check('konversi SD sukses 200 (jangan redirect)', '200', $c);
$j = json_of($body);
check('konversi SD success=true', 'ya', (!empty($j['success']) && $j['success'] == true) ? 'ya' : 'bukan');

/* ---------------- SEC 3: KB & TK sukses ---------------------------------- */
$row = $pdo->query("SELECT id_posisi FROM posisi_guru WHERE nama_posisi = 'Guru TK' LIMIT 1")->fetch_assoc();
$POS_TK = (int) $row['id_posisi'];

list($c, , $body) = konversi($cookie_a, $APP2, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '2',
	'email' => $EMAIL2,
	'jenjang' => 'KB', 'id_posisi' => $POS_KB,
));
$j = json_of($body);
check('konversi KB sukses', 'ya', (!empty($j['success'])) ? 'ya' : 'bukan');

list($c, , $body) = konversi($cookie_a, $APP3, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '3',
	'email' => $EMAIL3,
	'jenjang' => 'TK', 'id_posisi' => $POS_TK,
));
$j = json_of($body);
check('konversi TK sukses', 'ya', (!empty($j['success'])) ? 'ya' : 'bukan');

/* ---------------- SEC 4: tolak bermacam kondisi ------------------------- */
/* draft lamaran -> tolak (status bukan accepted) */
list($c, , $body) = konversi($cookie_a, $APPD, array(
	'emp_code' => 'EMPGURU1' . $STAMP . 'draft', 'email' => $EMAIL1,
	'jenjang' => 'SD', 'id_mapel' => $MAPEL_SD,
));
$j = json_of($body);
check('draft lamaran ditolak', 'ya', (empty($j['success'])) ? 'ya' : 'bukan');

/* admin B tanpa izin career_pelamar_to_guru -> tolak */
list($c, , $body) = konversi($cookie_b, $APPD, array(
	'emp_code' => 'EMPGURU1' . $STAMP . 'noperm', 'email' => 'noperm-' . $STAMP . '@example.test',
	'jenjang' => 'SD', 'id_mapel' => $MAPEL_SD,
));
$j = json_of($body);
check('tanpa perm career_pelamar_to_guru ditolak', 'ya', (empty($j['success'])) ? 'ya' : 'bukan');

/* GET-mutasi ditolak (bukan POST) */
list($c, , $bodyget) = get_admin('career_guru/konversi_simpan_post/' . $APP1, $cookie_a);
check('konversi_simpan GET ditolak', 'ya', (strpos($bodyget, 'Permintaan tidak sah (CSRF).') !== false OR (string) $c !== '200') ? 'ya' : 'bukan');

/* tanpa CSRF -> tolak */
$fields = array('emp_code' => 'EMPGURU1' . $STAMP . 'NoCSRF', 'email' => $EMAIL1,
	'jenjang' => 'SD', 'id_mapel' => $MAPEL_SD, 'kg_status' => 'stay');
list($c, , $body) = post_admin('career_guru/konversi_simpan_post/' . $APP2, $fields, $cookie_a);
$j = json_of($body);
check('tanpa CSRF ditolak', 'ya', (empty($j['success']) AND strpos($body, 'Permintaan tidak sah (CSRF).') !== false) ? 'ya' : 'bukan');

/* career_csrf kosong -> tolak */
list($c, , $body) = konversi($cookie_a, $APP2, array('career_csrf' => ''));
$j = json_of($body);
check('career_csrf kosong ditolak', 'ya', (empty($j['success'])) ? 'ya' : 'bukan');

/* lamaran sudah dikonversi -> 409 (oleh admin lain) */
list($c, , $body) = konversi($cookie_a, $APP1, array(
	'emp_code' => 'EMPGURU1' . $STAMP . 'late', 'email' => 'late-' . $STAMP . '@example.test',
	'jenjang' => 'SD', 'id_mapel' => $MAPEL_SD,
));
$j = json_of($body);
check('lamaran sudah dikonversi 409', 'ya', (empty($j['success'])
	AND strpos($body, 'sudah dikonversi') !== false) ? 'ya' : 'bukan');

/* nama + tgl_lahir sama dengan guru lama -> minta periksa_dipastikan */
list($c, , $body) = konversi($cookie_a, $APP5, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '5',
	'email' => $EMAIL5, 'jenjang' => 'TK', 'id_posisi' => $POS_TK,
	'nama_lengkap' => $NAMA_PERIKSA,
));
$j = json_of($body);
check('nama+lahir sama tanpa centang admin', 'ya', (empty($j['success'])) ? 'ya' : 'bukan');

list($c, , $body) = konversi($cookie_a, $APP5, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '5', 'email' => $EMAIL5,
	'jenjang' => 'TK', 'id_posisi' => $POS_TK, 'nama_lengkap' => $NAMA_PERIKSA,
	'periksa_dipastikan' => '1',
));
$j = json_of($body);
check('periksa dipastikan -> sukses', 'ya', (!empty($j['success'])) ? 'ya' : 'bukan');

/* emp_code terpakai guru lain -> blok */
list($c, , $body) = konversi($cookie_a, $APP6, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '1',
	'email' => $EMAIL6, 'jenjang' => 'SD', 'id_mapel' => $MAPEL_SD,
));
$j = json_of($body);
check('emp_code terpakai guru lain -> blok', 'ya', (empty($j['success'])
	AND strpos($body, 'emp_code sudah dipakai') !== false) ? 'ya' : 'bukan');

/* email terpakai guru lain -> blok */
list($c, , $body) = konversi($cookie_a, $APP7, array(
	'emp_code' => 'EMPGURU1' . $STAMP . '7',
	'email' => $EMAILDUP, 'jenjang' => 'SD', 'id_mapel' => $MAPEL_SD,
));
$j = json_of($body);
check('email terpakai guru lain -> blok', 'ya', (empty($j['success'])
	AND strpos($body, 'email sudah dipakai') !== false) ? 'ya' : 'bukan');

/* ---------------- SEC 5: verifikasi isi guru + link + audit -------------- */
$g_sd = $pdo->query("SELECT * FROM guru_sd WHERE emp_code = 'EMPGURU1{$STAMP}1' AND deleted_at IS NULL")->fetch_assoc();
check('guru_sd 1 baris', 1, $g_sd ? 1 : 0);
if ($g_sd) {
	check('nama_lengkap sama dgn kandidat', 1, ($g_sd['nama_lengkap'] === $NAMA_GURU_BASE . ' A') ? 1 : 0);
	check('email = email kandidat', 1, ($g_sd['email'] === $EMAIL1) ? 1 : 0);
	check('telepon >25 dipotong 25', 1, (strlen((string) $g_sd['no_telp']) === 25) ? 1 : 0);
	check('jenis_kelamin male=L', 1, ($g_sd['jenis_kelamin'] === 'L') ? 1 : 0);
	check('alamat tersalin', 1, ($g_sd['alamat'] === 'Jl Uji 1') ? 1 : 0);
	check('tempat/tgl lahir tersalin', 1, ($g_sd['tempat_lahir'] === 'Bandung' AND $g_sd['tgl_lahir'] === '1995-05-01') ? 1 : 0);
	check('satuan_pendidikan = SD (server)', 1, ($g_sd['satuan_pendidikan'] === 'SD') ? 1 : 0);
	check('presensi_role GURUSD (server)', 1, ($g_sd['presensi_role'] === 'GURUSD') ? 1 : 0);
	check('id_mapel legitimasi', 1, ((int) $g_sd['id_mapel'] === $MAPEL_SD) ? 1 : 0);
	check('pendidikan tersalin', 1, ($g_sd['pendidikan_terakhir'] === 'S1'
		AND $g_sd['universitas'] === 'Universitas Uji' AND $g_sd['jurusan'] === 'Matematika'
		AND (int) $g_sd['tahun_lulus'] === 2020) ? 1 : 0);
	check('email_ms_office kosong (opsional)', 1, (($g_sd['email_ms_office'] === '') OR ($g_sd['email_ms_office'] === null)) ? 1 : 0);
}
$g_kb = $pdo->query("SELECT jenis_kelamin, presensi_role, no_telp FROM guru_kb WHERE emp_code = 'EMPGURU1{$STAMP}2' AND deleted_at IS NULL")->fetch_assoc();
check('guru_kb female=P + GURUKB', 1, ($g_kb['jenis_kelamin'] === 'P' AND $g_kb['presensi_role'] === 'GURUKB') ? 1 : 0);
$g_tk = $pdo->query("SELECT jenis_kelamin, presensi_role, no_telp FROM guru_tk WHERE emp_code = 'EMPGURU1{$STAMP}3' AND deleted_at IS NULL")->fetch_assoc();
check('guru_tk profil NULL + GURUTK', 1, ((($g_tk['no_telp'] === NULL OR $g_tk['no_telp'] === '') OR $g_tk['no_telp'] === null) AND $g_tk['presensi_role'] === 'GURUTK' AND ($g_tk['jenis_kelamin'] === '' OR $g_tk['jenis_kelamin'] === NULL)) ? 1 : 0);
$g_tk5 = $pdo->query("SELECT nama_lengkap FROM guru_tk WHERE emp_code = 'EMPGURU1{$STAMP}5' AND deleted_at IS NULL")->fetch_assoc();
check('TK5 nama periksa-dipastikan', 1, ($g_tk5['nama_lengkap'] === $NAMA_PERIKSA) ? 1 : 0);

$SNAP_JSON = (int) $pdo->query("SELECT COUNT(*) n FROM career_guru_link WHERE snapshot_json LIKE '%no_telp%'")->fetch_assoc()['n'];
check('link snapshot TANPA telepon', 0, $SNAP_JSON);
$N_LINKS = (int) $pdo->query("SELECT COUNT(*) n FROM career_guru_link WHERE id_application IN ({$APP1},{$APP2},{$APP3},{$APP5})")->fetch_assoc()['n'];
check('4 baris link (SD,KB,TK,periksa)', 4, $N_LINKS);

list($c, , ) = get_admin('career_guru/cek_link/' . $APP1, $cookie_a);
check('cek_link 200', '200', $c);

$NAUD = (int) $pdo->query("SELECT COUNT(*) n FROM career_audit_logs WHERE action = 'application.convert_to_guru' AND object_type = 'career_application' AND object_id IN ({$APP1},{$APP2},{$APP3},{$APP5})")->fetch_assoc()['n'];
check('audit convert_to_guru 4 baris', 4, $NAUD);

list($c, , $raw) = get_admin('career_pelamar/view/' . $APP1, $cookie_a);
check('career_pelamar view 200', '200', $c);
check('tombol konversi muncul di view', 'ya', (strpos($raw, 'career_guru/konversi/') !== false OR strpos($raw, 'Sudah jadi guru') !== false) ? 'ya' : 'bukan');

echo "\nRingkasan smoke_guru1: PASS={$pass} FAIL={$fail}\n";
if ($fail > 0) exit(1);
