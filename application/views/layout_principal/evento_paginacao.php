<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
{eventos}

<div class="col-sm-12 evento-paginacao" style="margin-top: 15x; padding: 5px;">
    <div class="evento-paginacao-header">
        <a href="{url}Inicio/evento/{id_evento}" > <b> {nome} </b> </a> - {data}
    </div>
    <div class="evento-paginacao-meio">
        <p>
            {descricao}
        </p>
    </div>
</div>
{/eventos}