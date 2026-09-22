<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_mhcu_profile_redaction extends CI_Migration
{
    public function up()
    {
        $this->db->where('kode_profil', 'optimal')->update('mhcu_profil_kategori', array(
            'narasi_kategori' => 'Kondisi kesehatan mental berada pada kategori sangat baik. Bapak/Ibu memiliki kesejahteraan psikologis yang baik, tidak menunjukkan indikasi depresi, kecemasan, maupun burnout yang bermakna. Lingkungan kerja juga dirasakan mendukung, sehingga menjadi faktor protektif bagi kesehatan mental.',
        ));

        $this->db->where('kode_profil', 'fatigued')->update('mhcu_profil_kategori', array(
            'narasi_kategori' => 'Meskipun kesejahteraan psikologis Bapak/Ibu masih baik, mulai muncul kelelahan akibat pekerjaan. Kondisi ini belum mengganggu kesehatan mental secara bermakna, namun perlu diperhatikan agar tidak berkembang menjadi kelelahan yang lebih berat.',
        ));

        $this->db->where('kode_profil', 'burnout_emotional')->update('mhcu_profil_kategori', array(
            'narasi_kategori' => 'Hasil menunjukkan adanya tekanan emosional yang mulai bermakna serta kelelahan akibat pekerjaan. Kondisi ini dapat mempengaruhi produktivitas dan kesehatan mental maupun fisik apabila tidak ditangani.',
        ));
    }

    public function down()
    {
        $this->db->where('kode_profil', 'optimal')->update('mhcu_profil_kategori', array(
            'narasi_kategori' => 'Kondisi kesehatan mental berada pada kategori sangat baik. Bapak/Ibu memiliki kesejahteraan psikologis yang baik, tidak menunjukkan indikasi depresi, kecemasan, maupun burnout yang bermakna. Lingkungan kerja juga dirasakan mendukung sehingga menjadi faktor protektif bagi kesehatan mental.',
        ));

        $this->db->where('kode_profil', 'fatigued')->update('mhcu_profil_kategori', array(
            'narasi_kategori' => 'Meskipun kesejahteraan psikologis Bapak/Ibu masih baik, mulai muncul tanda-tanda kelelahan akibat pekerjaan. Kondisi ini belum mengganggu kesehatan mental secara bermakna, namun perlu diperhatikan agar tidak berkembang menjadi burnout yang lebih berat.',
        ));

        $this->db->where('kode_profil', 'burnout_emotional')->update('mhcu_profil_kategori', array(
            'narasi_kategori' => 'Hasil menunjukkan adanya tekanan emosional yang mulai bermakna serta kelelahan akibat pekerjaan. Kondisi ini dapat mempengaruhi produktivitas dan kesehatan fisik apabila tidak ditangani.',
        ));
    }
}
