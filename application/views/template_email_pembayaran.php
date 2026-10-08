<?php $email_title = 'KWITANSI DAN KARTU PESERTA JAKARTA COSMOPOLITE ISLAMIC SCHOOL'; ?>
<?php $this->load->view('email/_header'); ?>

<p style="margin:0 0 14px 0;">
	Kepada Yth.<br/>
	<strong style="font-size:15px;color:#2C7FB8;"><?php echo $nama_lengkap; ?></strong>
</p>
<p style="margin:0 0 16px 0;">
	Terima kasih sudah melakukan pembayaran pendaftaran <?php echo $tipe_pendaftaran; ?> JAKARTA COSMOPOLITE ISLAMIC SCHOOL.<br/>
	Berikut adalah user untuk mengakses data <?php echo $tipe_pendaftaran; ?> JAKARTA COSMOPOLITE ISLAMIC SCHOOL :
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #D6E8F5;border-radius:8px;overflow:hidden;margin:0 0 18px 0;font-size:13px;">
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;width:150px;">User</td>
		<td style="padding:10px 18px;font-weight:bold;"><?php echo $email; ?></td>
	</tr>
	<tr><td colspan="2" style="border-top:1px solid #D6E8F5;font-size:0;line-height:0;">&nbsp;</td></tr>
	<tr>
		<td style="background-color:#F5FAFD;padding:10px 18px;color:#5E7A8E;">Link Cek Data</td>
		<td style="padding:10px 18px;"><a href="https://psb.jacos.id/" target="_blank" style="color:#2C7FB8;font-weight:bold;">Cek Data Disini</a></td>
	</tr>
</table>

<p style="margin:0 0 16px 0;">
	Kami lampirkan juga kartu peserta sebagai bukti proses pendaftaran yang Sah
</p>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px 0;">
<tr><td style="background-color:#3FA7E0;border-radius:24px;">
<a href="https://psb.jacos.id/" target="_blank" style="display:inline-block;padding:11px 28px;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#FFFFFF;text-decoration:none;">Cek Data PSB</a>
</td></tr>
</table>

<p style="margin:0 0 4px 0;">Terima kasih,</p>
<p style="margin:0;"><strong style="font-style:italic;color:#2C7FB8;"><?php echo $nama_panitia; ?></strong></p>

<?php $this->load->view('email/_footer'); ?>
