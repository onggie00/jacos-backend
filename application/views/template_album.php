<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ALBUM PESERTA UJIAN</title>
</head>
<body style="font-family: times;">
		<table border="0" style="margin-top: 20px;margin-left: 20px;margin-right: 0px;">
			<tr>
				<td style="width: 700; text-align: center;text-align: center;font-weight:bold;font-size: 13pt;">ALBUM FOTO PESERTA UJIAN</td>
			</tr>
		</table>

		<table border="0.5" style="margin-top: 30px;margin-left: 30px;margin-right: 20px;" cellspacing="0">
			<?php
foreach ($peserta as $row) {
    echo "<tr>";

    foreach ($row as $cell) {

        if (!$cell) {
            echo "<td style='width:100px;padding:5px 18px'>&nbsp;</td>";
            continue;
        }

        $photo = "&nbsp;";
        if (!empty($cell->foto_peserta) && $cell->foto_peserta != "0.jpg") {
            $photo = "<img style='width:100px;height:100px;' src='".base_url('uploads/siswa_'.strtolower($jenjang).'/').$cell->foto_peserta."'/>";
        }

        echo "<td style='text-align:center;font-weight:bold;width:100px;padding:5px 18px'>
                {$photo}<br/><br/>".($cell->nomor_peserta ?: '&nbsp;')."
              </td>";
    }

    echo "</tr>";
}
?>
			<!-- <tr>
				<td style="text-align: left;padding-right: 300px;">Nomor Tagihan (Virtual Account)<br/><span style="font-weight: bold;"></td>
				<td style="text-align: left;">Total Tagihan<br/><span style="font-weight: bold;"></span></td>
			</tr> -->
		</table>

	<table style="margin-top: 20px;margin-left: 30px;margin-right: 20px;">
		<tr>
			<td style="width: 700;font-size: 13pt;text-align: center;font-weight: bold;text-transform: uppercase;text-decoration: underline;">depan</td>
		</tr>
	</table>
	<br/>
	<table border="0" cellspacing="0">
		<tr>
			<td>
				<table border="0.5" cellspacing="0" style="margin-left: 30px;margin-right: 20px;">
					<tr>
						<td style="width: 140;font-size: 13pt;text-align: center;font-weight: bold;text-transform: uppercase;">NO. TES</td>
						<td style="width: 141;font-size: 13pt;text-align: center;text-transform: uppercase;"><?= $peserta_awal ?></td>
						<td style="width: 141;font-size: 13pt;text-align: center;text-transform: uppercase;"><?= $peserta_akhir ?></td>
					</tr>
				</table>

				<table border="0" cellspacing="0">
					<tr>
						<td>
							<table border="0.5" cellspacing="0" style="margin-left: 30px;margin-right: 20px;">
								<tr>
									<td style="width: 220;height: 10px;font-size: 13pt;text-align: center;font-weight: bold;text-transform: uppercase;padding: 10px 0">ruang</td>
								</tr>
							</table>
							<table border="0.5" cellspacing="0" style="margin-left: 30px;margin-right: 20px;">
								<tr>
									<td style="width: 220;height: 30px;font-size: 24pt;text-align: center;font-weight: bold;text-transform: uppercase;padding: 26px 0"><?= $ruangan->nama_ruang ?></td>
								</tr>
							</table>
						</td>
						<td>
							<table border="0.5" cellspacing="0">
								<tr>
									<td style="width: 160;height: 135px;font-size: 11pt;text-align: center;font-weight: bold;text-transform: uppercase;padding: 13px "><?= $ruangan->judul_ujian.' '.$ruangan->tahun_ajaran; ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td >
				<table border="0.5" cellspacing="0" style="margin-right: 20px;">
					<tr>
						<td style="width: 230;height:138px;font-size: 12pt;text-align: left;padding: 18px 0px 18px 10px">
							Cibubur, ........................<br/>
							Kepala <?= ($jenjang == "FT")?  "SMA" : $jenjang; ?> Labschool Cibubur
							<br/>
							<br/>
							<br/>
							<br/>
							<br/>
							<br/>
							<br/>
							<span style="font-weight: bold;"><?= $ruangan->kepala_sekolah; ?></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</body>
</html>