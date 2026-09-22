<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mhcu_scoring - Library skoring MHCU (REVISI v2)
 * 
 * Perubahan dari v1:
 * - Dashboard: 8 profil bernama+emoji (bukan 4 warna rata-rata)
 * - Override item bunuh diri PHQ-9 #9 > 0 → langsung profil terberat
 * - Skor Psikososial (instrument 5/7) ikut menentukan profil
 * - Instrument 7 (Pimpinan) ditambahkan, pola sama Instrument 5
 * - Instrument 8 (Pimpinan) = no scoring, sama seperti Instrument 6
 */
class Mhcu_scoring {

    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Main scoring function
     * @param int $id_sesi
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function hitung($id_sesi)
    {
        $sesi = $this->CI->db->where('id_mhcu_sesi', $id_sesi)->get('mhcu_sesi')->row();
        if (empty($sesi)) {
            return array('success' => false, 'message' => 'Sesi tidak ditemukan');
        }

        // Tentukan target_role dari presensi_role
        $target_role = $this->_resolve_target_role($sesi->presensi_role);

        $instruments = $this->CI->db->order_by('no_urut', 'ASC')->get('mhcu_instrument')->result();
        if (empty($instruments)) {
            return array('success' => false, 'message' => 'Tidak ada instrument');
        }

        $all_scores = array();
        $raw_scores = array(); // key=value mentah utk dashboard
        $flag_suicide = false;

        foreach ($instruments as $inst) {
            $kode = $inst->kode_instrument;

            // Skip instrument yang tidak relevan dengan role
            if ($inst->target_role != 'all' && $inst->target_role != $target_role) {
                continue;
            }

            // Instrument tanpa skoring
            if ($kode == 'TAMBAHAN' || $kode == 'TAMBAHAN_PIMPINAN') {
                continue;
            }

            $items = $this->CI->db
                ->where('id_instrument', $inst->id_instrument)
                ->where('deleted_at IS NULL', null, false)
                ->order_by('no_urut_item', 'ASC')
                ->get('mhcu_instrument_item')->result();

            $jawaban = $this->CI->db
                ->where('id_sesi', $id_sesi)
                ->where('id_instrument', $inst->id_instrument)
                ->get('mhcu_sesi_instrument')->result();

            // Index jawaban by item
            $jawaban_map = array();
            foreach ($jawaban as $j) {
                $jawaban_map[$j->id_instrument_item] = $j;
            }

            // Get skala options for lookup
            $skala_map = array();
            foreach ($items as $item) {
                if (!isset($skala_map[$item->id_skala])) {
                    $opts = $this->CI->db->where('id_skala', $item->id_skala)->get('mhcu_skala_option')->result();
                    $skala_map[$item->id_skala] = array();
                    foreach ($opts as $o) {
                        $skala_map[$item->id_skala][$o->id_skala_option] = $o->skor_value;
                    }
                }
            }

            $result = array('scores' => array());

            switch ($kode) {
                case 'WHO5':
                    $result = $this->_score_who5($items, $jawaban_map, $skala_map, $inst->id_instrument, $id_sesi);
                    break;
                case 'PHQ9':
                    $result = $this->_score_phq9($items, $jawaban_map, $skala_map, $inst->id_instrument, $id_sesi);
                    if (isset($result['flag_suicide']) && $result['flag_suicide']) {
                        $flag_suicide = true;
                    }
                    break;
                case 'GAD7':
                    $result = $this->_score_gad7($items, $jawaban_map, $skala_map, $inst->id_instrument, $id_sesi);
                    break;
                case 'CBI':
                    $result = $this->_score_cbi($items, $jawaban_map, $skala_map, $inst->id_instrument, $id_sesi);
                    break;
                case 'PSIKOSOSIAL':
                case 'PSIKOSOSIAL_PIMPINAN':
                    $result = $this->_score_psikososial($items, $jawaban_map, $skala_map, $inst->id_instrument, $id_sesi);
                    break;
                default:
                    break;
            }

            if (!empty($result['scores'])) {
                foreach ($result['scores'] as $s) {
                    $s['id_instrument'] = $inst->id_instrument;
                    $all_scores[] = $s;
                }
            }

            // Simpan raw values utk dashboard
            if (isset($result['raw'])) {
                foreach ($result['raw'] as $rk => $rv) {
                    $raw_scores[$rk] = $rv;
                }
            }
        }

        // Dashboard 8-profil
        $profil = $this->_hitung_profil_dashboard($raw_scores, $flag_suicide, $target_role);

        // Bersihkan turunan lama untuk id_sesi ini agar re-scoring idempotent.
        // (Skor dihitung ulang, hasil individu di-upsert, tiket peringatan
        // yang masih 'waiting' di-drop supaya tidak duplikat.)
        // Semua dalam 1 transaksi supaya konsisten.
        $this->CI->db->trans_start();
        $this->CI->db->where('id_sesi', $id_sesi)->delete('mhcu_instrument_score');
        $this->CI->db->where('id_sesi', $id_sesi)->where('status_alert', 'waiting')->delete('mhcu_peringatan');

        // Insert ulang skor per-instrument (sudah bersih)
        if (!empty($all_scores)) {
            foreach ($all_scores as $s) {
                $s['id_sesi'] = $id_sesi;
                $this->CI->db->insert('mhcu_instrument_score', $s);
            }
        }

        // Lookup id_profil_kategori dari tabel referensi.
        // Kode profil (_get_profil key) = kode_profil di mhcu_profil_kategori.
        $profil_row = $this->CI->db->where('kode_profil', $profil['kode_profil'])->get('mhcu_profil_kategori')->row();
        $id_profil = $profil_row ? $profil_row->id_profil_kategori : null;
        $label_profil = ($profil_row && !empty($profil_row->label_profil)) ? $profil_row->label_profil : $profil['nama_profil'];
        $saran_kategori = ($profil_row && !empty($profil_row->saran_kategori)) ? $profil_row->saran_kategori : $profil['rekomendasi'];

        // Upsert mhcu_hasil_individu
        // is_krisis: profil dengan warna merah (Immediate & High Occupational Risk)
        $warna_merah = array('merah_tua', 'merah_muda');
        $is_krisis = in_array($profil['warna'], $warna_merah, TRUE) ? 1 : 0;
        $hasil_data = array(
            'id_sesi' => $id_sesi,
            'id_profil_kategori' => $id_profil,
            'kategori_keseluruhan' => $label_profil,
            'is_krisis' => $is_krisis,
            'saran_rekomendasi' => $saran_kategori,
        );
        $existing = $this->CI->db->where('id_sesi', $id_sesi)->get('mhcu_hasil_individu')->row();
        if (!empty($existing)) {
            $this->CI->db->where('id_sesi', $id_sesi)->update('mhcu_hasil_individu', $hasil_data);
        } else {
            $this->CI->db->insert('mhcu_hasil_individu', $hasil_data);
        }


        // Tiket peringatan: hanya profil dengan warna kategori berat
        // Mapping 9 warna → 4 jenis_alert:
        //   merah_tua, merah_muda → merah
        //   coklat_orange, orange_tua, orange_muda → orange
        //   kuning, hijau_pudar, hijau_muda, hijau_tua → skip (kuning/hijau)
        $warna_to_alert = array(
            'merah_tua' => 'merah',
            'merah_muda' => 'merah',
            'coklat_orange' => 'orange',
            'orange_tua' => 'orange',
            'orange_muda' => 'orange',
        );
        if (isset($warna_to_alert[$profil['warna']])) {
            $peringatan_data = array(
                'id_sesi' => $id_sesi,
                'jenis_alert' => $warna_to_alert[$profil['warna']],
                'status_alert' => 'waiting',
                'diproses_oleh' => '',
                'updated_by' => 'system',
            );
            $this->CI->db->insert('mhcu_peringatan', $peringatan_data);
        }
        $this->CI->db->trans_complete();

        return array(
            'success' => true,
            'message' => 'Scoring selesai',
            'data' => array(
                'kode_profil' => $profil['kode_profil'],
                'id_profil_kategori' => $id_profil,
                'is_krisis' => $is_krisis,
                'scores' => $all_scores,
                'flag_suicide' => $flag_suicide,
            ),
        );
    }

