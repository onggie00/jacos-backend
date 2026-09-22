<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Sd Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Sd site
*|
*/
class Ujian_ruang_pendaftaran_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_pendaftaran_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Ruang Pendaftaran Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_pendaftaran_sds'] = $this->model_ujian_ruang_pendaftaran_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_pendaftaran_sd_counts'] = $this->model_ujian_ruang_pendaftaran_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang_pendaftaran_sd/index/',
			'total_rows'   => $this->model_ujian_ruang_pendaftaran_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ruang Ujian PSB SD List');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_sd/ujian_ruang_pendaftaran_sd_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_pendaftaran_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_add');

		$this->template->title('Ruang Ujian PSB SD New');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_sd/ujian_ruang_pendaftaran_sd_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Pendaftaran Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_ruang', 'Nama Ruang', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('judul_ujian', 'Ujian', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kepala_sekolah', 'Kepala Sekolah', 'trim|required|max_length[150]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ruang' => $this->input->post('nama_ruang'),
				'judul_ujian' => $this->input->post('judul_ujian'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'kepala_sekolah' => $this->input->post('kepala_sekolah'),
				'meeting_url' => $this->input->post('meeting_url'),
				'meeting_password' => $this->input->post('meeting_password'),
				'maks_peserta' => $this->input->post('maks_peserta'),
			];

			
			$save_ujian_ruang_pendaftaran_sd = $this->model_ujian_ruang_pendaftaran_sd->store($save_data);
            

			if ($save_ujian_ruang_pendaftaran_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_pendaftaran_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_sd/edit/' . $save_ujian_ruang_pendaftaran_sd, 'Edit Ujian Ruang Pendaftaran Sd'),
						anchor('administrator/ujian_ruang_pendaftaran_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_pendaftaran_sd/edit/' . $save_ujian_ruang_pendaftaran_sd, 'Edit Ujian Ruang Pendaftaran Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_sd');
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
	* Update view Ujian Ruang Pendaftaran Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_update');

		$this->data['ujian_ruang_pendaftaran_sd'] = $this->model_ujian_ruang_pendaftaran_sd->find($id);

		$this->template->title('Ruang Ujian PSB SD Update');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_sd/ujian_ruang_pendaftaran_sd_update', $this->data);
	}

	/**
	* Update Ujian Ruang Pendaftaran Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_ruang', 'Nama Ruang', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('judul_ujian', 'Ujian', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kepala_sekolah', 'Kepala Sekolah', 'trim|required|max_length[150]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ruang' => $this->input->post('nama_ruang'),
				'judul_ujian' => $this->input->post('judul_ujian'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'kepala_sekolah' => $this->input->post('kepala_sekolah'),
				'meeting_url' => $this->input->post('meeting_url'),
				'meeting_password' => $this->input->post('meeting_password'),
				'maks_peserta' => $this->input->post('maks_peserta'),
			];

			
			$save_ujian_ruang_pendaftaran_sd = $this->model_ujian_ruang_pendaftaran_sd->change($id, $save_data);

			if ($save_ujian_ruang_pendaftaran_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_sd');
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
	* delete Ujian Ruang Pendaftaran Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_delete');

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
            set_message(cclang('has_been_deleted', 'ujian_ruang_pendaftaran_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_pendaftaran_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Pendaftaran Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_view');

		$this->data['ujian_ruang_pendaftaran_sd'] = $this->model_ujian_ruang_pendaftaran_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ruang Ujian PSB SD Detail');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_sd/ujian_ruang_pendaftaran_sd_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Pendaftaran Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_pendaftaran_sd = $this->model_ujian_ruang_pendaftaran_sd->find($id);

		
		
		return $this->model_ujian_ruang_pendaftaran_sd->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_export');

		$this->model_ujian_ruang_pendaftaran_sd->export('ujian_ruang_pendaftaran_sd', 'ujian_ruang_pendaftaran_sd');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_export');

		$this->model_ujian_ruang_pendaftaran_sd->pdf('ujian_ruang_pendaftaran_sd', 'ujian_ruang_pendaftaran_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_sd_export');

		$table = $title = 'ujian_ruang_pendaftaran_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_pendaftaran_sd->find($id);
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

	public function cetak_album($id_ruangan = null)
	{
		// Pastikan id valid dan angka
		$id_ruangan = (int) $id_ruangan;
		if ($id_ruangan <= 0) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">ID Ruangan tidak valid</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftarand_sd');
		}

		// Ambil data ruangan
		$get_ruangan = $this->mymodel->getbywhere(
			'ujian_ruang_pendaftarand_sd',
			'id_ruang_pendaftaran',
			$id_ruangan,
			"row"
		);

		if (!$get_ruangan) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">Ruangan tidak ditemukan</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftarand_sd');
		}

		// Query peserta tanpa parameter binding (gunakan casting)
		$get_peserta = $this->mymodel->withquery("
			SELECT d.nomor_peserta, s.nama_lengkap, s.foto_peserta 
			FROM ujian_ruang_pendaftaran_detaild_sd d
			LEFT JOIN siswad_sd s ON s.no_peserta = d.nomor_peserta
			WHERE d.id_ruang_pendaftaran = {$id_ruangan}
			ORDER BY d.nomor_peserta ASC
		", "result");

		if (empty($get_peserta)) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">Tidak ada data peserta</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftarand_sd');
		}

		// Isi hingga 20 peserta (pakai array_pad, lebih cepat dari while)
		$get_peserta = array_pad($get_peserta, 20, (object)[
			'nomor_peserta' => '&nbsp;',
			'nama_lengkap'  => 'kosong',
			'foto_peserta'  => '0.jpg'
		]);

		// Susunan kolom sesuai urutan yang diinginkan
		$tableData = [
			[$get_peserta[8],  $get_peserta[9],  $get_peserta[10], $get_peserta[11]],
			[$get_peserta[6],  $get_peserta[7],  $get_peserta[12], $get_peserta[13]],
			[$get_peserta[4],  $get_peserta[5],  $get_peserta[14], $get_peserta[15]],
			[$get_peserta[2],  $get_peserta[3],  $get_peserta[16], $get_peserta[17]],
			[$get_peserta[0],  $get_peserta[1],  $get_peserta[18], $get_peserta[19]],
		];

		// Siapkan data view
		$datas = [
			"ruangan" => $get_ruangan,
			"peserta" => $tableData,
			"jenjang" => "SD"
		];

		// Load library sekali saja
		$this->load->library('HtmlPdf');
		$pdf = new HTML2PDF('P', 'A4', 'en');

		ob_start();
		$this->load->view('template_album', $datas);
		$html = ob_get_clean();

		$pdf->WriteHTML($html);
		$nama_file = $get_ruangan->nama_ruang . '_' . $get_ruangan->tahun_ajaran . '.pdf';
		$path_file = FCPATH . 'uploads/album/' . $nama_file;

		$pdf->Output($path_file, 'F');
		redirect('uploads/album/' . $nama_file);
	}

	
}


/* End of file ujian_ruang_pendaftaran_sd.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Pendaftaran Sd.php */