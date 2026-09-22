<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Presensi Pramuka Controller
*| --------------------------------------------------------------------------
*| Presensi Pramuka site — Admin panel
*|
*/
class Presensi_pramuka extends Admin
{

    private $locked_jenjangs = null;
    private $lock_label = '';
    private $url_method = 'index';
    private $_user_jenjang_lock = null;
    private $_user_lock_label = '';

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_pramuka');
		$this->load->helper('guru_token');
		$this->lang->load('web_lang', $this->current_lang);

		$this->_detect_user_jenjang();
	}

	// =======================================================================
	// USER JENJANG DETECTION
	// =======================================================================

	private function _detect_user_jenjang()
	{
		$group = get_user_first_group();
		if ($group) {
			$group_name = strtolower($group->name);
			if ($group_name == 'guru_sd') {
				$this->_user_jenjang_lock = array('SD');
				$this->_user_lock_label = 'SD';
			} elseif ($group_name == 'guru_smp') {
				$this->_user_jenjang_lock = array('SMP');
				$this->_user_lock_label = 'SMP';
			} elseif ($group_name == 'guru_sma') {
				$this->_user_jenjang_lock = array('SMA', 'FT');
				$this->_user_lock_label = 'SMA & FT';
			} elseif ($group_name == 'guru_ft') {
				$this->_user_jenjang_lock = array('FT');
				$this->_user_lock_label = 'FT';
			}
		}
	}

	private function _is_admin()
	{
		return ($this->_user_jenjang_lock === null);
	}

	private function _is_jenjang_allowed($jenjang)
	{
		if ($this->_user_jenjang_lock === null) return true;
		return in_array(strtoupper($jenjang), $this->_user_jenjang_lock);
	}

	private function _get_default_method()
	{
		if ($this->_user_jenjang_lock === null) return 'index';
		$first = $this->_user_jenjang_lock[0];
		return strtolower($first);
	}

	private function _validate_jenjang($jenjang)
	{
		if ($this->locked_jenjangs === null) return true;
		return in_array(strtoupper($jenjang), $this->locked_jenjangs);
	}

	// =======================================================================
	// TABS / ROUTE METHODS
	// =======================================================================

	public function index($offset = 0)
	{
		if ($this->_user_jenjang_lock !== null) {
			$method = $this->_get_default_method();
			redirect('administrator/presensi_pramuka/' . $method);
			return;
		}

		$this->locked_jenjangs = null;
		$this->lock_label = '';
		$this->url_method = 'index';
		$this->_render_list($offset);
	}

	public function sd($offset = 0)
	{
		$this->is_allowed('presensi_pramuka_list');
		if (!$this->_is_jenjang_allowed('SD')) {
			set_message('Anda tidak memiliki akses ke halaman Presensi Pramuka SD.', 'error');
			redirect('administrator/presensi_pramuka/' . $this->_get_default_method());
			return;
		}
		$this->locked_jenjangs = array('SD');
		$this->lock_label = 'SD';
		$this->url_method = 'sd';
		$this->_render_list($offset);
	}

	public function smp($offset = 0)
	{
		$this->is_allowed('presensi_pramuka_list');
		if (!$this->_is_jenjang_allowed('SMP')) {
			set_message('Anda tidak memiliki akses ke halaman Presensi Pramuka SMP.', 'error');
			redirect('administrator/presensi_pramuka/' . $this->_get_default_method());
			return;
		}
		$this->locked_jenjangs = array('SMP');
		$this->lock_label = 'SMP';
		$this->url_method = 'smp';
		$this->_render_list($offset);
	}

	public function sma($offset = 0)
	{
		$this->is_allowed('presensi_pramuka_list');
		if (!$this->_is_jenjang_allowed('SMA')) {
			set_message('Anda tidak memiliki akses ke halaman Presensi Pramuka SMA.', 'error');
			redirect('administrator/presensi_pramuka/' . $this->_get_default_method());
			return;
		}
		$this->locked_jenjangs = array('SMA', 'FT');
		$this->lock_label = 'SMA & FT';
		$this->url_method = 'sma';
		$this->_render_list($offset);
	}

	public function ft($offset = 0)
	{
		$this->is_allowed('presensi_pramuka_list');
		if (!$this->_is_jenjang_allowed('FT')) {
			set_message('Anda tidak memiliki akses ke halaman Presensi Pramuka FT.', 'error');
			redirect('administrator/presensi_pramuka/' . $this->_get_default_method());
			return;
		}
		$this->locked_jenjangs = array('FT');
		$this->lock_label = 'FT';
		$this->url_method = 'ft';
		$this->_render_list($offset);
	}

	// =======================================================================
	// LIST RENDERER
	// =======================================================================

	private function _render_list($offset = 0)
	{
		$this->is_allowed('presensi_pramuka_list');

		$filters = $this->_get_active_filters();

		$this->data['presensi_pramukas'] = $this->model_presensi_pramuka->get_with_filter($filters, $this->limit_page, $offset);
		$this->data['presensi_pramuka_counts'] = $this->model_presensi_pramuka->count_with_filter($filters);
		$this->data['summary'] = $this->model_presensi_pramuka->get_summary($filters);
		$this->data['chart_data'] = $this->model_presensi_pramuka->get_weekly_chart($filters, 8);

		$this->data['locked_jenjangs'] = $this->locked_jenjangs;
		$this->data['lock_label'] = $this->lock_label;
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = $this->locked_jenjangs !== null;
		$this->data['list_jenjang'] = $this->_get_jenjang_list();

		$this->data['offset'] = $offset;
		$this->data['limit'] = $this->limit_page;

		$this->data['active_filters'] = $filters;

		$config = array(
			'base_url'     => 'administrator/presensi_pramuka/' . $this->url_method . '/',
			'total_rows'   => $this->data['presensi_pramuka_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);
		$this->data['pagination'] = $this->pagination($config);

		$title = 'Presensi Pramuka' . ($this->lock_label ? ' - ' . $this->lock_label : '');
		$this->template->title($title);
		$this->render('backend/standart/administrator/presensi_pramuka/presensi_pramuka_list', $this->data);
	}

	// =======================================================================
	// AJAX ENDPOINTS
	// =======================================================================

	public function get_tingkatan()
	{
		$jenjang = strtolower($this->input->get('jenjang'));
		if (empty($jenjang) && $this->locked_jenjangs !== null) {
			$jenjang = strtolower($this->locked_jenjangs[0]);
		}

		$tbl = 'tingkatan_' . $jenjang;
		$id_col = 'id_tingkatan_' . $jenjang;

		if (!$this->db->table_exists($tbl)) {
			echo json_encode(array('status' => 'error', 'data' => array()));
			return;
		}

		$rows = $this->mymodel->withquery(
			"SELECT {$id_col} AS id, label FROM {$tbl} ORDER BY label ASC",
			'result'
		);

		echo json_encode(array('status' => 'success', 'data' => $rows));
	}

	public function get_kelas()
	{
		$jenjang = strtolower($this->input->get('jenjang'));
		$id_tingkatan = (int) $this->input->get('id_tingkatan');

		if (empty($jenjang) && $this->locked_jenjangs !== null) {
			$jenjang = strtolower($this->locked_jenjangs[0]);
		}

		$tbl = get_kelas_table_name($jenjang);
		$id_col = get_kelas_id_column($jenjang);

		if (!$tbl) {
			echo json_encode(array('status' => 'error', 'data' => array()));
			return;
		}

		$where = "WHERE deleted_at IS NULL";
		if ($id_tingkatan > 0) {
			$where .= " AND id_tingkatan = " . $id_tingkatan;
		}

		$rows = $this->mymodel->withquery(
			"SELECT {$id_col} AS id_kelas, label FROM {$tbl} {$where} ORDER BY label ASC",
			'result'
		);

		echo json_encode(array('status' => 'success', 'data' => $rows));
	}

	public function get_siswa()
	{
		$jenjang = strtolower($this->input->get('jenjang'));
		$id_kelas = (int) $this->input->get('id_kelas');
		$kelas_label = $this->input->get('kelas');

		if (empty($jenjang) && $this->locked_jenjangs !== null) {
			$jenjang = strtolower($this->locked_jenjangs[0]);
		}

		$tbl_s = get_siswa_table_name($jenjang);
		$id_col_s = get_siswa_id_column($jenjang);
		$tbl_k = get_kelas_table_name($jenjang);
		$id_col_k = get_kelas_id_column($jenjang);

		if (!$tbl_s) {
			echo json_encode(array('status' => 'error', 'data' => array()));
			return;
		}

		$where = "WHERE s.deleted_at IS NULL";
		if ($id_kelas > 0) {
			$where .= " AND s.id_kelas = " . $id_kelas;
		} elseif (!empty($kelas_label) && $tbl_k) {
			$where .= " AND s.id_kelas IN (SELECT {$id_col_k} FROM {$tbl_k} WHERE label = '" . $this->db->escape_str($kelas_label) . "')";
		}

		$rows = $this->mymodel->withquery(
			"SELECT s.{$id_col_s} AS id_siswa_aktif, s.nama_lengkap, s.nis,
			        k.label AS nama_kelas
			 FROM {$tbl_s} s
			 LEFT JOIN {$tbl_k} k ON k.{$id_col_k} = s.id_kelas
			 {$where}
			 ORDER BY s.nama_lengkap ASC",
			'result'
		);

		echo json_encode(array('status' => 'success', 'data' => $rows));
	}

	public function get_list_ajax()
	{
		if (!$this->is_allowed('presensi_pramuka_list', false)) {
			echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
			return;
		}

		$offset = (int) $this->input->get('offset');
		$filters = $this->_get_active_filters();

		$presensi_pramukas = $this->model_presensi_pramuka->get_with_filter($filters, $this->limit_page, $offset);
		$presensi_pramuka_counts = $this->model_pramuka_count($filters);
		$summary = $this->model_presensi_pramuka->get_summary($filters);
		$chart_data = $this->model_presensi_pramuka->get_weekly_chart($filters, 8);

		$nis_map = $this->_build_nis_map($presensi_pramukas);

		$rows_html = '';
		$url_method = !empty($this->locked_jenjangs) ? strtolower($this->locked_jenjangs[0]) : 'index';

		if ($presensi_pramuka_counts > 0) {
			foreach ($presensi_pramukas as $pt) {
				$total = (float) $pt->total_nilai;
				$pred_info = get_predikat_pramuka($total);
				$pred = $pred_info['predikat'];
				$desk = $pred_info['deskripsi'];

				$badge_color = $pred == 'A' ? 'badge-success' : ($pred == 'B' ? 'badge-primary' : ($pred == 'C' ? 'badge-info' : ($pred == 'D' ? 'badge-warning' : 'badge-danger')));

				$status_badge = '';
				switch (strtolower($pt->status_hadir)) {
					case 'hadir':      $status_badge = '<span class="label label-success">Hadir</span>'; break;
					case 'terlambat':  $status_badge = '<span class="label label-warning">Terlambat</span>'; break;
					case 'izin':       $status_badge = '<span class="label label-info">Izin</span>'; break;
					case 'sakit':      $status_badge = '<span class="label label-primary">Sakit</span>'; break;
					case 'alfa':       $status_badge = '<span class="label label-danger">Alfa</span>'; break;
					default:           $status_badge = '<span class="label label-default">'._ent($pt->status_hadir).'</span>'; break;
				}

				$nis = isset($nis_map[strtolower($pt->jenjang)][(int) $pt->id_siswa_aktif])
					? $nis_map[strtolower($pt->jenjang)][(int) $pt->id_siswa_aktif]
					: '-';

				$rows_html .= '<tr>';
				$rows_html .= '<td><input type="checkbox" class="flat-red check" name="id[]" value="'.intval($pt->id_presensi_pramuka).'"></td>';
				$rows_html .= '<td>'._ent($pt->tanggal).'<br><small class="text-muted">'._ent($pt->hari).'</small></td>';
				$rows_html .= '<td><strong>'._ent($pt->nama_lengkap).'</strong><br><small class="text-muted">NIS: '._ent($nis).'</small></td>';
				$rows_html .= '<td><span class="label label-default">'._ent($pt->jenjang).'</span> '._ent($pt->kelas).'</td>';
				$rows_html .= '<td>'.$status_badge.'</td>';
				$rows_html .= '<td><strong>'._ent($pt->kehadiran).'</strong></td>';
				$rows_html .= '<td>'._ent($pt->status_kelengkapan).'<br><small class="text-muted">('._ent($pt->kelengkapan).')</small></td>';
				$rows_html .= '<td>'._ent($pt->status_keaktifan).'<br><small class="text-muted">('._ent($pt->keaktifan).')</small></td>';
				$rows_html .= '<td><strong style="font-size:15px">'._ent($pt->total_nilai).'</strong><br><span class="badge '.$badge_color.'" title="'.$desk.'">'.$pred.'</span></td>';
				$rows_html .= '<td class="text-center">';
				$rows_html .= '<div style="margin-bottom:3px"><a href="javascript:void(0);" class="btn btn-sm btn-info btn-detail-modal" data-id="'.intval($pt->id_presensi_pramuka).'" title="Lihat detail" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center"><i class="fa fa-eye"></i> Detail</a></div>';
				$rows_html .= '<div style="margin-bottom:3px"><a href="'.site_url('administrator/presensi_pramuka/edit/'.$pt->id_presensi_pramuka).'" class="btn btn-sm btn-success" title="Edit" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center"><i class="fa fa-edit"></i> Edit</a>';
				$rows_html .= ' <a href="javascript:void(0);" data-href="'.site_url('administrator/presensi_pramuka/'.$url_method.'/delete/'.$pt->id_presensi_pramuka).'" class="btn btn-sm btn-danger remove-data" title="Hapus" style="margin:2px 1px;padding:4px 0;font-size:11px;border-radius:3px;width:48%;display:inline-block;text-align:center"><i class="fa fa-trash"></i> Hapus</a></div>';
				$rows_html .= '</td>';
				$rows_html .= '</tr>';
			}
		} else {
			$rows_html .= '<tr><td colspan="10" class="text-center text-muted" style="padding:30px">Tidak ada data presensi pramuka.</td></tr>';
		}

		$config = array(
			'base_url'     => 'administrator/presensi_pramuka/' . $this->url_method . '/',
			'total_rows'   => $presensi_pramuka_counts,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);
		$pagination = $this->pagination($config);

		echo json_encode(array(
			'status'     => 'success',
			'rows_html'  => $rows_html,
			'pagination' => $pagination,
			'total_data' => $presensi_pramuka_counts,
			'summary'    => $summary,
			'chart_data' => $chart_data,
		));
	}

	private function model_pramuka_count($filters)
	{
		return $this->model_presensi_pramuka->count_with_filter($filters);
	}

	public function get_detail($id)
	{
		$this->is_allowed('presensi_pramuka_view', false);

		$data = $this->model_presensi_pramuka->find($id);
		if (empty($data)) {
			echo json_encode(array('status' => 'error', 'message' => 'Data tidak ditemukan'));
			return;
		}

		$nis = '-';
		$tbl_s = get_siswa_table_name(strtolower($data->jenjang));
		$id_col_s = get_siswa_id_column(strtolower($data->jenjang));
		if ($tbl_s) {
			$s_row = $this->mymodel->withquery(
				"SELECT nis FROM {$tbl_s} WHERE {$id_col_s} = " . (int) $data->id_siswa_aktif . " LIMIT 1",
				'row'
			);
			if (!empty($s_row)) $nis = $s_row->nis;
		}

		$total = (float) $data->total_nilai;
		$pred_info = get_predikat_pramuka($total);
		$pred = $pred_info['predikat'];
		$desk = $pred_info['deskripsi'];

		echo json_encode(array(
			'status' => 'success',
			'data'   => array(
				'id_presensi_pramuka' => $data->id_presensi_pramuka,
				'tanggal'             => $data->tanggal,
				'hari'                => $data->hari,
				'jenjang'             => $data->jenjang,
				'kelas'               => $data->kelas,
				'nama_lengkap'        => $data->nama_lengkap,
				'nis'                 => $nis,
				'status_hadir'        => $data->status_hadir,
				'kehadiran'           => $data->kehadiran,
				'status_kelengkapan'  => $data->status_kelengkapan,
				'kelengkapan'         => $data->kelengkapan,
				'status_keaktifan'    => $data->status_keaktifan,
				'keaktifan'           => $data->keaktifan,
				'total_nilai'         => $data->total_nilai,
				'predikat'            => $pred,
				'deskripsi'           => $desk,
				'updated_by'          => $data->updated_by,
				'created_at'          => $data->created_at,
				'updated_at'          => $data->updated_at,
			)
		));
	}

	// =======================================================================
	// CRUD
	// =======================================================================

	public function add()
	{
		$this->is_allowed('presensi_pramuka_add');

		$seg3 = strtolower($this->uri->segment(3));
		if (in_array($seg3, array('sd', 'smp', 'sma', 'ft'))) {
			$this->$seg3(0);
			return;
		}

		$this->data['list_jenjang'] = $this->_get_jenjang_list();
		$this->data['locked_jenjangs'] = $this->locked_jenjangs;
		$this->data['lock_label'] = $this->lock_label;
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = $this->locked_jenjangs !== null;

		$this->template->title('Tambah Presensi Pramuka');
		$this->render('backend/standart/administrator/presensi_pramuka/presensi_pramuka_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('presensi_pramuka_add', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required');
		$this->form_validation->set_rules('status_hadir', 'Status Hadir', 'trim|required');

		if ($this->form_validation->run()) {

			$jenjang = strtolower($this->input->post('jenjang'));

			if (!$this->_validate_jenjang($jenjang)) {
				echo json_encode(array(
					'success' => false,
					'message' => 'Jenjang tidak diizinkan. Hanya bisa ' . implode(', ', $this->locked_jenjangs)
				));
				return;
			}

			$tanggal = $this->input->post('tanggal');
			$id_siswa_aktif = $this->input->post('id_siswa_aktif');
			$status_hadir = ucfirst(strtolower($this->input->post('status_hadir')));
			$kelas = $this->input->post('kelas');

			$siswa_tbl = get_siswa_table_name($jenjang);
			$siswa_id = get_siswa_id_column($jenjang);
			$kelas_tbl = get_kelas_table_name($jenjang);
			$kelas_id = get_kelas_id_column($jenjang);

			$siswa = $this->mymodel->withquery(
				"SELECT s.{$siswa_id} AS id_siswa_aktif, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				 FROM {$siswa_tbl} s
				 LEFT JOIN {$kelas_tbl} k ON k.{$kelas_id} = s.id_kelas
				 WHERE s.{$siswa_id} = " . (int) $id_siswa_aktif,
				'row'
			);

			if (empty($siswa)) {
				echo json_encode(array('success' => false, 'message' => 'Siswa tidak ditemukan'));
				return;
			}

			// Penilaian Kehadiran
			if ($this->input->post('kehadiran') !== null && is_numeric($this->input->post('kehadiran'))) {
				$kehadiran = max(0, min(100, (int) $this->input->post('kehadiran')));
			} else {
				switch ($status_hadir) {
					case 'Hadir': $kehadiran = 100; break;
					case 'Terlambat': $kehadiran = 75; break;
					case 'Izin': $kehadiran = 50; break;
					case 'Sakit': $kehadiran = 50; break;
					case 'Alfa':
					default: $kehadiran = 0; break;
				}
			}

			// Penilaian Kelengkapan Atribut
			$status_kelengkapan = $this->input->post('status_kelengkapan');
			if (empty($status_kelengkapan)) $status_kelengkapan = 'Lengkap';
			if ($this->input->post('kelengkapan') !== null && is_numeric($this->input->post('kelengkapan'))) {
				$kelengkapan = max(0, min(100, (int) $this->input->post('kelengkapan')));
			} else {
				switch (strtolower($status_kelengkapan)) {
					case 'lengkap': $kelengkapan = 100; break;
					case 'kurang lengkap': $kelengkapan = 90; break;
					case 'tidak menggunakan atribut': $kelengkapan = 80; break;
					default: $kelengkapan = 100; break;
				}
			}

			// Penilaian Keaktifan
			$status_keaktifan = $this->input->post('status_keaktifan');
			if (empty($status_keaktifan)) $status_keaktifan = 'Aktif';
			if ($this->input->post('keaktifan') !== null && is_numeric($this->input->post('keaktifan'))) {
				$keaktifan = max(0, min(100, (int) $this->input->post('keaktifan')));
			} else {
				switch (strtolower($status_keaktifan)) {
					case 'aktif': $keaktifan = 100; break;
					case 'kurang aktif': $keaktifan = 90; break;
					case 'tidak aktif': $keaktifan = 80; break;
					default: $keaktifan = 100; break;
				}
			}

			if (in_array($status_hadir, array('Sakit', 'Izin', 'Alfa'))) {
				$status_kelengkapan = '-';
				$kelengkapan = $kehadiran;
				$status_keaktifan = '-';
				$keaktifan = $kehadiran;
			}

			$total_nilai = hitung_total_nilai_pramuka($kehadiran, $kelengkapan, $keaktifan);
			$hari = get_nama_hari_indo($tanggal);

			$save_data = array(
				'hari'               => $hari,
				'tanggal'            => $tanggal,
				'jenjang'            => strtoupper($jenjang),
				'id_siswa_aktif'     => $id_siswa_aktif,
				'nama_lengkap'       => $siswa->nama_lengkap,
				'kelas'              => !empty($siswa->nama_kelas) ? $siswa->nama_kelas : $kelas,
				'status_hadir'       => $status_hadir,
				'kehadiran'          => $kehadiran,
				'status_kelengkapan' => !empty($status_kelengkapan) ? $status_kelengkapan : '-',
				'kelengkapan'        => $kelengkapan,
				'status_keaktifan'   => !empty($status_keaktifan) ? $status_keaktifan : '-',
				'keaktifan'          => $keaktifan,
				'total_nilai'        => $total_nilai,
				'updated_by'         => $this->session->userdata('username'),
				'updated_at'         => date('Y-m-d H:i:s'),
			);

			$existing = $this->mymodel->withquery(
				"SELECT id_presensi_pramuka FROM presensi_pramuka
				 WHERE id_siswa_aktif = " . (int) $id_siswa_aktif . "
				 AND tanggal = '" . $this->db->escape_str($tanggal) . "'
				 AND deleted_at IS NULL",
				'row'
			);

			if (!empty($existing)) {
				$this->model_presensi_pramuka->change($existing->id_presensi_pramuka, $save_data);
				$id = $existing->id_presensi_pramuka;
			} else {
				$save_data['created_at'] = date('Y-m-d H:i:s');
				$id = $this->model_presensi_pramuka->store($save_data);
			}

			if ($id) {
				$redirect_url = $this->locked_jenjangs !== null
					? base_url('administrator/presensi_pramuka/' . $this->url_method)
					: base_url('administrator/presensi_pramuka');

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_save_data_stay', array(
						anchor('administrator/presensi_pramuka/edit/' . $id, 'Edit'),
						anchor('administrator/presensi_pramuka', ' Go back to list')
					));
				} else {
					set_message(cclang('success_save_data_redirect', array(
						anchor('administrator/presensi_pramuka/edit/' . $id, 'Edit')
					)), 'success');
					$this->data['success'] = true;
					$this->data['redirect'] = $redirect_url;
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}

	public function edit($id)
	{
		$this->is_allowed('presensi_pramuka_update');

		$this->data['presensi_pramuka'] = $this->model_presensi_pramuka->find($id);

		if (empty($this->data['presensi_pramuka'])) {
			set_message('Data presensi pramuka tidak ditemukan.', 'error');
			redirect('administrator/presensi_pramuka');
			return;
		}

		$record_jenjang = strtoupper($this->data['presensi_pramuka']->jenjang);
		if (!$this->_is_jenjang_allowed($record_jenjang)) {
			set_message('Anda tidak memiliki akses ke data presensi jenjang ' . $record_jenjang . '.', 'error');
			redirect('administrator/presensi_pramuka/' . $this->_get_default_method());
			return;
		}

		$this->data['list_jenjang'] = $this->_get_jenjang_list();
		$this->data['locked_jenjangs'] = $this->locked_jenjangs;
		$this->data['lock_label'] = $this->lock_label;
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = $this->locked_jenjangs !== null;

		$current_jenjang = strtolower($this->data['presensi_pramuka']->jenjang);
		$tbl_k = get_kelas_table_name($current_jenjang);
		$id_col_k = get_kelas_id_column($current_jenjang);
		$this->data['kelas_list'] = array();
		if ($tbl_k) {
			$this->data['kelas_list'] = $this->mymodel->withquery(
				"SELECT {$id_col_k} AS id_kelas, label FROM {$tbl_k} WHERE deleted_at IS NULL ORDER BY label ASC",
				'result'
			);
		}

		$this->template->title('Edit Presensi Pramuka');
		$this->render('backend/standart/administrator/presensi_pramuka/presensi_pramuka_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_pramuka_update', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required');
		$this->form_validation->set_rules('status_hadir', 'Status Hadir', 'trim|required');

		if ($this->form_validation->run()) {

			$jenjang = strtolower($this->input->post('jenjang'));

			if ($this->locked_jenjangs !== null && !$this->_validate_jenjang($jenjang)) {
				echo json_encode(array(
					'success' => false,
					'message' => 'Jenjang tidak diizinkan. Hanya bisa ' . implode(', ', $this->locked_jenjangs)
				));
				return;
			}

			$tanggal = $this->input->post('tanggal');
			$id_siswa_aktif = $this->input->post('id_siswa_aktif');
			$status_hadir = ucfirst(strtolower($this->input->post('status_hadir')));
			$kelas = $this->input->post('kelas');

			$siswa_tbl = get_siswa_table_name($jenjang);
			$siswa_id = get_siswa_id_column($jenjang);
			$kelas_tbl = get_kelas_table_name($jenjang);
			$kelas_id = get_kelas_id_column($jenjang);

			$siswa = $this->mymodel->withquery(
				"SELECT s.{$siswa_id} AS id_siswa_aktif, s.nama_lengkap, s.nis, s.id_kelas, k.label AS nama_kelas
				 FROM {$siswa_tbl} s
				 LEFT JOIN {$kelas_tbl} k ON k.{$kelas_id} = s.id_kelas
				 WHERE s.{$siswa_id} = " . (int) $id_siswa_aktif,
				'row'
			);

			if (empty($siswa)) {
				echo json_encode(array('success' => false, 'message' => 'Siswa tidak ditemukan'));
				return;
			}

			// Penilaian Kehadiran
			if ($this->input->post('kehadiran') !== null && is_numeric($this->input->post('kehadiran'))) {
				$kehadiran = max(0, min(100, (int) $this->input->post('kehadiran')));
			} else {
				switch ($status_hadir) {
					case 'Hadir': $kehadiran = 100; break;
					case 'Terlambat': $kehadiran = 75; break;
					case 'Izin': $kehadiran = 50; break;
					case 'Sakit': $kehadiran = 50; break;
					case 'Alfa':
					default: $kehadiran = 0; break;
				}
			}

			// Penilaian Kelengkapan Atribut
			$status_kelengkapan = $this->input->post('status_kelengkapan');
			if (empty($status_kelengkapan)) $status_kelengkapan = 'Lengkap';
			if ($this->input->post('kelengkapan') !== null && is_numeric($this->input->post('kelengkapan'))) {
				$kelengkapan = max(0, min(100, (int) $this->input->post('kelengkapan')));
			} else {
				switch (strtolower($status_kelengkapan)) {
					case 'lengkap': $kelengkapan = 100; break;
					case 'kurang lengkap': $kelengkapan = 90; break;
					case 'tidak menggunakan atribut': $kelengkapan = 80; break;
					default: $kelengkapan = 100; break;
				}
			}

			// Penilaian Keaktifan
			$status_keaktifan = $this->input->post('status_keaktifan');
			if (empty($status_keaktifan)) $status_keaktifan = 'Aktif';
			if ($this->input->post('keaktifan') !== null && is_numeric($this->input->post('keaktifan'))) {
				$keaktifan = max(0, min(100, (int) $this->input->post('keaktifan')));
			} else {
				switch (strtolower($status_keaktifan)) {
					case 'aktif': $keaktifan = 100; break;
					case 'kurang aktif': $keaktifan = 90; break;
					case 'tidak aktif': $keaktifan = 80; break;
					default: $keaktifan = 100; break;
				}
			}

			if (in_array($status_hadir, array('Sakit', 'Izin', 'Alfa'))) {
				$status_kelengkapan = '-';
				$kelengkapan = $kehadiran;
				$status_keaktifan = '-';
				$keaktifan = $kehadiran;
			}

			$total_nilai = hitung_total_nilai_pramuka($kehadiran, $kelengkapan, $keaktifan);
			$hari = get_nama_hari_indo($tanggal);

			$save_data = array(
				'hari'               => $hari,
				'tanggal'            => $tanggal,
				'jenjang'            => strtoupper($jenjang),
				'id_siswa_aktif'     => $id_siswa_aktif,
				'nama_lengkap'       => $siswa->nama_lengkap,
				'kelas'              => !empty($siswa->nama_kelas) ? $siswa->nama_kelas : $kelas,
				'status_hadir'       => $status_hadir,
				'kehadiran'          => $kehadiran,
				'status_kelengkapan' => !empty($status_kelengkapan) ? $status_kelengkapan : '-',
				'kelengkapan'        => $kelengkapan,
				'status_keaktifan'   => !empty($status_keaktifan) ? $status_keaktifan : '-',
				'keaktifan'          => $keaktifan,
				'total_nilai'        => $total_nilai,
				'updated_by'         => $this->session->userdata('username'),
				'updated_at'         => date('Y-m-d H:i:s'),
			);

			$save = $this->model_presensi_pramuka->change($id, $save_data);

			if ($save) {
				$redirect_url = $this->locked_jenjangs !== null
					? base_url('administrator/presensi_pramuka/' . $this->url_method)
					: base_url('administrator/presensi_pramuka');

				if ($this->input->post('save_type') == 'stay') {
					echo json_encode(array(
						'success' => true,
						'id'      => $id,
						'message' => cclang('success_update_data_stay', array(
							anchor('administrator/presensi_pramuka', ' Go back to list')
						))
					));
				} else {
					set_message(cclang('success_update_data_redirect'), 'success');
					echo json_encode(array(
						'success'  => true,
						'redirect' => $redirect_url
					));
				}
			} else {
				echo json_encode(array('success' => false, 'message' => cclang('data_not_change')));
			}
		} else {
			echo json_encode(array('success' => false, 'message' => validation_errors()));
		}
	}

	public function delete($id = null)
	{
		$this->is_allowed('presensi_pramuka_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (is_array($arr_id)) {
			foreach ($arr_id as $i) {
				$remove = $this->_remove($i);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'presensi_pramuka'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_pramuka'), 'error');
        }

		redirect_back();
	}

	public function view($id)
	{
		$this->is_allowed('presensi_pramuka_view');

		$this->data['presensi_pramuka'] = $this->model_presensi_pramuka->find($id);

		if (empty($this->data['presensi_pramuka'])) {
			set_message('Data presensi pramuka tidak ditemukan.', 'error');
			redirect('administrator/presensi_pramuka');
			return;
		}

		$record_jenjang = strtoupper($this->data['presensi_pramuka']->jenjang);
		if (!$this->_is_jenjang_allowed($record_jenjang)) {
			set_message('Anda tidak memiliki akses ke data presensi jenjang ' . $record_jenjang . '.', 'error');
			redirect('administrator/presensi_pramuka/' . $this->_get_default_method());
			return;
		}

		$total = (float) $this->data['presensi_pramuka']->total_nilai;
		$pred_info = get_predikat_pramuka($total);
		$this->data['predikat'] = $pred_info['predikat'];
		$this->data['deskripsi'] = $pred_info['deskripsi'];

		$this->template->title('Detail Presensi Pramuka');
		$this->render('backend/standart/administrator/presensi_pramuka/presensi_pramuka_view', $this->data);
	}

	private function _remove($id)
	{
		if ($this->locked_jenjangs !== null) {
			$record = $this->model_presensi_pramuka->find($id);
			if (!$record || !in_array(strtoupper($record->jenjang), $this->locked_jenjangs)) {
				return false;
			}
		}

		return $this->model_presensi_pramuka->change($id, array('deleted_at' => date('Y-m-d H:i:s')));
	}

	// =======================================================================
	// EXPORT EXCEL & PDF
	// =======================================================================

	public function export()
	{
		$this->is_allowed('presensi_pramuka_export');

		$seg3 = strtolower($this->uri->segment(3));
		if (in_array($seg3, array('sd', 'smp', 'sma', 'ft'))) {
			$this->locked_jenjangs = array(strtoupper($seg3));
		}

		$filters = $this->_get_active_filters();
		$filename = $this->model_presensi_pramuka->export_pramuka($filters);

		$file_path = FCPATH . 'uploads/' . $filename;
		if (file_exists($file_path)) {
			force_download($filename, file_get_contents($file_path));
		} else {
			set_message('Gagal membuat file export', 'error');
			redirect_back();
		}
	}

	public function export_pdf()
	{
		$this->is_allowed('presensi_pramuka_export');

		$seg3 = strtolower($this->uri->segment(3));
		if (in_array($seg3, array('sd', 'smp', 'sma', 'ft'))) {
			$this->locked_jenjangs = array(strtoupper($seg3));
		}

		$filters = $this->_get_active_filters();
		$this->data['presensi_pramukas'] = $this->model_presensi_pramuka->get_with_filter($filters, 0, 0);
		$this->data['filters'] = $filters;
		$this->data['nis_map'] = $this->_build_nis_map($this->data['presensi_pramukas']);

		$html = $this->load->view('backend/standart/administrator/presensi_pramuka/presensi_pramuka_pdf', $this->data, true);
		$this->load->library('HtmlPdf');
		$config = array(
			'orientation' => 'L',
			'format' => 'A4',
			'marges' => array(10, 10, 10, 10)
		);
		$this->pdf = new HTML2PDF($config['orientation'], $config['format'], 'en', true, 'UTF-8', $config['marges']);
		$this->pdf->writeHTML($html);
		$this->pdf->Output('Laporan_Presensi_Pramuka_' . date('Y-m-d') . '.pdf');
	}

	public function single_pdf($id)
	{
		$this->is_allowed('presensi_pramuka_export');

		$data = $this->model_presensi_pramuka->find($id);
		if (empty($data)) {
			show_404();
			return;
		}

		$nis = '-';
		$tbl_s = get_siswa_table_name(strtolower($data->jenjang));
		$id_col_s = get_siswa_id_column(strtolower($data->jenjang));
		if ($tbl_s) {
			$s_row = $this->mymodel->withquery(
				"SELECT nis FROM {$tbl_s} WHERE {$id_col_s} = " . (int) $data->id_siswa_aktif . " LIMIT 1",
				'row'
			);
			if (!empty($s_row)) $nis = $s_row->nis;
		}

		$total = (float) $data->total_nilai;
		$pred_info = get_predikat_pramuka($total);

		$this->data['presensi_pramuka'] = $data;
		$this->data['nis'] = $nis;
		$this->data['predikat'] = $pred_info['predikat'];
		$this->data['deskripsi'] = $pred_info['deskripsi'];

		$html = $this->load->view('backend/standart/administrator/presensi_pramuka/presensi_pramuka_single_pdf', $this->data, true);
		$this->load->library('HtmlPdf');
		$config = array(
			'orientation' => 'P',
			'format' => 'A4',
			'marges' => array(15, 15, 15, 15)
		);
		$this->pdf = new HTML2PDF($config['orientation'], $config['format'], 'en', true, 'UTF-8', $config['marges']);
		$this->pdf->writeHTML($html);
		$this->pdf->Output('Detail_Presensi_Pramuka_' . $data->nama_lengkap . '_' . $data->tanggal . '.pdf');
	}

	// =======================================================================
	// PRIVATE HELPERS
	// =======================================================================

	private function _get_active_filters()
	{
		$filters = array();

		if ($this->locked_jenjangs !== null) {
			$filters['jenjang'] = $this->locked_jenjangs[0];
		} elseif ($this->input->get('jenjang')) {
			$filters['jenjang'] = $this->input->get('jenjang');
		}

		if ($this->input->get('id_tingkatan')) {
			$filters['id_tingkatan'] = (int) $this->input->get('id_tingkatan');
		}
		if ($this->input->get('kelas')) {
			$filters['kelas'] = $this->input->get('kelas');
		}
		if ($this->input->get('status_hadir')) {
			$filters['status_hadir'] = $this->input->get('status_hadir');
		}
		if ($this->input->get('start_date')) {
			$filters['start_date'] = $this->input->get('start_date');
		}
		if ($this->input->get('end_date')) {
			$filters['end_date'] = $this->input->get('end_date');
		}
		if ($this->input->get('q')) {
			$filters['q'] = $this->input->get('q');
		}

		return $filters;
	}

	private function _get_jenjang_list()
	{
		$allowed = $this->_user_jenjang_lock !== null
			? $this->_user_jenjang_lock
			: array('SD', 'SMP', 'SMA', 'FT');

		$all = array(
			array('code' => 'SD', 'label' => 'SD'),
			array('code' => 'SMP', 'label' => 'SMP'),
			array('code' => 'SMA', 'label' => 'SMA'),
			array('code' => 'FT', 'label' => 'FT'),
		);
		$result = array();
		foreach ($all as $j) {
			if (in_array($j['code'], $allowed)) {
				$result[] = $j;
			}
		}
		return $result;
	}

	private function _build_nis_map($presensi_list)
	{
		$nis_map = array();
		if (empty($presensi_list)) return $nis_map;

		$ids_by_jenjang = array();
		foreach ($presensi_list as $p) {
			$j = strtolower($p->jenjang);
			if (!isset($ids_by_jenjang[$j])) $ids_by_jenjang[$j] = array();
			$ids_by_jenjang[$j][] = (int) $p->id_siswa_aktif;
		}

		foreach ($ids_by_jenjang as $j => $ids) {
			$tbl_s  = get_siswa_table_name($j);
			$id_col = get_siswa_id_column($j);
			if (!$tbl_s || !$id_col) continue;

			$in = implode(',', array_unique($ids));
			$rows = $this->mymodel->withquery(
				"SELECT {$id_col} AS id, nis FROM {$tbl_s} WHERE {$id_col} IN ({$in})",
				'result'
			);
			if (!empty($rows)) {
				$nis_map[$j] = array();
				foreach ($rows as $r) {
					$nis_map[$j][(int) $r->id] = $r->nis;
				}
			}
		}

		return $nis_map;
	}
}

/* End of file Presensi_pramuka.php */
/* Location: ./modules/presensi_pramuka/controllers/backend/Presensi_pramuka.php */
