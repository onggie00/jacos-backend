<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * career_core_helper — helper BERSAMA modul admin karier (Fase 6A).
 * Dipakai career_lowongan / career_template / career_pelamar / career_audit.
 * Atasan: CSRF buatan modul (csrf_protection CI3 tidak diubah), audit log,
 * stream dokumen privat, whitelist format file.
 */

// --------------------------------------------------------------------
// CSRF per-sesi buatan modul karier
// --------------------------------------------------------------------

if ( ! function_exists('career_csrf_token')) {
    /**
     * Token CSRF per-sesi untuk semua form/AJAX mutasi modul karier.
     * (csrf_protection CI3 global dibiarkan FALSE — jangan diubah.)
     */
    function career_csrf_token()
    {
        $ci =& get_instance();
        $ci->load->library('session');
        $tok = $ci->session->userdata('career_csrf');
        if (empty($tok) || !is_string($tok) || strlen($tok) < 32) {
            try {
                $tok = bin2hex(random_bytes(16));
            } catch (Exception $e) {
                $tok = hash('sha256', uniqid(mt_rand(), true));
            }
            $ci->session->set_userdata('career_csrf', $tok);
        }
        return $tok;
    }
}

if ( ! function_exists('career_csrf_field')) {
    /** Hidden input untuk form POST modul karier. */
    function career_csrf_field()
    {
        return '<input type="hidden" name="career_csrf" value="'
            . html_escape(career_csrf_token()) . '" class="career-csrf">';
    }
}

if ( ! function_exists('career_csrf_ok')) {
    /**
     * Validasi token CSRF POST modul karier. Gagal → TRUE dipulangkan
     * jika caller ingin menolak (403). Gunakan:
     *   if ( ! career_csrf_ok()) { return $this->_deny403(); }
     */
    function career_csrf_ok()
    {
        $ci =& get_instance();
        $posted = (string) $ci->input->post('career_csrf');
        $stored = (string) $ci->session->userdata('career_csrf');
        return $posted !== '' && $stored !== '' && hash_equals($stored, $posted);
    }
}

// --------------------------------------------------------------------
// Audit log bersama
// --------------------------------------------------------------------

if ( ! function_exists('career_audit')) {
    /**
     * Tulis satu baris career_audit_logs (actor admin).
     * $detail: array ringkas before/after field yang berubah — TIDAK untuk
     * isi dokumen/pii besar (aturan 6A: jangan log PII di file log).
     */
    function career_audit($action, $object_type, $object_id = NULL, $detail = array())
    {
        $ci =& get_instance();
        $ci->load->database();
        $actor_id = NULL;
        if ($ci->session->userdata('id')) {
            $actor_id = (int) $ci->session->userdata('id');
        }
        $ci->db->insert('career_audit_logs', array(
            'actor_type'  => 'admin',
            'actor_id'    => $actor_id,
            'action'      => (string) $action,
            'object_type' => (string) $object_type,
            'object_id'   => $object_id === NULL ? NULL : (int) $object_id,
            'detail_json' => json_encode(
                $detail,
                JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE
            ) ?: '{}',
            'ip_address'  => $ci->input->ip_address(),
        ));
    }
}

// --------------------------------------------------------------------
// Input helper kecil
// --------------------------------------------------------------------

if ( ! function_exists('career_q')) {
    /**
     * Normalisasi input GET/POST untuk filter: selalu string skalar.
     * Array (mis. ?q[]=x) dipangkas: elemen pertama skalar, kalau tak ada
     * → ''. (IP #21 — cegah warning + double-escape dari scurity().)
     */
    function career_q($v)
    {
        if (is_array($v)) {
            foreach ($v as $first) {
                if ( ! is_array($first)) { return (string) $first; }
            }
            return '';
        }
        return (string) $v;
    }
}

// --------------------------------------------------------------------
// Whitelist & normalisasi format file
// --------------------------------------------------------------------

