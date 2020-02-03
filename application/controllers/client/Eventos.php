<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Eventos extends CI_Controller {

    private $nav = array();
    private $client;
    private $auth = array();

    public function __construct() {
        parent::__construct();
        $this->client = new GuzzleHttp\Client();
        $this->auth = array('admin', '1234', 'digest');
        if (!$this->session->userdata('login_usuario')) {
            redirect('Inicio');
        } else {
            if ($this->session->userdata('tipo_usuario') == 0) {
                redirect('Inicio');
            }
            $id_usuario['id_usuario'] = $this->session->userdata('id_usuario');
            try {
                $url = base_url() . 'api/Usuario_api/usuario';
                $res = $this->client->request('GET', $url, ['query' => $id_usuario, "auth" => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->message[0]->senha_usuario == sha1("123")) {
                    redirect("Restrita/verify_pass");
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
            }

            $this->nav = $this->menu->get_menu_adm($this->session->userdata('tipo_usuario'));
        }
    }

    public function gerenciar_eventos($msg = null) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if (count($result->message) > 0) {
                if ($result->status) {
                    foreach ($result->message as $key => $value) {
                        if ($value->ativo == 1) {
                            if ($this->session->userdata('tipo_usuario') == 2) {
                                $dados['eventos'][$key]['nome'] = $value->nome;
                                $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                                $dados['eventos'][$key]['url'] = base_url();
                                if ($value->ativo == 1) {
                                    $dados['eventos'][$key]['btn_class'] = "btn btn-danger";
                                    $dados['eventos'][$key]['icon_class'] = "fa fa-window-close";
                                    $dados['eventos'][$key]['ativo'] = 1;
                                }
                                if ($value->estado == 1) {
                                    $dados['eventos'][$key]['btn_estado'] = 'btn btn-danger';
                                    $dados['eventos'][$key]['nome_estado'] = 'Encerrado';
                                } else {
                                    $dados['eventos'][$key]['btn_estado'] = 'btn btn-primary';
                                    $dados['eventos'][$key]['nome_estado'] = 'Planejado para ocorrer';
                                }
                            } else {
                                if ($this->session->userdata('tipo_usuario') == 1 && $this->session->userdata('id_usuario') == $value->id_usuario_fk) {
                                    $dados['eventos'][$key]['nome'] = $value->nome;
                                    $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                                    $dados['eventos'][$key]['url'] = base_url();
                                    if ($value->ativo == 1) {
                                        $dados['eventos'][$key]['btn_class'] = "btn btn-danger";
                                        $dados['eventos'][$key]['icon_class'] = "fa fa-window-close";
                                        $dados['eventos'][$key]['ativo'] = 1;
                                    }
                                    if ($value->estado == 1) {
                                        $dados['eventos'][$key]['btn_estado'] = 'btn btn-danger';
                                        $dados['eventos'][$key]['nome_estado'] = 'Encerrado';
                                    } else {
                                        $dados['eventos'][$key]['btn_estado'] = 'btn btn-primary';
                                        $dados['eventos'][$key]['nome_estado'] = 'Planejado para ocorrer';
                                    }
                                }
                            }
                        }
                    }
                    $dados['conteudo_evento'] = $this->parser->parse('adm/gerenciar_eventos/tabela_eventos', $dados, TRUE);
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/visualizar_evento', $dados, TRUE);
                } else {
                    echo "Erro";
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        if ($msg) {
            switch ($msg) {
                case 1:
                    $dados['msg_erro'] = 'Evento criado com sucesso. É necessário aguardar a validação do mesmo.';
                    break;
                case 2:
                    $dados['msg_erro'] = 'Evento editado com sucesso.';
                    break;
                case 3:
                    $dados['msg_erro'] = 'Evento excluído com sucesso.';
                    break;
                case 4:
                    $dados['msg_erro'] = 'Evento pai editado com sucesso.';
                    break;
            }
            $dados['cor_alert'] = 'success';
            $dados['display'] = 'block';
        }

        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function visualizar_evento($id = null) {
        if (!$id) {
            redirect('gerenciar/eventos/0');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['mostrar_evento'][$key]['id_evento'] = $value->id_evento;
                    $dados['mostrar_evento'][$key]['nome'] = $value->nome;
                    $dados['mostrar_evento'][$key]['local'] = $value->local;
                    $dados['mostrar_evento'][$key]['data'] = date('d/m/Y', strtotime($value->data));
                    $horaInicial = explode(":", $value->hora_inicial);
                    $horatermino = explode(":", $value->hora_termino);
                    $dados['mostrar_evento'][$key]['hora_inicial'] = $horaInicial[0] . ":" . $horaInicial[1];
                    $dados['mostrar_evento'][$key]['hora_termino'] = $horatermino[0] . ":" . $horatermino[1];
                    $dados['mostrar_evento'][$key]['descricao'] = $value->descricao;
                    $dados['mostrar_evento'][$key]['url'] = base_url();
                    $dados['mostrar_evento'][$key]['certificado'] = $value->certificado == 1 ? 'Sim.' : 'Não.';
                    $dados['mostrar_evento'][$key]['obrigatorio'] = $value->obrigatorio == 1 ? 'Sim.' : 'Não.';
                    $criador['id_usuario'] = $value->id_usuario_fk;
                    if ($value->sub_id_evento == NULL) {
                        $evento[0]['nome'] = 'Nenhum';
                        $dados['mostrar_evento'][$key]['evento_pai'] = $evento;
                    } else {
                        try {
                            $url = base_url() . 'api/Evento_api/evento';
                            $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $value->sub_id_evento], 'auth' => $this->auth]);
                            $result = json_decode($res->getBody());
                            if (count($result->message) > 0) {
                                foreach ($result->message as $e => $va) {
                                    $str = '<a href="' . base_url() . 'gerenciar/eventos/visualizar/' . $va->id_evento . '">' . $va->nome . '</a>';
                                    $evento[$e]['nome'] = $str;
                                }
                                $dados['mostrar_evento'][$key]['evento_pai'] = $evento;
                            }
                        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                            $response = $ex->getResponse();
                            $responseBodyAsString = $response->getBody()->getContents();
                            $dados = $responseBodyAsString;
                            echo $dados;
                        }
                    }
                    try {
                        $url = base_url() . 'api/Evento_api/eventoAsTipo';
                        $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id], 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status && count($result->message) > 0) {
                            foreach ($result->message as $k => $value) {
                                $tipo[$k]['tipo'] = $value->tipo;
                            }
                        } else {
                            $tipo[0]['tipo'] = "Este evento não está associado a nenhum tipo";
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                    try {
                        $url = base_url() . 'api/Evento_api/eventoAsprofessor';
                        $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id], 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status && count($result->message) > 0) {
                            foreach ($result->message as $k => $value) {
                                $professores[$k]['nome_prof'] = $value->nome_prof;
                            }
                        } else {
                            $professores[0]['nome_prof'] = "Não há nenhum Organizador/Professor associado";
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                    $dados['mostrar_evento'][$key]['tipos'] = $tipo;
                    $dados['mostrar_evento'][$key]['professores'] = $professores;
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/mostrar_evento', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function adicionar_evento() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        $form = $this->input->post();
        if (count($form) >= 10) {
            $this->form_validation->set_rules('nome', 'Nome', 'required');
            $this->form_validation->set_rules('sub_id_evento', 'Evento pai', 'required');
            $this->form_validation->set_rules('local', 'Local', 'required');
            $this->form_validation->set_rules('descricao', 'Descrição', 'required');
            $this->form_validation->set_rules('data', 'Data', 'required');
            $this->form_validation->set_rules('hora_inicial', 'Hora inicial', 'required');
            $this->form_validation->set_rules('hora_termino', 'Hora de término', 'required');
            $this->form_validation->set_rules('obrigado', 'Hora', 'required');
            $this->form_validation->set_rules('certificado', 'Hora', 'required');
            if ($this->form_validation->run()) {
                if ($form['sub_id_evento'] == "nenhum") {
                    unset($form['sub_id_evento']);
                } else {
                    $evento['sub_id_evento'] = $form['sub_id_evento'];
                }
                print_r($this->session->userdata());
                $evento['nome'] = $form['nome'];
                $evento['local'] = $form['local'];
                $evento['descricao'] = $form['descricao'];
                $evento['obrigatorio'] = $form['obrigado'];
                $evento['certificado'] = $form['certificado'];
                $evento['data'] = $form['data'];
                $evento['hora_inicial'] = $form['hora_inicial'];
                $evento['hora_termino'] = $form['hora_termino'];
                $evento['id_usuario_fk'] = $this->session->userdata("id_usuario");
                try {
                    $url = base_url() . 'api/Evento_api/evento';
                    $res = $this->client->request('POST', $url, ['form_params' => $evento, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        if (count($form['tipos']) == 1 && $form['tipos'] != "") {
                            redirect('gerenciar/eventos/1');
                        } else {
                            $id_evento = $result->ultimo_id;
                            foreach ($form['tipos'] as $key => $value) {
                                $tipo[$key]['id_evento_fk'] = $id_evento;
                                $tipo[$key]['id_tipo_fk'] = $value;
                            }
                            try {
                                $url = base_url() . 'api/Evento_api/eventoAstipo';
                                $res = $this->client->request('POST', $url, ['form_params' => $tipo, 'auth' => $this->auth]);
                                $result = json_decode($res->getBody());
                                if ($result->status) {
                                    redirect('gerenciar/eventos/1');
                                } else {
                                    $result->message;
                                }
                            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                $response = $ex->getResponse();
                                $responseBodyAsString = $response->getBody()->getContents();
                                $dados = $responseBodyAsString;
                                echo $dados;
                            }
                        }
                    } else {
                        echo "Erro";
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            } else {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_evento', $dados, TRUE);
            }
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                $dados['eventos_pai'] = array();
                foreach ($result->message as $key => $value) {
                    if ($value->estado == 0 && $value->sub_id_evento == null && $value->ativo == 1) {
                        $dados['eventos_pai'][$key]['nome'] = $value->nome;
                        $dados['eventos_pai'][$key]['id_evento'] = $value->id_evento;
                    }
                }
                if (count($dados['eventos_pai']) == 0) {
                    $dados['eventos_pai'][0]['nome'] = "Não há nenhum evento cadastrado.";
                    $dados['eventos_pai'][0]['id_evento'] = "nenhum";
                }
            } else {
                $dados['eventos_pai'][0]['nome'] = "Não há nenhum evento cadastrado.";
                $dados['eventos_pai'][0]['id_evento'] = "nenhum";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        try {
            $url = base_url() . 'api/Evento_api/tipo';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                if ($result->status) {
                    foreach ($result->message as $key => $value) {
                        $dados['tipos'][$key]['id_tipo'] = $value->id_tipo;
                        $dados['tipos'][$key]['tipo'] = $value->tipo;
                    }
                } else {
                    echo "Erro";
                }
            } else {
                $dados['tipos'][0]['tipo'] = "";
                $dados['tipos'][0]['id_tipo'] = "";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/adicionar_evento', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function editar_evento($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/eventos/0');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['evento'][$key]['id_evento'] = $id_evento;
                    $dados['evento'][$key]['nome'] = $value->nome;
                    $dados['evento'][$key]['local'] = $value->local;
                    $dados['evento'][$key]['descricao'] = $value->descricao;
                    $dados['evento'][$key]['data'] = $value->data;
                    $dataInicial = explode(":", $value->hora_inicial);
                    $datatermino = explode(":", $value->hora_termino);
                    $dados['evento'][$key]['hora_inicial'] = $dataInicial[0] . ":" . $dataInicial[1];
                    $dados['evento'][$key]['hora_termino'] = $datatermino[0] . ":" . $datatermino[1];
                    $dados['evento'][$key]['url'] = base_url();
                    if ($value->obrigatorio == 1) {
                        $dados['evento'][$key]['obrigatorio1'] = 'checked="checked"';
                        $dados['evento'][$key]['obrigatorio0'] = "";
                    } else {
                        $dados['evento'][$key]['obrigatorio1'] = "";
                        $dados['evento'][$key]['obrigatorio0'] = 'checked="checked"';
                    }
                    if ($value->certificado == 1) {
                        $dados['evento'][$key]['certificado1'] = 'checked="checked"';
                        $dados['evento'][$key]['certificado0'] = "";
                    } else {
                        $dados['evento'][$key]['certificado1'] = "";
                        $dados['evento'][$key]['certificado0'] = 'checked="checked"';
                    }
                    $tipo = array();
                    $tipo_nao_selecionado = array();
                    $tipo_selecionado = array();
                    try {
                        $url = base_url() . 'api/Evento_api/eventoAsTipo';
                        $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status) {
                            foreach ($result->message as $k => $value) {
                                $tipo_selecionado[$k]['id_tipo'] = $value->id_tipo;
                                $tipo_selecionado[$k]['tipo'] = $value->tipo;
                                $tipo_selecionado[$k]['selecionado'] = 'selected';
                                $tipo[$k]['tipo'] = $value->tipo;
                            }
                        } else {
                            echo "Erro!";
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                    try {
                        $url = base_url() . 'api/Evento_api/tipo_distinct';
                        $res = $this->client->request('GET', $url, ['query' => $tipo, 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status) {
                            if ($result->message > 0) {
                                foreach ($result->message as $k => $value) {
                                    $tipo_nao_selecionado[$k]['id_tipo'] = $value->id_tipo;
                                    $tipo_nao_selecionado[$k]['tipo'] = $value->tipo;
                                    $tipo_nao_selecionado[$k]['selecionado'] = "";
                                }
                            }
                        } else {
                            echo "Erro!";
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                    if (count($tipo_selecionado) == 0) {
                        $dados['evento'][$key]['tipos'] = $tipo_nao_selecionado;
                    } else if (count($tipo_nao_selecionado) == 0) {
                        $dados['evento'][$key]['tipos'] = $tipo_selecionado;
                    } else {
                        $dados['evento'][$key]['tipos'] = array_merge($tipo_selecionado, $tipo_nao_selecionado);
                    }
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $form = $this->input->post();
        if (count($form) > 0) {
            $tipox = array();
            if ((!empty($form['nome'])) && (!empty($form['local'])) && (!empty($form['descricao'])) && ($form['obrigado'] != "") &&
                    ($form['certificado'] != "" ) && (!empty($form['data'])) && (!empty($form['hora_inicial'])) && (!empty($form['hora_termino']))) {
                $form['id_evento'] = $id_evento;
                if (count($form['tipos']) == 1 && $form['tipos'][0] == "") {
                    $tipox = array();
                } else {
                    $tipox = $form['tipos'];
                }
                unset($form['tipos']);
                $form['obrigatorio'] = $form['obrigado'];
                unset($form['obrigado']);
                try {
                    $url = base_url() . 'api/Evento_api/evento';
                    $res = $this->client->request('PUT', $url, ['form_params' => $form, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        try {
                            $url = base_url() . 'api/Evento_api/eventoAstipo';
                            $res = $this->client->request('DELETE', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
                            $result = json_decode($res->getBody());
                            if ($result->status) {
                                if (count($tipox) == 0) {
                                    redirect('gerenciar/eventos/2');
                                } else {
                                    foreach ($tipox as $key => $value) {
                                        $xtipo[$key]['id_evento_fk'] = $id_evento;
                                        $xtipo[$key]['id_tipo_fk'] = $value;
                                    }
                                    try {
                                        $url = base_url() . 'api/Evento_api/eventoAstipo';
                                        $res = $this->client->request('POST', $url, ['form_params' => $xtipo, 'auth' => $this->auth]);
                                        $result = json_decode($res->getBody());
                                        if ($result->status) {
                                            redirect('gerenciar/eventos/2');
                                        } else {
                                            echo "Erro";
                                        }
                                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                        $response = $ex->getResponse();
                                        $responseBodyAsString = $response->getBody()->getContents();
                                        $dados = $responseBodyAsString;
                                        echo $dados;
                                    }
                                }
                            } else {
                                echo "Erro";
                            }
                        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                            $response = $ex->getResponse();
                            $responseBodyAsString = $response->getBody()->getContents();
                            $dados = $responseBodyAsString;
                            echo $dados;
                        }
                    } else {
                        echo $result->message;
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            } else {
                $dados['msg_erro'] = "Não deixe nenhum campo vazio, é necessário o preenchimento de todos. Caso não for atualizar, não apague o campo.";
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/editar_evento', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function excluir_evento($id = null) {
        if (!$id) {
            redirect('gerenciar/eventos');
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_evento' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/eventos/3');
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function toggle_ativar_evento($id_evento = null, $ativo = null) {
        if ($id_evento == null || $ativo == null) {
            redirect('gerenciar/eventos/0');
        }
        $dados['id_evento'] = $id_evento;
        $dados['ativo'] = $ativo == 1 ? 0 : 1;
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('PUT', $url, ['form_params' => $dados, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/eventos/validar');
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function toggle_estado($id_evento = null, $estado = null) {
        if ($id_evento == null || $estado == null) {
            redirect('gerenciar/eventos/0');
        }
        $dados['id_evento'] = $id_evento;
        $dados['estado'] = $estado;
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('PUT', $url, ['form_params' => $dados, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                $this->set_all_child_events_state($id_evento, $estado);
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function set_all_child_events_state($id_evento = null, $estado = null) {
        if ($id_evento == null || $estado == null) {
            redirect('gerenciar/eventos/0');
        }
        $dados['sub_id_evento'] = $id_evento;
        $dados['estado'] = $estado;
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('PUT', $url, ['form_params' => $dados, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/eventos/0');
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function alocar_professor($id = null, $msg = null) {
        if (!$id) {
            redirect('gerenciar/eventos/0');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }

        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());

            if (count($result->message) > 0) {

                $hora['hora_inicial'] = '\'' . $result->message[0]->hora_inicial . '\'';
                $hora['hora_termino'] = '\'' . $result->message[0]->hora_termino . '\'';

                try {
                    $url = base_url() . 'api/Evento_api/professor_as_evento_as_horario';
                    $res = $this->client->request('GET', $url, ['query' => $hora, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (count($result->message) == 0) {
                        $dados['conteudo_visualizar'] = "<div class='col-sm-10'><h5>Não há nenhum professor/organizador disponível com correspondência aos horários do evento</h5></div>";
                    } else {
                        foreach ($result->message as $key => $value) {
                            $dados['tabela_professores_eventos'][$key]['nome'] = $value->nome;
                            $dados['tabela_professores_eventos'][$key]['id_professor'] = $value->id_professor;
                            $dados['tabela_professores_eventos'][$key]['url'] = base_url();
                            $dados['tabela_professores_eventos'][$key]['id_evento'] = $id;
                        }
                        $dados['conteudo_visualizar'] = $this->parser->parse('adm/gerenciar_eventos/alocar_professores', $dados, TRUE);
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            } else {
                redirect('gerenciar/eventos/0');
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        if ($msg) {
            switch ($msg) {
                case 1:
                    $dados['msg_erro'] = 'Professor já está alocado';
                    break;
                case 2:
                    $dados['msg_erro'] = 'Professor alocado';
                    break;
                case 3:
                    $dados['msg_erro'] = 'Sucesso ao apagar';
                    break;
            }
            $dados['display'] = 'block';
            $dados['cor_alert'] = 'danger';
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/visualizar_alocar_professores', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function toggle_alocar($id_evento = null, $id_professor = null) {
        if ($id_evento == null || $id_professor == null) {
            redirect('gerenciar/eventos/0');
        }
        $dados['id_evento_fk'] = $id_evento;
        $dados['id_professor'] = $id_professor;

        try {
            $url = base_url() . 'api/Evento_api/eventoAsprofessor';
            $res = $this->client->request('GET', $url, ['query' => $dados, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());

            if (count($result->message) > 0) {
                redirect('gerenciar/eventos/alocar/' . $id_evento . '/1');
            } else {
                try {
                    $dados['id_professores_fk'] = $id_professor;
                    unset($dados['id_professor']);
                    $url = base_url() . 'api/Evento_api/eventoAsprofessor';
                    $res = $this->client->request('POST', $url, ['form_params' => $dados, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        redirect('gerenciar/eventos/alocar/' . $id_evento . '/2');
                    } else {
                        echo $result->message;
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function toggle_desalocar($id_evento = null, $id_professor = null) {
        if ($id_evento == null || $id_professor == null) {
            redirect('gerenciar/eventos/0');
        }
        $dados['id_evento_fk'] = $id_evento;
        $dados['id_professores_fk'] = $id_professor;
        try {
            $url = base_url() . 'api/Evento_api/eventoAsprofessor';
            $res = $this->client->request('DELETE', $url, ['query' => $dados, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/eventos/alocar/' . $id_evento . '/3');
            } else {
                echo $result->message;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function editar_evento_pai($id_evento = null) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos - Editando evento pai';
        $dados['display'] = 'none';
        $dados['id_evento_editando'] = $id_evento;
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        if (!$id_evento) {
            redirect('gerenciar/eventos/0');
        }
        $form = $this->input->post();
        if (count($form) >= 1) {
            if ($form['sub_id_evento'] == 'nenhum') {
                try {
                    $url = base_url() . 'api/Evento_api/set_evento_pai_null';
                    $res = $this->client->request('PUT', $url, ['form_params' => ['id_evento' => $id_evento], 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (!$result->status) {
                        echo "Erro";
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            } else {
                $evento['sub_id_evento'] = $form['sub_id_evento'];
                $evento['id_evento'] = $id_evento;
                try {
                    $url = base_url() . 'api/Evento_api/evento';
                    $res = $this->client->request('PUT', $url, ['form_params' => $evento, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (!$result->status) {
                        echo "Erro";
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
        }

        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['eventos_pai'][$key]['nome'] = $value->nome;
                    $dados['eventos_pai'][$key]['id_evento'] = $value->id_evento;
                    if ($value->id_evento == $id_evento) {
                        if ($value->sub_id_evento == null) {
                            $dados['evento_pai_atual'] = 'Nenhum';
                        } else {
                            try {
                                $url = base_url() . 'api/Evento_api/evento';
                                $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $value->sub_id_evento], 'auth' => $this->auth]);
                                $result = json_decode($res->getBody());
                                $dados['evento_pai_atual'] = $result->message[0]->nome;
                            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                $response = $ex->getResponse();
                                $responseBodyAsString = $response->getBody()->getContents();
                                $dados = $responseBodyAsString;
                                echo $dados;
                            }
                        }
                    }
                }
            } else {
                $dados['eventos_pai'][0]['nome'] = "Não há nenhum evento cadastrado.";
                $dados['eventos_pai'][0]['id_evento'] = "nenhum";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/editar_evento_pai', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function visualizar_eventos_encerrados() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    if ($value->estado == 1 && $value->ativo == 1) {
                        $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                        $dados['eventos'][$key]['nome'] = $value->nome;
                        $dados['eventos'][$key]['url'] = base_url();
                    }
                }
                if (count($dados['eventos']) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento encerrado. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/visualizar_eventos_encerrados', $dados, TRUE);
                }
            } else {
                $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento encerrado. </h5></div>";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function visualizar_eventos_ocorrer() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    if ($value->estado == 0 && $value->ativo == 1) {
                        $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                        $dados['eventos'][$key]['nome'] = $value->nome;
                        $dados['eventos'][$key]['url'] = base_url();
                    }
                }
                if (count($dados['eventos']) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento planejado para ocorrer. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/visualizar_eventos_ocorrer', $dados, TRUE);
                }
            } else {
                $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento planejado para ocorrer. </h5></div>";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function validar_eventos() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        switch ($this->session->userdata('tipo_usuario')) {
            case 0:
                $dados['area'] = "de leitura";
                break;
            case 1:
                $dados['area'] = "dos professores";
                break;
            case 2:
                $dados['area'] = "administrativa";
                break;
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 0) {
                        $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                        $dados['eventos'][$key]['nome'] = $value->nome;
                        $dados['eventos'][$key]['ativo'] = 0;
                        $dados['eventos'][$key]['url'] = base_url();
                    }
                }
                if (count($dados['eventos']) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento para a validação. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/validar_eventos', $dados, TRUE);
                }
            } else {
                $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento para a validação. </h5></div>";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

}
