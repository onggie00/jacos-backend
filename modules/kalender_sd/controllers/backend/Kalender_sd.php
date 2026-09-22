<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kalender Sd Controller
*| --------------------------------------------------------------------------
*| Kalender Sd site
*|
*/
class Kalender_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kalender_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kalender Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kalender_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kalender_sds'] = $this->model_kalender_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['kalender_sd_counts'] = $this->model_kalender_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kalender_sd/index/',
			'total_rows'   => $this->model_kalender_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kalender Sd List');
		$this->render('backend/standart/administrator/kalender_sd/kalender_sd_list', $this->data);
	}
	
	/**
	* Add new kalender_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('kalender_sd_add');

		$this->template->title('Kalender Sd New');
		$this->render('backend/standart/administrator/kalender_sd/kalender_sd_add', $this->data);
	}

	/**
	* Add New Kalender Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kalender_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_tipe', 'Id Tipe', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		
		
		if ($this->form_validation->run()) {
			$save_data = [
				'id_tipe' => $this->input->post('id_tipe'),
				'label' => $this->input->post('label'),
				'date' => $this->input->post('date'),
				'bulan' => get_bulan(date('m',strtotime($this->input->post('date')))),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
			];

			
			$save_kalender_sd = $this->model_kalender_sd->store($save_data);
            

			if ($save_kalender_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kalender_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kalender_sd/edit/' . $save_kalender_sd, 'Edit Kalender Sd'),
						anchor('administrator/kalender_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kalender_sd/edit/' . $save_kalender_sd, 'Edit Kalender Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kalender_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kalender_sd');
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
	* Update view Kalender Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kalender_sd_update');

		$this->data['kalender_sd'] = $this->model_kalender_sd->find($id);

		$this->template->title('Kalender Sd Update');
		$this->render('backend/standart/administrator/kalender_sd/kalender_sd_update', $this->data);
	}

	/**
	* Update Kalender Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kalender_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_tipe', 'Id Tipe', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_tipe' => $this->input->post('id_tipe'),
				'label' => $this->input->post('label'),
				'date' => $this->input->post('date'),
				'bulan' => get_bulan(date('m',strtotime($this->input->post('date')))),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
			];

			
			$save_kalender_sd = $this->model_kalender_sd->change($id, $save_data);

			if ($save_kalender_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kalender_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kalender_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kalender_sd');
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
	* delete Kalender Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kalender_sd_delete');

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
            set_message(cclang('has_been_deleted', 'kalender_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'kalender_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kalender Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kalender_sd_view');

		$this->data['kalender_sd'] = $this->model_kalender_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kalender Sd Detail');
		$this->render('backend/standart/administrator/kalender_sd/kalender_sd_view', $this->data);
	}
	
	/**
	* delete Kalender Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kalender_sd = $this->model_kalender_sd->find($id);

		
		
		return $this->model_kalender_sd->remove($id);
	}
	
	public function import()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_akademik"]["name"])) {
			$path = $_FILES["file_akademik"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				// $totalAll=PHPExcel_Cell::columnIndexFromString($highestColumn);
				// dd($highestColumn);
				// if($totalAll!=3){
				// 	$this->db->trans_rollback();
				// 	$this->load->library("session");
				// 	$this->session->set_flashdata('error', 'Jumlah kolom tidak sesuai');
				// 	redirect($_SERVER['HTTP_REFERER']);
				// }

				for ($row = 2; $row <= $highestRow; $row++) {

					$data_kalender = array(
						"id_tipe" => $worksheet->getCellByColumnAndRow(0, $row)->getValue(),
						"label" => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
						"date" => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
						"id_tahun_ajaran" => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
						"bulan" => get_bulan(date('m',strtotime($worksheet->getCellByColumnAndRow(2, $row)->getValue())))
					);
					$insertId = $this->mymodel->insertid("kalender_sd", $data_kalender);
					// dd($insertId);
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', 'Import kalender gagal label '.$data_kalender["label"]);
						redirect($_SERVER['HTTP_REFERER']);
					}

				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import kalender berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kalender_sd_export');

		$this->model_kalender_sd->export('kalender_sd', 'kalender_sd');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kalender_sd_export');

		$this->model_kalender_sd->pdf('kalender_sd', 'kalender_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kalender_sd_export');

		$table = $title = 'kalender_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kalender_sd->find($id);
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


/* End of file kalender_sd.php */
/* Location: ./application/controllers/administrator/Kalender Sd.php */