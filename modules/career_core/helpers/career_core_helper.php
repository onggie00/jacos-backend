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
