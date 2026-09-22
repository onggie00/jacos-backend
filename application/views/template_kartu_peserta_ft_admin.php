
<?php
			foreach ($list_peserta as $key => $value) {
		?>
<?= ($key%2 == 0) ? "<page>" : "<page>"; ?>
<style type="text/css">
    .divid{
        margin-top: 0px;
        margin-left: 0px;
        background-image: url(<?php echo base_url() . 'uploads/bg_peserta_ft_2025.jpg'; ?>);
        background-size: 100%;
        background-repeat: no-repeat;
        height: 1070px;
        width: 760px;
        font-family: times;
    }
</style>
    <div class="divid">
        <table border="0" style="margin-top: 10px;margin-left: 40px;font-family: arial;">
            <tr>
                <td style="width: 110;text-align:center;padding-top: 25px;"><img style="width:130px;height:100px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_vertical.png'; ?>" /></td>
                <td style="width: 370;text-align: center;font-weight:bold;font-size: 16pt;padding-top: 40px;">KARTU TES PSB FRANCE TRACK<br/>SMA LABSCHOOL CIBUBUR<br/>
                <span style="font-size: 12pt;">TAHUN PELAJARAN <span style="color: black;"><?= $value->tahun_ajaran; ?></span></span></td>
                <td style="width: 110;text-align:left;padding-top: 40px;"><img style="width:170px;height:80px;" src="<?php echo FCPATH . '/uploads/logo_ft.png'; ?>" /></td>
            </tr>
            <tr>
                <td style="border-top: 8px solid black;" colspan="3">&nbsp;</td>
            </tr>
        </table>

        <table>
            <tr>
                <td>
                    <table border="0" style="margin-left: 40px;">
                        <tr>
                            <td style="width:160px;border: 3px solid black;font-size: 14pt;font-weight:bold;padding: 10px 0px 10px 20px;font-family: times;"  ><span style="text-decoration: underline;">Lokasi Tes :</span><br/><span style="font-size: 14pt;"><?= (!empty($value->lokasi_ujian))  ? $value->lokasi_ujian : "-"; ?></span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table border="0" style="margin-left: 5px;">
                        <tr>
                            <td style="width:125px;border: 3px solid black;font-size: 14pt;font-weight:bold;padding: 10px 20px;font-family: times;"  ><span style="text-decoration: underline;">Ruang :</span><br/><span style="font-size: 14pt;"><?= (!empty($value->nama_ruang))  ? $value->nama_ruang : "-"; ?></span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table border="0" style="margin-left: 5px;">
                        <tr>
                            <td style="width:125px;border: 3px solid black;font-size: 14pt;font-weight:bold;padding: 10px 20px;font-family: times;"  ><span style="text-decoration: underline;">No. Peserta :</span><br/><span style="font-size: 14pt;"><?php echo $value->no_peserta; ?></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <table border="0" style="margin-left: 40px;">
            <tr>
                <td style="height:200px;vertical-align: top;padding: 10px 10px;" rowspan="2">
                    <table border="0">
                        <tr>
                            <!--<td style="border:1px solid black;padding: 90px 50px;font-weight:bold;">Photo</td>-->
                            <td><img style="width: 140px; height: 200px; object-fit: fill; image-orientation:none;" src="<?php echo FCPATH . '/uploads/siswa_'.$tipe_siswa.'/'.$value->foto_peserta; ?>" /></td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 10px 10px; font-size: 12pt;">
                    <table border="0">
                        <tr>
                            <td colspan="3" style="font-size: 14pt;font-weight: bold;font-family: times;text-transform: uppercase;"><?php echo $value->nama_lengkap; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Jenis Kelamin</td>
                            <td>:</td>
                            <td><?php echo $value->jenis_kelamin; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Sekolah Asal</td>
                            <td>:</td>
                            <td><?php echo $value->sekolah_asal; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Alamat Rumah</td>
                            <td>:</td>
                            <td><?php echo wordwrap($value->alamat, 40, "<br/>", false); ?></td>
                        </tr>
                    </table>
                    <table border="0">
                        <tr>
                            <td colspan="3" style="font-size: 14pt;font-weight: bold;font-family: times;">Informasi Akun Tes Peserta</td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Username</td>
                            <td>:</td>
                            <td><?php echo $value->no_peserta; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Password</td>
                            <td>:</td>
                            <td><?php echo $value->password_ujian;//$value->password_ujian; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Meeting ID Zoom</td>
                            <td>:</td>
                            <td><?php echo (!empty($data_ruang_ujian->meeting_url)) ? "<a href='".$data_ruang_ujian->meeting_url."'>".$data_ruang_ujian->meeting_id."</a>" : "-" ;//wordwrap($ruang->meeting_url, 40, "<br/>", false); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 130px;">Password Zoom</td>
                            <td>:</td>
                            <td><?php echo (!empty($data_ruang_ujian->meeting_password)) ? $data_ruang_ujian->meeting_password : "-";//wordwrap($ruang->meeting_password, 40, "<br/>", false); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border="0" style="margin-left: 40px;">
            <tr>
                <td >
                    <table border="1" style="text-align: center;font-size: 10pt;margin-top:5px;" cellspacing="0">
                        <tr>
                            <td style="padding:10px;width: 620px;background-color: blue;color: white;" colspan="2">
                                <span style="font-weight:bold;font-size:14pt;"><?php echo $title_simulasi; ?></span><br/>
                                <span style="font-size:14pt;"><?php echo $hari_ujian_simulasi.", ".$tgl_ujian_simulasi; ?></span>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td style="padding:10px;width: 620px;" colspan="2"><p><?php //echo $catatan_kp->keterangan_ujian; ?></p></td>
                        </tr> -->
                        <tr>
                            <td style="padding:10px;font-weight: bold;font-size: 14pt;text-align: center;">Waktu</td>
                            <td style="padding:10px;font-weight: bold;font-size: 14pt;text-align: center;">Agenda</td>
                        </tr>
                        <?php 
                            foreach ($waktu_simulasi as $key => $value) {
                                echo "<tr>";
                                echo "<td style='padding:10px;width:150px;'>".date("H:i", strtotime($value->waktu_mulai))." - ".date("H:i", strtotime($value->waktu_selesai))." WIB</td>";
                                echo "<td style='padding:10px;width:400px;'>".$value->simulasi."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>

                    <table border="1" style="text-align: center;font-size: 10pt;margin-top:5px;" cellspacing="0">
                        <tr>
                            <td style="padding:10px;width: 620px;background-color: red;color: white;" colspan="2">
                                <span style="font-weight:bold;font-size:14pt;"><?php echo $title; ?></span><br/>
                                <span style="font-size:14pt;"><?php echo $hari_ujian.", ".$tgl_ujian; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:10px;font-weight: bold;font-size: 14pt;text-align: center;">Waktu</td>
                            <td style="padding:10px;font-weight: bold;font-size: 14pt;text-align: center;">Agenda</td>
                        </tr>
                        <?php 
                            foreach ($waktu_materi as $key => $value) {
                                echo "<tr>";
                                echo "<td style='padding:10px;width:150px;'>".date("H:i", strtotime($value->waktu_mulai))." - ".date("H:i", strtotime($value->waktu_selesai))." WIB</td>";
                                echo "<td style='padding:10px;width:400px;'>".$value->materi."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
        <table style="padding-bottom: 0px;margin-left: 40px;">
            <tr>
                <td style="width: 300px;">
                    <table border="0" >
                        <tr>
                            <td colspan="2" style="font-weight: bold;font-size: 12pt;text-align: left;">
                                Peserta wajib menyiapkan
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10pt;">
                            <?php echo $catatan_kp->catatan_persiapan; ?>
                            </td>
                        </tr>
                    </table>
                    <table border="0" >
                        <tr>
                            <td colspan="2" style="font-weight: bold;font-size: 12pt;text-align: left;">
                                Hal-hal yang harus diperhatikan:
                                <br/>
                                <span style="font-size: 10pt;font-weight: normal;"><?php echo $catatan_kp->catatan_perhatikan; ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 10px;">
                    <table border="0">
                        <tr>
                            <td colspan="2" style="font-weight: bold;font-size: 12pt;text-align: center;">
                                Kontak Kami
                            </td>
                        </tr>
                        <?php 
                        if (!empty($kontak)) {
                            foreach ($kontak as $key_kontak => $value_kontak) {
                                echo "<tr style='font-size: 10pt;'>";
                                echo "<td>".$value_kontak->tipe."</td>";
                                echo "<td>:</td>";
                                echo "<td>".$value_kontak->kontak."</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </table>
                    (Pelayanan di jam kerja pukul 07.00 - 16.00 WIB hari kerja)
                </td>
            </tr>
        </table>
    </div>
    &nbsp;
<?= ($key%2 != 0 || $key == (count($list_peserta)-1) ) ? "</page>" : "</page>"; ?>
<?php
	}
?>
