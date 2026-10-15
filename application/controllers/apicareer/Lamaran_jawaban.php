<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Jawaban persyaratan non-berkas (input_type text/number/select) lamaran
 * milik kandidat. Hanya saat lamaran masih draft.
 *
 * PUT apicareer/Lamaran_jawaban
 * body: {id_application_requirement, answer}
 *
 * Kolom skema: career_application_requirements.answer_text (migration 003).
 */

class Lamaran_jawaban extends Career_base {

	public function index_put()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id_app_req = (int) $this->put('id_application_requirement');
		if ($id_app_req < 1)
		{
return $this->_err('id_application_requirement wajib diisi.', 400);
		}
		$answer = $this->put('answer');
		if ($answer !== NULL AND ! is_string($answer))
		{
return $this->_err('answer wajib berupa teks.', 422);
		}

		// Scope: requirement milik lamaran milik kandidat ini (anti-IDOR).
		$req = $this->db->select('ar.id_application_requirement, ar.id_application, a.status AS app_status, jr.input_type, jr.requirement_type, jr.requirement_name')
			->from('career_application_requirements ar')
			->join('career_applications a', 'a.id_application = ar.id_application')
			->join('career_job_posting_requirements jr', 'jr.id_job_posting_requirement = ar.id_job_posting_requirement')
			->where('ar.id_application_requirement', $id_app_req)
			->where('a.id_candidate', (int) $this->career_candidate_id)
			->get()->row();
		if ($req === NULL)
		{
return $this->_err('Persyaratan lamaran tidak ditemukan.', 404);
		}
		if ($req->app_status !== 'draft')
		{
return $this->_err('Lamaran sudah terkunci (bukan draft).', 409);
		}

		// Hanya persyaratan non-berkas yang menerima jawaban.
		if ($req->input_type === 'file' OR $req->input_type === 'document')
		{
return $this->_err('Persyaratan ini berupa unggahan berkas; gunakan apicareer/Lamaran_dokumen.', 422);
		}
		if ( ! in_array($req->input_type, array('text', 'number', 'select'), TRUE))
		{
return $this->_err('Tipe persyaratan (input_type) tidak menerima jawaban teks.', 422);
		}

		$answer = ($answer === NULL) ? '' : trim($answer);
		if (mb_strlen($answer) > 2000)
		{
return $this->_err('answer maksimal 2000 karakter.', 422);
		}

		// Validasi per input_type (keputusan Fase 4.5 #1).
		if ($req->input_type === 'number' AND $answer !== ''
			AND ( ! is_numeric($answer) OR strlen($answer) > 20))
		{
return $this->_err('answer harus berupa angka untuk persyaratan ini.', 422);
		}
		// select/text: jawab teks non-empty; nilai opsi select divalidasi admin.
		if ($req->input_type === 'select' AND $answer !== '' AND mb_strlen($answer) > 200)
		{
return $this->_err('answer (pilihan) maksimal 200 karakter.', 422);
		}
		if ($this->_has_4byte_char($answer))
		{
return $this->_err('answer berisi karakter unicode 4-byte yang tidak didukung (emoji tidak didukung).', 422);
		}

		$this->db->where('id_application_requirement', $id_app_req)
			->update('career_application_requirements', array(
				'answer_text' => ($answer === '' ? NULL : $answer),
			));

		$this->_audit('jawab_persyaratan', 'career_application_requirements', $id_app_req);
		$this->_ok(array(
			'id_application_requirement' => $id_app_req,
			'answer_text'                => ($answer === '' ? NULL : $answer),
			'requirement_type'           => $req->requirement_type,
		), 'Jawaban tersimpan.', 200);
	}
}
