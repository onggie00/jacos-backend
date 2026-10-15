<?php
/**
 * Smoke test patch 5C.1 — regresi perbaikan backend hasil review Fase 5C.
 *   1) Download dokumen: header Content-Type = mime asli (bukan text/html).
 *   2) GET /Lamaran/index: item memuat id_job_posting (integer).
 *
 * Pemakaian: php tests/api/smoke5c1.php [BASE_URL]
 * Prasyarat: sama seperti smoke45.php (jacos_db lokal + [DUMMY TEST] lowongan).
 * Membuat & menghapus data uji sendiri di jacos_db LOKAL. JANGAN di produksi.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080/apicareer';

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
	return array($code, is_array($json) ? $json : null, $raw);
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
		$fail++; echo "FAIL  $nama (harap " . print_r($harap, true) . ", terima " . print_r($terima, true) . ")\n";
	}
}

$pdo = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
if ($pdo->connect_errno) { echo "FAIL  mysqli jacos_db: {$pdo->connect_error}\n"; exit(1); }

$EMAIL = 'smoke5c1-' . date('ymdHis') . '@example.test';
$hash = password_hash('Rahasia5C1Aa', PASSWORD_BCRYPT);
$pdo->query("INSERT INTO career_candidates (email, password_hash, full_name, is_active, token_version, failed_login_count)
             VALUES ('{$EMAIL}', '{$hash}', 'Kandidat Smoke5C1', 1, 1, 0)");
$ID = (int) $pdo->query("SELECT id_candidate FROM career_candidates WHERE email = '{$EMAIL}'")->fetch_assoc()['id_candidate'];
$pdo->query("INSERT INTO career_candidate_profiles (id_candidate, summary) VALUES ({$ID}, 'profil 5c1')");

// kunci JWT + token
$cfile = file_get_contents(__DIR__ . '/../../application/config/credentials.php');
preg_match("/define\(\s*'CAREER_JWT_KEY'\s*,\s*'([^']+)'/", $cfile, $m);
require_once __DIR__ . '/../../application/libraries/Jwt/JWT.php';
use Firebase\JWT\JWT as TestJWT;
$now = time();
$T = TestJWT::encode(array('iss' => 'jacos-career', 'sub' => (string) $ID, 'ver' => 1, 'iat' => $now, 'exp' => $now + 3600), $m[1], 'HS256');

// lowongan [DUMMY TEST] + requirement document
$row = $pdo->query("SELECT id_job_posting FROM career_job_postings WHERE title LIKE '[DUMMY TEST]%' ORDER BY id_job_posting LIMIT 1")->fetch_assoc();
$ID_POST = (int) $row['id_job_posting'];
$row = $pdo->query("SELECT id_job_posting_requirement FROM career_job_posting_requirements WHERE id_job_posting = {$ID_POST} AND input_type IN ('file','document') ORDER BY display_order LIMIT 1")->fetch_assoc();
$REQ_DOC = (int) $row['id_job_posting_requirement'];

// PDF uji
$tmp = sys_get_temp_dir() . '/smoke5c1-' . getmypid() . '.pdf';
file_put_contents($tmp, "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Size 1/Root 1 0 R>>\n%%EOF\n");

echo "== 1. Lamaran index memuat id_job_posting (patch) ==\n";
list($c, $j, ) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_POST), $T);
check('lamaran draft -> 201', 201, $c);
$ID_APP = (int) $j['data']['id_application'];
list($c, $j, ) = http_json('GET', $BASE . '/Lamaran/index', array(), $T);
$item = null;
foreach ($j['data'] as $it) { if ((int) $it['id_application'] === $ID_APP) { $item = $it; break; } }
check('item list punya id_job_posting', $ID_POST, $item === null ? null : ($item['id_job_posting'] ?? null));
check('id_job_posting integer (bukan string)', 'integer', gettype($item === null ? 0 : $item['id_job_posting']));

echo "== 2. Upload + unduh Content-Type asli (patch) ==\n";
$rd = $pdo->query("SELECT id_application_requirement FROM career_application_requirements WHERE id_application = {$ID_APP} AND id_job_posting_requirement = {$REQ_DOC}")->fetch_assoc();
$AR = (int) $rd['id_application_requirement'];
list($c, , $upraw) = http('POST', $BASE . '/Lamaran_dokumen/upload',
	array('Authorization: Bearer ' . $T),
	array('id_application_requirement' => $AR),
	array('document' => $tmp));
check('upload pdf -> 201', 201, $c);
$up = json_decode($upraw, true);
$ID_DOC = (int) ($up['data']['id_application_document'] ?? 0);
check('upload beri id_application_document', 1, $ID_DOC > 0 ? 1 : 0);
list($c, $head, ) = http('GET', $BASE . '/Lamaran_dokumen/detail/' . $ID_DOC, array('Authorization: Bearer ' . $T));
$ct = header_val($head, 'Content-Type');
check('unduh Content-Type = application/pdf (bukan text/html)', 'application/pdf', preg_replace('/\s*;.*$/', '', (string) $ct));
check('unduh masih nosniff', 'nosniff', header_val($head, 'X-Content-Type-Options'));
$cd = header_val($head, 'Content-Disposition');
check('unduh disposition attachment', 1, (stripos((string) $cd, 'attachment') === 0) ? 1 : 0);
check('unduh Content-Length terkirim (>0)', 1, ((int) header_val($head, 'Content-Length') > 0) ? 1 : 0);

echo "== cleanup ==\n";
$pdo->query("DELETE d FROM career_application_documents d
             JOIN career_application_requirements r ON r.id_application_requirement = d.id_application_requirement
             WHERE r.id_application = {$ID_APP}");
$pdo->query("DELETE s FROM career_application_profile_snapshots s JOIN career_applications a ON a.id_application = s.id_application WHERE a.id_application = {$ID_APP}");
$pdo->query("DELETE a FROM career_applications a WHERE a.id_application = {$ID_APP}");
$pdo->query("DELETE FROM career_candidate_profiles WHERE id_candidate = {$ID}");
$pdo->query("DELETE FROM career_candidates WHERE id_candidate = {$ID}");
@unlink($tmp);
echo "Ringkasan smoke5c1: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
