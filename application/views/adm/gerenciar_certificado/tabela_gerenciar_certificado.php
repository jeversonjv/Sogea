<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th> 
            <th>Configurar</th> 
        </tr>
    </thead>
    <tbody>
        {eventos}
        <tr>
            <td> {nome} </td>
            <td> <a class="btn btn-primary" href="{url}gerenciar/certificado/configurar/{id_evento}"><i class="fa fa-wrench"></i></a> </td>
        </tr>
        {/eventos}
    </tbody>
</table>


