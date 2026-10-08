<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Cloud Translation v2 REST (fallback opsional).
 * Key dari .env GOOGLE_TRANSLATE_API_KEY; tanpa key → selalu false.
 * HASIL FALLBACK TIDAK disimpan ke cache (translation_cache tidak punya
 * kolom penanda sumber; config translator_google_store_fallback=false).
 */
class Translator_google {

    

    /**
     * @param array  $texts teks ternormalisasi
     * @param string $target_lang kode bahasa (config translator_google_target)
     * @param float  $budget sisa anggaran detik
     * @return array|false hasil urut sama
     */
    public function translate_batch(array $texts, $target_lang, &$budget)
    {
        $key = translator_env('GOOGLE_TRANSLATE_API_KEY');
        if ($key === '' || empty($texts)) {
            return false;
        }
        $targets = translator_cfg('translator_google_target', array());
        if (!isset($targets[$target_lang])) {
            return false;
        }
        $url = translator_cfg('translator_google_url', 'https://translation.googleapis.com/language/translate/v2');
        $timeout = (int) translator_cfg('translator_google_timeout', 3);

        $post = array(
            'q'      => $texts,
            'source' => translator_cfg('translator_google_source', 'id'),
            'target' => $targets[$target_lang],
            'format' => 'text',
            'key'    => $key,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => $timeout,
        ));
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0 || $code < 200 || $code >= 300 || $body === false) {
            log_message('error', 'Google: gagal errno=' . $errno . ' http=' . $code . ' (tanpa isi teks)');
            return false;
        }
        $json = json_decode($body, true);
        if (!isset($json['data']['translations']) || !is_array($json['data']['translations'])) {
            return false;
        }
        $out = array();
        foreach ($json['data']['translations'] as $tr) {
            $out[] = isset($tr['translatedText']) ? html_entity_decode($tr['translatedText'], ENT_QUOTES, 'UTF-8') : '';
        }
        return $out;
    }
}
