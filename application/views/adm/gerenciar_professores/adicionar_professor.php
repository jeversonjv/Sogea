<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-6">
    <form method="post" action="{url}gerenciar/professores/adicionar">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required class="form-control" />
        </div>

        <div class="form-group">
            <label for="siape">Siape:</label>
            <input type="text" name="siape" id="siape" required class="form-control" />
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/professores" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
