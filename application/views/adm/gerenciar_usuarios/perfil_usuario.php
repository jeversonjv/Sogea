<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<div class="col-sm-8">
    <h3>Alterar configurações de acesso</h3>
    <hr/>
    <form method="post" action="#">
        <div class="form-group">
            <label> Senha atual </label>
            <input type="password" name="senha_atual" id="senha_atual" required class="form-control"/>
        </div>
        <div class="form-group">
            <label> Nova Senha </label>
            <input type="password" name="senha_nova" id="senha_nova" required class="form-control"/>
        </div>
        <div class="form-group">
            <label> Repetir nova senha </label>
            <input type="password" name="rep_senha_nova" id="rep_senha_nova" required class="form-control"/>
        </div>
        <div class="form-group">
            <button class="btn btn-success" type="submit"> <i class="fa fa-save"></i> Salvar </button>
        </div>

    </form>
</div>