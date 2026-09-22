<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CI3 wrapper agar autoload 'template' library berhasil.
 * load_class() di system/core/Common.php prepend 'CI_' ke nama class,
 * sehingga CI3 cari class CI_Template. File Template.php asli
 * define 'class Template' (bukan CI_Template), jadi butuh subclass wrapper.
 *
 * Tanpa file ini, setiap request error:
 *   "Class 'CI_Template' not found" di Common.php line 196.
 */
class My_Template extends Template
{
    public function __construct()
    {
        parent::__construct();
    }
}
