<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Ft Controller
*| --------------------------------------------------------------------------
*| Presensi Ft site
*|
*/
class Presensi_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_ft_list');

		$get = $this->input->get();
		$limit = 40;
		$where = $this->_build_filter_where($get);

		$data_sql = "SELECT p.*, (SELECT COUNT(*) FROM presensi_catatan_pelajaran pcp WHERE pcp.id_siswa_aktif = p.id_siswa_aktif AND pcp.jenjang = 'ft' AND pcp.tanggal_waktu >= p.tanggal_absen AND pcp.tanggal_waktu < DATE_ADD(p.tanggal_absen, INTERVAL 1 DAY)) AS jml_catatan, s.nama_lengkap, s.nis, k.id_tingkatan, k.label AS nama_kelas, i.jenis_izin FROM presensi_ft p JOIN siswa_ft_aktif s ON p.id_siswa_aktif = s.id_siswa_ft_aktif JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft LEFT JOIN izin_siswa_ft i ON p.id_izin = i.id " . $where . " ORDER BY p.tanggal_absen DESC, s.nama_lengkap ASC LIMIT " . (int)$offset . "," . (int)$limit;
		$data_presensi = $this->mymodel->withquery($data_sql, 'result');

		$count_sql = "SELECT p.id FROM presensi_ft p JOIN siswa_ft_aktif s ON p.id_siswa_aktif = s.id_siswa_ft_aktif JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft LEFT JOIN izin_siswa_ft i ON p.id_izin = i.id " . $where;
		$value_counts = count($this->mymodel->withquery($count_sql, 'result'));

		$summary_sql = "SELECT
			SUM(p.status_absen='Hadir') AS total_hadir,
			SUM(p.status_absen='Terlambat') AS total_terlambat,
			SUM(p.status_absen='Izin') AS total_izin,
			SUM(p.status_absen='Sakit') AS total_sakit,
			SUM(p.status_absen IN ('Alfa','Tidak Hadir')) AS total_alfa
		FROM presensi_ft p
		JOIN siswa_ft_aktif s ON p.id_siswa_aktif = s.id_siswa_ft_aktif
		JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
		" . $where;
		$this->data['status_summary'] = $this->mymodel->withquery($summary_sql, 'row');

		$this->data['data_presensi'] = $data_presensi;
		$this->data['value_counts'] = $value_counts;
		$this->data['filter'] = $get;
		$this->data['offset'] = (int)$offset;
		$this->data['list_tahun_ajaran'] = $this->mymodel->withquery("SELECT * FROM tahun_ajaran ORDER BY label DESC", 'result');
		$this->data['list_tingkatan'] = $this->mymodel->withquery("SELECT * FROM tingkatan_ft ORDER BY label ASC", 'result');
		$this->data['list_kelas'] = $this->mymodel->withquery("SELECT * FROM kelas_ft ORDER BY label ASC", 'result');

		$config = array(
			'base_url' => 'administrator/presensi_ft/index',
			'total_rows' => $value_counts,
			'per_page' => $limit,
			'uri_segment' => 4,
			'reuse_query_string' => true,
		);
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi FT List');
		$this->render('backend/standart/administrator/presensi_ft/presensi_ft_list', $this->data);
	}

	private function _build_filter_where($get)
	{
		$where = '';
		if (!empty($get['id_tahun_ajaran'])) {
			$where .= (empty($where) ? 'WHERE ' : ' AND ') . "s.id_tahun_ajaran = '" . $this->db->escape_str($get['id_tahun_ajaran']) . "'";
		}
		if (!empty($get['id_tingkatan'])) {
			$where .= (empty($where) ? 'WHERE ' : ' AND ') . "k.id_tingkatan = '" . $this->db->escape_str($get['id_tingkatan']) . "'";
		}
		if (!empty($get['id_kelas'])) {
			$where .= (empty($where) ? 'WHERE ' : ' AND ') . "k.id_kelas_ft = '" . $this->db->escape_str($get['id_kelas']) . "'";
		}
		if (!empty($get['start_date']) && !empty($get['end_date'])) {
			$where .= (empty($where) ? 'WHERE ' : ' AND ') . "p.tanggal_absen >= '" . $this->db->escape_str($get['start_date']) . "' AND p.tanggal_absen <= '" . $this->db->escape_str($get['end_date']) . "'";
		}
		if (!empty($get['q'])) {
			$q = $this->db->escape_like_str($get['q']);
			$where .= (empty($where) ? 'WHERE ' : ' AND ') . "(s.nama_lengkap LIKE '%" . $q . "%' OR s.nis LIKE '%" . $q . "%')";
		}
		return $where;
	}
	
	/**
	* Add new presensi_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_ft_add');

		$this->template->title('Presensi FT New');
		$this->render('backend/standart/administrator/presensi_ft/presensi_ft_add', $this->data);
	}

	/**
	* Add New Presensi Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ssid', 'WIFI SSID', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ip', 'IP', 'trim|max_length[50]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('status_absen', 'Status Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('id_izin', 'Izin', 'trim|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'wifi_ssid' => $this->input->post('wifi_ssid'),
				'wifi_ip' => $this->input->post('wifi_ip'),
				'hari_absen' => $this->input->post('hari_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'alasan_terlambat' => $this->input->post('alasan_terlambat'),
				'id_izin' => $this->input->post('id_izin'),
			];

			
			$save_presensi_ft = $this->model_presensi_ft->store($save_data);
            

			if ($save_presensi_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_ft/edit/' . $save_presensi_ft, 'Edit Presensi Ft'),
						anchor('administrator/presensi_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_ft/edit/' . $save_presensi_ft, 'Edit Presensi Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_ft');
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
	* Update view Presensi Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_ft_update');

		$this->data['presensi_ft'] = $this->model_presensi_ft->find($id);

		$this->template->title('Presensi FT Update');
		$this->render('backend/standart/administrator/presensi_ft/presensi_ft_update', $this->data);
	}

	/**
	* Update Presensi Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ssid', 'WIFI SSID', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ip', 'IP', 'trim|max_length[50]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('status_absen', 'Status Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('id_izin', 'Izin', 'trim|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'wifi_ssid' => $this->input->post('wifi_ssid'),
				'wifi_ip' => $this->input->post('wifi_ip'),
				'hari_absen' => $this->input->post('hari_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'alasan_terlambat' => $this->input->post('alasan_terlambat'),
				'id_izin' => $this->input->post('id_izin'),
			];

			
			$save_presensi_ft = $this->model_presensi_ft->change($id, $save_data);

			if ($save_presensi_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_ft');
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
	* delete Presensi Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_ft_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_ft_view');

		$this->data['presensi_ft'] = $this->model_presensi_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi FT Detail');
		$this->render('backend/standart/administrator/presensi_ft/presensi_ft_view', $this->data);
	}
	
	/**
	* delete Presensi Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_ft = $this->model_presensi_ft->find($id);

		
		
		return $this->model_presensi_ft->remove($id);
	}

	/**
	 * Ambil catatan pelajaran FT dan kelompokkan per siswa serta tanggal.
	 */
	private function _get_catatan_pelajaran_map($start_date, $end_date)
	{
		$end_exclusive = date('Y-m-d', strtotime($end_date . ' +1 day')) . ' 00:00:00';

		$rows = $this->db
			->select('id_siswa_aktif, tanggal_waktu, jam_ke, status_hadir, keterangan, updated_by')
			->from('presensi_catatan_pelajaran')
			->where('jenjang', 'ft')
			->where('tanggal_waktu >=', $start_date . ' 00:00:00')
			->where('tanggal_waktu <', $end_exclusive)
			->order_by('id_siswa_aktif', 'ASC')
			->order_by('tanggal_waktu', 'ASC')
			->order_by('jam_ke', 'ASC')
			->get()
			->result();

		$map = array();
		foreach ($rows as $row) {
			$tanggal = date('Y-m-d', strtotime($row->tanggal_waktu));
			$keterangan = trim(preg_replace('/\s+/', ' ', strip_tags($row->keterangan)));
			$updated_by = trim(preg_replace('/\s+/', ' ', strip_tags($row->updated_by)));
			$map[$row->id_siswa_aktif][$tanggal][] = array(
				'tanggal' => $tanggal,
				'jam_ke' => $row->jam_ke,
				'status_hadir' => trim(strip_tags($row->status_hadir)),
				'keterangan' => $keterangan,
				'updated_by' => $updated_by
			);
		}

		return $map;
	}

	private function _get_export_sheet_title($kelas_label, $is_catatan)
	{
		$clean_label = trim(preg_replace('/[\[\]\:\*\?\/\\\\]/', '-', strip_tags($kelas_label)));
		$suffix = '-Catatan';

		if ($clean_label === '') {
			$clean_label = 'Kelas';
		}

		$max_length = 31 - PHPExcel_Shared_String::CountCharacters($suffix);
		$title = PHPExcel_Shared_String::Substring($clean_label, 0, $max_length);

		return $is_catatan ? $title . $suffix : $title;
	}

	private function _get_or_create_export_sheet($excel, $sheet_index, $title, $is_catatan = false)
	{
		$worksheet_names = $excel->getSheetNames();
		foreach ($worksheet_names as $key => $worksheet_name) {
			$worksheet_names[$key] = PHPExcel_Shared_String::StrToLower($worksheet_name);
		}
		$original_title = $title;
		$catatan_suffix = '-Catatan';
		$base_title = $is_catatan
			? PHPExcel_Shared_String::Substring(
				$original_title,
				0,
				PHPExcel_Shared_String::CountCharacters($original_title) - PHPExcel_Shared_String::CountCharacters($catatan_suffix)
			)
			: $original_title;
		$counter = 2;

		while (
			in_array(PHPExcel_Shared_String::StrToLower($title), $worksheet_names)
			|| (
				!$is_catatan
				&& in_array(PHPExcel_Shared_String::StrToLower($title . $catatan_suffix), $worksheet_names)
			)
		) {
			$counter_suffix = '-' . $counter;
			$title = PHPExcel_Shared_String::Substring(
				$base_title,
				0,
				31
					- PHPExcel_Shared_String::CountCharacters($counter_suffix)
					- PHPExcel_Shared_String::CountCharacters($catatan_suffix)
			) . $counter_suffix . ($is_catatan ? $catatan_suffix : '');
			$counter++;
		}

		if ($sheet_index === 0) {
			$sheet = $excel->setActiveSheetIndex(0);
		} else {
			$excel->createSheet($sheet_index);
			$sheet = $excel->setActiveSheetIndex($sheet_index);
		}

		$sheet->setTitle($title);
		return $sheet;
	}

	private function _create_catatan_pelajaran_sheet($excel, $sheet_index, $sheet_base_title, $siswa, $catatan_map)
	{
		$sheet_title = $sheet_base_title . '-Catatan';
		$sheet = $this->_get_or_create_export_sheet($excel, $sheet_index, $sheet_title, true);
		$headers = array('NO', 'NIS', 'NAMA SISWA', 'KELAS', 'TANGGAL', 'JAM KE', 'STATUS HADIR', 'KETERANGAN', 'UPDATED BY');
		$header_row = 3;
		$column = 'A';

		foreach ($headers as $header) {
			$sheet->setCellValue($column . $header_row, $header);
			$sheet->getStyle($column . $header_row)->getFont()->setBold(true);
			$sheet->getStyle($column . $header_row)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$column++;
		}

		$sheet->getColumnDimension('A')->setWidth(7);
		$sheet->getColumnDimension('B')->setWidth(18);
		$sheet->getColumnDimension('C')->setWidth(32);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(10);
		$sheet->getColumnDimension('G')->setWidth(18);
		$sheet->getColumnDimension('H')->setWidth(45);
		$sheet->getColumnDimension('I')->setWidth(25);

		$row = 4;
		$number = 1;
		foreach ($siswa as $siswa_row) {
			if (!isset($catatan_map[$siswa_row->id_siswa])) {
				continue;
			}

			$catatan_per_tanggal = $catatan_map[$siswa_row->id_siswa];
			ksort($catatan_per_tanggal);
			foreach ($catatan_per_tanggal as $details) {
				foreach ($details as $detail) {
					$values = array(
						$number,
						$siswa_row->nis,
						$siswa_row->nama_lengkap,
						$siswa_row->nama_kelas,
						date('d/m/Y', strtotime($detail['tanggal'])),
						$detail['jam_ke'],
						$detail['status_hadir'],
						$detail['keterangan'],
						$detail['updated_by']
					);
					$column = 'A';
					foreach ($values as $value) {
						$cell = $column . $row;
						$sheet->setCellValueExplicit(
							$cell,
							(string)$value,
							PHPExcel_Cell_DataType::TYPE_STRING
						);
						$sheet->getStyle($cell)->getAlignment()
							->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
						if ($column === 'H' || $column === 'I') {
							$sheet->getStyle($cell)->getAlignment()->setWrapText(true);
						}
						$column++;
					}
					$row++;
					$number++;
				}
			}
		}
	}

	private function _get_export_filter_label($id_tingkatan, $id_kelas)
	{
		$filter = null;

		if (!empty($id_kelas)) {
			$filter = $this->db
				->select('label')
				->where('id_kelas_ft', $id_kelas)
				->get('kelas_ft')
				->row();
		} elseif (!empty($id_tingkatan)) {
			$filter = $this->db
				->select('label')
				->where('id_tingkatan_ft', $id_tingkatan)
				->get('tingkatan_ft')
				->row();
		}

		return $filter && isset($filter->label) ? $filter->label : 'Semua';
	}

	private function _build_export_filename($start_date, $end_date, $filter_label)
	{
		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));
		$filter_label = trim(preg_replace('/\s+/', ' ', strip_tags($filter_label)));

		if ($filter_label === '') {
			$filter_label = 'Semua';
		}

		$filter_label = preg_replace('/[\\\\\/\:\*\?"<>\|]+/', '-', $filter_label);

		return 'Presensi FT - ' . $start_date . '-' . $end_date . '-' . $filter_label . '.xls';
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export_bulanan()
	{
		$this->is_allowed('presensi_ft_export');

		$id_tahun_ajaran = $this->input->get("id_tahun_ajaran");
		$id_tingkatan = $this->input->get("id_tingkatan");
		$id_kelas = $this->input->get("id_kelas");
		$start_date = $this->input->get("start_date") ?? date("Y-m-01");
		$bulan = date("m", strtotime($start_date));
		$tahun = date("Y", strtotime($start_date));
		$total_day_in_month = date("t", strtotime("$tahun-$bulan-01"));

		$field = ['nomor', 'nis', 'id_siswa', 'nama_kelas'];
		for ($i = 1; $i <= $total_day_in_month; $i++) {
			$field[] = "$i/$bulan/$tahun";
		}
		$field = array_merge($field, ["total_hadir", "total_alfa", "total_terlambat", "total_sakit", "total_izin"]);

		$where_kelas = "";
		if ($id_tingkatan) {
			$where_kelas = "WHERE id_tingkatan = '$id_tingkatan'";
		} elseif ($id_kelas) {
			$where_kelas = "WHERE id_kelas_ft = '$id_kelas'";
		}
		$get_kelas = $this->mymodel->withquery("SELECT * FROM kelas_ft $where_kelas", "result");

		$date_range = range(1, $total_day_in_month);
		$presensi_rows = $this->mymodel->withquery("SELECT * FROM presensi_ft WHERE MONTH(tanggal_absen) = '$bulan' AND YEAR(tanggal_absen) = '$tahun'", "result");
		$presensi_map = [];
		foreach ($presensi_rows as $row) {
			$tgl = date("j", strtotime($row->tanggal_absen));
			$presensi_map[$row->id_siswa_aktif][$tgl] = $row;
		}

		$month_start = "$tahun-$bulan-01";
		$month_end = "$tahun-$bulan-$total_day_in_month";
		$catatan_map = $this->_get_catatan_pelajaran_map($month_start, $month_end);
		$filename_end_date = $this->input->get("end_date")
			? $this->input->get("end_date")
			: $month_end;
		$filename = $this->_build_export_filename(
			$start_date,
			$filename_end_date,
			$this->_get_export_filter_label($id_tingkatan, $id_kelas)
		);

		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();

		if ($get_kelas) {
			foreach ($get_kelas as $key_kelas => $kelas) {
				$where = [];
				if ($id_tahun_ajaran) $where[] = "s.id_tahun_ajaran = '$id_tahun_ajaran'";
				if ($kelas->id_kelas_ft) $where[] = "s.id_kelas = '$kelas->id_kelas_ft'";
				$where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";

				$get_siswa = $this->mymodel->withquery("SELECT s.id_siswa_ft_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas FROM siswa_ft_aktif s JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft $where_clause ORDER BY k.label ASC, s.nama_lengkap ASC", "result");

				foreach ($get_siswa as $siswa) {
					$total = ['Hadir' => 0, 'Tidak Hadir' => 0, 'Alfa' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0];

					foreach ($date_range as $day) {
						$label = "$day/$bulan/$tahun";
						$presensi = $presensi_map[$siswa->id_siswa][$day] ?? null;
						if ($presensi) {
							$jam = date("H:i", strtotime($presensi->waktu_absen));
							$siswa->$label = substr($presensi->status_absen, 0, 1) . " ($jam) (" . $presensi->wifi_ssid . ")";
							if ($presensi->status_absen === 'Terlambat') {
								$siswa->$label .= $presensi->alasan_terlambat;
							}
							$total[$presensi->status_absen]++;
						} else {
							$siswa->$label = "-";
						}
					}

					$siswa->total_hadir = $total['Hadir'];
					$siswa->total_alfa = $total['Tidak Hadir'] + $total['Alfa'];
					$siswa->total_terlambat = $total['Terlambat'];
					$siswa->total_sakit = $total['Sakit'];
					$siswa->total_izin = $total['Izin'];
				}

				$presensi_sheet_index = $key_kelas * 2;
				$presensi_sheet = $this->_get_or_create_export_sheet(
					$objPHPExcel,
					$presensi_sheet_index,
					$this->_get_export_sheet_title($kelas->label, false)
				);

				$rowCount = 3;
				$col = 'A';
				foreach ($field as $f) {
					$label = strtoupper(str_replace(["_", "id_siswa"], [" ", "SISWA"], $f));
					if ($f === 'nomor') $label = 'NO';
					$presensi_sheet->setCellValue($col.$rowCount, $label);
					$col++;
				}

				$rowCount = 4;
				foreach ($get_siswa as $index => $siswa) {
					$col = 'A';
					foreach ($field as $f) {
						$val = isset($siswa->$f) ? strip_tags($siswa->$f) : '';
						if ($f === 'nomor') $val = $index + 1;
						if ($f === 'id_kelas') $val = $siswa->nama_kelas;
						if ($f === 'id_siswa') $val = $siswa->nama_lengkap;
						$cell = $col.$rowCount;
						$presensi_sheet->setCellValue($cell, $val);
						if (strpos($val, "\n") !== false) {
							$presensi_sheet->getStyle($cell)->getAlignment()->setWrapText(true);
							$presensi_sheet->getRowDimension($rowCount)->setRowHeight(-1);
						}
						$col++;
					}
					$rowCount++;
				}

				$this->_create_catatan_pelajaran_sheet(
					$objPHPExcel,
					$presensi_sheet_index + 1,
					$presensi_sheet->getTitle(),
					$get_siswa,
					$catatan_map
				);
			}
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}

	public function export_harian()
	{
		$this->is_allowed('presensi_ft_export');
	
		$id_tahun_ajaran = $this->input->get("id_tahun_ajaran");
		$id_tingkatan = $this->input->get("id_tingkatan");
		$id_kelas = $this->input->get("id_kelas");
	
		$tgl_start = $this->input->get("start_date") ? date("Y-m-d", strtotime($this->input->get("start_date"))) : date("Y-m-d");
		$tgl_end = $this->input->get("end_date") ? date("Y-m-d", strtotime($this->input->get("end_date"))) : date("Y-m-d");
	
		$kelas_query = '';
		if (!empty($id_tingkatan)) {
			$kelas_query = "SELECT * FROM kelas_ft WHERE id_tingkatan = '$id_tingkatan'";
		} elseif (!empty($id_kelas)) {
			$kelas_query = "SELECT * FROM kelas_ft WHERE id_kelas_ft = '$id_kelas'";
		}
		$get_kelas = $kelas_query ? $this->mymodel->withquery($kelas_query, "result") : [];
	
		$start = new DateTime($tgl_start);
		$end = (new DateTime($tgl_end))->modify('+1 day');
		$interval = new DateInterval('P1D');
		$period = new DatePeriod($start, $interval, $end);
	
		$field = ['nomor', 'nis', 'id_siswa', 'nama_kelas'];
		foreach ($period as $date) {
			$field[] = $date->format("d/m/Y");
		}
		$field = array_merge($field, ["total_hadir", "total_alfa", "total_terlambat", "total_sakit", "total_izin"]);
	
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$catatan_map = $this->_get_catatan_pelajaran_map($tgl_start, $tgl_end);
		$filename = $this->_build_export_filename(
			$tgl_start,
			$tgl_end,
			$this->_get_export_filter_label($id_tingkatan, $id_kelas)
		);
	
		if (!empty($get_kelas)) {
			foreach ($get_kelas as $key_kelas => $value_kelas) {
				$where = [];
				if (!empty($id_tahun_ajaran)) $where[] = "s.id_tahun_ajaran = '$id_tahun_ajaran'";
				if (!empty($value_kelas->id_kelas_ft)) $where[] = "s.id_kelas = '{$value_kelas->id_kelas_ft}'";
	
				$sql_where = $where ? "WHERE " . implode(" AND ", $where) : "";
				$get_siswa = $this->mymodel->withquery("SELECT s.id_siswa_ft_aktif AS id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas FROM siswa_ft_aktif s JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft $sql_where ORDER BY k.label ASC, s.nama_lengkap ASC", "result");
	
				$presensi_map = [];
				$presensi_rows = $this->mymodel->withquery("SELECT * FROM presensi_ft WHERE tanggal_absen BETWEEN '$tgl_start' AND '$tgl_end'", "result");
				foreach ($presensi_rows as $presensi) {
					$presensi_map[$presensi->id_siswa_aktif][$presensi->tanggal_absen] = $presensi;
				}

				foreach ($get_siswa as $key => $value) {
					$totals = ['Hadir' => 0, 'Tidak Hadir' => 0, 'Alfa' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0];
	
					foreach ($period as $date) {
						$tgl = $date->format("Y-m-d");
						$label = $date->format("d/m/Y");
						$presensi = $presensi_map[$value->id_siswa][$tgl] ?? null;

						if ($presensi) {
							$status = $presensi->status_absen;
							$waktu = date("H:i", strtotime($presensi->waktu_absen));
							$value->$label = substr($status, 0, 1) . " ($waktu) (" . $presensi->wifi_ssid . ")";
							if ($status === "Terlambat") {
								$value->$label .= $presensi->alasan_terlambat;
							}
							$totals[$status]++;
						} else {
							$value->$label = "-";
						}
					}
	
					$value->total_hadir = $totals['Hadir'];
					$value->total_alfa = $totals['Tidak Hadir'] + $totals['Alfa'];
					$value->total_terlambat = $totals['Terlambat'];
					$value->total_sakit = $totals['Sakit'];
					$value->total_izin = $totals['Izin'];
				}
	
				$presensi_sheet_index = $key_kelas * 2;
				$presensi_sheet = $this->_get_or_create_export_sheet(
					$objPHPExcel,
					$presensi_sheet_index,
					$this->_get_export_sheet_title($value_kelas->label, false)
				);
	
				$rowCount = 3;
				$column = 'A';
				foreach ($field as $col) {
					$label = strtoupper(str_replace(["_", "id_siswa"], [" ", "SISWA"], $col));
					if ($col == "nomor") $label = "NO";
					$presensi_sheet->setCellValue($column.$rowCount, $label);
					$column++;
				}
	
				$rowCount = 4;
				foreach ($get_siswa as $key => $value) {
					$column = 'A';
					foreach ($field as $col) {
						$val = isset($value->$col) ? strip_tags($value->$col) : '';
						if ($col == "nomor") $val = $key + 1;
						if ($col == "id_kelas") $val = $value->nama_kelas;
						if ($col == "id_siswa") $val = $value->nama_lengkap;
						$cell = $column.$rowCount;
						$presensi_sheet->setCellValue($cell, $val);
						if (strpos($val, "\n") !== false) {
							$presensi_sheet->getStyle($cell)->getAlignment()->setWrapText(true);
							$presensi_sheet->getRowDimension($rowCount)->setRowHeight(-1);
						}
						$column++;
					}
					$rowCount++;
				}

				$this->_create_catatan_pelajaran_sheet(
					$objPHPExcel,
					$presensi_sheet_index + 1,
					$presensi_sheet->getTitle(),
					$get_siswa,
					$catatan_map
				);
			}
		}
	
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}

	public function export_periode()
	{
		$this->is_allowed('presensi_ft_export');

		$id_tahun_ajaran = $this->input->get("id_tahun_ajaran");
		$id_tingkatan    = $this->input->get("id_tingkatan");
		$id_kelas        = $this->input->get("id_kelas");

		$start_date = $this->input->get("start_date") ?? date("Y-01-01");
		$end_date   = $this->input->get("end_date") ?? date("Y-m-d");

		// ======================
		// Generate bulan range
		// ======================
		$period = [];
		$start = new DateTime(date("Y-m-01", strtotime($start_date)));
		$end   = new DateTime(date("Y-m-01", strtotime($end_date)));

		while ($start <= $end) {
			$key = $start->format("Y-m");
			$period[$key] = [
				'label' => strtoupper($start->format("M"))
			];
			$start->modify("+1 month");
		}

		// ======================
		// Kelas
		// ======================
		$where_kelas = "";
		if ($id_tingkatan) {
			$where_kelas = "WHERE id_tingkatan = '$id_tingkatan'";
		} elseif ($id_kelas) {
			$where_kelas = "WHERE id_kelas_ft = '$id_kelas'";
		}

		$get_kelas = $this->mymodel->withquery("SELECT * FROM kelas_ft $where_kelas", "result");

		// ======================
		// Presensi (AGREGASI)
		// ======================
		$presensi = $this->mymodel->withquery("
			SELECT 
				id_siswa_aktif,
				DATE_FORMAT(tanggal_absen, '%Y-%m') as bulan,
				SUM(status_absen = 'Sakit') as sakit,
				SUM(status_absen = 'Izin') as izin,
				SUM(status_absen IN ('Alfa','Tidak Hadir')) as alfa,
				SUM(status_absen = 'Terlambat') as terlambat
			FROM presensi_ft
			WHERE tanggal_absen BETWEEN '$start_date' AND '$end_date'
			GROUP BY id_siswa_aktif, bulan
		", "result");

		$map = [];
		foreach ($presensi as $p) {
			$map[$p->id_siswa_aktif][$p->bulan] = $p;
		}
		$catatan_map = $this->_get_catatan_pelajaran_map($start_date, $end_date);
		$filename = $this->_build_export_filename(
			$start_date,
			$end_date,
			$this->_get_export_filter_label($id_tingkatan, $id_kelas)
		);

		// ======================
		// Excel Init
		// ======================
		$this->load->library('Excel/PHPExcel');
		$excel = new PHPExcel();

		foreach ($get_kelas as $i => $kelas) {

			// ======================
			// Ambil siswa
			// ======================
			$where = [];
			if ($id_tahun_ajaran) $where[] = "s.id_tahun_ajaran = '$id_tahun_ajaran'";
			if ($kelas->id_kelas_ft) $where[] = "s.id_kelas = '$kelas->id_kelas_ft'";
			$where_clause = $where ? "WHERE ".implode(" AND ", $where) : "";

			$siswa = $this->mymodel->withquery("
				SELECT s.id_siswa_ft_aktif AS id_siswa, s.nama_lengkap, s.nis,
					k.label AS nama_kelas
				FROM siswa_ft_aktif s
				JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
				$where_clause
				ORDER BY s.nama_lengkap
			", "result");

			$presensi_sheet_index = $i * 2;
			$sheet = $this->_get_or_create_export_sheet(
				$excel,
				$presensi_sheet_index,
				$this->_get_export_sheet_title($kelas->label, false)
			);

			$row1 = 3;
			$row2 = 4;
			$col  = 'A';

			// ======================
			// Header awal
			// ======================
			$base = ['NO','NIS','NAMA','KELAS'];

			foreach ($base as $b) {
				$sheet->mergeCells($col.$row1.':'.$col.$row2);
				$sheet->setCellValue($col.$row1, $b);
				$sheet->getStyle($col.$row1)->getFont()->setBold(true);
				$sheet->getStyle($col.$row1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle($col.$row1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheet->getStyle($col.$row1)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$sheet->getStyle($col.$row1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$sheet->getStyle($col.$row1)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$sheet->getStyle($col.$row1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$col++;
			}

			// ======================
			// Header bulan
			// ======================
			foreach ($period as $key => $p) {

				$startCol = $col;
				$endCol   = chr(ord($col)+2);

				$sheet->mergeCells($startCol.$row1.':'.$endCol.$row1);
				$sheet->setCellValue($startCol.$row1, $p['label']);

				$sheet->setCellValue($startCol.$row2, 'S');
				$sheet->setCellValue(chr(ord($startCol)+1).$row2, 'I');
				$sheet->setCellValue(chr(ord($startCol)+2).$row2, 'A');

				$sheet->getStyle($startCol.$row1.':'.$endCol.$row2)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => 'FFFF00')
						),
						'borders' => array(
							'top' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							),
							'bottom' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							),
							'left' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							),
							'right' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							)
							),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
						),
						'font' => array(
							'bold' => true
						)
					)
				);
				$col = chr(ord($col)+3);
			}

			// ======================
			// TOTAL
			// ======================
			$startCol = $col;
			$endCol   = chr(ord($col)+2);

			$sheet->mergeCells($startCol.$row1.':'.$endCol.$row1);
			$sheet->setCellValue($startCol.$row1, 'TOTAL');

			$sheet->setCellValue($startCol.$row2, 'S');
			$sheet->setCellValue(chr(ord($startCol)+1).$row2, 'I');
			$sheet->setCellValue(chr(ord($startCol)+2).$row2, 'A');
			$sheet->getStyle($startCol.$row1.':'.$endCol.$row2)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FFFF00')
					),
					'borders' => array(
						'top' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						),
						'bottom' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						),
						'left' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						),
						'right' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						)
						),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
					'font' => array(
						'bold' => true
					)
				)
			);
			$col = chr(ord($col)+3);

			// ======================
			// TERLAMBAT
			// ======================
			$sheet->mergeCells($col.$row1.':'.$col.$row2);
			$sheet->setCellValue($col.$row1, 'KETERLAMBATAN');
			$sheet->getStyle($col.$row1)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FFFF00')
					),
					'borders' => array(
						'top' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						),
						'bottom' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						),
						'left' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						),
						'right' => array(
							'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => '000000')
						)
						),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
					'font' => array(
						'bold' => true
					)
				)
			);

			// ======================
			// DATA
			// ======================
			$row = 5;

			foreach ($siswa as $idx => $s) {

				$col = 'A';
				$styleArray = array(
						'borders' => array(
							'top' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							),
							'bottom' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							),
							'left' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							),
							'right' => array(
								'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							)
							),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
						)
					);

				// $sheet->getDefaultStyle()->applyFromArray($styleArray);
				$sheet->getStyle($col.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue($col++.$row, $idx+1);
				$sheet->getStyle($col.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue($col++.$row, $s->nis);
				$sheet->setCellValue($col++.$row, $s->nama_lengkap);
				$sheet->getStyle($col.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue($col++.$row, $s->nama_kelas);

				$totalS = 0;
				$totalI = 0;
				$totalA = 0;
				$totalT = 0;

				foreach ($period as $key => $p) {

					$d = $map[$s->id_siswa][$key] ?? null;

					$S = $d->sakit ?? 0;
					$I = $d->izin ?? 0;
					$A = $d->alfa ?? 0;
					$T = $d->terlambat ?? 0;

					$sheet->setCellValue($col++.$row, $S)->getStyle($col.$row)->applyFromArray($styleArray);
					$sheet->setCellValue($col++.$row, $I)->getStyle($col.$row)->applyFromArray($styleArray);
					$sheet->setCellValue($col++.$row, $A)->getStyle($col.$row)->applyFromArray($styleArray);

					$totalS += $S;
					$totalI += $I;
					$totalA += $A;
					$totalT += $T;
				}

				// total
				$sheet->setCellValue($col++.$row, $totalS)->getStyle($col.$row)->applyFromArray($styleArray);
				$sheet->setCellValue($col++.$row, $totalI)->getStyle($col.$row)->applyFromArray($styleArray);
				$sheet->setCellValue($col++.$row, $totalA)->getStyle($col.$row)->applyFromArray($styleArray);

				// terlambat
				$sheet->setCellValue($col++.$row, $totalT)->getStyle($col.$row)->applyFromArray($styleArray);

				$row++;
			}

			$this->_create_catatan_pelajaran_sheet(
				$excel,
				$presensi_sheet_index + 1,
				$sheet->getTitle(),
				$siswa,
				$catatan_map
			);
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		PHPExcel_IOFactory::createWriter($excel, 'Excel5')->save('php://output');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_ft_export');

		$this->model_presensi_ft->pdf('presensi_ft', 'presensi_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_ft_export');

		$table = $title = 'presensi_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_ft->find($id);
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

	/**
	 * Detail catatan pelajaran per siswa per tanggal (AJAX partial view)
	 */
	public function detail_catatan()
	{
		if (!$this->is_allowed('presensi_ft_list', false)) {
			echo '<div class="alert alert-danger" style="margin:0"><i class="fa fa-exclamation-triangle"></i> Anda tidak memiliki akses.</div>';
			exit;
		}

		$id_siswa = $this->input->post('id_siswa_aktif');
		$tanggal  = $this->input->post('tanggal_absen');

		if (empty($id_siswa) || empty($tanggal)) {
			echo '<div class="alert alert-warning" style="margin:0"><i class="fa fa-info-circle"></i> Data tidak lengkap.</div>';
			exit;
		}

		$tanggal_esc = $this->db->escape_str($tanggal);
		$tanggal_start = $tanggal_esc . ' 00:00:00';

		$sql = "SELECT * FROM presensi_catatan_pelajaran
				WHERE id_siswa_aktif = '" . $this->db->escape_str($id_siswa) . "'
				AND jenjang = 'ft'
				AND tanggal_waktu >= '" . $tanggal_start . "'
				AND tanggal_waktu < DATE_ADD('" . $tanggal_esc . "', INTERVAL 1 DAY)
				ORDER BY jam_ke ASC";

		$this->data['catatan'] = $this->mymodel->withquery($sql, 'result');
		$this->load->view('backend/standart/administrator/presensi_ft/presensi_ft_detail_catatan', $this->data);
	}

	public function filter_presensi($offset = 0)
	{
		$this->index($offset);
	}

	/**
	 * AJAX: Get daftar siswa terlambat >= N kali dalam tahun ajaran tertentu
	 */
	public function get_siswa_terlambat()
	{
		if (!$this->is_allowed('presensi_ft_list', false)) {
			echo json_encode(array('success' => false, 'message' => 'Akses ditolak'));
			exit;
		}

		$id_tahun_ajaran = $this->input->post('id_tahun_ajaran');
		$id_tingkatan = $this->input->post('id_tingkatan');
		$id_kelas = $this->input->post('id_kelas');

		if (empty($id_tahun_ajaran)) {
			echo json_encode(array('success' => false, 'message' => 'Tahun ajaran wajib dipilih'));
			exit;
		}

		// Ambil range tanggal tahun ajaran
		$ta = $this->db->select('label,tanggal_mulai,tanggal_selesai')->where('id_tahun_ajaran', $this->db->escape_str($id_tahun_ajaran))->get('tahun_ajaran')->row();
		if (!$ta) {
			echo json_encode(array('success' => false, 'message' => 'Tahun ajaran tidak ditemukan'));
			exit;
		}

		$start = $this->db->escape_str($ta->tanggal_mulai);
		$end = $this->db->escape_str($ta->tanggal_selesai);

		// Query agregasi terlambat per siswa
		$sql = "SELECT 
				s.id_siswa_ft_aktif AS id_siswa_aktif,
				s.nama_lengkap,
				s.nis,
				k.label AS nama_kelas,
				COUNT(*) AS jml_terlambat
			FROM presensi_ft p
			JOIN siswa_ft_aktif s ON p.id_siswa_aktif = s.id_siswa_ft_aktif
			JOIN kelas_ft k ON s.id_kelas = k.id_kelas_ft
			WHERE p.status_absen = 'Terlambat'
			AND p.tanggal_absen >= " . $start . "
			AND p.tanggal_absen <= " . $end;

		if (!empty($id_tingkatan)) {
			$sql .= " AND k.id_tingkatan = " . $this->db->escape_str($id_tingkatan);
		}
		if (!empty($id_kelas)) {
			$sql .= " AND k.id_kelas_ft = " . $this->db->escape_str($id_kelas);
		}

		$sql .= " GROUP BY s.id_siswa_ft_aktif, s.nama_lengkap, s.nis, k.label
				HAVING jml_terlambat >= 3
				ORDER BY jml_terlambat DESC, s.nama_lengkap ASC";

		$siswa_terlambat = $this->mymodel->withquery($sql, 'result');

		// Cari max untuk highlight
		$max_terlambat = 0;
		if (!empty($siswa_terlambat)) {
			$max_terlambat = max(array_column($siswa_terlambat, 'jml_terlambat'));
		}

		echo json_encode(array(
			'success' => true,
			'data' => $siswa_terlambat,
			'max_terlambat' => $max_terlambat,
			'tahun_ajaran' => $ta->label,
			'start' => $ta->tanggal_mulai,
			'end' => $ta->tanggal_selesai
		));
	}

	/**
	 * AJAX: Get riwayat terlambat per siswa dalam tahun ajaran
	 */
	public function get_riwayat_terlambat()
	{
		if (!$this->is_allowed('presensi_ft_list', false)) {
			echo json_encode(array('success' => false, 'message' => 'Akses ditolak'));
			exit;
		}

		$id_siswa_aktif = $this->input->post('id_siswa_aktif');
		$id_tahun_ajaran = $this->input->post('id_tahun_ajaran');

		if (empty($id_siswa_aktif) || empty($id_tahun_ajaran)) {
			echo json_encode(array('success' => false, 'message' => 'Data tidak lengkap'));
			exit;
		}

		// Ambil range tanggal tahun ajaran
		$ta = $this->db->select('label,tanggal_mulai,tanggal_selesai')->where('id_tahun_ajaran', $this->db->escape_str($id_tahun_ajaran))->get('tahun_ajaran')->row();
		if (!$ta) {
			echo json_encode(array('success' => false, 'message' => 'Tahun ajaran tidak ditemukan'));
			exit;
		}

		$start = $this->db->escape_str($ta->tanggal_mulai);
		$end = $this->db->escape_str($ta->tanggal_selesai);
		$siswa = $this->db->escape_str($id_siswa_aktif);

		$sql = "SELECT 
				tanggal_absen,
				waktu_absen,
				alasan_terlambat,
				DATE_FORMAT(tanggal_absen, '%W') AS hari
			FROM presensi_ft
			WHERE id_siswa_aktif = " . $siswa . "
			AND status_absen = 'Terlambat'
			AND tanggal_absen >= " . $start . "
			AND tanggal_absen <= " . $end . "
			ORDER BY tanggal_absen DESC, waktu_absen DESC";

		$riwayat = $this->mymodel->withquery($sql, 'result');

		echo json_encode(array(
			'success' => true,
			'data' => $riwayat,
			'nama_siswa' => $this->db->select('nama_lengkap')->where('id_siswa_ft_aktif', $siswa)->get('siswa_ft_aktif')->row()->nama_lengkap
		));
	}
	
}


/* End of file presensi_ft.php */
/* Location: ./application/controllers/administrator/Presensi Ft.php */
