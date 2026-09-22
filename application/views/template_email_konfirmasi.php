<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $judul; ?></title>
</head>
<body style="font-family: arial, sans-serif, Times;">
	<div>
		<p>
			Kepada Yth. 
		</p>
		<p style="font-weight: bold;">
			Kepala <?php echo strtoupper($jenis_anggaran); ?> Labschool Cibubur
		</p>
		<p>
			Setelah melakukan pengecekan lebih lanjut. Dibawah ini merupakan rincian mengenai <strong><?= strtoupper($pengajuan_laporan); ?></strong> program dan anggaran kegiatan :
		</p>
		<div style="padding: 5px 15px;font-weight: bold;">
			<table border="0">
				<tr>
					<td style="padding: 5px 10px;">Nomor Program</td>
					<td>:</td>
					<td><?php echo $data_anggaran->nomor_program; ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Tahun Ajaran</td>
					<td>:</td>
					<td><?php echo $data_anggaran->tahun_ajaran; ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Nama Program</td>
					<td>:</td>
					<td><?php echo $data_anggaran->nama_program; ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Tanggal Program</td>
					<td>:</td>
					<td><?php echo date("d-m-Y", strtotime($data_anggaran->tanggal_program))." (".formatTanggal($data_anggaran->tanggal_program).")"; ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Jenis Kegiatan</td>
					<td>:</td>
					<td><?php echo $data_anggaran->jenis_kegiatan; ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Sub Jenis Kegiatan</td>
					<td>:</td>
					<td><?php echo $data_anggaran->sub_jenis_kegiatan; ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Nominal OKR</td>
					<td>:</td>
					<td><?php echo "Rp ".number_format($data_anggaran->nominal_okr, 0, "", "."); ?></td>
				</tr>
				<tr>
					<td style="padding: 5px 10px;">Nominal Pengajuan</td>
					<td>:</td>
					<td><?php echo "Rp ".number_format($data_anggaran->nominal_pengajuan, 0, "", "."); ?></td>
				</tr>
			</table>
		</div>
		<p style="margin-top:10px;">
			Sekretariat menyatakan <?php echo $pengajuan_laporan; ?> kegiatan tersebut diatas <strong><?php echo $status; ?></strong>.<br/>
		</p>
		<p style="margin-top:10px;">
			Atas perhatiannya, Terima kasih
		</p>
		<p style="font-weight: bold;font-style: italic;font-size: 10pt;">
			Sekretariat Labschool Cibubur
		</p>

	</div>
</body>
</html>