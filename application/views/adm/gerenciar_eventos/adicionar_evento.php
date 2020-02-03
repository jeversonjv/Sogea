<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-sm-8">
    <form method="post" action="{url}gerenciar/eventos/adicionar">
        <div class="form-group">
            <label for="nome"> <b>Nome:</b> </label> 
            <input type="text" name="nome" id="nome" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="evento_pai"> <b>Evento pai:</b> </label>
            <select name="sub_id_evento" id="sub_id_evento" required class="form-control">
                <option value=""></option>
                <option value="nenhum"> Nenhum </option>
                {eventos_pai}
                <option value="{id_evento}" > {nome} </option>
                {/eventos_pai}
            </select>
        </div>
        <div class="form-group">
            <label for="local"> <b>Local:</b> </label> 
            <input type="text" name="local" id="local" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="desc"> <b>Descrição:</b> </label> 
            <textarea rows="8" class="form-control" name="descricao" id="desc" required></textarea>
        </div>
        <div class="form-group">
            <label for="tipo"> <b>Tipo(s):</b> </label>
            <br/>
            <select multiple="multiple" name="tipos[]"  required class="form-control">
                <option value=""> Nenhum </option>
                {tipos}
                <option value="{id_tipo}"> {tipo} </option>
                {/tipos}
            </select>
        </div>

        <div class="form-group">
            <label for="data"> <b>Data:</b> </label> 
            <input type="date" name="data" id="data" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="hora_inicial"> <b>Horário inicial:</b> </label> 
            <input type="time" name="hora_inicial" id="hora_inicial" required class="form-control"/>
        </div>
        <div class="form-group">
            <label for="hora_termino"> <b>Horário de término:</b> </label> 
            <input type="time" name="hora_termino" id="hora_termino" required class="form-control"/>
        </div>

        <div class="form-group">
            <label for="obrigado"> <b>Obrigatório:</b> </label>
            <br/>
            <label for="obrigado_sim"> Sim: </label>
            <input type="radio" name="obrigado" id="obrigado_sim" value="1" required/>
            <label for="obrigado_nao"> Não: </label>
            <input type="radio" name="obrigado" id="obrigado_nao" value="0" required/>
        </div>
        <div class="form-group">
            <label for="certificado"> <b>Oferecerá certificado:</b> </label>
            <br/>
            <label for="certificado_sim"> Sim: </label>
            <input type="radio" name="certificado" id="certificado_sim" value="1" required/>
            <label for="certificado_nao"> Não: </label>
            <input type="radio" name="certificado" id="certificado_nao" value="0" required/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"> </i> Salvar</button>
            <a href="{url}gerenciar/eventos/0" class="btn btn-success"> <i class="fa fa-undo"> </i> Voltar</a>
        </div>
    </form>
</div>
