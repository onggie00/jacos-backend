<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API: Presensi_siswa_catatan (v2 — multi-pelanggaran)
 *
 * Tujuan  : Mencatat / memperbarui presensi kehadiran siswa per jam pelajaran
 *           dengan dukungan BANYAK catatan pelanggaran sekaligus (multi-detail).
 * Akses   : Guru (atau operator dengan token guru pada jenjang yang sama).
 * Tabel   : presensi_catatan_pelajaran (header) + presensi_catatan_detail (detail)
 *
 * Input POST (form-data / json):
 *   - jenjang                  : 'sd' | 'smp' | 'sma' | 'ft'
 *   - id_siswa_aktif[]         : array of int (siswa target — semua dapat kategori yg sama)
 *   - jam_ke                   : int 1..9
 *   - tanggal_waktu            : datetime (Y-m-d H:i:s)
 *   - kategori_catatan[]       : array of int (paralel dgn id_siswa_aktif[]; 1 kategori per siswa).
 *                                Kalau siswa butuh 2+ kategori, kirim request terpisah.
 *   - jenis_pelanggaran_custom[] : array of string (opsional, paralel; wajib kalau kategori = "Lainnya")
 *   - skor_custom[]            : array of int (opsional, paralel; default 0; dipakai kalau kategori = "Lainnya")
 *   - keterangan[]             : array of string (opsional, paralel; catatan spesifik pelanggaran)
 *   - keterangan_umum          : string (opsional) — disimpan di header.keterangan
 *
 * Header:
 *   - x-token                  : token login guru_<jenjang>
 *
 * Aturan update: jika sudah ada catatan untuk (id_siswa_aktif, hari, jam_ke,
 * tanggal_waktu = tanggal_yang_dipilih), row di-UPDATE (updated_at + updated_by
 * di-set) dan SEMUA detail lama di-replace dengan detail dari payload.
 * created_at di-handle DB. Seluruh proses dibungkus transaction; gagal -> rollback.
 *
 * Catatan: untuk backward compat dgn FE lama, kalau `kategori_catatan[]` TIDAK dikirim,
 * sistem fall-back ke mode lama: simpan `keterangan_umum` di header saja (tanpa detail).
 */
