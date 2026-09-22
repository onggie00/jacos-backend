<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: Times, serif;
            font-size: 12pt;
            color: #000;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .page {
            padding: 40px 60px;
        }
        .title {
            text-align: left;
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .kota-tanggal {
            margin-bottom: 14px;
        }
        .tujuan {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .pembuka {
            margin-bottom: 10px;
        }
        .data-diri {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .data-diri table {
            border-collapse: collapse;
        }
        .data-diri td {
            padding: 2px 0;
        }
        .col-label { width: 160px; }
        .col-titik  { width: 16px; }
        .isi-surat {
            margin-bottom: 20px;
            text-align: justify;
            line-height: 1.7;
        }
        .penutup {
            margin-bottom: 40px;
            text-align: justify;
            line-height: 1.7;
        }
        .ttd        { margin-bottom: 30px; }
        .ttd-space  { height: 70px; }

        /* ── TABEL BAWAH ── */
        .tabel-bawah {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }
        .bold { font-weight: bold; }

        /* List cuti di kolom kiri */
        .list-cuti {
            width: 100%;
            border-collapse: collapse;
        }
        .list-cuti td {
            border: none;
            padding: 1px 0;
        }
        .lc-no   { width: 20px; }
        .lc-sep  { width: 14px; }
        .lc-val  { width: 65px; }
    </style>
</head>
<body>
<div class="page">

    <!-- JUDUL -->
    <div class="title">
        Permintaan <?= htmlspecialchars($submission->submission_name) ?>
    </div>

    <!-- KOTA & TANGGAL -->
    <div class="kota-tanggal">
        <?= date('d F Y', strtotime($submission->created_at)) ?>
    </div>

    <!-- TUJUAN SURAT -->
    <div class="tujuan">
        Yang terhormat<br>
        <?= htmlspecialchars($yth) ?><br>
        <?= htmlspecialchars($submission->head_name) ?><br>
        di &ndash;<br>
        Tempat
    </div>

    <!-- PEMBUKA -->
    <div class="pembuka">
        Yang bertanda tangan di bawah ini:
    </div>

    <!-- DATA DIRI -->
    <div class="data-diri">
        <table>
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-titik">:</td>
                <td><?= htmlspecialchars($submission->nama_lengkap) ?></td>
            </tr>
            <tr>
                <td class="col-label">NIP</td>
                <td class="col-titik">:</td>
                <td><?= htmlspecialchars($submission->npp) ?></td>
            </tr>
            <tr>
                <td class="col-label">Pangkat/Gol. Ruang</td>
                <td class="col-titik">:</td>
                <td><?= !empty($submission->pangkat) ? htmlspecialchars($submission->pangkat) : '-' ?></td>
            </tr>
            <tr>
                <td class="col-label">Jabatan</td>
                <td class="col-titik">:</td>
                <td><?= htmlspecialchars($submission->jabatan) ?></td>
            </tr>
        </table>
    </div>

    <!-- ISI SURAT -->
    <div class="isi-surat">
        <?= $kalimat_permohonan ?>.
    </div>

    <!-- PENUTUP -->
    <div class="penutup">
        <?= $kalimat_penutup ?>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd">
        Hormat saya,<br>
        <div class="ttd-space"></div>
        <u><?= htmlspecialchars($submission->nama_lengkap) ?></u><br>
        NPP. <?= htmlspecialchars($submission->npp) ?>
    </div>

    <!--
        SOLUSI TABEL BAWAH untuk HTML2PDF:
        - HTML2PDF tidak support min-height & nested table dengan baik
        - Gunakan rowspan="2" pada kolom kiri agar setinggi 2 baris kanan
        - Kolom kanan dibuat 2 baris terpisah dengan border-top: 0 pada baris ke-2
          agar garis atas tidak double
        - Gunakan height (bukan min-height) pada td kanan untuk tinggi minimum
    -->
    <table class="tabel-bawah">
        <tr>
            <!-- KOLOM KIRI: rowspan 2 agar setinggi gabungan 2 baris kanan -->
            <td rowspan="2" style="width: 45%; border: 1px solid #000; padding: 8px 10px; vertical-align: top;">
                <div class="bold" style="margin-bottom: 6px;">CATATAN BAGIAN KEPEGAWAIAN</div>
                <div style="margin-bottom: 6px;">Cuti yang telah diambil dalam tahun yang bersangkutan:</div>
                <table class="list-cuti">
                    <tr>
                        <td class="lc-no">1.</td>
                        <td>Cuti Tahunan</td>
                        <td class="lc-sep">:</td>
                        <td class="lc-val">....... hari</td>
                    </tr>
                    <tr>
                        <td class="lc-no">2.</td>
                        <td>Cuti Besar</td>
                        <td class="lc-sep">:</td>
                        <td class="lc-val">....... hari</td>
                    </tr>
                    <tr>
                        <td class="lc-no">3.</td>
                        <td>Cuti Sakit</td>
                        <td class="lc-sep">:</td>
                        <td class="lc-val">....... hari</td>
                    </tr>
                    <tr>
                        <td class="lc-no">4.</td>
                        <td>Cuti Bersalin</td>
                        <td class="lc-sep">:</td>
                        <td class="lc-val">....... hari</td>
                    </tr>
                    <tr>
                        <td class="lc-no">5.</td>
                        <td>Cuti Karena Alasan Penting</td>
                        <td class="lc-sep">:</td>
                        <td class="lc-val">....... hari</td>
                    </tr>
                    <tr>
                        <td class="lc-no">6.</td>
                        <td>Keterangan Lain-lain</td>
                        <td class="lc-sep">:</td>
                        <td class="lc-val"></td>
                    </tr>
                </table>
                <div style="margin-top: 8px; line-height: 2.2;">
                    ............................................<br>
                    ............................................<br>
                    ............................................
                </div>
            </td>

            <!-- KOLOM KANAN BARIS 1: Catatan Atasan -->
            <td style="width: 45%;
                        height: 100px;
                        border-top: 1px solid #000;
                        border-right: 1px solid #000;
                        border-bottom: 1px solid #000;
                        border-left: 1px solid #000;
                        padding: 8px 10px;
                        vertical-align: top;">
                <div class="bold">CATATAN/PERTIMBANGAN ATASAN LANGSUNG:</div>
            </td>
        </tr>
        <tr>
            <!-- KOLOM KANAN BARIS 2: Keputusan Pejabat -->
            <!-- border-top: 0 agar tidak ada garis double dengan baris di atasnya -->
            <td style="width: 45%;
                        height: 100px;
                        border-top: 0;
                        border-right: 1px solid #000;
                        border-bottom: 1px solid #000;
                        border-left: 1px solid #000;
                        padding: 8px 10px;
                        vertical-align: top;">
                <div class="bold">KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI:</div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>