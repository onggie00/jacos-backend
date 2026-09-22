<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Detail Sd Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Pendaftaran Detail Sd site
*|
*/
class Ujian_ruang_pendaftaran_detail_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_pendaftaran_detail_sd');
		$this->lang->load('web_lang', $this->current_lang);
		$this->limit_page = 20;
	}

	/**
	* show all Ujian Ruang Pendaftaran Detail Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_pendaftaran_detail_sds'] = $this->model_ujian_ruang_pendaftaran_detail_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_pendaftaran_detail_sd_counts'] = $this->model_ujian_ruang_pendaftaran_detail_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang_pendaftaran_detail_sd/index/',
			'total_rows'   => $this->model_ujian_ruang_pendaftaran_detail_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Detail Peserta Ujian PSB SD List');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_sd/ujian_ruang_pendaftaran_detail_sd_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_pendaftaran_detail_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_add');

		$this->template->title('Detail Peserta Ujian PSB SD New');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_sd/ujian_ruang_pendaftaran_detail_sd_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Pendaftaran Detail Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_add', false)) {
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
				$cek_no_peserta = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".($nomor_awal+$i)."'","row");
				if(!empty($cek_no_peserta)){
					$save_ujian_ruang_pendaftaran_detail_sd = $this->model_ujian_ruang_pendaftaran_detail_sd->store($save_data);
				}
				else{
					continue;
				}
			}        

			if ($save_ujian_ruang_pendaftaran_detail_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_pendaftaran_detail_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_sd/edit/' . $save_ujian_ruang_pendaftaran_detail_sd, 'Edit Ujian Ruang Pendaftaran Detail Sd'),
						anchor('administrator/ujian_ruang_pendaftaran_detail_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_sd/edit/' . $save_ujian_ruang_pendaftaran_detail_sd, 'Edit Ujian Ruang Pendaftaran Detail Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_sd');
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
	* Update view Ujian Ruang Pendaftaran Detail Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_update');

		$this->data['ujian_ruang_pendaftaran_detail_sd'] = $this->model_ujian_ruang_pendaftaran_detail_sd->find($id);

		$this->template->title('Detail Peserta Ujian PSB SD Update');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_sd/ujian_ruang_pendaftaran_detail_sd_update', $this->data);
	}

	/**
	* Update Ujian Ruang Pendaftaran Detail Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_update', false)) {
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

			
			$save_ujian_ruang_pendaftaran_detail_sd = $this->model_ujian_ruang_pendaftaran_detail_sd->change($id, $save_data);

			if ($save_ujian_ruang_pendaftaran_detail_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_pendaftaran_detail_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_pendaftaran_detail_sd');
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
	* delete Ujian Ruang Pendaftaran Detail Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_delete');

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
            set_message(cclang('has_been_deleted', 'ujian_ruang_pendaftaran_detail_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_pendaftaran_detail_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Pendaftaran Detail Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_view');

		$this->data['ujian_ruang_pendaftaran_detail_sd'] = $this->model_ujian_ruang_pendaftaran_detail_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Peserta Ujian PSB SD Detail');
		$this->render('backend/standart/administrator/ujian_ruang_pendaftaran_detail_sd/ujian_ruang_pendaftaran_detail_sd_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Pendaftaran Detail Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_pendaftaran_detail_sd = $this->model_ujian_ruang_pendaftaran_detail_sd->find($id);

		
		
		return $this->model_ujian_ruang_pendaftaran_detail_sd->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_export');

		$this->model_ujian_ruang_pendaftaran_detail_sd->export('ujian_ruang_pendaftaran_detail_sd', 'ujian_ruang_pendaftaran_detail_sd');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_export');

		$this->model_ujian_ruang_pendaftaran_detail_sd->pdf('ujian_ruang_pendaftaran_detail_sd', 'ujian_ruang_pendaftaran_detail_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_pendaftaran_detail_sd_export');

		$table = $title = 'ujian_ruang_pendaftaran_detail_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_pendaftaran_detail_sd->find($id);
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

	public function cetak_absensi(){
		$this->load->library('HtmlPdf');

		$field = (!empty($this->input->get('f'))) ? $this->input->get('f') : "id_ruang_pendaftaran";
		$q = $this->input->get('q');
		$materi_simulasi = $this->input->get('materi_simulasi');

		//get all peserta
		$get_ruang = $this->mymodel->withquery("select nama_ruang, judul_ujian, tahun_ajaran, kepala_sekolah, tahun_ajaran, lokasi_ujian, meeting_id, meeting_url, meeting_password from ujian_ruang_pendaftaran_sd where ".$field." = '".$q."'","row");
		if (!empty($get_ruang)){
			$get_jadwal = $this->mymodel->withquery("select tgl_ujian, waktu_mulai, waktu_selesai, ".strtolower($materi_simulasi)." from waktu_".strtolower($materi_simulasi)."_tes where jenjang = 'sd' and ".strtolower($materi_simulasi)." like '%pelaksanaan%' ","row");
			$get_ruang->hari = formatHari(date("Y-m-d", strtotime($get_jadwal->tgl_ujian)));
			$get_ruang->tanggal = formatTanggal(date("Y-m-d", strtotime($get_jadwal->tgl_ujian)));
			$get_ruang->waktu = date("H:i", strtotime($get_jadwal->waktu_mulai))." - ".date("H:i", strtotime($get_jadwal->waktu_selesai))." WIB";
			$get_ruang->jenjang = "sd";
			$peserta = $this->mymodel->withquery("select s.nama_lengkap, s.no_peserta, s.password_ujian, s.foto_peserta, s.sekolah_asal, s.tahun_ajaran, s.gelombang, d.id_ruang_pendaftaran from ujian_ruang_pendaftaran_detail_sd d 
			join siswa_sd s on d.nomor_peserta = s.no_peserta 
			where d.$field = '".$q."' order by s.no_peserta ASC","result");
			if (!empty($peserta)){
				$get_ruang->list_peserta = $peserta;
			}
			else{
				$get_ruang->list_peserta = array();
			}
			foreach ($peserta as $key => $value) {
				$value->foto_peserta = (!empty($value->foto_peserta)) ? base_url('uploads/siswa_sd/').$value->foto_peserta : "" ;
				// echo ($key+1).". ".$value->no_peserta." ".$value->nama_lengkap." ".$value->gelombang." ".$value->tahun_ajaran."<br>";
			}

			$datas['data'] = $get_ruang;
			$pdf = new HTML2PDF('P', 'A4', 'en');
			ob_start();
			
			$this->load->view('template_absensi_ujian_pendaftaran', $datas);
			$html = ob_get_contents(); 
			ob_end_clean();

			$pdf->WriteHTML($html);
			$materi_simulasi = ($materi_simulasi == "simulasi") ? "SIMULASI" : "UJIAN";
			$pdf->Output('ABSENSI '.$materi_simulasi.' '.strtoupper($get_ruang->nama_ruang).'.pdf', 'I');
		}
		else{
			$this->data['success'] = false;
			$this->data['message'] = 'Data tidak ditemukan';
			echo json_encode($this->data);
		}
		//$get_pengaturan = $this->mymodel->withquery("select * from ","row");

	}

}


/* End of file ujian_ruang_pendaftaran_detail_sd.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Pendaftaran Detail Sd.php */