<?php

$paths = array(
    dirname(dirname(__FILE__)) . '/guru/Presensi_siswa_catatan.php',
    dirname(dirname(__FILE__)) . '/pimpinan/Presensi_siswa_catatan.php',
);
$failures = array();

foreach ($paths as $path) {
    if (!is_file($path)) {
        $failures[] = 'Controller tidak ditemukan: ' . $path;
        continue;
    }

    $source = file_get_contents($path);
    if (strpos($source, "post('status_hadir')") !== false || strpos($source, 'post("status_hadir")') !== false) {
        $failures[] = 'Controller masih membaca input status_hadir: ' . $path;
    }
    if (strpos($source, 'Validasi status_hadir') !== false || strpos($source, 'status_hadir tidak valid') !== false) {
        $failures[] = 'Controller masih memvalidasi status_hadir: ' . $path;
    }
    if (strpos($source, "'status_hadir'     => ''") === false && strpos($source, "'status_hadir'   => ''") === false) {
        $failures[] = 'Kolom status_hadir belum dipaksa kosong: ' . $path;
    }
}

if ($failures) {
    foreach ($failures as $failure) {
        fwrite(STDERR, 'FAIL: ' . $failure . PHP_EOL);
    }
    exit(1);
}

echo "PASS: status_hadir tidak menjadi input API." . PHP_EOL;
