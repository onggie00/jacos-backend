<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shared SPP payment-detail target rules.
 * Keeps table and month resolution strict before any payment write.
 */
class Spp_payment_detail
{
    public function table_for($jenjang)
    {
        $table_map = array(
            'SD'  => 'spp_sd',
            'SMP' => 'spp_smp',
            'SMA' => 'spp_sma',
            'FT'  => 'spp_ft',
            'KB'  => 'spp_kb',
            'TK'  => 'spp_tk'
        );
        $key = strtoupper(trim($jenjang));
        return isset($table_map[$key]) ? $table_map[$key] : false;
    }

    public function months($detail_bulan)
    {
        $allowed = array(
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember',
            'januari', 'februari', 'maret', 'april', 'mei', 'juni'
        );
        $months = array();
        foreach (explode(',', (string) $detail_bulan) as $month) {
            $month = strtolower(trim($month));
            if ($month === '') {
                continue;
            }
            if (!in_array($month, $allowed)) {
                return false;
            }
            if (!in_array($month, $months)) {
                $months[] = $month;
            }
        }
        return empty($months) ? false : $months;
    }
}
