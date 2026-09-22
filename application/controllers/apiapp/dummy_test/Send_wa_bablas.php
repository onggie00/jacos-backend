<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dummy test API BaBlast (https://docs.bablast.id)
 *
 * Setting di tabel pengaturan_akun:
 *   - bablast_apikey : API key BaBlast (wajib)
 *
 * Cara pakai (segment URL):
 *   GET  /apiapp/dummy_test/send_wa_bablas/send?notelp=081234567890&pesan=Halo
 *   GET  /apiapp/dummy_test/send_wa_bablas/bulk        (pakai payload contoh hardcoded)
 *   GET  /apiapp/dummy_test/send_wa_bablas/pairing?notelp=6281234567890
 *   GET  /apiapp/dummy_test/send_wa_bablas/status
 *   GET  /apiapp/dummy_test/send_wa_bablas/logout
 */
class Send_wa_bablas extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->send();
    }

    // Kirim pesan single
    public function send()
    {
        $notelp = $this->input->get_post('notelp');
        $pesan  = $this->input->get_post('pesan');

        if (empty($pesan)) {
            $pesan = "Test Whatsapp Bablas " . date("Y-m-d H:i:s");
        }

        if (empty($notelp)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Parameter notelp wajib diisi'
            ));
            return;
        }

        $notelp = $this->cek_notelp($notelp);

        $url = "https://api.bablast.id/send";
        $data = array(
            'phone' => $notelp,
            'message' => $pesan
        );

        $this->bablast_request($url, 'POST', $data);
    }

    // Kirim pesan bulk / massal.
    // Body POST JSON di-forward apa adanya ke BaBlast.
    // Body kosong/invalid -> pakai payload contoh hardcoded untuk test.
    public function bulk()
    {
        $url = "https://api.bablast.id/send/bulk";

        $raw = @file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (empty($data) || empty($data['contacts'])) {
            $data = array(
            'group_name' => 'Customers Q4 2023',
            'message' => "Halo {gender} {nama},\n\nTerimakasih telah bergabung dengan {group.program}. Status membership anda adalah {group.tier}.\n\nSelamat {waktu}!",
            'delay' => 5000,
            'kode' => 'rusli01',
                'contacts' => array(
                    array(
                        'nama' => 'John Doe',
                        'phone' => '628571713xxxx',
                        'jenis_kelamin' => 'laki-laki',
                        'email' => 'john@example.com',
                        'variables' => array(
                            array('key' => 'program', 'value' => 'Gold Membership'),
                            array('key' => 'tier', 'value' => 'VIP')
                        )
                    ),
                    array(
                        'nama' => 'Jane Smith',
                        'phone' => '628121378xxxx',
                        'jenis_kelamin' => 'perempuan',
                        'email' => 'jane@example.com',
                        'variables' => array(
                            array('key' => 'program', 'value' => 'Silver Membership'),
                            array('key' => 'tier', 'value' => 'Regular')
                        )
                    )
                )
            );
        }

        $this->bablast_request($url, 'POST', $data);
    }

    // Pairing device / nomor WA
    public function pairing()
    {
        $notelp = $this->input->get_post('notelp');
        if (empty($notelp)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Parameter notelp wajib diisi'
            ));
            return;
        }

        $notelp = $this->cek_notelp($notelp);

        $url = "https://api.bablast.id/connector/pairing";
        $data = array(
            'method' => 'code',
            'phone' => $notelp
        );

        $this->bablast_request($url, 'POST', $data);
    }

    // Cek status device / WA
    public function status()
    {
        $url = "https://api.bablast.id/connector/status";
        $this->bablast_request($url, 'GET', null);
    }

    // Disconnect / logout device
    public function logout()
    {
        $url = "https://api.bablast.id/connector/logout";
        $this->bablast_request($url, 'POST', null);
    }

    /**
     * Helper request ke API BaBlast pakai stream context.
     * $body = array payload (akan di-json_encode) atau null untuk request tanpa body.
     */
    private function bablast_request($url, $method, $body)
    {
        $apiKey = akunSetting('bablast_apikey');

        if (empty($apiKey)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Setting bablast_apikey belum diisi di pengaturan_akun'
            ));
            return;
        }

        $header = "Content-type: application/json\r\nAuthorization: Bearer " . $apiKey . "\r\n";
        if ($body === null) {
            $header = "Authorization: Bearer " . $apiKey . "\r\n";
        }

        $options = array(
            'http' => array(
                'header'  => $header,
                'method'  => $method,
                'content' => ($body !== null) ? json_encode($body) : '',
                'ignore_errors' => true
            )
        );

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);

        // ambil HTTP status code dari $http_response_header (diisi otomatis oleh stream wrapper)
        $http_code = 0;
        if (isset($http_response_header) && count($http_response_header) > 0) {
            // contoh baris pertama: "HTTP/1.1 200 OK"
            if (preg_match('/HTTP\/[\d.]+\s+(\d+)/', $http_response_header[0], $match)) {
                $http_code = (int)$match[1];
            }
        }

        if ($result === FALSE && $http_code == 0) {
            // gagal total (koneksi/timeout), tidak ada response dari server
            echo json_encode(array(
                'success' => false,
                'message' => 'Request ke API BaBlast gagal (koneksi/timeout)',
                'url' => $url
            ));
        } else {
            // tampilkan status HTTP + response asli dari BaBlast
            echo json_encode(array(
                'http_code' => $http_code,
                'url' => $url,
                'response' => json_decode($result, true)
            ));
        }
    }

    public function cek_notelp($nohp){
        $hp = "";
        if(!preg_match("/[^+0-9]/",trim($nohp))){
            // cek apakah no hp karakter ke 1 dan 2 adalah angka 62
            if(substr(trim($nohp), 0, 2)=="62"){
                $hp = trim($nohp);
            }
            // cek apakah no hp karakter ke 1 adalah angka 0
            else if(substr(trim($nohp), 0, 1)=="0"){
                $hp = "62".substr(trim($nohp), 1);
            }
            else if(substr(trim($nohp), 0, 1)=="8"){
                $hp = "62".substr(trim($nohp), 0);
            }
        }
        return $hp;
    }
}

