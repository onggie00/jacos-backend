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
}
