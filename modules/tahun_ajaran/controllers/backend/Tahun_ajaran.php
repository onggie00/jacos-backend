<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tahun Ajaran Controller
*| --------------------------------------------------------------------------
*| Tahun Ajaran site
*|
*/
class Tahun_ajaran extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tahun_ajaran');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tahun Ajarans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tahun_ajaran_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tahun_ajarans'] = $this->model_tahun_ajaran->get($filter, $field, $this->limit_page, $offset);
		$this->data['tahun_ajaran_counts'] = $this->model_tahun_ajaran->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tahun_ajaran/index/',
			'total_rows'   => $this->model_tahun_ajaran->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tahun Ajaran List');
		$this->render('backend/standart/administrator/tahun_ajaran/tahun_ajaran_list', $this->data);
	}
	
	/**
	* Add new tahun_ajarans
	*
	*/
	public function add()
	{
		$this->is_allowed('tahun_ajaran_add');

		$this->template->title('Tahun Ajaran New');
		$this->render('backend/standart/administrator/tahun_ajaran/tahun_ajaran_add', $this->data);
	}

	/**
	* Add New Tahun Ajarans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tahun_ajaran_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('sequence', 'Sequence', 'trim|required');
		

		if ($this->form_validation->run()) {
			$year1 = date("y",strtotime($this->input->post('tanggal_mulai')));
			$year2 = date("y",strtotime($this->input->post('tanggal_selesai')));
			$code = $year1."_".$year2;
			$save_data = [
				'label' => $this->input->post('label'),
				'tanggal_mulai' => $this->input->post('tanggal_mulai'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
				'sequence' => $this->input->post('sequence'),
				'code' => $code,
			];

			
			$save_tahun_ajaran = $this->model_tahun_ajaran->store($save_data);
            

			if ($save_tahun_ajaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tahun_ajaran;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tahun_ajaran/edit/' . $save_tahun_ajaran, 'Edit Tahun Ajaran'),
						anchor('administrator/tahun_ajaran', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tahun_ajaran/edit/' . $save_tahun_ajaran, 'Edit Tahun Ajaran')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tahun_ajaran');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tahun_ajaran');
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
	* Update view Tahun Ajarans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tahun_ajaran_update');

		$this->data['tahun_ajaran'] = $this->model_tahun_ajaran->find($id);

		$this->template->title('Tahun Ajaran Update');
		$this->render('backend/standart/administrator/tahun_ajaran/tahun_ajaran_update', $this->data);
	}

	/**
	* Update Tahun Ajarans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tahun_ajaran_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('label', 'Label', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('sequence', 'Sequence', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$year1 = date("y",strtotime($this->input->post('tanggal_mulai')));
			$year2 = date("y",strtotime($this->input->post('tanggal_selesai')));
			$code = $year1."_".$year2;
			$save_data = [
				'label' => $this->input->post('label'),
				'tanggal_mulai' => $this->input->post('tanggal_mulai'),
				'tanggal_selesai' => $this->input->post('tanggal_selesai'),
				'sequence' => $this->input->post('sequence'),
				'code' => $code,
			];

			
			$save_tahun_ajaran = $this->model_tahun_ajaran->change($id, $save_data);

			if ($save_tahun_ajaran) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tahun_ajaran', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tahun_ajaran');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tahun_ajaran');
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
	* delete Tahun Ajarans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tahun_ajaran_delete');

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
            set_message(cclang('has_been_deleted', 'tahun_ajaran'), 'success');
        } else {
            set_message(cclang('error_delete', 'tahun_ajaran'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tahun Ajarans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tahun_ajaran_view');

		$this->data['tahun_ajaran'] = $this->model_tahun_ajaran->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tahun Ajaran Detail');
		$this->render('backend/standart/administrator/tahun_ajaran/tahun_ajaran_view', $this->data);
	}
	
	/**
	* delete Tahun Ajarans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tahun_ajaran = $this->model_tahun_ajaran->find($id);

		
		
		return $this->model_tahun_ajaran->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tahun_ajaran_export');

		$this->model_tahun_ajaran->export('tahun_ajaran', 'tahun_ajaran');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tahun_ajaran_export');

		$this->model_tahun_ajaran->pdf('tahun_ajaran', 'tahun_ajaran');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tahun_ajaran_export');

		$table = $title = 'tahun_ajaran';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tahun_ajaran->find($id);
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


/* End of file tahun_ajaran.php */
/* Location: ./application/controllers/administrator/Tahun Ajaran.php */