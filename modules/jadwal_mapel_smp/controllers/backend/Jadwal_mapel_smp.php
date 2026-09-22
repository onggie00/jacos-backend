<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jadwal Mapel Smp Controller
*| --------------------------------------------------------------------------
*| Jadwal Mapel Smp site
*|
*/
class Jadwal_mapel_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jadwal_mapel_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jadwal Mapel Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jadwal_mapel_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jadwal_mapel_smps'] = $this->model_jadwal_mapel_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['jadwal_mapel_smp_counts'] = $this->model_jadwal_mapel_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jadwal_mapel_smp/index/',
			'total_rows'   => $this->model_jadwal_mapel_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jadwal Mapel Smp List');
		$this->render('backend/standart/administrator/jadwal_mapel_smp/jadwal_mapel_smp_list', $this->data);
	}
	
	/**
	* Add new jadwal_mapel_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('jadwal_mapel_smp_add');

		$this->template->title('Jadwal Mapel Smp New');
		$this->render('backend/standart/administrator/jadwal_mapel_smp/jadwal_mapel_smp_add', $this->data);
	}

	/**
	* Add New Jadwal Mapel Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jadwal_mapel_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
		$this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required');
		$this->form_validation->set_rules('id_kelas', 'Id Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_guru', 'Id Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ruang_kelas', 'Ruang Kelas', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'hari' => $this->input->post('hari'),
				'id_kelas' => $this->input->post('id_kelas'),
				'id_mapel' => $this->input->post('id_mapel'),
				'id_guru' => $this->input->post('id_guru'),
				'ruang_kelas' => $this->input->post('ruang_kelas'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
			];

			
			$save_jadwal_mapel_smp = $this->model_jadwal_mapel_smp->store($save_data);
            

			if ($save_jadwal_mapel_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jadwal_mapel_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jadwal_mapel_smp/edit/' . $save_jadwal_mapel_smp, 'Edit Jadwal Mapel Smp'),
						anchor('administrator/jadwal_mapel_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jadwal_mapel_smp/edit/' . $save_jadwal_mapel_smp, 'Edit Jadwal Mapel Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jadwal_mapel_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jadwal_mapel_smp');
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
	* Update view Jadwal Mapel Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jadwal_mapel_smp_update');

		$this->data['jadwal_mapel_smp'] = $this->model_jadwal_mapel_smp->find($id);

		$this->template->title('Jadwal Mapel Smp Update');
		$this->render('backend/standart/administrator/jadwal_mapel_smp/jadwal_mapel_smp_update', $this->data);
	}

	/**
	* Update Jadwal Mapel Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jadwal_mapel_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
		$this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required');
		$this->form_validation->set_rules('id_kelas', 'Id Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_guru', 'Id Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('ruang_kelas', 'Ruang Kelas', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'hari' => $this->input->post('hari'),
				'id_kelas' => $this->input->post('id_kelas'),
				'id_mapel' => $this->input->post('id_mapel'),
				'id_guru' => $this->input->post('id_guru'),
				'ruang_kelas' => $this->input->post('ruang_kelas'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
			];

			
			$save_jadwal_mapel_smp = $this->model_jadwal_mapel_smp->change($id, $save_data);

			if ($save_jadwal_mapel_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jadwal_mapel_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jadwal_mapel_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jadwal_mapel_smp');
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
	* delete Jadwal Mapel Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jadwal_mapel_smp_delete');

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
            set_message(cclang('has_been_deleted', 'jadwal_mapel_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'jadwal_mapel_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jadwal Mapel Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jadwal_mapel_smp_view');

		$this->data['jadwal_mapel_smp'] = $this->model_jadwal_mapel_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jadwal Mapel Smp Detail');
		$this->render('backend/standart/administrator/jadwal_mapel_smp/jadwal_mapel_smp_view', $this->data);
	}
	
	/**
	* delete Jadwal Mapel Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jadwal_mapel_smp = $this->model_jadwal_mapel_smp->find($id);

		
		
		return $this->model_jadwal_mapel_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jadwal_mapel_smp_export');

		$this->model_jadwal_mapel_smp->export('jadwal_mapel_smp', 'jadwal_mapel_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jadwal_mapel_smp_export');

		$this->model_jadwal_mapel_smp->pdf('jadwal_mapel_smp', 'jadwal_mapel_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jadwal_mapel_smp_export');

		$table = $title = 'jadwal_mapel_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jadwal_mapel_smp->find($id);
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

	public function import()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_mapel"]["name"])) {
			$path = $_FILES["file_mapel"]["tmp_name"];
			$p=$_FILES["file_mapel"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->load->library("session");
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					//check jadwal bertabrakan
					$hari=$worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$jam_mulai=$worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$jam_selesai=$worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$query_time="where (jam_mulai BETWEEN '$jam_mulai' AND '$jam_selesai' AND hari = '$hari') OR 
										(jam_selesai BETWEEN '$jam_mulai' AND '$jam_selesai' AND hari = '$hari') OR 
										(jam_mulai <= '$jam_mulai' AND jam_selesai >= '$jam_selesai' AND hari = '$hari')";

					//$jadwal_exist=$this->mymodel->withquery("select * from jadwal_mapel_smp $query_time", 'row');
					//if($jadwal_exist){
					//	$this->db->trans_rollback();
					//	$this->load->library("session");
					//	$this->session->set_flashdata('error', "Jadwal mapel hari $hari $jam_mulai - $jam_selesai sudah ada");
					//	redirect($_SERVER['HTTP_REFERER']);
					//}
					$kode_mapel=$worksheet->getCellByColumnAndRow(0, $row)->getValue();
					$id_mapel=$this->mymodel->withquery("select * from mata_pelajaran_smp where kode_mapel = '$kode_mapel'", 'row')->id_mapel;
					if(!$id_mapel){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Mapel dengan kode $kode_mapel tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					$label_kelas=$worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$id_kelas=$this->mymodel->withquery("select * from kelas_smp where label = '$label_kelas'", 'row')->id_kelas_smp;
					if(!$id_kelas){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Kelas dengan label $label_kelas tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					$guru_smp=$worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$id_guru=$this->mymodel->withquery("select * from guru_smp where emp_code = '$guru_smp'", 'row')->id_guru;
					if(!$id_guru){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Guru dengan emp_code $guru_smp tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					$tahun_ajaran=$worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$id_tahun_ajaran=$this->mymodel->withquery("select * from tahun_ajaran where label = '$tahun_ajaran'", 'row')->id_tahun_ajaran;
					if(!$id_tahun_ajaran){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Tahun ajaran dengan label $tahun_ajaran tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					$data_mapel = array(
						"id_mapel" => $id_mapel,
						"id_kelas" => $id_kelas,
						"id_guru" => $id_guru,
						"hari" => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
						"jam_mulai" => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
						"jam_selesai" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
						"ruang_kelas" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
						"id_tahun_ajaran" => $id_tahun_ajaran,
					);
									//check null
				foreach ($data_mapel as $key => $item) {
					if(!$item){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Data ada yang kosong");
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
					$insertId = $this->mymodel->insertid("jadwal_mapel_smp", $data_mapel);
					// dd($insertId);
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Import mapel gagal ");
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import mapel berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
}


/* End of file jadwal_mapel_smp.php */
/* Location: ./application/controllers/administrator/Jadwal Mapel Smp.php */