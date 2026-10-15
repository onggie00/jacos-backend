<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_career_lowongan — lowongan kerja + baris persyaratannya (Fase 6A).
 * Tabel career_job_postings / career_job_posting_requirements.
 * D12: baris persyaratan yang dipakai lamaran tidak boleh dihapus,
 * input_type/requirement_type tidak boleh diubah setelah lowongan
 * berlamar. Semua dipastikan di sini + di controller.
 */
class Model_career_lowongan extends MY_Model {

    private $primary_key  = 'id_job_posting';
    private $table_name   = 'career_job_postings';
    private $field_search = ['title'];

    public function __construct()
    {
        $config = array(
            'primary_key'  => $this->primary_key,
            'table_name'   => $this->table_name,
            'field_search' => $this->field_search,
        );
        parent::__construct($config);
    }

    // ---------------- daftar & filter ----------------

    /** Status tampil: draft | buka | tutup — hitung dari tanggal+terbit. */
    public function status_tampil($row)
    {
        $today = date('Y-m-d');
        if ((int) $row->is_published === 0) {
            return 'draft';
        }
        $open   = $row->opening_date  !== NULL ? $row->opening_date  : '0000-00-00';
        $close  = $row->closing_date  !== NULL ? $row->closing_date  : '9999-12-31';
        if ($today < $open)  { return 'draft'; }   // terbit tapi belum mulai
        if ($today > $close) { return 'tutup'; }
        return 'buka';
    }

    public function lowongan_list($q = NULL, $status = NULL, $limit = NULL, $offset = 0)
    {
        $this->db->from($this->table_name);
        if ($q !== NULL && $q !== '') {
            $this->db->like('title', $this->scurity($q));
        }
        $this->db->order_by('id_job_posting', 'DESC');
        $rows = ($limit !== NULL)
            ? $this->db->limit((int) $limit, (int) $offset)->get()->result()
            : $this->db->get()->result();
        if ($status === NULL || $status === '') {
            return $rows;
        }
        return array_values(array_filter($rows, function ($r) use ($status) {
            return $this->status_tampil($r) === $status;
        }));
    }

    public function lowongan_count($q = NULL, $status = NULL)
    {
        return count($this->lowongan_list($q, $status, NULL, 0));
    }

    public function find_lowongan($id)
    {
        $this->db->where($this->primary_key, (int) $id);
        return $this->db->get($this->table_name)->row();
    }

    /** TRUE bila lowongan sudah punya lamaran (draft maupun submit). */
    public function punya_lamaran($id)
    {
        $n = $this->db->where('id_job_posting', (int) $id)
            ->count_all_results('career_applications');
        return $n > 0;
    }

    // ---------------- mutasi lowongan ----------------

    public function store_lowongan($data)
    {
        $row = array(
            'title'           => $data['title'],
            'description'     => $data['description'],
            'opening_date'    => $data['opening_date'],
            'closing_date'    => $data['closing_date'],
            'education_level' => $data['education_level'],
            'is_published'    => (int) $data['is_published'],
            'created_by'      => $data['created_by'],
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        );
        $this->db->insert($this->table_name, $row);
        return (int) $this->db->insert_id();
    }

    public function change_lowongan($id, $data)
    {
        $row = array(
            'title'           => $data['title'],
            'description'     => $data['description'],
            'opening_date'    => $data['opening_date'],
            'closing_date'    => $data['closing_date'],
            'education_level' => $data['education_level'],
            'is_published'    => (int) $data['is_published'],
            'updated_at'      => date('Y-m-d H:i:s'),
        );
        $this->db->where($this->primary_key, (int) $id);
        return $this->db->update($this->table_name, $row);
    }

