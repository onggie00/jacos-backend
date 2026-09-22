<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 * Helper format date indonesian
 * 
 * Modul helper tanggal untuk PT. SOLUSI DIGITAL INDUSTRI
 * @link https://codenom.com/
 * @author Codenom Dev
 * @version 1.0
 * 
 * @access public
 */

if (!function_exists('akunSetting')) :
    function akunSetting($name_setting)
    {
        $CI = get_instance();
        // You may need to load the model if it hasn't been pre-loaded
        $CI->load->model('mymodel');
        $get_setting = $CI->mymodel->withquery("select * from pengaturan_akun where name_setting = '".$name_setting."'","row");
        return "$get_setting->value";
    }
endif;


