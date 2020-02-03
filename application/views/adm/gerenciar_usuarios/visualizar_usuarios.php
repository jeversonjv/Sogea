<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-12">
    <p><i>Clique no botão abaixo para administrar o tipo de usuário necessário.</i></p>
    <hr>
    <a class="btn btn-primary" href="{url}gerenciar/usuario/0" style="margin-right: 10px; margin-bottom: 10px;"> Colaboradores dos eventos </a>
    <a class="btn btn-primary" href="{url}gerenciar/usuario/1" style="margin-right: 10px; margin-bottom: 10px;"> Gerenciadores dos eventos </a>
    <a class="btn btn-primary" href="{url}gerenciar/usuario/2" style="margin-bottom: 10px;"> Administradores do sistema </a>
    <hr>
</div>
<div class="col-sm-4">
    <p><strong>Administrando:</strong> {tipo_usuario}.</p>
</div>
{conteudo_usuarios}
<div class="col-sm-1" style="margin-top: 3%;">
    <a href="{url}gerenciar/usuarios/adicionar" class="btn btn-success"> <i class="fa fa-plus"> </i>  Adicionar</a>
</div>