    /** Hapus hanya bila belum punya lamaran (FK RESTRICT juga menolak). */
    public function delete_lowongan($id)
    {
        $id = (int) $id;
        if ($id <= 0 || $this->punya_lamaran($id)) {
            return FALSE;
        }
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table_name);  // baris persyr TNK sediri by FK CASCADE
    }

    // ---------------- persyaratan lowongan ----------------

    public function requirements($id_job_posting)
    {
        return $this->db->where('id_job_posting', (int) $id_job_posting)
            ->order_by('display_order', 'ASC')
            ->order_by('id_job_posting_requirement', 'ASC')
            ->get('career_job_posting_requirements')
            ->result();
    }

    public function find_requirement($id_requirement)
    {
        return $this->db->where('id_job_posting_requirement', (int) $id_requirement)
            ->get('career_job_posting_requirements')->row();
    }

    /** TRUE bila baris persyaratan sudah dipakai lamaran (jawaban/dok). */
    public function requirement_dipakai($id_requirement)
    {
        $n = $this->db->where('id_job_posting_requirement', (int) $id_requirement)
            ->count_all_results('career_application_requirements');
        return $n > 0;
    }

    /** Nyatakan max display_order+1 utk lowongan. */
    public function next_order($id_job_posting)
    {
        $this->db->select_max('display_order');
        $this->db->where('id_job_posting', (int) $id_job_posting);
        $max = $this->db->get('career_job_posting_requirements')->row();
        return ((int) ($max && isset($max->display_order) ? $max->display_order : 0)) + 1;
    }

    public function add_requirement($id_job_posting, $data)
    {
        $row = array(
            'id_job_posting'          => (int) $id_job_posting,
            'id_requirement_template' => $data['id_requirement_template'],
            'requirement_name'        => $data['requirement_name'],
            'requirement_type'        => $data['requirement_type'],
            'input_type'              => $data['input_type'],
            'allowed_formats'         => $data['allowed_formats'],
            'max_file_size_kb'        => $data['max_file_size_kb'],
            'display_order'           => (int) $data['display_order'],
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        );
        $this->db->insert('career_job_posting_requirements', $row);
        return (int) $this->db->insert_id();
    }

    /**
     * Update persyaratan — field SECRENNya hanya yang boleh diubah:
     * requirement_name, allowed_formats, max_file_size_kb, display_order.
     * input_type/requirement_type HARUS sama dgn record eksisting (D12).
     */
    public function change_requirement($id_requirement, $data, $locked_input_type, $locked_requirement_type)
    {
        $row = array(
            'requirement_name' => $data['requirement_name'],
            'input_type'       => $locked_input_type,
            'requirement_type' => $locked_requirement_type,
            'allowed_formats'  => $data['allowed_formats'],
            'max_file_size_kb' => $data['max_file_size_kb'],
            'display_order'    => (int) $data['display_order'],
            'updated_at'       => date('Y-m-d H:i:s'),
        );
        $this->db->where('id_job_posting_requirement', (int) $id_requirement);
        return $this->db->update('career_job_posting_requirements', $row);
    }

    /** Hapus persyaratan — hanya bila belum dipakai lamaran (dgn FK CASCADE). */
    public function delete_requirement($id_requirement)
    {
        $id = (int) $id_requirement;
        if ($id <= 0 || $this->requirement_dipakai($id)) {
            return FALSE;
        }
        $this->db->where('id_job_posting_requirement', $id);
        return $this->db->delete('career_job_posting_requirements');
    }

    /** TRUE mk lowongan layak terbit: >=1 persyr + tanggal valid. */
    public function boleh_terbit($id_job_posting)
    {
        $posting = $this->find_lowongan($id_job_posting);
        if (!$posting) { return array(FALSE, 'Lowongan tidak ditemukan.'); }
        $n = $this->db->where('id_job_posting', (int) $id_job_posting)
            ->count_all_results('career_job_posting_requirements');
        if ($n < 1) { return array(FALSE, 'Minimal 1 persyaratan.'); }
        if ($posting->opening_date === NULL || $posting->closing_date === NULL) {
            return array(FALSE, 'Tanggal buka dan tutup wajib diisi.');
        }
        if ($posting->closing_date < $posting->opening_date) {
            return array(FALSE, 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.');
        }
        return array(TRUE, '');
    }
}
