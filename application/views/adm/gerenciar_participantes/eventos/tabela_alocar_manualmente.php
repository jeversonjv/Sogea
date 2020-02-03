<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-12">
    <h3> Alocando manualmente no evento: {nome_evento} </h3> 
</div>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>CPF/Matricula</th>
            <th>Adicionar</th>
            <th>Remover</th>
        </tr>
    </thead>
    <tbody>
        {tabela_participantes_eventos}
        <tr>
            <td>{nome}</td>
            <td>{cpf_matricula}</td>
            <td > <a class="btn btn-success {class_adicionar}" href="{url}gerenciar/participantes/eventos/manual/toggle/adicionar/{id_evento}/{id_participante}" > <i class="fa fa-plus"> </i> </a> </td>    
            <td > <a class="btn btn-danger {class_remover}" href="{url}gerenciar/participantes/eventos/manual/toggle/remover/{id_evento}/{id_participante}" > <i class="fa fa-minus-circle"> </i> </a> </td>    
        </tr>
        {/tabela_participantes_eventos}
    </tbody>
</table>
