<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Acara Presensi Evaluasi Controller
*| --------------------------------------------------------------------------
*| Acara Presensi Evaluasi site
*|
*/
class Acara_presensi_evaluasi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_acara_presensi_evaluasi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Acara Presensi Evaluasis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('acara_presensi_evaluasi_list');

		$id_acara = $this->input->get('id_acara'); // v2: filter by acara
		$search_npp = $this->input->get('q'); // v2.2: search by NPP only (f field selector dihapus)

		// v2.2: pagination over distinct (npp, peserta) - list summary, bukan evaluasi rows
		$total = $this->model_acara_presensi_evaluasi->count_distinct_peserta($id_acara, $search_npp);
		$this->data['acara_presensi_evaluasis'] = $this->model_acara_presensi_evaluasi->get_distinct_peserta($id_acara, $this->limit_page, $offset, $search_npp);
		$this->data['acara_presensi_evaluasi_counts'] = $total;

		$config = [
			'base_url'     => 'administrator/acara_presensi_evaluasi/index/',
			'total_rows'   => $total,
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
			'reuse_query_string' => TRUE, // v2: preserve id_acara & q di pagination links
		];

		// v2: populate dropdown acara untuk filter UI
		$this->data['acara_dropdown'] = $this->db->order_by('id_acara', 'DESC')->get('acara')->result();
		$this->data['id_acara_selected'] = $id_acara;

		// v2.4: chart per-peserta - dihapus dari page-level (lihat Tahap 2 view).

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Evaluasi Acara List');
		$this->render('backend/standart/administrator/acara_presensi_evaluasi/acara_presensi_evaluasi_list', $this->data);
	}
	
	/**
	* Add new acara_presensi_evaluasis
	*
	*/
	public function add()
	{
		$this->is_allowed('acara_presensi_evaluasi_add');

		$this->template->title('Evaluasi Acara New');
		$this->render('backend/standart/administrator/acara_presensi_evaluasi/acara_presensi_evaluasi_add', $this->data);
	}

	/**
	* Add New Acara Presensi Evaluasis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('acara_presensi_evaluasi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_acara', 'Acara', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|max_length[100]');
		$this->form_validation->set_rules('id_evaluasi_form', 'Pertanyaan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jawaban', 'Jawaban', 'trim|required');
		$this->form_validation->set_rules('created_at', 'Tanggal', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_acara' => $this->input->post('id_acara'),
				'npp' => $this->input->post('npp'),
				'id_evaluasi_form' => $this->input->post('id_evaluasi_form'),
				'jawaban' => $this->input->post('jawaban'),
				'created_at' => $this->input->post('created_at'),
			];

			
			$save_acara_presensi_evaluasi = $this->model_acara_presensi_evaluasi->store($save_data);
            

			if ($save_acara_presensi_evaluasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_acara_presensi_evaluasi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/acara_presensi_evaluasi/edit/' . $save_acara_presensi_evaluasi, 'Edit Acara Presensi Evaluasi'),
						anchor('administrator/acara_presensi_evaluasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/acara_presensi_evaluasi/edit/' . $save_acara_presensi_evaluasi, 'Edit Acara Presensi Evaluasi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara_presensi_evaluasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara_presensi_evaluasi');
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
	* Update view Acara Presensi Evaluasis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('acara_presensi_evaluasi_update');

		$this->data['acara_presensi_evaluasi'] = $this->model_acara_presensi_evaluasi->find($id);

		$this->template->title('Evaluasi Acara Update');
		$this->render('backend/standart/administrator/acara_presensi_evaluasi/acara_presensi_evaluasi_update', $this->data);
	}

	/**
	* Update Acara Presensi Evaluasis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('acara_presensi_evaluasi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_acara', 'Acara', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('npp', 'NPP', 'trim|max_length[100]');
		$this->form_validation->set_rules('id_evaluasi_form', 'Pertanyaan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jawaban', 'Jawaban', 'trim|required');
		$this->form_validation->set_rules('created_at', 'Tanggal', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_acara' => $this->input->post('id_acara'),
				'npp' => $this->input->post('npp'),
				'id_evaluasi_form' => $this->input->post('id_evaluasi_form'),
				'jawaban' => $this->input->post('jawaban'),
				'created_at' => $this->input->post('created_at'),
			];

			
			$save_acara_presensi_evaluasi = $this->model_acara_presensi_evaluasi->change($id, $save_data);

			if ($save_acara_presensi_evaluasi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/acara_presensi_evaluasi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara_presensi_evaluasi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara_presensi_evaluasi');
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
	* v2 AJAX: Get jawaban + info peserta by composite key (npp, peserta)
	* Return JSON {info: {npp, nama, peserta}, qa: [{pertanyaan, jawaban}]}
	*/
	public function get_jawaban_by_peserta()
	{
		$npp = $this->input->get('npp');
		$peserta = $this->input->get('peserta');

		$response = ['info' => null, 'qa' => []];

		if ($npp) {
			$result = $this->model_acara_presensi_evaluasi->get_jawaban_by_peserta($npp, $peserta);
			if (!empty($result)) {
				$first = $result[0];
				$response['info'] = [
					'npp' => $first->npp,
					'nama' => $first->guru_nama_lengkap,
					'peserta' => $first->acara_presensi_peserta
				];
				foreach ($result as $r) {
					$response['qa'][] = [
						'pertanyaan' => $r->acara_evaluasi_form_pertanyaan,
						'jawaban' => $r->jawaban
					];
				}
			}
		}

		echo json_encode($response);
	}

	/**
	* v2.4 AJAX: Get chart data per peserta (numeric jawaban + no_urut pertanyaan)
	* untuk modal grafik dengan Bar/Line chart.
	* Return JSON {info: {npp, nama, peserta, acara_nama}, chart: [{no_urut, pertanyaan, jawaban}]}
	*/
	public function get_chart_by_peserta()
	{
		$npp = $this->input->get('npp');
		$peserta = $this->input->get('peserta');
		$id_acara = $this->input->get('id_acara');

		$response = ['info' => null, 'chart' => []];

		if ($npp) {
			// Fetch info dari jawaban table (pakai model method existing untuk konsistensi)
			$info_result = $this->model_acara_presensi_evaluasi->get_jawaban_by_peserta($npp, $peserta);
			if (!empty($info_result)) {
				$first = $info_result[0];
				$response['info'] = [
					'npp' => $first->npp,
					'nama' => $first->guru_nama_lengkap,
					'peserta' => $first->acara_presensi_peserta,
					'acara_nama' => isset($first->acara_nama_acara) ? $first->acara_nama_acara : null,
				];
			}

			// Fetch chart data (numeric only, dengan no_urut)
			$chart_data = $this->model_acara_presensi_evaluasi->get_chart_data_by_peserta($npp, $peserta, $id_acara);
			foreach ($chart_data as $r) {
				$response['chart'][] = [
					'no_urut' => $r->no_urut,
					'pertanyaan' => $r->pertanyaan,
					'jawaban' => (float)$r->jawaban_num,
				];
			}
		}

		echo json_encode($response);
	}
	
	/**
	* delete Acara Presensi Evaluasis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('acara_presensi_evaluasi_delete');

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
            set_message(cclang('has_been_deleted', 'acara_presensi_evaluasi'), 'success');
        } else {
            set_message(cclang('error_delete', 'acara_presensi_evaluasi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Acara Presensi Evaluasis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('acara_presensi_evaluasi_view');

		$this->data['acara_presensi_evaluasi'] = $this->model_acara_presensi_evaluasi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Evaluasi Acara Detail');
		$this->render('backend/standart/administrator/acara_presensi_evaluasi/acara_presensi_evaluasi_view', $this->data);
	}
	
	/**
	* delete Acara Presensi Evaluasis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$acara_presensi_evaluasi = $this->model_acara_presensi_evaluasi->find($id);

		
		
		return $this->model_acara_presensi_evaluasi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('acara_presensi_evaluasi_export');

		$this->model_acara_presensi_evaluasi->export('acara_presensi_evaluasi', 'acara_presensi_evaluasi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('acara_presensi_evaluasi_export');

		$this->model_acara_presensi_evaluasi->pdf('acara_presensi_evaluasi', 'acara_presensi_evaluasi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('acara_presensi_evaluasi_export');

		$table = $title = 'acara_presensi_evaluasi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_acara_presensi_evaluasi->find($id);
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


/* End of file acara_presensi_evaluasi.php */
/* Location: ./application/controllers/administrator/Acara Presensi Evaluasi.php */