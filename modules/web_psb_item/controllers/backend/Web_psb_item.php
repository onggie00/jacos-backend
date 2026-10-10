<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Web Psb Item Controller
*| --------------------------------------------------------------------------
*| Kuesioner PSB — jawaban (item) per pertanyaan. Selalu diakses per pertanyaan:
*| ?id_pertanyaan=N (dari halaman detail pertanyaan di modul web_psb_pertanyaan).
*|
*/
class Web_psb_item extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_psb_item');
		// model lintas modul (HMVC): pakai prefix nama modul
		$this->load->model('web_psb_pertanyaan/model_web_psb_pertanyaan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/** ambil pertanyaan induk dari param; 404 bila tidak ada */
	private function _pertanyaan_induk()
	{
		$id_pertanyaan = (int) $this->input->get('id_pertanyaan');
		$pertanyaan = $this->model_web_psb_pertanyaan->find($id_pertanyaan);
		if (empty($pertanyaan)) {
			show_404();
		}
		return $pertanyaan;
	}

	/**
	* show all Web Psb Items (per pertanyaan)
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_psb_item_list');

		$pertanyaan = $this->_pertanyaan_induk();
		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['web_psb_items'] = $this->model_web_psb_item->get($filter, $field, $this->limit_page, $offset, $pertanyaan->id_step_pertanyaan);
		$this->data['web_psb_item_counts'] = $this->model_web_psb_item->count_all($filter, $field, $pertanyaan->id_step_pertanyaan);
		$this->data['pertanyaan'] = $pertanyaan;

		$config = [
			'base_url'     => 'administrator/web_psb_item/index/?id_pertanyaan=' . $pertanyaan->id_step_pertanyaan,
			'total_rows'   => $this->data['web_psb_item_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 0,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jawaban Kuesioner PSB List');
		$this->render('backend/standart/administrator/web_psb_item/web_psb_item_list', $this->data);
	}

	/**
	* Add new web_psb_items
	*/
	public function add()
	{
		$this->is_allowed('web_psb_item_add');

		$this->data['pertanyaan'] = $this->_pertanyaan_induk();

		$this->template->title('Jawaban Kuesioner PSB New');
		$this->render('backend/standart/administrator/web_psb_item/web_psb_item_add', $this->data);
	}

	/**
	* Add New Web Psb Items
	*/
	public function add_save()
	{
		if (!$this->is_allowed('web_psb_item_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_pertanyaan', 'Pertanyaan', 'trim|required|numeric');
		$this->form_validation->set_rules('jawaban', 'Jawaban', 'trim|required|max_length[255]');

		if ($this->form_validation->run()) {

			$pertanyaan = $this->model_web_psb_pertanyaan->find($this->input->post('id_pertanyaan'));
			if (empty($pertanyaan)) {
				$this->data['success'] = false;
				$this->data['message'] = 'Pertanyaan tidak ditemukan';
				echo json_encode($this->data);
				return;
			}

			$save_data = [
				'id_web_psb_pertanyaan' => (int) $pertanyaan->id_step_pertanyaan,
				'jawaban' => $this->input->post('jawaban'),
				'is_lainnya' => $this->input->post('is_lainnya') ? 1 : 0,
			];

			$id = $this->model_web_psb_item->store($save_data);

			if ($id) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id']      = $id;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/web_psb_item/add?id_pertanyaan=' . $pertanyaan->id_step_pertanyaan, 'Tambah lagi')
					]);
				} else {
					set_message(cclang('success_save_data_redirect', []), 'success');

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_psb_item/?id_pertanyaan=' . $pertanyaan->id_step_pertanyaan);
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	* Update view Web Psb Items
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_psb_item_update');

		$this->data['web_psb_item'] = $this->model_web_psb_item->find($id);
		if (empty($this->data['web_psb_item'])) {
			show_404();
		}
		$this->data['pertanyaan'] = $this->model_web_psb_pertanyaan->find($this->data['web_psb_item']->id_web_psb_pertanyaan);

		$this->template->title('Jawaban Kuesioner PSB Update');
		$this->render('backend/standart/administrator/web_psb_item/web_psb_item_update', $this->data);
	}

	/**
	* Update Web Psb Items
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_psb_item_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jawaban', 'Jawaban', 'trim|required|max_length[255]');

		if ($this->form_validation->run()) {

			$item = $this->model_web_psb_item->find($id);
			if (empty($item)) {
				$this->data['success'] = false;
				$this->data['message'] = 'Jawaban tidak ditemukan';
				echo json_encode($this->data);
				return;
			}

			$save_data = [
				'jawaban' => $this->input->post('jawaban'),
				'is_lainnya' => $this->input->post('is_lainnya') ? 1 : 0,
			];

			$save_web_psb_item = $this->model_web_psb_item->change($id, $save_data);

			if ($save_web_psb_item) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id']      = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_psb_item/?id_pertanyaan=' . $item->id_web_psb_pertanyaan, ' Go back to list')
					]);
				} else {
					set_message(cclang('success_update_data_redirect', []), 'success');

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_psb_item/?id_pertanyaan=' . $item->id_web_psb_pertanyaan);
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_psb_item/?id_pertanyaan=' . $item->id_web_psb_pertanyaan);
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
	* delete Web Psb Items
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_psb_item_delete');

		$arr_id = $this->input->get('id');
		$id_pertanyaan = null;
		$remove = false;

		if (!empty($id)) {
			$item = $this->model_web_psb_item->find($id);
			$id_pertanyaan = !empty($item) ? $item->id_web_psb_pertanyaan : null;
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$item = $this->model_web_psb_item->find($id);
				$id_pertanyaan = !empty($item) ? $item->id_web_psb_pertanyaan : null;
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
			set_message(cclang('has_been_deleted', 'jawaban kuesioner'), 'success');
		} else {
			set_message(cclang('error_delete', 'jawaban kuesioner'), 'error');
		}

		if ($id_pertanyaan !== null) {
			redirect('administrator/web_psb_item/?id_pertanyaan=' . $id_pertanyaan, 'refresh');
		}
		redirect_back();
	}

	/**
	* delete Web Psb Items
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		return $this->model_web_psb_item->remove($id);
	}
}

/* End of file Web_psb_item.php */
/* Location: ./modules/web_psb_item/controllers/backend/Web_psb_item.php */
