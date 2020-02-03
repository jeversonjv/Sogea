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
                        <li class="nav-item">
                            <a class="btn btn-link" href="{url}Inicio"> <i class="fa fa-home"></i> Tela Inicial</a>
                        </li>
                        <li class="nav-item links_menu">
                            <a class="btn btn-link" href="{url}Restrita/deslogar"> <i class="fa fa-times"> </i> Sair </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page Content -->

        <div class="flex-container">
            <div class="alert alert-{color} alert-dismissible fade show" style="display: {display}" role="alert">
                <strong>{msg}</strong> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <img src="{url}assets/imagens/favi.png" width="100" height="100"/>
            <div class="change-pass">
                <h3> <i class="fa fa-key"> </i> <strong>Alterar a senha</strong></h3>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Ao cadastrar um novo usuário, o sistema gera uma senha automática. Portanto, é necessário trocá-la.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="#">
                    <div class="form-group">
                        <label for="senha" > <strong>Senha:</strong> </label>
                        <input type="password" name="senha" id="senha" class="form-control" required/>
                    </div>
                    <div class="form-group">
                        <label for="rep_senha" > <strong>Repetir senha:</strong> </label>
                        <input type="password" name="rep_senha" id="rep_senha" class="form-control" required/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"> Alterar </button>
                    </div>
                </form>

            </div>

        </div>

        <!-- Footer -->
        <footer id="footer_verify" class="py-3 color-navbar-blue">
            <div class="container-fluid">
                <p class="text-center ">Copyright &copy; SOGEA 2018</p>
            </div>
            <!-- /.container -->
        </footer>

        <script src="{url}assets/js/jquery.js"></script>
        <script src="{url}assets/js/bootstrap.js"></script>
    </body>
</html>
