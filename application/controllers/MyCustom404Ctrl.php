<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MyCustom404Ctrl extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $dados = array('url' => base_url());
        $this->parser->parse('Erro404', $dados);
    }

}
