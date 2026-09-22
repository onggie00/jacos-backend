<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mymodel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /* ================= GET ================= */

    public function getall($table)
    {
        return $this->db->get($table)->result();
    }

    public function getallsort($table, $order, $sort = 'ASC')
    {
        return $this->db
            ->order_by($order, $sort)
            ->get($table)
            ->result();
    }

    public function getlast($table, $order)
    {
        return $this->db
            ->order_by($order, 'DESC')
            ->limit(1)
            ->get($table)
            ->row();
    }

    public function getfirst($table, $order)
    {
        return $this->db
            ->order_by($order, 'ASC')
            ->limit(1)
            ->get($table)
            ->row();
    }

    public function getalllimit($table, $start, $limit)
    {
        return $this->db
            ->limit($limit, $start)
            ->get($table)
            ->result();
    }

    public function getalllimitdesc($table, $start, $limit, $order)
    {
        return $this->db
            ->order_by($order, 'DESC')
            ->limit($limit, $start)
            ->get($table)
            ->result();
    }

    public function getalllimitasc($table, $start, $limit, $order)
    {
        return $this->db
            ->order_by($order, 'ASC')
            ->limit($limit, $start)
            ->get($table)
            ->result();
    }

    /* ================= WHERE ================= */

    private function applyWhere($where, $id = '')
    {
        if (is_array($where)) {
            $this->db->where($where);
        } elseif ($id !== '') {
            $this->db->where($where, $id);
        } else {
            $this->db->where($where);
        }
    }

    public function getbywhere($table, $where, $id = '', $result = 'result')
    {
        $this->applyWhere($where, $id);

        $query = $this->db->get($table);

        return ($result === 'row')
            ? $query->row()
            : $query->result();
    }

    public function getbywherelimit($table, $where, $key = '', $start, $limit)
    {
        $this->applyWhere($where, $key);

        return $this->db
            ->limit($limit, $start)
            ->get($table)
            ->result();
    }

    public function getbywheresort($table, $where, $key = '', $order, $sort)
    {
        $this->applyWhere($where, $key);

        return $this->db
            ->order_by($order, $sort)
            ->get($table)
            ->result();
    }

    public function getbywherelimitsort($table, $where, $key = '', $start, $limit, $order, $sort)
    {
        $this->applyWhere($where, $key);

        return $this->db
            ->order_by($order, $sort)
            ->limit($limit, $start)
            ->get($table)
            ->result();
    }

    public function getlastwhere($table, $where, $id = '', $order)
    {
        $this->applyWhere($where, $id);

        return $this->db
            ->order_by($order, 'DESC')
            ->limit(1)
            ->get($table)
            ->row();
    }

    /* ================= RAW QUERY (SAFER) ================= */

    public function withquery($sql, $result = 'result', $bind = [])
    {
        $query = $this->db->query($sql, $bind);

        // INSERT/UPDATE/DELETE return bool, bukan result object
        if ($query === true || $query === false) {
            return $query;
        }

        return ($result === 'row')
            ? $query->row()
            : $query->result();
    }

    /* ================= INSERT ================= */

    public function insert($table, $data)
    {
        $this->db->insert($table, $data);

        return $this->db->affected_rows();
    }

    public function insertid($table, $data)
    {
        $this->db->insert($table, $data);

        return $this->db->insert_id();
    }

    /* ================= UPDATE ================= */

    public function update($table, $data, $where, $id = '')
    {
        $this->applyWhere($where, $id);

        $this->db->update($table, $data);

        return $this->db->affected_rows();
    }

    public function update3($table, $data, $where, $like)
    {
        $this->db
            ->where($where)
            ->like($like)
            ->update($table, $data);

        return $this->db->affected_rows();
    }

    /* ================= DELETE ================= */

    public function delete($table, $where, $id)
    {
        $this->db
            ->where($where, $id)
            ->delete($table);

        return $this->db->affected_rows();
    }

    public function delete2($table, $where, $id, $where2, $id2)
    {
        $this->db
            ->where($where, $id)
            ->where($where2, $id2)
            ->delete($table);

        return $this->db->affected_rows();
    }

    /* ================= HISTORY ================= */

    public function add_history($msg)
    {
        $us  = $this->session->userdata('admin');
        $cek = $this->admin->checkusername($us);

        if (!$cek) return false;

        $data = [
            'id_admin'  => $cek->id_admin,
            'keterangan'=> $msg,
            'date_time' => date('Y-m-d H:i:s')
        ];

        return $this->insert('history_admin', $data);
    }

}
