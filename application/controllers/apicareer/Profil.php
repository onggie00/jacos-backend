<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profil kandidat — GET/PUT data profil sendiri.
 * Kolom yang boleh diubah hanya whitelist bawah; snapshot lamaran dibuat
 * saat submit (perubahan profil tidak mengubah lamaran yang terkunci).
 */

class Profil extends Career_base {

	/** kolom profil yang boleh di-update lewat API */
	protected $_allowed = array(
		'phone', 'place_of_birth', 'date_of_birth', 'gender', 'address', 'summary',
	);

	public function index_get()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$profile = $this->db->where('id_candidate', $candidate->id_candidate)
			->get('career_candidate_profiles')->row_array();

		if ($profile === NULL)
		{
return $this->_err('Profil belum ada.', 404);
		}

		$this->_ok(array(
			'id_candidate' => (int) $candidate->id_candidate,
			'full_name'    => $candidate->full_name,
			'profile'      => $profile,
		), 'OK');
	}

	public function index_put()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$in = array();
		foreach ($this->_allowed as $field)
		{
			$val = $this->put($field);
			if ($val === NULL) continue; // tidak diubah
			if ( ! $this->_validate_field($field, $val))
			{
return $this->_err('Nilai ' . $field . ' tidak valid.', 422);
			}
			$in[$field] = ($field === 'gender' ? $val : (string) $val);
		}

		if (empty($in))
		{
return $this->_err('Tidak ada field yang di-update.', 400);
		}

		$where = array('id_candidate' => $candidate->id_candidate);
		$exists = $this->db->where($where)->count_all_results('career_candidate_profiles');
		if ($exists === 0)
		{
			$in['id_candidate'] = $candidate->id_candidate;
			$this->db->insert('career_candidate_profiles', $in);
		}
		else
		{
			$this->db->where($where)->update('career_candidate_profiles', $in);
		}

		$this->_ok(NULL, 'Profil diperbarui.', 200);
	}

	protected function _validate_field($field, $val)
	{
		$lengths = array(
			'phone'         => 30,
			'place_of_birth'=> 120,
			'address'       => 500,
			'summary'       => 5000,
		);
		if ($field === 'gender')
		{
			return in_array($val, array('male', 'female'), TRUE);
		}
		if ($field === 'date_of_birth')
		{
			if ( ! is_string($val) OR ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return FALSE;
			$d = date_parse($val);
			return ($d && $d['error_count'] === 0 && $d['warning_count'] === 0);
		}
		$max = isset($lengths[$field]) ? $lengths[$field] : 500;
		return is_string($val) AND strlen($val) <= $max;
	}
}
