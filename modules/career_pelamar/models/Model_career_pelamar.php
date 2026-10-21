<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_career_pelamar — lamaran lintas lowongan untuk panel admin (Fase 6A).
 * BACA SAJA (kecuali audit unduh): tidak ada mutasi lamaran/dokumen di sini.
 * Tanpa password_hash/token_version (secret tidak pernah disentuh).
 */
class Model_career_pelamar extends MY_Model {

    private $primary_key = 'id_application';
    private $table_name  = 'career_applications';

    public function __construct()
    {
        $config = array(
            'primary_key' => $this->primary_key,
            'table_name'  => $this->table_name,
        );
        parent::__construct($config);
    }

    /** Status-filter mapping ke ENUM kerangkarbase. */
    private function _status_map($status)
    {
        $map = array(
            'draft'        => 'draft',
            'submitted'    => 'submitted',
            'in_process'   => 'in_process',
            'accepted'     => 'accepted',
            'not_accepted' => 'not_accepted',
            'withdrawn'    => 'withdrawn',
        );
        return isset($map[$status]) ? $map[$status] : NULL;
    }

    /** Peta kolom sort (whitelist) → ekspresi SQL. */
    private $_order_map = array(
        'id'       => 'a.id_application',
        'kandidat' => 'c.full_name',
        'lowongan' => 'p.title',
        'status'   => 'a.status',
        'kirim'    => 'a.submitted_at',
    );

