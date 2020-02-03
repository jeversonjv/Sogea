<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
    function salvaId(id) {
        sessionStorage.setItem("id_horario_expediente", id);
    }
    function cancelar() {
        sessionStorage.removeItem("id_horario_expediente");
    }
    function confirmar(url) {
        var aUrl = url;
        var id_horario_expediente = sessionStorage.getItem("id_horario_expediente");
        cancelar();
        window.location.href = aUrl + 'gerenciar/horario/excluir/' + id_horario_expediente;
    }
</script>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Horário Inicial</th>
            <th>Horário Final</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {horarios}
        <tr>
            <td>{hora_inicio}</td>
            <td>{hora_final}</td>
            <td> <button class="btn btn-danger" onclick="salvaId({id_horario_expediente});" data-toggle="modal" data-target="#confirma"> <i class="fa fa-trash"> </i> </button> </td>
        </tr>
        {/horarios}
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