    /**
     * Resolve presensi_role → target_role
     * Pimpinan: SD, SMP, SMA, PIMPINAN (non-unit)
     * Sisanya: guru_karyawan
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

    // ========================================================================
    // Instrument 1-4: TIDAK BERUBAH dari v1
    // ========================================================================

    /**
     * WHO-5: 5 item, skala 1-6 → skor_value 0-5
     * Raw = sum items (0-25), Final = raw * 4 (0-100)
     */
    private function _score_who5($items, $jawaban_map, $skala_map, $id_instrument, $id_sesi)
    {
        $raw = 0;
        foreach ($items as $item) {
            if (isset($jawaban_map[$item->id_instrument_item])) {
                $j = $jawaban_map[$item->id_instrument_item];
                $skor = isset($skala_map[$item->id_skala][$j->id_skala_option]) ? $skala_map[$item->id_skala][$j->id_skala_option] : 0;
                $raw += $skor;
            }
        }

        $final = $raw * 4;

        if ($final <= 28) {
            $kategori = 'Risiko Tinggi';
        } elseif ($final < 50) {
            $kategori = 'Menurun';
        } else {
            $kategori = 'Baik';
        }

        return array(
            'scores' => array(array(
                'dimensi_aspek' => 'Kesejahteraan Psikologis',
                'skor' => $final,
                'kategori' => $kategori,
            )),
            'kategori' => $kategori,
            'raw' => array('who5' => $final),
        );
    }

