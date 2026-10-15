<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_career_audit — BACA SAJA career_audit_logs (Fase 6A).
 * Keputusan Lead: histori hanya di career_audit_logs (tanpa tabel stage history).
 */
class Model_career_audit extends MY_Model {

    public function __construct()
    {
        parent::__construct(array(
            'primary_key' => 'id_audit_log',
            'table_name'  => 'career_audit_logs',
        ));
    }

    /** Daftar audit dengan filter + paging server-side. */
    public function daftar($filter = array(), $limit = 20, $offset = 0)
    {
        $this->_where_filter($filter);
        $this->db->from('career_audit_logs')
            ->order_by('created_at', 'DESC')
            ->order_by('id_audit_log', 'DESC')
            ->limit((int) $limit, (int) $offset);
        return $this->db->get()->result();
    }

    public function daftar_count($filter = array())
    {
        $this->_where_filter($filter);
        $this->db->from('career_audit_logs');
        return (int) $this->db->count_all_results();
    }

    private function _where_filter($filter)
    {
        if (isset($filter['action']) && $filter['action'] !== '') {
            $this->db->where('action', $filter['action']);
        }
        if (isset($filter['object_type']) && $filter['object_type'] !== '') {
            $this->db->where('object_type', $filter['object_type']);
        }
        if (isset($filter['object_id']) && (int) $filter['object_id'] > 0) {
            $this->db->where('object_id', (int) $filter['object_id']);
        }
        if (isset($filter['actor_type']) && in_array($filter['actor_type'], array('candidate','admin','system'), TRUE)) {
            $this->db->where('actor_type', $filter['actor_type']);
        }
        if (isset($filter['object_type']) && $filter['object_type'] === 'admin' && isset($filter['actor_id']) && (int) $filter['actor_id'] > 0) {
            $this->db->where('actor_id', (int) $filter['actor_id']);
        }
        if (isset($filter['tgl_dari']) && $filter['tgl_dari']) {
            $this->db->where('created_at >=', $filter['tgl_dari'] . ' 00:00:00');
        }
        if (isset($filter['tgl_sampai']) && $filter['tgl_sampai']) {
            $this->db->where('created_at <=', $filter['tgl_sampai'] . ' 23:59:59');
        }
    }

    /** Distinct action utk dropdown filter. */
    public function aksi_unik()
    {
        return $this->db->select('DISTINCT action', FALSE)
            ->order_by('action', 'ASC')
            ->get('career_audit_logs')->result_array();
    }

    /** Distinct object_type utk dropdown. */
    public function tipe_objek_unik()
    {
        return $this->db->select('DISTINCT object_type', FALSE)
            ->order_by('object_type', 'ASC')
            ->get('career_audit_logs')->result_array();
    }
}
