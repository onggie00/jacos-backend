<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_career_template — master template persyaratan (Fase 6A).
 * Tabel career_requirement_templates. Layout tidak boleh berubah —
 * kolom baru harus lewat migration terpisah.
 */
class Model_career_template extends MY_Model {

    private $primary_key   = 'id_requirement_template';
    private $table_name    = 'career_requirement_templates';
    private $field_search  = ['name'];
    private $field_order   = [NULL, 'name'];

    public function __construct()
    {
        $config = array(
            'primary_key'  => $this->primary_key,
            'table_name'   => $this->table_name,
            'field_search' => $this->field_search,
         );

        parent::__construct($config);
    }

    /** Daftar + filter nama + status aktif. */
    public function career_list($q = NULL, $status = NULL, $limit = NULL, $offset = 0)
    {
        $this->db->from($this->table_name);
        if ($q !== NULL && $q !== '') {
            $this->db->like('name', $this->db->escape_like_str(career_q($q)), 'both', FALSE);
        }
        if ($status === 'active') {
            $this->db->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $this->db->where('is_active', 0);
        }
        $this->db->order_by('name', 'ASC');
        $offset = max(0, (int) $offset);
        if ($limit !== NULL) {
            $this->db->limit((int) $limit, $offset);
        }
        return $this->db->get()->result();
    }

    public function career_count($q = NULL, $status = NULL)
    {
        $this->db->from($this->table_name);
        if ($q !== NULL && $q !== '') {
            $this->db->like('name', $this->db->escape_like_str(career_q($q)), 'both', FALSE);
        }
        if ($status === 'active') {
            $this->db->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $this->db->where('is_active', 0);
        }
        return (int) $this->db->count_all_results();
    }

    /** TRUE bila template sudah pernah dipakai oleh baris lowongan. */
    public function used_by_postings($id)
    {
        $n = $this->db->where('id_requirement_template', (int) $id)
            ->count_all_results('career_job_posting_requirements');
        return $n > 0;
    }

    /**
     * Peta id_template => bool dipakai untuk satu halaman list
     * (MED #5/#11 — hindari query N+1 per baris).
     */
    public function used_map(array $ids)
    {
        $map = array();
        $ids = array_map('intval', $ids);
        if ($ids === array()) { return $map; }
        $rows = $this->db->select('id_requirement_template')
            ->where_in('id_requirement_template', $ids)
            ->group_by('id_requirement_template')
            ->get('career_job_posting_requirements')->result();
        foreach ($rows as $r) { $map[(int) $r->id_requirement_template] = TRUE; }
        return $map;
    }

    /** Simpan (insert) — timestamps diisi eksplisit. */
    public function store_template($data)
    {
        $now = date('Y-m-d H:i:s');
        $row = array(
            'name'        => $data['name'],
            'description' => $data['description'],
            'is_active'   => $data['is_active'],
            'created_at'  => $now,
            'updated_at'  => $now,
        );
        $ok = $this->db->insert($this->table_name, $row);
        return $ok ? (int) $this->db->insert_id() : 0;
    }

    /** Update template (semua kolom editable modul). */
    public function change_template($id, $data)
    {
        $row = array(
            'name'        => $data['name'],
            'description' => $data['description'],
            'is_active'   => $data['is_active'],
            'updated_at'  => date('Y-m-d H:i:s'),
        );
        $this->db->where($this->primary_key, (int) $id);
        return $this->db->update($this->table_name, $row);
    }

    public function find_template($id)
    {
        $this->db->where($this->primary_key, (int) $id);
        return $this->db->get($this->table_name)->row();
    }

    /** Hapus hanya bila belum pernah dipakai baris lowongan (D12). */
    public function delete_unused($id)
    {
        $id = (int) $id;
        if ($id <= 0) { return FALSE; }
        // Transaksi + check-then-delete atomik (MED #10): kandidat mungkin
        // menyusun lamaran di antara check dan delete — lalu FK RESTRICT
        // bikin DB error kasar. Kunci baris + cek di dalam satu transaksi.
        $this->db->trans_start();
        $row = $this->db->select('id_requirement_template')
            ->where($this->primary_key, $id)
            ->for_update()->get($this->table_name)->row();
        $used = $this->used_by_postings($id);
        if ($row === NULL || ! $used) {
            $this->db->where($this->primary_key, $id);
            $this->db->delete($this->table_name);
        }
        $ok = $this->db->trans_complete();
        if ( ! $ok) { return FALSE; }
        return (! $used);
    }
}
