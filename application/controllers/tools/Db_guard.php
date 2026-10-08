<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/db_guard — cetak identitas koneksi default + pengaman host lokal.
 * CLI only. Keluar (exit 1) bila koneksi default bukan MySQL lokal.
 *
 * Pemakaian: dipanggil dari skrip uji sebelum rename/DDL/DML:
 *   php tool/run_cli.php tools/db_guard
 */
class Db_guard extends MX_Controller {

    public function __construct() { parent::__construct(); }

    public function index()
    {
        if (!$this->input->is_cli_request()) {
            show_error('CLI only', 403);
            return;
        }

        $host = (string) $this->db->query('SELECT @@hostname h')->row()->h;
        $port = (string) $this->db->query('SELECT @@port p')->row()->p;
        $db   = (string) $this->db->query('SELECT DATABASE() d')->row()->d;

        echo "koneksi default: @@hostname={$host} @@port={$port} DATABASE()={$db}\n";

        // pengaman: server harus mesin lokal ini
        $lokal = gethostname();
        $host_ok = (strcasecmp($host, (string) $lokal) === 0)
            || in_array(strtolower($host), array('localhost', '127.0.0.1'), true);

        if (!$host_ok) {
            echo "!! HENTIKAN: @@hostname '{$host}' bukan mesin lokal ('{$lokal}').\n";
            echo "!! Kemungkinan koneksi lewat SSH tunnel / ke server remote.\n";
            echo "!! Jika memang disengaja, jalankan ulang dengan konfirmasi eksplisit.\n";
            exit(1);
        }
        echo "OK: server lokal ({$lokal}). Aman untuk DML/DDL pengujian.\n";
    }
}
