<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{editar_usuario}
<div class="col-sm-6">
    <form method="post" action="{url}gerenciar/usuarios/editar/{id_usuario}/{id_tipo_usuario}">

        <div class="form-group">
            <label for="login_usuario">Login:</label>
            <input type="text" name="login_usuario" id="login_usuario" value="{login_usuario}" class="form-control" />
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" value="{email}" class="form-control" />
        </div>
        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" name="senha_usuario" id="senha" class="form-control"/>
        </div>
        <div class="form-group">
            <label for="tipo_usuario">Tipo de usuario:</label>
            <select name="tipo_usuario" class="form-control" id="tipo_usuario" {desabilitado}>
                {usuario}
                <option value="{id_tipo_usuario}" {selecionado} > {nome_tipo_usuario} </option>
                {/usuario}
            </select>
        </div>
        <div class="form-group">
            <label for="evento">Evento associado:</label> 
            <button class="btn btn-primary btn_ajuda" type="button" data-target="#info" data-toggle="modal">
                ?
            </button>	
            <select name="id_evento_fk" class="form-control" id="evento">
                <option value=""> Nenhum </option>
                {evento_associado}
                <option value="{id_evento_fk}" {selecionado}> {nome} </option>
                {/evento_associado}
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/usuario/0" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
{/editar_usuario}

<div class="modal fade" id="info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Como funciona?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Um usuário pode ou não estar associado para colaborar à um evento específico.
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-danger">Cancelar</button>
            </div>
        </div>
    </div>
</div>
