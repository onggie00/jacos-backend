<?php
/**
 * Smoke test apicareer (JACOS Careers, Fase 4).
 *
 * Pemakaian: php tests/api/smoke.php [BASE_URL]
 *   BASE_URL default: http://jacos.test:8080/apicareer
 *   Butuh curl dalam PATH (Git Bash / Windows 10+ punya curl).
 *
 * PRASYARAT: di DB target minimal ada 1 lowongan ter-publish dengan 1
 * requirement bertipe document (dummy). Lihat Bagian G
 * docs/PHASES/PHASE4_REPORT.md untuk SQL data dummy.
 *
 * Script ini MEMBUAT data uji di jacos_db (kandidat "smoke*", lamaran,
 * dokumen dummy). Hanya jalankan di environment dev.
 */

$BASE = (isset($argv[1]) && $argv[1] !== '') ? rtrim($argv[1], '/') : 'http://jacos.test:8080/apicareer';
$D = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'career-smoke-' . getmypid();
@mkdir($D, 0700, true);
register_shutdown_function(function () use ($D) { @array_map('unlink', glob($D . DIRECTORY_SEPARATOR . '*')); @rmdir($D); });

$pdfPath = $D . '/dokumen_dummy.pdf';
file_put_contents($pdfPath, "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF");
$pngPath = $D . '/dokumen_dummy.png';
file_put_contents($pngPath, 'konten bukan gambar');

$pass = 0; $fail = 0;

/** cek kode HTTP yang diharapkan vs kenyataan */
function pass_fail($nama, $harap, $terima) {
    global $pass, $fail;
    if ((string) $harap === (string) $terima) {
        $pass++;
        echo "PASS  $nama (HTTP $terima)\n";
    } else {
        $fail++;
        echo "FAIL  $nama (harap $harap, terima $terima)\n";
    }
}

/** kirim request via PHP curl ext; return array(STATUS, BODY) */
function http($method, $url, $headers = array(), $body = null, $files = array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
	if (count($headers) > 0) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if (count($files) > 0) {
		$post = (array) $body;
		foreach ($files as $field => $path) {
			$post[$field] = new CURLFile($path);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	} elseif ($body !== null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	}
	$raw = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($raw === false) {
		echo 'CURL ERR: ' . curl_error($ch) . "\n";
		$raw = '';
	}
	curl_close($ch);
	return array((string) $code, (string) $raw);
}

/** return array(STATUS, ARRAY json) */
function http_json($method, $url, $body, $token = null) {
    $headers = array('Content-Type: application/json');
    if ($token !== null) $headers[] = 'Authorization: Bearer ' . $token;
    list($code, $raw) = http($method, $url, $headers, json_encode($body));
    $json = json_decode($raw, true);
    return array($code, is_array($json) ? $json : null);
}

function arr_get($arr, $path, $default = null) {
    $cur = $arr;
    foreach (explode('.', $path) as $k) {
        if (!is_array($cur) || !array_key_exists($k, $cur)) return $default;
        $cur = $cur[$k];
    }
    return $cur;
}

$EMAIL = 'smoke' . time() . '@example.test';

// purge rate-limit (H3 menghitung SEMUA percobaan per IP/jam — jangan kunci suite)
$mysqli = new mysqli('127.0.0.1', 'root', '', 'jacos_db');
$mysqli->query('DELETE FROM career_login_attempts');
function smoke_purge($mysqli) { $mysqli->query('DELETE FROM career_login_attempts'); }

// ---------- 1. Auth ----------
echo "== 1. Auth ==\n";
list($c, ) = http_json('POST', $BASE . '/Auth/login', array());
pass_fail('login tanpa body -> 400', 400, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array(
    'email' => $EMAIL, 'password' => 'RahasiaSmoke123', 'full_name' => 'Kandidat Smoke',
));
pass_fail('register -> 201', 201, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array(
    'email' => $EMAIL, 'password' => 'RahasiaSmoke123', 'full_name' => 'Kandidat Smoke',
));
pass_fail('register dup -> 409', 409, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/register', array(
    'email' => 'pendek-smoke@example.test', 'password' => 'abc', 'full_name' => 'P',
));
pass_fail('register password pendek -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Auth/login', array(
    'email' => $EMAIL, 'password' => 'SalahSmoke999',
));
pass_fail('login salah -> 401', 401, $c);

