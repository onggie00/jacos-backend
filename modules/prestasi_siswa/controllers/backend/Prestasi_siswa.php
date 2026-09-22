<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 *| --------------------------------------------------------------------------
 *| Prestasi Siswa Controller
 *| --------------------------------------------------------------------------
 *| Prestasi Siswa site
 *|
 */
class Prestasi_siswa extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_prestasi_siswa');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Prestasi Siswas
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('prestasi_siswa_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['prestasi_siswas'] = $this->model_prestasi_siswa->get($filter, $field, $this->limit_page, $offset);
		$this->data['prestasi_siswa_counts'] = $this->model_prestasi_siswa->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/prestasi_siswa/index/',
			'total_rows'   => $this->model_prestasi_siswa->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Prestasi Siswa List');
		$this->render('backend/standart/administrator/prestasi_siswa/prestasi_siswa_list', $this->data);
	}

	/**
	 * Add new prestasi_siswas
	 *
	 */
	public function add()
	{
		$this->is_allowed('prestasi_siswa_add');

		$this->template->title('Prestasi Siswa New');
		$this->render('backend/standart/administrator/prestasi_siswa/prestasi_siswa_add', $this->data);
	}

	/**
	 * Add New Prestasi Siswas
	 *
	 * @return JSON
	 */
	public function add_save()
	{
		if (!$this->is_allowed('prestasi_siswa_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tgl_raih', 'Tgl Raih', 'trim|required');
		$this->form_validation->set_rules('juara', 'Juara', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('prestasi_siswa_file_prestasi_name', 'File Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('foto_prestasi', 'Foto Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_siswa', 'Id Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('tanggal_posting', 'Tanggal Posting', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('is_approved', 'Is Approved', 'trim|required|max_length[6]');


		if ($this->form_validation->run()) {
			$prestasi_siswa_file_prestasi_uuid = $this->input->post('prestasi_siswa_file_prestasi_uuid');
			$prestasi_siswa_file_prestasi_name = $this->input->post('prestasi_siswa_file_prestasi_name');

			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'tgl_raih' => $this->input->post('tgl_raih'),
				'juara' => $this->input->post('juara'),
				'foto_prestasi' => $this->input->post('foto_prestasi'),
				'id_siswa' => $this->input->post('id_siswa'),
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
				'tanggal_posting' => $this->input->post('tanggal_posting'),
				'jenjang' => $this->input->post('jenjang'),
				'is_approved' => $this->input->post('is_approved'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_siswa/')) {
				mkdir(FCPATH . '/uploads/prestasi_siswa/');
			}

			if (!empty($prestasi_siswa_file_prestasi_name)) {
				$prestasi_siswa_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_siswa_file_prestasi_name;

				rename(
					FCPATH . 'uploads/tmp/' . $prestasi_siswa_file_prestasi_uuid . '/' . $prestasi_siswa_file_prestasi_name,
					FCPATH . 'uploads/prestasi_siswa/' . $prestasi_siswa_file_prestasi_name_copy
				);

				if (!is_file(FCPATH . '/uploads/prestasi_siswa/' . $prestasi_siswa_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_siswa_file_prestasi_name_copy;
			}


			$save_prestasi_siswa = $this->model_prestasi_siswa->store($save_data);


			if ($save_prestasi_siswa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_prestasi_siswa;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/prestasi_siswa/edit/' . $save_prestasi_siswa, 'Edit Prestasi Siswa'),
						anchor('administrator/prestasi_siswa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
							anchor('administrator/prestasi_siswa/edit/' . $save_prestasi_siswa, 'Edit Prestasi Siswa')
						]),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_siswa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_siswa');
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
	 * Update view Prestasi Siswas
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('prestasi_siswa_update');

		$this->data['prestasi_siswa'] = $this->model_prestasi_siswa->find($id);

		$this->template->title('Prestasi Siswa Update');
		$this->render('backend/standart/administrator/prestasi_siswa/prestasi_siswa_update', $this->data);
	}

	/**
	 * Update Prestasi Siswas
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('prestasi_siswa_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_prestasi', 'Nama Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tgl_raih', 'Tgl Raih', 'trim|required');
		$this->form_validation->set_rules('juara', 'Juara', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('prestasi_siswa_file_prestasi_name', 'File Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('foto_prestasi', 'Foto Prestasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_siswa', 'Id Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('tanggal_posting', 'Tanggal Posting', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('is_approved', 'Is Approved', 'trim|required|max_length[6]');

		if ($this->form_validation->run()) {
			$prestasi_siswa_file_prestasi_uuid = $this->input->post('prestasi_siswa_file_prestasi_uuid');
			$prestasi_siswa_file_prestasi_name = $this->input->post('prestasi_siswa_file_prestasi_name');

			$save_data = [
				'nama_prestasi' => $this->input->post('nama_prestasi'),
				'tgl_raih' => $this->input->post('tgl_raih'),
				'juara' => $this->input->post('juara'),
				'foto_prestasi' => $this->input->post('foto_prestasi'),
				'id_siswa' => $this->input->post('id_siswa'),
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
				'tanggal_posting' => $this->input->post('tanggal_posting'),
				'jenjang' => $this->input->post('jenjang'),
				'is_approved' => $this->input->post('is_approved'),
			];

			if (!is_dir(FCPATH . '/uploads/prestasi_siswa/')) {
				mkdir(FCPATH . '/uploads/prestasi_siswa/');
			}

			if (!empty($prestasi_siswa_file_prestasi_uuid)) {
				$prestasi_siswa_file_prestasi_name_copy = date('YmdHis') . '-' . $prestasi_siswa_file_prestasi_name;

				rename(
					FCPATH . 'uploads/tmp/' . $prestasi_siswa_file_prestasi_uuid . '/' . $prestasi_siswa_file_prestasi_name,
					FCPATH . 'uploads/prestasi_siswa/' . $prestasi_siswa_file_prestasi_name_copy
				);

				if (!is_file(FCPATH . '/uploads/prestasi_siswa/' . $prestasi_siswa_file_prestasi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['file_prestasi'] = $prestasi_siswa_file_prestasi_name_copy;
			}


			$save_prestasi_siswa = $this->model_prestasi_siswa->change($id, $save_data);

			if ($save_prestasi_siswa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/prestasi_siswa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_siswa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_siswa');
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
	 * delete Prestasi Siswas
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('prestasi_siswa_delete');

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
			set_message(cclang('has_been_deleted', 'prestasi_siswa'), 'success');
		} else {
			set_message(cclang('error_delete', 'prestasi_siswa'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Prestasi Siswas
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('prestasi_siswa_view');

		$this->data['prestasi_siswa'] = $this->model_prestasi_siswa->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Prestasi Siswa Detail');
		$this->render('backend/standart/administrator/prestasi_siswa/prestasi_siswa_view', $this->data);
	}

	/**
	 * delete Prestasi Siswas
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$prestasi_siswa = $this->model_prestasi_siswa->find($id);

		if (!empty($prestasi_siswa->file_prestasi)) {
			$path = FCPATH . '/uploads/prestasi_siswa/' . $prestasi_siswa->file_prestasi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}


		return $this->model_prestasi_siswa->remove($id);
	}

	/**
	 * delete Prestasi Siswas
	 *
	 * @var $id String
	 */
	public function approve($id = null)
	{
		$arr_id = $this->input->get('id');

		foreach ($arr_id as $id) {
			$this->mymodel->update('prestasi_siswa', array('is_approved' => '1'), 'id_prestasi', $id);
		}

		set_message('Update status berhasil', 'success');

		redirect_back();
	}

	public function reject($id = null)
	{
		$arr_id = $this->input->get('id');

		foreach ($arr_id as $id) {
			$this->mymodel->update('prestasi_siswa', array('is_approved' => '2'), 'id_prestasi', $id);
		}

		set_message('Update status berhasil', 'success');

		redirect_back();
	}

	/**
	 * Upload Image Prestasi Siswa	* 
	 * @return JSON
	 */
	public function upload_file_prestasi_file()
	{
		if (!$this->is_allowed('prestasi_siswa_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'prestasi_siswa',
		]);
	}

	/**
	 * Delete Image Prestasi Siswa	* 
	 * @return JSON
	 */
	public function delete_file_prestasi_file($uuid)
	{
		if (!$this->is_allowed('prestasi_siswa_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		echo $this->delete_file([
			'uuid'              => $uuid,
			'delete_by'         => $this->input->get('by'),
			'field_name'        => 'file_prestasi',
			'upload_path_tmp'   => './uploads/tmp/',
			'table_name'        => 'prestasi_siswa',
			'primary_key'       => 'id_prestasi',
			'upload_path'       => 'uploads/prestasi_siswa/'
		]);
	}

	/**
	 * Get Image Prestasi Siswa	* 
	 * @return JSON
	 */
	public function get_file_prestasi_file($id)
	{
		if (!$this->is_allowed('prestasi_siswa_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
			]);
			exit;
		}

		$prestasi_siswa = $this->model_prestasi_siswa->find($id);

		echo $this->get_file([
			'uuid'              => $id,
			'delete_by'         => 'id',
			'field_name'        => 'file_prestasi',
			'table_name'        => 'prestasi_siswa',
			'primary_key'       => 'id_prestasi',
			'upload_path'       => 'uploads/prestasi_siswa/',
			'delete_endpoint'   => 'administrator/prestasi_siswa/delete_file_prestasi_file'
		]);
	}


	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('prestasi_siswa_export');

		$this->model_prestasi_siswa->export('prestasi_siswa', 'prestasi_siswa');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('prestasi_siswa_export');

		$this->model_prestasi_siswa->pdf('prestasi_siswa', 'prestasi_siswa');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('prestasi_siswa_export');

		$table = $title = 'prestasi_siswa';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_prestasi_siswa->find($id);
		$fields = $result->list_fields();

		$content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
			'data' => $data,
			'fields' => $fields,
			'title' => $title
		], TRUE);

		$this->pdf->initialize($config);
		$this->pdf->pdf->SetDisplayMode('fullpage');
		$this->pdf->writeHTML($content);
		$this->pdf->Output($table . '.pdf', 'H');
	}
}


/* End of file prestasi_siswa.php */
/* Location: ./application/controllers/administrator/Prestasi Siswa.php */
