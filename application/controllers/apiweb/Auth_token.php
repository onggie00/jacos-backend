<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_token extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model'); // Asumsi ada User_model untuk verifikasi kredensial
        $this->load->helper('url'); // Untuk base_url()
        $this->output->set_content_type('application/json'); // Set default content type
    }

    public function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // 1. Verifikasi Kredensial Pengguna (dari database atau model)
        // Ini adalah contoh mock. Di dunia nyata, Anda akan query database.
        $user = $this->User_model->get_user_by_credentials($username, $password);

        if ($user) {
            // 2. Buat Payload untuk Token
            $payload_data = [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role // Contoh: admin, user, dll.
            ];

            // 3. Generate Access Token dan Refresh Token
            $accessToken = $this->jwt_library->generateAccessToken($payload_data);
            $refreshToken = $this->jwt_library->generateRefreshToken($payload_data);

            // 4. Simpan Refresh Token di Database (PENTING!)
            // Di sini Anda perlu menyimpan refreshToken yang dihasilkan ke database bersama user_id
            // Contoh: $this->User_model->save_refresh_token($user->id, $refreshToken);
            // Dan saat verifikasi refresh token, Anda akan memeriksa apakah token ada di database.

            $response = [
                'status' => 'success',
                'message' => 'Login successful',
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken
            ];
            $this->output->set_status_header(200);
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Invalid username or password'
            ];
            $this->output->set_status_header(401); // Unauthorized
        }

        echo json_encode($response);
    }

    public function refresh_token() {
        $refreshToken = $this->input->post('refreshToken');

        if (!$refreshToken) {
            $response = ['status' => 'error', 'message' => 'Refresh token is required.'];
            $this->output->set_status_header(400);
            echo json_encode($response);
            return;
        }

        // 1. Verifikasi Refresh Token
        $decoded_refresh_token = $this->jwt_library->verifyToken($refreshToken);

        // 2. Periksa apakah refresh token valid dan ada di database
        // Asumsi $decoded_refresh_token->user_id tersedia setelah decode
        // Di sini Anda harus memverifikasi $refreshToken ini juga ada di DB
        // Misalnya: $is_refresh_token_valid_in_db = $this->User_model->is_refresh_token_valid($decoded_refresh_token->user_id, $refreshToken);

        if ($decoded_refresh_token) { // && $is_refresh_token_valid_in_db
            // 3. Hapus Refresh Token Lama dari Database (jika menggunakan rotasi)
            // Contoh: $this->User_model->delete_refresh_token($refreshToken);

            // 4. Generate Access Token dan Refresh Token Baru
            $user_data = [
                'user_id' => $decoded_refresh_token->user_id,
                'username' => $decoded_refresh_token->username,
                'role' => $decoded_refresh_token->role
            ];
            $newAccessToken = $this->jwt_library->generateAccessToken($user_data);
            $newRefreshToken = $this->jwt_library->generateRefreshToken($user_data);

            // 5. Simpan Refresh Token Baru di Database
            // Contoh: $this->User_model->save_refresh_token($decoded_refresh_token->user_id, $newRefreshToken);

            $response = [
                'status' => 'success',
                'message' => 'Token refreshed successfully',
                'accessToken' => $newAccessToken,
                'refreshToken' => $newRefreshToken
            ];
            $this->output->set_status_header(200);
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Invalid or expired refresh token. Please login again.'
            ];
            $this->output->set_status_header(401); // Unauthorized
        }

        echo json_encode($response);
    }

    public function logout() {
        $refreshToken = $this->input->post('refreshToken');

        if (!$refreshToken) {
            $response = ['status' => 'error', 'message' => 'Refresh token is required for logout.'];
            $this->output->set_status_header(400);
            echo json_encode($response);
            return;
        }

        // Hapus refresh token dari database
        // Contoh: $this->User_model->delete_refresh_token($refreshToken);

        $response = ['status' => 'success', 'message' => 'Logged out successfully.'];
        $this->output->set_status_header(200); // Or 204 No Content
        echo json_encode($response);
    }
}