class Presensi_siswa_catatan extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index_post()
    {
        // === 1. Ambil x-token dari header ===
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[strtolower($name)] = $value;
        }
        $token = isset($headers['x-token']) ? $headers['x-token'] : '';

        if (empty($token)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'x-token tidak ditemukan',
                'data'    => array(),
            ));
            return;
        }

        // === 2. Ambil & validasi input ===
        $jenjang        = $this->post('jenjang');
        $id_siswa_aktif = $this->post('id_siswa_aktif');
        $jam_ke_raw     = $this->post('jam_ke');
        $tanggal_waktu  = $this->post('tanggal_waktu');

        // --- 2a. Validasi jenjang ---
        if (empty($jenjang) || !in_array($jenjang, array('sd', 'smp', 'sma', 'ft'))) {
            $this->response(array(
                'status'  => 0,
                'message' => 'jenjang tidak valid. Pilihan: sd, smp, sma, ft',
                'data'    => array(),
            ));
            return;
        }

        // --- 2b. Validasi id_siswa_aktif ---
        if (empty($id_siswa_aktif) || !is_array($id_siswa_aktif)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'id_siswa_aktif harus berupa array. Contoh: [1,2,3]',
                'data'    => array(),
            ));
            return;
        }

        // --- 2c. Validasi jam_ke (0..12) ---
        if (!is_numeric($jam_ke_raw)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'jam_ke tidak valid. Pilihan: 0 sampai 12',
                'data'    => array(),
            ));
            return;
        }
        $jam_ke = (int)$jam_ke_raw;
        if ($jam_ke < 0 || $jam_ke > 12) {
            $this->response(array(
                'status'  => 0,
                'message' => 'jam_ke tidak valid. Pilihan: 0 sampai 12',
                'data'    => array(),
            ));
            return;
        }

        // --- 2d. Validasi tanggal_waktu ---
        if (empty($tanggal_waktu) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $tanggal_waktu)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'tanggal_waktu tidak valid. Format: Y-m-d H:i:s',
                'data'    => array(),
            ));
            return;
        }

        // === 3. Validasi token & ambil nama guru ===
        $guru = $this->mymodel->withquery(
            "SELECT id_guru, nama_lengkap FROM guru_$jenjang
             WHERE token = '$token' AND deleted_at IS NULL LIMIT 1",
            "row"
        );
        if (empty($guru)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'Token tidak valid atau guru tidak ditemukan di jenjang ' . $jenjang,
                'data'    => array(),
            ));
            return;
        }
        $nama_guru = $guru->nama_lengkap;

        // === 4. Resolve kategori_catatan[] (paralel dgn id_siswa_aktif[]) ===
        // 1 kategori per siswa. Kalau siswa butuh 2+ kategori, kirim request terpisah.
        // Array: kategori_catatan[], jenis_pelanggaran_custom[], skor_custom[], keterangan[]
        // harus sama panjang dgn id_siswa_aktif[].
        $kategori_in        = $this->post('kategori_catatan');
        $jenis_custom_in    = $this->post('jenis_pelanggaran_custom');
        $skor_custom_in     = $this->post('skor_custom');
        $keterangan_in      = $this->post('keterangan');
        $keterangan_umum_in = $this->post('keterangan_umum');

        $detail_payloads = array(); // index => detail row, paralel dgn id_siswa_aktif
        $use_detail = false;

        if (is_array($kategori_in) && count($kategori_in) > 0) {
            $n = count($kategori_in);
            $n_siswa = count($id_siswa_aktif);

            // Validasi: kategori_catatan[] harus sama panjang dgn id_siswa_aktif[]
            if ($n !== $n_siswa) {
                $this->response(array(
                    'status'  => 0,
                    'message' => 'kategori_catatan[] harus sama panjang dgn id_siswa_aktif[] (' . $n . ' vs ' . $n_siswa . ')',
                    'data'    => array(),
                ));
                return;
            }
            // Validasi panjang paralel opsional
            $len_jenis = is_array($jenis_custom_in) ? count($jenis_custom_in) : 0;
            $len_skor  = is_array($skor_custom_in)  ? count($skor_custom_in)  : 0;
            $len_ket   = is_array($keterangan_in)   ? count($keterangan_in)   : 0;
            if (($len_jenis > 0 && $len_jenis !== $n) ||
                ($len_skor  > 0 && $len_skor  !== $n) ||
                ($len_ket   > 0 && $len_ket   !== $n)) {
                $this->response(array(
                    'status'  => 0,
                    'message' => 'Array jenis_pelanggaran_custom/skor_custom/keterangan harus paralel dgn kategori_catatan (panjang sama)',
                    'data'    => array(),
                ));
                return;
            }

            // Lookup semua master kategori sekaligus
            $kategori_ids = array_map('intval', $kategori_in);
            $master_rows = $this->mymodel->withquery(
                "SELECT id_kategori_catatan, nama_kategori, skor, is_custom
                 FROM presensi_kategori_catatan
                 WHERE id_kategori_catatan IN (" . implode(',', $kategori_ids) . ")
                   AND status = 'aktif'",
                "result"
            );
            $master_map = array();
            if (!empty($master_rows)) {
                foreach ($master_rows as $mr) {
                    $master_map[(int)$mr->id_kategori_catatan] = $mr;
                }
            }

            $use_detail = true;
            for ($i = 0; $i < $n; $i++) {
                $kid = (int)$kategori_in[$i];
                if (!isset($master_map[$kid])) {
                    $this->response(array(
                        'status'  => 0,
                        'message' => 'kategori_catatan id=' . $kid . ' (index ' . $i . ') tidak ditemukan / nonaktif',
                        'data'    => array(),
                    ));
                    return;
                }
                $m = $master_map[$kid];
                $jenis_custom = ($len_jenis > 0 && isset($jenis_custom_in[$i])) ? trim($jenis_custom_in[$i]) : '';
                $skor_raw     = ($len_skor  > 0 && isset($skor_custom_in[$i]))  ? $skor_custom_in[$i]     : null;
                $keterangan   = ($len_ket   > 0 && isset($keterangan_in[$i]))   ? trim($keterangan_in[$i]) : null;

                if ((int)$m->is_custom === 1) {
                    // Kategori "Lainnya" — wajib isi jenis_pelanggaran_custom
                    if ($jenis_custom === '' || $jenis_custom === '-') {
                        $this->response(array(
                            'status'  => 0,
                            'message' => "kategori 'Lainnya' (index $i) wajib mengisi jenis_pelanggaran_custom",
                            'data'    => array(),
                        ));
                        return;
                    }
                    $skor_final = is_numeric($skor_raw) ? (int)$skor_raw : 0;
                } else {
                    // Non-Lainnya: jenis_custom = NULL, skor dari master
                    $jenis_custom = null;
                    $skor_final   = (int)$m->skor;
                }

                $detail_payloads[$i] = array(
                    'id_kategori_catatan'      => $kid,
                    'jenis_pelanggaran_custom' => $jenis_custom,
                    'skor'                     => $skor_final,
                    'keterangan'               => $keterangan,
                );
            }
        }
        // else: legacy fallback — tanpa detail, simpan keterangan_umum di header

        // === 5. Variabel turunan ===
        $hari_ini             = formatHari(substr($tanggal_waktu, 0, 10));
        $tanggal_hari_ini     = substr($tanggal_waktu, 0, 10);
        $kolom_pk_siswa       = 'id_siswa_' . $jenjang . '_aktif';

        // Resolve active tahun_ajaran (MAX sequence)
        $active_tahun = $this->mymodel->withquery(
            "SELECT id_tahun_ajaran FROM tahun_ajaran ORDER BY sequence DESC LIMIT 1",
            "row"
        );
        $id_tahun_ajaran = $active_tahun ? (int)$active_tahun->id_tahun_ajaran : null;

        // === 6. Transaksi DB: insert / update per siswa ===
        $data_berhasil = array();
        $data_dilewati = array();

        $this->db->trans_start();

        for ($i = 0; $i < count($id_siswa_aktif); $i++) {
            $siswa_id = (int)$id_siswa_aktif[$i];

            // --- 6a. Lookup siswa aktif (untuk nama_lengkap, nis) + nisn dari master ---
            $get_siswa = $this->mymodel->withquery(
                "SELECT sa.nama_lengkap, sa.nis, sa.id_siswa_$jenjang, s.nisn
                 FROM siswa_" . $jenjang . "_aktif sa
                 LEFT JOIN siswa_" . $jenjang . " s ON sa.id_siswa_$jenjang = s.id_siswa_$jenjang
                 WHERE sa." . $kolom_pk_siswa . " = " . $this->db->escape($siswa_id),
                "row"
            );

            if (empty($get_siswa)) {
                $data_dilewati[] = array(
                    'id_siswa_aktif' => $siswa_id,
                    'alasan'         => 'Siswa tidak ditemukan di siswa_' . $jenjang . '_aktif',
                );
                continue;
            }

            $nis  = $get_siswa->nis;
            $nisn_raw = isset($get_siswa->nisn) ? trim($get_siswa->nisn) : '';
            $nisn = ($nisn_raw === '' || $nisn_raw === '-') ? null : $nisn_raw;

            // --- 6b. Siapkan payload header ---
            $data = array(
                'id_siswa_aktif'   => $siswa_id,
                'nama_lengkap'     => $get_siswa->nama_lengkap,
                'nis'              => $nis,
                'nisn'             => $nisn,
                'id_tahun_ajaran'  => $id_tahun_ajaran,
                'hari'             => $hari_ini,
                'tanggal_waktu'    => $tanggal_waktu,
                'jenjang'          => $jenjang,
                'status_hadir'     => '',
                'keterangan'       => $keterangan_umum_in !== null ? (string)$keterangan_umum_in : '',
                'jam_ke'           => $jam_ke,
                'updated_by'       => $nama_guru,
            );

            // --- 6c. Cek existing ---
            $cek = $this->mymodel->withquery(
                "SELECT id_presensi_catatan FROM presensi_catatan_pelajaran
                 WHERE id_siswa_aktif = " . (int)$siswa_id . "
                   AND hari = '" . $hari_ini . "'
                   AND jam_ke = " . $jam_ke . "
                   AND DATE(tanggal_waktu) = '" . $tanggal_hari_ini . "'
                 LIMIT 1",
                "row"
            );

            $catatan_id = null;
            if (empty($cek)) {
                $insert_id = $this->mymodel->insertid('presensi_catatan_pelajaran', $data);
                if ($insert_id) {
                    $catatan_id = $insert_id;
                }
            } else {
                $data['updated_at'] = $tanggal_waktu;
                $update_ok = $this->mymodel->update(
                    'presensi_catatan_pelajaran',
                    $data,
                    'id_presensi_catatan',
                    $cek->id_presensi_catatan
                );
                if ($update_ok !== false && $update_ok >= 0) {
                    $catatan_id = $cek->id_presensi_catatan;
                }
            }

            if (empty($catatan_id)) {
                $data_dilewati[] = array(
                    'id_siswa_aktif' => $siswa_id,
                    'alasan'         => 'Gagal insert/update header',
                );
                continue;
            }

            // --- 6d. Insert/update detail (1 kategori per siswa, paralel index) ---
            if ($use_detail && isset($detail_payloads[$i])) {
                // Hapus detail lama (replace-all)
                $this->db->where('id_presensi_catatan', $catatan_id);
                $this->db->delete('presensi_catatan_detail');
                // Insert 1 detail baru
                $dp = $detail_payloads[$i];
                $row = array(
                    'id_presensi_catatan'      => $catatan_id,
                    'id_kategori_catatan'      => $dp['id_kategori_catatan'],
                    'jenis_pelanggaran_custom' => $dp['jenis_pelanggaran_custom'],
                    'skor'                     => $dp['skor'],
                    'keterangan'               => $dp['keterangan'],
                    'updated_by'               => $nama_guru,
                );
                $this->mymodel->insertid('presensi_catatan_detail', $row);
            }

            $data['id_presensi_catatan'] = $catatan_id;
            $data_berhasil[] = $data;
        }

        $this->db->trans_complete();

        // === 7. Cek hasil transaksi ===
        if ($this->db->trans_status() === FALSE) {
            $this->response(array(
                'status'  => 0,
                'message' => 'Transaksi gagal, semua perubahan di-rollback. Coba lagi.',
                'data'    => array(),
            ));
            return;
        }

        // === 8. Response sukses ===
        $this->response(array(
            'status'  => 1,
            'message' => 'Berhasil mencatat presensi per jam pelajaran!',
            'data'    => array(
                'success'         => $data_berhasil,
                'skipped'         => $data_dilewati,
                'success_count'   => count($data_berhasil),
                'skipped_count'   => count($data_dilewati),
                'hari'            => $hari_ini,
                'tanggal'         => $tanggal_hari_ini,
                'jam_ke'          => $jam_ke,
                'jenjang'         => $jenjang,
                'updated_by'      => $nama_guru,
                'id_tahun_ajaran' => $id_tahun_ajaran,
                'detail_count'    => count($detail_payloads),
            ),
        ));
    }
}
