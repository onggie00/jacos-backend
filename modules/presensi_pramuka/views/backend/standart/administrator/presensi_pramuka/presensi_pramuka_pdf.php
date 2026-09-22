<style type="text/css">
    * {
        font-size: 10px;
        font-family: Arial, Helvetica, sans-serif;
    }
    h2, p {
        margin: 0 0 5px 0;
        padding: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    table th, table td {
        border: 1px solid #999;
        padding: 5px;
    }
    table th {
        background-color: #f2f2f2;
        text-align: center;
        font-weight: bold;
    }
    .text-center {
        text-align: center;
    }
</style>
<page>
    <h2>LAPORAN PRESENSI PRAMUKA</h2>
    <p>
        <?php
        $info = array();
        if (!empty($filters['jenjang'])) $info[] = 'Jenjang: ' . strtoupper($filters['jenjang']);
        if (!empty($filters['kelas'])) $info[] = 'Kelas: ' . $filters['kelas'];
        if (!empty($filters['start_date'])) $info[] = 'Dari: ' . $filters['start_date'];
        if (!empty($filters['end_date'])) $info[] = 'Sampai: ' . $filters['end_date'];
        echo implode(' | ', $info);
        ?>
    </p>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Tanggal</th>
                <th width="8%">Hari</th>
                <th width="8%">NIS</th>
                <th width="18%">Nama Siswa</th>
                <th width="8%">Kelas</th>
                <th width="8%">Status</th>
                <th width="7%">Kehadiran</th>
                <th width="10%">Atribut</th>
                <th width="10%">Keaktifan</th>
                <th width="10%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($presensi_pramukas)): ?>
                <?php $no = 1; foreach ($presensi_pramukas as $p): ?>
                <?php
                $nis = isset($nis_map[strtolower($p->jenjang)][(int) $p->id_siswa_aktif])
                    ? $nis_map[strtolower($p->jenjang)][(int) $p->id_siswa_aktif]
                    : '-';
                $total = (float) $p->total_nilai;
                $pred = get_predikat_pramuka($total);
                ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $p->tanggal; ?></td>
                    <td><?= $p->hari; ?></td>
                    <td><?= $nis; ?></td>
                    <td><?= $p->nama_lengkap; ?></td>
                    <td class="text-center"><?= $p->kelas; ?></td>
                    <td class="text-center"><?= $p->status_hadir; ?></td>
                    <td class="text-center"><?= $p->kehadiran; ?></td>
                    <td><?= $p->status_kelengkapan; ?> (<?= $p->kelengkapan; ?>)</td>
                    <td><?= $p->status_keaktifan; ?> (<?= $p->keaktifan; ?>)</td>
                    <td class="text-center"><strong><?= $p->total_nilai; ?></strong> (<?= $pred['predikat']; ?>)</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data presensi pramuka.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <page_footer>
        [[page_cu]]/[[page_nb]]
    </page_footer>
</page>
