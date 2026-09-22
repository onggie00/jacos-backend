<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API: Presensi_siswa_catatan_detail (v2 — lintas-jenjang by nisn, multi-detail)
 *
 * Tujuan  : Mengambil riwayat catatan presensi per-jam seorang siswa, mengikuti
 *           NISN (lintas jenjang SD→SMP→SMA) supaya catatan terbawa saat siswa
 *           naik/pindah jenjang. Tiap header membawa array `catatan_detail`
 *           (multi pelanggaran per jam).
 * Akses   : Guru (atau operator dengan token guru pada jenjang yang sama).
 * Tabel   : presensi_catatan_pelajaran (header) + presensi_catatan_detail + presensi_kategori_catatan
 *
 * Input POST:
 *   - jenjang          : 'sd' | 'smp' | 'sma' | 'ft'  (untuk validasi token)
 *   - id_siswa_aktif   : int single (resolve nisn dari siswa_<jenjang>_aktif + siswa_<jenjang>)
 *   - tahun_ajaran     : int opsional (id_tahun_ajaran). Kosong = semua tahun ajaran.
 *   - tanggal          : date (Y-m-d) opsional. Default = hari ini. Abaikan jika semua_tanggal=1.
 *   - semua_tanggal    : '1' opsional — kalau '1', filter tanggal diabaikan (tampilkan semua tanggal).
 *
 * Header:
 *   - x-token          : token login guru_<jenjang>
 *
 * Response sukses: daftar catatan diurutkan DESC (terbaru dulu):
 *   ORDER BY tanggal_waktu DESC, jam_ke DESC, id_presensi_catatan DESC
 *
 * Setiap row berisi field header + array `catatan_detail`:
 *   [{ id_catatan_detail, id_kategori_catatan, nama_kategori, is_custom,
 *      jenis_pelanggaran_custom, skor, keterangan, created_at, updated_at }, ...]
 */
class Presensi_siswa_catatan_detail extends REST_Controller {

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
        $id_siswa_raw   = $this->post('id_siswa_aktif');
        $tahun_raw      = $this->post('tahun_ajaran');
        $tanggal_raw    = $this->post('tanggal');
        $semua_tanggal  = $this->post('semua_tanggal');

        // --- 2a. Validasi jenjang ---
        if (empty($jenjang) || !in_array($jenjang, array('sd', 'smp', 'sma', 'ft'))) {
            $this->response(array(
                'status'  => 0,
                'message' => 'jenjang tidak valid. Pilihan: sd, smp, sma, ft',
                'data'    => array(),
            ));
            return;
        }