if ( ! function_exists('career_file_allowed_list')) {
    /**
     * whitelist: ekstensi diperbolehkan untuk tipe berkas (lowongan).
     */
    function career_file_allowed_list()
    {
        return array('pdf','jpg','jpeg','png','doc','docx');
    }
}

if ( ! function_exists('career_file_formats_valid')) {
    /**
     * Validasi string allowed_formats dari form: ekstensi dari whitelist,
     * huruf kecil, dipisah koma. Balik array(persi) atau FALSE bila invalid.
     */
    function career_file_formats_valid($raw)
    {
        $raw = strtolower(trim((string) $raw));
        if ($raw === '') { return array(); }
        $parts = explode(',', $raw);
        $out = array();
        foreach ($parts as $p) {
            $p = trim($p, ". \t");
            if ($p === '') { continue; }
            if ( ! in_array($p, career_file_allowed_list(), TRUE)) { return FALSE; }
            if ( ! in_array($p, $out, TRUE)) { $out[] = $p; }
        }
        return $out;
    }
}

// --------------------------------------------------------------------
// Streaming dokumen privat (admin)
// --------------------------------------------------------------------

if ( ! function_exists('career_stream_admin_document')) {
    /**
     * Kirim berkas dari storage privat career dengan header() LANGSUNG
     * (pelajaran 5C.1: $this->output->set_header() + exit tidak terkirim).
     * Path di DB HARUS di dalam career_upload_dir — tolak lainnya.
     * $doc: row career_application_documents.
     * return TRUE bila terkirim; FALSE bila batal/invalid (caller pun
     * menampilkan not-found).
     */
    function career_stream_admin_document($doc)
    {
        $ci =& get_instance();
        $rel = trim((string) (isset($doc->file_path) ? $doc->file_path : ''), '/\\');
        if ($rel === '' || strpos($rel, '..') !== FALSE) { return FALSE; }

        $base_dir = rtrim($ci->config->item('career_upload_dir'), '/\\');
        $full = realpath(FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $rel));
        $allowed_root = realpath(FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $base_dir));
        if ($full === FALSE || $allowed_root === FALSE
            || strpos($full, $allowed_root . DIRECTORY_SEPARATOR) !== 0
            || ! is_file($full)) {
            return FALSE;
        }

        $mime = trim((string) $doc->mime_type, "\r\n\0");
        $mime_whitelist = array(
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );
        if ( ! in_array($mime, $mime_whitelist, TRUE)) { $mime = 'application/octet-stream'; }
        $ascii = preg_replace('/[^A-Za-z0-9._\-]/', '_', $doc->original_name);
        if ($ascii === '' || $ascii === NULL) { $ascii = 'dokumen'; }

        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: ' . $mime);
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="' . $ascii
            . '"; filename*=UTF-8\'\'' . rawurlencode($doc->original_name));
        header('Content-Length: ' . filesize($full));
        header('Cache-Control: no-store');
        readfile($full);
        return TRUE;  // caller menulis audit lalu exit (agar audit hanya
                      // untuk unduhan yang benar-benar terkirim).
    }
}

// --------------------------------------------------------------------
// Notifikasi in-app bersama (D18 — tanpa email, D2)
// --------------------------------------------------------------------

if ( ! function_exists('career_notify')) {
    /**
     * Sisipkan satu notifikasi in-app utk kandidat (career_notifications).
     * PANGGIL DALAM TRANSAKSI AKSI — commit/rollback bersama.
     * Teks harus netral: tanpa skor/catatan internal/angka keputusan.
     * $title <=200, $body <=1000(null boleh).
     */
    function career_notify($id_candidate, $type, $title, $body = NULL)
    {
        $ci =& get_instance();
        return $ci->db->insert('career_notifications', array(
            'id_candidate' => (int) $id_candidate,
            'type'         => substr((string) $type, 0, 50),
            'title'        => substr((string) $title, 0, 200),
            'body'         => $body === NULL ? NULL : substr((string) $body, 0, 1000),
            'is_read'      => 0,
        ));
    }
}

