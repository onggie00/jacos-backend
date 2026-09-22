<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Prestasi Siswa Bidang Controller
*| --------------------------------------------------------------------------
*| Prestasi Siswa Bidang site
*|
*/
class Prestasi_siswa_bidang extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_prestasi_siswa_bidang');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Prestasi Siswa Bidangs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('prestasi_siswa_bidang_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['prestasi_siswa_bidangs'] = $this->model_prestasi_siswa_bidang->get($filter, $field, $this->limit_page, $offset);
		$this->data['prestasi_siswa_bidang_counts'] = $this->model_prestasi_siswa_bidang->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/prestasi_siswa_bidang/index/',
			'total_rows'   => $this->model_prestasi_siswa_bidang->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
			'reuse_query_string' => TRUE,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Bidang Prestasi Siswa List');
		$this->render('backend/standart/administrator/prestasi_siswa_bidang/prestasi_siswa_bidang_list', $this->data);
	}
	
	/**
	* Add new prestasi_siswa_bidangs
	*
	*/
	public function add()
	{
		$this->is_allowed('prestasi_siswa_bidang_add');

		$this->template->title('Bidang Prestasi Siswa New');
		$this->render('backend/standart/administrator/prestasi_siswa_bidang/prestasi_siswa_bidang_add', $this->data);
	}

	/**
	* Add New Prestasi Siswa Bidangs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('prestasi_siswa_bidang_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_bidang', 'Nama Bidang', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_bidang' => $this->input->post('nama_bidang'),
			];

			
			$save_prestasi_siswa_bidang = $this->model_prestasi_siswa_bidang->store($save_data);
            

			if ($save_prestasi_siswa_bidang) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_prestasi_siswa_bidang;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/prestasi_siswa_bidang/edit/' . $save_prestasi_siswa_bidang, 'Edit Prestasi Siswa Bidang'),
						anchor('administrator/prestasi_siswa_bidang', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/prestasi_siswa_bidang/edit/' . $save_prestasi_siswa_bidang, 'Edit Prestasi Siswa Bidang')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_bidang');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_bidang');
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
	* Update view Prestasi Siswa Bidangs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('prestasi_siswa_bidang_update');

		$this->data['prestasi_siswa_bidang'] = $this->model_prestasi_siswa_bidang->find($id);

		$this->template->title('Bidang Prestasi Siswa Update');
		$this->render('backend/standart/administrator/prestasi_siswa_bidang/prestasi_siswa_bidang_update', $this->data);
	}

	/**
	* Update Prestasi Siswa Bidangs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('prestasi_siswa_bidang_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_bidang', 'Nama Bidang', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_bidang' => $this->input->post('nama_bidang'),
			];

			
			$save_prestasi_siswa_bidang = $this->model_prestasi_siswa_bidang->change($id, $save_data);

			if ($save_prestasi_siswa_bidang) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/prestasi_siswa_bidang', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_bidang');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/prestasi_siswa_bidang');
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
	* delete Prestasi Siswa Bidangs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('prestasi_siswa_bidang_delete');

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
            set_message(cclang('has_been_deleted', 'prestasi_siswa_bidang'), 'success');
        } else {
            set_message(cclang('error_delete', 'prestasi_siswa_bidang'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Prestasi Siswa Bidangs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('prestasi_siswa_bidang_view');

		$this->data['prestasi_siswa_bidang'] = $this->model_prestasi_siswa_bidang->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Bidang Prestasi Siswa Detail');
		$this->render('backend/standart/administrator/prestasi_siswa_bidang/prestasi_siswa_bidang_view', $this->data);
	}
	
	/**
	* delete Prestasi Siswa Bidangs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$prestasi_siswa_bidang = $this->model_prestasi_siswa_bidang->find($id);

		
		
		return $this->model_prestasi_siswa_bidang->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('prestasi_siswa_bidang_export');

		$this->model_prestasi_siswa_bidang->export('prestasi_siswa_bidang', 'prestasi_siswa_bidang');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('prestasi_siswa_bidang_export');

		$this->model_prestasi_siswa_bidang->pdf('prestasi_siswa_bidang', 'prestasi_siswa_bidang');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('prestasi_siswa_bidang_export');

		$table = $title = 'prestasi_siswa_bidang';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_prestasi_siswa_bidang->find($id);
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


/* End of file prestasi_siswa_bidang.php */
/* Location: ./application/controllers/administrator/Prestasi Siswa Bidang.php */