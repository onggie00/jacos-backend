<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CI3 wrapper untuk autoload 'OAuth2' library.
 * Sama kasusnya dgn My_Template: load_class() prepend 'CI_' otomatis.
 */
class My_OAuth2 extends OAuth2
{
    public function __construct()
    {
        parent::__construct();
    }
}
