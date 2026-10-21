<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Guru Kb Controller (GURU.1 Bagian A)
*| --------------------------------------------------------------------------
*| Kelola data guru KB. Clone terkendali dari modul guru_sd dengan
*| perbaikan keamanan (TANPA cacat yang dilarang prompt A-3):
*|  - tanpa mata_pelajaran_sd (mapel tidak dipakai untuk KB)
*|  - satuan_pendidikan / presensi_role / unit diset SERVER-side
*|  - emp_code & email unik LINTAS semua tabel guru (helper guru_core,
*|    query terparameter — bukan raw SQL)
*|  - upload foto whitelist jpg/jpeg/png/webp maks 2MB; nama file dibersihkan
*|  - redirect ke URL modul (bukan HTTP_REFERER)
*|  - soft delete deleted_at (filter deleted_at IS NULL)
*|
*| Catatan GURU.1: fitur slip gaji & import Excel TIDAK disalin untuk KB/TK
*| (diputuskan di report — alur slip gaji milik guru_sd; import dapat
*| ditambahkan kemudian bila jadi kebutuhan).
*/
class Guru_kb extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_guru_kb');
		$this->load->helper('guru_core/guru_core');
		$this->load->helper('text');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* Daftar guru KB
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('guru_kb_list');

		$filter   = $this->input->get('q');
		$field    = 'nama_lengkap';
		$sort     = $this->input->get('s');
		$sort_type = $this->input->get('d');

		/* View tidak boleh panggil $this->is_allowed (Loader) — flag saja. */
		$this->data['bisa_add']    = $this->is_allowed('guru_kb_add', false);
		$this->data['bisa_export'] = $this->is_allowed('guru_kb_export', false);
		$this->data['bisa_view']   = $this->is_allowed('guru_kb_view', false);
		$this->data['bisa_update'] = $this->is_allowed('guru_kb_update', false);
		$this->data['bisa_delete'] = $this->is_allowed('guru_kb_delete', false);

		$this->data['guru_kbs'] = $this->model_guru_kb->get($filter, $field, $this->limit_page, $offset, [], $sort, $sort_type);
		$this->data['guru_kb_counts'] = $this->model_guru_kb->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/guru_kb/index/',
			'total_rows'   => $this->model_guru_kb->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Guru Kb List');
		$this->render('backend/standart/administrator/guru_kb/guru_kb_list', $this->data);
	}

	/**
	* Form tambah guru KB
	*/
	public function add()
	{
		$this->is_allowed('guru_kb_add');

		$this->data['posisi'] = $this->_daftar_posisi();

		$this->template->title('Guru Kb New');
		$this->render('backend/standart/administrator/guru_kb/guru_kb_add', $this->data);
	}

	/**
	* Simpan guru KB baru (POST JSON)
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('guru_kb_add', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		$result = $this->_simpan(null);
		echo json_encode($result);
	}

	/**
	* Form ubah guru KB
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('guru_kb_update');

		$id = (int) $id;
		$this->data['guru_kb'] = $this->model_guru_kb->find($id);
		$this->data['posisi'] = $this->_daftar_posisi();

		if (!$this->data['guru_kb'] || $this->data['guru_kb']->deleted_at !== NULL) {
			set_message('Data guru KB tidak ditemukan.', 'error');
			redirect('administrator/guru_kb');
		}

		$this->template->title('Guru Kb Update');
		$this->render('backend/standart/administrator/guru_kb/guru_kb_update', $this->data);
	}

	/**
	* Simpan perubahan guru KB (POST JSON)
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('guru_kb_update', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		$id = (int) $id;
		$result = $this->_simpan($id);
		echo json_encode($result);
	}

	/**
	* Detail guru KB
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('guru_kb_view');

		$id = (int) $id;
		$this->data['guru_kb'] = $this->model_guru_kb->find($id);

		if (!$this->data['guru_kb'] || $this->data['guru_kb']->deleted_at !== NULL) {
			set_message('Data guru KB tidak ditemukan.', 'error');
			redirect('administrator/guru_kb');
		}

		$this->template->title('Guru Kb View');
		$this->render('backend/standart/administrator/guru_kb/guru_kb_view', $this->data);
	}

	/**
	* Hapus guru KB (soft delete deleted_at).
	*
	* @var $id String
	*/
	public function delete($id)
	{
		if (!$this->is_allowed('guru_kb_delete', false)) {
			set_message(cclang('sorry_you_do_not_have_permission_to_access'), 'error');
			redirect('administrator/guru_kb');
		}

		$id = (int) $id;
		$guru = $this->model_guru_kb->find($id);

		if (!$guru || $guru->deleted_at !== NULL) {
			set_message('Data guru KB tidak ditemukan.', 'error');
			redirect('administrator/guru_kb');
		}

		$this->db->where('id_guru', $id)->update('guru_kb', ['deleted_at' => date('Y-m-d H:i:s')]);

		set_message(cclang('success_delete_data', ['Data guru KB']), 'success');
		redirect('administrator/guru_kb');
	}

	/**
	* Export CSV tanpa kolom rahasia (token/login_token/token_expired/
	* device_id/slip_gaji tidak diekspor).
	*/
	public function export()
	{
		$this->is_allowed('guru_kb_export');

		$this->load->model('model_guru_kb');
		$rows = $this->model_guru_kb->get(null, null, 0, 0);

		$kolom = $this->_kolom_export();
		$nama_file = 'guru_kb_' . date('YmdHis') . '.csv';

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $nama_file . '"');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, $kolom);
		foreach ($rows as $r) {
			$baris = [];
			foreach ($kolom as $k) {
				$baris[] = isset($r->$k) ? $r->$k : '';
			}
			fputcsv($fp, $baris);
		}
		fclose($fp);
		exit;
	}

	private function _kolom_export()
	{
		return [
			'id_guru', 'nama_lengkap', 'nik', 'nuptk', 'npp', 'npwp',
			'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'status_menikah',
			'jumlah_anak', 'no_telp', 'email', 'email_ms_office',
			'satuan_pendidikan', 'unit', 'status_kepegawaian', 'emp_code',
			'keterangan_jabatan', 'jam_ajar', 'pendidikan_terakhir',
			'universitas', 'jurusan', 'tahun_lulus', 'presensi_role',
			'created_at',
		];
	}

	/**
	* Endpoint upload foto guru KB (fineUploader qqfile → tmp; pindah saat
	* simpan). Whitelist jpg/jpeg/png/webp, maks 2000 KB.
	*/
	public function upload_foto_file()
	{
		if (!$this->is_allowed('guru_kb_add', false)) {
			echo json_encode(['success' => false, 'error' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		$uuid = (string) $this->input->post('qquuid');
		if (!preg_match('/^[A-Za-z0-9\-\.]{8,64}$/', $uuid)) {
			echo json_encode(['success' => false, 'error' => 'uuid tidak sah']);
			exit;
		}

		echo $this->upload_file([
			'uuid'          => $uuid,
			'allowed_types' => 'jpg|jpeg|png|webp',
			'max_size'      => 2000,
			'table_name'    => 'guru_kb',
		]);
	}

	/** Hapus file tmp upload. */
	public function delete_foto_file($uuid)
	{
		if (!$this->is_allowed('guru_kb_add', false)) {
			echo json_encode(['success' => false, 'error' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		echo $this->delete_file(['uuid' => (string) $uuid]);
	}

	/**
	* Validasi + simpan (add/edit dipakai bersama).
	*
	* @param int|null $id  NULL = tambah; angka = edit id_guru
	* @param stdClass|null $row eingefügt pkg (dipakai konversi B lewat model saja)
	*/
	private function _simpan($id = null)
	{
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'Nuptk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'Npp', 'trim|max_length[255]');
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim');
		$this->form_validation->set_rules('status_menikah', 'Status Menikah', 'trim');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|integer');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[255]|valid_email');
		$this->form_validation->set_rules('email_ms_office', 'Email MS Office', 'trim|max_length[255]|valid_email');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|integer');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('keterangan_jabatan', 'Keterangan Jabatan', 'trim|max_length[50]');
		$this->form_validation->set_rules('jam_ajar', 'Jam Ajar', 'trim|max_length[50]');
		$this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim');
		$this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|max_length[50]');
		$this->form_validation->set_rules('pendidikan_terakhir', 'Pendidikan Terakhir', 'trim|max_length[50]');
		$this->form_validation->set_rules('universitas', 'Universitas', 'trim|max_length[200]');
		$this->form_validation->set_rules('jurusan', 'Jurusan', 'trim|max_length[150]');
		$this->form_validation->set_rules('tahun_lulus', 'Tahun Lulus', 'trim|integer');

		if (!$this->form_validation->run()) {
			return [
				'success' => false,
				'message' => 'Validasi gagal',
				'errors'  => $this->form_validation->error_array(),
			];
		}

		// Validasi manual NIK (callback form_validation tidak sinkron di HMVC
		// modul — dihindari sesi pelajaran modul lain).
		$nik = trim((string) $this->input->post('nik'));
		if ($nik !== '' && !preg_match('/^[0-9]{16}$/', $nik)) {
			return ['success' => false, 'message' => 'NIK harus 16 digit angka bila diisi.'];
		}

		$emp_code = trim((string) $this->input->post('emp_code'));
		$email    = trim((string) $this->input->post('email'));
		$email_ms = trim((string) $this->input->post('email_ms_office'));

		// Unik lintas tabel guru (helper terparameter; query telanjang DIHINDARI).
		$kecuali_tabel = 'guru_kb';
		$kecuali_id = $id !== null ? (int) $id : 0;

		if ($terpakai = emp_code_terpakai($emp_code, $kecuali_tabel, $kecuali_id)) {
			return ['success' => false, 'message' => 'Emp Code sudah digunakan guru lain (' . $terpakai['tabel'] . ' #' . $terpakai['id'] . ').'];
		}

		$this->_tanda_email_sudah_cek = [];
		foreach (array('email', 'email_ms_office') as $fld) {
			$value = ($fld === 'email') ? $email : $email_ms;
			if ($value !== '' && ($terpakai_email = guru_email_terpakai($value, $kecuali_tabel, $kecuali_id))) {
				return ['success' => false, 'message' => 'Email ' . $fld . ' sudah dipakai guru lain (' . $terpakai_email['tabel'] . ' #' . $terpakai_email['id'] . ').'];
			}
		}

		$tgl_lahir = $this->_tanggal_sah($this->input->post('tgl_lahir'));
		if ($tgl_lahir === FALSE) {
			return ['success' => false, 'message' => 'Tanggal lahir tidak sah (format YYYY-MM-DD).'];
		}

		// Foto via fineUploader tmp → direktori modul; nama dibersihkan.
		$foto_nama = $this->_pindah_foto();

		if ($foto_nama === FALSE) {
			return ['success' => false, 'message' => 'Gagal memindahkan berkas foto.'];
		}

		// SERVER-side: satuan_pendidikan, unit, presensi_role — BUKAN POST.
		$save_data = [
			'nama_lengkap'     => (string) strip_tags($this->input->post('nama_lengkap')),
			'nik'              => $this->input->post('nik'),
			'nuptk'            => $this->input->post('nuptk'),
			'npp'              => $this->input->post('npp'),
			'npwp'             => $this->input->post('npwp'),
			'alamat'           => $this->input->post('alamat'),
			'agama'            => $this->input->post('agama'),
			'jenis_kelamin'    => $this->input->post('jenis_kelamin'),
			'status_menikah'   => $this->input->post('status_menikah'),
			'jumlah_anak'      => $this->input->post('jumlah_anak'),
			'no_telp'          => $this->input->post('no_telp'),
			'email'            => $email,
			'id_posisi'        => $this->input->post('id_posisi') ?: null,
			'satuan_pendidikan'=> 'KB',
			'unit'             => 'KB',
			'status_kepegawaian' => $this->input->post('status_kepegawaian'),
			'informasi_kepala_pimpinan' => $this->input->post('informasi_kepala_pimpinan'),
			'emp_code'         => $emp_code,
			'token'            => null,
			'token_expired'    => null,
			'email_ms_office'  => $email_ms,
			'no_kk'            => $this->input->post('no_kk'),
			'tempat_lahir'     => $this->input->post('tempat_lahir'),
			'tgl_lahir'        => $tgl_lahir,
			'presensi_role'    => guru_presensi_role('KB'),
			'keterangan_jabatan' => $this->input->post('keterangan_jabatan'),
			'jam_ajar'         => $this->input->post('jam_ajar'),
			'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir'),
			'universitas'      => $this->input->post('universitas'),
			'jurusan'          => $this->input->post('jurusan'),
			'tahun_lulus'      => $this->input->post('tahun_lulus'),
		];

		if ($foto_nama) {
			$save_data['foto_profil'] = $foto_nama;
		}

		if ($id === null) {
			$save_data['login_token'] = '';
			$save_guru_kb = $this->model_guru_kb->store($save_data);
		} else {
			$save_guru_kb = $this->model_guru_kb->change($id, $save_data);
		}

		if ($save_guru_kb) {
			$url_edit = ($id === null)
				? 'administrator/guru_kb/edit/' . (int) $save_guru_kb
				: 'administrator/guru_kb/view/' . (int) $id;
			$url_list = 'administrator/guru_kb';

			return [
				'success' => true,
				'id'      => ($id === null) ? (int) $save_guru_kb : (int) $id,
				'message' => cclang('success_save_data_stay', [
					anchor($url_edit, 'View Guru KB'),
					anchor($url_list, ' Go back to list'),
				]),
				'redirect' => base_url($url_list),
			];
		}

		return ['success' => false, 'message' => cclang('data_not_change')];
	}

	/** Daftar posisi guru (dropdown form). */
	private function _daftar_posisi()
	{
		$rows = $this->db->select('id_posisi, nama_posisi')
			->order_by('id_posisi', 'ASC')
			->get('posisi_guru')
			->result();
		$out = [];
		foreach ($rows as $r) {
			$out[$r->id_posisi] = $r->nama_posisi;
		}
		return $out;
	}

	/** Validasi tanggal server (YYYY-MM-DD), '' boleh. */
	private function _tanggal_sah($value)
	{
		$value = trim((string) $value);
		if ($value === '') return NULL;
		$d = DateTime::createFromFormat('Y-m-d', $value);
		if (!$d || $d->format('Y-m-d') !== $value) return FALSE;
		return $value;
	}

	/**
	* Pindahkan/simpan foto dari input "foto_profil" ($_FILES) ke
	* uploads/guru_kb/ — whitelist ext + maks 2MB via CI upload lib;
	* nama file dibangkitkan server (tTimestamp + token acak), TIDAK dari klien.
	* Return nama file baru, '' bila tidak ada, FALSE bila gagal.
	*/
	private function _pindah_foto()
	{
		if (empty($_FILES['foto_profil']['name'])) {
			return '';
		}

		$direktori = FCPATH . 'uploads/guru_kb/';
		if (!is_dir($direktori) && !@mkdir($direktori, 0755, TRUE)) {
			return FALSE;
		}

		$this->load->library('upload');
		$this->upload->initialize([
			'upload_path'   => $direktori,
			'allowed_types' => 'jpg|jpeg|png|webp',
			'max_size'      => 2000,
			'file_name'     => 'kb-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)),
			'overwrite'     => FALSE,
		]);

		if (!$this->upload->do_upload('foto_profil')) {
			return FALSE;
		}

		$upload = $this->upload->data();
		return $upload['file_name'];
	}
}
