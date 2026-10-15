<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lamaran (draft/submit/daftar/detail) milik kandidat.
 *
 * Pola URL REST:
 *   POST   apicareer/Lamaran            (buat draft; body id_job_posting)
 *   GET    apicareer/Lamaran            (daftar milik sendiri)
 *   GET    apicareer/Lamaran/detail/<id>  (detail + persyaratan + 5 tahap; TANPA
 *          score/note_internal/review_note/rubric_note — kolom SENSITIF tidak
 *          pernah dikembalikan ke kandidat)
 *   POST   apicareer/Lamaran/submit     (body id_application; transaksi
 *          kunci FOR UPDATE — anti-race submit paralel dan upload bersamaan)
 */

class Lamaran extends Career_base {

	// ------------------------------------------------------------------
	public function index_post()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id_posting = (int) $this->post('id_job_posting');
		if ($id_posting < 1)
		{
return $this->_err('id_job_posting wajib diisi.', 400);
		}

		// M8: lowongan tidak terbuka/tutup → 409 (sesuai kontrak), bukan 404.
		$posting = $this->_get_open_posting($id_posting);
		if ($posting === NULL)
		{
return $this->_err('Lowongan tidak terbuka (belum dibuka atau sudah ditutup).', 409);
		}

		// M6: tanpa cek-dulu-then-insert yang bisa race — langsung insert dan tangkap
		// duplicate key (uq_applications_candidate_posting) → 409.
		$reqs = $this->db->select('id_job_posting_requirement')
			->where('id_job_posting', $id_posting)
			->order_by('display_order, id_job_posting_requirement')
			->get('career_job_posting_requirements')->result_array();

		$id_app = 0;
		try
		{
			$this->db->trans_start();
			$insert_ok = $this->db->insert('career_applications', array(
				'id_candidate'   => $candidate->id_candidate,
				'id_job_posting' => $id_posting,
				'status'         => 'draft',
				'current_stage_id' => NULL,
			));
			// db_debug=FALSE: error() hanya valid SEKARANG (ROLLBACK menimpanya).
			$err_code = (int) $this->db->error()['code'];
			$id_app = (int) $this->db->insert_id();
			if ($insert_ok !== FALSE)
			{
				foreach ($reqs as $r)
				{
					// konfigurasi transaksi disalin saat draft (status awal not_uploaded)
					$this->db->insert('career_application_requirements', array(
						'id_application'             => $id_app,
						'id_job_posting_requirement' => (int) $r['id_job_posting_requirement'],
						'status'                     => 'not_uploaded',
					));
				}
			}
			$this->db->trans_complete();
		}
		catch (Exception $e)
		{
			$this->db->trans_complete();
		}

		if ($this->db->trans_status() === FALSE)
		{
			if ($err_code === 1062)
			{
return $this->_err('Kandidat sudah punya lamaran untuk lowongan ini (satu lamaran per lowongan).', 409);
			}
			log_message('error', 'career/lamaran draft gagal untuk posting ' . $id_posting);
return $this->_err('Gagal membuat draft lamaran. Coba lagi.', 500);
		}

