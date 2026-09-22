<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kartu Peserta</title>
</head>
<body>
	<hr style="border-width: 2px;">
		<table border="0">
			<tr>
				<td style="width: 150;"><img style="width:180px;height:45px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_new.png'; ?>" /></td>
				<td style="width: 400;text-align: center;font-weight:bold;font-size: 13pt;">KARTU PESERTA<br/>PENERIMAAN SISWA BARU TA <?= $tahun_ajaran_psb; ?><br/><?php echo ($tipe_siswa == "ft") ? 'FRANCE TRACK SMA' : strtoupper($tipe_siswa); ?> LABSCHOOL CIBUBUR</td>
				<td style="width: 150;border-left:1px;"><p style="text-align: center;"><span>Nomor Peserta</span><br><span style="font-weight: bold;font-size:13pt;"><?php echo $siswa->no_peserta; ?></span></p></td>
			</tr>
		</table>
	<hr style="border-width: 2px;">
	
	<table border="0">
		<tr>
			<td style="border-right: 1px;height:200px;vertical-align: top;padding: 10px 10px;" rowspan="2">
				<table border="0">
					<tr>
						<!--<td style="border:1px solid black;padding: 90px 50px;font-weight:bold;">Photo</td>-->
						<td><img style="width: 130px; height: 200px; object-fit: fill; image-orientation:none;" src="<?php echo FCPATH . '/uploads/siswa_'.$tipe_siswa.'/'.$siswa->foto_peserta; ?>" /></td>
					</tr>
				</table>
			</td>
			<td style="padding: 10px 10px;">
				<table border="0">
					<tr>
						<td>Nama Lengkap Calon Siswa</td>
						<td>:</td>
						<td><?php echo $siswa->nama_lengkap; ?></td>
					</tr>
					<tr>
						<td>Jenis Kelamin</td>
						<td>:</td>
						<td><?php echo $siswa->jenis_kelamin; ?></td>
					</tr>
					<tr>
						<td>Sekolah Asal</td>
						<td>:</td>
						<td><?php echo $siswa->sekolah_asal; ?></td>
					</tr>
					<tr>
						<td>Alamat Rumah</td>
						<td>:</td>
						<td><?php echo wordwrap($siswa->alamat, 40, "<br/>", false); ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px;">
				<table border="1" style="text-align: center;width:450;font-size: 10pt;">
					<tr>
						<td style="font-weight:bold;font-size:14pt;padding:10px;" colspan="2"><?php echo $title; ?></td>
					</tr>
					<tr>
						<td style="padding:10px;" colspan="2"><?php echo $hari_ujian.", ".$tgl_ujian; ?></td>
					</tr>
					<tr>
						<td style="padding:10px;width: 450px;" colspan="2"><p><?php echo $catatan_kp->keterangan_ujian; ?></p></td>
					</tr>
					<tr>
						<td style="padding:10px;">Waktu</td>
						<td style="padding:10px;">Agenda</td>
					</tr>
					<?php 
						foreach ($waktu_materi as $key => $value) {
							echo "<tr>";
							echo "<td style='padding:10px;'>".date("H:i", strtotime($value->waktu_mulai))." - ".date("H:i", strtotime($value->waktu_selesai))." WIB</td>";
							echo "<td style='padding:10px;'>".$value->materi."</td>";
							echo "</tr>";
						}
					?>
				</table>
			</td>
		</tr>
	</table>
	<!--<hr>
	<table border="0" >
		<tr>
			<td colspan="2" style="font-weight: bold;font-size: 14pt;text-align: center;">
				Pilihan Sekolah
			</td>
		</tr>
		<tr>
			<td style="width:400; padding-top: 20px;">&nbsp;</td>
			<td style="width: 300;text-align: center;">Sekolah</td>
		</tr>
		<tr>
			<td style="width: 400; padding-top: 20px;"></td>
			<td style="width: 300;text-align: center;"><?php echo ($tipe_siswa == "ft") ? 'France Track SMA' : strtoupper($tipe_siswa); ?> LABSCHOOL CIBUBUR</td>
		</tr>
	</table>-->
	<hr>
	<table border="0" >
		<tr>
			<td colspan="2" style="font-weight: bold;font-size: 14pt;text-align: center;">
				Peserta wajib menyiapkan
			</td>
		</tr>
		<tr>
			<td style="font-size: 10pt;"><?php echo $catatan_kp->catatan_persiapan; ?></td>
		</tr>
	</table>
	<hr>
	<table style="padding-bottom: 10px;">
		<tr>
			<td>
				<table border="0" style="width: 200;">
					<tr>
						<td colspan="2" style="font-weight: bold;font-size: 14pt;text-align: center;">
							Hal-hal yang harus diperhatikan:
						</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;">
							<?php echo $catatan_kp->catatan_perhatikan; ?>
						</td>
					</tr>
				</table>
			</td>
			<td style="padding-left: 10px;">
				<table border="0" style="width: 200;">
					<tr>
						<td colspan="2" style="font-weight: bold;font-size: 14pt;text-align: center;">
							Kontak Kami
						</td>
					</tr>
					<?php 
					if (!empty($kontak)) {
						foreach ($kontak as $key => $value) {
							echo "<tr style='font-size: 10pt;'>";
							echo "<td>".$value->tipe."</td>";
							echo "<td>:</td>";
							echo "<td>".$value->kontak."</td>";
							echo "</tr>";
						}
					}
					?>
				</table>
				(Pelayanan di jam kerja pukul 07.00 - 16.00 WIB hari kerja)
			</td>
		</tr>
	</table>
	<hr style="border-width: 3px;">
</body>
</html>