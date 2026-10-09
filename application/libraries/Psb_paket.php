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

	/**
	 * Tahap B (v2): upsert status_daftar_ulang_<jenjang> karena transaksi paket
	 * pendaftaran (JI, pilihan_pembayaran 2/3) LUNAS. Dipakai bersama dua jalur:
	 * (a) callback VA BNI (Payment_notification, cabang JI), (b) admin mengubah
	 * status_transaksi jadi lunas di modul Transaksi. Jangan membuat salinan logika
	 * ini di tempat lain.
	 *
	 * Aturan:
	 * - bukan JI / jenjang bukan KB-TK-SD / pilihan bukan 2-3 / belum lunas → tidak melakukan apa pun
	 * - baris daftar ulang belum ada → insert (status 2 = lunas, tgl_bayar, kwitansi, kartu_sementara);
	 *   tanggal_lulus/tgl_daftar_ulang/tgl_aktivasi TIDAK di-set (urusan kelulusan observasi admin)
	 * - baris sudah ada → update hanya kolom yang masih kosong (idempotent: tgl_bayar/kwitansi/
	 *   kartu yang sudah terisi tidak ditimpa; data admin tidak disentuh)
	 * - tanpa rollback otomatis: pembalikan lunas→belum dibiarkan ke admin (log saja)
	 * - kegagalan TIDAK menggagalkan pemanggil: return array(status,message), error di log
	 */
	function upsert_daftar_ulang_paket($transaksi)
	{
		$ci =& $this->CI;
		$trx = explode("-", (string) $transaksi->no_transaksi);
		if (!isset($trx[0]) || $trx[0] != 'JI' || !isset($trx[1])) {
			return array("status" => 0, "message" => "bukan tagihan pendaftaran JI");
		}
		$jenjang = strtolower($trx[1]);
		if (!in_array($jenjang, array("kb", "tk", "sd"))) {
			return array("status" => 0, "message" => "jenjang bukan KB/TK/SD");
		}
		$pilihan = ($transaksi->pilihan_pembayaran === null || $transaksi->pilihan_pembayaran == '') ? 1 : (int) $transaksi->pilihan_pembayaran;
		if (!in_array($pilihan, array(2, 3))) {
			return array("status" => 0, "message" => "pilihan_pembayaran bukan paket (" . $pilihan . ")");
		}
		if ((int) $transaksi->status_transaksi != 1) {
			return array("status" => 0, "message" => "transaksi belum lunas");
		}
		$id_field = "id_siswa_" . $jenjang;
		$tabel_du = "status_daftar_ulang_" . $jenjang;
		$siswa = $ci->mymodel->withquery("select * from siswa_" . $jenjang . " where no_transaksi = '" . $ci->db->escape_str($transaksi->no_transaksi) . "' and is_show = 1 order by " . $id_field . " DESC", "row");
		if (empty($siswa)) {
			return array("status" => 0, "message" => "siswa tidak ditemukan untuk " . $transaksi->no_transaksi);
		}
		// nama file sama dengan pola cabang LDUI/JDUI Payment_notification (string saja, PDF di-stream endpoint export)
		$kwitansi = $transaksi->no_transaksi . '-' . $siswa->nama_lengkap . '.pdf';
		$kartu = $jenjang . '-' . $siswa->no_peserta . '-' . $siswa->nama_lengkap . '.pdf';
		$du = $ci->mymodel->getbywhere($tabel_du, $id_field, $siswa->$id_field, "row");
		if (empty($du)) {
			$data = array(
				$id_field => $siswa->$id_field,
				"status" => 2,
				"tgl_bayar" => date("Y-m-d H:i:s"),
				"kwitansi" => $kwitansi,
				"kartu_sementara" => $kartu
			);
			$ok = $ci->mymodel->insertid($tabel_du, $data);
			if (!$ok) {
				log_message("error", "upsert_daftar_ulang_paket insert gagal " . $transaksi->no_transaksi . ": " . json_encode($data));
				return array("status" => 0, "message" => "insert " . $tabel_du . " gagal");
			}
			return array("status" => 1, "aksi" => "insert", "message" => "baris " . $tabel_du . " dibuat");
		}
		$data = array("status" => 2);
		if ($du->tgl_bayar == '' || $du->tgl_bayar === null) { $data["tgl_bayar"] = date("Y-m-d H:i:s"); }
		if ($du->kwitansi == '' || $du->kwitansi === null) { $data["kwitansi"] = $kwitansi; }
		if ($du->kartu_sementara == '' || $du->kartu_sementara === null) { $data["kartu_sementara"] = $kartu; }
		if (count($data) > 1) {
			$ok = $ci->mymodel->update($tabel_du, $data, "id_daftar_ulang", $du->id_daftar_ulang);
			if (!$ok) {
				log_message("error", "upsert_daftar_ulang_paket update gagal " . $transaksi->no_transaksi . ": " . json_encode($data));
				return array("status" => 0, "message" => "update " . $tabel_du . " gagal");
			}
			return array("status" => 1, "aksi" => "update", "message" => "baris " . $tabel_du . " diperbarui (kolom kosong saja)");
		}
		return array("status" => 1, "aksi" => "skip", "message" => "baris sudah lengkap, tidak ada yang diubah (idempotent)");
	}
}
