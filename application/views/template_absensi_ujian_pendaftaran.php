
<?php
$chunks = !empty($data->list_peserta) ? array_chunk($data->list_peserta, 20) : [[]];
$pageNo = 1;
foreach ($chunks as $chunk) {
?>
	<page>
	<div style="padding: 10px;width: 700px;font-family: times;">
		<div>
			<table cellpadding="0" cellspacing="0" style="width: 700px;" border="0">
				<tr>
					<td style="padding-left : 30px;">
						<img src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_vertical.png'; ?>" width="130" height="100" />
					</td>
					<td>
						<p style="text-align: center;padding-left : 50px;padding-top:10px;">
							<span style="font-size: 12pt;font-weight: bold;">BERITA ACARA & DAFTAR HADIR PESERTA</span><br />
							<span style="font-size: 12pt;font-weight: bold;"><?= ($data->materi_simulasi == "simulasi") ? "SIMULASI ".strtoupper($data->judul_ujian) : strtoupper($data->judul_ujian) ; ?></span><br />
							<span style="font-size: 12pt; font-weight: bold;">TAHUN AJARAN <?= $data->tahun_ajaran; ?></span>
						</p>
					</td>
				</tr>
			</table>
			
			<div style="width: 700px;margin-left:50px;">
				<table>
					<tr>
						<td colspan="6" style="padding-top:10px;padding-bottom:5px;font-size: 10pt;text-align: left;">
						Pada hari ini <?= ucfirst($data->hari); ?> tanggal <?= $data->tanggal; ?>, dilaksanakan <?= ($data->materi_simulasi == "simulasi") ? "<b>SIMULASI ".strtoupper($data->judul_ujian)."</b>" : "<b>".strtoupper($data->judul_ujian)."</b>"/* $data->tahun_ajaran */; ?>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">WAKTU</td>
						<td style="font-weight:bold;">:</td>
						<td style="padding-right: 200px;font-weight: bold;"><?= $data->waktu; ?></td>
						<td style="font-weight:bold;">RUANG</td>
						<td style="font-weight:bold;">:</td>
						<td style="font-weight: bold;"><?= $data->nama_ruang; ?></td>
					</tr>
					<tr>
						<td colspan="6" style="font-size: 10pt;padding-top:5px;">Jumlah yang hadir : .................. ; Jumlah yang tidak hadir : ................</td>
					</tr>
				</table>
			</div>
		</div>
		
		<div style="font-size: 8pt; padding-top: 20px; padding-left: 50px; line-height: 2px;">
			<table cellspacing="0" border="1" style="text-align: center;">
				<tr>
					<th style="padding:5px;">NO.URUT</th>
					<th style="padding:5px;">NOMOR PESERTA</th>
					<th style="padding:5px;">NAMA PESERTA</th>
					<!-- <th style="padding:5px;">FOTO PESERTA</th> -->
					<th style="padding:5px;">TANDA TANGAN</th>
				</tr>
				<?php
				if (!empty($chunk)) {
					$no = ($pageNo - 1) * 20 + 1;
					foreach ($chunk as $key => $value) {
						echo "<tr style='page-break-inside:avoid;'>";
						echo "<td style='font-size: 8pt;padding:5px 0px;width:20px;'>".$no."</td>";
						echo "<td style='font-size: 8pt;padding:5px 0px;'>".$value->no_peserta."</td>";
						echo "<td style='font-size: 8pt;text-align:left;padding:5px 10px;width:250px;'>".$value->nama_lengkap."</td>";
						echo "<td style='font-size: 8pt;text-align:left;padding:5px 0px 5px 10px;width:30px;'>".$no." .............................</td>";
						echo "</tr>";
						$no++;
					}
				}
				?>
				<?php
				if ($pageNo == count($chunks)) {
				?>
					<tr>
						<td colspan="5" style="padding-bottom:20px;border:1px solid #000000;">
							<p style="font-size:10pt; font-weight:bold;text-transform: italic;text-align: left;">CATATAN PELAKSANAAN : </p><p>&nbsp;</p>
						</td>
					</tr>
					
					<tr>
						<td colspan="5" style="padding-bottom:100px;padding-top:10px;">
							<table border="0">
								<tr>
									<td style="padding-left:350px;">&nbsp;</td>
									<td style="text-align:right;padding-left:100px;">Kota Bekasi, ...................................</td>
								</tr>
								<tr>
									<td>Pengawas 1</td>
									<td>Pengawas 2</td>
								</tr>
							</table>
						</td>
					</tr>
				<?php
					}
				?>
			</table>
		</div>
	</div>
	</page>
<?php
$pageNo++;
	}
?>