list($c, $j) = http_json('POST', $BASE . '/Auth/login', array(
    'email' => $EMAIL, 'password' => 'RahasiaSmoke123',
));
$TOKEN = arr_get($j, 'data.token');
if (!is_string($TOKEN) || $TOKEN === '') {
    echo "FAIL  token login tidak terbaca — hentikan.\n";
    exit(1);
}
$AUTH = array('Authorization: Bearer ' . $TOKEN);

// ---------- 2. Profil & item ----------
echo "== 2. Profil & item ==\n";
list($c, ) = http('GET', $BASE . '/Auth/me', $AUTH);
pass_fail('me -> 200', 200, $c);
list($c, ) = http('GET', $BASE . '/Auth/me');
pass_fail('me tanpa token -> 401', 401, $c);
list($c, ) = http_json('PUT', $BASE . '/Profil/index', array('gender' => 'opsen'), $TOKEN);
pass_fail('profil gender invalid -> 422', 422, $c);
list($c, ) = http_json('POST', $BASE . '/Profil_item/index', array(
    'item_type' => 'absurd', 'title' => 'X', 'institution' => 'Y',
), $TOKEN);
pass_fail('item type invalid -> 422', 422, $c);
list($c, $j) = http_json('POST', $BASE . '/Profil_item/index', array(
    'item_type' => 'education', 'title' => 'S1 Smoke', 'institution' => 'Universitas Smoke',
), $TOKEN);
pass_fail('item education -> 201', 201, $c);
list($c, ) = http('PUT', $BASE . '/Profil_item/detail/999999', $AUTH, json_encode(array('title' => 'X')));
pass_fail('item milik orang lain -> 404', 404, $c);

// ---------- 3. Lowongan ----------
echo "== 3. Lowongan ==\n";
list($c, $j) = http('GET', $BASE . '/Lowongan/index');
$low = json_decode($j, true);
$list = arr_get($low, 'data', array());
if (!is_array($list) || count($list) === 0) {
    echo "SKIP  lowongan dummy belum ada (prasyarat: Bagian G PHASE4_REPORT.md)\n";
    echo "Ringkasan: PASS=$pass FAIL=$fail\n";
    exit(1);
}
$ID_POST = arr_get($list[0], 'id_job_posting');
list($c, ) = http('GET', $BASE . '/Lowongan/detail/' . $ID_POST);
pass_fail('lowongan detail -> 200', 200, $c);
list($c, ) = http('GET', $BASE . '/Lowongan/detail/999999');
pass_fail('lowongan detail tidak ada -> 404', 404, $c);

// ---------- 4. Lamaran + dokumen + submit ----------
echo "== 4. Lamaran + dokumen + submit ==\n";
list($c, $j) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_POST), $TOKEN);
pass_fail('lamaran draft -> 201', 201, $c);
$ID_APP = arr_get($j, 'data.id_application');
list($c, ) = http_json('POST', $BASE . '/Lamaran/index', array('id_job_posting' => $ID_POST), $TOKEN);
pass_fail('lamaran dup -> 409', 409, $c);

list($c, $j) = http('GET', $BASE . '/Lamaran/detail/' . $ID_APP, $AUTH);
$det = json_decode($j, true);
$ID_REQ = arr_get($det, 'data.requirements.0.id_application_requirement');
if (!is_numeric($ID_REQ) || (int) $ID_REQ < 1) {
    echo "SKIP  lowongan ini tidak punya requirement file (atur di Bagian G)\n";
    echo "Ringkasan: PASS=$pass FAIL=$fail\n";
    exit(1);
}

list($c, ) = http('POST', $BASE . '/Lamaran_dokumen/upload', $AUTH,
    array('id_application_requirement' => $ID_REQ), array('document' => $pngPath));
pass_fail('upload ext/MIME salah -> 422', 422, $c);

