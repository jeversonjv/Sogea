<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config['full_tag_open'] = '<ul class="pagination">';
$config['full_tag_close'] = '</ul>';

$config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
$config['num_tag_close'] = '</span></li>';

$config['cur_tag_open'] = "<li class='page-item active'><span class='page-link'>";
$config['cur_tag_close'] = '</span></li>';

$config['first_link'] = FALSE;
$config['last_link'] = FALSE;
$config['next_link'] = 'Próximo';
$config['next_tag_open'] = '<li class="next"> <span class="page-link">';
$config['next_tag_close'] = '</a></li>';

$config['prev_link'] = 'Anterior';
$config['prev_tag_open'] = '<li class="prev"><span class="page-link">';
$config['prev_tag_close'] = '</span></li>';
