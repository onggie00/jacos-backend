#!/usr/bin/env php
<?php
/**
 * tool/kamus/build_kamus.php — CLI pembangun language file kamus.
 *
 * Sumber : Berkas_sample/kamus_statis.csv (hasil review fase 4)
 * Output : application/language/<folder>/kamus_lang.php per bahasa
 *          (konvensi CI3: $lang['<kunci>'] = '<english>';)
 * Kunci  = teks Indonesia ternormalisasi + lowercase.
 *
 * Aturan:
 * - baris english_gb_draft kosong -> LEWATI (JANGAN diterjemahkan)
 * - jenis 'pesan-dinamis' -> LEWATI (mengandung variabel; fase lain)
 * - mengandung_variabel = 'ya' -> LEWATI
 *
 * Pemakaian: php tool/kamus/build_kamus.php [path-csv]
 * (dijalankan dari root project)
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$root = dirname(__DIR__, 2);
$csvPath = isset($argv[1]) ? $argv[1] : $root . '/Berkas_sample/kamus_statis.csv';
if (!is_file($csvPath)) {
    exit("CSV tidak ditemukan: $csvPath\n");
}

/* mapping kode bahasa -> folder language (harus konsisten dgn
 * config/translator.php translator_language_folder) */
$LANG_FOLDERS = array(
    'en-GB' => 'english',
);

/* normalisasi sama dgn translator_normalize() */
function kamus_normalize($t)
{
    $t = (string) $t;
    $t = str_replace("\r\n", "\n", $t);
    $t = preg_replace('/ {2,}/', ' ', $t);
    $t = trim($t);
    if (function_exists('normalizer_is_normalized')) {
        if (!normalizer_is_normalized($t, normalizer::FORM_C)) {
            $t = normalizer_normalize($t, normalizer::FORM_C);
        }
    }
    return $t;
}

function kamus_escape($s)
{
    return str_replace(array('\\', "'"), array('\\\\', "\\'"), $s);
}

$fh = fopen($csvPath, 'r');
if (!$fh) {
    exit("Gagal buka CSV\n");
}
// BOM
$first = fgets($fh, 4);
if (substr($first, 0, 3) !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
    rewind($fh);
}

$perLang = array();
$total = 0; $skipped_empty = 0; $skipped_dinamis = 0; $skipped_var = 0; $written = 0;

while (($row = fgetcsv($fh, 0, ',')) !== false) {
    if (count($row) < 7) continue;
    list($sumber, $jenis, $cnt, $lokasi, $var, $en, $catatan) = $row;
    $total++;
    if ($jenis === 'pesan-dinamis' || strpos($sumber, '<<<DINAMIS>>>') === 0) { $skipped_dinamis++; continue; }
    if (trim($var) === 'ya') { $skipped_var++; continue; }
    if (trim($en) === '') { $skipped_empty++; continue; }
    $kunci = mb_strtolower(kamus_normalize($sumber), 'UTF-8');
    if ($kunci === '') continue;
    foreach ($LANG_FOLDERS as $kode => $folder) {
        $perLang[$folder][$kunci] = $en;
        $written++;
    }
}
fclose($fh);

foreach ($perLang as $folder => $lang) {
    $dir = $root . '/application/language/' . $folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $file = $dir . '/kamus_lang.php';
    $out = "<?php\n";
    $out .= "/**\n";
    $out .= " * language/$folder/kamus_lang.php — kamus statis switch bahasa.\n";
    $out .= " * GENERATED oleh tool/kamus/build_kamus.php dari Berkas_sample/kamus_statis.csv.\n";
    $out .= " * JANGAN edit manual — perbarui CSV lalu jalankan ulang tool.\n";
    $out .= " * Kunci = teks Indonesia ternormalisasi + lowercase.\n";
    $out .= " */\n\ndefined('BASEPATH') OR exit('No direct script access allowed');\n\n" . '$lang = array();' . "\n\n";
    uksort($lang, 'strnatcasecmp');
    foreach ($lang as $kunci => $en) {
        $out .= "\$lang['" . kamus_escape($kunci) . "'] = '" . kamus_escape($en) . "';\n";
    }
    file_put_contents($file, $out);
    echo "$folder: " . count($lang) . " entri -> $file\n";
}

echo "Total baris CSV: $total | ditulis: $written | skip kosong: $skipped_empty | skip dinamis: $skipped_dinamis | skip var: $skipped_var\n";
