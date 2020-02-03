<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
    function salvaId(id) {
        sessionStorage.setItem("id_tipo", id);
    }
    function cancelar() {
        sessionStorage.removeItem("id_tipo");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_tipo = sessionStorage.getItem("id_tipo");
        cancelar();
        window.location.href = aUrl + 'gerenciar/tipo/excluir/' + id_tipo;
    }
</script>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Editar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {tabela_tipo}
        <tr>
            <td>{tipo}</td>
            <td> <a class="btn btn-primary" href="{url}gerenciar/tipo/editar/{id_tipo}"> <i class="fa fa-edit"> </i> </a> </td>
            <td> <button class="btn btn-danger" onclick="salvaId({id_tipo});" data-toggle="modal" data-target="#confirma"> <i class="fa fa-trash"> </i> </button> </td>
        </tr>
        {/tabela_tipo}
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
                    Deseja mesmo excluir o tipo? Não será possivel desfazer.
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="cancelar();" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
                <button onclick="confirmar('{url}');" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    </div>
</div>


