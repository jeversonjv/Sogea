<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{ultimo_evento}
<div class="col-sm-4 my-4">
    <div class="card">
        <div class="card-header"> 
            <h5 class="card-title"><a href="{url}Inicio/evento/{id_evento}">{nome}</a></h5>
        </div>
        <div class="card-body">
            <i>{data}</i> - <i>{hora_inicial}</i>
            <p class="card-text">{descricao}</p>
        </div>
    </div>
</div>
{/ultimo_evento}