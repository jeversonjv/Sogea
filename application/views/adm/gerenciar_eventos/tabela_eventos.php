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
            <th>Organizadores</th>
            <th>Editar</th>
            <th>Ativar/desativar</th>
            <th>Estado</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        {eventos}
        <tr>
            <td> <a href="{url}gerenciar/eventos/visualizar/{id_evento}"> {nome} </a> </td>
            <td>
                <ul>
                    <li><a href="{url}gerenciar/eventos/alocar/{id_evento}/0"> Alocar/Visualizar <a> </li>
                                </ul>
                                </td>
                                <td><a class="btn btn-primary" href="{url}gerenciar/eventos/editar/{id_evento}"> <i class="fa fa-user-edit"> </i> </a> </td>

                                <td><a class="{btn_class}" href="{url}gerenciar/eventos/toggle/ativo/{id_evento}/{ativo}"> <i class="{icon_class}"> </i> </a> </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="{btn_estado} dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {nome_estado}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{url}gerenciar/eventos/toggle/estado/{id_evento}/0"> Planejado para ocorrer </a>
                                            <a class="dropdown-item" href="{url}gerenciar/eventos/toggle/estado/{id_evento}/1"> Encerrado </a>
                                        </div>
                                    </div>
                                </td>
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
