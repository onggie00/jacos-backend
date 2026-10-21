<?php defined('FCPATH') OR exit('No direct script access allowed');

/**
 * Helper core modul Guru (GURU.1).
 *
 * Isi:
 *  - daftar tabel guru semua jenjang (SD/SMP/SMA/FT/KB/TK) untuk cek unik
 *    lintas jenjang (emp_code / email) — tabel lama TIDAK punya constraint
 *    unik; lintas tabel dijaga lewat query TERPARAMETER (aman SQLi).
 *  - util presensi_role default per jenjang.
 *
 * Dipakai modul guru_kb/guru_tk dan modul konversi pelamar→guru (GURU.1 B).
 */

if ( ! function_exists('guru_tables')) {
	/** Semua tabel guru terkait cek unik lintas jenjang. */
	function guru_tables()
	{
		return array('guru_sd', 'guru_smp', 'guru_sma', 'guru_ft', 'guru_kb', 'guru_tk');
	}
}

if ( ! function_exists('guru_ci')) {
	/** Instance CI untuk helper (defensif bila dipanggil di konteks helper). */
	function guru_ci()
	{
		return get_instance();
	}
}

if ( ! function_exists('emp_code_terpakai')) {
	/**
	 * Cari apakah emp_code (case-insensitive) sudah dipakai guru mana pun
	 * lintas jenjang. Return array('tabel', 'id', 'nama_selain') bila
	 * terpakai, FALSE bila bebas.
	 *
	 * @param string $emp_code     emp_code yang dicari
	 * @param string $kecuali_tabel tabel yang dikecualikan (mis. guru_kb)
	 * @param int    $kecuali_id   id baris tabel itu yang dikecualikan (edit)
	 */
	function emp_code_terpakai($emp_code, $kecuali_tabel = null, $kecuali_id = null)
	{
		$db = guru_ci()->db;
		$emp_code = trim((string) $emp_code);

		if ($emp_code === '') {
			return FALSE;
		}

		foreach (guru_tables() as $tbl) {
			$kecuali_id = (int) $kecuali_id;
			$kecuali_sql = ($kecuali_tabel === $tbl && $kecuali_id > 0)
				? ' AND id_guru != ' . $kecuali_id
				: '';

			$row = $db->query(
				"SELECT id_guru, COALESCE(nama_lengkap, '') AS nama_selain
				 FROM `{$tbl}`
				 WHERE LOWER(COALESCE(emp_code, '')) = ?
					AND deleted_at IS NULL{$kecuali_sql}
				 LIMIT 1",
				array($emp_code)
			)->row();

			if ($row) {
				return array(
					'tabel' => $tbl,
					'id' => (int) $row->id_guru,
					'nama_selain' => $row->nama_selain,
				);
			}
		}

		return FALSE;
	}
}

if ( ! function_exists('guru_email_terpakai')) {
	/**
	 * Cari email/email_ms_office case-insensitive lintas tabel guru karena
	 * email dipakai login aplikasi guru — GURU.1 menuntut unik.
	 * Return array('tabel','id','field','nama_selain') atau FALSE.
	 */
	function guru_email_terpakai($email, $kecuali_tabel = null, $kecuali_id = null)
	{
		$db = guru_ci()->db;
		$email = trim((string) $email);

		if ($email === '') {
			return FALSE;
		}

		foreach (guru_tables() as $tbl) {
			$kecuali_id = (int) $kecuali_id;
			$kecuali_sql = ($kecuali_tabel === $tbl && $kecuali_id > 0)
				? ' AND id_guru != ' . $kecuali_id
				: '';

			$row = $db->query(
				"SELECT id_guru,
						COALESCE(nama_lengkap, '') AS nama_selain,
						CASE WHEN LOWER(COALESCE(email, '')) = ? THEN 'email' ELSE 'email_ms_office' END AS field_cocok
				 FROM `{$tbl}`
				 WHERE (LOWER(COALESCE(email, '')) = ? OR LOWER(COALESCE(email_ms_office, '')) = ?)
					AND deleted_at IS NULL{$kecuali_sql}
				 LIMIT 1",
				array($email, $email, $email)
			)->row();

			if ($row) {
				return array(
					'tabel' => $tbl,
					'id' => (int) $row->id_guru,
					'field' => $row->field_cocok,
					'nama_selain' => $row->nama_selain,
				);
			}
		}

		return FALSE;
	}
}

if ( ! function_exists('guru_presensi_role')) {
	/** presensi_role DEFAULT untuk modul guru_* (SERVER-side, bukan POST). */
	function guru_presensi_role($jenjang)
	{
		$jenjang = strtoupper(trim((string) $jenjang));
		return ($jenjang === 'KB') ? 'GURUKB' : 'GURUTK';
	}
}
