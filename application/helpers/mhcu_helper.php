<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extract nama depan dari nama lengkap.
 *
 * Aturan:
 * 1. Buang gelar belakang (setelah koma): "Gerry Andika, S.Kom" -> "Gerry Andika"
 * 2. Skip gelar depan dari daftar: H., Dr., dr., Dra., drg., Ns., Ir., Prof., apt., Sp.
 * 3. Jika token pertama >= 3 huruf, ambil 1 token saja.
 *    Jika token pertama < 3 huruf (mis. I, H), gabung TEPAT 1 token lagi.
 *
 * Test:
 *   "Gerry Andika Sandy Susantio, S.Kom" -> "Gerry"
 *   "H. Soemadi" -> "Soemadi"
 *   "I Gusti Ngurah Aditya, S.Pd.H." -> "I Gusti Ngurah"
 *   "Holiyah Sari, S.Pd." -> "Holiyah"
 *   "Dr. Ahmad Fauzi, M.Pd." -> "Ahmad Fauzi"
 *   "Hawa Nur Azizah, S.Pd." -> "Hawa Nur Azizah"
 */
function mhcu_extract_nama_depan($nama_lengkap)
{
    if (empty($nama_lengkap)) return '';

    // 1. Buang gelar belakang (setelah koma pertama)
    $parts = explode(',', $nama_lengkap, 2);
    $depan = trim($parts[0]);

    // 2. Daftar gelar depan (case-insensitive)
    $gelar_depan = array(
        'H.', 'h.',
        'Dr.', 'dr.', 'DR.',
        'Dra.', 'dra.',
        'drg.', 'Drg.',
        'Ns.', 'ns.',
        'Ir.', 'ir.',
        'Prof.', 'prof.',
        'apt.', 'Apt.',
        'Sp.', 'sp.',
    );

    $tokens = preg_split('/\s+/', $depan);
    if (empty($tokens)) return '';

    // Strip karakter non-alpha di awal token pertama (mis. '?Dr.' -> 'Dr.')
    if (isset($tokens[0]) && !empty($tokens[0])) {
        $tokens[0] = ltrim($tokens[0], '?.,!;:-');
    }

    // Skip gelar depan
    $idx = 0;
    while ($idx < count($tokens) && in_array($tokens[$idx], $gelar_depan)) {
        $idx++;
    }

    // 3. Ambil token pertama (selalu)
    $nama_depan = (isset($tokens[$idx]) && $tokens[$idx] !== '') ? $tokens[$idx] : '';
    $total_len = strlen($nama_depan);
    $idx++;

    // 4. Kalau token pertama < 3 huruf (mis. I, H), gabung TEPAT 1 token lagi.
    //    Spec: 'I Gusti Ngurah' -> 'I Gusti', 'I Gusti Made' -> 'I Gusti Made'.
    if ($total_len < 3 && isset($tokens[$idx])) {
        $nama_depan .= ' ' . $tokens[$idx];
    }

    return trim($nama_depan);
}

/**
 * Resolusi sapaan berdasarkan gender kode ('L' / 'P').
 * Kalau $nama_depan diberikan, sapaan = 'Bapak Gerry' / 'Ibu Ani'.
 */
function mhcu_sapaan($gender, $nama_depan = '')
{
    $g = strtoupper(trim((string)$gender));
    // ponytail: gender kosong/tak dikenal (demografi tidak diisi) → sapaan netral, jangan default "Bapak"
    if ($g !== 'L' && $g !== 'P') {
        $base = 'Bapak/Ibu';
    } else {
        $base = ($g === 'P') ? 'Ibu' : 'Bapak';
    }
    $nama = trim((string)$nama_depan);
    return ($nama !== '') ? ($base . ' ' . $nama) : $base;
}

/**
 * Replace placeholder "Bapak/Ibu" dan "Bapak / Ibu" di teks dengan sapaan sesuai gender.
 * Kalau $nama_depan diberikan, sapaan menjadi 'Bapak Gerry' / 'Ibu Ani'.
 */
function mhcu_apply_sapaan($text, $gender, $nama_depan = '')
{
    if (empty($text)) return $text;
    $sapaan = mhcu_sapaan($gender, $nama_depan);
    $text = str_replace('Bapak/Ibu', $sapaan, $text);
    $text = str_replace('Bapak / Ibu', $sapaan, $text);
    $text = str_replace('Bapak/Ibu', $sapaan, $text);
    return $text;
}