// --------------------------------------------------------------------
// Mesin aturan tahap D16 (share antara controller & smoke)
// --------------------------------------------------------------------

if ( ! function_exists('career_stages_rekap')) {
    /**
     * Rekap 5 tahap lamaran (diurut stage_order) utk mesin D16.
     * PAKAI DI LUAR TRANSAKSI utk tampilan, atau DI DALAM transaksi
     * SETELAH FOR UPDATE utk keputusan.
     * Balik array stdClass: id_application_stage, id_stage (id_stage=stage
     * order 1..5), status, decided_at.
     */
    function career_stages_rekap($id_application)
    {
        $ci =& get_instance();
        return $ci->db->select('s.id_application_stage, s.id_stage, s.status, s.decided_at')
            ->where('s.id_application', (int) $id_application)
            ->order_by('s.id_stage', 'ASC')
            ->get('career_application_stages s')->result();
    }
}

if ( ! function_exists('career_stage_keputusan_valid')) {
    /**
     * Aturan D16 untuk KEPUTUSAN tahap $keputusan ('passed'|'failed') pada
     * baris tahap ke-N (1..5) dari rekap $tahapan.
     * Balik array(TRUE, '') utk lolos — atau array(FALSE, pesan_400/409).
     */
    function career_stage_keputusan_valid($tahapan, $n, $keputusan)
    {
        if ((int) $n < 1 || (int) $n > 5) { return array(FALSE, '404|Tahap tidak ditemukan.'); }
        if ( ! in_array($keputusan, array('passed', 'failed'), TRUE)) {
            return array(FALSE, '422|Keputusan harus passed/failed.');
        }
        $cur = isset($tahapan[$n - 1]) ? $tahapan[$n - 1] : NULL;
        if ($cur === NULL) { return array(FALSE, '404|Tahap tidak ditemukan.'); }
        if (in_array($cur->status, array('passed', 'failed'), TRUE)) {
            return array(FALSE, '409|Tahap ini sudah diputuskan. Gunakan koreksi.');
        }
        for ($i = 1; $i < $n; $i++) {
            if ( ! isset($tahapan[$i - 1]) || $tahapan[$i - 1]->status !== 'passed') {
                return array(FALSE, '409|Tahap ' . $i . ' belum lulus — keputusan harus berurutan.');
            }
        }
        return array(TRUE, '');
    }
}

if ( ! function_exists('career_lamaran_status_autokoreksi')) {
    /**
     * Simpan row career_applications SETELAH perubahan tahap, supaya
     * current_stage_id + status konsisten (D16.3/D16.4/koreksi D16.5).
     * $tahapan = rekap TERKINI (setelah penulisan tahap, tapi dalam trans
     * sama). Status akhir dipertahankan bila sudah accepted/not_accepted/
     * withdrawn — koreksi akhir ditangani aksi khusus.
     * return TRUE selalu (pemanggil menangani trans_status).
     */
    function career_lamaran_status_autokoreksi($id_application, $tahapan, $status_sekarang)
    {
        $ci =& get_instance();
        $curr_stage = NULL;
        $no_ada_failed = FALSE;
        foreach ($tahapan as $t) {
            if ($t->status === 'failed') { $no_ada_failed = TRUE; break; }
        }
        foreach ($tahapan as $t) {
            if ($t->status !== 'passed') { $curr_stage = (int) $t->id_stage; break; }
        }

        $up = array('current_stage_id' => $curr_stage);
        if ($no_ada_failed) {
            // Ada tahap failed → lamaran harus GUGUR (bisa dari submitted,
            // in_process, maupun hasil koreksi yang membuka status akhir).
            $up['status'] = 'not_accepted';
        } elseif ($status_sekarang === 'submitted') {
            // Aksi pertama pada lamaran submitted → in_process (D16.3).
            $up['status'] = 'in_process';
        }
        $ci->db->where('id_application', (int) $id_application)
            ->update('career_applications', $up);
        return TRUE;
    }
}
