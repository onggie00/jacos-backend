<?php
/**
 * Kwitansi Pembayaran — desain JACOS (ref: Berkas_sample/Kwitansi_JACOS.pdf, A5 landscape)
 * Renderer: HTML2PDF (TCPDF 5). Font wajib dejavusans (glyph checkbox U+2610/U+2611).
 * CATATAN html2pdf v4: div dalam td di-drop; text-align:right di td hanya jalan bila
 * td punya width eksplisit; float div % relatif ke page width. Makanya layout ini
 * full-table dengan td width eksplisit.
 * Variabel kontrak lama tetap diterima: $jenjang,$nama_ortu,$nama_lengkap,$va_number,
 * $tahun_ajaran,$total_biaya,$total_biaya_terbilang,$jenis_kwitansi,$jalur, dsb.
 * Variabel tambahan (opsional): $tanggal_bayar,$keterangan,$payment_method,$id_tingkatan,$no_transaksi.
 */
if (!function_exists('jacos_kwitansi_val')) {
	function jacos_kwitansi_val($v)
	{
		return isset($v) ? html_escape($v) : '';
	}
}
if (!function_exists('jacos_kwitansi_cb')) {
	function jacos_kwitansi_cb($checked)
	{
		return $checked ? "\xe2\x98\x91" : "\xe2\x98\x90";
	}
}
$tanggal_bayar_en = date("d F Y", strtotime(!empty($tanggal_bayar) ? $tanggal_bayar : date("Y-m-d")));
// Level: KB -> P1/P2 (id_tingkatan 1/2), TK -> K1/K2, SD -> Primary
$id_ting = isset($id_tingkatan) ? $id_tingkatan : '';
$cb_p1 = jacos_kwitansi_cb($jenjang == 'kb' && $id_ting == '1');
$cb_p2 = jacos_kwitansi_cb($jenjang == 'kb' && $id_ting == '2');
$cb_k1 = jacos_kwitansi_cb($jenjang == 'tk' && $id_ting == '1');
$cb_k2 = jacos_kwitansi_cb($jenjang == 'tk' && $id_ting == '2');
$cb_primary = jacos_kwitansi_cb($jenjang == 'sd');
// Payment Method: label dari web_payment_method, cocok persis dengan opsi desain
$pm = isset($payment_method) ? $payment_method : '';
$cb_cash = jacos_kwitansi_cb($pm == 'Cash');
$cb_transfer = jacos_kwitansi_cb($pm == 'Transfer BNI/BCA');
$cb_qris = jacos_kwitansi_cb($pm == 'QRIS BNI/BCA');
$amount = number_format(isset($total_biaya) ? (float)$total_biaya : 0, 0, "", ".");
$amount_words = isset($total_biaya_terbilang) ? ucfirst(strtolower(trim($total_biaya_terbilang))) : '';
$keterangan_txt = '';
if (!empty($keterangan)) {
	$keterangan_txt = $keterangan;
} else if (isset($jenis_kwitansi) && isset($tahun_ajaran)) {
	if ($jenis_kwitansi == "uang pendaftaran") {
		$keterangan_txt = "Pembayaran Pendaftaran Siswa Baru " . $jenjang . " JAKARTA COSMOPOLITE ISLAMIC SCHOOL Tahun Ajaran " . $tahun_ajaran;
	} else if ($jenis_kwitansi == "uang pangkal") {
		$keterangan_txt = "Pembayaran Uang Pangkal Dana Pendidikan Siswa Baru " . $jenjang . " JAKARTA COSMOPOLITE ISLAMIC SCHOOL Tahun Ajaran " . $tahun_ajaran;
	}
}
$keterangan_txt = nl2br(html_escape($keterangan_txt));
?>
<page format="A5" orientation="landscape" backtop="8mm" backbottom="8mm" backleft="8mm" backright="8mm">
	<!-- Header -->
	<table style="width: 194mm;">
		<tr>
			<td style="width: 45%;"><img style="width: 132px;" src="<?php echo FCPATH; ?>/uploads/image_assets/logo_jacos_horizontal.png" /></td>
			<td style="width: 55%; text-align: right; vertical-align: bottom; font-size: 9pt; font-weight: bold; color: #1071B0;">jacos.id | @jacos.school | +62 821-4000-0477<br/>Swadaya Raya Street 02, Duren Sawit, East Jakarta.</td>
		</tr>
	</table>
	<table style="width: 194mm;">
		<tr>
			<td style="width: 87.3mm; font-size: 1px;">&nbsp;</td>
			<td style="width: 35.3mm; height: 5px; background-color: #1071B0; font-size: 1px;">&nbsp;</td>
			<td style="width: 71.4mm; height: 5px; background-color: #F0D020; font-size: 1px;">&nbsp;</td>
		</tr>
	</table>

	<!-- Judul -->
	<table style="width: 194mm; margin-top: 4px;">
		<tr>
			<td style="width: 30%; font-size: 1px;">&nbsp;</td>
			<td style="width: 70%; text-align: right; font-size: 11pt; font-weight: bold;">Payment Receipt / Kwitansi Pembayaran</td>
		</tr>
	</table>

	<!-- Field -->
	<table style="width: 194mm; margin-top: 4px; font-family: dejavusans;">
		<tr>
			<td style="width: 28%; font-size: 10pt; font-weight: bold; padding: 2px 0;">Receipt No</td>
			<td style="width: 3%; font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; border-bottom: 0.6px solid #595959; padding: 2px 2px;"><?php echo jacos_kwitansi_val(isset($no_transaksi) ? $no_transaksi : null); ?></td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Date</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; border-bottom: 0.6px solid #595959; padding: 2px 2px;"><?php echo $tanggal_bayar_en; ?></td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Parent Name</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; border-bottom: 0.6px solid #595959; padding: 2px 2px;"><?php echo jacos_kwitansi_val(isset($nama_ortu) ? $nama_ortu : null); ?></td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Student Name</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; border-bottom: 0.6px solid #595959; padding: 2px 2px;"><?php echo jacos_kwitansi_val(isset($nama_lengkap) ? $nama_lengkap : null); ?></td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Amount</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; border-bottom: 0.6px solid #595959; padding: 2px 2px;"><?php echo $amount; ?></td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Amount Words</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; border-bottom: 0.6px solid #595959; padding: 2px 2px;"><?php echo $amount_words; ?></td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Level</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; padding: 2px 2px;"><?php echo $cb_p1; ?> P1 - <?php echo $cb_p2; ?> P2 - <?php echo $cb_k1; ?> K1 - <?php echo $cb_k2; ?> K2 - <?php echo $cb_primary; ?> Primary</td>
		</tr>
		<tr>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">Payment Method</td>
			<td style="font-size: 10pt; font-weight: bold; padding: 2px 0;">:</td>
			<td style="width: 69%; font-size: 11pt; color: #595959; padding: 2px 2px;"><?php echo $cb_cash; ?> Cash - <?php echo $cb_transfer; ?> Transfer BNI/BCA - <?php echo $cb_qris; ?> QRIS BNI/BCA</td>
		</tr>
	</table>

	<!-- Bawah: description kiri, ttd kanan -->
	<table style="width: 194mm; margin-top: 5px;">
		<tr>
			<td style="width: 45%; border: 1px solid #000000; padding: 5px 8px; vertical-align: top; margin-right: 10mm;">
				<span style="font-size: 8pt; font-weight: bold;">Payment Description</span><br/>
				<span style="font-size: 11pt; color: #595959;"><?php echo $keterangan_txt; ?></span>
			</td>
			<td style="width: 55%; text-align: right; vertical-align: top; font-size: 10pt; font-weight: bold; padding-left: 10mm;">
				Jakarta, <?php echo $tanggal_bayar_en; ?><br/>
				<br/>
				Authorized by,<br/>
				<br/>
				<br/>
				<br/>
				<br/>
				Admin JACOS
			</td>
		</tr>
	</table>
</page>
