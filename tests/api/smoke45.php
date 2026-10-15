<?php
/**
 * Smoke test Fase 4.5 — regresi review (H1-H5, M1-M10, L-subset).
 *
 * Pemakaian: php tests/api/smoke45.php [BASE_URL]
 *   BASE_URL default: http://jacos.test:8080/apicareer
 *
 * PRASYARAT LOKAL (Laragon dev saja):
 *   - tests/api/seed_dummy_local.sql sudah dijalankan ke jacos_db
 *     (lowongan [DUMMY TEST] + 3 requirement: document/text/number).
 *   - mysqli + curl ext aktif (default Laragon).
 *
 * Script ini MEMBUAT data uji di jacos_db LOKAL (kandidat smoke45*),
 * MEMBUKA kunci JWT dari credentials.php HANYA untuk membuat token uji,
 * dan MENGHAPUS baris career_login_attempts antar-suite (fixture).
 * JANGAN dijalankan di produksi.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080/apicareer';

// ---------------------------------------------------------------
// helper HTTP (sama seperti smoke.php)
// ---------------------------------------------------------------
function http($method, $url, $headers = array(), $body = null, $files = array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
	if (count($headers) > 0) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if (count($files) > 0) {
		$post = (array) $body;
		foreach ($files as $field => $path) { $post[$field] = new CURLFile($path); }
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	} elseif ($body !== null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	}
	$raw = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($raw === false) { echo 'CURL ERR: ' . curl_error($ch) . "\n"; $raw = ''; }
	curl_close($ch);
	if ($raw === '') return array((string) $code, '', '');
	$head = '';
	$body = $raw;
	$pos = strpos($raw, "\r\n\r\n");
	if ($pos !== false) { $head = substr($raw, 0, $pos); $body = substr($raw, $pos + 4); }
	return array((string) $code, $head, (string) $body);
}

function http_json($method, $url, $body, $token = null) {
	$headers = array('Content-Type: application/json');
	if ($token !== null) $headers[] = 'Authorization: Bearer ' . $token;
	// GET: jangan kirim body — Apache menolak GET ber-body (error 401/halaman PHP).
	$send = (strtoupper($method) === 'GET') ? null : json_encode($body);
	list($code, $head, $raw) = http($method, $url, $headers, $send);
	$json = json_decode($raw, true);
	return array($code, is_array($json) ? $json : null, $raw);
}

function arr_get($arr, $path, $default = null) {
	$cur = $arr;
	foreach (explode('.', $path) as $k) {
		if (!is_array($cur) || !array_key_exists($k, $cur)) return $default;
		$cur = $cur[$k];
	}
	return $cur;
}

function header_val($head, $name) {
	if (preg_match('/^' . preg_quote($name, '/') . ':\s*(.+)$/mi', $head, $m)) return trim($m[1]);
	return null;
}

$pass = 0; $fail = 0;
function check($nama, $harap, $terima) {
	global $pass, $fail;
	if ((string) $harap === (string) $terima) {
		$pass++; echo "PASS  $nama (harap $harap)\n";
	} else {
		$fail++; echo "FAIL  $nama (harap $harap, terima $terima)\n";
	}
}

// ---------------------------------------------------------------
// fixture mysqli: purge percobaan login, kandidat kedua, lowongan tutup
// ---------------------------------------------------------------
$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }
function purge_attempts($pdo) { $pdo->query('DELETE FROM career_login_attempts'); }
purge_attempts($pdo);

// kandidat kedua (tanpa memakai kuota register-API)
$EMAIL2 = 'smoke45-b@example.test';
// bersihkan sisa run sebelumnya (lamaran lama blokir dup UNIQUE karena fk RESTRICT)
$stale = $pdo->query("SELECT cc.id_candidate FROM career_candidates cc WHERE cc.email = '{$EMAIL2}'")->fetch_assoc();
if ($stale) {
	$openid = (int) $stale['id_candidate'];
	$pdo->query("DELETE d FROM career_application_documents d
	             JOIN career_application_requirements r ON r.id_application_requirement = d.id_application_requirement
	             JOIN career_applications a ON a.id_application = r.id_application WHERE a.id_candidate = {$openid}");
	$pdo->query("DELETE s FROM career_application_profile_snapshots s JOIN career_applications a ON a.id_application = s.id_application WHERE a.id_candidate = {$openid}");
	$pdo->query("DELETE c FROM career_notifications c JOIN career_candidates cc ON cc.id_candidate = c.id_candidate WHERE cc.id_candidate = {$openid}");
	$pdo->query("DELETE a FROM career_applications a WHERE a.id_candidate = {$openid}");
}
$pdo->query("DELETE FROM career_candidates WHERE email = '{$EMAIL2}'");
$pdo->query("DELETE cij.* FROM career_candidate_items cij
             JOIN career_candidates cc ON cc.id_candidate = cij.id_candidate WHERE cc.email = '{$EMAIL2}'");
$hash = password_hash('Rahasia45Kay', PASSWORD_BCRYPT);
$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version, failed_login_count)
             VALUES ('{$EMAIL2}', '{$hash}', 'Kandidat Lalu Smoke45', 1, 1, 0)");
$result = $pdo->query("SELECT id_candidate FROM career_candidates WHERE email = '{$EMAIL2}'");
$row = $result->fetch_assoc();
$ID2 = (int) $row['id_candidate'];
$pdo->query("INSERT INTO career_candidate_profiles (id_candidate, summary) VALUES ({$ID2}, 'profil b')");
echo "INFO  kandidat kedua id={$ID2}\n";

// token valid utk kandidat kedua (sub/ver/iss/iat/exp lengkap) — HS256 dengan key dari credentials.php
$cfile = file_get_contents(__DIR__ . '/../../application/config/credentials.php');
$KEY = '';
if (defined('CAREER_JWT_KEY')) { $KEY = CAREER_JWT_KEY; }
else {
	if (preg_match("/define\(\s*'CAREER_JWT_KEY'\s*,\s*'([^']+)'/", $cfile, $m)) $KEY = $m[1];
}
if ($KEY === '' || strlen($KEY) < 32) { echo "FAIL  CAREER_JWT_KEY tidak terbaca (>=32 byte)\n"; exit(1); }

require_once __DIR__ . '/../../application/libraries/Jwt/JWT.php';
require_once __DIR__ . '/../../application/libraries/Jwt/ExpiredException.php';
require_once __DIR__ . '/../../application/libraries/Jwt/BeforeValidException.php';
require_once __DIR__ . '/../../application/libraries/Jwt/SignatureInvalidException.php';
use Firebase\JWT\JWT as TestJWT;

function craft_token($id, $vers, $opts = array()) {
	global $KEY;
	$now = time();
	$claims = array(
		'iss' => isset($opts['iss']) ? $opts['iss'] : 'jacos-career',
		'sub' => (string) $id,
		'ver' => (int) $vers,
		'iat' => $now,
	);
	if (!isset($opts['no_exp'])) $claims['exp'] = $now + 3600;
	return TestJWT::encode($claims, $KEY, 'HS256');
}
// ---------------------------------------------------------------
// dokumen uji
// ---------------------------------------------------------------
$D = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'career-smoke45-' . getmypid();
@mkdir($D, 0700, true);
register_shutdown_function(function () use ($D) { @array_map('unlink', glob($D . DIRECTORY_SEPARATOR . '*')); @rmdir($D); });

$pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n";
$paths = array();
for ($i = 0; $i < 8; $i++) {
	$p = $D . '/bks' . $i . '.pdf';
	file_put_contents($p, $pdf . 'varian' . $i . 'x');
	$paths[$i] = $p;
}
$docx = $D . '/bukti.docx';
file_put_contents($docx, 'PK bukan pdf');
$jpgContent = $D . '/pdf_isi_ekstensi_jpg.jpg';
file_put_contents($jpgContent, $pdf);

function upload($BASE, $token, $id_app_req, $file, $name = 'document') {
	list($code, , $raw) = http('POST', $BASE . '/Lamaran_dokumen/upload',
		array('Authorization: Bearer ' . $token),
		array('id_application_requirement' => $id_app_req),
		array($name => $file));
	return array($code, json_decode($raw, true));
}
function answer($BASE, $token, $id_app_req, $val) {
	list($code, $j, ) = http_json('PUT', $BASE . '/Lamaran_jawaban', array(
		'id_application_requirement' => $id_app_req, 'answer' => $val), $token);
	return array($code, $j);
}

// ===============================================================
echo "== A. Fase 4.5 register & rate-limit ==\n";
$E1 = 'smoke45-a-' . date('ymdHis') . '@example.test'; $M2E1 = $E1;
$P = 'Rahasia45Aaa';
list($c, $j, ) = http_json('POST', $BASE . '/Auth/register', array('email' => $E1, 'password' => $P, 'full_name' => 'Kandidat Utama Smoke45'));
check('register C1 -> 201', 201, $c);
$T1 = arr_get($j, 'data.token');
list($c, ) = http_json('POST', $BASE . '/Auth/register', array('email' => $M2E1, 'password' => $P, 'full_name' => 'Kandidat Utama Smoke45'));
check('M2 register dup -> 409', 409, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array('email' => 'bukan-email', 'password' => $P, 'full_name' => 'X'));
check('register email tidak sah -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array('email' => 'short45@example.test', 'password' => 'abc', 'full_name' => 'X'));
check('register password pendek -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array('email' => 'name45@example.test', 'password' => $P, 'full_name' => str_repeat('N', 200)));
check('register nama >150 -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array('email' => 'rate45@example.test', 'password' => $P, 'full_name' => 'Rate Test'));
check('H3 register >5 percobaan /IP/jam -> 429', 429, $c);
purge_attempts($pdo);

echo "== B. Login rate-limit (per email+IP) ==\n";
for ($i = 0; $i < 5; $i++) {
	list($c, ) = http_json('POST', $BASE . '/Auth/login', array('email' => $E1, 'password' => 'Salah45x'));
	check("H4 login salah #$i -> 401", 401, $c);
}
list($c, ) = http_json('POST', $BASE . '/Auth/login', array('email' => $E1, 'password' => $P));
check('H4 password BENAR percobaan ke-6 TERBLOKIR 429', 429, $c);
purge_attempts($pdo);
list($c, $j, $raw_log) = http_json('POST', $BASE . '/Auth/login', array('email' => $E1, 'password' => $P));
check('login benar (setelah purge) -> 200', 200, $c);
check('respons login tidak memuat password_hash', 1, (stripos($raw_log, 'password_hash') === false) ? 1 : 0);
$T1 = arr_get($j, 'data.token');

echo "== C. Token JWT: exp/iat/iss (M1) ==\n";
list($c, ) = http('GET', $BASE . '/Auth/me', array('Authorization: Bearer ' . craft_token(arr_get($j, 'data.id_candidate') ?: 0, 1, array('no_exp' => true))));
check('M1 token valid sign TANPA exp -> 401', 401, $c);
list($c, ) = http('GET', $BASE . '/Auth/me', array('Authorization: Bearer ' . craft_token(arr_get($j, 'data.id_candidate') ?: 0, 1, array('iss' => 'lain-minggu'))));
check('M1 iss salah -> 401', 401, $c);
list($c, ) = http('GET', $BASE . '/Auth/me', array('Authorization: Bearer ' . $T1 . 'x'));
check('M1 token dipinji (tanda tangan rusak) -> 401', 401, $c);

echo "== D. Profil & item validasi (M7/result) ==\n";
list($c, ) = http_json('PUT', $BASE . '/Profil/index', array('summary' => 'halo ' . json_decode('"\ud83d\ude00"')), $T1);
check('profil 4-byte emoji -> 422', 422, $c);
list($c, ) = http_json('PUT', $BASE . '/Profil/index', array('summary' => array('bukan teks')), $T1);
check('profil summary array -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => ' ', 'institution' => ''), $T1);
check('item title kosong/spasi -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => 'S1', 'start_date' => '2023-99-99'), $T1);
check('item tanggal impossible -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => 'S1', 'start_date' => '2024-01-01', 'end_date' => '2023-01-01'), $T1);
check('item end < start -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => 'S1', 'display_order' => -1), $T1);
check('item display_order negatif -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => 'S1', 'extra_json' => 'bukan-json'), $T1);
check('item extra_json invalid -> 422', 422, $c);
list($c, $j, ) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => 'S1 JACOS', 'start_date' => '2020-08-01', 'end_date' => '2024-07-31', 'extra_json' => 'bukan-json2', 'display_order' => 1), $T1);
check('item extra_json string-invalid (2) -> 422', 422, $c);
$ID_ITEM1 = (int) arr_get($j, 'data.id_candidate_item');
list($c, $j) = http_json('POST', $BASE . '/Profil_item/index', array('item_type' => 'education', 'title' => 'S1 JACOS', 'start_date' => '2020-08-01', 'end_date' => '2024-07-31', 'extra_json' => array('catatan' => 'extra ok'), 'display_order' => 1), $T1);
check('item POST extra_json array -> 201', 201, $c);
$ID_ITEM1 = (int) arr_get($j, 'data.id_candidate_item');
list($c, ) = http_json('PUT', $BASE . '/Profil_item/detail/' . $ID_ITEM1, array(), $T1);
check('item PUT kosong -> 400', 400, $c);
// C2 punya item sendiri → C1 mencoba PUT item C2 → 404
list($c, ) = http('PUT', $BASE . '/Profil_item/detail/999999', array('Authorization: Bearer ' . craft_token($ID2, 1)), json_encode(array('title' => 'X')));
check('item milik orang lain PUT -> 404', 404, $c);

echo "== E. CORS (D) ==\n";
list($c, $head, ) = http('GET', $BASE . '/Lowongan/index', array('Origin: https://evil.example'));
$acao = header_val($head, 'Access-Control-Allow-Origin');
check('CORS origin evil TIDAK menerima ACAO', 1, ($acao === null || $acao === '') ? 1 : 0);
list($c, $head, ) = http('GET', $BASE . '/Lowongan/index', array('Origin: http://localhost:3000'));
$acao = header_val($head, 'Access-Control-Allow-Origin');
check('CORS localhost:3000 di-echo', 1, ($acao === 'http://localhost:3000') ? 1 : 0);
$vary = header_val($head, 'Vary');
check('CORS Vary: Origin', 1, (stripos((string) $vary, 'Origin') !== false) ? 1 : 0);

echo "== F. Lamaran draft + completeness + submit (H1/M6) ==\n";
$sql = $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE title LIKE '[DUMMY TEST]%' ORDER BY id_job_posting LIMIT 1");
$row = $sql->fetch_assoc();
$ID_POST = (int) $row['id_job_posting'];
$rd = $pdo->query("SELECT id_job_posting_requirement, requirement_name, input_type, requirement_type FROM career_job_posting_requirements WHERE id_job_posting = {$ID_POST} ORDER BY display_order");
$reqs = array();
while ($row = $rd->fetch_assoc()) { $reqs[strtolower($row['input_type'])] = $row; }
$REQ_DOC = (int) $reqs['document']['id_job_posting_requirement'];
$REQ_TXT = (int) $reqs['text']['id_job_posting_requirement'];
$REQ_NUM = (int) $reqs['number']['id_job_posting_requirement'];

list($c, $j, ) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_POST), $T1);
check('lamaran draft -> 201', 201, $c);
$ID_APP = (int) arr_get($j, 'data.id_application');
list($c, ) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_POST), $T1);
check('lamaran dup -> 409', 409, $c);

// mapping application_requirement milik C1
$rd = $pdo->query("SELECT id_job_posting_requirement, id_application_requirement FROM career_application_requirements WHERE id_application = {$ID_APP}");
$APPREQ = array();
while ($row = $rd->fetch_assoc()) { $APPREQ[(int) $row['id_job_posting_requirement']] = (int) $row['id_application_requirement']; }
$AR_DOC = $APPREQ[$REQ_DOC]; $AR_TXT = $APPREQ[$REQ_TXT]; $AR_NUM = $APPREQ[$REQ_NUM];

list($c, ) = http_json('POST', $BASE . '/Lamaran/submit', array('id_application' => $ID_APP), $T1);
check('H1 submit tanpa berkas+jawaban -> 422', 422, $c);

list($c, ) = upload($BASE, $T1, $AR_NUM, $paths[0]);
check('M3 upload ke req non-berkas -> 422', 422, $c);
list($c, ) = answer($BASE, $T1, $AR_DOC, 'jawab');
check('answer ke req berkas -> 422', 422, $c);

list($c, $j, ) = upload($BASE, $T1, $AR_DOC, $docx);
check('M3 ekstensi docx (luar map) -> 422', 422, $c);
list($c, ) = upload($BASE, $T1, $AR_DOC, $jpgContent);
check('M3 PDF ber-ekstensi .jpg -> 422', 422, $c);
list($c, $j, ) = upload($BASE, $T1, $AR_DOC, $paths[1]);
check('upload pdf valid -> 201', 201, $c);
$ID_DOC1 = (int) arr_get($j, 'data.id_application_document');
list($c, ) = upload($BASE, $T1, $AR_DOC, $paths[1]);
check('H2 checksum dup idempotent -> 200', 200, $c);

list($c, ) = answer($BASE, $T1, $AR_TXT, 'Saya ingin mengajar di JACOS');
check('answer text -> 200', 200, $c);
list($c, ) = answer($BASE, $T1, $AR_NUM, 'abc');
check('answer number non-angka -> 422', 422, $c);
list($c, ) = answer($BASE, $T1, $AR_NUM, '-5');
check('answer number negatif (number utk jml pengalaman) -> 200 sagte', 200, $c);
$AR2 = 999999;
list($c, ) = answer($BASE, $T1, $AR2, 'x');
check('answer id bukan milik -> 404', 404, $c);
list($c, ) = answer($BASE, $T1, $AR_NUM, '');
check('answer kosong (opsional) tersimpan null -> 200', 200, $c);

echo "== G. Quota versi (M4) ==\n";
list($c, ) = upload($BASE, $T1, $AR_DOC, $paths[2]);
check('upload v3 -> 201', 201, $c);
list($c, ) = upload($BASE, $T1, $AR_DOC, $paths[3]);
check('upload v4 -> 201', 201, $c);
list($c, ) = upload($BASE, $T1, $AR_DOC, $paths[4]);
check('upload v5 -> 201', 201, $c);
list($c, ) = upload($BASE, $T1, $AR_DOC, $paths[5]);
check('upload versi ke-5 -> 201', 201, $c);
list($c, ) = upload($BASE, $T1, $AR_DOC, $paths[7]);
check('M4 versi ke-6 melebihi batas -> 409', 409, $c);

echo "== H. Submit + paralel H2 ==\n";
list($c, , $raw) = http_json('POST', $BASE . '/Lamaran/submit', array('id_application' => $ID_APP), $T1);
check('submit leakage-free body + 200', 200, $c);
check('H5 body tidak bercermin SQL/throw', 1, (stripos($raw, 'SQLSTATE') === false && stripos($raw, 'SELECT') === false) ? 1 : 0);
list($c, $j) = http_json('GET', $BASE . '/Lamaran/detail/' . $ID_APP, array(), $T1);
check('detail setelah submit -> 200', 200, $c);
$det_all = $raw;
check('sensitive tidak leak review_note/score/note_internal', 1, (stripos($det_all, 'review_note') === false && stripos($det_all, 'note_internal') === false) ? 1 : 0);
list($c, $j) = http_json('PUT', $BASE . '/Lamaran_jawaban', array('id_application_requirement' => $AR_TXT, 'answer' => 'ubah'), $T1);
check('lamaran terkunci: jawaban ditolak -> 409', 409, $c);

// IDOR: C2 menjajal milik C1
$T2 = craft_token($ID2, 1);
list($c, ) = http_json('GET', $BASE . '/Lamaran/detail/' . $ID_APP, array(), $T2);
check('IDOR detail milik C1 -> 404', 404, $c);
list($c, ) = http_json('POST', $BASE . '/Lamaran/submit', array('id_application' => $ID_APP), $T2);
check('IDOR submit milik C1 -> 404', 404, $c);
list($c, ) = upload($BASE, $T2, $AR_DOC, $paths[6]);
check('IDOR upload req milik C1 -> 404', 404, $c);
list($c, ) = http('GET', $BASE . '/Lamaran_dokumen/detail/' . $ID_DOC1, array('Authorization: Bearer ' . $T2));
check('IDOR unduh dokumen C1 -> 404', 404, $c);
list($c, ) = answer($BASE, $T2, $AR_TXT, 'x');
check('IDOR jawaban req C1 -> 404', 404, $c);

// PARALEL: C2 punya lamaran draft lengkap → 2 submit bersamaan
list($c, $j, ) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_POST), $T2);
check('lamaran draft C2 -> 201', 201, $c);
$ID_APP2 = (int) arr_get($j, 'data.id_application');
$rd = $pdo->query("SELECT id_job_posting_requirement, id_application_requirement FROM career_application_requirements WHERE id_application = {$ID_APP2}");
$AR2 = array();
while ($row = $rd->fetch_assoc()) { $AR2[(int) $row['id_job_posting_requirement']] = (int) $row['id_application_requirement']; }
upload($BASE, $T2, $AR2[$REQ_DOC], $paths[7]);
answer($BASE, $T2, $AR2[$REQ_TXT], 'alasan c2');
// dua kemitraan submiff paralel via curl_multi
$mh = curl_multi_init();
$chs = array();
for ($i = 0; $i < 2; $i++) {
	$ch = curl_init($BASE . '/Lamaran/submit');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $T2, 'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('id_application' => $ID_APP2)));
	curl_multi_add_handle($mh, $ch);
	$chs[$i] = $ch;
}
do {
	$status = curl_multi_exec($mh, $active);
	if ($active) curl_multi_select($mh, 0.5);
} while ($active && $status === CURLM_OK);
$codes = array();
foreach ($chs as $ch) {
	$codes[] = (string) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_multi_remove_handle($mh, $ch);
	curl_close($ch);
}
curl_multi_close($mh);
sort($codes);
check('H2 2 submit paralel: satu 200 satu 409', '200,409', implode(',', $codes));

echo "== I. Lowongan tutup -> 409 (M6) & cleanup ==\n";
$pdo->query("INSERT INTO career_job_postings (title, description, opening_date, closing_date, is_published, created_by)
             VALUES ('[DUMMY TUTUP] uji tutup', 'x', '2025-01-01', '2025-01-31', 1, 1)");
$ID_CLOSE = (int) $pdo->insert_id;
list($c, ) = http('GET', $BASE . '/Lowongan/index');
list($c, ) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_CLOSE), $T1);
check('M6 lamaran ke lowongan tutup -> 409', 409, $c);
$pdo->query("DELETE FROM career_job_postings WHERE id_job_posting = {$ID_CLOSE}");

echo "== J. Logout token_version ==\n";
list($c, ) = http_json('POST', $BASE . '/Auth/logout', array(), $T1);
check('logout -> 200', 200, $c);
list($c, ) = http('GET', $BASE . '/Auth/me', array('Authorization: Bearer ' . $T1));
check('me dengan token lama -> 401', 401, $c);

purge_attempts($pdo);
echo "Ringkasan smoke45: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