        // --- 2b. Validasi id_siswa_aktif (int single) ---
        if (!is_numeric($id_siswa_raw)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'id_siswa_aktif tidak valid. Harus berupa integer.',
                'data'    => array(),
            ));
            return;
        }
        $id_siswa_aktif = (int)$id_siswa_raw;

        // --- 2c. Validasi tahun_ajaran (opsional, int) ---
        $id_tahun_filter = null;
        if ($tahun_raw !== null && $tahun_raw !== '') {
            if (!is_numeric($tahun_raw)) {
                $this->response(array(
                    'status'  => 0,
                    'message' => 'tahun_ajaran tidak valid. Harus berupa integer (id_tahun_ajaran).',
                    'data'    => array(),
                ));
                return;
            }
            $id_tahun_filter = (int)$tahun_raw;
        }

        // --- 2d. Validasi tanggal (opsional, default hari ini; semua_tanggal=1 override) ---
        $tanggal_filter = null;
        $skip_tanggal   = ($semua_tanggal !== null && (string)$semua_tanggal === '1');
        if (!$skip_tanggal) {
            if (!empty($tanggal_raw)) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_raw)) {
                    $this->response(array(
                        'status'  => 0,
                        'message' => 'tanggal tidak valid. Format: Y-m-d',
                        'data'    => array(),
                    ));
                    return;
                }
                $tanggal_filter = $tanggal_raw;
            } else {
                $tanggal_filter = date('Y-m-d');
            }
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

        // === 4. Resolve nisn dari id_siswa_aktif + jenjang ===
        $siswa = $this->mymodel->withquery(
            "SELECT sa.nama_lengkap, sa.nis, sa.id_siswa_$jenjang, s.nisn
             FROM siswa_" . $jenjang . "_aktif sa
             LEFT JOIN siswa_" . $jenjang . " s ON sa.id_siswa_$jenjang = s.id_siswa_$jenjang
             WHERE sa.id_siswa_" . $jenjang . "_aktif = " . $id_siswa_aktif,
            "row"
        );
        if (empty($siswa)) {
            $this->response(array(
                'status'  => 0,
                'message' => 'Siswa tidak ditemukan di siswa_' . $jenjang . '_aktif',
                'data'    => array(),
            ));
            return;
        }
        $nisn_raw = isset($siswa->nisn) ? trim($siswa->nisn) : '';
        $nisn     = ($nisn_raw === '' || $nisn_raw === '-') ? null : $nisn_raw;
        $nis      = $siswa->nis;

        // === 5. Bangun WHERE clause ===
        // Strategi: pakai nisn kalau ada (lintas-jenjang), else fallback nis (jenjang sama saja).
        $where = " 1=1 ";
        if (!empty($nisn)) {
            $where .= " AND pcp.nisn = " . $this->db->escape($nisn);
        } elseif (!empty($nis)) {
            $where .= " AND pcp.nis = " . $this->db->escape($nis);
        } else {
            // Tidak ada nis & nisn — fallback ke id_siswa_aktif + jenjang (1 siswa saja)
            $where .= " AND pcp.id_siswa_aktif = " . $id_siswa_aktif . " AND pcp.jenjang = " . $this->db->escape($jenjang);
        }
        if (!empty($tanggal_filter)) {
            $where .= " AND DATE(pcp.tanggal_waktu) = " . $this->db->escape($tanggal_filter);
        }
        if (!empty($id_tahun_filter)) {
            $where .= " AND pcp.id_tahun_ajaran = " . (int)$id_tahun_filter;
        }

        // === 6. Query header (DESC) ===
        $catatan_list = $this->mymodel->withquery(
            "SELECT pcp.id_presensi_catatan, pcp.id_siswa_aktif, pcp.nama_lengkap,
                    pcp.nis, pcp.nisn, pcp.id_tahun_ajaran,
                    pcp.hari, pcp.tanggal_waktu, pcp.jenjang, pcp.status_hadir,
                    pcp.jam_ke,
                    pcp.created_at, pcp.updated_at, pcp.updated_by
             FROM presensi_catatan_pelajaran pcp
             WHERE $where
             ORDER BY pcp.tanggal_waktu DESC, pcp.jam_ke DESC, pcp.id_presensi_catatan DESC",
            "result"
        );

        if (empty($catatan_list)) {
            $this->response(array(
                'status'  => 1,
                'message' => 'Tidak ada catatan untuk siswa ini' . (!empty($tanggal_filter) ? ' pada tanggal ' . $tanggal_filter : '') . '.',
                'data'    => array(),
            ));
            return;
        }

        // === 7. Ambil semua detail untuk header yang ditemukan (1 query, group by header) ===
        $header_ids = array();
        foreach ($catatan_list as $row) {
            $header_ids[] = (int)$row->id_presensi_catatan;
        }
        $detail_map = array();
        if (count($header_ids) > 0) {
            $detail_rows = $this->mymodel->withquery(
                "SELECT pcd.id_catatan_detail, pcd.id_presensi_catatan, pcd.id_kategori_catatan,
                        kc.nama_kategori, kc.is_custom,
                        pcd.jenis_pelanggaran_custom, pcd.skor, pcd.keterangan,
                        pcd.created_at, pcd.updated_at, pcd.updated_by
                 FROM presensi_catatan_detail pcd
                 JOIN presensi_kategori_catatan kc ON pcd.id_kategori_catatan = kc.id_kategori_catatan
                 WHERE pcd.id_presensi_catatan IN (" . implode(',', $header_ids) . ")
                 ORDER BY pcd.id_catatan_detail ASC",
                "result"
            );
            if (!empty($detail_rows)) {
                foreach ($detail_rows as $d) {
                    $detail_map[(int)$d->id_presensi_catatan][] = array(
                        'id_catatan_detail'        => (int)$d->id_catatan_detail,
                        'id_kategori_catatan'      => (int)$d->id_kategori_catatan,
                        'nama_kategori'            => $d->nama_kategori,
                        'is_custom'                => (int)$d->is_custom,
                        'jenis_pelanggaran_custom' => $d->jenis_pelanggaran_custom,
                        'skor'                     => (int)$d->skor,
                        'keterangan'               => $d->keterangan,
                        'created_at'               => $d->created_at,
                        'updated_at'               => $d->updated_at,
                        'updated_by'               => $d->updated_by,
                    );
                }
            }
        }

        // === 8. Attach catatan_detail ke setiap header ===
        foreach ($catatan_list as $row) {
            $hid = (int)$row->id_presensi_catatan;
            $row->catatan_detail = isset($detail_map[$hid]) ? $detail_map[$hid] : array();
        }

        // === 9. Response sukses ===
        $this->response(array(
            'status'  => 1,
            'message' => 'Berhasil mengambil catatan',
            'data'    => $catatan_list,
        ));
    }
}
