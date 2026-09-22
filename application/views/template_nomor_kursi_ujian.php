<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Nomor Urut Bangku Ujian</title>
</head>

<body style="font-family: times;">
	<div style="padding: 10px;width: 1000px;">
		<div style="margin-left:30px;">
			<p style="text-align: center;">
				<span style="font-size: 24pt;font-weight: bold;">DENAH DUDUK</span><br />
				<span style="font-size: 24pt;font-weight: bold;"><?= $judul_ujian ?></span><br />
				<span style="font-size: 24pt;font-weight: bold;"><?= $ruang->nama_ruang; ?></span><br />
			</p>
		</div>
		<div style="font-size: 12pt; padding-top: 50px; padding-left: 0px; line-height: 1px;">
			<table cellspacing="0" border="0">
				<tr>
					<td>
						<table cellspacing="0" border="1" style="font-size: 12pt;text-align: center;">
							<tr>
								<td style="padding:30px 50px;">PINTU</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="padding-top:20px;">
						<table cellspacing="0" border="1" style="font-size: 12pt;text-align: center;">
							<tr>
								<td style="padding:93px 15px;">PENGAWAS</td>
							</tr>
							<tr>
								<td style="padding:93px 15px;">PENGAWAS</td>
							</tr>
						</table>
					</td>
					<td style="padding-left:30px;padding-top:20px;">
						<table cellspacing="0" border="1" style="font-size: 14pt;text-align: center;">
							<?php
							if (!empty($peserta)) {
								$no = 1;
								$total_siswa = count($peserta);
								$total_siswa_baris = 10;
								foreach ($peserta as $key => $value) {
									if ($no == 1) {
										echo "<tr>";
										echo "<td style='padding:40px 10px;'>".$value->nomor_peserta_ujian."</td>";
										$no++;
									}
									else if($no == $total_siswa_baris){
										echo "<td style='padding:40px 10px;'>".$value->nomor_peserta_ujian."</td>";
										echo "</tr>";
										$no=1;
									}
									else{
										echo "<td style='padding:40px 10px;'>".$value->nomor_peserta_ujian."</td>";
										$no++;
									}
									/*if ($key == 0 || $key%4 !=0) {
										echo "<tr>";
										echo "<td style='padding:30px;'>".$value->nomor_peserta_ujian."</td>";
									}
									else if($key%4 == 0){
										echo "</tr>";
									}
									else{
										echo "<td style='padding:30px;'>".$value->nomor_peserta_ujian."</td>";
									}*/
								}
								if ($no != $total_siswa_baris) {
									for ($i=1; $i <= $total_siswa_baris ; $i++) {
										if ($no != $total_siswa_baris) {
											echo "<td style='padding:40px 10px;'>&nbsp;</td>";
											$no++;
										}
										else{
											echo "<td style='padding:40px 10px;'>&nbsp;</td>";
											echo "</tr>";
											break;
										}
									}
								}
							}
							?>
							<tr>
								
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
		</div>

	</div>
</body>

</html>