<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>            
            <th>Gerar relatório/nome evento</th>
            <th>Gerar relatório/horário ocorrência</th>
        </tr>
    </thead>
    <tbody>
        {eventos}
        <tr>
            <td> {nome} </td>
            <td><a class="btn btn-primary" target="__blank" href="{url}gerenciar/eventos/relatorio/gerar/eventos/{id_evento}" > <i class="fa fa-file"> </i> </a> </td>
            <td><a class="btn btn-primary" target="__blank" href="{url}gerenciar/eventos/relatorio/gerar/horario/{id_evento}" > <i class="fa fa-file"> </i> </a> </td>
        </tr>
        {/eventos}
    </tbody>
</table>
