
<style type="text/css">
    .divid{
        margin-top: -20px;
        margin-left: -20px;
        /* background-image: url(<?php //echo base_url() . 'uploads/bg_peserta_ft_2025.jpg'; ?>); */
        background-size: 100%;
        background-repeat: no-repeat;
        height: 90%;
        width: 100%;
        font-family: arial;
        padding-bottom:50px;
        border-top: 40px solid #376092;
        /* border-bottom: 30px solid #007AC5; */
    }
</style>
<page style="font-family: times;">
	<div class="divid" style="padding: 10px;width: 680px;">
		<div style="padding-left: 80px;">
			<img style="width: 200px; height: 50px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_new.png'; ?>" />
		</div>
		<div>
			<p style="text-align: center;">
				<span style="font-size: 12pt;font-weight: bold;">TANDA SISWA SEMENTARA</span><br />
				<span style="font-size: 20pt; font-weight: bold;"><?php echo strtoupper(($tipe_siswa=="ft")?"SMA":$tipe_siswa); ?> LABSCHOOL CIBUBUR</span><br />
				<span style="font-size: 12pt; font-weight: bold;">Tahun Ajaran <?php echo $tahun_ajar; ?></span>
			</p>
		</div>
		<div style="font-size: 10pt; padding-top: 50px; padding-left: 80px; line-height: 2px;">
			<table>
				<tr>
					<td>1. Nama Lengkap</td>
					<td>:</td>
					<td><?php echo strtoupper($siswa->nama_lengkap); ?></td>
				</tr>
				<tr>
					<td>2. Tempat, Tanggal lahir</td>
					<td>:</td>
					<td><?php echo $siswa->tempat_lahir . ", " . $siswa->tgl_lahir; ?></td>
				</tr>
				<tr>
					<td>3. Jenis Kelamin</td>
					<td>:</td>
					<td><?php echo $siswa->jenis_kelamin; ?></td>
				</tr>
				<tr>
					<td>4. No. Peserta</td>
					<td>:</td>
					<td><?php echo $siswa->no_peserta; ?></td>
				</tr>
				<tr>
					<td>5. Alamat</td>
					<td>:</td>
					<td><?php echo $siswa->alamat . ", " . $siswa->kota; ?></td>
				</tr>
				<tr>
					<td>6. Sekolah Asal</td>
					<td>:</td>
					<td><?php echo $siswa->sekolah_asal; ?></td>
				</tr>
			</table>
		</div>
		<div style="padding-top: 20px; padding-left: 80px;">
			<table>
				<tr>
					<td style="width: 300px;padding-top: 10px; padding-left: 5px;">
						<img style="width: 150px; height: 200px;" src="<?php echo FCPATH . '/uploads/siswa_' . $tipe_siswa . '/' . $siswa->foto_peserta; ?>" />
					</td>
					<td style="width: 300px;">
						<p>
							<?php $tgl_bayar = (!empty($transaksi->updated_at)) ? date("Y-m-d", strtotime($transaksi->updated_at)) : date("Y-m-d"); ?>
							Kota Bekasi, <?php echo formatTanggal($tgl_bayar); ?><br /><br />
							Kepala <?php echo strtoupper(($tipe_siswa=="ft")?"SMA":$tipe_siswa); ?> Labschool Cibubur
						</p>
						<img style="width: 200px; height: 90px;" src="<?php echo FCPATH . '/uploads/data_kepala_sekolah/'.$kepsek->file_ttd ; ?>" />
						<p>
							<?php echo $kepsek->nama_kepsek ?><br /><br />
							NRKS. <?php echo $kepsek->nrks ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		<div style="padding-top: 20px; padding-left: 80px;">
			<span style="font-size: 11pt;">CATATAN : </span>
			<p style="font-size: 8pt;word-wrap: break-word;">
				<?php echo wordwrap($catatan_kartu_siswa, 130, '<br />', true); ?>
			</p>
		</div>
	</div>
	<page_footer style="color: #666666; text-align: left;font-size: 9px;">
        <hr style="height: 0px; background-color: #000000; border: none; margin: 0px; padding: 0px;">
        <span style="word-wrap: break-word;">Jl. Raya Hankam Kampus Labschool No. 15-20, Jatiranggon, Bekasi Kota 17432, Telepon : +62 21 84304138 ; 84304140, Fax : +62 21 84304236 E-mail : <span style="text-decoration: underline;">bps@labschoolcibubur.sch.id</span>, Home Page: www.lasbchoolcibubur.sch.id</span>
    </page_footer>
</page>