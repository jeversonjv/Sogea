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
        window.location.href = aUrl + 'gerenciar/participantes/eventos/desalocar/' + id_evento;
    }
    function alocar_manualmente(url) {
        var aUrl = url;
        var id_evento = sessionStorage.getItem("id_evento");
        cancelar();
        window.location.href = aUrl + 'gerenciar/participantes/eventos/manual/' + id_evento;
    }
    function alocar_planilha(url) {
        var aUrl = url;
        var id_evento = sessionStorage.getItem("id_evento");
        cancelar();
        window.location.href = aUrl + 'gerenciar/participantes/eventos/planilha/' + id_evento;
    }

</script>

<table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>Evento</th>
            <th>Alocar participantes</th>
            <th>Visualizar participantes</th>
            <th>Desalocar</th>
        </tr>
    </thead>
    <tbody>
        {tabela_participantes_eventos}
        <tr>
            <td>{nome}</td>
            <td>
                <a class="btn btn-primary" onclick="salvaId({id_evento});" data-toggle="modal" data-target="#participantes" href="#"> <i class="fa fa-user"> </i> </a>
            </td>
            <td > <a class="btn btn-primary" href="{url}gerenciar/participantes/eventos/visualizar/{id_evento}" > <i class="fa fa-eye"> </i> </a> </td>
            <td> <a class="btn btn-danger" onclick="salvaId({id_evento});" data-toggle="modal" data-target="#confirma" href="#" > <i class="fa fa-trash"> </i> </a> </td>
        </tr>
        {/tabela_participantes_eventos}
    </tbody>
</table>


<div class="modal fade" id="confirma">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deseja desalocar?</h5>
                <button onclick="cancelar();" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Deseja mesmo desalocar? Todos os participantes desse evento será desalocado e não será possível refazer.
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="cancelar();" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
                <button onclick="confirmar('{url}');" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="participantes">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alocar participantes</h5>
                <button onclick="cancelar();" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <button  class="btn btn-primary" onclick="alocar_manualmente('{url}')"> <i class="fa fa-hand-spock"></i> Alocar manualmente </button>
                </div>
                <br/>
                <div class="col-sm-12">
                    <button  class="btn btn-primary" onclick="alocar_planilha('{url}')" > <i class="fa fa-table"></i> Alocar atráves de uma planilha </button>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" onclick="cancelar();" class="btn btn-danger">Cancelar</button>
            </div>
        </div>
    </div>
</div>
