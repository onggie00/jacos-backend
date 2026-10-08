<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Env_probe extends MX_Controller {
    public function __construct() { parent::__construct(); }
    public function index() {
        if (!$this->input->is_cli_request()) { show_404(); return; }
        $this->config->load('translator');
        echo 'ENVIRONMENT=', ENVIRONMENT, "\n";
        echo 'translator_enabled=', var_export($this->config->item('translator_enabled'), true), "\n";
        echo 'endpoints=', count((array) $this->config->item('translator_enabled_endpoints')), "\n";
    }
}
