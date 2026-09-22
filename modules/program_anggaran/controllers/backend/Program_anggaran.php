<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Program Anggaran Controller
*| --------------------------------------------------------------------------
*| Program Anggaran site
*|
*/
class Program_anggaran extends Admin
{
	private $allowed_jenjang = array('sd', 'smp', 'sma');

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_program_anggaran');
		$this->lang->load('web_lang', $this->current_lang);

		$this->data['jenjang_context'] = $this->get_jenjang_context();
	}

	/**
	 * Deteksi jenjang dari URL segment ke-3
	 */
	private function get_jenjang_context()
	{
		$seg = $this->uri->segment(3);
		if (in_array(strtolower($seg), $this->allowed_jenjang)) {
			return strtolower($seg);
		}
		return null;
	}

	/**
	 * Parse multi filter dari GET parameter ff[], fo[], fv[]
	 */
	private function parse_filters()
	{
		$filters = array();
		$ff = $this->input->get('ff');
		$fo = $this->input->get('fo');
		$fv = $this->input->get('fv');

		if (is_array($ff) && count($ff) > 0) {
			$max = count($ff);
			for ($i = 0; $i < $max; $i++) {
				$field = isset($ff[$i]) ? $ff[$i] : '';
				$operator = isset($fo[$i]) ? $fo[$i] : '';
				$value = isset($fv[$i]) ? $fv[$i] : '';

				if (!empty($field) && !empty($operator) && trim($value) !== '') {
					$filters[] = array(
						'field'    => $field,
						'operator' => $operator,
						'value'    => $value
					);
				}
			}
		}

		return $filters;
	}

	/**
	 * Cek apakah record jenjang sesuai dengan context URL
	 */
	private function is_jenjang_match($record_jenjang)
	{
		if (empty($this->data['jenjang_context'])) {
			return true;
		}
		$allowed = $this->model_program_anggaran->get_scope_values($this->data['jenjang_context']);
		return in_array(strtoupper($record_jenjang), $allowed);
	}

	/**
	 * Redirect dengan pesan error jika akses jenjang tidak sesuai
	 */
	private function deny_jenjang_access($record_jenjang = '')
	{
		$message = 'Akses ditolak: data ini milik jenjang ' . strtoupper($record_jenjang) . '.';
		if (empty($record_jenjang)) {
			$message = 'Akses ditolak: data tidak ditemukan atau bukan milik jenjang yang dipilih.';
		}

		set_message($message, 'error');

		if (!empty($this->data['jenjang_context'])) {
			redirect('administrator/program_anggaran/' . $this->data['jenjang_context']);
		}
		redirect('administrator/program_anggaran');
	}

	/**
	* show all Program Anggarans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('program_anggaran_list');

		// Jika parameter pertama adalah jenjang (sd/smp/sma), sesuaikan context dan offset
		if (in_array(strtolower($offset), $this->allowed_jenjang)) {
			$this->data['jenjang_context'] = strtolower($offset);
			$offset = intval($this->uri->segment(4));
		}

		$jenjang_context = $this->data['jenjang_context'];

		$filters = $this->parse_filters();
		$search  = $this->input->get('q');

		// Jika mode jenjang, hilangkan filter jenjang dari user (tidak bisa override)
		if (!empty($jenjang_context)) {
			$clean_filters = array();
			foreach ($filters as $filter) {
				if ($filter['field'] !== 'jenjang') {
					$clean_filters[] = $filter;
				}
			}
			$filters = $clean_filters;
		}

		$this->data['multi_filters'] = $filters;

		$this->data['program_anggarans'] = $this->model_program_anggaran->get_filtered($filters, $search, $jenjang_context, $this->limit_page, $offset);
		$this->data['program_anggaran_counts'] = $this->model_program_anggaran->count_filtered($filters, $search, $jenjang_context);

		$config = array(
			'base_url'     => !empty($jenjang_context) ? 'administrator/program_anggaran/' . $jenjang_context . '/' : 'administrator/program_anggaran/index/',
			'total_rows'   => $this->data['program_anggaran_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		// === DASHBOARD WIDGET DATA (tidak ikut filter tabel) ===
		$ta_filter = $this->input->get('ta');
		if (!empty($ta_filter)) {
			$dashboard_ta = $ta_filter;
		} else {
			$dashboard_ta = $this->model_program_anggaran->get_active_tahun_ajaran();
			if (empty($dashboard_ta)) {
				$dashboard_ta = $this->model_program_anggaran->get_fallback_tahun_ajaran($jenjang_context);
			}
		}
		$this->data['dashboard_ta'] = $dashboard_ta;
		$this->data['dashboard_data'] = $this->model_program_anggaran->dashboard_aggregate($dashboard_ta, $jenjang_context);
		$this->data['dashboard_tahun_ajaran_options'] = $this->model_program_anggaran->get_tahun_ajaran_options($jenjang_context);
		// === END DASHBOARD WIDGET DATA ===

		$this->template->title('Program Anggaran List');
		$this->render('backend/standart/administrator/program_anggaran/program_anggaran_list', $this->data);
	}

	/**
	* Add new program_anggarans
	*
	*/
	public function add()
	{
		$this->is_allowed('program_anggaran_add');

		$this->template->title('Program Anggaran New');
		$this->render('backend/standart/administrator/program_anggaran/program_anggaran_add', $this->data);
	}

	/**
	* Add New Program Anggarans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('program_anggaran_add', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		// Handle jenjang berdasarkan scope URL
		// SD/SMP: paksa jenjang sesuai URL
		// SMA: izinkan pilihan SMA atau FT (admin SMA mengelola keduanya)
		if (!empty($this->data['jenjang_context'])) {
			$ctx = strtolower($this->data['jenjang_context']);
			if ($ctx === 'sma') {
				$posted_jenjang = strtoupper($this->input->post('jenjang'));
				if (!in_array($posted_jenjang, array('SMA', 'FT'))) {
					echo json_encode(array(
						'success' => false,
						'message' => 'Jenjang harus SMA atau FT untuk admin SMA.'
					));
					exit;
				}
				$_POST['jenjang'] = $posted_jenjang;
			} else {
				$_POST['jenjang'] = strtoupper($ctx);
			}
		}

		$this->form_validation->set_rules('nomor_program', 'Nomor Program', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nama_program', 'Nama Program', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nominal_okr', 'Nominal OKR', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Aktif?', 'trim|required');

		if ($this->form_validation->run()) {

			$save_data = array(
				'nomor_program' => $this->input->post('nomor_program'),
				'nama_program' => $this->input->post('nama_program'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'jenis_kegiatan' => $this->input->post('jenis_kegiatan'),
				'nominal_okr' => $this->input->post('nominal_okr'),
				'jenjang' => $this->input->post('jenjang'),
				'is_active' => $this->input->post('is_active'),
			);

			$save_program_anggaran = $this->model_program_anggaran->store($save_data);

			if ($save_program_anggaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_program_anggaran;
					$this->data['message'] = cclang('success_save_data_stay', array(
						anchor('administrator/program_anggaran/edit/' . $save_program_anggaran, 'Edit Program Anggaran'),
						anchor('administrator/program_anggaran' . (!empty($this->data['jenjang_context']) ? '/' . $this->data['jenjang_context'] : ''), ' Go back to list')
					));
				} else {
					set_message(
						cclang('success_save_data_redirect', array(
						anchor('administrator/program_anggaran/edit/' . $save_program_anggaran, 'Edit Program Anggaran')
					)), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/program_anggaran' . (!empty($this->data['jenjang_context']) ? '/' . $this->data['jenjang_context'] : ''));
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/program_anggaran' . (!empty($this->data['jenjang_context']) ? '/' . $this->data['jenjang_context'] : ''));
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
	* Update view Program Anggarans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('program_anggaran_update');

		$program_anggaran = $this->model_program_anggaran->find($id);

		if (!$program_anggaran || !$this->is_jenjang_match($program_anggaran->jenjang)) {
			$this->deny_jenjang_access(!empty($program_anggaran) ? $program_anggaran->jenjang : '');
		}

		$this->data['program_anggaran'] = $program_anggaran;

		$this->template->title('Program Anggaran Update');
		$this->render('backend/standart/administrator/program_anggaran/program_anggaran_update', $this->data);
	}

	/**
	* Update Program Anggarans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('program_anggaran_update', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$program_anggaran = $this->model_program_anggaran->find($id);
		if (!$program_anggaran || !$this->is_jenjang_match($program_anggaran->jenjang)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Akses ditolak: data ini bukan milik jenjang yang dipilih.'
			));
			exit;
		}

		// Handle jenjang berdasarkan scope URL
		// SD/SMP: paksa jenjang sesuai URL
		// SMA: izinkan pilihan SMA atau FT (admin SMA mengelola keduanya)
		if (!empty($this->data['jenjang_context'])) {
			$ctx = strtolower($this->data['jenjang_context']);
			if ($ctx === 'sma') {
				$posted_jenjang = strtoupper($this->input->post('jenjang'));
				if (!in_array($posted_jenjang, array('SMA', 'FT'))) {
					echo json_encode(array(
						'success' => false,
						'message' => 'Jenjang harus SMA atau FT untuk admin SMA.'
					));
					exit;
				}
				$_POST['jenjang'] = $posted_jenjang;
			} else {
				$_POST['jenjang'] = strtoupper($ctx);
			}
		}

		$this->form_validation->set_rules('nomor_program', 'Nomor Program', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nama_program', 'Nama Program', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nominal_okr', 'Nominal OKR', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Aktif?', 'trim|required');

		if ($this->form_validation->run()) {

			$save_data = array(
				'nomor_program' => $this->input->post('nomor_program'),
				'nama_program' => $this->input->post('nama_program'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'jenis_kegiatan' => $this->input->post('jenis_kegiatan'),
				'nominal_okr' => $this->input->post('nominal_okr'),
				'jenjang' => $this->input->post('jenjang'),
				'is_active' => $this->input->post('is_active'),
			);

			$save_program_anggaran = $this->model_program_anggaran->change($id, $save_data);

			if ($save_program_anggaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', array(
						anchor('administrator/program_anggaran' . (!empty($this->data['jenjang_context']) ? '/' . $this->data['jenjang_context'] : ''), ' Go back to list')
					));
				} else {
					set_message(
						cclang('success_update_data_redirect', array(
					)), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/program_anggaran' . (!empty($this->data['jenjang_context']) ? '/' . $this->data['jenjang_context'] : ''));
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/program_anggaran' . (!empty($this->data['jenjang_context']) ? '/' . $this->data['jenjang_context'] : ''));
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
	* delete Program Anggarans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('program_anggaran_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;
		$denied = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
			if ($remove === 'denied') {
				$denied = true;
			}
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$result = $this->_remove($id);
				if ($result === 'denied') {
					$denied = true;
				} elseif ($result === true) {
					$remove = true;
				}
			}
		}

		if ($denied) {
			set_message('Beberapa data tidak dapat dihapus karena bukan milik jenjang yang dipilih.', 'error');
		} elseif ($remove) {
			set_message(cclang('has_been_deleted', 'program_anggaran'), 'success');
		} else {
			set_message(cclang('error_delete', 'program_anggaran'), 'error');
		}

		redirect_back();
	}

	/**
	* View view Program Anggarans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('program_anggaran_view');

		$program_anggaran = $this->model_program_anggaran->join_avaiable()->filter_avaiable()->find($id);

		if (!$program_anggaran || !$this->is_jenjang_match($program_anggaran->jenjang)) {
			$this->deny_jenjang_access(!empty($program_anggaran) ? $program_anggaran->jenjang : '');
		}

		$this->data['program_anggaran'] = $program_anggaran;

		$this->template->title('Program Anggaran Detail');
		$this->render('backend/standart/administrator/program_anggaran/program_anggaran_view', $this->data);
	}

	/**
	* delete Program Anggarans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$program_anggaran = $this->model_program_anggaran->find($id);

		if (!$program_anggaran) {
			return false;
		}

		if (!$this->is_jenjang_match($program_anggaran->jenjang)) {
			return 'denied';
		}

		return $this->model_program_anggaran->remove($id);
	}


	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('program_anggaran_export');

		$this->model_program_anggaran->export('program_anggaran', 'program_anggaran');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('program_anggaran_export');

		$this->model_program_anggaran->pdf('program_anggaran', 'program_anggaran');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('program_anggaran_export');

		$table = $title = 'program_anggaran';
		$this->load->library('HtmlPdf');

        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight');

        $result = $this->db->get($table);

        $data = $this->model_program_anggaran->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', array(
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ), TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function not_active(){
		$arr_id = $this->input->get('id');
		$denied = false;
		$update = false;

		foreach ($arr_id as $id) {
			$record = $this->model_program_anggaran->find($id);
			if ($record && $this->is_jenjang_match($record->jenjang)) {
				$update = $this->mymodel->update("program_anggaran", array("is_active" => 0), "id", $id);
			} else {
				$denied = true;
			}
		}

		if ($denied) {
			set_message('Beberapa data tidak dapat dinonaktifkan karena bukan milik jenjang yang dipilih.', 'error');
		} elseif ($update) {
            set_message("Berhasil Nonaktifkan Program Anggaran", 'success');
        } else {
            set_message("Gagal Nonaktifkan Program Anggaran", 'error');
        }

		redirect_back();
	}

	public function active(){
		$arr_id = $this->input->get('id');
		$denied = false;
		$update = false;

		foreach ($arr_id as $id) {
			$record = $this->model_program_anggaran->find($id);
			if ($record && $this->is_jenjang_match($record->jenjang)) {
				$update = $this->mymodel->update("program_anggaran", array("is_active" => 1), "id", $id);
			} else {
				$denied = true;
			}
		}

		if ($denied) {
			set_message('Beberapa data tidak dapat diaktifkan karena bukan milik jenjang yang dipilih.', 'error');
		} elseif ($update) {
            set_message("Berhasil Aktifkan Program Anggaran", 'success');
        } else {
            set_message("Gagal Aktifkan Program Anggaran", 'error');
        }

		redirect_back();
	}

	public function import()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();
		$this->load->library("session");

		if (isset($_FILES["file_program"]["name"])) {
			$path = $_FILES["file_program"]["tmp_name"];
			$p=$_FILES["file_program"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->load->library("session");
				$this->session->set_flashdata('failed', 'Format harus .xlsx atau .xls');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {

					$data_pendaftaran = array(
						"nomor_program" => ucwords($worksheet->getCellByColumnAndRow(0, $row)->getValue()),
						"nama_program" => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
						"tahun_ajaran" => (string)$worksheet->getCellByColumnAndRow(2, $row)->getValue(),
						"jenis_kegiatan" => (string)$worksheet->getCellByColumnAndRow(3, $row)->getValue(),
						"nominal_okr" => (int)$worksheet->getCellByColumnAndRow(4, $row)->getValue(),
						"jenjang" => (string)$worksheet->getCellByColumnAndRow(5, $row)->getValue(),
					);

					// Jika mode jenjang, skip baris yang jenjangnya tidak sesuai
					if (!empty($this->data['jenjang_context'])) {
						if (strtoupper($data_pendaftaran["jenjang"]) !== strtoupper($this->data['jenjang_context'])) {
							continue;
						}
					}

					//check null
					foreach ($data_pendaftaran as $key => $item) {
						if(empty($item)){
							$this->db->trans_rollback();
							$this->session->set_flashdata('failed', "Data ada yang kosong");
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
					//cek nomor_program
					$cek_nomor = $this->mymodel->withquery("select * from program_anggaran where nomor_program = '".$data_pendaftaran["nomor_program"]."'","row");
					if(!empty($cek_nomor)){
						$insertId = $cek_nomor->id;
						$this->mymodel->update("program_anggaran", $data_pendaftaran, "id", $insertId);
					}
					else{
						$insertId = $this->mymodel->insertid("program_anggaran", $data_pendaftaran);
					}
					if (empty($insertId)) {
						echo "GAGAL INSERT".$data_pendaftaran["nomor_program"]." - ".$data_pendaftaran["nama_program"]."<br/><br/>";
						$this->session->set_flashdata('failed', 'Import program gagal ' . $data_pendaftaran["nama_program"]);
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import program berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->session->set_flashdata('error', 'Import program gagal (File program tidak valid)');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}


}


/* End of file program_anggaran.php */
/* Location: ./application/controllers/administrator/Program Anggaran.php */