<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
    function salvaId(id) {
        sessionStorage.setItem("id_participante", id);
    }
    function cancelar() {
        sessionStorage.removeItem("id_participante");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_participante = sessionStorage.getItem("id_participante");
        cancelar();
        window.location.href = aUrl + 'gerenciar/participantes/sistema/excluir/' + id_participante;
    }
</script>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Matricula/CPF</th>
            <th>Editar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {tabela_participantes}
        <tr>
            <td>{nome}</td>
            <td>{cpf_matricula}</td>
            <td>
                <a class="btn btn-primary" href="{url}gerenciar/participantes/sistema/editar/{id_participante}"> <i class="fa fa-edit"> </i> </a>
            </td>
            <td> <a class="btn btn-danger" onclick="salvaId({id_participante});" data-toggle="modal" data-target="#confirma" href="#" > <i class="fa fa-trash"> </i> </a> </td>
        </tr>
        {/tabela_participantes}
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
                    Deseja mesmo excluir o participante? Não será possivel desfazer.
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="cancelar();" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
                <button onclick="confirmar('{url}');" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    </div>
</div>


