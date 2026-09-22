<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TKA GURU</title>
</head>
<style type="text/css">
    
</style>
<body style="font-family: arial;">
	<?php $tahun = $tka->tahun; ?>
    <?php 
        $rata_a = ($tka->bhs_indonesia + $tka->bhs_inggris + $tka->numerasi) / 3;
    ?>
		<table border="0" cellspacing="0" style="margin-top: 10px;margin-left:0px;background-color:#BDD7EE;padding:5px 5px;">
			<tr>
				<td style="width: 130; text-align: center;padding-left: 0px;padding-right: 0px;"><img style="width:130px;height:100px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_vertical.png'; ?>" /></td>
				<td style="width: 480; text-align: center;font-weight:bold;font-size: 14pt;padding-top: 20px;">
                    HASIL PENILAIAN <br/>TES KEMAMPUAN AKADEMIK (TKA) <br/> GURU SD - SMP - SMA LABSCHOOL CIBUBUR TAHUN <?php echo $tahun; ?> <br/><br/>
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
            <td style="width: 230;font-weight: bold;"><?php echo $tka->nama_lengkap; ?></td>
            <?php
                if (!empty($tka->mata_pelajaran)){
            ?>
                <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/books.png'; ?>" /></td>
                <td style="width: 100;padding:3px;font-size: 10pt;">Mata Pelajaran</td>
                <td style="width: 10;">:</td>
                <td style="font-weight: bold;"><?php echo wordwrap($tka->mata_pelajaran, 25, "<br>\n", true); ?></td>
            <?php
                }
                else{
            ?>
                <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/house.png'; ?>" /></td>
                <td style="width: 100;padding:3px;font-size: 10pt;">Unit Kerja</td>
                <td style="width: 10;">:</td>
                <td style="font-weight: bold;"><?php echo $tka->unit_kerja; ?></td>
            <?php
                }
            ?>
		</tr>
	</table>

    <table border="0" style="margin-top: 10px;margin-left: 30px;">
        <tr>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/identity-card.png'; ?>" /></td>
			<td style="width: 109;padding:3px;font-size: 10pt;">NPP</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;"><?php echo $tka->npp; ?></td>
            <?php
                if (!empty($tka->mata_pelajaran)){
            ?>
                <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/house.png'; ?>" /></td>
                <td style="width: 100;padding:3px;font-size: 10pt;">Unit Kerja</td>
                <td style="width: 10;">:</td>
                <td style="font-weight: bold;"><?php echo $tka->unit_kerja; ?></td>
            <?php
                }
            ?>
		</tr>
    </table>

    <table border="0" style="margin-top: 10px;margin-left: 30px;">
        <tr>
            <td ><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/bg-check.png'; ?>" /></td>
            <td style="width: 109;padding:3px;font-size: 10pt;">Golongan</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;padding-top:3px;"><?php echo (!empty($tka->golongan)) ? $tka->golongan : '-'; ?></td>
            <td><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/bg-check.png'; ?>" /></td>
            <td style="width: 100;padding:3px;font-size: 10pt;">Umur</td>
            <td style="width: 10;">:</td>
            <td style="font-weight: bold;"><?php echo (!empty($tka->umur)) ? $tka->umur. ' Tahun' : '-'; ?> </td>
        </tr>
	</table>

    <table border="0" style="margin-top: 10px;margin-left: 30px;">
        <tr>
            <td ><img style="width:20px;height:20px;" src="<?php echo FCPATH . '/uploads/bg-check.png'; ?>" /></td>
            <td style="width: 109;padding:3px;font-size: 10pt;">Masa Kerja</td>
            <td style="width: 10;">:</td>
            <td style="width: 230;font-weight: bold;"><?php echo (!empty($tka->masa_kerja)) ? $tka->masa_kerja. ' Tahun' : '-'; ?></td>
        </tr>
	</table>
    
    </div>

    <!--BG RED-->
    <div style="background-color: #FCE4D6;padding-bottom:5px;">
        <table border="0" cellspacing="0" style="margin-top: 0px;margin-left: 20px;">
            <tr>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;border-left: 2px solid black;width: 170;font-size: 12pt;padding: 5px 0px;font-weight: bold;text-align:center;"> Aspek Penilaian</td>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;width: 60;font-size: 12pt;padding: 5px 0px;font-weight: bold;text-align:center;">Skor</td>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;width: 240;font-size: 12pt;padding: 5px 0px;text-align:center;font-weight: bold;">Grafik</td>
                <td style="background-color: #C55A11;border-top: 2px solid black;border-bottom: 2px solid black;border-right: 2px solid black;width: 150;font-size: 12pt;padding: 5px 0px;font-weight: bold;padding-left:20px;">Predikat</td>
            </tr>
            <?php
                if ($tka->bhs_indonesia != null){
            ?>
                <tr>
                    <td style="border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:7px;padding-top:7px;">Bahasa Indonesia</td>
                    <td style="border-top: 1px solid black;border-right: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:7px;padding-top:7px;"><?php echo $tka->bhs_indonesia; ?></td>
                    <td style="width: 240;border-top: 1px solid black;border-right: 1px solid black;padding-bottom:4px;padding-top:4px;">
                        <table style=""><tr><td style="background-color:<?= $tka->warna_bhs_indonesia; ?>;width:<?= (198*$tka->bhs_indonesia/100) ?>px;">&nbsp;</td></tr></table>
                    </td>
                    <td style="width: 150;border-top: 1px solid black;border-right: 1px solid black;font-size: 10pt;padding-left:0px;padding-bottom:7px;padding-top:7px;text-align:center;"><?php echo $tka->predikat_bhs_indonesia; ?></td>
                </tr>
            <?php
                }
            ?>
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:7px;padding-top:7px;">Bahasa Inggris</td>
                <td style="border-right: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:7px;padding-top:7px;"><?php echo $tka->bhs_inggris; ?></td>
                <td style="border-right: 1px solid black;width: 240;padding-bottom:3px;padding-top:3px;">
                    <table style=""><tr><td style="background-color:<?= $tka->warna_bhs_inggris; ?>;width:<?= (198*$tka->bhs_inggris/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-right: 1px solid black;width: 150;font-size: 10pt;padding-left:0px;padding-bottom:7px;padding-top:7px;text-align:center;"><?php echo $tka->predikat_bhs_inggris; ?></td>
            </tr>
            <tr>
                <td style="border-left: 1px solid black;border-right: 1px solid black;width: 170;padding-left:20px;font-size: 10pt;padding-bottom:7px;padding-top:7px;">Numerasi</td>
                <td style="border-right: 1px solid black;width: 60;font-size: 10pt;text-align:center;font-weight:bold;padding-bottom:7px;padding-top:7px;"><?php echo $tka->numerasi; ?></td>
                <td style="border-right: 1px solid black;width: 240;padding-bottom:3px;padding-top:3px;">
                    <table style=""><tr><td style="background-color:<?= $tka->warna_numerasi; ?>;width:<?= (198*$tka->numerasi/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-right: 1px solid black;width: 150;font-size: 10pt;padding-left:0px;padding-bottom:7px;padding-top:7px;text-align:center;"><?php echo $tka->predikat_numerasi; ?></td>
            </tr>
            
            <tr>
                <td style="border: 1px solid black;vertical-align: middle;width: 170;text-align: center;font-size: 12pt;font-weight:bold; padding: 10px 0px">Rata-rata Individu </td>
                <td style="vertical-align: middle;border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;width: 60;font-size: 12pt;text-align:center;font-weight:bold"><?php echo $tka->rata_rata_individu; ?></td>
                <td style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;width: 240;padding-bottom:3px;padding-top:10px;">
                    <table style=""><tr><td style="background-color:<?= $tka->warna_rata_rata_individu; ?>;width:<?= (198*$tka->rata_rata_individu/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;width: 150;font-size: 10pt;padding-left:0px;padding-top:10px;text-align:center;"><?php echo $tka->predikat_rata_rata_individu; ?></td>
            </tr>
        </table>
    </div>
    <!--END BG RED-->
    <!--BG YELLOW-->
    <div style="background-color: #FFD1A4;padding-top 5xp;padding-bottom: 5px">
        <table border="0" cellspacing="0" style="margin-top: 5px;margin-left: 20px;">
            <tr>
                <td style="border: 1px solid black;vertical-align: middle;width: 212;text-align: center;font-size: 12pt;font-weight:bold; padding: 10px 0px">Rata-rata Guru <br/>Labschool Cibubur </td>
                <td style="vertical-align: middle;border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;width: 60;font-size: 12pt;text-align:center;font-weight:bold"><?php echo $tka->rata_rata_all; ?></td>
                <td style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;width: 240;padding-bottom:3px;padding-top:18px;">
                    <table style=""><tr><td style="background-color:<?= $tka->warna_rata_rata_all; ?>;width:<?= (198*$tka->rata_rata_all/100) ?>px;">&nbsp;</td></tr></table>
                </td>
                <td style="border-top: 1px solid black;border-bot/tom: 1px solid black;border-right: 1px solid black;width: 150;font-size: 10pt;padding-left:11px;padding-right:10px;padding-top:20px;text-align:center;"><?php echo $tka->predikat_rata_rata_all; ?></td>
            </tr>
            
        </table>
    </div>
    <!--END BG YELLOW-->
    
    <div style="background-color:#E8F0FB;">
        <table>
            <tr>
                <td>
                    <table style="margin-left: 20px;margin-top: 20px;background-color:#7cb974;padding:5px 30px 20px 30px;border-radius:10px;">
                        <tr>
                            <td style="font-weight:bold;font-size:14pt;color:#0e471b;text-align:center;">Skor TKA</td>
                        </tr>
                        <tr><td style="height:10px;"></td></tr>
                        <tr>
                            <td style="font-weight:bold;font-size:30pt;color:#01589A;text-align:center;"><?= $tka->rata_rata_individu; ?></td>
                        </tr>
                    </table>
                    <br/>
                    <!-- <span style="margin-left: 70px;font-size:8pt;">*Skor berdasarkan proporsi penilaian semua aspek</span> -->
                </td>
                <td style="width:20px;">
                    &nbsp;
                </td>
                <td>
                    <table border="1" cellspacing="0" style="margin-top: 20px;">
                        <tr>
                            <td style="background-color:#FCE4D6;font-weight:bold;font-size:10pt;text-align:center;" colspan="4">Kriteria</td>
                        </tr>
                        <tr>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Skor</td>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Warna</td>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Predikat</td>
                            <td style="background-color:#D8E9DA;font-weight:bold;font-size:10pt;text-align:center;">Keterangan</td>
                        </tr>
                        <tr>
                            <td style="font-size:9pt;padding: 3px 20px;background-color:#D8E9DA;">91,00 - 100</td>
                            <td style="padding-left:40px;background-color:#0002ee;">&nbsp;</td>
                            <td style="font-size:9pt;padding: 3px 5px 3px 3px;background-color:#D8E9DA;font-style: italic;text-align:center;">Distinguished Teacher</td>
                            <td style="font-size:7pt;padding: 3px 3px 3px 3px;background-color:#D8E9DA;">Potensi akademik sangat tinggi, mampu berpikir kritis, <br/> analitis, dan kompleks, dapat menjadi tutor</td>
                        </tr>
                        <tr>
                            <td style="font-size:9pt;padding: 3px 20px;background-color:#D8E9DA;">81,00 - 90,99</td>
                            <td style="padding-left:40px;background-color:#0440f6;">&nbsp;</td>
                            <td style="font-size:9pt;padding: 3px 5px 3px 3px;background-color:#D8E9DA;font-style: italic;text-align:center;">Proficient Teacher</td>
                            <td style="font-size:7pt;padding: 3px 3px 3px 3px;background-color:#D8E9DA;">Potensi akademik tinggi, siap menghadapi tantangan <br/> akademik tingkat lanjut secara mandiri</td>
                        </tr>
                        <tr>
                            <td style="font-size:9pt;padding: 3px 20px;background-color:#D8E9DA;">65,00 - 80,99</td>
                            <td style="padding-left:40px;background-color:#00a65a;">&nbsp;</td>
                            <td style="font-size:9pt;padding: 3px 5px 3px 3px;background-color:#D8E9DA;font-style: italic;text-align:center;">Competent Teacher</td>
                            <td style="font-size:7pt;padding: 3px 3px 3px 3px;background-color:#D8E9DA;">Potensi akademik baik, mampu melaksanakan <br/> proses pembelajaran dengan efektif, tetap perlu pembinaan</td>
                        </tr>
                        <tr>
                            <td style="font-size:9pt;padding: 3px 20px;background-color:#D8E9DA;">51,00 - 64,99</td>
                            <td style="padding-left:40px;background-color:#c48f0f;">&nbsp;</td>
                            <td style="font-size:9pt;padding: 3px 5px 3px 3px;background-color:#D8E9DA;font-style: italic;text-align:center;">Developing Teacher</td>
                            <td style="font-size:7pt;padding: 3px 3px 3px 3px;background-color:#D8E9DA;">Potensi akademik sedang, memerlukan penguatan <br/> pada beberapa aspek</td>
                        </tr>
                        <tr>
                            <td style="font-size:9pt;padding: 3px 20px;background-color:#D8E9DA;text-align:center;">&lt; 51,00</td>
                            <td style="padding-left:40px;background-color:#a7030a;">&nbsp;</td>
                            <td style="font-size:9pt;padding: 3px 5px 3px 3px;background-color:#D8E9DA;font-style: italic;text-align:center;">Intensive Improvement <br/>Teacher</td>
                            <td style="font-size:7pt;padding: 3px 3px 3px 3px;background-color:#D8E9DA;">Potensi akademik kurang, membutuhkan program <br/> penguatan akademik secara sistematis</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table border="0" style="margin-top: 10px;margin-left: 460px;padding:5px 10px;text-align: left;font-size: 10pt;">
            <tr>
                <td style="">Kota Bekasi, <?php echo formatTanggal(date('Y-m-d', strtotime('2026-07-07'))); ?></td>
            </tr>
            <tr>
                <td style="">Direktur LTRC Labschool Cibubur, </td>
            </tr>
            <tr>
                <td style=""><img style="width:100px;height:100px;" src="<?php echo FCPATH . '/asset/img/confidential/drdimas_signature.png'; ?>" /></td>
            </tr>
        </table>

        <table style="margin-top:0px;padding:5px 10px;text-align: left;">
            <tr>
                <td style="font-weight: bold;background-color:#BDD7EE;width: 150px;padding:2px 20px 2px 10px;text-align: center;">DOKUMEN PRIBADI</td>
                <td style="width:240;">&nbsp;</td>
                <td style="width: 200;">dr.Arief Dimas Dwiputro, M.Sc.</td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN 2: DETAIL ANALISIS KETERCAPAIAN INDIKATOR -->

    <div style="background-color:#BDD7EE;padding:5px;">
        <table border="0" cellspacing="0" style="width:100%;">
            <tr>
                <td style="width:130px;text-align:center;padding-top:15px;">
                    <img style="width:110px;height:90px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_vertical.png'; ?>" />
                </td>
                <td style="text-align:center;font-weight:bold;font-size:13pt;padding-top:15px;">
                    ANALISIS KETERCAPAIAN INDIKATOR<br/>
                    TES KEMAMPUAN AKADEMIK (TKA) <br/>GURU SD - SMP - SMA LABSCHOOL CIBUBUR <br/>TAHUN <?php echo $tka->tahun; ?><br/><br/>
                    <span style="font-weight:normal;font-size:6pt;color:#116AC4;text-decoration:underline;">
                        Jl. Raya Hankam 15-20 Jatiranggon, Jati Sampurna, Bekasi &nbsp; info@labschoolcibubur.sch.id &nbsp; 021 - 84304138, 84304140
                    </span>
                </td>
                <td style="width:130px;text-align:center;">
                    <img style="width:110px;height:110px;" src="<?php echo FCPATH . '/uploads/logo_yayasan.png'; ?>" />
                </td>
            </tr>
        </table>
    </div>

    <div style="background-color:#E8F0FB;padding:5px 15px 10px 15px;">
        <table border="0">
            <tr>
                <td style="width:100px;font-size:10pt;padding:3px;">Nama Guru</td>
                <td style="width:8px;">:</td>
                <td style="font-weight:bold;font-size:10pt;width:220px;"><?php echo $tka->nama_lengkap; ?></td>
                <td style="width:20px;">&nbsp;</td>
                <td style="width:100px;font-size:10pt;">Total Skor TKA</td>
                <td style="width:8px;">:</td>
                <td style="font-weight:bold;font-size:10pt;"><?= $tka->rata_rata_individu; ?></td>
            </tr>
            <tr>
                <td style="font-size:10pt;padding:3px;">Unit Kerja</td>
                <td>:</td>
                <td style="font-weight:bold;font-size:10pt;"><?php echo $tka->unit_kerja; ?></td>
            </tr>
        </table>
    </div>

    <?php
    // Lebar kolom fixed — total harus <= 695px (A4 portrait usable)
    // No: 25px | Indikator: 520px | Keterangan: 140px → total 695px
    define('COL_NO',  25);
    define('COL_IND', 530);
    define('COL_KET', 140);
    define('MAX_CHAR', 100); // wrap soal jika lebih dari ini

    function wrapSoal($text, $maxChar = 100) {
        // Potong dengan wordwrap agar tidak memotong kata
        return nl2br(wordwrap(htmlspecialchars($text), $maxChar, "\n", false));
    }

    $grouped = array();
    if (!empty($tka->detail_nilai)) {
        foreach ($tka->detail_nilai as $item) {
            $grouped[$item->judul_kategori][] = $item;
        }
    }

    $section_labels = array('A', 'B', 'C', 'D', 'E');
    $section_index  = 0;

    $grand_benar  = 0;
    $grand_salah  = 0;
    $grand_kosong = 0;

    foreach ($grouped as $kategori => $soal_list):
        $label      = $section_labels[$section_index++];
        $jml_benar  = 0;
        $jml_salah  = 0;
        $jml_kosong = 0;

        foreach ($soal_list as $s) {
            $k = strtolower(trim($s->keterangan));
            if ($k === 'benar')     $jml_benar++;
            elseif ($k === 'salah') $jml_salah++;
            else                    $jml_kosong++;
        }

        $grand_benar  += $jml_benar;
        $grand_salah  += $jml_salah;
        $grand_kosong += $jml_kosong;

        // Mapping skor & label
        $field_skor = '';
        $label_skor = 'Skor ' . $kategori;
        $nama_kat   = strtolower($kategori);
        if (strpos($nama_kat, 'indonesia') !== false) {
            $field_skor = 'bhs_indonesia';
            $label_skor = 'Skor Literasi Bahasa Indonesia';
        } elseif (strpos($nama_kat, 'inggris') !== false) {
            $field_skor = 'bhs_inggris';
            $label_skor = 'Skor Literasi Bahasa Inggris';
        } elseif (strpos($nama_kat, 'numerasi') !== false || strpos($nama_kat, 'kuantitatif') !== false) {
            $field_skor = 'numerasi';
            $label_skor = 'Skor Numerasi (Kuantitatif)';
        }
        $nilai_skor = ($field_skor && isset($tka->$field_skor)) ? $tka->$field_skor : '-';
    ?>

    <table border="0" cellspacing="0" cellpadding="0"
        style="margin-top:8px;margin-left:8px;width:<?php echo COL_NO+COL_IND+COL_KET; ?>px;border-collapse:collapse;table-layout:fixed;">

        <!-- Header kategori — full width, colspan 3 -->
        <tr>
            <td colspan="3"
                style="background-color:#2E4057;color:white;font-weight:bold;font-size:10pt;padding:5px 8px;width:<?php echo COL_NO+COL_IND+COL_KET; ?>px;">
                <?php echo $label . '. ' . $kategori; ?>
            </td>
        </tr>

        <!-- Sub-header kolom -->
        <tr>
            <td style="background-color:#4472C4;color:white;font-weight:bold;font-size:8pt;
                    text-align:center;width:<?php echo COL_NO; ?>px;padding:4px 2px;
                    border-right:1px solid #fff;">No</td>
            <td style="background-color:#4472C4;color:white;font-weight:bold;font-size:8pt;
                    width:<?php echo COL_IND; ?>px;padding:4px 6px;
                    border-right:1px solid #fff;">Indikator</td>
            <td style="background-color:#4472C4;color:white;font-weight:bold;font-size:8pt;
                    text-align:center;width:<?php echo COL_KET; ?>px;padding:4px 2px;">Keterangan</td>
        </tr>

        <!-- Baris data soal -->
        <?php foreach ($soal_list as $idx => $s):
            $ket = trim($s->keterangan);
            $k   = strtolower($ket);
            if ($k === 'benar') {
                $bg_ket = '#C6EFCE'; $fg_ket = '#276221'; $label_ket = 'Benar';
            } elseif ($k === 'salah') {
                $bg_ket = '#FFC7CE'; $fg_ket = '#9C0006'; $label_ket = 'Salah';
            } else {
                $bg_ket = '#FFEB9C'; $fg_ket = '#9C5700'; $label_ket = '-';
            }
            $row_bg = ($idx % 2 === 0) ? '#F2F7FF' : '#FFFFFF';
        ?>
        <tr>
            <td style="font-size:8pt;text-align:center;vertical-align:top;
                    width:<?php echo COL_NO; ?>px;padding:3px 2px;
                    background-color:<?php echo $row_bg; ?>;
                    border-top:1px solid #D9E1F2;border-right:1px solid #D9E1F2;">
                <?php echo $s->no_urut; ?>
            </td>
            <td style="font-size:8pt;vertical-align:top;
                    width:<?php echo COL_IND; ?>px;padding:3px 6px;
                    background-color:<?php echo $row_bg; ?>;
                    border-top:1px solid #D9E1F2;border-right:1px solid #D9E1F2;
                    word-wrap:break-word;overflow-wrap:break-word;">
                <?php echo wrapSoal($s->soal, MAX_CHAR); ?>
            </td>
            <td style="font-size:8pt;text-align:center;vertical-align:top;
                    width:<?php echo COL_KET; ?>px;padding:3px 2px;
                    background-color:<?php echo $bg_ket; ?>;color:<?php echo $fg_ket; ?>;
                    font-weight:bold;border-top:1px solid #D9E1F2;">
                <?php echo $label_ket; ?>
            </td>
        </tr>
        <?php endforeach; ?>

        <!-- Baris summary: masing-masing 1 baris sendiri, kolom No+Indikator digabung, Keterangan diisi angka -->
        <tr>
            <td colspan="2"
                style="background-color:#C6EFCE;font-size:8pt;padding:3px 8px;
                    border-top:2px solid #4472C4;border-right:1px solid #4472C4;">
                Jumlah Jawaban Benar
            </td>
            <td style="background-color:#C6EFCE;font-size:8pt;text-align:center;font-weight:bold;
                    padding:3px 2px;border-top:2px solid #4472C4;">
                <?php echo $jml_benar; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"
                style="background-color:#FFC7CE;font-size:8pt;padding:3px 8px;
                    border-top:1px solid #4472C4;border-right:1px solid #4472C4;">
                Jumlah Jawaban Salah
            </td>
            <td style="background-color:#FFC7CE;font-size:8pt;text-align:center;font-weight:bold;
                    padding:3px 2px;border-top:1px solid #4472C4;">
                <?php echo $jml_salah; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"
                style="background-color:#FFEB9C;font-size:8pt;padding:3px 8px;
                    border-top:1px solid #4472C4;border-right:1px solid #4472C4;">
                Jumlah Tidak Dijawab
            </td>
            <td style="background-color:#FFEB9C;font-size:8pt;text-align:center;font-weight:bold;
                    padding:3px 2px;border-top:1px solid #4472C4;">
                <?php echo $jml_kosong; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"
                style="background-color:#BDD7EE;font-size:9pt;font-weight:bold;
                    text-align:center;padding:4px 8px;
                    border-top:1px solid #4472C4;border-right:1px solid #4472C4;">
                <?php echo $label_skor; ?>
            </td>
            <td style="background-color:#BDD7EE;font-size:9pt;font-weight:bold;
                    text-align:center;padding:4px 2px;border-top:1px solid #4472C4;">
                <?php echo $nilai_skor; ?>
            </td>
        </tr>

    </table>

    <?php endforeach; ?>

    <!-- Grand Total -->
    <table border="0" cellspacing="0"
        style="margin-top:10px;margin-left:8px;width:695px;border-collapse:collapse;table-layout:fixed;">
        <tr>
            <td style="width:25px;padding:0px;">&nbsp;</td>
            <td style="width:575px;padding:0px;">&nbsp;</td>
            <td style="width:140px;padding:0px;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"
                style="background-color:#2E4057;color:white;font-size:9pt;padding:4px 8px;
                    border-bottom:1px solid #4472C4;border-right:1px solid #4472C4;">
                Jumlah Total Jawaban Benar
            </td>
            <td style="background-color:#C6EFCE;color:#276221;font-size:9pt;font-weight:bold;
                    text-align:center;padding:4px 2px;border-bottom:1px solid #4472C4;">
                <?php echo $grand_benar; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"
                style="background-color:#2E4057;color:white;font-size:9pt;padding:4px 8px;
                    border-bottom:1px solid #4472C4;border-right:1px solid #4472C4;">
                Jumlah Total Jawaban Salah
            </td>
            <td style="background-color:#FFC7CE;color:#9C0006;font-size:9pt;font-weight:bold;
                    text-align:center;padding:4px 2px;border-bottom:1px solid #4472C4;">
                <?php echo $grand_salah; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"
                style="background-color:#2E4057;color:white;font-size:9pt;padding:4px 8px;
                    border-right:1px solid #4472C4;">
                Jumlah Total Tidak Menjawab (dikosongkan)
            </td>
            <td style="background-color:#FFEB9C;color:#9C5700;font-size:9pt;font-weight:bold;
                    text-align:center;padding:4px 2px;">
                <?php echo $grand_kosong; ?>
            </td>
        </tr>
    </table>
</body>
</html>