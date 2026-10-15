<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lowongan (publik, tanpa login).
 * Hanya is_published=1 dan dalam jendela buka/tutup masing-masing.
 */

class Lowongan extends Career_base {

	// GET apicareer/Lowongan
	public function index_get()
	{
		$today = date('Y-m-d');
		$rows = $this->db->select('id_job_posting, title, description, opening_date, closing_date')
			->where('is_published', 1)
			->order_by('id_job_posting', 'DESC')
			->get('career_job_postings')
			->result_array();

		$out = array();
		foreach ($rows as $r)
		{
			if ($r['opening_date'] !== NULL AND $r['opening_date'] > $today) continue;
			if ($r['closing_date'] !== NULL AND $r['closing_date'] < $today) continue;
			$out[] = $r;
		}

		$this->_ok($out, 'OK');
	}

	// GET apicareer/Lowongan/detail/<id>  (+ daftar persyaratan lowongan)
	public function detail_get($id = 0)
	{
		$today = date('Y-m-d');
		$posting = $this->db->where('id_job_posting', (int) $id)->where('is_published', 1)->get('career_job_postings')->row();

		if ($posting === NULL
			OR ($posting->opening_date !== NULL AND $posting->opening_date > $today)
			OR ($posting->closing_date !== NULL AND $posting->closing_date < $today))
		{
return $this->_err('Lowongan tidak ditemukan atau sudah ditutup.', 404);
		}

		$requirements = $this->db->select('id_job_posting_requirement, requirement_name, requirement_type, input_type, allowed_formats, max_file_size_kb, display_order')
			->where('id_job_posting', (int) $posting->id_job_posting)
			->order_by('display_order, id_job_posting_requirement')
			->get('career_job_posting_requirements')
			->result_array();

		$ret = array(
			'id_job_posting' => (int) $posting->id_job_posting,
			'title'          => $posting->title,
			'description'    => $posting->description,
			'opening_date'   => $posting->opening_date,
			'closing_date'   => $posting->closing_date,
			'requirements'   => $requirements,
		);

		$this->_ok($ret, 'OK');
	}
}
