<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk tracker publik MHCU.
 * Hanya query agregat. Tidak baca mhcu_hasil_individu / mhcu_instrument_score.
 *
 * Definisi tahap:
 *   tahap = (jumlah instrument applicable yg sudah lengkap dijawab) + 1
 *   tahap 6 = "tahap terakhir, semua instrument non-TAMBAHAN sudah dijawab"
 *   max tahap = 6 untuk semua role (4 instrument umum + 2 instrument spesifik-role).
 *   "Instrument sudah lengkap" derive dari jumlah baris jawaban vs jumlah item.
 */
class Mhcu_tracker_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Hitung payload JSON publik.
     */
    public function build_payload($periode)
    {
        $payload = array(
            'periode' => array(
                'nama_periode'    => $periode ? $periode->nama_periode : '-',
                'tanggal_mulai'   => $periode ? $periode->tanggal_mulai : '-',
                'tanggal_selesai' => $periode ? $periode->tanggal_selesai : '-',
            ),
            'ringkasan' => array(
                'target'      => ($periode && isset($periode->target_peserta) && $periode->target_peserta !== null) ? (int) $periode->target_peserta : 0,
                'sudah_mulai' => 0,
                'selesai'     => 0,
            ),
            'per_unit'  => array(),
            'per_tahap' => array(),
            'last_updated' => date('Y-m-d H:i:s'),
        );
        if (empty($periode)) return $payload;
        $id_periode = (int) $periode->id_mhcu_periode;

        $sudah = $this->db->query("SELECT COUNT(*) AS c FROM mhcu_sesi WHERE id_mhcu_periode = " . $id_periode . " AND deleted_at IS NULL")->row();
        $seles = $this->db->query("SELECT COUNT(*) AS c FROM mhcu_sesi WHERE id_mhcu_periode = " . $id_periode . " AND status = 'selesai' AND deleted_at IS NULL")->row();
        $payload['ringkasan']['sudah_mulai'] = isset($sudah->c) ? (int) $sudah->c : 0;
        $payload['ringkasan']['selesai']     = isset($seles->c) ? (int) $seles->c : 0;

        $payload['per_unit']  = $this->compute_per_unit($id_periode);
        $payload['per_tahap'] = $this->compute_per_tahap($id_periode);

        return $payload;
    }

    /**
     * Breakdown per unit/jenjang (Opsi C).
     */
    public function compute_per_unit($id_periode)
    {
        $rows = array();
        $units = array(
            array('label' => 'Guru SD',  'table' => 'guru_sd',  'has_deleted_at' => true),
            array('label' => 'Guru SMP', 'table' => 'guru_smp', 'has_deleted_at' => true),
            array('label' => 'Guru SMA', 'table' => 'guru_sma', 'has_deleted_at' => true),
            array('label' => 'Guru FT',  'table' => 'guru_ft',  'has_deleted_at' => true),
            array('label' => 'Karyawan', 'table' => 'pegawai',  'has_deleted_at' => false),
        );
        foreach ($units as $u) {
            $tbl = $u['table'];
            $where_master     = $u['has_deleted_at'] ? " WHERE deleted_at IS NULL" : "";
            $where_master_jn  = $u['has_deleted_at'] ? " AND u.deleted_at IS NULL" : "";

            $total_row  = $this->db->query("SELECT COUNT(*) AS c FROM `" . $tbl . "`" . $where_master)->row();
            $seles_row  = $this->db->query(
                "SELECT COUNT(*) AS c FROM mhcu_sesi s "
                . "INNER JOIN `" . $tbl . "` u ON u.npp = s.npp "
                . "WHERE s.id_mhcu_periode = " . $id_periode
                . " AND s.status = 'selesai' AND s.deleted_at IS NULL" . $where_master_jn
            )->row();
            $rows[] = array(
                'label'   => $u['label'],
                'selesai' => isset($seles_row->c) ? (int) $seles_row->c : 0,
                'total'   => isset($total_row->c) ? (int) $total_row->c : 0,
            );
        }
                // Pimpinan: gabung 3 tabel
        $pim_selects = array();
        foreach (array("pimpinan_sd","pimpinan_sma","pimpinan_smp") as $alias) {
            $pim_selects[] = "SELECT npp FROM " . $alias;
        }
        $pim_union = implode(" UNION ", $pim_selects);
        $seles_row_pim = $this->db->query(
            "SELECT COUNT(*) AS c FROM mhcu_sesi s "
            . "WHERE s.id_mhcu_periode = " . $id_periode
            . " AND s.status = 'selesai' AND s.deleted_at IS NULL"
            . " AND s.npp IN (" . $pim_union . ")"
        )->row();
        $seles_pim = isset($seles_row_pim->c) ? (int) $seles_row_pim->c : 0;
        $total_pim = $this->db->query(
            "SELECT COUNT(DISTINCT npp) AS c FROM (" . $pim_union . ") AS t"
        )->row();
        $total_pim = isset($total_pim->c) ? (int) $total_pim->c : 0;
        $rows[] = array("label" => "Pimpinan", "selesai" => $seles_pim, "total" => $total_pim);
        return $rows;
    }
    /**
     * Breakdown per tahap posisi (sesuai instruksi prompt bagian 2).
     * Tahap = jumlah instrument applicable yg sudah lengkap dijawab + 1
     * (max 6 tahap. Tahap 6 = "semua instrument non-TAMBAHAN sudah dijawab")
     */
    public function compute_per_tahap($id_periode)
    {
        $tahap_counts = array_fill(1, 6, 0);
        $tahap_labels = array(
            1 => "Tahap 1 - Instrument 1 (WHO-5)",
            2 => "Tahap 2 - Instrument 2 (PHQ-9)",
            3 => "Tahap 3 - Instrument 3 (GAD-7)",
            4 => "Tahap 4 - Instrument 4 (CBI)",
            5 => "Tahap 5 - Instrument Psikososial",
            6 => "Tahap 6 - Instrument Tambahan",
        );
        // Ambil semua sesi non-selesai di periode ini
        $sesi_rows = $this->db->query(
            "SELECT id_mhcu_sesi, presensi_role FROM mhcu_sesi "
            . "WHERE id_mhcu_periode = " . $id_periode . " AND deleted_at IS NULL"
        )->result();
        if (empty($sesi_rows)) {
            $out = array();
            for ($i = 1; $i <= 6; $i++) {
                $out[] = array("tahap" => $i, "label" => $tahap_labels[$i], "jumlah" => 0);
            }
            return $out;
        }
        // Instrument applicable map (sesuai _resolve_target_role)
        // role="guru_karyawan" (default): id_instrument 1,2,3,4,5,6 (TAMBAHAN)
        // role="pimpinan": id_instrument 1,2,3,4,7,8
        $appl_map = array("guru_karyawan" => array(1,2,3,4,5), "pimpinan" => array(1,2,3,4,7));
        // Item count per instrument
        $items_map = array();
        $r = $this->db->query("SELECT id_instrument, COUNT(*) AS c FROM mhcu_instrument_item WHERE deleted_at IS NULL GROUP BY id_instrument")->result();
        foreach ($r as $ir) $items_map[intval($ir->id_instrument)] = intval($ir->c);
        foreach ($sesi_rows as $sr) {
            $role_upper = strtoupper(trim($sr->presensi_role));
            $is_pim = in_array($role_upper, array("SD","SMP","SMA","PIMPINAN"));
            $appl = $is_pim ? $appl_map["pimpinan"] : $appl_map["guru_karyawan"];
            // Optional TAMBAHAN (inst 6 utk gk, inst 8 utk pim) — tak ikut hitung di posisi
            // Ambil jawaban map utk sesi ini
            $jwb_rows = $this->db->query(
                "SELECT id_instrument, COUNT(DISTINCT id_instrument_item) AS c "
                . "FROM mhcu_sesi_instrument WHERE id_sesi = " . intval($sr->id_mhcu_sesi) . " GROUP BY id_instrument"
            )->result();
            $answered = array();
            foreach ($jwb_rows as $jr) $answered[intval($jr->id_instrument)] = intval($jr->c);
            // Hitung berapa dari $appl yg "lengkap"
            $lengkap = 0;
            foreach ($appl as $iid) {
                $tot = isset($items_map[$iid]) ? $items_map[$iid] : 0;
                $ans = isset($answered[$iid]) ? $answered[$iid] : 0;
                if ($tot > 0 && $ans >= $tot) $lengkap++;
            }
            // tahap = $lengkap + 1 (dengan max 6)
            $tahap = $lengkap + 1;
            if ($tahap > 6) $tahap = 6;
            $tahap_counts[$tahap]++;
        }
        $out = array();
        for ($i = 1; $i <= 6; $i++) {
            $out[] = array("tahap" => $i, "label" => $tahap_labels[$i], "jumlah" => $tahap_counts[$i]);
        }
        return $out;
    }
}