    /**
     * PHQ-9: 9 item, skala 0-3
     * Raw = sum items (0-27)
     * Item 9 > 0 → flag suicide
     */
    private function _score_phq9($items, $jawaban_map, $skala_map, $id_instrument, $id_sesi)
    {
        $raw = 0;
        $flag_suicide = false;

        foreach ($items as $item) {
            if (isset($jawaban_map[$item->id_instrument_item])) {
                $j = $jawaban_map[$item->id_instrument_item];
                $skor = isset($skala_map[$item->id_skala][$j->id_skala_option]) ? $skala_map[$item->id_skala][$j->id_skala_option] : 0;
                $raw += $skor;

                if ($item->no_urut_item == 9 && $skor > 0) {
                    $flag_suicide = true;
                }
            }
        }

        if ($raw <= 4) {
            $kategori = 'Minimal';
        } elseif ($raw <= 9) {
            $kategori = 'Ringan';
        } elseif ($raw <= 14) {
            $kategori = 'Sedang';
        } elseif ($raw <= 19) {
            $kategori = 'Cukup Parah';
        } else {
            $kategori = 'Berat';
        }

        return array(
            'scores' => array(array(
                'dimensi_aspek' => 'Depresi',
                'skor' => $raw,
                'kategori' => $kategori,
            )),
            'kategori' => $kategori,
            'flag_suicide' => $flag_suicide,
            'raw' => array('phq9' => $raw),
        );
    }

    /**
     * GAD-7: 7 item, skala 0-3
     * Raw = sum items (0-21)
     */
    private function _score_gad7($items, $jawaban_map, $skala_map, $id_instrument, $id_sesi)
    {
        $raw = 0;
        foreach ($items as $item) {
            if (isset($jawaban_map[$item->id_instrument_item])) {
                $j = $jawaban_map[$item->id_instrument_item];
                $skor = isset($skala_map[$item->id_skala][$j->id_skala_option]) ? $skala_map[$item->id_skala][$j->id_skala_option] : 0;
                $raw += $skor;
            }
        }

        if ($raw <= 4) {
            $kategori = 'Minimal';
        } elseif ($raw <= 9) {
            $kategori = 'Ringan';
        } elseif ($raw <= 14) {
            $kategori = 'Sedang';
        } else {
            $kategori = 'Berat';
        }

        return array(
            'scores' => array(array(
                'dimensi_aspek' => 'Kecemasan',
                'skor' => $raw,
                'kategori' => $kategori,
            )),
            'kategori' => $kategori,
            'raw' => array('gad7' => $raw),
        );
    }

