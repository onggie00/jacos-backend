<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Acara Presensi Realtime Tracker - Public API
*| --------------------------------------------------------------------------
*| Endpoint publik untuk monitoring evaluasi acara.
*| Tidak memerlukan login (anonim user bisa akses).
*|
*/
class Public_api extends MX_Controller
{

	public $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('acara_presensi_realtime_tracker/model_realtime_tracker');
	}

	/**
	 * AJAX endpoint - get realtime status
	 *
	 * @param int $id_acara
	 * @return JSON
	 */
	public function status($id_acara = 0)
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
	public function activity($id_acara = 0)
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
	public function breakdown($id_acara = 0)
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

	/**
	 * Public tracker page
	 *
	 * @param int $id_acara
	 */
	public function test()
	{
		echo json_encode(array('test' => 'ok', 'uri' => $this->uri->uri_string()));
	}

	public function view($id_acara = 0)
	{
		$id_acara = (int) $id_acara;

		if ($id_acara <= 0) {
			show_404();
			return;
		}

		$acara = $this->model_realtime_tracker->get_acara_detail($id_acara);
		if (!$acara) {
			show_404();
			return;
		}

		$this->data['acara'] = $acara;
		$this->data['id_acara'] = $id_acara;
		$this->data['is_public'] = true;

		// Load view langsung tanpa template (public page)
		$this->load->view('public/tracker', $this->data);
	}

}

/* End of file Public_api.php */
/* Location: ./modules/acara_presensi_realtime_tracker/controllers/Public_api.php */
