<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bri_spp_matcher
{
    public function resolve($rows, $billAmount)
    {
        $groups = array();

        foreach ($rows as $row) {
            $kodeTagihan = trim($row->kode_tagihan);
            $countBill = (int) $row->count_bill;
            $detailBulan = array();

            foreach (explode(',', (string) $row->detail_bulan) as $bulan) {
                $bulan = trim($bulan);
                if ($bulan !== '') {
                    $detailBulan[] = $bulan;
                }
            }

            if ($kodeTagihan === '' || $countBill < 1) {
                continue;
            }
            if (count($detailBulan) !== $countBill) {
                continue;
            }
            if ((float) $row->total_biaya * $countBill != (float) $billAmount) {
                continue;
            }

            if (!isset($groups[$kodeTagihan])) {
                $groups[$kodeTagihan] = array();
            }
            $groups[$kodeTagihan][] = $row;
        }

        if (count($groups) !== 1) {
            return false;
        }

        $kodeTagihan = key($groups);
        return $groups[$kodeTagihan][0];
    }
}
