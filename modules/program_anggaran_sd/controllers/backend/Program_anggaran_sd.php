<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

/**
*| --------------------------------------------------------------------------
*| Program Anggaran Sd Controller
*| --------------------------------------------------------------------------
*| Program Anggaran Sd site
*|
*/
class Program_anggaran_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_program_anggaran_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * Parse multi filter dari GET parameter ff[], fo[], fv[]
	 */
	private function parse_filters()
	{
		$filters = array();
		$ff = $this->input->get('ff');
		$fo = $this->input->get('fo');
		$fv = $this->input->get('fv');

		if (is_array($ff) && count($ff) > 0) {
			$max = count($ff);
			for ($i = 0; $i < $max; $i++) {
				$field = isset($ff[$i]) ? $ff[$i] : '';
				$operator = isset($fo[$i]) ? $fo[$i] : '';
				$value = isset($fv[$i]) ? $fv[$i] : '';

				if (!empty($field) && !empty($operator) && trim($value) !== '') {
					$filters[] = array(
						'field'    => $field,
						'operator' => $operator,
						'value'    => $value
					);
				}
			}
		}

		return $filters;
	}

	function get_program($id)
    {
		$data = $this->mymodel->withquery("select * from program_anggaran where nomor_program = '".$id."'","row");
		//hitung max okr yang tersisa
        $okr = $data->nominal_okr;
        $get_program_by_jenis_kegiatan = $this->mymodel->getbywhere("program_anggaran_sd", "nomor_program = '".$data->nomor_program."' and jenis_kegiatan=", $data->jenis_kegiatan,"result");
        $okr_terpakai = 0;
        foreach ($get_program_by_jenis_kegiatan as $key => $value) {
                        $okr_terpakai = $okr_terpakai + $value->nominal_pengajuan;
        }
        $okr = $okr - $okr_terpakai;
        $data->sisa_okr = $okr;
		echo json_encode($data);
    }

	/**
	* Get detail Program Anggaran SD untuk modal
	*
	* @var $id String
	* @return JSON
	*/
	public function get_detail($id)
	{
		if (!$this->is_allowed('program_anggaran_sd_view', false)) {
			echo json_encode(array(
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			));
			exit;
		}

		$program_anggaran_sd = $this->model_program_anggaran_sd->join_avaiable()->filter_avaiable()->find($id);

		if (!$program_anggaran_sd) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Data tidak ditemukan'
			));
			exit;
		}

		// Hitung sisa OKR
		$okr = $program_anggaran_sd->nominal_okr;
		$get_program_by_jenis_kegiatan = $this->mymodel->getbywhere("program_anggaran_sd", "nomor_program = '".$program_anggaran_sd->nomor_program."' and jenis_kegiatan=", $program_anggaran_sd->jenis_kegiatan, "result");
		$okr_terpakai = 0;
		foreach ($get_program_by_jenis_kegiatan as $key => $value) {
			$okr_terpakai = $okr_terpakai + $value->nominal_pengajuan;
		}
		$sisa_okr = $okr - $okr_terpakai;

		// Format tanggal
		$tanggal_program       = !empty($program_anggaran_sd->tanggal_program) ? date("d-m-Y", strtotime($program_anggaran_sd->tanggal_program)) : '-';
		$tanggal_pencairan     = !empty($program_anggaran_sd->tanggal_pencairan) ? date("d-m-Y", strtotime($program_anggaran_sd->tanggal_pencairan)) : '-';
		$tanggal_pengajuan     = !empty($program_anggaran_sd->created_at) ? date("d-m-Y H:i", strtotime($program_anggaran_sd->created_at)) : '-';

		// Status color
		$sp = $program_anggaran_sd->status_pengajuan;
		$sp_color = '#95a5a6';
		if ($sp == "1" || $sp == "5") $sp_color = '#EA4335';
		elseif ($sp == "2" || $sp == "3") $sp_color = '#FBBC04';
		elseif ($sp == "4") $sp_color = '#34A853';
		elseif ($sp == "6") $sp_color = '#4387F8';

		$sl = $program_anggaran_sd->status_laporan;
		$sl_color = '#95a5a6';
		if ($sl == "1" || $sl == "5") $sl_color = '#EA4335';
		elseif ($sl == "2" || $sl == "3") $sl_color = '#FBBC04';
		elseif ($sl == "4") $sl_color = '#34A853';
		elseif ($sl == "6") $sl_color = '#4387F8';

		$data = array(
			'success' => true,
			'nomor_program' => $program_anggaran_sd->nomor_program,
			'nama_program' => $program_anggaran_sd->nama_program,
			'jenis_kegiatan' => $program_anggaran_sd->jenis_kegiatan,
			'sub_jenis_kegiatan' => $program_anggaran_sd->sub_jenis_kegiatan,
			'tahun_ajaran' => $program_anggaran_sd->tahun_ajaran,
			'tanggal_program' => $tanggal_program,
			'tanggal_pengajuan' => $tanggal_pengajuan,
			'tanggal_pencairan' => $tanggal_pencairan,
			'nominal_okr' => number_format($program_anggaran_sd->nominal_okr, 0, ',', '.'),
			'nominal_pengajuan' => number_format($program_anggaran_sd->nominal_pengajuan, 0, ',', '.'),
			'sisa_okr' => number_format($sisa_okr, 0, ',', '.'),
			'dasar_pelaksanaan_kegiatan' => $program_anggaran_sd->dasar_pelaksanaan_kegiatan,
			'tema_kegiatan' => $program_anggaran_sd->tema_kegiatan,
			'tujuan_kegiatan' => $program_anggaran_sd->tujuan_kegiatan,
			'gambaran_acara_kegiatan' => $program_anggaran_sd->gambaran_acara_kegiatan,
			'hasil_yang_diharapkan' => $program_anggaran_sd->hasil_yang_diharapkan,
			'tempat_dan_waktu_pelaksanaan' => $program_anggaran_sd->tempat_dan_waktu_pelaksanaan,
			'mitra_kegiatan' => $program_anggaran_sd->mitra_kegiatan,
			'kepanitiaan' => $program_anggaran_sd->kepanitiaan,
			'catatan_pengajuan' => $program_anggaran_sd->catatan_pengajuan,
			'catatan_laporan' => $program_anggaran_sd->catatan_laporan,
			'status_pengajuan_text' => $program_anggaran_sd->program_anggaran_status_pengajuan_status,
			'status_pengajuan_color' => $sp_color,
			'status_laporan_text' => $program_anggaran_sd->program_anggaran_status_laporan_status,
			'status_laporan_color' => $sl_color,
			'file_proposal_pengajuan' => $program_anggaran_sd->file_proposal_pengajuan,
			'file_proposal_keuangan' => $program_anggaran_sd->file_proposal_keuangan,
			'file_laporan_kegiatan' => $program_anggaran_sd->file_laporan_kegiatan,
			'file_laporan_keuangan' => $program_anggaran_sd->file_laporan_keuangan,
			'file_kwitansi' => $program_anggaran_sd->file_kwitansi,
		);

		echo json_encode($data);
	}

	/**
	* show all Program Anggaran Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('program_anggaran_sd_list');

		$filters = $this->parse_filters();
		$search  = $this->input->get('q');

		$this->data['multi_filters'] = $filters;

		$this->data['program_anggaran_sds'] = $this->model_program_anggaran_sd->get_filtered($filters, $search, $this->limit_page, $offset);
		$this->data['program_anggaran_sd_counts'] = $this->model_program_anggaran_sd->count_filtered($filters, $search);
		//$this->data['total_proposal'] = $this->mymodel->withquery("select count(id) as total_proposal from program_anggaran_sd where (status_pengajuan != '4' or status_pengajuan != '6') and status_laporan != '4'","row")->total_proposal;
		$this->data['total_proposal'] = $this->mymodel->withquery("select count(id) as total_proposal from program_anggaran_sd where (status_pengajuan = '1' or status_pengajuan = '2' or status_pengajuan = '3' or status_pengajuan = '5') or (status_laporan = '1' or status_laporan = '2' or status_laporan = '3' or status_laporan = '5' or status_laporan IS NULL)","row")->total_proposal;
		//$this->data['total_proposal'] = $this->mymodel->withquery("select count(id) as total_proposal from program_anggaran_sd where (status_laporan != '4')","row")->total_proposal;

		$config = [
			'base_url'     => 'administrator/program_anggaran_sd/index/',
			'total_rows'   => $this->model_program_anggaran_sd->count_filtered($filters, $search),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		//get user role
		$get_role = $this->mymodel->getbywhere("aauth_user_to_group","(group_id = '10') and user_id=",$this->session->userdata('id'),"result");
		if (!empty($get_role)) {
			$array = array('user_keuangan' => true);
			$this->session->set_userdata( $array );
		}else{
			$array = array('user_keuangan' => false);
			$this->session->set_userdata( $array );
		}

		$this->template->title('Program Anggaran SD List');
		$this->render('backend/standart/administrator/program_anggaran_sd/program_anggaran_sd_list', $this->data);
	}
	
	/**
	* Add new program_anggaran_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('program_anggaran_sd_add');

		$this->template->title('Program Anggaran SD New');
		$this->render('backend/standart/administrator/program_anggaran_sd/program_anggaran_sd_add', $this->data);
	}

	/**
	* Add New Program Anggaran Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('program_anggaran_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nomor_program', 'Nomor Program', 'trim|required');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_program', 'Nama Program', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tanggal_program', 'Tanggal Program', 'trim|required');
		$this->form_validation->set_rules('nominal_okr', 'Nominal OKR', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('nominal_pengajuan', 'Nominal Pengajuan', 'trim|required|max_length[20]');
		

		if ($this->form_validation->run()) {
			$program_anggaran_sd_file_proposal_pengajuan_uuid = $this->input->post('program_anggaran_sd_file_proposal_pengajuan_uuid');
			$program_anggaran_sd_file_proposal_pengajuan_name = $this->input->post('program_anggaran_sd_file_proposal_pengajuan_name');
			$program_anggaran_sd_file_proposal_keuangan_uuid = $this->input->post('program_anggaran_sd_file_proposal_keuangan_uuid');
			$program_anggaran_sd_file_proposal_keuangan_name = $this->input->post('program_anggaran_sd_file_proposal_keuangan_name');
		
			//cek OKR
			//hitung max okr yang tersisa
			$get_program = $this->mymodel->getbywhere("program_anggaran","nomor_program",$this->input->post('nomor_program'),"row");
        	$okr = $get_program->nominal_okr;
        	$get_program_by_jenis_kegiatan = $this->mymodel->getbywhere("program_anggaran_sd", "nomor_program = '".$get_program->nomor_program."' and jenis_kegiatan=", $get_program->jenis_kegiatan,"result");
        	$okr_terpakai = 0;
        	foreach ($get_program_by_jenis_kegiatan as $key => $value) {
                        $okr_terpakai = $okr_terpakai + $value->nominal_pengajuan;
        	}
        	$okr = $okr - $okr_terpakai;
            $okr = $okr - (int)$this->input->post('nominal_pengajuan');
            if ($okr < 0) {
            	$okr = 0;
            	$this->data['success'] = false;
            	$this->data['message'] = "Nominal pengajuan melebihi saldo OKR, masukkan nominal lebih rendah";
            	echo json_encode($this->data);
				return false;
            }
            else{
            	$save_data = [
					'nomor_program' => $this->input->post('nomor_program'),
					'jenis_kegiatan' => $this->input->post('jenis_kegiatan'),
					'sub_jenis_kegiatan' => $this->input->post('sub_jenis_kegiatan'),
					'nama_program' => $this->input->post('nama_program'),
					'tahun_ajaran' => $this->input->post('tahun_ajaran'),
					'tanggal_program' => $this->input->post('tanggal_program'),
					'nominal_okr' => $this->input->post('nominal_okr'),
					'nominal_pengajuan' => $this->input->post('nominal_pengajuan'),
					'status_pengajuan' => '1',
					'nominal' => $this->input->post('nominal_pengajuan'),
					'nomor' => $this->input->post('nomor_program'),
					'dasar_pelaksanaan_kegiatan' => $this->input->post('dasar_pelaksanaan_kegiatan'),
					'tema_kegiatan' => $this->input->post('tema_kegiatan'),
					'tujuan_kegiatan' => $this->input->post('tujuan_kegiatan'),
					'gambaran_acara_kegiatan' => $this->input->post('gambaran_acara_kegiatan'),
					'hasil_yang_diharapkan' => $this->input->post('hasil_yang_diharapkan'),
					'tempat_dan_waktu_pelaksanaan' => $this->input->post('tempat_dan_waktu_pelaksanaan'),
					'mitra_kegiatan' => $this->input->post('mitra_kegiatan'),
					'kepanitiaan' =>$this->input->post('kepanitiaan'),
				];
            }

			if (!is_dir(FCPATH . '/uploads/program_anggaran_sd/')) {
				mkdir(FCPATH . '/uploads/program_anggaran_sd/');
			}

			if (!empty($program_anggaran_sd_file_proposal_pengajuan_name)) {
				$program_anggaran_sd_file_proposal_pengajuan_name_copy = date('YmdHis') . '-' . $program_anggaran_sd_file_proposal_pengajuan_name;

				rename(FCPATH . 'uploads/tmp/' . $program_anggaran_sd_file_proposal_pengajuan_uuid . '/' . $program_anggaran_sd_file_proposal_pengajuan_name, 
						FCPATH . 'uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_pengajuan_name_copy);

				if (!is_file(FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_pengajuan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_proposal_pengajuan'] = $program_anggaran_sd_file_proposal_pengajuan_name_copy;
			}
		
			if (!empty($program_anggaran_sd_file_proposal_keuangan_name)) {
				$program_anggaran_sd_file_proposal_keuangan_name_copy = date('YmdHis') . '-' . $program_anggaran_sd_file_proposal_keuangan_name;

				rename(FCPATH . 'uploads/tmp/' . $program_anggaran_sd_file_proposal_keuangan_uuid . '/' . $program_anggaran_sd_file_proposal_keuangan_name, 
						FCPATH . 'uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_keuangan_name_copy);

				if (!is_file(FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_keuangan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_proposal_keuangan'] = $program_anggaran_sd_file_proposal_keuangan_name_copy;
			}

			if (!empty($save_data['file_proposal_pengajuan']) && !empty($save_data['file_proposal_keuangan'])) {
				$save_data['status_pengajuan'] = "2";
				$data_email["file_proposal_keuangan"] = $program_anggaran_sd_file_proposal_keuangan_name_copy;
				$data_email["file_proposal_pengajuan"] = $program_anggaran_sd_file_proposal_pengajuan_name_copy;
				$data["pengajuan_laporan"] = "pengajuan";
			}		
			
			$save_program_anggaran_sd = $this->model_program_anggaran_sd->store($save_data);
            

			if ($save_program_anggaran_sd) {
				if ($save_data['status_pengajuan'] == 2) {
					//kirim email ke Bagian Anggaran
					$save_data["nominal_okr"] = $get_program->nominal_okr;
					$save_data["id_program"] = $save_program_anggaran_sd;
					$save_data["pengajuan_laporan"] = "pengajuan";
					//$data_email["email"] = "onggiedannys@gmail.com";
					$data_email["email"] = "anggaran@labschoolcibubur.sch.id";
					$data_email["id_program"] = $save_program_anggaran_sd;
					$data_email["jenis_anggaran"] = "SD";
					$data_email["data_anggaran"] = $save_data;
	                $this->send_email_file("",$data_email['email'],$data_email);	
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_program_anggaran_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/program_anggaran_sd/edit/' . $save_program_anggaran_sd, 'Edit Program Anggaran Sd'),
						anchor('administrator/program_anggaran_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/program_anggaran_sd/edit/' . $save_program_anggaran_sd, 'Edit Program Anggaran Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/program_anggaran_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/program_anggaran_sd');
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
	* Update view Program Anggaran Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('program_anggaran_sd_update');

		$this->data['program_anggaran_sd'] = $this->model_program_anggaran_sd->find($id);

		$this->template->title('Program Anggaran SD Update');
		$this->render('backend/standart/administrator/program_anggaran_sd/program_anggaran_sd_update', $this->data);
	}

	/**
	* Update Program Anggaran Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nomor_program', 'Nomor Program', 'trim|required');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_program', 'Nama Program', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tanggal_program', 'Tanggal Program', 'trim|required');
		$this->form_validation->set_rules('nominal_pengajuan', 'Nominal Pengajuan', 'trim|required|max_length[20]');
		
		if ($this->form_validation->run()) {
			$program_anggaran_sd_file_proposal_pengajuan_uuid = $this->input->post('program_anggaran_sd_file_proposal_pengajuan_uuid');
			$program_anggaran_sd_file_proposal_pengajuan_name = $this->input->post('program_anggaran_sd_file_proposal_pengajuan_name');
			$program_anggaran_sd_file_proposal_keuangan_uuid = $this->input->post('program_anggaran_sd_file_proposal_keuangan_uuid');
			$program_anggaran_sd_file_proposal_keuangan_name = $this->input->post('program_anggaran_sd_file_proposal_keuangan_name');
			$program_anggaran_sd_file_laporan_kegiatan_uuid = $this->input->post('program_anggaran_sd_file_laporan_kegiatan_uuid');
			$program_anggaran_sd_file_laporan_kegiatan_name = $this->input->post('program_anggaran_sd_file_laporan_kegiatan_name');
			
			//cek OKR
			//hitung max okr yang tersisa
			$get_program = $this->mymodel->getbywhere("program_anggaran_sd","id",$id,"row");
            $okr = $get_program->nominal_okr;
            $this->db->where("nomor_program", $this->input->post('nomor_program'));
            $this->db->where("jenis_kegiatan", $this->input->post('jenis_kegiatan'));
            $this->db->where("id !=", $id);
            $get_program_by_jenis_kegiatan = $this->db->get("program_anggaran_sd")->result();
            $okr_terpakai = 0;
            foreach ($get_program_by_jenis_kegiatan as $key => $value) {
                $okr_terpakai = $okr_terpakai + $value->nominal_pengajuan;
            }
            $okr = $okr - $okr_terpakai;
            $okr = $okr - (int)$this->input->post('nominal_pengajuan');
            if ($okr < 0 && ($get_program->status_pengajuan != 4 && $get_program->status_pengajuan != 6)) {
            	$okr = 0;
            	$this->data['success'] = false;
            	$this->data['message'] = "Nominal pengajuan melebihi saldo OKR, masukkan nominal lebih rendah";
            	echo json_encode($this->data);
				return false;
            }
            else{
				$save_data = [
					'nomor_program' => $this->input->post('nomor_program'),
					'jenis_kegiatan' => $this->input->post('jenis_kegiatan'),
					'sub_jenis_kegiatan' => $this->input->post('sub_jenis_kegiatan'),
					'nama_program' => $this->input->post('nama_program'),
					'tahun_ajaran' => $this->input->post('tahun_ajaran'),
					'tanggal_program' => $this->input->post('tanggal_program'),
					'nominal_pengajuan' => $this->input->post('nominal_pengajuan'),
					'nominal' => $this->input->post('nominal_pengajuan'),
					'nomor' => $this->input->post('nomor_program'),
					'dasar_pelaksanaan_kegiatan' => $this->input->post('dasar_pelaksanaan_kegiatan'),
					'tema_kegiatan' => $this->input->post('tema_kegiatan'),
					'tujuan_kegiatan' => $this->input->post('tujuan_kegiatan'),
					'gambaran_acara_kegiatan' => $this->input->post('gambaran_acara_kegiatan'),
					'hasil_yang_diharapkan' => $this->input->post('hasil_yang_diharapkan'),
					'tempat_dan_waktu_pelaksanaan' => $this->input->post('tempat_dan_waktu_pelaksanaan'),
					'mitra_kegiatan' => $this->input->post('mitra_kegiatan'),
					'kepanitiaan' =>$this->input->post('kepanitiaan'),
				];
            }

			if (!is_dir(FCPATH . '/uploads/program_anggaran_sd/')) {
				mkdir(FCPATH . '/uploads/program_anggaran_sd/');
			}

			if (!empty($program_anggaran_sd_file_proposal_pengajuan_uuid)) {
				$program_anggaran_sd_file_proposal_pengajuan_name_copy = date('YmdHis') . '-' . $program_anggaran_sd_file_proposal_pengajuan_name;

				rename(FCPATH . 'uploads/tmp/' . $program_anggaran_sd_file_proposal_pengajuan_uuid . '/' . $program_anggaran_sd_file_proposal_pengajuan_name, 
						FCPATH . 'uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_pengajuan_name_copy);

				if (!is_file(FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_pengajuan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_proposal_pengajuan'] = $program_anggaran_sd_file_proposal_pengajuan_name_copy;
				$save_data['status_pengajuan'] = 2;
				$save_data['catatan_pengajuan'] = "";
				$data_email["file_proposal_pengajuan"] = $program_anggaran_sd_file_proposal_pengajuan_name_copy;
				$data["pengajuan_laporan"] = "pengajuan";
			}
		
			if (!empty($program_anggaran_sd_file_proposal_keuangan_uuid)) {
				$program_anggaran_sd_file_proposal_keuangan_name_copy = date('YmdHis') . '-' . $program_anggaran_sd_file_proposal_keuangan_name;

				rename(FCPATH . 'uploads/tmp/' . $program_anggaran_sd_file_proposal_keuangan_uuid . '/' . $program_anggaran_sd_file_proposal_keuangan_name, 
						FCPATH . 'uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_keuangan_name_copy);

				if (!is_file(FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd_file_proposal_keuangan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_proposal_keuangan'] = $program_anggaran_sd_file_proposal_keuangan_name_copy;
				$save_data['status_pengajuan'] = 2;
				$save_data['catatan_pengajuan'] = "";
				$data_email["file_proposal_keuangan"] = $program_anggaran_sd_file_proposal_keuangan_name_copy;
				$data["pengajuan_laporan"] = "pengajuan";
			}
		
			if (!empty($program_anggaran_sd_file_laporan_kegiatan_uuid)) {
				$program_anggaran_sd_file_laporan_kegiatan_name_copy = date('YmdHis') . '-' . $program_anggaran_sd_file_laporan_kegiatan_name;

				rename(FCPATH . 'uploads/tmp/' . $program_anggaran_sd_file_laporan_kegiatan_uuid . '/' . $program_anggaran_sd_file_laporan_kegiatan_name, 
						FCPATH . 'uploads/program_anggaran_sd/' . $program_anggaran_sd_file_laporan_kegiatan_name_copy);

				if (!is_file(FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd_file_laporan_kegiatan_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_laporan_kegiatan'] = $program_anggaran_sd_file_laporan_kegiatan_name_copy;
				$save_data['status_laporan'] = 2;
				$save_data['status'] = 2;
				$save_data['catatan_laporan'] = "";
				$data_email["file_laporan_kegiatan"] = $program_anggaran_sd_file_laporan_kegiatan_name_copy;
				$data["pengajuan_laporan"] = "laporan";
			}
		
			$listed_image = [];
			//if (count((array) $this->input->post('program_anggaran_sd_file_laporan_keuangan_name'))) {
			if (!empty($this->input->post('program_anggaran_sd_file_laporan_keuangan_name')[0])) {
				foreach ((array) $_POST['program_anggaran_sd_file_laporan_keuangan_name'] as $idx => $file_name) {
					if (isset($_POST['program_anggaran_sd_file_laporan_keuangan_uuid'][$idx]) AND !empty($_POST['program_anggaran_sd_file_laporan_keuangan_uuid'][$idx])) {
						$program_anggaran_sd_file_laporan_keuangan_name_copy = date('YmdHis') . '-' . $idx . '-' . $file_name;

						rename(FCPATH . 'uploads/tmp/' . $_POST['program_anggaran_sd_file_laporan_keuangan_uuid'][$idx] . '/' .  $file_name, 
								FCPATH . 'uploads/program_anggaran_sd/' . $program_anggaran_sd_file_laporan_keuangan_name_copy);

						$listed_image[] = $program_anggaran_sd_file_laporan_keuangan_name_copy;

						if (!is_file(FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd_file_laporan_keuangan_name_copy)) {
							echo json_encode([
								'success' => false,
								'message' => 'Error uploading file'
								]);
							exit;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
				$save_data['status_laporan'] = 2;
				$save_data['status'] = 2;
				$save_data['catatan_laporan'] = "";
				$data_email["file_laporan_keuangan"] = implode(',',$listed_image);
				$data["pengajuan_laporan"] = "laporan";
			}
			
			$save_data['file_laporan_keuangan'] = implode(',',$listed_image);
		
			if (!empty($this->input->post('tanggal_pencairan'))) {
				$save_data['tanggal_pencairan'] = $this->input->post('tanggal_pencairan');
				$save_data['status_pengajuan'] = "6";
			}
			$save_program_anggaran_sd = $this->model_program_anggaran_sd->change($id, $save_data);

			if ($save_program_anggaran_sd) {
				if (!empty($this->input->post('tanggal_pencairan')) && $this->session->userdata('user_keuangan') == true) {
					$this->cetak_kwitansi_program($id, "SD");
				}
				//if ($save_data['status_pengajuan'] != "6" || ($save_data['status_pengajuan'] == 6 && $save_data['status_laporan'] == 2)) {
					//kirim email ke Bagian Anggaran
					$save_data["nominal_okr"] = $get_program->nominal_okr;
					$save_data["id_program"] = $id;
					$save_data["pengajuan_laporan"] = $data["pengajuan_laporan"];
					//$data_email["email"] = "onggiedannys@gmail.com";
					$data_email["email"] = "anggaran@labschoolcibubur.sch.id";
					$data_email["id_program"] = $id;
					$data_email["jenis_anggaran"] = "SD";
					$data_email["data_anggaran"] = $save_data;
	                $this->send_email_file("",$data_email['email'],$data_email);
				//}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/program_anggaran_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/program_anggaran_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/program_anggaran_sd');
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
	* delete Program Anggaran Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('program_anggaran_sd_delete');

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
            set_message(cclang('has_been_deleted', 'program_anggaran_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'program_anggaran_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Program Anggaran Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('program_anggaran_sd_view');

		$this->data['program_anggaran_sd'] = $this->model_program_anggaran_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Program Anggaran SD Detail');
		$this->render('backend/standart/administrator/program_anggaran_sd/program_anggaran_sd_view', $this->data);
	}
	
	/**
	* delete Program Anggaran Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$program_anggaran_sd = $this->model_program_anggaran_sd->find($id);

		if (!empty($program_anggaran_sd->file_proposal_pengajuan)) {
			$path = FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd->file_proposal_pengajuan;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($program_anggaran_sd->file_proposal_keuangan)) {
			$path = FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd->file_proposal_keuangan;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($program_anggaran_sd->file_laporan_kegiatan)) {
			$path = FCPATH . '/uploads/program_anggaran_sd/' . $program_anggaran_sd->file_laporan_kegiatan;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		if (!empty($program_anggaran_sd->file_laporan_keuangan)) {
			foreach ((array) explode(',', $program_anggaran_sd->file_laporan_keuangan) as $filename) {
				$path = FCPATH . '/uploads/program_anggaran_sd/' . $filename;

				if (is_file($path)) {
					$delete_file = unlink($path);
				}
			}
		}
		
		return $this->model_program_anggaran_sd->remove($id);
	}
	
	/**
	* Upload Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function upload_file_proposal_pengajuan_file()
	{
		if (!$this->is_allowed('program_anggaran_sd_add', false) && !$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'program_anggaran_sd',
		]);
	}

	/**
	* Delete Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function delete_file_proposal_pengajuan_file($uuid)
	{
		if (!$this->is_allowed('program_anggaran_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_proposal_pengajuan', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/'
        ]);
	}

	/**
	* Get Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function get_file_proposal_pengajuan_file($id)
	{
		if (!$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$program_anggaran_sd = $this->model_program_anggaran_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_proposal_pengajuan', 
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/',
            'delete_endpoint'   => 'administrator/program_anggaran_sd/delete_file_proposal_pengajuan_file'
        ]);
	}
	
	/**
	* Upload Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function upload_file_proposal_keuangan_file()
	{
		if (!$this->is_allowed('program_anggaran_sd_add', false) && !$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'program_anggaran_sd',
		]);
	}

	/**
	* Delete Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function delete_file_proposal_keuangan_file($uuid)
	{
		if (!$this->is_allowed('program_anggaran_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_proposal_keuangan', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/'
        ]);
	}

	/**
	* Get Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function get_file_proposal_keuangan_file($id)
	{
		if (!$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$program_anggaran_sd = $this->model_program_anggaran_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_proposal_keuangan', 
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/',
            'delete_endpoint'   => 'administrator/program_anggaran_sd/delete_file_proposal_keuangan_file'
        ]);
	}
	
	/**
	* Upload Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function upload_file_laporan_kegiatan_file()
	{
		if (!$this->is_allowed('program_anggaran_sd_add', false) && !$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'program_anggaran_sd',
		]);
	}

	/**
	* Delete Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function delete_file_laporan_kegiatan_file($uuid)
	{
		if (!$this->is_allowed('program_anggaran_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_laporan_kegiatan', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/'
        ]);
	}

	/**
	* Get Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function get_file_laporan_kegiatan_file($id)
	{
		if (!$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$program_anggaran_sd = $this->model_program_anggaran_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_laporan_kegiatan', 
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/',
            'delete_endpoint'   => 'administrator/program_anggaran_sd/delete_file_laporan_kegiatan_file'
        ]);
	}
	
	
	/**
	* Upload Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function upload_file_laporan_keuangan_file()
	{
		if (!$this->is_allowed('program_anggaran_sd_add', false) && !$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'program_anggaran_sd',
		]);
	}

	/**
	* Delete Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function delete_file_laporan_keuangan_file($uuid)
	{
		if (!$this->is_allowed('program_anggaran_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_laporan_keuangan', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/'
        ]);
	}

	/**
	* Get Image Program Anggaran Sd	* 
	* @return JSON
	*/
	public function get_file_laporan_keuangan_file($id)
	{
		if (!$this->is_allowed('program_anggaran_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$program_anggaran_sd = $this->model_program_anggaran_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_laporan_keuangan', 
            'table_name'        => 'program_anggaran_sd',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/program_anggaran_sd/',
            'delete_endpoint'   => 'administrator/program_anggaran_sd/delete_file_laporan_keuangan_file'
        ]);
	}
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('program_anggaran_sd_export');

		//$this->model_program_anggaran_sd->export('program_anggaran_sd', 'program_anggaran_sd');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search   = ['nomor_program', 'tahun_ajaran', 'nama_program', 'tanggal_program', 'jenis_kegiatan', 'sub_jenis_kegiatan', 'tanggal_pencairan', 'nominal_okr', 'nominal_pengajuan', 'sisa_saldo_okr', 'dasar_pelaksanaan_kegiatan', 'tema_kegiatan', 'tujuan_kegiatan', 'gambaran_acara_kegiatan', 'hasil_yang_diharapkan', 'tempat_dan_waktu_pelaksanaan', 'mitra_kegiatan', 'kepanitiaan', 'file_proposal_pengajuan', 'file_proposal_keuangan', 'status_pengajuan', 'catatan_pengajuan', 'file_laporan_kegiatan', 'file_laporan_keuangan', 'status_laporan', 'catatan_laporan', 'tanggal_pengajuan'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select ps.nomor_program, ps.tahun_ajaran, ps.nama_program, ps.tanggal_program, ps.jenis_kegiatan, ps.sub_jenis_kegiatan, ps.tanggal_pencairan, ps.nominal_okr, ps.nominal_pengajuan, '0' as sisa_saldo_okr, ps.file_proposal_pengajuan, ps.file_proposal_keuangan, sp.status as status_pengajuan, ps.catatan_pengajuan, ps.file_laporan_kegiatan, ps.file_laporan_keuangan, sl.status as status_laporan, ps.catatan_laporan, ps.created_at as tanggal_pengajuan, ps.dasar_pelaksanaan_kegiatan, ps.tema_kegiatan, ps.tujuan_kegiatan, ps.gambaran_acara_kegiatan, ps.hasil_yang_diharapkan, ps.tempat_dan_waktu_pelaksanaan, ps.mitra_kegiatan, ps.kepanitiaan from program_anggaran_sd ps join program_anggaran_status_pengajuan sp on ps.status_pengajuan = sp.id_program_anggaran_status_pengajuan left join program_anggaran_status_laporan sl on ps.status_laporan = sl.id_program_anggaran_status_laporan","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "status_pengajuan"){
                $where .= "(" . "sp.status LIKE '%" . $inputan . "%' )";
            }
            else if($field == "status_laporan"){
                $where .= "(" . "sl.status LIKE '%" . $inputan . "%' )";
            }
            else if($field == "sisa_saldo_okr" || $field == "tanggal_pengajuan"){
            	//tidak dimasukkan
            }
            else{
                $where .= "(" . "ps.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select ps.nomor_program, ps.tahun_ajaran, ps.nama_program, ps.tanggal_program, ps.jenis_kegiatan, ps.sub_jenis_kegiatan, ps.tanggal_pencairan, ps.nominal_okr, ps.nominal_pengajuan, '0' as sisa_saldo_okr, ps.file_proposal_pengajuan, ps.file_proposal_keuangan, sp.status as status_pengajuan, ps.catatan_pengajuan, ps.file_laporan_kegiatan, ps.file_laporan_keuangan, sl.status as status_laporan, ps.catatan_laporan, ps.created_at as tanggal_pengajuan, ps.dasar_pelaksanaan_kegiatan, ps.tema_kegiatan, ps.tujuan_kegiatan, ps.gambaran_acara_kegiatan, ps.hasil_yang_diharapkan, ps.tempat_dan_waktu_pelaksanaan, ps.mitra_kegiatan, ps.kepanitiaan from program_anggaran_sd ps join program_anggaran_status_pengajuan sp on ps.status_pengajuan = sp.id_program_anggaran_status_pengajuan left join program_anggaran_status_laporan sl on ps.status_laporan = sl.id_program_anggaran_status_laporan where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "ps.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "sisa_saldo_okr" || $field == "tanggal_pengajuan"){
		                continue;
		            }
	                else {
	                    $where .= "OR " . "ps.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "sp.status LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "sl.status LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	        }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select ps.nomor_program, ps.tahun_ajaran, ps.nama_program, ps.tanggal_program, ps.jenis_kegiatan, ps.sub_jenis_kegiatan, ps.tanggal_pencairan, ps.nominal_okr, ps.nominal_pengajuan, '0' as sisa_saldo_okr, ps.file_proposal_pengajuan, ps.file_proposal_keuangan, sp.status as status_pengajuan, ps.catatan_pengajuan, ps.file_laporan_kegiatan, ps.file_laporan_keuangan, sl.status as status_laporan, ps.catatan_laporan, ps.created_at as tanggal_pengajuan, ps.dasar_pelaksanaan_kegiatan, ps.tema_kegiatan, ps.tujuan_kegiatan, ps.gambaran_acara_kegiatan, ps.hasil_yang_diharapkan, ps.tempat_dan_waktu_pelaksanaan, ps.mitra_kegiatan, ps.kepanitiaan from program_anggaran_sd ps join program_anggaran_status_pengajuan sp on ps.status_pengajuan = sp.id_program_anggaran_status_pengajuan left join program_anggaran_status_laporan sl on ps.status_laporan = sl.id_program_anggaran_status_laporan where ".$where,"result");
		}
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
		for ($i = 0; $i < count($field_search); $i++)  
		{
			if (strpos($field_search[$i], "created")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL PENGAJUAN'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data as $key => $value) {
			//hitung max okr yang tersisa
            $okr = $value->nominal_okr;
            $get_program_by_jenis_kegiatan = $this->mymodel->getbywhere("program_anggaran_sd", "nomor_program = '".$value->nomor_program."' and jenis_kegiatan=", $value->jenis_kegiatan,"result");
            $okr_terpakai = 0;
            foreach ($get_program_by_jenis_kegiatan as $key_hitung => $value_hitung) {
                $okr_terpakai = $okr_terpakai + $value_hitung->nominal_pengajuan;
            }
            $okr = $okr - $okr_terpakai;
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "file") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/program_anggaran_sd/").$value_data;
		        }
		        if (strpos($kolom, "saldo")) {
		        	$value_data = $okr;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Program Anggaran sd_'.date("Y-m-d Hi").'.xls"'); 
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
		$this->is_allowed('program_anggaran_sd_export');

		$this->model_program_anggaran_sd->pdf('program_anggaran_sd', 'program_anggaran_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('program_anggaran_sd_export');

		$table = $title = 'program_anggaran_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_program_anggaran_sd->find($id);
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

	public function send_email_file($file="",$to='',$data)
    {
      $to = urldecode($to);
      $mail = new PHPMailer;
      // Konfigurasi SMTP
      $mail->isSMTP();
      $mail->SMTPDebug =0;
      // $mail->Host = 'mail.namagz.com';
      $mail->Host = 'smtp.office365.com';
      $mail->SMTPOptions = array(
         'ssl' => array(
           'verify_peer' => false,
           'verify_peer_name' => false,
           'allow_self_signed' => true
          )
      );
      $mail->SMTPAuth = true;
      $mail->Username = 'sekretariat_app@labschoolcibubur.sch.id';
      $mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — kredensial lama dihapus
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->addReplyTo('sekretariat_app@labschoolcibubur.sch.id', 'Labschool Cibubur');
      $mail->setFrom('sekretariat_app@labschoolcibubur.sch.id', 'Labschool Cibubur');

      // Menambahkan penerima
      $mail->addAddress($to);

      // Menambahkan beberapa penerima


      // Subjek email
      $judul = strtoupper($data['data_anggaran']['pengajuan_laporan']).' PROGRAM & ANGGARAN '.$data['jenis_anggaran'];
      $mail->Subject = '[No Reply] '.$judul;

      // Mengatur format email ke HTML
      $mail->isHTML(true);
      //$mail->AddEmbeddedImage('./assets/image/admin/bg_footer_mail_black.png', 'bg_footer_mail_black'); //ini yg dipakai utk
      //$mail->addStringAttachment(file_get_contents(base_url("assets/image/admin/")."bg_footer_mail"), "bg_footer_mail");
      if (!empty($data['file_proposal_pengajuan'])) {
        //$mail->AddAttachment('./uploads/program_anggaran_'.strtolower($data['jenis_anggaran']).'/'.$data['file_proposal_pengajuan']);
        //$mail->AddEmbeddedImage('./uploads/program_anggaran_'.strtolower($data['jenis_anggaran']).'/'.$data->file_proposal_pengajuan, 'file_proposal_pengajuan');
        $data_['file_proposal_pengajuan'] = $data['file_proposal_pengajuan'];
      }
      if (!empty($data['file_proposal_keuangan'])) {
        //$mail->AddAttachment('./uploads/program_anggaran_'.strtolower($data['jenis_anggaran']).'/'.$data['file_proposal_keuangan']);
        $data_['file_proposal_keuangan'] = $data['file_proposal_keuangan'];
      }
      if (!empty($data['file_laporan_kegiatan'])) {
        //$mail->AddAttachment('./uploads/program_anggaran_'.strtolower($data['jenis_anggaran']).'/'.$data['file_laporan_kegiatan']);
        $data_['file_laporan_kegiatan'] = $data['file_laporan_kegiatan'];
      }
      if (!empty($data['file_laporan_keuangan'])) {
      	$list_file = explode(",", $data['file_laporan_keuangan']);
      	for ($i=0; $i < count($list_file); $i++) { 
      		//$mail->AddAttachment('./uploads/program_anggaran_'.strtolower($data['jenis_anggaran']).'/'.$list_file[$i]);
      	}
      	$data_['file_laporan_keuangan'] = $data['file_laporan_keuangan'];
      }

      //Create single sign token validation
      $token = md5(date("YmdHis").$judul);
      $this->mymodel->update("program_anggaran_".strtolower($data['jenis_anggaran']), array("token_confirmation" => $token), "id", $data['id_program']);
      // Konten/isi
       $data_['to'] = $to;
       $data_['judul'] = $judul;
       $data_['jenis_anggaran'] = $data['jenis_anggaran'];
       $data_['pengajuan_laporan'] = $data['data_anggaran']['pengajuan_laporan'];
       $data_['token_confirmation'] = $token;
       $data_['data_anggaran'] = $data['data_anggaran'];
       $mailContent = $this->load->view('template_email_anggaran',$data_,true);
       $mail->Body = $mailContent;
      // Menambahakn lampiran

      // Kirim email
      if(!$mail->send()){
          //echo 'Pesan tidak dapat dikirim.';
          //echo 'Mailer Error: ' . $mail->ErrorInfo;
      }else{
          //echo 'Pesan telah terkirim ';
      }
    }

	public function cetak_kwitansi_program($id_program, $jenjang_program){
	  $this->load->library('HtmlPdf');
	  $this->load->helper('uang_helper');
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
        $data = null;
        //cek data program dari semua jenjang
		$jenjang = "SD";
        $no_urut = 0;
		$program_sd = $this->mymodel->withquery("select id, nomor, tanggal_pencairan from program_anggaran_sd where status_pengajuan = 6 order by tanggal_pencairan ASC","result");
		$program_smp = $this->mymodel->withquery("select id, nomor, tanggal_pencairan from program_anggaran_smp where status_pengajuan = 6 order by tanggal_pencairan ASC","result");
		$program_sma = $this->mymodel->withquery("select id, nomor, tanggal_pencairan from program_anggaran_sma where status_pengajuan = 6 order by tanggal_pencairan ASC","result");
		$array_program = array();
	  	foreach ($program_sd as $key => $value) {
	  		array_push($array_program, array("id" => $value->id, "jenjang" => "SD", "tanggal_pencairan" => $value->tanggal_pencairan));
	  	}
	  	foreach ($program_smp as $key => $value) {
	  		array_push($array_program, array("id" => $value->id, "jenjang" => "SMP", "tanggal_pencairan" => $value->tanggal_pencairan));
	  	}
	  	foreach ($program_sma as $key => $value) {
	  		array_push($array_program, array("id" => $value->id, "jenjang" => "SMA", "tanggal_pencairan" => $value->tanggal_pencairan));
	  	}
		usort($array_program, function($a, $b)
        {
        	if ($a["tanggal_pencairan"] == $b["tanggal_pencairan"])
            	return (0);
        	return (($a["tanggal_pencairan"] < $b["tanggal_pencairan"]) ? -1 : 1);
        });
        foreach ($array_program as $key => $value) {
        	if ($value['id'] == $id_program && $value['jenjang'] == $jenjang_program) {
        		$no_urut = $key+1;
        	}
        }
		$get_pengaturan_program = $this->mymodel->withquery("select * from pengaturan_kwitansi_program where jenjang = '".$jenjang."'","row");
		$get_program = $this->mymodel->withquery("select * from program_anggaran_".strtolower($jenjang)." where id='".$id_program."'","row");
		$tahun = explode("-", $get_program->tahun_ajaran);
		$tahun_ajaran = substr($tahun[0], 2)."-".substr($tahun[1], 2);
		$nomor_kwitansi = $no_urut."/KW/KEU/".$tahun_ajaran."/".date("Y");
        $datas = array(
			"kepala_sekolah_nama" => $get_pengaturan_program->kepala_sekolah_nama,
			"kepala_sekolah_npp" => $get_pengaturan_program->kepala_sekolah_npp,
			"kepala_tu_nama" => $get_pengaturan_program->kepala_tu_nama,
			"kepala_tu_npp" => $get_pengaturan_program->kepala_tu_npp,
			"kepala_sekretariat_nama" => $get_pengaturan_program->kepala_sekretariat_nama,
			"kepala_sekretariat_npp" => $get_pengaturan_program->kepala_sekretariat_npp,
			"nomor_kwitansi" => $nomor_kwitansi,
			"tanggal_pencairan" => $get_program->tanggal_pencairan,
			"nominal_pengajuan" => $get_program->nominal_pengajuan,
			"nominal_pengajuan_teks" => strtoupper(terbilang($get_program->nominal_pengajuan)),
			"jenis_kegiatan" => $get_program->jenis_kegiatan,
			"sub_jenis_kegiatan" => $get_program->sub_jenis_kegiatan,
			"nama_program" => $get_program->nama_program,
			"jenjang" => $jenjang,
			"nomor_program" => $get_program->nomor_program,
			"tahun_ajaran_sekarang" => $get_program->tahun_ajaran,
		);

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_kwitansi_program', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = str_replace('/', '-', $get_program->nama_program)."-".$get_program->id.'.pdf';
		$this->mymodel->update("program_anggaran_sd", array("file_kwitansi" => $nama_file), "id", $id_program);
        //var_dump($data);
        $pdf->Output(FCPATH.'/uploads/program_anggaran_sd/'.$nama_file, 'F');
	}

	public function resend_email($id){
	  	$get_program = $this->mymodel->withquery("select * from program_anggaran_sd where id='".$id."'","row");
	  	$save_data = [
			'nomor_program' => $get_program->nomor_program,
			'jenis_kegiatan' => $get_program->jenis_kegiatan,
			'sub_jenis_kegiatan' => $get_program->sub_jenis_kegiatan,
			'nama_program' => $get_program->nama_program,
			'tahun_ajaran' => $get_program->tahun_ajaran,
			'tanggal_program' => $get_program->tanggal_program,
			'nominal_okr' => $get_program->nominal_okr,
			'nominal_pengajuan' => $get_program->nominal_pengajuan,
			'status_pengajuan' => $get_program->status_pengajuan,
			'nominal' => $get_program->nominal_pengajuan,
			'nomor' => $get_program->nomor_program,
		];
		if (!empty($get_program->file_proposal_pengajuan)) {
			$save_data['file_proposal_pengajuan'] = $get_program->file_proposal_pengajuan;
		}
		if (!empty($get_program->file_proposal_keuangan)) {
			$save_data['file_proposal_keuangan'] = $get_program->file_proposal_keuangan;
		}
		if (!empty($get_program->file_laporan_keuangan)) {
			$save_data['file_laporan_keuangan'] = $get_program->file_laporan_keuangan;
		}
		if (!empty($get_program->file_laporan_kegiatan)) {
			$save_data['file_laporan_kegiatan'] = $get_program->file_laporan_kegiatan;
		}
	  	$save_data["nominal_okr"] = $get_program->nominal_okr;
		$save_data["id_program"] = $id;
		if ($get_program->status_pengajuan == 2) {
			$save_data["pengajuan_laporan"] = "pengajuan";
		}
		else if($get_program->status_laporan == 2){
			$save_data["pengajuan_laporan"] = "laporan";
		}
	  	$save_data["email"] = "anggaran@labschoolcibubur.sch.id";
	  	//$save_data["email"] = "onggiedannys@gmail.com";
		$save_data["id_program"] = $id;
		$save_data["jenis_anggaran"] = "SD";
		$save_data["data_anggaran"] = $save_data;
		$this->send_email_file("",$save_data['email'],$save_data);
		redirect('administrator/program_anggaran_sd','refresh');
	 }

	
}


/* End of file program_anggaran_sd.php */
/* Location: ./application/controllers/administrator/Program Anggaran Sd.php */