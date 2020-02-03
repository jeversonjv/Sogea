<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-6">
    <form method="post" action="{url}gerenciar/tipo/adicionar">
        <div class="form-group">
            <label for="tipo">Tipo:</label>
            <input type="text" name="tipo" id="tipo" required class="form-control" />
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/tipo" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
