<?php
$img_depan = FCPATH.'uploads/acara/'.$acara->file_certificate;
$img_belakang = !empty($acara->file_certificate_back) ? FCPATH.'uploads/acara/'.$acara->file_certificate_back : '';

// Helper: konversi font_style ke CSS inline
function fontStyleCSS($style) {
    $css = '';
    if ($style === 'bold' || $style === 'bold_italic') $css .= 'font-weight:bold;';
    if ($style === 'italic' || $style === 'bold_italic') $css .= 'font-style:italic;';
    return $css;
}

// Render bersyarat: skip jika posisi NULL/0 atau flag show=0 (untuk NPP & Instansi)
$show_npp = !empty($style_sertifikat['show_npp'])
    && !empty($style_sertifikat['npp_pos_top'])
    && !empty($style_sertifikat['npp_pos_left']);
$show_instansi = !empty($style_sertifikat['show_instansi'])
    && !empty($nama_instansi)
    && !empty($style_sertifikat['instansi_pos_top'])
    && !empty($style_sertifikat['instansi_pos_left']);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Sertifikat Acara</title>
</head>
<body style="margin:0;padding:0;font-family:arial;">

<!-- Halaman Depan: background-image + absolute positioning -->
<div style="position:relative;width:297mm;height:210mm;background-image:url(<?= $img_depan; ?>);background-size:297mm 210mm;background-repeat:no-repeat;page-break-after:always;">
	<div style="position:absolute;top:<?= $style_sertifikat['no_pos_top']; ?>%;left:<?= $style_sertifikat['no_pos_left']; ?>%;">
		<span style="font-size:<?= $style_sertifikat['no_font_size']; ?>pt;<?= fontStyleCSS($style_sertifikat['no_font_style']); ?>"><?= !empty($nomor_sertifikat) ? $nomor_sertifikat : '&nbsp;'; ?></span>
	</div>
	<div style="position:absolute;top:<?= $style_sertifikat['nama_pos_top']; ?>%;left:<?= $style_sertifikat['nama_pos_left']; ?>%;">
		<span style="font-size:<?= $style_sertifikat['nama_font_size']; ?>pt;<?= fontStyleCSS($style_sertifikat['nama_font_style']); ?>"><?= $user->nama_lengkap; ?></span>
	</div>
	<?php if ($show_instansi): ?>
	<div style="position:absolute;top:<?= $style_sertifikat['instansi_pos_top']; ?>%;left:<?= $style_sertifikat['instansi_pos_left']; ?>%;">
		<span style="font-size:<?= $style_sertifikat['instansi_font_size']; ?>pt;<?= fontStyleCSS($style_sertifikat['instansi_font_style']); ?>"><?= strtoupper($nama_instansi); ?></span>
	</div>
	<?php endif; ?>
	<?php if ($show_npp): ?>
	<div style="position:absolute;top:<?= $style_sertifikat['npp_pos_top']; ?>%;left:<?= $style_sertifikat['npp_pos_left']; ?>%;">
		<span style="font-size:<?= $style_sertifikat['npp_font_size']; ?>pt;<?= fontStyleCSS($style_sertifikat['npp_font_style']); ?>"><?= $user->npp; ?></span>
	</div>
	<?php endif; ?>
</div>

<?php if (!empty($img_belakang)): ?>
<!-- Halaman Belakang -->
<div style="width:297mm;height:210mm;background-image:url(<?= $img_belakang; ?>);background-size:297mm 210mm;background-repeat:no-repeat;">
</div>
<?php endif; ?>

</body>
</html>
