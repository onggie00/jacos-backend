<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mhcu_periode extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_mhcu_periode');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('mhcu_periode_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort = $this->input->get('s');
		$sort_type = $this->input->get('d');

		$multi_filters = $this->_parse_filters();
		$has_multi_filter = !empty($multi_filters);

		if ($has_multi_filter) {
			$this->data['mhcu_periodes'] = $this->model_mhcu_periode->get(null, null, $this->limit_page, $offset, array(), $sort, $sort_type, $multi_filters);
			$this->data['mhcu_periode_counts'] = $this->model_mhcu_periode->count_all(null, null, $multi_filters);
		} else {
			$this->data['mhcu_periodes'] = $this->model_mhcu_periode->get($filter, $field, $this->limit_page, $offset, array(), $sort, $sort_type);
			$this->data['mhcu_periode_counts'] = $this->model_mhcu_periode->count_all($filter, $field);
		}

		$config = array(
			'base_url'     => 'administrator/mhcu_periode/index/',
			'total_rows'   => $has_multi_filter ? $this->model_mhcu_periode->count_all(null, null, $multi_filters) : $this->model_mhcu_periode->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);
		$this->data['multi_filters'] = $multi_filters;

		$this->template->title('Periode MHCU');
		$this->render('backend/standart/administrator/mhcu_periode/mhcu_periode_list', $this->data);
	}

	private function _parse_filters()
	{
		$fields    = $this->input->get('ff');
		$operators = $this->input->get('fo');
		$values    = $this->input->get('fv');

		if (!is_array($fields) || !is_array($operators) || !is_array($values)) {
			return array();
		}

		$filters = array();
		for ($i = 0; $i < count($fields); $i++) {
			if (!empty($fields[$i]) && !empty($values[$i])) {
				$filters[] = array(
					'field'    => $fields[$i],
					'operator' => isset($operators[$i]) ? $operators[$i] : 'contains',
					'value'    => $values[$i],
				);
			}
		}
		return $filters;
	}

	public function add_save()
	{
		if (!$this->is_allowed('mhcu_periode_add', false)) {
			echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
			exit;
		}

		$this->form_validation->set_rules('nama_periode', 'Nama Periode', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Status Aktif', 'trim|required');

		if ($this->form_validation->run()) {
			$save_data = array(
				'nama_periode' => $this->input->post('nama_periode'),
				'tanggal_mulai' => $this->input->post('tanggal_mulai'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
				'is_active' => $this->input->post('is_active'),
				'tanggal_publish' => ($this->input->post('tanggal_publish') !== '') ? $this->input->post('tanggal_publish') : null,
			);
		
			$save = $this->model_mhcu_periode->store($save_data);

			if ($save) {
				echo json_encode(array('success' => true, 'message' => 'Periode berhasil ditambahkan'));
			} else {
				echo json_encode(array('success' => false, 'message' => cclang('data_not_change')));
			}
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}
	
	public function edit($id)
	{
		$this->is_allowed('mhcu_periode_update');

		$this->data['mhcu_periode'] = $this->model_mhcu_periode->find($id);

		$this->template->title('Edit Periode MHCU');
		$this->render('backend/standart/administrator/mhcu_periode/mhcu_periode_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('mhcu_periode_update', false)) {
			echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
			exit;
		}
		
		$this->form_validation->set_rules('nama_periode', 'Nama Periode', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Status Aktif', 'trim|required');
		
		if ($this->form_validation->run()) {
			$save_data = array(
				'nama_periode' => $this->input->post('nama_periode'),
				'tanggal_mulai' => $this->input->post('tanggal_mulai'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
				'is_active' => $this->input->post('is_active'),
				'target_peserta' => ($this->input->post('target_peserta') !== '' && $this->input->post('target_peserta') !== null) ? (int) $this->input->post('target_peserta') : null,
				'tanggal_publish' => ($this->input->post('tanggal_publish') !== '') ? $this->input->post('tanggal_publish') : null,
			);

			$save = $this->model_mhcu_periode->change($id, $save_data);

			if ($save) {
				set_message(cclang('success_update_data_redirect', array()), 'success');
				echo json_encode(array('success' => true, 'redirect' => base_url('administrator/mhcu_periode')));
			} else {
				echo json_encode(array('success' => false, 'message' => cclang('data_not_change')));
			}
		} else {
			echo json_encode(array('success' => false, 'message' => 'Validasi gagal', 'errors' => $this->form_validation->error_array()));
		}
	}

	public function get_detail($id)
	{
		if (!$this->is_allowed('mhcu_periode_view', false)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$periode = $this->model_mhcu_periode->find($id);

		if ($periode) {
			$periode->tanggal_mulai_formatted = formatTanggal($periode->tanggal_mulai);
			$periode->tanggal_selesai_formatted = formatTanggal($periode->tanggal_selesai);
			$periode->tanggal_publish_formatted = !empty($periode->tanggal_publish) ? formatTanggal($periode->tanggal_publish) : null;
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($periode));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array('error' => 'Data tidak ditemukan')));
		}
	}
	
	public function delete($id = null)
	{
		$this->is_allowed('mhcu_periode_delete');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_mhcu_periode->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->model_mhcu_periode->remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'Periode MHCU'), 'success');
        } else {
            set_message(cclang('error_delete', 'Periode MHCU'), 'error');
        }

		redirect_back();
	}

	/**
	 * Export semua sesi selesai pada periode tertentu, multi-sheet (1 sheet per user).
	 */
	public function export($id)
	{
		if (!$this->is_allowed('mhcu_periode_view', false)) {
			$this->session->set_flashdata('error', cclang('sorry_you_do_not_have_permission_to_access'));
			redirect_back();
			return;
		}

		$periode = $this->model_mhcu_periode->find($id);
		if (!$periode) {
			$this->session->set_flashdata('error', 'Periode tidak ditemukan');
			redirect_back();
			return;
		}

		// Ambil semua sesi selesai pada periode ini
		$sesis = $this->db
			->select('mhcu_sesi.*, pk.label_profil AS kategori_keseluruhan, pk.warna AS kategori_warna, mhcu_hasil_individu.is_krisis, pk.saran_kategori AS saran_rekomendasi')
			->join('mhcu_hasil_individu', 'mhcu_hasil_individu.id_sesi = mhcu_sesi.id_mhcu_sesi', 'LEFT')
			->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT')
			->where('mhcu_sesi.id_mhcu_periode', $id)
			->where('mhcu_sesi.deleted_at IS NULL', null, false)
			->where('mhcu_sesi.status', 'selesai')
			->order_by('mhcu_sesi.nama_lengkap', 'ASC')
			->get('mhcu_sesi')->result();

		// Attach items untuk tiap sesi
		foreach ($sesis as $sesi) {
			$sesi->nama_periode = $periode->nama_periode;
			$sesi->items = $this->_get_sesi_items($sesi->id_mhcu_sesi);
		}

		$filename = $periode->nama_periode;

		$this->load->library('mhcu_excel');
		$this->mhcu_excel->generate_sesi_export($sesis, $periode, $filename);
	}

	/**
	 * Helper: ambil list item jawaban untuk 1 sesi.
	 */
	private function _get_sesi_items($id_sesi)
	{
		$rows = $this->db
			->select('mi.kode_instrument, mi.nama_instrument, mii.text_item, mii.dimensi_aspek, mii.no_urut_item, mii.is_reversed, msi.id_skala_option, mso.label_option, msi.text_value')
			->from('mhcu_sesi_instrument msi')
			->join('mhcu_instrument_item mii', 'mii.id_instrument_item = msi.id_instrument_item', 'LEFT')
			->join('mhcu_instrument mi', 'mi.id_instrument = msi.id_instrument', 'LEFT')
			->join('mhcu_skala_option mso', 'mso.id_skala_option = msi.id_skala_option', 'LEFT')
			->where('msi.id_sesi', $id_sesi)
			->order_by('mi.no_urut', 'ASC')
			->order_by('mii.no_urut_item', 'ASC')
			->get()
			->result();

		foreach ($rows as $r) {
			if (!empty($r->label_option)) {
				$r->jawaban_text = $r->label_option;
			} elseif (!empty($r->text_value)) {
				$r->jawaban_text = $r->text_value;
			} else {
				$r->jawaban_text = '-';
			}
		}

		return $rows;
	}

}

/* End of file Mhcu_periode.php */