    /**
     * Daftar lamaran — filter lowongan/status/pencarian nama/email,
     * paging di SQL (server-side). join kandidat + lowongan.
     */
    public function daftar($filter = array(), $limit = 20, $offset = 0, $sort_col = NULL, $sort_dir = 'desc')
    {
        $this->_where_filter($filter);
        $this->db->select('a.id_application, a.status, a.submitted_at, a.id_job_posting,
                           a.id_candidate, c.full_name, c.email,
                           p.title AS posting_title, p.closing_date')
            ->from($this->table_name . ' a')
            ->join('career_candidates c', 'c.id_candidate = a.id_candidate', 'inner')
            ->join('career_job_postings p', 'p.id_job_posting = a.id_job_posting', 'inner');
        // Sort whitelist; fallback: terbaru dulu (id DESC).
        if ($sort_col !== NULL && isset($this->_order_map[$sort_col])) {
            $this->db->order_by($this->_order_map[$sort_col], $sort_dir === 'asc' ? 'ASC' : 'DESC');
        }
        $this->db->order_by('a.id_application', 'DESC')
            ->limit((int) $limit, (int) $offset);
        return $this->db->get()->result();
    }

    public function daftar_count($filter = array())
    {
        $this->_where_filter($filter);
        $this->db->select('COUNT(*) n')
            ->from($this->table_name . ' a')
            ->join('career_candidates c', 'c.id_candidate = a.id_candidate', 'inner')
            ->join('career_job_postings p', 'p.id_job_posting = a.id_job_posting', 'inner');
        return (int) $this->db->get()->row()->n;
    }

    private function _where_filter($filter)
    {
        if (isset($filter['id_job_posting']) && (int) $filter['id_job_posting'] > 0) {
            $this->db->where('a.id_job_posting', (int) $filter['id_job_posting']);
        }
        if (isset($filter['status']) && $filter['status']) {
            $st = $this->_status_map($filter['status']);
            if ($st !== NULL) { $this->db->where('a.status', $st); }
        }
        if (isset($filter['stage_id']) && (int) $filter['stage_id'] > 0) {
            $this->db->where('a.current_stage_id', (int) $filter['stage_id']);
        }
        if ( ! empty($filter['perlu_verifikasi'])) {
            // 6B: lamaran submitted/in_process dengan >=1 requirement
            // pending_review (EXISTS — tetap 1 query anti-N+1).
            $this->db->where('a.status IN ("submitted","in_process")', NULL, FALSE);
            $this->db->where(
                'EXISTS (SELECT 1 FROM career_application_requirements r'
                . ' WHERE r.id_application = a.id_application'
                . ' AND r.status = "pending_review")', NULL, FALSE);
        }
        if (isset($filter['q']) && $filter['q'] !== '') {
            // LOW #21: tanpa scurity() (double-escape); like() sudah
            // #17 6B.2: like() CI me-escape nilai + wildcard (ESCAPE '!'),
            // tanpa escape_like_str ganda.
            $q = career_q($filter['q']);
            $this->db->group_start();
            $this->db->like('c.full_name', $q, 'both');
            $this->db->or_like('c.email', $q, 'both');
            $this->db->group_end();
        }
    }

    /** Detail satu lamaran — bila kandidat TIDAK sama dgn milik lamaran 404 (IDOR serupa端 admin). */
    public function find_lamaran($id_application)
    {
        return $this->db->select('a.*, c.full_name, c.email,
                                  p.title AS posting_title, p.closing_date')
            ->from($this->table_name . ' a')
            ->join('career_candidates c', 'c.id_candidate = a.id_candidate', 'inner')
            ->join('career_job_postings p', 'p.id_job_posting = a.id_job_posting', 'inner')
            ->where('a.id_application', (int) $id_application)
            ->get()->row();
    }

    /** Toko profil snapshot (BACA — jangan tampilkan kolom validasi psik/skor di sini). */
    public function snapshot($id_application)
    {
        return $this->db->where('id_application', (int) $id_application)
            ->get('career_application_profile_snapshots')->row();
    }

    /** Persyaratan lamaran + nama persyr dpdit dan dokumen versi terbaru.
     *  Subquery dlm JOIN tidak didukung AR — pakai raw query dgn binding. */
    public function persyaratan($id_application)
    {
        return $this->db->query(
            "SELECT ar.id_application_requirement, ar.status AS requirement_status,
                    ar.answer_text, ar.review_note,
                    jr.requirement_name, jr.requirement_type, jr.input_type,
                    jr.allowed_formats, jr.max_file_size_kb, jr.display_order,
                    d.id_application_document, d.version, d.original_name,
                    d.mime_type, d.file_size, d.uploaded_at
             FROM career_application_requirements ar
             JOIN career_job_posting_requirements jr
                   ON jr.id_job_posting_requirement = ar.id_job_posting_requirement
             LEFT JOIN career_application_documents d
                   ON d.id_application_requirement = ar.id_application_requirement
                  AND d.version = (SELECT MAX(x.version) FROM career_application_documents x
                                   WHERE x.id_application_requirement = ar.id_application_requirement)
             WHERE ar.id_application = ?
             ORDER BY jr.display_order ASC, jr.id_job_posting_requirement ASC",
            array((int) $id_application)
        )->result();
    }

    /** 5 tahap lamaran (status-mode) + nama tahap. */
    public function tahapan($id_application)
    {
        return $this->db->query(
            "SELECT s.id_application_stage, s.id_stage, s.status, s.scheduled_at,
                    s.decided_at, st.stage_order, st.name, st.mode,
                    sch.schedule_datetime, sch.channel, sch.note, sch.is_published
             FROM career_application_stages s
             JOIN career_stages st ON st.id_stage = s.id_stage
             LEFT JOIN career_selection_schedules sch
                   ON sch.id_application_stage = s.id_application_stage
                  AND sch.id_selection_schedule = (SELECT MAX(x.id_selection_schedule)
                                                   FROM career_selection_schedules x
                                                   WHERE x.id_application_stage = s.id_application_stage)
             WHERE s.id_application = ?
             ORDER BY st.stage_order ASC",
            array((int) $id_application)
        )->result();
    }

    /** Timeline audit yang menyentuh lamaran (object app + doc/req turunannya). */
    public function audit_lamaran($id_application, $limit = 20, $offset = 0, $lihat_skor = FALSE)
    {
        // M2 6B.3: baris audit nilai ber-skor hanya detail lengkap bagi
        // pemegang career_pelamar_score_view; user lain melihat baris
        // TANPA detail (kolom non-sensitif lain tetap tampil).
        $sel = $lihat_skor ? 'detail_json' :
            "IF(action = 'simpan_nilai_tahap', NULL, detail_json) AS detail_json";
        return $this->db->query(
            "SELECT id_audit_log, actor_type, actor_id, action, object_type, object_id,
                    {$sel}, ip_address, created_at, updated_at
             FROM career_audit_logs
             WHERE (object_type = 'career_application' AND object_id = ?)
                OR (object_type = 'career_application_document' AND object_id IN
                    (SELECT id_application_document FROM career_application_documents
                     WHERE id_application = ?))
                OR (object_type = 'job_posting_requirement' AND object_id IN
                    (SELECT id_job_posting_requirement FROM career_application_requirements
                     WHERE id_application = ?))
             ORDER BY created_at DESC, id_audit_log DESC
             LIMIT ? OFFSET ?",
            array((int) $id_application, (int) $id_application, (int) $id_application,
                  (int) $limit, (int) $offset)
        )->result();
    }

    /** Dokumen terbaru dari urutan requirement (utk unduh admin). */
    public function dokumen_unduh($id_application_document, $id_application)
    {
        return $this->db
            ->where('id_application_document', (int) $id_application_document)
            ->where('id_application', (int) $id_application)
            ->get('career_application_documents')->row();
    }

    /** Notifikasi ($id_candidate) — 50 terbaru. */
    public function notifikasi($id_candidate)
    {
        return $this->db->where('id_candidate', (int) $id_candidate)
            ->order_by('created_at', 'DESC')
            ->limit(50)
            ->get('career_notifications')->result();
    }

// ====================================================================
// Fase 6B — mutasi terkontrol (semua method di bawah ini hanya boleh
// dipanggil controller dengan izin eksplisit + POST + CSRF + transaksi).
// ====================================================================

/** Kunci baris lamaran dalam transaksi (anti-race dua admin). */
public function kunci_lamaran($id_application)
{
    return $this->db->query(
        "SELECT a.*, c.full_name, c.email
         FROM career_applications a
         JOIN career_candidates c ON c.id_candidate = a.id_candidate
         WHERE a.id_application = ? FOR UPDATE",
        array((int) $id_application)
    )->row();
}

/** Baris requirement utk verifikasi (pra-kontrol). */
public function verifikasi_req($id_application_requirement)
{
    return $this->db->query(
        "SELECT ar.id_application_requirement, ar.status AS requirement_status,
                ar.answer_text, ar.id_application,
                jr.requirement_name, jr.requirement_type, jr.input_type
         FROM career_application_requirements ar
         JOIN career_job_posting_requirements jr
               ON jr.id_job_posting_requirement = ar.id_job_posting_requirement
         WHERE ar.id_application_requirement = ?",
        array((int) $id_application_requirement)
    )->row();
}

/**
 * Verifikasi satu persyaratan (D17) — PANGGIL DALAM TRANSAKSI,
 * lamaran sudah terkunci FOR UPDATE & statusnya sudah dicek controller.
 * $app = stdClass lamaran (hasil kunci_lamaran).
 */
public function set_verifikasi($id_application_requirement, $baris_req, $app, $keputusan_baru, $pesan_kandidat, $review_note)
{
    if ( ! in_array($keputusan_baru, array('verified', 'revision_requested', 'rejected'), TRUE)) {
        return array(FALSE, '422|Keputusan verifikasi tidak sah.');
    }
    if ($baris_req->requirement_status === $keputusan_baru) {
        // #18 6B.2: status sama = konflik -> 409.
        return array(FALSE, '409|Persyaratan sudah berstatus ' . $keputusan_baru . '.');
    }
    if ($baris_req->requirement_status === 'not_uploaded') {
        return array(FALSE, '422|Persyaratan belum diunggah/diisi kandidat — tidak dapat diverifikasi.');
    }
    if ($keputusan_baru === 'verified') {
        // #12 6B.1: jenis BERKAS = selain jawaban ketik; API menganggap
        // 'document' dan 'file' sama (Lamaran.php:350-352).
        if ( ! in_array($baris_req->input_type, array('text', 'number', 'select'), TRUE)) {
            // NOTE: CI3 count_all() mengabaikan WHERE seluruhnya (hanya menghitung
            // total tabel) — gunakan count_all_results() agar WHERE dipakai.
            $n = (int) $this->db->where('id_application_requirement', (int) $id_application_requirement)
                ->count_all_results('career_application_documents');
            if ($n < 1) {
                return array(FALSE, '422|Verified tidak boleh: persyaratan belum punya dokumen.');
            }
        } else {
            if (trim((string) $baris_req->answer_text) === '') {
                return array(FALSE, '422|Verified tidak boleh: persyaratan belum ada jawaban.');
            }
        }
    }
    if (in_array($keputusan_baru, array('revision_requested', 'rejected'), TRUE)
        && trim((string) $pesan_kandidat) === '') {
        return array(FALSE, '422|Pesan untuk kandidat wajib diisi (maksimal 500 karakter).');
    }

    $ok = $this->db->where('id_application_requirement', (int) $id_application_requirement)
        ->update('career_application_requirements', array(
            'status'      => $keputusan_baru,
            'reviewed_by' => (int) get_instance()->session->userdata('id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
            // review_note = catatan INTERNAL — tidak pernah dikirim kandidat.
            'review_note' => $review_note === NULL
                ? NULL : career_potong(career_teks_aman($review_note), 2000),
        ));
    if ( ! $ok) { return array(FALSE, '500|Gagal menyimpan keputusan verifikasi.'); }

    // D17/D18: revision_requested/rejected → notifikasi in-app dengan pesan
    // admin (SATU-SATUNYA jalur alasan ke kandidat), dalam transaksi sama.
    if (in_array($keputusan_baru, array('revision_requested', 'rejected'), TRUE)) {
        $judul = ($keputusan_baru === 'revision_requested' ? 'Perlu revisi: ' : 'Berkas ditolak: ')
            . $baris_req->requirement_name;
        $tipe  = $keputusan_baru === 'revision_requested' ? 'requirement_revision' : 'requirement_rejected';
        career_notify($app->id_candidate, $tipe, $judul,
            career_potong(career_teks_aman($pesan_kandidat), 500));
    }
    return array(TRUE, $keputusan_baru === 'verified' ? 'Dokumen terverifikasi.' : 'Status persyaratan diperbarui.');
}

/**
 * Keputusan tahap (D16) — PANGGIL dalam transaksi + lamaran SUDAH
 * terkunci FOR UPDATE. $n = stage order 1..5, $keputusan = passed/failed.
 */
public function putus_tahap($id_application, $n, $keputusan)
{
    $tahapan = career_stages_rekap($id_application);
    if (count($tahapan) !== 5) {
        return array(FALSE, '409|Perangkat tahap belum lengkap (5 tahap wajib).');
    }
    list($ok, $pesan) = career_stage_keputusan_valid($tahapan, $n, $keputusan);
    if ( ! $ok) { return array(FALSE, $pesan); }

    $baris = $tahapan[$n - 1];
    $ok = $this->db
        ->where('id_application_stage', (int) $baris->id_application_stage)
        ->update('career_application_stages', array(
            'status'     => $keputusan,
            'decided_at' => date('Y-m-d H:i:s'),
        ));
    if ( ! $ok) { return array(FALSE, '500|Gagal menyimpan keputusan tahap.'); }

    // Rekap terkini + autokoreksi current_stage_id/status lamaran (D16.3-4).
    $tahap_baru = career_stages_rekap($id_application);
    $app = $this->db->where('id_application', (int) $id_application)->get('career_applications')->row();
    career_lamaran_status_autokoreksi($id_application, $tahap_baru, $app->status);

    // D18: notifikasi keputusan tahap — teks netral, tanpa skor.
    if ($keputusan === 'failed') {
        career_notify($app->id_candidate, 'stage_failed',
            'Hasil tahap seleksi',
            'Anda tidak melanjutkan ke tahap berikutnya. Terima kasih atas partisipasi Anda.');
    } elseif ((int) $n < 5) {
        career_notify($app->id_candidate, 'stage_passed',
            'Tahap seleksi lulus',
            'Anda melanjutkan ke tahap berikutnya. Pantau halaman riwayat untuk informasi jadwal.');
    }
    return array(TRUE, 'Keputusan tahap ' . (int) $n . ' tersimpan.');
}

/**
 * Koreksi keputusan (D16.5) — wajib alasan; reset tahap selebihnya
 * (lebih belakangan) yang sudah diputuskan ke pending; recompute
 * current_stage_id & status lamaran secara konsisten.
 * PANGGIL dalam transaksi + lamaran SUDAH terkunci FOR UPDATE.
 */
public function koreksi_tahap($id_application, $n, $keputusan_baru, $alasan)
{
    if (career_panjang((string) $alasan) === 0) {
        return array(FALSE, '422|Alasan koreksi wajib diisi.');
    }
    if ( ! in_array($keputusan_baru, array('passed', 'failed', 'pending'), TRUE)) {
        return array(FALSE, '422|Koreksi tahap: passed/failed/pending.');
    }
    $app = $this->db->where('id_application', (int) $id_application)->get('career_applications')->row();
    // D16.5: hanya lamaran yang SUDAH berjalan/selesai yang boleh
    // dikoreksi; draft (belum ada keputusan) & withdrawn (dicabut
    // kandidat) ditolak.
    if ( ! in_array($app->status,
            array('submitted', 'in_process', 'accepted', 'not_accepted'), TRUE)) {
        return array(FALSE, '409|Koreksi hanya untuk lamaran submitted/in_process/accepted/not_accepted.');
    }
    $tahapan = career_stages_rekap($id_application);
    if (count($tahapan) !== 5 || ! isset($tahapan[$n - 1])) {
        return array(FALSE, '404|Tahap tidak ditemukan.');
    }
    $cur = $tahapan[$n - 1];
    if ( ! in_array($cur->status, array('passed', 'failed'), TRUE)) {
        return array(FALSE, '409|Tahap belum pernah diputuskan — gunakan keputusan, bukan koreksi.');
    }
    if ($cur->status === $keputusan_baru) {
        return array(FALSE, '409|Koreksi tidak mengubah apa pun (keputusan sama).');
    }
    if ($keputusan_baru === 'passed') {
        // D16.2 tetap berlaku: koreksi ke "passed" hanya bila seluruh
        // tahap sebelumnya passed.
        for ($i = 1; $i < (int) $n; $i++) {
            if ($tahapan[$i - 1]->status !== 'passed') {
                return array(FALSE, '409|Tahap ' . $i . ' belum lulus — koreksi ke "passed" melanggar urutan (D16.2).');
            }
        }
    }

    // Butir #5: koreksi un-publish jadwal tahap N + semua tahap
    // berikutnya; tahap berikutnya yang sudah diputuskan kembali
    // pending (jadwal/scheduled yatim tidak boleh tertinggal).
    for ($i = (int) $n; $i <= 5; $i++) {
        $t = $tahapan[$i - 1];
        $this->db->where('id_application_stage', (int) $t->id_application_stage)
            ->where('is_published', 1)
            ->update('career_selection_schedules', array('is_published' => 0));
        $this->db->where('id_application_stage', (int) $t->id_application_stage)
            ->update('career_application_stages', array('scheduled_at' => NULL));
        if ($i > (int) $n) {
            $this->db->where('id_application_stage', (int) $t->id_application_stage)
                ->update('career_application_stages', array(
                    'status' => 'pending', 'decided_at' => NULL,
                ));
        }
    }

    $up = $keputusan_baru === 'pending'
        ? array('status' => 'pending', 'decided_at' => NULL)
        : array('status' => $keputusan_baru, 'decided_at' => date('Y-m-d H:i:s'));
    $this->db->where('id_application_stage', (int) $cur->id_application_stage)
        ->update('career_application_stages', $up);

    // Status akhir lama (accepted/not_accepted) tidak lagi konsisten setelah
    // koreksi: kembalikan ke in_process lalu biarkan autokoreksi memutuskan.
    $app = $this->db->where('id_application', (int) $id_application)->get('career_applications')->row();
    if (in_array($app->status, array('accepted', 'not_accepted'), TRUE)) {
        $this->db->where('id_application', (int) $id_application)
            ->update('career_applications', array('status' => 'in_process'));
    }
    career_lamaran_status_autokoreksi($id_application, career_stages_rekap($id_application), $app->status);

    // D18: kandidat diberi tahu bahwa keputusan telah direvisi — teks
    // netral, tanpa skor/catatan internal.
    career_notify($app->id_candidate, 'keputusan_dikoreksi',
        'Keputusan tahap direvisi',
        'Keputusan tahap seleksi lamaran Anda diperbarui oleh administrator. Cek riwayat lamaran untuk status terbaru.');
    return array(TRUE, 'Koreksi keputusan disimpan.');
}

/** Status akhir "Diterima" — eksplisit, hanya bila tahap 1..5 semua passed (D16.4). */
public function terima_akhir($id_application)
{
    $tahapan = career_stages_rekap($id_application);
    if (count($tahapan) !== 5) {
        return array(FALSE, '409|Perangkat tahap belum lengkap (5 tahap wajib).');
    }
    foreach ($tahapan as $t) {
        if ($t->status !== 'passed') {
            return array(FALSE, '409|Diterima hanya bila tahap 1-5 semua lulus.');
        }
    }
    $app = $this->db->where('id_application', (int) $id_application)->get('career_applications')->row();
    if ( ! in_array($app->status, array('in_process', 'submitted'), TRUE)) {
        return array(FALSE, '409|Lamaran tidak sedang dalam proses.');
    }

    $this->db->where('id_application', (int) $id_application)
        ->update('career_applications', array('status' => 'accepted', 'current_stage_id' => NULL));
    career_notify($app->id_candidate, 'application_accepted',
        'Lamaran diterima',
        'Selamat, lamaran Anda dinyatakan diterima. Info lanjut akan disampaikan melalui kanal yang berlaku.');
    return array(TRUE, 'Status akhir diperbarui: diterima.');
}

// --------------------------------------------------------------------
// Jadwal per tahap (D16 + D18)
// --------------------------------------------------------------------

/** Baris tahap + lamaran utk jadwal. */
public function app_status_dari_req($id_application_requirement)
{
    return $this->db->query(
        "SELECT a.id_application, a.status AS app_status, a.id_candidate, a.current_stage_id, a.submitted_at
         FROM career_application_requirements ar
         JOIN career_applications a ON a.id_application = ar.id_application
         WHERE ar.id_application_requirement = ?",
        array((int) $id_application_requirement)
    )->row();
}

public function stage_untuk_jadwal($id_application_stage)
{
    return $this->db->query(
        "SELECT s.id_application_stage, s.id_stage, s.status, s.id_application,
                a.id_candidate, a.status AS app_status,
                st.stage_order, st.name
         FROM career_application_stages s
         JOIN career_applications a ON a.id_application = s.id_application
         JOIN career_stages st ON st.id_stage = s.id_stage
         WHERE s.id_application_stage = ?",
        array((int) $id_application_stage)
    )->row();
}

/** Baris jadwal milik satu tahap. */
public function jadwal_tahap($id_application_stage)
{
    return $this->db->where('id_application_stage', (int) $id_application_stage)
        ->get('career_selection_schedules')->row();
}

/**
 * Tambah/ubah jadwal SATU baris career_selection_schedules per tahap.
 * $data sudah divalidasi controller (tanggal/channel/note). Jadwal yang
 * SUDAH dipublikasi tidak diubah diam-diam — harus batal publikasi dulu
 * (batasan: baris terkunci is_published=0 pada UPDATE).
 * PANGGIL dalam transaksi + lamaran terkunci.
 */
public function jadwal_simpan($id_application_stage, $data)
{
    $data['id_application_stage'] = (int) $id_application_stage;
    $row = $this->jadwal_tahap($id_application_stage);
    if ($row === NULL) {
        if ( ! $this->db->insert('career_selection_schedules', $data)) {
            return array(FALSE, '500|Gagal menyimpan jadwal.');
        }
        return array(TRUE, (int) $this->db->insert_id());
    }
    if ((int) $row->is_published === 1) {
        // tolak perubahan jadwal terpublikasi — batalkan publikasi dulu
        return array(FALSE, '409|Jadwal sudah dipublikasikan — batalkan publikasi dulu.');
    }
    $ok = $this->db->where('id_selection_schedule', (int) $row->id_selection_schedule)
        ->update('career_selection_schedules', $data);
    return $ok
        ? array(TRUE, (int) $row->id_selection_schedule)
        : array(FALSE, '500|Gagal menyimpan jadwal.');
}

/** Hapus jadwal (hanya yang belum dipublikasi). */
public function jadwal_hapus($id_application_stage)
{
    $row = $this->jadwal_tahap($id_application_stage);
    if ($row === NULL) { return array(FALSE, '404|Jadwal tidak ditemukan.'); }
    if ((int) $row->is_published === 1) {
        return array(FALSE, '409|Jadwal sudah dipublikasikan — batalkan publikasi dulu.');
    }
    // HIGH-2: tahap yang sudah diputuskan tidak boleh dihapus jadwalnya.
    $app_st = $this->stage_untuk_jadwal($id_application_stage);
    if ($app_st !== NULL && in_array($app_st->status, array('passed', 'failed'), TRUE)) {
        return array(FALSE, '409|Tahap sudah diputuskan — jadwal tidak dapat dihapus.');
    }
    $this->db->where('id_selection_schedule', (int) $row->id_selection_schedule)->delete('career_selection_schedules');
    return array(TRUE, 'Jadwal dihapus.');
}

/**
 * Publikasi/batal publikasi jadwal + status tahap ikut 'scheduled'
 * (aturan 6B: status tahap mengikuti jadwal terpublikasi; batal
 * publikasi → kembali pending). D18: publikasi → notifikasi.
 * PANGGIL dalam transaksi + lamaran terkunci.
 */
public function jadwal_publikasi($id_application_stage, $publikasi)
{
    $row = $this->jadwal_tahap($id_application_stage);
    if ($row === NULL) { return array(FALSE, '404|Jadwal tidak ditemukan.'); }
    $app_st = $this->stage_untuk_jadwal($id_application_stage);

    if ((int) $publikasi === 1) {
        if ((int) $row->is_published === 1) {
            // Publikasi ulang atas jadwal yang TIDAK pernah dicabut →
            // tolak, TIDAK mengirim notifikasi kedua (#8c).
            return array(FALSE, '409|Jadwal sudah dipublikasikan.');
        }
        if (in_array($app_st->status, array('passed', 'failed'), TRUE)) {
            return array(FALSE, '409|Tahap sudah diputuskan — jadwal tidak dapat dipublikasikan.');
        }
        $this->db->where('id_selection_schedule', (int) $row->id_selection_schedule)
            ->update('career_selection_schedules', array('is_published' => 1));
        // Aksi jadwal pertama pada lamaran submitted → in_process (#8a).
        career_lamaran_status_autokoreksi((int) $app_st->id_application,
            career_stages_rekap((int) $app_st->id_application), (string) $app_st->app_status);
        // Ringkas jadwal di baris tahap utk API kandidat (#8b):
        // scheduled_at diisi dari jadwal terpublikasi.
        $this->db->set('status', 'scheduled')
            ->set('scheduled_at', (string) $row->schedule_datetime)
            ->where('id_application_stage', (int) $id_application_stage)
            ->where('status', 'pending')
            ->update('career_application_stages');
        // D18: notifikasi jadwal terpublikasi (tanpa skor/catatan internal;
        // detail tanggal tampil kandidat lewat stepper di FE).
        career_notify($app_st->id_candidate, 'stage_scheduled',
            'Jadwal tahap ' . $app_st->stage_order . ' dipublikasikan',
            'Jadwal untuk tahap "' . $app_st->name . '" telah terbit. Lihat detail riwayat lamaran Anda.');
        return array(TRUE, 'Jadwal dipublikasikan.');
    }

    // Batal publikasi: hanya menyentuh tahap berstatus 'scheduled'
    // (HIGH-2 6B.2 + H1 6B.3: SEMUA guard SEBELUM tulisan sehingga 409
    // TIDAK meninggalkan baris yang berubah).
    if (in_array($app_st->status, array('passed', 'failed'), TRUE)) {
        return array(FALSE, '409|Tahap sudah diputuskan — publikasi jadwal tidak dapat dibatalkan.');
    }
    if (in_array($app_st->app_status, array('accepted', 'not_accepted'), TRUE)) {
        return array(FALSE, '409|Lamaran sudah diputuskan akhirnya — batal publikasi tidak dapat dilakukan.');
    }
    if ((int) $row->is_published !== 1) {
        // 409 TANPA menulis apa pun (H1 6B.3).
        return array(FALSE, '409|Jadwal memang belum dipublikasikan.');
    }
    $this->db->where('id_selection_schedule', (int) $row->id_selection_schedule)
        ->update('career_selection_schedules', array('is_published' => 0));
    $this->db->set('status', 'pending')
        ->set('scheduled_at', NULL)
        ->where('id_application_stage', (int) $id_application_stage)
        ->where('status', 'scheduled')
        ->update('career_application_stages');
    // D18: notifikasi singkat netral saat publikasi dicabut (#8c).
    career_notify($app_st->id_candidate, 'stage_schedule_cancelled',
        'Jadwal tahap ' . $app_st->stage_order . ' diperbarui',
        'Jadwal tahap "' . $app_st->name . '" untuk sementara ditarik. Informasi jadwal terbaru menyusul di riwayat lamaran.');
    return array(TRUE, 'Publikasi jadwal dibatalkan.');
}

// --------------------------------------------------------------------
// Nilai & catatan internal (D13) — SATU sumber kebenaran:
// tabel career_evaluation_scores (keputusan tercatat DATABASE_DESIGN §3:
// score = agregasi dari career_evaluation_scores; kolom stages.score/
// note_internal dibiarkan NULL — tidak dipakai, tidak duplikasi).
// --------------------------------------------------------------------

/** Semua catatan nilai satu lamaran (hanya utk halaman ber-izin skor). */
public function nilai_lamaran($id_application)
{
    return $this->db->query(
        "SELECT e.id_evaluation_score, e.id_application_stage, e.evaluator_id,
                e.score, e.rubric_note, e.updated_at, st.stage_order, st.name AS stage_name
         FROM career_evaluation_scores e
         JOIN career_application_stages s ON s.id_application_stage = e.id_application_stage
         JOIN career_stages st ON st.id_stage = s.id_stage
         WHERE s.id_application = ?
         ORDER BY st.stage_order ASC, e.id_evaluation_score ASC",
        array((int) $id_application)
    )->result();
}

/** Baris nilai utk satu tahap (satu baris per tahap). */
public function nilai_tahap($id_application_stage)
{
    return $this->db->where('id_application_stage', (int) $id_application_stage)
        ->order_by('id_evaluation_score', 'DESC')
        ->get('career_evaluation_scores')->row();
}

/**
 * Simpan (insert/update) nilai satu tahap — D13.
 * score = 0..100 boleh desimal (kolom decimal(6,2)); rubric_note text.
 * PANGGIL dalam transaksi. return array(ok, pesan).
 */
public function nilai_simpan($id_application_stage, $score, $rubric_note)
{
    if ($score === '' || $score === NULL) {
        return array(FALSE, '422|Nilai (0-100) wajib diisi.');
    }
    // Ketat: hanya 0..100 bulat/desimal (desimal maks 2 tempat) —
    // menolak "1e1", " 5", dsb (#16).
    if ( ! preg_match('/^(?:100(?:\\.00?)?|\\d{1,2}(?:\\.\\d{1,2})?)$/', (string) $score)) {
        return array(FALSE, '422|Nilai harus angka 0-100 (digit, desimal maks 2 tempat).');
    }
    if (career_panjang((string) $rubric_note) > 2000) {
        return array(FALSE, '422|Catatan maksimal 2000 karakter.');
    }
    $row = $this->nilai_tahap($id_application_stage);
    $data = array(
        // #16: aturan TUNGGAL penilai — baris lama dipertahankan:
        // penilai pertama tidak digantikan saat penilai lain memperbarui.
        'score'        => round((float) $score, 2),
        'rubric_note'  => $rubric_note === NULL
            ? NULL : career_potong(career_teks_aman($rubric_note), 2000),
    );
    if ($row === NULL) {
        $data['id_application_stage'] = (int) $id_application_stage;
        $data['evaluator_id'] = (int) get_instance()->session->userdata('id');
        $ok = $this->db->insert('career_evaluation_scores', $data);
    } else {
        $ok = $this->db->where('id_evaluation_score', (int) $row->id_evaluation_score)
            ->update('career_evaluation_scores', $data);
    }
    return $ok ? array(TRUE, 'Nilai lapangan tersimpan.') : array(FALSE, '500|Gagal menyimpan nilai.');
}

// --------------------------------------------------------------------
// Ringkasan 6B (agregat, tanpa N+1)
// --------------------------------------------------------------------

/** Hitungan lamaran per status (1 query). */
public function ringkasan_status()
{
    $rows = $this->db->select('status, COUNT(*) n', FALSE)
        ->group_by('status')->get('career_applications')->result();
    $out = array_fill_keys(
        array('draft','submitted','in_process','accepted','not_accepted','withdrawn'), 0);
    foreach ($rows as $r) { if (isset($out[$r->status])) { $out[$r->status] = (int) $r->n; } }
    return $out;
}
}
