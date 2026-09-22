<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Guru Ft Piket Controller
*| --------------------------------------------------------------------------
*| Guru Ft Piket site
*|
*/
class Guru_ft_piket extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_guru_ft_piket');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Guru Ft Pikets
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('guru_ft_piket_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$list_guru = array();
		$list_guru_count = 0;
		if ($filter == "" && $field == "") {
			$get_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft ", "result");
			//search guru by filter SMA
			$get_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft ", "result");
			foreach ($get_guru_ft as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_ft);
			}
			foreach ($get_guru_sma as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_sma);
			}
			$this->data['guru_ft_pikets'] = $list_guru;
			$this->data['guru_ft_piket_counts'] = $list_guru_count;
		}
		else if($field == ""){
			//search guru by filter FT
			$get_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' or k.label like '%".$filter."%' order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' or k.label like '%".$filter."%' ", "result");
			//search guru by filter SMA
			$get_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' or k.label like '%".$filter."%' order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' or k.label like '%".$filter."%' ", "result");
			foreach ($get_guru_ft as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_ft);
			}
			foreach ($get_guru_sma as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_sma);
			}
			$this->data['guru_ft_pikets'] = $list_guru;
			$this->data['guru_ft_piket_counts'] = $list_guru_count;
		}
		else if ($field == "nama_lengkap") {
			//search guru by filter FT
			$get_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' ", "result");
			//search guru by filter SMA
			$get_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft where g.nama_lengkap like '%".$filter."%' ", "result");
			foreach ($get_guru_ft as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_ft);
			}
			foreach ($get_guru_sma as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_sma);
			}
			$this->data['guru_ft_pikets'] = $list_guru;
			$this->data['guru_ft_piket_counts'] = $list_guru_count;
		}
		else if ($field == "id_kelas") {
			//search guru by filter FT
			$get_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft where k.label like '%".$filter."%' order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_ft = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_ft g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'ft' join kelas_ft k on p.id_kelas = k.id_kelas_ft where k.label like '%".$filter."%' ", "result");
			//search guru by filter SMA
			$get_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft where k.label like '%".$filter."%' order by p.id_guru_piket DESC limit ".$offset.",".$this->limit_page." ", "result");
			$count_guru_sma = $this->mymodel->withquery("select p.id_guru_piket, p.jenjang, g.id_guru, g.nama_lengkap, p.id_kelas, k.label as nama_kelas from guru_sma g join guru_ft_piket p on g.id_guru = p.id_guru and p.jenjang = 'sma' join kelas_ft k on p.id_kelas = k.id_kelas_ft where k.label like '%".$filter."%' ", "result");
			foreach ($get_guru_ft as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_ft);
			}
			foreach ($get_guru_sma as $key => $value) {
				$list_guru[] = $value;
				$list_guru_count = $list_guru_count + count($count_guru_sma);
			}
			$this->data['guru_ft_pikets'] = $list_guru;
			$this->data['guru_ft_piket_counts'] = $list_guru_count;
		}
		/*$this->data['guru_ft_pikets'] = $this->model_guru_ft_piket->get($filter, $field, $this->limit_page, $offset);
		$this->data['guru_ft_piket_counts'] = $this->model_guru_ft_piket->count_all($filter, $field);*/
		$config = [
			'base_url'     => 'administrator/guru_ft_piket/index/',
			'total_rows'   => $list_guru_count,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Guru Piket FT List');
		$this->render('backend/standart/administrator/guru_ft_piket/guru_ft_piket_list', $this->data);
	}
	
	/**
	* Add new guru_ft_pikets
	*
	*/
	public function add()
	{
		$this->is_allowed('guru_ft_piket_add');

		$this->template->title('Guru Piket FT New');
		$this->render('backend/standart/administrator/guru_ft_piket/guru_ft_piket_add', $this->data);
	}

	/**
	* Add New Guru Ft Pikets
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('guru_ft_piket_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'id_kelas' => $this->input->post('id_kelas'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_guru_ft_piket = $this->model_guru_ft_piket->store($save_data);
            

			if ($save_guru_ft_piket) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_guru_ft_piket;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/guru_ft_piket/edit/' . $save_guru_ft_piket, 'Edit Guru Ft Piket'),
						anchor('administrator/guru_ft_piket', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/guru_ft_piket/edit/' . $save_guru_ft_piket, 'Edit Guru Ft Piket')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/guru_ft_piket');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/guru_ft_piket');
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
	* Update view Guru Ft Pikets
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('guru_ft_piket_update');

		$this->data['guru_ft_piket'] = $this->model_guru_ft_piket->find($id);

		$this->template->title('Guru Piket FT Update');
		$this->render('backend/standart/administrator/guru_ft_piket/guru_ft_piket_update', $this->data);
	}

	/**
	* Update Guru Ft Pikets
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('guru_ft_piket_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'id_kelas' => $this->input->post('id_kelas'),
				'jenjang' => $this->input->post('jenjang'),
			];

			
			$save_guru_ft_piket = $this->model_guru_ft_piket->change($id, $save_data);

			if ($save_guru_ft_piket) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/guru_ft_piket', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/guru_ft_piket');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/guru_ft_piket');
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
	* delete Guru Ft Pikets
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('guru_ft_piket_delete');

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
            set_message(cclang('has_been_deleted', 'guru_ft_piket'), 'success');
        } else {
            set_message(cclang('error_delete', 'guru_ft_piket'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Guru Ft Pikets
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('guru_ft_piket_view');

		$this->data['guru_ft_piket'] = $this->model_guru_ft_piket->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Guru Piket FT Detail');
		$this->render('backend/standart/administrator/guru_ft_piket/guru_ft_piket_view', $this->data);
	}
	
	/**
	* delete Guru Ft Pikets
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$guru_ft_piket = $this->model_guru_ft_piket->find($id);

		
		
		return $this->model_guru_ft_piket->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('guru_ft_piket_export');

		$this->model_guru_ft_piket->export('guru_ft_piket', 'guru_ft_piket');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('guru_ft_piket_export');

		$this->model_guru_ft_piket->pdf('guru_ft_piket', 'guru_ft_piket');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('guru_ft_piket_export');

		$table = $title = 'guru_ft_piket';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_guru_ft_piket->find($id);
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


/* End of file guru_ft_piket.php */
/* Location: ./application/controllers/administrator/Guru Ft Piket.php */