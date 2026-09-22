<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Mapel Ketetapan Smp Controller
*| --------------------------------------------------------------------------
*| Manage ketetapan rules for mapel (hari/jam restrictions)
*|
*/
class Mapel_ketetapan_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_mapel_ketetapan_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* Show all ketetapan
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('mapel_ketetapan_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ketetapans'] = $this->model_mapel_ketetapan_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['ketetapan_counts'] = $this->model_mapel_ketetapan_smp->count_all($filter, $field);
		$this->data['offset'] = $offset;

		$config = array(
			'base_url'     => 'administrator/mapel_ketetapan_smp/index/',
			'total_rows'   => $this->model_mapel_ketetapan_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		// Get all mapel for dropdown
		$this->data['mapel_list'] = $this->db->get('mata_pelajaran_smp')->result();

		$this->template->title('Ketetapan Mapel SMP');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/ketetapan_list', $this->data);
	}

	/**
	* Add new ketetapan
	*/
	public function add()
	{
		$this->is_allowed('mapel_ketetapan_smp_add');

		$this->data['mapel_list'] = $this->db->get('mata_pelajaran_smp')->result();
		$this->data['hari_list'] = $this->db->get('pelajaran_hari')->result();
		$this->data['jam_list'] = $this->db->query("
			SELECT DISTINCT jam_ke FROM pelajaran_jam WHERE keterangan LIKE 'MENGAJAR%' AND jam_ke != '-' ORDER BY CAST(jam_ke AS UNSIGNED)
		")->result();

		$this->template->title('Tambah Ketetapan');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/ketetapan_add', $this->data);
	}

	/**
	* Save new ketetapan
	*/
	public function add_save()
	{
		if (!$this->is_allowed('mapel_ketetapan_smp_add', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$this->form_validation->set_rules('kode_mapel', 'Kode Mapel', 'trim|required');
		$this->form_validation->set_rules('tipe_ketetapan', 'Tipe Ketetapan', 'trim|required');
		$this->form_validation->set_rules('nilai', 'Nilai', 'trim|required');

		if ($this->form_validation->run()) {
			$kode_mapel = $this->input->post('kode_mapel');
			$tipe = $this->input->post('tipe_ketetapan');
			$nilai_array = $this->input->post('nilai');
			$keterangan = $this->input->post('keterangan');
			
			// Build comma separated values
			$nilai = implode(',', $nilai_array);
			
			// Check if already exists
			$existing = $this->db->get_where('mapel_ketetapan_smp', array(
				'kode_mapel' => $kode_mapel,
				'tipe_ketetapan' => $tipe
			))->row();
			
			if ($existing) {
				// Update
				$this->db->where('id_ketetapan', $existing->id_ketetapan);
				$this->db->update('mapel_ketetapan_smp', array(
					'nilai' => $nilai,
					'keterangan' => $keterangan
				));
				$save_id = $existing->id_ketetapan;
			} else {
				// Insert
				$this->db->insert('mapel_ketetapan_smp', array(
					'kode_mapel' => $kode_mapel,
					'tipe_ketetapan' => $tipe,
					'nilai' => $nilai,
					'keterangan' => $keterangan
				));
				$save_id = $this->db->insert_id();
			}

			if ($save_id) {
				$this->data['success'] = true;
				$this->data['message'] = 'Ketetapan berhasil disimpan';
				$this->data['redirect'] = base_url('administrator/mapel_ketetapan_smp');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = 'Gagal menyimpan ketetapan';
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Validasi gagal';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	* Edit ketetapan
	*/
	public function edit($id)
	{
		$this->is_allowed('mapel_ketetapan_smp_update');

		$this->data['ketetapan'] = $this->model_mapel_ketetapan_smp->find($id);
		$this->data['mapel_list'] = $this->db->get('mata_pelajaran_smp')->result();
		$this->data['hari_list'] = $this->db->get('pelajaran_hari')->result();
		$this->data['jam_list'] = $this->db->query("
			SELECT DISTINCT jam_ke FROM pelajaran_jam WHERE keterangan LIKE 'MENGAJAR%' AND jam_ke != '-' ORDER BY CAST(jam_ke AS UNSIGNED)
		")->result();

		$this->template->title('Edit Ketetapan');
		$this->render('backend/standart/administrator/mapel_alokasi_smp/ketetapan_edit', $this->data);
	}

	/**
	* Save edit
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('mapel_ketetapan_smp_update', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$this->form_validation->set_rules('kode_mapel', 'Kode Mapel', 'trim|required');
		$this->form_validation->set_rules('tipe_ketetapan', 'Tipe Ketetapan', 'trim|required');
		$this->form_validation->set_rules('nilai', 'Nilai', 'trim|required');

		if ($this->form_validation->run()) {
			$kode_mapel = $this->input->post('kode_mapel');
			$tipe = $this->input->post('tipe_ketetapan');
			$nilai_array = $this->input->post('nilai');
			$keterangan = $this->input->post('keterangan');
			
			$nilai = implode(',', $nilai_array);
			
			$this->db->where('id_ketetapan', $id);
			$this->db->update('mapel_ketetapan_smp', array(
				'kode_mapel' => $kode_mapel,
				'tipe_ketetapan' => $tipe,
				'nilai' => $nilai,
				'keterangan' => $keterangan
			));

			$this->data['success'] = true;
			$this->data['message'] = 'Ketetapan berhasil diupdate';
			$this->data['redirect'] = base_url('administrator/mapel_ketetapan_smp');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Validasi gagal';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	* Delete ketetapan
	*/
	public function delete($id = null)
	{
		$this->is_allowed('mapel_ketetapan_smp_delete');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_mapel_ketetapan_smp->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->model_mapel_ketetapan_smp->remove($id);
			}
		}

		if ($remove) {
            set_message('Ketetapan berhasil dihapus', 'success');
        } else {
            set_message('Gagal menghapus ketetapan', 'error');
        }

		redirect_back();
	}
}

/* End of file Mapel_ketetapan_smp.php */
/* Location: ./modules/mapel_alokasi_smp/controllers/backend/Mapel_ketetapan_smp.php */
