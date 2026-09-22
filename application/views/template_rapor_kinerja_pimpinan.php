<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>RAPOR KINERJA WAKIL KEPALA SEKOLAH</title>
</head>
<style type="text/css">
    
</style>
<body style="font-family: arial;">
	<?php $tahun_ajaran = $rapor->tahun_ajaran; ?>
    <?php $rata_rata = ($rapor->kepribadian_sosial + $rapor->leadership + $rapor->pengembangan_sekolah + $rapor->bidang_tugas_wakil_akademik_kesiswaan) / 4; ?>
		<table border="0" cellspacing="0" style="margin-top: 10px;margin-left:0px;background-color:#BDD7EE;padding:5px 5px;">
			<tr>
				<td style="width: 130; text-align: center;padding-left: 0px;padding-right: 0px;"><img style="width:130px;height:130px;" src="<?php echo FCPATH . '/uploads/logo_labschool_2025.png'; ?>" /></td>
				<td style="width: 480; text-align: center;font-weight:bold;font-size: 14pt;padding-top: 20px;">
                    HASIL PENILAIAN KINERJA WAKIL KEPALA SEKOLAH LABSCHOOL CIBUBUR<br/>TAHUN AJARAN <?php echo $tahun_ajaran; ?> <br/><br/>
                    <span style="font-weight:normal;font-family: arial;font-size:6pt;color:#116AC4;text-decoration:underline;">Jl. Raya Hankam 15-20 Jatiranggon, Jati Sampurna, Bekasi info@labschoolcibubur.sch.id 021 - 84304138, 84304140</span>
                </td>
				<td style="width: 130; text-align: center;padding-left: 0px;padding-right: 0px;"><img style="width:120px;height:120px;" src="<?php echo FCPATH . '/uploads/logo_yayasan.png'; ?>" /></td>
			</tr>
		</table>

    <div style="background-color:#E8F0FB;padding-bottom: 30px;">
	<table border="0" style="margin-top: 20px;margin-left: 30px;">
		<tr>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/user.png'; ?>" /></td>
			<td style="width: 109;padding:5px 3px;font-size: 10pt;">Nama Pegawai</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;"><?php echo $rapor->nama_lengkap; ?></td>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/books.png'; ?>" /></td>
            <td style="width: 100;padding:3px;font-size: 10pt;">Jabatan</td>
            <td style="width: 10;">:</td>
            <td style="font-weight: bold;"><?php echo $rapor->jabatan; ?>
            </td>
		</tr>
	</table>

    <table border="0" style="margin-top: 10px;margin-left: 30px;">
        <tr>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/identity-card.png'; ?>" /></td>
			<td style="width: 109;padding:3px;font-size: 10pt;">NPP</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;"><?php echo $rapor->npp; ?></td>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/house.png'; ?>" /></td>
            <td style="width: 100;padding:3px;font-size: 10pt;">Unit Kerja</td>
            <td style="width: 10;">:</td>
            <td style="font-weight: bold;"><?php echo $rapor->unit_kerja; ?>
            </td>
		</tr>
    </table>

    <table border="0" style="margin-top: 10px;margin-left: 30px;">
        <tr>
            <td ><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/bg-check.png'; ?>" /></td>
            <td style="width: 109;padding:3px;font-size: 10pt;">Golongan</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;padding-top:3px;"><?php echo $rapor->golongan; ?></td>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/bg-check.png'; ?>" /></td>
            <td style="width: 100;padding:3px;font-size: 10pt;">Umur</td>
            <td style="width: 10;">:</td>
            <td style="font-weight: bold;"><?php echo $rapor->umur; ?> Tahun</td>
        </tr>
	</table>

    <table border="0" style="margin-top: 10px;margin-left: 30px;">
        <tr>
            <td ><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/bg-check.png'; ?>" /></td>
            <td style="width: 109;padding:3px;font-size: 10pt;">Masa Kerja</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;"><?php echo $rapor->masa_kerja; ?> Tahun</td>
        </tr>
	</table>
    
    </div>

    <!--BG RED-->
    <div style="background-color: #FCE4D6;padding-bottom:5px;">
        <table border="0" cellspacing="0" style="margin-top: 0px;margin-left: 20px;">
            <tr>
                <td colspan="2" style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;border-left: 2px solid black;width: 170;font-size: 12pt;padding: 5px 0px;font-weight: bold;text-align:center;"> Aspek Penilaian</td>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;width: 60;font-size: 12pt;padding: 5px 0px;font-weight: bold;text-align:center;">Skor</td>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;width: 215;padding-bottom:10px;padding-top:10px;font-size: 12pt;padding: 5px 0px;text-align:center;font-weight: bold;">Grafik</td>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;width: 120;font-size: 12pt;padding: 5px 0px;font-weight: bold;padding-left:20px;">Predikat</td>
            </tr>
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black;text-align: center;vertical-align: middle;width: 30;">1</td>
                <td style="border-right: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:10px;padding-top:10px;">Kepribadian Sosial</td>
                <td style="border-right: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->kepribadian_sosial; ?></td>
                <td style="width: 215;padding-bottom:10px;padding-top:10px;border-right: 1px solid black;">
                    <table style=""><tr><td style="background-color:<?= $rapor->warna_kepribadian_sosial; ?>;width:<?= (153*$rapor->kepribadian_sosial/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="width: 120;border-right: 1px solid black;font-size: 10pt;padding-left:20px;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->predikat_kepribadian_sosial; ?></td>
            </tr>
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black;text-align: center;vertical-align: middle;width: 30;">2</td>
                <td style="border-right: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:10px;padding-top:10px;">Leadership</td>
                <td style="border-right: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->leadership; ?></td>
                <td style="border-right: 1px solid black;width: 215;padding-bottom:10px;padding-top:10px;">
                    <table style=""><tr><td style="background-color:<?= $rapor->warna_leadership; ?>;width:<?= (153*$rapor->leadership/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-right: 1px solid black;width: 120;font-size: 10pt;padding-left:20px;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->predikat_leadership; ?></td>
            </tr>
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black;text-align: center;vertical-align: middle;width: 30;">3</td>
                <td style="border-right: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:10px;padding-top:10px;">Pengembangan Sekolah</td>
                <td style="border-right: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->pengembangan_sekolah; ?></td>
                <td style="border-right: 1px solid black;width: 215;padding-bottom:10px;padding-top:10px;">
                    <table style=""><tr><td style="background-color:<?= $rapor->warna_pengembangan_sekolah; ?>;width:<?= (153*$rapor->pengembangan_sekolah/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-right: 1px solid black;width: 120;font-size: 10pt;padding-left:20px;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->predikat_pengembangan_sekolah; ?></td>
            </tr>
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;text-align: center;vertical-align: middle;width: 30;">4</td>
                <td style="border-right: 1px solid black;border-bottom: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:10px;padding-top:10px;">Bidang Tugas Wakil Akademik/Kesiswaan*</td>
                <td style="border-right: 1px solid black;border-bottom: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->bidang_tugas_wakil_akademik_kesiswaan; ?></td>
                <td style="border-right: 1px solid black;border-bottom: 1px solid black;width: 215;padding-bottom:10px;padding-top:10px;">
                    <table style=""><tr><td style="background-color:<?= $rapor->warna_bidang_tugas_wakil_akademik_kesiswaan; ?>;width:<?= (153*$rapor->bidang_tugas_wakil_akademik_kesiswaan/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-right: 1px solid black;border-bottom: 1px solid black;width: 120;font-size: 10pt;padding-left:20px;padding-bottom:10px;padding-top:10px;"><?php echo $rapor->predikat_bidang_tugas_wakil_akademik_kesiswaan; ?></td>
            </tr>
        </table>
    </div>
    <!--END BG RED-->
    
    <div style="background-color:#E8F0FB;">
        <table>
            <tr>
                <td>
                    <table style="margin-left: 70px;margin-top: 20px;background-color:#D8E9DA;padding:10px 50px 30px 50px;border-radius:10px;">
                        <tr>
                            <td style="font-weight:bold;font-size:14pt;color:#64676B;text-align:center;">Skor</td>
                        </tr>
                        <tr><td style="height:10px;"></td></tr>
                        <tr>
                            <td style="font-weight:bold;font-size:30pt;color:#01589A;"><?= $rapor->total_skor; ?></td>
                        </tr>
                    </table>
                    <br/>
                    <span style="margin-left: 70px;font-size:8pt;">*Skor berdasarkan proporsi penilaian semua aspek</span>
                </td>
                <td style="width:145px;">
                    &nbsp;
                </td>
                <td>
                    <table border="1" cellspacing="0" style="margin-top: 20px;">
                        <tr>
                            <td style="background-color:#FCE4D6;font-weight:bold;font-size:10pt;text-align:center;" colspan="3">Kriteria</td>
                        </tr>
                        <tr>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Skor</td>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Warna</td>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Predikat</td>
                        </tr>
                        <tr>
                            <td style="font-size:10pt;padding: 3px 20px;background-color:#D8E9DA;">91 - 100</td>
                            <td style="padding-left:40px;background-color:#2F2FFF;">&nbsp;</td>
                            <td style="font-size:10pt;padding: 3px 20px 3px 3px;background-color:#D8E9DA;">Sangat Baik</td>
                        </tr>
                        <tr>
                            <td style="font-size:10pt;padding: 3px 20px;background-color:#D8E9DA;">81 - 90</td>
                            <td style="padding-left:40px;background-color:#2C7BE5;">&nbsp;</td>
                            <td style="font-size:10pt;padding: 3px 20px 3px 3px;background-color:#D8E9DA;">Baik</td>
                        </tr>
                        <tr>
                            <td style="font-size:10pt;padding: 3px 20px;background-color:#D8E9DA;">71 - 80</td>
                            <td style="padding-left:40px;background-color:#FFFF00">&nbsp;</td>
                            <td style="font-size:10pt;padding: 3px 20px 3px 3px;background-color:#D8E9DA;">Cukup</td>
                        </tr>
                        <tr>
                            <td style="font-size:10pt;padding: 3px 20px;background-color:#D8E9DA;">61 - 70</td>
                            <td style="padding-left:40px;background-color:#FF8040;">&nbsp;</td>
                            <td style="font-size:10pt;padding: 3px 20px 3px 3px;background-color:#D8E9DA;">Kurang</td>
                        </tr>
                        <tr>
                            <td style="font-size:10pt;padding: 3px 20px;background-color:#D8E9DA;text-align:center;">&lt; 61</td>
                            <td style="padding-left:40px;background-color:#FF4A4A;">&nbsp;</td>
                            <td style="font-size:10pt;padding: 3px 20px 3px 3px;background-color:#D8E9DA;">Sangat Kurang</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table border="0" style="margin-top: 10px;margin-left: 460px;padding:5px 10px;text-align: left;font-size: 10pt;">
            <tr>
                <td style="">Kota Bekasi, <?php echo formatTanggal(date('Y-m-d', strtotime('2025-08-17'))); ?></td>
            </tr>
            <tr>
                <td style="">Pengembang Pendidikan Labschool Cibubur</td>
            </tr>
            <tr>
                <td style="">Kepala,</td>
            </tr>
            <tr>
                <td style=""><img style="width:100px;height:100px;" src="<?php echo FCPATH . '/asset/img/confidential/Deden_signature.png'; ?>" /></td>
            </tr>
        </table>

        <table style="margin-top:0px;padding:5px 10px;text-align: left;">
            <tr>
                <td style="font-weight: bold;background-color:#BDD7EE;width: 150px;padding:2px 20px 2px 10px;text-align: center;">DOKUMEN PRIBADI</td>
                <td style="width:240;">&nbsp;</td>
                <td style="width: 200;">Deden E. Ariffan, M.Pd.</td>
            </tr>
        </table>
    </div>
</body>
</html>