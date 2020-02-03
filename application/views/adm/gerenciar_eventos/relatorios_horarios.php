<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
    .container{
        height: 100%;
        width: 100%;
    }
    .container table{
        width: 100%;
        font-size: 18px;
    }
    .container table, th, td{
        border: 1px solid #ccc;
        border-collapse: collapse;
    }
    table th{
        background-color: #D9DADB;
    }

    table td{
        padding: 5px;
    } 
    #td_evento{
        width: 70%;
    }


</style>


<div class="container">
    <table>
        <thead>
            <tr>
                <th>Evento</th>
                <th>Ocorrência</th>
                <th>Data ocorrência</th>
            </tr>
        </thead>
        <tbody>
            {eventos}
            <tr>
                <td> {nome} </td>
                <td> {nome_ocorrencia} </td>
                <td> {horario} </td>
            </tr>    
            {/eventos}
        </tbody>
    </table>
</div>