    /**
     * CBI: 19 item, skala 1-5 → transformasi 0/25/50/75/100
     * Item 12 reversed (CBI_per_aspek.docx: "Butir Nomor 12 bersifat favorable/reversed")
     * 3 dimensi: Personal (1-6), Work (7-13), Client (14-19)
     */
    private function _score_cbi($items, $jawaban_map, $skala_map, $id_instrument, $id_sesi)
    {
        // Skala 3 (CBI): skor_value di DB sudah 0/25/50/75/100 (label posisi → nilai).
        // Jadi tidak perlu transform lagi. Item reversed: cukup 100 - skor_value.
        $personal = array();
        $work = array();
        $client = array();

        foreach ($items as $item) {
            if (!isset($jawaban_map[$item->id_instrument_item])) continue;

            $j = $jawaban_map[$item->id_instrument_item];
            $skor_value = isset($skala_map[$item->id_skala][$j->id_skala_option]) ? $skala_map[$item->id_skala][$j->id_skala_option] : 0;
            $transformed = ($item->is_reversed == 1) ? (100 - $skor_value) : $skor_value;

            $no = $item->no_urut_item;
            if ($no >= 1 && $no <= 6) {
                $personal[] = $transformed;
            } elseif ($no >= 7 && $no <= 13) {
                $work[] = $transformed;
            } elseif ($no >= 14 && $no <= 19) {
                $client[] = $transformed;
            }
        }

        $scores = array();
        $dimensi_skor = array();

        $avg_personal = count($personal) > 0 ? array_sum($personal) / count($personal) : 0;
        $kat_personal = $this->_kategori_cbi($avg_personal);
        $scores[] = array('dimensi_aspek' => 'Personal Burnout', 'skor' => round($avg_personal, 2), 'kategori' => $kat_personal);
        $dimensi_skor[] = $avg_personal;

        $avg_work = count($work) > 0 ? array_sum($work) / count($work) : 0;
        $kat_work = $this->_kategori_cbi($avg_work);
        $scores[] = array('dimensi_aspek' => 'Work-Related Burnout', 'skor' => round($avg_work, 2), 'kategori' => $kat_work);
        $dimensi_skor[] = $avg_work;

        $avg_client = count($client) > 0 ? array_sum($client) / count($client) : 0;
        $kat_client = $this->_kategori_cbi($avg_client);
        $scores[] = array('dimensi_aspek' => 'Client-Related Burnout', 'skor' => round($avg_client, 2), 'kategori' => $kat_client);
        $dimensi_skor[] = $avg_client;

        $avg_all = count($dimensi_skor) > 0 ? array_sum($dimensi_skor) / count($dimensi_skor) : 0;
        $kat_all = $this->_kategori_cbi($avg_all);
        $scores[] = array('dimensi_aspek' => 'CBI Keseluruhan', 'skor' => round($avg_all, 2), 'kategori' => $kat_all);

        return array(
            'scores' => $scores,
            'kategori' => $kat_all,
            'raw' => array('cbi' => round($avg_all, 2)),
        );
    }

    private function _kategori_cbi($skor)
    {
        if ($skor <= 25.00) return 'Rendah';
        if ($skor <= 50.00) return 'Sedang';
        if ($skor <= 75.00) return 'Tinggi';
        return 'Sangat Tinggi';
    }

    // ========================================================================
    // Instrument 5 (Guru/Karyawan) & 7 (Pimpinan): Faktor Psikososial
    // Pola sama, aspek & jumlah item beda
    // ========================================================================

