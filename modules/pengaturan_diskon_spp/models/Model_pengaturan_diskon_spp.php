<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pengaturan_diskon_spp extends MY_Model {

    private $primary_key    = 'id_setting';
    private $table_name     = 'pengaturan_diskon_spp';
    private $field_search   = ['jenjang'];

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
    * Label jenjang untuk dropdown form
    */
    public function jenjang_labels()
    {
        return [
            'kb' => 'KB (Kelompok Bermain)',
            'tk' => 'TK',
            'sd' => 'SD',
            'smp' => 'SMP',
            'sma' => 'SMA',
            'ft' => 'FT',
        ];
    }

    /**
    * Opsi tingkatan untuk dropdown (berdasarkan jenjang)
    */
    public function tingkatan_all($jenjang)
    {
        if (empty($jenjang) || !in_array($jenjang, ['kb','tk','sd','smp','sma','ft'])) {
            return array();
        }
        // pk tabel = id_tingkatan_{jenjang} mis. id_tingkatan_sd; alias jadi id_tingkatan
        $pk = 'id_tingkatan_' . $jenjang;
        $query = $this->db->query("select *, '".$pk."' as pk_kolom, ".$pk." as id_tingkatan from tingkatan_".$jenjang." order by (label+0 = 0) asc, (label+0) asc, label asc");

        return $query->result();
    }

    /**
    * Hitung row dgn alamat diskon yang sama (jenjang + id_tingkatan)
    * utk cegah duplikat; exclude_id dipakai saat edit_save
    */
    public function count_by_alamat($jenjang, $id_tingkatan, $exclude_id = null)
    {
        $sql = "select count(id_setting) as c from pengaturan_diskon_spp 
        where jenjang = '".str_replace("'", "", $jenjang)."' 
        and ".(!empty($id_tingkatan) ? "id_tingkatan = '".(int) $id_tingkatan."'" : "id_tingkatan is null")."
        ".(!empty($exclude_id) ? "and id_setting <> '".(int) $exclude_id."'" : "")."";
        $query = $this->db->query($sql);

        return ($query->row() && $query->row()->c > 0) ? true : false;
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "pengaturan_diskon_spp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pengaturan_diskon_spp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pengaturan_diskon_spp.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "pengaturan_diskon_spp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pengaturan_diskon_spp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pengaturan_diskon_spp.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset ? $offset : 0);
        $this->db->order_by('pengaturan_diskon_spp.'.$this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable()
    {
        $this->db->select('pengaturan_diskon_spp.*');

        return $this;
    }

    public function filter_avaiable()
    {
        return $this;
    }

}


/* End of file Model_pengaturan_diskon_spp.php */
/* Location: ./modules/pengaturan_diskon_spp/models/Model_pengaturan_diskon_spp.php */
