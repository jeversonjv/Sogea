<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
    function salvaId(id, id2) {
        sessionStorage.setItem("id_usuario", id);
        sessionStorage.setItem("tipo_usuario", id2);
    }
    function cancelar() {
        sessionStorage.removeItem("id_usuario");
        sessionStorage.removeItem("tipo_usuario");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_usuario = sessionStorage.getItem("id_usuario");
        var tipo_usuario = sessionStorage.getItem("tipo_usuario");
        cancelar();
        window.location.href = aUrl + 'gerenciar/usuarios/excluir/' + id_usuario + '/' + tipo_usuario;
    }
</script>


<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Evento</th>
            <th>Editar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {usuarios}
        <tr>
            <td>{login}</td>
            <td>{email}</td>
            <td>{evento}</td>
            <td><a class="btn btn-primary" href="{url}gerenciar/usuarios/editar/{id_usuario}/{tipo_usuario}"> <i class="fa fa-edit"> </i> </a> </td>
            <td><button class="btn btn-danger" onclick="salvaId({id_usuario}, {tipo_usuario});" data-toggle="modal" data-target="#confirma" {desabilitado}> <i class="fa fa-trash"> </i> </button> </td>
        </tr>
        {/usuarios}
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
                    Deseja mesmo excluir o usuário? Não será possivel desfazer.
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="cancelar();" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
                <button onclick="confirmar('{url}');" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    </div>
</div>
