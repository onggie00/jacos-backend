<?php
/**
 * Kwitansi Pembayaran (OPEN / tipe_tagihan='open') — desain JACOS, mengikuti template_kwitansi.php
 * (ref: Berkas_sample/Kwitansi_JACOS.pdf, A5 landscape). Satu bahasa: Indonesia.
 * Renderer: HTML2PDF (TCPDF 5). Font wajib dejavusans (glyph checkbox U+2610/U+2611).
 * CATATAN html2pdf v4: div dalam td di-drop; text-align/right butuh td width eksplisit;
 * layout full-table dengan td width eksplisit.
 * Variabel kontrak lama tetap diterima: $jenjang,$nama_ortu,$nama_lengkap,$va_number,
 * $tahun_ajaran,$total_biaya,$total_biaya_terbilang,$jenis_kwitansi,$jalur,$termin,
 * $sisa_tagihan,$riwayat (HTML table).
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
if (!function_exists('jacos_kwitansi_tgl')) {
	function jacos_kwitansi_tgl($tgl)
	{
		if (function_exists('formatTanggal')) {
			return formatTanggal($tgl);
		}
		return date("d-m-Y", strtotime($tgl));
	}
}
// Tanggal = tanggal bayar (bukan hari ini); fallback hari ini bila kosong
$tanggal_kwitansi = jacos_kwitansi_tgl(!empty($tanggal_bayar) ? $tanggal_bayar : date("Y-m-d"));
// Level: KB -> P1/P2 (id_tingkatan 1/2), TK -> K1/K2, SD -> Primary
$jenjang_l = strtolower(isset($jenjang) ? $jenjang : '');
$id_ting = isset($id_tingkatan) ? $id_tingkatan : '';
$cb_p1 = jacos_kwitansi_cb($jenjang_l == 'kb' && $id_ting == '1');
$cb_p2 = jacos_kwitansi_cb($jenjang_l == 'kb' && $id_ting == '2');
$cb_k1 = jacos_kwitansi_cb($jenjang_l == 'tk' && $id_ting == '1');
$cb_k2 = jacos_kwitansi_cb($jenjang_l == 'tk' && $id_ting == '2');
$cb_primary = jacos_kwitansi_cb($jenjang_l == 'sd');
// Payment Method: label checkbox = nama di web_payment_method; kosong/tidak dikenal -> Cash
$pm = isset($payment_method) ? $payment_method : '';
if ($pm != 'Cash' && $pm != 'Bank Transfer' && $pm != 'QRIS' && $pm != 'Virtual Account') {
	$pm = 'Cash';
}
$cb_cash = jacos_kwitansi_cb($pm == 'Cash');
$cb_va = jacos_kwitansi_cb($pm == 'Virtual Account');
$cb_transfer = jacos_kwitansi_cb($pm == 'Bank Transfer');
$cb_qris = jacos_kwitansi_cb($pm == 'QRIS');
$setoran = number_format(isset($total_biaya) ? (float) $total_biaya : 0, 0, "", ".");
$terbilang_txt = isset($total_biaya_terbilang) ? ucfirst(strtolower(trim($total_biaya_terbilang))) : '';
$sisa = number_format(isset($sisa_tagihan) ? (float) $sisa_tagihan : 0, 0, "", ".");
$termin_txt = isset($termin) ? $termin : 0;
// Keterangan = description transaksi; fallback teks lama berbasis jenis_kwitansi bila kosong
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
<page format="A5" orientation="landscape" backtop="7mm" backbottom="7mm" backleft="7mm" backright="7mm">
	<!-- Header -->
	<table style="width: 196mm;">
		<tr>
			<td style="width: 45%;"><img style="height: 48px;" src="<?php echo FCPATH; ?>/uploads/image_assets/JACOS-VERTIKAL.png" /></td>
			<td style="width: 55%; text-align: right; vertical-align: bottom; font-size: 8pt; font-weight: bold; color: #1071B0;">jacos.id | @jacos.school | +62 821-4000-0477<br/>Swadaya Raya Street 02, Duren Sawit, East Jakarta.</td>
		</tr>
	</table>
	<table style="width: 196mm;">
		<tr>
			<td style="width: 88.3mm; font-size: 1px;">&nbsp;</td>
			<td style="width: 35.3mm; height: 4px; background-color: #1071B0; font-size: 1px;">&nbsp;</td>
			<td style="width: 72.4mm; height: 4px; background-color: #F0D020; font-size: 1px;">&nbsp;</td>
		</tr>
	</table>

	<!-- Judul -->
	<table style="width: 196mm; margin-top: 3px;">
		<tr>
			<td style="width: 30%; font-size: 1px;">&nbsp;</td>
			<td style="width: 70%; text-align: right; font-size: 10pt; font-weight: bold;">Kwitansi Pembayaran</td>
		</tr>
	</table>

	<!-- Baris atas: field kiri, ringkasan kanan -->
	<table style="width: 196mm; margin-top: 3px; font-family: dejavusans;">
		<tr>
			<td style="width: 55%; vertical-align: top; padding-right: 3mm;">
				<table style="width: 100%;">
					<tr>
						<td style="width: 38%; font-size: 8.5pt; font-weight: bold; padding: 1px 0;">No. Kwitansi</td>
						<td style="width: 4%; font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 9pt; color: #595959; border-bottom: 0.6px solid #595959;"><?php echo jacos_kwitansi_val(isset($no_transaksi) ? $no_transaksi : null); ?></td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Tanggal</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 9pt; color: #595959; border-bottom: 0.6px solid #595959;"><?php echo $tanggal_kwitansi; ?></td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Nama Orang Tua/Wali</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 9pt; color: #595959; border-bottom: 0.6px solid #595959;"><?php echo jacos_kwitansi_val(isset($nama_ortu) ? $nama_ortu : null); ?></td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Nama Siswa</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 9pt; color: #595959; border-bottom: 0.6px solid #595959;"><?php echo jacos_kwitansi_val(isset($nama_lengkap) ? $nama_lengkap : null); ?></td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Virtual Account</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 9pt; color: #595959; border-bottom: 0.6px solid #595959;"><?php echo jacos_kwitansi_val(isset($va_number) ? $va_number : null); ?></td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Tahun Ajaran</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 9pt; color: #595959; border-bottom: 0.6px solid #595959;"><?php echo jacos_kwitansi_val(isset($tahun_ajaran) ? $tahun_ajaran : null); ?></td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Jenjang</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 8pt; color: #595959; padding: 1px 2px;"><?php echo $cb_p1; ?> P1 - <?php echo $cb_p2; ?> P2 - <?php echo $cb_k1; ?> K1 - <?php echo $cb_k2; ?> K2 - <?php echo $cb_primary; ?> Primary</td>
					</tr>
					<tr>
						<td style="font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Metode Pembayaran</td>
						<td style="font-size: 8.5pt; font-weight: bold;">:</td>
						<td style="width: 58%; font-size: 8pt; color: #595959; padding: 1px 2px;"><?php echo $cb_cash; ?> Cash - <?php echo $cb_va; ?> Virtual Account - <?php echo $cb_transfer; ?> Bank Transfer - <?php echo $cb_qris; ?> QRIS</td>
					</tr>
				</table>
			</td>
			<td style="width: 45%; vertical-align: top;">
				<table style="width: 100%; border: 1px solid #000000;">
					<tr>
						<td style="width: 45%; font-size: 8.5pt; font-weight: bold; padding: 2px 6px;">Jumlah Setoran</td>
						<td style="width: 55%; font-size: 10pt; font-weight: bold; padding: 2px 6px; text-align: right;">Rp <?php echo $setoran; ?>,-</td>
					</tr>
					<tr>
						<td style="width: 45%; font-size: 8.5pt; font-weight: bold; padding: 2px 6px; vertical-align: top;">Terbilang</td>
						<td style="width: 55%; font-size: 8.5pt; color: #595959; padding: 2px 6px;"><?php echo $terbilang_txt; ?></td>
					</tr>
					<tr>
						<td style="width: 45%; font-size: 8.5pt; font-weight: bold; padding: 2px 6px;">Termin</td>
						<td style="width: 55%; font-size: 9pt; padding: 2px 6px; text-align: right;"><?php echo jacos_kwitansi_val($termin_txt); ?></td>
					</tr>
					<tr>
						<td style="width: 45%; font-size: 8.5pt; font-weight: bold; padding: 2px 6px;">Sisa Tagihan</td>
						<td style="width: 55%; font-size: 10pt; font-weight: bold; padding: 2px 6px; text-align: right;">Rp <?php echo $sisa; ?>,-</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<!-- Riwayat Pembayaran -->
	<table style="width: 196mm; margin-top: 3px; font-family: dejavusans;">
		<tr>
			<td style="width: 100%; font-size: 8.5pt; font-weight: bold; padding: 1px 0;">Riwayat Pembayaran:</td>
		</tr>
		<tr>
			<td style="width: 100%; font-size: 7pt; padding: 1px 4px;"><?php echo isset($riwayat) ? $riwayat : '&nbsp;'; ?></td>
		</tr>
	</table>

	<!-- Bawah: keterangan kiri, ttd kanan -->
	<table style="width: 196mm; margin-top: 3px;">
		<tr>
			<td style="width: 55%; border: 1px solid #000000; padding: 4px 6px; vertical-align: top;">
				<span style="font-size: 8pt; font-weight: bold;">Keterangan:</span><br/>
				<span style="font-size: 8.5pt; color: #595959;"><?php echo $keterangan_txt; ?></span>
			</td>
			<td style="width: 45%; text-align: right; vertical-align: top; font-size: 9pt; font-weight: bold; padding-left: 8mm;">
				Jakarta, <?php echo $tanggal_kwitansi; ?><br/>
				<br/>
				Authorized by,<br/>
				<br/>
				<br/>
				<br/>
				Admin JACOS
			</td>
		</tr>
	</table>
</page>
