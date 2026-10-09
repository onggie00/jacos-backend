<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Psb_paket — kalkulasi paket pilihan pembayaran PSB (KB/TK/SD).
 * Sumber nominal: tabel biaya_pendaftaran (reguler + kolom internal).
 * Rumus (integer):
 *   opsi 1 = pendaftaran
 *   opsi 2 = pendaftaran + nominal_cicilan_pertama
 *   opsi 3 = pendaftaran + daftar_ulang - diskon  (diskon = round(daftar_ulang * persen / 100))
 * Internal (TK asal list_sekolah_tk.is_internal = 1) memakai kolom *_internal.
 */
class Psb_paket {

	var $CI;

	function __construct()
	{
		$this->CI =& get_instance();
	}

	/** row biaya_pendaftaran per jenjang (KB/TK/SD) */
	function get_biaya($jenjang)
	{
		return $this->CI->mymodel->getbywhere("biaya_pendaftaran", "jenjang", strtoupper($jenjang), "row");
	}

	/**
	 * Internal bila sekolah_asal siswa (nama TK asal) terdaftar di list_sekolah_tk
	 * dengan is_internal = 1. Jenjang tanpa isian TK asal id-based otomatis reguler.
	 */
	function is_internal($sekolah_asal)
	{
		$sekolah_asal = trim((string) $sekolah_asal);
		if ($sekolah_asal == '') {
			return false;
		}
		$cek = $this->CI->mymodel->withquery("select id_list_sekolah_tk from list_sekolah_tk where is_internal = 1 and nama_sekolah = '" . $this->CI->db->escape_str($sekolah_asal) . "'", "row");
		return !empty($cek);
	}

	/** deskripsi (bahasa Inggris, masuk kwitansi) per pilihan; 1/2/3 */
	function deskripsi($pilihan, $diskon_persen)
	{
		$pilihan = (int) $pilihan;
		if ($pilihan == 2) {
			return "Admission Fee + First Installment of the Enrollment Fee";
		}
		if ($pilihan == 3) {
			$persen = $this->format_persen($diskon_persen);
			return "Admission Fee + Full Payment Enrollment Fee - (Disc. " . $persen . "%)";
		}
		return "Admission Fee";
	}

	/** 10.00 -> "10", 7.50 -> "7.5" */
	function format_persen($diskon_persen)
	{
		$v = (float) $diskon_persen;
		$s = ($v == (int) $v) ? (string) (int) $v : rtrim(rtrim(number_format($v, 2, ".", ""), "0"), ".");
		return $s;
	}

	/**
	 * Satu opsi: rincian (label + nominal), diskon_persen, total, deskripsi.
	 * $biaya = row biaya_pendaftaran.
	 */
	function hitung_opsi($biaya, $is_internal, $pilihan)
	{
		$pilihan  = (int) $pilihan;
		$diskon_p = (float) $biaya->diskon_pelunasan_persen;

		$daftar = (int) (($is_internal && isset($biaya->nominal_pendaftaran_internal) && $biaya->nominal_pendaftaran_internal > 0) ? $biaya->nominal_pendaftaran_internal : $biaya->nominal_pendaftaran);
		$pangkal = (int) (($is_internal && isset($biaya->nominal_daftar_ulang_internal) && $biaya->nominal_daftar_ulang_internal > 0) ? $biaya->nominal_daftar_ulang_internal : $biaya->nominal_daftar_ulang);
		$cicilan = (int) $biaya->nominal_cicilan_pertama;

		$rincian = array();
		$diskon_persen = 0;
		if ($pilihan == 2) {
			$rincian[] = array("label" => "Uang Pendaftaran", "nominal" => $daftar);
			$rincian[] = array("label" => "Cicilan Pertama Uang Pangkal", "nominal" => $cicilan);
			$total = $daftar + $cicilan;
		}
		else if ($pilihan == 3) {
			$diskon = (int) round($pangkal * $diskon_p / 100);
			$rincian[] = array("label" => "Uang Pendaftaran", "nominal" => $daftar);
			$rincian[] = array("label" => "Uang Pangkal", "nominal" => $pangkal);
			$rincian[] = array("label" => "Diskon Pelunasan " . $this->format_persen($diskon_p) . "%", "nominal" => -1 * $diskon);
			$total = $daftar + $pangkal - $diskon;
			$diskon_persen = $diskon_p;
		}
		else {
			$pilihan = 1;
			$rincian[] = array("label" => "Uang Pendaftaran", "nominal" => $daftar);
			$total = $daftar;
		}

		return array(
			"kode" => $pilihan,
			"rincian" => $rincian,
			"diskon_persen" => $diskon_persen,
			"total" => $total,
			"deskripsi" => $this->deskripsi($pilihan, $diskon_p)
		);
	}

