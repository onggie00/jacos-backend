<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Forget_password extends MY_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->library('Forget_password_policy');
  }

  public function index()
  {
    $form_token = md5(uniqid((string) mt_rand(), true));
    $this->session->set_userdata('forget_password_form_token', $form_token);
    $this->load->view(
      'forget_password',
      array('form_token' => $form_token)
    );
  }

  public function validate()
  {
    $form_token = (string) $this->input->post('form_token');
    $session_token = (string) $this->session->userdata('forget_password_form_token');
    $this->session->unset_userdata('forget_password_form_token');
    $this->session->unset_userdata('forget_password_context');

    if ($form_token === '' || $session_token === '' || $form_token !== $session_token) {
      $this->fail_and_redirect('Sesi form tidak valid. Silakan ulangi proses.');
      return;
    }

    $role = trim((string) $this->input->post('role'));
    if (!$this->forget_password_policy->is_valid_role($role)) {
      $this->fail_and_redirect('Role akun tidak valid.');
      return;
    }

    $identity_field = $this->forget_password_policy->identity_field($role);
    $identity = trim((string) $this->input->post($identity_field));
    if ($identity === '') {
      $this->fail_and_redirect(strtoupper($identity_field) . ' wajib diisi.');
      return;
    }

    if ($role === 'student' || $role === 'parent') {
      $student = $this->find_student_account($identity, $role);
      if (empty($student)) {
        $this->fail_and_redirect('Akun tidak ditemukan.');
        return;
      }

      if ($this->forget_password_policy->normalize_birth_date($student->tgl_lahir) === false) {
        $this->fail_and_redirect('Tanggal lahir siswa belum terdaftar dengan benar. Hubungi admin.');
        return;
      }

      $validation_token = md5(uniqid((string) mt_rand(), true));
      $this->session->set_userdata(
        'forget_password_context',
        array(
          'role' => $role,
          'email' => $student->account_email,
          'jenjang' => $student->jenjang,
          'id_siswa' => $student->id_siswa,
          'validation_token' => $validation_token,
          'attempts' => 0
        )
      );

      $this->load->view(
        'forget_password_validation',
        array(
          'role' => $role,
          'validation_field' => 'tgl_lahir',
          'validation_token' => $validation_token
        )
      );
      return;
    }

    $account = $this->find_non_student_account($identity, $role);
    if (empty($account)) {
      $this->fail_and_redirect(
        $this->identity_label($identity_field) . ' ' . $this->role_label($role) . ' tidak ditemukan.'
      );
      return;
    }

    if (empty($account->email_ms_office)) {
      $this->fail_and_redirect(
        'Akun ' . $this->role_label($role) . ' (NPP ' . html_escape($identity) .
        ') belum memiliki email Microsoft. Hubungi admin untuk setup akun.'
      );
      return;
    }

    $validation_field = $this->forget_password_policy->validation_field($role);
    if ($validation_field !== false) {
      $validation_token = md5(uniqid((string) mt_rand(), true));
      $this->session->set_userdata(
        'forget_password_context',
        array(
          'role' => $role,
          'email' => $account->email_ms_office,
          'table' => $account->table,
          'validation_token' => $validation_token,
          'attempts' => 0
        )
      );
      $this->load->view(
        'forget_password_validation',
        array(
          'role' => $role,
          'validation_field' => $validation_field,
          'validation_token' => $validation_token
        )
      );
      return;
    }

    $this->do_reset($account->email_ms_office, $role);
    redirect('forget_password');
  }

  private function find_student_account($nis, $role)
  {
    $tables = array('ft', 'sma', 'smp', 'sd');
    $email_field = $role === 'parent' ? 'email_ms_office_ortu' : 'email_ms_office';

    foreach ($tables as $jenjang) {
      $row = $this->mymodel->withquery(
        "SELECT siswa.id_siswa_{$jenjang} AS id_siswa,
                siswa.tgl_lahir,
                siswa." . $email_field . " AS account_email
         FROM siswa_{$jenjang} siswa
         JOIN siswa_{$jenjang}_aktif aktif
           ON aktif.id_siswa_{$jenjang} = siswa.id_siswa_{$jenjang}
         WHERE aktif.nis = " . $this->db->escape($nis) . "
           AND aktif.is_active = 1
           AND siswa.deleted_at IS NULL
         ORDER BY aktif.id_siswa_{$jenjang}_aktif DESC",
        'row'
      );

      if (!empty($row)) {
        if (empty($row->account_email)) {
          return null;
        }
        $row->jenjang = $jenjang;
        return $row;
      }
    }

    return null;
  }

  private function find_non_student_account($identity, $role)
  {
    $role_tables = array(
      'teacher' => array('guru_ft', 'guru_sma', 'guru_smp', 'guru_sd'),
      'staff' => array('pegawai'),
      'leader' => array('pimpinan_sma', 'pimpinan_smp', 'pimpinan_sd')
    );

    if (!isset($role_tables[$role])) {
      return null;
    }

    $lookup_field = $role === 'leader' ? 'npp' : 'email_ms_office';
    foreach ($role_tables[$role] as $table) {
      $deleted_filter = $role === 'leader' ? '' : ' AND deleted_at IS NULL';
      $row = $this->mymodel->withquery(
        'SELECT npp, email_ms_office FROM ' . $table .
        ' WHERE ' . $lookup_field . ' = ' . $this->db->escape($identity) . $deleted_filter,
        'row'
      );

      if (!empty($row)) {
        $row->table = $table;
        return $row;
      }
    }

    return null;
  }

  private function get_non_student_by_context($context)
  {
    $role_tables = array(
      'teacher' => array('guru_ft', 'guru_sma', 'guru_smp', 'guru_sd'),
      'staff' => array('pegawai')
    );
    $role = isset($context['role']) ? $context['role'] : '';
    $table = isset($context['table']) ? $context['table'] : '';
    $email = isset($context['email']) ? $context['email'] : '';

    if (
      !isset($role_tables[$role]) ||
      !in_array($table, $role_tables[$role], true) ||
      $email === ''
    ) {
      return null;
    }

    return $this->mymodel->withquery(
      'SELECT npp, email_ms_office FROM ' . $table .
      ' WHERE email_ms_office = ' . $this->db->escape($email) .
      ' AND deleted_at IS NULL',
      'row'
    );
  }

  private function get_student_by_context($context)
  {
    $jenjang = isset($context['jenjang']) ? $context['jenjang'] : '';
    $id_siswa = isset($context['id_siswa']) ? (int) $context['id_siswa'] : 0;
    $role = isset($context['role']) ? $context['role'] : '';
    if (!in_array($jenjang, array('ft', 'sma', 'smp', 'sd'), true) || $id_siswa < 1) {
      return null;
    }

    $email_field = $role === 'parent' ? 'email_ms_office_ortu' : 'email_ms_office';
    return $this->mymodel->withquery(
      'SELECT tgl_lahir, ' . $email_field . ' AS account_email
       FROM siswa_' . $jenjang . '
       WHERE id_siswa_' . $jenjang . ' = ' . $id_siswa . '
         AND ' . $email_field . ' = ' . $this->db->escape($context['email']) . '
         AND deleted_at IS NULL',
      'row'
    );
  }

  private function role_label($role)
  {
    $labels = array(
      'teacher' => 'guru',
      'staff' => 'staff',
      'leader' => 'pimpinan'
    );

    return isset($labels[$role]) ? $labels[$role] : 'user';
  }

  private function identity_label($identity_field)
  {
    $labels = array(
      'nis' => 'NIS',
      'email' => 'Email',
      'npp' => 'NPP'
    );

    return isset($labels[$identity_field]) ? $labels[$identity_field] : 'Identitas';
  }

  private function fail_and_redirect($message)
  {
    $this->session->set_flashdata('failed', $message);
    redirect('forget_password');
  }

  private function show_validation_error($context, $message)
  {
    $context['attempts'] = isset($context['attempts']) ? (int) $context['attempts'] + 1 : 1;
    if ($context['attempts'] >= 5) {
      $this->session->unset_userdata('forget_password_context');
      $this->fail_and_redirect('Terlalu banyak percobaan validasi. Silakan ulangi proses.');
      return;
    }

    $context['validation_token'] = md5(uniqid((string) mt_rand(), true));
    $this->session->set_userdata('forget_password_context', $context);
    $this->load->view(
      'forget_password_validation',
      array(
        'role' => $context['role'],
        'validation_field' => $this->forget_password_policy->validation_field($context['role']),
        'validation_token' => $context['validation_token'],
        'failed' => $message
      )
    );
  }

  private function do_reset($email, $role)
  {
    if (!$this->refresh_token()) {
      $this->session->set_flashdata('failed', 'Layanan reset password sedang bermasalah. Silakan coba kembali.');
      return false;
    }

    $user_microsoft_id = $this->get_user_microsoft_id($email);
    if (!$user_microsoft_id) {
      $this->session->set_flashdata('failed', 'Akun Microsoft tidak ditemukan atau tidak dapat diakses.');
      return false;
    }

    $access_token = $this->session->userdata('access_token');
    $password = "Labschool123456";
    $post_params = array(
        'passwordProfile' => array(
            'forceChangePasswordNextSignIn' => false,
            'password' => $password
        ),
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token, 'Content-type: application/json'));
    curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/users/".$user_microsoft_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_params));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $rez = $response === '' ? array() : json_decode($response, true);
    if (
      $response === false ||
      $curl_error !== '' ||
      $http_code < 200 ||
      $http_code >= 300 ||
      (is_array($rez) && isset($rez['error']))
    ) {
      log_message('error', 'Microsoft Graph reset password failed for ' . $email . ' with HTTP ' . $http_code);
      $this->session->set_flashdata('failed', 'Password gagal diatur ulang oleh Microsoft. Silakan coba kembali.');
      return false;
    }

    $this->session->set_flashdata('success',
        'Password berhasil diatur ulang. Silakan login kembali ke dalam aplikasi.'
    );
    $this->session->set_flashdata('reset_email', $email);
    $this->session->set_flashdata('reset_password', $password);

    $this->mymodel->insert("user_reset_password",array(
        "email" => $email,
        "role" => $role
    ));
    return true;
  }

  public function reset_password()
  {
    $context = $this->session->userdata('forget_password_context');
    if (
      !is_array($context) ||
      empty($context['email']) ||
      empty($context['role']) ||
      empty($context['validation_token']) ||
      $this->forget_password_policy->validation_field($context['role']) === false
    ) {
      $this->fail_and_redirect('Sesi validasi reset password tidak ditemukan. Silakan ulangi proses.');
      return;
    }

    $validation_token = (string) $this->input->post('validation_token');
    if ($validation_token === '' || $validation_token !== $context['validation_token']) {
      $this->session->unset_userdata('forget_password_context');
      $this->fail_and_redirect('Sesi validasi tanggal lahir tidak valid. Silakan ulangi proses.');
      return;
    }

    if ($context['role'] === 'student' || $context['role'] === 'parent') {
      $student = $this->get_student_by_context($context);
      if (empty($student)) {
        $this->session->unset_userdata('forget_password_context');
        $this->fail_and_redirect('Data siswa tidak ditemukan. Silakan ulangi proses.');
        return;
      }

      if (!$this->forget_password_policy->birth_dates_match($this->input->post('tgl_lahir'), $student->tgl_lahir)) {
        $this->show_validation_error($context, 'Tanggal lahir siswa tidak sesuai.');
        return;
      }
      $account_email = $student->account_email;
    } else {
      $account = $this->get_non_student_by_context($context);
      if (empty($account)) {
        $this->session->unset_userdata('forget_password_context');
        $this->fail_and_redirect('Data akun tidak ditemukan. Silakan ulangi proses.');
        return;
      }

      $input_npp = trim((string) $this->input->post('npp'));
      if ($input_npp === '' || $input_npp !== trim((string) $account->npp)) {
        $this->show_validation_error($context, 'NPP tidak sesuai.');
        return;
      }
      $account_email = $account->email_ms_office;
    }

    if ($this->do_reset($account_email, $context['role'])) {
      $this->session->unset_userdata('forget_password_context');
    }
    redirect('forget_password');
  }

	public function refresh_token(){
		$appid = akunSetting("microsoft_client_id");
	  $tennantid = akunSetting("microsoft_tenant_id");
    $refresh_token = akunSetting('administrator_refresh_token');
    $secret_key = akunSetting("microsoft_secret_key");
    $secret_id = akunSetting("microsoft_secret_id");
	  $refresh_token_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/token";
		$data_token = array(
			'client_id' => $appid,
      'scope' => 'user.read openid profile offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All',
      'refresh_token' => $refresh_token,
			'grant_type' => 'refresh_token',
			'client_secret' => $secret_key
		);
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_URL, $refresh_token_url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$rez = json_decode(curl_exec($ch), 1);

		if (!is_array($rez) || array_key_exists('error', $rez)) {
      $error_code = is_array($rez) && isset($rez['error']) ? $rez['error'] : 'invalid_response';
      log_message('error', 'Microsoft token refresh failed: ' . $error_code);
      curl_close($ch);
      return false;
		  } else {
        if(!empty($rez['access_token']) && !empty($rez['refresh_token'])) {
          $newdata = array(
            'access_token' => $rez['access_token'],
            'refresh_token' => $rez['refresh_token'],
          );
          $this->session->set_userdata($newdata);
          $this->mymodel->update('pengaturan_akun', array('value' => $rez['access_token'], 'updated_at' => date("Y-m-d H:i:s")), array('name_setting' => 'administrator_access_token'));
          $this->mymodel->update('pengaturan_akun', array('value' => $rez['refresh_token'], 'updated_at' => date("Y-m-d H:i:s")), array('name_setting' => 'administrator_refresh_token'));
          curl_close($ch);
          return true;
        }
		  }
		  curl_close($ch);
      return false;
	}

  public function get_user_microsoft_id($email = null){
    $url = "https://graph.microsoft.com/v1.0/users/".$email;
    $access_token = $this->session->userdata('access_token');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token, 'Content-type: application/json'));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 0);
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $rez = json_decode($response, 1);
    if (
      $response === false ||
      $curl_error !== '' ||
      $http_code < 200 ||
      $http_code >= 300 ||
      !is_array($rez) ||
      empty($rez['id'])
    ) {
      log_message('error', 'Microsoft user lookup failed for ' . $email . ' with HTTP ' . $http_code);
      return false;
    }
    return $rez['id'];
  }
}
