<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .card-qrcode{
        display: inline-block;
        width: 150px;
        float: left;
        border: 1px solid black;
        box-sizing: border-box;
        padding: 5px;
        margin-left: 5px;
    }

    .name-qrcode{
        text-align: center;
    }
    .img-qrcode{
        width: 145px;
        height: 140px;
    }

</style>

<div class="name-qrcode">
    <p><b>Nome:</b> {nome_participante}</p>
    <p><b>Matrícula/CPF:</b> {matricula_cpf}</p>
</div>
<hr>

{qrcodes}
<div class="card-qrcode">
    <img class="img-qrcode" src="{url}assets/imagens/qrcode_tmp/{id_participante}/{id_img}.png"/>
    <p>{nome_ocorrencia} - {data}, {horario} </p>
</div>
{/qrcodes}
