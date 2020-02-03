<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{evento}
<div class="col-sm-8">
    <form method="post" action="{url}gerenciar/eventos/editar/{id_evento}">
        <div class="form-group">
            <label for="nome"> <b>Nome:</b> </label> 
            <input type="text" name="nome" id="nome" value="{nome}" required  class="form-control"/>
        </div>

        <div class="form-group">
            <label> <b> Editar evento pai: </b> </label> <br>
            <a href="{url}gerenciar/eventos/editar/eventopai/{id_evento}" class="btn btn-primary"> Clique aqui, caso queira editar o evento pai. </a>
        </div>

        <div class="form-group">
            <label for="local"> <b>Local:</b> </label> 
            <input type="text" name="local" id="local" value="{local}" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="desc"> <b>Descrição:</b> </label> 
            <textarea rows="8" class="form-control" name="descricao" required id="desc">{descricao}</textarea>
        </div>
        <div class="form-group">
            <label for="tipo"> <b>Tipo(s):</b> </label>
            <br/>
            <select multiple="multiple" name="tipos[]" required class="form-control">
                <option value=""> Nenhum </option>
                {tipos}
                <option value="{id_tipo}" {selecionado}> {tipo} </option>
                {/tipos}
            </select>
        </div>

        <div class="form-group">
            <label for="data"> <b>Data:</b> </label> 
            <input type="date" name="data" id="data" value="{data}" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="hora_inicial"> <b>Horário inicial:</b> </label> 
            <input type="time" name="hora_inicial" id="hora_inicial" value="{hora_inicial}" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="hora_termino"> <b>Horário de término:</b> </label> 
            <input type="time" name="hora_termino" id="hora_termino" value="{hora_termino}" required class="form-control"/>
        </div>

        <div class="form-group">
            <label for="obrigado"> <b>Obrigatório:</b> </label>
            <br/>
            <label for="obrigado_sim"> Sim: </label>
            <input type="radio" name="obrigado" id="obrigado_sim" required {obrigatorio1} value="1" />
                   <label for="obrigado_nao"> Não: </label>
            <input type="radio" name="obrigado" id="obrigado_nao" required {obrigatorio0} value="0" />
        </div>
        <div class="form-group">
            <label for="certificado"> <b>Oferecerá certificado:</b> </label>
            <br/>
            <label for="certificado_sim"> Sim: </label>
            <input type="radio" name="certificado" id="certificado_sim" required {certificado1} value="1" />
                   <label for="certificado_nao"> Não: </label>
            <input type="radio" name="certificado" id="certificado_nao" required {certificado0} value="0" />
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/eventos/0" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
{/evento}
