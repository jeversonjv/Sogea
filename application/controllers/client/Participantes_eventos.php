<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Participantes_eventos extends CI_Controller {

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

    public function gerenciar_participantes_eventos() {
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
            $res = $this->client->request('GET', $url, ['query' => ['ativo' => 1], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['tabela_participantes_eventos'] = array();
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    if ($this->session->userdata('tipo_usuario') == 2) {
                        $dados['tabela_participantes_eventos'][$key]['nome'] = $value->nome;
                        $dados['tabela_participantes_eventos'][$key]['id_evento'] = $value->id_evento;
                        $dados['tabela_participantes_eventos'][$key]['url'] = base_url();
                        $dados['id_evento'] = $value->id_evento;
                    } else {
                        if ($this->session->userdata('tipo_usuario') == 1 && $this->session->userdata('id_usuario') == $value->id_usuario_fk) {
                            $dados['tabela_participantes_eventos'][$key]['nome'] = $value->nome;
                            $dados['tabela_participantes_eventos'][$key]['id_evento'] = $value->id_evento;
                            $dados['tabela_participantes_eventos'][$key]['url'] = base_url();
                            $dados['id_evento'] = $value->id_evento;
                        }
                    }
                }
                $dados['conteudo_participantes_eventos'] = $this->parser->parse('adm/gerenciar_participantes/eventos/tabela_participantes_eventos', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_participantes_eventos', $dados, TRUE);
            } else {
                $dados['conteudo_participantes_eventos'] = "<div class='col-sm-12'><h5> Não há nenhum evento registrado. </h5></div>";
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_participantes_eventos', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerenciar_participantes_eventos_pai() {
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
            $res = $this->client->request('GET', $url, ['query' => ['ativo' => 1], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['tabela_participantes_eventos'] = array();
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    if ($value->sub_id_evento == null) {
                        if ($this->session->userdata('tipo_usuario') == 2) {
                            $dados['tabela_participantes_eventos'][$key]['nome'] = $value->nome;
                            $dados['tabela_participantes_eventos'][$key]['id_evento'] = $value->id_evento;
                            $dados['tabela_participantes_eventos'][$key]['url'] = base_url();
                            $dados['id_evento'] = $value->id_evento;
                        } else {
                            if ($this->session->userdata('tipo_usuario') == 1 && $this->session->userdata('id_usuario') == $value->id_usuario_fk) {
                                $dados['tabela_participantes_eventos'][$key]['nome'] = $value->nome;
                                $dados['tabela_participantes_eventos'][$key]['id_evento'] = $value->id_evento;
                                $dados['tabela_participantes_eventos'][$key]['url'] = base_url();
                                $dados['id_evento'] = $value->id_evento;
                            }
                        }
                    }
                }
                $dados['conteudo_participantes_eventos'] = $this->parser->parse('adm/gerenciar_participantes/eventos/tabela_participantes_eventos', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_participantes_eventos', $dados, TRUE);
            } else {
                $dados['conteudo_participantes_eventos'] = "<div class='col-sm-12'><h5> Não há nenhum evento registrado. </h5></div>";
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_participantes_eventos', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function alocar_participantes_evento_manual($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/participantes/eventos');
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
        $participante = array();
        $qtd_participantes = 0;
        $qtd_alocado = 0;
        try {
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->message2 != 0) {
                $qtd_participantes = $result->message2;
                $qtd_alocado = count($result->message);
                if (count($result->message) > 0) {
                    foreach ($result->message as $key => $value) {
                        $dados['tabela_participantes_eventos'][$key]['id_evento'] = $id_evento;
                        $dados['tabela_participantes_eventos'][$key]['class_adicionar'] = 'desabilitar';
                        $dados['tabela_participantes_eventos'][$key]['nome'] = $value->nome;
                        $dados['tabela_participantes_eventos'][$key]['cpf_matricula'] = $value->cpf_matricula;
                        $dados['tabela_participantes_eventos'][$key]['class_remover'] = '';
                        $dados['tabela_participantes_eventos'][$key]['id_participante'] = $value->id_participante;
                        $participante[$key]['id_participante'] = $value->id_participante;
                        $dados['tabela_participantes_eventos'][$key]['url'] = base_url();
                    }
                }
                try {
                    $url = base_url() . 'api/Evento_api/evento';
                    $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $id_evento], 'auth' => $this->auth]);
                    $r = json_decode($res->getBody());
                    $dados['nome_evento'] = $r->message[0]->nome;
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
                if ($qtd_alocado < $qtd_participantes) {
                    try {
                        $url = base_url() . 'api/Participante_api/participante_distinct';
                        $res = $this->client->request('GET', $url, ['query' => $participante, 'auth' => $this->auth]);
                        $r = json_decode($res->getBody());
                        if ($r->status) {
                            $indice = isset($key) ? $key + 1 : 0;
                            foreach ($r->message as $value) {
                                $dados['tabela_participantes_eventos'][$indice]['id_evento'] = $id_evento;
                                $dados['tabela_participantes_eventos'][$indice]['class_remover'] = 'desabilitar';
                                $dados['tabela_participantes_eventos'][$indice]['class_adicionar'] = '';
                                $dados['tabela_participantes_eventos'][$indice]['cpf_matricula'] = $value->cpf_matricula;
                                $dados['tabela_participantes_eventos'][$indice]['nome'] = $value->nome;
                                $dados['tabela_participantes_eventos'][$indice]['id_participante'] = $value->id_participante;
                                $dados['tabela_participantes_eventos'][$indice]['url'] = base_url();
                                $indice++;
                            }
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                }
                $dados['visualizar_alocar_manualmente'] = $this->parser->parse('adm/gerenciar_participantes/eventos/tabela_alocar_manualmente', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_alocar_manualmente', $dados, TRUE);
            } else {
                $dados['visualizar_alocar_manualmente'] = "<div class='col-sm-12'><h5> Não há nenhum participante registrado no sistema. </h5></div>";
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_alocar_manualmente', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function toggle_adicionar($id_evento = null, $id_participante = null) {
        if ($id_evento == null || $id_participante == null) {
            redirect('gerenciar/participantes/eventos');
        }
        try {
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_participante_fk' => $id_participante, 'id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                try {
                    $data['id_participante_fk'] = $id_participante;
                    $data['id_evento_fk'] = $id_evento;
                    $url = base_url() . 'api/Participante_api/participante_as_evento';
                    $res = $this->client->request('POST', $url, ['form_params' => $data, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        redirect('gerenciar/participantes/eventos/manual/' . $id_evento);
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
                redirect('gerenciar/participantes/eventos/manual/' . $id_evento);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function toggle_remover($id_evento = null, $id_participante = null) {
        if ($id_evento == null || $id_participante == null) {
            redirect('gerenciar/participantes/eventos');
        }
        try {
            $data['id_participante_fk'] = $id_participante;
            $data['id_evento_fk'] = $id_evento;
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('DELETE', $url, ['query' => $data, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/participantes/eventos/manual/' . $id_evento);
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

    public function desalocar($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/participantes/eventos');
        }
        try {
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/participantes/eventos/manual/' . $id_evento);
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

    public function visualizar_participantes($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/participantes/eventos');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        $dados['id_evento'] = $id_evento;
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
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['tabela_participantes_eventos'][$key]['id_evento'] = $id_evento;
                    $dados['tabela_participantes_eventos'][$key]['nome'] = $value->nome;
                    $dados['tabela_participantes_eventos'][$key]['cpf_matricula'] = $value->cpf_matricula;
                }
                $dados['conteudo_visualizar'] = $this->parser->parse('adm/gerenciar_participantes/eventos/tabela_visualizar_participantes', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_participantes', $dados, TRUE);
            } else {
                $dados['conteudo_visualizar'] = "<div class='col-sm-12'><h5>Não há nenhum participante alocado neste evento.</h5></div>";
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/visualizar_participantes', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function alocar_participantes_planilha($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/participantes/eventos');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de participantes';
        $dados['display'] = 'none';
        $participantes = array();
        $achou_erro = false;
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
        if (!empty($form['participante'])) {
            $linhas = explode("\n", $form['participante']);
            foreach ($linhas as $key => $value) {
                $registro = explode("\t", $value);
                if (count($registro) == 1 && $registro[0] == "") {
                    continue;
                } else {
                    if (count($registro) == 2) {
                        if ((filter_var($registro[1], FILTER_SANITIZE_NUMBER_INT)) !== '') {
                            $participantes[$key]['nome'] = $registro[0];
                            $participantes[$key]['cpf_matricula'] = str_replace("-", "", filter_var($registro[1], FILTER_SANITIZE_NUMBER_INT));
                        } else {
                            $dados['msg_erro'] = "Erro no formato dos dados, verifique o tutorial.";
                            $dados['display'] = 'block';
                            $dados['cor_alert'] = 'danger';
                            $achou_erro = true;
                            $participantes = array();
                            break;
                        }
                    } else {
                        $dados['msg_erro'] = "Erro no formato dos dados, verifique o tutorial.";
                        $dados['display'] = 'block';
                        $dados['cor_alert'] = 'danger';
                        $achou_erro = true;
                        $participantes = array();
                        break;
                    }
                }
            }
            if (count($participantes) > 0) {
                foreach ($participantes as $value) {
                    try {
                        $url = base_url() . 'api/Participante_api/participante_matricula';
                        $res = $this->client->request('GET', $url, ['query' => ['cpf_matricula' => $value['cpf_matricula']], 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if (count($result->message) == 0) {
                            try {
                                $url = base_url() . 'api/Participante_api/participante';
                                $res = $this->client->request('POST', $url, ['form_params' => $value, 'auth' => $this->auth]);
                                $result1 = json_decode($res->getBody());
                                if ($result1->status) {
                                    $evento_participante['id_participante_fk'] = $result1->message2;
                                    $evento_participante['id_evento_fk'] = $id_evento;
                                    try {
                                        $url = base_url() . 'api/Participante_api/participante_as_evento';
                                        $res = $this->client->request('POST', $url, ['form_params' => $evento_participante, 'auth' => $this->auth]);
                                        $result2 = json_decode($res->getBody());
                                        if (!$result2->status) {
                                            $dados['msg_erro'] = "Erro no formato dos dados, verifique o tutorial.";
                                            $dados['display'] = 'block';
                                            $dados['cor_alert'] = 'danger';
                                            $achou_erro = true;
                                            break;
                                        }
                                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                        $response = $ex->getResponse();
                                        $responseBodyAsString = $response->getBody()->getContents();
                                        $dados = $responseBodyAsString;
                                        echo $dados;
                                    }
                                } else {
                                    echo $result1->message;
                                }
                            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                $response = $ex->getResponse();
                                $responseBodyAsString = $response->getBody()->getContents();
                                $dados = $responseBodyAsString;
                                echo $dados;
                            }
                        } else {
                            $evento_participante2['id_participante_fk'] = $result->message[0]->id_participante;
                            $evento_participante2['id_evento_fk'] = $id_evento;
                            try {
                                $url = base_url() . 'api/Participante_api/participante_as_evento';
                                $res = $this->client->request('GET', $url, ['query' => $evento_participante2, 'auth' => $this->auth]);
                                $result3 = json_decode($res->getBody());
                                if (count($result3->message) == 0) {
                                    try {
                                        $url = base_url() . 'api/Participante_api/participante_as_evento';
                                        $res = $this->client->request('POST', $url, ['form_params' => $evento_participante2, 'auth' => $this->auth]);
                                        $result4 = json_decode($res->getBody());
                                        if (!$result4->status) {
                                            $dados['msg_erro'] = "Erro no formato dos dados, verifique o tutorial.";
                                            $dados['display'] = 'block';
                                            $dados['cor_alert'] = 'danger';
                                            $achou_erro = true;
                                            break;
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
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                }
                if (!$achou_erro) {
                    $dados['msg_erro'] = "Sucesso ao alocar participantes";
                    $dados['display'] = 'block';
                    $dados['cor_alert'] = 'success';
                }
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/eventos/alocar_planilha', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

}
