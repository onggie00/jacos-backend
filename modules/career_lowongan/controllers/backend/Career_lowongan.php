<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Lowongan Controller (Fase 6A)
*| --------------------------------------------------------------------------
*| CRUD lowongan kerja + editor persyaratannya.
*| D12: lowongan dgn lamaran tidak dihapus; baris persyarat yang dipakai
*| lamaran tidak dihapus; input_type/requirement_type terkunci setelah
*| ada lamaran. Semua mutasi dicatat ke career_audit_logs.
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

		$q      = $this->input->get('q');
		$status = $this->input->get('status');

		$this->data['q']       = $q;
		$this->data['status']  = $status;
		$this->data['lowongans']       = $this->model_career_lowongan->lowongan_list($q, $status, $this->limit_page, $offset);
		$this->data['lowongan_counts'] = $this->model_career_lowongan->lowongan_count($q, $status);
		foreach ($this->data['lowongans'] as $row) {
			$row->status_tampil = $this->model_career_lowongan->status_tampil($row);
			$row->punya_lamaran = $this->model_career_lowongan->punya_lamaran($row->id_job_posting);
		}

		$config = [
			'base_url'    => 'administrator/career_lowongan/index/',
			'total_rows'  => $this->data['lowongan_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Lowongan Kerja');
		$this->render('backend/standart/administrator/career_lowongan/career_lowongan_list', $this->data);
	}

	/* ------------------------------------------------------------ */
	/* Buat lowongan                                                */
	/* ------------------------------------------------------------ */

	public function add()
	{
		$this->is_allowed('career_lowongan_add');

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

		$this->db->trans_start();
		$id = $this->model_career_lowongan->store_lowongan($data);
		foreach ($requirements as $req) {
			$req['display_order'] = $this->model_career_lowongan->next_order($id);
			$rid = $this->model_career_lowongan->add_requirement($id, $req);
			career_audit('create_lowongan_requirement', 'job_posting_requirement', $rid, [
				'id_job_posting' => $id,
				'nama' => $req['requirement_name'],
			]);
		}
		career_audit('create_lowongan', 'career_job_posting', $id, [
			'title' => $data['title'],
			'is_published' => (int) $data['is_published'],
			'jumlah_persyaratan' => count($requirements),
		]);
		$this->db->trans_complete();

		if (!$id) {
			return $this->_json_deny('Gagal menyimpan lowongan.');
		}
		return $this->_json_done(TRUE, $id, base_url('administrator/career_lowongan/edit/' . $id));
	}

	/* ------------------------------------------------------------ */
	/* Ubah lowongan                                                */
	/* ------------------------------------------------------------ */

	public function edit($id)
	{
		$this->is_allowed('career_lowongan_update');

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

		if ((int) $data['is_published'] === 1 && (int) $old->is_published === 1) {
			list($ok, $msg) = $this->model_career_lowongan->boleh_terbit($id);
			if (!$ok) {
				return $this->_json_deny($msg);
			}
		}

		$this->db->trans_start();
		$ok = $this->model_career_lowongan->change_lowongan($id, $data);
		career_audit('update_lowongan', 'career_job_posting', $id, [
			'before' => ['title' => $old->title, 'is_published' => (int) $old->is_published],
			'after'  => ['title' => $data['title'], 'is_published' => (int) $data['is_published']],
		]);
		$this->db->trans_complete();

		return $this->_json_done($ok, (int) $id);
	}

	/* ------------------------------------------------------------ */
	/* Persyaratan — tambah, ubah, naik/turun, hapus                */
	/* ------------------------------------------------------------ */

	private function _requirement_payload()
	{
		$type = $this->input->post('requirement_type');
		$type = ($type === 'optional') ? 'optional' : 'required';

		$input = $this->input->post('input_type');
		if (!in_array($input, array('document', 'text', 'number', 'select'), TRUE)) {
			return array(NULL, 'Tipe isian harus document / text / number / select.');
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
		if ($input === 'select') {
			return array(NULL, 'Tipe "select" belum didukung (opsi pilihan belum ada di skema) — gunakan teks untuk sekarang.');
		}

		$smarty_template = (int) $this->input->post('id_requirement_template');
		return array(array(
			'id_requirement_template' => $smarty_template > 0 ? $smarty_template : NULL,
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

		// Template dipakai sebagai sumber nilai awal (bukan relasi utk posting)
		$this->db->trans_start();
		$req['display_order'] = $this->model_career_lowongan->next_order($id_job_posting);
		$rid = $this->model_career_lowongan->add_requirement($id_job_posting, $req);
		career_audit('create_lowongan_requirement', 'job_posting_requirement', $rid, [
			'id_job_posting' => (int) $id_job_posting,
			'nama' => $req['requirement_name'],
			'input_type' => $req['input_type'],
		]);
		$this->db->trans_complete();

		return $this->_json_done($rid > 0, $rid, base_url('administrator/career_lowongan/edit/' . (int) $id_job_posting));
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

		$this->db->trans_start();
		$ok = $this->model_career_lowongan->change_requirement($id_requirement, $req, $req['input_type'], $req['requirement_type']);
		career_audit('update_lowongan_requirement', 'job_posting_requirement', $id_requirement, [
			'id_job_posting' => (int) $old->id_job_posting,
			'nama' => $req['requirement_name'],
		]);
		$this->db->trans_complete();

		return $this->_json_done($ok, (int) $id_requirement,
			base_url('administrator/career_lowongan/edit/' . (int) $old->id_job_posting));
	}

	/** Naik/turun satu posisi (display_order swap). */
	public function requirement_move($id_requirement, $dir)
	{
		if (!$this->is_allowed('career_lowongan_update', false)) {
			set_message(cclang('sorry_you_do_not_have_permission_to_access'), 'error');
			redirect_back();
		}
		if (!career_csrf_ok()) {
			set_message('Sesi tidak sah (CSRF).', 'error');
			redirect_back();
		}

		$old = $this->model_career_lowongan->find_requirement($id_requirement);
		if (!$old) {
			set_message('Persyaratan tidak ditemukan.', 'error');
			redirect('administrator/career_lowongan/edit/' . (int) $this->input->post('id_job_posting'), 'refresh');
		}

		$delta   = ($dir === 'up') ? -1 : 1;
		$neighbor = $this->db
			->where('id_job_posting', (int) $old->id_job_posting)
			->where('display_order', (int) $old->display_order + $delta)
			->get('career_job_posting_requirements')->row();

		if ($neighbor) {
			$this->db->trans_start();
			$this->db->where('id_job_posting_requirement', (int) $neighbor->id_job_posting_requirement)
				->update('career_job_posting_requirements', ['display_order' => (int) $old->display_order]);
			$this->db->where('id_job_posting_requirement', (int) $old->id_job_posting_requirement)
				->update('career_job_posting_requirements', ['display_order' => (int) $neighbor->display_order]);
			career_audit('move_lowongan_requirement', 'job_posting_requirement', $id_requirement, [
				'id_job_posting' => (int) $old->id_job_posting,
				'nama' => $old->requirement_name,
				'urutan' => $dir,
			]);
			$this->db->trans_complete();
		}

		redirect('administrator/career_lowongan/edit/' . (int) $old->id_job_posting, 'refresh');
	}

	/** Hapus persyarat — hanya yang belum dipakai lamaran (D12). */
	public function requirement_delete($id_requirement)
	{
		if (!$this->is_allowed('career_lowongan_delete', false)) {
			set_message(cclang('sorry_you_do_not_have_permission_to_access'), 'error');
			redirect_back();
		}

		$old = $this->model_career_lowongan->find_requirement($id_requirement);
		if (!$old) {
			set_message('Persyaratan tidak ditemukan.', 'error');
			redirect('administrator/career_lowongan', 'refresh');
		}

		if ($this->model_career_lowongan->delete_requirement($id_requirement)) {
			career_audit('delete_lowongan_requirement', 'job_posting_requirement', $id_requirement, [
				'id_job_posting' => (int) $old->id_job_posting,
				'nama' => $old->requirement_name,
			]);
			set_message('Persyaratan dihapus.', 'success');
		} else {
			set_message('Persyaratan sudah dipakai lamaran dan tidak boleh dihapus (jawaban/dokumen kandidat ikut terhapus).', 'error');
		}

		redirect('administrator/career_lowongan/edit/' . (int) $old->id_job_posting, 'refresh');
	}

	/* ------------------------------------------------------------ */
	/* Hapus lowongan                                               */
	/* ------------------------------------------------------------ */

	public function delete($id = null)
	{
		if (!$this->is_allowed('career_lowongan_delete', false)) {
			set_message(cclang('sorry_you_do_not_have_permission_to_access'), 'error');
			redirect('administrator/career_lowongan', 'refresh');
		}

		$old = $this->model_career_lowongan->find_lowongan($id);
		if (!$old) {
			set_message('Lowongan tidak ditemukan.', 'error');
			redirect('administrator/career_lowongan', 'refresh');
		}

		if ($this->model_career_lowongan->delete_lowongan($id)) {
			career_audit('delete_lowongan', 'career_job_posting', $id, [
				'title' => $old->title,
			]);
			set_message('Lowongan dihapus.', 'success');
		} else {
			// D12 + FK RESTRICT: lowongan ada lamaran → tutup saja
			set_message('Lowongan sudah memiliki lamaran dan tidak boleh dihapus — ubah status jadi tutup / unpublikasi.', 'error');
		}

		redirect('administrator/career_lowongan', 'refresh');
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

		$opening = $this->input->post('opening_date');
		$closing = $this->input->post('closing_date');
		if ($opening === NULL || $opening === '' || $closing === NULL || $closing === '') {
			return 'Tanggal buka dan tutup wajib diisi.';
		}
		$open_t  = strtotime($opening);
		$close_t = strtotime($closing);
		if ($open_t === FALSE || $close_t === FALSE) {
			return 'Format tanggal tidak sah (YYYY-MM-DD).';
		}
		if ($close_t < $open_t) {
			return 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.';
		}

		$edu = trim((string) $this->input->post('education_level'));
		if (strlen($edu) > 20) {
			return 'Jenjang pendidikan maks 20 karakter.';
		}

		return array(
			'title'           => $title,
			'description'     => (string) $this->input->post('description'),
			'opening_date'    => date('Y-m-d', $open_t),
			'closing_date'    => date('Y-m-d', $close_t),
			'education_level' => $edu === '' ? NULL : $edu,
			'is_published'    => $this->input->post('is_published') == '1' ? 1 : 0,
		);
	}

	/** Input baris persyarat utk add_save (masa awal). */
	private function _requirements_input()
	{
		$names = $this->input->post('requirement_name') !== NULL ? $this->input->post('requirement_name') : array();
		if (!is_array($names)) {
			return 'Baris persyaratan tidak sah.';
		}
		$out = array();
		foreach ($names as $i => $nm) {
			if (trim((string) $nm) === '') {
				continue;
			}
			$this->load->helper('form');
			$input = isset($this->input->post('input_type')[$i]) ? $this->input->post('input_type')[$i] : 'text';
			if (!in_array($input, array('document', 'text', 'number', 'select'), TRUE)) {
				return 'Baris persyratan ' . ($i + 1) . ': tipe isian tidak sah.';
			}
			$allowed = NULL;
			$max_kb  = NULL;
			if ($input === 'document') {
				$fmt = career_file_formats_valid(isset($this->input->post('allowed_formats')[$i]) ? $this->input->post('allowed_formats')[$i] : '');
				if ($fmt === FALSE || count($fmt) === 0) {
					return 'Baris persyratan ' . ($i + 1) . ': format berkas tidak sah / kosong.';
				}
				$allowed = implode(',', $fmt);
				$mkb = isset($this->input->post('max_file_size_kb')[$i]) ? $this->input->post('max_file_size_kb')[$i] : '';
				$max_kb = $mkb !== '' && $mkb !== NULL ? (int) $mkb : NULL;
				if ($max_kb !== NULL && ($max_kb < 1 || $max_kb > 5120)) {
					return 'Baris ' . ($i + 1) . ': max_file_size_kb harus 1..5120 KB.';
				}
			}
			$out[] = array(
				'id_requirement_template' => NULL,
				'requirement_type' => (isset($this->input->post('requirement_type')[$i]) && $this->input->post('requirement_type')[$i] === 'optional') ? 'optional' : 'required',
				'input_type'       => $input,
				'requirement_name' => trim((string) $nm),
				'allowed_formats'  => $allowed,
				'max_file_size_kb' => $max_kb,
			);
		}
		return $out;
	}

	/** Cek boleh terbed dari DATa belum disimpan utk add_save (tanpa red akt ke DB). */
	private function _boleh_terbit_data($data, $requirements)
	{
		if (count($requirements) < 1) {
			return array(FALSE, 'Minimal 1 persyaratan untuk dapat terbit.');
		}
		if ($data['opening_date'] === NULL || $data['closing_date'] === NULL) {
			return array(FALSE, 'Tanggal buka dan tutup wajib diisi.');
		}
		if ($data['closing_date'] < $data['opening_date']) {
			return array(FALSE, 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.');
		}
		return array(TRUE, '');
	}

	private function _json_done($success, $id = NULL, $redirect = NULL)
	{
		echo json_encode([
			'success'  => (bool) $success,
			'id'       => $id,
			'message'  => $success ? 'Tersimpan.' : 'Gagal menyimpan.',
			'redirect' => $redirect,
		]);
		exit;
	}

	private function _json_deny($message)
	{
		echo json_encode(['success' => false, 'message' => $message]);
		exit;
	}
}
