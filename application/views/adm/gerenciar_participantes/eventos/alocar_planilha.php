<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="col-sm-12">
    <h4> Tutorial para alocar participante a partir de uma planilha </h4>
    <hr/>
    <div class="col-sm-4" style="float: left; border-right: 1px solid rgba(0, 0, 0, 0.125); border-left: 1px solid rgba(0, 0, 0, 0.125);">
        <figure>
            <figcaption> Passo 1 - 1ª Coluna: Nome; 2ª Coluna: Matrícula/CPF; Sem cabeçalhos.</figcaption>
            <img class="img-fluid" src="{url}assets/imagens/passo1.png"/>
        </figure>
    </div>
    <div class="col-sm-4" style="float: left; border-right: 1px solid rgba(0, 0, 0, 0.125); border-left: 1px solid rgba(0, 0, 0, 0.125);">
        <figure>
            <figcaption> Passo 2 - Selecione e copie a planilha. Seguindo rigorosamente este formato.</figcaption>
            <img class="img-fluid" src="{url}assets/imagens/passo2.png" />
        </figure>
    </div>
    <div class="col-sm-4" style="float: left; border-right: 1px solid rgba(0, 0, 0, 0.125); border-left: 1px solid rgba(0, 0, 0, 0.125);">
        <figure>
            <figcaption> Passo 3 - Cole os dados abaixo, com o formato indicado e salve-os. </figcaption>
            <img class="img-fluid" src="{url}assets/imagens/passo3.png" />        
        </figure>
    </div>
</div>

<div class="col-sm-12">
    <hr/>
    <form class="form-horizontal" method="post" action="#">
        <div class="form-group">
            <label for="participantes" > Participantes: </label>
            <textarea class="form-control" id="participantes" name="participante" required rows="10"></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/participantes/eventos" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>