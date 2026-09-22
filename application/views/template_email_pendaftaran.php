<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SLIP PEMBAYARAN PENDAFTARAN SISWA BARU</title>
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
			Terima kasih sudah melakukan proses pendaftaran <?php echo $tipe_pendaftaran; ?> Labschool Cibubur,<br/>
			Berikutnya, silakan melakukan proses pembayaran pendaftaran dengan tata cara sebagai berikut :
		</p>
		<div style="padding: 5px 15px;font-weight: bold;">
			<table border="0">
				<tr>
					<td>Nama</td>
					<td>:</td>
					<td><?php echo $nama_lengkap; ?></td>
				</tr>
				<tr>
					<td>Jenjang</td>
					<td>:</td>
					<td><?php echo $jenjang; ?></td>
				</tr>
				<tr>
					<td>Virtual Account</td>
					<td>:</td>
					<td><?php echo $transaksi->va_number; ?></td>
				</tr>
				<tr>
					<td>Bank</td>
					<td>:</td>
					<td><?php echo $transaksi->nama_bank; ?></td>
				</tr>
				<tr>
					<td>Jumlah</td>
					<td>:</td>
					<td><?php echo "Rp ".number_format($transaksi->total_biaya, 0, "", "."); ?></td>
				</tr>
			</table>
		</div>
		<p>
			<?php 
				if ($daftar_ulang != "1") {
			?>
					Syarat untuk mendapatkan kartu peserta :<br/>
					1. Melakukan pembayaran pendaftaran melalui tata cara yang telah diberikan di atas.<br/>
					2. Setelah melakukan pembayaran, kartu peserta akan terkirim secara otomatis ke email yang digunakan
					saat pendaftaran sebelumnya / bisa juga di akses melalui link cek data <a href="https://psb.labschoolcibubur.sch.id/" target="_blank">https://psb.labschoolcibubur.sch.id</a> <br/>
					3. Silakan mencetak kartu peserta sebagai bukti proses pendaftaran yang Sah.
			<?php
				}
				else{
			?>
					Syarat untuk mendapatkan kartu siswa sementara :<br/>
					1. Melakukan pembayaran uang pangkal melalui tata cara yang telah diberikan di atas.<br/>
					2. Setelah melakukan pembayaran, kartu siswa sementara akan terkirim secara otomatis ke email yang digunakan
					saat pendaftaran sebelumnya / bisa juga di akses melalui link cek data <a href="https://psb.labschoolcibubur.sch.id/" target="_blank">https://psb.labschoolcibubur.sch.id</a> <br/>
					3. Silakan mencetak kartu siswa sementara sebagai bukti proses pendaftaran yang Sah.
			<?php
				}
			?>
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