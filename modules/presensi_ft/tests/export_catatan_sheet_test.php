<?php

$controller_path = dirname(__DIR__) . '/controllers/backend/Presensi_ft.php';
$source = file_get_contents($controller_path);
$failures = array();

function assert_source_contains($source, $needle, $message, &$failures)
{
	if (strpos($source, $needle) === false) {
		$failures[] = $message;
	}
}

function assert_source_not_contains($source, $needle, $message, &$failures)
{
	if (strpos($source, $needle) !== false) {
		$failures[] = $message;
	}
}

function assert_same($expected, $actual, $message, &$failures)
{
	if ($expected !== $actual) {
		$failures[] = $message . ' Expected: ' . var_export($expected, true) . '; actual: ' . var_export($actual, true);
	}
}

assert_source_contains(
	$source,
	"private function _create_catatan_pelajaran_sheet(",
	'Helper pembentukan sheet catatan belum tersedia.',
	$failures
);
assert_source_contains(
	$source,
	"'TANGGAL', 'JAM KE', 'STATUS HADIR', 'KETERANGAN', 'UPDATED BY'",
	'Kolom detail sheet catatan belum lengkap.',
	$failures
);
assert_source_contains(
	$source,
	'$suffix = \'-Catatan\';',
	'Nama sheet catatan belum menggunakan akhiran -Catatan.',
	$failures
);
assert_source_not_contains(
	$source,
	'"\nCatatan Pelajaran:\n"',
	'Catatan pelajaran masih digabungkan ke sel presensi harian atau bulanan.',
	$failures
);
assert_source_not_contains(
	$source,
	'$sheet->setCellValue($catatanCol.$row2, \'CATATAN\');',
	'Kolom CATATAN masih tersedia pada sheet presensi periode.',
	$failures
);
assert_same(
	3,
	substr_count($source, '$this->_create_catatan_pelajaran_sheet('),
	'Sheet catatan harus dibuat pada export harian, bulanan, dan periode.',
	$failures
);
assert_source_contains(
	$source,
	"->where('jenjang', 'ft')",
	'Query catatan harus dibatasi untuk jenjang FT.',
	$failures
);
assert_source_contains(
	$source,
	"->where('tanggal_waktu >=', \$start_date . ' 00:00:00')",
	'Query catatan harus memiliki batas tanggal awal.',
	$failures
);
assert_source_contains(
	$source,
	"->where('tanggal_waktu <', \$end_exclusive)",
	'Query catatan harus memiliki batas tanggal akhir eksklusif.',
	$failures
);

