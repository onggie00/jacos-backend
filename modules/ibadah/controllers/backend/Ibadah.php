<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ibadah Controller
*| --------------------------------------------------------------------------
*| Ibadah site
*|
*/
class Ibadah extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ibadah');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ibadahs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ibadah_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ibadahs'] = $this->model_ibadah->get($filter, $field, $this->limit_page, $offset);
		$this->data['ibadah_counts'] = $this->model_ibadah->count_all($filter, $field);
		$this->data['stats'] = $this->model_ibadah->get_stats();

		$config = array(
			'base_url'     => site_url('administrator/ibadah/index'),
			'total_rows'   => $this->data['ibadah_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
			'page_query_string' => FALSE,
			'query_string_segment' => 'offset',
			'use_page_numbers' => FALSE,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ibadah');
		$this->render('backend/standart/administrator/ibadah/ibadah_list', $this->data);
	}
	
	/**
	* Add new ibadahs
	*
	*/
	public function add()
	{
		$this->is_allowed('ibadah_add');

		$this->template->title('Ibadah New');
		$this->render('backend/standart/administrator/ibadah/ibadah_add', $this->data);
	}

	/**
	* Add New Ibadahs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ibadah_add', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				));
			exit;
		}

		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_ibadah', 'Jenis Ibadah', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('waktu', 'Waktu', 'trim|required');
		$this->form_validation->set_rules('jumlah_rakaat', 'Jumlah Rakaat', 'trim|max_length[11]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = array(
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'jenjang' => $this->input->post('jenjang'),
				'id_ibadah' => $this->input->post('id_ibadah'),
				'tanggal' => $this->input->post('tanggal'),
				'waktu' => $this->input->post('waktu'),
				'jumlah_rakaat' => $this->input->post('jumlah_rakaat'),
				'created_at' => $this->input->post('created_at'),
				'updated_at' => $this->input->post('updated_at'),
				'deleted_at' => $this->input->post('deleted_at'),
				'keterangan' => $this->input->post('keterangan'),
			);

			
			$save_ibadah = $this->model_ibadah->store($save_data);
            

			if ($save_ibadah) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ibadah;
					$this->data['message'] = cclang('success_save_data_stay', array(
						anchor('administrator/ibadah/edit/' . $save_ibadah, 'Edit Ibadah'),
						anchor('administrator/ibadah', ' Go back to list')
					));
				} else {
					set_message(
						cclang('success_save_data_redirect', array(
						anchor('administrator/ibadah/edit/' . $save_ibadah, 'Edit Ibadah')
					)), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ibadah');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ibadah');
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
	* Update view Ibadahs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ibadah_update');

		$this->data['ibadah'] = $this->model_ibadah->find($id);

		$this->template->title('Ibadah Update');
		$this->render('backend/standart/administrator/ibadah/ibadah_update', $this->data);
	}

	/**
	* Update Ibadahs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ibadah_update', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				));
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_ibadah', 'Jenis Ibadah', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('waktu', 'Waktu', 'trim|required');
		$this->form_validation->set_rules('jumlah_rakaat', 'Jumlah Rakaat', 'trim|max_length[11]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = array(
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'jenjang' => $this->input->post('jenjang'),
				'id_ibadah' => $this->input->post('id_ibadah'),
				'tanggal' => $this->input->post('tanggal'),
				'waktu' => $this->input->post('waktu'),
				'jumlah_rakaat' => $this->input->post('jumlah_rakaat'),
				'created_at' => $this->input->post('created_at'),
				'updated_at' => $this->input->post('updated_at'),
				'deleted_at' => $this->input->post('deleted_at'),
				'keterangan' => $this->input->post('keterangan'),
			);

			
			$save_ibadah = $this->model_ibadah->change($id, $save_data);

			if ($save_ibadah) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', array(
						anchor('administrator/ibadah', ' Go back to list')
					));
				} else {
					set_message(
						cclang('success_update_data_redirect', array(
					)), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ibadah');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ibadah');
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
	* delete Ibadahs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ibadah_delete');

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
            set_message(cclang('has_been_deleted', 'ibadah'), 'success');
        } else {
            set_message(cclang('error_delete', 'ibadah'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ibadahs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ibadah_view');

		$this->data['ibadah'] = $this->model_ibadah->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ibadah Detail');
		$this->render('backend/standart/administrator/ibadah/ibadah_view', $this->data);
	}
	
	/**
	* delete Ibadahs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ibadah = $this->model_ibadah->find($id);

		
		
		return $this->model_ibadah->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ibadah_export');

		$jenjang = $this->input->get('jenjang');
		$id_tahun_ajaran = $this->input->get("id_tahun_ajaran") ?: null;
		$id_tingkatan    = $this->input->get("id_tingkatan") ?: null;
		$id_kelas        = $this->input->get("id_kelas") ?: null;

		// Default tanggal: bulan ini
		$start_date = $this->input->get("start_date") ?: date("Y-m-01");
		$end_date   = $this->input->get("end_date") ?: date("Y-m-t");

		// Buat daftar semua tanggal dalam rentang
		$period = new DatePeriod(
			new DateTime($start_date),
			new DateInterval('P1D'),
			(new DateTime($end_date))->modify('+1 day')
		);
		$tanggal_list = array();
		foreach ($period as $dt) {
			$tanggal_list[] = $dt->format("Y-m-d");
		}

		// ambil list kelas
		if (!empty($id_tingkatan)) {
			$get_kelas = $this->mymodel->withquery("SELECT * FROM kelas_{$jenjang} WHERE id_tingkatan = '{$id_tingkatan}'", "result");
		} elseif (!empty($id_kelas)) {
			$get_kelas = $this->mymodel->withquery("SELECT * FROM kelas_{$jenjang} WHERE id_kelas_{$jenjang} = '{$id_kelas}'", "result");
		} else {
			$get_kelas = array();
		}

		// Header kolom Excel
		$field = array('nomor', 'nis', 'id_siswa', 'nama_kelas');
		foreach ($tanggal_list as $tgl) {
			$field[] = date("d/m/Y", strtotime($tgl));
		}
		$field[] = "total_hadir";
		$field[] = "total_tidak_hadir";

		// Load PHPExcel
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();

		// Fungsi untuk generate WHERE
		$buildWhere = function($tahun_ajaran, $kelas = null) {
			$where = array();
			if (!empty($tahun_ajaran)) $where[] = "sa.id_tahun_ajaran='{$tahun_ajaran}'";
			if (!empty($kelas)) $where[] = "sa.id_kelas='{$kelas}'";
			return $where ? "WHERE " . implode(" AND ", $where) : "";
		};

		// Loop tiap kelas (atau 1 sheet jika tanpa filter kelas)
		$kelas_list = !empty($get_kelas) ? $get_kelas : array((object)array('id_kelas_'.$jenjang => $id_kelas, 'label' => 'Semua Kelas'));

		foreach ($kelas_list as $key_kelas => $value_kelas) {

			$id_kelas_field = "id_kelas_{$jenjang}";
			$where = $buildWhere($id_tahun_ajaran, $value_kelas->$id_kelas_field ?? $id_kelas);

			// Ambil siswa
			$get_siswa = $this->mymodel->withquery("
				SELECT sa.id_siswa_{$jenjang}_aktif AS id_siswa, sa.nama_lengkap, sa.nis, sa.id_kelas, k.label AS nama_kelas 
				FROM siswa_{$jenjang}_aktif sa 
				JOIN siswa_{$jenjang} s ON sa.id_siswa_{$jenjang} = s.id_siswa_{$jenjang} and s.agama = 'Islam' 
				JOIN kelas_{$jenjang} k ON sa.id_kelas = k.id_kelas_{$jenjang} 
				{$where} 
				ORDER BY k.label ASC, sa.nama_lengkap ASC
			", "result");

			if (!$get_siswa) continue;

			// Ambil semua presensi dalam rentang tanggal untuk siswa ini
			$ids = implode(",", array_map(function($s) { return "'{$s->id_siswa}'"; }, $get_siswa));
			$presensi = $this->mymodel->withquery("
				SELECT id_siswa_aktif, tanggal, waktu 
				FROM ibadah 
				WHERE tanggal BETWEEN '{$start_date}' AND '{$end_date}'
				AND id_siswa_aktif IN ({$ids})
			", "result");

			// Index presensi
			$presensi_map = array();
			foreach ($presensi as $p) {
				$presensi_map[$p->id_siswa_aktif][$p->tanggal] = $p;
			}

			// Isi data siswa
			foreach ($get_siswa as $s) {
				$total_h = $total_th = 0;
				foreach ($tanggal_list as $tgl) {
					$nama_kolom = date("d/m/Y", strtotime($tgl));
					if (!empty($presensi_map[$s->id_siswa][$tgl])) {
						$waktu = date("H:i", strtotime($presensi_map[$s->id_siswa][$tgl]->waktu));
						$s->$nama_kolom = "H ({$waktu})";
						$total_h++;
					} else {
						$s->$nama_kolom = "-";
						$total_th++;
					}
				}
				$s->total_hadir = $total_h;
				$s->total_tidak_hadir = $total_th;
			}

			// Buat Sheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($key_kelas);
			$objPHPExcel->getActiveSheet()->setTitle($value_kelas->label);

			// Tulis header
			$column = 'A';
			foreach ($field as $f) {
				$header = ($f == "nomor") ? 'NO' : (($f == "id_siswa") ? 'SISWA' : strtoupper(str_replace("_", " ", $f)));
				$objPHPExcel->getActiveSheet()->setCellValue($column . '3', $header);
				$column++;
			}

			// Tulis data
			$rowCount = 4;
			foreach ($get_siswa as $idx => $s) {
				$column = 'A';
				foreach ($field as $kolom) {
					$value_data = null;
					if ($kolom == "nomor") $value_data = $idx + 1;
					elseif ($kolom == "id_kelas") $value_data = $s->nama_kelas;
					elseif ($kolom == "id_siswa") $value_data = $s->nama_lengkap;
					elseif (isset($s->$kolom)) $value_data = strip_tags($s->$kolom);
					$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $value_data);
					$column++;
				}
				$rowCount++;
			}
		}

		// Output Excel
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data Presensi Ibadah Siswa ' . strtoupper($jenjang) . ' - ' . date("d M Y", strtotime($start_date)) . ' s.d ' . date("d M Y", strtotime($end_date)) . ' - ' . date("Y-m-d Hi") . '.xls"');
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ibadah_export');

		$this->model_ibadah->pdf('ibadah', 'ibadah');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ibadah_export');

		$table = $title = 'ibadah';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ibadah->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', array(
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ), TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function filter_presensi($offset = 0)
	{
		$this->is_allowed('ibadah_list');
		$get = $this->input->get();
		$limit = $this->limit_page;
		$jenjang = $this->input->get('jenjang');

		if (empty($jenjang)) {
			redirect('administrator/ibadah');
		}

		// build WHERE clause
		$conditions = array();
		if (!empty($get['id_tahun_ajaran'])) {
			$conditions[] = "s.id_tahun_ajaran = '" . $this->db->escape_str($get['id_tahun_ajaran']) . "'";
		}
		if (!empty($get['id_tingkatan'])) {
			$conditions[] = "k.id_tingkatan = '" . $this->db->escape_str($get['id_tingkatan']) . "'";
		}
		if (!empty($get['id_kelas'])) {
			$conditions[] = "k.id_kelas_{$jenjang} = '" . $this->db->escape_str($get['id_kelas']) . "'";
		}
		if (!empty($get['start_date']) && !empty($get['end_date'])) {
			$sd = $this->db->escape_str($get['start_date']);
			$ed = $this->db->escape_str($get['end_date']);
			$conditions[] = "i.tanggal >= '{$sd}' AND i.tanggal <= '{$ed}'";
		}
		$where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

		// get data with limit
		$get_data = $this->mymodel->withquery("
			SELECT i.*, 
				s.nama_lengkap AS siswa_{$jenjang}_aktif_nama_lengkap, 
				CONCAT('{$jenjang}') AS jenjang, 
				k.id_tingkatan, 
				k.label AS nama_kelas, 
				ib.ibadah AS ibadah_setting_ibadah
			FROM ibadah i
			JOIN siswa_{$jenjang}_aktif s ON i.id_siswa_aktif = s.id_siswa_{$jenjang}_aktif
			JOIN kelas_{$jenjang} k ON s.id_kelas = k.id_kelas_{$jenjang}
			JOIN ibadah_setting ib ON i.id_ibadah = ib.id
			{$where}
			ORDER BY i.tanggal DESC, s.nama_lengkap ASC
			LIMIT {$offset}, {$limit}
		", 'result');

		$total_rows = $this->model_ibadah->count_filter_presensi($jenjang, $where);
		$stats = $this->model_ibadah->get_stats_filter($jenjang, $where);

		$base_url = 'administrator/ibadah/filter_presensi?' . http_build_query($get);
		$base_url = preg_replace('/[&?]offset=\d+/', '', $base_url);
		$base_url .= (strpos($base_url, '?') !== false) ? '&' : '?';

		$config = array(
			'base_url'     => $base_url,
			'total_rows'   => $total_rows,
			'per_page'     => $limit,
			'uri_segment'  => 4,
			'page_query_string' => TRUE,
			'query_string_segment' => 'offset',
			'use_page_numbers' => FALSE,
		);

		$this->data['data_presensi'] = $get_data;
		$this->data['value_counts'] = $total_rows;
		$this->data['pagination'] = $this->pagination($config);
		$this->data['stats'] = $stats;
		$this->data['jenjang'] = $jenjang;

		$this->template->title('Presensi Ibadah');
		$this->render('backend/standart/administrator/ibadah/ibadah_list_filter', $this->data);
	}

	
}


/* End of file ibadah.php */
/* Location: ./application/controllers/administrator/Ibadah.php */
