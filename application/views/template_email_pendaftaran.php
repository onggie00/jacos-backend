<?php $email_title = 'SLIP PEMBAYARAN PENDAFTARAN SISWA BARU'; ?>
<?php $this->load->view('email/_header'); ?>

<p style="margin:0 0 14px 0;">
	Kepada Yth.<br/>
	<strong style="font-size:15px;color:#2C7FB8;"><?php echo $nama_lengkap; ?></strong>
</p>
<p style="margin:0 0 16px 0;">
	Terima kasih sudah melakukan proses pendaftaran <?php echo $tipe_pendaftaran; ?> JAKARTA COSMOPOLITE ISLAMIC SCHOOL.<br/>
	Berikutnya, silakan melakukan proses pembayaran pendaftaran dengan tata cara sebagai berikut :
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #D6E8F5;border-radius:8px;overflow:hidden;margin:0 0 18px 0;font-size:13px;">
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;width:150px;">Nama</td>
		<td style="padding:10px 18px;font-weight:bold;"><?php echo $nama_lengkap; ?></td>
	</tr>
	<tr><td colspan="2" style="border-top:1px solid #D6E8F5;font-size:0;line-height:0;">&nbsp;</td></tr>
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;">Jenjang</td>
		<td style="padding:10px 18px;font-weight:bold;"><?php echo $jenjang; ?></td>
	</tr>
	<tr><td colspan="2" style="border-top:1px solid #D6E8F5;font-size:0;line-height:0;">&nbsp;</td></tr>
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;">Virtual Account</td>
		<td style="padding:10px 18px;font-weight:bold;"><?php echo $transaksi->va_number; ?></td>
	</tr>
	<tr><td colspan="2" style="border-top:1px solid #D6E8F5;font-size:0;line-height:0;">&nbsp;</td></tr>
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;">Bank</td>
		<td style="padding:10px 18px;font-weight:bold;"><?php echo $transaksi->nama_bank; ?></td>
	</tr>
	<tr><td colspan="2" style="border-top:1px solid #D6E8F5;font-size:0;line-height:0;">&nbsp;</td></tr>
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;">Jumlah</td>
		<td style="padding:10px 18px;font-weight:bold;color:#2C7FB8;"><?php echo "Rp ".number_format($transaksi->total_biaya, 0, "", "."); ?></td>
	</tr>
</table>

<?php if ($daftar_ulang != "1") { ?>
<p style="margin:0 0 8px 0;"><strong style="color:#2C7FB8;">Syarat untuk mendapatkan kartu peserta :</strong></p>
<ol style="margin:0 0 16px 0;padding-left:20px;">
	<li style="margin-bottom:6px;">Melakukan pembayaran pendaftaran melalui tata cara yang telah diberikan di atas.</li>
	<li style="margin-bottom:6px;">Setelah melakukan pembayaran, kartu peserta akan terkirim secara otomatis ke email yang digunakan saat pendaftaran sebelumnya / bisa juga diakses melalui link cek data <a href="https://psb.jacos.id/" target="_blank" style="color:#2C7FB8;">https://psb.jacos.id</a></li>
	<li>Silakan mencetak kartu peserta sebagai bukti proses pendaftaran yang Sah.</li>
</ol>
<?php } else { ?>
<p style="margin:0 0 8px 0;"><strong style="color:#2C7FB8;">Syarat untuk mendapatkan kartu siswa sementara :</strong></p>
<ol style="margin:0 0 16px 0;padding-left:20px;">
	<li style="margin-bottom:6px;">Melakukan pembayaran uang pangkal melalui tata cara yang telah diberikan di atas.</li>
	<li style="margin-bottom:6px;">Setelah melakukan pembayaran, kartu siswa sementara akan terkirim secara otomatis ke email yang digunakan saat pendaftaran sebelumnya / bisa juga diakses melalui link cek data <a href="https://psb.jacos.id/" target="_blank" style="color:#2C7FB8;">https://psb.jacos.id</a></li>
	<li>Silakan mencetak kartu siswa sementara sebagai bukti proses pendaftaran yang Sah.</li>
</ol>
<?php } ?>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:6px 0 18px 0;">
<tr><td style="background-color:#3FA7E0;border-radius:24px;">
<a href="https://psb.jacos.id/" target="_blank" style="display:inline-block;padding:11px 28px;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#FFFFFF;text-decoration:none;">Cek Data PSB</a>
</td></tr>
</table>

<p style="margin:0 0 4px 0;">Terima kasih,</p>
<p style="margin:0;"><strong style="font-style:italic;color:#2C7FB8;"><?php echo $nama_panitia; ?></strong></p>

<?php $this->load->view('email/_footer'); ?>
