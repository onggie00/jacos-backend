<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Office Submission Controller
*| --------------------------------------------------------------------------
*| Presensi Office Submission site
*|
*/
class Presensi_office_submission extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_office_submission');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Office Submissions
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_office_submission_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['presensi_office_submissions'] = $this->model_presensi_office_submission->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_office_submission_counts'] = $this->model_presensi_office_submission->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_office_submission/index/',
			'total_rows'   => $this->model_presensi_office_submission->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Office Submission List');
		$this->render('backend/standart/administrator/presensi_office_submission/presensi_office_submission_list', $this->data);
	}
	
	/**
	* Add new presensi_office_submissions
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_office_submission_add');

		$this->template->title('Presensi Office Submission New');
		$this->render('backend/standart/administrator/presensi_office_submission/presensi_office_submission_add', $this->data);
	}

	/**
	* Add New Presensi Office Submissions
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_office_submission_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('submission_code', 'Jenis', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('notes', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('presensi_office_submission_file_submission_name', 'File', 'trim|required');
		$this->form_validation->set_rules('submission_status', 'Status Approval', 'trim|required');
		$this->form_validation->set_rules('date_start', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('date_end', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('head_role', 'Head Role (optional)', 'trim|max_length[30]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[255]');
		

		if ($this->form_validation->run()) {
			$presensi_office_submission_file_submission_uuid = $this->input->post('presensi_office_submission_file_submission_uuid');
			$presensi_office_submission_file_submission_name = $this->input->post('presensi_office_submission_file_submission_name');
		
			$npp = $this->input->post('npp');
			$save_data = [
				'npp' => $npp,
				'nama_lengkap' => explode('|', $this->input->post('nama_lengkap'))[1],
				'submission_code' => $this->input->post('submission_code'),
				'notes' => $this->input->post('notes'),
				'submission_status' => $this->input->post('submission_status'),
				'date_start' => $this->input->post('date_start'),
				'date_end' => $this->input->post('date_end'),
				'role' => $this->input->post('role'),
				'head_role' => $this->input->post('head_role'),
				'updated_by' => (!empty($this->input->post('updated_by'))) ? $this->input->post('updated_by') : "-",
			];

			if (!is_dir(FCPATH . '/uploads/submission/'.$npp.'/')) {
				mkdir(FCPATH . '/uploads/submission/'.$npp.'/', 0777, true);
			}

			if (!empty($presensi_office_submission_file_submission_name)) {
				$presensi_office_submission_file_submission_name_copy = date('YmdHis') . '-' . $presensi_office_submission_file_submission_name;

				rename(FCPATH . 'uploads/tmp/' . $presensi_office_submission_file_submission_uuid . '/' . $presensi_office_submission_file_submission_name, 
						FCPATH . 'uploads/submission/'.$npp.'/' . $presensi_office_submission_file_submission_name_copy);

				if (!is_file(FCPATH . '/uploads/submission/'.$npp.'/' . $presensi_office_submission_file_submission_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_submission'] = './uploads/submission/'.$npp.'/'.$presensi_office_submission_file_submission_name_copy;
			}
		
			
			$save_presensi_office_submission = $this->model_presensi_office_submission->store($save_data);
            

			if ($save_presensi_office_submission) {
				//jika submission_status = 1 / disetujui maka insert ke table presensi_office sesuai dengan total hari nya
				if ($this->input->post('submission_status') == 1) {
					//cek presensi_office
					$get_submission = $this->mymodel->withquery("select s.*, p.nama_tabel, p.head_role, t.presensi_code from presensi_office_submission s 
					left join presensi_setting_role p on p.nama_role = s.role 
					join presensi_submission_type t on t.code = s.submission_code 
					where s.id_submission = '".$save_presensi_office_submission."'","row");
					$list_day = $this->get_working_days($get_submission->date_start,$get_submission->date_end);
					foreach ($list_day as $key => $value) {
						$cek_presensi = $this->mymodel->withquery("select * from presensi_office where npp = ? and presensi_date = ?","row", array($get_submission->npp, $value));
						$data_insert = array(
							'npp'=>$get_submission->npp,
							'presensi_date'=>$value, 
							'status_presensi'=>$get_submission->presensi_code,
							'status_presensi_selesai'=>$get_submission->presensi_code,
							'npp'=>$get_submission->npp,
							'nama_lengkap'=>$get_submission->nama_lengkap,
							'presensi_hari'=>formatHari($value),
							'role' => $get_submission->role,
							'presensi_device'=>'Labscib Apps',
							'keterangan'=>$get_submission->notes,
							'updated_by' => $nama_lengkap
						);
						if (empty($cek_presensi)) {
							$data_insert['created_at'] = date('Y-m-d H:i:s');
							//insert
							$insert = $this->mymodel->insert('presensi_office',$data_insert);
						}
						else if (!empty($cek_presensi)) {
							//update
							$data_insert['updated_at'] = date('Y-m-d H:i:s');
							$update = $this->mymodel->update('presensi_office', $data_insert, 'id_presensi', $cek_presensi->id_presensi);
						}
					}
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_office_submission;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_office_submission/edit/' . $save_presensi_office_submission, 'Edit Presensi Office Submission'),
						anchor('administrator/presensi_office_submission', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_office_submission/edit/' . $save_presensi_office_submission, 'Edit Presensi Office Submission')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_office_submission');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_office_submission');
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
	* Update view Presensi Office Submissions
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_office_submission_update');

		$this->data['presensi_office_submission'] = $this->model_presensi_office_submission->find($id);

		$this->template->title('Presensi Office Submission Update');
		$this->render('backend/standart/administrator/presensi_office_submission/presensi_office_submission_update', $this->data);
	}

	/**
	* Update Presensi Office Submissions
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_office_submission_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('npp', 'NPP', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('submission_code', 'Jenis', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('notes', 'Keterangan', 'trim|required');
		$this->form_validation->set_rules('presensi_office_submission_file_submission_name', 'File', 'trim|required');
		$this->form_validation->set_rules('submission_status', 'Status Approval', 'trim|required');
		$this->form_validation->set_rules('date_start', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('date_end', 'Tanggal Selesai', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('head_role', 'Head Role (optional)', 'trim|max_length[30]');
		$this->form_validation->set_rules('updated_by', 'Diubah Oleh', 'trim|max_length[255]');
		
		if ($this->form_validation->run()) {
			$presensi_office_submission_file_submission_uuid = $this->input->post('presensi_office_submission_file_submission_uuid');
			$presensi_office_submission_file_submission_name = $this->input->post('presensi_office_submission_file_submission_name');
		
			$save_data = [
				'npp' => $this->input->post('npp'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'submission_code' => $this->input->post('submission_code'),
				'notes' => $this->input->post('notes'),
				'submission_status' => $this->input->post('submission_status'),
				'date_start' => $this->input->post('date_start'),
				'date_end' => $this->input->post('date_end'),
				'role' => $this->input->post('role'),
				'head_role' => $this->input->post('head_role'),
				'updated_by' => $this->input->post('updated_by'),
			];

			if (!is_dir(FCPATH . '/uploads/presensi_office_submission/')) {
				mkdir(FCPATH . '/uploads/presensi_office_submission/');
			}

			if (!empty($presensi_office_submission_file_submission_uuid)) {
				$presensi_office_submission_file_submission_name_copy = date('YmdHis') . '-' . $presensi_office_submission_file_submission_name;

				rename(FCPATH . 'uploads/tmp/' . $presensi_office_submission_file_submission_uuid . '/' . $presensi_office_submission_file_submission_name, 
						FCPATH . 'uploads/presensi_office_submission/' . $presensi_office_submission_file_submission_name_copy);

				if (!is_file(FCPATH . '/uploads/presensi_office_submission/' . $presensi_office_submission_file_submission_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_submission'] = $presensi_office_submission_file_submission_name_copy;
			}
		
			
			$save_presensi_office_submission = $this->model_presensi_office_submission->change($id, $save_data);

			if ($save_presensi_office_submission) {
				//add presensi_office
				$get_presensi = $this->mymodel->withquery("select id_presensi, npp, nama_lengkap, status_presensi, status_presensi_selesai from presensi_office where npp = '".$save_data['npp']."' and presensi_date >= '".$save_data['date_start']."' and presensi_date <= '".$save_data['date_end']."'", "result");
				if($save_data['submission_status'] == 1){
					if(!empty($get_presensi)){
						foreach ($get_presensi as $key => $value) {
							$data_update = array(
								'status_presensi' => str_replace("PENGAJUAN ", "", $value->status_presensi),
								'status_presensi_selesai' => str_replace("PENGAJUAN ", "", $value->status_presensi_selesai),
								'updated_at' => date('Y-m-d H:i:s'),
								'updated_by' => "System Administrator",
							);
							$this->mymodel->update("presensi_office", $data_update, "id_presensi", $value->id_presensi);
						}
					}
				}
				else if($save_data['submission_status'] == 2){
					if(!empty($get_presensi)){
						foreach ($get_presensi as $key => $value) {
							$data_update = array(
								'status_presensi' => $value->status_presensi." DITOLAK",
								'status_presensi_selesai' => $value->status_presensi_selesai." DITOLAK",
								'updated_at' => date('Y-m-d H:i:s'),
								'updated_by' => "System Administrator",
							);
							$this->mymodel->update("presensi_office", $data_update, "id_presensi", $value->id_presensi);
						}
					}
				}
				else if($save_data['submission_status'] == 3){
					if(!empty($get_presensi)){
						foreach ($get_presensi as $key => $value) {
							$data_update = array(
								'status_presensi' => $value->status_presensi." Perlu validasi lanjutan",
								'status_presensi_selesai' => $value->status_presensi_selesai." Perlu validasi lanjutan",
								'updated_at' => date('Y-m-d H:i:s'),
								'updated_by' => "System Administrator",
							);
							$this->mymodel->update("presensi_office", $data_update, "id_presensi", $value->id_presensi);
						}
					}
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_office_submission', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_office_submission');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_office_submission');
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
	* delete Presensi Office Submissions
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_office_submission_delete');

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
            set_message(cclang('has_been_deleted', 'presensi_office_submission'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_office_submission'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Office Submissions
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_office_submission_view');

		$this->data['presensi_office_submission'] = $this->model_presensi_office_submission->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Office Submission Detail');
		$this->render('backend/standart/administrator/presensi_office_submission/presensi_office_submission_view', $this->data);
	}
	
	/**
	* delete Presensi Office Submissions
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_office_submission = $this->model_presensi_office_submission->find($id);

		if (!empty($presensi_office_submission->file_submission)) {
			$path = FCPATH . '/uploads/presensi_office_submission/' . $presensi_office_submission->file_submission;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_presensi_office_submission->remove($id);
	}
	
	/**
	* Upload Image Presensi Office Submission	* 
	* @return JSON
	*/
	public function upload_file_submission_file()
	{
		if (!$this->is_allowed('presensi_office_submission_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'presensi_office_submission',
		]);
	}

	/**
	* Delete Image Presensi Office Submission	* 
	* @return JSON
	*/
	public function delete_file_submission_file($uuid)
	{
		if (!$this->is_allowed('presensi_office_submission_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_submission', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'presensi_office_submission',
            'primary_key'       => 'id_submission',
            'upload_path'       => 'uploads/presensi_office_submission/'
        ]);
	}

	/**
	* Get Image Presensi Office Submission	* 
	* @return JSON
	*/
	public function get_file_submission_file($id)
	{
		if (!$this->is_allowed('presensi_office_submission_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$presensi_office_submission = $this->model_presensi_office_submission->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_submission', 
            'table_name'        => 'presensi_office_submission',
            'primary_key'       => 'id_submission',
            'upload_path'       => 'uploads/presensi_office_submission/',
            'delete_endpoint'   => 'administrator/presensi_office_submission/delete_file_submission_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_office_submission_export');

		$this->model_presensi_office_submission->export('presensi_office_submission', 'presensi_office_submission');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_office_submission_export');

		$this->model_presensi_office_submission->pdf('presensi_office_submission', 'presensi_office_submission');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_office_submission_export');

		$table = $title = 'presensi_office_submission';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_office_submission->find($id);
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


	public function get_user(){
		$tipe_user = $this->input->post('tipe_user');
		$data = $this->mymodel->withquery("select u.npp, u.nama_lengkap, u.presensi_role, r.nama_role, r.head_role from $tipe_user u 
		left join presensi_setting_role r on r.nama_role = u.presensi_role 
		where u.deleted_at is null 
		order by u.nama_lengkap asc","result");
		
		$daftar_select = "";
		if(!empty($data)){
			$daftar_select .= "<option value=''>-- Pilih User --</option>";
			foreach ($data as $key => $item) {
				if($item->head_role == null){
					$item->head_role = "NULL";
				}
				$daftar_select .= "<option value='".$item->npp."|".$item->nama_lengkap."|".$item->presensi_role."|".$item->head_role."'>".$item->nama_lengkap."</option>";
			}
			$daftar_select .= "";
		}
		echo $daftar_select;
	}

	function get_working_days($start, $end)
    {
        $result = [];

        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);

        // include end date
        $endDate->modify('+1 day');

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

        foreach ($period as $date) {
            $dayOfWeek = $date->format('N'); // 1 (Senin) - 7 (Minggu)

            if ($dayOfWeek < 6) { // exclude Sabtu(6) & Minggu(7)
                $result[] = $date->format('Y-m-d');
            }
        }

        return $result;
    }
}


/* End of file presensi_office_submission.php */
/* Location: ./application/controllers/administrator/Presensi Office Submission.php */