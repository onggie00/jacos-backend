<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mhcu_sesi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_mhcu_sesi');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('mhcu_sesi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$filters = array(
			'id_mhcu_periode' => $this->input->get('periode'),
			'presensi_role' => $this->input->get('role'),
			'status' => $this->input->get('status'),
			'kategori' => $this->input->get('kategori'),
		);

		// Default periode → terbaru jika kosong
		$latest_periode = $this->model_mhcu_sesi->get_latest_periode();
		if (empty($filters['id_mhcu_periode']) && !empty($latest_periode)) {
			$filters['id_mhcu_periode'] = $latest_periode->id_mhcu_periode;
		}
		$periode_aktif = $filters['id_mhcu_periode'];

		$this->data['sessions'] = $this->model_mhcu_sesi->get($filter, $field, $this->limit_page, $offset, $filters);
		$this->data['session_counts'] = $this->model_mhcu_sesi->count_all($filter, $field, $filters);
		$this->data['periodes'] = $this->model_mhcu_sesi->get_periodes();
		$this->data['roles'] = $this->model_mhcu_sesi->get_roles();

		// Statistik
		$periode_filter = $periode_aktif;
		$periode_cond = !empty($periode_filter) ? ' AND s.id_mhcu_periode = ' . intval($periode_filter) : '';

		$stats = $this->mymodel->withquery("SELECT
			COUNT(*) as total,
			SUM(CASE WHEN s.status = 'selesai' THEN 1 ELSE 0 END) as selesai,
			SUM(CASE WHEN s.status != 'selesai' THEN 1 ELSE 0 END) as belum,
			SUM(CASE WHEN hi.is_krisis = 1 THEN 1 ELSE 0 END) as krisis
			FROM mhcu_sesi s
			LEFT JOIN mhcu_hasil_individu hi ON hi.id_sesi = s.id_mhcu_sesi
			WHERE s.deleted_at IS NULL " . $periode_cond, "row");
		$this->data['stats'] = $stats;

		// Hitung peserta tidak hadir untuk periode aktif
		$role_filter = $this->input->get('role');
		$stats->tidak_hadir = 0;
		$stats->total_peserta = 0;
		if (!empty($periode_aktif)) {
			$stats->tidak_hadir = $this->model_mhcu_sesi->count_tidak_hadir($periode_aktif, $role_filter);
			$stats->total_peserta = $this->model_mhcu_sesi->count_total_peserta($role_filter);
		}

		// Kategori breakdown
		$kategori_stats = $this->mymodel->withquery("SELECT
			COALESCE(pk.label_profil, 'Belum Selesai') as kategori,
			COUNT(*) as jumlah
			FROM mhcu_sesi s
			LEFT JOIN mhcu_hasil_individu hi ON hi.id_sesi = s.id_mhcu_sesi
			LEFT JOIN mhcu_profil_kategori pk ON pk.id_profil_kategori = hi.id_profil_kategori
			WHERE s.deleted_at IS NULL " . $periode_cond . "
			GROUP BY pk.label_profil", "result");
		$this->data['kategori_stats'] = $kategori_stats;

		$config = array(
			'base_url'     => 'administrator/mhcu_sesi/index/',
			'total_rows'   => $this->model_mhcu_sesi->count_all($filter, $field, $filters),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Monitoring Sesi MHCU');
		$this->render('backend/standart/administrator/mhcu_sesi/mhcu_sesi_list', $this->data);
	}

	/**
	 * Ambil daftar peserta yang tidak hadir untuk periode tertentu (JSON)
	 * Dipakai modal "Peserta Tidak Hadir"
	 */
	public function tidak_hadir()
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$periode_id = $this->input->get('periode');
		if (empty($periode_id)) {
			$latest = $this->model_mhcu_sesi->get_latest_periode();
			if ($latest) $periode_id = $latest->id_mhcu_periode;
		}

		if (empty($periode_id)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Periode belum tersedia. Silahkan buat periode terlebih dahulu.')));
			return;
		}

		$role_filter = $this->input->get('role');
		$rows = $this->model_mhcu_sesi->get_tidak_hadir($periode_id, $role_filter);
		$total_peserta = $this->model_mhcu_sesi->count_total_peserta($role_filter);

		$periode_info = $this->db->where('id_mhcu_periode', $periode_id)->get('mhcu_periode')->row();
		$nama_periode = ($periode_info && !empty($periode_info->nama_periode)) ? $periode_info->nama_periode : '';

		$this->output->set_content_type('application/json')->set_output(json_encode(array(
			'rows' => $rows,
			'count' => count($rows),
			'total_peserta' => $total_peserta,
			'periode_id' => $periode_id,
			'periode_nama' => $nama_periode,
		)));
	}

	/**
	 * Export Excel daftar peserta tidak hadir
	 */
	public function export_tidak_hadir()
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$periode_id = $this->input->get('periode');
		if (empty($periode_id)) {
			$latest = $this->model_mhcu_sesi->get_latest_periode();
			if ($latest) $periode_id = $latest->id_mhcu_periode;
		}

		if (empty($periode_id)) {
			$this->session->set_flashdata('error', 'Periode belum tersedia.');
			redirect_back();
			return;
		}

		$role_filter = $this->input->get('role');
		$rows = $this->model_mhcu_sesi->get_tidak_hadir($periode_id, $role_filter);

		$periode_info = $this->db->where('id_mhcu_periode', $periode_id)->get('mhcu_periode')->row();
		$nama_periode = ($periode_info && !empty($periode_info->nama_periode)) ? $periode_info->nama_periode : '';

		$this->load->library('mhcu_excel');
		$this->mhcu_excel->generate_tidak_hadir_export($rows, $nama_periode);
	}

	/**
	 * Export Rekap Instrumen — single sheet, 1 baris per peserta.
	 * Kolom: No, Nama, NPP, [Demografi...], WHO-5, PHQ-9, GAD-7, CBI, Psikososial, Instrumen 6, Kategori, Krisis
	 * Skor diambil dari mhcu_instrument_score (sudah processed by Mhcu_scoring).
	 * Wajib filter periode. Hanya sesi 'selesai'.
	 */
	public function export_rekap_instrumen()
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$periode_filter = $this->input->get('periode');
		if (empty($periode_filter)) {
			$this->session->set_flashdata('error', 'Silahkan pilih filter periode terlebih dahulu sebelum export.');
			redirect_back();
			return;
		}

		$filters = array(
			'id_mhcu_periode' => $periode_filter,
			'presensi_role'    => $this->input->get('role'),
			'status'           => 'selesai',
			'kategori'         => $this->input->get('kategori'),
		);

		// Ambil semua sesi selesai (no limit)
		$sesis = $this->model_mhcu_sesi->get(null, null, 100000, 0, $filters);

		// Ambil semua pertanyaan demografi (untuk header kolom dinamis)
		$all_pertanyaan = $this->db->order_by('no_urut', 'ASC')->get('mhcu_demografi_pertanyaan')->result();

		// Kumpulkan semua id_sesi
		$sesi_ids = array();
		foreach ($sesis as $s) {
			$sesi_ids[] = intval($s->id_mhcu_sesi);
		}

		// Bulk load demografi jawaban untuk semua sesi sekaligus
		$demografi_map = array(); // [id_sesi][id_demografi_pertanyaan] = jawaban
		if (!empty($sesi_ids)) {
			$ids_csv = implode(',', $sesi_ids);
			$demo_rows = $this->db->query(
				"SELECT sd.id_sesi, sd.id_demografi_pertanyaan,
					CASE WHEN sd.id_demografi_pertanyaan = 9 THEN sd.demografi_text_option
						 ELSE COALESCE(do2.label_option, sd.demografi_text_option) END AS jawaban
				FROM mhcu_sesi_demografi sd
				LEFT JOIN mhcu_demografi_option do2 ON do2.id_demografi_option = sd.id_demografi_option
				WHERE sd.id_sesi IN (" . $ids_csv . ")"
			)->result();
			foreach ($demo_rows as $dr) {
				if (!isset($demografi_map[$dr->id_sesi])) {
					$demografi_map[$dr->id_sesi] = array();
				}
				$demografi_map[$dr->id_sesi][$dr->id_demografi_pertanyaan] = $dr->jawaban;
			}
		}

		// Bulk load skor instrument untuk semua sesi sekaligus
		$skor_map = array(); // [id_sesi][kode_instrument] = skor (processed)
		if (!empty($sesi_ids)) {
			$ids_csv = implode(',', $sesi_ids);
			$skor_rows = $this->db->query(
				"SELECT mis.id_sesi, mi.kode_instrument, mis.dimensi_aspek, mis.skor, bk.warna
				FROM mhcu_instrument_score mis
				JOIN mhcu_instrument mi ON mi.id_instrument = mis.id_instrument
				LEFT JOIN mhcu_band_kategori bk
					ON bk.kode_instrument = mi.kode_instrument
					AND (
						 (bk.dimensi_aspek IS NULL AND (mis.dimensi_aspek IN ('Kesejahteraan Psikologis','Depresi','Kecemasan','CBI Keseluruhan') OR mi.kode_instrument = 'CBI'))
					  OR (bk.dimensi_aspek = 'Total' AND mis.dimensi_aspek LIKE '%Total%')
					  OR (bk.kode_instrument IN ('PSIKOSOSIAL','PSIKOSOSIAL_PIMPINAN') AND bk.dimensi_aspek = mis.dimensi_aspek)
					)
					AND mis.skor BETWEEN bk.batas_bawah AND bk.batas_atas
				WHERE mis.id_sesi IN (" . $ids_csv . ")
				ORDER BY mi.no_urut ASC"
			)->result();
			foreach ($skor_rows as $sr) {
				if (!isset($skor_map[$sr->id_sesi])) {
					$skor_map[$sr->id_sesi] = array();
				}
				$kode = $sr->kode_instrument;
				$dim = strtolower($sr->dimensi_aspek);

				if ($kode === 'WHO5' && $dim === 'kesejahteraan psikologis') {
					$skor_map[$sr->id_sesi]['who5'] = $sr->skor;
					$skor_map[$sr->id_sesi]['who5_warna'] = $sr->warna;
				} elseif ($kode === 'PHQ9' && $dim === 'depresi') {
					$skor_map[$sr->id_sesi]['phq9'] = $sr->skor;
					$skor_map[$sr->id_sesi]['phq9_warna'] = $sr->warna;
				} elseif ($kode === 'GAD7' && $dim === 'kecemasan') {
					$skor_map[$sr->id_sesi]['gad7'] = $sr->skor;
					$skor_map[$sr->id_sesi]['gad7_warna'] = $sr->warna;
				} elseif ($kode === 'CBI' && $dim === 'personal burnout') {
					$skor_map[$sr->id_sesi]['cbi_personal'] = $sr->skor;
					$skor_map[$sr->id_sesi]['cbi_personal_warna'] = $sr->warna;
				} elseif ($kode === 'CBI' && $dim === 'work-related burnout') {
					$skor_map[$sr->id_sesi]['cbi_work'] = $sr->skor;
					$skor_map[$sr->id_sesi]['cbi_work_warna'] = $sr->warna;
				} elseif ($kode === 'CBI' && $dim === 'client-related burnout') {
					$skor_map[$sr->id_sesi]['cbi_client'] = $sr->skor;
					$skor_map[$sr->id_sesi]['cbi_client_warna'] = $sr->warna;
				} elseif ($kode === 'CBI' && $dim === 'cbi keseluruhan') {
					$skor_map[$sr->id_sesi]['cbi'] = $sr->skor;
					$skor_map[$sr->id_sesi]['cbi_warna'] = $sr->warna;
				} elseif (($kode === 'PSIKOSOSIAL' || $kode === 'PSIKOSOSIAL_PIMPINAN') && strpos($dim, 'total') !== false) {
					$skor_map[$sr->id_sesi]['psikososial'] = $sr->skor;
					$skor_map[$sr->id_sesi]['psikososial_warna'] = $sr->warna;
				}

				// Simpan skor per-aspek psikososial (bukan total)
				if (($kode === 'PSIKOSOSIAL' || $kode === 'PSIKOSOSIAL_PIMPINAN') && strpos($dim, 'total') === false) {
					if (!isset($skor_map[$sr->id_sesi]['psikososial_aspek'])) {
						$skor_map[$sr->id_sesi]['psikososial_aspek'] = array();
					}
					$skor_map[$sr->id_sesi]['psikososial_aspek'][$sr->dimensi_aspek] = array(
						'skor' => $sr->skor,
						'warna' => $sr->warna,
					);
				}
			}
		}

		// Bulk load kategori + krisis dari mhcu_hasil_individu
		$hasil_map = array();
		if (!empty($sesi_ids)) {
			$ids_csv = implode(',', $sesi_ids);
			$hasil_rows = $this->db->query(
				"SELECT hi.id_sesi, pk.label_profil AS kategori, pk.warna AS kategori_warna, hi.is_krisis
				FROM mhcu_hasil_individu hi
				LEFT JOIN mhcu_profil_kategori pk ON pk.id_profil_kategori = hi.id_profil_kategori
				WHERE hi.id_sesi IN (" . $ids_csv . ")"
			)->result();
			foreach ($hasil_rows as $hr) {
				$hasil_map[$hr->id_sesi] = $hr;
			}
		}

		// Bulk load jawaban Instrumen 6 (Guru/Karyawan, id_instrument=6)
		// & Instrumen 8 (Pimpinan, id_instrument=8).
		// Struktur: tambahan_map[id_sesi][id_instrument_item] = array of ['label', 'text']
		// multi-select: 1+ rows per item, label = opsi (atau 'Lainnya: <text>').
		// text-only: 1 row, label = text_value.
		$tambahan_map = array();
		if (!empty($sesi_ids)) {
			$ids_csv = implode(',', $sesi_ids);
			$tambahan_rows = $this->db->query(
				"SELECT si.id_sesi, si.id_instrument_item, ii.id_instrument,
						ii.input_type, ii.no_urut_item,
						so.label_option, so.is_lainnya,
						si.text_value, si.id_skala_option
				FROM mhcu_sesi_instrument si
				JOIN mhcu_instrument_item ii ON ii.id_instrument_item = si.id_instrument_item
				LEFT JOIN mhcu_skala_option so ON so.id_skala_option = si.id_skala_option
				WHERE si.id_sesi IN (" . $ids_csv . ")
				  AND ii.id_instrument IN (6, 8)
				ORDER BY si.id_sesi, ii.no_urut_item, si.id_skala_option"
			)->result();
			foreach ($tambahan_rows as $tr) {
				if (!isset($tambahan_map[$tr->id_sesi])) {
					$tambahan_map[$tr->id_sesi] = array('id_instrument' => intval($tr->id_instrument), 'items' => array());
				}
				$item_no = intval($tr->no_urut_item);
				if (!isset($tambahan_map[$tr->id_sesi]['items'][$item_no])) {
					$tambahan_map[$tr->id_sesi]['items'][$item_no] = array(
						'input_type' => $tr->input_type,
						'choices'    => array(),
					);
				}
				// Susun label per baris (1 baris = 1 pilihan utk checkbox, atau 1 teks utk text-only)
				if ($tr->input_type === 'checkbox_multi_choice' && $tr->id_skala_option > 0) {
					$label = $tr->label_option;
					if ($tr->is_lainnya == 1 && trim($tr->text_value) !== '') {
						$label = 'Lainnya: ' . $tr->text_value;
					} elseif ($tr->id_skala_option == 0 && trim($tr->text_value) !== '') {
						// edge case: text tanpa label_option (mungkin "Lainnya" tanpa is_lainnya flag)
						$label = trim($tr->text_value);
					}
					$tambahan_map[$tr->id_sesi]['items'][$item_no]['choices'][] = $label;
				} else {
					// text-only: text_value jadi jawaban
					$tambahan_map[$tr->id_sesi]['items'][$item_no]['choices'][] = trim($tr->text_value);
				}
			}
		}

		// Susun data akhir
		$export_data = array();
		foreach ($sesis as $s) {
			$sid = $s->id_mhcu_sesi;

			// Demografi
			$demo_answers = array();
			foreach ($all_pertanyaan as $pq) {
				$jawaban = '';
				if (isset($demografi_map[$sid][$pq->id_demografi_pertanyaan])) {
					$jawaban = $demografi_map[$sid][$pq->id_demografi_pertanyaan];
				}
				$demo_answers[] = $jawaban;
			}

			// Skor
			$skor = isset($skor_map[$sid]) ? $skor_map[$sid] : array();

			// Kategori & Krisis
			$kategori = '';
			$is_krisis = 0;
			$kategori_warna = '';
			if (isset($hasil_map[$sid])) {
				$kategori = isset($hasil_map[$sid]->kategori) ? $hasil_map[$sid]->kategori : '';
				$is_krisis = isset($hasil_map[$sid]->is_krisis) ? intval($hasil_map[$sid]->is_krisis) : 0;
				$kategori_warna = isset($hasil_map[$sid]->kategori_warna) ? $hasil_map[$sid]->kategori_warna : '';
			}

			$info = $this->_get_unit_jabatan($s->npp, $s->presensi_role);

			// Bulk load tambahan (instrumen 6 / 8) -> susun 4 kolom generik.
			// Struktur instrumen 6 (GK): item1=checkbox, item2=text, item3=text -> kategori dari Mhcu_tambahan_kategori
			// Struktur instrumen 8 (Pim): item1/2/3=text, item4=checkbox
			$tambahan = isset($tambahan_map[$sid]) ? $tambahan_map[$sid] : array('id_instrument' => 0, 'items' => array());
			$tambahan_choices = function($no) use ($tambahan) {
				if (isset($tambahan['items'][$no]['choices'])) {
					return $tambahan['items'][$no]['choices'];
				}
				return array();
			};
			$inst6_id = 6;
			$inst8_id = 8;
			$is_pim_inst8 = ($tambahan['id_instrument'] == $inst8_id);

			// Default kolom kosong (untuk role tanpa data tambahan)
			$dukungan_pilihan = '';
			$dukungan_lainnya = '';
			$tambahan_text_1 = '';
			$tambahan_text_2 = '';
			$tambahan_text_3 = '';
			$tambahan_text_4 = '';
			$kategori_tambahan = '';

			if ($is_pim_inst8) {
				// Pimpinan: item1/2/3=text, item4=checkbox
				$c1 = $tambahan_choices(1); $tambahan_text_1 = !empty($c1) ? $c1[0] : '';
				$c2 = $tambahan_choices(2); $tambahan_text_2 = !empty($c2) ? $c2[0] : '';
				$c3 = $tambahan_choices(3); $tambahan_text_3 = !empty($c3) ? $c3[0] : '';
				$c4 = $tambahan_choices(4); $dukungan_pilihan = !empty($c4) ? implode('; ', $c4) : '';
			} else {
				// GK: item1=checkbox, item2=text, item3=text
				$c1 = $tambahan_choices(1);
				// §3.4: pisahkan "Lainnya: <text>" jadi kolom sendiri utk rekap ringkas
				$pilihan_list = array();
				$lainnya_text = '';
				foreach ($c1 as $ch) {
					if (strpos($ch, 'Lainnya:') === 0) {
						$lainnya_text = trim(substr($ch, strlen('Lainnya:')));
					} else {
						$pilihan_list[] = $ch;
					}
				}
				$dukungan_pilihan = !empty($pilihan_list) ? implode('; ', $pilihan_list) : '';
				$dukungan_lainnya = $lainnya_text;
				$c2 = $tambahan_choices(2); $tambahan_text_2 = !empty($c2) ? $c2[0] : '';
				$c3 = $tambahan_choices(3); $tambahan_text_3 = !empty($c3) ? $c3[0] : '';
				// §3.1 + §3.2: kategorisasi pertanyaan 3 dengan Mhcu_tambahan_kategori
				if (trim($tambahan_text_3) !== '') {
					$this->load->library('Mhcu_tambahan_kategori');
					$tags = Mhcu_tambahan_kategori::match($tambahan_text_3);
					$labels = array();
					foreach ($tags as $t) $labels[] = Mhcu_tambahan_kategori::label($t);
					$kategori_tambahan = implode('; ', $labels);
				}
			}

			$export_data[] = array(
				'nama_lengkap'   => $s->nama_lengkap,
				'npp'            => $s->npp,
				'presensi_role'  => $s->presensi_role,
				'demografi'      => $demo_answers,
				'who5'              => isset($skor['who5']) ? $skor['who5'] : '',
				'who5_warna'       => isset($skor['who5_warna']) ? $skor['who5_warna'] : '',
				'phq9'              => isset($skor['phq9']) ? $skor['phq9'] : '',
				'phq9_warna'        => isset($skor['phq9_warna']) ? $skor['phq9_warna'] : '',
				'gad7'              => isset($skor['gad7']) ? $skor['gad7'] : '',
				'gad7_warna'        => isset($skor['gad7_warna']) ? $skor['gad7_warna'] : '',
				'cbi_personal'      => isset($skor['cbi_personal']) ? $skor['cbi_personal'] : '',
				'cbi_personal_warna'=> isset($skor['cbi_personal_warna']) ? $skor['cbi_personal_warna'] : '',
				'cbi_work'          => isset($skor['cbi_work']) ? $skor['cbi_work'] : '',
				'cbi_work_warna'    => isset($skor['cbi_work_warna']) ? $skor['cbi_work_warna'] : '',
				'cbi_client'        => isset($skor['cbi_client']) ? $skor['cbi_client'] : '',
				'cbi_client_warna'  => isset($skor['cbi_client_warna']) ? $skor['cbi_client_warna'] : '',
				'cbi'               => isset($skor['cbi']) ? $skor['cbi'] : '',
				'cbi_warna'         => isset($skor['cbi_warna']) ? $skor['cbi_warna'] : '',
				'psikososial'       => isset($skor['psikososial']) ? $skor['psikososial'] : '',
				'psikososial_warna' => isset($skor['psikososial_warna']) ? $skor['psikososial_warna'] : '',
				'psikososial_aspek' => isset($skor['psikososial_aspek']) ? $skor['psikososial_aspek'] : array(),
				// Instrumen tambahan (6=gk, 8=pim) — 6 kolom generik
				'dukungan_pilihan'  => $dukungan_pilihan,
				'dukungan_lainnya'  => $dukungan_lainnya,
				'tambahan_t1'       => $tambahan_text_1, // pim: tantangan sbg pimpinan
				'tambahan_t2'       => $tambahan_text_2, // gk: tantangan 6 bln / pim: tantangan hidup
				'tambahan_t3'       => $tambahan_text_3, // gk: harapan dukungan / pim: perbaikan 1 hal
				'kategori_tambahan' => $kategori_tambahan, // gk saja (keyword-match p3)
				'kategori'          => $kategori,
				'kategori_warna'    => $kategori_warna,
				'is_krisis'      => $is_krisis,
			);
		}

		// Info periode untuk filename
		$periode_info = $this->db->where('id_mhcu_periode', $periode_filter)->get('mhcu_periode')->row();
		$filename = 'rekap_instrumen_mhcu';
		if ($periode_info && !empty($periode_info->nama_periode)) {
			$slug = preg_replace('/[^A-Za-z0-9_\-]/', '_', $periode_info->nama_periode);
			$filename = 'rekap_instrumen_mhcu_' . strtolower($slug);
		}

		$this->load->library('mhcu_excel');
		$this->mhcu_excel->generate_rekap_instrumen_export($export_data, $all_pertanyaan, $periode_info, $filename);
	}

	public function get_chart_data()
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$periode_id = $this->input->get('periode');
		$periode_cond = !empty($periode_id) ? ' AND mis.id_sesi IN (SELECT id_mhcu_sesi FROM mhcu_sesi WHERE id_mhcu_periode = ' . intval($periode_id) . ' AND deleted_at IS NULL)' : '';

		// Rata-rata skor per instrument per dimensi
		$scores = $this->mymodel->withquery("SELECT
			mi.nama_instrument,
			mi.kode_instrument,
			mis.dimensi_aspek,
			ROUND(AVG(mis.skor), 2) as avg_skor,
			COUNT(*) as jumlah
			FROM mhcu_instrument_score mis
			JOIN mhcu_instrument mi ON mi.id_instrument = mis.id_instrument
			WHERE 1=1 " . $periode_cond . "
			GROUP BY mi.kode_instrument, mis.dimensi_aspek
			ORDER BY mi.no_urut, mis.dimensi_aspek", "result");

		$this->output->set_content_type('application/json')->set_output(json_encode(array('scores' => $scores)));
	}

	public function view($id)
	{
		$this->is_allowed('mhcu_sesi_view');
		$this->data['detail'] = $this->model_mhcu_sesi->get_sesi_detail($id);
		$this->template->title('Detail Sesi MHCU');
		$this->render('backend/standart/administrator/mhcu_sesi/mhcu_sesi_view', $this->data);
	}

	public function get_detail($id)
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$detail = $this->model_mhcu_sesi->get_sesi_detail($id);

		if (!empty($detail['sesi'])) {
			if (!empty($detail['sesi']->submitted_at)) {
				$detail['sesi']->submitted_at_formatted = formatTanggal($detail['sesi']->submitted_at) . ' ' . date('H:i', strtotime($detail['sesi']->submitted_at));
			}
			$this->output->set_content_type('application/json')->set_output(json_encode($detail));
		} else {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Data tidak ditemukan')));
		}
	}

	/**
	 * Export Excel multi-sheet (1 sheet per user)
	 * Wajib ada filter periode. Hanya sesi berstatus 'selesai' yang di-export.
	 */
	public function export()
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$periode_filter = $this->input->get('periode');

		// Wajib ada filter periode
		if (empty($periode_filter)) {
			$this->session->set_flashdata('error', 'Silahkan pilih filter periode terlebih dahulu sebelum export.');
			redirect_back();
			return;
		}

		$filters = array(
			'id_mhcu_periode' => $periode_filter,
			'presensi_role'    => $this->input->get('role'),
			'status'           => 'selesai', // hanya sesi selesai
			'kategori'         => $this->input->get('kategori'),
		);

		// Ambil semua sesi (no limit) pada filter tsb
		$sesis = $this->model_mhcu_sesi->get(null, null, 100000, 0, $filters);

		// Attach items, saran, demografis untuk tiap sesi
		$sesis = $this->_attach_export_data($sesis);

		// Info periode untuk header file
		$periode_info = $this->db->where('id_mhcu_periode', $periode_filter)->get('mhcu_periode')->row();
		$filename = 'monitoring_mhcu';
		if ($periode_info && !empty($periode_info->nama_periode)) {
			$slug = preg_replace('/[^A-Za-z0-9_\-]/', '_', $periode_info->nama_periode);
			$filename = 'monitoring_mhcu_' . strtolower($slug);
		}

		$this->load->library('mhcu_excel');
		$this->mhcu_excel->generate_sesi_export($sesis, $periode_info, $filename);
	}

	/**
	 * Attach data export (items, skor, hasil, unit/jabatan, demografi) ke tiap sesi.
	 * Dipakai export() multi-sheet dan export_excel() per-user.
	 */
	private function _attach_export_data($sesis)
	{
		foreach ($sesis as $sesi) {
			$sesi->items = $this->_get_sesi_items($sesi->id_mhcu_sesi);
			$sesi->export_scores = $this->db->query(
				"SELECT mi.kode_instrument, mis.dimensi_aspek, mis.skor, bk.warna
				FROM mhcu_instrument_score mis
				JOIN mhcu_instrument mi ON mi.id_instrument = mis.id_instrument
				LEFT JOIN mhcu_band_kategori bk
					ON bk.kode_instrument = mi.kode_instrument
					AND (
						 (bk.dimensi_aspek IS NULL AND mis.dimensi_aspek IN ('Kesejahteraan Psikologis','Depresi','Kecemasan','CBI Keseluruhan'))
					  OR (bk.dimensi_aspek = 'Total' AND mis.dimensi_aspek LIKE '%Total%')
					)
					AND mis.skor BETWEEN bk.batas_bawah AND bk.batas_atas
				WHERE mis.id_sesi = " . intval($sesi->id_mhcu_sesi) . "
				AND (
					 (mi.kode_instrument = 'WHO5' AND mis.dimensi_aspek = 'Kesejahteraan Psikologis')
				  OR (mi.kode_instrument = 'PHQ9' AND mis.dimensi_aspek = 'Depresi')
				  OR (mi.kode_instrument = 'GAD7' AND mis.dimensi_aspek = 'Kecemasan')
				  OR (mi.kode_instrument = 'CBI' AND mis.dimensi_aspek = 'CBI Keseluruhan')
				  OR (mi.kode_instrument IN ('PSIKOSOSIAL', 'PSIKOSOSIAL_PIMPINAN') AND mis.dimensi_aspek LIKE '%Total%')
				)"
			)->result();
			$hasil = $this->db->select('mhcu_hasil_individu.is_krisis, pk.label_profil AS kategori_keseluruhan, pk.saran_kategori AS saran_rekomendasi, pk.narasi_kategori')
				->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT')
				->where('id_sesi', $sesi->id_mhcu_sesi)->get('mhcu_hasil_individu')->row();
			$sesi->saran_rekomendasi = ($hasil && !empty($hasil->saran_rekomendasi)) ? $hasil->saran_rekomendasi : '';
			$sesi->narasi_kategori = ($hasil && !empty($hasil->narasi_kategori)) ? $hasil->narasi_kategori : '';
			$sesi->kategori_keseluruhan = ($hasil && !empty($hasil->kategori_keseluruhan)) ? $hasil->kategori_keseluruhan : '';
			$sesi->is_krisis = ($hasil && isset($hasil->is_krisis)) ? $hasil->is_krisis : 0;
			$info = $this->_get_unit_jabatan($sesi->npp, $sesi->presensi_role);
			$sesi->unit = $info['unit'];
			$sesi->jabatan = $info['jabatan'];

			// Load demografi peserta
			$sesi->demografi = $this->db
				->select('dp.demografi_pertanyaan, CASE WHEN sd.id_demografi_pertanyaan = 9 THEN sd.demografi_text_option ELSE COALESCE(do2.label_option, sd.demografi_text_option) END AS jawaban')
				->join('mhcu_demografi_pertanyaan dp', 'dp.id_demografi_pertanyaan = sd.id_demografi_pertanyaan')
				->join('mhcu_demografi_option do2', 'do2.id_demografi_option = sd.id_demografi_option', 'LEFT')
				->where('sd.id_sesi', $sesi->id_mhcu_sesi)
				->order_by('dp.no_urut', 'ASC')
				->get('mhcu_sesi_demografi sd')
				->result();
		}
		return $sesis;
	}

	/**
	 * Export Excel individual per sesi (1 sheet milik user tersebut).
	 */
	function export_excel($id_sesi)
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$sesi = $this->db->where('id_mhcu_sesi', intval($id_sesi))
			->where('deleted_at IS NULL', null, false)
			->get('mhcu_sesi')->row();
		if (empty($sesi)) {
			$this->session->set_flashdata('error', 'Data sesi tidak ditemukan.');
			redirect_back();
			return;
		}

		$sesis = $this->_attach_export_data(array($sesi));

		$periode_info = $this->db->where('id_mhcu_periode', $sesi->id_mhcu_periode)->get('mhcu_periode')->row();
		$nama = isset($sesi->nama_lengkap) ? $sesi->nama_lengkap : ('sesi_' . $id_sesi);
		$filename = 'MHCU_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nama);

		$this->load->library('mhcu_excel');
		$this->mhcu_excel->generate_sesi_export($sesis, $periode_info, $filename);
	}

	/**
	 * Cari unit & jabatan dari tabel guru/pegawai/pimpinan berdasarkan npp + presensi_role.
	 */
	private function _get_unit_jabatan($npp, $presensi_role)
	{
		$role_upper = strtoupper(trim($presensi_role));

		$role_map = array(
			'GURUFT'   => 'guru_ft',
			'GURUSD'   => 'guru_sd',
			'GURUSMP'  => 'guru_smp',
			'GURUSMA'  => 'guru_sma',
			'PEGAWAI'  => 'pegawai',
			'STAFF'    => 'pegawai',
			'SD'       => 'pimpinan_sd',
			'SMP'      => 'pimpinan_smp',
			'SMA'      => 'pimpinan_sma',
			'PIMPINAN' => 'pimpinan',
		);

		$table = isset($role_map[$role_upper]) ? $role_map[$role_upper] : '';
		$unit = '';
		$jabatan = '';

		if (!empty($table)) {
			$this->db->where('npp', $npp);
			// pimpinan tidak punya kolom deleted_at
			if ($table !== 'pimpinan') {
				$this->db->where('deleted_at IS NULL', null, false);
			}
			$row = $this->db->get($table)->row();
			if ($row) {
				$unit = isset($row->unit) ? $row->unit : '';
				$jabatan = isset($row->keterangan_jabatan) ? $row->keterangan_jabatan : '';
			}
		}

		return array(
			'unit' => !empty($unit) ? $unit : $presensi_role,
			'jabatan' => !empty($jabatan) ? $jabatan : '-',
		);
	}

	/**
	 * Resolve presensi_role → target_role (backend helper)
	 */
	private function _resolve_target_role_backend($presensi_role)
	{
		$role_upper = strtoupper(trim($presensi_role));
		$pimpinan_roles = array('SD', 'SMP', 'SMA', 'PIMPINAN');
		if (in_array($role_upper, $pimpinan_roles)) {
			return 'pimpinan';
		}
		return 'guru_karyawan';
	}

	/**
	 * Lookup band kategori dari tabel mhcu_band_kategori.
	 */
	private function _lookup_band_kategori($kode_instrument, $dimensi_aspek, $skor)
	{
		$where_aspek = ($dimensi_aspek === null)
			? 'dimensi_aspek IS NULL'
			: "dimensi_aspek = '" . $this->db->escape_str($dimensi_aspek) . "'";

		return $this->db->query(
			"SELECT label_kategori, warna, deskripsi FROM mhcu_band_kategori
			 WHERE kode_instrument = '" . $this->db->escape_str($kode_instrument) . "'
			 AND " . $where_aspek . "
			 AND " . floatval($skor) . " BETWEEN batas_bawah AND batas_atas
			 LIMIT 1"
		)->row();
	}

	/**
	 * Bangun data array untuk template PDF dari 1 sesi.
	 */
	private function _build_pdf_data($id_sesi)
	{
		$sesi = $this->db->where('id_mhcu_sesi', $id_sesi)->get('mhcu_sesi')->row();
		if (empty($sesi)) return null;

		$periode = $this->db->where('id_mhcu_periode', $sesi->id_mhcu_periode)->get('mhcu_periode')->row();
		$nama_periode = ($periode && !empty($periode->nama_periode)) ? $periode->nama_periode : '';

		$hasil = $this->db->select('mhcu_hasil_individu.*, pk.label_profil AS kategori_keseluruhan, pk.saran_kategori AS saran_rekomendasi, pk.narasi_kategori, pk.warna AS kategori_warna, pk.emoji AS kategori_emoji')
			->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT')
			->where('mhcu_hasil_individu.id_sesi', $id_sesi)->get('mhcu_hasil_individu')->row();
		$scores = $this->db
			->select('mhcu_instrument_score.*, mhcu_instrument.nama_instrument, mhcu_instrument.kode_instrument')
			->join('mhcu_instrument', 'mhcu_instrument.id_instrument = mhcu_instrument_score.id_instrument', 'LEFT')
			->where('mhcu_instrument_score.id_sesi', $id_sesi)
			->order_by('mhcu_instrument.no_urut', 'ASC')
			->get('mhcu_instrument_score')->result();

		$info = $this->_get_unit_jabatan($sesi->npp, $sesi->presensi_role);

		$tanggal = '-';
		if (!empty($sesi->submitted_at)) {
			$tanggal = formatTanggal($sesi->submitted_at);
		}

		// Ambil gender dari mhcu_sesi_demografi (id_demografi_pertanyaan = 2)
		$gender_row = $this->db->query(
			"SELECT do.label_option
			 FROM mhcu_sesi_demografi sd
			 LEFT JOIN mhcu_demografi_option do ON do.id_demografi_option = sd.id_demografi_option
			 WHERE sd.id_sesi = " . intval($id_sesi) . "
			 AND sd.id_demografi_pertanyaan = 2
			 LIMIT 1"
		)->row();
		// gender kosong (demografi tidak ditemukan) → '' → sapaan netral "Bapak/Ibu" via mhcu_sapaan()
		$gender = (!empty($gender_row) && strtolower(trim($gender_row->label_option)) === 'perempuan') ? 'P' : ((!empty($gender_row) && trim($gender_row->label_option) !== '') ? 'L' : '');

		// Jabatan dari mhcu_sesi_demografi (id_demografi_pertanyaan = 5)
		$jabatan_row = $this->db->query(
			"SELECT do.label_option
			 FROM mhcu_sesi_demografi sd
			 LEFT JOIN mhcu_demografi_option do ON do.id_demografi_option = sd.id_demografi_option
			 WHERE sd.id_sesi = " . intval($id_sesi) . "
			 AND sd.id_demografi_pertanyaan = 5
			 LIMIT 1"
		)->row();
		$jabatan = ($jabatan_row && !empty($jabatan_row->label_option)) ? $jabatan_row->label_option : (!empty($info['jabatan']) ? $info['jabatan'] : '-');

		// Extract nama depan
		$nama_depan = mhcu_extract_nama_depan($sesi->nama_lengkap);

		// Index scores by dimensi_aspek
		$skor_map = array();
		foreach ($scores as $sc) {
			$skor_map[$sc->dimensi_aspek] = $sc;
		}

		// Mapping profil_mental: lookup warna & deskripsi dari mhcu_band_kategori
		$profil_map = array(
			'Kesejahteraan Psikologis' => 'Mental Wellbeing',
			'Depresi' => 'Depression Risk',
			'Kecemasan' => 'Anxiety Risk',
			'CBI Keseluruhan' => 'Burnout Risk',
		);
		$inst_map = array(
			'Mental Wellbeing' => 'WHO5',
			'Depression Risk' => 'PHQ9',
			'Anxiety Risk' => 'GAD7',
			'Burnout Risk' => 'CBI',
		);

		$profil_mental = array();
		foreach ($profil_map as $dimensi => $label) {
			$sc = isset($skor_map[$dimensi]) ? $skor_map[$dimensi] : null;
			$kategori = $sc ? $sc->kategori : '-';
			$skor_val = $sc ? floatval($sc->skor) : 0;
			$kode = isset($inst_map[$label]) ? $inst_map[$label] : '';
			$band = !empty($kode) ? $this->_lookup_band_kategori($kode, null, $skor_val) : null;
			$sw = $band ? $band->warna : 'hijau';
			$deskripsi = $band ? $band->deskripsi : $kategori;
			$profil_mental[] = array(
				'aspek' => $label,
				'status_warna' => $sw,
				'kategori' => $kategori,
				'deskripsi' => $deskripsi,
			);
		}

		// Psikososial: role-aware, dengan skor/skor_maks/persentase + deskripsi dari band_kategori
		$target_role = $this->_resolve_target_role_backend($sesi->presensi_role);

		if ($target_role == 'pimpinan') {
			$psiko_aspek = array('Leadership Capacity & Workload', 'Organizational support', 'Psychological safety', 'Work life balance', 'Leadership Meaning');
			$kode_psiko = 'PSIKOSOSIAL_PIMPINAN';
		} else {
			$psiko_aspek = array('Job Demand', 'Meaning & Engagement', 'Leadership Support', 'Team Support', 'Psychological Safety', 'Resources & Role Clarity');
			$kode_psiko = 'PSIKOSOSIAL';
		}

		$psikososial = array();
		foreach ($psiko_aspek as $asp) {
			$sc = isset($skor_map[$asp]) ? $skor_map[$asp] : null;
			$kategori = $sc ? $sc->kategori : '-';
			$skor_val = $sc ? floatval($sc->skor) : 0;
			// Cari max dari band_kategori
			$max_row = $this->db->query(
				"SELECT MAX(batas_atas) AS skor_maks FROM mhcu_band_kategori
				 WHERE kode_instrument = '" . $this->db->escape_str($kode_psiko) . "'
				 AND dimensi_aspek = '" . $this->db->escape_str($asp) . "'"
			)->row();
			$skor_maks = ($max_row && $max_row->skor_maks) ? floatval($max_row->skor_maks) : 12;
			$pct = ($skor_maks > 0) ? round($skor_val / $skor_maks * 100, 1) : 0;
			$band = $this->_lookup_band_kategori($kode_psiko, $asp, $skor_val);
			$sw = $band ? $band->warna : 'hijau';
			$deskripsi = $band ? $band->deskripsi : '';
			$psikososial[] = array(
				'aspek' => $asp,
				'status_warna' => $sw,
				'kategori' => $kategori,
				'deskripsi' => $deskripsi,
				'skor' => $skor_val,
				'skor_maks' => $skor_maks,
				'persentase' => $pct,
			);
		}

		// Kesimpulan: ambil langsung dari mhcu_prokol_kategori via join
		$kategori_kes = ($hasil && !empty($hasil->kategori_keseluruhan)) ? $hasil->kategori_keseluruhan : '';
		$saran = ($hasil && !empty($hasil->saran_rekomendasi)) ? $hasil->saran_rekomendasi : '';
		$kes_emoji = ($hasil && !empty($hasil->kategori_emoji)) ? $hasil->kategori_emoji : '';
		$kes_warna = ($hasil && !empty($hasil->kategori_warna)) ? $hasil->kategori_warna : 'kuning';
		$kes_label = $kategori_kes;

		$warna_hex = array(
			'hijau_tua'   => '#538135',
			'hijau_muda'  => '#A8D08D',
			'hijau_pudar' => '#E2EFD9',
			'kuning'      => '#FFC000',
			'coklat_orange' => '#806000',
			'orange_tua'  => '#C45911',
			'orange_muda' => '#F4B083',
			'merah_muda'  => '#BD5163',
			'merah_tua'   => '#C00000',
		);

		$hex = isset($warna_hex[$kes_warna]) ? $warna_hex[$kes_warna] : '#999999';

		$narasi = ($hasil && !empty($hasil->narasi_kategori)) ? $hasil->narasi_kategori : '';
		$kesimpulan = array(
			'warna' => $kes_warna,
			'profil_warna' => $hex,
			'emoji' => $kes_emoji,
			'label' => $kes_label,
			'deskripsi_profil' => $narasi,
			'rekomendasi' => $saran,
		);

		$care_profiles = array(
			'Immediate Professional Follow-up',
			'Burnout Dominant',
			'Burnout with emotional distress',
			'Emotional Distress',
		);
		$show_mhcu_care = in_array($kategori_kes, $care_profiles, TRUE);
		$mhcu_care_url = '';
		if ($show_mhcu_care) {
			$care_token = md5(intval($id_sesi) . MHCU_CARE_TOKEN_SALT);
			$mhcu_care_url = base_url('mhcu_care?token=' . urlencode($care_token) . '&id_sesi=' . intval($id_sesi));
		}

		return array(
			'nama' => $sesi->nama_lengkap,
			'nama_depan' => $nama_depan,
			'gender' => $gender,
			'unit' => $info['unit'],
			'jabatan' => $jabatan,
			'tanggal_pemeriksaan' => $tanggal,
			'periode' => $nama_periode,
			'kategori_keseluruhan' => $kategori_kes,
			'kategori_warna' => ($hasil && !empty($hasil->kategori_warna)) ? $hasil->kategori_warna : '',
			'is_krisis' => ($hasil && isset($hasil->is_krisis)) ? $hasil->is_krisis : 0,
			'saran_rekomendasi' => $saran,
			'profil_mental' => $profil_mental,
			'psikososial' => $psikososial,
			'kesimpulan' => $kesimpulan,
			'show_mhcu_care' => $show_mhcu_care,
			'mhcu_care_url' => $mhcu_care_url,
		);
	}

	/**
	 * Export PDF individual per sesi.
	 */
	function export_pdf($id_sesi)
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$data = $this->_build_pdf_data($id_sesi);
		if (!$data) {
			$this->session->set_flashdata('error', 'Data sesi tidak ditemukan.');
			redirect_back();
			return;
		}

		$this->load->library('HtmlPdf');
		$pdf = new HTML2PDF('P', 'A4', 'en');
		ob_start();
		$this->load->view('template_mhcu_view', $data);
		$html = ob_get_contents();
		ob_end_clean();

		$pdf->WriteHTML($html);
		$nama_file = 'MHCU_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['nama']) . '.pdf';
		$pdf->Output($nama_file, 'I');
		exit();
	}

	/**
	 * Export PDF bulk (selected sesi) -> ZIP.
	 * Menerima POST 'ids' (array of id_mhcu_sesi).
	 */
	function export_pdf_bulk()
	{
		if (!$this->is_allowed('mhcu_sesi_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$ids = $this->input->post('ids');
		if (empty($ids) || !is_array($ids)) {
			$this->session->set_flashdata('error', 'Pilih minimal satu sesi untuk di-export.');
			redirect_back();
			return;
		}

		$tmp = tempnam(sys_get_temp_dir(), 'mhcu_') . '.zip';
		$zip = new ZipArchive();
		if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
			$this->session->set_flashdata('error', 'Gagal membuat file ZIP.');
			redirect_back();
			return;
		}

		$this->load->library('HtmlPdf');
		$pdf_gen = new HTML2PDF('P', 'A4', 'en');
		$count = 0;

		foreach ($ids as $id_sesi) {
			$id_sesi = intval($id_sesi);
			if ($id_sesi <= 0) continue;

			$data = $this->_build_pdf_data($id_sesi);
			if (!$data) continue;

			ob_start();
			$this->load->view('template_mhcu_view', $data);
			$html = ob_get_contents();
			ob_end_clean();

			$pdf_gen->WriteHTML($html);
			$pdf_content = $pdf_gen->Output('', 'S');

			$nama_file = 'MHCU_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['nama']) . '.pdf';
			$zip->addFromString($nama_file, $pdf_content);
			$count++;
		}

		$zip->close();

		if ($count === 0) {
			unlink($tmp);
			$this->session->set_flashdata('error', 'Tidak ada data sesi yang valid untuk di-export.');
			redirect_back();
			return;
		}

		$zip_name = 'MHCU_Report_' . date('Ymd_His') . '.zip';
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $zip_name . '"');
		header('Content-Length: ' . filesize($tmp));
		readfile($tmp);
		unlink($tmp);
		exit();
	}

	/**
	 * Ambil list item jawaban untuk 1 sesi, JOIN dengan skala_option untuk label jawaban.
	 */
	private function _get_sesi_items($id_sesi)
	{
		$rows = $this->db
			->select('mi.kode_instrument, mi.nama_instrument, mii.text_item, mii.dimensi_aspek, mii.no_urut_item, mii.is_reversed, msi.id_skala_option, mso.label_option, mso.skor_value, msi.text_value')
			->from('mhcu_sesi_instrument msi')
			->join('mhcu_instrument_item mii', 'mii.id_instrument_item = msi.id_instrument_item', 'LEFT')
			->join('mhcu_instrument mi', 'mi.id_instrument = msi.id_instrument', 'LEFT')
			->join('mhcu_skala_option mso', 'mso.id_skala_option = msi.id_skala_option', 'LEFT')
			->where('msi.id_sesi', $id_sesi)
			->order_by('mi.no_urut', 'ASC')
			->order_by('mii.no_urut_item', 'ASC')
			->get()
			->result();

		// Build jawaban_text
		foreach ($rows as $r) {
			if (!empty($r->label_option)) {
				$r->jawaban_text = $r->label_option;
			} elseif (!empty($r->text_value)) {
				$r->jawaban_text = $r->text_value;
			} else {
				$r->jawaban_text = '-';
			}
		}

		return $rows;
	}

	/**
	 * Re-calculate skor 1 sesi (id_mhcu_sesi).
	 * Dipakai setelah fix bug penilaian untuk refresh hasil lama.
	 * GET /administrator/mhcu_sesi/recalculate?id_sesi=<id>
	 * Pakai query string (bukan URI segment) karena routing HMVC
	 * memblokir method baru dengan argumen posisi.
	 * Response JSON untuk AJAX.
	 */
	function recalculate()
	{
		$id_sesi = intval($this->input->get('id_sesi'));
		if (!$this->is_allowed('mhcu_sesi_update', false)) {
			echo json_encode(array('status' => 0, 'message' => 'Tidak punya akses.'));
			return;
		}
		if (!$id_sesi) {
			echo json_encode(array('status' => 0, 'message' => 'id_sesi kosong.'));
			return;
		}

		$sesi = $this->db->where('id_mhcu_sesi', $id_sesi)->get('mhcu_sesi')->row();
		if (empty($sesi)) {
			echo json_encode(array('status' => 0, 'message' => 'Sesi tidak ditemukan.'));
			return;
		}

		// Bypass guard 'status=selesai' di endpoint app: izinkan recalc untuk admin.
		$sesi_was_selesai = ($sesi->status === 'selesai');
		if (!$sesi_was_selesai) {
			$this->db->where('id_mhcu_sesi', $id_sesi)->update('mhcu_sesi', array('status' => 'selesai'));
		}

		$this->load->library('Mhcu_scoring');
		$res = $this->mhcu_scoring->hitung($id_sesi);

		echo json_encode(array(
			'status'  => !empty($res['success']) ? 1 : 0,
			'message' => isset($res['message']) ? $res['message'] : 'Selesai',
			'data'    => isset($res['data']) ? $res['data'] : array(),
		));
	}

	/**
	 * Re-calculate skor untuk SEMUA sesi selesai dalam 1 periode.
	 * GET /administrator/mhcu_sesi/recalculate_periode?id_periode=<id>
	 * Pakai query string (bukan URI segment) karena routing HMVC
	 * memblokir method baru dengan argumen posisi.
	 * Response JSON.
	 */
	function recalculate_periode()
	{
		$id_periode = intval($this->input->get('id_periode'));
		if (!$this->is_allowed('mhcu_sesi_update', false)) {
			echo json_encode(array('status' => 0, 'message' => 'Tidak punya akses.'));
			return;
		}

		$id_periode = intval($id_periode);
		if (!$id_periode) {
			echo json_encode(array('status' => 0, 'message' => 'id_periode kosong.'));
			return;
		}

		$periode = $this->db->where('id_mhcu_periode', $id_periode)->get('mhcu_periode')->row();
		if (empty($periode)) {
			echo json_encode(array('status' => 0, 'message' => 'Periode tidak ditemukan.'));
			return;
		}

		$this->load->library('Mhcu_scoring');

		$rows = $this->db->where('id_mhcu_periode', $id_periode)
			->where('status', 'selesai')
			->where('deleted_at IS NULL', null, false)
			->order_by('id_mhcu_sesi', 'ASC')
			->get('mhcu_sesi')->result();

		$success = 0;
		$failed  = 0;
		$by_cat  = array();
		foreach ($rows as $r) {
			$res = $this->mhcu_scoring->hitung($r->id_mhcu_sesi);
			if (!empty($res['success'])) {
				$success++;
				$cat = isset($res['data']['kode_profil']) ? $res['data']['kode_profil'] : '-';
				if (!isset($by_cat[$cat])) $by_cat[$cat] = 0;
				$by_cat[$cat]++;
			} else {
				$failed++;
			}
		}

		echo json_encode(array(
			'status'  => 1,
			'message' => 'Recalculate periode "' . $periode->nama_periode . '" selesai. Berhasil: ' . $success . ', Gagal: ' . $failed,
			'data'    => array(
				'id_periode'      => $id_periode,
				'nama_periode'    => $periode->nama_periode,
				'total'           => count($rows),
				'success'         => $success,
				'failed'          => $failed,
				'distribution'    => $by_cat,
			),
		));
	}

}

/* End of file Mhcu_sesi.php */
