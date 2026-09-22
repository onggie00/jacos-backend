<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . '/application/controllers/phpmailer/PHPMailerAutoload.php';
ob_start();
class Send_email_labschool extends MY_Controller {

    /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/user_guide/general/urls.html
   */
    public function index()
    {
        $nama_lengkap = $this->input->post('nama_lengkap');
        $email_ms_office = $this->input->post('email_ms_office');
        $password = $this->input->post('password');
        $pesan_pembuka = $this->input->post('pesan_pembuka');
        $pesan = $this->input->post('pesan');
        $pesan_penutup = $this->input->post('pesan_penutup');
        $judul = $this->input->post('judul');
        $to = $this->input->post('to');
        if (strpos($pesan, 'reset password') !== false) {
            // $pesan_text = $pesan;
            $pesan .= "<br/><table border='0'>
				<tr>
					<td>Nama</td>
					<td>:</td>
					<td>".$nama_lengkap."</td>
				</tr>
				<tr>
					<td>Email Microsoft</td>
					<td>:</td>
					<td>".$email_ms_office."</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>:</td>
					<td>".$password."</td>
				</tr>
			</table>";
        }
        $data = array(
            'nama_lengkap' => $nama_lengkap,
            'email_ms_office' => $email_ms_office,
            'password' => $password,
            'pesan_pembuka' => $pesan_pembuka,
            'pesan' => $pesan,
            'pesan_penutup' => $pesan_penutup,
            'judul' => $judul,
            'to' => $to,
        );
        $kirim_email = $this->send_email_file(null,$to,$data);
        
        echo json_encode(array('status' => true, 'pesan' => 'Email berhasil dikirim', 'data' => $data));
        /* if ($kirim_email) {
            echo json_encode(array('status' => true, 'pesan' => 'Email berhasil dikirim', 'data' => $data));
        }
        else{
            echo json_encode(array('status' => false, 'pesan' => 'Email gagal dikirim', 'data' => $data));
        } */
    }

    public function send_email_file($file="",$to='',$data)
    {
        $to = urldecode($to);
        $mail = new PHPMailer;
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->SMTPDebug =0;
        $mail->Host = 'smtp.office365.com'; // [JACOS] TODO(manual): ganti SMTP host milik Jacos
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@labschoolcibubur.sch.id'; // [JACOS] TODO(manual): ganti akun SMTP Jacos
        $mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — JANGAN hardcode kredensial lama lagi
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
        $mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');

        // Menambahkan penerima
        $mail->addAddress($to);

        // Menambahkan beberapa penerima


        // Subjek email
        $mail->Subject = '[No Reply] '.$data['judul'];

        // Mengatur format email ke HTML
        $mail->isHTML(true);
        // Konten/isi
        $data_['to'] = $to;
        $data_['nama_lengkap'] = $data['nama_lengkap'];
        $data_['pesan'] = $data['pesan'];
        $data_['judul'] = $data['judul'];
        $data_['pesan_pembuka'] = $data['pesan_pembuka'];
        $data_['pesan_penutup'] = $data['pesan_penutup'];
        $data_['email_ms_office'] = $data['email_ms_office'];
        $data_['password'] = $data['password'];
        $mailContent = $this->load->view('template_send_email',$data_,true);
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

}