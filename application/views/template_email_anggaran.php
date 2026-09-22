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
			Kepala Sekretariat Labschool Cibubur
		</p>
		<p>
			Terdapat <?php echo $pengajuan_laporan; ?> PROGRAM DAN ANGGARAN untuk Jenjang <?php echo $jenis_anggaran; ?>, Dibawah ini merupakan rincian mengenai <?php echo $pengajuan_laporan; ?> program dan anggaran kegiatan serta terlampir berkas kelengkapan :
		</p>
		<div style="padding: 5px 15px;font-weight: bold;">
			<table border="0">
				<tr style="padding: 5px 15px;">
					<td>Nomor Program</td>
					<td>:</td>
					<td><?php echo $data_anggaran['nomor_program']; ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Tahun Ajaran</td>
					<td>:</td>
					<td><?php echo $data_anggaran['tahun_ajaran']; ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Nama Program</td>
					<td>:</td>
					<td><?php echo $data_anggaran['nama_program']; ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Tanggal Program</td>
					<td>:</td>
					<td><?php echo date("d-m-Y", strtotime($data_anggaran['tanggal_program']))." (".formatTanggal($data_anggaran['tanggal_program']).")"; ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Jenis Kegiatan</td>
					<td>:</td>
					<td><?php echo $data_anggaran['jenis_kegiatan']; ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Sub Jenis Kegiatan</td>
					<td>:</td>
					<td><?php echo $data_anggaran['sub_jenis_kegiatan']; ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Nominal OKR</td>
					<td>:</td>
					<td><?php echo "Rp ".number_format($data_anggaran['nominal_okr'], 0, "", "."); ?></td>
				</tr>
				<tr style="padding: 5px 15px;">
					<td>Nominal Pengajuan</td>
					<td>:</td>
					<td><?php echo "Rp ".number_format($data_anggaran['nominal_pengajuan'], 0, "", "."); ?></td>
				</tr>
			</table>
		</div>
		<p style="margin-top:10px;">
			Dimohon untuk segera melakukan pengecekan lampiran dan melakukan konfirmasi pada tautan form berikut : <br/>
		</p>
		<div>
			<a href="<?php echo site_url('email_confirmation/index?token=').$token_confirmation."&pengajuan_laporan=".$pengajuan_laporan."&jenjang=".$jenis_anggaran; ?>"><button style="width:200px;height:40px;padding:10px;background-color:#35AB55;border: none;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 12pt;cursor:pointer;margin: 4px 2px;">KONFIRMASI DISINI</button></a>
		</div>
		<div>
			<?php if(!empty($file_proposal_pengajuan)){ ?>
				<a href="<?php echo base_url('/').'/uploads/program_anggaran_'.strtolower($jenis_anggaran).'/'.$file_proposal_pengajuan; ?>"><button style="width:200px;height:40px;padding:10px;background-color:#35AB55;border: none;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 12pt;cursor:pointer;margin: 4px 2px;">LIHAT FILE PENGAJUAN PROPOSAL</button></a>
			<?php } ?>
			<?php if(!empty($file_proposal_keuangan)){ ?>
				<a href="<?php echo base_url('/').'/uploads/program_anggaran_'.strtolower($jenis_anggaran).'/'.$file_proposal_keuangan; ?>"><button style="width:200px;height:40px;padding:10px;background-color:#35AB55;border: none;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 12pt;cursor:pointer;margin: 4px 2px;">LIHAT FILE PENGAJUAN PROPOSAL KEUANGAN</button></a>
			<?php } ?>
			<?php if(!empty($file_laporan_kegiatan)){ ?>
				<a href="<?php echo base_url('/').'/uploads/program_anggaran_'.strtolower($jenis_anggaran).'/'.$file_laporan_kegiatan; ?>"><button style="width:200px;height:40px;padding:10px;background-color:#35AB55;border: none;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 12pt;cursor:pointer;margin: 4px 2px;">LIHAT FILE LAPORAN</button></a>
			<?php } ?>
			<?php if(!empty($file_laporan_keuangan)){ 
				$list_file = explode(",", $file_laporan_keuangan);
				for ($i=0; $i < count($list_file); $i++) { 
			?>
				<a href="<?php echo base_url('/').'/uploads/program_anggaran_'.strtolower($jenis_anggaran).'/'.$list_file[$i]; ?>"><button style="width:200px;height:40px;padding:10px;background-color:#35AB55;border: none;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 12pt;cursor:pointer;margin: 4px 2px;">LIHAT FILE LAPORAN KEUANGAN <?= $i; ?></button></a>
			<?php } } ?>
		</div>
		<p style="margin-top:10px;">
			Atas Perhatiannya, Terima kasih
		</p>

	</div>
</body>
</html>