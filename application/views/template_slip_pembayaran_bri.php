<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SLIP PEMBAYARAN</title>
</head>
<body style="font-family: times;">
	<?php $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb","id","1","row")->label; ?>
		<table border="0" style="margin-top: 20px;">
			<tr>
				<td style="width: 100; text-align: center;padding-left: 20px"><img style="width:160px;height:50px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_new.png'; ?>" /></td>
				<td style="width: 400; text-align: center;text-align: center;font-weight:bold;font-size: 14pt;">PENERIMAAN SISWA BARU<br/><?php echo ($tipe_siswa == "ft") ? 'FRANCE TRACK' : strtoupper($tipe_siswa); ?> LABSCHOOL CIBUBUR<br/>TAHUN AJARAN <?php echo $tahun_ajaran;//date('Y', strtotime('+1 years')) . '/' . date('Y', strtotime('+2 years')) ?></td>
				<td style="width: 100; text-align: center;padding-right: 20px;"><img style="width:130px;height:50px;" src="<?php echo FCPATH . '/uploads/logo_bri.png'; ?>" /></td>
			</tr>
		</table>
	<hr style="border-width: 2px;">

	<table style="margin-top: 20px;margin-left: 50px;margin-right: 20px;">
		<tr>
			<td style="width: 600;font-size: 14pt;text-align: center;font-weight: bold;">SLIP PEMBAYARAN</td>
		</tr>
	</table>

	<table style="margin-top: 50px;margin-left: 50px;margin-right: 20px;">
		<tr>
			<td style="text-align: left;padding-right: 300px;">Nomor Tagihan (Virtual Account)<br/><span style="font-weight: bold;"><?php echo $transaksi->va_number; ?></span></td>
			<td style="text-align: left;">Total Tagihan<br/><span style="font-weight: bold;"><?php echo "Rp ".number_format($transaksi->total_biaya,0, "", "."); ?></span></td>
		</tr>
	</table>

	<table style="margin-bottom:50px;margin-left: 50px;margin-right: 20px;">
		<tr>
			<td style="text-align: left;">Batas Akhir Pembayaran<br/><span style="font-weight: bold;color: red;"><?php echo $transaksi->expired_datetime; ?></span></td>
		</tr>
	</table>

	<table style="margin-left: 50px;margin-right: 20px;">
		<tr>
			<td style="text-align: left;padding-top:5px;">Nama Calon Siswa</td>
			<td>:</td>
			<td style="font-weight: bold;padding-bottom:5px;"><?php echo $siswa->nama_lengkap; ?></td>
		</tr>
		<tr>
			<td style="text-align: left;padding-top:5px;">Email Pendaftar</td>
			<td>:</td>
			<td style="font-weight: bold;padding-bottom:5px;"><?php echo $siswa->email; ?></td>
		</tr>
		<tr>
			<td style="text-align: left;padding-top:5px;">Pilihan Sekolah</td>
			<td>:</td>
			<td style="font-weight: bold;padding-bottom:5px;"><?php echo strtoupper($tipe_siswa); ?> LABSCHOOL CIBUBUR</td>
		</tr>
	</table>

	<table style="margin-top: 50px;margin-left: 50px;margin-right: 20px;">
		<tr>
			<td style="font-weight:bold;font-size:12pt;">Mohon Dibaca :</td>
		</tr>
	</table>

	<table style="margin-left: 50px;margin-right: 20px;">
		<tr>
			<td style="width: 600px;"><?php echo html_entity_decode($mohon_dibaca); ?></td>
		</tr>
	</table>
</body>
</html>