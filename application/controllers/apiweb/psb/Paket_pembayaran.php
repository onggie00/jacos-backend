<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paket_pembayaran — API pilihan paket pembayaran PSB (KB/TK/SD).
 * Kontrak FE: Berkas_sample/agent-prompt/20261010-psb-paket-pembayaran-be.md §5.
 *
 * A. opsi_post   — daftar opsi pembayaran utk transaksi pendaftaran milik pemanggil
 *    B. pilih_post  — pilih paket (update transaksi yang sama, bukan transaksi baru)
 *
 * Identitas pemanggil: email + nama_lengkap (ala Cek_data). Pemilik transaksi =
 * transaksi.user_email/user_name sama dengan identitas tsb.
 * Kunci: VA sudah terbit (va_number != ''/'PENDING') atau tagihan lunas (status_transaksi=1)
 * → pilihan tidak boleh diganti.
 */
class Paket_pembayaran extends REST_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('Psb_paket');
	}

	/** cari siswa (kb/tk/sd) + transaksi pendaftaran; return array(status, message, kode, siswa, transaksi, jenjang) */
	function _cari($email, $nama_lengkap, $no_transaksi)
	{
		$email = trim((string) $email);
		$nama_lengkap = trim((string) $nama_lengkap);
		$no_transaksi = trim((string) $no_transaksi);
		if ($email == '' || $nama_lengkap == '' || $no_transaksi == '') {
			return array("status" => 0, "kode" => "parameter_kurang", "message" => "email, nama_lengkap, dan no_transaksi wajib diisi");
		}
		// transaksi harus ada, tipe Pendaftaran (prefix JI-)
		$transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '" . $this->db->escape_str($no_transaksi) . "' and is_show = 1 order by id_transaksi DESC", "row");
		if (empty($transaksi)) {
			return array("status" => 0, "kode" => "tidak_berhak", "message" => "Transaksi tidak ditemukan");
		}
		$trx_type = explode("-", $transaksi->no_transaksi);
		if (!isset($trx_type[0]) || $trx_type[0] != 'JI' || !isset($trx_type[1])) {
			return array("status" => 0, "kode" => "bukan_transaksi_pendaftaran", "message" => "Transaksi bukan tagihan pendaftaran");
		}
		$jenjang = strtolower($trx_type[1]);
		if (!in_array($jenjang, array("kb", "tk", "sd"))) {
			return array("status" => 0, "kode" => "bukan_transaksi_pendaftaran", "message" => "Transaksi bukan tagihan pendaftaran KB/TK/SD");
		}
		// siswa pemilik — pencocokan identitas via helper bersama Psb_paket (trim + case-insensitive)
		$siswa = $this->psb_paket->cari_siswa($jenjang, $email, $nama_lengkap);
		if (empty($siswa)) {
			return array("status" => 0, "kode" => "tidak_berhak", "message" => "Data siswa tidak ditemukan");
		}
		if (!$this->psb_paket->verifikasi_pemilik($transaksi, $email, $nama_lengkap)) {
			return array("status" => 0, "kode" => "tidak_berhak", "message" => "Transaksi bukan milik pemanggil");
		}
		return array("status" => 1, "siswa" => $siswa, "transaksi" => $transaksi, "jenjang" => $jenjang);
	}

	/** status kunci pilihan: null = bebas, string = alasan terkunci */
	function _alasan_terkunci($transaksi)
	{
		if ((int) $transaksi->status_transaksi == 1) {
			return "sudah_lunas";
		}
		$va = (string) $transaksi->va_number;
		if ($va != '' && $va != 'PENDING') {
			return "sudah_terkunci";
		}
		return null;
	}

	/** A. daftar opsi pembayaran */
	public function opsi_post()
	{
		$hasil = $this->_cari($this->post("email"), $this->post("nama_lengkap"), $this->post("no_transaksi"));
		if ($hasil["status"] != 1) {
			$this->response(array("status" => 0, "kode" => $hasil["kode"], "message" => $hasil["message"], "data" => array()), "200");
		}
		$siswa = $hasil["siswa"];
		$transaksi = $hasil["transaksi"];
		$jenjang = $hasil["jenjang"];

		$biaya = $this->psb_paket->get_biaya($jenjang);
		if (empty($biaya)) {
			$this->response(array("status" => 0, "kode" => "bukan_transaksi_pendaftaran", "message" => "Konfigurasi biaya pendaftaran tidak ditemukan", "data" => array()), "200");
		}
		$is_internal = $this->psb_paket->is_internal($siswa->sekolah_asal);
		$pilihan_aktif = ($transaksi->pilihan_pembayaran === null || $transaksi->pilihan_pembayaran == '') ? 1 : (int) $transaksi->pilihan_pembayaran;

		$judul_opsi = array(
			1 => "Uang Pendaftaran",
			2 => "Pendaftaran + Cicilan Pertama Uang Pangkal",
			3 => "Pendaftaran + Pelunasan Uang Pangkal"
		);
		$opsi = array();
		foreach (array(1, 2, 3) as $kode) {
			$hitung = $this->psb_paket->hitung_opsi($biaya, $is_internal, $kode);
			$opsi[] = array(
				"kode" => $kode,
				"judul" => $judul_opsi[$kode],
				"deskripsi" => $hitung["deskripsi"],
				"rincian" => $hitung["rincian"],
				"diskon_persen" => $hitung["diskon_persen"],
				"total" => $hitung["total"]
			);
		}

		$alasan = $this->_alasan_terkunci($transaksi);
		$this->response(array(
			"status" => 1,
			"message" => "Berhasil",
			"no_transaksi" => $transaksi->no_transaksi,
			"pilihan_aktif" => $pilihan_aktif,
			"terkunci" => ($alasan !== null),
			"alasan_terkunci" => $alasan,
			"is_internal" => $is_internal,
			"opsi" => $opsi
		), "200");
	}

	/** B. pilih paket — update transaksi yang sama */
	public function pilih_post()
	{
		$pilihan = (int) $this->post("pilihan_pembayaran");
		if (!in_array($pilihan, array(1, 2, 3))) {
			$this->response(array("status" => 0, "kode" => "pilihan_invalid", "message" => "pilihan_pembayaran hanya boleh 1, 2, atau 3", "data" => array()), "200");
		}

		$hasil = $this->_cari($this->post("email"), $this->post("nama_lengkap"), $this->post("no_transaksi"));
		if ($hasil["status"] != 1) {
			$this->response(array("status" => 0, "kode" => $hasil["kode"], "message" => $hasil["message"], "data" => array()), "200");
		}
		$siswa = $hasil["siswa"];
		$transaksi = $hasil["transaksi"];
		$jenjang = $hasil["jenjang"];

		// tolak bila lunas / VA sudah terbit
		$alasan = $this->_alasan_terkunci($transaksi);
		if ($alasan === "sudah_lunas") {
			$this->response(array("status" => 0, "kode" => "sudah_lunas", "message" => "Tagihan sudah lunas, pilihan tidak bisa diubah", "data" => array()), "200");
		}
		if ($alasan === "sudah_terkunci") {
			$this->response(array("status" => 0, "kode" => "sudah_terkunci", "message" => "Virtual Account sudah dibuat, pilihan tidak bisa diubah", "data" => array()), "200");
		}

		$biaya = $this->psb_paket->get_biaya($jenjang);
		if (empty($biaya)) {
			$this->response(array("status" => 0, "kode" => "bukan_transaksi_pendaftaran", "message" => "Konfigurasi biaya pendaftaran tidak ditemukan", "data" => array()), "200");
		}
		$is_internal = $this->psb_paket->is_internal($siswa->sekolah_asal);
		$hitung = $this->psb_paket->hitung_opsi($biaya, $is_internal, $pilihan);

		// opsi 2 = open (bisa dibayar bertahap, flag fixed manual oleh admin); opsi 1/3 = fixed
		$tipe_tagihan = ($pilihan == 2) ? 'open' : 'fixed';
		$update = array(
			"pilihan_pembayaran" => $pilihan,
			"total_biaya" => $hitung["total"],
			"description" => $hitung["deskripsi"],
			"tipe_tagihan" => $tipe_tagihan,
			"updated_at" => date("Y-m-d H:i:s")
		);
		$this->mymodel->update("transaksi", $update, "id_transaksi", $transaksi->id_transaksi);

		$this->response(array(
			"status" => 1,
			"message" => "Berhasil memilih paket pembayaran",
			"no_transaksi" => $transaksi->no_transaksi,
			"pilihan_pembayaran" => $pilihan,
			"total_biaya" => $hitung["total"],
			"description" => $hitung["deskripsi"],
			"judul_tagihan" => $this->psb_paket->judul_tagihan($pilihan, $jenjang, $siswa->nama_lengkap),
			"tipe_tagihan" => $tipe_tagihan
		), "200");
	}
}
