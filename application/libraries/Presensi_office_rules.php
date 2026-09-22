<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Presensi_office_rules
{
    public function checkoutStatus($checkOut, $startCheckout)
    {
        if (empty($checkOut) || empty($startCheckout)) {
            return '-';
        }

        return strtotime($checkOut) < strtotime($startCheckout) ? 'E' : 'SH';
    }

    public function canReportCheckout($row)
    {
        return empty($row->check_out);
    }

    public function isPendingCheckin($row)
    {
        return ($row->status_presensi == 'TROUBLE' || !empty($row->file_report))
            && (string) $row->report_status != '1';
    }

    public function isPendingCheckout($row)
    {
        return ($row->status_presensi_selesai == 'TROUBLE' || !empty($row->file_report_end))
            && (string) $row->report_status_end != '1';
    }
}
