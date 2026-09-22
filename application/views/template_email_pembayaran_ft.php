<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>KWITANSI DAN KARTU PESERTA LABSCHOOL CIBUBUR</title>
</head>
<body style="font-family: arial, sans-serif, Times;">
	<div>
		<p>
			Kepada Yth. 
		</p>
		<p style="font-weight: bold;">
			<?php echo $nama_lengkap; ?>
		</p>
		<p>
			Terimakasih sudah melalukan pembayaran pendaftaran <?php echo $tipe_pendaftaran; ?> Labschool Cibubur, <br/>
			berikut adalah user untuk mengakses data <?php echo $tipe_pendaftaran; ?> Labschool Cibubur :
		</p>
		<div style="padding: 5px 15px;font-weight: bold;">
			<table border="0">
				<tr>
					<td>User</td>
					<td>:</td>
					<td><?php echo $email; ?></td>
				</tr>
				<tr>
					<td>Link Cek Data</td>
					<td>:</td>
					<td><a href="https://psb.labschoolcibubur.sch.id/" target="_blank" >Cek Data Disini</a></td>
				</tr>
			</table>
		</div>
		<p>
			Kami lampirkan juga kartu peserta sebagai bukti proses pendaftaran yang Sah<br/>
			<!--*Selanjutnya silahkan bergabung pada link Grup Whatsapp Untuk calon siswa <?php echo $jenjang; ?> Labschool Cibubur :<br/>
			<a href="https://chat.whatsapp.com/FIl7AtWyJP0HAqfkCIkZPE" target="_blank">Gabung Grup Whatsapp</a>-->
		</p>
		<p style="margin-top:10px;">
			Terima kasih,
		</p>
		<p style="font-weight: bold;font-style: italic;">
			<?php echo $nama_panitia; ?>
		</p>
		<p style="font-weight: bold;font-style: italic;font-size: 10pt;">
			Kontak Humas PSB SD Labschool Cibubur :<br/>
			WhatsApp: 0811-1343-199<br/>
			Kontak Humas PSB SMP Labschool Cibubur :<br/>
			WhatsApp : 0813-1677-7291<br/>
			Kontak Humas PSB SMA Labschool Cibubur :<br/>
			WhatsApp: 0812-9040-6057<br/>
		</p>

	</div>
</body>
</html>