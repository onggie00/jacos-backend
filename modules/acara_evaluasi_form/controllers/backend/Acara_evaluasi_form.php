<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Acara Evaluasi Form Controller
*| --------------------------------------------------------------------------
*| Acara Evaluasi Form site
*|
*/
class Acara_evaluasi_form extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_acara_evaluasi_form');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Acara Evaluasi Forms
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('acara_evaluasi_form_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['acara_evaluasi_forms'] = $this->model_acara_evaluasi_form->get($filter, $field, $this->limit_page, $offset);
		$this->data['acara_evaluasi_form_counts'] = $this->model_acara_evaluasi_form->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/acara_evaluasi_form/index/',
			'total_rows'   => $this->model_acara_evaluasi_form->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Form Evaluasi Acara List');
		$this->render('backend/standart/administrator/acara_evaluasi_form/acara_evaluasi_form_list', $this->data);
	}
	
	/**
	* Add new acara_evaluasi_forms
	*
	*/
	public function add()
	{
		$this->is_allowed('acara_evaluasi_form_add');

		$this->template->title('Form Evaluasi Acara New');
		$this->render('backend/standart/administrator/acara_evaluasi_form/acara_evaluasi_form_add', $this->data);
	}

	/**
	* Add New Acara Evaluasi Forms
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('acara_evaluasi_form_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_acara', 'Acara', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('pertanyaan', 'Pertanyaan', 'trim|required');
		$this->form_validation->set_rules('tipe_pertanyaan', 'Tipe Pertanyaan', 'trim|required');
		$this->form_validation->set_rules('no_urut', 'Nomor Pertanyaan', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_acara' => $this->input->post('id_acara'),
				'pertanyaan' => $this->input->post('pertanyaan'),
				'tipe_pertanyaan' => $this->input->post('tipe_pertanyaan'),
				'is_required' => $this->input->post('is_required'),
				'jawaban_pertanyaan' => $this->input->post('jawaban_pertanyaan'),
				'no_urut' => $this->input->post('no_urut'),
			];

			
			$save_acara_evaluasi_form = $this->model_acara_evaluasi_form->store($save_data);
            

			if ($save_acara_evaluasi_form) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_acara_evaluasi_form;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/acara_evaluasi_form/edit/' . $save_acara_evaluasi_form, 'Edit Acara Evaluasi Form'),
						anchor('administrator/acara_evaluasi_form', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/acara_evaluasi_form/edit/' . $save_acara_evaluasi_form, 'Edit Acara Evaluasi Form')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara_evaluasi_form');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara_evaluasi_form');
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
	* Get acara list for dropdown (AJAX JSON)
	*/
	public function get_acara_list()
	{
		$acaras = $this->db->select('id_acara, nama_acara')
			->where('deleted_at IS NULL', null, false)
			->order_by('nama_acara', 'ASC')
			->get('acara')
			->result();

		echo json_encode($acaras);
	}

	/**
	* Add multiple evaluasi forms at once (AJAX JSON)
	*/
	public function add_multiple_save()
	{
		if (!$this->is_allowed('acara_evaluasi_form_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$id_acara = $this->input->post('id_acara');
		$pertanyaan = $this->input->post('pertanyaan');
		$tipe_pertanyaan = $this->input->post('tipe_pertanyaan');
		$is_required = $this->input->post('is_required');
		$jawaban_pertanyaan = $this->input->post('jawaban_pertanyaan');
		$no_urut = $this->input->post('no_urut');

		if (empty($id_acara)) {
			echo json_encode(['success' => false, 'message' => 'Acara wajib dipilih.']);
			exit;
		}

		if (empty($pertanyaan) || !is_array($pertanyaan)) {
			echo json_encode(['success' => false, 'message' => 'Minimal 1 pertanyaan harus diisi.']);
			exit;
		}

		$this->db->trans_start();
		$saved = 0;

		foreach ($pertanyaan as $i => $p) {
			if (empty(trim($p))) continue;

			$save_data = [
				'id_acara' => $id_acara,
				'pertanyaan' => trim($p),
				'tipe_pertanyaan' => !empty($tipe_pertanyaan[$i]) ? $tipe_pertanyaan[$i] : 'text',
				'is_required' => !empty($is_required[$i]) ? $is_required[$i] : 0,
				'jawaban_pertanyaan' => !empty($jawaban_pertanyaan[$i]) ? trim($jawaban_pertanyaan[$i]) : '',
				'no_urut' => !empty($no_urut[$i]) ? intval($no_urut[$i]) : ($i + 1),
			];

			$this->model_acara_evaluasi_form->store($save_data);
			$saved++;
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data.']);
		} else {
			echo json_encode(['success' => true, 'message' => $saved . ' pertanyaan berhasil disimpan.']);
		}
	}

		/**
	* Update view Acara Evaluasi Forms
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('acara_evaluasi_form_update');

		$this->data['acara_evaluasi_form'] = $this->model_acara_evaluasi_form->find($id);

		$this->template->title('Form Evaluasi Acara Update');
		$this->render('backend/standart/administrator/acara_evaluasi_form/acara_evaluasi_form_update', $this->data);
	}

	/**
	* Update Acara Evaluasi Forms
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('acara_evaluasi_form_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_acara', 'Acara', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('pertanyaan', 'Pertanyaan', 'trim|required');
		$this->form_validation->set_rules('tipe_pertanyaan', 'Tipe Pertanyaan', 'trim|required');
		$this->form_validation->set_rules('no_urut', 'Nomor Pertanyaan', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_acara' => $this->input->post('id_acara'),
				'pertanyaan' => $this->input->post('pertanyaan'),
				'tipe_pertanyaan' => $this->input->post('tipe_pertanyaan'),
				'is_required' => $this->input->post('is_required'),
				'jawaban_pertanyaan' => $this->input->post('jawaban_pertanyaan'),
				'no_urut' => $this->input->post('no_urut'),
			];

			
			$save_acara_evaluasi_form = $this->model_acara_evaluasi_form->change($id, $save_data);

			if ($save_acara_evaluasi_form) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/acara_evaluasi_form', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara_evaluasi_form');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara_evaluasi_form');
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
	* delete Acara Evaluasi Forms
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('acara_evaluasi_form_delete');

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
            set_message(cclang('has_been_deleted', 'acara_evaluasi_form'), 'success');
        } else {
            set_message(cclang('error_delete', 'acara_evaluasi_form'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Acara Evaluasi Forms
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('acara_evaluasi_form_view');

		$this->data['acara_evaluasi_form'] = $this->model_acara_evaluasi_form->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Form Evaluasi Acara Detail');
		$this->render('backend/standart/administrator/acara_evaluasi_form/acara_evaluasi_form_view', $this->data);
	}
	
	/**
	* delete Acara Evaluasi Forms
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$acara_evaluasi_form = $this->model_acara_evaluasi_form->find($id);

		
		
		return $this->model_acara_evaluasi_form->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('acara_evaluasi_form_export');

		$this->model_acara_evaluasi_form->export('acara_evaluasi_form', 'acara_evaluasi_form');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('acara_evaluasi_form_export');

		$this->model_acara_evaluasi_form->pdf('acara_evaluasi_form', 'acara_evaluasi_form');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('acara_evaluasi_form_export');

		$table = $title = 'acara_evaluasi_form';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_acara_evaluasi_form->find($id);
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


/* End of file acara_evaluasi_form.php */
/* Location: ./application/controllers/administrator/Acara Evaluasi Form.php */