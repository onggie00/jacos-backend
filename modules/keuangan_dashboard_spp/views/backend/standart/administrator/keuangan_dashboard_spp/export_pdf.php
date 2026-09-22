<?php
function kd_pdf_rupiah($n) {
    return 'Rp ' . number_format(intval($n), 0, ',', '.');
}
function kd_pdf_status($s) {
    $s = intval($s);
    if ($s === 0) return 'Belum Dibayar';
    if ($s === 1) return 'Menunggu';
    if ($s === 2) return 'Lunas';
    return '-';
}
function kd_pdf_kategori($k) {
    $map = array('awal' => 'Lebih Awal', 'tepat' => 'Tepat Waktu', 'telat' => 'Telat Bayar', 'dimuka' => 'Pembayaran Dimuka');
    return isset($map[$k]) ? $map[$k] : '-';
}
?>
<html>
<head>
<style>
    body { font-family: helvetica; font-size: 10px; }
    h1 { font-size: 16px; margin: 0 0 2px 0; }
    .filter-line { font-size: 10px; color: #555; margin-bottom: 10px; }
    table.summary td { padding: 1px 8px 1px 0; }
    table.detail { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.detail th {
        background: #C2410C; color: #FFFFFF; padding: 4px 5px;
        font-size: 9px; text-align: left;
    }
    table.detail td { border: 1px solid #D6D3D1; padding: 3px 5px; font-size: 9px; }
    .num { text-align: right; }
</style>
</head>
<body>
    <h1>LAPORAN DASHBOARD SPP</h1>
    <div class="filter-line">
        Periode: <b><?= $filter_info['bulan'] . ' ' . $filter_info['tahun']; ?></b> |
        Jenjang: <b><?= $filter_info['jenjang']; ?></b> |
        Kelas: <b><?= $filter_info['kelas']; ?></b> |
        Status: <b><?= $filter_info['status']; ?></b>
    </div>

    <table class="summary">
        <tr><td><b>Total Tagihan</b></td><td><?= kd_pdf_rupiah($summary['total_tagihan']); ?></td></tr>
        <tr><td><b>Total Diterima</b></td><td><?= kd_pdf_rupiah($summary['total_diterima']); ?></td></tr>
        <tr><td><b>Total Belum Dibayar</b></td><td><?= kd_pdf_rupiah($summary['belum_dibayar']); ?></td></tr>
        <tr><td><b>Kolektibilitas</b></td><td><?= $summary['kolektibilitas']; ?>%</td></tr>
    </table>

    <table class="detail">
        <thead>
            <tr>
                <th>No Transaksi</th>
                <th>Siswa</th>
                <th>Jenjang</th>
                <th>Kelas</th>
                <th>Bulan</th>
                <th>Jml</th>
                <th>Nominal</th>
                <th>Total Dibayar</th>
                <th>Status</th>
                <th>Kategori</th>
                <th>Tgl Bayar</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)) { ?>
            <tr><td colspan="11" align="center">Tidak ada data untuk filter ini.</td></tr>
        <?php } ?>
        <?php foreach ($rows as $r) { ?>
            <tr>
                <td><?= $r->no_transaksi; ?></td>
                <td><?= htmlspecialchars($r->user_name); ?></td>
                <td><?= $r->jenjang ? $r->jenjang : '-'; ?></td>
                <td><?= $r->kelas ? htmlspecialchars($r->kelas) : '-'; ?></td>
                <td><?= $r->bulan; ?></td>
                <td><?= $r->count_bill ? intval($r->count_bill) : 1; ?></td>
                <td class="num"><?= kd_pdf_rupiah($r->total_biaya); ?></td>
                <td class="num"><?= kd_pdf_rupiah($r->total_biaya * max(1, intval($r->count_bill))); ?></td>
                <td><?= kd_pdf_status($r->status_transaksi); ?></td>
                <td><?= kd_pdf_kategori($r->kategori); ?></td>
                <td><?= $r->updated_at ? $r->updated_at : '-'; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</body>
</html>
