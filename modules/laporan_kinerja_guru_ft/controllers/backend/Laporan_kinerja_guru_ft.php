<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Laporan Kinerja Guru Ft Controller
*| --------------------------------------------------------------------------
*| Laporan Kinerja Guru Ft site
*|
*/
class Laporan_kinerja_guru_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_laporan_kinerja_guru_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Laporan Kinerja Guru Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('laporan_kinerja_guru_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['laporan_kinerja_guru_fts'] = $this->model_laporan_kinerja_guru_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['laporan_kinerja_guru_ft_counts'] = $this->model_laporan_kinerja_guru_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/laporan_kinerja_guru_ft/index/',
			'total_rows'   => $this->model_laporan_kinerja_guru_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Laporan Kinerja Guru FT List');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_ft/laporan_kinerja_guru_ft_list', $this->data);
	}
	
	/**
	* Add new laporan_kinerja_guru_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('laporan_kinerja_guru_ft_add');

		$this->template->title('Laporan Kinerja Guru FT New');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_ft/laporan_kinerja_guru_ft_add', $this->data);
	}

	/**
	* Add New Laporan Kinerja Guru Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('laporan_kinerja_guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('mata_pelajaran', 'Mata Pelajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nilai_pimpinan', 'Penilaian Pimpinan', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sejawat', 'Penilaian Sejawat', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_siswa', 'Penilaian Siswa', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sendiri', 'Penilaian Sendiri', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_prestasi', 'Penilaian Prestasi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_presensi', 'Penilaian Presensi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'mata_pelajaran' => $this->input->post('mata_pelajaran'),
				'nilai_pimpinan' => $this->input->post('nilai_pimpinan'),
				'nilai_sejawat' => $this->input->post('nilai_sejawat'),
				'nilai_siswa' => $this->input->post('nilai_siswa'),
				'nilai_sendiri' => $this->input->post('nilai_sendiri'),
				'nilai_prestasi' => $this->input->post('nilai_prestasi'),
				'nilai_presensi' => $this->input->post('nilai_presensi'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_guru_ft = $this->model_laporan_kinerja_guru_ft->store($save_data);
            

			if ($save_laporan_kinerja_guru_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_laporan_kinerja_guru_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/laporan_kinerja_guru_ft/edit/' . $save_laporan_kinerja_guru_ft, 'Edit Laporan Kinerja Guru Ft'),
						anchor('administrator/laporan_kinerja_guru_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/laporan_kinerja_guru_ft/edit/' . $save_laporan_kinerja_guru_ft, 'Edit Laporan Kinerja Guru Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_ft');
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
	* Update view Laporan Kinerja Guru Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('laporan_kinerja_guru_ft_update');

		$this->data['laporan_kinerja_guru_ft'] = $this->model_laporan_kinerja_guru_ft->find($id);

		$this->template->title('Laporan Kinerja Guru FT Update');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_ft/laporan_kinerja_guru_ft_update', $this->data);
	}

	/**
	* Update Laporan Kinerja Guru Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('laporan_kinerja_guru_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('mata_pelajaran', 'Mata Pelajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nilai_pimpinan', 'Penilaian Pimpinan', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sejawat', 'Penilaian Sejawat', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_siswa', 'Penilaian Siswa', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sendiri', 'Penilaian Sendiri', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_prestasi', 'Penilaian Prestasi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_presensi', 'Penilaian Presensi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'mata_pelajaran' => $this->input->post('mata_pelajaran'),
				'nilai_pimpinan' => $this->input->post('nilai_pimpinan'),
				'nilai_sejawat' => $this->input->post('nilai_sejawat'),
				'nilai_siswa' => $this->input->post('nilai_siswa'),
				'nilai_sendiri' => $this->input->post('nilai_sendiri'),
				'nilai_prestasi' => $this->input->post('nilai_prestasi'),
				'nilai_presensi' => $this->input->post('nilai_presensi'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_guru_ft = $this->model_laporan_kinerja_guru_ft->change($id, $save_data);

			if ($save_laporan_kinerja_guru_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/laporan_kinerja_guru_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_ft');
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
	* delete Laporan Kinerja Guru Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('laporan_kinerja_guru_ft_delete');

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
            set_message(cclang('has_been_deleted', 'laporan_kinerja_guru_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'laporan_kinerja_guru_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Laporan Kinerja Guru Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('laporan_kinerja_guru_ft_view');

		$this->data['laporan_kinerja_guru_ft'] = $this->model_laporan_kinerja_guru_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Laporan Kinerja Guru FT Detail');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_ft/laporan_kinerja_guru_ft_view', $this->data);
	}
	
	/**
	* delete Laporan Kinerja Guru Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$laporan_kinerja_guru_ft = $this->model_laporan_kinerja_guru_ft->find($id);

		
		
		return $this->model_laporan_kinerja_guru_ft->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('laporan_kinerja_guru_ft_export');

		$this->model_laporan_kinerja_guru_ft->export('laporan_kinerja_guru_ft', 'laporan_kinerja_guru_ft');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('laporan_kinerja_guru_ft_export');

		$this->model_laporan_kinerja_guru_ft->pdf('laporan_kinerja_guru_ft', 'laporan_kinerja_guru_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('laporan_kinerja_guru_ft_export');

		$table = $title = 'laporan_kinerja_guru_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_laporan_kinerja_guru_ft->find($id);
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


/* End of file laporan_kinerja_guru_ft.php */
/* Location: ./application/controllers/administrator/Laporan Kinerja Guru Ft.php */