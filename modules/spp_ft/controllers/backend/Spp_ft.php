<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 *| --------------------------------------------------------------------------
 *| Spp Ft Controller
 *| --------------------------------------------------------------------------
 *| Spp Ft site
 *|
 */
class Spp_ft extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_spp_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Spp Fts
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('spp_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['spp_fts'] = $this->model_spp_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['spp_ft_counts'] = $this->model_spp_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/spp_ft/index/',
			'total_rows'   => $this->model_spp_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Spp Ft List');
		$this->render('backend/standart/administrator/spp_ft/spp_ft_list', $this->data);
	}


	/**
	 * Update view Spp Fts
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('spp_ft_update');

		$this->data['spp_ft'] = $this->model_spp_ft->find($id);

		$this->template->title('Spp Ft Update');
		$this->render('backend/standart/administrator/spp_ft/spp_ft_update', $this->data);
	}

	public function add_save()
	{
		$this->load->library("session");
		if (!$this->is_allowed('spp_ft_list', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$bln = $this->input->post('bulan');
		$tahun_ajaran = $this->input->post('tahun_ajaran');
		$custom_nominal_spp = $this->input->post('custom_nominal_spp');

		$id_siswa = $this->input->post('id_siswa_ft_aktif');
		if (empty($id_siswa)) {
			//get data siswa aktif per kelas tersebut
			$list_siswa = $this->mymodel->getbywhere("siswa_ft_aktif", "id_kelas", $this->input->post("id_kelas"), "result");
			$id_siswa = array();
			foreach ($list_siswa as $key => $value) {
				array_push($id_siswa, $value->id_siswa_ft_aktif);
			}
		} else if (count($id_siswa) > 1) {
			// Hapus elemen kosong pertama dari dropdown placeholder
			array_shift($id_siswa);
			$id_siswa = array_values($id_siswa); // re-index
		}

		// Tracking hasil generate
		$success_count = 0;
		$update_count = 0;
		$skip_count = 0;
		// Error messages
		$warnings_errors = array();
		// Info SPP type/custom per siswa
		$warnings_info = array();

		foreach ($id_siswa as $key_siswa => $value_siswa) {
			$this->db->trans_begin();

			$get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.* from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join kelas_ft k on k.id_kelas_ft = sa.id_kelas where sa.id_siswa_ft_aktif = " . (int)$value_siswa, 'row');
			if (!$get_siswa) {
				$warnings_errors[] = "Siswa ID {$value_siswa} tidak ditemukan.";
				$this->db->trans_rollback();
				continue;
			}

			$bank = 'BNI';
			$jenjang = 'ft';
			$nama_siswa = $get_siswa->nama_lengkap;

			if (!empty($get_siswa->id_tingkatan)) {
				$get_biaya = $this->mymodel->getbywhere("tingkatan_ft", "id_tingkatan_ft", $get_siswa->id_tingkatan, "row");
			} else {
				$get_biaya = null;
			}

			$get_tahun_ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where code = '" . $this->db->escape_str($tahun_ajaran) . "'", "row");

			if (!$get_tahun_ajaran_aktif) {
				$warnings_errors[] = "Tahun ajaran '{$tahun_ajaran}' tidak ditemukan.";
				$this->db->trans_rollback();
				continue;
			}

			$f = strtotime($get_tahun_ajaran_aktif->tanggal_mulai);
			$first = date('Y-m-01', $f);
			$l = strtotime($get_tahun_ajaran_aktif->tanggal_selesai);
			$last = date('Y-m-01', $l);

			// Logic SPP: custom_nominal_input > spp_custom > spp_type > default
			$spp_custom = $get_siswa->spp_custom;
			$spp_type = $get_siswa->spp_type ?: 'FULL';
			$biaya_default = $get_biaya ? $get_biaya->biaya_spp : 0;

			// Jika custom nominal dari input modal diisi, hitung berdasarkan spp_type
			if (!empty($custom_nominal_spp) && $custom_nominal_spp > 0) {
				$nominal_dasar = (int) $custom_nominal_spp;
				
				if ($spp_type == 'FREE') {
					$biaya = 0;
				} elseif ($spp_type == 'HALF') {
					$biaya = $nominal_dasar / 2;
				} else {
					$biaya = $nominal_dasar;
				}
				
				// Simpan ke spp_custom siswa
				$this->mymodel->update('siswa_ft_aktif', ['spp_custom' => $biaya], 'id_siswa_ft_aktif', $value_siswa);
				$spp_custom = $biaya;
				
				$warnings_info[$nama_siswa] = "SPP Custom: Rp " . number_format($biaya, 0, ',', '.') . " (input: " . number_format($nominal_dasar, 0, ',', '.') . ", {$spp_type})";
			} else if (!empty($spp_custom) && $spp_custom > 0) {
				// Gunakan spp_custom dari DB apa adanya
				$biaya = $spp_custom;
				$warnings_info[$nama_siswa] = "SPP Custom DB: Rp " . number_format($spp_custom, 0, ',', '.');
			} else {
				// Ambil dari tingkatan, hitung berdasarkan spp_type
				if ($spp_type == 'FREE') {
					$biaya = 0;
					$warnings_info[$nama_siswa] = "FREE (Rp " . number_format($biaya_default, 0, ',', '.') . ")";
				} elseif ($spp_type == 'HALF') {
					$biaya = $biaya_default / 2;
					$warnings_info[$nama_siswa] = "HALF (Rp " . number_format($biaya, 0, ',', '.') . ")";
				} else {
					$biaya = $biaya_default;
				}
			}

			$cek_detail = $this->mymodel->getbywhere("spp_ft", array("id_siswa_aktif" => $value_siswa, "id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran), "row");
			$arrMonth = $this->echoDate(strtotime($first), strtotime($last));

			// Flag status siswa
			$siswa_has_new = false;
			$siswa_has_update = false;
			$is_update = false;
			$siswa_skipped_months = array();

			if (!$cek_detail) {
				$detail_spp = array(
					"id_siswa_aktif" => $value_siswa,
					"nama" => $nama_siswa,
					"kelas" => $get_siswa->label,
					"tahun_ajaran" => $get_tahun_ajaran_aktif->label,
					"id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran,
					"nominal" => $biaya
				);
				$id_detail_spp = $this->mymodel->insertid("spp_ft", $detail_spp);
			} else {
				$id_detail_spp = $cek_detail[0]->id;
				$update_spp = array();
				if ($cek_detail[0]->nominal != $biaya) $update_spp['nominal'] = $biaya;
				if ($cek_detail[0]->nama != $nama_siswa) $update_spp['nama'] = $nama_siswa;
				if ($cek_detail[0]->kelas != $get_siswa->label) $update_spp['kelas'] = $get_siswa->label;
				if (!empty($update_spp)) {
					$this->mymodel->update("spp_ft", $update_spp, "id", $id_detail_spp);
					$is_update = true;
				}
			}

			// Update kolom bulan di spp_ft
			foreach ($arrMonth as $in) {
				$bulan = explode("_", $in);
				$nama_bulan = get_bulan((int)$bulan[1]);
				$kolom_bulan = strtolower($nama_bulan);
				$current_value = isset($cek_detail[0]->$kolom_bulan) ? $cek_detail[0]->$kolom_bulan : null;
				
				if ($current_value !== null && $current_value !== '' && $current_value !== '-') {
					continue;
				}
				
				if ($bln && in_array((int)$bulan[1], $bln)) {
					$this->mymodel->update("spp_ft", array($kolom_bulan => null), "id", $id_detail_spp);
				} else if (!$bln) {
					$this->mymodel->update("spp_ft", array($kolom_bulan => null), "id", $id_detail_spp);
				} else {
					$this->mymodel->update("spp_ft", array($kolom_bulan => '-'), "id", $id_detail_spp);
				}
			}

			// Insert/update transaksi_spp
			foreach ($arrMonth as $i) {
				$bulan = explode("_", $i);
				$nama_bulan = get_bulan((int) $bulan[1]);

				if ($bln && !in_array((int)$bulan[1], $bln)) {
					continue;
				}

				// Cek transaksi dengan filter jenjang FT
				$cek_transaksi_bln = $this->mymodel->withquery("
					SELECT * FROM transaksi_spp 
					WHERE id_siswa_aktif = " . (int)$value_siswa . " 
					AND id_tahun_ajaran = " . (int)$get_tahun_ajaran_aktif->id_tahun_ajaran . " 
					AND bulan = '" . $this->db->escape_str($nama_bulan) . "'
					AND (no_transaksi LIKE '%ft%' OR no_transaksi LIKE '%FT%')
				", "row");

				if (!$cek_transaksi_bln) {
					$no_transaksi = 'LISPP-' . $jenjang . '-' . $tahun_ajaran . '-' . $value_siswa . '-' . $nama_bulan;
					$data_transaksi = array(
						"no_transaksi" => $no_transaksi,
						"nama_bank" => $bank,
						"user_email" => $get_siswa->email,
						"user_name" => $nama_siswa,
						"user_phone" => "",
						"description" => "Tagihan SPP Bulan " . $nama_bulan . " " . $bulan[0],
						"id_biaya_pendaftaran" => 1,
						"total_biaya" => $biaya,
						"status_transaksi" => "0",
						"created_at" => date("Y-m-d H:i:s"),
						"bulan" => $nama_bulan,
						"id_siswa_aktif" => $value_siswa,
						"id_tahun_ajaran" => $get_tahun_ajaran_aktif->id_tahun_ajaran,
						"id_spp" => $id_detail_spp
					);
					$this->mymodel->insertid("transaksi_spp", $data_transaksi);
					$siswa_has_new = true;
				} else {
					$update_transaksi = array();
					if ($cek_transaksi_bln->user_name != $nama_siswa) $update_transaksi['user_name'] = $nama_siswa;
					if ($cek_transaksi_bln->total_biaya != $biaya) $update_transaksi['total_biaya'] = $biaya;
					if (!empty($update_transaksi)) {
						$this->mymodel->update("transaksi_spp", $update_transaksi, "id_transaksi", $cek_transaksi_bln->id_transaksi);
						$siswa_has_update = true;
					} else {
						$siswa_skipped_months[] = $nama_bulan;
					}
				}
			}

			try {
				if ($this->db->trans_status() === FALSE) {
					$db_error = $this->db->error();
					$this->db->trans_rollback();
					$warnings_errors[] = "Gagal generate SPP untuk {$nama_siswa}. DB Error: " . $db_error['message'];
				} else {
					$this->db->trans_commit();
					if ($siswa_has_new) {
						$success_count++;
					} else if ($siswa_has_update || $is_update) {
						$update_count++;
					} else {
						$skip_count++;
					}
				}
			} catch (Exception $e) {
				$this->db->trans_rollback();
				$warnings_errors[] = "Error: " . $e->getMessage();
			}
		}

		// Bangun pesan hasil sederhana
		$msg = "Generate SPP: {$success_count} baru";
		if ($update_count > 0) $msg .= ", {$update_count} update";
		if ($skip_count > 0) $msg .= ", {$skip_count} skip";
		
		if (!empty($warnings_errors)) {
			$msg .= "\n" . implode("\n", $warnings_errors);
		}

		if ($success_count > 0 || $update_count > 0) {
			$this->session->set_flashdata('success', $msg);
		} else if ($skip_count > 0) {
			$this->session->set_flashdata('warning', $msg);
		} else {
			$this->session->set_flashdata('error', $msg);
		}

		redirect($_SERVER['HTTP_REFERER']);
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

	/**
	 * Update Spp Fts
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('spp_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_aktif', 'Id Siswa Aktif', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kelas', 'Kelas', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('nominal', 'Nominal', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {

			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'nama' => $this->input->post('nama'),
				'kelas' => $this->input->post('kelas'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'nominal' => $this->input->post('nominal'),
				'juli' => $this->input->post('juli'),
				'agustus' => $this->input->post('agustus'),
				'september' => $this->input->post('september'),
				'oktober' => $this->input->post('oktober'),
				'november' => $this->input->post('november'),
				'desember' => $this->input->post('desember'),
				'januari' => $this->input->post('januari'),
				'februari' => $this->input->post('februari'),
				'maret' => $this->input->post('maret'),
				'april' => $this->input->post('april'),
				'mei' => $this->input->post('mei'),
				'juni' => $this->input->post('juni'),
			];


			$save_spp_ft = $this->model_spp_ft->change($id, $save_data);

			if ($save_spp_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/spp_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/spp_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/spp_ft');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	 * delete Spp Fts
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('spp_ft_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
			set_message(cclang('has_been_deleted', 'spp_ft'), 'success');
		} else {
			set_message(cclang('error_delete', 'spp_ft'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Spp Fts
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('spp_ft_view');

		$this->data['spp_ft'] = $this->model_spp_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Spp Ft Detail');
		$this->render('backend/standart/administrator/spp_ft/spp_ft_view', $this->data);
	}

	/**
	 * delete Spp Fts
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$spp_ft = $this->model_spp_ft->find($id);

		// Guard: transaksi_spp yang menunjuk id_spp ini akan orphan (tidak muncul di
		// keuangan_dashboard_spp) jika row master dihapus. Tolak delete saat masih ada
		// transaksi terkait — re-point/pindahkan transaksinya dulu.
		$n_transaksi = $this->db->where('id_spp', $id)
			->like('no_transaksi', 'LISPP-FT')
			->count_all_results('transaksi_spp');
		if ($n_transaksi > 0) {
			set_message('Tidak bisa dihapus: masih ada ' . $n_transaksi . ' transaksi SPP yang terikat ke data ini. Pindahkan/hapus transaksinya terlebih dahulu.', 'error');
			return false;
		}

		return $this->model_spp_ft->remove($id);
	}


	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('spp_ft_export');

		//$this->model_spp_ft->export('spp_ft', 'spp_ft');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id_siswa_aktif', 'nama', 'kelas', 'nis', 'tahun_ajaran', 'nominal', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember', 'januari', 'februari', 'maret', 'april', 'mei', 'juni'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select spp.*, s.nama_lengkap, s.nis from spp_ft spp join siswa_ft_aktif s on spp.id_siswa_aktif = s.id_siswa_ft_aktif join tahun_ajaran t on spp.id_tahun_ajaran = t.id_tahun_ajaran order by spp.kelas ASC, spp.nama ASC, spp.tahun_ajaran DESC","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "nama_lengkap"){
                $where .= "(" . "s.nama_lengkap LIKE '%" . $inputan . "%' )";
            }
            else if($field == "nis"){
                $where .= "(" . "s.nis LIKE '%" . $inputan . "%' )";
            }
			else if($field == "tahun_ajaran"){
				$where .= "(" . "t.label LIKE '%" . $inputan . "%' )";
			}
            else{
                $where .= "(" . "spp.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select spp.*, s.nama_lengkap, s.nis from spp_ft spp join siswa_ft_aktif s on spp.id_siswa_aktif = s.id_siswa_ft_aktif join tahun_ajaran t on spp.id_tahun_ajaran = t.id_tahun_ajaran where ".$where." order by spp.kelas ASC, spp.nama ASC, spp.tahun_ajaran DESC","result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $fields) {
	                if ($iterasi == 1) {
	                    $where .= "spp.".$fields . " LIKE '%" . $inputan . "%' ";
	                }
	                else {
	                    if($fields == "nama_lengkap"){
			                $where .= " OR (" . "s.nama_lengkap LIKE '%" . $inputan . "%' )";
			            }
			            else if($fields == "nis"){
			                $where .= " OR (" . "s.nis LIKE '%" . $inputan . "%' )";
			            }
						else if($fields == "tahun_ajaran"){
							$where .= " OR (" . "t.label LIKE '%" . $inputan . "%' )";
						}
			            else{
			                $where .= " OR (" . "spp.".$fields . " LIKE '%" . $inputan . "%' )";
			            }
	                }
	                $iterasi++;
	        }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select spp.*, s.nama_lengkap, s.nis from spp_ft spp join siswa_ft_aktif s on spp.id_siswa_aktif = s.id_siswa_ft_aktif join tahun_ajaran t on spp.id_tahun_ajaran = t.id_tahun_ajaran where ".$where." order by spp.kelas ASC, spp.nama ASC, spp.tahun_ajaran DESC","result");
		}
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);  
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "id_siswa_aktif") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "id_siswa_aktif") !== false) {
		        	$value_data = ($key+1);
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Rekap SPP ft_'.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('spp_ft_export');

		$this->model_spp_ft->pdf('spp_ft', 'spp_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('spp_ft_export');

		$table = $title = 'spp_ft';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_spp_ft->find($id);
		$fields = $result->list_fields();

		$content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
			'data' => $data,
			'fields' => $fields,
			'title' => $title
		], TRUE);

		$this->pdf->initialize($config);
		$this->pdf->pdf->SetDisplayMode('fullpage');
		$this->pdf->writeHTML($content);
		$this->pdf->Output($table . '.pdf', 'H');
	}

	public function sync_spp_data(){
		$get_spp = $this->mymodel->withquery("select id as id_spp, nama, id_siswa_aktif, id_tahun_ajaran from spp_ft order by id_spp ASC","result");
		foreach ($get_spp as $key => $value) {
			$get_transaksi = $this->mymodel->withquery("select id_spp, id_siswa_aktif, no_transaksi, id_tahun_ajaran, updated_at, bulan, status_transaksi from transaksi_spp where id_spp = '".$value->id_spp."' and id_siswa_aktif = '".$value->id_siswa_aktif."' and id_tahun_ajaran = '".$value->id_tahun_ajaran."' and status_transaksi = '2' and no_transaksi like '%"."ft"."%' ","result");
			//cek status transaksi dan spp ft
			foreach ($get_transaksi as $key_transaksi => $value_transaksi) {
				if (!empty($value_transaksi->updated_at)) {
					$bulan = strtolower($value_transaksi->bulan);
					$data_update = array(
						$bulan => $value_transaksi->updated_at,
					);
					$this->mymodel->update("spp_ft",$data_update,"id",$value->id_spp);
					//echo $bulan." - ".$value_transaksi->id_spp." - ".$value->id_siswa_aktif."<br/>";
				}
			}
		}
		$this->session->set_flashdata('success', 'Sync SPP berhasil');
		redirect('administrator/spp_ft');
	}

	public function export_tunggakan()
	{
		$this->is_allowed('spp_ft_export');

		$bulan_list = ['juli', 'agustus', 'september', 'oktober', 'november', 'desember', 'januari', 'februari', 'maret', 'april', 'mei', 'juni'];

		$filter_bulan = $this->input->get('bulan');
		$id_tahun_ajaran = $this->input->get('id_tahun_ajaran');

		if (!empty($filter_bulan)) {
			$bulan_aktif = array_intersect(explode(',', $filter_bulan), $bulan_list);
			$bulan_aktif = array_values($bulan_aktif);
		} else {
			$bulan_aktif = $bulan_list;
		}

		$where_ta = !empty($id_tahun_ajaran) ? "AND spp.id_tahun_ajaran = " . (int)$id_tahun_ajaran : "";

		$label_ta = 'Semua Tahun Ajaran';
		if (!empty($id_tahun_ajaran)) {
			$res_ta = $this->mymodel->withquery("SELECT label FROM tahun_ajaran WHERE id_tahun_ajaran = " . (int)$id_tahun_ajaran, "row");
			if ($res_ta) $label_ta = $res_ta->label;
		}

		$queries = [
			'SD'  => "SELECT spp.*, s.nama_lengkap, s.nis, 'SD' as nama_unit
					FROM spp_sd spp
					JOIN siswa_sd_aktif s ON spp.id_siswa_aktif = s.id_siswa_sd_aktif
					JOIN tahun_ajaran t ON spp.id_tahun_ajaran = t.id_tahun_ajaran
					WHERE 1=1 $where_ta
					ORDER BY spp.kelas ASC",

			'SMP' => "SELECT spp.*, s.nama_lengkap, s.nis, 'SMP' as nama_unit
					FROM spp_smp spp
					JOIN siswa_smp_aktif s ON spp.id_siswa_aktif = s.id_siswa_smp_aktif
					JOIN tahun_ajaran t ON spp.id_tahun_ajaran = t.id_tahun_ajaran
					WHERE 1=1 $where_ta
					ORDER BY spp.kelas ASC",

			'SMA' => "SELECT spp.*, s.nama_lengkap, s.nis, 'SMA' as nama_unit
					FROM spp_sma spp
					JOIN siswa_sma_aktif s ON spp.id_siswa_aktif = s.id_siswa_sma_aktif
					JOIN tahun_ajaran t ON spp.id_tahun_ajaran = t.id_tahun_ajaran
					WHERE spp.kelas NOT LIKE '%FT%' $where_ta
					ORDER BY spp.kelas ASC",

			'SMA France Track' => "SELECT spp.*, s.nama_lengkap, s.nis, 'SMA France Track' as nama_unit
					FROM spp_sma spp
					JOIN siswa_sma_aktif s ON spp.id_siswa_aktif = s.id_siswa_sma_aktif
					JOIN tahun_ajaran t ON spp.id_tahun_ajaran = t.id_tahun_ajaran
					WHERE spp.kelas LIKE '%FT%' $where_ta
					ORDER BY spp.kelas ASC",

			'FT' => "SELECT spp.*, s.nama_lengkap, s.nis, 'FT' as nama_unit
					FROM spp_ft spp
					JOIN siswa_ft_aktif s ON spp.id_siswa_aktif = s.id_siswa_ft_aktif
					JOIN tahun_ajaran t ON spp.id_tahun_ajaran = t.id_tahun_ajaran
					WHERE 1=1 $where_ta
					ORDER BY spp.kelas ASC",
		];

		$data_grouped = [];
		foreach ($queries as $nama_unit => $sql) {
			$rows = $this->mymodel->withquery($sql, "result");
			foreach ($rows as $row) {
				preg_match('/^(\d+)/', trim($row->kelas), $matches);
				$tingkatan = isset($matches[1]) ? (int)$matches[1] : $row->kelas;

				if (!isset($data_grouped[$nama_unit][$tingkatan])) {
					$data_grouped[$nama_unit][$tingkatan] = [
						'nominal'   => $row->nominal,
						'jml_siswa' => 0,
						'bulan'     => array_fill_keys($bulan_list, 0),
					];
				}
				$data_grouped[$nama_unit][$tingkatan]['jml_siswa']++;
				foreach ($bulan_aktif as $bln) {
					if (empty($row->$bln)) {
						$data_grouped[$nama_unit][$tingkatan]['bulan'][$bln]++;
					}
				}
			}

			if (isset($data_grouped[$nama_unit])) {
				ksort($data_grouped[$nama_unit], SORT_NUMERIC);
			}
		}

		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();

		$row = 1;
		$sheet->setCellValue('A' . $row, 'Rekapitulasi Tunggakan SPP');
		$row++;
		$sheet->setCellValue('A' . $row, 'Per ' . formatTanggal(date('Y-m-d')));
		$row++;
		$sheet->setCellValue('A' . $row, 'Tahun Ajaran : ' . str_replace('/', ' - ', $label_ta));
		$row++;

		$grand_total_siswa   = 0;
		$grand_total_nominal = 0;
		$col_total_nominal   = 'A';

		foreach ($data_grouped as $unit => $kelas_list) {
			$row++;

			$sheet->setCellValue('A' . $row, 'Unit : ' . $unit . ' Labschool Cibubur');
			$row++;

			$sheet->setCellValue('A' . $row, 'No');
			$sheet->setCellValue('B' . $row, 'Kelas');
			$sheet->setCellValue('C' . $row, 'Jml Siswa');
			$sheet->setCellValue('D' . $row, 'Nominal');

			$col_index     = 5;
			$bulan_col_map = [];
			foreach ($bulan_aktif as $bln) {
				$col_letter = PHPExcel_Cell::stringFromColumnIndex($col_index - 1);
				$sheet->setCellValue($col_letter . $row, ucfirst($bln));
				$bulan_col_map[$bln] = $col_index;
				$col_index += 2;
			}

			$col_total_jml     = PHPExcel_Cell::stringFromColumnIndex($col_index - 1);
			$col_total_nominal = PHPExcel_Cell::stringFromColumnIndex($col_index);
			$sheet->setCellValue($col_total_jml     . $row, 'Total Tunggakan');
			$sheet->setCellValue($col_total_nominal . $row, 'Total Nominal');
			$row++;

			$no                         = 1;
			$subtotal_penunggak         = array_fill_keys($bulan_list, 0);
			$subtotal_jml_siswa         = 0;
			$subtotal_tunggakan_siswa   = 0;
			$subtotal_tunggakan_nominal = 0;

			foreach ($kelas_list as $tingkatan => $d) {
				$jml_siswa = $d['jml_siswa'];
				$nominal   = $d['nominal'];
				$subtotal_jml_siswa += $jml_siswa;

				$sheet->setCellValue('A' . $row, $no);
				$sheet->setCellValue('B' . $row, $tingkatan);
				$sheet->setCellValue('C' . $row, $jml_siswa);
				$sheet->setCellValue('D' . $row, $nominal);

				$total_tunggak_siswa   = 0;
				$total_tunggak_nominal = 0;

				foreach ($bulan_aktif as $bln) {
					$ci      = $bulan_col_map[$bln];
					$c1      = PHPExcel_Cell::stringFromColumnIndex($ci - 1);
					$c2      = PHPExcel_Cell::stringFromColumnIndex($ci);
					$jml_tgg = $d['bulan'][$bln];
					$pct     = ($jml_siswa > 0) ? round(($jml_tgg / $jml_siswa) * 100) : 0;

					$sheet->setCellValue($c1 . $row, $jml_tgg);
					$sheet->setCellValue($c2 . $row, $pct . '%');

					$subtotal_penunggak[$bln]  += $jml_tgg;
					$total_tunggak_siswa       += $jml_tgg;
					$total_tunggak_nominal     += ($jml_tgg * $nominal);
				}

				$sheet->setCellValue($col_total_jml     . $row, $total_tunggak_siswa);
				$sheet->setCellValue($col_total_nominal . $row, $total_tunggak_nominal);

				$subtotal_tunggakan_siswa   += $total_tunggak_siswa;
				$subtotal_tunggakan_nominal += $total_tunggak_nominal;
				$grand_total_nominal        += $total_tunggak_nominal;
				$no++;
				$row++;
			}

			$sheet->setCellValue('C' . $row, $subtotal_jml_siswa);
			foreach ($bulan_aktif as $bln) {
				$ci  = $bulan_col_map[$bln];
				$c1  = PHPExcel_Cell::stringFromColumnIndex($ci - 1);
				$c2  = PHPExcel_Cell::stringFromColumnIndex($ci);
				$jt  = $subtotal_penunggak[$bln];
				$pct = ($subtotal_jml_siswa > 0) ? round(($jt / $subtotal_jml_siswa) * 100) : 0;
				$sheet->setCellValue($c1 . $row, $jt);
				$sheet->setCellValue($c2 . $row, $pct . '%');
			}
			$sheet->setCellValue($col_total_jml     . $row, $subtotal_tunggakan_siswa);
			$sheet->setCellValue($col_total_nominal . $row, $subtotal_tunggakan_nominal);

			$grand_total_siswa += $subtotal_jml_siswa;
			$row++;
		}

		$row++;
		$sheet->setCellValue('B'               . $row, 'Total Siswa');
		$sheet->setCellValue('C'               . $row, $grand_total_siswa);
		$sheet->setCellValue($col_total_nominal . $row, $grand_total_nominal);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Rekapitulasi Tunggakan SPP_' . date("Y-m-d Hi") . '.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}


/* End of file spp_ft.php */
/* Location: ./application/controllers/administrator/Spp Ft.php */