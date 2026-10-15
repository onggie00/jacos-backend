<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Kandidat Controller (Fase 6B)
*| --------------------------------------------------------------------------
*| Daftar akun kandidat (baca, perm career_kandidat_list) + reset password
*| D2 (perm career_candidate_reset_password — lupa password ditangani
*| admin, TANPA email apa pun sesuai keputusan D2). Reset TIDAK pernah
*| menampilkan hash lama; audit hanya mencatat aksi, bukan sandi.
*/
class Career_kandidat extends Admin
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_career_kandidat');
		$this->load->helper('career_core/career_core');
	}

	/** GET administrator/career_kandidat — daftar + cari + paging. */
	public function index($offset = 0)
	{
		if (!$this->is_allowed('career_kandidat_list', false)) {
			show_error('Forbidden', 403);
		}

		$filter = array(
			'q' => career_q($this->input->get('q')),
		);
		$offset = max(0, (int) $offset);

		$this->data['q']            = $filter['q'];
		$this->data['kandidats']    = $this->model_career_kandidat->daftar($filter, $this->limit_page, $offset);
		$this->data['jumlah']       = $this->model_career_kandidat->daftar_count($filter);
		$this->data['csrf_field']   = career_csrf_field();

		$config = [
			'base_url'    => 'administrator/career_kandidat/index/',
			'total_rows'  => $this->data['jumlah'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kandidat Karier');
		$this->render('backend/standart/administrator/career_kandidat/career_kandidat_list', $this->data);
	}

	/**
	 * Rekam kandidat? Tidak di 6B — kandidat mendaftar sendiri di FE.
	 * Detail kandidat ada lewat menu Pelamar (view per lamaran).
	 */

	/**
	 * Reset password (D2): POST administrator/career_kandidat/reset_password/<id_candidate>
	 * param: password_baru (admin tulis / generate, <200). Lalu:
	 * - hash dihitung DI SINI dgn pola yang sama dengan SIPD? CI3? Ya
	 *   password_hash PHP asli (kandidat bukan user aauth bermasalah —
	 *   akun kandidat & hash dipegang minimal di career_candidates).
	 * - SET token_version berkurang? Lain file (diluar 6B).
	 */
	public function reset_password($id_candidate = NULL)
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Method harus POST (reset password tidak lewat GET).');
		}
		if (! career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}
		if (!$this->is_allowed('career_candidate_reset_password', false)) {
			return $this->_json_deny('Forbidden');
		}

		$id = (int) $id_candidate;
		$sandi = career_q($this->input->post('password_baru'));
		if ($id < 1) return $this->_json_deny('Kandidat tidak ditemukan.', 404);
		if (strlen($sandi) > 200) return $this->_json_deny('Password maksimal 200 karakter.', 422);

		list($ok, $pesan) = $this->model_career_kandidat->reset_password($id, $sandi);
		if (!$ok) {
			list($kode, $teks) = $this->_pecah($pesan);
			return $this->_json_deny($teks, $kode);
		}

		// Audit: tanpa menyimpan sandi apa pun (bukan plaintext, bukan hash).
		career_audit('reset_password_kandidat', 'career_candidates', $id);
		return $this->_json_done('Password kandidat direset. Bagikan password baru lewat saluran administrasi (BUKAN email — keputusan D2). Wajib sarankan kandidat ganti password di profil.');
	}

	private function _pecah($pesan)
	{
		$pisah = explode('|', (string) $pesan, 2);
		$kode = ctype_digit($pisah[0]) ? (int) $pisah[0] : 422;
		return array($kode, isset($pisah[1]) ? $pisah[1] : $pesan);
	}

	private function _json_done($pesan)
	{
		$this->output->set_status_header(200);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('success' => true, 'message' => (string) $pesan)));
	}

	private function _json_deny($pesan = 'Forbidden', $kode = 403)
	{
		$this->output->set_status_header($kode);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('success' => false, 'message' => (string) $pesan)));
	}
}
