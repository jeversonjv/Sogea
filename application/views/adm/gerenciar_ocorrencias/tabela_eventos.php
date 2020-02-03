<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="col-sm-5"><h5> Clique no evento desejado para gerenciar suas ocorrências. </h5></div>


<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
        </tr>
    </thead>
    <tbody>
        {eventos}
        <tr>
            <td> <a href="{url}gerenciar/ocorrencias/evento/{id_evento}"> {nome} </a> </td>
        </tr>
        {/eventos}
    </tbody>

</table>

