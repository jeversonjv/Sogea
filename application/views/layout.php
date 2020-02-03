<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
    <head>
        <title>LPTI</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="{url}assets/css/bootstrap.css" /> 
        <link rel="stylesheet" href="{url}assets/css/style.css" /> 
        <link rel="stylesheet" href="{url}assets/imagens/gly/css/fontawesome-all.css" />
        <link href="https://fonts.googleapis.com/css?family=Tajawal:400,700" rel="stylesheet">
        <link rel="icon" type="image/png" sizes="152x152" href="{url}assets/imagens/favi.png">
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg color-navbar-blue">
            <div id="nav_container" class="container-fluid">
                <a class="navbar-brand" href="{url}Inicio"><img src="{url}assets/imagens/logo.png" height="33" width="136"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item active">
                            <h6 class="nav-link">
                                <span class="sr-only">(current)</span>
                            </h6>
                        </li>

                        {nome_nav_layout}
                        <li class="nav-item">
                            <a class="btn btn-link"href="{url}{link}" {toggle} {target}> <i class="fa fa-{icon}"></i> {nome} </a>
                        </li>
                        {/nome_nav_layout}
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page Content -->
        <div class="container" style="margin-top: 10px;">
            <div class="alert alert-{color} alert-dismissible fade show" style="display: {display}" role="alert">
                <strong>{msg}</strong> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-sm-4 my-2">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active"> <b>Categorias</b>	</li>
                        <div class="tipos_eventos">
                            {tipo_evento}
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <a href="{url}Inicio/categoria/{id_tipo}">{tipo}</a>
                            </li>
                            {/tipo_evento}
                            <div>
                                </ul>
                            </div>
                            <div class="col-sm-8 my-2">
                                {conteudo1}
                            </div>
                        </div><!-- /.row -->
                        {linha_central}
                        <h3 style="display: inline; margin-top: 45px;">{msg_layout_central}</h3>
                        <div class="row">
                            {conteudo2}
                        </div> <!-- /.row -->
                        <a href="{url}Inicio/eventos" style="float: right;">Mais eventos &xrarr;</a>
                        <!-- Footer -->
                </div>
                <footer class="py-3 color-navbar-blue">
                    <div class="container-fluid">
                        <p class="text-center ">Copyright &copy; SOGEA 2018</p>
                    </div>
                    <!-- /.container -->
                </footer>


                <!-- Modal users -->
                <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Login usuário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{url}Inicio/logar" method="post" class="modal-body">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="form-control-label" for="login_usuario"><b>Login: </b></label>
                                        <input type="text" id="login_usuario" name="login_usuario" class="form-control" required  placeholder="Digite seu login..." />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="form-control-label" for="senha_usuario"><b>Senha: </b></label>
                                        <input type="password" id="senha_usuario" name="senha_usuario" class="form-control" required  placeholder="Digite sua senha..." />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Logar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="search_presence" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Verificar presenças</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="modal-body" method="post" action="{url}Inicio/presenca/participante/buscar">
                                <div class="form-group">
                                    <label for="flag_id" > Matrícula: </label>
                                    <input type="text" name="flag_id" id="flag_id" required class="form-control" maxlength="12"/>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                                </div>               
                            </form>

                        </div>
                    </div>
                </div>

                <div class="modal fade" id="gerar_folha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Verificar presenças</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="modal-body" method="post" action="{url}Inicio/presenca/participante/folha">
                                <div class="form-group">
                                    <label for="flag_id" > Matrícula: </label>
                                    <input type="text" name="flag_id" id="flag_id" required class="form-control" maxlength="12"/>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                                </div>               
                            </form>

                        </div>
                    </div>
                </div>

                <script src="{url}assets/js/jquery.js"></script>
                <script src="{url}assets/js/popper.js"></script>
                <script src="{url}assets/js/bootstrap.js"></script>
                </body>
                </html>
