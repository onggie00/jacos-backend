<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Mapel Alokasi Smp Controller
*| --------------------------------------------------------------------------
*| Manage alokasi jam per mapel per kelas for jadwal generation
*|
*/
class Mapel_alokasi_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mapel_alokasi_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* Show all alokasi
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mapel_alokasi_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');
		$status_filter = $this->input->get('status');
		$tingkatan_filter = $this->input->get('tingkatan');

		$this->data['alokasis'] = $this->model_mapel_alokasi_smp->get($filter, $field, $this->limit_page, $offset, array(), $sort, $sort_type);
		$this->data['alokasi_counts'] = $this->model_mapel_alokasi_smp->count_all($filter, $field);
		$this->data['guru_loads'] = $this->model_mapel_alokasi_smp->get_guru_load_summary();

		$config = array(
			'base_url'     => 'administrator/mapel_alokasi_smp/index/',
			'total_rows'   => $this->model_mapel_alokasi_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Mapel Alokasi SMP');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_list', $this->data);
	}

	/**
	* Generate draft combinations
	*/
	public function generate_draft()
	{
		$this->is_allowed('mapel_alokasi_smp_add');

		$this->data['combinations'] = $this->model_mapel_alokasi_smp->get_draft_combinations();
		$this->data['guru_loads'] = $this->model_mapel_alokasi_smp->get_guru_load_summary();

		$this->template->title('Generate Draft Alokasi');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_generate', $this->data);
	}

	/**
	* Save draft (bulk)
	*/
	public function save_draft()
	{
		try {
			// Check permission
			if (!$this->is_allowed('mapel_alokasi_smp_add', false)) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'success' => false,
						'message' => cclang('sorry_you_do_not_have_permission_to_access')
					)));
				return;
			}

			// Get post data
			$alokasi_data = $this->input->post('alokasi');
			
			if (empty($alokasi_data)) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'success' => false,
						'message' => 'Tidak ada data alokasi yang dikirim'
					)));
				return;
			}

			// Filter entries with jam_target > 0
			$to_save = array();
			$skipped_empty = 0;
			
			foreach ($alokasi_data as $key => $item) {
				$jam_target = isset($item['jam_target']) ? intval($item['jam_target']) : 0;
				$id_guru = isset($item['id_guru']) ? intval($item['id_guru']) : 0;
				$id_kelas_smp = isset($item['id_kelas_smp']) ? intval($item['id_kelas_smp']) : 0;
				$id_mapel = isset($item['id_mapel']) ? intval($item['id_mapel']) : 0;
				
				// Skip empty/zero entries
				if ($jam_target <= 0) {
					$skipped_empty++;
					continue;
				}
				
				// Validate required IDs
				if ($id_guru <= 0 || $id_kelas_smp <= 0 || $id_mapel <= 0) {
					$skipped_empty++;
					continue;
				}
				
				// Validate jam_target range
				if ($jam_target > 10) {
					$this->output
						->set_content_type('application/json')
						->set_output(json_encode(array(
							'success' => false,
							'message' => 'Jam maksimal 10 per kelas',
							'errors' => array($key => 'Jam maksimal 10, input: ' . $jam_target)
						)));
					return;
				}
				
				$to_save[] = array(
					'key' => $key,
					'id_guru' => $id_guru,
					'id_kelas_smp' => $id_kelas_smp,
					'id_mapel' => $id_mapel,
					'jam_target' => $jam_target
				);
			}
			
			// If no data to save
			if (empty($to_save)) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'success' => false,
						'message' => 'Tidak ada data yang diisi. Silakan isi minimal 1 jam/minggu.'
					)));
				return;
			}
			
			// Save to database
			$this->db->trans_start();
			
			$saved = 0;
			$updated = 0;
			$saved_names = array();
			
			foreach ($to_save as $item) {
				// Get guru name for message
				$guru = $this->db->select('nama_lengkap')->get_where('guru_smp', array('id_guru' => $item['id_guru']))->row();
				$guru_name = $guru ? $guru->nama_lengkap : 'Guru#' . $item['id_guru'];
				
				// Check if exists
				$existing = $this->db->get_where('mapel_alokasi_smp', array(
					'id_guru' => $item['id_guru'],
					'id_kelas_smp' => $item['id_kelas_smp'],
					'id_mapel' => $item['id_mapel']
				))->row();
				
				if ($existing) {
					// Update if still draft
					if ($existing->status == 'draft') {
						$this->db->where('id_alokasi_smp', $existing->id_alokasi_smp);
						$this->db->update('mapel_alokasi_smp', array(
							'jam_target' => $item['jam_target']
						));
						$updated++;
						$saved_names[] = $guru_name . ' (update)';
					}
				} else {
					// Insert new
					$this->db->insert('mapel_alokasi_smp', array(
						'id_guru' => $item['id_guru'],
						'id_kelas_smp' => $item['id_kelas_smp'],
						'id_mapel' => $item['id_mapel'],
						'jam_target' => $item['jam_target'],
						'jam_terpenuhi' => 0,
						'total_jam' => $item['jam_target'],
						'status' => 'draft'
					));
					$saved++;
					$saved_names[] = $guru_name;
				}
			}
			
			$this->db->trans_complete();
			
			// Build response
			if ($this->db->trans_status() === FALSE) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'success' => false,
						'message' => 'Gagal menyimpan ke database. Silakan coba lagi.'
					)));
				return;
			}
			
			// Success message
			$message_parts = array();
			if ($saved > 0) $message_parts[] = "$saved baru";
			if ($updated > 0) $message_parts[] = "$updated update";
			
			$message = 'Berhasil menyimpan: ' . implode(', ', $message_parts);
			
			if ($skipped_empty > 0) {
				$message .= " | $skipped_empty kelas kosong dilewati";
			}
			
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array(
					'success' => true,
					'message' => $message,
					'saved_names' => array_unique($saved_names),
					'redirect' => base_url('administrator/mapel_alokasi_smp')
				)));
			return;
			
		} catch (Exception $e) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array(
					'success' => false,
					'message' => 'Error: ' . $e->getMessage()
				)));
			return;
		}
	}

	/**
	* Set status to final (bulk)
	*/
	public function set_final()
	{
		if (!$this->is_allowed('mapel_alokasi_smp_update', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$ids = $this->input->post('ids');
		
		if (empty($ids)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Tidak ada data yang dipilih'
			));
			exit;
		}

		$this->db->where_in('id_alokasi_smp', $ids);
		$this->db->where('status', 'draft');
		$this->db->update('mapel_alokasi_smp', array('status' => 'final'));
		
		$affected = $this->db->affected_rows();
		
		echo json_encode(array(
			'success' => true,
			'message' => "$affected alokasi diubah ke status final"
		));
	}

	/**
	* Set status back to draft
	*/
	public function set_draft()
	{
		if (!$this->is_allowed('mapel_alokasi_smp_update', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$ids = $this->input->post('ids');
		
		if (empty($ids)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Tidak ada data yang dipilih'
			));
			exit;
		}

		$this->db->where_in('id_alokasi_smp', $ids);
		$this->db->where('status', 'final');
		$this->db->update('mapel_alokasi_smp', array('status' => 'draft'));
		
		$affected = $this->db->affected_rows();
		
		echo json_encode(array(
			'success' => true,
			'message' => "$affected alokasi diubah ke status draft"
		));
	}

	/**
	* Fase 1: Preview alokasi yang siap dijadwalkan
	* Sort by constraint terberat duluan
	*/
	public function fase1()
	{
		$this->is_allowed('mapel_alokasi_smp_list');

		// Get all alokasi with status 'final'
		$this->db->select('
			mapel_alokasi_smp.*,
			guru_smp.nama_lengkap as guru_nama,
			guru_smp.kode_mapel as guru_kode_mapel,
			guru_smp.jam_ajar as guru_jam_ajar,
			kelas_smp.label as kelas_label,
			kelas_smp.id_tingkatan,
			mata_pelajaran_smp.nama_mapel as mapel_nama,
			mata_pelajaran_smp.kode_mapel as mapel_kode
		');
		$this->db->join('guru_smp', 'guru_smp.id_guru = mapel_alokasi_smp.id_guru', 'LEFT');
		$this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = mapel_alokasi_smp.id_kelas_smp', 'LEFT');
		$this->db->join('mata_pelajaran_smp', 'mata_pelajaran_smp.id_mapel = mapel_alokasi_smp.id_mapel', 'LEFT');
		$this->db->where('mapel_alokasi_smp.status', 'final');
		$this->db->where('mapel_alokasi_smp.jam_target >', 0);
		$this->db->where('mapel_alokasi_smp.jam_terpenuhi < mapel_alokasi_smp.jam_target', NULL, FALSE);
		$alokasis = $this->db->get('mapel_alokasi_smp')->result();

		// Calculate current load per guru
		$guru_load = array();
		foreach ($alokasis as $a) {
			if (!isset($guru_load[$a->id_guru])) {
				$guru_load[$a->id_guru] = 0;
			}
			$guru_load[$a->id_guru] += $a->jam_target;
		}

		// Get guru jam_ajar and calculate sisa
		$guru_sisa = array();
		foreach ($alokasis as $a) {
			if (!isset($guru_sisa[$a->id_guru])) {
				$current_load = $this->model_mapel_alokasi_smp->get_current_load($a->id_guru);
				$guru_sisa[$a->id_guru] = $a->guru_jam_ajar - $current_load;
			}
		}

		// Expand alokasi into individual slots (jam_target=3 means 3 separate entries)
		$expanded = array();
		foreach ($alokasis as $a) {
			for ($i = 0; $i < $a->jam_target; $i++) {
				$expanded[] = array(
					'id_alokasi_smp' => $a->id_alokasi_smp,
					'id_guru' => $a->id_guru,
					'id_kelas_smp' => $a->id_kelas_smp,
					'id_mapel' => $a->id_mapel,
					'guru_nama' => $a->guru_nama,
					'guru_kode_mapel' => $a->guru_kode_mapel,
					'guru_jam_ajar' => $a->guru_jam_ajar,
					'guru_sisa_jam' => isset($guru_sisa[$a->id_guru]) ? $guru_sisa[$a->id_guru] : 0,
					'kelas_label' => $a->kelas_label,
					'id_tingkatan' => $a->id_tingkatan,
					'mapel_nama' => $a->mapel_nama,
					'mapel_kode' => $a->mapel_kode,
					'jam_ke' => $i + 1,
					'jam_target' => $a->jam_target
				);
			}
		}

		// Sort by constraint: guru with least sisa_jam first
		usort($expanded, function($a, $b) {
			// First: sort by guru sisa_jam (ascending - most constrained first)
			if ($a['guru_sisa_jam'] != $b['guru_sisa_jam']) {
				return $a['guru_sisa_jam'] - $b['guru_sisa_jam'];
			}
			// Second: sort by guru_id for consistency
			if ($a['id_guru'] != $b['id_guru']) {
				return $a['id_guru'] - $b['id_guru'];
			}
			// Third: sort by kelas
			return $a['id_kelas_smp'] - $b['id_kelas_smp'];
		});

		// Get ketetapan rules - load model from other module
		$ketetapan_map = array();
		$ketetapan_result = $this->db->get('mapel_ketetapan_smp')->result();
		foreach ($ketetapan_result as $row) {
			if (!isset($ketetapan_map[$row->kode_mapel])) {
				$ketetapan_map[$row->kode_mapel] = array();
			}
			$ketetapan_map[$row->kode_mapel][$row->tipe_ketetapan] = explode(',', $row->nilai);
		}

		// Summary stats
		$total_slots = count($expanded);
		$total_guru = count($guru_sisa);
		$total_alokasi = count($alokasis);

		$this->data['expanded'] = $expanded;
		$this->data['guru_sisa'] = $guru_sisa;
		$this->data['ketetapan_map'] = $ketetapan_map;
		$this->data['total_slots'] = $total_slots;
		$this->data['total_guru'] = $total_guru;
		$this->data['total_alokasi'] = $total_alokasi;

		$this->template->title('Fase 1: Preview Alokasi');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_fase1', $this->data);
	}

	/**
	* Get Fase 1 data as JSON (for AJAX)
	*/
	public function fase1_data()
	{
		if (!$this->is_allowed('mapel_alokasi_smp_list', false)) {
			echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
			exit;
		}

		// Same logic as fase1() but return JSON
		$this->db->select('
			mapel_alokasi_smp.*,
			guru_smp.nama_lengkap as guru_nama,
			guru_smp.jam_ajar as guru_jam_ajar
		');
		$this->db->join('guru_smp', 'guru_smp.id_guru = mapel_alokasi_smp.id_guru', 'LEFT');
		$this->db->where('status', 'final');
		$this->db->where('jam_target >', 0);
		$this->db->where('jam_terpenuhi < jam_target', NULL, FALSE);
		$alokasis = $this->db->get('mapel_alokasi_smp')->result();

		$expanded = array();
		foreach ($alokasis as $a) {
			for ($i = 0; $i < $a->jam_target; $i++) {
				$expanded[] = array(
					'id_alokasi_smp' => (int)$a->id_alokasi_smp,
					'id_guru' => (int)$a->id_guru,
					'id_kelas_smp' => (int)$a->id_kelas_smp,
					'id_mapel' => (int)$a->id_mapel,
					'jam_ke' => $i + 1
				);
			}
		}

		echo json_encode(array(
			'success' => true,
			'data' => $expanded,
			'total' => count($expanded)
		));
	}

	/**
	* Fase 2: Generate jadwal - penempatan waktu
	*/
	public function fase2()
	{
		$this->is_allowed('mapel_alokasi_smp_update');

		// Start generation
		$result = $this->_run_fase2();

		$this->data['result'] = $result;
		$this->template->title('Fase 2: Hasil Generate');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_fase2', $this->data);
	}

	/**
	* Run Fase 2 logic
	*/
	private function _run_fase2()
	{
		$result = array(
			'success' => true,
			'total_slots' => 0,
			'berhasil' => 0,
			'gagal' => 0,
			'log' => array(),
			'errors' => array()
		);

		// Get all alokasi final yang belum terpenuhi
		$this->db->select('
			mapel_alokasi_smp.*,
			guru_smp.nama_lengkap as guru_nama,
			guru_smp.kode_mapel as guru_kode_mapel,
			kelas_smp.id_tingkatan
		');
		$this->db->join('guru_smp', 'guru_smp.id_guru = mapel_alokasi_smp.id_guru', 'LEFT');
		$this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = mapel_alokasi_smp.id_kelas_smp', 'LEFT');
		$this->db->where('mapel_alokasi_smp.status', 'final');
		$this->db->where('mapel_alokasi_smp.jam_target >', 0);
		$this->db->where('mapel_alokasi_smp.jam_terpenuhi < mapel_alokasi_smp.jam_target', NULL, FALSE);
		$alokasis = $this->db->get('mapel_alokasi_smp')->result();

		// Expand into slots
		$slots = array();
		foreach ($alokasis as $a) {
			for ($i = 0; $i < ($a->jam_target - $a->jam_terpenuhi); $i++) {
				$slots[] = $a;
			}
		}
		$result['total_slots'] = count($slots);

		if (empty($slots)) {
			$result['log'][] = 'Tidak ada slot yang perlu dijadwalkan';
			return $result;
		}

		// Get ketetapan rules
		$ketetapan_map = array();
		$ketetapan_result = $this->db->get('mapel_ketetapan_smp')->result();
		foreach ($ketetapan_result as $row) {
			if (!isset($ketetapan_map[$row->kode_mapel])) {
				$ketetapan_map[$row->kode_mapel] = array();
			}
			$ketetapan_map[$row->kode_mapel][$row->tipe_ketetapan] = explode(',', $row->nilai);
		}

		// Get all valid slots (MENGAJAR only)
		$valid_slots = $this->db->query("
			SELECT psw.id_pelajaran_waktu, psw.id_hari, psw.id_jam,
				   pj.jam_ke, pj.jam_pelajaran, pj.keterangan
			FROM pelajaran_setting_waktu psw
			JOIN pelajaran_jam pj ON pj.id_jam = psw.id_jam
			WHERE pj.keterangan LIKE 'MENGAJAR%'
			ORDER BY psw.id_hari, pj.jam_ke
		")->result();

		// Get existing jadwal for conflict checking
		$existing_jadwal = $this->db->get('jadwal_pelajaran_smp')->result();
		$kelas_slot_map = array(); // kelas -> slot -> true
		$guru_slot_map = array(); // guru -> slot -> true
		$kelas_hari_mapel_map = array(); // kelas -> hari -> mapel -> count

		foreach ($existing_jadwal as $j) {
			$kelas_slot_map[$j->id_kelas_smp][$j->id_pelajaran_waktu] = true;
			$guru_slot_map[$j->id_guru][$j->id_pelajaran_waktu] = true;

			// Get hari from pelajaran_setting_waktu
			$slot_info = $this->_get_slot_info($j->id_pelajaran_waktu);
			if ($slot_info) {
				if (!isset($kelas_hari_mapel_map[$j->id_kelas_smp][$slot_info->id_hari][$j->id_mapel])) {
					$kelas_hari_mapel_map[$j->id_kelas_smp][$slot_info->id_hari][$j->id_mapel] = 0;
				}
				$kelas_hari_mapel_map[$j->id_kelas_smp][$slot_info->id_hari][$j->id_mapel]++;
			}
		}

		// Sort slots by constraint (most constrained first)
		usort($slots, function($a, $b) {
			return $a->id_guru - $b->id_guru;
		});

		// Process each slot
		$this->db->trans_start();

		foreach ($slots as $slot) {
			$id_guru = $slot->id_guru;
			$id_kelas_smp = $slot->id_kelas_smp;
			$id_mapel = $slot->id_mapel;
			$guru_kode = $slot->guru_kode_mapel;
			$id_tingkatan = $slot->id_tingkatan;

			// Get ketetapan for this mapel
			$hari_ketetapan = isset($ketetapan_map[$guru_kode]['hari']) ? $ketetapan_map[$guru_kode]['hari'] : null;
			$jam_ketetapan = isset($ketetapan_map[$guru_kode]['jam']) ? $ketetapan_map[$guru_kode]['jam'] : null;

			// Filter valid slots based on ketetapan
			$filtered_slots = array();
			foreach ($valid_slots as $vs) {
				// Check hari ketetapan
				if ($hari_ketetapan && !in_array($vs->id_hari, $hari_ketetapan)) {
					continue;
				}
				// Check jam ketetapan
				if ($jam_ketetapan && !in_array($vs->jam_ke, $jam_ketetapan)) {
					continue;
				}
				$filtered_slots[] = $vs;
			}

			// Shuffle for randomness
			shuffle($filtered_slots);

			$placed = false;
			$max_retries = 100;
			$retry = 0;

			foreach ($filtered_slots as $vs) {
				if ($retry >= $max_retries) break;
				$retry++;

				$id_pelajaran_waktu = $vs->id_pelajaran_waktu;
				$id_hari = $vs->id_hari;

				// Check 1: Kelas tidak bentrok di slot ini
				if (isset($kelas_slot_map[$id_kelas_smp][$id_pelajaran_waktu])) {
					continue;
				}

				// Check 2: Guru tidak bentrok di slot ini (lintas kelas)
				if (isset($guru_slot_map[$id_guru][$id_pelajaran_waktu])) {
					continue;
				}

				// Check 3: Max 3 jam per hari untuk (kelas, mapel)
				$hari_mapel_count = 0;
				if (isset($kelas_hari_mapel_map[$id_kelas_smp][$id_hari][$id_mapel])) {
					$hari_mapel_count = $kelas_hari_mapel_map[$id_kelas_smp][$id_hari][$id_mapel];
				}
				if ($hari_mapel_count >= 3) {
					continue;
				}

				// Check 4: Total jam mapel untuk kelas ini belum melebihi total_jam
				// (This is already ensured by only processing slots where jam_terpenuhi < jam_target)

				// All checks passed - insert
				$insert_data = array(
					'id_alokasi_smp' => $slot->id_alokasi_smp,
					'id_kelas_smp' => $id_kelas_smp,
					'id_pelajaran_waktu' => $id_pelajaran_waktu,
					'id_mapel' => $id_mapel,
					'id_guru' => $id_guru,
					'kode_mapel' => $guru_kode,
					'mapel_nomor' => '1',
					'jam_per_kelas' => $slot->jam_target
				);

				$this->db->insert('jadwal_pelajaran_smp', $insert_data);

				if ($this->db->affected_rows() > 0) {
					// Update conflict maps
					$kelas_slot_map[$id_kelas_smp][$id_pelajaran_waktu] = true;
					$guru_slot_map[$id_guru][$id_pelajaran_waktu] = true;
					$kelas_hari_mapel_map[$id_kelas_smp][$id_hari][$id_mapel] = $hari_mapel_count + 1;

					// Update jam_terpenuhi
					$this->db->where('id_alokasi_smp', $slot->id_alokasi_smp);
					$this->db->set('jam_terpenuhi', 'jam_terpenuhi + 1', FALSE);
					$this->db->update('mapel_alokasi_smp');

					$result['berhasil'];
					$result['log'][] = "OK: {$slot->guru_nama} -> {$slot->kelas_label} ({$guru_kode}) slot {$id_pelajaran_waktu}";
					$placed = true;
					break;
				}
			}

			if (!$placed) {
				$result['gagal'];
				$result['errors'][] = "GAGAL: {$slot->guru_nama} -> {$slot->kelas_label} ({$guru_kode}) - tidak ada slot valid";
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$result['success'] = false;
			$result['errors'][] = 'Transaksi database gagal';
		}

		// Final stats
		$result['berhasil'] = $this->db->query("SELECT COUNT(*) as cnt FROM jadwal_pelajaran_smp")->row()->cnt;
		$result['sisa'] = $this->db->query("SELECT COUNT(*) as cnt FROM mapel_alokasi_smp WHERE status='final' AND jam_target > jam_terpenuhi")->row()->cnt;

		return $result;
	}

	/**
	* Helper: Get slot info (hari) from id_pelajaran_waktu
	*/
	private function _get_slot_info($id_pelajaran_waktu)
	{
		return $this->db->select('psw.*, pj.jam_ke')
			->join('pelajaran_jam pj', 'pj.id_jam = psw.id_jam')
			->where('psw.id_pelajaran_waktu', $id_pelajaran_waktu)
			->get('pelajaran_setting_waktu psw')
			->row();
	}

	/**
	* Reset jadwal (hapus semua dan reset jam_terpenuhi)
	*/
	public function reset_jadwal()
	{
		if (!$this->is_allowed('mapel_alokasi_smp_delete', false)) {
			echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
			exit;
		}

		$this->db->trans_start();
		$this->db->truncate('jadwal_pelajaran_smp');
		$this->db->update('mapel_alokasi_smp', array('jam_terpenuhi' => 0));
		$this->db->trans_complete();

		if ($this->db->trans_status()) {
			echo json_encode(array('success' => true, 'message' => 'Jadwal berhasil direset'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Gagal reset jadwal'));
		}
	}

	/**
	* Delete alokasi
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mapel_alokasi_smp_delete');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_mapel_alokasi_smp->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->model_mapel_alokasi_smp->remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'alokasi'), 'success');
        } else {
            set_message(cclang('error_delete', 'alokasi'), 'error');
        }

		redirect_back();
	}

	/**
	* View detail
	*/
	public function view($id)
	{
		$this->is_allowed('mapel_alokasi_smp_view');

		$this->data['alokasi'] = $this->model_mapel_alokasi_smp->join_avaiable()->find($id);

		$this->template->title('Detail Alokasi');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_view', $this->data);
	}

	/**
	* Export to excel
	*/
	public function export()
	{
		$this->is_allowed('mapel_alokasi_smp_export');
		$this->model_mapel_alokasi_smp->export('mapel_alokasi_smp', 'mapel_alokasi_smp');
	}

	/**
	* Export to PDF
	*/
	public function export_pdf()
	{
		$this->is_allowed('mapel_alokasi_smp_export');
		$this->model_mapel_alokasi_smp->pdf('mapel_alokasi_smp', 'mapel_alokasi_smp');
	}

	/**
	* Import Excel - Form upload
	*/
	public function import_excel()
	{
		$this->is_allowed('mapel_alokasi_smp_add');

		$this->template->title('Import Alokasi dari Excel');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_import', $this->data);
	}

	/**
	* Import Excel - Process
	*/
	public function import_excel_process()
	{
		if (!$this->is_allowed('mapel_alokasi_smp_add', false)) {
			echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
			exit;
		}

		// Check file upload
		if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] != 0) {
			echo json_encode(array('success' => false, 'message' => 'File tidak ditemukan atau error'));
			exit;
		}

		$file = $_FILES['file_excel']['tmp_name'];
		$ext = pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION);

		if (!in_array(strtolower($ext), array('xlsx', 'xls', 'xlsm'))) {
			echo json_encode(array('success' => false, 'message' => 'Format file harus .xlsx, .xls, atau .xlsm'));
			exit;
		}

		try {
			require_once APPPATH . 'libraries/Excel/PHPExcel.php';
			$objPHPExcel = PHPExcel_IOFactory::load($file);
			$sheet = $objPHPExcel->getSheetByName('Lembar2 (4)');

			if (!$sheet) {
				$sheet = $objPHPExcel->getSheet(1); // Fallback to sheet index 1
			}

			if (!$sheet) {
				echo json_encode(array('success' => false, 'message' => 'Sheet Lembar2 tidak ditemukan'));
				exit;
			}

			// Mapping kolom ke kelas
			$kelas_col_map = array(
				'J' => '7A', 'K' => '7B', 'L' => '7C', 'M' => '7D',
				'N' => '7E', 'O' => '7F', 'P' => '7G', 'Q' => '7H',
				'S' => '8A', 'T' => '8B', 'U' => '8C', 'V' => '8D',
				'W' => '8E', 'X' => '8F', 'Y' => '8G',
				'AA' => '9A', 'AB' => '9B', 'AC' => '9C', 'AD' => '9D',
				'AE' => '9E', 'AF' => '9F', 'AG' => '9G'
			);

			// Get kelas mapping from DB
			$kelas_db = array();
			$kelas_result = $this->db->where('id_tingkatan !=', 4)->get('kelas_smp')->result();
			foreach ($kelas_result as $k) {
				$kelas_db[$k->label] = $k->id_kelas_smp;
			}

			// Process rows 11-58 (guru data)
			$imported = 0;
			$skipped = 0;
			$errors = array();
			$details = array();

			$this->db->trans_start();

			for ($row = 11; $row <= 58; $row++) {
				$nama_excel = trim($sheet->getCell('B' . $row)->getValue());
				$kode_full = trim($sheet->getCell('H' . $row)->getValue());

				if (empty($nama_excel) || empty($kode_full)) {
					$skipped++;
					continue;
				}

				// Extract kode_mapel (before "-")
				$kode_mapel = $kode_full;
				if (strpos($kode_full, '-') !== false) {
					$kode_mapel = explode('-', $kode_full)[0];
				}

				// Match guru by nama_lengkap (before comma = non-gelar)
				$nama_clean = strtolower(trim($nama_excel));
				$guru = null;

				// Try exact match first
				$guru = $this->db->where('LOWER(nama_lengkap)', $nama_clean)->get('guru_smp')->row();

				if (!$guru) {
					// Try match before comma (non-gelar)
					$gurus = $this->db->get('guru_smp')->result();
					foreach ($gurus as $g) {
						$nama_db = strtolower(trim($g->nama_lengkap));
						// Get name before comma
						$nama_db_parts = explode(',', $nama_db);
						$nama_db_no_gelar = trim($nama_db_parts[0]);

						if ($nama_clean === $nama_db_no_gelar || strpos($nama_clean, $nama_db_no_gelar) !== false || strpos($nama_db_no_gelar, $nama_clean) !== false) {
							$guru = $g;
							break;
						}
					}
				}

				if (!$guru) {
					$errors[] = "Row {$row}: Guru '{$nama_excel}' tidak ditemukan di database";
					$skipped++;
					continue;
				}

				// Get mapel from kode_mapel
				$mapel = $this->db->where('kode_mapel', $kode_mapel)->get('mata_pelajaran_smp')->row();
				if (!$mapel) {
					$errors[] = "Row {$row}: Mapel dengan kode '{$kode_mapel}' tidak ditemukan";
					$skipped++;
					continue;
				}

				// Read jam per kelas and insert
				$guru_jam_count = 0;
				foreach ($kelas_col_map as $col => $kelas_label) {
					$jam = intval($sheet->getCell($col . $row)->getValue());

					if ($jam <= 0) continue;

					if (!isset($kelas_db[$kelas_label])) {
						continue;
					}

					$id_kelas_smp = $kelas_db[$kelas_label];

					// Check if exists
					$existing = $this->db->get_where('mapel_alokasi_smp', array(
						'id_guru' => $guru->id_guru,
						'id_kelas_smp' => $id_kelas_smp,
						'id_mapel' => $mapel->id_mapel
					))->row();

					if ($existing) {
						// Update if draft
						if ($existing->status == 'draft') {
							$this->db->where('id_alokasi_smp', $existing->id_alokasi_smp);
							$this->db->update('mapel_alokasi_smp', array(
								'jam_target' => $jam,
								'total_jam' => $jam
							));
							$guru_jam_count++;
						}
					} else {
						// Insert new
						$this->db->insert('mapel_alokasi_smp', array(
							'id_guru' => $guru->id_guru,
							'id_kelas_smp' => $id_kelas_smp,
							'id_mapel' => $mapel->id_mapel,
							'jam_target' => $jam,
							'jam_terpenuhi' => 0,
							'total_jam' => $jam,
							'status' => 'final'
						));
						$guru_jam_count++;
					}
				}

				if ($guru_jam_count > 0) {
					$imported++;
					$details[] = $guru->nama_lengkap . ' (' . $kode_mapel . '): ' . $guru_jam_count . ' kelas';
				} else {
					$skipped++;
				}
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				echo json_encode(array('success' => false, 'message' => 'Gagal import ke database'));
				exit;
			}

			$message = "Import berhasil: {$imported} guru diimport";
			if ($skipped > 0) $message .= ", {$skipped} dilewati";

			echo json_encode(array(
				'success' => true,
				'message' => $message,
				'details' => $details,
				'errors' => $errors,
				'redirect' => base_url('administrator/mapel_alokasi_smp')
			));

		} catch (Exception $e) {
			echo json_encode(array('success' => false, 'message' => 'Error: ' . $e->getMessage()));
		}
		exit;
	}

	/**
	* Quick Generate - Preview
	*/
	public function quick_generate_preview()
	{
		$this->is_allowed('mapel_alokasi_smp_update');

		// Count final alokasi
		$final_count = $this->db->where('status', 'final')->where('jam_target >', 0)->count_all_results('mapel_alokasi_smp');
		$jadwal_count = $this->db->count_all('jadwal_pelajaran_smp');

		// Get summary per guru
		$guru_summary = $this->db->query("
			SELECT 
				g.nama_lengkap,
				g.kode_mapel,
				g.jam_ajar as kuota,
				COALESCE(SUM(a.jam_target), 0) as total_target,
				COUNT(a.id_alokasi_smp) as jumlah_kelas
			FROM guru_smp g
			LEFT JOIN mapel_alokasi_smp a ON a.id_guru = g.id_guru AND a.status = 'final'
			WHERE g.kode_mapel IS NOT NULL AND g.kode_mapel != ''
			GROUP BY g.id_guru
			HAVING total_target > 0
			ORDER BY g.nama_lengkap
		")->result();

		// Get total slots needed
		$total_slots = $this->db->query("
			SELECT COALESCE(SUM(jam_target), 0) as total
			FROM mapel_alokasi_smp
			WHERE status = 'final' AND jam_target > 0
		")->row()->total;

		$this->data['final_count'] = $final_count;
		$this->data['jadwal_count'] = $jadwal_count;
		$this->data['guru_summary'] = $guru_summary;
		$this->data['total_slots'] = $total_slots;

		$this->template->title('Quick Generate');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/mapel_alokasi_smp_quick_generate', $this->data);
	}

	/**
	* Quick Generate - Execute
	*/
	public function quick_generate_execute()
	{
		if (!$this->is_allowed('mapel_alokasi_smp_update', false)) {
			echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
			exit;
		}

		// Check if there are final alokasi
		$final_count = $this->db->where('status', 'final')->where('jam_target >', 0)->count_all_results('mapel_alokasi_smp');

		if ($final_count == 0) {
			echo json_encode(array('success' => false, 'message' => 'Tidak ada alokasi berstatus FINAL. Import atau set alokasi ke FINAL terlebih dahulu.'));
			exit;
		}

		// Run Fase 2
		$result = $this->_run_fase2();

		echo json_encode(array(
			'success' => $result['success'],
			'message' => $result['success'] ? 'Generate berhasil! ' . $result['berhasil'] . ' slot terjadwal.' : 'Generate selesai dengan error.',
			'detail' => $result,
			'redirect' => base_url('administrator/jadwal_pelajaran_smp')
		));
		exit;
	}
}

/* End of file Mapel_alokasi_smp.php */
/* Location: ./modules/mapel_alokasi_smp/controllers/backend/Mapel_alokasi_smp.php */
