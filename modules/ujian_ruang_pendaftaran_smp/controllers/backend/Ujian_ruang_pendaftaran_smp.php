<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Smp Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Smp site
*|
*/
class Ujian_ruang_pendaftaran_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_pendaftaran_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Ruang Pendaftaran Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_pendaftaran_smps'] = $this->model_ujian_ruang_pendaftaran_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_pendaftaran_smp_counts'] = $this->model_ujian_ruang_pendaftaran_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang_pendaftaran_smp/index/',
			'total_rows'   => $this->model_ujian_ruang_pendaftaran_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ruang Ujian PSB SMP List');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_smp/ujian_ruang_pendaftaran_smp_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_pendaftaran_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_add');

		$this->template->title('Ruang Ujian PSB SMP New');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_smp/ujian_ruang_pendaftaran_smp_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Pendaftaran Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_smp_add', false)) {
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

			
			$save_ujian_ruang_pendaftaran_smp = $this->model_ujian_ruang_pendaftaran_smp->store($save_data);
            

			if ($save_ujian_ruang_pendaftaran_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_pendaftaran_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_smp/edit/' . $save_ujian_ruang_pendaftaran_smp, 'Edit Ujian Ruang Pendaftaran Smp'),
						anchor('administrator/ujian_ruang_pendaftaran_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_pendaftaran_smp/edit/' . $save_ujian_ruang_pendaftaran_smp, 'Edit Ujian Ruang Pendaftaran Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_smp');
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
	* Update view Ujian Ruang Pendaftaran Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_update');

		$this->data['ujian_ruang_pendaftaran_smp'] = $this->model_ujian_ruang_pendaftaran_smp->find($id);

		$this->template->title('Ruang Ujian PSB SMP Update');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_smp/ujian_ruang_pendaftaran_smp_update', $this->data);
	}

	/**
	* Update Ujian Ruang Pendaftaran Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_smp_update', false)) {
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

			
			$save_ujian_ruang_pendaftaran_smp = $this->model_ujian_ruang_pendaftaran_smp->change($id, $save_data);

			if ($save_ujian_ruang_pendaftaran_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_smp');
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
	* delete Ujian Ruang Pendaftaran Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_delete');

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
            set_message(cclang('has_been_deleted', 'ujian_ruang_pendaftaran_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_pendaftaran_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Pendaftaran Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_view');

		$this->data['ujian_ruang_pendaftaran_smp'] = $this->model_ujian_ruang_pendaftaran_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ruang Ujian PSB SMP Detail');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_smp/ujian_ruang_pendaftaran_smp_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Pendaftaran Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_pendaftaran_smp = $this->model_ujian_ruang_pendaftaran_smp->find($id);

		
		
		return $this->model_ujian_ruang_pendaftaran_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_export');

		$this->model_ujian_ruang_pendaftaran_smp->export('ujian_ruang_pendaftaran_smp', 'ujian_ruang_pendaftaran_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_export');

		$this->model_ujian_ruang_pendaftaran_smp->pdf('ujian_ruang_pendaftaran_smp', 'ujian_ruang_pendaftaran_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_smp_export');

		$table = $title = 'ujian_ruang_pendaftaran_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_pendaftaran_smp->find($id);
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
			return redirect('administrator/ujian_ruang_pendaftaran_smp');
		}

		// Ambil data ruangan
		$get_ruangan = $this->mymodel->getbywhere(
			'ujian_ruang_pendaftaran_smp',
			'id_ruang_pendaftaran',
			$id_ruangan,
			"row"
		);

		if (!$get_ruangan) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">Ruangan tidak ditemukan</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftaran_smp');
		}

		// Query peserta tanpa parameter binding (gunakan casting)
		$get_peserta = $this->mymodel->withquery("
			SELECT d.nomor_peserta, s.nama_lengkap, s.foto_peserta 
			FROM ujian_ruang_pendaftaran_detail_smp d
			LEFT JOIN siswa_smp s ON s.no_peserta = d.nomor_peserta
			WHERE d.id_ruang_pendaftaran = {$id_ruangan}
			ORDER BY d.nomor_peserta ASC
		", "result");

		if (empty($get_peserta)) {
			$this->session->set_flashdata('message',
				'<div class="alert alert-danger" role="alert">Tidak ada data peserta</div>'
			);
			return redirect('administrator/ujian_ruang_pendaftaran_smp');
		}

		// Isi hingga 20 peserta (pakai array_pad, lebih cepat dari while)
		$get_peserta = array_pad($get_peserta, 20, (object)[
			'nomor_peserta' => '0',
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
			"jenjang" => "SMP"
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


/* End of file ujian_ruang_pendaftaran_smp.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Pendaftaran Smp.php */