<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Transaksi Lain Sd Controller
*| --------------------------------------------------------------------------
*| Transaksi Lain Sd site
*|
*/
class Transaksi_lain_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_transaksi_lain_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Transaksi Lain Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('transaksi_lain_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['transaksi_lain_smas'] = $this->model_transaksi_lain_sma->get($filter, $field, $this->limit_page, $offset);
		$this->data['transaksi_lain_sma_counts'] = $this->model_transaksi_lain_sma->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/transaksi_lain_sma/index/',
			'total_rows'   => $this->model_transaksi_lain_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		// Infobox stats
		$this->data['stats'] = $this->model_transaksi_lain_sma->get_stats($filter, $field);

		// Chart data
		$this->data['chart_data'] = $this->model_transaksi_lain_sma->get_chart_data($filter, $field);

		// Pass current filter to view
		$this->data['current_filter_q'] = $filter;
		$this->data['current_filter_f'] = $field;

		$this->template->title('List Tagihan SMA List');
		$this->render('backend/standart/administrator/transaksi_lain_sma/transaksi_lain_sma_list', $this->data);
	}
	
	
		/**
	* Update view Transaksi Lain Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('transaksi_lain_sma_update');

		$this->data['transaksi_lain_sma'] = $this->model_transaksi_lain_sma->find($id);

		$this->template->title('List Tagihan SMA Update');
		$this->render('backend/standart/administrator/transaksi_lain_sma/transaksi_lain_sma_update', $this->data);
	}

	/**
	* Update Transaksi Lain Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('transaksi_lain_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_transaksi_lain', 'Tagihan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('tanggal_bayar', 'Tanggal Pembayaran', 'trim');
		$this->form_validation->set_rules('va_number', 'VA Number', 'trim|max_length[30]');
		$this->form_validation->set_rules('kode_tagihan', 'Nomor Tagihan', 'trim|max_length[100]');
		$this->form_validation->set_rules('transaksi_lain_sma_file_kwitansi_name', 'File Kwitansi', 'trim');
		$this->form_validation->set_rules('nominal_bayar', 'Nominal Bayar', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('status_transaksi', 'Status Transaksi', 'trim|required');
		
		if ($this->form_validation->run()) {
			$transaksi_lain_sma_file_kwitansi_uuid = $this->input->post('transaksi_lain_sma_file_kwitansi_uuid');
			$transaksi_lain_sma_file_kwitansi_name = $this->input->post('transaksi_lain_sma_file_kwitansi_name');
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_transaksi_lain' => $this->input->post('id_transaksi_lain'),
				'tanggal_bayar' => (!empty($this->input->post('tanggal_bayar')) ? date('Y-m-d H:i:s', strtotime($this->input->post('tanggal_bayar'))) : null),
				'va_number' => $this->input->post('va_number'),
				'kode_tagihan' => $this->input->post('kode_tagihan'),
				'nominal_bayar' => $this->input->post('nominal_bayar'),
				'status_transaksi' => $this->input->post('status_transaksi'),
			];

			if (!is_dir(FCPATH . '/uploads/transaksi_lain_sma/')) {
				mkdir(FCPATH . '/uploads/transaksi_lain_sma/');
			}

			if (!empty($transaksi_lain_sma_file_kwitansi_uuid)) {
				$transaksi_lain_sma_file_kwitansi_name_copy = date('YmdHis') . '-' . $transaksi_lain_sma_file_kwitansi_name;

				rename(FCPATH . 'uploads/tmp/' . $transaksi_lain_sma_file_kwitansi_uuid . '/' . $transaksi_lain_sma_file_kwitansi_name, 
						FCPATH . 'uploads/transaksi_lain_sma/' . $transaksi_lain_sma_file_kwitansi_name_copy);

				if (!is_file(FCPATH . '/uploads/transaksi_lain_sma/' . $transaksi_lain_sma_file_kwitansi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_kwitansi'] = $transaksi_lain_sma_file_kwitansi_name_copy;
			}
		
			
			$save_transaksi_lain_sma = $this->model_transaksi_lain_sma->change($id, $save_data);

			if ($save_transaksi_lain_sma) {
				//cetak kwitansi jika status lunas
				if ($save_data['status_transaksi'] == "2") {
			        $no_transaksi = $this->input->post('kode_tagihan');
			        $jenjang = "sd";
			        $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.kode_tagihan = '". $no_transaksi . "'" , "row");
			        $cetak_kwitansi = $this->cetak_kwitansi(array("jenjang" => $jenjang, "nama_lengkap" => $get_tagihan->nama_lengkap, "va_number" => $get_tagihan->va_number, "kode_tagihan" => $get_tagihan->kode_tagihan, "total_biaya" => $get_tagihan->nominal_bayar, "total_biaya_terbilang" => terbilang($get_tagihan->nominal_bayar) . " Rupiah", "id_siswa" => $get_tagihan->id_siswa_aktif, "jenis_kwitansi" => $get_tagihan->nama_transaksi));
			        //update file kwitansi
			        $data_transaksi = array(
		                "status_transaksi" => 2,
		                "updated_at" => date("Y-m-d H:i:s"),
		                "file_kwitansi" => $get_tagihan->kode_tagihan . '-' . str_replace(' ', '_', $get_tagihan->nama_lengkap) . '.pdf'
		              );
		            $transaksi = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data_transaksi, "kode_tagihan", $no_transaksi);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/transaksi_lain_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi_lain_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi_lain_sma');
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
	* delete Transaksi Lain Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('transaksi_lain_sma_delete');

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
            set_message(cclang('has_been_deleted', 'transaksi_lain_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'transaksi_lain_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Transaksi Lain Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('transaksi_lain_sma_view');

		$this->data['transaksi_lain_sma'] = $this->model_transaksi_lain_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('List Tagihan SMA Detail');
		$this->render('backend/standart/administrator/transaksi_lain_sma/transaksi_lain_sma_view', $this->data);
	}
	
	/**
	* delete Transaksi Lain Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$transaksi_lain_sma = $this->model_transaksi_lain_sma->find($id);

		if (!empty($transaksi_lain_sma->file_kwitansi)) {
			$path = FCPATH . '/uploads/transaksi_lain_sma/' . $transaksi_lain_sma->file_kwitansi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_transaksi_lain_sma->remove($id);
	}
	
	/**
	* Upload Image Transaksi Lain Sd	* 
	* @return JSON
	*/
	public function upload_file_kwitansi_file()
	{
		if (!$this->is_allowed('transaksi_lain_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'transaksi_lain_sma',
		]);
	}

	/**
	* Delete Image Transaksi Lain Sd	* 
	* @return JSON
	*/
	public function delete_file_kwitansi_file($uuid)
	{
		if (!$this->is_allowed('transaksi_lain_sma_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_kwitansi', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'transaksi_lain_sma',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/transaksi_lain_sma/'
        ]);
	}

	/**
	* Get Image Transaksi Lain Sd	* 
	* @return JSON
	*/
	public function get_file_kwitansi_file($id)
	{
		if (!$this->is_allowed('transaksi_lain_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$transaksi_lain_sma = $this->model_transaksi_lain_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_kwitansi', 
            'table_name'        => 'transaksi_lain_sma',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/transaksi_lain_sma/',
            'delete_endpoint'   => 'administrator/transaksi_lain_sma/delete_file_kwitansi_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('transaksi_lain_sma_export');

		//$this->model_transaksi_lain_sma->export('transaksi_lain_sma', 'transaksi_lain_sma');
		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$where = "";
		$field = ['nomor', 'id_siswa_aktif', 'nis', 'id_kelas', 'nama_transaksi', 'status_transaksi', 'nominal_bayar', 'tanggal_bayar', 'va_number', 'kode_tagihan'];

		if (!empty($q) && ($f == "nama_siswa" || $f == "id_siswa_aktif")) {
			$where = "where s.nama_lengkap like '%".$q."%'";
		}
		else if (!empty($q) && $f == "va_number") {
			$where = "where t.va_number like '%".$q."%'";
		}
		else if (!empty($q) && ($f == "nama_kelas" || $f == "id_kelas")) {
			$where = "where k.label like '%".$q."%'";
		}
		else if (!empty($q) && $f == "nama_transaksi") {
			$where = "where m.nama_transaksi like '%".$q."%'";
		}
		else if (!empty($q) && $f == "kode_tagihan") {
			$where = "where t.kode_tagihan like '%".$q."%'";
		}
		else if (!empty($q) && $f == "status_transaksi") {
			$status_map = array('belum dibayar' => '0', 'menunggu pembayaran' => '1', 'lunas' => '2', 'kadaluarsa' => '3', 'expired' => '3');
			$mapped = false;
			foreach ($status_map as $text => $val) {
				if (strpos($q, $text) !== false) {
					$where = "where t.status_transaksi = '".$val."'";
					$mapped = true;
					break;
				}
			}
			if (!$mapped) {
				$where = "where t.status_transaksi like '%".$q."%'";
			}
		}
		else if(!empty($q) && empty($f)){
			$where = "where (s.nama_lengkap like '%".$q."%'";
			$where .= " or k.label like '%".$q."%'";
			$where .= " or m.nama_transaksi like '%".$q."%'";
			$where .= " or t.va_number like '%".$q."%'";
			$where .= " or t.kode_tagihan like '%".$q."%')";
		}

		$get_data = $this->mymodel->withquery("select s.id_siswa_sma_aktif as id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label as nama_kelas, m.nama_transaksi, t.va_number, t.kode_tagihan, t.tanggal_bayar, t.nominal_bayar, 
			case when t.status_transaksi = '0' then 'Belum Dibayar' 
			when t.status_transaksi = '1' then 'Menunggu Pembayaran'
			when t.status_transaksi = '2' then 'Lunas'
			when t.status_transaksi = '3' then 'kadaluarsa' end as status_transaksi
		 	from transaksi_lain_sma t join transaksi_lain_sma_manajemen m on t.id_transaksi_lain = m.id join siswa_sma_aktif s on t.id_siswa_aktif = s.id_siswa_sma_aktif join kelas_sma k on s.id_kelas = k.id_kelas_sma ".$where." order by s.nama_lengkap ASC","result");
		
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field); $i++)  
		{
			//if (strpos($field_search[$i], "id") !== false) {
			if($field[$i] == "nomor") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if($field[$i] == "id_siswa") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SISWA'));
			}
			else if($field[$i] == "id_kelas") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KELAS'));
			}
			else if($field[$i] == "id_transaksi_lain") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA TAGIHAN'));
			}
			else if($field[$i] == "kode_tagihan") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMOR TAGIHAN'));
			}
			else if($field[$i] == "nominal_bayar") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NOMINAL'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field); $j++) {
				$kolom = $field[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "nomor") !== false) {
		        	$value_data = ($key+1);
		        }
		        if (strpos($kolom, "id_kelas") !== false) {
		        	$value_data = $value->nama_kelas;
		        }
		        if (strpos($kolom, "id_siswa") !== false) {
		        	$value_data = $value->nama_lengkap;
		        }
		        if (strpos($kolom, "id_transaksi_lain") !== false) {
		        	$value_data = $value->nama_transaksi;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="List Tagihan Lain SD - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');

	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('transaksi_lain_sma_export');

		$this->model_transaksi_lain_sma->pdf('transaksi_lain_sma', 'transaksi_lain_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('transaksi_lain_sma_export');

		$table = $title = 'transaksi_lain_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_transaksi_lain_sma->find($id);
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

	function cetak_kwitansi($get = '')
  	{
	    //$usecookie = __DIR__ . "/cookie.txt";
	    $header[] = 'Content-Type: application/json';
	    $header[] = "Accept-Encoding: gzip, deflate";
	    $header[] = "Cache-Control: max-age=0";
	    $header[] = "Connection: keep-alive";
	    $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

	    $ch = curl_init();
	    //curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_VERBOSE, false);
	    // curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_ENCODING, true);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

	    if ($get) {
	      $endpoint = site_url('/apiapp/tagihan_lain/export_pdf_kwitansi');
	      //$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
	      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) .
	        '&jenjang=' . urlencode($get['jenjang']) .
	        '&nama_lengkap=' . urlencode($get['nama_lengkap']) .
	        '&total_biaya=' . urlencode($get['total_biaya']) .
	        '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) .
	        '&va_number=' . urlencode($get['va_number']) .
	        '&no_transaksi=' . urlencode($get['kode_tagihan']);
	      curl_setopt($ch, CURLOPT_URL, $url);
	    }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    $rs = curl_exec($ch);

	    if (empty($rs)) {
	      // var_dump($rs, curl_error($ch));
	      curl_close($ch);
	      return false;
	    }
	    curl_close($ch);
	    return $rs;
  	}

	
}


/* End of file transaksi_lain_sma.php */
/* Location: ./application/controllers/administrator/Transaksi Lain Sd.php */