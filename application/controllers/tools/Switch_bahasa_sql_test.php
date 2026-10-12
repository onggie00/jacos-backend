<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * tools/switch_bahasa_sql_test — uji idempotensi & rollback file SQL
 * switch_bahasa di DATABASE UJI TERPISAH (dibuat & dibuang). CLI only.
 *
 * Pemakaian: php tool/run_cli.php tools/switch_bahasa_sql_test
 */
class Switch_bahasa_sql_test extends MX_Controller {

    public function __construct() { parent::__construct(); }

    public function index()
    {
        if (!$this->input->is_cli_request()) { show_404(); return; }

        $host = (string) $this->db->query('SELECT @@hostname h')->row()->h;
        $lokal = gethostname();
        if (strcasecmp($host, (string) $lokal) !== 0) {
            exit("STOP: koneksi bukan lokal ({$host})\n");
        }
        $testDb = 'jacos_switch_sql_test';
        $dir = FCPATH . 'database/sql/switch_bahasa/';

        $m = new mysqli('127.0.0.1', 'root', '', '', (int) $this->db->port ?: 3306);
        if ($m->connect_errno) exit("koneksi gagal\n");
        $m->query("DROP DATABASE IF EXISTS `{$testDb}`");
        $m->query("CREATE DATABASE `{$testDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $m->select_db($testDb);
        echo "DB uji: {$testDb}\n";

        // mini tabel akun (19) utk uji index email — struktur minimal
        $akun = array('siswa_sd','siswa_smp','siswa_sma','siswa_ft','siswa_kb','siswa_tk','switch_account',
                      'guru_sd','guru_smp','guru_sma','guru_ft','pegawai','pimpinan','pimpinan_sd','pimpinan_sma','pimpinan_smp','pramubhakti','security');
        // tambah 'pimpinan' tunggal? tabel 'pimpinan' sudah termasuk list
        foreach ($akun as $t) {
            $m->query("CREATE TABLE `{$t}` (id INT AUTO_INCREMENT PRIMARY KEY, email_ms_office VARCHAR(255), email_ms_office_ortu VARCHAR(255), deleted_at DATETIME NULL, deleted_at_ortu DATETIME NULL) ENGINE=InnoDB");
        }
        // mini panel tables utk 05
        $m->query("CREATE TABLE menu (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, label VARCHAR(200), type VARCHAR(200), link VARCHAR(200), sort INT, parent INT, icon VARCHAR(50), menu_type_id INT, active INT) ENGINE=InnoDB");
        $m->query("INSERT INTO menu (label, link) VALUES ('Pengaturan','administrator/sumber_informasi')");
        $m->query("CREATE TABLE aauth_perms (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), definition VARCHAR(200)) ENGINE=InnoDB");
        $m->query("CREATE TABLE aauth_groups (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), definition VARCHAR(200)) ENGINE=InnoDB");
        $m->query("INSERT INTO aauth_groups (name, definition) VALUES ('Admin','Superadmin Group')");
        $m->query("CREATE TABLE aauth_perm_to_group (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, perm_id INT UNSIGNED, group_id INT UNSIGNED) ENGINE=InnoDB");

        $fails = 0;
        $run = function($file, $label) use ($m, $dir, &$fails) {
            $sql = file_get_contents($dir . $file);
            if ($sql === false) { echo "SKIP {$file} (tidak ada)\n"; return; }
            if ($m->multi_query($sql)) {
                while ($m->more_results()) {
                    $m->next_result();
                    $r = $m->store_result();
                    if (is_object($r)) $r->free();
                }
                echo "PASS {$label}\n";
            } else {
                echo "FAIL {$label}: ", $m->error, "\n";
                $fails++;
            }
        };

        echo "== uji eksekusi ==\n";
        $run('01_tabel.sql', '01_tabel');
        $run('02_index_email_akun.sql', '02_index_email_akun');
        $run('03_kolom_audit.sql', '03_kolom_audit');
        $run('04_verifikasi.sql', '04_verifikasi');
        $run('05_izin_menu.sql', '05_izin_menu');
        // idempotensi: jalankan ulang file yang memang idempoten
        $run('01_tabel.sql', '01_tabel (2x idempoten)');
        $run('03_kolom_audit.sql', '03_kolom_audit (2x) — ERROR dup kolom = HARUS FAIL');
        $run('05_izin_menu.sql', '05_izin_menu (2x idempoten)');

        echo "== verifikasi isi ==\n";
        $tabel = $m->query("SELECT GROUP_CONCAT(TABLE_NAME ORDER BY TABLE_NAME) t FROM information_schema.TABLES WHERE TABLE_SCHEMA='{$testDb}' AND TABLE_NAME IN ('setting_bahasa','translation_cache','translation_override','translation_usage_bulanan')")->fetch_row()[0];
        echo 'tabel fitur: ', $tabel, "\n";
        echo 'index email tabel akun: ', $m->query("SELECT COUNT(DISTINCT TABLE_NAME) c FROM information_schema.STATISTICS WHERE TABLE_SCHEMA='{$testDb}' AND COLUMN_NAME IN ('email_ms_office','email_ms_office_ortu')")->fetch_row()[0], "\n";
        echo 'kolom audit: ', $m->query("SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='{$testDb}' AND TABLE_NAME='translation_override' AND COLUMN_NAME IN ('created_by','updated_by')")->fetch_row()[0], "\n";
        echo 'perm: ', $m->query("SELECT COUNT(*) c FROM aauth_perms WHERE name LIKE 'setting_bahasa_admin_%' OR name='menu_pengaturan_bahasa'")->fetch_row()[0], "\n";
        echo 'menu: ', $m->query("SELECT COUNT(*) c FROM menu WHERE link='administrator/setting_bahasa'")->fetch_row()[0], "\n";
        echo 'perm_to_group Admin: ', $m->query("SELECT COUNT(*) c FROM aauth_perm_to_group g JOIN aauth_perms p ON p.id=g.perm_id JOIN aauth_groups gr ON gr.id=g.group_id WHERE gr.name='Admin'")->fetch_row()[0], "\n";

        echo "== rollback ==\n";
        $run('90_rollback.sql', '90_rollback');
        $r = $m->query("SELECT COUNT(*) c FROM information_schema.TABLES WHERE TABLE_SCHEMA='{$testDb}' AND TABLE_NAME IN ('translation_cache','translation_override','translation_usage_bulanan')");
        echo 'sisa tabel fitur (harap 0; setting_bahasa disengaja tidak di-drop): ', $r->fetch_row()[0], "\n";
        echo 'sisa perm: ', $m->query("SELECT COUNT(*) c FROM aauth_perms WHERE name LIKE 'setting_bahasa_admin_%' OR name='menu_pengaturan_bahasa'")->fetch_row()[0], "\n";
        echo 'sisa menu: ', $m->query("SELECT COUNT(*) c FROM menu WHERE link='administrator/setting_bahasa'")->fetch_row()[0], "\n";

        $m->query("DROP DATABASE `{$testDb}`");
        echo "DB uji dihapus.\n";
        echo ($fails === 0 ? "SELESAI (02 sengaja tidak dijalankan 2x: ALTER tanpa IF NOT EXISTS di 5.7)\n" : "SELESAI DENGAN {$fails} FAIL\n");
    }
}
