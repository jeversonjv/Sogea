<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-8">
    {ocorrencia}
    <form method="post" action="{url}gerenciar/ocorrencias/editar/{id_ocorrencia}">
        <div class="form-group">
            <label for="nome">Nome: </label>
            <input type="text" name="nome" id="nome" value="{nome}" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="horario">Horário: </label>
            <input type="time" name="horario" id="horario" value="{horario}" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="data">Data: </label>
            <input type="date" name="data" id="data" value="{data}" required class="form-control"/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/ocorrencias/evento/{id_evento_fk}" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
    {/ocorrencia}
</div>