<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ekskul Controller
*| --------------------------------------------------------------------------
*| Ekskul site
*|
*/
class Ekskul extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ekskul');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ekskuls
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ekskul_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ekskuls'] = $this->model_ekskul->get($filter, $field, $this->limit_page, $offset);
		$this->data['ekskul_counts'] = $this->model_ekskul->count_all($filter, $field);

		// Stats for cards
		$this->data['stats'] = new stdClass();
		$this->data['stats']->total = $this->model_ekskul->count_all();
		$this->data['stats']->sd = $this->db->where('jenjang', 'sd')->count_all_results('ekskul');
		$this->data['stats']->smp = $this->db->where('jenjang', 'smp')->count_all_results('ekskul');
		$this->data['stats']->sma = $this->db->where('jenjang', 'sma')->count_all_results('ekskul');
		$this->data['stats']->ft = $this->db->where('jenjang', 'ft')->count_all_results('ekskul');

		$config = array(
			'base_url'     => 'administrator/ekskul/index/',
			'total_rows'   => $this->model_ekskul->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		);

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ekstrakurikuler List');
		$this->render('backend/standart/administrator/ekskul/ekskul_list', $this->data);
	}
	
	/**
	* Add new ekskuls
	*
	*/
	public function add()
	{
		$this->is_allowed('ekskul_add');

		$this->template->title('Ekstrakurikuler New');
		$this->render('backend/standart/administrator/ekskul/ekskul_add', $this->data);
	}

	/**
	* Add New Ekskuls
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ekskul_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama', 'Ekstrakurikuler', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('ekskul_foto_name', 'Foto', 'trim|max_length[255]');
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('hari[]', 'Hari', 'trim|max_length[50]');
		$this->form_validation->set_rules('tanggal_posting', 'Tanggal Posting', 'trim');
		$this->form_validation->set_rules('nominal_biaya', 'Nominal Biaya', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('link_daftar', 'Link Pendaftaran', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tahun_ajaran_aktif', 'Tahun Ajaran Aktif', 'trim|max_length[20]');
		$this->form_validation->set_rules('semester_aktif', 'Semester Aktif', 'trim|max_length[1]|numeric');
		$this->form_validation->set_rules('bank', 'Bank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('rekening', 'Nomor Rekening', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('atas_nama_rekening', 'Atas Nama Rekening', 'trim|required|max_length[100]');

		if ($this->form_validation->run()) {
			$ekskul_foto_uuid = $this->input->post('ekskul_foto_uuid');
			$ekskul_foto_name = $this->input->post('ekskul_foto_name');
		
			$save_data = [
				'nama' => $this->input->post('nama'),
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
				'hari' => implode(',', (array) $this->input->post('hari')),
				'jam' => $this->input->post('jam'),
				'tanggal_posting' => $this->input->post('tanggal_posting'),
				'nominal_biaya' => $this->input->post('nominal_biaya'),
				'link_daftar' => $this->input->post('link_daftar'),
				'jenjang' => $this->input->post('jenjang'),
				'tahun_ajaran_aktif' => $this->input->post('tahun_ajaran_aktif'),
				'semester_aktif' => $this->input->post('semester_aktif'),
				'bank' => $this->input->post('bank'),
				'rekening' => $this->input->post('rekening'),
				'atas_nama_rekening' => $this->input->post('atas_nama_rekening'),
			];

			if (!is_dir(FCPATH . '/uploads/ekskul/')) {
				mkdir(FCPATH . '/uploads/ekskul/');
			}

			if (!empty($ekskul_foto_name)) {
				$ekskul_foto_name_copy = date('YmdHis') . '-' . $ekskul_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $ekskul_foto_uuid . '/' . $ekskul_foto_name, 
						FCPATH . 'uploads/ekskul/' . $ekskul_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/ekskul/' . $ekskul_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $ekskul_foto_name_copy;
			}
		
			
			$save_ekskul = $this->model_ekskul->store($save_data);
            

			if ($save_ekskul) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ekskul;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ekskul/edit/' . $save_ekskul, 'Edit Ekskul'),
						anchor('administrator/ekskul', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ekskul/edit/' . $save_ekskul, 'Edit Ekskul')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul');
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
	* Update view Ekskuls
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ekskul_update');

		$this->data['ekskul'] = $this->model_ekskul->find($id);

		$this->template->title('Ekstrakurikuler Update');
		$this->render('backend/standart/administrator/ekskul/ekskul_update', $this->data);
	}

	/**
	* Update Ekskuls
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ekskul_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama', 'Ekstrakurikuler', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('ekskul_foto_name', 'Foto', 'trim|max_length[255]');
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		$this->form_validation->set_rules('hari[]', 'Hari', 'trim|max_length[50]');
		$this->form_validation->set_rules('tanggal_posting', 'Tanggal Posting', 'trim');
		$this->form_validation->set_rules('nominal_biaya', 'Nominal Biaya', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('link_daftar', 'Link Pendaftaran', 'trim|required');
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tahun_ajaran_aktif', 'Tahun Ajaran Aktif', 'trim|max_length[20]');
		$this->form_validation->set_rules('semester_aktif', 'Semester Aktif', 'trim|max_length[1]|numeric');
		$this->form_validation->set_rules('bank', 'Bank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('rekening', 'Semester Aktif', 'trim|required|max_length[50]|numeric');
		$this->form_validation->set_rules('atas_nama_rekening', 'Atas Nama Rekening', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
			$ekskul_foto_uuid = $this->input->post('ekskul_foto_uuid');
			$ekskul_foto_name = $this->input->post('ekskul_foto_name');
		
			$save_data = [
				'nama' => $this->input->post('nama'),
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
				'hari' => implode(',', (array) $this->input->post('hari')),
				'jam' => $this->input->post('jam'),
				'tanggal_posting' => $this->input->post('tanggal_posting'),
				'nominal_biaya' => $this->input->post('nominal_biaya'),
				'link_daftar' => $this->input->post('link_daftar'),
				'jenjang' => $this->input->post('jenjang'),
				'tahun_ajaran_aktif' => $this->input->post('tahun_ajaran_aktif'),
				'semester_aktif' => $this->input->post('semester_aktif'),
				'bank' => $this->input->post('bank'),
				'rekening' => $this->input->post('rekening'),
				'atas_nama_rekening' => $this->input->post('atas_nama_rekening'),
			];

			if (!is_dir(FCPATH . '/uploads/ekskul/')) {
				mkdir(FCPATH . '/uploads/ekskul/');
			}

			if (!empty($ekskul_foto_uuid)) {
				$ekskul_foto_name_copy = date('YmdHis') . '-' . $ekskul_foto_name;

				rename(FCPATH . 'uploads/tmp/' . $ekskul_foto_uuid . '/' . $ekskul_foto_name, 
						FCPATH . 'uploads/ekskul/' . $ekskul_foto_name_copy);

				if (!is_file(FCPATH . '/uploads/ekskul/' . $ekskul_foto_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto'] = $ekskul_foto_name_copy;
			}
		
			
			$save_ekskul = $this->model_ekskul->change($id, $save_data);

			if ($save_ekskul) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ekskul', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul');
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
	* delete Ekskuls
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ekskul_delete');

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
            set_message(cclang('has_been_deleted', 'ekskul'), 'success');
        } else {
            set_message(cclang('error_delete', 'ekskul'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ekskuls
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ekskul_view');

		$this->data['ekskul'] = $this->model_ekskul->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ekstrakurikuler Detail');
		$this->render('backend/standart/administrator/ekskul/ekskul_view', $this->data);
	}
	
	/**
	* delete Ekskuls
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ekskul = $this->model_ekskul->find($id);

		if (!empty($ekskul->foto)) {
			$path = FCPATH . '/uploads/ekskul/' . $ekskul->foto;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_ekskul->remove($id);
	}
	
	/**
	* Upload Image Ekskul	* 
	* @return JSON
	*/
	public function upload_foto_file()
	{
		if (!$this->is_allowed('ekskul_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'ekskul',
		]);
	}

	/**
	* Delete Image Ekskul	* 
	* @return JSON
	*/
	public function delete_foto_file($uuid)
	{
		if (!$this->is_allowed('ekskul_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'foto', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'ekskul',
            'primary_key'       => 'id_ekskul',
            'upload_path'       => 'uploads/ekskul/'
        ]);
	}

	/**
	* Get Image Ekskul	* 
	* @return JSON
	*/
	public function get_foto_file($id)
	{
		if (!$this->is_allowed('ekskul_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$ekskul = $this->model_ekskul->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto', 
            'table_name'        => 'ekskul',
            'primary_key'       => 'id_ekskul',
            'upload_path'       => 'uploads/ekskul/',
            'delete_endpoint'   => 'administrator/ekskul/delete_foto_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ekskul_export');

		$this->model_ekskul->export('ekskul', 'ekskul');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ekskul_export');

		$this->model_ekskul->pdf('ekskul', 'ekskul');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ekskul_export');

		$table = $title = 'ekskul';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ekskul->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	/**
	* Get ekskul detail for AJAX modal
	*
	* @var $id String
	* @return JSON
	*/
	public function get_detail($id)
	{
		if (!$this->is_allowed('ekskul_view', false)) {
			echo json_encode(array(
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$ekskul = $this->model_ekskul->find($id);

		if (empty($ekskul)) {
			echo json_encode(array('error' => 'Data ekskul tidak ditemukan'));
			exit;
		}

		header('Content-Type: application/json');
		echo json_encode($ekskul);
	}

	public function setting_update(){
		$tahun_ajaran_aktif = $this->input->post('tahun_ajaran_aktif');
		$semester_aktif = $this->input->post('semester_aktif');
		$jenjang = $this->input->post('jenjang');
		$data_update = array(
			'tahun_ajaran_aktif' => $tahun_ajaran_aktif,
			'semester_aktif' => $semester_aktif,
		);
		$this->mymodel->update("ekskul", $data_update, "jenjang", $jenjang);
		$this->session->set_flashdata('success', 'Setting berhasil diubah');
		redirect('administrator/ekskul');
	}

	public function setting_bank_update(){
		$bank = $this->input->post('bank');
		$rekening = $this->input->post('rekening');
		$atas_nama_rekening = $this->input->post('atas_nama_rekening');
		$jenjang = $this->input->post('jenjang');
		$data_update = array(
			'bank' => $bank,
			'rekening' => $rekening,
			'atas_nama_rekening' => $atas_nama_rekening
		);
		$this->mymodel->update("ekskul", $data_update, "jenjang", $jenjang);
		$this->session->set_flashdata('success', 'Setting berhasil diubah');
		redirect('administrator/ekskul');
	}
}


/* End of file ekskul.php */
/* Location: ./application/controllers/administrator/Ekskul.php */