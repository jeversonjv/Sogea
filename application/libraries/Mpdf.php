<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mpdf {

    public function __construct() {
        require_once ('mpdf/vendor/autoload.php');
    }

}
