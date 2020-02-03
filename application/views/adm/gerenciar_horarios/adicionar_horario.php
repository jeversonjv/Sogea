<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-5">
    <form method="post" action="{url}gerenciar/horario/adicionar">
        <div class="form-group">
            <label for="hora_inicio" > Horário inicial: </label>
            <input type="time" name="hora_inicio" id="hora_inicio" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="hora_final" > Horário final: </label>
            <input type="time" name="hora_final" id="hora_final" required class="form-control"/>
        </div>
        <div class="form-group">
            <button class="btn btn-success"> <i class="fa fa-plus"> </i> Adicionar </button>
            <a href="{url}gerenciar/horario" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>

</div>
