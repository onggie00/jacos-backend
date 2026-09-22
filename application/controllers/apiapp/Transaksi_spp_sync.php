<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Transaksi_spp_sync extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $id_siswa = null;
        $bln = ["01","02","03","04","05","06","07","08","09","10","11","12"];
		$jenjang = $this->input->get("jenjang");
		if (empty($id_siswa)) {
			//get data siswa aktif per kelas tersebut
			$list_siswa = $this->mymodel->getbywhere("siswa_sd_aktif","id_tahun_ajaran", 9,"result");
			$id_siswa = array();
			foreach ($list_siswa as $key => $value) {
				array_push($id_siswa, $value->id_siswa_sd_aktif);
			}
		}

        foreach ($id_siswa as $key_siswa => $value_siswa) {
			$get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.* from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join kelas_sd k on k.id_kelas_sd = sa.id_kelas where sa.id_siswa_sd_aktif =" . $value_siswa, 'row');
			$bank = 'BRI';
			if ($get_siswa) {
				$get_biaya = $this->mymodel->getbywhere("tingkatan_sd", "id_tingkatan_sd", $get_siswa->id_tingkatan, "row");
			}
			$jenjang = 'SD';

			// $cek_transaksi = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi like '%LISPP-" . $jenjang . "-" . $tahun_ajaran . "-" . $id_siswa . "%'", "row");
			// if (!$cek_transaksi) {
			$get_tahun_ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where id_tahun_ajaran = '9'", "row");

			$f = strtotime($get_tahun_ajaran_aktif->tanggal_mulai);
			$first = date('Y-m-01', $f);
			$l = strtotime($get_tahun_ajaran_aktif->tanggal_selesai);
			$last = date('Y-m-01', $l);

			if ($get_siswa->spp_custom != 0 && $get_siswa->spp_custom != null) {
				$biaya = $get_siswa->spp_custom;
			} else {
				$biaya = $get_biaya->biaya_spp;
			}

			$cek_detail = $this->mymodel->getbywhere("spp_sd", array("id_siswa_aktif" => $value_siswa, "id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran), "row");
			// dd($cek_detail);
			//insert tabel spp_unit
			$arrMonth = $this->echoDate(strtotime($first), strtotime($last));
			if (!$cek_detail) {
				/* $detail_spp = array(
					"id_siswa_aktif" => $value_siswa,
					"nama" => $get_siswa->nama_lengkap,
					"kelas" => $get_siswa->label,
					"tahun_ajaran" => $get_tahun_ajaran_aktif->label,
					"id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran,
					"nominal" => $biaya
				);
				$id_detail_spp = $this->mymodel->insertid("spp_sd", $detail_spp);
				foreach ($arrMonth as $in) {
					$bulan = explode("_", $in);
					$nama_bulan = get_bulan((int) $bulan[1]);
					if ($bln) {
						$this->mymodel->update2("spp_sd", array(get_bulan((int) $bulan[1]) => '-'), "id", $id_detail_spp);
					}
				} */
			} else {
				$id_detail_spp = $cek_detail[0]->id;
			}

			// dd($arrMonth);
			//insert tabel transaksi_spp
			foreach ($arrMonth as $i) {
				$bulan = explode("_", $i);
				$nama_bulan = get_bulan((int) $bulan[1]);
				$nama_bulan_lower = strtolower($nama_bulan);
				if ($bln) {
					// $update = $this->mymodel->update2("spp_sd", array(get_bulan((int) $bulan[1]) => '-'), "id", $id_detail_spp);
					foreach ($bln as $key => $item) {
						if ($item == $bulan[1]) {
							$no_transaksi = 'LISPP-' . $jenjang . '-' . $get_tahun_ajaran_aktif->code . '-' . $value_siswa . '-' . $nama_bulan;
							$data_transaksi = array(
								"no_transaksi" => $no_transaksi,
								"nama_bank" => $bank,
								"user_email" => $get_siswa->email,
								"user_name" => $get_siswa->nama_lengkap,
								"user_phone" => "",
								"description" => "Tagihan SPP Bulan " . $nama_bulan . " " . $bulan[0],
								"id_biaya_pendaftaran" => 1,
								"total_biaya" => $biaya,
								"created_at" => date("Y-m-d H:i:s"),
								"bulan" => $nama_bulan,
								"id_siswa_aktif" => $value_siswa,
								"id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran,
								"id_spp" => $id_detail_spp
							);
							// dd($data_transaksi);
							$cek_transaksi_bln = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi like '%LISPP-" . $jenjang . "-" . $get_tahun_ajaran_aktif->code . "-" . $value_siswa . "-" . $nama_bulan . "%' and id_spp = " . $id_detail_spp, "row", "row");
							if (!$cek_transaksi_bln) {
                                $data_transaksi['status_transaksi'] = ($cek_detail->$nama_bulan_lower != NULL) ? 2 : 0;
								$save_transaksi_spp = $this->mymodel->insertid("transaksi_spp", $data_transaksi);
                                $data_transaksi['id_transaksi_spp'] = $save_transaksi_spp;
								//$update = $this->mymodel->update2("spp_sd", array(get_bulan2((int) $item) => null), "id", $id_detail_spp);
								// dd($this->db->last_query());
								// cek($this->db->last_query(), true);
							} else {
								$data_transaksi['status_transaksi'] = ($cek_detail->$nama_bulan_lower != NULL) ? 2 : 0;
                                //$save_transaksi_spp = $this->mymodel->update("transaksi_spp", $data_transaksi, 'id_transaksi', $cek_transaksi_bln->id);
								/* $this->session->set_flashdata('warning', "Tagihan siswa sudah ada" . $cek_transaksi_bln->kode_tagihan);
								redirect($_SERVER['HTTP_REFERER']); */
							}
						}
					}
				} else {
					$no_transaksi = 'LISPP-' . $jenjang . '-' . $tahun_ajaran . '-' . $value_siswa . '-' . $nama_bulan;
					$data_transaksi = array(
						"no_transaksi" => $no_transaksi,
						"nama_bank" => $bank,
						"user_email" => $get_siswa->email,
						"user_name" => $get_siswa->nama_lengkap,
						"user_phone" => "",
						"description" => "Tagihan SPP Bulan " . $nama_bulan . " " . $bulan[0],
						"id_biaya_pendaftaran" => 1,
						"total_biaya" => $biaya,
						"created_at" => date("Y-m-d H:i:s"),
						"bulan" => $nama_bulan,
						"id_siswa_aktif" => $value_siswa,
						"id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran,
						"id_spp" => $id_detail_spp
					);
					// dd($data_transaksi);
					$cek_transaksi_bln = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi like '%LISPP-" . $jenjang . "-" . $tahun_ajaran . "-" . $value_siswa . "-" . $nama_bulan . "%' and id_spp = " . $id_detail_spp, "row", "row");
					if (!$cek_transaksi_bln) {
                        $data_transaksi['status_transaksi'] = ($cek_detail->$nama_bulan_lower != NULL) ? 2 : 0;
						$save_transaksi_spp = $this->mymodel->insertid("transaksi_spp", $data_transaksi);
                        //$data_transaksi['id_transaksi_spp'] = $save_transaksi_spp;
					} else {
                        //$save_transaksi_spp = $this->mymodel->update("transaksi_spp", $data_transaksi, 'id_transaksi', $cek_transaksi_bln->id);
						/* $this->session->set_flashdata('warning', "Tagihan siswa sudah ada" . $cek_transaksi_bln->kode_tagihan);
						redirect($_SERVER['HTTP_REFERER']); */
					}
				}
                print_r($data_transaksi);
				//var_dump($cek_detail[0]->$nama_bulan_lower);
                echo "<br/>";
			}
            echo "<br/><br/>";
		}

        if (!empty($data)) {
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }

    function echoDate($start, $end)
	{

		$current = $start;

		$ret = array();

		while ($current <= $end) {


			$format = date('Y_m', $current);
			$ret[] = $format;
			$current = @date('Y-M-01', $current) . "+1 month";
			$current = @strtotime($current);
		}

		return $ret;
	}
}
