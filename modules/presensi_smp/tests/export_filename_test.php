<?php

$controller_path = dirname(__DIR__) . '/controllers/backend/Presensi_smp.php';
$source = file_get_contents($controller_path);
$failures = array();

function assert_filename_same($expected, $actual, $message, &$failures)
{
	if ($expected !== $actual) {
		$failures[] = $message . ' Expected: ' . var_export($expected, true) . '; actual: ' . var_export($actual, true);
	}
}

if (strpos($source, 'private function _build_export_filename(') === false) {
	$failures[] = 'Helper pembentukan filename export belum tersedia.';
}

if (substr_count($source, 'header(\'Content-Disposition: attachment;filename="\' . $filename . \'"\');') !== 3) {
	$failures[] = 'Ketiga export belum menggunakan filename hasil helper.';
}

if (!$failures) {
	define('BASEPATH', dirname(__DIR__, 3) . '/system/');
	class Admin
	{
	}

	require_once $controller_path;

	$reflection = new ReflectionClass('Presensi_smp');
	$controller = $reflection->newInstanceWithoutConstructor();
	$method = $reflection->getMethod('_build_export_filename');
	$method->setAccessible(true);

	assert_filename_same(
		'Presensi SMP - 2026-07-01-2026-07-20-VII A.xls',
		$method->invoke($controller, '2026-07-01', '2026-07-20', 'VII A'),
		'Format filename export tidak sesuai.',
		$failures
	);
	assert_filename_same(
		'Presensi SMP - 2026-07-01-2026-07-20-VII-A.xls',
		$method->invoke($controller, '2026-07-01', '2026-07-20', 'VII/A'),
		'Label filter pada filename belum disanitasi.',
		$failures
	);
	assert_filename_same(
		'Presensi SMP - 2026-07-01-2026-07-20-Semua.xls',
		$method->invoke($controller, '2026-07-01', '2026-07-20', ''),
		'Filename tanpa filter kelas atau tingkatan harus memakai fallback Semua.',
		$failures
	);
}

if ($failures) {
	foreach ($failures as $failure) {
		fwrite(STDERR, 'FAIL: ' . $failure . PHP_EOL);
	}
	exit(1);
}

echo 'PASS: Filename export presensi SMP sesuai filter.' . PHP_EOL;
