<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-10">
    <a href="{url}gerenciar/certificado/participantes/{id_evento}/0" class="btn btn-primary">Clique aqui para configurar os participantes que receberão certificados.</a>
</div>
{certificado_info}
<div class="col-sm-12" style="margin-top: 1%">
    <form method="post" enctype="multipart/form-data" action="{url}gerenciar/certificado/configurar/{id_evento}">
        <div class="form-group">
            <label for="conteudo" >Conteúdo:</label>
            <textarea id="conteudo" name="conteudo" required class="form-control" rows="4">{conteudo}</textarea>
        </div>
        <div class="form-group">
            <label for="data" >Data:</label>
            <input type="text" name="data" id="data" value="{data}" class="form-control" required/> 
        </div>
        <div class="form-group">
            <label for="assinatura1" >Assinatura 1:</label>
            <textarea id="assinatura1" name="assinatura1" class="form-control" rows="4">{assinatura1}</textarea>
        </div>
        <div class="form-group">
            <label for="assinatura2" >Assinatura 2:</label>
            <textarea id="assinatura2" name="assinatura2" class="form-control" rows="4">{assinatura2}</textarea>
        </div>
        <div class="form-group">
            <input type="file" name="fundo_certificado" id="fundo_certificado" class="form-control"/>
        </div>
        <div class="form-group">
            <button class="btn btn-success" type="submit" ><i class="fa fa-save"></i> Salvar</button>
            <a  class="btn btn-success" target="_blank" href="{url}gerenciar/certificado/pre_visualizar/{id_evento}" > <i class="fa fa-eye" ></i> Pré-visualizar </a>
            <a  class="btn btn-success" target="_blank" href="{url}gerenciar/certificado/gerar/{id_evento}" > <i class="fa fa-file-pdf" ></i> Gerar </a>
        </div>
    </form>
</div>
{/certificado_info}
