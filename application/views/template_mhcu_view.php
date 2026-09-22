<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
/**
 * application/views/template_mhcu_view.php
 *
 * Report PDF "Hasil MHCU Individu", dirender via html2pdf.
 * Kontrak data:
 *   $nama, $unit, $jabatan, $tanggal_pemeriksaan  (string)
 *   $profil_mental  (array of array(aspek, status_warna, kategori, deskripsi))
 *   $psikososial    (array of array(aspek, status_warna, kategori, deskripsi))
 *   $kesimpulan     (array(warna, profil_warna, label, deskripsi_profil, rekomendasi))
 */

function mhcu_warna_hex($kode_warna)
{
    $map = array(
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
    return isset($map[$kode_warna]) ? $map[$kode_warna] : '#999999';
}

function mhcu_warna_text($kode_warna)
{
    // Putih utk warna gelap, hitam utk warna terang
    $gelap = array('merah_tua', 'merah_muda', 'coklat_orange', 'orange_tua', 'hijau_tua', 'kuning');
    return in_array($kode_warna, $gelap, TRUE) ? '#ffffff' : '#222222';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.45;
            padding-bottom: 30px;
        }
        /* ===== HEADER BANNER ===== */
        .header-banner {
            background-color: #1f3a5f;
            color: #ffffff;
            padding: 8px 14px;
            border-bottom: 3px solid #c2410c;
            margin-bottom: 10px;
            width: 700px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        .header-subtitle {
            font-size: 10px;
            font-style: italic;
            color: #d1d5db;
            margin: 0;
        }
        .header-periode {
            font-size: 10px;
            color: #fbbf24;
            margin-top: 3px;
        }
        /* ===== PROFIL CARD ===== */
        .profil-card {
            border: 1px solid #e5e7eb;
            border-left: 4px solid #1f3a5f;
            background-color: #f9fafb;
            padding: 8px 14px;
            margin-bottom: 10px;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table td {
            padding: 2px 6px;
            font-size: 11px;
            vertical-align: top;
        }
        table.info-table td.label {
            width: 140px;
            font-weight: bold;
            color: #4b5563;
        }
        /* ===== SECTION TITLE ===== */
        h2.section-title {
            font-size: 12px;
            color: #1f3a5f;
            padding: 4px 10px;
            margin: 10px 0 6px 0;
            border-left: 4px solid #c2410c;
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* ===== TABEL ASPEK ===== */
        table.aspek-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            page-break-inside: avoid;
            height: auto;
        }
        table.aspek-table th {
            background-color: #1f3a5f;
            color: #ffffff;
            font-size: 11px;
            padding: 5px 6px;
            text-align: left;
            font-weight: bold;
        }
        table.aspek-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: middle;
        }
        table.aspek-table tr.alt td {
            background-color: #f9fafb;
        }
        /* ===== STATUS BADGE (HTML2PDF compatible) ===== */
        .kategori-badge {
            background-color: #e5e7eb;
            color: #ffffff;
            padding: 4px 12px;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
        }
        .kategori-badge--hijau  { background-color: #28A745; }
        .kategori-badge--kuning { background-color: #F0AD4E; }
        .kategori-badge--orange { background-color: #FD7E14; }
        .kategori-badge--merah  { background-color: #DC3545; }
        /* ===== STATUS LABEL ===== */
        .status-label {
            display: block;
            font-size: 11px;
            font-weight: bold;
        }
        .status-kategori-detail {
            display: block;
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }
        /* ===== PROGRESS BAR PSIKOSOSIAL ===== */
        .psiko-bar-row {
            margin-bottom: 4px;
            page-break-inside: avoid;
        }
        .psiko-bar-header {
            display: inline;
            font-size: 10px;
            font-weight: bold;
        }
        .psiko-bar-meta {
            display: inline;
            font-size: 9px;
            color: #4b5563;
        }
        .psiko-bar-track {
            width: 100%;
            height: 10px;
            background-color: #e5e7eb;
            border: 1px solid #d1d5db;
            margin-top: 2px;
        }
        .psiko-bar-fill {
            height: 100%;
        }
        .psiko-bar-fill--hijau  { background-color: #28A745; }
        .psiko-bar-fill--kuning { background-color: #F0AD4E; }
        .psiko-bar-fill--orange { background-color: #FD7E14; }
        .psiko-bar-fill--merah  { background-color: #DC3545; }
        .psiko-bar-deskripsi {
            display: block;
            font-size: 8px;
            color: #6b7280;
            font-style: italic;
            margin-top: 1px;
            width: 150px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        /* ===== KESIMPULAN CARD ===== */
        table.kesimpulan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        table.kesimpulan-table td {
            border: 1px solid #e5e7eb;
            padding: 10px 10px;
            font-size: 10px;
            vertical-align: top;
        }
        td.profil-cell {
            width: 22%;
            text-align: center;
            font-weight: bold;
            word-wrap: break-word;
            overflow-wrap: break-word;
            overflow: hidden;
            vertical-align: middle;
            padding: 0;
            max-width: 22%;
        }
        td.profil-cell .profil-label-wrap {
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            line-height: 1.15;
            font-size: 10px;
            font-weight: bold;
            color: inherit;
        }
        .profil-label {
            display: inline-block;
            font-size: 10px;
            font-weight: bold;
            line-height: 1.15;
            vertical-align: middle;
            color: #ffffff;
        }
        .col-title {
            color: #1f3a5f;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        /* ===== NOTES BOX ===== */
        .notes-box {
            margin-top: 10px;
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #6b7280;
            background-color: #fafafa;
            font-size: 9px;
            font-style: italic;
            color: #4b5563;
        }
        .notes-box strong { color: #1f2937; }
        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            font-style: italic;
        }
        /* ===== DOKUMEN RAHASIA ===== */
        .dokumen-rahasia {
            position: absolute;
            top: 0;
            right: 0;
            border: 1px solid #000000;
            background-color: #ffffff;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            z-index: 999;
            margin-top: 0px;
        }
        /* ===== TANDA TANGAN ===== */
        .ttd-block {
            text-align: right;
            font-size: 10px;
            line-height: 1.4;
            color: #1f2937;
            margin-top: 8px;
            page-break-inside: avoid;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        /* ===== MHCU CARE LINK BOX ===== */
        .care-box {
            margin-top: 6px;
            padding: 5px 7px;
            border: 1px solid #cc6677;
            border-left: 4px solid #cc6677;
            background-color: #fdeef2;
            page-break-inside: avoid;
            font-size: 8px;
            line-height: 1.3;
            color: #1f2937;
        }
        .care-box h3 {
            font-size: 10px;
            color: #b84d5e;
            margin: 0 0 3px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .care-box p {
            margin: 0 0 3px 0;
            text-align: justify;
        }
        .care-link {
            display: block;
            margin-top: 0px;
            margin-left: 5px;
            padding: 0px 5px;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            font-family: Arial, Helvetica, sans-serif;
            text-decoration: underline;
            font-weight: bold;
            font-size: 10px;
            color: #1f3a5f;
            word-break: break-all;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <div class="header-title">Mental Health Check Up Report</div>
        <div class="header-subtitle">Labschool Cibubur's Teacher and Staff Wellbeing Program</div>
        <?php if (!empty($periode)): ?>
        <div class="header-periode">Periode: <?php echo htmlspecialchars($periode); ?></div>
        <?php endif; ?>
    </div>
    <div class="dokumen-rahasia">DOKUMEN RAHASIA</div>

    <div class="profil-card">
        <table class="info-table">
            <tr>
                <td class="label">Nama</td>
                <td>: <?php echo htmlspecialchars($nama); ?></td>
            </tr>
            <tr>
                <td class="label">Unit</td>
                <td>: <?php echo htmlspecialchars($unit); ?></td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td>: <?php echo htmlspecialchars($jabatan); ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal Pemeriksaan</td>
                <td>: <?php echo htmlspecialchars($tanggal_pemeriksaan); ?></td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">Profil Mental Health</h2>
    <table class="aspek-table">
        <tr>
            <th style="width:25%;">Aspek</th>
            <th style="width:20%;">Status</th>
            <th style="width:55%;">Deskripsi</th>
        </tr>
        <?php
        $emoji_map = array(
            'hijau'  => '<span style="color:#28A745;font-size:30px;vertical-align:middle;line-height:1;">&bull;</span>',
            'kuning' => '<span style="color:#F0AD4E;font-size:30px;vertical-align:middle;line-height:1;">&bull;</span>',
            'orange' => '<span style="color:#FD7E14;font-size:30px;vertical-align:middle;line-height:1;">&bull;</span>',
            'merah'  => '<span style="color:#DC3545;font-size:30px;vertical-align:middle;line-height:1;">&bull;</span>',
        );
        $label_map = array(
            'hijau'  => 'Baik',
            'kuning' => 'Ringan',
            'orange' => 'Sedang',
            'merah'  => 'Risiko Tinggi',
        );
        $i = 0;
        foreach ($profil_mental as $row):
            $i++;
            $sw = $row['status_warna'];
            // Override: saat is_krisis=1, Depression Risk dipaksa merah
            if (!empty($is_krisis) && isset($row['aspek']) && $row['aspek'] === 'Depression Risk') {
                $sw = 'merah';
            }
            $emoji = isset($emoji_map[$sw]) ? $emoji_map[$sw] : '?';
            $status_label = isset($label_map[$sw]) ? $label_map[$sw] : $row['kategori'];
            $row['deskripsi'] = mhcu_apply_sapaan($row['deskripsi'], isset($gender) ? $gender : '');
        ?>
        <tr<?php echo ($i % 2 == 0) ? ' class="alt"' : ''; ?>>
            <td><strong><?php echo htmlspecialchars($row['aspek']); ?></strong></td>
            <td style="vertical-align:middle;line-height:1;">
                <?php echo $emoji; ?>
                <span class="status-label" style="vertical-align:middle;line-height:1;"> <?php echo htmlspecialchars($status_label); ?></span>
            </td>
            <td style="vertical-align:middle; text-align:left; padding-top:4px;"><?php echo nl2br(htmlspecialchars(wordwrap($row['deskripsi'], 80, "\n", true))); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2 class="section-title">Work Psychosocial Environment</h2>
    <?php
    $psiko_emoji_map = array(
        'hijau'  => '&raquo; ',
        'kuning' => '&raquo; ',
        'orange' => '&raquo; ',
        'merah'  => '&raquo; ',
    );
    // Label kategori psiko diturunkan dari status_warna (yg sudah benar dari mhcu_band_kategori).
    // Data $row['kategori'] dari mhcu_instrument_score stale, jangan dipakai.
    $psiko_label_map = array(
        'hijau'  => 'Baik',
        'kuning' => 'Risiko Sedang',
        'orange' => 'Sedang',
        'merah'  => 'Risiko Tinggi',
    );
    foreach ($psikososial as $row):
        $sw = $row['status_warna'];
        $pct = isset($row['persentase']) ? $row['persentase'] : 0;
        $skor = isset($row['skor']) ? $row['skor'] : 0;
        $skor_maks = isset($row['skor_maks']) ? $row['skor_maks'] : 12;
        $emoji = isset($psiko_emoji_map[$sw]) ? $psiko_emoji_map[$sw] : '';
        $status_label = isset($psiko_label_map[$sw]) ? $psiko_label_map[$sw] : '-';
        $deskripsi = isset($row['deskripsi']) ? mhcu_apply_sapaan($row['deskripsi'], isset($gender) ? $gender : '') : '';
        $is_krisis = !empty($is_krisis) ? 1 : 0;
    ?>
    <div class="psiko-bar-row">
        <span class="psiko-bar-header"><?php echo $emoji; ?> <?php echo htmlspecialchars($row['aspek']); ?></span>
        <span class="psiko-bar-meta"> <?php echo htmlspecialchars($status_label); ?> (<?php echo intval($skor); ?>/<?php echo intval($skor_maks); ?>)</span><br>
        <div class="psiko-bar-track">
            <div class="psiko-bar-fill psiko-bar-fill--<?php echo $sw; ?>" style="width: <?php echo ($pct > 0 ? $pct : ($skor == 0 ? 10 : 0)); ?>%;"></div>
        </div>
        <?php if (!empty($deskripsi)): ?>
        <span class="psiko-bar-deskripsi"><?php echo nl2br(htmlspecialchars(wordwrap($deskripsi, 80, "\n", true))); ?></span>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <h2 class="section-title">Kesimpulan</h2>
<?php
$kes_warna = $kesimpulan['warna'];
$kes_hex = isset($kesimpulan['profil_warna']) ? $kesimpulan['profil_warna'] : mhcu_warna_hex($kes_warna);
$kes_text = mhcu_warna_text($kes_warna);
?>
    <table class="kesimpulan-table">
        <tr>
            <td class="profil-cell" style="background-color: <?php echo $kes_hex; ?>; color: #ffffff; width:22%;">
                <span class="profil-label"><?php echo nl2br(htmlspecialchars(wordwrap($kesimpulan['label'], 18, "\n", true))); ?></span>
            </td>
            <td style="width:39%;padding:4px 6px;text-align:justify;font-size:8px;">
                <div class="col-title">Deskripsi</div>
                <?php echo nl2br(htmlspecialchars(mhcu_apply_sapaan($kesimpulan['deskripsi_profil'], isset($gender) ? $gender : ''))); ?>
            </td>
            <td style="width:39%;padding:4px 6px;text-align:justify;font-size:8px;">
                <div class="col-title">Rekomendasi</div>
                <?php echo nl2br(htmlspecialchars(mhcu_apply_sapaan($kesimpulan['rekomendasi'], isset($gender) ? $gender : ''))); ?>
            </td>
        </tr>
    </table>

    <?php if (!empty($show_mhcu_care) && !empty($mhcu_care_url)): ?>
    <div class="care-box">
        <h3>MHCU Care - Konseling Psikolog Profesional</h3>
        <p>
            Dari hasil MHCU, Bapak/Ibu disarankan untuk mengikuti MHCU Care berupa konseling bersama profesional psikolog klinis yang akan difasilitasi oleh Labschool Cibubur. MHCU Care ini bertujuan untuk membantu dan sebagai bentuk perawatan lanjutan untuk Bapak/Ibu yang membutuhkan agar bisa kembali mencapai kesejahteraan psikologis yang optimal.
        </p>
        <p>Silahkan klik link berikut untuk informasi lebih lanjut: <a href="<?php echo htmlspecialchars($mhcu_care_url); ?>" class="care-link" style= target="_blank">&raquo; Link MHCU Care &laquo;</a></p>
        <p>Jika ada pertanyaan dan informasi lebih lanjut sebelum konseling bisa menghubungi helpdesk <a href="https://wa.me/6282133762818?text=<?php echo rawurlencode('Halo, Saya "' . $nama . '" ingin konfirmasi pengisian jadwal konsultasi MHCU Care sudah dilakukan.'); ?>" target="_blank" rel="noopener" style="color:#1f3a5f;">082133762818</a> (dewi)</p>
    </div>
    <?php endif; ?>

    <div class="notes-box">
        <strong>*Catatan:</strong><br>
        Hasil MHCU merupakan skrining untuk memotret kondisi kesehatan mental dan faktor psikososial kerja pada saat
        pemeriksaan dilakukan. Hasil ini bukan merupakan diagnosis gangguan mental.<br><br>
        Apabila hasil menunjukkan risiko sedang atau tinggi, atau apabila Anda merasakan keluhan yang menetap maupun semakin mengganggu aktivitas
        sehari-hari, disarankan untuk berkonsultasi dengan psikolog atau tenaga kesehatan mental yang kompeten.
    </div>

    <div class="ttd-block">
        Kota Bekasi, <?php echo htmlspecialchars($tanggal_pemeriksaan); ?><br>
        Psikolog,<br><br>
        <span class="ttd-nama">Dewi Yulia Nurul Majid, M.Psi., Psikolog</span><br>
        SIPP: 20210373-2023-02-1051
    </div>

</body>
</html>