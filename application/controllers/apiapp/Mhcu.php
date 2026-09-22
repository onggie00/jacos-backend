<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Mhcu extends REST_Controller {

    function __construct()
    {
        parent::__construct();

        // Fallback: parse JSON body untuk POST request (mobile app kirim Content-Type: application/json)
        // post() baca dari _post_args, bukan $_POST — jadi harus set _post_args langsung
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($this->post())) {
            $raw = json_decode($this->input->raw_input_stream, true);
            if (is_array($raw)) {
                $this->_post_args = $raw;
            }
        }
    }

    /**
     * GET periode_aktif
     */
    public function periode_aktif_get()
    {
        $token = $this->_get_token();

        $data = $this->mymodel->withquery(
            "SELECT * FROM mhcu_periode WHERE is_active = 1 ORDER BY id_mhcu_periode DESC LIMIT 1",
            "row"
        );

        if (!empty($data)) {
            $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
        } else {
            $msg = array('status' => 0, 'message' => 'Tidak ada periode aktif', 'data' => array());
        }

        $this->response($msg, 200);
    }

    /**
     * GET cek_sesi
     */
    public function cek_sesi_get()
    {
        $token = $this->_get_token();
        $npp = $this->get('npp');

        if (empty($npp)) {
            $this->response(array('status' => 0, 'message' => 'Parameter npp wajib diisi', 'data' => array()), 200);
            return;
        }

        $periode = $this->mymodel->withquery(
            "SELECT * FROM mhcu_periode WHERE is_active = 1 ORDER BY id_mhcu_periode DESC LIMIT 1",
            "row"
        );

        if (empty($periode)) {
            $this->response(array('status' => 0, 'message' => 'Tidak ada periode aktif', 'data' => array('status_sesi' => 'no_period')), 200);
            return;
        }

        $sesi = $this->mymodel->withquery(
            "SELECT * FROM mhcu_sesi WHERE npp = '" . $this->db->escape_str($npp) . "' AND id_mhcu_periode = " . intval($periode->id_mhcu_periode) . " AND deleted_at IS NULL LIMIT 1",
            "row"
        );

        if (empty($sesi)) {
            $data = array('status_sesi' => 'belum_mulai', 'periode' => $periode);
        } elseif ($sesi->status == 'selesai') {
            $data = array('status_sesi' => 'sudah_selesai', 'sesi' => $sesi, 'periode' => $periode);
        } else {
            // Lanjut_isi: hitung progress per instrument (derive) + tentukan next_instrument_id
            $target_role = $this->_resolve_target_role($sesi->presensi_role);

            $rows_total = $this->mymodel->withquery(
                "SELECT ii.id_instrument, COUNT(*) AS total_items FROM mhcu_instrument_item ii WHERE ii.deleted_at IS NULL GROUP BY ii.id_instrument",
                "result"
            );
            $total_items_map = array();
            foreach ($rows_total as $rt) $total_items_map[intval($rt->id_instrument)] = intval($rt->total_items);

            $rows_answered = $this->mymodel->withquery(
                "SELECT si.id_instrument, COUNT(DISTINCT si.id_instrument_item) AS answered_items FROM mhcu_sesi_instrument si WHERE si.id_sesi = " . intval($sesi->id_mhcu_sesi) . " GROUP BY si.id_instrument",
                "result"
            );
            $answered_map = array();
            foreach ($rows_answered as $ra) $answered_map[intval($ra->id_instrument)] = intval($ra->answered_items);

            // Ambil instrument list sesuai role (ikut logika instrument_get)
            $inst_rows = $this->mymodel->withquery(
                "SELECT id_instrument, no_urut, nama_instrument, kode_instrument FROM mhcu_instrument WHERE (target_role IN ('all', '" . $this->db->escape_str($target_role) . "') OR target_role IS NULL) ORDER BY no_urut ASC",
                "result"
            );

            $progress = array();
            $next_instrument_id = null;
            foreach ($inst_rows as $ir) {
                $iid = intval($ir->id_instrument);
                $total = isset($total_items_map[$iid]) ? $total_items_map[$iid] : 0;
                $answered = isset($answered_map[$iid]) ? $answered_map[$iid] : 0;
                $status = ($total > 0 && $answered >= $total) ? 'selesai' : 'belum';
                $progress[] = array(
                    'id_instrument' => $iid,
                    'no_urut' => intval($ir->no_urut),
                    'nama_instrument' => $ir->nama_instrument,
                    'kode_instrument' => $ir->kode_instrument,
                    'total_items' => $total,
                    'answered_items' => $answered,
                    'status' => $status,
                );
                // Instrument pertama yg belum selesai = next_instrument_id
                if ($status === 'belum' && $next_instrument_id === null) {
                    $next_instrument_id = $iid;
                }
            }

            $data = array(
                'status_sesi' => 'lanjut_isi',
                'sesi' => $sesi,
                'periode' => $periode,
                'progress' => $progress,
                'next_instrument_id' => $next_instrument_id,
            );
        }

        $this->response(array('status' => 1, 'message' => 'Berhasil', 'data' => $data), 200);
    }

    /**
     * POST mulai_sesi
     */
    public function mulai_sesi_post()
    {
        $token = $this->_get_token();
        $npp = $this->post('npp');
        $presensi_role = $this->post('presensi_role');
        $id_mhcu_periode = $this->post('id_mhcu_periode');

        if (empty($npp) || empty($presensi_role)) {
            $this->response(array('status' => 0, 'message' => 'Parameter npp dan presensi_role wajib diisi', 'data' => array()), 200);
            return;
        }

        // Ambil periode: gunakan id_mhcu_periode jika dikirim,否则 ambil periode aktif terbaru
        if (!empty($id_mhcu_periode)) {
            $periode = $this->mymodel->withquery(
                "SELECT * FROM mhcu_periode WHERE id_mhcu_periode = " . intval($id_mhcu_periode) . " LIMIT 1",
                "row"
            );
        } else {
            $periode = $this->mymodel->withquery(
                "SELECT * FROM mhcu_periode WHERE is_active = 1 ORDER BY id_mhcu_periode DESC LIMIT 1",
                "row"
            );
        }

        if (empty($periode)) {
            $this->response(array('status' => 0, 'message' => 'Tidak ada periode aktif', 'data' => array()), 200);
            return;
        }

        $existing = $this->mymodel->withquery(
            "SELECT * FROM mhcu_sesi WHERE npp = '" . $this->db->escape_str($npp) . "' AND id_mhcu_periode = " . intval($periode->id_mhcu_periode) . " AND deleted_at IS NULL LIMIT 1",
            "row"
        );

        if (!empty($existing)) {
            $this->response(array('status' => 1, 'message' => 'Sesi sudah ada', 'data' => $existing), 200);
            return;
        }

        // Ambil nama_tabel dari presensi_setting_role
        $role_row = $this->mymodel->withquery(
            "SELECT nama_tabel FROM presensi_setting_role WHERE nama_role = '" . $this->db->escape_str($presensi_role) . "' LIMIT 1",
            "row"
        );
        $nama_tabel = !empty($role_row->nama_tabel) ? $role_row->nama_tabel : '';

        // Ambil id_user dan nama_lengkap dari tabel referensi berdasarkan nama_tabel + npp
        $id_user = null;
        $nama_lengkap = '';
        if (!empty($nama_tabel)) {
            $pk_candidates = array(
                'guru_sd' => 'id_guru', 'guru_smp' => 'id_guru', 'guru_sma' => 'id_guru', 'guru_ft' => 'id_guru',
                'pegawai' => 'id_pegawai', 'pramubhakti' => 'id_pegawai',
                'pimpinan_sd' => 'id_pimpinan', 'pimpinan_smp' => 'id_pimpinan', 'pimpinan_sma' => 'id_pimpinan', 'pimpinan' => 'id_pimpinan',
                'security' => 'id_security',
            );
            $pk_col = isset($pk_candidates[$nama_tabel]) ? $pk_candidates[$nama_tabel] : '';
            if (!empty($pk_col)) {
                // ponytail: skema tabel role tidak seragam (kolom nama berbeda, deleted_at tidak semua ada) — susun query sesuai kolom yang ada
                $nama_col = '';
                if ($this->db->field_exists('nama_lengkap', $nama_tabel)) {
                    $nama_col = 'nama_lengkap';
                } elseif ($this->db->field_exists('nama_pimpinan', $nama_tabel)) {
                    $nama_col = 'nama_pimpinan AS nama_lengkap';
                }
                if (!empty($nama_col)) {
                    $user_row = $this->mymodel->withquery(
                        "SELECT " . $pk_col . " AS id_user, " . $nama_col . " FROM " . $nama_tabel . " WHERE npp = '" . $this->db->escape_str($npp) . "'"
                        . ($this->db->field_exists('deleted_at', $nama_tabel) ? " AND deleted_at IS NULL" : "")
                        . " LIMIT 1",
                        "row"
                    );
                    if (!empty($user_row)) {
                        $id_user = intval($user_row->id_user);
                        $nama_lengkap = trim($user_row->nama_lengkap);
                    }
                }
            }
        }

        if (empty($nama_lengkap)) {
            $this->response(array('status' => 0, 'message' => 'Data pengguna tidak ditemukan untuk NPP: ' . $npp, 'data' => array()), 200);
            return;
        }

        $data_insert = array(
            'npp' => $npp,
            'nama_lengkap' => $nama_lengkap,
            'presensi_role' => $presensi_role,
            'id_user' => $id_user,
            'nama_tabel' => $nama_tabel,
            'id_mhcu_periode' => $periode->id_mhcu_periode,
            'status' => 'belum_selesai',
            'submitted_at' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('mhcu_sesi', $data_insert);
        $id_sesi = $this->db->insert_id();

        if ($id_sesi) {
            $sesi = $this->mymodel->withquery("SELECT * FROM mhcu_sesi WHERE id_mhcu_sesi = " . intval($id_sesi), "row");
            $this->response(array('status' => 1, 'message' => 'Sesi berhasil dibuat', 'data' => $sesi), 200);
        } else {
            $this->response(array('status' => 0, 'message' => 'Gagal membuat sesi', 'data' => array()), 200);
        }
    }

    /**
     * GET demografi
     */
    public function demografi_get()
    {
        $token = $this->_get_token();

        $pertanyaan = $this->mymodel->withquery(
            "SELECT * FROM mhcu_demografi_pertanyaan ORDER BY no_urut ASC",
            "result"
        );

        if (!empty($pertanyaan)) {
            foreach ($pertanyaan as $p) {
                $p->options = $this->mymodel->withquery(
                    "SELECT * FROM mhcu_demografi_option WHERE id_demografi_pertanyaan = " . intval($p->id_demografi_pertanyaan) . " ORDER BY no_urut ASC",
                    "result"
                );
            }
            $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $pertanyaan);
        } else {
            $msg = array('status' => 0, 'message' => 'Belum ada pertanyaan demografi', 'data' => array());
        }

        $this->response($msg, 200);
    }

    /**
     * POST simpan_demografi
     */
    public function simpan_demografi_post()
    {
        $token = $this->_get_token();
        $id_sesi = $this->post('id_sesi');
        $jawaban = $this->post('jawaban');

        if (empty($id_sesi) || empty($jawaban)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_sesi dan jawaban wajib diisi', 'data' => array()), 200);
            return;
        }

        if (!is_array($jawaban)) {
            $this->response(array('status' => 0, 'message' => 'Jawaban harus berupa array', 'data' => array()), 200);
            return;
        }

        $saved = 0;
        foreach ($jawaban as $j) {
            $id_pertanyaan = isset($j['id_demografi_pertanyaan']) ? intval($j['id_demografi_pertanyaan']) : 0;
            $id_option = isset($j['id_demografi_option']) ? intval($j['id_demografi_option']) : 0;
            $text_value = isset($j['demografi_text_option']) ? $j['demografi_text_option'] : '';

            if ($id_pertanyaan == 0) continue;

            $existing = $this->mymodel->withquery(
                "SELECT id_sesi_demografi FROM mhcu_sesi_demografi WHERE id_sesi = " . intval($id_sesi) . " AND id_demografi_pertanyaan = " . $id_pertanyaan,
                "row"
            );

            if (!empty($existing)) {
                $this->db->where('id_sesi_demografi', $existing->id_sesi_demografi)->update('mhcu_sesi_demografi', array(
                    'id_demografi_option' => $id_option,
                    'demografi_text_option' => $text_value,
                ));
            } else {
                $this->db->insert('mhcu_sesi_demografi', array(
                    'id_sesi' => intval($id_sesi),
                    'id_demografi_pertanyaan' => $id_pertanyaan,
                    'id_demografi_option' => $id_option,
                    'demografi_text_option' => $text_value,
                ));
            }
            $saved++;
        }

        $this->response(array('status' => 1, 'message' => 'Berhasil simpan ' . $saved . ' jawaban demografi', 'data' => array('saved' => $saved)), 200);
    }

    /**
     * GET instrument
     * Filter by target_role based on session's presensi_role
     */
    public function instrument_get()
    {
        $token = $this->_get_token();
        $id_sesi = $this->get('id_sesi');

        // Resolve target_role from session
        // Default: tampilkan instrument yang applicable untuk siapa saja
        // ('all') + yang role-specific (guru_karyawan / pimpinan) ketika sesi ada.
        $allowed = array("'all'");
        if (!empty($id_sesi)) {
            $sesi = $this->mymodel->withquery(
                "SELECT presensi_role FROM mhcu_sesi WHERE id_mhcu_sesi = " . intval($id_sesi) . " AND deleted_at IS NULL",
                "row"
            );
            if (!empty($sesi)) {
                $resolved = $this->_resolve_target_role($sesi->presensi_role);
                $allowed[] = "'" . $this->db->escape_str($resolved) . "'";
            }
        }

        $instruments = $this->mymodel->withquery(
            "SELECT * FROM mhcu_instrument WHERE (target_role IN (" . implode(',', $allowed) . ") OR target_role IS NULL) ORDER BY no_urut ASC",
            "result"
        );

        if (!empty($instruments)) {
            // Hitung progress per instrument (derive: jumlah item yg punya jawaban di sesi ini)
            // Dipakai di app utk tahu instrument mana yg sudah 'selesai' & mana yg boleh di-edit (back).
            $progress_map = array();
            if (!empty($id_sesi)) {
                $rows_total = $this->mymodel->withquery(
                    "SELECT ii.id_instrument, COUNT(*) AS total_items FROM mhcu_instrument_item ii WHERE ii.deleted_at IS NULL GROUP BY ii.id_instrument",
                    "result"
                );
                $total_items_map = array();
                foreach ($rows_total as $rt) $total_items_map[intval($rt->id_instrument)] = intval($rt->total_items);

                $rows_answered = $this->mymodel->withquery(
                    "SELECT si.id_instrument, COUNT(DISTINCT si.id_instrument_item) AS answered_items FROM mhcu_sesi_instrument si WHERE si.id_sesi = " . intval($id_sesi) . " GROUP BY si.id_instrument",
                    "result"
                );
                $answered_map = array();
                foreach ($rows_answered as $ra) $answered_map[intval($ra->id_instrument)] = intval($ra->answered_items);

                foreach ($instruments as $inst) {
                    $total = isset($total_items_map[intval($inst->id_instrument)]) ? $total_items_map[intval($inst->id_instrument)] : 0;
                    $answered = isset($answered_map[intval($inst->id_instrument)]) ? $answered_map[intval($inst->id_instrument)] : 0;
                    $progress_map[intval($inst->id_instrument)] = array(
                        'total_items' => $total,
                        'answered_items' => $answered,
                        'status' => ($total > 0 && $answered >= $total) ? 'selesai' : 'belum',
                    );
                }
            }

            foreach ($instruments as $inst) {
                // Tambah judul_instrument = "Sesi {no_urut} - {nama_instrument}" (poin #4 + tambahan user)
                $inst->judul_instrument = 'Sesi ' . $inst->no_urut;
                // instruksi sudah ada di kolom tabel (poin #4). target_role sudah ada.
                if (isset($progress_map[intval($inst->id_instrument)])) {
                    $inst->progress = $progress_map[intval($inst->id_instrument)];
                }

                $inst->items = $this->mymodel->withquery(
                    "SELECT ii.*, ms.nama_skala FROM mhcu_instrument_item ii LEFT JOIN mhcu_skala ms ON ms.id_skala = ii.id_skala WHERE ii.id_instrument = " . intval($inst->id_instrument) . " AND ii.deleted_at IS NULL ORDER BY ii.no_urut_item ASC",
                    "result"
                );

                if (!empty($inst->items)) {
                    foreach ($inst->items as $item) {
                        // Pilihan skala untuk item ini (radio_single_choice / checkbox_multi_choice)
                        $item->skala_options = $this->mymodel->withquery(
                            "SELECT * FROM mhcu_skala_option WHERE id_skala = " . intval($item->id_skala) . " ORDER BY no_urut_option ASC",
                            "result"
                        );
                        // max_pilihan sudah ada di kolom. Tandai di item juga.
                        if (!isset($item->max_pilihan)) {
                            $item->max_pilihan = null;
                        }
                    }
                }
            }
            $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $instruments);
        } else {
            $msg = array('status' => 0, 'message' => 'Belum ada instrument', 'data' => array());
        }

        $this->response($msg, 200);
    }

    /**
     * POST simpan_jawaban_instrument
     * Catatan v3:
     * - Submit bisa per instrument (bukan harus semua sekaligus). Kontrak endpoint tdk berubah.
     * - Max_pilihan utk checkbox divalidasi backend (poin #9, jangan cuma percaya UI).
     * - Urutan: instrument harus berurutan. Submit ke instrument N ditolak kalau instrument (N-1) belum selesai.
     *   Back ke instrument yg sudah selesai tetap boleh (jawaban sebelumnya di-overwrite).
     */
    public function simpan_jawaban_instrument_post()
    {
        $token = $this->_get_token();
        $id_sesi = $this->post('id_sesi');
        $jawaban = $this->post('jawaban');

        if (empty($id_sesi) || empty($jawaban)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_sesi dan jawaban wajib diisi', 'data' => array()), 200);
            return;
        }

        if (!is_array($jawaban)) {
            $this->response(array('status' => 0, 'message' => 'Jawaban harus berupa array', 'data' => array()), 200);
            return;
        }

        // Validasi sesi
        $sesi = $this->mymodel->withquery(
            "SELECT * FROM mhcu_sesi WHERE id_mhcu_sesi = " . intval($id_sesi) . " AND deleted_at IS NULL",
            "row"
        );
        if (empty($sesi)) {
            $this->response(array('status' => 0, 'message' => 'Sesi tidak ditemukan', 'data' => array()), 200);
            return;
        }
        if ($sesi->status == 'selesai') {
            $this->response(array('status' => 0, 'message' => 'Sesi sudah selesai, jawaban tidak dapat diubah', 'data' => array()), 200);
            return;
        }

        // Tentukan himpunan id_instrument yg akan disubmit (unik) utk validasi urutan.
        $submit_instrument_ids = array();
        foreach ($jawaban as $j) {
            if (isset($j['id_instrument']) && intval($j['id_instrument']) > 0) {
                $submit_instrument_ids[intval($j['id_instrument'])] = true;
            }
        }
        $submit_instrument_ids = array_keys($submit_instrument_ids);

        // Validasi urutan: instrument harus berurutan no_urut ascending sesuai role.
        // Aturan: kalau submit ke instrument dgn no_urut = N, maka SEMUA instrument dgn no_urut < N
        // untuk role ini harus sudah status 'selesai'.
        if (!empty($submit_instrument_ids)) {
            $target_role = $this->_resolve_target_role($sesi->presensi_role);
            $inst_list = $this->mymodel->withquery(
                "SELECT id_instrument, no_urut, kode_instrument, nama_instrument FROM mhcu_instrument WHERE (target_role IN ('all', '" . $this->db->escape_str($target_role) . "') OR target_role IS NULL) ORDER BY no_urut ASC",
                "result"
            );
            $inst_by_id = array();
            foreach ($inst_list as $ir) $inst_by_id[intval($ir->id_instrument)] = $ir;

            // Hitung answered_items per instrument utk sesi ini.
            $ans_rows = $this->mymodel->withquery(
                "SELECT si.id_instrument, COUNT(DISTINCT si.id_instrument_item) AS answered_items FROM mhcu_sesi_instrument si WHERE si.id_sesi = " . intval($id_sesi) . " GROUP BY si.id_instrument",
                "result"
            );
            $ans_map = array();
            foreach ($ans_rows as $ar) $ans_map[intval($ar->id_instrument)] = intval($ar->answered_items);

            $total_rows = $this->mymodel->withquery(
                "SELECT id_instrument, COUNT(*) AS total_items FROM mhcu_instrument_item WHERE deleted_at IS NULL GROUP BY id_instrument",
                "result"
            );
            $total_map = array();
            foreach ($total_rows as $tr) $total_map[intval($tr->id_instrument)] = intval($tr->total_items);

            // Cek utk setiap instrument yg disubmit, no_urut terkecilnya.
            // (Kalau app submit beberapa instrument sekaligus, yg di-lock instrument terkecil.)
            $min_no_urut = null;
            foreach ($submit_instrument_ids as $sid) {
                if (!isset($inst_by_id[$sid])) continue; // skip kalau id ga valid (akan kena error di loop utama)
                if ($min_no_urut === null || intval($inst_by_id[$sid]->no_urut) < $min_no_urut) {
                    $min_no_urut = intval($inst_by_id[$sid]->no_urut);
                }
            }

            // Pastikan semua instrument dgn no_urut < min_no_urut berstatus 'selesai'.
            if ($min_no_urut !== null && $min_no_urut > 1) {
                foreach ($inst_list as $ir) {
                    $ord = intval($ir->no_urut);
                    if ($ord >= $min_no_urut) break;
                    $ans = isset($ans_map[intval($ir->id_instrument)]) ? $ans_map[intval($ir->id_instrument)] : 0;
                    $tot = isset($total_map[intval($ir->id_instrument)]) ? $total_map[intval($ir->id_instrument)] : 0;
                    if ($tot === 0 || $ans < $tot) {
                        $this->response(array(
                            'status' => 0,
                            'message' => 'Instrument sebelumnya (Sesi ' . $ord . ' - ' . $ir->nama_instrument . ') belum selesai. Mohon selesaikan terlebih dahulu.',
                            'data' => array('blocked_instrument_id' => intval($ir->id_instrument)),
                        ), 200);
                        return;
                    }
                }
            }
        }

        $saved = 0;
        $skipped = 0;
        $max_pilihan_errors = array();

        foreach ($jawaban as $j) {
            $id_instrument_item = isset($j['id_instrument_item']) ? intval($j['id_instrument_item']) : 0;
            $id_instrument = isset($j['id_instrument']) ? intval($j['id_instrument']) : 0;
            $id_skala_option = isset($j['id_skala_option']) ? intval($j['id_skala_option']) : 0;
            $text_value = isset($j['text_value']) ? $j['text_value'] : '';

            if ($id_instrument_item == 0) { $skipped++; continue; }

            // Ambil id_skala dari instrument_item jika tidak dikirim client
            $id_skala = isset($j['id_skala']) ? intval($j['id_skala']) : 0;
            $item_row = null;
            if ($id_skala == 0 && $id_instrument_item > 0) {
                $item_row = $this->mymodel->withquery(
                    "SELECT id_skala, max_pilihan, input_type FROM mhcu_instrument_item WHERE id_instrument_item = " . $id_instrument_item,
                    "row"
                );
                if (!empty($item_row)) {
                    $id_skala = intval($item_row->id_skala);
                }
            }

            // Validasi max_pilihan: utk checkbox_multi_choice, kalau max_pilihan NOT NULL, tolak jika
            // jumlah DISTINCT id_skala_option setelah save > max_pilihan.
            // Logic: payload akan overwrite existing row (upsert by id_sesi+id_instrument_item),
            // jadi hitung distinct option dari merge(existing, payload), bukan jumlah row mentah.
            if ($id_skala_option > 0) {
                if (empty($item_row)) {
                    $item_row = $this->mymodel->withquery(
                        "SELECT max_pilihan, input_type FROM mhcu_instrument_item WHERE id_instrument_item = " . $id_instrument_item,
                        "row"
                    );
                }
                if (!empty($item_row) && $item_row->input_type === 'checkbox_multi_choice' && $item_row->max_pilihan !== null) {
                    // Ambil distinct id_skala_option yg SUDAH ada di DB untuk item ini
                    $existing_opts_rows = $this->mymodel->withquery(
                        "SELECT DISTINCT id_skala_option FROM mhcu_sesi_instrument WHERE id_sesi = " . intval($id_sesi) . " AND id_instrument_item = " . $id_instrument_item . " AND id_skala_option > 0",
                        "result"
                    );
                    $existing_opts = array();
                    if (!empty($existing_opts_rows)) {
                        foreach ($existing_opts_rows as $er) $existing_opts[intval($er->id_skala_option)] = true;
                    }
                    // Hitung distinct options dari payload untuk item ini
                    $payload_opts = array();
                    foreach ($jawaban as $jj) {
                        if (isset($jj['id_instrument_item']) && intval($jj['id_instrument_item']) === $id_instrument_item
                            && isset($jj['id_skala_option']) && intval($jj['id_skala_option']) > 0) {
                            $payload_opts[intval($jj['id_skala_option'])] = true;
                        }
                    }
                    // Merge: final = existing options yg TIDAK di-overwrite oleh payload + payload options
                    $final_opts = $existing_opts;
                    foreach (array_keys($payload_opts) as $po) $final_opts[$po] = true;
                    $total = count($final_opts);
                    if ($total > intval($item_row->max_pilihan)) {
                        $max_pilihan_errors[] = array(
                            'id_instrument_item' => $id_instrument_item,
                            'max_pilihan' => intval($item_row->max_pilihan),
                            'attempted' => $total,
                        );
                        $skipped++;
                        continue;
                    }
                }
            }

            $existing = $this->mymodel->withquery(
                "SELECT id_sesi_instrument FROM mhcu_sesi_instrument WHERE id_sesi = " . intval($id_sesi) . " AND id_instrument_item = " . $id_instrument_item,
                "row"
            );

            if (!empty($existing)) {
                $this->db->where('id_sesi_instrument', $existing->id_sesi_instrument)->update('mhcu_sesi_instrument', array(
                    'id_instrument' => $id_instrument,
                    'id_skala' => $id_skala,
                    'id_skala_option' => $id_skala_option,
                    'text_value' => $text_value,
                ));
            } else {
                $this->db->insert('mhcu_sesi_instrument', array(
                    'id_sesi' => intval($id_sesi),
                    'id_instrument' => $id_instrument,
                    'id_instrument_item' => $id_instrument_item,
                    'id_skala' => $id_skala,
                    'id_skala_option' => $id_skala_option,
                    'text_value' => $text_value,
                ));
            }
            $saved++;
        }

        if (!empty($max_pilihan_errors)) {
            $this->response(array(
                'status' => 0,
                'message' => 'Sebagian pilihan melebihi batas maksimal',
                'data' => array('saved' => $saved, 'skipped' => $skipped, 'errors' => $max_pilihan_errors),
            ), 200);
            return;
        }

        $this->response(array('status' => 1, 'message' => 'Berhasil simpan ' . $saved . ' jawaban', 'data' => array('saved' => $saved, 'skipped' => $skipped)), 200);
    }

    /**
     * POST selesai_sesi
     */
    public function selesai_sesi_post()
    {
        $token = $this->_get_token();
        $id_sesi = $this->post('id_sesi');

        if (empty($id_sesi)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_sesi wajib diisi', 'data' => array()), 200);
            return;
        }

        $sesi = $this->mymodel->withquery("SELECT * FROM mhcu_sesi WHERE id_mhcu_sesi = " . intval($id_sesi) . " AND deleted_at IS NULL", "row");

        if (empty($sesi)) {
            $this->response(array('status' => 0, 'message' => 'Sesi tidak ditemukan', 'data' => array()), 200);
            return;
        }

        if ($sesi->status == 'selesai') {
            $this->response(array('status' => 0, 'message' => 'Sesi sudah selesai sebelumnya', 'data' => array()), 200);
            return;
        }

        // Validate all required items answered (role-aware)
        $target_role = $this->_resolve_target_role($sesi->presensi_role);
        $instruments = $this->mymodel->withquery(
            "SELECT * FROM mhcu_instrument WHERE (target_role IN ('all', '" . $this->db->escape_str($target_role) . "') OR target_role IS NULL) AND kode_instrument NOT LIKE 'TAMBAHAN%' ORDER BY no_urut ASC",
            "result"
        );
        $all_answered = true;
        $missing = array();

        foreach ($instruments as $inst) {
            $items = $this->mymodel->withquery(
                "SELECT id_instrument_item FROM mhcu_instrument_item WHERE id_instrument = " . intval($inst->id_instrument) . " AND deleted_at IS NULL",
                "result"
            );
            foreach ($items as $item) {
                $answered = $this->mymodel->withquery(
                    "SELECT id_sesi_instrument FROM mhcu_sesi_instrument WHERE id_sesi = " . intval($id_sesi) . " AND id_instrument_item = " . intval($item->id_instrument_item),
                    "row"
                );
                if (empty($answered)) {
                    $all_answered = false;
                    $missing[] = $inst->kode_instrument . '#' . $item->id_instrument_item;
                }
            }
        }

        if (!$all_answered) {
            $this->response(array('status' => 0, 'message' => 'Masih ada item yang belum dijawab', 'data' => array('missing_count' => count($missing), 'missing' => array_slice($missing, 0, 10))), 200);
            return;
        }

        // Run scoring
        $this->load->library('Mhcu_scoring');
        $scoring_result = $this->mhcu_scoring->hitung(intval($id_sesi));

        if (!$scoring_result['success']) {
            $this->response(array('status' => 0, 'message' => 'Gagal scoring: ' . $scoring_result['message'], 'data' => array()), 200);
            return;
        }

        // Update session status
        $this->db->where('id_mhcu_sesi', intval($id_sesi))->update('mhcu_sesi', array('status' => 'selesai'));

        $this->response(array('status' => 1, 'message' => 'Sesi selesai', 'data' => $scoring_result['data']), 200);
    }

    /**
     * GET hasil
     */
    public function hasil_get()
    {
        $token = $this->_get_token();
        $id_sesi = $this->get('id_sesi');

        if (empty($id_sesi)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_sesi wajib diisi', 'data' => array()), 200);
            return;
        }

        $sesi = $this->mymodel->withquery("SELECT * FROM mhcu_sesi WHERE id_mhcu_sesi = " . intval($id_sesi) . " AND deleted_at IS NULL", "row");

        if (empty($sesi)) {
            $this->response(array('status' => 0, 'message' => 'Sesi tidak ditemukan', 'data' => array()), 200);
            return;
        }

        // Cek tanggal_publish dari periode
        $periode = $this->mymodel->withquery("SELECT tanggal_publish FROM mhcu_periode WHERE id_mhcu_periode = " . intval($sesi->id_mhcu_periode), "row");
        $tanggal_publish = (!empty($periode) && !empty($periode->tanggal_publish)) ? $periode->tanggal_publish : null;
        $now = date('Y-m-d');
        $is_published = empty($tanggal_publish) || $now >= $tanggal_publish;

        if (!$is_published) {
            $this->response(array(
                'status' => 1,
                'message' => 'Hasil belum dipublish',
                'data' => array(
                    'sesi' => $sesi,
                    'hasil' => null,
                    'scores' => array(),
                    'is_published' => false,
                    'tanggal_publish' => $tanggal_publish,
                    'hasil_individu' => null,
                )
            ), 200);
            return;
        }

        $hasil = $this->mymodel->withquery("SELECT hi.*, pk.label_profil AS kategori_keseluruhan, pk.narasi_kategori, pk.saran_kategori AS saran_rekomendasi, pk.warna AS kategori_warna, pk.emoji AS kategori_emoji FROM mhcu_hasil_individu hi LEFT JOIN mhcu_profil_kategori pk ON pk.id_profil_kategori = hi.id_profil_kategori WHERE hi.id_sesi = " . intval($id_sesi), "row");

        $scores = $this->mymodel->withquery(
            "SELECT mis.*, mi.nama_instrument, mi.kode_instrument FROM mhcu_instrument_score mis LEFT JOIN mhcu_instrument mi ON mi.id_instrument = mis.id_instrument WHERE mis.id_sesi = " . intval($id_sesi) . " ORDER BY mi.no_urut ASC, mis.id_instrument_score ASC",
            "result"
        );

        // Tambah icon & warna kompatibel (tanpa emoji)
        if (!empty($hasil) && !empty($hasil->kategori_keseluruhan)) {
            $icon_map = array(
                'Immediate Professional Follow-up' => array('icon' => '!!!', 'profil_warna' => '#C00000'),
                'High Occupational Risk'           => array('icon' => '!!',  'profil_warna' => '#BD5163'),
                'Burnout with emotional distress'  => array('icon' => '!',   'profil_warna' => '#806000'),
                'Emotional Distress'               => array('icon' => '!',   'profil_warna' => '#F4B083'),
                'Burnout Dominant'                 => array('icon' => '!',   'profil_warna' => '#C45911'),
                'Needs Monitoring'                 => array('icon' => '~',   'profil_warna' => '#FFC000'),
                'Workplace Support Needed'         => array('icon' => '~',   'profil_warna' => '#E2EFD9'),
                'Healthy but Fatigued'             => array('icon' => '-',   'profil_warna' => '#A8D08D'),
                'Optimal Wellbeing'                => array('icon' => '+',   'profil_warna' => '#538135'),
            );
            $matched = false;
            foreach ($icon_map as $label => $info) {
                if (strpos($hasil->kategori_keseluruhan, $label) !== false) {
                    $hasil->kategori_icon = $info['icon'];
                    $hasil->profil_warna = $info['profil_warna'];
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $hasil->kategori_icon = '?';
                $hasil->profil_warna = '#F0AD4E';
            }
        }

        // URL hasil individu PDF (dengan token deterministik agar bisa diakses browser — sama dengan Mhcu_care)
        $pdf_token = md5(intval($id_sesi) . MHCU_CARE_TOKEN_SALT);
        $hasil_individu_url = "https://admin.labschoolcibubur.sch.id/"."apiapp/mhcu/export_individu_pdf?id_sesi=" . intval($id_sesi) . '&token=' . urlencode($pdf_token);//base_url('apiapp/mhcu/export_individu_pdf?id_sesi=' . intval($id_sesi) . '&token=' . urlencode($pdf_token));
        $hasil->hasil_individu = $hasil_individu_url;
        $data = array(
            'sesi' => $sesi,
            'hasil' => $hasil,
            'scores' => $scores,
            'is_published' => true,
            'tanggal_publish' => $tanggal_publish,
            // 'hasil_individu' => $hasil_individu_url,
        );

        $this->response(array('status' => 1, 'message' => 'Berhasil', 'data' => $data), 200);
    }

    /**
     * GET riwayat
     * Modul admin (mhcu_sesi) pakai endpoint ini untuk lihat skor/kategori.
     * App sisi peserta tidak boleh lihat hasil -> kalau view=app, field skor disembunyikan
     * dan daftar sesi tetap dikembalikan (tanggal, periode, status) sesuai poin #6.
     */
    public function riwayat_get()
    {
        $token = $this->_get_token();
        $npp = $this->get('npp');
        $view = $this->get('view'); // 'app' = peserta (tanpa skor), default = admin (dengan skor)

        if (empty($npp)) {
            $this->response(array('status' => 0, 'message' => 'Parameter npp wajib diisi', 'data' => array()), 200);
            return;
        }

        $include_kategori = ($view !== 'app');

        $select_kategori = $include_kategori
            ? "pk.label_profil AS kategori_keseluruhan, pk.warna AS kategori_warna, hi.is_krisis"
            : "NULL AS kategori_keseluruhan, NULL AS kategori_warna, NULL AS is_krisis";

        $riwayat = $this->mymodel->withquery(
            "SELECT s.*, p.nama_periode, p.tanggal_mulai, p.tanggal_selesai, p.tanggal_publish, " . $select_kategori . " FROM mhcu_sesi s LEFT JOIN mhcu_periode p ON p.id_mhcu_periode = s.id_mhcu_periode LEFT JOIN mhcu_hasil_individu hi ON hi.id_sesi = s.id_mhcu_sesi LEFT JOIN mhcu_profil_kategori pk ON pk.id_profil_kategori = hi.id_profil_kategori WHERE s.npp = '" . $this->db->escape_str($npp) . "' AND s.deleted_at IS NULL ORDER BY s.id_mhcu_sesi DESC",
            "result"
        );

        $now = date('Y-m-d');

        if (!empty($riwayat)) {
            foreach ($riwayat as &$item) {
                // Cek tanggal_publish per item
                $tp = !empty($item->tanggal_publish) ? $item->tanggal_publish : null;
                $item->is_published = empty($tp) || $now >= $tp;

                $item->kategori_icon = null;
                $item->profil_warna = null;
                $item->hasil_individu = null;

                // Jika belum publish, sembunyikan kategori/skor
                if (!$item->is_published) {
                    $item->kategori_keseluruhan = null;
                    $item->is_krisis = null;
                } elseif ($include_kategori && !empty($item->kategori_keseluruhan)) {
                    $icon_map = array(
                        'Immediate Professional Follow-up' => array('icon' => '!!!', 'profil_warna' => '#C00000'),
                        'High Occupational Risk'           => array('icon' => '!!',  'profil_warna' => '#BD5163'),
                        'Burnout with emotional distress'  => array('icon' => '!',   'profil_warna' => '#806000'),
                        'Emotional Distress'               => array('icon' => '!',   'profil_warna' => '#F4B083'),
                        'Burnout Dominant'                 => array('icon' => '!',   'profil_warna' => '#C45911'),
                        'Needs Monitoring'                 => array('icon' => '~',   'profil_warna' => '#FFC000'),
                        'Workplace Support Needed'         => array('icon' => '~',   'profil_warna' => '#E2EFD9'),
                        'Healthy but Fatigued'             => array('icon' => '-',   'profil_warna' => '#A8D08D'),
                        'Optimal Wellbeing'                => array('icon' => '+',   'profil_warna' => '#538135'),
                    );
                    $item->kategori_icon = '?';
                    $item->profil_warna = '#F0AD4E';
                    foreach ($icon_map as $label => $info) {
                        if (strpos($item->kategori_keseluruhan, $label) !== false) {
                            $item->kategori_icon = $info['icon'];
                            $item->profil_warna = $info['profil_warna'];
                            break;
                        }
                    }
                }

                // Tambah URL hasil individu jika sudah publish
                if ($item->is_published && !empty($item->status) && $item->status === 'selesai') {
                    $pdf_token = md5(intval($item->id_mhcu_sesi) . MHCU_CARE_TOKEN_SALT);
                    $item->hasil_individu = base_url('apiapp/mhcu/export_individu_pdf?id_sesi=' . intval($item->id_mhcu_sesi) . '&token=' . urlencode($pdf_token));
                }
            }
            unset($item);
            $msg = array('status' => 1, 'message' => 'Berhasil', 'data' => $riwayat);
        } else {
            $msg = array('status' => 0, 'message' => 'Belum ada riwayat', 'data' => array());
        }

        $this->response($msg, 200);
    }

    /**
     * GET export_individu_pdf
     * Generate PDF hasil individu menggunakan template_mhcu_view.php
     */
    public function export_individu_pdf_get()
    {
        $id_sesi = $this->get('id_sesi');
        $token   = (string) $this->get('token');

        // Validasi token deterministik (sama dengan Mhcu_care::_resolve)
        if ($id_sesi <= 0 || $token === '' || !hash_equals(md5(intval($id_sesi) . MHCU_CARE_TOKEN_SALT), $token)) {
            $this->response(array('status' => 0, 'message' => 'Token tidak valid', 'data' => array()), 200);
            return;
        }

        if (empty($id_sesi)) {
            $this->response(array('status' => 0, 'message' => 'Parameter id_sesi wajib diisi', 'data' => array()), 200);
            return;
        }

        $sesi = $this->mymodel->withquery("SELECT * FROM mhcu_sesi WHERE id_mhcu_sesi = " . intval($id_sesi) . " AND deleted_at IS NULL", "row");

        if (empty($sesi)) {
            $this->response(array('status' => 0, 'message' => 'Sesi tidak ditemukan', 'data' => array()), 200);
            return;
        }

        // Cek tanggal_publish
        $periode = $this->mymodel->withquery("SELECT * FROM mhcu_periode WHERE id_mhcu_periode = " . intval($sesi->id_mhcu_periode), "row");
        $tanggal_publish = (!empty($periode) && !empty($periode->tanggal_publish)) ? $periode->tanggal_publish : null;
        $now = date('Y-m-d');
        $is_published = empty($tanggal_publish) || $now >= $tanggal_publish;

        if (!$is_published) {
            $this->response(array('status' => 0, 'message' => 'Hasil belum dipublish', 'data' => array('tanggal_publish' => $tanggal_publish)), 200);
            return;
        }

        // Build PDF data (reuse logic dari Mhcu_sesi::_build_pdf_data)
        $data = $this->_build_pdf_data($id_sesi);
        if (empty($data)) {
            $this->response(array('status' => 0, 'message' => 'Gagal membangun data PDF', 'data' => array()), 200);
            return;
        }

        // Generate PDF
        require_once(APPPATH . 'libraries/htmlpdf/html2pdf.class.php');
        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        $this->load->view('template_mhcu_view', $data);
        $html = ob_get_contents();
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = 'MHCU_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['nama']) . '.pdf';
        $pdf->Output($nama_file, 'I');
    }

    /**
     * Helper: build data untuk PDF hasil individu
     */
    private function _build_pdf_data($id_sesi)
    {
        $sesi = $this->db->where('id_mhcu_sesi', $id_sesi)->get('mhcu_sesi')->row();
        if (empty($sesi)) return null;

        $periode = $this->db->where('id_mhcu_periode', $sesi->id_mhcu_periode)->get('mhcu_periode')->row();
        $nama_periode = ($periode && !empty($periode->nama_periode)) ? $periode->nama_periode : '';

        $hasil = $this->db->select('mhcu_hasil_individu.*, pk.label_profil AS kategori_keseluruhan, pk.narasi_kategori, pk.saran_kategori AS saran_rekomendasi, pk.warna AS kategori_warna, pk.emoji AS kategori_emoji')
            ->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT')
            ->where('mhcu_hasil_individu.id_sesi', $id_sesi)->get('mhcu_hasil_individu')->row();
        $scores = $this->db
            ->select('mhcu_instrument_score.*, mhcu_instrument.nama_instrument, mhcu_instrument.kode_instrument')
            ->join('mhcu_instrument', 'mhcu_instrument.id_instrument = mhcu_instrument_score.id_instrument', 'LEFT')
            ->where('mhcu_instrument_score.id_sesi', $id_sesi)
            ->order_by('mhcu_instrument.no_urut', 'ASC')
            ->get('mhcu_instrument_score')->result();

        $info = $this->_get_unit_jabatan($sesi->npp, $sesi->presensi_role);

        $tanggal = '-';
        if (!empty($sesi->submitted_at)) {
            $tanggal = formatTanggal($sesi->submitted_at);
        }

        // Ambil gender dari mhcu_sesi_demografi (id_demografi_pertanyaan = 2)
        $gender_row = $this->db->query(
            "SELECT do.label_option
             FROM mhcu_sesi_demografi sd
             LEFT JOIN mhcu_demografi_option do ON do.id_demografi_option = sd.id_demografi_option
             WHERE sd.id_sesi = " . intval($id_sesi) . "
             AND sd.id_demografi_pertanyaan = 2
             LIMIT 1"
        )->row();
        // gender kosong (demografi tidak ditemukan) → '' → sapaan netral "Bapak/Ibu" via mhcu_sapaan()
        $gender = (!empty($gender_row) && strtolower(trim($gender_row->label_option)) === 'perempuan') ? 'P' : ((!empty($gender_row) && trim($gender_row->label_option) !== '') ? 'L' : '');

        // Jabatan dari mhcu_sesi_demografi (id_demografi_pertanyaan = 5)
        $jabatan_row = $this->db->query(
            "SELECT do.label_option
             FROM mhcu_sesi_demografi sd
             LEFT JOIN mhcu_demografi_option do ON do.id_demografi_option = sd.id_demografi_option
             WHERE sd.id_sesi = " . intval($id_sesi) . "
             AND sd.id_demografi_pertanyaan = 5
             LIMIT 1"
        )->row();
        $jabatan = ($jabatan_row && !empty($jabatan_row->label_option)) ? $jabatan_row->label_option : (!empty($info['jabatan']) ? $info['jabatan'] : '-');

        // Extract nama depan
        $nama_depan = mhcu_extract_nama_depan($sesi->nama_lengkap);

        // Index scores by dimensi_aspek
        $skor_map = array();
        foreach ($scores as $sc) {
            $skor_map[$sc->dimensi_aspek] = $sc;
        }

        // Mapping profil_mental: lookup warna & deskripsi dari mhcu_band_kategori
        $profil_map = array(
            'Kesejahteraan Psikologis' => 'Mental Wellbeing',
            'Depresi' => 'Depression Risk',
            'Kecemasan' => 'Anxiety Risk',
            'CBI Keseluruhan' => 'Burnout Risk',
        );
        $inst_map = array(
            'Mental Wellbeing' => 'WHO5',
            'Depression Risk' => 'PHQ9',
            'Anxiety Risk' => 'GAD7',
            'Burnout Risk' => 'CBI',
        );

        $profil_mental = array();
        foreach ($profil_map as $dimensi => $label) {
            $sc = isset($skor_map[$dimensi]) ? $skor_map[$dimensi] : null;
            $kategori = $sc ? $sc->kategori : '-';
            $skor_val = $sc ? floatval($sc->skor) : 0;
            $kode = isset($inst_map[$label]) ? $inst_map[$label] : '';
            $band = !empty($kode) ? $this->_lookup_band_kategori($kode, null, $skor_val) : null;
            $sw = $band ? $band->warna : 'hijau';
            $deskripsi = $band ? $band->deskripsi : $kategori;
            $profil_mental[] = array(
                'aspek' => $label,
                'status_warna' => $sw,
                'kategori' => $kategori,
                'deskripsi' => $deskripsi,
            );
        }

        // Psikososial: role-aware, dengan skor/skor_maks/persentase + deskripsi dari band_kategori
        $target_role = $this->_resolve_target_role($sesi->presensi_role);

        if ($target_role == 'pimpinan') {
            $psiko_aspek = array('Leadership Capacity & Workload', 'Organizational support', 'Psychological safety', 'Work life balance', 'Leadership Meaning');
            $kode_psiko = 'PSIKOSOSIAL_PIMPINAN';
        } else {
            $psiko_aspek = array('Job Demand', 'Meaning & Engagement', 'Leadership Support', 'Team Support', 'Psychological Safety', 'Resources & Role Clarity');
            $kode_psiko = 'PSIKOSOSIAL';
        }

        $psikososial = array();
        foreach ($psiko_aspek as $asp) {
            $sc = isset($skor_map[$asp]) ? $skor_map[$asp] : null;
            $kategori = $sc ? $sc->kategori : '-';
            $skor_val = $sc ? floatval($sc->skor) : 0;
            // Cari max dari band_kategori
            $max_row = $this->db->query(
                "SELECT MAX(batas_atas) AS skor_maks FROM mhcu_band_kategori
                 WHERE kode_instrument = '" . $this->db->escape_str($kode_psiko) . "'
                 AND dimensi_aspek = '" . $this->db->escape_str($asp) . "'"
            )->row();
            $skor_maks = ($max_row && $max_row->skor_maks) ? floatval($max_row->skor_maks) : 12;
            $pct = ($skor_maks > 0) ? round($skor_val / $skor_maks * 100, 1) : 0;
            $band = $this->_lookup_band_kategori($kode_psiko, $asp, $skor_val);
            $sw = $band ? $band->warna : 'hijau';
            $deskripsi = $band ? $band->deskripsi : '';
            $psikososial[] = array(
                'aspek' => $asp,
                'status_warna' => $sw,
                'kategori' => $kategori,
                'deskripsi' => $deskripsi,
                'skor' => $skor_val,
                'skor_maks' => $skor_maks,
                'persentase' => $pct,
            );
        }

        $profil_label_map = array(
            'Optimal Wellbeing' => 'Optimal Wellbeing',
            'Healthy but Fatigued' => 'Healthy but Fatigued',
            'Workplace Support Needed' => 'Workplace Support Needed',
            'Needs Monitoring' => 'Needs Monitoring',
            'Burnout with emotional distress' => 'Burnout with emotional distress',
            'Burnout Dominant' => 'Burnout Dominant',
            'Emotional Distress' => 'Emotional Distress',
            'High Occupational Risk' => 'High Occupational Risk',
            'Immediate Professional Follow-up' => 'Immediate Professional Follow-up',
        );

        $kategori_kes = ($hasil && !empty($hasil->kategori_keseluruhan)) ? $hasil->kategori_keseluruhan : '';
        $saran = ($hasil && !empty($hasil->saran_rekomendasi)) ? $hasil->saran_rekomendasi : '';

        $kes_emoji = '';
        foreach ($profil_label_map as $full_name => $label) {
            if ($kategori_kes === $full_name || strpos($kategori_kes, $label) !== false) {
                $kes_emoji = mb_substr($full_name, 0, 1);
                break;
            }
        }

        // Sumber kebenaran: $hasil->kategori_warna (kode dr DB mhcu_profil_kategori.warna).
        // Lookup via profil_label_map sebelumnya brittle — kalau label drift/miss → fallback kuning/abu-abu.
        $kes_warna = ($hasil && !empty($hasil->kategori_warna)) ? $hasil->kategori_warna : 'kuning';
        $kes_label = $kategori_kes;

        $warna_hex = array(
            'hijau_tua'   => '#538135',
            'hijau_muda'  => '#A8D08D',
            'hijau_pudar' => '#E2EFD9',
            'kuning'      => '#FFC000',
            'coklat_orange' => '#806000',
            'orange_tua'  => '#C45911',
            'orange_muda' => '#F4B083',
            'merah_muda'  => '#BD5163',
            'merah_tua'   => '#C00000',
        );

        $hex = isset($warna_hex[$kes_warna]) ? $warna_hex[$kes_warna] : '#999999';

        $narasi = ($hasil && !empty($hasil->narasi_kategori)) ? $hasil->narasi_kategori : $saran;
        $kesimpulan = array(
            'warna' => $kes_warna,
            'profil_warna' => $hex,
            'label' => $kes_label,
            'deskripsi_profil' => $narasi,
            'rekomendasi' => $saran,
        );

        // Tampilkan link MHCU Care hanya untuk profil yang butuh tindak lanjut
        $care_profiles = array(
            'Immediate Professional Follow-up',
            'Burnout Dominant',
            'Burnout with emotional distress',
            'Emotional Distress',
        );
        $show_mhcu_care = in_array($kategori_kes, $care_profiles, TRUE);
        $mhcu_care_url = '';
        if ($show_mhcu_care) {
            // Token harus sama dengan Mhcu_care::_resolve(): md5(id_sesi . MHCU_CARE_TOKEN_SALT)
            $care_token = md5(intval($id_sesi) . MHCU_CARE_TOKEN_SALT);
            $mhcu_care_url = base_url('mhcu_care?token=' . urlencode($care_token) . '&id_sesi=' . intval($id_sesi));
        }

        return array(
            'nama' => $sesi->nama_lengkap,
            'nama_depan' => $nama_depan,
            'gender' => $gender,
            'unit' => $info['unit'],
            'jabatan' => $jabatan,
            'tanggal_pemeriksaan' => $tanggal,
            'periode' => $nama_periode,
            'profil_mental' => $profil_mental,
            'psikososial' => $psikososial,
            'kesimpulan' => $kesimpulan,
            'is_krisis' => ($hasil && isset($hasil->is_krisis)) ? (int) $hasil->is_krisis : 0,
            'show_mhcu_care' => $show_mhcu_care,
            'mhcu_care_url' => $mhcu_care_url,
        );
    }

    /**
     * Helper: get unit & jabatan dari npp
     */
    private function _get_unit_jabatan($npp, $presensi_role)
    {
        $role_upper = strtoupper(trim($presensi_role));

        $role_map = array(
            'GURUFT'   => 'guru_ft',
            'GURUSD'   => 'guru_sd',
            'GURUSMP'  => 'guru_smp',
            'GURUSMA'  => 'guru_sma',
            'PEGAWAI'  => 'pegawai',
            'STAFF'    => 'pegawai',
            'SD'       => 'pimpinan_sd',
            'SMP'      => 'pimpinan_smp',
            'SMA'      => 'pimpinan_sma',
            'PIMPINAN' => 'pimpinan',
        );

        $table = isset($role_map[$role_upper]) ? $role_map[$role_upper] : '';
        $unit = '';
        $jabatan = '';

        if (!empty($table)) {
            // ponytail: skema tabel role tidak seragam — pimpinan tidak punya deleted_at/unit/keterangan_jabatan (pakai jabatan), jadi cek kolom dulu
            if ($this->db->field_exists('deleted_at', $table)) {
                $this->db->where('deleted_at IS NULL', null, false);
            }
            $this->db->where('npp', $npp);
            if ($this->db->field_exists('keterangan_jabatan', $table)) {
                $this->db->select('*, keterangan_jabatan AS jabatan_final');
            } elseif ($this->db->field_exists('jabatan', $table)) {
                $this->db->select('*, jabatan AS jabatan_final');
            } else {
                $this->db->select('*');
            }
            $row = $this->db->get($table)->row();
            if ($row) {
                $unit = isset($row->unit) ? $row->unit : '';
                $jabatan = isset($row->jabatan_final) ? $row->jabatan_final : '';
            }
        }

        return array(
            'unit' => !empty($unit) ? $unit : $presensi_role,
            'jabatan' => !empty($jabatan) ? $jabatan : '-',
        );
    }

    /**
     * Helper: resolve presensi_role → target_role
     */
    private function _resolve_target_role($presensi_role)
    {
        $role_upper = strtoupper(trim($presensi_role));
        $pimpinan_roles = array('SD', 'SMP', 'SMA', 'PIMPINAN');
        if (in_array($role_upper, $pimpinan_roles)) {
            return 'pimpinan';
        }
        return 'guru_karyawan';
    }

    /**
     * Lookup band kategori dari tabel mhcu_band_kategori.
     */
    private function _lookup_band_kategori($kode_instrument, $dimensi_aspek, $skor)
    {
        $where_aspek = ($dimensi_aspek === null)
            ? 'dimensi_aspek IS NULL'
            : "dimensi_aspek = '" . $this->db->escape_str($dimensi_aspek) . "'";

        return $this->db->query(
            "SELECT label_kategori, warna, deskripsi FROM mhcu_band_kategori
             WHERE kode_instrument = '" . $this->db->escape_str($kode_instrument) . "'
             AND " . $where_aspek . "
             AND " . floatval($skor) . " BETWEEN batas_bawah AND batas_atas
             LIMIT 1"
        )->row();
    }

    /**
     * Helper: get token from header
     */
    private function _get_token()
    {
        $token = "";
        $headers = array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        if (isset($headers['x-token'])) {
            $token = $headers['x-token'];
        }
        return $token;
    }

    /**
     * Public recalculate skor MHCU untuk semua sesi selesai dalam 1 periode.
     * GET /apiapp/mhcu/recalculate_mhcu_periode?id_periode=<id>
     * Tidak butuh x-api-key / x-token (publik, untuk cron / trigger admin).
     * Mengulang perhitungan scoring pada setiap sesi selesai di periode tersebut
     * dan meng-update mhcu_hasil_individu (UPSERT).
     */
    public function recalculate_mhcu_periode_get()
    {
        $id_periode = intval($this->get('id_periode'));
        if (!$id_periode) {
            $this->response(array('status' => 0, 'message' => 'id_periode kosong'), 200);
            return;
        }

        $periode = $this->db->where('id_mhcu_periode', $id_periode)->get('mhcu_periode')->row();
        if (empty($periode)) {
            $this->response(array('status' => 0, 'message' => 'Periode tidak ditemukan'), 200);
            return;
        }

        $this->load->library('Mhcu_scoring');

        $rows = $this->db->where('id_mhcu_periode', $id_periode)
            ->where('status', 'selesai')
            ->where('deleted_at IS NULL', null, false)
            ->order_by('id_mhcu_sesi', 'ASC')
            ->get('mhcu_sesi')->result();

        $success = 0;
        $failed  = 0;
        $by_cat  = array();
        foreach ($rows as $r) {
            $res = $this->mhcu_scoring->hitung($r->id_mhcu_sesi);
            if (!empty($res['success'])) {
                $success++;
                $cat = isset($res['data']['kode_profil']) ? $res['data']['kode_profil'] : '-';
                if (!isset($by_cat[$cat])) $by_cat[$cat] = 0;
                $by_cat[$cat]++;
            } else {
                $failed++;
            }
        }

        $this->response(array(
            'status'  => 1,
            'message' => 'Recalculate periode "' . $periode->nama_periode . '" selesai. Berhasil: ' . $success . ', Gagal: ' . $failed,
            'data'    => array(
                'id_periode'   => $id_periode,
                'nama_periode' => $periode->nama_periode,
                'total'        => count($rows),
                'success'      => $success,
                'failed'       => $failed,
                'distribution' => $by_cat,
            ),
        ), 200);
    }

    /**
     * Public recalculate skor MHCU untuk 1 sesi tertentu.
     * GET /apiapp/mhcu/recalculate_mhcu_sesi?id_sesi=<id>
     */
    public function recalculate_mhcu_sesi_get()
    {
        $id_sesi = intval($this->get('id_sesi'));
        if (!$id_sesi) {
            $this->response(array('status' => 0, 'message' => 'id_sesi kosong'), 200);
            return;
        }

        $sesi = $this->db->where('id_mhcu_sesi', $id_sesi)->get('mhcu_sesi')->row();
        if (empty($sesi)) {
            $this->response(array('status' => 0, 'message' => 'Sesi tidak ditemukan'), 200);
            return;
        }

        $this->load->library('Mhcu_scoring');
        $res = $this->mhcu_scoring->hitung($id_sesi);

        $this->response(array(
            'status'  => !empty($res['success']) ? 1 : 0,
            'message' => isset($res['message']) ? $res['message'] : 'Selesai',
            'data'    => isset($res['data']) ? $res['data'] : array(),
        ), 200);
    }
}
