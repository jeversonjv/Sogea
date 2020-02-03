<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{mostrar_evento}
<div class="evento">
    <div class="evento-header">
        <h4>Evento: {nome}</h4>
        <strong>Data: </strong> {data} <br/>
        <strong>Horário inicial: </strong> {hora_inicial} <br/>
        <strong>Horário previsto para o término: </strong> {hora_termino} <br/>
        <strong>local: </strong> {local} <br/>
    </div>
    <div class="evento-body">
        <p>
        <h4>Descrição do evento</h4>
        {descricao}
        </p>
    </div>
    <div class="evento-relacionado">
        <h4>Eventos secundários</h4>
        <ul class="list-group">
            {eventos_secundarios}
            <li class="list-group-item"><a href="{url}Inicio/evento/{id_evento}" > {nome_relacionado} </a></li>
            {/eventos_secundarios}
        </ul>
    </div>

    <div class="evento-footer">
        <strong>O evento é obrigatório? </strong> {obrigatorio} <br/>
        <strong>O evento terá certificado? </strong> {certificado} <br/>
    </div>
</div>
{/mostrar_evento}
