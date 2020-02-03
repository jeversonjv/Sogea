<?php

class Menu {

    private $nav = array();

    public function get_menu_adm($tipo_usuario) {
        switch ($tipo_usuario) {
            case 0:
                $this->nav = [
                    "restrita_menu" => array(
                        0 => array(
                            "titulo" => "Participantes",
                            "icon" => "users",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Leitura de presença",
                                    "link_menu" => "gerenciar/leitura/presenca",
                                    "url" => base_url()
                                )
                            )
                        )
                    )
                ];
                break;
            case 1:
                $this->nav = [
                    "restrita_menu" => array(
                        0 => array(
                            "titulo" => "Eventos",
                            "icon" => "calendar-alt",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Gerir eventos",
                                    "link_menu" => "gerenciar/eventos/0",
                                    "url" => base_url()
                                ),
                                1 => array(
                                    "item_menu" => "Relatório de horários",
                                    "link_menu" => "gerenciar/eventos/relatorio",
                                    "url" => base_url()
                                )
                            )
                        ),
                        1 => array(
                            "titulo" => "Participantes",
                            "icon" => "users",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Gerir participantes",
                                    "link_menu" => "gerenciar/participantes",
                                    "url" => base_url()
                                ),
                                1 => array(
                                    "item_menu" => "Gerir presenças/geral",
                                    "link_menu" => "gerenciar/presencas",
                                    "url" => base_url()
                                ),
                                2 => array(
                                    "item_menu" => "Gerir presenças/pai",
                                    "link_menu" => "gerenciar/presencas/pai",
                                    "url" => base_url()
                                ),
                                3 => array(
                                    "item_menu" => "Relatórios - eventos/geral",
                                    "link_menu" => "gerenciar/relatorio/geral",
                                    "url" => base_url()
                                ),
                                4 => array(
                                    "item_menu" => "Relatórios - eventos/pai",
                                    "link_menu" => "gerenciar/presencas/pai",
                                    "url" => base_url()
                                ),
                                5 => array(
                                    "item_menu" => "Gerar certificado",
                                    "link_menu" => "gerenciar/certificado",
                                    "url" => base_url()
                                ),
                                6 => array(
                                    "item_menu" => "Leitura de presença",
                                    "link_menu" => "gerenciar/leitura/presenca",
                                    "url" => base_url()
                                )
                            )
                        )
                    )
                ];
                break;
            case 2:
                $this->nav = [
                    "restrita_menu" => array(
                        0 => array(
                            "titulo" => "Professores",
                            "icon" => "user-tie",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Gerir professores",
                                    "link_menu" => "gerenciar/professores",
                                    "url" => base_url()
                                ),
                                1 => array(
                                    "item_menu" => "Gerir horários",
                                    "link_menu" => "gerenciar/horario",
                                    "url" => base_url()
                                )
                            )
                        ),
                        1 => array(
                            "titulo" => "Usuários",
                            "icon" => "user-cog",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Gerir usuários",
                                    "link_menu" => "gerenciar/usuario/0",
                                    "url" => base_url()
                                )
                            )
                        ),
                        2 => array(
                            "titulo" => "Eventos",
                            "icon" => "calendar-alt",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Gerir eventos",
                                    "link_menu" => "gerenciar/eventos/0",
                                    "url" => base_url()
                                ),
                                1 => array(
                                    "item_menu" => "Validar eventos",
                                    "link_menu" => "gerenciar/eventos/validar",
                                    "url" => base_url()
                                ),
                                2 => array(
                                    "item_menu" => "Eventos encerrados",
                                    "link_menu" => "gerenciar/eventos/encerrados",
                                    "url" => base_url()
                                ),
                                3 => array(
                                    "item_menu" => "Eventos que ocorrerão",
                                    "link_menu" => "gerenciar/eventos/ocorrer",
                                    "url" => base_url()
                                ),
                                4 => array(
                                    "item_menu" => "Gerir ocorrências",
                                    "link_menu" => "gerenciar/ocorrencias",
                                    "url" => base_url()
                                ),
                                5 => array(
                                    "item_menu" => "Gerir tipo de eventos",
                                    "link_menu" => "gerenciar/tipo",
                                    "url" => base_url()
                                ),
                                6 => array(
                                    "item_menu" => "Relatório de horários",
                                    "link_menu" => "gerenciar/eventos/relatorio",
                                    "url" => base_url()
                                )
                            )
                        ),
                        3 => array(
                            "titulo" => "Participantes",
                            "icon" => "users",
                            "itens_menu" => array(
                                0 => array(
                                    "item_menu" => "Gerir participantes",
                                    "link_menu" => "gerenciar/participantes",
                                    "url" => base_url()
                                ),
                                1 => array(
                                    "item_menu" => "Gerir presenças eventos/geral",
                                    "link_menu" => "gerenciar/presencas",
                                    "url" => base_url()
                                ),
                                2 => array(
                                    "item_menu" => "Gerir presenças eventos/pai",
                                    "link_menu" => "gerenciar/presencas/pai",
                                    "url" => base_url()
                                ),
                                3 => array(
                                    "item_menu" => "Relatórios - eventos/geral",
                                    "link_menu" => "gerenciar/relatorio/geral",
                                    "url" => base_url()
                                ),
                                4 => array(
                                    "item_menu" => "Relatórios - eventos/pai",
                                    "link_menu" => "gerenciar/relatorio/pai",
                                    "url" => base_url()
                                ),
                                5 => array(
                                    "item_menu" => "Gerar certificado",
                                    "link_menu" => "gerenciar/certificado",
                                    "url" => base_url()
                                ),
                                6 => array(
                                    "item_menu" => "Leitura de presença",
                                    "link_menu" => "gerenciar/leitura/presenca",
                                    "url" => base_url()
                                )
                            )
                        )
                    )
                ];
                break;
        }
        return $this->nav;
    }

    public function get_menu_inicio($isLogado) {
        if ($isLogado) {
            $this->nav = [
                "nome_nav_layout" => array(
                    0 => array(
                        "nome" => "Verificar presenças",
                        "link" => "#",
                        "url" => "",
                        "target" => 'data-target="#search_presence"',
                        "toggle" => 'data-toggle="modal"',
                        "icon" => "file-alt"
                    ),
                    1 => array(
                        "nome" => "Gerar folha de presenças",
                        "link" => "#",
                        "link_modal" => "",
                        "url" => "",
                        "target" => 'data-target="#gerar_folha"',
                        "toggle" => 'data-toggle="modal"',
                        "icon" => "qrcode"
                    ),
                    2 => array(
                        "nome" => "Logar",
                        "link" => "#",
                        "url" => "",
                        "target" => 'data-target="#login"',
                        "toggle" => 'data-toggle="modal"',
                        "icon" => "sign-in-alt"
                    )
                )
            ];
        } else {
            $this->nav = [
                "nome_nav_layout" => array(
                    0 => array(
                        "nome" => "Verificar presenças",
                        "link" => "#",
                        "url" => "",
                        "target" => 'data-target="#search_presence"',
                        "toggle" => 'data-toggle="modal"',
                        "icon" => "file-alt"
                    ),
                    1 => array(
                        "nome" => "Gerar folha de presenças",
                        "link" => "#",
                        "link_modal" => "",
                        "url" => "",
                        "target" => 'data-target="#gerar_folha"',
                        "toggle" => 'data-toggle="modal"',
                        "icon" => "qrcode"
                    ),
                    2 => array(
                        "nome" => "Gerenciar",
                        "link" => "Restrita/verify_pass",
                        "link_modal" => "",
                        "url" => base_url(),
                        "target" => "",
                        "toggle" => "",
                        "icon" => "wrench"
                    ),
                    3 => array(
                        "nome" => "Sair",
                        "link" => "Restrita/deslogar",
                        "link_modal" => "",
                        "url" => base_url(),
                        "target" => "",
                        "toggle" => "",
                        "icon" => "sign-in-alt"
                    )
                )
            ];
        }
        return $this->nav;
    }

    public function retorna_array_usuario($tipo) {
        switch ($tipo) {
            case 0:
                $this->nav = [
                    0 => array(
                        "id_tipo_usuario" => 0,
                        "nome_tipo_usuario" => "Colaborador",
                        "selecionado" => "selected"
                    ),
                    1 => array(
                        "id_tipo_usuario" => 1,
                        "nome_tipo_usuario" => "Professor/Organizador",
                        "selecionado" => ""
                    ),
                    2 => array(
                        "id_tipo_usuario" => 2,
                        "nome_tipo_usuario" => "Administrador",
                        "selecionado" => ""
                    )
                ];
                break;
            case 1:
                $this->nav = [
                    0 => array(
                        "id_tipo_usuario" => 0,
                        "nome_tipo_usuario" => "Colaborador",
                        "selecionado" => ""
                    ),
                    1 => array(
                        "id_tipo_usuario" => 1,
                        "nome_tipo_usuario" => "Professor/Organizador",
                        "selecionado" => "selected"
                    ),
                    2 => array(
                        "id_tipo_usuario" => 2,
                        "nome_tipo_usuario" => "Administrador",
                        "selecionado" => ""
                    )
                ];
                break;
            case 2:
                $this->nav = [
                    0 => array(
                        "id_tipo_usuario" => 0,
                        "nome_tipo_usuario" => "Colaborador",
                        "selecionado" => ""
                    ),
                    1 => array(
                        "id_tipo_usuario" => 1,
                        "nome_tipo_usuario" => "Professor/Organizador",
                        "selecionado" => ""
                    ),
                    2 => array(
                        "id_tipo_usuario" => 2,
                        "nome_tipo_usuario" => "Administrador",
                        "selecionado" => "selected"
                    )
                ];
                break;
        }
        return $this->nav;
    }

}
