<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ekskul Presensi Pelatih Sma Controller
*| --------------------------------------------------------------------------
*| Ekskul Presensi Pelatih Sma site
*|
*/
class Ekskul_presensi_pelatih_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ekskul_presensi_pelatih_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ekskul Presensi Pelatih Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ekskul_presensi_pelatih_smas'] = $this->model_ekskul_presensi_pelatih_sma->get($filter, $field, $this->limit_page, $offset);
		$this->data['ekskul_presensi_pelatih_sma_counts'] = $this->model_ekskul_presensi_pelatih_sma->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ekskul_presensi_pelatih_sma/index/',
			'total_rows'   => $this->model_ekskul_presensi_pelatih_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Pelatih  SMA List');
		$this->render('backend/standart/administrator/ekskul_presensi_pelatih_sma/ekskul_presensi_pelatih_sma_list', $this->data);
	}
	
	/**
	* Add new ekskul_presensi_pelatih_smas
	*
	*/
	public function add()
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_add');

		$this->template->title('Presensi Pelatih  SMA New');
		$this->render('backend/standart/administrator/ekskul_presensi_pelatih_sma/ekskul_presensi_pelatih_sma_add', $this->data);
	}

	/**
	* Add New Ekskul Presensi Pelatih Smas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ekskul_presensi_pelatih_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id', 'Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_pelatih', 'Pelatih', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('status_absen', 'Status', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('keterangan_presensi', 'Keterangan', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id' => $this->input->post('id'),
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_pelatih' => $this->input->post('id_pelatih'),
				'hari_absen' => $this->input->post('hari_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'keterangan_presensi' => $this->input->post('keterangan_presensi'),
			];

			
			$save_ekskul_presensi_pelatih_sma = $this->model_ekskul_presensi_pelatih_sma->store($save_data);
                        $save_ekskul_presensi_pelatih_sma = true;
            

			if ($save_ekskul_presensi_pelatih_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ekskul_presensi_pelatih_sma;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ekskul_presensi_pelatih_sma/edit/' . $save_ekskul_presensi_pelatih_sma, 'Edit Ekskul Presensi Pelatih Sma'),
						anchor('administrator/ekskul_presensi_pelatih_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ekskul_presensi_pelatih_sma/edit/' . $save_ekskul_presensi_pelatih_sma, 'Edit Ekskul Presensi Pelatih Sma')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_pelatih_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_pelatih_sma');
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
	* Update view Ekskul Presensi Pelatih Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_update');

		$this->data['ekskul_presensi_pelatih_sma'] = $this->model_ekskul_presensi_pelatih_sma->find($id);

		$this->template->title('Presensi Pelatih  SMA Update');
		$this->render('backend/standart/administrator/ekskul_presensi_pelatih_sma/ekskul_presensi_pelatih_sma_update', $this->data);
	}

	/**
	* Update Ekskul Presensi Pelatih Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ekskul_presensi_pelatih_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id', 'Id', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_pelatih', 'Pelatih', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('status_absen', 'Status', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('keterangan_presensi', 'Keterangan', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id' => $this->input->post('id'),
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_pelatih' => $this->input->post('id_pelatih'),
				'hari_absen' => $this->input->post('hari_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'keterangan_presensi' => $this->input->post('keterangan_presensi'),
			];

			
			$save_ekskul_presensi_pelatih_sma = $this->model_ekskul_presensi_pelatih_sma->change($id, $save_data);

			if ($save_ekskul_presensi_pelatih_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ekskul_presensi_pelatih_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_pelatih_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_pelatih_sma');
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
	* delete Ekskul Presensi Pelatih Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_delete');

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
            set_message(cclang('has_been_deleted', 'ekskul_presensi_pelatih_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'ekskul_presensi_pelatih_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ekskul Presensi Pelatih Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_view');

		$this->data['ekskul_presensi_pelatih_sma'] = $this->model_ekskul_presensi_pelatih_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Pelatih  SMA Detail');
		$this->render('backend/standart/administrator/ekskul_presensi_pelatih_sma/ekskul_presensi_pelatih_sma_view', $this->data);
	}
	
	/**
	* delete Ekskul Presensi Pelatih Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ekskul_presensi_pelatih_sma = $this->model_ekskul_presensi_pelatih_sma->find($id);

		
		
		return $this->model_ekskul_presensi_pelatih_sma->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_export');

		$this->model_ekskul_presensi_pelatih_sma->export('ekskul_presensi_pelatih_sma', 'ekskul_presensi_pelatih_sma');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_export');

		$this->model_ekskul_presensi_pelatih_sma->pdf('ekskul_presensi_pelatih_sma', 'ekskul_presensi_pelatih_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ekskul_presensi_pelatih_sma_export');

		$table = $title = 'ekskul_presensi_pelatih_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ekskul_presensi_pelatih_sma->find($id);
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


/* End of file ekskul_presensi_pelatih_sma.php */
/* Location: ./application/controllers/administrator/Ekskul Presensi Pelatih Sma.php */