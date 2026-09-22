<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Acara Presensi Realtime Tracker Controller
*| --------------------------------------------------------------------------
*| Monitoring realtime evaluasi acara - dashboard status pengisian
*|
*/
class Acara_presensi_realtime_tracker extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('acara_presensi_realtime_tracker/model_realtime_tracker');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * Show realtime tracker page for an acara
	 *
	 * @param int $id_acara
	 */
	public function index($id_acara = 0)
	{
		$this->is_allowed('acara_list');

		$id_acara = (int) $id_acara;

		if ($id_acara <= 0) {
			setflashdata('error', 'ID Acara tidak valid.');
			redirect('administrator/acara');
		}

		$acara = $this->model_realtime_tracker->get_acara_detail($id_acara);
		if (!$acara) {
			setflashdata('error', 'Acara tidak ditemukan.');
			redirect('administrator/acara');
		}

		$this->data['acara'] = $acara;
		$this->data['id_acara'] = $id_acara;

		$this->template->title('Monitoring Evaluasi: ' . $acara->nama_acara);
		$this->render('backend/standart/administrator/acara_presensi_realtime_tracker/acara_presensi_realtime_tracker_list', $this->data);
	}

	/**
	 * AJAX endpoint - get realtime status
	 *
	 * @param int $id_acara
	 * @return JSON
	 */
	public function ajax_status($id_acara = 0)
	{
		$id_acara = (int) $id_acara;

		if ($id_acara <= 0) {
			echo json_encode(array(
				'status' => 0,
				'message' => 'ID Acara tidak valid'
			));
			return;
		}

		// Cek acara exists
		$acara = $this->model_realtime_tracker->get_acara_detail($id_acara);
		if (!$acara) {
			echo json_encode(array(
				'status' => 0,
				'message' => 'Acara tidak ditemukan'
			));
			return;
		}

		$data = $this->model_realtime_tracker->get_status_realtime($id_acara);

		echo json_encode(array(
			'status' => 1,
			'data' => $data
		));
	}

	/**
	 * AJAX endpoint - get activity feed
	 *
	 * @param int $id_acara
	 * @return JSON
	 */
	public function ajax_activity($id_acara = 0)
	{
		$id_acara = (int) $id_acara;
		$limit = (int) $this->input->get('limit');
		if ($limit <= 0) $limit = 10;

		if ($id_acara <= 0) {
			echo json_encode(array(
				'status' => 0,
				'message' => 'ID Acara tidak valid'
			));
			return;
		}

		// Cek acara exists
		$acara = $this->model_realtime_tracker->get_acara_detail($id_acara);
		if (!$acara) {
			echo json_encode(array(
				'status' => 0,
				'message' => 'Acara tidak ditemukan'
			));
			return;
		}

		$feed = $this->model_realtime_tracker->get_activity_feed($id_acara, $limit);

		echo json_encode(array(
			'status' => 1,
			'data' => $feed
		));
	}

	/**
	 * AJAX endpoint - get role breakdown for chart
	 *
	 * @param int $id_acara
	 * @return JSON
	 */
	public function ajax_breakdown($id_acara = 0)
	{
		$id_acara = (int) $id_acara;

		if ($id_acara <= 0) {
			echo json_encode(array(
				'status' => 0,
				'message' => 'ID Acara tidak valid'
			));
			return;
		}

		$acara = $this->model_realtime_tracker->get_acara_detail($id_acara);
		if (!$acara) {
			echo json_encode(array(
				'status' => 0,
				'message' => 'Acara tidak ditemukan'
			));
			return;
		}

		$breakdown = $this->model_realtime_tracker->get_role_breakdown($id_acara);

		echo json_encode(array(
			'status' => 1,
			'data' => $breakdown
		));
	}

}

/* End of file Acara_presensi_realtime_tracker.php */
/* Location: ./modules/acara_presensi_realtime_tracker/controllers/backend/Acara_presensi_realtime_tracker.php */
