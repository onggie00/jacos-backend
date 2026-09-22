<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Dashboard SPP (non-tabel).
 * Sumber data: transaksi_spp (dedup) + spp_sd/smp/sma/ft + tahun_ajaran.
 * Semua nominal pakai total_biaya (payment_amount tidak reliabel).
 */
class Model_keuangan_dashboard_spp extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	private $bulan_nama = array(
		1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
		5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
		9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
	);

	public function nama_bulan($num)
	{
		return isset($this->bulan_nama[intval($num)]) ? $this->bulan_nama[intval($num)] : '';
	}

	/** TA berjalan: CURDATE() di antara tanggal_mulai-selesai; fallback TA dgn sequence tertinggi. */
	public function get_active_tahun_ajaran()
	{
		$row = $this->db->where('CURDATE() BETWEEN tanggal_mulai AND tanggal_selesai', null, false)
			->order_by('sequence', 'ASC')
			->get('tahun_ajaran')
			->row();
		if ($row) {
			return intval($row->id_tahun_ajaran);
		}
		$row = $this->db->select('id_tahun_ajaran')
			->order_by('sequence', 'DESC')
			->get('tahun_ajaran')
			->row();
		return $row ? intval($row->id_tahun_ajaran) : 0;
	}

	public function get_tahun_ajaran_options()
	{
		return $this->db->select('id_tahun_ajaran, label')
			->order_by('sequence', 'ASC')
			->get('tahun_ajaran')
			->result();
	}

	public function daftar_bulan()
	{
		return $this->bulan_nama;
	}

	/**
	 * Peta (bulan 1-12, tahun) -> id_tahun_ajaran.
	 * Bulan >= Juli -> TA "{tahun}/{tahun+1}", else TA "{tahun-1}/{tahun}".
	 * Return NULL jika TA tidak ada di tabel (filter invalid).
	 */
	public function get_id_tahun_ajaran($bulan_num, $tahun)
	{
		$bulan_num = intval($bulan_num);
		$tahun = intval($tahun);
		if ($bulan_num >= 7) {
			$label = $tahun . '/' . ($tahun + 1);
		} else {
			$label = ($tahun - 1) . '/' . $tahun;
		}
		$row = $this->db->select('id_tahun_ajaran')
			->where('label', $label)
			->get('tahun_ajaran')
			->row();
		return $row ? intval($row->id_tahun_ajaran) : null;
	}

	/**
	 * Dedup: hanya row terbaru per (id_spp, bulan, id_tahun_ajaran) + jenjang.
	 * id_spp TIDAK unik antar jenjang (spp_sd/smp/sma/ft overlap), jadi prefix
	 * jenjang dari no_transaksi (LISPP-sd/smp/sma/ft-...) wajib masuk GROUP BY,
	 * kalau tidak row beda jenjang saling meniadakan. Row id_spp NULL lolos tanpa dedup.
	 */
	/** Flag: temp table dedup sudah dibuat utk koneksi/request ini. */
	private $dedup_tmp_ready = false;

	/**
	 * Dedup v1 (lama) — subquery GROUP BY MAX, disimpan sbg rollback cepat.
	 * Aktifkan kembali dgn mengganti body _dedup_join() jadi: return $this->_dedup_join_v1();
	 */
	private function _dedup_join_v1()
	{
		return " LEFT JOIN (
			SELECT MAX(id_transaksi) AS idt
			FROM transaksi_spp
			WHERE id_spp IS NOT NULL
			GROUP BY id_spp, bulan, id_tahun_ajaran, LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(no_transaksi, '-', 2), '-', -1))
		) d ON d.idt = t.id_transaksi ";
	}

	/**
	 * Materialize dedup 1x per request ke temp table (hidup per koneksi MySQL, dipakai
	 * ulang semua query core/detail/trend/breakdown/aging/rekap) — memangkas materialize
	 * berulang full-scan transaksi_spp. Grup identik dgn v1; prefix jenjang WAJIB tetap
	 * masuk GROUP BY (id_spp tidak unik antar spp_sd/smp/sma/ft — test case id_spp=14:
	 * 21 grup, tanpa prefix jadi 1 grup = bug meniadakan antar-jenjang).
	 */
	private function _ensure_dedup_tmp()
	{
		if ($this->dedup_tmp_ready) {
			return;
		}
		// 64MB per koneksi: 120k grup ≈ 5MB; jaga-jaga utk pertumbuhan data (default 16MB)
		$this->db->query("SET SESSION max_heap_table_size = 67108864");
		$this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp_dedup_spp");
		$this->db->query("CREATE TEMPORARY TABLE tmp_dedup_spp (PRIMARY KEY (idt)) ENGINE=MEMORY AS
			SELECT MAX(id_transaksi) AS idt
			FROM transaksi_spp
			WHERE id_spp IS NOT NULL
			GROUP BY id_spp, bulan, id_tahun_ajaran, LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(no_transaksi, '-', 2), '-', -1))");
		$this->dedup_tmp_ready = true;
	}

	private function _dedup_join()
	{
		$this->_ensure_dedup_tmp();
		return " LEFT JOIN tmp_dedup_spp d ON d.idt = t.id_transaksi ";
	}

	/**
	 * Dedup utk query multi-referensi (get_aging/get_aging_count: UNION 4 branch).
	 * Temp table tidak boleh direferensikan >1x per statement (MySQL err 1137),
	 * jadi pakai CTE — optimizer materialize CTE multi-referensi 1x per statement.
	 * Grup identik dgn v1.
	 */
	private function _dedup_cte_select()
	{
		return "SELECT MAX(id_transaksi) AS idt
			FROM transaksi_spp
			WHERE id_spp IS NOT NULL
			GROUP BY id_spp, bulan, id_tahun_ajaran, LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(no_transaksi, '-', 2), '-', -1))";
	}

	private function _dedup_cte_join()
	{
		return " LEFT JOIN d ON d.idt = t.id_transaksi ";
	}

	private function _dedup_where()
	{
		return " (d.idt IS NOT NULL OR t.id_spp IS NULL) ";
	}

	/** CASE kategori pembayaran (hanya status 2; updated_at NULL -> NULL). */
	private function _kategori_case($tgl_awal, $tgl_akhir)
	{
		return " CASE
			WHEN t.status_transaksi <> 2 THEN NULL
			WHEN t.updated_at IS NULL THEN NULL
			WHEN t.count_bill > 1 THEN 'dimuka'
			WHEN t.updated_at < " . $this->db->escape($tgl_awal) . " THEN 'awal'
			WHEN t.updated_at <= " . $this->db->escape($tgl_akhir) . " THEN 'tepat'
			ELSE 'telat'
		END ";
	}

	/** Join 4 tabel spp (jenjang via prefix no_transaksi; id_spp collide antar jenjang). */
	private function _spp_join($alias_prefix = 's')
	{
		$sql = " LEFT JOIN spp_sd  " . $alias_prefix . "1 ON t.id_spp = " . $alias_prefix . "1.id AND t.no_transaksi LIKE '%LISPP-SD%' ";
		$sql .= " LEFT JOIN spp_smp " . $alias_prefix . "2 ON t.id_spp = " . $alias_prefix . "2.id AND t.no_transaksi LIKE '%LISPP-SMP%' ";
		$sql .= " LEFT JOIN spp_sma " . $alias_prefix . "3 ON t.id_spp = " . $alias_prefix . "3.id AND t.no_transaksi LIKE '%LISPP-SMA%' ";
		$sql .= " LEFT JOIN spp_ft  " . $alias_prefix . "4 ON t.id_spp = " . $alias_prefix . "4.id AND t.no_transaksi LIKE '%LISPP-FT%' ";
		return $sql;
	}

	private function _jenjang_case($alias_prefix = 's')
	{
		return " CASE
			WHEN " . $alias_prefix . "1.id IS NOT NULL THEN 'SD'
			WHEN " . $alias_prefix . "2.id IS NOT NULL THEN 'SMP'
			WHEN " . $alias_prefix . "3.id IS NOT NULL THEN 'SMA'
			WHEN " . $alias_prefix . "4.id IS NOT NULL THEN 'FT'
			ELSE NULL
		END ";
	}

	/**
	 * Fragment WHERE filter lanjutan (jenjang/kelas/status) — dipakai bersama
	 * core/detail/trend/breakdown. $skip_jenjang=true utk breakdown (jenjang tetap semua).
	 */
	private function _filter_where($filters, $skip_jenjang = false)
	{
		$where = '';
		if (!$skip_jenjang && !empty($filters['jenjang'])) {
			$j = strtoupper($filters['jenjang']);
			$map = array('SD' => 's1', 'SMP' => 's2', 'SMA' => 's3', 'FT' => 's4');
			if (isset($map[$j])) {
				$where .= " AND " . $map[$j] . ".id IS NOT NULL ";
			}
		}
		if (!empty($filters['kelas'])) {
			$where .= " AND COALESCE(s1.kelas, s2.kelas, s3.kelas, s4.kelas) = " . $this->db->escape($filters['kelas']) . " ";
		}
		if (!empty($filters['tingkatan'])) {
			$where .= " AND " . $this->_tingkatan_cond_sql($filters['tingkatan'], array('SD' => 's1', 'SMP' => 's2', 'SMA' => 's3', 'FT' => 's4')) . " ";
		}
		if ($filters['status'] !== '' && $filters['status'] !== null && $filters['status'] !== false) {
			$where .= " AND t.status_transaksi = " . intval($filters['status']) . " ";
		}
		return $where;
	}

	/** Nama tabel relasi kelas/tingkatan per jenjang. */
	private function _tingkatan_tables()
	{
		return array(
			'SD'  => array('kelas_sd',  'tingkatan_sd',  'id_tingkatan_sd'),
			'SMP' => array('kelas_smp', 'tingkatan_smp', 'id_tingkatan_smp'),
			'SMA' => array('kelas_sma', 'tingkatan_sma', 'id_tingkatan_sma'),
			'FT'  => array('kelas_ft',  'tingkatan_ft',  'id_tingkatan_ft'),
		);
	}

	/** Fragment SQL: kelas alias s1-s4 terbatas pd kelas dgn tingkatan label X (OR antar jenjang). */
	private function _tingkatan_cond_sql($tingkatan, $alias_map)
	{
		$tn = $this->db->escape($tingkatan);
		$parts = array();
		foreach ($this->_tingkatan_tables() as $j => $t) {
			$a = $alias_map[$j];
			$parts[] = "(" . $a . ".id IS NOT NULL AND " . $a . ".kelas IN (
				SELECT kl.label FROM " . $t[0] . " kl JOIN " . $t[1] . " tn ON tn." . $t[2] . " = kl.id_tingkatan
				WHERE tn.label = " . $tn . "))";
		}
		return "(" . implode(" OR ", $parts) . ")";
	}

	/** Opsi tingkatan utk dropdown filter, per jenjang (urut numerik dulu, lalu label). Cache TTL singkat. */
	public function get_tingkatan_options()
	{
		$cached = $this->_cache_get('tingkatan_options');
		if (is_array($cached)) {
			return $cached;
		}
		$out = array('SD' => array(), 'SMP' => array(), 'SMA' => array(), 'FT' => array());
		foreach ($this->_tingkatan_tables() as $j => $t) {
			$rows = $this->db->select('label')
				->order_by('(label + 0 = 0)', 'ASC', false)
				->order_by('label + 0', 'ASC', false)
				->order_by('label', 'ASC')
				->get($t[1])
				->result();
			$labels = array();
			foreach ($rows as $r) {
				if ($r->label !== '') {
					$labels[] = $r->label;
				}
			}
			$out[$j] = $labels;
		}
		$this->_cache_set('tingkatan_options', $out);
		return $out;
	}

	/** Opsi kelas terbatas pd tingkatan terpilih (label kelas yg ter-map di tabel kelas). */
	public function get_kelas_by_tingkatan($jenjang, $tingkatan)
	{
		$map = array('SD' => 'spp_sd', 'SMP' => 'spp_smp', 'SMA' => 'spp_sma', 'FT' => 'spp_ft');
		$j = strtoupper($jenjang);
		if (!isset($map[$j]) || $tingkatan === '' || $tingkatan === null) {
			return array();
		}
		$t = $this->_tingkatan_tables()[$j];
		$tn = $this->db->escape($tingkatan);
		$sql = "SELECT DISTINCT sp.kelas
			FROM " . $map[$j] . " sp
			WHERE sp.kelas IS NOT NULL AND sp.kelas <> ''
			AND sp.kelas IN (SELECT kl.label FROM " . $t[0] . " kl JOIN " . $t[1] . " tn ON tn." . $t[2] . " = kl.id_tingkatan WHERE tn.label = " . $tn . ")
			ORDER BY sp.kelas ASC";
		$rows = $this->db->query($sql)->result();
		$out = array();
		foreach ($rows as $r) { $out[] = $r->kelas; }
		return $out;
	}

	/**
	 * Cache file CI (TTL singkat) utk data turunan yang jarang berubah (opsi dropdown).
	 */
	private function _cache_get($key)
	{
		if (!isset($this->cache)) {
			$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy', 'key_prefix' => 'kd_spp_'));
		}
		return $this->cache->get($key);
	}

	private function _cache_set($key, $val, $ttl = 300)
	{
		if (!isset($this->cache)) {
			$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy', 'key_prefix' => 'kd_spp_'));
		}
		$this->cache->save($key, $val, $ttl);
	}

	/**
	 * Agregat core 2 periode sekaligus (bulan filter + bulan lalu rolling) dalam 1 scan.
	 * Dedup/join sama dgn get_core_by_month; agregat bulan lalu pakai conditional aggregate
	 * supaya subquery dedup cukup materialize 1x, bukan 2x.
	 * Return: array{rows: array{status_transaksi, kategori, c, nominal, c_prev, nominal_prev},
	 *               prev_valid: bool} — null jika TA bulan filter tidak ada.
	 */
	public function get_core_two_months($bulan_num, $tahun, $filters = array())
	{
		$ta_id = $this->get_id_tahun_ajaran($bulan_num, $tahun);
		if ($ta_id === null) {
			return null;
		}
		$prev_ts = mktime(0, 0, 0, intval($bulan_num) - 1, 1, intval($tahun));
		$prev_bulan = intval(date('n', $prev_ts));
		$prev_tahun = intval(date('Y', $prev_ts));
		$ta_prev = $this->get_id_tahun_ajaran($prev_bulan, $prev_tahun);

		$bulan_nama = $this->nama_bulan($bulan_num);
		$prev_nama = $this->nama_bulan($prev_bulan);
		$cur_period = " (t.bulan = " . $this->db->escape($bulan_nama) . " AND t.id_tahun_ajaran = " . intval($ta_id) . ") ";
		if ($ta_prev === null) {
			// TA bulan lalu tidak ada di tabel: agregat prev nol + penanda invalid
			$prev_period = " (1=0) ";
			$prev_valid = false;
		} else {
			$prev_period = " (t.bulan = " . $this->db->escape($prev_nama) . " AND t.id_tahun_ajaran = " . intval($ta_prev) . ") ";
			$prev_valid = true;
		}

		$tgl_awal = sprintf('%04d-%02d-01 00:00:00', $tahun, $bulan_num);
		$tgl_akhir = date('Y-m-t 23:59:59', strtotime($tgl_awal));
		$kategori = $this->_kategori_case($tgl_awal, $tgl_akhir);
		$sql = "SELECT t.status_transaksi, " . $kategori . " AS kategori,
			COUNT(CASE WHEN " . $cur_period . " THEN 1 END) AS c,
			COALESCE(SUM(CASE WHEN " . $cur_period . " THEN t.total_biaya END), 0) AS nominal,
			COUNT(CASE WHEN " . $prev_period . " THEN 1 END) AS c_prev,
			COALESCE(SUM(CASE WHEN " . $prev_period . " THEN t.total_biaya END), 0) AS nominal_prev
			FROM transaksi_spp t"
			. $this->_dedup_join()
			. $this->_spp_join()
			. " WHERE (" . $cur_period . " OR " . $prev_period . ")"
			. $this->_filter_where($filters)
			. " GROUP BY t.status_transaksi, kategori";
		return array('rows' => $this->db->query($sql)->result(), 'prev_valid' => $prev_valid);
	}

	/**
	 * Agregat core untuk 1 bulan + tahun kalender (dipakai juga untuk bulan lalu).
	 * $bulan_num 1-12, $tahun kalender.
	 */
	public function get_core_by_month($bulan_num, $tahun, $filters = array())
	{
		$ta_id = $this->get_id_tahun_ajaran($bulan_num, $tahun);
		if ($ta_id === null) {
			return null;
		}
		$bulan_nama = $this->nama_bulan($bulan_num);
		$tgl_awal = sprintf('%04d-%02d-01 00:00:00', $tahun, $bulan_num);
		$tgl_akhir = date('Y-m-t 23:59:59', strtotime($tgl_awal));
		$kategori = $this->_kategori_case($tgl_awal, $tgl_akhir);
		$sql = "SELECT t.status_transaksi, " . $kategori . " AS kategori, COUNT(*) AS c, COALESCE(SUM(t.total_biaya),0) AS nominal
			FROM transaksi_spp t"
			. $this->_dedup_join()
			. $this->_spp_join()
			. " WHERE " . $this->_dedup_where()
			. " AND t.bulan = " . $this->db->escape($bulan_nama)
			. " AND t.id_tahun_ajaran = " . intval($ta_id)
			. $this->_filter_where($filters)
			. " GROUP BY t.status_transaksi, kategori";
		return $this->db->query($sql)->result();
	}

	/**
	 * Tabel detail (paginasi server-side).
	 * $filters: jenjang ('SD'|'SMP'|'SMA'|'FT'|''), kelas (''), status (''|0|1|2)
	 * $sort_by: alias whitelist; $sort_dir: 'asc'|'desc'
	 */
	public function get_detail($bulan_num, $tahun, $filters, $limit, $offset, $sort_by = '', $sort_dir = 'desc', $date_start = '', $date_end = '')
	{
		$build = $this->_detail_query($bulan_num, $tahun, $filters, $date_start, $date_end);
		if ($build === null) {
			return array();
		}
		$sql = $build['select'] . $build['where'] . $this->_sort_order($sort_by, $sort_dir);
		if (intval($limit) > 0) {
			$sql .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
		}
		return $this->db->query($sql)->result();
	}

	/** Filter rentang tanggal bayar (updated_at). Format YYYY-MM-DD; kosong = tanpa filter. */
	private function _date_where($date_start, $date_end)
	{
		$where = '';
		if ($date_start && ($ts = strtotime($date_start)) !== false) {
			$where .= " AND t.updated_at >= " . $this->db->escape(date('Y-m-d 00:00:00', $ts)) . " ";
		}
		if ($date_end && ($ts = strtotime($date_end)) !== false) {
			$where .= " AND t.updated_at <= " . $this->db->escape(date('Y-m-d 23:59:59', $ts)) . " ";
		}
		return $where;
	}

	/** ORDER BY whitelist utk tabel detail. */
	private function _sort_order($sort_by, $sort_dir)
	{
		$map = array(
			'no_transaksi' => 't.no_transaksi',
			'siswa'        => 't.user_name',
			'jenjang'      => 'jenjang',
			'kelas'        => 'kelas',
			'bulan'        => 't.bulan',
			'jumlah'       => 't.count_bill',
			'nominal'      => 't.total_biaya',
			'total_dibayar' => 'total_dibayar',
			'status'       => 't.status_transaksi',
			'kategori'     => 'kategori',
			'tgl_bayar'    => 't.updated_at',
		);
		if (!isset($map[$sort_by])) {
			return " ORDER BY t.id_transaksi DESC";
		}
		$dir = (strtolower($sort_dir) === 'asc') ? 'ASC' : 'DESC';
		return " ORDER BY " . $map[$sort_by] . " " . $dir . ", t.id_transaksi DESC";
	}

	public function count_detail($bulan_num, $tahun, $filters, $date_start = '', $date_end = '')
	{
		$build = $this->_detail_query($bulan_num, $tahun, $filters, $date_start, $date_end);
		if ($build === null) {
			return 0;
		}
		$row = $this->db->query("SELECT COUNT(*) AS c " . $build['where'])->row();
		return intval($row->c);
	}

	private function _detail_query($bulan_num, $tahun, $filters, $date_start = '', $date_end = '')
	{
		$ta_id = $this->get_id_tahun_ajaran($bulan_num, $tahun);
		if ($ta_id === null) {
			return null;
		}
		$bulan_nama = $this->nama_bulan($bulan_num);
		$tgl_awal = sprintf('%04d-%02d-01 00:00:00', $tahun, $bulan_num);
		$tgl_akhir = date('Y-m-t 23:59:59', strtotime($tgl_awal));
		$kategori = $this->_kategori_case($tgl_awal, $tgl_akhir);
		$jenjang = $this->_jenjang_case();

		$select = "SELECT t.id_transaksi, t.no_transaksi, t.bulan, t.detail_bulan, t.count_bill,
			t.total_biaya, t.total_biaya * COALESCE(t.count_bill, 1) AS total_dibayar,
			t.status_transaksi, t.updated_at, t.user_name,
			" . $kategori . " AS kategori,
			" . $jenjang . " AS jenjang,
			COALESCE(s1.kelas, s2.kelas, s3.kelas, s4.kelas) AS kelas ";
		$where = " FROM transaksi_spp t"
			. $this->_dedup_join()
			. $this->_spp_join()
			. " WHERE " . $this->_dedup_where()
			. " AND t.bulan = " . $this->db->escape($bulan_nama)
			. " AND t.id_tahun_ajaran = " . intval($ta_id)
			. $this->_filter_where($filters)
			. $this->_date_where($date_start, $date_end);
		return array('select' => $select, 'where' => $where);
	}

	/**
	 * Trend 12 bulan rolling (ending bulan+tahun filter).
	 * Return array row: {ym: 'YYYY-MM', c, nominal} — hanya bulan berdata.
	 */
	public function get_trend($bulan_num, $tahun, $filters = array())
	{
		$end_ts = mktime(0, 0, 0, intval($bulan_num) + 1, 1, intval($tahun));
		$start_ts = mktime(0, 0, 0, intval($bulan_num) - 11, 1, intval($tahun));
		$sql = "SELECT DATE_FORMAT(t.updated_at, '%Y-%m') AS ym, COUNT(*) AS c, COALESCE(SUM(t.total_biaya),0) AS nominal
			FROM transaksi_spp t"
			. $this->_dedup_join()
			. $this->_spp_join()
			. " WHERE " . $this->_dedup_where()
			. " AND t.status_transaksi = 2"
			. " AND t.updated_at >= " . $this->db->escape(date('Y-m-d 00:00:00', $start_ts))
			. " AND t.updated_at < " . $this->db->escape(date('Y-m-d 00:00:00', $end_ts))
			. $this->_filter_where($filters)
			. " GROUP BY ym ORDER BY ym";
		return $this->db->query($sql)->result();
	}

	/**
	 * Aging report / daftar penunggak (status 0/1, dedup), urut tunggakan terlama.
	 * Return array row: {jenjang, nama, kelas, jml_bulan, total_tunggakan, tunggakan_terlama}
	 */
	/**
	 * Rentang bulan tunggakan per TA: dari bulan mulai TA s/d MIN(bulan akhir TA,
	 * bulan/tahun filter atas). Return array {id, months[]} — hanya TA dgn rentang tak kosong.
	 */
	private function _aging_ta_ranges($filters)
	{
		$bulan_filter = isset($filters['bulan']) && intval($filters['bulan']) >= 1 && intval($filters['bulan']) <= 12 ? intval($filters['bulan']) : intval(date('n'));
		$tahun_filter = isset($filters['tahun']) && intval($filters['tahun']) >= 2000 ? intval($filters['tahun']) : intval(date('Y'));
		$filter_ym = sprintf('%04d%02d', $tahun_filter, $bulan_filter);

		if (!empty($filters['id_tahun_ajaran'])) {
			$tas = $this->db->where('id_tahun_ajaran', intval($filters['id_tahun_ajaran']))->get('tahun_ajaran')->result();
		} else {
			$tas = $this->db->order_by('sequence', 'ASC')->get('tahun_ajaran')->result();
		}

		$out = array();
		foreach ($tas as $ta) {
			$start_ym = date('Ym', strtotime($ta->tanggal_mulai));
			$end_ym = date('Ym', strtotime($ta->tanggal_selesai));
			if ($filter_ym < $start_ym) {
				continue; // filter sebelum TA mulai -> rentang kosong
			}
			$eff_end_ym = ($end_ym < $filter_ym) ? $end_ym : $filter_ym;
			$months = array();
			$y = intval(substr($start_ym, 0, 4));
			$m = intval(substr($start_ym, 4, 2));
			while (sprintf('%04d%02d', $y, $m) <= $eff_end_ym) {
				$months[] = $this->bulan_nama[$m];
				$m++;
				if ($m > 12) { $m = 1; $y++; }
			}
			if (empty($months)) {
				continue;
			}
			$out[] = array('id' => intval($ta->id_tahun_ajaran), 'months' => $months);
		}
		return $out;
	}

	/** WHERE blok rentang per-TA: (TA=X AND bulan IN (...)) OR ... ; null = rentang kosong. */
	private function _aging_rentang_cond($filters)
	{
		$ranges = $this->_aging_ta_ranges($filters);
		if (empty($ranges)) {
			return null;
		}
		$parts = array();
		foreach ($ranges as $r) {
			$months = array();
			foreach ($r['months'] as $m) {
				$months[] = $this->db->escape($m);
			}
			$parts[] = "(t.id_tahun_ajaran = " . intval($r['id']) . " AND t.bulan IN (" . implode(',', $months) . "))";
		}
		return " AND (" . implode(" OR ", $parts) . ") ";
	}

	/** WHERE search nama/NIS utk aging (LIKE %q%), escape wildcard manual. */
	private function _aging_search_cond($q)
	{
		if ($q === '' || $q === null) {
			return '';
		}
		$raw = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $q);
		$like = $this->db->escape($raw);
		return " AND (sa.nama_lengkap LIKE " . $like . " OR sa.nis LIKE " . $like . ") ";
	}

	public function get_aging($limit = 200, $filters = array(), $offset = 0)
	{
		// status filter: aging hanya relevan utk belum/menunggu
		$status_cond = " IN (0, 1)";
		if ($filters['status'] !== '' && $filters['status'] !== null && $filters['status'] !== false) {
			$st = intval($filters['status']);
			if ($st === 2) {
				return array();
			}
			$status_cond = " = " . $st;
		}
		$kelas_cond = '';
		if (!empty($filters['kelas'])) {
			$kelas_cond = " AND sp.kelas = " . $this->db->escape($filters['kelas']);
		}
		$rentang_cond = $this->_aging_rentang_cond($filters);
		if ($rentang_cond === null) {
			return array();
		}
		$search_cond = $this->_aging_search_cond(isset($filters['q']) ? $filters['q'] : '');
		$tingkatan_map = $this->_tingkatan_tables();
		$ta_scope_cond = !empty($filters['id_tahun_ajaran']) ? " AND sp.id_tahun_ajaran = " . intval($filters['id_tahun_ajaran']) : '';
		$order_expr = "CONCAT(LPAD(t.id_tahun_ajaran,3,'0'), LPAD(FIELD(t.bulan,'Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni'),2,'0'))";
		$branches = array();
		$map = array(
			'SD'  => array('spp_sd',  'siswa_sd_aktif',  'id_siswa_sd_aktif'),
			'SMP' => array('spp_smp', 'siswa_smp_aktif', 'id_siswa_smp_aktif'),
			'SMA' => array('spp_sma', 'siswa_sma_aktif', 'id_siswa_sma_aktif'),
			'FT'  => array('spp_ft',  'siswa_ft_aktif',  'id_siswa_ft_aktif'),
		);
		if (!empty($filters['jenjang']) && isset($map[strtoupper($filters['jenjang'])])) {
			$only = strtoupper($filters['jenjang']);
			$map = array($only => $map[$only]);
		}
		foreach ($map as $j => $tbl) {
			$tingkatan_cond = !empty($filters['tingkatan']) ? " AND sp.kelas IN (SELECT kl.label FROM " . $tingkatan_map[$j][0] . " kl JOIN " . $tingkatan_map[$j][1] . " tn ON tn." . $tingkatan_map[$j][2] . " = kl.id_tingkatan WHERE tn.label = " . $this->db->escape($filters['tingkatan']) . ")" : '';
			$branches[] = "SELECT '" . $j . "' AS jenjang, sa.nama_lengkap AS nama, sa.nis AS nis, sp.kelas AS kelas,
				COUNT(DISTINCT CONCAT(t.id_tahun_ajaran, '-', t.bulan)) AS jml_bulan, COALESCE(SUM(t.total_biaya),0) AS total_tunggakan,
				MIN(" . $order_expr . ") AS oldest_key,
				SUBSTRING_INDEX(GROUP_CONCAT(CONCAT(t.bulan, ' ', tta.label) ORDER BY " . $order_expr . " SEPARATOR ','), ',', 1) AS tunggakan_terlama,
				SUBSTRING_INDEX(GROUP_CONCAT(tta.label ORDER BY " . $order_expr . " SEPARATOR ','), ',', 1) AS tahun_ajaran
				FROM transaksi_spp t"
				. $this->_dedup_cte_join()
				. " JOIN " . $tbl[0] . " sp ON sp.id = t.id_spp AND t.no_transaksi LIKE '%LISPP-" . $j . "%'"
				. $ta_scope_cond . "
				JOIN " . $tbl[1] . " sa ON sa." . $tbl[2] . " = sp.id_siswa_aktif AND sa.is_active = 1
				LEFT JOIN tahun_ajaran tta ON tta.id_tahun_ajaran = t.id_tahun_ajaran
				WHERE " . $this->_dedup_where() . "
				AND t.status_transaksi " . $status_cond . $kelas_cond . $search_cond . $tingkatan_cond . $rentang_cond . "
				GROUP BY sp.id, sa.nama_lengkap, sp.kelas";
		}
		$sql = "WITH d AS (" . $this->_dedup_cte_select() . ") SELECT * FROM (" . implode(" UNION ALL ", $branches) . ") x
			ORDER BY x.oldest_key ASC, x.jml_bulan DESC, x.total_tunggakan DESC
			LIMIT " . intval($limit) . " OFFSET " . intval($offset);
		return $this->db->query($sql)->result();
	}

	/** Jumlah siswa penunggak (utk paginasi aging), filter sama dgn get_aging. */
	public function get_aging_count($filters = array())
	{
		$status_cond = " IN (0, 1)";
		if ($filters['status'] !== '' && $filters['status'] !== null && $filters['status'] !== false) {
			$st = intval($filters['status']);
			if ($st === 2) {
				return 0;
			}
			$status_cond = " = " . $st;
		}
		$kelas_cond = '';
		if (!empty($filters['kelas'])) {
			$kelas_cond = " AND sp.kelas = " . $this->db->escape($filters['kelas']);
		}
		$rentang_cond = $this->_aging_rentang_cond($filters);
		if ($rentang_cond === null) {
			return 0;
		}
		$search_cond = $this->_aging_search_cond(isset($filters['q']) ? $filters['q'] : '');
		$tingkatan_map = $this->_tingkatan_tables();
		$ta_scope_cond = !empty($filters['id_tahun_ajaran']) ? " AND sp.id_tahun_ajaran = " . intval($filters['id_tahun_ajaran']) : '';
		$branches = array();
		$map = array(
			'SD'  => array('spp_sd',  'siswa_sd_aktif',  'id_siswa_sd_aktif'),
			'SMP' => array('spp_smp', 'siswa_smp_aktif', 'id_siswa_smp_aktif'),
			'SMA' => array('spp_sma', 'siswa_sma_aktif', 'id_siswa_sma_aktif'),
			'FT'  => array('spp_ft',  'siswa_ft_aktif',  'id_siswa_ft_aktif'),
		);
		if (!empty($filters['jenjang']) && isset($map[strtoupper($filters['jenjang'])])) {
			$only = strtoupper($filters['jenjang']);
			$map = array($only => $map[$only]);
		}
		foreach ($map as $j => $tbl) {
			$tingkatan_cond = !empty($filters['tingkatan']) ? " AND sp.kelas IN (SELECT kl.label FROM " . $tingkatan_map[$j][0] . " kl JOIN " . $tingkatan_map[$j][1] . " tn ON tn." . $tingkatan_map[$j][2] . " = kl.id_tingkatan WHERE tn.label = " . $this->db->escape($filters['tingkatan']) . ")" : '';
			$branches[] = "SELECT sp.id
				FROM transaksi_spp t"
				. $this->_dedup_cte_join()
				. " JOIN " . $tbl[0] . " sp ON sp.id = t.id_spp AND t.no_transaksi LIKE '%LISPP-" . $j . "%'"
				. $ta_scope_cond . "
				JOIN " . $tbl[1] . " sa ON sa." . $tbl[2] . " = sp.id_siswa_aktif AND sa.is_active = 1
				WHERE " . $this->_dedup_where() . "
				AND t.status_transaksi " . $status_cond . $kelas_cond . $search_cond . $tingkatan_cond . $rentang_cond . "
				GROUP BY sp.id";
		}
		$sql = "WITH d AS (" . $this->_dedup_cte_select() . ") SELECT COUNT(*) AS c FROM (" . implode(" UNION ALL ", $branches) . ") x";
		$row = $this->db->query($sql)->row();
		return intval($row->c);
	}

	/**
	 * Detail tunggakan 1 siswa (status 0/1, dedup) per jenjang, urut TA + Juli→Juni.
	 * $rentang_cond: hasil _aging_rentang_cond() utk pembatasan rentang (opsional).
	 */
	private function _aging_detail_rows($jenjang, $nis, $rentang_cond = '')
	{
		$map = array(
			'SD'  => array('spp_sd',  'siswa_sd_aktif',  'id_siswa_sd_aktif'),
			'SMP' => array('spp_smp', 'siswa_smp_aktif', 'id_siswa_smp_aktif'),
			'SMA' => array('spp_sma', 'siswa_sma_aktif', 'id_siswa_sma_aktif'),
			'FT'  => array('spp_ft',  'siswa_ft_aktif',  'id_siswa_ft_aktif'),
		);
		$j = strtoupper($jenjang);
		if (!isset($map[$j]) || $nis === '' || $nis === null) {
			return array();
		}
		$tbl = $map[$j];
		$sql = "SELECT t.bulan, tta.label AS tahun_ajaran, t.status_transaksi,
			t.total_biaya, sa.nama_lengkap AS nama, sp.kelas AS kelas, sa.is_active
			FROM transaksi_spp t"
			. $this->_dedup_join()
			. " JOIN " . $tbl[0] . " sp ON sp.id = t.id_spp AND t.no_transaksi LIKE '%LISPP-" . $j . "%'
			JOIN " . $tbl[1] . " sa ON sa." . $tbl[2] . " = sp.id_siswa_aktif AND sa.nis = " . $this->db->escape($nis) . "
			LEFT JOIN tahun_ajaran tta ON tta.id_tahun_ajaran = t.id_tahun_ajaran
			WHERE " . $this->_dedup_where() . "
			AND t.status_transaksi IN (0, 1)"
			. $rentang_cond . "
			ORDER BY tta.sequence ASC, FIELD(t.bulan,'Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni') ASC, t.id_transaksi ASC";
		return $this->db->query($sql)->result();
	}

	/** Tabel 1 modal detail: tunggakan siswa dlm rentang aging aktif (filter TA + bulan/tahun). */
	public function get_aging_detail_terakhir($jenjang, $nis, $filters = array())
	{
		$rentang_cond = $this->_aging_rentang_cond($filters);
		if ($rentang_cond === null) {
			return array();
		}
		return $this->_aging_detail_rows($jenjang, $nis, $rentang_cond);
	}

	/** Tabel 2 modal detail: seluruh tunggakan siswa sejak jadi siswa (tanpa rentang). */
	public function get_aging_detail_semua($jenjang, $nis)
	{
		return $this->_aging_detail_rows($jenjang, $nis);
	}

	/**
	 * Susun data laporan PDF tunggakan (ala contoh tagihan): kelompok per TA + total.
	 * $scope: 'terakhir' (rentang aging aktif) | 'semua' (tanpa rentang).
	 */
	public function build_tunggakan_pdf_data($jenjang, $nis, $filters = array(), $scope = 'semua')
	{
		if ($scope === 'terakhir') {
			$rows = $this->get_aging_detail_terakhir($jenjang, $nis, $filters);
		} else {
			$rows = $this->get_aging_detail_semua($jenjang, $nis);
		}
		$siswa = array('jenjang' => $jenjang, 'nama' => '', 'nis' => $nis, 'kelas' => '', 'status' => '');
		$groups = array();
		$total = 0;
		if (!empty($rows)) {
			$siswa['nama'] = $rows[0]->nama;
			$siswa['kelas'] = $rows[0]->kelas;
			$siswa['status'] = intval($rows[0]->is_active) === 1 ? 'Aktif' : 'Non-Aktif';
			foreach ($rows as $r) {
				$label = ($r->tahun_ajaran !== null && $r->tahun_ajaran !== '') ? $r->tahun_ajaran : '-';
				if (!isset($groups[$label])) {
					$groups[$label] = array('label' => $label, 'rows' => array(), 'total' => 0);
				}
				$groups[$label]['rows'][] = $r;
				$groups[$label]['total'] += intval($r->total_biaya);
				$total += intval($r->total_biaya);
			}
		}
		return array('siswa' => $siswa, 'groups' => $groups, 'total' => $total);
	}

	/**
	 * Rekapitulasi tunggakan per unit + tingkatan (ala sample Rekap Tunggakan).
	 * $cutoff (opsional, 'Y-m-d'): cut-off simulasi — tagihan lunas SETELAH tanggal ini
	 * tetap dihitung menunggak; bulan efektif = jatuh tempo <= cutoff.
	 * Return null jika TA tidak ada; struktur:
	 * {months:[6 bulan semester], units:[{label, rows[], subtotal}], grand}
	 * Bulan efektif = bulan semester <= filter; bulan setelahnya kolom 0.
	 */
	public function get_rekap_tunggakan($bulan, $tahun, $cutoff = null)
	{
		$ta_id = $this->get_id_tahun_ajaran($bulan, $tahun);
		if ($ta_id === null) {
			return null;
		}
		$ta = $this->db->select('label')->where('id_tahun_ajaran', $ta_id)->get('tahun_ajaran')->row();
		if (!$ta || !preg_match('/^(\\d{4})\\/(\\d{4})$/', $ta->label, $m)) {
			return null;
		}
		if (intval($bulan) >= 7) {
			$months = array('Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		} else {
			$months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni');
		}
		$idx = array_search($this->nama_bulan($bulan), $months);
		if ($idx === false) {
			return null;
		}
		$tahun_semester = (intval($bulan) >= 7) ? $m[1] : $m[2];
		$bulan_num = array_flip($this->bulan_nama);
		$cut_sql = null;
		if ($cutoff !== null && ($cut_ts = strtotime($cutoff)) !== false) {
			// bulan efektif: jatuh tempo (tahun semester + bulan) <= cut-off
			$cut_ym = intval(date('Ym', $cut_ts));
			$eff = array();
			foreach ($months as $mm) {
				if (intval($tahun_semester . sprintf('%02d', $bulan_num[$mm])) <= $cut_ym) {
					$eff[] = $mm;
				}
			}
			$cut_sql = $this->db->escape(date('Y-m-d 23:59:59', $cut_ts));
		} else {
			$eff = array_slice($months, 0, $idx + 1);
		}

		$unit_labels = array(
			'SD'  => 'SD Labschool Cibubur',
			'SMP' => 'SMP Labschool Cibubur',
			'SMA' => 'SMA Labschool Cibubur',
			'FT'  => 'SMA Labschool Cibubur Program France Track',
		);
		$siswa_map = array(
			'SD'  => array('spp_sd',  'siswa_sd_aktif',  'id_siswa_sd_aktif'),
			'SMP' => array('spp_smp', 'siswa_smp_aktif', 'id_siswa_smp_aktif'),
			'SMA' => array('spp_sma', 'siswa_sma_aktif', 'id_siswa_sma_aktif'),
			'FT'  => array('spp_ft',  'siswa_ft_aktif',  'id_siswa_ft_aktif'),
		);
		$units = array();
		$grand = array('jml_siswa' => 0, 'tot_siswa' => 0, 'tot_nominal' => 0, 'pg' => array_fill(0, 6, 0));
		foreach ($siswa_map as $j => $tbl) {
			$rows = $this->_rekap_branch($j, $tbl, $ta_id, $eff, $cut_sql);
			$sub = array('jml_siswa' => 0, 'tot_siswa' => 0, 'tot_nominal' => 0, 'pg' => array_fill(0, 6, 0));
			foreach ($rows as $r) {
				$sub['jml_siswa'] += intval($r->jml_siswa);
				$sub['tot_siswa'] += intval($r->tot_siswa);
				$sub['tot_nominal'] += intval($r->tot_nominal);
				for ($i = 0; $i < 6; $i++) {
					$sub['pg'][$i] += intval($r->{'pg_' . $i});
				}
			}
			$units[] = array('label' => $unit_labels[$j], 'rows' => $rows, 'subtotal' => $sub);
			$grand['jml_siswa'] += $sub['jml_siswa'];
			$grand['tot_siswa'] += $sub['tot_siswa'];
			$grand['tot_nominal'] += $sub['tot_nominal'];
			for ($i = 0; $i < 6; $i++) {
				$grand['pg'][$i] += $sub['pg'][$i];
			}
		}
		return array('months' => $months, 'units' => $units, 'grand' => $grand);
	}

	/** Query rekap 1 jenjang: baris per tingkatan numerik + kolom penunggak per bulan efektif. */
	private function _rekap_branch($j, $tbl, $ta_id, $eff_months, $cut_sql = null)
	{
		$tt = $this->_tingkatan_tables()[$j];
		$sa_id = $tbl[2];
		$months_in = array();
		foreach ($eff_months as $mm) {
			$months_in[] = $this->db->escape($mm);
		}
		// penunggak per cut-off: belum/menunggu, ATAU lunas tapi dibayar setelah cut-off
		if ($cut_sql !== null) {
			$unpaid = "d.idt IS NOT NULL AND (t.status_transaksi IN (0, 1) OR (t.status_transaksi = 2 AND t.updated_at > " . $cut_sql . ")) AND ";
		} else {
			$unpaid = "d.idt IS NOT NULL AND t.status_transaksi IN (0, 1) AND ";
		}
		$sql = "SELECT tn.label AS tingkatan, tn.biaya_spp AS nominal,
			COUNT(DISTINCT sa." . $sa_id . ") AS jml_siswa";
		for ($i = 0; $i < 6; $i++) {
			if (isset($eff_months[$i])) {
				$sql .= ", COUNT(DISTINCT CASE WHEN " . $unpaid . "t.bulan = " . $this->db->escape($eff_months[$i]) . " THEN sa." . $sa_id . " END) AS pg_" . $i;
			} else {
				$sql .= ", 0 AS pg_" . $i;
			}
		}
		$sql .= ", COUNT(DISTINCT CASE WHEN " . $unpaid . "t.id_spp IS NOT NULL THEN sa." . $sa_id . " END) AS tot_siswa,
			COALESCE(SUM(CASE WHEN " . $unpaid . "t.id_spp IS NOT NULL THEN t.total_biaya ELSE 0 END), 0) AS tot_nominal
			FROM " . $tt[1] . " tn
			JOIN " . $tt[0] . " kl ON kl.id_tingkatan = tn." . $tt[2] . "
			JOIN " . $tbl[0] . " sp ON sp.kelas = kl.label AND sp.id_tahun_ajaran = " . intval($ta_id) . "
			JOIN " . $tbl[1] . " sa ON sa." . $sa_id . " = sp.id_siswa_aktif AND sa.is_active = 1
			LEFT JOIN transaksi_spp t ON t.id_spp = sp.id AND t.no_transaksi LIKE '%LISPP-" . $j . "%' AND t.id_tahun_ajaran = " . intval($ta_id) . "
				AND t.bulan IN (" . implode(',', $months_in) . ")
			LEFT JOIN tmp_dedup_spp d ON d.idt = t.id_transaksi
			WHERE tn.label REGEXP " . $this->db->escape('^[0-9]+$') . "
			GROUP BY tn.label, tn.biaya_spp
			ORDER BY tn.label + 0 ASC";
		return $this->db->query($sql)->result();
	}

	/**
	 * Breakdown per jenjang untuk 1 bulan filter.
	 * Return array row: {jenjang, tagihan, lunas_nominal, lunas_count}
	 */
	public function get_breakdown_jenjang($bulan_num, $tahun, $filters = array())
	{
		$ta_id = $this->get_id_tahun_ajaran($bulan_num, $tahun);
		if ($ta_id === null) {
			return array();
		}
		$bulan_nama = $this->nama_bulan($bulan_num);
		$jenjang = $this->_jenjang_case();
		$sql = "SELECT " . $jenjang . " AS jenjang,
				COALESCE(SUM(t.total_biaya),0) AS tagihan,
				COALESCE(SUM(CASE WHEN t.status_transaksi = 2 THEN t.total_biaya ELSE 0 END),0) AS lunas_nominal,
				SUM(CASE WHEN t.status_transaksi = 2 THEN 1 ELSE 0 END) AS lunas_count
				FROM transaksi_spp t"
				. $this->_dedup_join()
				. $this->_spp_join()
				. " WHERE " . $this->_dedup_where()
				. " AND t.bulan = " . $this->db->escape($bulan_nama)
				. " AND t.id_tahun_ajaran = " . intval($ta_id)
				. $this->_filter_where($filters, true)
				. " GROUP BY jenjang";
		return $this->db->query($sql)->result();
	}

	/** Opsi kelas untuk filter lanjutan, per jenjang (dari tabel spp_*). Cache TTL singkat. */
	public function get_kelas_options()
	{
		$cached = $this->_cache_get('kelas_options');
		if (is_array($cached)) {
			return $cached;
		}
		$out = array('SD' => array(), 'SMP' => array(), 'SMA' => array(), 'FT' => array());
		$map = array('SD' => 'spp_sd', 'SMP' => 'spp_smp', 'SMA' => 'spp_sma', 'FT' => 'spp_ft');
		foreach ($map as $j => $tbl) {
			$rows = $this->db->select('DISTINCT kelas', false)
				->where('kelas IS NOT NULL', null, false)
				->order_by('kelas', 'ASC')
				->get($tbl)
				->result();
			$opts = array();
			foreach ($rows as $r) {
				if ($r->kelas !== '') {
					$opts[] = $r->kelas;
				}
			}
			$out[$j] = $opts;
		}
		$this->_cache_set('kelas_options', $out);
		return $out;
	}
}
