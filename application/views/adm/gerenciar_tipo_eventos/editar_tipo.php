<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{editar_tipo}
<div class="col-sm-6">
    <form method="post" action="{url}gerenciar/tipo/editar/{id_tipo}">
        <input type="hidden" name="id_tipo" value="{id_tipo}" />
        <div class="form-group">
            <label for="tipo">Tipo:</label>
            <input type="text" name="tipo" id="tipo" value="{tipo}" required class="form-control" />
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/tipo" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
{/editar_tipo}
