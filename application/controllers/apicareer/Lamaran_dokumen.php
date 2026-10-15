<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dokumen lamaran — upload per persyaratan + unduh (pemilik saja).
 *
 * Folder privat: application/storage/career/ (dilindungi .htaccess Deny;
 * file_path disimpan relatif). Nama file acak, versi+1 per persyaratan,
 * checksum sha256 (UNIQUE di skema).
 */

class Lamaran_dokumen extends Career_base {

	public function upload_post()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id_app_req = (int) $this->post('id_application_requirement');
		if ($id_app_req < 1)
		{
return $this->_err('id_application_requirement wajib diisi.', 400);
		}

		// Scope: requirement MILIK kandidat ini; item milik fak ikutan → 404.
		$req = $this->db->select('ar.id_application_requirement, ar.id_application, ar.id_job_posting_requirement, ar.status, a.id_candidate, a.status AS app_status, jr.requirement_name, jr.allowed_formats, jr.max_file_size_kb')
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
		if ($req->app_status !== 'draft')
		{
return $this->_err('Lamaran sudah terkunci (bukan draft) — tidak dapat mengunggah dokumen.', 409);
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

		// Ekstensi vs allowed_formats (mis. "PDF,JPG"; normalization case-insensitive).
		// + MIME hasil finfo diharuskan cocok dengan ekstensi yang diizinkan.
		$allowed = array();
		if ($req->allowed_formats !== NULL AND $req->allowed_formats !== '')
		{
			foreach (preg_split('/[\s,;]+/', strtoupper($req->allowed_formats)) as $f)
			{
				$f = trim($f, '*.');
				if ($f !== '') $allowed[] = $f;
			}
		}
		// default aman bila admin belum set allowed_formats
		if (empty($allowed))
		{
			$allowed = array('PDF', 'JPG', 'JPEG', 'PNG');
		}

		$orig = (string) $file['name'];
		$ext = strtoupper(pathinfo($orig, PATHINFO_EXTENSION));
		if ( ! empty($allowed) AND ! in_array($ext, $allowed, TRUE))
		{
return $this->_err('Format file tidak diperbolehkan. Yang diizinkan: ' . implode(', ', $allowed) . '.', 422);
		}

		// MIME nyata dari isi file (finfo; bukan nama file).
		$mime = '';
		if (function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $file['tmp_name']);
			finfo_close($finfo);
		}
		// Map ekstensi -> awalan MIME yang dianggap cocok.
		$mime_map = array(
			'PDF'  => 'application/pdf',
			'JPG'  => 'image/jpeg',
			'JPEG' => 'image/jpeg',
			'PNG'  => 'image/png',
		);
		$mime_prefix_ok = array();
		foreach ($allowed as $a)
		{
			if (isset($mime_map[$a])) $mime_prefix_ok[] = $mime_map[$a];
		}
		if ($mime !== '' AND ! empty($mime_prefix_ok))
		{
			$ok = FALSE;
			foreach ($mime_prefix_ok as $p) { if (strpos($mime, $p) === 0) { $ok = TRUE; break; } }
			if ( ! $ok)
			{
return $this->_err('Isi file bukan format yang diizinkan (terdeteksi ' . $mime . ').', 422);
			}
		}

		// Verifikasi lamaran masih draft (anti-race sederhana sebelum move).
		$st = $this->db->select('status')->where('id_application', (int) $req->id_application)->get('career_applications')->row();
		if ($st === NULL OR $st->status !== 'draft')
		{
return $this->_err('Lamaran sudah terkunci.', 409);
		}

		// Checksum duplikat: reupload isi sama = idempotent (UNIQUE skema).
		$sha = hash_file('sha256', $file['tmp_name']);
		$dup = $this->db->where('id_application_requirement', $id_app_req)->where('checksum_sha256', $sha)
			->count_all_results('career_application_documents');
		if ($dup > 0)
		{
			return $this->_ok(NULL, 'File identik sudah diunggah sebelumnya (tidak membuat versi baru).', 200);
		}

		$version = 1;
		$lastv = $this->db->select_max('version')->where('id_application_requirement', $id_app_req)
			->get('career_application_documents')->row();
		if ($lastv !== NULL AND $lastv->version !== NULL) $version = (int) $lastv->version + 1;

		$base_dir = rtrim($this->config->item('career_upload_dir'), '/\\');
		$dir = FCPATH . $base_dir;
		if ( ! is_dir($dir)) mkdir($dir, 0750, TRUE);
		$random_name = 'app' . $req->id_application . '_req' . $id_app_req . '_v' . $version . '_' . bin2hex($this->_random_bytes(16)) . '.' . strtolower($ext);
		$dest = $dir . DIRECTORY_SEPARATOR . $random_name;

		if ( ! move_uploaded_file($file['tmp_name'], $dest))
		{
return $this->_err('Gagal menyimpan file.', 500);
		}

		$this->db->insert('career_application_documents', array(
			'id_application_requirement' => $id_app_req,
			'id_application'             => (int) $req->id_application,
			'version'                    => $version,
			'file_path'                  => $base_dir . '/' . $random_name,
			'original_name'              => substr($orig, 0, 255),
			'mime_type'                  => $mime !== '' ? substr($mime, 0, 120) : 'application/octet-stream',
			'file_size'                  => (int) $file['size'],
			'checksum_sha256'            => $sha,
			'uploaded_at'                => date('Y-m-d H:i:s'),
		));
		$id_doc = (int) $this->db->insert_id();

		// Status persyaratan naik ke pending_review di MVP (verifikasi bebas admin).
		$this->db->where('id_application_requirement', $id_app_req)
			->update('career_application_requirements', array('status' => 'pending_review'));

		$this->_audit('upload_dokumen', 'career_application_documents', $id_doc);
		$this->_ok(array(
			'id_application_document' => $id_doc,
			'version'                 => $version,
			'original_name'           => $orig,
			'download_url'            => 'apicareer/Lamaran_dokumen/detail/' . $id_doc,
		), 'Dokumen diunggah.', 201);
	}

	public function detail_get($id = 0)
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$doc = $this->db->select('d.id_application_document, d.file_path, d.original_name, d.mime_type, d.id_application')
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

		$this->output->set_content_type($doc->mime_type ?: 'application/octet-stream')
			->set_header('Content-Disposition: attachment; filename="' . basename($doc->original_name) . '"')
			->set_output(file_get_contents($path));
	}

	protected function _random_bytes($n)
	{
		$b = '';
		if (function_exists('random_bytes')) $b = random_bytes($n);
		elseif (function_exists('openssl_random_pseudo_bytes')) $b = openssl_random_pseudo_bytes($n);
		else for ($i = 0; $i < $n; $i++) $b .= chr(mt_rand(0, 255));
		return $b;
	}
}
