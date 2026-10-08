<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Penerap whitelist aktif: jalan di struktur response (array/stdChildObject),
 * path notasi titik + wildcard "*", tipe nilai selain string tidak disentuh.
 * Key, struktur, urutan, tipe TIDAK berubah.
 */
class Translator_whitelist {

    private $whitelist;     // key => array(path => array('tipe'=>,'bersama'=>))
    private $excluded_keys; // set endpoint excluded

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->config('translate_whitelist');
        $aktif = $CI->config->item('translate_whitelist');
        $excl  = $CI->config->item('translate_whitelist_excluded');
        $this->whitelist = is_array($aktif) ? $aktif : array();
        $this->excluded_keys = array();
        if (is_array($excl)) {
            foreach ($excl as $k => $v) {
                $this->excluded_keys[strtolower($k)] = true;
            }
        }
    }

    /**
     * Spesifikasi path aktif utk endpoint. NULL jika endpoint tidak ada di
     * whitelist aktif (termasuk endpoint yang SELURUHNYA excluded — mereka
     * tidak pernah dimasukkan ke whitelist aktif). Excluded bersifat per
     * path; endpoint campuran (mis. presensi: label aktif + keterangan UGC)
     * tetap aktif hanya untuk path whitelist-nya.
     */
    public function endpoint_aktif($endpoint_key)
    {
        if (isset($this->whitelist[$endpoint_key])) {
            return $this->whitelist[$endpoint_key];
        }
        $k = strtolower(trim($endpoint_key));
        if (isset($this->whitelist[$k])) {
            return $this->whitelist[$k];
        }
        return null;
    }

    /**
     * Jalan di data, kembalikan daftar item teks kandidat terjemahan
     * dengan PATH KONKRET (indeks array numerik di-resolve). Setter
     * terapkan() menulis hasil lewat path tersebut.
     *
     * @param mixed  $data  array/stdClass response
     * @param string $endpoint_key
     * @return array list item: array('path' => array(segmen), 'text' =>, 'tipe' =>, 'bersama' =>)
     */
    public function kumpulkan(&$data, $endpoint_key)
    {
        $spec = $this->endpoint_aktif($endpoint_key);
        $items = array();
        if ($spec === null) {
            return $items;
        }
        foreach ($spec as $path => $meta) {
            if (!is_array($meta) || !isset($meta['tipe'])) {
                continue;
            }
            $this->jalan_path($data, explode('.', $path), $meta, $items, array());
        }
        return $items;
    }

    private function jalan_path(&$cur, array $segs, $meta, &$items, $resolved)
    {
        if (empty($segs)) {
            if (is_string($cur)) {
                $items[] = array(
                    'path'    => $resolved,
                    'text'    => $cur,
                    'tipe'    => $meta['tipe'],
                    'bersama' => !empty($meta['bersama']),
                );
            }
            return;
        }
        $seg = array_shift($segs);

        if ($seg === '*') {
            if (is_array($cur)) {
                foreach ($cur as $k => $v) {
                    $r = $resolved; $r[] = $k;
                    $this->jalan_path($cur[$k], $segs, $meta, $items, $r);
                }
            } elseif (is_object($cur)) {
                foreach (get_object_vars($cur) as $k => $dummy) {
                    $r = $resolved; $r[] = $k;
                    $this->jalan_path($cur->{$k}, $segs, $meta, $items, $r);
                }
            }
            return;
        }

        if (is_array($cur)) {
            if (array_key_exists($seg, $cur)) {
                $r = $resolved; $r[] = $seg;
                $this->jalan_path($cur[$seg], $segs, $meta, $items, $r);
            } elseif (self::is_list($cur)) {
                foreach ($cur as $k => $v) {
                    $r = $resolved; $r[] = $k;
                    $this->jalan_path($cur[$k], $segs, $meta, $items, $r);
                }
            }
        } elseif (is_object($cur) && isset($cur->{$seg})) {
            $r = $resolved; $r[] = $seg;
            $this->jalan_path($cur->{$seg}, $segs, $meta, $items, $r);
        }
    }

    /**
     * Tulis hasil terjemahan ke struktur asli berdasarkan path konkret.
     * $items: hasil kumpulkan(); $results: teks hasil per index item.
     */
    public function terapkan(&$data, $items, $results)
    {
        foreach ($items as $i => $it) {
            $cur =& $data;
            $ok = true;
            foreach ($it['path'] as $seg) {
                if (is_array($cur) && array_key_exists($seg, $cur)) {
                    $cur =& $cur[$seg];
                } elseif (is_object($cur) && isset($cur->{$seg})) {
                    $cur =& $cur->{$seg};
                } else {
                    $ok = false;
                    break;
                }
            }
            if ($ok && is_string($cur)) {
                $cur = $results[$i];
            }
            unset($cur);
        }
    }

    private static function is_list(array $a)
    {
        $i = 0;
        foreach ($a as $k => $v) {
            if ($k !== $i++) {
                return false;
            }
        }
        return true;
    }
}