    /**
     * Faktor Psikososial: 16 item (Guru/Karyawan) atau 15 item (Pimpinan)
     * Skala 0-4, aspek = jumlah butir
     * Total = jumlah semua item
     */
    private function _score_psikososial($items, $jawaban_map, $skala_map, $id_instrument, $id_sesi)
    {
        $item_skor = array();
        foreach ($items as $item) {
            if (isset($jawaban_map[$item->id_instrument_item])) {
                $j = $jawaban_map[$item->id_instrument_item];
                $skor = isset($skala_map[$item->id_skala][$j->id_skala_option]) ? $skala_map[$item->id_skala][$j->id_skala_option] : 0;
                $item_skor[$item->no_urut_item] = $skor;
            } else {
                $item_skor[$item->no_urut_item] = 0;
            }
        }

        // Group items by dimensi_aspek
        $aspek_items = array();
        foreach ($items as $item) {
            $asp = $item->dimensi_aspek;
            if (!isset($aspek_items[$asp])) {
                $aspek_items[$asp] = array();
            }
            $aspek_items[$asp][] = $item->no_urut_item;
        }

        $scores = array();
        $total = 0;
        $item_count = 0;

        foreach ($aspek_items as $nama => $nos) {
            $sum = 0;
            $count = count($nos);
            foreach ($nos as $no) {
                $sum += isset($item_skor[$no]) ? $item_skor[$no] : 0;
            }
            $max = $count * 4; // max per item = 4
            $kat = $this->_kategori_psikososial_aspek($kode, $nama, $sum);
            $scores[] = array('dimensi_aspek' => $nama, 'skor' => $sum, 'kategori' => $kat);
            $total += $sum;
            $item_count += $count;
        }

        // Total
        $max_total = $item_count * 4;
        $total_kode = ($item_count == 15) ? 'PSIKOSOSIAL_PIMPINAN' : 'PSIKOSOSIAL';
        $kat_total = $this->_kategori_psikososial_total($total_kode, $total);

        $total_label = ($item_count == 15) ? 'Faktor Psikososial Pimpinan - Total' : 'Faktor Psikososial - Total';
        $scores[] = array('dimensi_aspek' => $total_label, 'skor' => $total, 'kategori' => $kat_total);

        return array(
            'scores' => $scores,
            'kategori' => $kat_total,
            'raw' => array('psikososial_total' => $total, 'psikososial_max' => $max_total),
        );
    }

    /**
     * Lookup band kategori dari tabel mhcu_band_kategori.
     * Return row (label_kategori, warna, deskripsi) atau null.
     */
    private function _lookup_band($kode_instrument, $dimensi_aspek, $skor)
    {
        $where_aspek = ($dimensi_aspek === null)
            ? 'dimensi_aspek IS NULL'
            : "dimensi_aspek = '" . $this->CI->db->escape_str($dimensi_aspek) . "'";

        $row = $this->CI->db->query(
            "SELECT label_kategori, warna, deskripsi FROM mhcu_band_kategori
             WHERE kode_instrument = '" . $this->CI->db->escape_str($kode_instrument) . "'
             AND " . $where_aspek . "
             AND " . floatval($skor) . " BETWEEN batas_bawah AND batas_atas
             LIMIT 1"
        )->row();

        return $row;
    }

    /**
     * Kategori aspek psikososial (per aspek) — lookup dari mhcu_band_kategori
     */
    private function _kategori_psikososial_aspek($kode_instrument, $dimensi_aspek, $skor)
    {
        $band = $this->_lookup_band($kode_instrument, $dimensi_aspek, $skor);
        return $band ? $band->label_kategori : 'Baik';
    }

    /**
     * Kategori total psikososial — lookup dari mhcu_band_kategori
     */
    private function _kategori_psikososial_total($kode_instrument, $skor)
    {
        $band = $this->_lookup_band($kode_instrument, 'Total', $skor);
        return $band ? $band->label_kategori : 'Baik';
    }

    // ========================================================================
    // Dashboard 9-Profil (REVISI v3.3, fixx)
    // Sumber: Rumus Profil MHCU fixx.docx
    // ========================================================================

