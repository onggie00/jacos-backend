<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dokumen lamaran — upload per persyaratan + unduh (pemilik saja).
 *
 * Folder privat: application/storage/career/ (dilindungi .htaccess Deny;
 * file_path disimpan relatif). Nama file acak, versi+1 per persyaratan,
 * checksum sha256 (dupe = idempotent, TANPA UNIQUE DB — divalidasi manual,
 * keputusan Fase 4.5). Upload memakai kunci baris lamaran yang sama dengan
 * submit (FOR UPDATE) supaya tidak bisa selip di tengah submit.
 */

class Lamaran_dokumen extends Career_base {

	/** Map ekstensi → MIME persis (M3: fail-closed; ekstensi di luar map ditolak). */
	protected $_mime_map = array(
		'PDF'  => 'application/pdf',
		'JPG'  => 'image/jpeg',
		'JPEG' => 'image/jpeg',
		'PNG'  => 'image/png',
	);

	public function upload_post()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id_app_req = (int) $this->post('id_application_requirement');
		if ($id_app_req < 1)
		{
return $this->_err('id_application_requirement wajib diisi.', 400);
		}

		// Scope: requirement MILIK kandidat ini; item milik kandidat lain → 404.
		$req = $this->db->select('ar.id_application_requirement, ar.id_application, ar.id_job_posting_requirement, ar.status, a.id_candidate, a.status AS app_status, jr.requirement_name, jr.allowed_formats, jr.max_file_size_kb, jr.input_type')
			->from('career_application_requirements ar')
			->join('career_applications a', 'a.id_application = ar.id_application')
			->join('career_job_posting_requirements jr', 'jr.id_job_posting_requirement = ar.id_job_posting_requirement')
			->where('ar.id_application_requirement', $id_app_req)
			->where('a.id_candidate', (int) $this->career_candidate_id)
			->get()->row();
		// item lamaran milik kandidat lain → 404 (anti IDOR)
		if ($req === NULL)
		{
return $this->_err('Persyaratan lamaran tidak ditemukan.', 404);
		}

		// M3: tolak upload ke persyaratan non-berkas.
		if ( ! $this->_req_is_document($req->input_type))
		{
return $this->_err('Persyaratan ini bukan berkas; jawab lewat apicareer/Lamaran_jawaban.', 422);
		}

		if ( ! isset($_FILES['document']) OR $_FILES['document']['error'] !== UPLOAD_ERR_OK)
		{
return $this->_err('File tidak terkirim (field form-data: document).', 400);
		}
		$file = $_FILES['document'];

		// Ukuran
		$max_kb = $req->max_file_size_kb !== NULL ? (int) $req->max_file_size_kb : (int) $this->config->item('career_upload_max_kb');
		if ($file['size'] > $max_kb * 1024)
		{
return $this->_err('Ukuran file melebihi batas ' . $max_kb . ' KB.', 422);
		}

		// Ekstensi vs allowed_formats (normalisasi case-insensitive).
		$allowed = array();
		if ($req->allowed_formats !== NULL AND $req->allowed_formats !== '')
		{
			foreach (preg_split('/[\s,;]+/', strtoupper($req->allowed_formats)) as $f)
			{
				$f = trim($f, '*.');
				if ($f !== '') $allowed[] = $f;
			}
		}
		if (empty($allowed))
		{
			$allowed = array('PDF', 'JPG', 'JPEG', 'PNG');
		}

		$orig = (string) $file['name'];
		$ext = strtoupper(pathinfo($orig, PATHINFO_EXTENSION));
		if ( ! in_array($ext, $allowed, TRUE))
		{
return $this->_err('Format file tidak diperbolehkan. Yang diizinkan: ' . implode(', ', $allowed) . '.', 422);
		}

