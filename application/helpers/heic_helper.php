<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * HEIC to JPEG conversion helper.
 * Tries: 1) heif-convert (libheif-examples), 2) python3 pillow-heif
 *
 * Install:
 *   apt install libheif-examples python3-pip && pip3 install pillow-heif
 */

// Guard for CLI/test context where CI log_message() is unavailable
if (!function_exists('log_message')) {
    function log_message($level = 'debug', $msg = '') { /* noop */ }
}

if (!function_exists('convert_heic_to_jpeg')) {
    /**
     * Convert HEIC file to JPEG. Cached: only re-converts if original is newer.
     *
     * @param string $file_path  Absolute path to HEIC file
     * @return string|false      Absolute path to JPEG, or false on failure
     */
    function convert_heic_to_jpeg($file_path)
    {
        if (!is_file($file_path)) {
            log_message('error', 'HEIC convert: file not found: ' . $file_path);
            return false;
        }

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        if (!in_array($ext, array('heic', 'heif'))) {
            return false;
        }

        $jpeg_path = preg_replace('/\.(heic|heif)$/i', '.jpg', $file_path);

        // Skip if cached JPEG exists and is newer than original
        if (is_file($jpeg_path) && filemtime($jpeg_path) >= filemtime($file_path)) {
            return $jpeg_path;
        }

        // Try 1: heif-convert (fast, handles most HEIC files)
        $result = _heic_via_heif_convert($file_path, $jpeg_path);
        if ($result) return $jpeg_path;

        // Try 2: python3 pillow-heif (handles Xiaomi, newer iPhone HEIC)
        $result = _heic_via_python($file_path, $jpeg_path);
        if ($result) return $jpeg_path;

        log_message('error', 'HEIC convert: all methods failed for ' . basename($file_path));
        return false;
    }
}

/**
 * Try heif-convert CLI tool.
 */
function _heic_via_heif_convert($src, $dst)
{
    $bin = trim(shell_exec('which heif-convert 2>/dev/null'));
    if (empty($bin)) return false;

    $cmd = sprintf('%s %s %s 2>&1', escapeshellarg($bin), escapeshellarg($src), escapeshellarg($dst));
    $output = array();
    $status = 0;
    exec($cmd, $output, $status);

    if ($status === 0 && file_exists($dst)) {
        return true;
    }

    log_message('debug', 'HEIC heif-convert failed (status=' . $status . '): ' . implode(' ', $output));
    return false;
}

/**
 * Try python3 with pillow-heif library.
 */
function _heic_via_python($src, $dst)
{
    $bin = trim(shell_exec('which python3 2>/dev/null'));
    if (empty($bin)) return false;

    $py = sprintf(
        'from PIL import Image; from pillow_heif import register_heif_opener; register_heif_opener(); Image.open(%s).save(%s, "JPEG", quality=85)',
        escapeshellarg($src),
        escapeshellarg($dst)
    );

    $cmd = sprintf('%s -c %s 2>&1', escapeshellarg($bin), escapeshellarg($py));
    $output = array();
    $status = 0;
    exec($cmd, $output, $status);

    if ($status === 0 && file_exists($dst)) {
        return true;
    }

    log_message('debug', 'HEIC python failed (status=' . $status . '): ' . implode(' ', $output));
    return false;
}
