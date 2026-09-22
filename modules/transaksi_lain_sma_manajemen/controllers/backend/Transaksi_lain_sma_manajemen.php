<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Transaksi Lain Sd Manajemen Controller
*| --------------------------------------------------------------------------
*| Transaksi Lain Sd Manajemen site
*|
*/
class Transaksi_lain_sma_manajemen extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_transaksi_lain_sma_manajemen');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Transaksi Lain Sd Manajemens
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['transaksi_lain_sma_manajemens'] = $this->model_transaksi_lain_sma_manajemen->get($filter, $field, $this->limit_page, $offset);
		$this->data['transaksi_lain_sma_manajemen_counts'] = $this->model_transaksi_lain_sma_manajemen->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/transaksi_lain_sma_manajemen/index/',
			'total_rows'   => $this->model_transaksi_lain_sma_manajemen->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		// Infobox stats
		$this->data['stats'] = $this->model_transaksi_lain_sma_manajemen->get_stats($filter, $field);

		// Chart data
		$this->data['chart_data'] = $this->model_transaksi_lain_sma_manajemen->get_chart_data($filter, $field);

		// Pass current filter to view
		$this->data['current_filter_q'] = $filter;
		$this->data['current_filter_f'] = $field;

		$this->template->title('Manajemen Tagihan SMA List');
		$this->render('backend/standart/administrator/transaksi_lain_sma_manajemen/transaksi_lain_sma_manajemen_list', $this->data);
	}
	
	/**
	* Add new transaksi_lain_sma_manajemens
	*
	*/
	public function add()
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_add');

		$this->template->title('Manajemen Tagihan SMA New');
		$this->render('backend/standart/administrator/transaksi_lain_sma_manajemen/transaksi_lain_sma_manajemen_add', $this->data);
	}

	/**
	* Add New Transaksi Lain Sd Manajemens
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('transaksi_lain_sma_manajemen_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_kategori', 'Kategori', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_transaksi', 'Nama Tagihan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nominal', 'Nominal', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Tahun Ajaran', 'trim|max_length[11]');
		$this->form_validation->set_rules('id_tingkatan', 'Tingkatan', 'trim|max_length[100]');
		$this->form_validation->set_rules('id_kelas[]', 'Kelas', 'trim|max_length[100]');
		$this->form_validation->set_rules('id_siswa[]', 'Siswa', 'trim|max_length[255]');
		$this->form_validation->set_rules('tanggal_tagihan_mulai', 'Tagihan Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_tagihan_selesai', 'Tagihan Selesai', 'trim|required');
		$this->form_validation->set_rules('tipe_bank', 'Bank', 'trim|required|max_length[3]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_kategori' => $this->input->post('id_kategori'),
				'nama_transaksi' => $this->input->post('nama_transaksi'),
				'keterangan' => $this->input->post('keterangan'),
				'nominal' => $this->input->post('nominal'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'id_kelas' => implode(',', (array) $this->input->post('id_kelas')),
				'id_siswa' => implode(',', (array) $this->input->post('id_siswa')),
				'tanggal_tagihan_mulai' => $this->input->post('tanggal_tagihan_mulai'),
				'tanggal_tagihan_selesai' => $this->input->post('tanggal_tagihan_selesai'),
				'tipe_bank' => $this->input->post('tipe_bank'),
			];

			
			$save_transaksi_lain_sma_manajemen = $this->model_transaksi_lain_sma_manajemen->store($save_data);
            

			if ($save_transaksi_lain_sma_manajemen) {
				//cek tingkatan, kelas dan siswa
				$post = $this->input->post();
				$q = "";
				$get_data = "";
				if (!empty($post['id_siswa'])) {
					for ($i=0; $i < count($post['id_siswa']); $i++) { 
						if ($q == "") {
							$q = "where s.id_siswa_sma_aktif = '".$post['id_siswa'][$i]."'";
						}
						else{
							$q .= " or s.id_siswa_sma_aktif = '".$post['id_siswa'][$i]."'";
						}
					}
					$get_data = $this->mymodel->withquery("select s.id_siswa_sma_aktif as id_siswa_aktif, s.nama_lengkap, s.id_tahun_ajaran, k.label as nama_kelas, ta.label as tahun_ajaran, s.device_id_siswa, s.device_id_ortu from siswa_sma_aktif s join kelas_sma k on s.id_kelas = k.id_kelas_sma join tahun_ajaran ta on s.id_tahun_ajaran = ta.id_tahun_ajaran ".$q." order by s.nama_lengkap ASC","result");
				}
				else if (!empty($post['id_kelas'])) {
					for ($i=0; $i < count($post['id_kelas']); $i++) { 
						if ($q == "") {
							$q = "where s.id_kelas = '".$post['id_kelas'][$i]."'";
						}
						else{
							$q .= " or s.id_kelas = '".$post['id_kelas'][$i]."'";
						}
					}
					$get_data = $this->mymodel->withquery("select s.id_siswa_sma_aktif as id_siswa_aktif, s.nama_lengkap, s.id_tahun_ajaran, k.label as nama_kelas, ta.label as tahun_ajaran, s.device_id_siswa, s.device_id_ortu from siswa_sma_aktif s join kelas_sma k on s.id_kelas = k.id_kelas_sma join tahun_ajaran ta on s.id_tahun_ajaran = ta.id_tahun_ajaran ".$q." order by s.nama_lengkap ASC","result");
				}
				else if (!empty($post['id_tingkatan'])) {
					for ($i=0; $i < count($post['id_tingkatan']); $i++) { 
						if ($q == "") {
							$q = "where k.id_tingkatan = '".$post['id_tingkatan'][$i]."'";
						}
						else{
							$q .= " or k.id_tingkatan = '".$post['id_tingkatan'][$i]."'";
						}
					}
					$get_data = $this->mymodel->withquery("select s.id_siswa_sma_aktif as id_siswa_aktif, s.nama_lengkap, s.id_tahun_ajaran, k.label as nama_kelas, ta.label as tahun_ajaran, s.device_id_siswa, s.device_id_ortu from siswa_sma_aktif s join kelas_sma k on s.id_kelas = k.id_kelas_sma join tahun_ajaran ta on s.id_tahun_ajaran = ta.id_tahun_ajaran ".$q." order by s.nama_lengkap ASC","result");
				}				

				if (!empty($get_data)) {
					foreach ($get_data as $key => $value) {
						$array_insert = array(
							"id_siswa_aktif" => $value->id_siswa_aktif,
							"id_transaksi_lain" => $save_transaksi_lain_sma_manajemen,
							"nominal_bayar" => $post['nominal'],
							"status_transaksi" => 0,
							"created_at" => date("Y-m-d H:i:s")
						);
						$this->mymodel->insert("transaksi_lain_sd",$array_insert);
					}
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_transaksi_lain_sma_manajemen;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/transaksi_lain_sma_manajemen/edit/' . $save_transaksi_lain_sma_manajemen, 'Edit Transaksi Lain Sd Manajemen'),
						anchor('administrator/transaksi_lain_sma_manajemen', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/transaksi_lain_sma_manajemen/edit/' . $save_transaksi_lain_sma_manajemen, 'Edit Transaksi Lain Sd Manajemen')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi_lain_sma_manajemen');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi_lain_sma_manajemen');
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
	* Update view Transaksi Lain Sd Manajemens
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_update');

		$this->data['transaksi_lain_sma_manajemen'] = $this->model_transaksi_lain_sma_manajemen->find($id);

		$this->template->title('Manajemen Tagihan SMA Update');
		$this->render('backend/standart/administrator/transaksi_lain_sma_manajemen/transaksi_lain_sma_manajemen_update', $this->data);
	}

	/**
	* Update Transaksi Lain Sd Manajemens
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('transaksi_lain_sma_manajemen_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_kategori', 'Kategori', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_transaksi', 'Nama Tagihan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nominal', 'Nominal', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Tahun Ajaran', 'trim|max_length[11]');
		$this->form_validation->set_rules('id_tingkatan', 'Tingkatan', 'trim|max_length[100]');
		$this->form_validation->set_rules('id_kelas[]', 'Kelas', 'trim|max_length[100]');
		$this->form_validation->set_rules('id_siswa[]', 'Siswa', 'trim|max_length[255]');
		$this->form_validation->set_rules('tanggal_tagihan_mulai', 'Tagihan Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_tagihan_selesai', 'Tagihan Selesai', 'trim|required');
		$this->form_validation->set_rules('tipe_bank', 'Bank', 'trim|required|max_length[3]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_kategori' => $this->input->post('id_kategori'),
				'nama_transaksi' => $this->input->post('nama_transaksi'),
				'keterangan' => $this->input->post('keterangan'),
				'nominal' => $this->input->post('nominal'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'id_kelas' => implode(',', (array) $this->input->post('id_kelas')),
				'id_siswa' => implode(',', (array) $this->input->post('id_siswa')),
				'tanggal_tagihan_mulai' => $this->input->post('tanggal_tagihan_mulai'),
				'tanggal_tagihan_selesai' => $this->input->post('tanggal_tagihan_selesai'),
				'tipe_bank' => $this->input->post('tipe_bank'),
			];

			
			$save_transaksi_lain_sma_manajemen = $this->model_transaksi_lain_sma_manajemen->change($id, $save_data);

			if ($save_transaksi_lain_sma_manajemen) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/transaksi_lain_sma_manajemen', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi_lain_sma_manajemen');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi_lain_sma_manajemen');
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
	* delete Transaksi Lain Sd Manajemens
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_delete');

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
            set_message(cclang('has_been_deleted', 'transaksi_lain_sma_manajemen'), 'success');
        } else {
            set_message(cclang('error_delete', 'transaksi_lain_sma_manajemen'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Transaksi Lain Sd Manajemens
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_view');

		$this->data['transaksi_lain_sma_manajemen'] = $this->model_transaksi_lain_sma_manajemen->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Manajemen Tagihan SMA Detail');
		$this->render('backend/standart/administrator/transaksi_lain_sma_manajemen/transaksi_lain_sma_manajemen_view', $this->data);
	}
	
	/**
	* delete Transaksi Lain Sd Manajemens
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$transaksi_lain_sma_manajemen = $this->model_transaksi_lain_sma_manajemen->find($id);

		
		
		return $this->model_transaksi_lain_sma_manajemen->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_export');

		$filter = $this->input->get('q');
		$field = $this->input->get('f');

		$this->model_transaksi_lain_sma_manajemen->export_filtered('transaksi_lain_sma_manajemen', 'transaksi_lain_sma_manajemen', $filter, $field);
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_export');

		$this->model_transaksi_lain_sma_manajemen->pdf('transaksi_lain_sma_manajemen', 'transaksi_lain_sma_manajemen');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('transaksi_lain_sma_manajemen_export');

		$table = $title = 'transaksi_lain_sma_manajemen';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_transaksi_lain_sma_manajemen->find($id);
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

	
}


/* End of file transaksi_lain_sma_manajemen.php */
/* Location: ./application/controllers/administrator/Transaksi Lain Sd Manajemen.php */