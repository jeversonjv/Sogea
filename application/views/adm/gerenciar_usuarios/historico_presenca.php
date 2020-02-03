<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        {usuarios}
        <tr>
            <td> <a href="{url}gerenciar/usuarios/historicos/{id_usuario}" >{login}</a></td>
            <td>{email}</td>
        </tr>
        {/usuarios}
    </tbody>

</table>

