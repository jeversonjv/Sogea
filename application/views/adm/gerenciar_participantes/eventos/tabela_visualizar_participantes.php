<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>CPF/Matrícula</th>
        </tr>
    </thead>
    <tbody>
        {tabela_participantes_eventos}
        <tr>
            <td>{nome}</td>
            <td>{cpf_matricula}</td>
        </tr>
        {/tabela_participantes_eventos}
    </tbody>
</table>


