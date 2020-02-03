<!DOCTYPE html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Gerenciamento</title>
        <link rel="stylesheet" href="{url}assets/css/bootstrap.css" /> 

        <link rel="stylesheet" href="{url}assets/css/style2.css" />
        <link rel="stylesheet" href="{url}assets/DataTables/datatables.css" />
        <link rel="stylesheet" href="{url}assets/imagens/gly/css/fontawesome-all.css" />
        <link href="https://fonts.googleapis.com/css?family=Tajawal:400,700" rel="stylesheet">
        <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
        <link rel="icon" type="image/png" sizes="152x152" href="{url}assets/imagens/favi.png">
    </head>
    <body>
        <div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    {restrita_menu}
                    <hr id="hr_menu">
                    <li id="titulo_menu">
                        <i class="fa fa-{icon}"></i> {titulo}
                    </li>
                    {itens_menu}
                    <li id="white">
                        <a href="{url}{link_menu}"> <i class="fa fa-chevron-right"></i> {item_menu}</a>
                    </li>
                    {/itens_menu}

                    {/restrita_menu}
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <nav class="navbar navbar-expand-lg navbar-light bg-ligth" style="border-bottom: 1px solid #086aa9;">
                        <a class="navbar-brand" href="#">
                            <img src="{url}assets/imagens/logo-restrita.png" height="33" width="136">
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive"
                                aria-expanded="false" aria-label="Toggle navigation">
                            <span id="toggler-icon" class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarResponsive">
                            <ul class="navbar-nav ml-auto">

                                <li class="nav-item">
                                    <h6 class="nav-link" style="margin-top: 2px;">
                                        <span class="sr-only">(current)</span>
                                    </h6>
                                </li>
                                <li class="nav-item links_menu">
                                    <a class="btn btn-link" href="{url}Inicio"> <i class="fa fa-home"></i> Tela inicial </a>
                                </li>
                                <li class="nav-item links_menu">
                                    <a class="btn btn-link" href="{url}Restrita"> <i class="fa fa-book"></i> Sobre o sistema </a>
                                </li>
                                <li class="nav-item links_menu">
                                    <a class="btn btn-link" href="{url}gerenciar/usuarios/perfil"> <i class="fa fa-user"> </i> Perfil </a>
                                </li>
                                <li class="nav-item links_menu">
                                    <a class="btn btn-link" href="{url}gerenciar/usuarios/historico"> <i class="fa fa-history"></i> Histórico de leitura </a>
                                </li>
                                <li class="nav-item links_menu">
                                    <a class="btn btn-link" href="{url}Restrita/deslogar"> <i class="fa fa-times"> </i> Sair </a>
                                </li>
                            </ul>

                        </div>
                    </nav>
                    <a href="#menu-toggle" class="btn btn-secondary menu-toggle" style="margin-top: 5px; margin-bottom: 5px;">Mostrar o menu</a>
                    <div class="alert alert-{cor_alert} alert-dismissible fade show" style="display: {display}" role="alert">
                        <strong>
                            {msg_erro}
                        </strong> 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <h1 style="margin-top: 15px;">Área {area}</h1>
                    <h4>{gerenciamento_area}</h4>
                    <div class="row" style="margin-top: 30px; margin-bottom: 40px;">
                        {conteudo}    
                    </div>
                </div>
            </div>
            <!-- /#page-content-wrapper -->

        </div>
        <!-- /#wrapper -->

        <!-- Menu Toggle Script -->
        <script src="{url}assets/js/jquery.js"></script>
        <script src="{url}assets/js/popper.js"></script>
        <script src="{url}assets/js/bootstrap.js"></script>
        <script src="{url}assets/DataTables/datatables.js"></script>
        <script src="{url}assets/js/main.js"></script>

    </body>

</html>
