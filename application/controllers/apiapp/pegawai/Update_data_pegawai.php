<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with, x-api-key, Accept, Content-Type, User-Agent');
date_default_timezone_set('Asia/Jakarta');
define( 'CLIENT_ID', 'a14698c4-b98b-4573-a6b7-13110ebf1335');
define( 'TENANT_ID', '10fc2260-5c60-4800-af0e-789640a374a3'); // Only for user with mail Labschoolcibubur or just fill common to accept all microsoft mail
define( 'SECRET_ID', '017ee22e-7091-433f-93c4-0897a7dcde5b');
define( 'GRAPH_USER_SCOPES', 'user.read mail.read mail.send offline_access User.ReadWrite');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Update_data_pegawai extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }

  function index_post()
  {
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token'])) $token = $headers['x-token'];

    $id = $this->post('id_pegawai');
    $pegawai = "pegawai";

    // Ambil data lama dari tabel pegawai (bukan activity_user)
    $old_data = $this->mymodel->getbywhere($pegawai, 'id_pegawai', $id, 'row');

    $data = array(
        'nama_lengkap' => $this->post('nama_lengkap'),
        'nik' => $this->post('nik'),
        'nuptk' => $this->post('nuptk'),
        // 'npp' => $this->post('npp'),
        'npwp' => $this->post('npwp'),
        'alamat' => $this->post('alamat'),
        'agama' => $this->post('agama'),
        'jenis_kelamin' => $this->post('jenis_kelamin'),
        'status_menikah' => $this->post('status_menikah'),
        'jumlah_anak' => $this->post('jumlah_anak'),
        'no_telp' => $this->post('no_telp'),
        'email' => $this->post('email'),
        'id_posisi' => $this->post('id_posisi'),
        'unit' => $this->post('unit'),
        'id_mapel' => $this->post('id_mapel'),
        'status_kepegawaian' => $this->post('status_kepegawaian'),
        'informasi_kepala_pimpinan' => $this->post('informasi_kepala_pimpinan'),
        'no_kk' => $this->post('no_kk'),
        'tempat_lahir' => $this->post('tempat_lahir'),
        'tgl_lahir' => $this->post('tgl_lahir'),
        'keterangan_jabatan' => $this->post('keterangan_jabatan'),
        'jam_ajar' => $this->post('jam_ajar'),
        'pendidikan_terakhir' => $this->post('pendidikan_terakhir'),
        'universitas' => $this->post('universitas'),
        'jurusan' => $this->post('jurusan'),
        'tahun_lulus' => $this->post('tahun_lulus'),
    );

    // Foto profil - simpan ke folder berdasarkan NPP
    if (!empty($_FILES['foto_profil']['name'])) {
      $npp = !empty($data['npp']) ? $data['npp'] : ($old_data->npp ?? 'unknown');
      $uploaddir = './uploads/pegawai/' . $npp . '/';

      // Buat folder jika belum ada
      if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0775, true);
      }

      $img = explode('.', $_FILES['foto_profil']['name']);
      $extension = end($img);
      $file_name = md5(date('y-m-d h:i:s') . $_FILES['foto_profil']['name']) . "." . $extension;
      $uploadfile = $uploaddir . $file_name;

      if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadfile)) {
        $data['foto_profil'] = $npp . '/' . $file_name;
      }
    }

    // Hapus field yang kosong/null agar tidak menimpa data lama dengan kosong
    $data = array_filter($data, function($v) {
      return $v !== null && $v !== '';
    });

    // Bandingkan dengan data lama, catat perubahan saja
    $changes = [];
    if ($old_data) {
      foreach ($data as $field => $new_val) {
        $old_val = $old_data->$field ?? '';
        if ((string)$old_val !== (string)$new_val) {
          $changes[$field] = [
            'lama' => $old_val,
            'baru' => $new_val
          ];
        }
      }
    }

    $update_guru = -1;
    if (!empty($data)) {
      $update_guru = $this->mymodel->update($pegawai, $data, 'id_pegawai', $id);
    }

    // Simpan log aktivitas jika ada perubahan dan update berhasil
    if ($update_guru != -1 && !empty($changes)) {
      $updated_by = $old_data->nama_lengkap ?? ($data['nama_lengkap'] ?? 'pegawai');
      $now = date('Y-m-d H:i:s');

      $this->mymodel->insertid('activity_user', [
        'endpoint'    => '/apiapp/pegawai/update_data_pegawai',
        'keterangan'  => 'update_data_pegawai',
        'value'       => json_encode($changes, JSON_UNESCAPED_UNICODE),
        'ip_address'  => $this->input->ip_address(),
        'created_at'  => $now,
        'updated_by'  => $updated_by,
      ]);

      // Kirim notifikasi email ke sekretariat
      try {
        $var_send_email = $this->_send_change_email($updated_by, $changes, $now);
      } catch (Exception $e) {
        echo "Gagal kirim email notifikasi perubahan pegawai:" .$e->getMessage();
        // log_message('error', 'Gagal kirim email notifikasi perubahan pegawai: ' . $e->getMessage());
      }
    }

    if ($update_guru == -1) {
      $msg = array('status' => 0, 'message' => 'Gagal update data pegawai', 'data' => array());
    } else {
      $msg = array('status' => 1, 'message' => 'Berhasil update data', 'data' => []);
    }
    $this->response($msg);
  }

  public function update_microsoft($data){
      $appid = CLIENT_ID;
	    $tennantid = TENANT_ID;
	    //$secret = SECRET_ID;
	    //$_SESSION['state']=session_id();
	    $login_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/authorize";
	    $params = array(
	      'client_id' => $appid,
	      'redirect_uri' => base_url("administrator/auth/oauth_login"),
	      'response_type' => 'token',
	      'response_mode' => 'form_post',
	      'scope' => 'user.read openid profile offline_access',
		  'prompt' => 'select_account'
	      //'state' =>$_SESSION['state']
	    );
  }
  
  public function send_email_file($file = "", $to = '', $data)
	{
		$to = urldecode($to);
		$attachments = array();
		$this->load->library('resend_mailer');

		// Menambahkan beberapa penerima


		// Subjek email
		$subject = '[No Reply] SLIP PEMBAYARAN PENDAFTARAN ULANG SISWA BARU';

		// Mengatur format email ke HTML
		//$mail->AddEmbeddedImage('./assets/image/admin/bg_footer_mail_black.png', 'bg_footer_mail_black'); //ini yg dipakai utk
		//$mail->addStringAttachment(file_get_contents(base_url("assets/image/admin/")."bg_footer_mail"), "bg_footer_mail");
		if (!empty($data['slip_pembayaran'])) {
			$this->resend_mailer->add_attachment($attachments, FCPATH.'uploads/slip_pembayaran/' . $data['slip_pembayaran']);
			//$mail->AddEmbeddedImage('./uploads/slip_pembayaran/'.$data->slip_pembayaran, 'slip_pembayaran');
		}
		// Konten/isi
		$data_['to'] = $to;
		$data_['nama_lengkap'] = $data['nama_lengkap'];
		$data_['jenjang'] = $data['jenjang'];
		$data_['nama_panitia'] = $data['nama_panitia'];
		$data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
		$data_['transaksi'] = $data['transaksi'];
		$data_['daftar_ulang'] = "1";
		$mailContent = $this->load->view('template_email_pendaftaran', $data_, true);
		// Menambahakn lampiran

		// Kirim email
		$this->resend_mailer->send($to, $subject, $mailContent, $attachments);
	}

	/**
	 * Kirim email notifikasi perubahan data pegawai ke sekretariat
	 */
	private function _send_change_email($updated_by, $changes, $datetime)
	{
		// $to = 'sekretariat2@jacos.id';
    $to = 'onggiedannys@gmail.com';
		$subject = '[No Reply] Perubahan Profile Data Pegawai';

		// Format table rows
		$rows = '';
		$no = 1;
		foreach ($changes as $field => $val) {
			$label = ucwords(str_replace('_', ' ', $field));
			$old = $val['lama'] ?? '-';
			$new = $val['baru'] ?? '-';
			$rows .= '<tr>
				<td style="padding:8px;border:1px solid #D6E8F5;text-align:center;">' . $no++ . '</td>
				<td style="padding:8px;border:1px solid #D6E8F5;font-weight:600;">' . htmlspecialchars($label) . '</td>
				<td style="padding:8px;border:1px solid #D6E8F5;">' . htmlspecialchars($old) . '</td>
				<td style="padding:8px;border:1px solid #D6E8F5;color:#2e7d32;font-weight:600;">' . htmlspecialchars($new) . '</td>
			</tr>';
		}

		$body = $this->load->view('email/_header', array('email_title' => 'Notifikasi Perubahan Data Pegawai'), true);
		$body .= '
			<p style="margin:0 0 5px;"><strong>Nama:</strong> ' . htmlspecialchars($updated_by) . '</p>
			<p style="margin:0 0 15px;"><strong>Waktu:</strong> ' . htmlspecialchars($datetime) . '</p>
			<table style="width:100%;border-collapse:collapse;margin-bottom:15px;font-size:13px;">
				<thead>
					<tr style="background-color:#F5FAFD;color:#5E7A8E;">
						<th style="padding:8px;border:1px solid #D6E8F5;width:40px;">No</th>
						<th style="padding:8px;border:1px solid #D6E8F5;">Field</th>
						<th style="padding:8px;border:1px solid #D6E8F5;">Sebelumnya</th>
						<th style="padding:8px;border:1px solid #D6E8F5;">Sesudah</th>
					</tr>
				</thead>
				<tbody>' . $rows . '</tbody>
			</table>
			<p style="margin:0;font-size:12px;color:#999;">Email ini dikirim otomatis oleh sistem.</p>
		';
		$body .= $this->load->view('email/_footer', array(), true);

		$attachments = array();
		$this->load->library('resend_mailer');
		$this->resend_mailer->send($to, $subject, $body, $attachments);
	}
  
}
