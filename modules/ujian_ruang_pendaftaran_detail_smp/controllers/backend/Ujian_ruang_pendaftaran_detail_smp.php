<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Detail Smp Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Detail Smp site
*|
*/
class Ujian_ruang_pendaftaran_detail_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_pendaftaran_detail_smp');
		$this->lang->load('web_lang', $this->current_lang);
		$this->limit_page = 20;
	}

	/**
	* show all Ujian Ruang Pendaftaran Detail Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_pendaftaran_detail_smps'] = $this->model_ujian_ruang_pendaftaran_detail_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_pendaftaran_detail_smp_counts'] = $this->model_ujian_ruang_pendaftaran_detail_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang_pendaftaran_detail_smp/index/',
			'total_rows'   => $this->model_ujian_ruang_pendaftaran_detail_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Detail Peserta Ujian PSB SMP List');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_smp/ujian_ruang_pendaftaran_detail_smp_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_pendaftaran_detail_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_add');

		$this->template->title('Detail Peserta Ujian PSB SMP New');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_smp/ujian_ruang_pendaftaran_detail_smp_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Pendaftaran Detail Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_ruang_pendaftaran', 'Ruang', 'trim|required|max_length[11]');
		//$this->form_validation->set_rules('nomor_peserta', 'Nomor Peserta', 'trim|required|max_length[30]');
		

		if ($this->form_validation->run()) {
		
			$nomor_awal = (int)$this->input->post('nomor_awal');
			$nomor_akhir = (int)$this->input->post('nomor_akhir');
			$total_peserta = (int)$nomor_akhir - (int)$nomor_awal;
			for($i=0; $i <= $total_peserta; $i++) {
				$save_data = [
					'id_ruang_pendaftaran' => $this->input->post('id_ruang_pendaftaran'),
					'nomor_peserta' => ($nomor_awal+$i),//$this->input->post('nomor_peserta')[$i],
				];
				//cek nomor peserta
				$cek_no_peserta = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".($nomor_awal+$i)."'","row");
				if(!empty($cek_no_peserta)){
					$save_ujian_ruang_pendaftaran_detail_smp = $this->model_ujian_ruang_pendaftaran_detail_smp->store($save_data);
				}
				else{
					continue;
				}
			}
            

			if ($save_ujian_ruang_pendaftaran_detail_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_pendaftaran_detail_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_smp/edit/' . $save_ujian_ruang_pendaftaran_detail_smp, 'Edit Ujian Ruang Pendaftaran Detail Smp'),
						anchor('administrator/ujian_ruang_pendaftaran_detail_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_smp/edit/' . $save_ujian_ruang_pendaftaran_detail_smp, 'Edit Ujian Ruang Pendaftaran Detail Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_smp');
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
	* Update view Ujian Ruang Pendaftaran Detail Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_update');

		$this->data['ujian_ruang_pendaftaran_detail_smp'] = $this->model_ujian_ruang_pendaftaran_detail_smp->find($id);

		$this->template->title('Detail Peserta Ujian PSB SMP Update');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_smp/ujian_ruang_pendaftaran_detail_smp_update', $this->data);
	}

	/**
	* Update Ujian Ruang Pendaftaran Detail Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ruang_pendaftaran', 'Ruang', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nomor_peserta', 'Nomor Peserta', 'trim|required|max_length[30]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ruang_pendaftaran' => $this->input->post('id_ruang_pendaftaran'),
				'nomor_peserta' => $this->input->post('nomor_peserta'),
			];

			
			$save_ujian_ruang_pendaftaran_detail_smp = $this->model_ujian_ruang_pendaftaran_detail_smp->change($id, $save_data);

			if ($save_ujian_ruang_pendaftaran_detail_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_smp');
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
	* delete Ujian Ruang Pendaftaran Detail Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_delete');

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
            set_message(cclang('has_been_deleted', 'ujian_ruang_pendaftaran_detail_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_pendaftaran_detail_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Pendaftaran Detail Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_view');

		$this->data['ujian_ruang_pendaftaran_detail_smp'] = $this->model_ujian_ruang_pendaftaran_detail_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Peserta Ujian PSB SMP Detail');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_smp/ujian_ruang_pendaftaran_detail_smp_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Pendaftaran Detail Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_pendaftaran_detail_smp = $this->model_ujian_ruang_pendaftaran_detail_smp->find($id);

		
		
		return $this->model_ujian_ruang_pendaftaran_detail_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_export');

		$this->model_ujian_ruang_pendaftaran_detail_smp->export('ujian_ruang_pendaftaran_detail_smp', 'ujian_ruang_pendaftaran_detail_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_export');

		$this->model_ujian_ruang_pendaftaran_detail_smp->pdf('ujian_ruang_pendaftaran_detail_smp', 'ujian_ruang_pendaftaran_detail_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_smp_export');

		$table = $title = 'ujian_ruang_pendaftaran_detail_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_pendaftaran_detail_smp->find($id);
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


/* End of file ujian_ruang_pendaftaran_detail_smp.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Pendaftaran Detail Smp.php */