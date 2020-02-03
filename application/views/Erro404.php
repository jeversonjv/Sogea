<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<style>
    .caixao{
        display: flex;
        height: 100%;
        width: 100%;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .caixao p, a{
        font-size: 50px;
        text-decoration: none;
        color: #0B67A4;
    }
    @media (min-width: 300px) and (max-width: 1024px){
        .caixao img{
            width: 500px;
            height: 120px;
        }
    }
</style>

<div class="caixao" style="background-image: url('{url}assets/imagens/error.gif');">
    <img src="{url}assets/imagens/logo-restrita.png" width="450" height="120"/>
    <p>Ops, página não existente no servidor.</p>
    <a href="{url}Inicio"> Clique aqui para voltar. </a>
</div>