<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
    .participante{
        height: auto;
        border: 1px solid rgba(0,0,0, 0.3);
        padding: 3px;
    }
    .participante-info{
        text-align: center;
    }

    .presenca-registrada{
        box-sizing: border-box;
        width: 100%;
        padding: 1%;
    }
    .presenca-registrada p{
        display: inline;
    }
    .presenca-registrada img{
        width: 60px;
        height: 60px;
    }

    .presenca-registrada-item{
        border: 1px solid rgba(0,0,0, 0.3);
        width: 22%;
        box-sizing: border-box;
        text-align: center;
        float: left;
        font-size: 13px;
        margin: 0% 2%;
    }

</style>

<div class="participante">
    <p class="participante-info" > <b>Nome:</b> {nome} | <b>CPF/matricula:</b> {cpf_matricula}</p>
    <p class="participante-info" > Presenças: {qtd_presenca}/{qtd_total} - {porcentagem}% de presença. </p>
    <div class="presenca-registrada">
        <hr/>
        <b>Presenças registradas:</b> <br/><br/>
        {presenca_registrada}
    </div>
    <div class="presenca-registrada">
        <hr/>
        <b>Presenças não registradas:</b> <br/> <br/>
        {presenca_nao_registrada}
    </div>
</div>

