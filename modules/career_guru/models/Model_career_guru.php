<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model konversi pelamar accepted → guru SD/KB/TK (Fase GURU.1 Bagian B).
 *
 * Pola transaksi: dipanggil dari controller yang sudah trans_begin(); semua
 * query memakai satu koneksi ($this->db). SELECT FOR UPDATE dijalankan di
 * kunci_lamaran() supaya race dua admin hanya menghasilkan tepat satu baris
 * guru + satu baris career_guru_link.
 */
class Model_career_guru extends CI_Model
{

	/** Tabel guru per jenjang (SD→guru_sd, KB→guru_kb, TK→guru_tk). */
	public function tabel_jenjang($jenjang)
	{
		$map = array('SD' => 'guru_sd', 'KB' => 'guru_kb', 'TK' => 'guru_tk');
		return isset($map[$jenjang]) ? $map[$jenjang] : NULL;
	}

	/** posisi default per jenjang (Guru SD=2, Guru KB=5, Guru TK=6). */
	public function id_posisi_default($jenjang)
	{
		$map = array('SD' => 2, 'KB' => 5, 'TK' => 6);
		return isset($map[$jenjang]) ? $map[$jenjang] : NULL;
	}

	/** presensi_role default per jenjang (server-side, bukan POST). */
	public function presensi_role($jenjang)
	{
		if ($jenjang === 'KB') return 'GURUKB';
		if ($jenjang === 'TK') return 'GURUTK';
		return 'GURUSD';
	}

	/** Kunci lamaran + kandidat untuk trans (SELECT FOR UPDATE). */
	public function kunci_lamaran($id_application)
	{
		return $this->db->query(
		'SELECT ap.id_application, ap.status, ap.id_candidate, ap.submitted_at,'
		. ' c.full_name, c.email, c.is_active AS kandidat_aktif'
		. ' FROM career_applications ap'
		. ' JOIN career_candidates c ON c.id_candidate = ap.id_candidate'
		. ' WHERE ap.id_application = ? FOR UPDATE',
		array((int) $id_application)
	)->row();
	}

	/** Cari link konversi yang sudah ada untuk lamaran ini. */
	public function link_lamaran($id_application)
	{
		return $this->db->where('id_application', (int) $id_application)
			->limit(1)
			->get('career_guru_link')
			->row();
	}

	/** Cari link konversi untuk kandidat yang sama (satu guru/link per kandidat). */
	public function link_kandidat($id_candidate, $kecuali_application = NULL)
	{
		$this->db->where('id_candidate', (int) $id_candidate);
		if ($kecuali_application) {
			$this->db->where('id_application !=', (int) $kecuali_application);
		}
		return $this->db->limit(1)->get('career_guru_link')->row();
	}

	/** Profil kandidat (phone/place/date/gender/summary/address). */
	public function profil($id_candidate)
	{
		return $this->db->select('phone, place_of_birth, date_of_birth, gender, address, summary')
			->where('id_candidate', (int) $id_candidate)
			->limit(1)
			->get('career_candidate_profiles')
			->row();
	}

	/** Pendidikan terbaru kandidat (end_date DESC, id DESC). */
	public function pendidikan_terbaru($id_candidate)
	{
		return $this->db->where('item_type', 'education')
			->where('id_candidate', (int) $id_candidate)
			->order_by('end_date', 'DESC')
			->order_by('id_candidate_item', 'DESC')
			->limit(1)
			->get('career_candidate_items')
			->row();
	}

