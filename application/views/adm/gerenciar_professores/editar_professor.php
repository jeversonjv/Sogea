<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{editar_professor}
<div class="col-sm-6">
    <form method="post" action="{url}gerenciar/professores/editar/salvar">
        <input type="hidden" name="id_professor" value="{id_professor}" />
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="{nome}" required class="form-control" />
        </div>
        <div class="form-group">
            <label for="siape">Siape:</label>
            <input type="text" name="siape" id="siape" value="{siape}" required class="form-control"/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/professores" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
{/editar_professor}
