<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-8">
    <form method="post" action="{url}gerenciar/ocorrencias/adicionar/{id_evento_fk}">
        <div class="form-group">
            <label for="nome">Nome: </label>
            <input type="text" name="nome" id="nome" required class="form-control" maxlength="20" placeholder="Máximo de 20 digitos"/>
        </div>
        <div class="form-group">
            <label for="horario">Horário: </label>
            <input type="time" name="horario" id="horario" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="data">Data: </label>
            <input type="date" name="data" id="data" required class="form-control"/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/ocorrencias/evento/{id_evento_fk}" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>