		// M3 fail-closed: ekstensi yang tidak dikenali di map ditolak meski
		// masuk allowed_formats (mis. DOC, MP4 — isi tidak bisa diverifikasi).
		if ( ! isset($this->_mime_map[$ext]))
		{
return $this->_err('Format ' . $ext . ' tidak didukung server. Gunakan: PDF, JPG, JPEG, PNG.', 422);
		}

		// M3 fail-closed: finfo wajib ada dan cocok dengan MIME ekstensi file itu
		// (bukan gabungan semua format); PDF ber-ekstensi .jpg ditolak.
		if ( ! function_exists('finfo_open'))
		{
			log_message('error', 'career upload: finfo tidak tersedia.');
return $this->_err('Server tidak dapat memverifikasi isi file. Coba lagi.', 500);
		}
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime  = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);
		if ( ! is_string($mime) OR $mime === '')
		{
return $this->_err('Isi file tidak dapat dikenali. Gunakan ' . $ext . ' yang benar.', 422);
		}
		if (stripos($mime, $this->_mime_map[$ext]) !== 0)
		{
return $this->_err('Isi file (' . $mime . ') tidak cocok dengan ekstensi .' . strtolower($ext) . '.', 422);
		}

		// L1: sanitasi nama tampilan saat simpan (tanpa quote/kontrol karakter).
		$safe_name = preg_replace('/[^A-Za-z0-9._\- ]+/', '_', $orig);
		$safe_name = trim(preg_replace('/\s+/', ' ', $safe_name));
		if ($safe_name === '' OR $safe_name === '.') $safe_name = 'dokumen.' . strtolower($ext);
		$safe_name = substr($safe_name, 0, 200);

		// 6C: keputusan alur — draft (isian) ATAU revisi (revision_requested
		// pada lamaran submitted/in_process). Keputusan final digulang ulang
		// DI DALAM transaksi dari baris terkunci.
		$revisi = FALSE;
		$alasan_gagal = '';
		if (in_array($req->app_status, array('submitted', 'in_process'), TRUE)
			AND $req->status === 'revision_requested')
		{
			$revisi = TRUE;
		}
		elseif ($req->app_status !== 'draft')
		{
			// final/withdrawn dsb — gagal cepat (keputusan juga diperiksa
			// ulang dalam transaksi, ini hanya hemat kerja).
			$alasan_gagal = 'Lamaran sudah terkunci (bukan draft) — tidak dapat mengunggah dokumen.';
		}

		// H2/M4: kunci lamaran + hitung versi/kuota DALAM transaksi.
		$this->db->trans_start();
		$locked = $this->db->query('SELECT id_application, status FROM career_applications WHERE id_application = ? AND id_candidate = ? FOR UPDATE', array((int) $req->id_application, (int) $this->career_candidate_id))->row();
		if ($locked === NULL)
		{
			$this->db->trans_complete();
return $this->_err('Lamaran tidak ditemukan (bukan milik kandidat ini).', 404);
		}
		// 6C: baca ulang status lamaran & requirement DALAM transaksi.
		$req_now = $this->db->select('a.status AS app_status, ar.status AS req_status')
			->from('career_application_requirements ar')
			->join('career_applications a', 'a.id_application = ar.id_application')
			->where('ar.id_application_requirement', $id_app_req)
			->get()->row();
		if ($req_now === NULL)
		{
			$this->db->trans_complete();
return $this->_err('Persyaratan lamaran tidak ditemukan.', 404);
		}
		if ($req_now->app_status === 'draft')
		{
			// alur isian pertama (lama).
		}
		elseif (in_array($req_now->app_status, array('submitted', 'in_process'), TRUE)
			AND $req_now->req_status === 'revision_requested')
		{
			$revisi = TRUE;
		}
		else
		{
			$this->db->trans_complete();
return $this->_err($alasan_gagal !== '' ? $alasan_gagal : 'Dokumen tidak dapat diunggah (lamaran terkunci dan tidak sedang meminta revisi).', 409);
		}

		// Checksum duplikat: reupload isi sama = idempotent, TANPA versi baru.
		$sha = hash_file('sha256', $file['tmp_name']);
		$dup = $this->db->where('id_application_requirement', $id_app_req)->where('checksum_sha256', $sha)
			->count_all_results('career_application_documents');
		if ($dup > 0)
		{
			$this->db->trans_complete();
			// F4a (D19): saat revisi, file identik tidak membantu — wajib berbeda
			// dari versi yang dicatat, supaya persyaratan tidak terkunci.
			if ($revisi)
			{
				return $this->_err('Unggah file yang berbeda dari yang diminta revisi (file identik tidak membuat versi baru).', 422);
			}
			return $this->_ok(NULL, 'File identik sudah diunggah sebelumnya (tidak membuat versi baru).', 200);
		}

		// M4: kuota versi per persyaratan.
		$max_ver = (int) $this->config->item('career_doc_max_versions');
		$nver = $this->db->where('id_application_requirement', $id_app_req)
			->count_all_results('career_application_documents');
		if ($nver >= $max_ver)
		{
			$this->db->trans_complete();
return $this->_err('Batas versi tercapai (maksimal ' . $max_ver . ' berkas untuk persyaratan ini). Silakan hubungi admin untuk penanganan manual.', 409);
		}

		// M4: kuota total per lamaran ( semua berkas semua persyaratan ).
		$total_kb = (int) ($this->db->select('COALESCE(SUM(file_size),0) AS t', FALSE)
			->where('id_application', (int) $req->id_application)
			->get('career_application_documents')->row()->t / 1024);
		$max_total = (int) $this->config->item('career_app_max_total_kb');
		if ($total_kb + ceil($file['size'] / 1024) > $max_total)
		{
			$this->db->trans_complete();
return $this->_err('Total ukuran dokumen lamaran melebihi ' . round($max_total / 1024) . ' MB.', 409);
		}

		// Versi dihitung di dalam kunci (anti-race select_max lalu insert).
		$nver = $this->db->where('id_application_requirement', $id_app_req)
			->count_all_results('career_application_documents');
		$version = $nver + 1;

		$base_dir = rtrim($this->config->item('career_upload_dir'), '/\\');
		$dir = FCPATH . $base_dir;
		if ( ! is_dir($dir)) mkdir($dir, 0750, TRUE);
		$rand = $this->_random_bytes(16);
		if ($rand === NULL OR strlen($rand) === 0)
		{
			$this->db->trans_complete();
			log_message('error', 'career upload: CSPRNG tidak tersedia.');
return $this->_err('Server tidak dapat menghasilkan nama aman untuk file. Coba lagi.', 500);
		}
		$random_name = 'app' . $req->id_application . '_req' . $id_app_req . '_v' . $version . '_' . bin2hex($rand) . '.' . strtolower($ext);
		$dest = $dir . DIRECTORY_SEPARATOR . $random_name;

		if ( ! move_uploaded_file($file['tmp_name'], $dest))
		{
			$this->db->trans_complete();
return $this->_err('Gagal menyimpan file.', 500);
		}

		$this->db->insert('career_application_documents', array(
			'id_application_requirement' => $id_app_req,
			'id_application'             => (int) $req->id_application,
			'version'                    => $version,
			'file_path'                  => $base_dir . '/' . $random_name,
			'original_name'              => $safe_name,
			'mime_type'                  => substr($mime, 0, 120),
			'file_size'                  => (int) $file['size'],
			'checksum_sha256'            => $sha,
			'uploaded_at'                => date('Y-m-d H:i:s'),
		));
		$id_doc = (int) $this->db->insert_id();

		// F2: status persyaratan naik SEBELUM commit, DALAM transaksi, dengan
		// guard status (race dengan keputusan admin tidak boleh menimpa
		// verified/rejected). Jalur revisi juga membersihkan keputusan lama.
		if ($revisi)
		{
			$this->db->where('id_application_requirement', $id_app_req)
				->where('status', 'revision_requested')
				->update('career_application_requirements', array(
					'status'      => 'pending_review',
					'reviewed_by' => NULL,
					'reviewed_at' => NULL,
					'review_note' => NULL,
				));
			if ($this->db->affected_rows() === 0)
			{
				// Dibawa kembali ke keadaan semula bersama insert versi; tanpa
				// trans_complete setelah rollback manual.
				$this->db->trans_rollback();
				@unlink($dest);
				return $this->_err('Status persyaratan berubah — sudah tidak meminta revisi. Segarkan halaman dan coba lagi.', 409);
			}
		}
		else
		{
			$this->db->where('id_application_requirement', $id_app_req)
				->update('career_application_requirements', array('status' => 'pending_review'));
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			@unlink($dest);
			log_message('error', 'career upload gagal menyimpan metadata untuk ' . $id_app_req);
return $this->_err('Gagal menyimpan dokumen. Coba lagi.', 500);
		}

		$this->_audit('upload_dokumen', 'career_application_documents', $id_doc,
			array('revisi' => $revisi ? 1 : 0));
		$this->_ok(array(
			'id_application_document' => $id_doc,
			'version'                 => $version,
			'original_name'           => $safe_name,
			'download_url'            => 'apicareer/Lamaran_dokumen/detail/' . $id_doc,
		), 'Dokumen diunggah.', 201);
	}

	public function detail_get($id = 0)
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$doc = $this->db->select('d.id_application_document, d.file_path, d.original_name, d.mime_type, d.file_size, d.id_application')
			->from('career_application_documents d')
			->join('career_applications a', 'a.id_application = d.id_application')
			->where('d.id_application_document', (int) $id)
			->where('a.id_candidate', (int) $this->career_candidate_id)
			->get()->row();
		if ($doc === NULL)
		{
return $this->_err('Dokumen tidak ditemukan.', 404);
		}

		$path = FCPATH . $doc->file_path;
		if ( ! is_file($path))
		{
return $this->_err('File fisik tidak ditemukan di server.', 404);
		}

		$this->_audit('unduh_dokumen', 'career_application_documents', $doc->id_application_document);

		// L1: header unduh lengkap — nama aman ASCII + UTF-8 rawurlencode,
		// nosniff, Content-Length; streaming tanpa muat seluruh file ke memori.
		$ascii = preg_replace("/[^A-Za-z0-9._\- ]+/", '_', $doc->original_name);
		if ($ascii === '' OR $ascii === '.') $ascii = 'dokumen';
		// Patch 5C.1: set_content_type()/set_header() + exit membuat header tidak
		// pernah dikirim (CI output tidak tersalurkan) — kirim header langsung.
		$mime = trim((string) ($doc->mime_type ? $doc->mime_type : 'application/octet-stream'), "\r\n\0");
		header('Content-Type: ' . $mime);
		header('X-Content-Type-Options: nosniff');
		header('Content-Disposition: attachment; filename="' . $ascii . '"; filename*=UTF-8\'\'' . rawurlencode($doc->original_name));
		header('Content-Length: ' . filesize($path));

		$fh = fopen($path, 'rb');
		if ($fh === FALSE)
		{
return $this->_err('File fisik tidak dapat dibaca.', 500);
		}
		while ( ! feof($fh)) { echo fread($fh, 8192); }
		fclose($fh);
		exit;
	}

	/** L12: CSPRNG saja — tanpa fallback mt_rand (fail-closed). */
	protected function _random_bytes($n)
	{
		if (function_exists('random_bytes')) return random_bytes($n);
		if (function_exists('openssl_random_pseudo_bytes')) return openssl_random_pseudo_bytes($n);
		return NULL;
	}

	/** H1: tipe input berkas = file ATAU document (default skema). */
	protected function _req_is_document($input_type)
	{
		return ($input_type === 'file' OR $input_type === 'document');
	}
}
