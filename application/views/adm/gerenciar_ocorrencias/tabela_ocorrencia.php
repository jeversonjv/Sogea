<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script>
    function salvaId(id, id2) {
        sessionStorage.setItem("id_ocorrencia", id);
        sessionStorage.setItem("id_evento_fk", id2);
    }
    function cancelar() {
        sessionStorage.removeItem("id_ocorrencia");
        sessionStorage.removeItem("id_evento_fk");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_ocorrencia = sessionStorage.getItem("id_ocorrencia");
        var id_evento_fk = sessionStorage.getItem("id_evento_fk");
        cancelar();
        window.location.href = aUrl + 'gerenciar/ocorrencias/excluir/' + id_ocorrencia + '/' + id_evento_fk;
    }
</script>


<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Horário</th>
            <th>Data</th>
            <th>Editar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {ocorrencia}
        <tr>
            <td> {nome} </td>
            <td> {horario} </td>
            <td> {data} </td>
            <td><a class="btn btn-primary" href="{url}gerenciar/ocorrencias/editar/{id_ocorrencia}"> <i class="fa fa-edit"> </i> </a> </td>
            <td><button class="btn btn-danger" onclick="salvaId({id_ocorrencia}, {id_evento_fk});" data-toggle="modal" data-target="#confirma"> <i class="fa fa-trash"> </i> </button> </td>
        </tr>
        {/ocorrencia}
    </tbody>
</table>

<div class="modal fade" id="confirma">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deseja excluir?</h5>
                <button onclick="cancelar();" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Deseja mesmo excluir o evento? Não será possivel desfazer.
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="cancelar();" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
                <button onclick="confirmar('{url}');" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    </div>
</div>
