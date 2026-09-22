
<?php
			foreach ($ujian as $key => $value) {
		?>
	<page>
	<div style="padding: 10px;width: 700px;font-family: times;">
		<div>
			<p style="text-align: center;">
				<span style="font-size: 12pt;font-weight: bold;">BERITA ACARA & DAFTAR HADIR PESERTA</span><br />
				<span style="font-size: 12pt;font-weight: bold;"><?= strtoupper($value->nama_ujian); ?></span><br />
				<span style="font-size: 12pt; font-weight: bold;">TAHUN AJARAN <?= $tahun_ajar; ?></span>
			</p>
			<p style="text-align: left;margin-left:50px;">
				<span style="font-size: 10pt;">Pada hari ini <?= ucfirst($value->hari); ?> tanggal <?= date("d-m-Y", strtotime($value->tanggal)); ?>, dilaksanakan <?= $value->nama_ujian." ".$tahun_ajar; ?></span><br />
			</p>
			<div style="width: 700px;margin-left:50px;">
				<table>
					<tr>
						<td>MATA PELAJARAN</td>
						<td>:</td>
						<td><?= $value->nama_mapel; ?></td>
					</tr>
					<tr>
						<td>WAKTU</td>
						<td>:</td>
						<td style="padding-right: 200px;"><?= date("H:i",strtotime($value->jam_mulai))." - ".date("H:i", strtotime($value->jam_selesai)); ?></td>
						<td>RUANG</td>
						<td>:</td>
						<td><?= $ruang->nama_ruang; ?></td>
					</tr>
					<tr>
						<td>KELAS</td>
						<td>:</td>
						<td><?= "............."//$kelas->kelas; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div>
			<p style="font-size: 10pt;margin-left:50px;">
				Jumlah yang hadir : .................. ; Jumlah yang tidak hadir : ........, yakni nomor : ..........;.........;.........; ..........
			</p>
		</div>
		<div style="font-size: 8pt; padding-top: 20px; padding-left: 50px; line-height: 2px;">
			<table cellspacing="0" border="1" style="text-align: center;">
				<tr>
					<th style="padding:5px;">NO.URUT</th>
					<th style="padding:5px;">NOMOR PESERTA</th>
					<th style="padding:5px;">NAMA PESERTA</th>
					<th style="padding:5px;">KELAS</th>
					<th style="padding:5px;">TANDA TANGAN</th>
				</tr>
				<?php
				if (!empty($peserta)) {
					$no = 1;
					foreach ($peserta as $key => $value) {
						echo "<tr>";
						echo "<td style='font-size: 8pt;padding:5px 0px;width:20px;'>".$no."</td>";
						echo "<td style='font-size: 8pt;padding:5px 0px;'>".$value->nomor_peserta_ujian."</td>";
						echo "<td style='font-size: 8pt;text-align:left;padding:5px 10px;width:250px;'>".$value->nama_siswa."</td>";
						echo "<td style='font-size: 8pt;padding:5px 0px;width:30px;'>".$kelas->kelas."</td>";
						echo "<td style='font-size: 8pt;text-align:left;padding:5px 0px 5px 10px;width:30px;'>".$no." .............................</td>";
						echo "</tr>";
						$no++;
					}	
				}
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
								<td>Pengawas II</td>
								<td>Pengawas I</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
	</page>
		<?php
			}
		?>