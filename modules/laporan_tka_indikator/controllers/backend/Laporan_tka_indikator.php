<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Laporan Tka Indikator Controller
*| --------------------------------------------------------------------------
*| Laporan Tka Indikator site
*
*/
class Laporan_tka_indikator extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_laporan_tka_indikator');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Laporan Tka Indikators
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('laporan_tka_indikator_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$tahun_filter = $this->input->get('tahun');
		$kategori_filter = $this->input->get('kategori');

		$this->data['laporan_tka_indikators'] = $this->model_laporan_tka_indikator->get($filter, $field, $this->limit_page, $offset, [], $tahun_filter, $kategori_filter);
		$this->data['laporan_tka_indikator_counts'] = $this->model_laporan_tka_indikator->count_all($filter, $field, $tahun_filter, $kategori_filter);

		$config = [
			'base_url'     => 'administrator/laporan_tka_indikator/index/',
			'total_rows'   => $this->model_laporan_tka_indikator->count_all($filter, $field, $tahun_filter, $kategori_filter),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		// Data untuk filter modal
		$this->data['list_tahun'] = $this->model_laporan_tka_indikator->get_unique_tahun();
		$this->data['list_kategori'] = $this->model_laporan_tka_indikator->get_unique_kategori();
		$this->data['filter_tahun'] = $tahun_filter;
		$this->data['filter_kategori'] = $kategori_filter;
		$this->data['has_filter'] = (!empty($tahun_filter) || !empty($kategori_filter)) ? true : false;

		$this->template->title('Laporan TKA Indikator');
		$this->render('backend/standart/administrator/laporan_tka_indikator/laporan_tka_indikator_list', $this->data);
	}
	
	/**
	* Add new laporan_tka_indikators
	*
	*/
	public function add()
	{
		$this->is_allowed('laporan_tka_indikator_add');

		$this->template->title('Tambah Laporan TKA Indikator');
		$this->render('backend/standart/administrator/laporan_tka_indikator/laporan_tka_indikator_add', $this->data);
	}

	/**
	* Add New Laporan Tka Indikators
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('laporan_tka_indikator_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul_kategori', 'Kategori', 'trim|required|max_length[120]');
		$this->form_validation->set_rules('soal', 'Soal', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun', 'Tahun', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul_kategori' => $this->input->post('judul_kategori'),
				'soal' => $this->input->post('soal'),
				'no_urut' => $this->input->post('no_urut'),
				'tahun' => $this->input->post('tahun'),
			];

			
			$save_laporan_tka_indikator = $this->model_laporan_tka_indikator->store($save_data);
            

			if ($save_laporan_tka_indikator) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_laporan_tka_indikator;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/laporan_tka_indikator/edit/' . $save_laporan_tka_indikator, 'Edit Laporan TKA Indikator'),
						anchor('administrator/laporan_tka_indikator', ' Kembali ke daftar')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/laporan_tka_indikator/edit/' . $save_laporan_tka_indikator, 'Edit Laporan TKA Indikator')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_tka_indikator');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_tka_indikator');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Validasi gagal';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Laporan Tka Indikators
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('laporan_tka_indikator_update');

		$this->data['laporan_tka_indikator'] = $this->model_laporan_tka_indikator->find($id);

		$this->template->title('Edit Laporan TKA Indikator');
		$this->render('backend/standart/administrator/laporan_tka_indikator/laporan_tka_indikator_update', $this->data);
	}

	/**
	* Update Laporan Tka Indikators
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('laporan_tka_indikator_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul_kategori', 'Kategori', 'trim|required|max_length[120]');
		$this->form_validation->set_rules('soal', 'Soal', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun', 'Tahun', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul_kategori' => $this->input->post('judul_kategori'),
				'soal' => $this->input->post('soal'),
				'no_urut' => $this->input->post('no_urut'),
				'tahun' => $this->input->post('tahun'),
			];

			
			$save_laporan_tka_indikator = $this->model_laporan_tka_indikator->change($id, $save_data);

			if ($save_laporan_tka_indikator) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/laporan_tka_indikator', ' Kembali ke daftar')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_tka_indikator');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_tka_indikator');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Validasi gagal';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Laporan Tka Indikators
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('laporan_tka_indikator_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'laporan_tka_indikator'), 'success');
        } else {
            set_message(cclang('error_delete', 'laporan_tka_indikator'), 'error');
        }

		redirect_back();
	}

	/**
	* delete Laporan Tka Indikators
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$laporan_tka_indikator = $this->model_laporan_tka_indikator->find($id);

		
		
		return $this->model_laporan_tka_indikator->remove($id);
	}
	
	
	/**
	* Export to excel - hanya export sesuai filter
	*
	* @return Files Excel .xlsx
	*/
	public function export()
	{
		$this->is_allowed('laporan_tka_indikator_export');

		$tahun = $this->input->get('tahun');
		$kategori = $this->input->get('kategori');

		// Redirect jika tidak ada filter sama sekali
		if (empty($tahun) && empty($kategori)) {
			set_message('Pilih minimal satu filter (tahun atau kategori) sebelum mengekspor data.', 'error');
			redirect_back();
			return;
		}

		$this->model_laporan_tka_indikator->export_filtered('laporan_tka_indikator', $tahun, $kategori);
	}

	/**
	* Import from excel
	*
	* @return JSON
	*/
	public function import()
	{
		if (!$this->is_allowed('laporan_tka_indikator_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->load->library('session');

		if (!isset($_FILES["file_import"]["name"]) || empty($_FILES["file_import"]["name"])) {
			echo json_encode([
				'success' => false,
				'message' => 'File tidak ditemukan. Silakan pilih file Excel.'
			]);
			exit;
		}

		$path = $_FILES["file_import"]["tmp_name"];
		$filename = $_FILES["file_import"]["name"];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if ($ext != 'xlsx' && $ext != 'xls') {
			echo json_encode([
				'success' => false,
				'message' => 'Format file harus .xlsx atau .xls'
			]);
			exit;
		}

		try {
			$this->load->library('Excel/PHPExcel');

			$object = PHPExcel_IOFactory::load($path);
			$worksheet = $object->getActiveSheet();
			$highestRow = $worksheet->getHighestRow();
			$total_imported = 0;
			$duplicate_list = [];
			$error_list = [];

			$this->db->trans_begin();

			for ($row = 2; $row <= $highestRow; $row++) {
				$no_urut = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
				$soal = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
				$kategori = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());
				$tahun = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

				// Handle null
				$soal = $soal ? $soal : '';
				$kategori = $kategori ? $kategori : '';
				$no_urut = ($no_urut !== null) ? intval($no_urut) : 0;
				$tahun = ($tahun !== null) ? intval($tahun) : 0;

				// Skip baris kosong
				if (empty($soal) && empty($kategori)) {
					continue;
				}

				// Validasi
				if (empty($soal)) {
					$error_list[] = "Baris {$row}: Soal kosong";
					continue;
				}
				if (empty($kategori)) {
					$error_list[] = "Baris {$row}: Kategori kosong";
					continue;
				}
				if ($tahun <= 0) {
					$error_list[] = "Baris {$row}: Tahun tidak valid (" . $worksheet->getCellByColumnAndRow(3, $row)->getValue() . ")";
					continue;
				}

				// Cek duplikat
				if ($this->model_laporan_tka_indikator->is_duplicate($tahun, $soal, $kategori, $no_urut)) {
					$duplicate_list[] = "Baris {$row}: Sudah ada di database";
					continue;
				}

				$save_data = [
					'no_urut' => $no_urut,
					'soal' => $soal,
					'judul_kategori' => $kategori,
					'tahun' => $tahun,
				];

				$save = $this->model_laporan_tka_indikator->store($save_data);
				if ($save) {
					$total_imported++;
				} else {
					$db_error = $this->db->error();
					$error_list[] = "Baris {$row}: Gagal simpan - " . $db_error['message'];
				}
			}

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				echo json_encode([
					'success' => false,
					'message' => 'Gagal mengimpor data. Transaksi dibatalkan.'
				]);
				exit;
			}

			$this->db->trans_commit();

			$has_issue = !empty($error_list) || !empty($duplicate_list);
			$detail_html = '';

			if (!empty($error_list)) {
				$detail_html .= '<b>Gagal import (' . count($error_list) . '):</b><br>';
				foreach ($error_list as $e) $detail_html .= '• ' . htmlspecialchars($e) . '<br>';
			}
			if (!empty($duplicate_list)) {
				$detail_html .= '<b>Duplikat (' . count($duplicate_list) . '):</b><br>';
				foreach ($duplicate_list as $d) $detail_html .= '• ' . htmlspecialchars($d) . '<br>';
			}

			$success_msg = "Import berhasil! {$total_imported} data diimpor.";
			if ($has_issue) $success_msg .= " " . (count($error_list) + count($duplicate_list)) . " data dilewati.";

			echo json_encode([
				'success' => true,
				'message' => $success_msg,
				'detail' => $detail_html,
				'has_issue' => $has_issue,
			]);

		} catch (\Exception $e) {
			if ($this->db->trans_started()) {
				$this->db->trans_rollback();
			}
			echo json_encode([
				'success' => false,
				'message' => 'Error: ' . $e->getMessage()
			]);
		}
	}

	/**
	* Download sample import file
	*
	*/
	public function download_sample()
	{
		$file = FCPATH . 'berkas_sample/Daftar Soal Indikator.xlsx';
		
		if (file_exists($file)) {
			$this->load->helper('download');
			force_download('Daftar Soal Indikator.xlsx', file_get_contents($file));
		} else {
			show_404();
		}
	}

	/**
	* Hapus data berdasarkan filter (tahun dan/atau kategori)
	*
	* @return JSON
	*/
	public function clear_filtered()
	{
		if (!$this->is_allowed('laporan_tka_indikator_delete', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$tahun = $this->input->post('tahun');
		$kategori = $this->input->post('kategori');

		if (empty($tahun) && empty($kategori)) {
			echo json_encode([
				'success' => false,
				'message' => 'Pilih minimal satu filter (tahun atau kategori) untuk mengosongkan data.'
			]);
			exit;
		}

		// Hitung data yang akan dihapus
		$total = $this->model_laporan_tka_indikator->count_all(null, null, $tahun, $kategori);

		if ($total == 0) {
			echo json_encode([
				'success' => false,
				'message' => 'Tidak ada data yang ditemukan untuk filter yang dipilih.'
			]);
			exit;
		}

		$deleted = $this->model_laporan_tka_indikator->delete_filtered($tahun, $kategori);

		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => "Berhasil menghapus {$total} data indikator."
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Gagal menghapus data. Silakan coba lagi.'
			]);
		}
	}

	
}


/* End of file laporan_tka_indikator.php */
/* Location: ./application/controllers/administrator/Laporan Tka Indikator.php */
