<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Detail Ft Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Detail Ft site
*|
*/
class Ujian_ruang_pendaftaran_detail_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_pendaftaran_detail_ft');
		$this->lang->load('web_lang', $this->current_lang);
		$this->limit_page = 20;
	}

	/**
	* show all Ujian Ruang Pendaftaran Detail Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_pendaftaran_detail_fts'] = $this->model_ujian_ruang_pendaftaran_detail_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_pendaftaran_detail_ft_counts'] = $this->model_ujian_ruang_pendaftaran_detail_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang_pendaftaran_detail_ft/index/',
			'total_rows'   => $this->model_ujian_ruang_pendaftaran_detail_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);
		
		$this->session->set_userdata('url_back', $config['base_url']."?f=".$field."&q=".urlencode($filter));

		$this->template->title('Detail Peserta Ujian PSB FT List');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_ft/ujian_ruang_pendaftaran_detail_ft_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_pendaftaran_detail_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_add');

		$this->template->title('Detail Peserta Ujian PSB FT New');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_ft/ujian_ruang_pendaftaran_detail_ft_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Pendaftaran Detail Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_ruang_pendaftaran', 'Ruang', 'trim|required|max_length[11]');
		//$this->form_validation->set_rules('nomor_peserta', 'Nomor Peserta', 'trim|required|max_length[30]');
		

		if ($this->form_validation->run()) {
			$nomor_awal = (int)$this->input->post('nomor_awal');
			$nomor_akhir = (int)$this->input->post('nomor_akhir');
			$total_peserta = (int)$nomor_akhir - (int)$nomor_awal;
			for($i=0; $i <= $total_peserta; $i++) {
				$save_data = [
					'id_ruang_pendaftaran' => $this->input->post('id_ruang_pendaftaran'),
					'nomor_peserta' => ($nomor_awal+$i),//$this->input->post('nomor_peserta')[$i],
				];
				//cek nomor peserta
				$cek_no_peserta = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".($nomor_awal+$i)."'","row");
				if(!empty($cek_no_peserta)){
					$save_ujian_ruang_pendaftaran_detail_ft = $this->model_ujian_ruang_pendaftaran_detail_ft->store($save_data);
				}
				else{
					continue;
				}
			}
            

			if ($save_ujian_ruang_pendaftaran_detail_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_pendaftaran_detail_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_ft/edit/' . $save_ujian_ruang_pendaftaran_detail_ft, 'Edit Ujian Ruang Pendaftaran Detail Ft'),
						anchor('administrator/ujian_ruang_pendaftaran_detail_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_ft/edit/' . $save_ujian_ruang_pendaftaran_detail_ft, 'Edit Ujian Ruang Pendaftaran Detail Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_ft');
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
	* Update view Ujian Ruang Pendaftaran Detail Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_update');

		$this->data['ujian_ruang_pendaftaran_detail_ft'] = $this->model_ujian_ruang_pendaftaran_detail_ft->find($id);

		$this->template->title('Detail Peserta Ujian PSB FT Update');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_ft/ujian_ruang_pendaftaran_detail_ft_update', $this->data);
	}

	/**
	* Update Ujian Ruang Pendaftaran Detail Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ruang_pendaftaran', 'Ruang', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nomor_peserta', 'Nomor Peserta', 'trim|required|max_length[30]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ruang_pendaftaran' => $this->input->post('id_ruang_pendaftaran'),
				'nomor_peserta' => $this->input->post('nomor_peserta'),
			];

			
			$save_ujian_ruang_pendaftaran_detail_ft = $this->model_ujian_ruang_pendaftaran_detail_ft->change($id, $save_data);

			if ($save_ujian_ruang_pendaftaran_detail_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url($this->session->userdata('url_back'));//$_SERVER['HTTP_REFERER'];//base_url('administrator/ujian_ruang_pendaftaran_detail_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_ft');
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
	* delete Ujian Ruang Pendaftaran Detail Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'ujian_ruang_pendaftaran_detail_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_pendaftaran_detail_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Pendaftaran Detail Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_view');

		$this->data['ujian_ruang_pendaftaran_detail_ft'] = $this->model_ujian_ruang_pendaftaran_detail_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Peserta Ujian PSB FT Detail');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_ft/ujian_ruang_pendaftaran_detail_ft_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Pendaftaran Detail Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_pendaftaran_detail_ft = $this->model_ujian_ruang_pendaftaran_detail_ft->find($id);

		
		
		return $this->model_ujian_ruang_pendaftaran_detail_ft->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
	    $this->is_allowed('ujian_ruang_pendaftaran_detail_ft_export');

	    $f = $this->input->get('f', true);
	    $q = (int) $this->input->get('q');

	    $field = ['nomor', 'no_peserta', 'nama_lengkap', 'sekolah_asal', 'nama_ruang', 'password'];

	    // ================= LOAD EXCEL =================
	    $this->load->library('Excel/PHPExcel');
	    $objPHPExcel = new PHPExcel();

	    // =========================================================
	    // 🔵 MODE 1 — FILTER PER RUANG (1 SHEET)
	    // =========================================================
	    if (!empty($f) && !empty($q)) {

	        $query = "SELECT s.no_peserta, s.nama_lengkap, s.sekolah_asal,
	                         s.password_ujian, r.nama_ruang
	                  FROM ujian_ruang_pendaftaran_detail_ft d
	                  INNER JOIN siswa_ft s 
	                          ON s.no_peserta = d.nomor_peserta
	                  INNER JOIN ujian_ruang_pendaftaran_ft r 
	                          ON d.id_ruang_pendaftaran = r.id_ruang_pendaftaran
	                  WHERE r.id_ruang_pendaftaran = {$q}
	                  ORDER BY s.no_peserta ASC, s.nama_lengkap ASC";

	        $data = $this->mymodel->withquery($query, "result");

	        $sheet = $objPHPExcel->setActiveSheetIndex(0);
	        $sheet_name = !empty($data) ? $data[0]->nama_ruang : 'Ruang';
	        $sheet->setTitle(substr($sheet_name, 0, 31));

	        // header
	        $column = 'A';
	        foreach ($field as $fcol) {
	            $title = ($fcol == 'nomor') ? 'NO' : strtoupper(str_replace("_", " ", $fcol));
	            $sheet->setCellValue($column.'1', $title);
	            $column++;
	        }

	        // data
	        $rowCount = 2;
	        foreach ($data as $key => $value) {
	            $rowArr = [
	                'nomor'         => $key + 1,
	                'no_peserta'    => $value->no_peserta,
	                'nama_lengkap'  => $value->nama_lengkap,
	                'sekolah_asal'  => $value->sekolah_asal,
	                'nama_ruang'    => $value->nama_ruang,
	                'password'      => $value->password_ujian,
	            ];

	            $column = 'A';
	            foreach ($field as $fcol) {
	                $sheet->setCellValue($column.$rowCount, $rowArr[$fcol] ?? '');
	                $column++;
	            }
	            $rowCount++;
	        }

	        $nama_file = $sheet_name;
	    }

	    // =========================================================
	    // 🔵 MODE 2 — TANPA FILTER (MULTI SHEET PER RUANG)
	    // =========================================================
	    else {

	        $query = "SELECT s.no_peserta, s.nama_lengkap, s.sekolah_asal,
	                         s.password_ujian, r.nama_ruang,
	                         r.id_ruang_pendaftaran
	                  FROM ujian_ruang_pendaftaran_detail_ft d
	                  INNER JOIN siswa_ft s 
	                          ON s.no_peserta = d.nomor_peserta
	                  INNER JOIN ujian_ruang_pendaftaran_ft r 
	                          ON d.id_ruang_pendaftaran = r.id_ruang_pendaftaran
	                  ORDER BY r.nama_ruang ASC, s.no_peserta ASC";

	        $data = $this->mymodel->withquery($query, "result");

	        // 🔹 group by ruang (yang pasti ada)
	        $grouped = [];
	        foreach ($data as $row) {
	            if (!empty($row->nama_ruang)) {
	                $grouped[$row->nama_ruang][] = $row;
	            }
	        }

	        $sheetIndex = 0;

	        foreach ($grouped as $nama_ruang => $rows) {

	            if ($sheetIndex == 0) {
	                $sheet = $objPHPExcel->setActiveSheetIndex(0);
	            } else {
	                $sheet = $objPHPExcel->createSheet($sheetIndex);
	            }

	            $sheet->setTitle(substr($nama_ruang, 0, 31));

	            // header
	            $column = 'A';
	            foreach ($field as $fcol) {
	                $title = ($fcol == 'nomor') ? 'NO' : strtoupper(str_replace("_", " ", $fcol));
	                $sheet->setCellValue($column.'1', $title);
	                $column++;
	            }

	            // data
	            $rowCount = 2;
	            foreach ($rows as $key => $value) {

	                $rowArr = [
	                    'nomor'         => $key + 1,
	                    'no_peserta'    => $value->no_peserta,
	                    'nama_lengkap'  => $value->nama_lengkap,
	                    'sekolah_asal'  => $value->sekolah_asal,
	                    'nama_ruang'    => $value->nama_ruang,
	                    'password'      => $value->password_ujian,
	                ];

	                $column = 'A';
	                foreach ($field as $fcol) {
	                    $sheet->setCellValue($column.$rowCount, $rowArr[$fcol] ?? '');
	                    $column++;
	                }
	                $rowCount++;
	            }

	            $sheetIndex++;
	        }

	        $nama_file = "SEMUA RUANG";
	    }

	    // ================= OUTPUT =================
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="Daftar Peserta '.$nama_file.' - '.date("Y-m-d Hi").'.xls"');
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
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_export');

		$this->model_ujian_ruang_pendaftaran_detail_ft->pdf('ujian_ruang_pendaftaran_detail_ft', 'ujian_ruang_pendaftaran_detail_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_ft_export');

		$table = $title = 'ujian_ruang_pendaftaran_detail_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_pendaftaran_detail_ft->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function kosongkan_ruang($id = null){
		$id_ruang = $id;
		$this->mymodel->delete("ujian_ruang_pendaftaran_detail_ft", "id_ruang_pendaftaran", $id_ruang);

		set_message(
			cclang('has_been_deleted', [
		]), 'success');
		redirect('administrator/ujian_ruang_pendaftaran_detail_ft');
	}

	public function cetak_absensi(){
		$this->load->library('HtmlPdf');

		$field = (!empty($this->input->get('f'))) ? $this->input->get('f') : "id_ruang_pendaftaran";
		$q = $this->input->get('q');
		$materi_simulasi = $this->input->get('materi_simulasi');

		//get all peserta
		$get_ruang = $this->mymodel->withquery("select nama_ruang, judul_ujian, tahun_ajaran, kepala_sekolah, tahun_ajaran, lokasi_ujian, meeting_id, meeting_url, meeting_password from ujian_ruang_pendaftaran_ft where ".$field." = '".$q."'","row");
		if (!empty($get_ruang)){
			$get_jadwal = $this->mymodel->withquery("select tgl_ujian, waktu_mulai, waktu_selesai, ".strtolower($materi_simulasi)." from waktu_".strtolower($materi_simulasi)."_tes where jenjang = 'ft' and ".strtolower($materi_simulasi)." like '%pelaksanaan%' ","row");
			$get_ruang->hari = formatHari(date("Y-m-d", strtotime($get_jadwal->tgl_ujian)));
			$get_ruang->tanggal = formatTanggal(date("Y-m-d", strtotime($get_jadwal->tgl_ujian)));
			$get_ruang->waktu = date("H:i", strtotime($get_jadwal->waktu_mulai))." - ".date("H:i", strtotime($get_jadwal->waktu_selesai))." WIB";
			$get_ruang->jenjang = "ft";
			$get_ruang->materi_simulasi = $materi_simulasi;
			$peserta = $this->mymodel->withquery("select s.nama_lengkap, s.no_peserta, s.password_ujian, s.foto_peserta, s.sekolah_asal, s.tahun_ajaran, s.gelombang, d.id_ruang_pendaftaran from ujian_ruang_pendaftaran_detail_ft d 
			join siswa_ft s on d.nomor_peserta = s.no_peserta 
			where d.$field = '".$q."' order by s.no_peserta ASC","result");
			if (!empty($peserta)){
				$get_ruang->list_peserta = $peserta;
			}
			else{
				$get_ruang->list_peserta = array();
			}
			foreach ($peserta as $key => $value) {
				$value->foto_peserta = (!empty($value->foto_peserta)) ? base_url('uploads/siswa_ft/').$value->foto_peserta : "" ;
				// echo ($key+1).". ".$value->no_peserta." ".$value->nama_lengkap." ".$value->gelombang." ".$value->tahun_ajaran."<br>";
			}

			$datas['data'] = $get_ruang;
			$pdf = new HTML2PDF('P', 'A4', 'en');
			ob_start();
			
			$this->load->view('template_absensi_ujian_pendaftaran', $datas);
			$html = ob_get_contents(); 
			ob_end_clean();

			$pdf->WriteHTML($html);
			$materi_simulasi = ($materi_simulasi == "simulasi") ? "SIMULASI" : "UJIAN";
			$pdf->Output('ABSENSI '.$materi_simulasi.' '.strtoupper($get_ruang->nama_ruang).'.pdf', 'I');
		}
		else{
			$this->data['success'] = false;
			$this->data['message'] = 'Data tidak ditemukan';
			echo json_encode($this->data);
		}
		//$get_pengaturan = $this->mymodel->withquery("select * from ","row");

	}

	public function cetak_kartu_peserta(){
		$id_ruang_pendaftaran = $this->input->get("id_ruang_pendaftaran");
		$jenjang = "ft";
		// $tahun_ajaran = urldecode($this->input->get("tahun_ajaran"));
		// $gelombang = urldecode($this->input->get("gelombang"));

		$get_peserta = $this->mymodel->withquery("select d.id_ruang_pendaftaran, d.tahun_ajaran, d.gelombang, d.nomor_peserta as no_peserta, s.nama_lengkap, s.password_ujian, s.foto_peserta, s.sekolah_asal, s.alamat, s.jenis_kelamin, r.nama_ruang, r.lokasi_ujian, r.meeting_id, r.meeting_url, r.meeting_password from ujian_ruang_pendaftaran_detail_".$jenjang." d 
		join siswa_".$jenjang." s on d.nomor_peserta = s.no_peserta 
		join ujian_ruang_pendaftaran_ft r on d.id_ruang_pendaftaran = r.id_ruang_pendaftaran
		where d.id_ruang_pendaftaran = '".$id_ruang_pendaftaran."' order by s.no_peserta ASC","result");
		$nama_ruang = "";
		foreach ($get_peserta as $key => $value) {
			if(!empty($value->foto_peserta)){
				// $value->foto_peserta = base_url('uploads/siswa_'.$jenjang.'/').$value->foto_peserta;
			}
			$nama_ruang = $value->nama_ruang;
        }

		$get_waktu_materi = $this->mymodel->withquery("select tgl_ujian, waktu_mulai, waktu_selesai, materi from waktu_materi_tes where jenjang = '".$jenjang."' order by tgl_ujian ASC","result");
        if (!empty($get_waktu_materi)) {
			$hari_ujian = array();
			foreach ($get_waktu_materi as $key => $value) {
				//nentukan tgl
				array_push($hari_ujian, $value->tgl_ujian);
			}
			$hari_ujian = array_unique($hari_ujian);
			$convert_hari = "";
			$convert_tgl = "";
			foreach ($hari_ujian as $key => $value) {
				if ($convert_hari == "") {
				$convert_hari = formatHari($value);
				}
				else{
				$convert_hari = $convert_hari.", ".formatHari($value);
				}
				if ($convert_tgl == "") {
				$convert_tgl = date("d", strtotime($value));
				}
				else{
				$convert_tgl = $convert_tgl.", ".date("d", strtotime($value));
				}
				if (!empty($convert_tgl)) {
				$convert_tgl = $convert_tgl." ".formatBulan($value)." ".date("Y", strtotime($value));
				}
			}
        }
        $get_waktu_simulasi = $this->mymodel->withquery("select tgl_ujian, waktu_mulai, waktu_selesai, simulasi from waktu_simulasi_tes where jenjang = '".$jenjang."' order by tgl_ujian ASC","result");
        if (!empty($get_waktu_simulasi)) {
			$hari_ujian = array();
			foreach ($get_waktu_simulasi as $key => $value) {
				//nentukan tgl
				array_push($hari_ujian, $value->tgl_ujian);
			}
			$hari_ujian = array_unique($hari_ujian);
			$convert_hari_simulasi = "";
			$convert_tgl_simulasi = "";
			foreach ($hari_ujian as $key => $value) {
				if ($convert_hari_simulasi == "") {
				$convert_hari_simulasi = formatHari($value);
				}
				else{
				$convert_hari_simulasi = $convert_hari_simulasi.", ".formatHari($value);
				}
				if ($convert_tgl_simulasi == "") {
				$convert_tgl_simulasi = date("d", strtotime($value));
				}
				else{
				$convert_tgl_simulasi = $convert_tgl_simulasi.", ".date("d", strtotime($value));
				}
				if (!empty($convert_tgl_simulasi)) {
				$convert_tgl_simulasi = $convert_tgl_simulasi." ".formatBulan($value)." ".date("Y", strtotime($value));
				}
			}
        }
		$datas['list_peserta'] = $get_peserta;
        $datas['waktu_materi'] = $get_waktu_materi;
        $datas['waktu_simulasi'] = $get_waktu_simulasi;
        $datas['hari_ujian'] = $convert_hari;
        $datas['hari_ujian_simulasi'] = $convert_hari_simulasi;
        $datas['tgl_ujian'] = $convert_tgl;
        $datas['tgl_ujian_simulasi'] = $convert_tgl_simulasi;
		$datas['kontak'] = $this->mymodel->withquery("select k.*, t.tipe from kontak_labschool k join tipe_kontak t on k.tipe_kontak = t.id_tipe_kontak where jenjang = '".$jenjang."'","result");
        $datas['catatan_kp'] = $this->mymodel->withquery("select * from catatan_kartu_peserta where jenjang = '".$jenjang."'","row");
        if($jenjang=="ft"){
          $datas['title'] = "PELAKSANAAN TES";
          $datas['title_simulasi'] = "PELAKSANAAN SIMULASI";
        }else{
          $datas['title'] = "JADWAL READINESS OF LEARNING (ROLE) OBSERVATION";
          $datas['title_simulasi'] = "PELAKSANAAN SIMULASI";
        }
        $datas['tipe_siswa'] = $jenjang;

        $this->load->library('HtmlPdf');
		$pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
            
       	$this->load->view('template_kartu_peserta_ft_admin', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = 'KARTU_UJIAN '.$nama_ruang."-".$jenjang.'.pdf';
        //$pdf->Output(FCPATH.'/uploads/kartu_ujian/'.$nama_file, 'I');
        $pdf->Output($nama_file, 'I');
	}
	
}


/* End of file ujian_ruang_pendaftaran_detail_ft.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Pendaftaran Detail Ft.php */