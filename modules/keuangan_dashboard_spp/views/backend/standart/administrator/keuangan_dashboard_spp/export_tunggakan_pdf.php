<?php
function kd_pdf_rupiah($n) {
    return number_format(intval($n), 0, ',', '.');
}
/** "Juli" + "2017/2018" -> "Juli 2017" (Jul-Des = tahun pertama, Jan-Jun = tahun kedua). */
function kd_pdf_bulan_tahun($bulan, $ta) {
    if ($ta !== null && preg_match('/^(\d{4})\/(\d{4})$/', $ta, $m)) {
        $jul_des = array('Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $tahun = in_array($bulan, $jul_des) ? $m[1] : $m[2];
        return $bulan . ' ' . $tahun;
    }
    return $bulan;
}
$kd_logo = FCPATH . 'uploads/logo_labschool_2025.png';
$kd_ta_pertama = !empty($groups) ? $groups[key($groups)]['label'] : '-';
?>
<page backtop="8mm" backbottom="15mm" backleft="8mm" backright="8mm">
    <!-- KOP -->
    <table style="width:100%;">
        <tr>
            <td style="width:24mm;"><img src="<?= $kd_logo; ?>" style="width:20mm;" /></td>
            <td style="text-align:center;">
                <span style="font-size:16px;font-weight:bold;">LABSCHOOL CIBUBUR</span><br />
                <span style="font-size:8px;">Jalan Raya Hankam Kampus Labschool No. 15 - 20 Kelurahan Jatiranggon, Kecamatan Jatisampurna<br />
                Kota Bekasi - Jawa Barat 17432 Telp. 021 84304138, 84304140<br />
                Website : www.labschoolcibubur.sch.id. Email : labscib.ypunj@gmail.com</span>
            </td>
            <td style="width:24mm;"></td>
        </tr>
    </table>
    <div style="border-bottom:2px solid #333333;margin-top:1mm;"></div>

    <!-- JUDUL -->
    <h2 style="text-align:center;font-size:13px;margin:5mm 0 0 0;">LAPORAN KEWAJIBAN ADMINISTRASI SISWA</h2>
    <div style="text-align:center;font-size:10px;margin-bottom:4mm;"><?= $periode; ?></div>

    <!-- IDENTITAS SISWA -->
    <table style="width:100%;font-size:9px;">
        <tr>
            <td style="width:50%;">NIS : <b><?= htmlspecialchars($siswa['nis']); ?></b><br />
                Nama : <b><?= htmlspecialchars($siswa['nama'] !== '' ? $siswa['nama'] : '-'); ?></b></td>
            <td style="width:50%;">Kelas : <b>[<?= $kd_ta_pertama; ?>] <?= htmlspecialchars($siswa['kelas'] !== '' ? $siswa['kelas'] : '-'); ?></b><br />
                Status : <b><?= $siswa['status'] !== '' ? $siswa['status'] : '-'; ?></b></td>
        </tr>
    </table>

    <!-- RINCIAN TUNGGAKAN -->
    <table style="width:100%;border:0.5px solid #D6D3D1;margin-top:3mm;font-size:8.5px;">
        <tr style="background-color:#F5F5F4;">
            <th style="border:0.5px solid #D6D3D1;padding:2px 4px;width:8mm;text-align:left;">No.</th>
            <th style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:left;">Nama Pembayaran</th>
            <th style="border:0.5px solid #D6D3D1;padding:2px 4px;width:24mm;text-align:left;">Bulan</th>
            <th style="border:0.5px solid #D6D3D1;padding:2px 4px;width:24mm;text-align:right;">Tarif/Biaya</th>
            <th style="border:0.5px solid #D6D3D1;padding:2px 4px;width:22mm;text-align:right;">Pembayaran</th>
            <th style="border:0.5px solid #D6D3D1;padding:2px 4px;width:24mm;text-align:right;">Sisa</th>
        </tr>
        <?php if (empty($groups)) { ?>
        <tr>
            <td colspan="6" style="border:0.5px solid #D6D3D1;padding:4px;text-align:center;">Tidak ada tunggakan.</td>
        </tr>
        <?php } $kd_no = 1; foreach ($groups as $kd_g) { foreach ($kd_g['rows'] as $kd_r) { ?>
        <tr>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;"><?= $kd_no++; ?>.</td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;">SPP TAHUN <?= $kd_g['label']; ?></td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;"><?= kd_pdf_bulan_tahun($kd_r->bulan, $kd_g['label']); ?></td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:right;"><?= kd_pdf_rupiah($kd_r->total_biaya); ?></td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:right;">0</td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:right;"><?= kd_pdf_rupiah($kd_r->total_biaya); ?></td>
        </tr>
        <?php } } if (!empty($groups)) { ?>
        <tr style="font-weight:bold;background-color:#FAFAF9;">
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;"></td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;">Jumlah</td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;"></td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:right;"><?= kd_pdf_rupiah($total); ?></td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:right;">0</td>
            <td style="border:0.5px solid #D6D3D1;padding:2px 4px;text-align:right;"><?= kd_pdf_rupiah($total); ?></td>
        </tr>
        <?php } ?>
    </table>

    <page_footer>
        <table style="width:100%;font-size:8px;color:#444444;">
            <tr>
                <td style="text-align:left;width:70%;">Dicetak : <?= $dicetak; ?></td>
                <td style="text-align:right;">Halaman [[page_cu]] dari [[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
</page>
