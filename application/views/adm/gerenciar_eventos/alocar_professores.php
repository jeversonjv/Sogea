<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Adicionar</th>
            <th>Remover</th>
        </tr>
    </thead>
    <tbody>
        {tabela_professores_eventos}
        <tr>
            <td>{nome}</td>
            <td > <a class="btn btn-success" href="{url}gerenciar/eventos/visualizar/toggle/adicionar/{id_evento}/{id_professor}" > <i class="fa fa-plus"> </i> </a> </td>    
            <td > <a class="btn btn-danger" href="{url}gerenciar/eventos/visualizar/toggle/remover/{id_evento}/{id_professor}" > <i class="fa fa-minus-circle"> </i> </a> </td>    
        </tr>
        {/tabela_professores_eventos}
    </tbody>
</table>
