<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script>
    function preencher() {
        var btn_checked = document.querySelectorAll("input[type=checkbox]");
        var preencher_all = document.getElementById('preencher_all');
        var status = preencher_all.checked === true ? true : false;
        for (let i = 0; i < btn_checked.length; i++) {
            btn_checked[i].checked = status;
        }
    }
</script>

<div class="col-sm-12">
    <span style="float:right;"> Marcar todos: &nbsp;<input onclick="preencher()" id="preencher_all" type="checkbox" style="float:right; width: 30px; height: 30px; margin-bottom: 2%;"/> </span>
</div>
<div class="col-sm-12">
    <form method="post" action="{url}gerenciar/certificado/participantes/salvar/{id_evento}" >
        <div class="form-group">
            <table  id="tabela" class="table table-responsive-xl table-bordered table-condensed table-striped">
                <thead>
                    <tr>
                        <th>Nome</th> 
                        <th>Configurar</th> 
                    </tr>
                </thead>
                <tbody>
                    {participantes}
                    <tr>
                        <td> {nome} </td>
                        <td> <input name="id_participante[]" value="{id_participante}" {checked} type="checkbox" style="width: 30px; height: 30px;"/> </td>
                    </tr>
                    {/participantes}
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <button class="btn btn-success" type="submit" > <i class="fa fa-save"></i> Salvar</button>
            <a class="btn btn-success" href="{url}gerenciar/certificado/configurar/{id_evento}" > <i class="fa fa-undo"></i> Voltar</a>
        </div>
    </form>
</div>