if (!$failures) {
	define('BASEPATH', dirname(__DIR__, 3) . '/system/');
	class Admin
	{
	}

	require_once dirname(__DIR__, 3) . '/application/libraries/Excel/PHPExcel.php';
	require_once $controller_path;

	$reflection = new ReflectionClass('Presensi_ft');
	$controller = $reflection->newInstanceWithoutConstructor();

	$title_method = $reflection->getMethod('_get_export_sheet_title');
	$title_method->setAccessible(true);
	assert_same(
		'X-IPA-Catatan',
		$title_method->invoke($controller, 'X/IPA', true),
		'Nama sheet catatan harus menyanitasi karakter terlarang.',
		$failures
	);
	assert_same(
		23,
		PHPExcel_Shared_String::CountCharacters($title_method->invoke($controller, str_repeat('A', 40), false)),
		'Nama sheet utama harus memakai base maksimal 23 karakter.',
		$failures
	);
	$unicode_title = $title_method->invoke($controller, str_repeat('ก', 30), false);
	assert_same(23, PHPExcel_Shared_String::CountCharacters($unicode_title), 'Nama sheet Unicode harus dipotong per karakter.', $failures);
	assert_same(1, preg_match('//u', $unicode_title), 'Nama sheet Unicode harus tetap valid UTF-8.', $failures);

	$student = new stdClass();
	$student->id_siswa = 10;
	$student->nis = '12345';
	$student->nama_lengkap = 'Siswa Uji';
	$student->nama_kelas = 'X IPA';

	$catatan_map = array(
		10 => array(
			'2026-07-20' => array(
				array(
					'tanggal' => '2026-07-20',
					'jam_ke' => 2,
					'status_hadir' => 'Hadir',
					'keterangan' => 'Aktif berdiskusi',
					'updated_by' => 'Guru Uji'
				)
			)
		)
	);

	$excel = new PHPExcel();
	$base_sheet_method = $reflection->getMethod('_get_or_create_export_sheet');
	$base_sheet_method->setAccessible(true);
	$base_sheet_method->invoke($controller, $excel, 0, 'X IPA');

	$sheet_method = $reflection->getMethod('_create_catatan_pelajaran_sheet');
	$sheet_method->setAccessible(true);
	$sheet_method->invoke($controller, $excel, 1, $excel->getSheet(0)->getTitle(), array($student), $catatan_map);
	$sheet = $excel->getSheet(1);

	assert_same('X IPA-Catatan', $sheet->getTitle(), 'Nama sheet detail tidak sesuai.', $failures);
	assert_same('12345', $sheet->getCell('B4')->getValue(), 'NIS catatan tidak ditulis.', $failures);
	assert_same('20/07/2026', $sheet->getCell('E4')->getValue(), 'Tanggal catatan tidak diformat.', $failures);
	assert_same('Aktif berdiskusi', $sheet->getCell('H4')->getValue(), 'Keterangan catatan tidak ditulis.', $failures);

	$base_sheet_method->invoke($controller, $excel, 2, 'XI IPA');
	$sheet_method->invoke($controller, $excel, 3, $excel->getSheet(2)->getTitle(), array(), array());
	assert_same(4, $excel->getSheetCount(), 'Workbook dua kelas harus memiliki tepat empat sheet.', $failures);
	assert_same('X IPA', $excel->getSheet(0)->getTitle(), 'Sheet presensi kelas pertama salah urutan.', $failures);
	assert_same('XI IPA', $excel->getSheet(2)->getTitle(), 'Sheet presensi kelas kedua salah urutan.', $failures);
	assert_same('XI IPA-Catatan', $excel->getSheet(3)->getTitle(), 'Sheet catatan kelas kedua salah urutan.', $failures);

	$collision_excel = new PHPExcel();
	$base_sheet_method->invoke($controller, $collision_excel, 0, $title_method->invoke($controller, 'X/IPA', false));
	$sheet_method->invoke($controller, $collision_excel, 1, $collision_excel->getSheet(0)->getTitle(), array(), array());
	$base_sheet_method->invoke($controller, $collision_excel, 2, $title_method->invoke($controller, 'X:IPA', false));
	$sheet_method->invoke($controller, $collision_excel, 3, $collision_excel->getSheet(2)->getTitle(), array(), array());
	assert_same('X-IPA-2', $collision_excel->getSheet(2)->getTitle(), 'Collision sheet presensi harus diberi suffix angka.', $failures);
	assert_same('X-IPA-2-Catatan', $collision_excel->getSheet(3)->getTitle(), 'Collision sheet catatan harus tetap berakhiran -Catatan.', $failures);
	$base_sheet_method->invoke($controller, $collision_excel, 4, $title_method->invoke($controller, 'x/ipa', false), false);
	$sheet_method->invoke($controller, $collision_excel, 5, $collision_excel->getSheet(4)->getTitle(), array(), array());
	assert_same('x-ipa-3', $collision_excel->getSheet(4)->getTitle(), 'Collision nama sheet harus case-insensitive.', $failures);
	assert_same('x-ipa-3-Catatan', $collision_excel->getSheet(5)->getTitle(), 'Pasangan collision case-insensitive harus konsisten.', $failures);
	$base_sheet_method->invoke($controller, $collision_excel, 6, $title_method->invoke($controller, 'X-IPA-Catatan', false), false);
	$sheet_method->invoke($controller, $collision_excel, 7, $collision_excel->getSheet(6)->getTitle(), array(), array());
	assert_same('X-IPA-Catatan-2', $collision_excel->getSheet(6)->getTitle(), 'Nama kelas berakhiran -Catatan harus diperlakukan sebagai base.', $failures);
	assert_same('X-IPA-Catatan-2-Catatan', $collision_excel->getSheet(7)->getTitle(), 'Pasangan nama kelas berakhiran -Catatan harus deterministik.', $failures);

	$reverse_collision_excel = new PHPExcel();
	$base_sheet_method->invoke($controller, $reverse_collision_excel, 0, $title_method->invoke($controller, 'A-Catatan', false), false);
	$sheet_method->invoke($controller, $reverse_collision_excel, 1, $reverse_collision_excel->getSheet(0)->getTitle(), array(), array());
	$base_sheet_method->invoke($controller, $reverse_collision_excel, 2, $title_method->invoke($controller, 'A', false), false);
	$sheet_method->invoke($controller, $reverse_collision_excel, 3, $reverse_collision_excel->getSheet(2)->getTitle(), array(), array());
	assert_same('A-2', $reverse_collision_excel->getSheet(2)->getTitle(), 'Sheet utama harus mereservasi nama pasangan catatan.', $failures);
	assert_same('A-2-Catatan', $reverse_collision_excel->getSheet(3)->getTitle(), 'Collision urutan terbalik harus mempertahankan pasangan.', $failures);

	$long_collision_excel = new PHPExcel();
	try {
		$base_sheet_method->invoke($controller, $long_collision_excel, 0, $title_method->invoke($controller, str_repeat('A', 23) . '1', false), false);
		$sheet_method->invoke($controller, $long_collision_excel, 1, $long_collision_excel->getSheet(0)->getTitle(), array(), array());
		$base_sheet_method->invoke($controller, $long_collision_excel, 2, $title_method->invoke($controller, str_repeat('A', 23) . '2', false), false);
		$sheet_method->invoke($controller, $long_collision_excel, 3, $long_collision_excel->getSheet(2)->getTitle(), array(), array());
		assert_same(23, PHPExcel_Shared_String::CountCharacters($long_collision_excel->getSheet(2)->getTitle()), 'Base collision panjang harus tetap maksimal 23 karakter.', $failures);
		assert_same(31, PHPExcel_Shared_String::CountCharacters($long_collision_excel->getSheet(3)->getTitle()), 'Sheet catatan collision panjang harus tetap maksimal 31 karakter.', $failures);
	} catch (Exception $exception) {
		$failures[] = 'Collision label panjang tidak boleh menggagalkan workbook: ' . $exception->getMessage();
	}
}

if ($failures) {
	foreach ($failures as $failure) {
		fwrite(STDERR, "FAIL: " . $failure . PHP_EOL);
	}
	exit(1);
}

echo "PASS: Struktur export catatan pelajaran terpisah terdeteksi." . PHP_EOL;
