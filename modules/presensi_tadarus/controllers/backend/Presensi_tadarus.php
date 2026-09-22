<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Tadarus Controller
*| --------------------------------------------------------------------------
*| Presensi Tadarus site — Admin panel
*|
*/
class Presensi_tadarus extends Admin
{

    /**
     * Locked jenjangs (null = no lock / all)
     * Set by sd()/smp()/sma()/ft() methods.
     */
    private $locked_jenjangs = null;

    /**
     * Lock label for display (e.g. "SMA & FT")
     */
    private $lock_label = '';

    /**
     * URL method name for pagination base_url
     */
    private $url_method = 'index';

    /**
     * User-based jenjang lock (from user group).
     * Null = admin (no lock), array = guru_* (locked).
     */
    private $_user_jenjang_lock = null;

    /**
     * User lock label (e.g. "SMA & FT" for guru_sma)
     */
    private $_user_lock_label = '';

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_tadarus');
		$this->load->helper('guru_token');
		$this->lang->load('web_lang', $this->current_lang);

		// Auto-detect jenjang lock from user group
		$this->_detect_user_jenjang();
	}

	// =======================================================================
	// USER JENJANG DETECTION
	// =======================================================================

	/**
	 * Detect user group and set jenjang lock accordingly.
	 * guru_sd → SD, guru_smp → SMP, guru_sma → SMA+FT, guru_ft → FT
	 * admin/default → no lock
	 */
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

	/**
	 * Check if current user is admin (no jenjang restriction).
	 */
	private function _is_admin()
	{
		return ($this->_user_jenjang_lock === null);
	}

	/**
	 * Check if jenjang is in user's allowed scope.
	 */
	private function _is_jenjang_allowed($jenjang)
	{
		if ($this->_user_jenjang_lock === null) return true;
		return in_array(strtoupper($jenjang), $this->_user_jenjang_lock);
	}

	/**
	 * Get default URL method based on user's jenjang lock.
	 */
	private function _get_default_method()
	{
		if ($this->_user_jenjang_lock === null) return 'index';
		$first = $this->_user_jenjang_lock[0];
		return strtolower($first);
	}

	/**
	 * Redirect non-admin user to their allowed jenjang URL.
	 */
	private function _redirect_to_allowed()
	{
		$method = $this->_get_default_method();
		redirect('administrator/presensi_tadarus/' . $method);
	}

	// =======================================================================
	// ENTRY POINTS (with optional jenjang lock)
	// =======================================================================

	/**
	* Show all Presensi Tadarus (no lock) — ADMIN ONLY
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_tadarus_list');

		// Non-admin users must use jenjang-locked URL
		if (!$this->_is_admin()) {
			$default_method = $this->_get_default_method();
			redirect('administrator/presensi_tadarus/' . $default_method);
			return;
		}

		$this->locked_jenjangs = null;
		$this->lock_label = '';
		$this->url_method = 'index';
		$this->_render_list($offset);
	}

	/**
	* Locked to SD
	*/
	public function sd($offset = 0)
	{
		$this->is_allowed('presensi_tadarus_list');

		// Non-admin: verify scope
		if (!$this->_is_admin() && !$this->_is_jenjang_allowed('SD')) {
			$this->_redirect_to_allowed();
			return;
		}

		$this->locked_jenjangs = array('SD');
		$this->lock_label = 'SD';
		$this->url_method = 'sd';
		$this->_render_list($offset);
	}

	/**
	* Locked to SMP
	*/
	public function smp($offset = 0)
	{
		$this->is_allowed('presensi_tadarus_list');

		// Non-admin: verify scope
		if (!$this->_is_admin() && !$this->_is_jenjang_allowed('SMP')) {
			$this->_redirect_to_allowed();
			return;
		}

		$this->locked_jenjangs = array('SMP');
		$this->lock_label = 'SMP';
		$this->url_method = 'smp';
		$this->_render_list($offset);
	}

	/**
	* Locked to SMA + FT (guru_sma boleh akses FT)
	*/
	public function sma($offset = 0)
	{
		$this->is_allowed('presensi_tadarus_list');

		// Non-admin: verify scope
		if (!$this->_is_admin() && !$this->_is_jenjang_allowed('SMA')) {
			$this->_redirect_to_allowed();
			return;
		}

		$this->locked_jenjangs = array('SMA', 'FT');
		$this->lock_label = 'SMA & FT';
		$this->url_method = 'sma';
		$this->_render_list($offset);
	}

	/**
	* Locked to FT
	*/
	public function ft($offset = 0)
	{
		$this->is_allowed('presensi_tadarus_list');

		// Non-admin: verify scope
		if (!$this->_is_admin() && !$this->_is_jenjang_allowed('FT')) {
			$this->_redirect_to_allowed();
			return;
		}

		$this->locked_jenjangs = array('FT');
		$this->lock_label = 'FT';
		$this->url_method = 'ft';
		$this->_render_list($offset);
	}

	// =======================================================================
	// PRIVATE: Render list (shared by all entry points)
	// =======================================================================
	private function _render_list($offset)
	{
		$filters = $this->_build_filters();

		$this->data['presensi_tadaruss'] = $this->model_presensi_tadarus->get_with_filter($filters, $this->limit_page, $offset);
		$this->data['presensi_tadarus_counts'] = $this->model_presensi_tadarus->count_with_filter($filters);
		$this->data['summary'] = $this->model_presensi_tadarus->get_summary($filters);
		$this->data['chart_data'] = $this->model_presensi_tadarus->get_weekly_chart($filters, 8);

		$this->data['list_jenjang'] = $this->_get_jenjang_list();
		$this->data['list_tingkatan'] = $this->_get_tingkatan_list();
		$this->data['list_kelas'] = $this->_get_kelas_list();
		$this->data['filters'] = $filters;

		$this->data['locked_jenjangs'] = $this->locked_jenjangs;
		$this->data['lock_label'] = $this->lock_label;
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = $this->locked_jenjangs !== null;
		$this->data['is_admin'] = $this->_is_admin();

		$config = array(
			'base_url'     => 'administrator/presensi_tadarus/' . $this->url_method . '/',
			'total_rows'   => $this->data['presensi_tadarus_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
			'reuse_query_string' => true,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Tadarus' . ($this->lock_label ? ' - ' . $this->lock_label : ''));
		$this->render('backend/standart/administrator/presensi_tadarus/presensi_tadarus_list', $this->data);
	}

	/**
	* Build filters from GET params
	*/
	private function _build_filters()
	{
		$filters = array();

		$jenjang = $this->input->get('jenjang');
		$tingkatan = $this->input->get('tingkatan');
		$kelas = $this->input->get('kelas');
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$status_hadir = $this->input->get('status_hadir');
		$q = $this->input->get('q');

		// Enforce user-based lock for non-admin (always, regardless of URL)
		if ($this->_user_jenjang_lock !== null) {
			$filters['jenjang_lock'] = $this->_user_jenjang_lock;
			if (count($this->_user_jenjang_lock) == 1) {
				$filters['jenjang'] = $this->_user_jenjang_lock[0];
			} elseif (!empty($jenjang) && in_array(strtoupper($jenjang), $this->_user_jenjang_lock)) {
				$filters['jenjang'] = $jenjang;
			}
		} elseif ($this->locked_jenjangs !== null) {
			// Admin with URL lock
			$filters['jenjang_lock'] = $this->locked_jenjangs;
			if (count($this->locked_jenjangs) == 1) {
				$filters['jenjang'] = $this->locked_jenjangs[0];
			} elseif (!empty($jenjang) && in_array(strtoupper($jenjang), $this->locked_jenjangs)) {
				$filters['jenjang'] = $jenjang;
			}
		} else {
			// Admin, no lock
			if (!empty($jenjang)) $filters['jenjang'] = $jenjang;
		}

		if (!empty($tingkatan)) $filters['id_tingkatan'] = $tingkatan;
		if (!empty($kelas)) $filters['kelas'] = $kelas;
		if (!empty($start_date)) $filters['start_date'] = $start_date;
		if (!empty($end_date)) $filters['end_date'] = $end_date;
		if (!empty($status_hadir)) $filters['status_hadir'] = $status_hadir;
		if (!empty($q)) $filters['q'] = $q;

		return $filters;
	}

	/**
	 * Check if filters have at least one meaningful value.
	 * Used to validate export requests.
	 */
	private function _has_valid_filters($filters)
	{
		$meaningful_keys = array('jenjang', 'jenjang_lock', 'id_tingkatan', 'kelas', 'start_date', 'end_date', 'status_hadir', 'q');
		foreach ($meaningful_keys as $key) {
			if (!empty($filters[$key])) return true;
		}
		return false;
	}

	/**
	* Get jenjang list (filtered by lock if any)
	*/
	private function _get_jenjang_list()
	{
		$all = array(
			array('code' => 'SD', 'label' => 'SD'),
			array('code' => 'SMP', 'label' => 'SMP'),
			array('code' => 'SMA', 'label' => 'SMA'),
			array('code' => 'FT', 'label' => 'FT'),
		);
		if ($this->locked_jenjangs === null) {
			return $all;
		}
		$result = array();
		foreach ($all as $j) {
			if (in_array($j['code'], $this->locked_jenjangs)) {
				$result[] = $j;
			}
		}
		return $result;
	}

	/**
	* Get tingkatan list (filtered by lock if any)
	*/
	private function _get_tingkatan_list()
	{
		$result = array();
		$jenjangs = $this->_get_jenjangs_to_query();

		foreach ($jenjangs as $j) {
			$tbl = 'tingkatan_' . $j;
			$id_col = 'id_tingkatan_' . $j;
			$data = $this->mymodel->withquery("SELECT {$id_col} AS id, label, '{$j}' AS jenjang FROM {$tbl} ORDER BY label ASC", 'result');
			if (!empty($data)) {
				$result = array_merge($result, $data);
			}
		}

		return $result;
	}

	/**
	* Get kelas list (filtered by lock if any)
	*/
	private function _get_kelas_list()
	{
		$result = array();
		$jenjangs = $this->_get_jenjangs_to_query();

		foreach ($jenjangs as $j) {
			$kelas_tbl = 'kelas_' . $j;
			$kelas_id = 'id_kelas_' . $j;
			$tingkatan_id = 'id_tingkatan_' . $j;
			$data = $this->mymodel->withquery(
				"SELECT k.{$kelas_id} AS id_kelas, k.label, k.id_tingkatan, t.label AS tingkatan_label, '{$j}' AS jenjang
				 FROM {$kelas_tbl} k
				 LEFT JOIN tingkatan_{$j} t ON t.{$tingkatan_id} = k.id_tingkatan
				 WHERE k.is_active = 1
				 ORDER BY k.label ASC",
				'result'
			);
			if (!empty($data)) {
				$result = array_merge($result, $data);
			}
		}

		return $result;
	}

	/**
	* Get list of jenjang to query (lowercase), respecting lock
	*/
	private function _get_jenjangs_to_query()
	{
		if ($this->locked_jenjangs === null) {
			return array('sd', 'smp', 'sma', 'ft');
		}
		$out = array();
		foreach ($this->locked_jenjangs as $j) {
			$out[] = strtolower($j);
		}
		return $out;
	}

	/**
	* Validate jenjang is allowed (for add/edit)
	*/
	private function _validate_jenjang($jenjang)
	{
		$jenjang = strtoupper($jenjang);
		if ($this->locked_jenjangs === null) {
			return true;
		}
		return in_array($jenjang, $this->locked_jenjangs);
	}

	// =======================================================================
	// AJAX: Get Tingkatan by Jenjang (respect lock)
	// =======================================================================
	public function get_tingkatan()
	{
		$jenjang = strtolower($this->input->get('jenjang'));
		if (empty($jenjang)) {
			echo json_encode(array());
			return;
		}

		if ($this->locked_jenjangs !== null && !in_array(strtoupper($jenjang), $this->locked_jenjangs)) {
			echo json_encode(array());
			return;
		}

		$tbl = 'tingkatan_' . $jenjang;
		$id_col = 'id_tingkatan_' . $jenjang;

		$data = $this->mymodel->withquery("SELECT {$id_col} AS id, label FROM {$tbl} ORDER BY label ASC", 'result');
		echo json_encode($data);
	}

	// =======================================================================
	// AJAX: Get Kelas by Tingkatan (respect lock)
	// =======================================================================
	public function get_kelas()
	{
		$jenjang = strtolower($this->input->get('jenjang'));
		$id_tingkatan = $this->input->get('id_tingkatan');

		if (empty($jenjang)) {
			echo json_encode(array());
			return;
		}

		if ($this->locked_jenjangs !== null && !in_array(strtoupper($jenjang), $this->locked_jenjangs)) {
			echo json_encode(array());
			return;
		}

		$kelas_tbl = 'kelas_' . $jenjang;
		$kelas_id = 'id_kelas_' . $jenjang;

		$where = ' WHERE is_active = 1';
		if (!empty($id_tingkatan)) {
			$where .= " AND id_tingkatan = " . (int) $id_tingkatan;
		}

		$data = $this->mymodel->withquery("SELECT {$kelas_id} AS id_kelas, label, nama_kelas FROM {$kelas_tbl}{$where} ORDER BY label ASC", 'result');

		// For SMA, also include FT classes
		if ($jenjang == 'sma' && ($this->locked_jenjangs === null || in_array('FT', $this->locked_jenjangs))) {
			$where_ft = ' WHERE is_active = 1';
			if (!empty($id_tingkatan)) {
				$where_ft .= " AND id_tingkatan = " . (int) $id_tingkatan;
			}
			$data_ft = $this->mymodel->withquery("SELECT id_kelas_ft AS id_kelas, label, nama_kelas FROM kelas_ft{$where_ft} ORDER BY label ASC", 'result');
			if (!empty($data_ft)) {
				foreach ($data_ft as $d) {
					$d->label = $d->label . ' (FT)';
					$data[] = $d;
				}
			}
		}

		echo json_encode($data);
	}

	// =======================================================================
	// AJAX: Get Detail Presensi (for modal)
	// =======================================================================
	public function get_detail($id)
	{
		$this->is_allowed('presensi_tadarus_view', false);

		$data = $this->model_presensi_tadarus->find($id);

		if (empty($data)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Data tidak ditemukan'
			));
			return;
		}

		// Check access based on jenjang lock
		$record_jenjang = strtoupper($data->jenjang);
		if ($this->locked_jenjangs !== null && !in_array($record_jenjang, $this->locked_jenjangs)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Akses ditolak'
			));
			return;
		}

		// Get NIS from siswa table
		$jenjang = strtolower($data->jenjang);
		$siswa_tbl = get_siswa_table_name($jenjang);
		$siswa_id = get_siswa_id_column($jenjang);

		$nis = '';
		if ($siswa_tbl && $siswa_id) {
			$siswa = $this->mymodel->withquery(
				"SELECT nis FROM {$siswa_tbl} WHERE {$siswa_id} = " . (int) $data->id_siswa_aktif,
				'row'
			);
			if (!empty($siswa)) {
				$nis = $siswa->nis;
			}
		}

		$data->nis = $nis;

		echo json_encode(array(
			'success' => true,
			'data' => $data
		));
	}

	// =======================================================================
	// AJAX: Get Siswa by Kelas (respect lock)
	// =======================================================================
	public function get_siswa()
	{
		$jenjang = strtolower($this->input->get('jenjang'));
		$id_kelas = $this->input->get('id_kelas');

		if (empty($jenjang)) {
			echo json_encode(array());
			return;
		}

		if ($this->locked_jenjangs !== null && !in_array(strtoupper($jenjang), $this->locked_jenjangs)) {
			echo json_encode(array());
			return;
		}

		$siswa_tbl = get_siswa_table_name($jenjang);
		$siswa_id = get_siswa_id_column($jenjang);
		$kelas_tbl = get_kelas_table_name($jenjang);
		$kelas_id = get_kelas_id_column($jenjang);

		if (!$siswa_tbl || !$kelas_tbl) {
			echo json_encode(array());
			return;
		}

		$where_kelas = '';
		if (!empty($id_kelas)) {
			$where_kelas = " AND s.id_kelas = " . (int) $id_kelas;
		}

		$sql = "SELECT
					s.{$siswa_id} AS id_siswa_aktif,
					s.nama_lengkap,
					s.nis,
					s.id_kelas,
					k.label AS nama_kelas
				FROM {$siswa_tbl} s
				LEFT JOIN {$kelas_tbl} k ON k.{$kelas_id} = s.id_kelas
				WHERE s.deleted_at IS NULL AND s.is_active = 1
				{$where_kelas}
				ORDER BY s.nama_lengkap ASC";

		$data = $this->mymodel->withquery($sql, 'result');
		echo json_encode($data);
	}

	// =======================================================================
	// ADD
	// =======================================================================
	public function add()
	{
		$this->is_allowed('presensi_tadarus_add');

		// Auto-detect lock from URL when accessed via /sma/add etc.
		$seg3 = strtolower($this->uri->segment(3));
		if (in_array($seg3, array('sd', 'smp', 'sma', 'ft'))) {
			$this->$seg3(0);
			return;
		}

		// Dropdown data
		$this->data['list_jenjang'] = $this->_get_jenjang_list();
		$this->data['locked_jenjangs'] = $this->locked_jenjangs;
		$this->data['lock_label'] = $this->lock_label;
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = $this->locked_jenjangs !== null;

		$this->template->title('Tambah Presensi Tadarus');
		$this->render('backend/standart/administrator/presensi_tadarus/presensi_tadarus_add', $this->data);
	}

	/**
	* Add New Presensi Tadarus
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_tadarus_add', false)) {
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
			$status_hadir = $this->input->post('status_hadir');
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

			$non_hadir = in_array(ucfirst(strtolower($status_hadir)), array('Sakit', 'Izin', 'Alfa', 'Tidak hadir'));

			if ($non_hadir) {
				$kehadiran = 0; $kelengkapan = 0; $adab = 0; $keaktifan = 0;
			} else {
				$kehadiran = max(1, min(4, (int) $this->input->post('kehadiran')));
				$kelengkapan = max(1, min(4, (int) $this->input->post('kelengkapan')));
				$adab = max(1, min(4, (int) $this->input->post('adab')));
				$keaktifan = max(1, min(4, (int) $this->input->post('keaktifan')));
			}

			$total_nilai = hitung_total_nilai_tadarus($kehadiran, $kelengkapan, $adab, $keaktifan);
			$hari = get_nama_hari_indo($tanggal);

			$save_data = array(
				'hari'           => $hari,
				'tanggal'        => $tanggal,
				'jenjang'        => strtoupper($jenjang),
				'id_siswa_aktif' => $id_siswa_aktif,
				'nama_lengkap'   => $siswa->nama_lengkap,
				'kelas'          => !empty($siswa->nama_kelas) ? $siswa->nama_kelas : $kelas,
				'status_hadir'   => ucfirst(strtolower($status_hadir)),
				'kehadiran'      => $kehadiran,
				'kelengkapan'    => $kelengkapan,
				'adab'           => $adab,
				'keaktifan'      => $keaktifan,
				'total_nilai'    => $total_nilai,
				'updated_by'     => $this->session->userdata('username'),
				'updated_at'     => date('Y-m-d H:i:s'),
			);

			$existing = $this->mymodel->withquery(
				"SELECT id_presensi_tadarus FROM presensi_tadarus
				 WHERE id_siswa_aktif = " . (int) $id_siswa_aktif . "
				 AND tanggal = '" . $this->db->escape_str($tanggal) . "'
				 AND deleted_at IS NULL",
				'row'
			);

			if (!empty($existing)) {
				$this->model_presensi_tadarus->change($existing->id_presensi_tadarus, $save_data);
				$id = $existing->id_presensi_tadarus;
			} else {
				$save_data['created_at'] = date('Y-m-d H:i:s');
				$id = $this->model_presensi_tadarus->store($save_data);
			}

			if ($id) {
				$redirect_url = $this->locked_jenjangs !== null
					? base_url('administrator/presensi_tadarus/' . $this->url_method)
					: base_url('administrator/presensi_tadarus');

				if ($this->input->post('save_type') == 'stay') {
					echo json_encode(array(
						'success' => true,
						'id'      => $id,
						'message' => cclang('success_save_data_stay', array(
							anchor('administrator/presensi_tadarus/edit/' . $id, 'Edit'),
							anchor('administrator/presensi_tadarus', ' Go back to list')
						))
					));
				} else {
					set_message(cclang('success_save_data_redirect', array(
						anchor('administrator/presensi_tadarus/edit/' . $id, 'Edit')
					)), 'success');
					echo json_encode(array(
						'success'  => true,
						'redirect' => $redirect_url
					));
				}
			} else {
				echo json_encode(array('success' => false, 'message' => cclang('data_not_change')));
			}

		} else {
			echo json_encode(array(
				'success' => false,
				'message' => 'Validasi gagal',
				'errors'  => $this->form_validation->error_array()
			));
		}
	}

	// =======================================================================
	// EDIT
	// =======================================================================
	public function edit($id)
	{
		$this->is_allowed('presensi_tadarus_update');

		$this->data['presensi_tadarus'] = $this->model_presensi_tadarus->find($id);

		if (empty($this->data['presensi_tadarus'])) {
			show_404();
			return;
		}

		$record_jenjang = strtoupper($this->data['presensi_tadarus']->jenjang);
		if ($this->locked_jenjangs !== null && !in_array($record_jenjang, $this->locked_jenjangs)) {
			show_error('Anda tidak memiliki akses ke data presensi tadarus untuk jenjang ' . $record_jenjang, 403);
			return;
		}

		// If no URL lock, restrict edit to record's jenjang
		$allowed_for_edit = $this->locked_jenjangs;
		if ($allowed_for_edit === null) {
			$allowed_for_edit = array($record_jenjang);
		}

		$this->data['list_jenjang'] = $this->_filter_jenjang_list($allowed_for_edit);
		$this->data['locked_jenjangs'] = $allowed_for_edit;
		$this->data['lock_label'] = implode(' & ', $allowed_for_edit);
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = true; // always lock when editing

		$current_jenjang = strtolower($this->data['presensi_tadarus']->jenjang);
		$this->data['list_tingkatan'] = $this->mymodel->withquery(
			"SELECT id_tingkatan_{$current_jenjang} AS id, label FROM tingkatan_{$current_jenjang} ORDER BY label ASC",
			'result'
		);

		$this->data['list_kelas'] = $this->mymodel->withquery(
			"SELECT id_kelas_{$current_jenjang} AS id_kelas, label FROM kelas_{$current_jenjang} ORDER BY label ASC",
			'result'
		);

		$this->template->title('Edit Presensi Tadarus');
		$this->render('backend/standart/administrator/presensi_tadarus/presensi_tadarus_update', $this->data);
	}

	private function _filter_jenjang_list($allowed)
	{
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

	/**
	* Update Presensi Tadarus
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_tadarus_update', false)) {
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
			$status_hadir = $this->input->post('status_hadir');
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

			$non_hadir = in_array(ucfirst(strtolower($status_hadir)), array('Sakit', 'Izin', 'Alfa', 'Tidak hadir'));

			if ($non_hadir) {
				$kehadiran = 0; $kelengkapan = 0; $adab = 0; $keaktifan = 0;
			} else {
				$kehadiran = max(1, min(4, (int) $this->input->post('kehadiran')));
				$kelengkapan = max(1, min(4, (int) $this->input->post('kelengkapan')));
				$adab = max(1, min(4, (int) $this->input->post('adab')));
				$keaktifan = max(1, min(4, (int) $this->input->post('keaktifan')));
			}

			$total_nilai = hitung_total_nilai_tadarus($kehadiran, $kelengkapan, $adab, $keaktifan);
			$hari = get_nama_hari_indo($tanggal);

			$save_data = array(
				'hari'           => $hari,
				'tanggal'        => $tanggal,
				'jenjang'        => strtoupper($jenjang),
				'id_siswa_aktif' => $id_siswa_aktif,
				'nama_lengkap'   => $siswa->nama_lengkap,
				'kelas'          => !empty($siswa->nama_kelas) ? $siswa->nama_kelas : $kelas,
				'status_hadir'   => ucfirst(strtolower($status_hadir)),
				'kehadiran'      => $kehadiran,
				'kelengkapan'    => $kelengkapan,
				'adab'           => $adab,
				'keaktifan'      => $keaktifan,
				'total_nilai'    => $total_nilai,
				'updated_by'     => $this->session->userdata('username'),
				'updated_at'     => date('Y-m-d H:i:s'),
			);

			$save = $this->model_presensi_tadarus->change($id, $save_data);

			if ($save) {
				$redirect_url = $this->locked_jenjangs !== null
					? base_url('administrator/presensi_tadarus/' . $this->url_method)
					: base_url('administrator/presensi_tadarus');

				if ($this->input->post('save_type') == 'stay') {
					echo json_encode(array(
						'success' => true,
						'id'      => $id,
						'message' => cclang('success_update_data_stay', array(
							anchor('administrator/presensi_tadarus', ' Go back to list')
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
			echo json_encode(array(
				'success' => false,
				'message' => 'Validasi gagal',
				'errors'  => $this->form_validation->error_array()
			));
		}
	}

	// =======================================================================
	// DELETE
	// =======================================================================
	public function delete($id = null)
	{
		$this->is_allowed('presensi_tadarus_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id, $this->locked_jenjangs);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id, $this->locked_jenjangs);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'presensi_tadarus'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_tadarus'), 'error');
        }

		redirect_back();
	}

	/**
	* View detail Presensi Tadarus
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_tadarus_view');

		$this->data['presensi_tadarus'] = $this->model_presensi_tadarus->find($id);

		if (empty($this->data['presensi_tadarus'])) {
			show_404();
			return;
		}

		$record_jenjang = strtoupper($this->data['presensi_tadarus']->jenjang);
		if ($this->locked_jenjangs !== null && !in_array($record_jenjang, $this->locked_jenjangs)) {
			show_error('Anda tidak memiliki akses ke data presensi tadarus untuk jenjang ' . $record_jenjang, 403);
			return;
		}

		$total = (float) $this->data['presensi_tadarus']->total_nilai;
		if ($total >= 90) { $predikat = 'A'; $deskripsi = 'Sangat Baik'; }
		elseif ($total >= 80) { $predikat = 'B'; $deskripsi = 'Baik'; }
		elseif ($total >= 70) { $predikat = 'C'; $deskripsi = 'Cukup'; }
		elseif ($total >= 60) { $predikat = 'D'; $deskripsi = 'Perlu Bimbingan'; }
		else { $predikat = 'E'; $deskripsi = 'Perlu Pembinaan Intensif'; }

		$this->data['predikat'] = $predikat;
		$this->data['deskripsi'] = $deskripsi;
		$this->data['locked_jenjangs'] = $this->locked_jenjangs;
		$this->data['url_method'] = $this->url_method;
		$this->data['is_locked'] = $this->locked_jenjangs !== null;

		$this->template->title('Detail Presensi Tadarus');
		$this->render('backend/standart/administrator/presensi_tadarus/presensi_tadarus_view', $this->data);
	}

	/**
	* delete Presensi Tadarus (soft delete), with lock check
	*/
	private function _remove($id, $lock = null)
	{
		if ($lock !== null) {
			$record = $this->model_presensi_tadarus->find($id);
			if (empty($record)) {
				return false;
			}
			$rec_jenjang = strtoupper($record->jenjang);
			if (!in_array($rec_jenjang, $lock)) {
				return false;
			}
		}
		return $this->model_presensi_tadarus->change($id, array('deleted_at' => date('Y-m-d H:i:s')));
	}

	// =======================================================================
	// EXPORT
	// =======================================================================
	public function export()
	{
		$this->is_allowed('presensi_tadarus_export');

		$filters = $this->_build_filters();

		// Validate filters
		if (!$this->_has_valid_filters($filters)) {
			header('Content-Type: application/json');
			echo json_encode(array(
				'success' => false,
				'message' => 'Silakan pilih minimal satu filter sebelum mengekspor data.'
			));
			return;
		}

		$filename = $this->model_presensi_tadarus->export_tadarus($filters);

		force_download(FCPATH . 'uploads/' . $filename, NULL);
		@unlink(FCPATH . 'uploads/' . $filename);
	}

	public function export_pdf()
	{
		$this->is_allowed('presensi_tadarus_export');

		$filters = $this->_build_filters();

		// Validate filters
		if (!$this->_has_valid_filters($filters)) {
			header('Content-Type: application/json');
			echo json_encode(array(
				'success' => false,
				'message' => 'Silakan pilih minimal satu filter sebelum mengekspor data.'
			));
			return;
		}

		$this->model_presensi_tadarus->pdf('presensi_tadarus', 'presensi_tadarus');
	}

	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_tadarus_export');

		$table = $title = 'presensi_tadarus';
		$this->load->library('HtmlPdf');

        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight');

        $result = $this->db->get($table);

        $data = $this->model_presensi_tadarus->find($id);
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
}


/* End of file presensi_tadarus.php */
/* Location: ./modules/presensi_tadarus/controllers/backend/Presensi_tadarus.php */
