<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Update_tagihan_paid_off extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->library('Spp_payment_detail');
  }
  public function index_post()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];

    $kode_tagihan = $this->post('kode_tagihan');

    $get_transaksi_spp = $this->mymodel->withquery("select * from transaksi_spp where kode_tagihan = '" . $kode_tagihan . "'", "row");
    if (empty($get_transaksi_spp)) {
      $this->response(array('status' => 0, 'message' => 'Transaksi SPP tidak ditemukan', 'data' => array()), "200");
      return;
    }
    $total_biaya = $get_transaksi_spp->total_biaya * $get_transaksi_spp->count_bill;

    //CETAK KWITANSI SPP
    $cetak_kwitansi = $this->cetak_kwitansi_spp(array(
      "tipe_siswa" => $this->post('jenjang'),
      "jenjang" => strtoupper($this->post('jenjang')),
      "nama_lengkap" => $get_transaksi_spp->user_name,
      "va_number" => $get_transaksi_spp->va_number,
      "total_biaya" => $total_biaya,
      "total_biaya_terbilang" => terbilang($total_biaya) . " Rupiah",
      "id_siswa" => $get_transaksi_spp->id_siswa_aktif,
      "detail_bulan" => $get_transaksi_spp->detail_bulan,
      "no_transaksi" => $kode_tagihan
    ));

    $data_transaksi = array(
      "status_transaksi" => '2',
      "updated_at" => date("Y-m-d H:i:s"),
      "file_kwitansi" => $kode_tagihan . '-' . str_replace(' ', '_', $get_transaksi_spp->user_name) . '.pdf'
    );

    //UPDATE TRANSAKSI SPP + SPP DETAIL secara atomic.
    //Guard id_siswa_aktif + id_tahun_ajaran karena id_spp tidak global unik antar jenjang.
    $this->db->trans_begin();
    if (!$this->_update_spp_detail($get_transaksi_spp, $data_transaksi["updated_at"])) {
      $this->db->trans_rollback();
      $this->response(array('status' => 0, 'message' => 'Gagal memperbarui detail SPP', 'data' => array()), "200");
      return;
    }
    $where_tagihan = "kode_tagihan = " . $this->db->escape($kode_tagihan) . " and status_transaksi = '1'";
    $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, $where_tagihan);
    if ($transaksi < 1 || $this->db->trans_status() === false) {
      $this->db->trans_rollback();
      $this->response(array('status' => 0, 'message' => 'Gagal memperbarui transaksi SPP', 'data' => array()), "200");
      return;
    }
    $this->db->trans_commit();

    $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => array());
    $status = "200";

    $this->response($msg, $status);
  }

  private function _update_spp_detail($transaction, $updated_at)
  {
    $transaction_parts = explode('-', $transaction->no_transaksi);
    $jenjang = isset($transaction_parts[1]) ? $transaction_parts[1] : '';
    $table = $this->spp_payment_detail->table_for($jenjang);
    $months = $this->spp_payment_detail->months($transaction->detail_bulan);
    if ($table === false || $months === false || empty($transaction->id_siswa_aktif) || empty($transaction->id_tahun_ajaran)) {
      return false;
    }

    $where = array(
      'id' => $transaction->id_spp,
      'id_siswa_aktif' => $transaction->id_siswa_aktif,
      'id_tahun_ajaran' => $transaction->id_tahun_ajaran
    );

    foreach ($months as $month) {
      $this->db->where($where)->update($table, array($month => $updated_at));
      $check = $this->db->select($month)->where($where)->get($table)->row();
      if (empty($check) || (string) $check->$month !== (string) $updated_at) {
        return false;
      }
    }
    return true;
  }

  function cetak_kwitansi_spp($get = '')
  {
    //$usecookie = __DIR__ . "/cookie.txt";
    $header[] = 'Content-Type: application/json';
    $header[] = "Accept-Encoding: gzip, deflate";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    // curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

    if ($get) {
      $endpoint = site_url('/apiapp/siswa/export_pdf_kwitansi_spp');
      //$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) .
        '&tipe_siswa=' . urlencode($get['tipe_siswa']) .
        '&jenjang=' . urlencode($get['jenjang']) .
        '&nama_lengkap=' . urlencode($get['nama_lengkap']) .
        '&total_biaya=' . urlencode($get['total_biaya']) .
        '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) .
        '&va_number=' . urlencode($get['va_number']) .
        '&detail_bulan=' . urlencode($get['detail_bulan']) .
        '&no_transaksi=' . urlencode($get['no_transaksi']);
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $rs = curl_exec($ch);

    if (empty($rs)) {
      // var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return $rs;
  }
}
