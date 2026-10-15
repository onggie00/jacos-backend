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

    /**
     * Daftar lamaran — filter lowongan/status/pencarian nama/email,
     * paging di SQL (server-side). join kandidat + lowongan.
     */
    public function daftar($filter = array(), $limit = 20, $offset = 0)
    {
        $this->_where_filter($filter);
        $this->db->select('a.id_application, a.status, a.submitted_at, a.id_job_posting,
                           a.id_candidate, c.full_name, c.email,
                           p.title AS posting_title, p.closing_date')
            ->from($this->table_name . ' a')
            ->join('career_candidates c', 'c.id_candidate = a.id_candidate', 'inner')
            ->join('career_job_postings p', 'p.id_job_posting = a.id_job_posting', 'inner')
            ->order_by('a.id_application DESC')
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
        if (isset($filter['q']) && $filter['q'] !== '') {
            $q = $this->scurity($filter['q']);
            $this->db->group_start();
            $this->db->like('c.full_name', $q);
            $this->db->or_like('c.email', $q);
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
                    s.decided_at, s.score, s.note_internal,
                    st.stage_order, st.name, st.mode,
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
    public function audit_lamaran($id_application, $limit = 20, $offset = 0)
    {
        return $this->db->query(
            "SELECT * FROM career_audit_logs
             WHERE (object_type = 'career_application' AND object_id = ?)
                OR (object_type = 'career_application_document' AND object_id IN
                    (SELECT id_application_document FROM career_application_documents
                     WHERE id_application = ?))
                OR (object_type = 'job_posting_requirement' AND object_id IN
                    (SELECT id_job_posting_requirement FROM career_application_requirements
                     WHERE id_application = ?))
             ORDER BY created_at DESC
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

    /** Notifikasi ($id_candidate) — 20 terbaru. */
    public function notifikasi($id_candidate)
    {
        return $this->db->where('id_candidate', (int) $id_candidate)
            ->order_by('created_at', 'DESC')
            ->limit(50)
            ->get('career_notifications')->result();
    }
}
