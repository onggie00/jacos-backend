<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define( 'API_ACCESS_KEY', 'AAAAX38-FW8:APA91bGoy4cJtX9jf4kfphdyh-1EZ3VFU8GlbVzXmka4-x-c2q6-oAvoltIKeSzoW4Pz8hbUL_MT0EW6NTUctWryTgsAlAmakleTaC-QzwLocy8OaVbswc_RuCC-tUaqPKta3TiYdoJ-' );
define( 'PRIVATE_FIREBASE_KEY', FCPATH . 'labscib-app-c0ca345e64d9.json');
require FCPATH . '/vendor/autoload.php';

/**
*| --------------------------------------------------------------------------
*| Agenda Controller
*| --------------------------------------------------------------------------
*| Agenda site
*|
*/
class Agenda extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_agenda');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Agendas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('agenda_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['agendas'] = $this->model_agenda->get($filter, $field, $this->limit_page, $offset);
		$this->data['agenda_counts'] = $this->model_agenda->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/agenda/index/',
			'total_rows'   => $this->model_agenda->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Agenda List');
		$this->render('backend/standart/administrator/agenda/agenda_list', $this->data);
	}
	
	/**
	* Add new agendas
	*
	*/
	public function add()
	{
		$this->is_allowed('agenda_add');

		$this->template->title('Agenda New');
		$this->render('backend/standart/administrator/agenda/agenda_add', $this->data);
	}

	/**
	* Add New Agendas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('agenda_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('agenda_img_thumbnail_name', 'Img Thumbnail', 'trim|required');
		$this->form_validation->set_rules('agenda_img_agenda_name', 'Img Agenda', 'trim|required');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		

		if ($this->form_validation->run()) {
			$agenda_img_thumbnail_uuid = $this->input->post('agenda_img_thumbnail_uuid');
			$agenda_img_thumbnail_name = $this->input->post('agenda_img_thumbnail_name');
			$agenda_img_agenda_uuid = $this->input->post('agenda_img_agenda_uuid');
			$agenda_img_agenda_name = $this->input->post('agenda_img_agenda_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'deskripsi' => $this->input->post('deskripsi'),
				'created_at' => $this->input->post('created_at'),
				'jenjang' => $this->input->post('jenjang'),
				'id_kategori' => $this->input->post('id_kategori'),
			];

			if (!is_dir(FCPATH . '/uploads/agenda/')) {
				mkdir(FCPATH . '/uploads/agenda/');
			}

			if (!empty($agenda_img_thumbnail_name)) {
				$agenda_img_thumbnail_name_copy = date('YmdHis') . '-' . $agenda_img_thumbnail_name;

				rename(FCPATH . 'uploads/tmp/' . $agenda_img_thumbnail_uuid . '/' . $agenda_img_thumbnail_name, 
						FCPATH . 'uploads/agenda/' . $agenda_img_thumbnail_name_copy);

				if (!is_file(FCPATH . '/uploads/agenda/' . $agenda_img_thumbnail_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_thumbnail'] = $agenda_img_thumbnail_name_copy;
			}
		
			if (!empty($agenda_img_agenda_name)) {
				$agenda_img_agenda_name_copy = date('YmdHis') . '-' . $agenda_img_agenda_name;

				rename(FCPATH . 'uploads/tmp/' . $agenda_img_agenda_uuid . '/' . $agenda_img_agenda_name, 
						FCPATH . 'uploads/agenda/' . $agenda_img_agenda_name_copy);

				if (!is_file(FCPATH . '/uploads/agenda/' . $agenda_img_agenda_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_agenda'] = $agenda_img_agenda_name_copy;
			}
		
			
			$save_agenda = $this->model_agenda->store($save_data);
            

			if ($save_agenda) {
				//send notification firebase
				//SMA
				if ($save_data['jenjang'] == "sma") {
					$get_siswa_sma = $this->mymodel->withquery("select id_siswa_sma_aktif, device_id_siswa, device_id_ortu from siswa_sma_aktif where device_id_siswa != '' ","result");
					foreach ($get_siswa_sma as $key => $value) {
						//$this->send_notif($get_fcm->fcm_id ,$id_announcement);
						$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_siswa, array("id_agenda"=>$save_agenda) );
						if (!empty($value->device_id_ortu)) {
							$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_ortu, array("id_agenda"=>$save_agenda) );
						}
					}
				}
				else if ($save_data['jenjang'] == "ft") {
					//FT
					$get_siswa_ft = $this->mymodel->withquery("select id_siswa_ft_aktif, device_id_siswa, device_id_ortu from siswa_ft_aktif where device_id_siswa != '' ","result");
					foreach ($get_siswa_ft as $key => $value) {
						$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_siswa, array("id_agenda"=>$save_agenda) );
						if (!empty($value->device_id_ortu)) {
							$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_ortu, array("id_agenda"=>$save_agenda) );
						}
					}
				}
				else if ($save_data['jenjang'] == "smp") {
					//SMP
					$get_siswa_smp = $this->mymodel->withquery("select id_siswa_smp_aktif, device_id_siswa, device_id_ortu from siswa_smp_aktif where device_id_siswa != '' ","result");
					foreach ($get_siswa_smp as $key => $value) {
						$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_siswa, array("id_agenda"=>$save_agenda) );
						if (!empty($value->device_id_ortu)) {
							$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_ortu, array("id_agenda"=>$save_agenda) );
						}
					}
				}
				else if ($save_data['jenjang'] == "sd") {
					//SD
					$get_siswa_sd = $this->mymodel->withquery("select id_siswa_sd_aktif, device_id_siswa, device_id_ortu from siswa_sd_aktif where device_id_siswa != '' ","result");
					foreach ($get_siswa_sd as $key => $value) {
						$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_siswa, array("id_agenda"=>$save_agenda) );
						if (!empty($value->device_id_ortu)) {
							$this->send_notif("Agenda baru ".$save_data['judul'],strip_tags($save_data['deskripsi']), $value->device_id_ortu, array("id_agenda"=>$save_agenda) );
						}
					}
				}
				
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_agenda;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/agenda/edit/' . $save_agenda, 'Edit Agenda'),
						anchor('administrator/agenda', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/agenda/edit/' . $save_agenda, 'Edit Agenda')
					]), 'success');

            		$this->data['success'] = true;
					//$this->data['redirect'] = base_url('administrator/agenda');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					//$this->data['redirect'] = base_url('administrator/agenda');
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
	* Update view Agendas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('agenda_update');

		$this->data['agenda'] = $this->model_agenda->find($id);

		$this->template->title('Agenda Update');
		$this->render('backend/standart/administrator/agenda/agenda_update', $this->data);
	}

	/**
	* Update Agendas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('agenda_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|required');
		$this->form_validation->set_rules('agenda_img_thumbnail_name', 'Img Thumbnail', 'trim|required');
		$this->form_validation->set_rules('agenda_img_agenda_name', 'Img Agenda', 'trim|required');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		
		if ($this->form_validation->run()) {
			$agenda_img_thumbnail_uuid = $this->input->post('agenda_img_thumbnail_uuid');
			$agenda_img_thumbnail_name = $this->input->post('agenda_img_thumbnail_name');
			$agenda_img_agenda_uuid = $this->input->post('agenda_img_agenda_uuid');
			$agenda_img_agenda_name = $this->input->post('agenda_img_agenda_name');
		
			$save_data = [
				'judul' => $this->input->post('judul'),
				'deskripsi' => $this->input->post('deskripsi'),
				'created_at' => $this->input->post('created_at'),
				'jenjang' => $this->input->post('jenjang'),
				'id_kategori' => $this->input->post('id_kategori'),
			];

			if (!is_dir(FCPATH . '/uploads/agenda/')) {
				mkdir(FCPATH . '/uploads/agenda/');
			}

			if (!empty($agenda_img_thumbnail_uuid)) {
				$agenda_img_thumbnail_name_copy = date('YmdHis') . '-' . $agenda_img_thumbnail_name;

				rename(FCPATH . 'uploads/tmp/' . $agenda_img_thumbnail_uuid . '/' . $agenda_img_thumbnail_name, 
						FCPATH . 'uploads/agenda/' . $agenda_img_thumbnail_name_copy);

				if (!is_file(FCPATH . '/uploads/agenda/' . $agenda_img_thumbnail_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_thumbnail'] = $agenda_img_thumbnail_name_copy;
			}
		
			if (!empty($agenda_img_agenda_uuid)) {
				$agenda_img_agenda_name_copy = date('YmdHis') . '-' . $agenda_img_agenda_name;

				rename(FCPATH . 'uploads/tmp/' . $agenda_img_agenda_uuid . '/' . $agenda_img_agenda_name, 
						FCPATH . 'uploads/agenda/' . $agenda_img_agenda_name_copy);

				if (!is_file(FCPATH . '/uploads/agenda/' . $agenda_img_agenda_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['img_agenda'] = $agenda_img_agenda_name_copy;
			}
		
			
			$save_agenda = $this->model_agenda->change($id, $save_data);

			if ($save_agenda) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/agenda', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/agenda');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/agenda');
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
	* delete Agendas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('agenda_delete');

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
            set_message(cclang('has_been_deleted', 'agenda'), 'success');
        } else {
            set_message(cclang('error_delete', 'agenda'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Agendas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('agenda_view');

		$this->data['agenda'] = $this->model_agenda->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Agenda Detail');
		$this->render('backend/standart/administrator/agenda/agenda_view', $this->data);
	}
	
	/**
	* delete Agendas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$agenda = $this->model_agenda->find($id);

		if (!empty($agenda->img_thumbnail)) {
			$path = FCPATH . '/uploads/agenda/' . $agenda->img_thumbnail;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($agenda->img_agenda)) {
			$path = FCPATH . '/uploads/agenda/' . $agenda->img_agenda;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_agenda->remove($id);
	}
	
	/**
	* Upload Image Agenda	* 
	* @return JSON
	*/
	public function upload_img_thumbnail_file()
	{
		if (!$this->is_allowed('agenda_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'agenda',
		]);
	}

	/**
	* Delete Image Agenda	* 
	* @return JSON
	*/
	public function delete_img_thumbnail_file($uuid)
	{
		if (!$this->is_allowed('agenda_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'img_thumbnail', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'agenda',
            'primary_key'       => 'id_agenda',
            'upload_path'       => 'uploads/agenda/'
        ]);
	}

	/**
	* Get Image Agenda	* 
	* @return JSON
	*/
	public function get_img_thumbnail_file($id)
	{
		if (!$this->is_allowed('agenda_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$agenda = $this->model_agenda->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_thumbnail', 
            'table_name'        => 'agenda',
            'primary_key'       => 'id_agenda',
            'upload_path'       => 'uploads/agenda/',
            'delete_endpoint'   => 'administrator/agenda/delete_img_thumbnail_file'
        ]);
	}
	
	/**
	* Upload Image Agenda	* 
	* @return JSON
	*/
	public function upload_img_agenda_file()
	{
		if (!$this->is_allowed('agenda_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'agenda',
		]);
	}

	/**
	* Delete Image Agenda	* 
	* @return JSON
	*/
	public function delete_img_agenda_file($uuid)
	{
		if (!$this->is_allowed('agenda_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'img_agenda', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'agenda',
            'primary_key'       => 'id_agenda',
            'upload_path'       => 'uploads/agenda/'
        ]);
	}

	/**
	* Get Image Agenda	* 
	* @return JSON
	*/
	public function get_img_agenda_file($id)
	{
		if (!$this->is_allowed('agenda_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$agenda = $this->model_agenda->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'img_agenda', 
            'table_name'        => 'agenda',
            'primary_key'       => 'id_agenda',
            'upload_path'       => 'uploads/agenda/',
            'delete_endpoint'   => 'administrator/agenda/delete_img_agenda_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('agenda_export');

		$this->model_agenda->export('agenda', 'agenda');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('agenda_export');

		$this->model_agenda->pdf('agenda', 'agenda');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('agenda_export');

		$table = $title = 'agenda';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_agenda->find($id);
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

	public function send_notif_legacy($title,$desc,$id_fcm,$data)
	{
		$Msg = array(
			'body' => $desc,
			'title' => $title
		);
		$fcmFields = array(
			'to' => $id_fcm,
			'notification' => $Msg,
			 'data'=>$data
		);
		$headers = array(
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		);
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
		$result = curl_exec($ch );
		curl_close( $ch );

		$cek_respon = explode(',',$result);
		$berhasil = substr($cek_respon[1],strpos($cek_respon[1],':')+1);
		//echo $result."\n\n";
	}

	public function send_notif($title,$desc,$fcm_id,$data){
        //$firebaseService = new FirebaseService();
        $token = $this->getAccessToken();
        $query = "select * from agenda where id_agenda = '".$data['id_agenda']."'";
        $get_data = $this->mymodel->withquery($query,"row");
        $data = [
            'token' => $fcm_id,
            'title' => $title,
            'body' => $desc
        ];
        $data_notification = array(
            'message' => array(
                'token' => $fcm_id,
                'notification' => array(
                    'title' => $data['title'],
                    'body' => $data['body']
                ),
                'data' => array(
                	"img_thumbnail" => base_url()."uploads/agenda/".$get_data->img_thumbnail,
                	"id_agenda" => $get_data->id_agenda,
                	"id_kategori" => $get_data->id_kategori
                )
                //'data' => array('route' => 'detailAnnouncement?idAnnouncement='.$data['id_agenda'])
            )
        );
        $send_notif = $this->firebase_send_notif($data);
        //$result = $firebaseService->sendMessage($fcm_id, $data['title'] ?? '', $data['body'] ?? '', array('route' => 'detailAnnouncement?idAnnouncement='.$data['id_agenda']));
        //print_r($token);
    }

	function getAccessToken(){

        //require "google-api-php-client/vendor/autoload.php";
        $client= new \Google_Client();
        //$client= new Google\Client();
        $client->setAuthConfig(PRIVATE_FIREBASE_KEY);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        //$client->fetchAccessTokenWithAssertion();
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        //result array(access_token, expires_in, token_type, created)
        $result=$token['access_token'];
                
        return $result;
    
    }
    function firebase_send_notif($data){
        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json'
        ];
    
        $fields = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body']
                ]
            ]
        ];
    
        $fields = json_encode($fields);
        $url = 'https://fcm.googleapis.com/v1/projects/labscib-app/messages:send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $result = curl_exec($ch);
        curl_close($ch);
        /*print_r($result);
        echo "<br/><br/>";*/
    }

	
}


/* End of file agenda.php */
/* Location: ./application/controllers/administrator/Agenda.php */