	/**
	 * Tabrakan guru lintas jenjang (B-2) menurut jenis:
	 *   'blok'    → emp_code / email / email_ms_office TIDAK BOLEH dipakai
	 *               guru lain (emp_code atau email dipakai/login).
	 *   'periksa' → nama_lengkap + tgl_lahir sama → peringatan admin
	 *               (checkbox konfirmasi "sudah memeriksa, orang berbeda").
	 * Return array baris {blok|periksa, tabel, id, field, nama_selain}.
	 */
	public function tabrakan_guru($nama_lengkap, $tgl_lahir, $emp_code, $email, $email_ms_office, $nik)
	{
		$out = array();

		// --- blok: bidang unik ------------------------------------
		$blok_fields = array(
			'emp_code'        => $emp_code,
			'email'           => $email,
			'email_ms_office' => $email_ms_office,
			'nik'             => $nik,
		);
		foreach ($blok_fields as $field => $nilai) {
			$nilai = trim((string) $nilai);
			if ($nilai === '') { continue; }
			if ($field === 'email_ms_office' && $email_ms_office === $email) { continue; }
			if ($field === 'email' && $email === $email_ms_office) { continue; }

			foreach (guru_tables() as $tbl) {
				$enum_selain = $this->db->query(
					"SELECT id_guru, COALESCE(nama_lengkap,'') AS nama_selain
					 FROM `{$tbl}` WHERE {$field} = ? AND deleted_at IS NULL LIMIT 1",
					array($nilai)
				)->row();
				if ($enum_selain) {
					$out[] = array(
						'jenis' => 'blok',
						'tabel' => $tbl,
						'id'    => (int) $enum_selain->id_guru,
						'field' => $field,
						'nama_selain' => $enum_selain->nama_selain,
					);
					break;
				}
			}
		}

		// --- periksa: nama_lengkap + tgl_lahir (lintas jenjang) -----------
		$tgl = (string) $tgl_lahir;
		if ($tgl !== '') {
			foreach (guru_tables() as $tbl) {
				$row = $this->db->query(
					"SELECT id_guru, COALESCE(nama_lengkap,'') AS nama_selain
					 FROM `{$tbl}`
					 WHERE LOWER(nama_lengkap) = LOWER(?) AND tgl_lahir = ?
						AND deleted_at IS NULL LIMIT 1",
					array($nama_lengkap, $tgl)
				)->row();
				if ($row) {
					$out[] = array(
						'jenis' => 'periksa',
						'tabel' => $tbl,
						'id'    => (int) $row->id_guru,
						'field' => 'nama_lengkap+tgl_lahir',
						'nama_selain' => $row->nama_selain,
					);
					break;
				}
			}
		}

		return $out;
	}

	/** Insert baris guru ke tabel jenjang; return (int) id_guru. */
	public function insert_guru($jenjang, $data)
	{
		$tbl = $this->tabel_jenjang($jenjang);
		if ( ! $tbl) { return 0; }

		$defaults = array(
			'kode_mapel'   => '',
			'mapel_nomor'  => 0,
			'presensi_role'=> $this->presensi_role($jenjang),
			'satuan_pendidikan' => ($jenjang === 'KB') ? 'KB' : ($jenjang === 'TK' ? 'TK' : 'SD'),
			'foto_profil'  => '',
			'login_token'  => '',
			'token'        => NULL,
			'token_expired'=> NULL,
			'device_id'    => NULL,
			'slip_gaji'    => NULL,
			'deleted_at'   => NULL,
			'status_kepegawaian' => NULL,
			'informasi_kepala_pimpinan' => NULL,
			'unit'         => ($jenjang === 'KB') ? 'KB' : ($jenjang === 'TK' ? 'TK' : 'SD'),
		);
		$data = array_merge($defaults, $data);
		$this->db->insert($tbl, $data);

		return (int) $this->db->insert_id();
	}

	/** Insert baris link konversi. */
	public function insert_link($data)
	{
		$this->db->insert('career_guru_link', $data);
		return (int) $this->db->insert_id();
	}

	/** Ambil baris guru per jenjang (untuk view/lencana). */
	public function get_guru_tabel($jenjang, $id_guru)
	{
		$tbl = $this->tabel_jenjang($jenjang);
		if ( ! $tbl) { return NULL; }
		return $this->db->where('id_guru', (int) $id_guru)
			->limit(1)->get($tbl)->row();
	}
}
