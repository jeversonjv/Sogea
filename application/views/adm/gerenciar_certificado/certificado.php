<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
    *{
        margin: 0;
        padding: 0;
    }

    #container{
        background: white;
        width: 100%;
        height: 100%;
    }
    .empurra{
        height: 1px;
    }
    .centro{
        text-align: center;
        margin-top: 200px;
    }
    .centro h1{
        font-size: 45px;
    }
    .centro-texto{
        width: 55%;
        margin: 0 auto;
        text-align: justify;
    }

    .footer{
        text-align: center;
        margin: 0 auto;
        width: 60%;
        margin-top: 5%;
    }

    .ass1{
        width: 45%;
        float: left;
        margin-top: 5%;
    }
    .ass2{
        width: 45%;
        float: right;
    }


</style>

{certificado}
<div id="container" style="background-image: url('{url}assets/imagens/certificado/{id_evento}.{ext_imagem}'); background-repeat: no-repeat; background-size: 100% 100%;">
    <div class="empurra"></div>
    <div class="centro">
        <h2>{nome_participante}</h2>
        <div class="centro-texto">
            {conteudo}
        </div>
    </div>
    <div class="footer">
        <p>{data}</p>
        <div class="ass1">
            {assinatura1}
        </div>
        <div class="ass2">
            {assinatura2}
        </div>
    </div>
</div>
{/certificado}


