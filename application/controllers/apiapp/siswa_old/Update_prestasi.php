<?php
header("Access-Control-Allow-Origin: *"); header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); header('Access-Control-Allow-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

class Update_prestasi extends REST_Controller {
    function __construct() {
        parent::__construct();
    }

    /**
     * POST - Update data prestasi siswa.
     *
     * Hanya untuk row dengan is_approved = 0. Kalau approved=1 -> 403.
     * Plus: FK check, date format, DB transaction.
     *
     * Required: id_prestasi, jenjang (sd/smp/sma/ft), x-token header,
     *           nama_prestasi, id_siswa, tgl_raih (YYYY-MM-DD),
     *           jenis_prestasi_id, id_prestasi_bidang.
     *
     * TIDAK boleh diupdate dari sisi client: is_approved, tanggal_posting.
     */
    function index_post() {

        // ====== C1 (Option B): minimum auth check - x-token wajib ada. ======
        // Validasi JWT penuh / owner check (id_siswa vs pemilik token) di-defer (lihat TASKS.md).
        $token = "";
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        if (isset($headers['x-token'])) $token = $headers['x-token'];

        if (empty($token)) {
            $msg = array('status' => 0, 'message' => 'x-token wajib diisi', 'data' => array());
            $this->response($msg, 401);
            return;
        }

        // ====== Validasi id_prestasi & jenjang ======
        $id_prestasi = $this->post('id_prestasi');
        $jenjang     = $this->post('jenjang');

        if (empty($id_prestasi)) {
            $msg = array('status' => 0, 'message' => 'id_prestasi wajib diisi', 'data' => array());
            $this->response($msg, 400);
            return;
        }

        // Tabel per jenjang (mirror Tambah_prestasi)
        if ($jenjang == 'sd') {
            $table     = 'prestasi_siswa_sd';
            $uploaddir = './uploads/prestasi_siswa_sd/';
        } elseif ($jenjang == 'smp') {
            $table     = 'prestasi_siswa_smp';
            $uploaddir = './uploads/prestasi_siswa_smp/';
        } elseif ($jenjang == 'sma') {
            $table     = 'prestasi_siswa_sma';
            $uploaddir = './uploads/prestasi_siswa_sma/';
        } else {
            $table     = 'prestasi_siswa_ft';
            $uploaddir = './uploads/prestasi_siswa_ft/';
        }

        // ====== Cek row exists + is_approved ======
        $existing = $this->mymodel->getbywhere($table, 'id_prestasi', $id_prestasi, 'row');

        if (empty($existing)) {
            $msg = array('status' => 0, 'message' => 'Data prestasi tidak ditemukan', 'data' => array());
            $this->response($msg, 404);
            return;
        }

        if (isset($existing->is_approved) && (int)$existing->is_approved === 1) {
            $msg = array('status' => 0, 'message' => 'Data sudah disetujui, tidak dapat diubah', 'data' => array());
            $this->response($msg, 403);
            return;
        }

        // ====== Ambil nilai kurasi_pusprenas & link_pusprenas (tidak wajib) ======
        $kurasi_pusprenas = $this->post('kurasi_pusprenas');
        $link_pusprenas   = $this->post('link_pusprenas');

        // ====== H1 (Option B): validasi required fields. ======
        $required = array(
            'nama_prestasi'      => 'Nama prestasi',
            'id_siswa'           => 'ID siswa',
            'tgl_raih'           => 'Tanggal raih',
            'jenis_prestasi_id'  => 'ID jenis prestasi',
            'id_prestasi_bidang' => 'ID bidang prestasi',
        );
        foreach ($required as $field => $label) {
            $val = $this->post($field);
            if ($val === null || $val === '' || (is_string($val) && trim($val) === '')) {
                $msg = array('status' => 0, 'message' => $label . ' wajib diisi', 'data' => array());
                $this->response($msg, 400);
                return;
            }
        }

        // ====== D1: Foreign Key check ======
        // Cegah orphan row: pastikan id_siswa, jenis_prestasi_id, id_prestasi_bidang ada di tabel master.

        // -- id_siswa: cek di siswa_{jenjang}_aktif (kolom id_siswa)
        $siswa_aktif_table = '';
        if ($jenjang == 'sd')      $siswa_aktif_table = 'siswa_sd_aktif';
        elseif ($jenjang == 'smp') $siswa_aktif_table = 'siswa_smp_aktif';
        elseif ($jenjang == 'sma') $siswa_aktif_table = 'siswa_sma_aktif';
        else                       $siswa_aktif_table = 'siswa_ft_aktif';

        $id_siswa_post = (int)$this->post('id_siswa');
        // Cek di siswa_aktif dulu (kolom 'id_siswa' ada di sini umumnya)
        $siswa_check = $this->mymodel->getbywhere($siswa_aktif_table, 'id_siswa_' . $jenjang . '_aktif', $id_siswa_post, 'row');
        if (empty($siswa_check)) {
            // Fallback: cek di tabel master siswa_{jenjang}
            $siswa_master = str_replace('_aktif', '', $siswa_aktif_table);
            $siswa_check2 = $this->mymodel->getbywhere($siswa_master, 'id_siswa_' . $jenjang . '_aktif', $id_siswa_post, 'row');
            if (empty($siswa_check2)) {
                $msg = array('status' => 0, 'message' => 'id_siswa tidak ditemukan di tabel master siswa', 'data' => array());
                $this->response($msg, 400);
                return;
            }
        }

        // -- jenis_prestasi_id: cek di tabel jenis_prestasi
        $jenis_prestasi_id_post = (int)$this->post('jenis_prestasi_id');
        $jp_check = $this->mymodel->getbywhere('jenis_prestasi', 'id_jenis_prestasi', $jenis_prestasi_id_post, 'row');
        if (empty($jp_check)) {
            $msg = array('status' => 0, 'message' => 'jenis_prestasi_id tidak ditemukan di tabel jenis_prestasi', 'data' => array());
            $this->response($msg, 400);
            return;
        }

        // -- id_prestasi_bidang: cek di tabel prestasi_siswa_bidang
        $id_prestasi_bidang_post = (int)$this->post('id_prestasi_bidang');
        $pb_check = $this->mymodel->getbywhere('prestasi_siswa_bidang', 'id_prestasi_bidang', $id_prestasi_bidang_post, 'row');
        if (empty($pb_check)) {
            $msg = array('status' => 0, 'message' => 'id_prestasi_bidang tidak ditemukan di tabel prestasi_siswa_bidang', 'data' => array());
            $this->response($msg, 400);
            return;
        }

        // ====== D3: Validasi format tanggal tgl_raih (YYYY-MM-DD) ======
        $tgl_raih_post = $this->post('tgl_raih');
        $d = DateTime::createFromFormat('Y-m-d', $tgl_raih_post);
        if (!$d || $d->format('Y-m-d') !== $tgl_raih_post) {
            $msg = array('status' => 0, 'message' => 'Format tgl_raih tidak valid (gunakan YYYY-MM-DD)', 'data' => array());
            $this->response($msg, 400);
            return;
        }

        // ====== Siapkan data update (TIDAK include is_approved, tanggal_posting) ======
        $data = array(
            'nama_prestasi'      => $this->post('nama_prestasi'),
            'id_siswa'           => $id_siswa_post,
            'konten'             => $this->post('konten'),
            'tgl_raih'           => $tgl_raih_post,
            'judul'              => (!empty($this->post('judul'))) ? $this->post('judul') : '',
            'jenis_prestasi_id'  => $jenis_prestasi_id_post,
            'juara'              => $this->post('juara'),
            'id_prestasi_bidang' => $id_prestasi_bidang_post,
            'kurasi_pusprenas'   => $kurasi_pusprenas,
            'link_pusprenas'     => $link_pusprenas,
        );

        // ====== H2 (Option B): whitelist ekstensi + size cap upload. ======
        $upload_allowed_ext = array('jpg','jpeg','png','gif','heic','webp');
        $upload_max_bytes   = 10 * 1024 * 1024;

        // File lama yang akan dihapus setelah DB commit sukses.
        $old_files_to_cleanup = array();
        // File baru yang sudah ter-upload - perlu di-rollback kalau DB gagal.
        $new_files_uploaded = array();

        // ====== Handle file_prestasi (replace + hapus file lama) ======
        if (!empty($_FILES['file_prestasi']['name'])) {
            $img = explode('.', $_FILES['file_prestasi']['name']);
            $extension = strtolower(end($img));
            if (!in_array($extension, $upload_allowed_ext)) {
                $msg = array('status' => 0, 'message' => 'Ekstensi file_prestasi tidak diizinkan: ' . $extension, 'data' => array());
                $this->response($msg, 400);
                return;
            }
            if (isset($_FILES['file_prestasi']['size']) && (int)$_FILES['file_prestasi']['size'] > $upload_max_bytes) {
                $msg = array('status' => 0, 'message' => 'Ukuran file_prestasi melebihi 10MB', 'data' => array());
                $this->response($msg, 400);
                return;
            }

            $file_name  = md5(date('y-m-d h:i:s') . $_FILES['file_prestasi']['name']) . '.' . $extension;
            $uploadfile = $uploaddir . $file_name;
            if (move_uploaded_file($_FILES['file_prestasi']['tmp_name'], $uploadfile)) {
                $data['file_prestasi'] = $file_name;
                $new_files_uploaded[] = $uploadfile;
                if (!empty($existing->file_prestasi)) {
                    $old_files_to_cleanup[] = $uploaddir . $existing->file_prestasi;
                }
            }
        }

        // ====== Handle foto_prestasi (replace + hapus file lama) ======
        if (!empty($_FILES['foto_prestasi']['name'])) {
            $img = explode('.', $_FILES['foto_prestasi']['name']);
            $extension = strtolower(end($img));
            if (!in_array($extension, $upload_allowed_ext)) {
                $msg = array('status' => 0, 'message' => 'Ekstensi foto_prestasi tidak diizinkan: ' . $extension, 'data' => array());
                $this->response($msg, 400);
                return;
            }
            if (isset($_FILES['foto_prestasi']['size']) && (int)$_FILES['foto_prestasi']['size'] > $upload_max_bytes) {
                $msg = array('status' => 0, 'message' => 'Ukuran foto_prestasi melebihi 10MB', 'data' => array());
                $this->response($msg, 400);
                return;
            }

            $file_name  = md5(date('y-m-d h:i:s') . $_FILES['foto_prestasi']['name']) . '.' . $extension;
            $uploadfile = $uploaddir . $file_name;
            if (move_uploaded_file($_FILES['foto_prestasi']['tmp_name'], $uploadfile)) {
                $data['foto_prestasi'] = $file_name;
                $new_files_uploaded[] = $uploadfile;
                if (!empty($existing->foto_prestasi)) {
                    $old_files_to_cleanup[] = $uploaddir . $existing->foto_prestasi;
                }
            }
        }

        // ====== TRANSACTION: Update DB dengan rollback safety ======
        $this->db->trans_start();
        $this->mymodel->update($table, $data, 'id_prestasi', $id_prestasi);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // DB gagal - hapus file baru yang sudah ter-upload (rollback fisik).
            foreach ($new_files_uploaded as $new_path) {
                if (is_file($new_path)) {
                    @unlink($new_path);
                }
            }
            $msg = array('status' => 0, 'message' => 'Gagal update data (DB transaction failed)', 'data' => array());
            $this->response($msg, 500);
            return;
        }

        // DB commit sukses - hapus file lama (best-effort).
        foreach ($old_files_to_cleanup as $old_path) {
            if (is_file($old_path)) {
                @unlink($old_path);
            }
        }

        // Re-fetch untuk response.
        $updated = $this->mymodel->getbywhere($table, 'id_prestasi', $id_prestasi, 'row');

        $msg = array(
            'status'  => 1,
            'message' => 'Berhasil update data',
            'data'    => $updated ? $updated : array()
        );

        $this->response($msg);
    }
}