list($c, $j) = http('POST', $BASE . '/Lamaran_dokumen/upload', $AUTH,
    array('id_application_requirement' => $ID_REQ), array('document' => $pdfPath));
pass_fail('upload pdf -> 201', 201, $c);
$ID_DOC = arr_get(json_decode($j, true), 'data.id_application_document');

list($c, ) = http('POST', $BASE . '/Lamaran_dokumen/upload', $AUTH,
    array('id_application_requirement' => 999999), array('document' => $pdfPath));
pass_fail('upload requirement bukan milik -> 404', 404, $c);

// H1: lengkapi jawaban persyaratan wajib non-berkas sebelum submit
$ID_REQ_TXT = 0;
list($c, $jd) = http('GET', $BASE . '/Lamaran/detail/' . $ID_APP, $AUTH);
$det = json_decode($jd, true);
if (is_array($det)) {
	foreach (arr_get($det, 'data.requirements', array()) as $r) {
		if (arr_get($r, 'requirement_type') === 'required'
			AND in_array(arr_get($r, 'input_type'), array('text','number','select'))
			AND (int) arr_get($r, 'id_application_requirement') > 0)
		{
			$ID_REQ_TXT = (int) arr_get($r, 'id_application_requirement');
		}
	}
}
if ($ID_REQ_TXT > 0) {
	list($c, ) = http_json('PUT', $BASE . '/Lamaran_jawaban', array(
		'id_application_requirement' => $ID_REQ_TXT, 'answer' => 'Alasan uji smoke'), $TOKEN);
}

list($c, ) = http_json('POST', $BASE . '/Lamaran/submit', array('id_application' => $ID_APP), $TOKEN);
pass_fail('submit -> 200', 200, $c);
list($c, $j) = http_json('POST', $BASE . '/Lamaran/submit', array('id_application' => $ID_APP), $TOKEN);
pass_fail('submit lagi -> 409', 409, $c);
list($c, ) = http_json('POST', $BASE . '/Lamaran/submit', array('id_application' => 999999), $TOKEN);
pass_fail('submit bukan milik -> 404', 404, $c);

list($c, $j) = http('GET', $BASE . '/Lamaran/detail/' . $ID_APP, $AUTH);
$det = json_decode($j, true);
pass_fail('detail stage terisi -> 200', 200, $c);
$stages = arr_get($det, 'data.stages', array());
if (is_array($stages) && count($stages) === 5) pass_fail('5 stage pending', 5, 5); else pass_fail('5 stage pending', 5, count((array) $stages));

// ---------- 5. Dokumen unduh + notifikasi ----------
echo "== 5. Unduh dan notifikasi ==\n";
list($c, ) = http('GET', $BASE . '/Lamaran_dokumen/detail/' . $ID_DOC, $AUTH);
pass_fail('unduh dokumen -> 200', 200, $c);
list($c, ) = http('GET', $BASE . '/Lamaran_dokumen/detail/999999', $AUTH);
pass_fail('unduh dokumen bukan milik -> 404', 404, $c);
list($c, $j) = http('GET', $BASE . '/Notifikasi/index', $AUTH);
$notes = arr_get(json_decode($j, true), 'data', array());
pass_fail('notifikasi >=1 setelah submit', 1, (is_array($notes) && count($notes) >= 1) ? 1 : 0);
$ID_NOT = arr_get($notes, '0.id_notification');
list($c, ) = http('PUT', $BASE . '/Notifikasi/read/' . $ID_NOT, $AUTH);
pass_fail('notifikasi read -> 200', 200, $c);

// ---------- 6. Logout + token invalid ----------
echo "== 6. Logout ==\n";
list($c, ) = http_json('POST', $BASE . '/Auth/logout', array(), $TOKEN);
pass_fail('logout -> 200', 200, $c);
list($c, ) = http('GET', $BASE . '/Auth/me', $AUTH);
pass_fail('me dengan token lama -> 401', 401, $c);

echo "Ringkasan: PASS=$pass FAIL=$fail\n";
exit($fail === 0 ? 0 : 1);
