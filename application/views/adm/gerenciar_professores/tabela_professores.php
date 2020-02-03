<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
    function salvaId(id) {
        sessionStorage.setItem("id_professor", id);
    }
    function cancelar() {
        sessionStorage.removeItem("id_professor");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_professor = sessionStorage.getItem("id_professor");
        cancelar();
        window.location.href = aUrl + 'gerenciar/professores/excluir/' + id_professor;
    }
</script>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Siape</th>
            <th>Horário</th>
            <th>Editar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {tabela_professores}
        <tr>
            <td>{nome}</td>
            <td>{siape}</td>
            <td>
                <a href="{url}gerenciar/professores/horario/{id_professor}/7" class="btn btn-primary"> <i class="fa fa-clock"> </i> </a>
            </td>
            <td>
                <a href="{url}gerenciar/professores/editar/{id_professor}" class="btn btn-primary"> <i class="fa fa-edit"> </i> </a>
            </td>
            <td> <a class="btn btn-danger" onclick="salvaId({id_professor});" data-toggle="modal" data-target="#confirma" href="#" > <i class="fa fa-trash"> </i> </a> </td>
        </tr>
        {/tabela_professores}
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
                    Deseja mesmo excluir o professor? Não será possivel desfazer.
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="cancelar();" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
                <button onclick="confirmar('{url}');" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    </div>
</div>


