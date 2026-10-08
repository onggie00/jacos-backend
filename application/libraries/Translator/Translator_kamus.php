<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Loader kamus statis: application/language/<folder>/kamus_lang.php
 * konvensi CI3: $lang['<kunci>'] = '<english>'; kunci = teks Indonesia
 * ternormalisasi + lowercase. Dipakai translate_message() dan lookup label.
 */
class Translator_kamus {

    private static $memo = array();

    /**
     * @param string $kode_bahasa
     * @return array|false mapping kunci (lowercase ternormalisasi) => teks; false jika folder tidak ada
     */
    public function load($kode_bahasa)
    {
        if (array_key_exists($kode_bahasa, self::$memo)) {
            return self::$memo[$kode_bahasa];
        }

        $CI =& get_instance();
        $CI->load->config('translator');
        $map = $CI->config->item('translator_language_folder');
        if (!is_array($map) || !isset($map[$kode_bahasa])) {
            self::$memo[$kode_bahasa] = false;
            return false;
        }
        $folder = $map[$kode_bahasa];
        $file = APPPATH . 'language/' . $folder . '/kamus_lang.php';
        if (!is_file($file)) {
            self::$memo[$kode_bahasa] = false;
            return false;
        }
        $lang = array();
        include $file; // mendefinisikan $lang
        self::$memo[$kode_bahasa] = is_array($lang) ? $lang : false;
        return self::$memo[$kode_bahasa];
    }

    /** Lookup satu kunci (teks Indonesia ternormalisasi, lowercase). */
    public function get($kunci, $kode_bahasa)
    {
        $kamus = $this->load($kode_bahasa);
        if ($kamus === false) {
            return null;
        }
        return isset($kamus[$kunci]) ? $kamus[$kunci] : null;
    }
}
