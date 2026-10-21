<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Guru KB (GURU.1 Bagian A).
 *
 * Mengikuti pola Model_guru_sd (MY_Model: store/change/remove; field_search).
 * Beda disengaja (per prompt A-3):
 *  - TANPA join mata_pelajaran_sd (mapel tidak dipakai untuk KB)
 *  - Soft delete deleted_at IS NULL di filter_avaiable
 *  - field_search dipersempit (tanpa kolom rahasia: token, login_token,
 *    token_expired, device_id)
 */
class Model_guru_kb extends MY_Model
{

    private $primary_key    = 'id_guru';
    private $table_name     = 'guru_kb';
    private $field_search   = ['nama_lengkap', 'nik', 'nuptk', 'npp', 'npwp', 'unit', 'email', 'id_posisi', 'status_kepegawaian', 'emp_code', 'keterangan_jabatan', 'jam_ajar', 'pendidikan_terakhir', 'universitas', 'jurusan', 'tahun_lulus', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'no_telp'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
        );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $fd) {
                if ($iterasi == 1) {
                    $where .= "guru_kb." . $fd . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "guru_kb." . $fd . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "guru_kb." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $sort = null, $sort_type = null)
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $fd) {
                if ($iterasi == 1) {
                    $where .= "guru_kb." . $fd . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "guru_kb." . $fd . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "guru_kb." . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) and count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);

        $sort_by = (empty($sort)) ? 'id_guru' : $this->scurity($sort);
        $sort_type = (empty($sort_type)) ? 'desc' : $this->scurity($sort_type);

        $this->db->order_by('guru_kb.' . $sort_by, $sort_type);

        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable()
    {
        $this->db->join('posisi_guru', 'posisi_guru.id_posisi = guru_kb.id_posisi', 'LEFT');
        $this->db->select('guru_kb.*,posisi_guru.nama_posisi as posisi_guru_nama_posisi');

        return $this;
    }

    public function filter_avaiable()
    {
        /** Soft delete: baris yang sudah dihapus tidak muncul di daftar. */
        $this->db->where('guru_kb.deleted_at IS NULL', NULL, FALSE);

        return $this;
    }
}

/* End of file Model_guru_kb.php */
/* Location: ./modules/guru_kb/models/Model_guru_kb.php */
