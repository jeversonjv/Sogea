<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="col-sm-7">
    <video id="preview"></video>
</div>

<form id="FormQrcode" action="{url}gerenciar/leitura/presenca" method="post"> 
    <input type="hidden" id="id_qrCode" name="id_qrCode"/>
</form>

<div class="col-sm-4">
    <a class="btn btn-success" href="{url}gerenciar/leitura/presenca"> <i class="fa fa-camera"></i> Ler novamente</a>
</div>

<script src="{url}assets/js/presenca.js" ></script>
