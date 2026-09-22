<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SLIP <?= strtoupper($slip->jenis).' '.$slip->periode. ' '.str_replace('_', ' ', $slip->npp); ?></title>
</head>
<body style="font-size:10pt;">
	<?php $tahun_ajaran = $rapor->tahun_ajaran; ?>
        <table border="0">
            <tr>
                <td style="width: 180">LABSCHOOL CIBUBUR</td>
                <td style="width: 350">PERIODE : <?php echo strtoupper($slip->periode); ?></td>
                <td style="width: 110;">No. KWITANSI : </td>
                <td style="width: 80;text-align:right"><?php echo $slip->kwitansi_gaji; ?></td>
            </tr>
        </table>

		<table border="0" cellspacing="0" style="margin-top: 20px;margin-left:0px;">
			<tr>
				<td style="width: 300; text-align: left;">Nama : <?php echo $slip->nama_lengkap; ?></td>
				<td style="width: 100;">&nbsp;</td>
				<td style="width: 300; text-align: left;">Jabatan : <?php echo $slip->jabatan; ?></td>
			</tr>
            <tr>
                <td style="width: 300; text-align: left;">NPP : <?php echo $slip->npp; ?></td>
                <td style="width: 100;">&nbsp;</td>
                <td style="width: 300; text-align: left;">Gol : <?php echo $slip->golongan; ?></td>
            </tr>
		</table>

        <table border="0" cellspacing="0" style="margin-top: 20px;margin-left:0px;">
            <tr>
                <td style="vertical-align: top">
                    <table border="0" cellspacing="0">
                        <tr>
                            <td style="width: 165; text-align: left;">PENGHASILAN</td>
                            <td style="width: 20;">&nbsp;</td>
                            <td style="width: 70;">&nbsp;</td>
                            <td style="width: 110;">&nbsp;</td>
                        </tr>
                        <?php
                            foreach ($penghasilan as $key => $value) {
                        ?>
                            <tr>
                                <td style="width: 130; text-align: left; padding-left:30px;"><?= $value['tunjangan']; ?></td>
                                <td style="width: 20;">Rp </td>
                                <td style="width: 70;text-align:right;"><?= $value['jumlah']; ?></td>
                                <td style="width: 110;">&nbsp;</td>
                            </tr>
                        <?php
                            }
                        ?>
                    </table>
                </td>
                <td style="vertical-align: top">
                    <table border="0" cellspacing="0">
                        <tr>
                            <td style="width: 130; text-align: left;">POTONGAN</td>
                            <td style="width: 20;">&nbsp;</td>
                            <td style="width: 70;">&nbsp;</td>
                            <td style="width: 100;">&nbsp;</td>
                        </tr>
                        <?php
                            foreach ($potongan as $key => $value) {
                        ?>
                            <tr>
                                <td style="width: 147; text-align: left; padding-left:30px;"><?= $value['tunjangan']; ?></td>
                                <td style="width: 20;">Rp </td>
                                <td style="width: 70;text-align:right;"><?= $value['jumlah']; ?></td>
                                <td style="width: 100;">&nbsp;</td>
                            </tr>
                        <?php
                            }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
        

        <table border="0" cellspacing="0" style="margin-left:0px;margin-top: 10px;">
            <tr>
                <td style="width: 188; text-align: left;">A. Total Penghasilan</td>
                <td style="width: 22;">Rp</td>
                <td style="width: 70; text-align: right;"><?= $slip->total_penghasilan_tunjangan; ?></td>
                <td style="width: 110;">&nbsp;</td>
                <td style="width: 205; text-align: left;">B. Total Potongan</td>
                <td style="width: 20;">Rp</td>
                <td style="width: 70; text-align: right;"><?= $slip->total_potongan_tunjangan; ?></td>
            </tr>
        </table>

        <table border="0" cellspacing="0" style="margin-top: 20px;">
            <tr>
                <td style="width: 188; text-align: left;">Total diterima (A-B) </td>
                <td style="width: 22;">Rp</td>
                <td style="width: 70; text-align: right;"><?= $slip->total_diterima_tunjangan; ?></td>
            </tr>
        </table>

    <table border="0" cellspacing="0" style="margin-top: 10px;margin-left: 0px;text-align: left;">
        <tr>
            <td style="width: 400">&nbsp;</td>
            <td style="width: 200;">Kota Bekasi, <?php echo $slip->tanggal_tunjangan; ?></td>
        </tr>
    </table>

    <table border="0" cellspacing="0" style="margin-top:10px;text-align: left;">
        <tr>
            <td style="width: 300; padding-left: 30px;">Lunas dibayar</td>
        </tr>
        <tr>
            <td style="width: 300; padding-left: 30px;">Bendahara Labschool Cibubur</td>
        </tr>
    </table>

    <table border="0" cellspacing="0" style="margin-top: 70px;text-align: left;">
        <tr>
            <td style="width: 340; padding-left: 30px;">Ahmad Badarudin, SE</td>
            <td style="width: 300; padding-left: 5px;"><?= $slip->nama_lengkap; ?></td>
        </tr>
    </table>

</body>
</html>