/* --------------------------------------------------------------------------
 * SAMPLE PAYLOAD UNTUK TEST DI POSTMAN
 * --------------------------------------------------------------------------
 * 1. SEND (single)
 *    POST http://localhost/apiapp/dummy_test/send_wa_bablas/send
 *    Body -> x-www-form-urlencoded:  notelp=081234567890 & pesan=Halo test
 *    atau raw JSON:  {"notelp": "081234567890", "pesan": "Halo test"}
 *
 * 2. BULK (massal) - POST raw JSON body:
 *    POST http://localhost/apiapp/dummy_test/send_wa_bablas/bulk
 *    Header: Content-Type: application/json
 *    Body:
    {
      "group_name": "Customers Q4 2023",
      "message": "Halo {gender} {nama},\n\nTerimakasih telah bergabung dengan {group.program}. Status membership anda adalah {group.tier}.\n\nSelamat {waktu}!",
      "delay": 5000,
      "kode": "rusli01",
      "contacts": [
        {
          "nama": "John Doe",
          "phone": "628571713xxxx",
          "jenis_kelamin": "laki-laki",
          "email": "john@example.com",
          "variables": [
            {"key": "program", "value": "Gold Membership"},
            {"key": "tier", "value": "VIP"}
          ]
        },
        {
          "nama": "Jane Smith",
          "phone": "628121378xxxx",
          "jenis_kelamin": "perempuan",
          "email": "jane@example.com",
          "variables": [
            {"key": "program", "value": "Silver Membership"},
            {"key": "tier", "value": "Regular"}
          ]
        }
      ]
    }
 *    (Body kosong -> otomatis pakai payload hardcoded di atas)
 *
 * 3. PAIRING
 *    POST http://localhost/apiapp/dummy_test/send_wa_bablas/pairing
 *    Body -> x-www-form-urlencoded:  notelp=6281234567890
 *
 * 4. STATUS
 *    GET http://localhost/apiapp/dummy_test/send_wa_bablas/status
 *
 * 5. LOGOUT
 *    POST http://localhost/apiapp/dummy_test/send_wa_bablas/logout
 * -------------------------------------------------------------------------- */
