		<?php
			foreach ($list_siswa as $key => $value) {
		?>
<?= ($key%2 == 0) ? "<page>" : ""; ?>
	<div style="margin-left: 8px;margin-right: 8px;margin-top: 2px;">
		<table border="1" cellspacing="0" style="margin-top: 5px;margin-bottom: 5px;">
			<tr>
				<td colspan="2" style="width: 600px;text-align: center;font-weight:bold;font-size: 10pt;padding-bottom:5px;">
					<table border=0 cellspacing="0">
						<tr>
							<td style="width:200px;text-align:center;">
								<img style="width:200px;height:50px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_new.png'; ?>" />
							</td>
							<td style="width:400px;text-align:center;padding-top:5px;">
								KARTU TANDA PESERTA UJIAN <?= (strtoupper($jenjang) == "FT") ? "SMA" : strtoupper($jenjang); ?> LABSCHOOL CIBUBUR <br/><?= wordwrap($value->judul_ujian, 50, '<br />', true); ?><br/>
					TAHUN AJARAN <?= $tahun_ajar ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border:1px;padding:2px 5px;width:170px;height:435px;">
					<p style="font-weight:bold;">Nomor Peserta</p>
					<h2 style="text-align:center;font-weight:bold;"><?= $value->nomor_peserta_ujian; ?></h2>
					<table border="0">
						<tr style="font-weight:bold;font-size: 7pt;"><td>NAMA</td><td>:</td><td style="width:130px"> <?= wordwrap($value->nama_siswa, 25, '<br/>', true); ?></td></tr>
						<tr style="font-weight:bold;font-size: 7pt;"><td>NIS</td><td>:</td><td> <?= $value->nis; ?></td></tr>
						<tr style="font-weight:bold;font-size: 7pt;"><td>KELAS</td><td>:</td><td> <?= $value->kelas; ?></td></tr>
						<tr style="font-weight:bold;font-size: 7pt;"><td style="padding-top:3px;">RUANG</td><td style="padding-top:3px;">:</td><td style="padding:5px;"> <div style="border:1px solid #000000;width:85px;padding:3px;text-align:center;font-size:8pt;"><?= $ruang->nama_ruang; ?></div></td></tr>
						<tr style="font-weight:bold;font-size: 7pt;"><td>AKUN LOGIN TES</td><td>:</td><td> &nbsp;</td></tr>
						<tr style="font-weight:bold;font-size: 7pt;"><td>USERNAME</td><td>:</td><td style="color:red;"> <?= $value->nis; ?></td></tr>
						<tr style="font-weight:bold;font-size: 7pt;"><td>PASSWORD</td><td>:</td><td style="color:red;"> <?= $value->password; ?></td></tr>
						<tr style="font-size: 7pt;"><td colspan="3" style="text-align:center;padding-top:10px;"> Bekasi, <?= $today_date; ?></td></tr>
						<tr style="font-size: 7pt;"><td colspan="3" style="text-align:center;padding-top:5px;"> Kepala <?= (strtoupper($jenjang) == "FT") ? "SMA" : strtoupper($jenjang); ?> Labschool Cibubur</td></tr>
						<tr style="font-size: 7pt;"><td colspan="3" style="text-align:center;padding-top:10px;"><img style="width:100px;height:50px;" src="<?php echo FCPATH . '/uploads/data_kepala_sekolah/'.$kepsek->file_ttd; ?>"></td></tr>
						<tr style="font-size: 7pt;"><td colspan="3" style="text-align:center;font-weight:bold;padding-top:10px;"> <?= $kepsek->nama_kepsek; ?></td></tr>
					</table>
				</td>
				<td style="width:430px;padding:2px 5px;">
					<p style="font-weight:bold;">Jadwal Ujian</p>
					<table border="1" cellspacing="0">
						<tr>
							<th style='font-size: 8pt;padding:3px;text-align:center;width:100px'>Hari, Tanggal</th>
							<th style='font-size: 8pt;padding:3px;text-align:center;width:50px'>Waktu</th>
							<th style='font-size: 8pt;padding:3px;text-align:center;width:200px'>Bidang Studi</th>
							<th style='font-size: 8pt;padding:3px;text-align:center;width:50px'>Paraf Pengawas</th>
						</tr>
						<?php
							$no = 1;
							foreach ($ujian as $key_ujian => $value_ujian) {
								if(empty($value_ujian->daftar_kelas)){
									echo "<tr>";
									echo "<td style='font-size:5pt;padding:5px;text-align:center;'>".ucfirst($value_ujian->hari).", ".$value_ujian->tgl_format."</td>";
									echo "<td style='font-size:6pt;padding:5px;text-align:center;'>".date("H:i", strtotime($value_ujian->jam_mulai))." - ".date("H:i", strtotime($value_ujian->jam_selesai))."</td>";
									echo "<td style='font-size:5pt;padding:5px;text-align:center;'>".wordwrap($value_ujian->nama_mapel, 40, '<br />', true)."</td>";
									echo "<td style='font-size:6pt;padding:5px;'>".$no." ..........................</td>";
									echo "</tr>";
								$no++;
								}
								else{
									if(strpos($value_ujian->daftar_kelas, $value->kelas) !== false){
										echo "<tr>";
										echo "<td style='font-size:5pt;padding:5px;text-align:center;'>".ucfirst($value_ujian->hari).", ".$value_ujian->tgl_format."</td>";
										echo "<td style='font-size:6pt;padding:5px;text-align:center;'>".date("H:i", strtotime($value_ujian->jam_mulai))." - ".date("H:i", strtotime($value_ujian->jam_selesai))."</td>";
										echo "<td style='font-size:5pt;padding:5px;text-align:center;'>".wordwrap($value_ujian->nama_mapel, 40, '<br />', true)."</td>";
										echo "<td style='font-size:6pt;padding:5px;'>".$no." ..........................</td>";
										echo "</tr>";
										$no++;
									}
								}
							}
						?>
					</table>

					<p style="text-align:right;bottom:5px;right:5px;font-size:6pt;color:gray;margin:0;font-style:italic;padding-top:10px;">
						*dicetak dengan ukuran kertas A4
					</p>
				</td>
			</tr>
		</table>
&nbsp;
	</div>&nbsp;
<?= ($key%2 != 0 || $key == (count($list_siswa)-1) ) ? "</page>" : ""; ?>
		<?php
			}
		?>