<?php
/**
 * Regression checks for SPP payment detail target resolution.
 * Run: php application/controllers/apiapp/tests/spp_payment_detail_test.php
 */
define('BASEPATH', true);
require_once dirname(__FILE__) . '/../../../libraries/Spp_payment_detail.php';

$detail = new Spp_payment_detail();

if ($detail->table_for('sd') !== 'spp_sd') {
    throw new Exception('sd must resolve to spp_sd');
}
if ($detail->table_for('SD') !== 'spp_sd') {
    throw new Exception('SD must resolve case-insensitively');
}
if ($detail->table_for('unknown') !== false) {
    throw new Exception('unknown jenjang must be rejected');
}
if ($detail->months('Juli,Agustus,September') !== array('juli', 'agustus', 'september')) {
    throw new Exception('detail_bulan must resolve exact month names');
}
if ($detail->months('Juni,Juniper') !== false) {
    throw new Exception('unknown month must be rejected, not partially matched');
}

echo "PASS\n";
