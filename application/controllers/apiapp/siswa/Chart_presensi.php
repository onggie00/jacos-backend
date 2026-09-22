<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chart Presensi Siswa - aggregated chart data for presensi visualization.
 *
 * Input (POST):
 *   - jenjang         (required) : sd | smp | sma | ft
 *   - id_siswa_aktif  (required) : ID siswa aktif
 *   - start_date      (optional) : Y-m-d, default = today - 7 days
 *   - end_date        (optional) : Y-m-d, default = today
 *   - tipe_chart      (optional) : harian | mingguan | bulanan, default = harian
 *
 * Output: 3 chart sections (bar_chart, line_chart, pie_chart) + parameter echo.
 *
 * Style reference: application/controllers/apiapp/ortu/Get_presensi.php
 */
class Chart_presensi extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index_post()
    {
        // ----- Step 1: Parse & validate input -----
        $jenjang        = $this->post('jenjang');
        $id_siswa_aktif = $this->post('id_siswa_aktif');
        $start_date     = $this->post('start_date');
        $end_date       = $this->post('end_date');
        $tipe_chart     = $this->post('tipe_chart');
        if (empty($tipe_chart)) {
            $tipe_chart = 'harian';
        }

        // Validate jenjang
        $allowed_jenjang = array('sd', 'smp', 'sma', 'ft');
        if (!in_array($jenjang, $allowed_jenjang)) {
            $this->response(array(
                'status'  => 400,
                'message' => 'jenjang tidak valid. Pilihan: sd, smp, sma, ft',
                'data'    => array()
            ), 200);
            return;
        }

        // Validate id_siswa_aktif
        if (empty($id_siswa_aktif)) {
            $this->response(array(
                'status'  => 400,
                'message' => 'id_siswa_aktif wajib diisi',
                'data'    => array()
            ), 200);
            return;
        }

        // Default date range: 7 hari terakhir
        if (empty($start_date)) {
            $start_date = date('Y-m-d', strtotime('-7 days'));
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-d');
        }

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
            $this->response(array(
                'status'  => 400,
                'message' => 'Format tanggal tidak valid (Y-m-d)',
                'data'    => array()
            ), 200);
            return;
        }

        // Ensure end >= start
        if (strtotime($end_date) < strtotime($start_date)) {
            $tmp        = $start_date;
            $start_date = $end_date;
            $end_date   = $tmp;
        }

        // Validate tipe_chart
        $allowed_tipe = array('harian', 'mingguan', 'bulanan');
        if (!in_array($tipe_chart, $allowed_tipe)) {
            $tipe_chart = 'harian';
        }

        // ----- Step 2: Build table names & fetch data -----
        $tabel_presensi = 'presensi_' . $jenjang;
        $tabel_izin     = 'izin_siswa_' . $jenjang;

        $safe_id = $this->db->escape_str($id_siswa_aktif);

        $presensi_rows = $this->mymodel->withquery(
            "SELECT tanggal_absen, status_absen
             FROM " . $tabel_presensi . "
             WHERE id_siswa_aktif = '" . $safe_id . "'
               AND tanggal_absen BETWEEN '" . $start_date . "' AND '" . $end_date . "'
             ORDER BY tanggal_absen ASC",
            'result'
        );

        $izin_rows = $this->mymodel->withquery(
            "SELECT tanggal_mulai, tanggal_selesai, jenis_izin
             FROM " . $tabel_izin . "
             WHERE id_siswa_aktif = '" . $safe_id . "'
               AND tanggal_mulai <= '" . $end_date . "'
               AND tanggal_selesai >= '" . $start_date . "'",
            'result'
        );

        // ----- Step 3: Index presensi by date -----
        $presensi_by_date = array();
        if (!empty($presensi_rows)) {
            foreach ($presensi_rows as $row) {
                $presensi_by_date[$row->tanggal_absen] = $row->status_absen;
            }
        }

        // ----- Step 4: Index izin by overlap date -----
        $izin_by_date = array();
        if (!empty($izin_rows)) {
            $cursor = strtotime($start_date);
            $end_ts = strtotime($end_date);
            while ($cursor <= $end_ts) {
                $date_str = date('Y-m-d', $cursor);
                foreach ($izin_rows as $izin) {
                    if ($date_str >= $izin->tanggal_mulai && $date_str <= $izin->tanggal_selesai) {
                        $izin_by_date[$date_str] = $izin->jenis_izin;
                        break;
                    }
                }
                $cursor = strtotime('+1 day', $cursor);
            }
        }

        // ----- Step 5: Classify each date -----
        $daily_status = array();
        $cursor = strtotime($start_date);
        $end_ts = strtotime($end_date);
        while ($cursor <= $end_ts) {
            $date_str = date('Y-m-d', $cursor);
            if (isset($presensi_by_date[$date_str])) {
                $daily_status[$date_str] = $presensi_by_date[$date_str]; // 'Hadir' or 'Terlambat'
            } elseif (isset($izin_by_date[$date_str])) {
                $tipe = strtolower($izin_by_date[$date_str]);
                if ($tipe == 'sakit') {
                    $daily_status[$date_str] = 'Sakit';
                } else {
                    $daily_status[$date_str] = 'Tidak Hadir';
                }
            } else {
                $daily_status[$date_str] = 'Alfa';
            }
            $cursor = strtotime('+1 day', $cursor);
        }

        // ----- Step 6: Pie chart - count 5 kategori -----
        $pie_counts = array(
            'Hadir' => 0, 'Terlambat' => 0, 'Alfa' => 0,
            'Sakit' => 0, 'Tidak Hadir' => 0
        );
        foreach ($daily_status as $status) {
            if (isset($pie_counts[$status])) {
                $pie_counts[$status]++;
            }
        }
        $pie_categories = array();
        foreach ($pie_counts as $label => $value) {
            $pie_categories[] = array('label' => $label, 'value' => $value);
        }

        // ----- Step 7: Bar/Line chart - group by tipe_chart -----
        $periods = array();
        $cursor  = strtotime($start_date);
        $end_ts  = strtotime($end_date);
        while ($cursor <= $end_ts) {
            $date_str = date('Y-m-d', $cursor);
            $key      = $this->_period_key($date_str, $tipe_chart);
            if (!isset($periods[$key])) {
                $periods[$key] = 0;
            }
            $cursor = strtotime('+1 day', $cursor);
        }
        ksort($periods);

        foreach ($daily_status as $date => $status) {
            if ($status == 'Hadir' || $status == 'Terlambat') {
                $key = $this->_period_key($date, $tipe_chart);
                if (isset($periods[$key])) {
                    $periods[$key]++;
                }
            }
        }

        // ----- Step 8: Build response -----
        $x_axis_label = ($tipe_chart == 'harian') ? 'Tanggal' : (($tipe_chart == 'mingguan') ? 'Minggu' : 'Bulan');
        $y_axis_label = 'Total Kehadiran (Hadir+Terlambat)';

        $bar_chart = array(
            'type'         => 'bar',
            'x_axis_label' => $x_axis_label,
            'y_axis_label' => $y_axis_label,
            'labels'       => array_keys($periods),
            'values'       => array_values($periods)
        );
        $line_chart = array(
            'type'         => 'line',
            'x_axis_label' => $x_axis_label,
            'y_axis_label' => $y_axis_label,
            'labels'       => array_keys($periods),
            'values'       => array_values($periods)
        );
        $pie_chart = array(
            'type'       => 'pie',
            'categories' => $pie_categories
        );

        // Detect no-data case for response message (spec §2)
        $has_data = (!empty($presensi_rows) || !empty($izin_rows));
        $response = array(
            'status'  => 200,
            'message' => $has_data
                ? 'Data chart presensi ditemukan'
                : 'Data chart presensi tidak ditemukan untuk rentang tanggal tersebut',
            'data'    => array(
                'parameter'  => array(
                    'jenjang'        => $jenjang,
                    'id_siswa_aktif' => $id_siswa_aktif,
                    'start_date'     => $start_date,
                    'end_date'       => $end_date,
                    'tipe_chart'     => $tipe_chart
                ),
                'bar_chart'  => $bar_chart,
                'line_chart' => $line_chart,
                'pie_chart'  => $pie_chart
            )
        );

        $this->response($response, 200);
    }

    /**
     * Hitung period key untuk grouping bar/line chart berdasarkan tipe_chart.
     *
     * @param string $date_str    Format Y-m-d
     * @param string $tipe_chart  harian | mingguan | bulanan
     * @return string Period key
     */
    private function _period_key($date_str, $tipe_chart)
    {
        $ts = strtotime($date_str);
        if ($tipe_chart == 'harian') {
            return $date_str;
        } elseif ($tipe_chart == 'mingguan') {
            $week = date('W', $ts);
            $year = date('o', $ts); // ISO year
            return 'W' . str_pad($week, 2, '0', STR_PAD_LEFT) . ' ' . $year;
        } else { // bulanan
            return date('Y-m', $ts);
        }
    }
}
