<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Web Pengaturan Homepage Controller
*| --------------------------------------------------------------------------
*| Web Pengaturan Homepage site
*|
*/
class Web_pengaturan_homepage extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_web_pengaturan_homepage');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Web Pengaturan Homepages
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('web_pengaturan_homepage_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['web_pengaturan_homepages'] = $this->model_web_pengaturan_homepage->get($filter, $field, $this->limit_page, $offset);
		$this->data['web_pengaturan_homepage_counts'] = $this->model_web_pengaturan_homepage->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/web_pengaturan_homepage/index/',
			'total_rows'   => $this->model_web_pengaturan_homepage->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Web Pengaturan Homepage List');
		$this->render('backend/standart/administrator/web_pengaturan_homepage/web_pengaturan_homepage_list', $this->data);
	}
	
	
		/**
	* Update view Web Pengaturan Homepages
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('web_pengaturan_homepage_update');

		$this->data['web_pengaturan_homepage'] = $this->model_web_pengaturan_homepage->find($id);

		$this->template->title('Web Pengaturan Homepage Update');
		$this->render('backend/standart/administrator/web_pengaturan_homepage/web_pengaturan_homepage_update', $this->data);
	}

	/**
	* Update Web Pengaturan Homepages
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('web_pengaturan_homepage_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('link_youtube', 'Link Youtube', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sub_judul', 'Sub Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('link_whatsapp', 'Link Whatsapp', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('footer_alamat', 'Alamat', 'trim|required');
		$this->form_validation->set_rules('footer_notelp', 'Nomor Telepon', 'trim|required');
		$this->form_validation->set_rules('footer_email', 'Email', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'link_youtube' => $this->input->post('link_youtube'),
				'judul' => $this->input->post('judul'),
				'sub_judul' => $this->input->post('sub_judul'),
				'link_whatsapp' => $this->input->post('link_whatsapp'),
				'footer_alamat' => $this->input->post('footer_alamat'),
				'footer_notelp' => $this->input->post('footer_notelp'),
				'footer_email' => $this->input->post('footer_email'),
				'footer_instagram' => $this->input->post('footer_instagram'),
				'footer_facebook' => $this->input->post('footer_facebook'),
				'footer_twitter' => $this->input->post('footer_twitter'),
			];

			
			$save_web_pengaturan_homepage = $this->model_web_pengaturan_homepage->change($id, $save_data);

			if ($save_web_pengaturan_homepage) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/web_pengaturan_homepage', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/web_pengaturan_homepage');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/web_pengaturan_homepage');
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
	* delete Web Pengaturan Homepages
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('web_pengaturan_homepage_delete');

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
            set_message(cclang('has_been_deleted', 'web_pengaturan_homepage'), 'success');
        } else {
            set_message(cclang('error_delete', 'web_pengaturan_homepage'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Web Pengaturan Homepages
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('web_pengaturan_homepage_view');

		$this->data['web_pengaturan_homepage'] = $this->model_web_pengaturan_homepage->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Web Pengaturan Homepage Detail');
		$this->render('backend/standart/administrator/web_pengaturan_homepage/web_pengaturan_homepage_view', $this->data);
	}
	
	/**
	* delete Web Pengaturan Homepages
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$web_pengaturan_homepage = $this->model_web_pengaturan_homepage->find($id);

		
		
		return $this->model_web_pengaturan_homepage->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('web_pengaturan_homepage_export');

		$this->model_web_pengaturan_homepage->export('web_pengaturan_homepage', 'web_pengaturan_homepage');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('web_pengaturan_homepage_export');

		$this->model_web_pengaturan_homepage->pdf('web_pengaturan_homepage', 'web_pengaturan_homepage');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('web_pengaturan_homepage_export');

		$table = $title = 'web_pengaturan_homepage';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_web_pengaturan_homepage->find($id);
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


/* End of file web_pengaturan_homepage.php */
/* Location: ./application/controllers/administrator/Web Pengaturan Homepage.php */