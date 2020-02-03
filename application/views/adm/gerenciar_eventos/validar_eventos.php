<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script>
    function salvaId(id) {
        sessionStorage.setItem("id_evento", id);
    }
    function cancelar() {
        sessionStorage.removeItem("id_evento");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_evento = sessionStorage.getItem("id_evento");
        cancelar();
        window.location.href = aUrl + 'gerenciar/eventos/excluir/' + id_evento;
    }
</script>


<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Ativar/desativar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {eventos}
        <tr>
            <td> <a href="{url}gerenciar/eventos/visualizar/{id_evento}"> {nome} </a> </td>
            <td><a class="btn btn-success" href="{url}gerenciar/eventos/toggle/ativo/{id_evento}/{ativo}"> <i class="fa fa-check-square"> </i> </a> </td>
            <td><button class="btn btn-danger" onclick="salvaId({id_evento});" data-toggle="modal" data-target="#confirma"> <i class="fa fa-trash"> </i> </button> </td>
        </tr>
        {/eventos}
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
