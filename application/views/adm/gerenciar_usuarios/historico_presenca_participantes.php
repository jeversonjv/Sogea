<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Participante</th>
            <th>CPF/Matrícula</th>
            <th>Ocorrência</th>
            <th>Evento</th>
            <th>Horário</th>
        </tr>
    </thead>
    <tbody>
        {usuarios}
        <tr>
            <td> {nome_participante} </td>
            <td> {cpf_matricula} </td>
            <td> {nome_ocorrencia} </td>
            <td> {nome_evento} </td>
            <td> {horario} </td>
        </tr>
        {/usuarios}
    </tbody>
</table>

<a href="{url}gerenciar/usuarios/historico" class="btn btn-success"><i class="fa fa-undo-alt"></i> Voltar</a>

