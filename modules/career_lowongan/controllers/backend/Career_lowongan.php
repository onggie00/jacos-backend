<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Lowongan Controller (Fase 6A, revisi 6A.1)
*| --------------------------------------------------------------------------
*| CRUD lowongan kerja + editor persyaratannya.
*| D12: lowongan dgn lamaran tidak dihapus; baris persyarat yang dipakai
*| lamaran tidak dihapus; input_type/requirement_type terkunci per baris
*| setelah ada lamaran. Semua mutasi POST + CSRF + audit career_audit_logs.
*/
class Career_lowongan extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_career_lowongan');
		$this->load->helper('career_core/career_core');
		$this->load->library('form_validation');
	}

	/* ------------------------------------------------------------ */
	/* Daftar                                                       */
	/* ------------------------------------------------------------ */

	public function index($offset = 0)
	{
		$this->is_allowed('career_lowongan_list');

		$q      = career_q($this->input->get('q'));
		$status = career_q($this->input->get('status'));
		$offset = max(0, (int) $offset);

		$this->data['q']       = $q;
		$this->data['status']  = $status;
		$this->data['lowongans']       = $this->model_career_lowongan->lowongan_list($q, $status, $this->limit_page, $offset);
		$this->data['lowongan_counts'] = $this->model_career_lowongan->lowongan_count($q, $status);

		// MED #11: peta punya-lamaran utk halaman ini (anti N+1).
		$ids = array();
		foreach ($this->data['lowongans'] as $row) { $ids[] = (int) $row->id_job_posting; }
		$punya = $this->model_career_lowongan->punya_ids($ids);
		foreach ($this->data['lowongans'] as $row) {
			$row->status_tampil = $this->model_career_lowongan->status_tampil($row);
			$row->punya_lamaran = !empty($punya[(int) $row->id_job_posting]);
		}

		$config = [
			'base_url'    => 'administrator/career_lowongan/index/',
			'total_rows'  => $this->data['lowongan_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->data['csrf_field'] = career_csrf_field();

		$this->template->title('Lowongan Kerja');
		$this->render('backend/standart/administrator/career_lowongan/career_lowongan_list', $this->data);
	}

	/* ------------------------------------------------------------ */
	/* Buat lowongan                                                */
	/* ------------------------------------------------------------ */

	public function add()
	{
		if (!$this->is_allowed('career_lowongan_add')) {
			return;
		}

		$this->data['templates_aktif'] = $this->_templates_aktif();
		$this->template->title('Tambah Lowongan');
		$this->render('backend/standart/administrator/career_lowongan/career_lowongan_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('career_lowongan_add', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}

		$data = $this->_posting_input();
		if (is_string($data)) {
			return $this->_json_deny($data);
		}

		$requirements = $this->_requirements_input();
		if (is_string($requirements)) {
			return $this->_json_deny($requirements);
		}

		if ((int) $data['is_published'] === 1) {
			list($ok, $msg) = $this->_boleh_terbit_data($data, $requirements);
			if (!$ok) {
				return $this->_json_deny($msg);
			}
		}

		$data['created_by'] = (int) $this->session->userdata('id');

		// MED #9: transaksi + trans_status, audit HANYA setelah commit.
		$this->db->trans_start();
		$id = $this->model_career_lowongan->store_lowongan($data);
		$rids = array();
		if ($id > 0) {
			foreach ($requirements as $req) {
				$req['display_order'] = $this->model_career_lowongan->next_order($id);
				$rid = $this->model_career_lowongan->add_requirement($id, $req);
				if ($rid > 0) {
					$rids[] = array('rid' => $rid, 'nama' => $req['requirement_name']);
				}
			}
		}
		$ok = $this->db->trans_complete();

		if (!$ok || $id <= 0) {
			return $this->_json_deny('Gagal menyimpan lowongan.');
		}
		foreach ($rids as $r) {
			career_audit('create_lowongan_requirement', 'job_posting_requirement', $r['rid'], [
				'id_job_posting' => $id,
				'nama' => $r['nama'],
			]);
		}
		career_audit('create_lowongan', 'career_job_posting', $id, [
			'title' => $data['title'],
			'is_published' => (int) $data['is_published'],
			'jumlah_persyaratan' => count($rids),
		]);
		return $this->_json_done(TRUE, $id, base_url('administrator/career_lowongan/edit/' . $id));
	}

	/* ------------------------------------------------------------ */
	/* Ubah lowongan                                                */
	/* ------------------------------------------------------------ */

	public function edit($id)
	{
		if (!$this->is_allowed('career_lowongan_update')) {
			return;
		}

		$posting = $this->model_career_lowongan->find_lowongan($id);
		if (!$posting) {
			show_error('Lowongan tidak ditemukan.', 404);
		}
		if (!$this->is_allowed('career_lowongan_view', false)) {
			show_error('Izin detail lowongan tidak ada', 403);
		}

		$this->data['lowongan']        = $posting;
		$this->data['requirements']    = $this->model_career_lowongan->requirements($id);
		$this->data['punya_lamaran']   = $this->model_career_lowongan->punya_lamaran($id);
		$this->data['dipakai_req']     = $this->model_career_lowongan->dipakai_map($id);
		$this->data['templates_aktif'] = $this->_templates_aktif();

		$this->template->title('Ubah Lowongan');
		$this->render('backend/standart/administrator/career_lowongan/career_lowongan_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}

		$old = $this->model_career_lowongan->find_lowongan($id);
		if (!$old) {
			return $this->_json_deny('Lowongan tidak ditemukan.');
		}

		$data = $this->_posting_input();
		if (is_string($data)) {
			return $this->_json_deny($data);
		}

		// HIGH #3: aturan terbit berlaku juga untuk draf→terbit lewat edit.
		// Validasi memakai tanggal yang DIKIRIM + jumlah persyr di DB
		// (penghitungan dilakukan di dalam transaksi bersama update).
		if ((int) $data['is_published'] === 1) {
			list($ok, $msg) = $this->_boleh_terbit_data($data, NULL);
			if (!$ok) {
				return $this->_json_deny($msg);
			}
		}

		$this->db->trans_start();
		if ((int) $data['is_published'] === 1
			&& $this->model_career_lowongan->hitung_persyaratan($id) < 1) {
			$this->db->trans_rollback();
			return $this->_json_deny('Minimal 1 persyaratan untuk dapat terbit.');
		}
		$ok = $this->model_career_lowongan->change_lowongan($id, $data);
		// 6C nit: transaksi selalu ditutup (auto-rollback bila gagal).
		$this->db->trans_complete();
		$ok = ($ok) && $this->db->trans_status();

		if (!$ok) {
			return $this->_json_done(false, (int) $id, 'Gagal menyimpan.');
		}
		career_audit('update_lowongan', 'career_job_posting', (int) $id, [
			'before' => [
				'title'        => $old->title,
				'is_published' => (int) $old->is_published,
				'opening_date' => $old->opening_date,
				'closing_date' => $old->closing_date,
			],
			'after' => [
				'title'        => $data['title'],
				'is_published' => (int) $data['is_published'],
				'opening_date' => $data['opening_date'],
				'closing_date' => $data['closing_date'],
			],
		]);
		return $this->_json_done(true, (int) $id);
	}

	/* ------------------------------------------------------------ */
	/* Terbit / tutup (aksi eksplisit POST + CSRF + audit)          */
	/* ------------------------------------------------------------ */

	public function terbitkan($id = null)
	{
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}
		$old = $this->model_career_lowongan->find_lowongan($id);
		if (!$old) {
			return $this->_json_done(false, (int) $id, 'Lowongan tidak ditemukan.');
		}
		if ((int) $old->is_published === 1) {
			return $this->_json_done(true, (int) $id, 'Lowongan sudah terbit.');
		}
		list($ok, $msg) = $this->model_career_lowongan->boleh_terbit($id);
		if (!$ok) {
			return $this->_json_done(false, (int) $id, $msg);
		}
		$this->db->trans_start();
		$this->db->where('id_job_posting', (int) $id)
			->update('career_job_postings', array(
				'is_published' => 1,
				'updated_at'   => date('Y-m-d H:i:s'),
			));
		$ok = $this->db->trans_complete();
		if (!$ok) {
			return $this->_json_done(false, (int) $id, 'Gagal menerbitkan.');
		}
		career_audit('publish_lowongan', 'career_job_posting', (int) $id, [
			'title' => $old->title,
		]);
		return $this->_json_done(true, (int) $id, 'Lowongan diterbitkan.');
	}

	public function tutup($id = null)
	{
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}
		$old = $this->model_career_lowongan->find_lowongan($id);
		if (!$old) {
			return $this->_json_done(false, (int) $id, 'Lowongan tidak ditemukan.');
		}
		$this->db->trans_start();
		$this->db->where('id_job_posting', (int) $id)
			->update('career_job_postings', array(
				'is_published' => 0,
				'updated_at'   => date('Y-m-d H:i:s'),
			));
		$ok = $this->db->trans_complete();
		if (!$ok) {
			return $this->_json_done(false, (int) $id, 'Gagal menutup.');
		}
		career_audit('close_lowongan', 'career_job_posting', (int) $id, [
			'title'     => $old->title,
			'lifted'    => 'manual',
			'before'    => (int) $old->is_published,
			'after'     => 0,
		]);
		return $this->_json_done(true, (int) $id, 'Lowongan ditutup (draf).');
	}

	/* ------------------------------------------------------------ */
	/* Persyaratan — tambah, ubah, naik/turun, hapus                */
	/* ------------------------------------------------------------ */

	private function _requirement_payload()
	{
		$type = $this->input->post('requirement_type');
		$type = ($type === 'optional') ? 'optional' : 'required';

		$input = $this->input->post('input_type');
		if (!in_array($input, array('document', 'text', 'number'), TRUE)) {
			if ($input === 'select') {
				return array(NULL, 'Tipe "select" belum didukung (opsi pilihan belum ada di skema) — gunakan teks untuk sekarang.');
			}
			return array(NULL, 'Tipe isian harus document / text / number.');
		}

		$name = trim((string) $this->input->post('requirement_name'));
		if ($name === '' || strlen($name) > 200) {
			return array(NULL, 'Nama persyaratan wajib diisi, maks 200 karakter.');
		}

		$allowed_formats = NULL;
		$max_kb = NULL;
		if ($input === 'document') {
			$fmt = career_file_formats_valid($this->input->post('allowed_formats'));
			if ($fmt === FALSE) {
				return array(NULL, 'allowed_formats hanya: pdf,jpg,jpeg,png,doc,docx.');
			}
			if (count($fmt) === 0) {
				return array(NULL, 'allowed_formats wajib untuk tipe berkas.');
			}
			$allowed_formats = implode(',', $fmt);
			$max_kb = $this->input->post('max_file_size_kb') !== NULL && $this->input->post('max_file_size_kb') !== ''
				? (int) $this->input->post('max_file_size_kb') : NULL;

			if ($max_kb !== NULL && ($max_kb < 1 || $max_kb > 5120)) {
				return array(NULL, 'max_file_size_kb harus 1..5120 KB.');
			}
		}

		$smarty_template = (int) $this->input->post('id_requirement_template');
		if ($smarty_template > 0) {
			// MED #8/#12: template harus ada & aktif, selain itu NULL.
			$tpl = $this->db->where('id_requirement_template', $smarty_template)
				->where('is_active', 1)
				->get('career_requirement_templates')->row();
			$smarty_template = $tpl ? $smarty_template : NULL;
		} else {
			$smarty_template = NULL;
		}
		return array(array(
			'id_requirement_template' => $smarty_template,
			'requirement_type'        => $type,
			'input_type'              => $input,
			'requirement_name'        => $name,
			'allowed_formats'         => $allowed_formats,
			'max_file_size_kb'        => $max_kb,
		), '');
	}

	public function requirement_add($id_job_posting)
	{
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF).');
		}
		if (!$this->model_career_lowongan->find_lowongan($id_job_posting)) {
			return $this->_json_deny('Lowongan tidak ditemukan.');
		}

		list($req, $err) = $this->_requirement_payload();
		if ($req === NULL) {
			return $this->_json_deny($err);
		}

		// MED #12: tambah persyaratan WAJIB pada lowongan yang sudah
		// berlamaran butuh konfirmasi eksplisit dari klien (konfirmasi=1).
		if ($req['requirement_type'] === 'required'
			&& $this->model_career_lowongan->punya_lamaran($id_job_posting)
			&& $this->input->post('konfirmasi') !== '1') {
			return $this->_json_deny('Lowongan ini sudah memiliki lamaran. Menambah persyaratan WAJIB membuat lamaran lama kekurangan syarat — kirim konfirmasi eksplisit (konfirmasi=1).');
		}

		$this->db->trans_start();
		$req['display_order'] = $this->model_career_lowongan->next_order($id_job_posting);
		$rid = $this->model_career_lowongan->add_requirement($id_job_posting, $req);
		// 6C nit: trans_complete TANPA syarat - transaksi selalu ditutup
		// (auto-rollback bila gagal); tidak ada transaksi nyangkut.
		$this->db->trans_complete();
		$ok = ($rid > 0) && $this->db->trans_status();

		if (!$ok) {
			return $this->_json_deny('Gagal menambah persyaratan.');
		}
		career_audit('create_lowongan_requirement', 'job_posting_requirement', $rid, [
			'id_job_posting' => (int) $id_job_posting,
			'nama' => $req['requirement_name'],
			'input_type' => $req['input_type'],
		]);
		return $this->_json_done(true, $rid, base_url('administrator/career_lowongan/edit/' . (int) $id_job_posting));
	}

	public function requirement_update($id_requirement)
	{
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF).');
		}

		$old = $this->model_career_lowongan->find_requirement($id_requirement);
		if (!$old) {
			return $this->_json_deny('Persyaratan tidak ditemukan.');
		}

		list($req, $err) = $this->_requirement_payload();
		if ($req === NULL) {
			return $this->_json_deny($err);
		}
		// D12 — data input tdk berubah setelah dipakai:
		if ($this->model_career_lowongan->requirement_dipakai($id_requirement)) {
			if ($req['input_type'] !== $old->input_type
				|| $req['requirement_type'] !== $old->requirement_type) {
				return $this->_json_deny('Tipe isian/wajib/ops terkunci karena sudah dipakai lamaran — ubah nama, format, ukuran, atau urutan saja.');
			}
		}
		// HIGH #6: display_order DIPERTAHANKAN — payload ubah tidak mengirim urutan.
		unset($req['display_order']);

		$this->db->trans_start();
		$ok = $this->model_career_lowongan->change_requirement($id_requirement, $req, $req['input_type'], $req['requirement_type']);
		// 6C nit: transaksi selalu ditutup (auto-rollback bila gagal).
		$this->db->trans_complete();
		$ok = ($ok) && $this->db->trans_status();

		if (!$ok) {
			return $this->_json_deny('Gagal menyimpan persyaratan.');
		}
		career_audit('update_lowongan_requirement', 'job_posting_requirement', (int) $id_requirement, [
			'id_job_posting' => (int) $old->id_job_posting,
			'before' => ['nama' => $old->requirement_name, 'allowed_formats' => $old->allowed_formats, 'max_file_size_kb' => $old->max_file_size_kb ? (int) $old->max_file_size_kb : NULL],
			'after'  => ['nama' => $req['requirement_name'], 'allowed_formats' => $req['allowed_formats'], 'max_file_size_kb' => $req['max_file_size_kb']],
		]);
		return $this->_json_done(true, (int) $id_requirement,
			base_url('administrator/career_lowongan/edit/' . (int) $old->id_job_posting));
	}

	/** Naik/turun satu posisi — POST + CSRF + swap tetangga NYATA (MED #15). */
	public function requirement_move($id_requirement)
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Metode harus POST.');
		}
		$dir = (string) $this->input->post('dir');
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF).');
		}
		if (!in_array($dir, array('up', 'down'), TRUE)) {
			return $this->_json_deny('Arah hanya up/down.');
		}

		$old = $this->model_career_lowongan->find_requirement($id_requirement);
		if (!$old) {
			return $this->_json_deny('Persyaratan tidak ditemukan.');
		}

		// Baris lowongan diurutkan dulu; tetangga = elemen urutan sebenarnya
		// (bukan display_order ± 1 yang bisa bolong/0 setelah SE deletes).
		$rows = $this->db->where('id_job_posting', (int) $old->id_job_posting)
			->order_by('display_order', 'ASC')
			->order_by('id_job_posting_requirement', 'ASC')
			->select('id_job_posting_requirement, display_order')
			->get('career_job_posting_requirements')->result();
		$idx = NULL;
		foreach ($rows as $i => $r) {
			if ((int) $r->id_job_posting_requirement === (int) $id_requirement) { $idx = $i; break; }
		}
		if ($idx === NULL) { return $this->_json_deny('Persyaratan tidak ditemukan.'); }
		$jdx = ($dir === 'up') ? $idx - 1 : $idx + 1;
		if ($jdx < 0 || $jdx >= count($rows)) {
			return $this->_json_done(true, (int) $id_requirement);  // sudah di ujung
		}

		$this->db->trans_start();
		$this->db->where('id_job_posting_requirement', (int) $rows[$jdx]->id_job_posting_requirement)
			->update('career_job_posting_requirements', ['display_order' => (int) $rows[$idx]->display_order]);
		$this->db->where('id_job_posting_requirement', (int) $rows[$idx]->id_job_posting_requirement)
			->update('career_job_posting_requirements', ['display_order' => (int) $rows[$jdx]->display_order]);
		$ok = $this->db->trans_complete();
		if (!$ok) {
			return $this->_json_deny('Gagal mengubah urutan.');
		}
		career_audit('move_lowongan_requirement', 'job_posting_requirement', (int) $id_requirement, [
			'id_job_posting' => (int) $old->id_job_posting,
			'nama' => $old->requirement_name,
			'urutan' => $dir,
		]);
		return $this->_json_done(true, (int) $id_requirement);
	}

	/** Hapus persyarat — POST + CSRF + perm update (D12 + MED #10/#25). */
	public function requirement_delete($id_requirement)
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Metode harus POST.');
		}
		if (!$this->is_allowed('career_lowongan_update', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF).');
		}

		$old = $this->model_career_lowongan->find_requirement($id_requirement);
		if (!$old) {
			return $this->_json_done(false, (int) $id_requirement, 'Persyaratan tidak ditemukan.');
		}

		if ($this->model_career_lowongan->delete_requirement($id_requirement)) {
			career_audit('delete_lowongan_requirement', 'job_posting_requirement', (int) $id_requirement, [
				'id_job_posting' => (int) $old->id_job_posting,
				'nama' => $old->requirement_name,
			]);
			return $this->_json_done(true, (int) $id_requirement, 'Persyaratan dihapus.');
		}
		return $this->_json_done(false, (int) $id_requirement,
			'Persyaratan sudah dipakai lamaran dan tidak boleh dihapus (jawaban/dokumen kandidat ikut terhapus).');
	}

	/* ------------------------------------------------------------ */
	/* Hapus lowongan                                               */
	/* ------------------------------------------------------------ */

	public function delete($id = null)
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Metode harus POST.');
		}
		if (!$this->is_allowed('career_lowongan_delete', false)) {
			return $this->_json_deny(cclang('sorry_you_do_not_have_permission_to_access'));
		}
		if (!career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF).');
		}

		$old = $this->model_career_lowongan->find_lowongan($id);
		if (!$old) {
			return $this->_json_done(false, (int) $id, 'Lowongan tidak ditemukan.');
		}

		if ($this->model_career_lowongan->delete_lowongan($id)) {
			career_audit('delete_lowongan', 'career_job_posting', (int) $id, [
				'title' => $old->title,
			]);
			return $this->_json_done(true, (int) $id, 'Lowongan dihapus.');
		}
		// D12 + FK RESTRICT: lowongan ada lamaran → tutup saja
		return $this->_json_done(false, (int) $id,
			'Lowongan sudah memiliki lamaran dan tidak boleh dihapus — ubah status jadi tutup / unpublikasi.');
	}

	/* ------------------------------------------------------------ */
	/* Helper                                                       */
	/* ------------------------------------------------------------ */

	private function _templates_aktif()
	{
		return $this->db->where('is_active', 1)
			->order_by('name', 'ASC')
			->get('career_requirement_templates')
			->result();
	}

	/** Input head posting dgn validasi — hasil ARRAY atau STRING pesan error. */
	private function _posting_input()
	{
		$title = trim((string) $this->input->post('title'));
		if ($title === '' || strlen($title) > 200) {
			return 'Judul wajib diisi, maks 200 karakter.';
		}

		$description = (string) $this->input->post('description');
		if (strlen($description) > 10000) {
			return 'Deskripsi maksimal 10.000 karakter.';
		}

		$opening = $this->input->post('opening_date');
		$closing = $this->input->post('closing_date');
		if ($opening === NULL || $opening === '' || $closing === NULL || $closing === '') {
			return 'Tanggal buka dan tutup wajib diisi.';
		}
		// MED #14: parse ketat YYYY-MM-DD (strtotime menerima "now"/"monday").
		$open_d  = DateTime::createFromFormat('Y-m-d', (string) $opening);
		$close_d = DateTime::createFromFormat('Y-m-d', (string) $closing);
		if (!$open_d || !$close_d
			|| $open_d->format('Y-m-d') !== (string) $opening
			|| $close_d->format('Y-m-d') !== (string) $closing) {
			return 'Format tanggal tidak sah (YYYY-MM-DD).';
		}
		if ($close_d < $open_d) {
			return 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.';
		}

		$edu = trim((string) $this->input->post('education_level'));
		if (strlen($edu) > 20) {
			return 'Jenjang pendidikan maks 20 karakter.';
		}

		return array(
			'title'           => $title,
			'description'     => $description,
			'opening_date'    => $open_d->format('Y-m-d'),
			'closing_date'    => $close_d->format('Y-m-d'),
			'education_level' => $edu === '' ? NULL : $edu,
			'is_published'    => $this->input->post('is_published') == '1' ? 1 : 0,
		);
	}

	/**
	 * Input baris persyarat utk add_save — MED #8: parallel arrays SELALU
	 * disubmit utk semua baris (tanpa disabled) sehingga index tidak
	 * bergeser. Kolom format/KB hanya dipakai bila input_type document.
	 */
	private function _requirements_input()
	{
		$names  = $this->input->post('requirement_name');
		$types  = $this->input->post('requirement_type');
		$inputs = $this->input->post('input_type');
		$fmts   = $this->input->post('allowed_formats');
		$kbs    = $this->input->post('max_file_size_kb');
		$templates = $this->input->post('id_requirement_template');

		foreach (array($names, $types, $inputs, $fmts, $kbs, $templates) as $v) {
			if ($v !== NULL && !is_array($v)) {
				return 'Baris persyaratan tidak sah.';
			}
		}
		if ($names === NULL) { $names = array(); }
		$n = count($names);
		$out = array();
		for ($i = 0; $i < $n; $i++) {
			$nm = trim((string) (isset($names[$i]) ? $names[$i] : ''));
			if ($nm === '') {
				continue; // baris kosong dilewati
			}
			if (strlen($nm) > 200) {
				return 'Baris persyaratan ' . ($i + 1) . ': nama maks 200 karakter.';
			}

			$input = isset($inputs[$i]) ? (string) $inputs[$i] : 'text';
			if ($input === 'select') {
				return 'Baris persyaratan ' . ($i + 1) . ': Tipe "select" belum didukung (opsi pilihan belum ada di skema) — gunakan teks untuk sekarang.';
			}
			if (!in_array($input, array('document', 'text', 'number'), TRUE)) {
				return 'Baris persyaratan ' . ($i + 1) . ': tipe isian tidak sah.';
			}

			$allowed = NULL;
			$max_kb  = NULL;
			if ($input === 'document') {
				$fmt = career_file_formats_valid(isset($fmts[$i]) ? $fmts[$i] : '');
				if ($fmt === FALSE || count($fmt) === 0) {
					return 'Baris persyaratan ' . ($i + 1) . ': format berkas tidak sah / kosong.';
				}
				$allowed = implode(',', $fmt);
				$max_kb = isset($kbs[$i]) && $kbs[$i] !== '' ? (int) $kbs[$i] : NULL;
				if ($max_kb !== NULL && ($max_kb < 1 || $max_kb > 5120)) {
					return 'Baris ' . ($i + 1) . ': max_file_size_kb harus 1..5120 KB.';
				}
			}

			$type = (isset($types[$i]) && $types[$i] === 'optional') ? 'optional' : 'required';

			$tpl_id = isset($templates[$i]) ? (int) $templates[$i] : 0;
			if ($tpl_id > 0) {
				$tpl = $this->db->where('id_requirement_template', $tpl_id)
					->where('is_active', 1)
					->get('career_requirement_templates')->row();
				$tpl_id = $tpl ? $tpl_id : 0;
			}

			$out[] = array(
				'id_requirement_template' => $tpl_id > 0 ? $tpl_id : NULL,
				'requirement_type' => $type,
				'input_type'       => $input,
				'requirement_name' => $nm,
				'allowed_formats'  => $allowed,
				'max_file_size_kb' => $max_kb,
			);
		}
		return $out;
	}

	/** Cek boleh terbit dari Data BELUM disimpan (add/edit). $requirements NULL → hanya tanggal. */
	private function _boleh_terbit_data($data, $requirements)
	{
		if ($requirements !== NULL && count($requirements) < 1) {
			return array(FALSE, 'Minimal 1 persyaratan untuk dapat terbit.');
		}
		if ($data['closing_date'] < $data['opening_date']) {
			return array(FALSE, 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.');
		}
		return array(TRUE, '');
	}

	private function _json_done($success, $id = NULL, $redirect = NULL, $message = NULL)
	{
		$this->output->set_content_type('application/json')->set_output(json_encode([
			'success'  => (bool) $success,
			'id'       => $id,
			'message'  => $message !== NULL ? $message : ($success ? 'Tersimpan.' : 'Gagal menyimpan.'),
			'redirect' => $redirect,
		]));
	}

	private function _json_deny($message, $status = 403)
	{
		$this->output->set_status_header($status);
		$this->output->set_content_type('application/json')->set_output(json_encode([
			'success' => false,
			'message' => $message,
		]));
	}
}
