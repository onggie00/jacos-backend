<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tracker Publik MHCU (Layar Proyektor).
 * Lokasi: application/controllers/Mhcu_tracker.php (root, MY_Controller).
 * Pattern: Forget_password.php / acara_presensi_realtime_tracker Public_api.
 * Privasi: HANYA angka agregat. Tidak ada nama/npp/skor/kategori per individu.
 */
class Mhcu_tracker extends MY_Controller
{
    const SESSION_KEY = 'mhcu_tracker_authenticated';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mhcu_tracker_model');
    }

    public function index()
    {
        if ($this->_is_authenticated()) {
            $this->_render_tracker_page();
            return;
        }
        $error = $this->session->flashdata('mhcu_tracker_error');
        $this->_render_gate($error);
    }

    public function unlock()
    {
        $input_pw = (string) $this->input->post('password', true);
        if ($input_pw === '') {
            $this->session->set_flashdata('mhcu_tracker_error', 'Password wajib diisi.');
            redirect('mhcu_tracker');
            return;
        }
        $expected = $this->_expected_password();
        if (hash_equals($expected, $input_pw)) {
            $this->session->set_userdata(self::SESSION_KEY, true);
            redirect('mhcu_tracker');
            return;
        }
        $this->session->set_flashdata('mhcu_tracker_error', 'Password salah.');
        redirect('mhcu_tracker');
    }

    public function logout()
    {
        $this->session->unset_userdata(self::SESSION_KEY);
        redirect('mhcu_tracker');
    }

    public function tracker_publik()
    {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Pragma: no-cache');
        $periode = $this->_get_active_periode();
        $payload = $this->mhcu_tracker_model->build_payload($periode);
        $this->output->set_content_type('application/json');
        echo json_encode($payload);
    }

    private function _is_authenticated()
    {
        return (bool) $this->session->userdata(self::SESSION_KEY);
    }

    private function _expected_password()
    {
        $periode = $this->_get_active_periode();
        if (!empty($periode) && isset($periode->id_mhcu_periode)) {
            return date('Ymd') . $periode->id_mhcu_periode;
        }
        return 'Labschool1234';
    }

    private function _get_active_periode()
    {
        return $this->db
            ->order_by('id_mhcu_periode', 'DESC')
            ->where('is_active', 1)
            ->limit(1)
            ->get('mhcu_periode')
            ->row();
    }

    private function _render_gate($error = '')
    {
        $error_html = '';
        if (!empty($error)) {
            $error_html = '<div style="margin-top:14px;padding:10px;background:#7A1F1F;color:#fff;border-radius:6px;font-size:14px">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        $action = site_url('mhcu_tracker/unlock');
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>MHCU Tracker - Login</title>';
        echo '<style>body{margin:0;height:100vh;background:#0E0E12;color:#E8E8EA;font-family:"Segoe UI",system-ui,sans-serif;display:flex;align-items:center;justify-content:center}';
        echo '.box{background:#171821;border:1px solid #2A2A35;border-radius:10px;padding:36px 32px;width:360px;box-shadow:0 10px 40px rgba(0,0,0,.5)}';
        echo 'h1{margin:0 0 8px;font-size:22px;font-weight:600}';
        echo 'p{margin:0 0 22px;color:#9A9AA8;font-size:13px}';
        echo 'input[type=password]{width:100%;padding:12px 14px;background:#0E0E12;border:1px solid #2A2A35;border-radius:6px;color:#E8E8EA;font-size:15px;box-sizing:border-box}';
        echo 'input[type=password]:focus{outline:none;border-color:#C2410C}';
        echo 'button{width:100%;margin-top:16px;padding:12px;background:#C2410C;color:#fff;border:0;border-radius:6px;font-size:15px;font-weight:600;cursor:pointer}';
        echo 'button:hover{background:#A8350A}';
        echo '.hint{margin-top:14px;font-size:11px;color:#6A6A78;text-align:center}';
        echo '</style></head><body>';
        echo '<form class="box" method="POST" action="' . $action . '">';
        echo '<h1>MHCU Tracker</h1>';
        echo '<p>Halaman publik untuk layar proyektor. Masukkan password untuk melanjutkan.</p>';
        echo '<input type="password" name="password" placeholder="Password" autofocus required>';
        echo '<button type="submit">Buka Tracker</button>';
        echo $error_html;
        echo '<div class="hint">Tracker publik &mdash; tidak menyimpan data pribadi.</div>';
        echo '</form></body></html>';
    }

    private function _render_tracker_page()
    {
        $this->data['base_url'] = base_url();
        $this->load->view('mhcu_tracker_view', $this->data);
    }
}
