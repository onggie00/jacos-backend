<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Klien DeepL REST (cURL murni, tanpa SDK).
 * - Auth via header "Authorization: DeepL-Auth-Key <KEY>".
 * - Batch: banyak parameter text dalam satu request; chunk <= max_texts dan
 *   <= max_body_bytes; dedup sebelum kirim (oleh caller).
 * - Retry 1x hanya utk 429/5xx/timeout bila anggaran waktu masih ada.
 * - 456 (kuota) / 403 (key) -> breaker panjang, tanpa retry.
 */
class Translator_deepl {

    public $last_http_code = 0;


    

    public function endpoint_url()
    {
        $key = translator_env('DEEPL_API_KEY');
        $free = (substr($key, -3) === ':fx');
        return $free ? translator_cfg('translator_deepl_url_free', 'https://api-free.deepl.com/v2/translate')
                     : translator_cfg('translator_deepl_url_pro', 'https://api.deepl.com/v2/translate');
    }

    /**
     * Kirim batch teks.
     *
     * @param array  $texts      teks (sudah dedup & ternormalisasi)
     * @param string $target_lang kode target (mis. EN-GB)
     * @param bool   $html       pakai tag_handling=html
     * @param float  $budget     sisa anggaran detik utk seluruh pemanggilan
     * @return array|false list teks hasil (urut sama), false saat gagal
     */
    public function translate_batch(array $texts, $target_lang, $html, &$budget)
    {
        $key = translator_env('DEEPL_API_KEY');
        if ($key === '' || empty($texts)) {
            return false;
        }
        $max_texts = (int) translator_cfg('translator_batch_max_texts', 50);
        $max_bytes = (int) translator_cfg('translator_batch_max_body_bytes', 28000);
        $chunks = array();
        $cur = array();
        $cur_bytes = 0;
        foreach ($texts as $t) {
            $b = strlen($t) + 12; // + overhead "text[]="
            if (count($cur) >= $max_texts || ($cur_bytes + $b) > $max_bytes) {
                if (!empty($cur)) {
                    $chunks[] = $cur;
                }
                $cur = array();
                $cur_bytes = 0;
            }
            $cur[] = $t;
            $cur_bytes += $b;
        }
        if (!empty($cur)) {
            $chunks[] = $cur;
        }

        $out = array();
        foreach ($chunks as $chunk) {
            $t0 = microtime(true);
            if ((microtime(true) - $GLOBALS['_translator_t0']) > $budget) {
                return false; // anggaran habis; caller pakai asli utk sisa
            }
            $res = $this->request($chunk, $target_lang, $html, $budget);
            if ($res === false) {
                return false;
            }
            $out = array_merge($out, $res);
        }
        return $out;
    }

    private function request(array $texts, $target_lang, $html, &$budget)
    {
        $url = $this->endpoint_url();
        $key = translator_env('DEEPL_API_KEY');
        $connect = (int) translator_cfg('translator_deepl_connect_timeout', 2);
        $total = (int) translator_cfg('translator_deepl_total_timeout', 3);
        $retries_left = (int) translator_cfg('translator_max_retries', 1);
        $retry_on = translator_cfg('translator_retry_on', array());

        $post = array(
            'source_lang' => translator_cfg('translator_deepl_source_lang', 'ID'),
            'target_lang' => $target_lang,
            'preserve_formatting' => '1',
        );
        if ($html) {
            $post['tag_handling'] = translator_cfg('translator_deepl_tag_handling_html', 'html');
        }
        foreach ($texts as $t) {
            $post['text'][] = $t;
        }

        while (true) {
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($post),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => $connect,
                CURLOPT_TIMEOUT => $total,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: DeepL-Auth-Key ' . $key,
                    'Content-Type: application/x-www-form-urlencoded',
                ),
            ));
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $this->last_http_code = $code;

            if ($errno === 0 && $code >= 200 && $code < 300 && $body !== false) {
                $json = json_decode($body, true);
                if (isset($json['translations']) && is_array($json['translations'])) {
                    $out = array();
                    foreach ($json['translations'] as $tr) {
                        $out[] = isset($tr['text']) ? $tr['text'] : '';
                    }
                    return $out;
                }
                log_message('error', 'DeepL: respons tak terduga (hash batch hanya log jumlah)');
                return false;
            }

            // kegagalan
            if ($code === 456 || $code === 403) {
                // kuota habis / key salah — breaker panjang, tanpa retry
                translator_breaker_open(translator_cfg('translator_breaker_open_seconds_quota', 3600));
                log_message('error', 'DeepL: HTTP ' . $code . ' — breaker panjang (key/kuota). Tidak menyertakan key di log.');
                return false;
            }

            if (in_array($code, $retry_on, true) || $errno !== 0) {
                translator_breaker_register_fail($code);
                if ($retries_left > 0 && (microtime(true) - $GLOBALS['_translator_t0']) < $budget) {
                    $retries_left--;
                    continue; // satu retry
                }
                log_message('error', 'DeepL: gagal errno=' . $errno . ' http=' . $code . ' — fallback (tanpa isi teks)');
                return false;
            }

            log_message('error', 'DeepL: HTTP ' . $code . ' — fallback (tanpa isi teks)');
            return false;
        }
    }
}
