<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pengaturan Diskon Spp Controller
*| --------------------------------------------------------------------------
*| Pengaturan diskon SPP bayar 1 tahun ajaran (per jenjang + tingkatan)
*|
*/
class Pengaturan_diskon_spp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pengaturan_diskon_spp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pengaturan Diskon Spps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pengaturan_diskon_spp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pengaturan_diskon_spps'] = $this->model_pengaturan_diskon_spp->get($filter, $field, $this->limit_page, $offset);
		$this->data['pengaturan_diskon_spp_counts'] = $this->model_pengaturan_diskon_spp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pengaturan_diskon_spp/index/',
			'total_rows'   => $this->model_pengaturan_diskon_spp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengaturan Diskon Spp List');
		$this->render('backend/standart/administrator/pengaturan_diskon_spp/pengaturan_diskon_spp_list', $this->data);
	}
	
	/**
	* Add new pengaturan_diskon_spps
	*
	*/
	public function add()
	{
		$this->is_allowed('pengaturan_diskon_spp_add');

		$this->data['jenjangs'] = $this->model_pengaturan_diskon_spp->jenjang_labels();
		$this->data['tingkatans'] = array();

		$this->template->title('Pengaturan Diskon Spp New');
		$this->render('backend/standart/administrator/pengaturan_diskon_spp/pengaturan_diskon_spp_add', $this->data);
	}

	/**
	* Add New Pengaturan Diskon Spps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pengaturan_diskon_spp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('min_pembayaran', 'Minimal Bulan', 'trim|required|numeric|greater_than[0]');
		$this->form_validation->set_rules('persen_diskon', 'Persen Diskon', 'trim|numeric|less_than[100]');

		if ($this->form_validation->run()) {
		
			$persen = $this->input->post('persen_diskon');
			$save_data = [
				'jenjang' => strtolower($this->input->post('jenjang')),
				'id_tingkatan' => ($this->input->post('id_tingkatan') !== '' && $this->input->post('id_tingkatan') !== null) ? (int) $this->input->post('id_tingkatan') : null,
				'min_pembayaran' => (int) $this->input->post('min_pembayaran'),
				'persen_diskon' => ($persen !== '' && $persen !== null) ? (float) $persen : 0,
				'aktif' => ($this->input->post('aktif') == '1') ? '1' : '0',
			];

			//validasi: agar tidak duplikat ALAMAT diskon yang sama (jenjang + id_tingkatan)
			if ($this->model_pengaturan_diskon_spp->count_by_alamat($save_data['jenjang'], $save_data['id_tingkatan'])) {
				$this->data['success'] = false;
				$this->data['message'] = 'Pengaturan untuk jenjang + tingkatan ini sudah ada';
				echo json_encode($this->data);
				exit;
			}

			$save_pengaturan_diskon_spp = $this->model_pengaturan_diskon_spp->store($save_data);
            

			if ($save_pengaturan_diskon_spp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pengaturan_diskon_spp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pengaturan_diskon_spp/edit/' . $save_pengaturan_diskon_spp, 'Edit Pengaturan Diskon Spp'),
						anchor('administrator/pengaturan_diskon_spp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', []),
						'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_diskon_spp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_diskon_spp');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	/**
	* Update view Pengaturan Diskon Spps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pengaturan_diskon_spp_update');

		$this->is_allowed('pengaturan_diskon_spp_update');

		$this->data['pengaturan_diskon_spp'] = $this->model_pengaturan_diskon_spp->find($id);
		$row = $this->data['pengaturan_diskon_spp'];
		$this->data['jenjangs'] = $this->model_pengaturan_diskon_spp->jenjang_labels();
		$this->data['tingkatans'] = !empty($row->jenjang) ? $this->model_pengaturan_diskon_spp->tingkatan_all($row->jenjang) : array();

		$this->template->title('Pengaturan Diskon Spp Update');
		$this->render('backend/standart/administrator/pengaturan_diskon_spp/pengaturan_diskon_spp_update', $this->data);
	}

	/**
	* Update Pengaturan Diskon Spps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pengaturan_diskon_spp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('min_pembayaran', 'Minimal Bulan', 'trim|required|numeric|greater_than[0]');
		$this->form_validation->set_rules('persen_diskon', 'Persen Diskon', 'trim|numeric|less_than[100]');
		
		if ($this->form_validation->run()) {
		
			$persen = $this->input->post('persen_diskon');
			$save_data = [
				'jenjang' => strtolower($this->input->post('jenjang')),
				'id_tingkatan' => ($this->input->post('id_tingkatan') !== '' && $this->input->post('id_tingkatan') !== null) ? (int) $this->input->post('id_tingkatan') : null,
				'min_pembayaran' => (int) $this->input->post('min_pembayaran'),
				'persen_diskon' => ($persen !== '' && $persen !== null) ? (float) $persen : 0,
				'aktif' => ($this->input->post('aktif') == '1') ? '1' : '0',
			];

			//validasi: agar tidak duplikat ALAMAT diskon yang sama (jenjang + id_tingkatan), kecuali dirinya
			if ($this->model_pengaturan_diskon_spp->count_by_alamat($save_data['jenjang'], $save_data['id_tingkatan'], $id)) {
				$this->data['success'] = false;
				$this->data['message'] = 'Pengaturan untuk jenjang + tingkatan ini sudah ada';
				echo json_encode($this->data);
				exit;
			}

			$save_pengaturan_diskon_spp = $this->model_pengaturan_diskon_spp->change($id, $save_data);

			if ($save_pengaturan_diskon_spp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pengaturan_diskon_spp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pengaturan_diskon_spp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pengaturan_diskon_spp');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	* Opsi tingkatan (dropdown form) berdasarkan jenjang
	*
	* @return JSON
	*/
	public function tingkatan_options()
	{
		$jenjang = strtolower($this->input->post('jenjang'));
		$this->data['success'] = true;
		$this->data['options'] = $this->model_pengaturan_diskon_spp->tingkatan_all($jenjang);
		echo json_encode($this->data);
	}

	/**
	* delete Pengaturan Diskon Spps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pengaturan_diskon_spp_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'pengaturan_diskon_spp'), 'success');
        } else {
            set_message(cclang('error_delete', 'pengaturan_diskon_spp'), 'error');
        }

		redirect_back();
	}

	/**
	* Delete all Pengaturan Diskon Spps
	*
	* @var $id String
	*/
	public function delete_all()
	{
		$this->is_allowed('pengaturan_diskon_spp_delete');

		$this->load->helper('file');

		$ids = $this->input->post('ids');

		if (!empty($ids)) {
			foreach ($ids as $id) {
				$remove = $this->_remove($id);
			}
		}

		echo json_encode(['success' => $remove, 'message' => 'Berhasil hapus data terpilih']);
	}

		/**
	* View view Pengaturan Diskon Spps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pengaturan_diskon_spp_view');

		$this->data['pengaturan_diskon_spp'] = $this->model_pengaturan_diskon_spp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pengaturan Diskon Spp Detail');
		$this->render('backend/standart/administrator/pengaturan_diskon_spp/pengaturan_diskon_spp_view', $this->data);
	}
	
	/**
	* delete Pengaturan Diskon Spps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pengaturan_diskon_spp = $this->model_pengaturan_diskon_spp->find($id);

		
		
		return $this->model_pengaturan_diskon_spp->remove($id);
	}
	
	
}


/* End of file Pengaturan_diskon_spp.php */
/* Location: ./modules/pengaturan_diskon_spp/controllers/backend/Pengaturan_diskon_spp.php */
