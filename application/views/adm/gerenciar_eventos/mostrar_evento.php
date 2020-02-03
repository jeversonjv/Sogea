<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{mostrar_evento}
<div class="col-sm-12">
    <div class="evento">
        <div class="evento-header">
            <h4>Evento: {nome}</h4>
            <strong>Data: </strong> {data} <br/>
            <strong>Horário inicial: </strong> {hora_inicial} <br/>
            <strong>Horário previso para o término: </strong> {hora_termino} <br/>
            <strong>local: </strong> {local} <br/>
        </div>
        <div class="evento-body">
            <p>
            <h4>Descrição do evento</h4>
            {descricao}
            </p>
            <hr />
            <h5>Tipo(s) do evento:</h5>
            <ul>
                {tipos}
                <li> {tipo}; </li>                
                {/tipos}
            </ul>
            <hr />
            <h5>Evento pai: </h5>
            <ul>
                {evento_pai}
                <li> {nome}. </li>
                {/evento_pai}
            </ul>
            <hr />
            <h5>Organizadores/Professores associados: </h5>
            <ul>
                {professores}
                <li> {nome_prof}; </li>
                {/professores}
            </ul>
        </div>
        <div class="evento-footer">
            <strong>O evento é obrigatório? </strong> {obrigatorio} <br/>
            <strong>O evento terá certificado? </strong> {certificado} <br/>
        </div>
    </div>
    <a href="{url}gerenciar/eventos/0" class="btn btn-success" style="margin-top: 30px;"> <i class="fa fa-undo"> </i> Voltar</a>
</div>
{/mostrar_evento}

