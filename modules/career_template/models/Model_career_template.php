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
            $this->db->like('name', $this->scurity($q));
        }
        if ($status === 'active') {
            $this->db->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $this->db->where('is_active', 0);
        }
        $this->db->order_by('name', 'ASC');
        if ($limit !== NULL) {
            $this->db->limit((int) $limit, (int) $offset);
        }
        return $this->db->get()->result();
    }

    public function career_count($q = NULL, $status = NULL)
    {
        $this->db->from($this->table_name);
        if ($q !== NULL && $q !== '') {
            $this->db->like('name', $this->scurity($q));
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
        $this->db->insert($this->table_name, $row);
        return (int) $this->db->insert_id();
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
        if ($id <= 0 || $this->used_by_postings($id)) {
            return FALSE;
        }
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table_name);
    }
}
