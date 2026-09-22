<style type="text/css">
    * {
        font-size: 11px;
        font-family: Arial, Helvetica, sans-serif;
    }
    h2, h4, p {
        margin: 0 0 5px 0;
        padding: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    table th, table td {
        border: 1px solid #999;
        padding: 8px;
    }
    table th {
        background-color: #f2f2f2;
        text-align: left;
    }
    .text-center {
        text-align: center;
    }
</style>
<page>
    <h2>DETAIL PRESENSI PRAMUKA</h2>
    <h4>Labschool Kaizen</h4>
    <hr>

    <table>
        <tr>
            <th width="30%">Tanggal & Hari</th>
            <td><?= $presensi_pramuka->hari; ?>, <?= $presensi_pramuka->tanggal; ?></td>
        </tr>
        <tr>
            <th>NIS</th>
            <td><?= $nis; ?></td>
        </tr>
        <tr>
            <th>Nama Siswa</th>
            <td><strong><?= $presensi_pramuka->nama_lengkap; ?></strong></td>
        </tr>
        <tr>
            <th>Jenjang / Kelas</th>
            <td><?= $presensi_pramuka->jenjang; ?> / <?= $presensi_pramuka->kelas; ?></td>
        </tr>
        <tr>
            <th>Status Kehadiran</th>
            <td><?= $presensi_pramuka->status_hadir; ?> (Nilai: <?= $presensi_pramuka->kehadiran; ?>)</td>
        </tr>
        <tr>
            <th>Kelengkapan Atribut</th>
            <td><?= $presensi_pramuka->status_kelengkapan; ?> (Nilai: <?= $presensi_pramuka->kelengkapan; ?>)</td>
        </tr>
        <tr>
            <th>Keaktifan</th>
            <td><?= $presensi_pramuka->status_keaktifan; ?> (Nilai: <?= $presensi_pramuka->keaktifan; ?>)</td>
        </tr>
        <tr style="background-color:#f9f9f9">
            <th>Total Nilai Akhir</th>
            <td><strong style="font-size:14px"><?= $presensi_pramuka->total_nilai; ?></strong> (Predikat <?= $predikat; ?> - <?= $deskripsi; ?>)</td>
        </tr>
        <tr>
            <th>Diinput / Diupdate Oleh</th>
            <td><?= $presensi_pramuka->updated_by; ?></td>
        </tr>
    </table>

    <page_footer>
        [[page_cu]]/[[page_nb]]
    </page_footer>
</page>
