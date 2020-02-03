<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-12">
    <h4>Horario do professor(a): {nome_professor}</h4>
    <p><i>Clique no botão abaixo para visualizar o horário, no respectivo dia.</i></p>
    <hr/>
    {semana}
    <a class="btn btn-primary" style=" margin: 5px;" href="{url}gerenciar/professores/horario/{id_professor}/{id_dia_semana}"> {dia_semana} </a>
    {/semana}
    <hr/>
    <div class="col-sm-6">
        {conteudo_visualizar}
    </div>
</div>