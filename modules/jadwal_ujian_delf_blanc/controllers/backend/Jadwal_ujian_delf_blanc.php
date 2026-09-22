<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jadwal Ujian Delf Blanc Controller
*| --------------------------------------------------------------------------
*| Jadwal Ujian Delf Blanc site
*|
*/
class Jadwal_ujian_delf_blanc extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jadwal_ujian_delf_blanc');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jadwal Ujian Delf Blancs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jadwal_ujian_delf_blancs'] = $this->model_jadwal_ujian_delf_blanc->get($filter, $field, $this->limit_page, $offset);
		$this->data['jadwal_ujian_delf_blanc_counts'] = $this->model_jadwal_ujian_delf_blanc->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jadwal_ujian_delf_blanc/index/',
			'total_rows'   => $this->model_jadwal_ujian_delf_blanc->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jadwal Ujian Delf Blanc List');
		$this->render('backend/standart/administrator/jadwal_ujian_delf_blanc/jadwal_ujian_delf_blanc_list', $this->data);
	}
	
	/**
	* Add new jadwal_ujian_delf_blancs
	*
	*/
	public function add()
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_add');

		$this->template->title('Jadwal Ujian Delf Blanc New');
		$this->render('backend/standart/administrator/jadwal_ujian_delf_blanc/jadwal_ujian_delf_blanc_add', $this->data);
	}

	/**
	* Add New Jadwal Ujian Delf Blancs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jadwal_ujian_delf_blanc_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_jenis_ujian', 'Id Jenis Ujian', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('ruang', 'Ruang', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
		$this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required|max_length[20]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_mapel' => $this->input->post('id_mapel'),
				'id_jenis_ujian' => $this->input->post('id_jenis_ujian'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'tanggal' => $this->input->post('tanggal'),
				'ruang' => $this->input->post('ruang'),
				'hari' => $this->input->post('hari'),
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			
			$save_jadwal_ujian_delf_blanc = $this->model_jadwal_ujian_delf_blanc->store($save_data);
            

			if ($save_jadwal_ujian_delf_blanc) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jadwal_ujian_delf_blanc;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jadwal_ujian_delf_blanc/edit/' . $save_jadwal_ujian_delf_blanc, 'Edit Jadwal Ujian Delf Blanc'),
						anchor('administrator/jadwal_ujian_delf_blanc', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jadwal_ujian_delf_blanc/edit/' . $save_jadwal_ujian_delf_blanc, 'Edit Jadwal Ujian Delf Blanc')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_delf_blanc');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_delf_blanc');
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
	* Update view Jadwal Ujian Delf Blancs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_update');

		$this->data['jadwal_ujian_delf_blanc'] = $this->model_jadwal_ujian_delf_blanc->find($id);

		$this->template->title('Jadwal Ujian Delf Blanc Update');
		$this->render('backend/standart/administrator/jadwal_ujian_delf_blanc/jadwal_ujian_delf_blanc_update', $this->data);
	}

	/**
	* Update Jadwal Ujian Delf Blancs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jadwal_ujian_delf_blanc_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_jenis_ujian', 'Id Jenis Ujian', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_tingkatan', 'Id Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('ruang', 'Ruang', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
		$this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required|max_length[20]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_mapel' => $this->input->post('id_mapel'),
				'id_jenis_ujian' => $this->input->post('id_jenis_ujian'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'tanggal' => $this->input->post('tanggal'),
				'ruang' => $this->input->post('ruang'),
				'hari' => $this->input->post('hari'),
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			
			$save_jadwal_ujian_delf_blanc = $this->model_jadwal_ujian_delf_blanc->change($id, $save_data);

			if ($save_jadwal_ujian_delf_blanc) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jadwal_ujian_delf_blanc', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_delf_blanc');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_delf_blanc');
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
	* delete Jadwal Ujian Delf Blancs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_delete');

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
            set_message(cclang('has_been_deleted', 'jadwal_ujian_delf_blanc'), 'success');
        } else {
            set_message(cclang('error_delete', 'jadwal_ujian_delf_blanc'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jadwal Ujian Delf Blancs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_view');

		$this->data['jadwal_ujian_delf_blanc'] = $this->model_jadwal_ujian_delf_blanc->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jadwal Ujian Delf Blanc Detail');
		$this->render('backend/standart/administrator/jadwal_ujian_delf_blanc/jadwal_ujian_delf_blanc_view', $this->data);
	}
	
	/**
	* delete Jadwal Ujian Delf Blancs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jadwal_ujian_delf_blanc = $this->model_jadwal_ujian_delf_blanc->find($id);

		
		
		return $this->model_jadwal_ujian_delf_blanc->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_export');

		$this->model_jadwal_ujian_delf_blanc->export('jadwal_ujian_delf_blanc', 'jadwal_ujian_delf_blanc');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_export');

		$this->model_jadwal_ujian_delf_blanc->pdf('jadwal_ujian_delf_blanc', 'jadwal_ujian_delf_blanc');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jadwal_ujian_delf_blanc_export');

		$table = $title = 'jadwal_ujian_delf_blanc';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jadwal_ujian_delf_blanc->find($id);
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


/* End of file jadwal_ujian_delf_blanc.php */
/* Location: ./application/controllers/administrator/Jadwal Ujian Delf Blanc.php */