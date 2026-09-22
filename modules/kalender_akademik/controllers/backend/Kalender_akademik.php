<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kalender Akademik Controller
*| --------------------------------------------------------------------------
*| Kalender Akademik site
*|
*/
class Kalender_akademik extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kalender_akademik');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kalender Akademiks
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kalender_akademik_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kalender_akademiks'] = $this->model_kalender_akademik->get($filter, $field, $this->limit_page, $offset);
		$this->data['kalender_akademik_counts'] = $this->model_kalender_akademik->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kalender_akademik/index/',
			'total_rows'   => $this->model_kalender_akademik->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kalender Akademik List');
		$this->render('backend/standart/administrator/kalender_akademik/kalender_akademik_list', $this->data);
	}
	
	/**
	* Add new kalender_akademiks
	*
	*/
	public function add()
	{
		$this->is_allowed('kalender_akademik_add');

		$this->template->title('Kalender Akademik New');
		$this->render('backend/standart/administrator/kalender_akademik/kalender_akademik_add', $this->data);
	}

	/**
	* Add New Kalender Akademiks
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kalender_akademik_add', false)) {
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
		
			if (empty($this->input->post('date_to'))) {
				$save_data = [
					'id_tipe' => $this->input->post('id_tipe'),
					'label' => $this->input->post('label'),
					'date' => $this->input->post('date'),
					'bulan' => get_bulan(date('m',strtotime($this->input->post('date')))),
					'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				];
	
				$save_kalender_akademik = $this->model_kalender_akademik->store($save_data);
			}
			else if(!empty($this->input->post('date_to'))){
				//input lebih dari 1 tanggal sesuai tanggal date to
				$from = $this->input->post('date');
				$to = $this->input->post('date_to');
				$day_diff = "";
				$day_diff = (strtotime($to) - strtotime($from));
				$day_diff = round($day_diff / 86400);
				for ($i=0; $i <= $day_diff; $i++) {
					$selected_date = date('Y-m-d', strtotime($from) + ($i * 86400));
					$save_data = [
						'id_tipe' => $this->input->post('id_tipe'),
						'label' => $this->input->post('label'),
						'date' => $selected_date,
						'bulan' => get_bulan(date('m',strtotime($selected_date))),
						'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
					];
					$save_kalender_akademik = $this->model_kalender_akademik->store($save_data);
				}
			}
			else{
				$save_kalender_akademik = false;
			}
            

			if ($save_kalender_akademik) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kalender_akademik;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kalender_akademik/edit/' . $save_kalender_akademik, 'Edit Kalender Akademik'),
						anchor('administrator/kalender_akademik', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kalender_akademik/edit/' . $save_kalender_akademik, 'Edit Kalender Akademik')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kalender_akademik');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kalender_akademik');
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
	* Update view Kalender Akademiks
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kalender_akademik_update');

		$this->data['kalender_akademik'] = $this->model_kalender_akademik->find($id);

		$this->template->title('Kalender Akademik Update');
		$this->render('backend/standart/administrator/kalender_akademik/kalender_akademik_update', $this->data);
	}

	/**
	* Update Kalender Akademiks
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kalender_akademik_update', false)) {
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

			
			$save_kalender_akademik = $this->model_kalender_akademik->change($id, $save_data);

			if ($save_kalender_akademik) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kalender_akademik', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kalender_akademik');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kalender_akademik');
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
	* delete Kalender Akademiks
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kalender_akademik_delete');

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
            set_message(cclang('has_been_deleted', 'kalender_akademik'), 'success');
        } else {
            set_message(cclang('error_delete', 'kalender_akademik'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kalender Akademiks
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kalender_akademik_view');

		$this->data['kalender_akademik'] = $this->model_kalender_akademik->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kalender Akademik Detail');
		$this->render('backend/standart/administrator/kalender_akademik/kalender_akademik_view', $this->data);
	}
	
	/**
	* delete Kalender Akademiks
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kalender_akademik = $this->model_kalender_akademik->find($id);

		
		
		return $this->model_kalender_akademik->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kalender_akademik_export');

		$this->model_kalender_akademik->export('kalender_akademik', 'kalender_akademik');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kalender_akademik_export');

		$this->model_kalender_akademik->pdf('kalender_akademik', 'kalender_akademik');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kalender_akademik_export');

		$table = $title = 'kalender_akademik';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kalender_akademik->find($id);
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


/* End of file kalender_akademik.php */
/* Location: ./application/controllers/administrator/Kalender Akademik.php */