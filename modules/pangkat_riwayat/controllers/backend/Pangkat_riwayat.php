<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pangkat Riwayat Controller
*| --------------------------------------------------------------------------
*| Pangkat Riwayat site
*|
*/
class Pangkat_riwayat extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pangkat_riwayat');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pangkat Riwayats
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pangkat_riwayat_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pangkat_riwayats'] = $this->model_pangkat_riwayat->get($filter, $field, $this->limit_page, $offset);
		$this->data['pangkat_riwayat_counts'] = $this->model_pangkat_riwayat->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pangkat_riwayat/index/',
			'total_rows'   => $this->model_pangkat_riwayat->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);
		
		// Get promotion recommendation data
		$this->data['promotion_data'] = $this->model_pangkat_riwayat->get_promotion_recommendation();

		$this->template->title('Pangkat Riwayat List');
		$this->render('backend/standart/administrator/pangkat_riwayat/pangkat_riwayat_list', $this->data);
	}
	
	/**
	* Add new pangkat_riwayats
	*
	*/
	public function add()
	{
		$this->is_allowed('pangkat_riwayat_add');

		$this->template->title('Pangkat Riwayat New');
		$this->render('backend/standart/administrator/pangkat_riwayat/pangkat_riwayat_add', $this->data);
	}

	/**
	* Add New Pangkat Riwayats
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pangkat_riwayat_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('unit', 'Unit', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('nama_tabel', 'Role', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('id_user', 'User', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('nomor', 'Nomor', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('pangkat', 'Pangkat', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tmt', 'TMT', 'trim|required');
		$this->form_validation->set_rules('is_sk_calon', 'SK Calon?', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('is_cant_promoted', 'Sudah Maksimal?', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'unit' => $this->input->post('unit'),
				'nama_tabel' => $this->input->post('nama_tabel'),
				'id_user' => $this->input->post('id_user'),
				'npp' => $this->input->post('npp'),
				'nomor' => $this->input->post('nomor'),
				'pangkat' => $this->input->post('pangkat'),
				'golongan' => $this->input->post('golongan'),
				'tmt' => $this->input->post('tmt'),
				'is_sk_calon' => $this->input->post('is_sk_calon'),
				'is_cant_promoted' => $this->input->post('is_cant_promoted'),
			];

			
			$save_pangkat_riwayat = $this->model_pangkat_riwayat->store($save_data);
            

			if ($save_pangkat_riwayat) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pangkat_riwayat;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pangkat_riwayat/edit/' . $save_pangkat_riwayat, 'Edit Pangkat Riwayat'),
						anchor('administrator/pangkat_riwayat', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pangkat_riwayat/edit/' . $save_pangkat_riwayat, 'Edit Pangkat Riwayat')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pangkat_riwayat');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pangkat_riwayat');
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
	* Update view Pangkat Riwayats
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pangkat_riwayat_update');

		$this->data['pangkat_riwayat'] = $this->model_pangkat_riwayat->find($id);

		$this->template->title('Pangkat Riwayat Update');
		$this->render('backend/standart/administrator/pangkat_riwayat/pangkat_riwayat_update', $this->data);
	}

	/**
	* Update Pangkat Riwayats
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pangkat_riwayat_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('unit', 'Unit', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('nama_tabel', 'Role', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('id_user', 'User', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('nomor', 'Nomor', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('pangkat', 'Pangkat', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tmt', 'TMT', 'trim|required');
		$this->form_validation->set_rules('is_sk_calon', 'SK Calon?', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('is_cant_promoted', 'Sudah Maksimal?', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'unit' => $this->input->post('unit'),
				'nama_tabel' => $this->input->post('nama_tabel'),
				'id_user' => $this->input->post('id_user'),
				'npp' => $this->input->post('npp'),
				'nomor' => $this->input->post('nomor'),
				'pangkat' => $this->input->post('pangkat'),
				'golongan' => $this->input->post('golongan'),
				'tmt' => $this->input->post('tmt'),
				'is_sk_calon' => $this->input->post('is_sk_calon'),
				'is_cant_promoted' => $this->input->post('is_cant_promoted'),
			];

			
			$save_pangkat_riwayat = $this->model_pangkat_riwayat->change($id, $save_data);

			if ($save_pangkat_riwayat) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pangkat_riwayat', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pangkat_riwayat');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pangkat_riwayat');
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
	* delete Pangkat Riwayats
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pangkat_riwayat_delete');

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
            set_message(cclang('has_been_deleted', 'pangkat_riwayat'), 'success');
        } else {
            set_message(cclang('error_delete', 'pangkat_riwayat'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pangkat Riwayats
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pangkat_riwayat_view');

		$this->data['pangkat_riwayat'] = $this->model_pangkat_riwayat->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pangkat Riwayat Detail');
		$this->render('backend/standart/administrator/pangkat_riwayat/pangkat_riwayat_view', $this->data);
	}
	
	/**
	* delete Pangkat Riwayats
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pangkat_riwayat = $this->model_pangkat_riwayat->find($id);

		
		
		return $this->model_pangkat_riwayat->remove($id);
	}
	
	
	/**
	* Get users by table name (AJAX)
	*
	* @return JSON
	*/
	public function get_users_by_table()
	{
		$nama_tabel = $this->input->get('nama_tabel');
		
		if (empty($nama_tabel)) {
			echo json_encode([]);
			exit;
		}

		// Mapping table name to primary key
		$table_pk_map = [
			'pegawai' => 'id_pegawai',
			'guru_sd' => 'id_guru',
			'guru_smp' => 'id_guru',
			'guru_sma' => 'id_guru',
			'pimpinan_sd' => 'id_pimpinan',
			'pimpinan_smp' => 'id_pimpinan',
			'pimpinan_sma' => 'id_pimpinan',
			'pramubhakti' => 'id_pramubhakti',
			'security' => 'id_security',
		];

		if (!isset($table_pk_map[$nama_tabel])) {
			echo json_encode([]);
			exit;
		}

		$pk = $table_pk_map[$nama_tabel];
		$query = $this->db->select("{$pk} as id, nama_lengkap")
			->where('deleted_at IS NULL', null, false)
			->or_where('deleted_at', '0000-00-00 00:00:00')
			->order_by('nama_lengkap', 'ASC')
			->get($nama_tabel);

		echo json_encode($query->result());
		exit;
	}

	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pangkat_riwayat_export');

		$value 	= $this->input->get('q');
		$col 	= $this->input->get('f');

		$this->model_pangkat_riwayat->export_pangkat_riwayat('pangkat_riwayat', 'pangkat_riwayat', $value, $col);
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pangkat_riwayat_export');

		$this->model_pangkat_riwayat->pdf('pangkat_riwayat', 'pangkat_riwayat');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pangkat_riwayat_export');

		$table = $title = 'pangkat_riwayat';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pangkat_riwayat->find($id);
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
	* Import from excel
	*
	* @return JSON
	*/
	public function import()
	{
		if (!$this->is_allowed('pangkat_riwayat_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$config['upload_path']          = './uploads/pangkat_riwayat/';
		$config['allowed_types']        = 'xls|xlsx';
		$config['max_size']             = 5048;
		$config['file_name']            = 'import_pangkat_riwayat_' . time();

		// Create directory if not exists
		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, TRUE);
		}

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file_import')) {
			echo json_encode([
				'success' => false,
				'message' => $this->upload->display_errors('', '')
			]);
			exit;
		}

		$upload_data = $this->upload->data();
		$file_path = $upload_data['full_path'];

		$result = $this->model_pangkat_riwayat->import_data($file_path);

		// Delete uploaded file after import
		@unlink($file_path);

		echo json_encode($result);
		exit;
	}

}


/* End of file pangkat_riwayat.php */
/* Location: ./application/controllers/administrator/Pangkat Riwayat.php */