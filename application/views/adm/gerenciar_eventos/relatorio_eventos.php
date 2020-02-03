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
        width: 60%;
    }


</style>


<div class="container">
    <table>
        <thead>
            <tr>
                <th>Evento</th>
                <th>Nome ocorrência</th>
                <th>Data ocorrência</th>
            </tr>
        </thead>
        <tbody>
               <?php foreach($eventos as $key => $value): ?>
                    <?php foreach( $value['ocorrencias'] as $k => $v ): ?>
                        <?php if($k == 0): ?>
                            <tr>
                                <td id="#td_evento" rowspan="<?=$value['qtd_rows']?>"><?=$value['nome']?></td>
                                <td><?=$v['nome_ocorrencia']?></td>
                                <td><?=$v['horario']?></td>
                            </tr>
                        <?php else: ?>    
                            <tr>
                                <td><?=$v['nome_ocorrencia']?></td>
                                <td><?=$v['horario']?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>    
        </tbody>
    </table>
</div>