    /**
     * Tentukan profil dashboard dari skor mentah + flag suicide.
     *
     * Rumus per dokumen "fixx":
     *  P9 Immediate: (PHQ 15-27 DAN GAD 15-21 DAN WHO 0-28) atau item9≥1 (override)
     *  P8 High Occupational Risk: (WHO 0-49 atau PHQ 10-27 atau GAD 10-21)
     *      dan CBI 25-100 dan Psiko<33
     *  P5 Burnout with emotional distress: (PHQ 10-27 atau GAD 10-21)
     *      dan CBI≥50 dan Psiko≥33 (WHO bebas)
     *  P7 Emotional Distress: (PHQ 10-27 atau GAD 10-27), CBI<50,
     *      Psiko cukup/baik 33-64 (WHO bebas)
     *  P6 Burnout Dominant: CBI 50-100 dan (PHQ 0-9 atau GAD 0-9),
     *      Psiko 0-64 (WHO bebas)
     *  P4 Needs Monitoring: (WHO 28-100 atau PHQ 5-9 atau GAD 5-9),
     *      CBI 25-75, Psiko≥33
     *  P1 Optimal: GK dan Pim (WHO 50-100 DAN PHQ 0-4 DAN GAD 0-4); CBI 0-25,
     *      Psiko 49-64 (GK) / 45-60 (Pim)
     *  P2 Healthy Fatigued: (WHO 28-100 ATAU PHQ 0-9 ATAU GAD 0-9), CBI 0-50,
     *      Psiko 49-64 (GK) / 45-60 (Pim)
     *  P3 Workplace Support: (WHO 28-100 ATAU PHQ 0-9 ATAU GAD 0-9), CBI 0-50,
     *      Psiko 0-48 (GK) / 0-45 (Pim)
     *
     * Prioritas evaluasi: berat→ringan, TAPI 3 profil terakhir dari
     * ringan→berat: Optimal → Fatigued → Workplace Support.
     *
     * Fallback: WHO≤28 yang tidak tertangkap P9 (item9=0, atau
     * PHQ/GAD/WHO tidak semua terpenuhi) → Emotional Distress
     * (asumsi pengisian saat stres, hasil anomali).
     *
     * $raw_scores keys: who5, phq9, gad7, cbi, psikososial_total, psikososial_max
     */
    private function _hitung_profil_dashboard($raw, $flag_suicide, $target_role)
    {
        $who5 = isset($raw['who5']) ? $raw['who5'] : 0;
        $phq9 = isset($raw['phq9']) ? $raw['phq9'] : 0;
        $gad7 = isset($raw['gad7']) ? $raw['gad7'] : 0;
        $cbi = isset($raw['cbi']) ? $raw['cbi'] : 0;

        $psiko_total = isset($raw['psikososial_total']) ? $raw['psikososial_total'] : 0;
        $psiko_max = isset($raw['psikososial_max']) ? $raw['psikososial_max'] : 64;
        // Ambang batas psikososial (skor MENTAH):
        // Guru/Karyawan (max=64): CB>=33
        // Pimpinan (max=60): CB>=30
        // Catatan: P1, P2, P3 pakai ambang 49/48 (lihat _hitung_profil_dashboard).
        $is_pimpinan = ($psiko_max == 60);
        $psiko_cukup = $is_pimpinan ? 30 : 33;

        // === Profil 9 (paling berat): Override item bunuh diri ===
        // Pengecekan khusus: item9≥1 otomatis Immediate terlepas dari skor lainnya
        if ($flag_suicide) {
            return $this->_get_profil('immediate');
        }

        // === Profil 9 Immediate: (PHQ 15-27 DAN GAD 15-21 DAN WHO 0-28)
        //     atau item9≥1 (item9≥1 sudah ditangkap flag_suicide di atas) ===
        if (($phq9 >= 15 && $phq9 <= 27) && ($gad7 >= 15 && $gad7 <= 21) && $who5 <= 28) {
            return $this->_get_profil('immediate');
        }

        // === Profil 8 High Occupational Risk: (WHO≤49 atau PHQ 10-27 atau GAD 10-21)
        //     dan CBI 25-100 dan Psiko<33 ===
        if (($who5 <= 49 || ($phq9 >= 10 && $phq9 <= 27) || ($gad7 >= 10 && $gad7 <= 21))
            && $cbi >= 25 && $cbi <= 100 && $psiko_total < $psiko_cukup) {
            return $this->_get_profil('high_risk');
        }

        // === Profil 5 Burnout with emotional distress: (PHQ 10-27 atau GAD 10-21)
        //     dan CBI≥50 dan Psiko≥33 (WHO bebas) ===
        if ((($phq9 >= 10 && $phq9 <= 27) || ($gad7 >= 10 && $gad7 <= 21))
            && $cbi >= 50 && $psiko_total >= $psiko_cukup) {
            return $this->_get_profil('burnout_emotional');
        }

        // === Profil 7 Emotional Distress: (PHQ 10-27 atau GAD 10-27), CBI<50,
        //     Psiko cukup/baik 33-64 (WHO bebas) ===
        // ponytail: P7 unik - GK & Pim sama-sama 33, bukan pattern 33/30 seperti P4/P5/P8
        if ((($phq9 >= 10 && $phq9 <= 27) || ($gad7 >= 10 && $gad7 <= 27))
            && $cbi < 50 && $psiko_total >= 33) {
            return $this->_get_profil('emotional');
        }

        // === Profil 6 Burnout Dominant: CBI 50-100 dan (PHQ 0-9 atau GAD 0-9),
        //     Psiko 0-64 (WHO bebas) ===
        if ($cbi >= 50 && $cbi <= 100 && ($phq9 <= 9 || $gad7 <= 9) && $psiko_total <= $psiko_max) {
            return $this->_get_profil('burnout');
        }

        // === Profil 4 Needs Monitoring: (WHO 28-100 atau PHQ 5-9 atau GAD 5-9),
        //     CBI 25-75, Psiko≥33 ===
        if (($who5 >= 28 || ($phq9 >= 5 && $phq9 <= 9) || ($gad7 >= 5 && $gad7 <= 9))
            && $cbi >= 25 && $cbi <= 75 && $psiko_total >= $psiko_cukup) {
            return $this->_get_profil('monitoring');
        }

        // === 3 profil terakhir: ringan→berat (Optimal → Fatigued → Workplace) ===

        // Ambang P1/P2/P3 beda GK vs Pim (per "Rumus Profil MHCU fixx.docx"):
        //   GK: P1/P2 Psiko 49-64, P3 Psiko 0-48
        //   Pim: P1/P2 Psiko 45-60, P3 Psiko 0-45
        $psiko_lower_p1p2 = $is_pimpinan ? 45 : 49;
        $psiko_upper_p3   = $is_pimpinan ? 45 : 48;

        // === Profil 1 Optimal: GK dan Pim pakai AND (WHO 50-100 DAN PHQ 0-4 DAN GAD 0-4);
        //     CBI 0-25, Psiko 49-64 (GK) / 45-60 (Pim) ===
        if ($cbi <= 25 && $psiko_total >= $psiko_lower_p1p2 && $psiko_total <= $psiko_max
            && $who5 >= 50 && $phq9 <= 4 && $gad7 <= 4) {
            return $this->_get_profil('optimal');
        }

        // === Profil 2 Healthy Fatigued: (WHO 28-100 ATAU PHQ 0-9 ATAU GAD 0-9),
        //     CBI 0-50, Psiko 49-64 (GK) / 45-60 (Pim) ===
        if (($who5 >= 28 || $phq9 <= 9 || $gad7 <= 9) && $cbi <= 50
            && $psiko_total >= $psiko_lower_p1p2 && $psiko_total <= $psiko_max) {
            return $this->_get_profil('fatigued');
        }

        // === Profil 3 Workplace Support: (WHO 28-100 ATAU PHQ 0-9 ATAU GAD 0-9),
        //     CBI 0-50, Psiko 0-48 (GK) / 0-45 (Pim) ===
        if (($who5 >= 28 || $phq9 <= 9 || $gad7 <= 9) && $cbi <= 50
            && $psiko_total <= $psiko_upper_p3) {
            return $this->_get_profil('workplace_support');
        }

        // Fallback: WHO≤28 yang tidak tertangkap P9 (item9=0, atau
        // PHQ/GAD/WHO tidak semua terpenuhi) → Emotional Distress
        if ($who5 <= 28) {
            return $this->_get_profil('emotional');
        }

        // Fallback ultimate
        return $this->_get_profil('monitoring');
    }

