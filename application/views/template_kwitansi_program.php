<?php
/**
 * Wrap text with <br/> at word boundary before $limit characters.
 */
function wrap_text_limit($text, $limit = 80) {
    if (empty($text) || strlen($text) <= $limit) {
        return $text;
    }
    $words = explode(' ', $text);
    $lines = array();
    $current = '';
    foreach ($words as $word) {
        if (strlen($current) + strlen($word) + 1 > $limit && !empty($current)) {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = empty($current) ? $word : $current . ' ' . $word;
        }
    }
    if (!empty($current)) {
        $lines[] = $current;
    }
    return implode('<br/>', $lines);
}

$pembayaran_text = (!empty($sub_jenis_kegiatan)) ? $jenis_kegiatan . ' - ' . $sub_jenis_kegiatan : $jenis_kegiatan;
$pembayaran_text = wrap_text_limit($pembayaran_text, 80);
$nama_program_text = wrap_text_limit($nama_program, 80);
$terbilang_text = wrap_text_limit($nominal_pengajuan_teks . ' RUPIAH', 80);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>KWITANSI/BUKTI PEMBAYARAN</title>
</head>
<body >
	<div style="margin-top:50px;padding-left: 40px;">
		<div style="font-size: 16pt; text-align: center;font-weight: bold;">
			KWITANSI/BUKTI PEMBAYARAN
		</div>
        <div style="font-size: 14pt; text-align: center;">
			Nomor : <?= $nomor_kwitansi; ?>
		</div>

		<div style="width:700px;margin-top:10px;">
			<table style="font-size: 10pt;">
				<tr>
					<td style="padding:10px;">Sudah terima dari</td>
					<td style="padding:10px;">:</td>
					<td style="padding:10px;"><?php echo "Labschool Cibubur"; ?></td>
				</tr>
				<tr>
					<td style="padding:10px;">Jumlah uang</td>
					<td style="padding:10px;">:</td>
					<td style="padding:10px;"><?php echo "Rp. ".number_format($nominal_pengajuan,0,"","."); ?></td>
				</tr>
                <tr>
					<td style="padding:10px;">Terbilang</td>
					<td style="padding:10px;">:</td>
					<td style="padding:10px;"><?php echo $terbilang_text; ?></td>
				</tr>
                <tr>
					<td style="padding:10px;">Untuk pembayaran</td>
					<td style="padding:10px;">:</td>
					<td style="padding-left:10px;padding-top:10px"><?php echo $pembayaran_text; ?></td>
				</tr>
                <tr>
					<td style="padding:0px;">&nbsp;</td>
					<td style="padding:0px;">&nbsp;</td>
					<td style="padding-left:10px;"><?php echo $nama_program_text; ?></td>
				</tr>
			</table>
		</div>
        <div style="margin-top: 150px;">
			<table style="font-family: Times;">
				<tr>
					<td style="padding-right: 350px;">&nbsp;</td>
					<td style="padding-right: 10px;"><span >Kota Bekasi,</span></td>
					<td style="text-align: right;"><span><?php echo formatTanggal($tanggal_pencairan); ?></span></td>
				</tr>
			</table>
		</div>

		<table style="font-size: 10pt;">
			<tr>
				<td>
					<div style="width: 250px;">
						<span> Mengetahui</span><br/>
						<span>
							<?php 
								if ($jenjang == "SD") {
									echo "Kepala SD Labschool Cibubur";
								}
								else if ($jenjang == "SMP") {
									echo "Kepala SMP Labschool Cibubur";
								}
                                else if ($jenjang == "SMA") {
									echo "Kepala SMA Labschool Cibubur";
								}
							?>
						</span>
					</div>
                    <div style="padding-top: 70px;">
                        <span style="text-decoration:underline;"><?= $kepala_sekolah_nama; ?></span><br/>
                        <span style="padding-bottom:10px;"><?= $kepala_sekolah_npp; ?></span>
                    </div>
				</td>
				<td>
					<div style="width: 250px;padding-left:100px;">
						<br/>
						<span>
							<?php 
                            	if ($jenjang == "SD") {
                            	    echo "Kepala TU SD Labschool Cibubur";
                            	}
                            	else if ($jenjang == "SMP") {
                            	    echo "Kepala TU SMP Labschool Cibubur";
                            	}
                            	else if ($jenjang == "SMA") {
                            	    echo "Kepala TU SMA Labschool Cibubur";
                            	}
                            ?>
						</span>
					</div>
					<div style="padding-top: 70px;padding-left:100px;">
                    	<span style="text-decoration:underline;"><?= $kepala_tu_nama; ?></span><br/>
                    	<span style="padding-bottom:10px;"><?= $kepala_tu_npp; ?></span>
                    </div>
				</td>
			</tr>
		</table>
        <table style="margin-top:10px;">
            <tr>
				<td>SETUJU dibebankan pada Mata Anggaran</td>
                <td style="padding-left: 10px;">: <?= $nomor_program; ?></td>
                <td style="text-align:right;padding-left:20px;"><?= "TA ".$tahun_ajaran_sekarang; ?></td>
			</tr>
        </table>
        <table style="margin-top:10px;">
            <tr>
				<td>
					A.n BPH Labschool Cibubur<br/>
					Kepala Sekretariat
				</td>
			</tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td style="padding-top:70px;">
                <span style="text-decoration:underline;"><?= $kepala_sekretariat_nama; ?></span><br/>
                <span><?= $kepala_sekretariat_npp; ?></span>
                </td>
            </tr>
        </table>
	</div>
</body>
</html>
