<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="col-sm-8">
    <h4>Evento pai atual: {evento_pai_atual}.</h4>

    <form method="post" action="{url}gerenciar/eventos/editar/eventopai/{id_evento_editando}">
        <div class="form-group">
            <label for="evento_pai"> Evento pai: </label>
            <select name="sub_id_evento" id="evento_pai" required class="form-control"> 
                <option value="nenhum" > Nenhum </option>
                {eventos_pai}
                <option value="{id_evento}" > {nome} </option>
                {/eventos_pai}
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/eventos/editar/{id_evento_editando}" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>