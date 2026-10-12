<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *| --------------------------------------------------------------------------
 *| Setting_bahasa Controller (modul admin fase 6)
 *| --------------------------------------------------------------------------
 *| Monitoring & operasional fitur switch bahasa:
 *|  - index     : B1 konfigurasi bahasa user (lihat)
 *|  - override  : B2 CRUD override terjemahan
 *|  - cache     : B3 cache terjemahan (lihat + aksi koreksi)
 *|  - statistik : B4 statistik cache & kuota DeepL
 *|  - status    : B5 status sistem + uji lookup
 *
 * Akses: permission 'setting_bahasa_admin_*' (aauth_perms), dibuat & diberikan
 * ke group Admin oleh installer SQL fase 6. Superadmin bypass.
 *
 * CSRF modul-level: config global csrf_protection FALSE, jadi token CSRF
 * di-generate per session dan diverifikasi manual pada semua aksi POST.
 */
class Setting_bahasa extends Admin {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_setting_bahasa_admin');
        $this->load->helper('translator_safe_db');
        $this->load->helper('translator_sources');
        $this->load->config('translator');
        $this->load->config('bahasa');
        $this->load->library('Translator');
        $this->limit_page = 20;
        $this->init_csrf();
    }

    /* ================= CSRF modul-level ================= */

    private function init_csrf()
    {
        if (!$this->session->userdata('translator_csrf')) {
            $this->session->set_userdata('translator_csrf', bin2hex(random_bytes(20)));
        }
        $this->data['translator_csrf'] = $this->session->userdata('translator_csrf');
    }

    private function cek_csrf()
    {
        $token = $this->input->post('translator_csrf');
        $sess  = $this->session->userdata('translator_csrf');
        if (!$token || !$sess || !hash_equals($sess, $token)) {
            set_message('Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.', 'danger');
            redirect('administrator/setting_bahasa');
            return false;
        }
        return true;
    }

    private function daftar_bahasa()
    {
        $list = $this->config->item('bahasa');
        $out = array();
        foreach ((array) $list as $kode => $b) {
            if ($kode === 'id') continue; // 'id' = bahasa sumber, override tidak relevan
            $out[$kode] = $b['nama'];
        }
        return $out;
    }

    private function log_aksi($aksi, $id)
    {
        // mekanisme audit: log_message info (tanpa isi teks user)
        log_message('info', 'Setting_bahasa admin: user_id=' . $this->aauth->get_user_id()
            . ' aksi=' . $aksi . ' id=' . (int) $id);
    }

    /* ===================== B1: konfigurasi bahasa user ===================== */

    public function index($offset = 0)
    {
        $this->is_allowed('setting_bahasa_admin_view');

        $filter = array(
            'role'    => $this->input->get('role', true),
            'kode'    => $this->input->get('kode', true),
            'jenjang' => $this->input->get('jenjang', true),
            'q'       => $this->input->get('q', true),
        );

        $rows = $this->model_setting_bahasa_admin->daftar_setting($filter, $this->limit_page, (int) $offset);
        $total = $this->model_setting_bahasa_admin->hitung_setting($filter);
        $ringkasan = $this->model_setting_bahasa_admin->ringkasan();

        $this->data['rows'] = ($rows === false) ? null : $rows;      // null = tabel bermasalah
        $this->data['total'] = $total;
        $this->data['ringkasan'] = $ringkasan;
        $this->data['filter'] = $filter;
        $this->data['bahasa_cfg'] = $this->config->item('bahasa');

        $config = array(
            'base_url'    => site_url('administrator/setting_bahasa/index'),
            'total_rows'  => ($total === false) ? 0 : $total,
            'per_page'    => $this->limit_page,
            'uri_segment' => 4,
        );
        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Pengaturan Bahasa');
        $this->render('backend/standart/administrator/setting_bahasa/setting_list', $this->data);
    }

    /* ===================== B2: override CRUD ===================== */

    public function override($offset = 0)
    {
        $this->is_allowed('setting_bahasa_admin_view');

        $filter = array(
            'kode'  => $this->input->get('kode', true),
            'aktif' => $this->input->get('aktif', true),
            'q'     => $this->input->get('q', true),
        );
        $rows = $this->model_setting_bahasa_admin->daftar_override($filter, $this->limit_page, (int) $offset);
        $total = $this->model_setting_bahasa_admin->hitung_override($filter);

        $this->data['rows'] = ($rows === false) ? null : $rows;
        $this->data['total'] = $total;
        $this->data['filter'] = $filter;
        $this->data['bahasa_opt'] = $this->daftar_bahasa();

        $config = array(
            'base_url'    => site_url('administrator/setting_bahasa/override/index'),
            'total_rows'  => ($total === false) ? 0 : $total,
            'per_page'    => $this->limit_page,
            'uri_segment' => 5,
        );
        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Override Terjemahan');
        $this->render('backend/standart/administrator/setting_bahasa/override_list', $this->data);
    }

    private function validasi_override_input()
    {
        $in = array(
            'teks_asal'   => trim((string) $this->input->post('teks_asal')),
            'teks_hasil'  => trim((string) $this->input->post('teks_hasil')),
            'kode'        => trim((string) $this->input->post('kode_bahasa')),
            'tipe'        => trim((string) $this->input->post('tipe')) === 'html' ? 'html' : 'teks',
            'aktif'       => $this->input->post('aktif') ? 1 : 0,
            'catatan'     => trim((string) $this->input->post('catatan')),
        );
        $bahasa_cfg = $this->config->item('bahasa');

        if ($in['teks_asal'] === '') return array('err' => 'Teks asal wajib diisi.');
        if (mb_strlen($in['teks_asal']) > 500) return array('err' => 'Teks asal maksimal 500 karakter.');
        if ($in['teks_hasil'] === '') return array('err' => 'Teks hasil wajib diisi.');
        if ($in['kode'] === '' || $in['kode'] === 'id' || !isset($bahasa_cfg[$in['kode']])) {
            return array('err' => 'Kode bahasa tidak valid (pilih bahasa target yang didukung, bukan "id").');
        }
        return array('in' => $in);
    }

    public function override_add()
    {
        $this->is_allowed('setting_bahasa_admin_add');
        $this->data['bahasa_opt'] = $this->daftar_bahasa();
        $this->data['mode'] = 'add';
        $this->data['row'] = null;
        $this->data['prefill_asal'] = '';
        $this->data['prefill_hasil'] = '';
        $this->template->title('Tambah Override');
        $this->render('backend/standart/administrator/setting_bahasa/override_form', $this->data);
    }

    public function override_save()
    {
        $this->is_allowed('setting_bahasa_admin_add', false);
        if (!$this->cek_csrf()) return;

        $v = $this->validasi_override_input();
        if (isset($v['err'])) {
            set_message($v['err'], 'danger');
            redirect('administrator/setting_bahasa/override_add');
            return;
        }
        $in = $v['in'];
        $norm = translator_normalize($in['teks_asal']);
        $hash = translator_hash($norm, $in['tipe']);

        $dup = $this->model_setting_bahasa_admin->cari_override_hash($hash, $in['kode']);
        if ($dup !== null) {
            set_message('Gagal: override untuk teks asal + bahasa tersebut sudah ada (id ' . $dup . ').', 'danger');
            redirect('administrator/setting_bahasa/override_add');
            return;
        }

        $ok = $this->model_setting_bahasa_admin->simpan_override(array(
            'hash_sumber' => $hash,
            'kode_bahasa' => $in['kode'],
            'teks_asal'   => $norm,
            'teks_hasil'  => $in['teks_hasil'],
            'aktif'       => $in['aktif'],
            'catatan'     => $in['catatan'],
        ), $this->aauth->get_user_id());
        if ($ok) {
            $this->log_aksi('override_add', $this->db->insert_id());
            set_message('Override tersimpan dan berlaku pada request berikutnya.', 'success');
        } else {
            set_message('Gagal menyimpan override (database).', 'danger');
        }
        redirect('administrator/setting_bahasa/override');
    }

    public function override_edit($id)
    {
        $this->is_allowed('setting_bahasa_admin_update');
        $row = $this->model_setting_bahasa_admin->ambil_override($id);
        if (empty($row)) {
            set_message('Override tidak ditemukan.', 'danger');
            redirect('administrator/setting_bahasa/override');
            return;
        }
        $this->data['bahasa_opt'] = $this->daftar_bahasa();
        $this->data['mode'] = 'edit';
        $this->data['row'] = $row;
        $this->data['prefill_asal'] = '';
        $this->data['prefill_hasil'] = '';
        $this->template->title('Ubah Override');
        $this->render('backend/standart/administrator/setting_bahasa/override_form', $this->data);
    }

    public function override_update($id)
    {
        $this->is_allowed('setting_bahasa_admin_update', false);
        if (!$this->cek_csrf()) return;

        $lama = $this->model_setting_bahasa_admin->ambil_override($id);
        if (empty($lama)) {
            set_message('Override tidak ditemukan.', 'danger');
            redirect('administrator/setting_bahasa/override');
            return;
        }
        $v = $this->validasi_override_input();
        if (isset($v['err'])) {
            set_message($v['err'], 'danger');
            redirect('administrator/setting_bahasa/override_edit/' . (int) $id);
            return;
        }
        $in = $v['in'];
        $norm = translator_normalize($in['teks_asal']);
        $hash = translator_hash($norm, $in['tipe']);

        $dup = $this->model_setting_bahasa_admin->cari_override_hash($hash, $in['kode']);
        if ($dup !== null && $dup !== (int) $id) {
            set_message('Gagal: override untuk teks asal + bahasa tersebut sudah ada (id ' . $dup . ').', 'danger');
            redirect('administrator/setting_bahasa/override_edit/' . (int) $id);
            return;
        }

        $ok = $this->model_setting_bahasa_admin->ubah_override($id, array(
            'kode_bahasa' => $in['kode'],
            'teks_asal'   => $norm,
            'teks_hasil'  => $in['teks_hasil'],
            'aktif'       => $in['aktif'],
            'catatan'     => $in['catatan'],
        ), $this->aauth->get_user_id());
        if ($ok) {
            $this->log_aksi('override_update', $id);
            set_message('Override diperbarui. Berlaku pada request berikutnya.', 'success');
        } else {
            set_message('Gagal memperbarui override (database).', 'danger');
        }
        redirect('administrator/setting_bahasa/override');
    }

    public function override_toggle($id)
    {
        $this->is_allowed('setting_bahasa_admin_update', false);
        if (!$this->cek_csrf()) return;

        $row = $this->model_setting_bahasa_admin->ambil_override($id);
        if (empty($row)) {
            set_message('Override tidak ditemukan.', 'danger');
            redirect('administrator/setting_bahasa/override');
            return;
        }
        $baru = $row->aktif ? 0 : 1;
        $ok = $this->model_setting_bahasa_admin->toggle_override($id, $baru);
        if ($ok) {
            $this->log_aksi('override_toggle', $id);
            set_message('Override dinonaktifkan/diaktifkan (' . ($baru ? 'aktif' : 'nonaktif') . ').', 'success');
        } else {
            set_message('Gagal mengubah status override (database).', 'danger');
        }
        redirect('administrator/setting_bahasa/override');
    }

    public function override_delete($id)
    {
        $this->is_allowed('setting_bahasa_admin_delete', false);
        if (!$this->cek_csrf()) return;

        $row = $this->model_setting_bahasa_admin->ambil_override($id);
        if (empty($row)) {
            set_message('Override tidak ditemukan.', 'danger');
            redirect('administrator/setting_bahasa/override');
            return;
        }
        $ok = $this->model_setting_bahasa_admin->hapus_override($id);
        if ($ok) {
            $this->log_aksi('override_delete', $id);
            set_message('Override dihapus permanen.', 'success');
        } else {
            set_message('Gagal menghapus override (database).', 'danger');
        }
        redirect('administrator/setting_bahasa/override');
    }

    /* ===================== B3: cache ===================== */

    public function cache($offset = 0)
    {
        $this->is_allowed('setting_bahasa_admin_view');

        $filter = array(
            'kode' => $this->input->get('kode', true),
            'q'    => $this->input->get('q', true),
            'urut' => $this->input->get('urut', true),
        );
        $rows = $this->model_setting_bahasa_admin->daftar_cache($filter, $this->limit_page, (int) $offset);
        $total = $this->model_setting_bahasa_admin->hitung_cache($filter);

        $this->data['rows'] = ($rows === false) ? null : $rows;
        $this->data['total'] = $total;
        $this->data['filter'] = $filter;
        $this->data['bahasa_opt'] = $this->daftar_bahasa();

        $config = array(
            'base_url'    => site_url('administrator/setting_bahasa/cache/index'),
            'total_rows'  => ($total === false) ? 0 : $total,
            'per_page'    => $this->limit_page,
            'uri_segment' => 5,
        );
        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Cache Terjemahan');
        $this->render('backend/standart/administrator/setting_bahasa/cache_list', $this->data);
    }

    /** Lihat lengkap satu entri cache (dipakai juga sebagai target "Koreksi"). */
    public function cache_detail($id)
    {
        $this->is_allowed('setting_bahasa_admin_view');
        $row = $this->model_setting_bahasa_admin->ambil_cache($id);
        if (empty($row)) {
            set_message('Entri cache tidak ditemukan.', 'danger');
            redirect('administrator/setting_bahasa/cache');
            return;
        }
        $this->data['row'] = $row;
        $this->data['bahasa_opt'] = $this->daftar_bahasa();
        $this->template->title('Detail Cache');
        $this->render('backend/standart/administrator/setting_bahasa/cache_detail', $this->data);
    }

    /** Aksi "Koreksi": buka form override dgn teks terisi dari baris cache. */
    public function cache_koreksi($id)
    {
        $this->is_allowed('setting_bahasa_admin_add');
        $row = $this->model_setting_bahasa_admin->ambil_cache($id);
        if (empty($row)) {
            set_message('Entri cache tidak ditemukan.', 'danger');
            redirect('administrator/setting_bahasa/cache');
            return;
        }
        $this->data['bahasa_opt'] = $this->daftar_bahasa();
        $this->data['mode'] = 'add';
        $this->data['row'] = null;
        $this->data['prefill_asal'] = $row->teks_asal;
        $this->data['prefill_hasil'] = $row->teks_hasil;
        $this->template->title('Koreksi ke Override');
        $this->render('backend/standart/administrator/setting_bahasa/override_form', $this->data);
    }

    /* ===================== B4: statistik ===================== */

    public function statistik()
    {
        $this->is_allowed('setting_bahasa_admin_view');

        $info_cache = $this->model_setting_bahasa_admin->info_tabel('translation_cache');
        $per_kode = $this->model_setting_bahasa_admin->cache_per_kode();
        $populer = $this->model_setting_bahasa_admin->cache_populer(20);
        $tak_terpakai = $this->model_setting_bahasa_admin->cache_tak_terpakai_90_hari();
        $usage = $this->model_setting_bahasa_admin->usage_6_bulan();
        $ov_aktif = $this->model_setting_bahasa_admin->hitung_override_aktif(1);
        $ov_nonaktif = $this->model_setting_bahasa_admin->hitung_override_aktif(0);
        $limit = (int) translator_cfg('translator_monthly_char_limit', 500000);

        // hasil tombol "Cek kuota di DeepL" (session, dipopulate oleh usage_check)
        $deepl_usage = $this->session->userdata('translator_deepl_usage');
        $deepl_usage_err = $this->session->userdata('translator_deepl_usage_err');

        $this->data['info_cache'] = $info_cache;
        $this->data['per_kode'] = ($per_kode === false) ? null : $per_kode;
        $this->data['populer'] = ($populer === false) ? null : $populer;
        $this->data['tak_terpakai'] = $tak_terpakai;
        $this->data['usage'] = ($usage === false) ? null : $usage;
        $this->data['limit'] = $limit;
        $this->data['ov_aktif'] = $ov_aktif;
        $this->data['ov_nonaktif'] = $ov_nonaktif;
        $this->data['deepl_usage'] = $deepl_usage;
        $this->data['deepl_usage_err'] = $deepl_usage_err;

        $this->template->title('Statistik Terjemahan');
        $this->render('backend/standart/administrator/setting_bahasa/statistik', $this->data);
    }

    /**
     * SATU-SATUNYA panggilan DeepL di modul admin: GET /v2/usage (tidak memakai
     * kuota). Hanya dijalankan saat tombol diklik; hasil disimpan di session
     * untuk ditampilkan di halaman statistik.
     */
    public function usage_check()
    {
        $this->is_allowed('setting_bahasa_admin_view', false);
        if (!$this->cek_csrf()) return;

        $key = translator_env('DEEPL_API_KEY');
        if ($key === '') {
            $this->session->set_userdata('translator_deepl_usage_err', 'DEEPL_API_KEY tidak terisi di .env.');
            redirect('administrator/setting_bahasa/statistik');
            return;
        }
        $url = (substr($key, -3) === ':fx')
            ? 'https://api-free.deepl.com/v2/usage'
            : 'https://api.deepl.com/v2/usage';

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HTTPHEADER => array('Authorization: DeepL-Auth-Key ' . $key),
        ));
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0 || $code !== 200 || $body === false) {
            $this->session->set_userdata('translator_deepl_usage_err', 'Gagal mengambil usage dari DeepL (HTTP ' . $code . ').');
        } else {
            $json = json_decode($body, true);
            $this->session->set_userdata('translator_deepl_usage', array(
                'character_count' => isset($json['character_count']) ? (int) $json['character_count'] : 0,
                'character_limit' => isset($json['character_limit']) ? (int) $json['character_limit'] : 0,
            ));
            $this->session->set_userdata('translator_deepl_usage_err', false);
        }
        redirect('administrator/setting_bahasa/statistik');
    }

    /* ===================== B5: status sistem ===================== */

    public function status()
    {
        $this->is_allowed('setting_bahasa_admin_view');

        $this->data['enabled'] = translator_cfg('translator_enabled', false);
        $this->data['endpoints'] = (array) translator_cfg('translator_enabled_endpoints', array());
        $this->data['breaker_open'] = translator_breaker_is_open();

        // sisa waktu breaker: baca CI cache
        $CI =& get_instance();
        $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        $st = $CI->cache->get(translator_cfg('translator_breaker_cache_key', 'translator_breaker_v1'));
        $this->data['breaker_sisa'] = (is_array($st) && !empty($st['open_until']))
            ? max(0, (int) ($st['open_until'] - time())) : 0;

        $this->data['key_ada'] = (translator_env('DEEPL_API_KEY') !== '');
        $this->data['bahasa_cfg'] = $this->config->item('bahasa');

        // jumlah entri kamus termuat
        $this->load->library('Translator');
        $kamus = $this->translator->kamus->load('en-GB');
        $this->data['kamus_entries'] = is_array($kamus) ? count($kamus) : 0;

        $this->template->title('Status Sistem Bahasa');
        $this->render('backend/standart/administrator/setting_bahasa/status', $this->data);
    }

    /** Uji lookup: dari sumber mana teks akan dilayani. TIDAK memanggil DeepL/Google. */
    public function uji_lookup()
    {
        $this->is_allowed('setting_bahasa_admin_view', false);
        if (!$this->cek_csrf()) return;

        $teks = trim((string) $this->input->post('uji_teks'));
        $kode = trim((string) $this->input->post('uji_kode'));
        $hasil = array('sumber' => null, 'hasil' => null, 'teks_asal' => '');

        if ($teks !== '' && $kode !== '' && $kode !== 'id') {
            $norm = translator_normalize($teks);
            $hasil['teks_asal'] = $norm;
            if ($this->translator->filter->murni_angka_tanggal($norm)) {
                $hasil['sumber'] = 'dilewati-filter';
            } else {
                $hash = translator_hash($norm, 'teks');
                $cari = $this->model_setting_bahasa_admin->cari_untuk_uji($hash, $kode);
                if (is_array($cari)) {
                    $hasil['sumber'] = $cari['sumber'];
                    $hasil['hasil'] = $cari['hasil'];
                } else {
                    $val = $this->translator->kamus->get(mb_strtolower($norm, 'UTF-8'), $kode);
                    if ($val !== null && $val !== '') {
                        $hasil['sumber'] = 'kamus';
                        $hasil['hasil'] = $val;
                    } else {
                        $hasil['sumber'] = 'deepl'; // belum ada; akan diterjemahkan DeepL saat dipakai
                    }
                }
            }
        }
        $this->session->set_userdata('translator_uji_lookup', $hasil);
        redirect('administrator/setting_bahasa/status');
    }

    /* ===================== Warm-up (isi cache dari modul admin) ===================== */

    // batas potongan (chunk) per request AJAX
    private $warmup_chunk_strings = 20;
    private $warmup_chunk_chars = 4000;
    private $warmup_chunk_seconds = 20;

    private function _cache_driver()
    {
        static $drv = null;
        if ($drv === null) {
            get_instance()->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
            $drv = get_instance()->cache;
        }
        return $drv;
    }

    /** Endpoint utk dropdown: whitelist aktif + ada path bersama=true + dipetakan di helper sumber. */
    private function _warmup_endpoints()
    {
        $out = array();
        foreach ((array) $this->config->item('translate_whitelist') as $ep => $spec) {
            $ada = false;
            foreach ((array) $spec as $meta) {
                if (is_array($meta) && !empty($meta['bersama'])) { $ada = true; break; }
            }
            if ($ada && !empty(translator_endpoint_sources($ep))) $out[$ep] = $ep;
        }
        ksort($out);
        return $out;
    }

    /** Validasi scope POST (endpoint ATAU gelombang). return array('err'=>..) atau array('mode'=>,'kunci'=>) */
    private function _warmup_scope()
    {
        $ep = trim((string) $this->input->post('endpoint'));
        $wave = trim((string) $this->input->post('wave'));
        if ($ep !== '') {
            $spec = $this->translator->whitelist->endpoint_aktif($ep);
            if ($spec === null) return array('err' => 'Endpoint tidak ada di whitelist aktif (atau seluruhnya excluded): ' . $ep);
            $ada = false;
            foreach ((array) $spec as $meta) {
                if (is_array($meta) && !empty($meta['bersama'])) { $ada = true; break; }
            }
            if (!$ada) return array('err' => 'Endpoint tidak punya path whitelist bersama=true: ' . $ep);
            if (empty(translator_endpoint_sources($ep))) return array('err' => 'Endpoint belum dipetakan di sumber data warm-up: ' . $ep);
            return array('mode' => 'endpoint', 'kunci' => $ep);
        }
        if ($wave !== '') {
            if (!in_array($wave, array('1a', '1b', '2', '3'), true)) {
                return array('err' => 'Gelombang tidak valid (pilih 1a/1b/2/3).');
            }
            return array('mode' => 'wave', 'kunci' => $wave);
        }
        return array('err' => 'Pilih endpoint atau gelombang dulu.');
    }

    /** Query sumber: endpoint tunggal atau gelombang (endpoint excluded / tanpa bersama=true DITOLAK). */
    private function _warmup_sources($mode, $kunci)
    {
        if ($mode === 'endpoint') return array($kunci => translator_endpoint_sources($kunci));
        $out = array();
        foreach (translator_wave_sources($kunci) as $ep => $q) {
            $spec = $this->translator->whitelist->endpoint_aktif($ep);
            if ($spec === null) continue; // excluded / tidak aktif -> lewati
            $ada = false;
            foreach ((array) $spec as $meta) {
                if (is_array($meta) && !empty($meta['bersama'])) { $ada = true; break; }
            }
            if (!$ada) continue;
            $out[$ep] = $q;
        }
        return $out;
    }

    private function _warmup_kumpulkan($mode, $kunci)
    {
        $semua = array();
        $err = false;
        foreach ($this->_warmup_sources($mode, $kunci) as $ep => $queries) {
            $f = translator_fetch_strings($queries);
            if ($f['err']) $err = true;
            foreach ($f['strings'] as $txt => $tipe) {
                if (!isset($semua[$txt])) $semua[$txt] = $tipe;
            }
        }
        return array('semua' => $semua, 'err' => $err);
    }

    /** String yang BELUM tercover kamus/override/cache (urut teks) — deterministik & resumable. */
    private function _warmup_sisa($semua, $lang)
    {
        $kamus = $this->translator->kamus->load($lang);
        $kamus_keys = is_array($kamus) ? array_keys($kamus) : array();
        $hashes = array();
        foreach ($semua as $txt => $tipe) $hashes[] = translator_hash($txt, $tipe);
        $ov = $this->translator->cache->ambil_override_banyak($lang, $hashes);
        $ov = is_array($ov) ? $ov : array();
        $ca = $this->translator->cache->ambil_banyak($lang, $hashes);
        $ca = is_array($ca) ? $ca : array();
        $sisa = array();
        foreach ($semua as $txt => $tipe) {
            $kena = ($tipe !== 'html' && in_array(mb_strtolower($txt, 'UTF-8'), $kamus_keys, true));
            $h = translator_hash($txt, $tipe);
            if ($kena || isset($ov[$h]) || isset($ca[$h])) continue;
            $sisa[] = array('text' => $txt, 'tipe' => $tipe, 'bersama' => true);
        }
        usort($sisa, array($this, '_warmup_urut'));
        return $sisa;
    }

    private function _warmup_urut($a, $b)
    {
        return strcmp($a['text'], $b['text']);
    }

    /** Kurangi daftar string sampai anggaran karakter (urut teks, ambil yang muat). */
    private function _warmup_potong_anggaran(array $sisa, $max_chars)
    {
        $out = array(); $c = 0;
        foreach ($sisa as $it) {
            $len = strlen($it['text']);
            if ($c + $len > $max_chars) continue;
            $out[] = $it; $c += $len;
        }
        return array('items' => $out, 'chars' => $c);
    }

    public function warmup()
    {
        $this->is_allowed('setting_bahasa_admin_warmup');

        $lock = $this->_cache_driver()->get('setting_bahasa_warmup_lock');
        $this->data['lock_ada'] = !empty($lock);
        $this->data['endpoint_opt'] = $this->_warmup_endpoints();
        $this->data['wave_opt'] = array('1a' => 'Gelombang 1a (teks)', '1b' => 'Gelombang 1b (HTML)', '2' => 'Gelombang 2 (label & master)');
        $this->data['bahasa_opt'] = $this->daftar_bahasa();
        $this->data['max_chars_default'] = 5000;
        $this->data['max_chars_cap'] = (int) translator_cfg('translator_warmup_web_max_chars', 20000);
        $this->data['limit'] = (int) translator_cfg('translator_monthly_char_limit', 500000);

        $this->template->title('Warm-up Cache Terjemahan');
        $this->render('backend/standart/administrator/setting_bahasa/warmup_list', $this->data);
    }

    private function _warmup_json($arr)
    {
        $this->output->enable_profiler(FALSE); // output JSON murni (bukan HTML)
        $this->output->set_content_type('application/json')->set_output(json_encode($arr));
    }

    /**
     * Audit warm-up: log_message('info') TIDAK terekam karena log_threshold=1
     * (hanya ERROR) — di lokal maupun produksi. Pakai level error dgn prefix
     * 'AUDIT' agar benar-benar terekam & gampang difilter. Tanpa isi teks.
     */
    private function _log_warmup($msg)
    {
        log_message('error', 'AUDIT warmup_bahasa: user_id=' . $this->aauth->get_user_id() . ' ' . $msg);
    }

    private function _warmup_cek_csrf()
    {
        $token = (string) $this->input->post('translator_csrf');
        $sess = $this->session->userdata('translator_csrf');
        if (!$token || !$sess || !hash_equals($sess, $token)) {
            $this->_warmup_json(array('ok' => false, 'err' => 'Token keamanan tidak valid. Muat ulang halaman.'));
            return false;
        }
        return true;
    }

    private function _warmup_bahasa_valid()
    {
        $lang = trim((string) $this->input->post('lang'));
        $bahasa = $this->config->item('bahasa');
        if ($lang === '' || $lang === 'id' || !isset($bahasa[$lang])) {
            $this->_warmup_json(array('ok' => false, 'err' => 'Bahasa tidak valid (pilih bahasa target, bukan "id").'));
            return null;
        }
        return $lang;
    }

    /** Tombol "Hitung" (dry-run, TANPA DeepL). POST: endpoint|wave, lang. */
    public function warmup_hitung()
    {
        if (!$this->is_allowed('setting_bahasa_admin_warmup', false)) {
            $this->_warmup_json(array('ok' => false, 'err' => 'Akses ditolak: izin setting_bahasa_admin_warmup diperlukan.'));
            return;
        }
        if (!$this->_warmup_cek_csrf()) return;
        $lang = $this->_warmup_bahasa_valid();
        if ($lang === null) return;
        $scope = $this->_warmup_scope();
        if (isset($scope['err'])) { $this->_warmup_json(array('ok' => false, 'err' => $scope['err'])); return; }

        $k = $this->_warmup_kumpulkan($scope['mode'], $scope['kunci']);
        $stat = array('teks' => array('n' => 0, 'c' => 0), 'html' => array('n' => 0, 'c' => 0));
        $total_c = 0;
        foreach ($k['semua'] as $txt => $tipe) {
            if (!isset($stat[$tipe])) $stat[$tipe] = array('n' => 0, 'c' => 0);
            $stat[$tipe]['n']++;
            $stat[$tipe]['c'] += strlen($txt);
            $total_c += strlen($txt);
        }
        $sisa = $this->_warmup_sisa($k['semua'], $lang);
        $sisa_c = 0;
        foreach ($sisa as $it) $sisa_c += strlen($it['text']);

        $this->_warmup_json(array(
            'ok' => true,
            'scope' => $scope,
            'lang' => $lang,
            'per_tipe' => $stat,
            'total_n' => count($k['semua']),
            'total_c' => $total_c,
            'belum_n' => count($sisa),
            'belum_c' => $sisa_c,
            'sumber_err' => $k['err'],
            'kosong' => empty($k['semua']),
        ));
    }

    /**
     * Eksekusi bertahap. POST: aksi=mulai|langkah|henti, endpoint|wave, lang, max_chars, token.
     * Tiap request memproses SATU potongan (maks 20 string / 4.000 char / 20 detik,
     * mana yang lebih dulu) lewat pipeline Translator yang sama (normalisasi, hash,
     * tipe, tag_handling), lalu mengembalikan progres; frontend mengulang sampai
     * selesai atau Berhenti ditekan. String yang sudah di cache dilewati (resumable).
     */
    public function warmup_jalankan()
    {
        if (!$this->is_allowed('setting_bahasa_admin_warmup', false)) {
            $this->_warmup_json(array('ok' => false, 'err' => 'Akses ditolak: izin setting_bahasa_admin_warmup diperlukan.'));
            return;
        }
        if (!$this->_warmup_cek_csrf()) return;
        $lang = $this->_warmup_bahasa_valid();
        if ($lang === null) return;
        $aksi = (string) $this->input->post('aksi');
        $drv = $this->_cache_driver();

        if ($aksi === 'henti') {
            $drv->delete('setting_bahasa_warmup_state');
            $drv->delete('setting_bahasa_warmup_lock');
            $this->_log_warmup('henti');
            $this->_warmup_json(array('ok' => true, 'status' => 'dihentikan'));
            return;
        }

        if ($aksi === 'mulai') {
            $scope = $this->_warmup_scope();
            if (isset($scope['err'])) { $this->_warmup_json(array('ok' => false, 'err' => $scope['err'])); return; }

            if (!empty($drv->get('setting_bahasa_warmup_lock'))) {
                $this->_warmup_json(array('ok' => false, 'err' => 'Masih ada sesi warm-up berjalan (lock aktif). Tunggu selesai atau tekan Berhenti.'));
                return;
            }
            if (translator_env('DEEPL_API_KEY') === '') {
                $this->_warmup_json(array('ok' => false, 'err' => 'DEEPL_API_KEY kosong di .env — warm-up tidak bisa dijalankan.'));
                return;
            }
            $cap = (int) translator_cfg('translator_warmup_web_max_chars', 20000);
            $max_chars = (int) $this->input->post('max_chars');
            if ($max_chars <= 0) $max_chars = 5000;
            if ($max_chars > $cap) $max_chars = $cap;

            $k = $this->_warmup_kumpulkan($scope['mode'], $scope['kunci']);
            if (empty($k['semua'])) {
                $this->_warmup_json(array('ok' => false, 'err' => 'Tidak ada string ditemukan utk scope ini (tabel sumber kosong / tidak terbaca).'));
                return;
            }
            $sisa = $this->_warmup_sisa($k['semua'], $lang);
            $pot = $this->_warmup_potong_anggaran($sisa, $max_chars);
            if (empty($pot['items'])) {
                $this->_warmup_json(array('ok' => true, 'status' => 'selesai', 'done' => true, 'msg' => 'Semua string sudah tercover kamus/override/cache atau melebihi anggaran. Tidak ada yang diterjemahkan.', 'url_cache' => site_url('administrator/setting_bahasa/cache')));
                return;
            }

            $token = bin2hex(random_bytes(8));
            $state = array(
                'token' => $token,
                'mode' => $scope['mode'],
                'kunci' => $scope['kunci'],
                'lang' => $lang,
                'max_chars' => $max_chars,
                'sisa' => $pot['items'],
                'total_n' => count($pot['items']),
                'total_c' => $pot['chars'],
                'processed_n' => 0,
                'processed_c' => 0,
                'deepl_chars' => 0,
                'deepl_billed' => 0,
                'gagal' => 0,
                'mulai' => date('Y-m-d H:i:s'),
            );
            $drv->save('setting_bahasa_warmup_state', $state, 600);
            $drv->save('setting_bahasa_warmup_lock', array('token' => $token, 'user' => $this->aauth->get_user_id(), 'mulai' => time()), 120);
            $this->_log_warmup('mulai ' . $scope['mode'] . '=' . $scope['kunci'] . ' lang=' . $lang . ' strings=' . count($pot['items']) . ' chars=' . $pot['chars']);

            $this->_warmup_json(array(
                'ok' => true, 'status' => 'jalan', 'token' => $token, 'done' => false,
                'sisa_n' => count($pot['items']), 'total_n' => count($pot['items']),
                'processed_n' => 0, 'total_c' => $pot['chars'], 'processed_c' => 0, 'deepl_chars' => 0,
            ));
            return;
        }

        // ---- aksi = langkah (satu potongan) ----
        $state = $drv->get('setting_bahasa_warmup_state');
        if (!is_array($state) || (empty($state['sisa']) && empty($state['total_n']))) {
            $this->_warmup_json(array('ok' => false, 'err' => 'Tidak ada sesi warm-up aktif (kedaluwarsa). Klik Jalankan lagi — aman karena yang sudah di cache dilewati.'));
            return;
        }
        $token = (string) $this->input->post('token');
        if ($token === '' || !hash_equals((string) $state['token'], $token)) {
            $this->_warmup_json(array('ok' => false, 'err' => 'Sesi warm-up dipegang proses lain. Muat ulang halaman.'));
            return;
        }
        if (empty($state['sisa'])) {
            $drv->delete('setting_bahasa_warmup_lock');
            $this->_warmup_json($this->_warmup_ringkasan($state, true));
            return;
        }

        // hormati circuit breaker
        if (translator_breaker_is_open()) {
            $drv->delete('setting_bahasa_warmup_lock');
            $this->_warmup_json(array('ok' => true, 'status' => 'jeda', 'alasan' => 'Circuit breaker terbuka (DeepL gagal berulang). Tunggu sebentar lalu klik Jalankan lagi.', 'done' => false));
            return;
        }
        // DeepL key
        if (translator_env('DEEPL_API_KEY') === '') {
            $drv->delete('setting_bahasa_warmup_lock');
            $this->_warmup_json(array('ok' => true, 'status' => 'berhenti', 'alasan' => 'DEEPL_API_KEY kosong.', 'done' => false));
            return;
        }
        // kuota: berhenti bila billed bulan berjalan akan melewati 90% limit
        $limit = (int) translator_cfg('translator_monthly_char_limit', 500000);
        $u = translator_safe_query(
            "SELECT jumlah_karakter FROM translation_usage_bulanan WHERE bulan = DATE_FORMAT(NOW(), '%Y-%m') AND penyedia = 'deepl' LIMIT 1",
            array(), 'row', null);
        $pakai = ($u && isset($u->jumlah_karakter)) ? (int) $u->jumlah_karakter : 0;
        $est_chunk = 0; $n_chunk = 0;
        foreach ($state['sisa'] as $it) {
            if ($n_chunk >= $this->warmup_chunk_strings || $est_chunk + strlen($it['text']) > $this->warmup_chunk_chars) break;
            $est_chunk += strlen($it['text']); $n_chunk++;
        }
        if ($limit > 0 && ($pakai + $est_chunk) > floor($limit * 0.9)) {
            $drv->delete('setting_bahasa_warmup_lock');
            $this->_warmup_json(array('ok' => true, 'status' => 'berhenti', 'alasan' => 'Kuota DeepL bulan ini akan melewati 90% limit (terpakai ' . $pakai . '/' . $limit . '). Sisa string bisa dilanjutkan nanti.', 'done' => false));
            return;
        }

        // ambil satu potongan
        $chunk = array(); $c_chunk = 0;
        $t0 = microtime(true);
        while (!empty($state['sisa'])) {
            if (count($chunk) >= $this->warmup_chunk_strings) break;
            $it = $state['sisa'][0];
            if ($c_chunk + strlen($it['text']) > $this->warmup_chunk_chars) break;
            if ((microtime(true) - $t0) > $this->warmup_chunk_seconds) break;
            $chunk[] = $it;
            array_shift($state['sisa']);
            $c_chunk += strlen($it['text']);
        }

        // pipeline Translator yang SAMA (timeout = anggaran potongan);
        // warm-up independen kill-switch translator_enabled
        $this->config->set_item('translator_enabled', TRUE);
        $this->config->set_item('translator_deepl_total_timeout', $this->warmup_chunk_seconds);
        $this->config->set_item('translator_time_budget', (float) $this->warmup_chunk_seconds);
        $this->translator->translate_strings($chunk, $state['lang']);
        $st = $this->translator->get_last_stats();

        $state['processed_n'] += count($chunk);
        $state['processed_c'] += $c_chunk;
        $state['deepl_chars'] += (int) $st['deepl_chars'];
        $state['deepl_billed'] += (int) $st['deepl_billed'];
        $state['gagal'] += (int) $st['asli_gagal'];

        $done = empty($state['sisa']);
        if ($done) {
            $drv->save('setting_bahasa_warmup_state', $state, 600);
            $drv->delete('setting_bahasa_warmup_lock');
            $this->_log_warmup('selesai ' . $state['mode'] . '=' . $state['kunci'] . ' lang=' . $state['lang'] . ' strings=' . $state['processed_n'] . ' chars=' . $state['processed_c'] . ' deepl_billed=' . $state['deepl_billed']);
        } else {
            $drv->save('setting_bahasa_warmup_state', $state, 600);
            $drv->save('setting_bahasa_warmup_lock', array('token' => $state['token'], 'user' => $this->aauth->get_user_id(), 'mulai' => time()), 120);
        }
        $this->_warmup_json($this->_warmup_ringkasan($state, $done));
    }

    private function _warmup_ringkasan($state, $done)
    {
        $out = array(
            'ok' => true,
            'status' => $done ? 'selesai' : 'jalan',
            'done' => $done,
            'sisa_n' => count($state['sisa']),
            'total_n' => (int) $state['total_n'],
            'processed_n' => (int) $state['processed_n'],
            'total_c' => (int) $state['total_c'],
            'processed_c' => (int) $state['processed_c'],
            'deepl_chars' => (int) $state['deepl_chars'],
            'deepl_billed' => (int) $state['deepl_billed'],
            'gagal' => (int) $state['gagal'],
        );
        if ($done) {
            $out['msg'] = 'Warm-up selesai: ' . (int) $state['processed_n'] . ' string / ' . (int) $state['processed_c'] . ' karakter diproses; DeepL billed ' . (int) $state['deepl_billed'] . ' karakter; gagal ' . (int) $state['gagal'] . '.';
            $out['url_cache'] = site_url('administrator/setting_bahasa/cache');
        }
        return $out;
    }
}
