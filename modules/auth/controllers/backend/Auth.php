<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define( 'CLIENT_ID', 'cb3ca268-e619-4b43-8aff-81538fa687c0');
define( 'TENANT_ID', '10fc2260-5c60-4800-af0e-789640a374a3'); // Only for user with mail Labschoolcibubur or just fill common to accept all microsoft mail
define( 'SECRET_ID', '4aa8a802-af7f-41cf-b7f6-34e50fc432e4'); //backup active secret : bf68c70f-691c-4b2b-a3c6-0314d60d51a5
define( 'CLIENT_SECRET', ''); //April 2028
define( 'GRAPH_USER_SCOPES', 'user.read mail.read mail.send offline_access User-PasswordProfile.ReadWrite.All');

/**
*| --------------------------------------------------------------------------
*| Auth Controller
*| --------------------------------------------------------------------------
*| For authentication
*|
*/
class Auth extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Login user
	*
	*/
	public function login()
	{
		
		if ($this->aauth->is_loggedin()) {
			redirect('administrator/user/profile','refresh');
		}
		$data = [];
		$this->config->load('site');

		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');


		if ($this->form_validation->run()) {
			if ($this->aauth->login($this->input->post('username'), $this->input->post('password'),$this->input->post('remember'))) {
				$ref = $this->session->userdata('redirect');
        
                if ($ref) {
					redirect($ref,'refresh');
                } else {
					redirect('/administrator/user/profile','refresh');
                }

			} else {
				$data['error'] = $this->aauth->print_errors(TRUE);
			}
		} else {
			$data['error'] = validation_errors();
		}
		$this->template->build('backend/standart/administrator/login', $data);
	}

	public function oauth_microsoft()
	{
		$appid = CLIENT_ID;
	    $tennantid = TENANT_ID;
	    //$secret = SECRET_ID;
	    //$_SESSION['state']=session_id();
	    $login_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/authorize";
	    $params = array(
	      'client_id' => $appid,
	      'redirect_uri' => base_url("administrator/auth/oauth_login"),
	      'response_type' => 'code',
	      'response_mode' => 'form_post',
	      'scope' => 'user.read openid profile offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All',
		  'prompt' => 'select_account'
	      //'state' =>$_SESSION['state']
	    );
	    header('Location: ' . $login_url . '?' . http_build_query($params));
	}

	public function oauth_login(){
	echo '<script> url = window.location.href;i=url.indexOf("#");if(i>0) {url=url.replace("#","?");window.location.href=url;}</script>';
	// print_r($_POST);
	$code = $_POST['code'];
	$post = $this->refresh_token($code);
	// print_r($post);
    if (array_key_exists('access_token', $post)) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $post['access_token'], 'Content-type: application/json'));
      curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me/");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $rez = json_decode(curl_exec($ch), 1);
      if (array_key_exists('error', $rez)) {
        var_dump($rez['error']);
        die();
      } else {
        $newdata = array(
          'access_token' => $post['access_token'],
		  'refresh_token' => $post['refresh_token'],
        );
        $this->session->set_userdata($newdata);
		//simpan ke database
		
				if($email == "onggi@labschoolcibubur.sch.id" || $email == "richi@labschoolcibubur.sch.id"){
					$this->mymodel->update("pengaturan_akun", array("value" => $post['access_token'], "updated_at" => date("Y-m-d H:i:s")), array("name_setting" => "administrator_access_token"));
					$this->mymodel->update("pengaturan_akun", array("value" => $post['refresh_token'], "updated_at" => date("Y-m-d H:i:s")), array("name_setting" => "administrator_refresh_token"));
				}
      }
      curl_close($ch);
    }
    $email = $rez["mail"];
    // echo $email."- token : ".$this->session->userdata('access_token')." <br/> ";
    // print_r($rez); 

    	$cek = $this->mymodel->withquery("select u.* from aauth_users u where email = '" . htmlspecialchars($email) . "'", "row");
		$cookie = array(
			'name'	 => 'user',
			'value'	 => '',
			'expire' => -3600,
			'path'	 => '/',
		);
		$this->input->set_cookie($cookie);
		if (!empty($cek)) {
    	/*$login_user = $this->aauth->login($email);
    	print_r($login_user);
    	if($login_user){*/
			$ref = $this->session->userdata('redirect');
			$random_string = rand(9999,99999999);
			$cookie = array(
				'name'	 => 'user',
				'value'	 => $cek->id . "-" . $random_string,
				'expire' => 99*999*999,
				'path'	 => '/',
			);
			$this->input->set_cookie($cookie);
			$data_login = array(
				'id' => $cek->id,
				'username' => $cek->username,
				'email' => $cek->email,
				'microsoft_id' => $rez["id"],
				'loggedin' => TRUE
			);
			$this->session->set_userdata($data_login);
			$this->update_last_login($cek->id);
			$this->mymodel->delete("aauth_login_attempts", "timestamp like '%".date("Y-m-d")."%' and login_attempts=", $cek->id);
            if ($ref) {
					redirect($ref,'refresh');
            } else {
					redirect('/administrator/user/profile','refresh');
            }

		} else {
			$data['error'] = $this->aauth->print_errors(TRUE);
			echo '
			<div style="
			    font-family: Arial, sans-serif;
			    background: #f8f9fa;
			    border: 1px solid #dee2e6;
			    border-radius: 10px;
			    padding: 20px;
			    width: 600px;
			    margin: 20px auto;
			    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
			">
			    <h2 style="color: #007bff; text-align:center;">User Not Found, please user registered account</h2>
			    <div style="margin-bottom:10px;">
			        <strong>Email:</strong> <span style="color:#333;">' . htmlspecialchars($email) . '</span>
			    </div>
			    <div style="margin-bottom:10px;">
			        <strong>Token:</strong> <code style="background:#e9ecef; padding:3px 6px; border-radius:4px;word-break:break-all">' . htmlspecialchars($_POST['access_token']) . '</code>
			    </div>
			    <div>
			        <strong>Response:</strong>
			        <pre style="
			            background:#212529;
			            color:#f8f9fa;
			            padding:10px;
			            border-radius:8px;
			            overflow-x:auto;
			            font-size:14px;
			        ">' . htmlspecialchars(print_r($rez, true)) . '</pre>
			    </div>
				<div style="margin-top:10px;text-align: center;"><a href="'.base_url().'" style="padding:10px;background-color:#35AB55;border: none;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 12pt;cursor:pointer;margin: 4px 2px;"><i class="fa fa-arrow-left"></i> Back</a></div>
			</div>';
			$this->CI->session->sess_destroy();
		}
		//$this->template->build('backend/standart/administrator/dashboard', $data);
	}

	public function refresh_token($code){
		$appid = CLIENT_ID;
	    $tennantid = TENANT_ID;
		$client_secret = CLIENT_SECRET;
	    //$secret = SECRET_ID;
	    //$_SESSION['state']=session_id();
	    $refresh_token_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/token";
		// d6f3a9b9-f6fb-4e5a-83a8-4a40d0d10d25
		$data_token = array(
			'client_id' => $appid,
			'redirect_uri' => base_url("administrator/auth/oauth_login"),
			'grant_type' => 'authorization_code',
			'code' => $code,
			'response_type' => 'token',
			'response_mode' => 'form_post',
			'scope' => 'user.read openid profile offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All',
			'client_secret' => $client_secret
		);
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_URL, $refresh_token_url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$rez = json_decode(curl_exec($ch), 1);
		// print_r($rez);
		// echo "<br/><br/>";
		return $rez;
		//header('Location: ' . $refresh_token_url . '?' . http_build_query($params));
	}

	/**
	* Register user member
	*
	*/
	public function register()
	{
		$data = [];

		$this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[aauth_users.username]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
		$this->form_validation->set_rules('full_name', 'Full Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[aauth_users.email]');
		$this->form_validation->set_rules('agree', 'Agree', 'trim|required');
		$this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_valid_captcha');

		$this->form_validation->set_message('is_unique', 'User already used');

		if ($this->form_validation->run()) {
			$save_data = [
				'full_name' => $this->input->post('full_name')
			];
			$save_user = $this->aauth->create_user($this->input->post('email'), $this->input->post('password'), $this->input->post('username'), $save_data);

			if ($save_user) {
				set_message('Your account sucessfully created');
				$this->aauth->add_member($save_user, 4);
				redirect('administrator/login', 'refresh');
			} else {
				$data['error'] = $this->aauth->print_errors();
			}
		} else {
			$data['error'] = validation_errors();
		}

		$this->template->build('backend/standart/administrator/register_member', $data);
	}

	/**
	* User forgot password
	*
	* @var String $id 
	*/
	public function forgot_password()
	{
		$data = [];

		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_valid_captcha');

		$this->form_validation->set_message('is_unique', 'User already used');

		if ($this->form_validation->run()) {
			//custom your action
			$reset = $this->aauth->remind_password($this->input->post('email'));
			if ($reset) {
				set_message('Your password reset link send to your mail');
			} else {
				set_message('Failed to send password reminder', 'danger');
			}
			redirect('administrator/login', 'refresh');
		} else {
			$data['error'] = validation_errors();
		}

		$this->template->build('backend/standart/administrator/forgot_password', $data);
	}

	/**
	* User session logout
	*
	*/
	public function logout()
	{
		$this->aauth->logout();
		$this->session->unset_userdata('user_keuangan');
		redirect('/');
	}

	public function update_last_login($user_id = FALSE) {

		if ($user_id == FALSE)
			$user_id = $this->session->userdata('id');

		$data['timestamp'] = date("Y-m-d H:i:s");
		$data['ip_address'] = $this->input->ip_address();
		$data['login_attempts'] = $user_id;
		$this->mymodel->insert("aauth_login_attempts", $data);
		//$this->aauth_db->where('id', $user_id);
		//return $this->aauth_db->update($this->config_vars['users'], $data);
	}
}

/* End of file Auth.php */
/* Location: ./application/controllers/administrator/Auth.php */