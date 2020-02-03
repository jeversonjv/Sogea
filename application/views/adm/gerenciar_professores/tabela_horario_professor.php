<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<p><strong>Dia da semana:</strong> {dia_semana}.</p>

<table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>Hora de ínicio</th>
            <th>Hora de término</th>
            <th>Trabalha?</th>
        </tr>
    </thead>
    <tbody>
        {horario_professor}
        <tr>
            <td>{hora_inicio}</td>
            <td>{hora_final}</td>
            <td> <a href="{url}client/Professores/toggle_horario/{id_professor_fk}/{id_horario_fk}/{id_dia}/{trabalha}" class="btn {btn}"> <i class="{img_btn}"> </i> </td>
        </tr>
        {/horario_professor}
    </tbody>
</table>
<a href="{url}gerenciar/professores" class="btn btn-success"> <i class="fa fa-undo" /> </i> Voltar</a>
