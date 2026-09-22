<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kartu Peserta PPSBB</title>
</head>
<style type="text/css">
    #divid{
        margin-top: 0px;
        margin-left: 0px;
        background-image: url(<?php echo base_url() . 'uploads/bg_peserta_ppsbb_ft_2025.jpg'; ?>);
        background-size: 100%;
        background-repeat: no-repeat;
        height: 720px;
        width: 1040px;
        font-family: times;
    }
    .field-dots{
        border-bottom: 2px dotted #999;
        width: 320px;
        font-size: 13pt;
    }
</style>
<body>
    <div id="divid">
        <table border="0" style="margin-top: 10px;margin-left: 40px;font-family: arial;">
            <tr>
                <td style="width: 130;text-align:center;padding-top: 15px;">
                    <img style="width:90px;height:90px;" src="<?php echo FCPATH . '/uploads/logo_labschool_cibubur_vertical.png'; ?>" />
                </td>
                <td style="width: 620;text-align: center;font-weight:bold;font-size: 16pt;padding-top: 15px;">
                    KARTU PESERTA<br/>
                    PSB FRANCE TRACK SMA LABSCHOOL CIBUBUR<br/>
                    JALUR PRESTASI<br/>
                    <span style="font-size: 12pt;">TAHUN PELAJARAN <span style="color: #e8434c;"><?= $tahun_ajaran_psb; ?></span></span>
                </td>
                <td style="width: 130;text-align:left;padding-top: 25px;"><img style="width:140px;height:65px;" src="<?php echo FCPATH . '/uploads/logo_ft.png'; ?>" /></td>
            </tr>
            <tr>
                <td style="border-top: 8px solid black;" colspan="3">&nbsp;</td>
            </tr>
        </table>

        <table border="0" style="margin-left: 40px;margin-top: 8px;width: 980px;">
            <tr>
                <td style="width:300px;border: 3px solid black;font-size: 14pt;font-weight:bold;padding: 8px 0px 8px 20px;font-family: times;"  ><span style="text-decoration: underline;">Pilihan Sekolah :</span><br/><span style="font-size: 14pt;">Labschool Cibubur</span></td>
                <td style="width:340px;">&nbsp;</td>
                <td style="width:260px;border: 3px solid black;font-size: 14pt;font-weight:bold;padding: 8px 20px;font-family: times;"  ><span style="text-decoration: underline;">No. Peserta</span><br/><span style="font-size: 14pt;color: #e8434c; text-align: left; display: block;"><?php echo $siswa->no_peserta; ?></span></td>
                <td style="width:60px;">&nbsp;</td>
            </tr>
        </table>

        <table border="0" style="margin-left: 40px;margin-top: 10px;">
            <tr>
                <td style="width: 180px;vertical-align: top;padding: 5px 10px;">
                    <img style="width: 150px; height: 210px;" src="<?php echo FCPATH . '/uploads/siluet_peserta.png'; ?>" />
                </td>
                <td style="vertical-align: top;padding: 5px 10px;font-family: times;">
                    <table border="0" style="font-size: 14pt;">
                        <tr>
                            <td colspan="3" style="font-size: 16pt;font-weight: bold;font-family: times;text-transform: uppercase;padding-bottom: 10px;"><?php echo $siswa->nama_lengkap; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 150px;font-size: 13pt;">NISN</td>
                            <td style="font-size: 13pt;">:</td>
                            <td class="field-dots">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size: 13pt;">Jenis Kelamin</td>
                            <td style="font-size: 13pt;">:</td>
                            <td class="field-dots">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size: 13pt;">No. Handphone</td>
                            <td style="font-size: 13pt;">:</td>
                            <td class="field-dots">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size: 13pt;">Sekolah Asal</td>
                            <td style="font-size: 13pt;">:</td>
                            <td class="field-dots">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size: 13pt;">Jalur Prestasi</td>
                            <td style="font-size: 13pt;">:</td>
                            <td class="field-dots">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: bottom;padding: 5px 10px;font-family: times;font-size: 10pt;width: 300px;">
                    <table border="0">
                        <tr>
                            <td colspan="2" style="font-weight: bold;font-size: 12pt;text-align: center;">
                                Kontak Kami
                            </td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>:&nbsp;&nbsp;021-84301138, 84301140</td>
                        </tr>
                        <tr>
                            <td>Whatsapp</td>
                            <td>:&nbsp;&nbsp;0812-9040-6057</td>
                        </tr>
                    </table>
                    (Hanya melayani pukul 07.00 - 16.00 WIB di hari kerja)
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
