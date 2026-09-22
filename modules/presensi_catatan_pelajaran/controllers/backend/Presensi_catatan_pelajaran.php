<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Catatan Pelajaran Controller
*| --------------------------------------------------------------------------
*| Presensi Catatan Pelajaran site
*|
*/
class Presensi_catatan_pelajaran extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_catatan_pelajaran');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Catatan Pelajarans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_catatan_pelajaran_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_catatan_pelajarans'] = $this->model_presensi_catatan_pelajaran->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_catatan_pelajaran_counts'] = $this->model_presensi_catatan_pelajaran->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_catatan_pelajaran/index/',
			'total_rows'   => $this->model_presensi_catatan_pelajaran->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Catatan Presensi Pelajaran List');
		$this->render('backend/standart/administrator/presensi_catatan_pelajaran/presensi_catatan_pelajaran_list', $this->data);
	}
	
	/**
	* Add new presensi_catatan_pelajarans
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_catatan_pelajaran_add');

		$this->template->title('Catatan Presensi Pelajaran New');
		$this->render('backend/standart/administrator/presensi_catatan_pelajaran/presensi_catatan_pelajaran_add', $this->data);
	}

	/**
	* Add New Presensi Catatan Pelajarans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_catatan_pelajaran_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('id_siswa_aktif', 'NIS', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tanggal_waktu', 'Tanggal Waktu', 'trim|required');
		$this->form_validation->set_rules('status_hadir', 'Status Kehadiran', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
		$this->form_validation->set_rules('jam_ke', 'Jam Ke-', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenjang' => $this->input->post('jenjang'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'hari' => $this->input->post('hari'),
				'tanggal_waktu' => $this->input->post('tanggal_waktu'),
				'status_hadir' => $this->input->post('status_hadir'),
				'keterangan' => $this->input->post('keterangan'),
				'jam_ke' => $this->input->post('jam_ke'),
			];

			
			$save_presensi_catatan_pelajaran = $this->model_presensi_catatan_pelajaran->store($save_data);
            

			if ($save_presensi_catatan_pelajaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_catatan_pelajaran;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_catatan_pelajaran/edit/' . $save_presensi_catatan_pelajaran, 'Edit Presensi Catatan Pelajaran'),
						anchor('administrator/presensi_catatan_pelajaran', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_catatan_pelajaran/edit/' . $save_presensi_catatan_pelajaran, 'Edit Presensi Catatan Pelajaran')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_catatan_pelajaran');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_catatan_pelajaran');
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
	* Update view Presensi Catatan Pelajarans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_catatan_pelajaran_update');

		$this->data['presensi_catatan_pelajaran'] = $this->model_presensi_catatan_pelajaran->find($id);

		$this->template->title('Catatan Presensi Pelajaran Update');
		$this->render('backend/standart/administrator/presensi_catatan_pelajaran/presensi_catatan_pelajaran_update', $this->data);
	}

	/**
	* Update Presensi Catatan Pelajarans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_catatan_pelajaran_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('id_siswa_aktif', 'NIS', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tanggal_waktu', 'Tanggal Waktu', 'trim|required');
		$this->form_validation->set_rules('status_hadir', 'Status Kehadiran', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
		$this->form_validation->set_rules('jam_ke', 'Jam Ke-', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenjang' => $this->input->post('jenjang'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'hari' => $this->input->post('hari'),
				'tanggal_waktu' => $this->input->post('tanggal_waktu'),
				'status_hadir' => $this->input->post('status_hadir'),
				'keterangan' => $this->input->post('keterangan'),
				'jam_ke' => $this->input->post('jam_ke'),
			];

			
			$save_presensi_catatan_pelajaran = $this->model_presensi_catatan_pelajaran->change($id, $save_data);

			if ($save_presensi_catatan_pelajaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_catatan_pelajaran', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_catatan_pelajaran');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_catatan_pelajaran');
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
	* delete Presensi Catatan Pelajarans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_catatan_pelajaran_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_catatan_pelajaran'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_catatan_pelajaran'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Catatan Pelajarans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_catatan_pelajaran_view');

		$this->data['presensi_catatan_pelajaran'] = $this->model_presensi_catatan_pelajaran->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Catatan Presensi Pelajaran Detail');
		$this->render('backend/standart/administrator/presensi_catatan_pelajaran/presensi_catatan_pelajaran_view', $this->data);
	}
	
	/**
	* delete Presensi Catatan Pelajarans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_catatan_pelajaran = $this->model_presensi_catatan_pelajaran->find($id);

		
		
		return $this->model_presensi_catatan_pelajaran->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_catatan_pelajaran_export');

		$this->model_presensi_catatan_pelajaran->export('presensi_catatan_pelajaran', 'presensi_catatan_pelajaran');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_catatan_pelajaran_export');

		$this->model_presensi_catatan_pelajaran->pdf('presensi_catatan_pelajaran', 'presensi_catatan_pelajaran');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_catatan_pelajaran_export');

		$table = $title = 'presensi_catatan_pelajaran';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_catatan_pelajaran->find($id);
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


/* End of file presensi_catatan_pelajaran.php */
/* Location: ./application/controllers/administrator/Presensi Catatan Pelajaran.php */