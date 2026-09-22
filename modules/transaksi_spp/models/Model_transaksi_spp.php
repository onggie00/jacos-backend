<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_transaksi_spp extends MY_Model {

    private $primary_key    = 'id_transaksi';
    private $table_name     = 'transaksi_spp';
    private $field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'description', 'total_biaya', 'status_transaksi', 'expired_datetime', 'created_at', 'updated_at', 'bulan', 'kode_tagihan', 'file_kwitansi'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    /**
     * Build WHERE clause dari multi-filter params.
     * @param array $filters array of ['field'=>..., 'operator'=>..., 'value'=>...]
     * @return string|null
     */
    private function _build_multi_where($filters = array())
    {
        if (empty($filters) || !is_array($filters)) {
            return null;
        }

        $conditions = array();
        foreach ($filters as $f) {
            $field    = $this->scurity($f['field']);
            $operator = isset($f['operator']) ? $this->scurity($f['operator']) : 'contains';
            $value    = $f['value'];

            if (empty($field) || ($value === '' || $value === null)) {
                continue;
            }

            // Special handling for status_transaksi
            if ($field === 'status_transaksi') {
                if ($operator === 'equals') {
                    $val_lower = strtolower($value);
                    if (strpos($val_lower, 'menunggu aktivasi') !== false || $value === '0') {
                        $conditions[] = "transaksi_spp.status_transaksi = '0'";
                    } elseif (strpos($val_lower, 'menunggu pembayaran') !== false || $value === '1') {
                        $conditions[] = "transaksi_spp.status_transaksi = '1'";
                    } elseif (strpos($val_lower, 'berhasil') !== false || strpos($val_lower, 'lunas') !== false || $value === '2') {
                        $conditions[] = "transaksi_spp.status_transaksi = '2'";
                    }
                }
                continue;
            }

            $db_field = 'transaksi_spp.' . $field;
            $escaped  = $this->db->escape_str($value);

            switch ($operator) {
                case 'equals':
                    $conditions[] = "{$db_field} = '{$escaped}'";
                    break;
                case 'starts_with':
                    $conditions[] = "{$db_field} LIKE '{$escaped}%'";
                    break;
                case 'ends_with':
                    $conditions[] = "{$db_field} LIKE '%{$escaped}'";
                    break;
                case 'gt':
                    $conditions[] = "{$db_field} > '{$escaped}'";
                    break;
                case 'lt':
                    $conditions[] = "{$db_field} < '{$escaped}'";
                    break;
                case 'contains':
                default:
                    $conditions[] = "{$db_field} LIKE '%{$escaped}%'";
                    break;
            }
        }

        if (empty($conditions)) {
            return null;
        }

        return '(' . implode(' AND ', $conditions) . ')';
    }

    /**
     * Build WHERE clause dari quick search (q + f).
     */
    private function _build_quick_where($q, $field)
    {
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            $iterasi = 1;
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "transaksi_spp." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "transaksi_spp." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            // Status transaksi text search
            if (strpos(strtolower($q), "menunggu aktivasi") !== false) {
                $where .= "OR (transaksi_spp.status_transaksi = '0') ";
            } elseif (strpos(strtolower($q), "menunggu pembayaran") !== false) {
                $where .= "OR (transaksi_spp.status_transaksi = '1') ";
            } elseif (strpos(strtolower($q), "berhasil") !== false || strpos(strtolower($q), "lunas") !== false) {
                $where .= "OR (transaksi_spp.status_transaksi = '2') ";
            }
            $where = '(' . $where . ')';
        } else {
            if ($field == "status_transaksi") {
                $val_lower = strtolower($q);
                if (strpos($val_lower, "menunggu aktivasi") !== false || $q == "0") {
                    $where .= "(transaksi_spp.status_transaksi = '0')";
                } elseif (strpos($val_lower, "menunggu pembayaran") !== false || $q == "1") {
                    $where .= "(transaksi_spp.status_transaksi = '1')";
                } else {
                    $where .= "(transaksi_spp.status_transaksi = '2')";
                }
            } else {
                $where .= "(transaksi_spp." . $field . " LIKE '%" . $this->db->escape_str($q) . "%')";
            }
        }

        return $where;
    }

    public function count_all($q = null, $field = null, $multi_filters = array())
    {
        $this->join_avaiable()->filter_avaiable();

        // Multi-filter优先
        if (!empty($multi_filters)) {
            $multi_where = $this->_build_multi_where($multi_filters);
            if ($multi_where) {
                $this->db->where($multi_where);
            }
        } elseif (!empty($q)) {
            $where = $this->_build_quick_where($q, $field);
            if ($where) {
                $this->db->where($where);
            }
        }

        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $multi_filters = array())
    {
        $this->join_avaiable()->filter_avaiable();

        // Multi-filter优先
        if (!empty($multi_filters)) {
            $multi_where = $this->_build_multi_where($multi_filters);
            if ($multi_where) {
                $this->db->where($multi_where);
            }
        } elseif (!empty($q)) {
            $where = $this->_build_quick_where($q, $field);
            if ($where) {
                $this->db->where($where);
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('transaksi_spp.updated_at', "DESC");
        $this->db->order_by('transaksi_spp.' . $this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->select('transaksi_spp.*');
        return $this;
    }

    public function updateSpp($tbl_spp, $id_spp, $bulan)
    {
        $object = [strtolower($bulan) => date('Y-m-d H:i:s')];
        $where = ['id' => $id_spp];
        return $this->db->update($tbl_spp, $object, $where);
    }

    public function filter_avaiable() {
        if (!$this->aauth->is_admin()) {
        }
        return $this;
    }
}

/* End of file Model_transaksi_spp.php */