		$this->_audit('buat_draft_lamaran', 'career_applications', $id_app);
		$this->_ok(array(
			'id_application' => $id_app,
			'status'         => 'draft',
			'title'          => $posting->title,
		), 'Draft lamaran dibuat.', 201);
	}

	// ------------------------------------------------------------------
	public function index_get()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$rows = $this->db->select('a.id_application, a.status, a.submitted_at, a.created_at, p.title AS posting_title, p.closing_date')
			->from('career_applications a')
			->join('career_job_postings p', 'p.id_job_posting = a.id_job_posting')
			->where('a.id_candidate', $candidate->id_candidate)
			->order_by('a.id_application', 'DESC')
			->get()->result_array();
		foreach ($rows as &$r) { $r['id_application'] = (int) $r['id_application']; }
		unset($r); // jebakan referensi foreach
		$this->_ok($rows, 'OK');
	}

	// ------------------------------------------------------------------
	public function detail_get($id = 0)
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$app = $this->_own_application($id);
		if ($app === NULL) return;

		// Persyaratan + status dokumen versi terbaru + jawaban non-berkas
		$reqs = $this->db->select('jr.id_job_posting_requirement, jr.requirement_name, jr.requirement_type, jr.input_type, jr.allowed_formats, jr.max_file_size_kb, ar.id_application_requirement, ar.status AS requirement_status, ar.answer_text')
			->from('career_application_requirements ar')
			->join('career_job_posting_requirements jr', 'jr.id_job_posting_requirement = ar.id_job_posting_requirement')
			->where('ar.id_application', (int) $app->id_application)
			->order_by('jr.display_order')
			->get()->result_array();

		// dokumen versi terbaru per persyaratan (metadata saja; unduh lewat endpoint dokumen)
		foreach ($reqs as $i => $r)
		{
			$reqs[$i]['id_application_requirement'] = (int) $r['id_application_requirement'];
			$reqs[$i]['upload_url'] = 'apicareer/Lamaran_dokumen/upload';
			if ($this->_req_is_document($r['input_type']))
			{
				$doc = $this->db->select('id_application_document, version, original_name, mime_type, file_size, uploaded_at')
					->where('id_application_requirement', $r['id_application_requirement'])
					->order_by('version', 'DESC')
					->get('career_application_documents')->row_array();
				if ($doc !== NULL)
				{
					$doc['id_application_document'] = (int) $doc['id_application_document'];
					$reqs[$i]['latest_document'] = $doc;
					$reqs[$i]['download_url'] = 'apicareer/Lamaran_dokumen/detail/' . $doc['id_application_document'];
				}
				else { $reqs[$i]['latest_document'] = NULL; }
			}
		}

		// 5 tahap + jadwal terpublikasi (gamblang: TANPA score/etc)
		$stages = $this->_stages_safe($app);

		$posting = $this->db->where('id_job_posting', (int) $app->id_job_posting)->get('career_job_postings')->row();

		$this->_ok(array(
			'id_application'      => (int) $app->id_application,
			'status'              => $app->status,
			'submitted_at'        => $app->submitted_at,
			'current_stage_id'    => $app->current_stage_id !== NULL ? (int) $app->current_stage_id : NULL,
			'id_job_posting'      => (int) $app->id_job_posting,
			'posting_title'       => $posting !== NULL ? $posting->title : $app->posting_title,
			'closing_date'        => $posting !== NULL ? $posting->closing_date : NULL,
			'requirements'        => $reqs,
			'stages'              => $stages,
		), 'OK');
	}

	// ------------------------------------------------------------------
	public function submit_post()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id_app = (int) $this->post('id_application');
		$app = $this->_own_application($id_app, TRUE);
		if ($app === NULL) return;

		$posting = $this->_get_open_posting((int) $app->id_job_posting);
		if ($posting === NULL)
		{
return $this->_err('Lowongan sudah ditutup; lamaran tidak dapat dikirim.', 409);
		}

		// M10: validasi tahap SEBELUM transaksi — tepat 5 tahap aktif,
		// pesan 500 jelas, bukan undefined index di tengah transaksi.
		$stage_by_order = $this->_stage_ids();
		if (count($stage_by_order) !== 5
			OR ! isset($stage_by_order[1], $stage_by_order[2], $stage_by_order[3], $stage_by_order[4], $stage_by_order[5]))
		{
			log_message('error', 'career/lamaran submit: career_stages tidak tepat 5 tahap aktif.');
return $this->_err('Konfigurasi tahap seleksi tidak lengkap (harus tepat 5 tahap aktif). Hubungi admin.', 500);
		}

		// Snapshot profil + item (berbeda dari profil hidup — FE mengambil dari
		// lamaran) + kunci baris lamaran supaya submit paralel dan upload
		// bersamaan tidak bisa lolos bersamaan (H2).
		$this->db->trans_start();

		// Kunci baris lamaran (SELECT ... FOR UPDATE dengan binding).
		$app = $this->db->query('SELECT a.*, p.title AS posting_title FROM career_applications a JOIN career_job_postings p ON p.id_job_posting = a.id_job_posting WHERE a.id_application = ? AND a.id_candidate = ? FOR UPDATE', array((int) $id_app, (int) $candidate->id_candidate))->row();
		if ($app === NULL)
		{
			$this->db->trans_complete();
return $this->_err('Lamaran tidak ditemukan.', 404);
		}
		if ($app->status !== 'draft')
		{
			$this->db->trans_complete();
return $this->_err('Lamaran sudah terkunci (status bukan draft).', 409);
		}

		// M6: sinkronkan persyaratan lowongan yang belum tersalin (admin
		// menambah persyaratan setelah draft dibuat tidak boleh lolos begitu saja).
		$salinan = $this->db->select('id_job_posting_requirement')
			->where('id_application', (int) $app->id_application)
			->get('career_application_requirements')->result_array();
		$ada = array();
		foreach ($salinan as $s) { $ada[(int) $s['id_job_posting_requirement']] = TRUE; }
		$kurang = $this->db->select('id_job_posting_requirement')
			->where('id_job_posting', (int) $app->id_job_posting)
			->get('career_job_posting_requirements')->result_array();
		foreach ($kurang as $k)
		{
			$id_req = (int) $k['id_job_posting_requirement'];
			if ( ! isset($ada[$id_req]))
			{
				$this->db->insert('career_application_requirements', array(
					'id_application'             => $app->id_application,
					'id_job_posting_requirement' => $id_req,
					'status'                     => 'not_uploaded',
				));
			}
		}

		// Kelengkapan (cek DALAM transaksi, setelah kunci): persyaratan
		// required wajib punya berkas (H1: file ATAU document) / jawaban non-empty.
		$reqs = $this->db->select('jr.requirement_type, jr.input_type, ar.id_application_requirement, ar.answer_text')
			->from('career_application_requirements ar')
			->join('career_job_posting_requirements jr', 'jr.id_job_posting_requirement = ar.id_job_posting_requirement')
			->where('ar.id_application', (int) $app->id_application)
			->get()->result();
		$missing = 0;
		foreach ($reqs as $r)
		{
			if ($r->requirement_type !== 'required') continue;
			if ($this->_req_is_document($r->input_type))
			{
				$ndocs = $this->db->where('id_application_requirement', $r->id_application_requirement)
					->count_all_results('career_application_documents');
				if ($ndocs === 0)
				{
					$missing++;
				}
			}
			elseif (trim((string) $r->answer_text) === '')
			{
				$missing++;
			}
		}
		if ($missing > 0)
		{
			$this->db->trans_complete();
return $this->_err('Persyaratan wajib belum lengkap (' . $missing . ' belum terpenuhi). Submit diblokir.', 422);
		}

		// L9: snapshot kolom eksplisit — TANPA attachment_path/created_at/id.
		$profile = $this->db
			->select('phone, place_of_birth, date_of_birth, gender, address, summary')
			->where('id_candidate', $candidate->id_candidate)
			->get('career_candidate_profiles')->row_array();
		$items = $this->db->select('item_type, title, institution, start_date, end_date, description, extra_json, display_order')
			->where('id_candidate', $candidate->id_candidate)
			->order_by('item_type, display_order')
			->get('career_candidate_items')->result_array();
		$candidate_row = $this->db->select('email, full_name')
			->where('id_candidate', $candidate->id_candidate)->get('career_candidates')->row_array();

		$this->db->where('id_application', $app->id_application)
			->delete('career_application_profile_snapshots'); // rerun submit draft jadi idempotent
		$this->db->insert('career_application_profile_snapshots', array(
			'id_application' => $app->id_application,
			'profile_json'   => json_encode(array(
				'candidate' => $candidate_row,
				'profile'   => $profile,
				'items'     => $items,
			)),
			'snapshot_at'    => date('Y-m-d H:i:s'),
		));

		// Mulai ulang baris tahap (idempotent juga) — 5 tahap, urutan data.
		$this->db->where('id_application', $app->id_application)
			->delete('career_application_stages');
		$orders = array(
			$stage_by_order[1] => 1, $stage_by_order[2] => 2, $stage_by_order[3] => 3,
			$stage_by_order[4] => 4, $stage_by_order[5] => 5,
		);
		foreach ($orders as $id_stage => $order)
		{
			$this->db->insert('career_application_stages', array(
				'id_application' => $app->id_application,
				'id_stage'       => $id_stage,
				'status'         => 'pending',
			));
		}

		$ok_update = $this->db->where('id_application', $app->id_application)
			->where('status', 'draft')
			->update('career_applications', array(
				'status'           => 'submitted',
				'submitted_at'     => date('Y-m-d H:i:s'),
				'current_stage_id' => $stage_by_order[1],
			));
		$n_affected = $this->db->affected_rows();
		$this->db->trans_complete();

		if ($ok_update === FALSE OR $this->db->trans_status() === FALSE)
		{
			log_message('error', 'career/lamaran submit gagal untuk aplikasi ' . $id_app);
return $this->_err('Gagal submit lamaran. Coba lagi.', 500);
		}
		// H2: UPDATE ... WHERE status='draft' — affected_rows()==0 berarti
		// status sudah berubah (los submit paralel) → 409, bukan 200.
		if ($n_affected === 0)
		{
return $this->_err('Lamaran sudah terkunci (submitted oleh sesi lain).', 409);
		}

		$this->_audit('submit_lamaran', 'career_applications', $app->id_application);
		$this->_notify('submit_lamaran', 'Lamaran dikirim', 'Lamaran Anda telah dikirim untuk proses Seleksi Berkas.');

		$this->_ok(array('id_application' => (int) $app->id_application, 'status' => 'submitted'),
			'Lamaran dikirim. Lamaran terkunci.', 200);
	}

	// ------------------------------------------------------------------
	/** Urutan tahap berdasarkan stage_order (data, bukan hardcode). */
	protected function _stage_ids()
	{
		$rows = $this->db->select('id_stage, stage_order')
			->where('is_active', 1)
			->order_by('stage_order')
			->get('career_stages')->result_array();
		$by_order = array();
		foreach ($rows as $r) { $by_order[(int) $r['stage_order']] = (int) $r['id_stage']; }
		return $by_order;
	}

	/** H1: tipe input berkas = file ATAU document (default skema). */
	protected function _req_is_document($input_type)
	{
		return ($input_type === 'file' OR $input_type === 'document');
	}

	/** Tahap yang AMAN ditampilkan ke kandidat (tanpa score/note_internal). */
	protected function _stages_safe($app)
	{
		$rows = $this->db->select('s.stage_order, s.name, s.mode, st.status, st.scheduled_at, st.decided_at')
			->from('career_application_stages st')
			->join('career_stages s', 's.id_stage = st.id_stage')
			->where('st.id_application', (int) $app->id_application)
			->order_by('s.stage_order')
			->get()->result_array();
		$out = array();
		foreach ($rows as $r)
		{
			$out[] = array(
				'stage_order'  => (int) $r['stage_order'],
				'name'         => $r['name'],
				'mode'         => $r['mode'],
				'status'       => $r['status'],
				'scheduled_at' => $r['scheduled_at'],
				// published schedule detail (is_published=1) — channel/note
				'schedule'     => $this->_published_schedule($r['stage_order'], $app),
			);
		}
		return $out;
	}

	protected function _published_schedule($stage_order, $app)
	{
		$sched = $this->db->select('cs.schedule_datetime, cs.channel, cs.note')
			->from('career_selection_schedules cs')
			->join('career_application_stages st', 'st.id_application_stage = cs.id_application_stage', 'inner')
			->join('career_stages s', 's.id_stage = st.id_stage', 'inner')
			->where('cs.is_published', 1)
			->where('st.id_application', (int) $app->id_application)
			->where('s.stage_order', (int) $stage_order)
			->order_by('cs.id_selection_schedule', 'DESC')
			->get()->row();
		if ($sched === NULL) return NULL;
		return array(
			'schedule_datetime' => $sched->schedule_datetime,
			'channel'           => $sched->channel,
			'note'              => $sched->note,
		);
	}

	protected function _get_open_posting($id_posting)
	{
		$posting = $this->db->where('id_job_posting', (int) $id_posting)->get('career_job_postings')->row();
		if ($posting === NULL) return NULL;
		return $this->_posting_is_open($posting) ? $posting : NULL;
	}
}
