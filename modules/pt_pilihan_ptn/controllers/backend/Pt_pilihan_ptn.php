<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pt Pilihan Ptn Controller
*| --------------------------------------------------------------------------
*| Pt Pilihan Ptn site
*|
*/
class Pt_pilihan_ptn extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pt_pilihan_ptn');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pt Pilihan Ptns
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pt_pilihan_ptn_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pt_pilihan_ptns'] = $this->model_pt_pilihan_ptn->get($filter, $field, $this->limit_page, $offset);
		$this->data['pt_pilihan_ptn_counts'] = $this->model_pt_pilihan_ptn->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pt_pilihan_ptn/index/',
			'total_rows'   => $this->model_pt_pilihan_ptn->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pilihan PTN List');
		$this->render('backend/standart/administrator/pt_pilihan_ptn/pt_pilihan_ptn_list', $this->data);
	}
	
	/**
	* Add new pt_pilihan_ptns
	*
	*/
	public function add()
	{
		$this->is_allowed('pt_pilihan_ptn_add');

		$this->template->title('Pilihan PTN New');
		$this->render('backend/standart/administrator/pt_pilihan_ptn/pt_pilihan_ptn_add', $this->data);
	}

	/**
	* Add New Pt Pilihan Ptns
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pt_pilihan_ptn_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_pt', 'Perguruan Tinggi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_jurusan', 'Jurusan', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenjang' => $this->input->post('jenjang'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_pt' => $this->input->post('id_pt'),
				'id_jurusan' => $this->input->post('id_jurusan'),
			];

			
			$save_pt_pilihan_ptn = $this->model_pt_pilihan_ptn->store($save_data);
            

			if ($save_pt_pilihan_ptn) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pt_pilihan_ptn;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pt_pilihan_ptn/edit/' . $save_pt_pilihan_ptn, 'Edit Pt Pilihan Ptn'),
						anchor('administrator/pt_pilihan_ptn', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pt_pilihan_ptn/edit/' . $save_pt_pilihan_ptn, 'Edit Pt Pilihan Ptn')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_pilihan_ptn');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_pilihan_ptn');
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
	* Update view Pt Pilihan Ptns
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pt_pilihan_ptn_update');

		$this->data['pt_pilihan_ptn'] = $this->model_pt_pilihan_ptn->find($id);

		$this->template->title('Pilihan PTN Update');
		$this->render('backend/standart/administrator/pt_pilihan_ptn/pt_pilihan_ptn_update', $this->data);
	}

	/**
	* Update Pt Pilihan Ptns
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pt_pilihan_ptn_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_pt', 'Perguruan Tinggi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_jurusan', 'Jurusan', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenjang' => $this->input->post('jenjang'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_pt' => $this->input->post('id_pt'),
				'id_jurusan' => $this->input->post('id_jurusan'),
			];

			
			$save_pt_pilihan_ptn = $this->model_pt_pilihan_ptn->change($id, $save_data);

			if ($save_pt_pilihan_ptn) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pt_pilihan_ptn', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_pilihan_ptn');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_pilihan_ptn');
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
	* delete Pt Pilihan Ptns
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pt_pilihan_ptn_delete');

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
            set_message(cclang('has_been_deleted', 'pt_pilihan_ptn'), 'success');
        } else {
            set_message(cclang('error_delete', 'pt_pilihan_ptn'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pt Pilihan Ptns
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pt_pilihan_ptn_view');

		$this->data['pt_pilihan_ptn'] = $this->model_pt_pilihan_ptn->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pilihan PTN Detail');
		$this->render('backend/standart/administrator/pt_pilihan_ptn/pt_pilihan_ptn_view', $this->data);
	}
	
	/**
	* delete Pt Pilihan Ptns
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pt_pilihan_ptn = $this->model_pt_pilihan_ptn->find($id);

		
		
		return $this->model_pt_pilihan_ptn->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pt_pilihan_ptn_export');

		$field       = $this->input->get("f");      // 'tahun_ajaran'
		$inputan     = $this->input->get("q");      // id_tahun_ajaran
		$id_kelas_filter = $this->input->get("id_kelas"); // optional, filter 1 kelas saja

		$field_search = [
			'id','id_siswa_aktif','nis','id_kelas',
			'nilai_utbk1','nilai_utbk2','nilai_utbk3','nilai_utbk4',
			'id_pt','id_jurusan','tgl_pilih_1','tgl_ubah_1',
			'persentase_1_utbk1','persentase_1_utbk2','persentase_1_utbk3','persentase_1_utbk4',
			'id_pt2','id_jurusan2','tgl_pilih_2','tgl_ubah_2',
			'persentase_2_utbk1','persentase_2_utbk2','persentase_2_utbk3','persentase_2_utbk4'
		];

		// ── Ambil filter tahun ajaran ──────────────────────────────────────────
		$tgl_mulai  = null;
		$tgl_selesai = null;
		if ($field == "tahun_ajaran" && $inputan) {
			$get_ta = $this->mymodel->withquery(
				"SELECT * FROM tahun_ajaran WHERE id_tahun_ajaran = '{$inputan}'", "row"
			);
			if ($get_ta) {
				$tgl_mulai   = $get_ta->tanggal_mulai;
				$tgl_selesai = $get_ta->tanggal_selesai; // sesuaikan nama kolom
			}
		}

		// ── Kumpulkan daftar kelas yang akan di-export ─────────────────────────
		// Format: [['id_kelas' => x, 'label' => y, 'jenjang' => 'ft'|'sma'], ...]
		$list_kelas = [];

		if (!empty($id_kelas_filter)) {
			// Hanya 1 kelas spesifik
			$k = $this->mymodel->withquery(
				"SELECT id_kelas_ft AS id_kelas, label, 'ft' AS jenjang FROM kelas_ft WHERE id_kelas_ft = '{$id_kelas_filter}'", "row"
			);
			if (empty($k)) {
				$k = $this->mymodel->withquery(
					"SELECT id_kelas_sma AS id_kelas, label, 'sma' AS jenjang FROM kelas_sma WHERE id_kelas_sma = '{$id_kelas_filter}'", "row"
				);
			}
			if ($k) {
				$list_kelas[] = ['id_kelas' => $k->id_kelas, 'label' => $k->label, 'jenjang' => $k->jenjang];
			}
		} else {
			// Semua kelas FT
			$kelas_ft = $this->mymodel->withquery(
				"SELECT id_kelas_ft AS id_kelas, label FROM kelas_ft ORDER BY label ASC", "result"
			);
			foreach ($kelas_ft as $k) {
				$list_kelas[] = ['id_kelas' => $k->id_kelas, 'label' => $k->label, 'jenjang' => 'ft'];
			}
			// Semua kelas SMA
			$kelas_sma = $this->mymodel->withquery(
				"SELECT id_kelas_sma AS id_kelas, label FROM kelas_sma ORDER BY label ASC", "result"
			);
			foreach ($kelas_sma as $k) {
				$list_kelas[] = ['id_kelas' => $k->id_kelas, 'label' => $k->label, 'jenjang' => 'sma'];
			}
		}

		// ── Inisialisasi PHPExcel ──────────────────────────────────────────────
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->removeSheetByIndex(0); // hapus sheet default dulu

		$sheet_index = 0;

		foreach ($list_kelas as $kelas) {
			$jenjang  = $kelas['jenjang'];
			$id_kelas = $kelas['id_kelas'];
			$label    = $kelas['label'];

			// ── Query data siswa per kelas ──────────────────────────────────
			$where_ta = "";
			if ($tgl_mulai && $tgl_selesai) {
				$where_ta = " AND pt.created_at >= '{$tgl_mulai}' AND pt.created_at <= '{$tgl_selesai}'";
			}

			$get_data = $this->mymodel->withquery(
				"SELECT 
					s.id_siswa_{$jenjang}_aktif AS id_siswa_aktif,
					s.nis,
					s.id_kelas,
					'{$jenjang}' AS jenjang,
					pt.id,
					pt.created_at,
					pt.updated_at
				 FROM siswa_{$jenjang}_aktif s
				 LEFT JOIN pt_pilihan_ptn pt 
					ON pt.id_siswa_aktif = s.id_siswa_{$jenjang}_aktif 
					AND pt.jenjang = '{$jenjang}'
					{$where_ta}
				 WHERE s.id_kelas = '{$id_kelas}'
				 GROUP BY s.id_siswa_{$jenjang}_aktif
				 ORDER BY s.nis ASC",
				"result"
			);

			// Skip kelas yang tidak ada datanya
			if (empty($get_data)) continue;

			// ── Buat sheet baru ─────────────────────────────────────────────
			$objSheet = new PHPExcel_Worksheet($objPHPExcel, substr($label, 0, 31)); // max 31 char
			$objPHPExcel->addSheet($objSheet, $sheet_index);
			$objPHPExcel->setActiveSheetIndex($sheet_index);
			$activeSheet = $objPHPExcel->getActiveSheet();
			$activeSheet->setTitle(substr($label, 0, 31));

			// ── Header kolom ────────────────────────────────────────────────
			$rowCount = 1;
			$column   = 'A';
			$header_map = [
				'id'            => 'NO',
				'id_siswa_aktif'=> 'SISWA',
				'id_kelas'      => 'KELAS',
				'id_pt'         => 'PILIHAN 1',
				'id_jurusan'    => 'JURUSAN 1',
				'id_pt2'        => 'PILIHAN 2',
				'id_jurusan2'   => 'JURUSAN 2',
			];
			foreach ($field_search as $fs) {
				$header = isset($header_map[$fs])
					? $header_map[$fs]
					: strtoupper(str_replace("_", " ", $fs));
				$activeSheet->setCellValue($column . $rowCount, $header);
				$column++;
			}

			// ── Isi data ────────────────────────────────────────────────────
			$rowCount = 2;
			foreach ($get_data as $key => $value) {

				// Data siswa
				$siswa = $this->mymodel->withquery(
					"SELECT s.nis, s.nama_lengkap, k.label AS nama_kelas
					 FROM siswa_{$jenjang}_aktif s
					 JOIN kelas_{$jenjang} k ON s.id_kelas = k.id_kelas_{$jenjang}
					 WHERE s.id_siswa_{$jenjang}_aktif = '{$value->id_siswa_aktif}'", "row"
				);
				
				$id_siswa_asli         = $value->id_siswa_aktif; // simpan dulu sebelum di-overwrite
				$value->id_siswa_aktif = $siswa ? $siswa->nama_lengkap : '';
				$value->nis            = $siswa ? $siswa->nis : '';
				$value->id_kelas       = $siswa ? $siswa->nama_kelas : '';

				// Nilai UTBK
				$get_nilai = $this->mymodel->withquery(
					"SELECT nilai_utbk1, nilai_utbk2, nilai_utbk3, nilai_utbk4
					FROM pt_to_siswa
					WHERE jenjang = '{$jenjang}' AND id_siswa_aktif = '{$id_siswa_asli}'", "row"
				);

				// Pilihan PTN
				$get_pilihan = $this->mymodel->withquery(
					"SELECT id_pt, id_jurusan FROM pt_pilihan_ptn
					WHERE jenjang = '{$jenjang}' AND id_siswa_aktif = '{$id_siswa_asli}'", "result"
				);

				// Map nilai ke $value
				$value->id_siswa_aktif   = $siswa ? $siswa->nama_lengkap : '';
				$value->nis              = $siswa ? $siswa->nis : '';
				$value->id_kelas         = $siswa ? $siswa->nama_kelas : '';
				$value->nilai_utbk1      = $get_nilai ? $get_nilai->nilai_utbk1 : 0;
				$value->nilai_utbk2      = $get_nilai ? $get_nilai->nilai_utbk2 : 0;
				$value->nilai_utbk3      = $get_nilai ? $get_nilai->nilai_utbk3 : 0;
				$value->nilai_utbk4      = $get_nilai ? $get_nilai->nilai_utbk4 : 0;

				// Default kosong untuk pilihan
				$value->id_pt = $value->id_jurusan = '';
				$value->id_pt2 = $value->id_jurusan2 = '';
				$value->tgl_pilih_1 = $value->tgl_ubah_1 = '';
				$value->tgl_pilih_2 = $value->tgl_ubah_2 = '';
				$value->persentase_1_utbk1 = $value->persentase_1_utbk2 = '';
				$value->persentase_1_utbk3 = $value->persentase_1_utbk4 = '';
				$value->persentase_2_utbk1 = $value->persentase_2_utbk2 = '';
				$value->persentase_2_utbk3 = $value->persentase_2_utbk4 = '';

				if (!empty($get_pilihan)) {
					foreach ($get_pilihan as $key_ptn => $value_ptn) {
						$ptn = $this->mymodel->withquery(
							"SELECT p.nama_pt, j.jurusan, j.passing_grade
							FROM pt_perguruan_tinggi p
							JOIN pt_jurusan j ON p.id = j.id_perguruan_tinggi
							WHERE p.id = '{$value_ptn->id_pt}' AND j.id = '{$value_ptn->id_jurusan}'", "row"
						);
						if (!$ptn) continue;

						$pg = $ptn->passing_grade ?: 1; // hindari division by zero

						if ($key_ptn == 0) {
							$value->id_pt              = $ptn->nama_pt;
							$value->id_jurusan         = $ptn->jurusan;
							$value->tgl_pilih_1        = $value->created_at;
							$value->tgl_ubah_1         = $value->updated_at;
							$value->persentase_1_utbk1 = number_format(($value->nilai_utbk1 * 100) / $pg, 2, ".", "");
							$value->persentase_1_utbk2 = number_format(($value->nilai_utbk2 * 100) / $pg, 2, ".", "");
							$value->persentase_1_utbk3 = number_format(($value->nilai_utbk3 * 100) / $pg, 2, ".", "");
							$value->persentase_1_utbk4 = number_format(($value->nilai_utbk4 * 100) / $pg, 2, ".", "");
						} elseif ($key_ptn == 1) {
							$value->id_pt2             = $ptn->nama_pt;
							$value->id_jurusan2        = $ptn->jurusan;
							$value->tgl_pilih_2        = $value->created_at;
							$value->tgl_ubah_2         = $value->updated_at;
							$value->persentase_2_utbk1 = number_format(($value->nilai_utbk1 * 100) / $pg, 2, ".", "");
							$value->persentase_2_utbk2 = number_format(($value->nilai_utbk2 * 100) / $pg, 2, ".", "");
							$value->persentase_2_utbk3 = number_format(($value->nilai_utbk3 * 100) / $pg, 2, ".", "");
							$value->persentase_2_utbk4 = number_format(($value->nilai_utbk4 * 100) / $pg, 2, ".", "");
						}
					}
				}

				// Tulis ke sheet
				$column = 'A';
				foreach ($field_search as $j => $kolom) {
					if ($kolom == 'id') {
						$value_data = $key + 1;
					} elseif (!isset($value->$kolom)) {
						$value_data = null;
					} elseif ($value->$kolom !== "") {
						$value_data = strip_tags($value->$kolom);
					} else {
						$value_data = "";
					}
					$activeSheet->setCellValue($column . $rowCount, $value_data);
					$column++;
				}
				$rowCount++;
			}

			$sheet_index++;
		}

		// Jika tidak ada sheet yang terbuat (semua kelas kosong)
		if ($sheet_index == 0) {
			$objPHPExcel->createSheet(0);
			$objPHPExcel->setActiveSheetIndex(0)->setTitle('Tidak ada data');
			$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tidak ada data untuk tahun ajaran yang dipilih.');
		}

		$objPHPExcel->setActiveSheetIndex(0);

		// ── Output ──────────────────────────────────────────────────────────────
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data Pilihan PTN Siswa - ' . date("Y-m-d Hi") . '.xls"');
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
		$this->is_allowed('pt_pilihan_ptn_export');

		$this->model_pt_pilihan_ptn->pdf('pt_pilihan_ptn', 'pt_pilihan_ptn');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pt_pilihan_ptn_export');

		$table = $title = 'pt_pilihan_ptn';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pt_pilihan_ptn->find($id);
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

	
}


/* End of file pt_pilihan_ptn.php */
/* Location: ./application/controllers/administrator/Pt Pilihan Ptn.php */