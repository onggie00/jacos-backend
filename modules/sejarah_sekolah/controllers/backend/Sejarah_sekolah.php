<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Sejarah Sekolah Controller
*| --------------------------------------------------------------------------
*| Sejarah Sekolah site
*|
*/
class Sejarah_sekolah extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_sejarah_sekolah');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Sejarah Sekolahs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('sejarah_sekolah_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['sejarah_sekolahs'] = $this->model_sejarah_sekolah->get($filter, $field, $this->limit_page, $offset);
		$this->data['sejarah_sekolah_counts'] = $this->model_sejarah_sekolah->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/sejarah_sekolah/index/',
			'total_rows'   => $this->model_sejarah_sekolah->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Sejarah Sekolah List');
		$this->render('backend/standart/administrator/sejarah_sekolah/sejarah_sekolah_list', $this->data);
	}
	
	/**
	* Add new sejarah_sekolahs
	*
	*/
	public function add()
	{
		$this->is_allowed('sejarah_sekolah_add');

		$this->template->title('Sejarah Sekolah New');
		$this->render('backend/standart/administrator/sejarah_sekolah/sejarah_sekolah_add', $this->data);
	}

	/**
	* Add New Sejarah Sekolahs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('sejarah_sekolah_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[25]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
			];

			
			$save_sejarah_sekolah = $this->model_sejarah_sekolah->store($save_data);
            

			if ($save_sejarah_sekolah) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_sejarah_sekolah;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/sejarah_sekolah/edit/' . $save_sejarah_sekolah, 'Edit Sejarah Sekolah'),
						anchor('administrator/sejarah_sekolah', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/sejarah_sekolah/edit/' . $save_sejarah_sekolah, 'Edit Sejarah Sekolah')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sejarah_sekolah');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sejarah_sekolah');
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
	* Update view Sejarah Sekolahs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('sejarah_sekolah_update');

		$this->data['sejarah_sekolah'] = $this->model_sejarah_sekolah->find($id);

		$this->template->title('Sejarah Sekolah Update');
		$this->render('backend/standart/administrator/sejarah_sekolah/sejarah_sekolah_update', $this->data);
	}

	/**
	* Update Sejarah Sekolahs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('sejarah_sekolah_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[25]');
		$this->form_validation->set_rules('konten', 'Konten', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'konten' => $this->input->post('konten'),
			];

			
			$save_sejarah_sekolah = $this->model_sejarah_sekolah->change($id, $save_data);

			if ($save_sejarah_sekolah) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/sejarah_sekolah', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sejarah_sekolah');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sejarah_sekolah');
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
	* delete Sejarah Sekolahs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('sejarah_sekolah_delete');

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
            set_message(cclang('has_been_deleted', 'sejarah_sekolah'), 'success');
        } else {
            set_message(cclang('error_delete', 'sejarah_sekolah'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Sejarah Sekolahs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('sejarah_sekolah_view');

		$this->data['sejarah_sekolah'] = $this->model_sejarah_sekolah->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Sejarah Sekolah Detail');
		$this->render('backend/standart/administrator/sejarah_sekolah/sejarah_sekolah_view', $this->data);
	}
	
	/**
	* delete Sejarah Sekolahs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$sejarah_sekolah = $this->model_sejarah_sekolah->find($id);

		
		
		return $this->model_sejarah_sekolah->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('sejarah_sekolah_export');

		$this->model_sejarah_sekolah->export('sejarah_sekolah', 'sejarah_sekolah');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('sejarah_sekolah_export');

		$this->model_sejarah_sekolah->pdf('sejarah_sekolah', 'sejarah_sekolah');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('sejarah_sekolah_export');

		$table = $title = 'sejarah_sekolah';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_sejarah_sekolah->find($id);
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


/* End of file sejarah_sekolah.php */
/* Location: ./application/controllers/administrator/Sejarah Sekolah.php */