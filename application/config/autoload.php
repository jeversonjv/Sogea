<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();

$autoload['libraries'] = array('parser', 'guzzle', 'session', 'form_validation', 'menu', 'pagination', 'mpdf', 'qrcodereader', 'upload', 'encryption', 'hashid');

$autoload['drivers'] = array();

$autoload['helper'] = array('url', 'form');

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array('Evento_model', 'Usuario_model', 'Professor_model', 'Participante_model', 'Ocorrencia_model', 'Certificado_model');
