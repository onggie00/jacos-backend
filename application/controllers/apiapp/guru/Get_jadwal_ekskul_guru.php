<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_jadwal_ekskul_guru extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
        $jenjang = $this->post("jenjang");
        $today   = strtolower(formatHari(date("Y-m-d")));
        $list_data = [];

        // Query ekskul utama
        $sql = "SELECT DISTINCT e.id_ekskul, e.nama AS nama_ekskul, e.hari, e.jam, 
                    e.tahun_ajaran_aktif, e.semester_aktif 
                FROM ekskul e 
                LEFT JOIN ekskul_manajemen_{$jenjang} m ON e.id_ekskul = m.id_ekskul 
                WHERE e.hari LIKE '%{$today}%' AND e.jenjang = '{$jenjang}' 
                ORDER BY jam ASC";
        $data = $this->mymodel->withquery($sql, "result");

        // Helper ambil detail anggota
        $getDetailAnggota = function($id_ekskul, $tahun, $semester, $jenjang_table) {
            $sql = "SELECT m.id_member, m.id_siswa, s.nama_lengkap, k.label AS nama_kelas, 
                        m.jenjang, m.status_member, m.file_pembayaran
                    FROM ekskul_member_{$jenjang_table} m
                    JOIN siswa_{$jenjang_table}_aktif s ON m.id_siswa = s.id_siswa_{$jenjang_table}_aktif
                    JOIN kelas_{$jenjang_table} k ON s.id_kelas = k.id_kelas_{$jenjang_table}
                    WHERE m.id_ekskul = '{$id_ekskul}' 
                    AND m.tahun_ajaran = '{$tahun}' AND m.semester = '{$semester}'
                    ORDER BY k.label ASC, s.nama_lengkap ASC";
            return $this->mymodel->withquery($sql, "result");
        };

        // Proses ekskul per jenjang
        foreach ($data as $value) {
            $total_peserta = $total_hadir = $total_tidak_hadir = 0;
            $all_detail = [];

            // Ambil anggota SMA
            $data_sma = $getDetailAnggota($value->id_ekskul, $value->tahun_ajaran_aktif, $value->semester_aktif, $jenjang);

            // Jika jenjang = sma → ambil juga anggota FT
            $data_ft = [];
            if ($jenjang === "sma") {
                $jenjang_ft = "ft";
                $data_ft = $getDetailAnggota($value->id_ekskul, $value->tahun_ajaran_aktif, $value->semester_aktif, $jenjang_ft);
            }

            // Gabungkan anggota
            $all_detail = array_merge($data_sma, $data_ft);

            // Proses presensi + status
            foreach ($all_detail as $d) {
                $cek = $this->mymodel->withquery(
                    "SELECT id FROM ekskul_presensi_siswa_{$d->jenjang} 
                    WHERE id_siswa_aktif = '{$d->id_siswa}' 
                    AND tanggal_absen = '".date("Y-m-d")."' 
                    AND id_ekskul = '{$value->id_ekskul}' 
                    AND status_absen = 'Hadir'", 
                    "row"
                );
                $d->is_absen = (empty($cek)) ? false : true;
                $total_hadir += $d->is_absen ? 1 : 0;
                $total_tidak_hadir += $d->is_absen ? 0 : 1;
                $total_peserta++;

                // File pembayaran
                if (!empty($d->file_pembayaran)) {
                    $d->file_pembayaran = base_url("uploads/ekskul_member_{$d->jenjang}/") . $d->file_pembayaran;
                }

                // Status member
                $d->status_member_text = $d->status_member == "1" ? "Aktif" : "Tidak Aktif";
            }

            // Tambahkan hasil ke value
            $value->jadwal = ucfirst($today) . ", " . formatTanggal(date("Y-m-d"));
            $value->waktu  = date("H:i", strtotime($value->jam)) . " WIB";
            $value->total_peserta = $total_peserta;
            $value->total_hadir   = $total_hadir;
            $value->total_tidak_hadir = $total_tidak_hadir;
            $value->list_anggota  = $all_detail;

            $list_data[] = $value;
        }

        // Response
        $msg = !empty($list_data)
            ? ['status' => 200, 'message'=>'Data Ekstrakurikuler ditemukan', 'data'=>$list_data]
            : ['status' => 401, 'message'=>'Data Ekstrakurikuler tidak ditemukan', 'data'=>$list_data];

        $this->response($msg);
    }
}