	/** judul (header tampilan FE) per pilihan, jenjang, nama siswa */
	function judul_tagihan($pilihan, $jenjang, $nama_lengkap)
	{
		$pilihan = (int) $pilihan;
		if ($pilihan == 2) {
			return "Tagihan Pendaftaran + Cicilan Pertama Uang Pangkal " . strtoupper($jenjang) . " a.n " . $nama_lengkap;
		}
		if ($pilihan == 3) {
			return "Tagihan Pendaftaran + Pelunasan Uang Pangkal " . strtoupper($jenjang) . " a.n " . $nama_lengkap;
		}
		return "Tagihan Pendaftaran " . strtoupper($jenjang) . " a.n " . $nama_lengkap;
	}

	/** judul + deskripsi dari row transaksi (pilihan NULL = opsi 1) */
	function dari_transaksi($transaksi)
	{
		$pilihan = ($transaksi->pilihan_pembayaran === null || $transaksi->pilihan_pembayaran == '') ? 1 : (int) $transaksi->pilihan_pembayaran;
		$trx_type = explode("-", $transaksi->no_transaksi);
		$jenjang = isset($trx_type[1]) ? $trx_type[1] : '';
		return array(
			"pilihan_pembayaran" => $pilihan,
			"description" => $transaksi->description,
			"judul_tagihan" => $this->judul_tagihan($pilihan, $jenjang, $transaksi->user_name)
		);
	}

	/* ===== Identitas pemanggil (dipakai bersama Cek_data & Paket_pembayaran) =====
	 * Pencocokan identitas WAJIB lewat fungsi di bawah: trim + case-insensitive
	 * di kedua sisi. Jangan menyalin kondisi pembanding identitas di tempat lain —
	 * (bug lama: transaksi.user_name vs siswa.nama_lengkap beda kapital → PHP
	 * case-sensitive membandingkan dua nilai berbeda → tak ada input yang lolos).
	 */

	/** normalisasi identitas: trim + lowercase */
	function norm_identitas($nilai)
	{
		return trim(strtolower(trim((string) $nilai)));
	}

	/** true bila dua nilai identitas sama (trim + case-insensitive) */
	function identitas_cocok($a, $b)
	{
		return $this->norm_identitas($a) === $this->norm_identitas($b);
	}

	/**
	 * Cari siswa PSB per jenjang (ft/sma/smp/sd/kb/tk) dengan identitas
	 * email + nama_lengkap (exact, tapi trim + case-insensitive via verifikasi
	 * PHP di atas hasil SQL ci-collation). Return row siswa atau null.
	 */
	function cari_siswa($jenjang, $email, $nama_lengkap)
	{
		$jenjang = strtolower(trim((string) $jenjang));
		$pk = array(
			"ft" => "id_siswa_ft", "sma" => "id_siswa_sma", "smp" => "id_siswa_smp",
			"sd" => "id_siswa_sd", "kb" => "id_siswa_kb", "tk" => "id_siswa_tk"
		);
		if (!isset($pk[$jenjang])) {
			return null;
		}
		$email = trim((string) $email);
		$nama_lengkap = trim((string) $nama_lengkap);
		if ($email == '' || $nama_lengkap == '') {
			return null;
		}
		$siswa = $this->CI->mymodel->withquery("select * from siswa_" . $jenjang . " where email = '" . $this->CI->db->escape_str($email) . "' and nama_lengkap = '" . $this->CI->db->escape_str($nama_lengkap) . "' and is_show = 1 order by " . $pk[$jenjang] . " DESC", "row");
		if (empty($siswa)) {
			return null;
		}
		// verifikasi final PHP (menutup celah trailing space/kapital yang lolos SQL ci)
		if (!$this->identitas_cocok($siswa->email, $email) || !$this->identitas_cocok($siswa->nama_lengkap, $nama_lengkap)) {
			return null;
		}
		return $siswa;
	}

	/** true bila transaksi (user_email + user_name) milik identitas pemanggil */
	function verifikasi_pemilik($transaksi, $email, $nama_lengkap)
	{
		return $this->identitas_cocok($transaksi->user_email, $email) && $this->identitas_cocok($transaksi->user_name, $nama_lengkap);
	}
}
