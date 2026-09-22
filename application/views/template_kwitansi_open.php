<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>KWITANSI PEMBAYARAN</title>
</head>
<body >
	<div style="padding-left: 50px;">
		<table>
			<tr>
				<td>
					<img style="width:200px;height:50px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_new.png'; ?>" />
				</td>
			</tr>
		</table>
		<div style="font-size: 16pt; text-align: center;font-weight: bold;">
			KWITANSI PEMBAYARAN <?php echo (!empty($jenis_kwitansi)) ? strtoupper($jenis_kwitansi) : ""; ?>
		</div>
		<div style="margin-top: 10px;">
			<table style="font-family: Times;">
				<tr>
					<td style="padding-right: 400px;">&nbsp;</td>
					<td style="padding-right: 50px;"><span >Bekasi,</span></td>
					<td style="text-align: right;"><span><?php echo formatTanggal(date("Y-m-d")); ?></span></td>
				</tr>
			</table>
		</div>

		<div style="border:1px solid black;width:700px;margin-top:10px;">
			<table style="font-size: 10pt;font-weight: bold;">
				<tr>
					<td style="padding:10px;">JENIS PENDAFTARAN</td>
					<td style="padding:10px;">:</td>
					<td style="padding:10px;"><?php echo ($jenjang == "FT") ? 'France Track' : strtoupper($jenjang); ?></td>
				</tr>
				<tr>
					<td style="padding:10px;">JALUR</td>
					<td style="padding:10px;">:</td>
					<td style="padding:10px;"><?php echo $jalur; ?></td>
				</tr>
			</table>
			<div style="font-size: 10pt;font-weight: bold;padding: 10px;">TELAH TERIMA DARI</div>
			<table style="font-size: 8pt;">
				<tr>
					<td style="padding: 10px;">NAMA ORANG TUA/WALI</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo $nama_ortu; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">NAMA SISWA</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo $nama_lengkap; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">VIRTUAL ACCOUNT</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo $va_number; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">TAHUN AJARAN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo $tahun_ajaran; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">JUMLAH SETORAN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo "Rp ".number_format($total_biaya,0, "",".").",-"; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">TERMIN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px; text-transform: capitalize;"><?php echo $termin; ?></td>
				</tr>
                <tr>
					<td style="padding: 10px;;">SISA TAGIHAN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px; text-transform: capitalize;"><?php echo "Rp ".number_format($sisa_tagihan,0, "",".").".-"; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">RIWAYAT PEMBAYARAN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px; text-transform: capitalize;">&nbsp;</td>
				</tr>
				<tr>
					<td style="padding: 10px;">&nbsp;</td>
					<td style="padding: 10px;">&nbsp;</td>
					<td ><?php echo $riwayat; ?></td>
				</tr>
			</table>
		</div>

		<table style="font-size: 8pt;border:1px solid black;">
			<tr>
				<td style="border-right:1px solid black;">
					<div style="width: 346px;padding-top:10px;">
						<span style="font-style: underline;font-weight: bold;">Keterangan</span>:
						<p>
							<?php 
								if ($jenis_kwitansi == "uang pendaftaran") {
									echo "Pembayaran Pendaftaran Siswa Baru ".$jenjang." Labschool Cibubur Tahun Ajaran ".$tahun_ajaran;
								}
								else if($jenis_kwitansi == "uang pangkal"){
									echo "Pembayaran Uang Pangkal Dana Pendidikan Siswa Baru ".$jenjang." Labschool Cibubur Tahun Ajaran ".$tahun_ajaran;
								}
							?>
						</p>
					</div>
				</td>
				<td>
					<div style="width: 316px;padding-left: 30px;">
						<table>
							<tr>
								<td style="text-align: center;padding-top:10px;"><img style="width:110px;height:80px;" src="<?php echo FCPATH . '/uploads/ttd-pak-ahmad-badaruddin.png'; ?>" /></td>
							</tr>
							<tr>
								<td style="text-align: center;">Bagian Keuangan</td>
							</tr>
							<tr>
								<td style="text-align: center;font-weight: bold;">Ahmad Badaruddin, S.E</td>
								<td style="text-align: center;padding-left: 70px;font-weight: bold;"><?php echo $nama_ortu; ?></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>

	</div>
</body>
</html>