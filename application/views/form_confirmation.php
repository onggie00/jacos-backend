<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Confirmation Form</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body style="font-family: arial, sans-serif, Times;background-color: #5491F8; height: 100vh;">
	<div class="container">
		<div class="row mt-5 bg-white rounded">
			<?php 
				if (empty($this->input->get("jenjang")) || empty($this->input->get("pengajuan_laporan")) || empty($this->input->get("token"))) {
					echo "<h3>Permintaan tidak valid</h3>";
				}
				else{
					$cek = $this->mymodel->getbywhere("program_anggaran_".strtolower($this->input->get("jenjang")), "token_confirmation", $this->input->get("token"), "row");
					if (empty($cek)) {
						echo "<h3>Token tidak ditemukan / Form telah berhasil dikonfirmasi</h3>";
					}
					else{
			?>
					<div class="col-md-6 p-3">
						<p class="h3">
							Detail Kegiatan
						</p>
						<table border="0">
							<tr>
								<td style="padding: 5px 10px;">Nomor Program</td>
								<td>:</td>
								<td><?php echo $cek->nomor_program; ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Tahun Ajaran</td>
								<td>:</td>
								<td><?php echo $cek->tahun_ajaran; ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Nama Program</td>
								<td>:</td>
								<td><?php echo $cek->nama_program; ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Tanggal Program</td>
								<td>:</td>
								<td><?php echo date("d-m-Y", strtotime($cek->tanggal_program))." (".formatTanggal($cek->tanggal_program).")"; ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Jenis Kegiatan</td>
								<td>:</td>
								<td><?php echo $cek->jenis_kegiatan; ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Sub Jenis Kegiatan</td>
								<td>:</td>
								<td><?php echo $cek->sub_jenis_kegiatan; ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Nominal OKR</td>
								<td>:</td>
								<td><?php echo "Rp ".number_format($cek->nominal_okr, 0, "", "."); ?></td>
							</tr>
							<tr>
								<td style="padding: 5px 10px;">Nominal Pengajuan</td>
								<td>:</td>
								<td><?php echo "Rp ".number_format($cek->nominal_pengajuan, 0, "", "."); ?></td>
							</tr>
						</table>
					</div>
					<div class="col-md-6 p-3 form-group">
						<p class="h3">
							Form Konfirmasi
						</p>
						<form class="mt-3" method="post" action="<?php echo site_url('email_confirmation/konfirmasi'); ?>">
							<input type="hidden" name="id_program" value="<?= $cek->id; ?>"/>
							<input type="hidden" name="pengajuan_laporan" value="<?= $this->input->get("pengajuan_laporan"); ?>"/>
							<input type="hidden" name="jenjang" value="<?= $this->input->get("jenjang"); ?>"/>
							<div>
								<label>Catatan</label><br/>
								<textarea class="form-control" name="catatan" style="resize: none;height: 100px;"></textarea>
							</div>
							<div class="mt-3 text-right">
								<input class="btn btn-danger" type="submit" value="Tolak" name="btn_reject" style="width:100px;height:40px;padding:10px;margin: 4px 2px;"/>
								<input class="btn btn-success" type="submit" value="Setuju" name="btn_approve" style="width:100px;height:40px;padding:10px;margin: 4px 2px;"/>
							</div>
						</form>
					</div>
			<?php
					}
				}
			?>
		</div>
	</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>