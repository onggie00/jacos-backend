<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang site
*|
*/
class Ujian_ruang extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Ruangs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruangs'] = $this->model_ujian_ruang->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_counts'] = $this->model_ujian_ruang->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ujian_ruang/index/',
			'total_rows'   => $this->model_ujian_ruang->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ruang Ujian List');
		$this->render('backend/standart/administrator/ujian_ruang/ujian_ruang_list', $this->data);
	}
	
	/**
	* Add new ujian_ruangs
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_add');

		$this->template->title('Ruang Ujian New');
		$this->render('backend/standart/administrator/ujian_ruang/ujian_ruang_add', $this->data);
	}

	/**
	* Add New Ujian Ruangs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_ruang', 'Nama Ruangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('penanggung_jawab', 'Penanggung Jawab', 'trim|required');
		$this->form_validation->set_rules('kategori_ruang[]', 'Kategori Ruang', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ruang' => $this->input->post('nama_ruang'),
				'penanggung_jawab' => $this->input->post('penanggung_jawab'),
				'kategori_ruang' => implode(',', (array) $this->input->post('kategori_ruang')),
			];

			
			$save_ujian_ruang = $this->model_ujian_ruang->store($save_data);
            

			if ($save_ujian_ruang) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang/edit/' . $save_ujian_ruang, 'Edit Ujian Ruang'),
						anchor('administrator/ujian_ruang', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang/edit/' . $save_ujian_ruang, 'Edit Ujian Ruang')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang');
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
	* Update view Ujian Ruangs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_update');

		$this->data['ujian_ruang'] = $this->model_ujian_ruang->find($id);

		$this->template->title('Ruang Ujian Update');
		$this->render('backend/standart/administrator/ujian_ruang/ujian_ruang_update', $this->data);
	}

	/**
	* Update Ujian Ruangs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_ruang', 'Nama Ruangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('penanggung_jawab', 'Penanggung Jawab', 'trim|required');
		$this->form_validation->set_rules('kategori_ruang[]', 'Kategori Ruang', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_ruang' => $this->input->post('nama_ruang'),
				'penanggung_jawab' => $this->input->post('penanggung_jawab'),
				'kategori_ruang' => implode(',', (array) $this->input->post('kategori_ruang')),
			];

			
			$save_ujian_ruang = $this->model_ujian_ruang->change($id, $save_data);

			if ($save_ujian_ruang) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang');
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
	* delete Ujian Ruangs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_delete');

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
			//delete ujian_kelas dengan ruang tsb
			$this->mymodel->delete("ujian_ruang_kelas", "id_ruang", $id);
			$this->mymodel->delete("ujian_ruang_detail", "id_ruang", $id);
			//$this->mymodel->update("ujian_ruang_kelas", array("id_ruang" => 0), "id_ruang", $id);
			//$this->mymodel->update("ujian_ruang_detail", array("id_ruang" => 0), "id_ruang", $id);
            set_message(cclang('has_been_deleted', 'ujian_ruang'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruangs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_view');

		$this->data['ujian_ruang'] = $this->model_ujian_ruang->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Ruang Ujian Detail');
		$this->render('backend/standart/administrator/ujian_ruang/ujian_ruang_view', $this->data);
	}
	
	/**
	* delete Ujian Ruangs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang = $this->model_ujian_ruang->find($id);

		
		
		return $this->model_ujian_ruang->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_export');

		$this->model_ujian_ruang->export('ujian_ruang', 'ujian_ruang');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('ujian_ruang_export');

		$this->model_ujian_ruang->pdf('ujian_ruang', 'ujian_ruang');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_export');

		$table = $title = 'ujian_ruang';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang->find($id);
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

	public function atur_bangku(){
		$get_ruang = $this->mymodel->withquery("select id_ruang, nama_ruang, penanggung_jawab from ujian_ruang order by nama_ruang ASC","result");
		$kelas_0 = array();
		$kelas_1 = array();
		$kelas_2 = array();
		$kelas_3 = array();
		foreach ($get_ruang as $key => $value) {
			$get_kelas = $this->mymodel->withquery("select * from ujian_ruang_kelas where id_ruang = '".$value->id_ruang."' order by nomor_peserta_awal ASC","result");
			$urutan = count($get_kelas);
			$nomor_kelas = $key;
			foreach ($get_kelas as $key_kelas => $value_kelas) {
				$get_peserta = $this->mymodel->withquery("select * from ujian_ruang_detail where id_ruang_kelas = '".$value_kelas->id_ruang_kelas."' order by nomor_peserta_ujian ASC","result");
				${"kelas_$key_kelas"} = array();
				foreach ($get_peserta as $key_peserta => $value_peserta) {
					$no_urut = $nomor_kelas;
					$nomor = $nomor_kelas;
					//$this->mymodel->update("ujian_ruang_detail", array("urutan_kursi" => $no_urut), "id_detail", $value_peserta->id_detail);
					/*if ($nomor == 0) {
						$nomor = 1;
					}*/
					$nomor_kelas++;
					//echo $nomor_kelas."-";
					array_push(${"kelas_$key_kelas"}, $value_peserta->nomor_peserta_ujian);
				}
				echo "<br/>";
			}
			//shuffle all array based on class
				$array_combined = [];
				$count = max(count($kelas_0), count($kelas_1), count($kelas_2), count($kelas_3));
				for ($i = 0; $i < $count; $i++) {
				    if (isset($kelas_0[$i])) {
				        $array_combined[] = $kelas_0[$i];
				    }
				    if (isset($kelas_1[$i])) {
				        $array_combined[] = $kelas_1[$i];
				    }
				    if (isset($kelas_2[$i])) {
				        $array_combined[] = $kelas_2[$i];
				    }
				    if (isset($kelas_3[$i])) {
				        $array_combined[] = $kelas_3[$i];
				    }
				}
				foreach ($array_combined as $key => $value) {
					//echo "(".$key.")".$value;
					$this->mymodel->update("ujian_ruang_detail", array("urutan_kursi" => $key+1), "nomor_peserta_ujian", $value);
				}
			$kelas_0 = array();
			$kelas_1 = array();
			$kelas_2 = array();
			$kelas_3 = array();
		}
		$this->session->set_flashdata('success', 'Berhasil melakukan pengaturan bangku');
		redirect_back();
	}

	public function print_bangku($id_ruang){
		$this->load->library('HtmlPdf');

        $get_ruang = $this->mymodel->getbywhere("ujian_ruang", "id_ruang", $id_ruang,"row");
        $get_kelas = $this->mymodel->withquery("select id_ruang, id_ruang_kelas, kelas, kapasitas from ujian_ruang_kelas where id_ruang = '".$id_ruang."'","result");
        $get_peserta = $this->mymodel->withquery("select * from ujian_ruang_detail where id_ruang = '".$id_ruang."' group by nomor_peserta_ujian order by urutan_kursi ASC","result");
        $get_ujian = $this->mymodel->withquery("select * from jadwal_ujian_".strtolower($get_peserta[0]->jenjang)." ","row");
        $get_nama_ujian = $this->mymodel->withquery("select nama_ujian from jenis_ujian where id_jenis_ujian = '".$get_ujian->id_jenis_ujian."'","row");
        $tahun_ajaran = $this->mymodel->withquery("select label from tahun_ajaran where id_tahun_ajaran = '".$get_ujian->id_tahun_ajaran."'","row");

        $datas['tahun_ajar'] = $tahun_ajaran->label;
        $datas['judul_ujian'] = $get_nama_ujian->nama_ujian;
        $datas['ujian'] = $get_ujian;
        $datas['ruang'] = $get_ruang;
        $datas['kelas'] = $get_kelas;
        $datas['peserta'] = $get_peserta;

        $pdf = new HTML2PDF('L', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_nomor_kursi_ujian', $datas);
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $pdf->Output('NOMOR URUT KURSI '.strtoupper($get_ruang->nama_ruang).'.pdf', 'I');
	}

	
}


/* End of file ujian_ruang.php */
/* Location: ./application/controllers/administrator/Ujian Ruang.php */