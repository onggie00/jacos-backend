<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_acara_presensi_evaluasi extends MY_Model {

    private $primary_key    = 'id_presensi_evaluasi';
    private $table_name     = 'acara_presensi_evaluasi';
    private $field_search   = ['id_acara', 'npp', 'id_evaluasi_form', 'jawaban'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $id_acara = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "acara_presensi_evaluasi.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "acara_presensi_evaluasi.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "acara_presensi_evaluasi.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        if (!empty($id_acara)) {
            $this->db->where('acara_presensi_evaluasi.id_acara', $id_acara);
        }
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $id_acara = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "acara_presensi_evaluasi.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "acara_presensi_evaluasi.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "acara_presensi_evaluasi.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        if (!empty($id_acara)) {
            $this->db->where('acara_presensi_evaluasi.id_acara', $id_acara);
        }
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('acara_presensi_evaluasi.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('acara', 'acara.id_acara = acara_presensi_evaluasi.id_acara', 'LEFT');
        $this->db->join('acara_evaluasi_form', 'acara_evaluasi_form.id_evaluasi_form = acara_presensi_evaluasi.id_evaluasi_form', 'LEFT');
        $this->db->join('guru_ft', 'guru_ft.npp = acara_presensi_evaluasi.npp', 'LEFT');
        // v2: LEFT JOIN acara_presensi (composite key: id_acara + npp) untuk fetch kolom peserta
        $this->db->join('acara_presensi', 'acara_presensi.id_acara = acara_presensi_evaluasi.id_acara AND acara_presensi.npp = acara_presensi_evaluasi.npp', 'LEFT');

        $this->db->select('acara_presensi_evaluasi.*,acara.nama_acara as acara_nama_acara,acara_evaluasi_form.pertanyaan as acara_evaluasi_form_pertanyaan,guru_ft.nama_lengkap as guru_nama_lengkap,acara_presensi.peserta as acara_presensi_peserta');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * v2: Fetch pertanyaan & jawaban by composite key (npp, peserta).
     * Peserta opsional — kalau kosong, return semua Q&A untuk npp tsb.
     */
    public function get_jawaban_by_peserta($npp = '', $peserta = '')
    {
        if (empty($npp)) return [];
        $this->join_avaiable();
        $this->db->where('acara_presensi_evaluasi.npp', $npp);
        if (!empty($peserta)) {
            $this->db->where('acara_presensi.peserta', $peserta);
        }
        $query = $this->db->get('acara_presensi_evaluasi');
        return $query->result();
    }

    /**
     * v2.2: Count distinct (npp, peserta) untuk pagination list summary.
     * Filter by id_acara + search_npp (LIKE).
     */
    public function count_distinct_peserta($id_acara = null, $search_npp = '')
    {
        $sql = "SELECT COUNT(*) as cnt FROM (
                    SELECT 1
                    FROM acara_presensi_evaluasi ape
                    LEFT JOIN acara_presensi ap ON ap.id_acara = ape.id_acara AND ap.npp = ape.npp
                    WHERE 1=1";
        if (!empty($id_acara)) {
            $sql .= " AND ape.id_acara = " . (int)$id_acara;
        }
        if (!empty($search_npp)) {
            $sql .= " AND ape.npp LIKE '%" . $this->scurity($search_npp) . "%'";
        }
        $sql .= " GROUP BY ape.npp, IFNULL(ap.peserta, '')
                ) AS sub";
        $row = $this->db->query($sql)->row();
        return (int)($row->cnt ?? 0);
    }

    /**
     * v2.2: Get distinct (npp, peserta) rows dengan qa_count, paginated.
     * Return array of objects: {npp, peserta, guru_nama_lengkap, qa_count}
     * Filter by id_acara + search_npp (LIKE).
     */
    public function get_distinct_peserta($id_acara = null, $limit = 0, $offset = 0, $search_npp = '')
    {
        $sql = "SELECT sub.npp, sub.peserta, g.nama_lengkap as guru_nama_lengkap, sub.qa_count, sub.sample_id
                FROM (
                    SELECT
                        ape.npp,
                        IFNULL(ap.peserta, '') as peserta,
                        COUNT(*) as qa_count,
                        MIN(ape.id_presensi_evaluasi) as sample_id
                    FROM acara_presensi_evaluasi ape
                    LEFT JOIN acara_presensi ap ON ap.id_acara = ape.id_acara AND ap.npp = ape.npp
                    WHERE 1=1";
        if (!empty($id_acara)) {
            $sql .= " AND ape.id_acara = " . (int)$id_acara;
        }
        if (!empty($search_npp)) {
            $sql .= " AND ape.npp LIKE '%" . $this->scurity($search_npp) . "%'";
        }
        $sql .= " GROUP BY ape.npp, IFNULL(ap.peserta, '')
                  ORDER BY ape.npp ASC
                  LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
                ) sub
                LEFT JOIN guru_ft g ON g.npp = sub.npp
                ORDER BY g.nama_lengkap ASC, sub.npp ASC";
        return $this->db->query($sql)->result();
    }

    /**
     * v2.3: Chart data - numeric jawaban count per pertanyaan (text jawaban diabaikan via REGEXP).
     * Filter by id_acara (opsional).
     * Return array of {id_evaluasi_form, pertanyaan, numeric_count, numeric_sum}
     * Deprecated di v2.4 - page-level chart dihapus, diganti per-peserta modal.
     */
    public function get_numeric_chart_data($id_acara = null)
    {
        $sql = "SELECT
                    aef.id_evaluasi_form,
                    aef.pertanyaan,
                    COUNT(CASE WHEN ape.jawaban REGEXP '^[0-9]+(\.[0-9]+)?$' THEN 1 END) as numeric_count,
                    SUM(CASE WHEN ape.jawaban REGEXP '^[0-9]+(\.[0-9]+)?$' THEN CAST(ape.jawaban AS DECIMAL(10,2)) ELSE 0 END) as numeric_sum
                FROM acara_presensi_evaluasi ape
                LEFT JOIN acara_evaluasi_form aef ON aef.id_evaluasi_form = ape.id_evaluasi_form
                WHERE aef.id_evaluasi_form IS NOT NULL";
        if (!empty($id_acara)) {
            $sql .= " AND ape.id_acara = " . (int)$id_acara;
        }
        $sql .= " GROUP BY aef.id_evaluasi_form, aef.pertanyaan
                  HAVING numeric_count > 0
                  ORDER BY aef.id_evaluasi_form ASC";
        return $this->db->query($sql)->result();
    }

    /**
     * v2.4: Chart data per peserta - numeric jawaban + no_urut pertanyaan.
     * JOIN acara_evaluasi_form untuk fetch no_urut (untuk X-axis) + pertanyaan.
     * Filter numeric jawaban only (REGEXP), ORDER BY no_urut ASC.
     * Return array of {id_evaluasi_form, no_urut, pertanyaan, jawaban_num}
     */
    public function get_chart_data_by_peserta($npp, $peserta, $id_acara = null)
    {
        if (empty($npp)) return [];

        $sql = "SELECT
                    aef.id_evaluasi_form,
                    aef.no_urut,
                    aef.pertanyaan,
                    CAST(ape.jawaban AS DECIMAL(10,2)) as jawaban_num
                FROM acara_presensi_evaluasi ape
                INNER JOIN acara_evaluasi_form aef ON aef.id_evaluasi_form = ape.id_evaluasi_form
                LEFT JOIN acara_presensi ap ON ap.id_acara = ape.id_acara AND ap.npp = ape.npp
                WHERE ape.npp = " . $this->db->escape($npp) . "
                  AND ape.jawaban REGEXP '^[0-9]+(\.[0-9]+)?$'";

        if (!empty($id_acara)) {
            $sql .= " AND ape.id_acara = " . (int)$id_acara;
        }
        if (!empty($peserta)) {
            $sql .= " AND IFNULL(ap.peserta, '') = " . $this->db->escape($peserta);
        }
        $sql .= " ORDER BY CAST(IFNULL(aef.no_urut, 999) AS UNSIGNED) ASC, aef.id_evaluasi_form ASC";

        return $this->db->query($sql)->result();
    }

    /**
     * v2.3: Chart data - completion: total peserta vs filled (have evaluasi entries).
     * Filter by id_acara (opsional).
     * Return array {total, filled, not_filled, percent_filled, percent_not_filled}
     */
    public function get_completion_chart_data($id_acara = null)
    {
        // Total peserta di acara_presensi untuk filter tsb
        $sql_total = "SELECT COUNT(DISTINCT npp) as total FROM acara_presensi WHERE 1=1";
        if (!empty($id_acara)) {
            $sql_total .= " AND id_acara = " . (int)$id_acara;
        }
        $total_row = $this->db->query($sql_total)->row();
        $total_peserta = (int)($total_row->total ?? 0);

        // Filled = DISTINCT npp yang punya entry di acara_presensi_evaluasi
        $sql_filled = "SELECT COUNT(DISTINCT ape.npp) as filled
                       FROM acara_presensi_evaluasi ape
                       WHERE 1=1";
        if (!empty($id_acara)) {
            $sql_filled .= " AND ape.id_acara = " . (int)$id_acara;
        }
        $filled_row = $this->db->query($sql_filled)->row();
        $filled = (int)($filled_row->filled ?? 0);

        $not_filled = max(0, $total_peserta - $filled);
        $percent_filled = $total_peserta > 0 ? round(($filled / $total_peserta) * 100, 1) : 0;
        $percent_not_filled = $total_peserta > 0 ? round(($not_filled / $total_peserta) * 100, 1) : 0;

        return [
            'total' => $total_peserta,
            'filled' => $filled,
            'not_filled' => $not_filled,
            'percent_filled' => $percent_filled,
            'percent_not_filled' => $percent_not_filled,
        ];
    }

}

/* End of file Model_acara_presensi_evaluasi.php */
/* Location: ./application/models/Model_acara_presensi_evaluasi.php */