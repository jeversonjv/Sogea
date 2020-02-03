<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-8">
    <form method="POST" action="{url}gerenciar/participantes/sistema/adicionar">
        <div class="form-group">
            <label for="nome">Nome: </label>
            <input  type="text" name="nome" id="nome" class="form-control" required/>
        </div>
        <div class="form-group">
            <label for="cpf_matricula">CPF/Matricula: </label> 
            <input  type="text" name="cpf_matricula" id="cpf_matricula" class="form-control" maxlength="12" required placeholder="Digite sem ponto. Ex: 201618110000; 00000000000"/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/participantes/sistema" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
