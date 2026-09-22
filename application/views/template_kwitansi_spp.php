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
			KWITANSI PEMBAYARAN SPP
		</div>
		<div style="margin-top: 10px;">
			<table style="font-family: Times;">
				<tr>
					<td style="padding-right: 400px;">&nbsp;</td>
					<td style="padding-right: 50px;"><span >Bekasi,</span></td>
					<td style="text-align: right;"><span><?php echo formatTanggal($tanggal_transaksi); ?></span></td>
				</tr>
			</table>
		</div>

		<div style="border:1px solid black;width:700px;margin-top:10px;">
			<table style="font-size: 10pt;font-weight: bold;">
				<tr>
					<td style="padding:10px;">JENJANG</td>
					<td style="padding:10px;">:</td>
					<td style="padding:10px;"><?php echo $jenjang; ?></td>
				</tr>
			</table>
			<div style="font-size: 10pt;font-weight: bold;padding: 10px;">TELAH TERIMA DARI</div>
			<table style="font-size: 8pt;">
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
					<td style="padding: 10px;;">TAGIHAN BULAN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo $detail_bulan; ?> </td>
				</tr>
				<tr>
					<td style="padding: 10px;;">JUMLAH SETORAN</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px;"><?php echo "Rp ".number_format($total_biaya,0, "",".").",-"; ?></td>
				</tr>
				<tr>
					<td style="padding: 10px;;">TERBILANG</td>
					<td style="padding: 10px;">:</td>
					<td style="font-weight: bold;padding: 10px; text-transform: capitalize;">--- # <?php echo $total_biaya_terbilang; ?> # ---</td>
				</tr>
			</table>
		</div>

		<table style="font-size: 8pt;border:1px solid black;">
			<tr>
				<td style="border-right:1px solid black;">
					<div style="width: 346px;padding-top:10px;">
						<span style="font-style: underline;font-weight: bold;">Keterangan</span>:
						<p>
							
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