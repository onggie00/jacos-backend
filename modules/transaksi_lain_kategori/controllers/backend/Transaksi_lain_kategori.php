<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Transaksi Lain Kategori Controller
*| --------------------------------------------------------------------------
*| Transaksi Lain Kategori site
*|
*/
class Transaksi_lain_kategori extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_transaksi_lain_kategori');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Transaksi Lain Kategoris
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('transaksi_lain_kategori_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['transaksi_lain_kategoris'] = $this->model_transaksi_lain_kategori->get($filter, $field, $this->limit_page, $offset);
		$this->data['transaksi_lain_kategori_counts'] = $this->model_transaksi_lain_kategori->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/transaksi_lain_kategori/index/',
			'total_rows'   => $this->model_transaksi_lain_kategori->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Transaksi Lain Kategori List');
		$this->render('backend/standart/administrator/transaksi_lain_kategori/transaksi_lain_kategori_list', $this->data);
	}
	
	/**
	* Add new transaksi_lain_kategoris
	*
	*/
	public function add()
	{
		$this->is_allowed('transaksi_lain_kategori_add');

		$this->template->title('Transaksi Lain Kategori New');
		$this->render('backend/standart/administrator/transaksi_lain_kategori/transaksi_lain_kategori_add', $this->data);
	}

	/**
	* Add New Transaksi Lain Kategoris
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('transaksi_lain_kategori_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_kategori', 'Kategori', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_kategori' => $this->input->post('nama_kategori'),
			];

			
			$save_transaksi_lain_kategori = $this->model_transaksi_lain_kategori->store($save_data);
            

			if ($save_transaksi_lain_kategori) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_transaksi_lain_kategori;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/transaksi_lain_kategori/edit/' . $save_transaksi_lain_kategori, 'Edit Transaksi Lain Kategori'),
						anchor('administrator/transaksi_lain_kategori', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/transaksi_lain_kategori/edit/' . $save_transaksi_lain_kategori, 'Edit Transaksi Lain Kategori')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi_lain_kategori');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi_lain_kategori');
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
	* Update view Transaksi Lain Kategoris
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('transaksi_lain_kategori_update');

		$this->data['transaksi_lain_kategori'] = $this->model_transaksi_lain_kategori->find($id);

		$this->template->title('Transaksi Lain Kategori Update');
		$this->render('backend/standart/administrator/transaksi_lain_kategori/transaksi_lain_kategori_update', $this->data);
	}

	/**
	* Update Transaksi Lain Kategoris
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('transaksi_lain_kategori_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_kategori', 'Kategori', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_kategori' => $this->input->post('nama_kategori'),
			];

			
			$save_transaksi_lain_kategori = $this->model_transaksi_lain_kategori->change($id, $save_data);

			if ($save_transaksi_lain_kategori) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/transaksi_lain_kategori', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi_lain_kategori');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi_lain_kategori');
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
	* delete Transaksi Lain Kategoris
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('transaksi_lain_kategori_delete');

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
            set_message(cclang('has_been_deleted', 'transaksi_lain_kategori'), 'success');
        } else {
            set_message(cclang('error_delete', 'transaksi_lain_kategori'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Transaksi Lain Kategoris
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('transaksi_lain_kategori_view');

		$this->data['transaksi_lain_kategori'] = $this->model_transaksi_lain_kategori->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Transaksi Lain Kategori Detail');
		$this->render('backend/standart/administrator/transaksi_lain_kategori/transaksi_lain_kategori_view', $this->data);
	}
	
	/**
	* delete Transaksi Lain Kategoris
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$transaksi_lain_kategori = $this->model_transaksi_lain_kategori->find($id);

		
		
		return $this->model_transaksi_lain_kategori->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('transaksi_lain_kategori_export');

		$this->model_transaksi_lain_kategori->export('transaksi_lain_kategori', 'transaksi_lain_kategori');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('transaksi_lain_kategori_export');

		$this->model_transaksi_lain_kategori->pdf('transaksi_lain_kategori', 'transaksi_lain_kategori');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('transaksi_lain_kategori_export');

		$table = $title = 'transaksi_lain_kategori';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_transaksi_lain_kategori->find($id);
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


/* End of file transaksi_lain_kategori.php */
/* Location: ./application/controllers/administrator/Transaksi Lain Kategori.php */