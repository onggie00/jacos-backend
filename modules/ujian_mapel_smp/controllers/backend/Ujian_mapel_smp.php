<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Mapel Smp Controller
*| --------------------------------------------------------------------------
*| Ujian Mapel Smp site
*|
*/
class Ujian_mapel_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_mapel_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Mapel Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_mapel_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_mapel_smps'] = $this->model_ujian_mapel_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_mapel_smp_counts'] = $this->model_ujian_mapel_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_mapel_smp/index/',
			'total_rows'   => $this->model_ujian_mapel_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ujian Mapel SMP List');
		$this->render('backend/standart/administrator/ujian_mapel_smp/ujian_mapel_smp_list', $this->data);
	}
	
	/**
	* Add new ujian_mapel_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_mapel_smp_add');

		$this->template->title('Ujian Mapel SMP New');
		$this->render('backend/standart/administrator/ujian_mapel_smp/ujian_mapel_smp_add', $this->data);
	}

	/**
	* Add New Ujian Mapel Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_mapel_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_mapel', 'Mata Pelajaran', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kode_mapel_ujian', 'Kode Mapel Ujian', 'trim|required|max_length[10]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_mapel' => $this->input->post('nama_mapel'),
				'kode_mapel_ujian' => $this->input->post('kode_mapel_ujian'),
			];

			
			$save_ujian_mapel_smp = $this->model_ujian_mapel_smp->store($save_data);
            

			if ($save_ujian_mapel_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_mapel_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_mapel_smp/edit/' . $save_ujian_mapel_smp, 'Edit Ujian Mapel Smp'),
						anchor('administrator/ujian_mapel_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_mapel_smp/edit/' . $save_ujian_mapel_smp, 'Edit Ujian Mapel Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_mapel_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_mapel_smp');
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
	* Update view Ujian Mapel Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_mapel_smp_update');

		$this->data['ujian_mapel_smp'] = $this->model_ujian_mapel_smp->find($id);

		$this->template->title('Ujian Mapel SMP Update');
		$this->render('backend/standart/administrator/ujian_mapel_smp/ujian_mapel_smp_update', $this->data);
	}

	/**
	* Update Ujian Mapel Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_mapel_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_mapel', 'Mata Pelajaran', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kode_mapel_ujian', 'Kode Mapel Ujian', 'trim|required|max_length[10]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_mapel' => $this->input->post('nama_mapel'),
				'kode_mapel_ujian' => $this->input->post('kode_mapel_ujian'),
			];

			
			$save_ujian_mapel_smp = $this->model_ujian_mapel_smp->change($id, $save_data);

			if ($save_ujian_mapel_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_mapel_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_mapel_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_mapel_smp');
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
	* delete Ujian Mapel Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_mapel_smp_delete');

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
            set_message(cclang('has_been_deleted', 'ujian_mapel_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_mapel_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Mapel Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_mapel_smp_view');

		$this->data['ujian_mapel_smp'] = $this->model_ujian_mapel_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ujian Mapel SMP Detail');
		$this->render('backend/standart/administrator/ujian_mapel_smp/ujian_mapel_smp_view', $this->data);
	}
	
	/**
	* delete Ujian Mapel Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_mapel_smp = $this->model_ujian_mapel_smp->find($id);

		
		
		return $this->model_ujian_mapel_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_mapel_smp_export');

		$this->model_ujian_mapel_smp->export('ujian_mapel_smp', 'ujian_mapel_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_mapel_smp_export');

		$this->model_ujian_mapel_smp->pdf('ujian_mapel_smp', 'ujian_mapel_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_mapel_smp_export');

		$table = $title = 'ujian_mapel_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_mapel_smp->find($id);
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

	public function import_mapel()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p=$_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' || $ext!='xls'){
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				//redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				//$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					$nama_mapel = strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue());
					$kode_mapel = strtoupper($worksheet->getCellByColumnAndRow(2, $row)->getValue());
					$data_mapel = array(
						"nama_mapel" => $nama_mapel,
						"kode_mapel_ujian" => $kode_mapel,
					);
					//check null
					foreach ($data_mapel as $key => $item) {
						if(empty($item)){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong");
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					$cek_mapel = $this->mymodel->withquery("select * from ujian_mapel_smp where nama_mapel = '".$nama_mapel."' or kode_mapel_ujian = '".$kode_mapel."'","row");
					if (!empty($cek_mapel)) {
						$update_data = $this->mymodel->update("ujian_mapel_smp", $data_mapel, "id_mapel", $cek_mapel->id_mapel);
						if (!$update_data) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', 'Import data gagal ' . $data_mapel["nama_mapel"]);
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					else{
						$save_data = $this->mymodel->insertid("ujian_mapel_smp", $data_mapel);
						if (!empty($save_data)) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', 'Import data gagal ' . $data_mapel["nama_mapel"]);
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
				}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data mapel ujian berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	
}


/* End of file ujian_mapel_smp.php */
/* Location: ./application/controllers/administrator/Ujian Mapel Smp.php */