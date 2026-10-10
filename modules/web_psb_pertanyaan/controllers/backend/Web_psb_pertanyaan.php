<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Web Psb Pertanyaan Controller
*| --------------------------------------------------------------------------
*| Kuesioner PSB — master pertanyaan kuesioner pendaftaran (step 5).
*| Sumber data web PSB: tabel web_psb_step_pertanyaan (dibaca apiweb/Get_content_per_step.php)
*|
*/
class Web_psb_pertanyaan extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_psb_pertanyaan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/** daftar tipe input yang dipahami web PSB */
	private function _tipe_input()
	{
		return array('radio' => 'Pilihan (radio — satu jawaban)', 'check' => 'Kotak centang (check — boleh lebih dari satu)');
	}

	/** daftar jenjang */
	private function _jenjang()
	{
		return array('kb' => 'KB', 'tk' => 'TK', 'sd' => 'SD', 'smp' => 'SMP', 'sma' => 'SMA', 'ft' => 'France Track');
	}

	/**
	* show all Web Psb Pertanyaans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_psb_pertanyaan_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['web_psb_pertanyaans'] = $this->model_web_psb_pertanyaan->get($filter, $field, $this->limit_page, $offset);
		$this->data['web_psb_pertanyaan_counts'] = $this->model_web_psb_pertanyaan->count_all($filter, $field);
		$this->data['total_pertanyaan'] = $this->model_web_psb_pertanyaan->count_all('', '');

		$config = [
			'base_url'     => 'administrator/web_psb_pertanyaan/index/',
			'total_rows'   => $this->data['web_psb_pertanyaan_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);
		$this->data['tipe_input_list'] = $this->_tipe_input();
		$this->data['jenjang_list'] = $this->_jenjang();

		$this->template->title('Kuesioner PSB List');
		$this->render('backend/standart/administrator/web_psb_pertanyaan/web_psb_pertanyaan_list', $this->data);
	}

	/**
	* Add new web_psb_pertanyaans
	*/
	public function add()
	{
		$this->is_allowed('web_psb_pertanyaan_add');

		$this->data['tipe_input_list'] = $this->_tipe_input();
		$this->data['jenjang_list'] = $this->_jenjang();

		$this->template->title('Kuesioner PSB New');
		$this->render('backend/standart/administrator/web_psb_pertanyaan/web_psb_pertanyaan_add', $this->data);
	}

	/**
	* Add New Web Psb Pertanyaans
	*/
	public function add_save()
	{
		if (!$this->is_allowed('web_psb_pertanyaan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('pertanyaan', 'Pertanyaan', 'trim|required');
		$this->form_validation->set_rules('step', 'Step', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|numeric|max_length[11]');
		$this->form_validation->set_rules('tipe_input', 'Tipe Input', 'trim|required|in_list[radio,check]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|in_list[kb,tk,sd,smp,sma,ft]');

		if ($this->form_validation->run()) {

			$save_data = [
				'step' => $this->input->post('step'),
				'pertanyaan' => $this->input->post('pertanyaan'),
				'no_urut' => (int) ($this->input->post('no_urut') !== '' ? $this->input->post('no_urut') : 0),
				'tipe_input' => $this->input->post('tipe_input'),
				'min_input' => $this->input->post('min_input'),
				'max_input' => $this->input->post('max_input'),
				'jenjang' => $this->input->post('jenjang'),
				'is_ppsbb' => $this->input->post('is_ppsbb') ? 1 : 0,
			];

			$id = $this->model_web_psb_pertanyaan->store($save_data);

			if ($id) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id']      = $id;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/web_psb_pertanyaan/edit/' . $id, 'Go to edit')
					]);
				} else {
					set_message(cclang('success_save_data_redirect', []), 'success');

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_psb_pertanyaan');
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
	* Update view Web Psb Pertanyaans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_psb_pertanyaan_update');

		$this->data['web_psb_pertanyaan'] = $this->model_web_psb_pertanyaan->find($id);
		$this->data['tipe_input_list'] = $this->_tipe_input();
		$this->data['jenjang_list'] = $this->_jenjang();

		$this->template->title('Kuesioner PSB Update');
		$this->render('backend/standart/administrator/web_psb_pertanyaan/web_psb_pertanyaan_update', $this->data);
	}

	/**
	* Update Web Psb Pertanyaans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_psb_pertanyaan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('pertanyaan', 'Pertanyaan', 'trim|required');
		$this->form_validation->set_rules('step', 'Step', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|numeric|max_length[11]');
		$this->form_validation->set_rules('tipe_input', 'Tipe Input', 'trim|required|in_list[radio,check]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|in_list[kb,tk,sd,smp,sma,ft]');

		if ($this->form_validation->run()) {

			$save_data = [
				'step' => $this->input->post('step'),
				'pertanyaan' => $this->input->post('pertanyaan'),
				'no_urut' => (int) ($this->input->post('no_urut') !== '' ? $this->input->post('no_urut') : 0),
				'tipe_input' => $this->input->post('tipe_input'),
				'min_input' => $this->input->post('min_input'),
				'max_input' => $this->input->post('max_input'),
				'jenjang' => $this->input->post('jenjang'),
				'is_ppsbb' => $this->input->post('is_ppsbb') ? 1 : 0,
			];

			$save_web_psb_pertanyaan = $this->model_web_psb_pertanyaan->change($id, $save_data);

			if ($save_web_psb_pertanyaan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id']      = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_psb_pertanyaan', ' Go back to list')
					]);
				} else {
					set_message(cclang('success_update_data_redirect', []), 'success');

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_psb_pertanyaan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_psb_pertanyaan');
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
	* delete Web Psb Pertanyaans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_psb_pertanyaan_delete');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			if ((int) $this->model_web_psb_pertanyaan->count_items($id) > 0) {
				// keputusan: pertanyaan yang masih punya jawaban ditolak hapus
				set_message('Pertanyaan masih memiliki jawaban — hapus jawabannya terlebih dahulu.', 'error');
				redirect_back();
			}
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				if ((int) $this->model_web_psb_pertanyaan->count_items($id) > 0) {
					set_message('Beberapa pertanyaan masih memiliki jawaban — hapus jawabannya terlebih dahulu.', 'error');
					redirect_back();
				}
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
			set_message(cclang('has_been_deleted', 'pertanyaan kuesioner'), 'success');
		} else {
			set_message(cclang('error_delete', 'pertanyaan kuesioner'), 'error');
		}

		redirect_back();
	}

	/**
	* View view Web Psb Pertanyaans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('web_psb_pertanyaan_view');

		$this->data['web_psb_pertanyaan'] = $this->model_web_psb_pertanyaan->find($id);
		$this->data['items'] = $this->model_web_psb_pertanyaan->db
			->where('id_web_psb_pertanyaan', (int) $id)
			->order_by('id_web_psb_item', 'ASC')
			->get('web_psb_step_item')->result();
		$this->data['tipe_input_list'] = $this->_tipe_input();
		$this->data['jenjang_list'] = $this->_jenjang();

		$this->template->title('Kuesioner PSB Detail');
		$this->render('backend/standart/administrator/web_psb_pertanyaan/web_psb_pertanyaan_view', $this->data);
	}

	/**
	* delete Web Psb Pertanyaans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		return $this->model_web_psb_pertanyaan->remove($id);
	}
}

/* End of file Web_psb_pertanyaan.php */
/* Location: ./modules/web_psb_pertanyaan/controllers/backend/Web_psb_pertanyaan.php */