    /**
     * Ambil data lengkap 1 profil (nama, emoji, warna, rekomendasi)
     * Sumber: Rumus Profil MHCU fix (rev final).docx (9 profil, 9 warna unik)
     */
    private function _get_profil($key)
    {
        $profil = array(
            'immediate' => array(
                'kode_profil' => 'immediate',
                'nama_profil' => 'Immediate Professional Follow-up',
                'icon' => '!!!',
                'warna' => 'merah_tua',
                'rekomendasi' => 'Sangat disarankan melakukan konsultasi dengan psikolog atau psikiater sesegera mungkin. Bila mengalami pikiran untuk menyakiti diri sendiri atau merasa tidak mampu menjalankan aktivitas sehari-hari, segera mencari bantuan profesional atau menghubungi orang yang dipercaya.',
            ),
            'high_risk' => array(
                'kode_profil' => 'high_risk',
                'nama_profil' => 'High Occupational Risk',
                'icon' => '!!',
                'warna' => 'merah_muda',
                'rekomendasi' => 'Disarankan melakukan diskusi dengan pimpinan mengenai beban kerja dan dukungan organisasi, mengikuti program pendampingan wellbeing, serta berkonsultasi dengan psikolog bila diperlukan. Monitoring ulang dalam waktu 1 bulan.',
            ),
            'burnout_emotional' => array(
                'kode_profil' => 'burnout_emotional',
                'nama_profil' => 'Burnout with emotional distress',
                'icon' => '!',
                'warna' => 'coklat_orange',
                'rekomendasi' => 'Fokus pada pemulihan energi, peninjauan ulang beban kerja serta disarankan berkonsultasi dengan psikolog untuk asesmen lebih lanjut, menjaga rutinitas harian, memperkuat dukungan sosial, dan memantau perkembangan gejala.',
            ),
            'emotional' => array(
                'kode_profil' => 'emotional',
                'nama_profil' => 'Emotional Distress',
                'icon' => '!',
                'warna' => 'orange_muda',
                'rekomendasi' => 'Disarankan berkonsultasi dengan psikolog untuk asesmen lebih lanjut, menjaga rutinitas harian, memperkuat dukungan sosial, dan memantau perkembangan gejala.',
            ),
            'burnout' => array(
                'kode_profil' => 'burnout',
                'nama_profil' => 'Burnout Dominant',
                'icon' => '!',
                'warna' => 'orange_tua',
                'rekomendasi' => 'Fokus pada pemulihan energi, peninjauan ulang beban kerja, peningkatan dukungan organisasi, coaching dengan atasan, dan monitoring burnout secara berkala.',
            ),
            'monitoring' => array(
                'kode_profil' => 'monitoring',
                'nama_profil' => 'Needs Monitoring',
                'icon' => '~',
                'warna' => 'kuning',
                'rekomendasi' => 'Tingkatkan kualitas tidur, aktivitas fisik, dukungan sosial, dan lakukan evaluasi ulang kondisi dalam 1-3 bulan. Pertimbangkan konsultasi apabila keluhan menetap atau memburuk.',
            ),
            'workplace_support' => array(
                'kode_profil' => 'workplace_support',
                'nama_profil' => 'Workplace Support Needed',
                'icon' => '~',
                'warna' => 'hijau_pudar',
                'rekomendasi' => 'Diskusikan hambatan kerja dengan atasan, manfaatkan dukungan rekan kerja, serta identifikasi kebutuhan sumber daya atau komunikasi yang perlu diperbaiki.',
            ),
            'fatigued' => array(
                'kode_profil' => 'fatigued',
                'nama_profil' => 'Healthy but Fatigued',
                'icon' => '-',
                'warna' => 'hijau_muda',
                'rekomendasi' => 'Prioritaskan waktu istirahat, evaluasi beban kerja, manfaatkan cuti bila diperlukan, dan lakukan aktivitas pemulihan energi secara teratur.',
            ),
            'optimal' => array(
                'kode_profil' => 'optimal',
                'nama_profil' => 'Optimal Wellbeing',
                'icon' => '+',
                'warna' => 'hijau_tua',
                'rekomendasi' => 'Pertahankan kebiasaan hidup sehat, keseimbangan kerja-kehidupan pribadi, serta hubungan positif dengan rekan kerja. Ikuti program wellbeing sekolah yang mungkin akan diadakan sebagai upaya promotif.',
            ),
        );

        return isset($profil[$key]) ? $profil[$key] : $profil['monitoring'];
    }
}

/* End of file Mhcu_scoring.php */
/* Location: ./application/libraries/Mhcu_scoring.php */
