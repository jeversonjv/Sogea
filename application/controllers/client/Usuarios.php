<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

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

    public function gerenciar_usuarios($tipo_usuario = null) {
        if ($this->session->userdata('tipo_usuario') <= 1) {
            redirect('Inicio');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de usuários';
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

        if (isset($tipo_usuario)) {
            try {
                $url = base_url() . 'api/Usuario_api/usuario';
                $res = $this->client->request('GET', $url, ['query' => ['tipo_usuario' => $tipo_usuario], 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if (count($result->message) > 0) {
                    $QtdUsers = count($result->message);
                    foreach ($result->message as $key => $value) {
                        if ($value->id_usuario != $this->session->userdata('id_usuario')) {
                            $dados['usuarios'][$key]['id_usuario'] = $value->id_usuario;
                            $dados['usuarios'][$key]['login'] = $value->login_usuario;
                            $dados['usuarios'][$key]['tipo_usuario'] = $tipo_usuario;
                            $dados['usuarios'][$key]['email'] = $value->email;
                            $evento = isset($value->id_evento_fk) ? $value->id_evento_fk : "Não está associado a nenhum evento.";
                            if (is_numeric($evento)) {
                                try {
                                    $url = base_url() . 'api/Evento_api/evento';
                                    $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $evento], 'auth' => $this->auth]);
                                    $result = json_decode($res->getBody());
                                    if (count($result->message) > 0) {
                                        $dados['usuarios'][$key]['evento'] = $result->message[0]->nome;
                                    }
                                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                    $response = $ex->getResponse();
                                    $responseBodyAsString = $response->getBody()->getContents();
                                    $dados = $responseBodyAsString;
                                }
                            } else {
                                $dados['usuarios'][$key]['evento'] = $evento;
                            }
                            $dados['usuarios'][$key]['url'] = base_url();
                            if ($tipo_usuario == 2) {
                                if ($QtdUsers == 2) {
                                    $dados['usuarios'][$key]['desabilitado'] = 'disabled';
                                    $dados['display'] = 'block';
                                    $dados['cor_alert'] = 'danger';
                                    $dados['msg_erro'] = 'Não é possivel excluir, quando há apenas um administrador.';
                                }
                            } else {
                                $dados['usuarios'][$key]['desabilitado'] = '';
                            }
                        }
                    }
                    $dados['conteudo_usuarios'] = $this->parser->parse('adm/gerenciar_usuarios/tabela_usuarios', $dados, TRUE);
                } else {
                    $dados['conteudo_usuarios'] = "<div class='col-sm-12'><h5>Nenhum usuário desse tipo cadastrado.</h5></div>";
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
            }
            switch ($tipo_usuario) {
                case 2:
                    $dados['tipo_usuario'] = "Administradores do sistema";
                    break;
                case 1:
                    $dados['tipo_usuario'] = 'Gerenciadores dos eventos';
                    break;
                case 0:
                    $dados['tipo_usuario'] = ' Colaboradores dos eventos';
                    break;
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_usuarios/visualizar_usuarios', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function adicionar_usuario() {
        if ($this->session->userdata('tipo_usuario') <= 1) {
            redirect('Inicio');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de usuários';
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
        if (isset($form['login_usuario']) && isset($form['email']) && isset($form['tipo_usuario'])) {
            $this->form_validation->set_rules('login_usuario', 'Login', 'required');
            $this->form_validation->set_rules('email', 'E-mail', 'required');
            $this->form_validation->set_rules('tipo_usuario', 'Tipo Usuario', 'required');
            $form['senha_usuario'] = sha1("123");
            if ($form['id_evento_fk'] == "") {
                unset($form['id_evento_fk']);
            }
            if ($this->form_validation->run()) {
                try {
                    $url = base_url() . 'api/Usuario_api/usuario';
                    $res = $this->client->request('POST', $url, ['form_params' => $form, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        redirect('gerenciar/usuario/' . $form["tipo_usuario"]);
                    } else {
                        echo $result->message;
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $dados['msg_erro'] = 'Usuário/Email já cadastrado no sistema.';
            $dados['display'] = 'block';
            $dados['cor_alert'] = 'danger';
                }
            } else {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_professor', $dados, TRUE);
            }
        }
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['ativo' => 1, 'estado' => '0'], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1 && $value->estado == 0 && $value->sub_id_evento == null) {
                        $dados['evento_associado'][$key]['id_evento_fk'] = $value->id_evento;
                        $dados['evento_associado'][$key]['nome'] = $value->nome;
                        $dados['evento_associado'][$key]['desabilitado'] = '';
                        $dados['evento_associado'][$key]['selecionado'] = '';
                    }
                }
            } else {
                $dados['evento_associado'][0]['id_evento_fk'] = "";
                $dados['evento_associado'][0]['nome'] = "Não eventos cadastrados";
                $dados['evento_associado'][0]['selecionado'] = 'selected';
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $dados['msg_erro'] = 'Usuarios já cadastrado no sistema.';
            $dados['display'] = 'block';
            $dados['cor_alert'] = 'danger';
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_usuarios/adicionar_usuario', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function editar_usuario($id = null, $tipo_usuario = null) {
        if ($this->session->userdata('tipo_usuario') <= 1) {
            redirect('Inicio');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de usuários';
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
        if ($tipo_usuario == 2) {
            try {
                $url = base_url() . 'api/Usuario_api/usuario';
                $res = $this->client->request('GET', $url, ['query' => ['tipo_usuario' => 2], 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    $qtdAdmins = count($result->message);
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
        $form = $this->input->post();
        if (isset($form['login_usuario']) || isset($form['email']) || isset($form['senha_usuario']) || isset($form['tipo_usuario']) || isset($form['id_evento_fk'])) {
            $form['id_usuario'] = $id;
            if ($form['senha_usuario'] == "") {
                unset($form['senha_usuario']);
            } else {
                $form['senha_usuario'] = sha1($form['senha_usuario']);
            }
            if ($form['id_evento_fk'] == "") {
                try {
                    $url = base_url() . 'api/Usuario_api/usuario_set_evento_null';
                    $res = $this->client->request('PUT', $url, ['form_params' => ['id_usuario' => $id], 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (!$result->status) {
                        echo $result->message;
                    } else {
                        unset($form['id_evento_fk']);
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
            if ($tipo_usuario == 2 && $qtdAdmins == 1) {
                unset($form['tipo_usuario']);
            }
            try {
                $url = base_url() . 'api/Usuario_api/usuario';
                $res = $this->client->request('PUT', $url, ['form_params' => $form, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    redirect('gerenciar/usuario/' . $tipo_usuario);
                } else {
                    echo $result->message;
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_usuario', $dados, TRUE);
            }
        }
        try {
            $url = base_url() . 'api/Usuario_api/usuario';
            $res = $this->client->request('GET', $url, ['query' => ['id_usuario' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                $id_evento_fk = -1;
                foreach ($result->message as $key => $value) {
                    $dados['editar_usuario'][$key]['login_usuario'] = $value->login_usuario;
                    $dados['editar_usuario'][$key]['email'] = $value->email;
                    $dados['editar_usuario'][$key]['id_usuario'] = $id;
                    $dados['editar_usuario'][$key]['id_tipo_usuario'] = $tipo_usuario;
                    if ($tipo_usuario == 2) {
                        $dados['editar_usuario'][$key]['desabilitado'] = $qtdAdmins == 1 ? "disabled" : "";
                    }
                    if ($value->id_evento_fk != "") {
                        $id_evento_fk = $value->id_evento_fk;
                    }
                    if ($value->tipo_usuario == $tipo_usuario) {
                        $dados['editar_usuario'][$key]['usuario'] = $this->menu->retorna_array_usuario($value->tipo_usuario);
                    }
                    try {
                        $url = base_url() . 'api/Evento_api/evento';
                        $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if (count($result->message) > 0) {
                            foreach ($result->message as $k => $v) {
                                $evento[$k]['id_evento_fk'] = $v->id_evento;
                                $evento[$k]['nome'] = $v->nome;
                                if (($id_evento_fk != -1) && ($id_evento_fk == $v->id_evento)) {
                                    $evento[$k]['selecionado'] = 'selected';
                                } else {
                                    $evento[$k]['selecionado'] = '';
                                }
                            }
                        } else {
                            $dados['display'] = 'block';
                            $dados['cor_alert'] = 'danger';
                            $dados['msg_erro'] = "Nenhum evento cadastrado!";
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $dados['display'] = 'block';
                        $dados['cor_alert'] = 'danger';
                        $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_usuario', $dados, TRUE);
                    }
                    $dados['editar_usuario'][$key]['evento_associado'] = $evento;
                    $dados['editar_usuario'][$key]['url'] = base_url();
                }
            } else {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = "Nenhum dado para atualizar!";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $dados['display'] = 'block';
            $dados['cor_alert'] = 'danger';
            $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_usuario', $dados, TRUE);
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_usuarios/editar_usuario', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function excluir_usuario($id = null, $tipo_usuario = null) {
        if ($this->session->userdata('tipo_usuario') <= 1) {
            redirect('Inicio');
        }
        if ($id == null || $tipo_usuario == null) {
            redirect('gerenciar/usuario/0');
        }

        if ($tipo_usuario == 2) {
            try {
                $url = base_url() . 'api/Usuario_api/usuario';
                $res = $this->client->request('GET', $url, ['query' => ['tipo_usuario' => $tipo_usuario], 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if (count($result->message) <= 2) {
                    redirect('gerenciar/usuario/2');
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
                echo $dados;
            }
        }
        try {
            $url = base_url() . 'api/Usuario_api/usuario';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_usuario' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/usuario/' . $tipo_usuario);
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

    public function gerenciar_perfil() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de usuários';
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
        if (count($form) == 3) {
            if ($form['senha_nova'] == $form['rep_senha_nova']) {

                if (strlen($form['senha_nova']) >= 8) {
                    $id_usuario['id_usuario'] = $this->session->userdata('id_usuario');
                    try {
                        $url = base_url() . 'api/Usuario_api/usuario';
                        $res = $this->client->request('GET', $url, ['query' => $id_usuario, "auth" => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->message[0]->senha_usuario == sha1($form['senha_atual'])) {
                            $user['senha_usuario'] = sha1($form['senha_nova']);
                            $user['id_usuario'] = $id_usuario['id_usuario'];
                            try {
                                $url = base_url() . 'api/Usuario_api/usuario';
                                $res = $this->client->request('PUT', $url, ['form_params' => $user, "auth" => $this->auth]);
                                $result = json_decode($res->getBody());
                                if ($result->status) {
                                    $dados['display'] = 'blcok';
                                    $dados['cor_alert'] = 'success';
                                    $dados['msg_erro'] = 'Senha atualizada com sucesso.';
                                } else {
                                    echo "Erro";
                                }
                            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                $response = $ex->getResponse();
                                $responseBodyAsString = $response->getBody()->getContents();
                                $dados = $responseBodyAsString;
                            }
                        } else {
                            $dados['display'] = 'blcok';
                            $dados['cor_alert'] = 'danger';
                            $dados['msg_erro'] = 'A senha atual não é válida.';
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                    }
                } else {
                    $dados['display'] = 'blcok';
                    $dados['cor_alert'] = 'danger';
                    $dados['msg_erro'] = 'Está senha não é permitida, é necessário uma senha de 8 dígitos.';
                }
            } else {
                $dados['display'] = 'blcok';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = 'Nova senha e repetir nova senha não se coincidem.';
            }
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_usuarios/perfil_usuario', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function historico_presenca() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de usuários';
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
            $dados['usuarios'] = array();
            $url = base_url() . 'api/Usuario_api/usuario';
            $res = $this->client->request('GET', $url, ["auth" => $this->auth]);
            if ($this->session->userdata('tipo_usuario') == 2) {
                $result = json_decode($res->getBody());
                foreach ($result->message as $key => $value) {
                    $dados['usuarios'][$key]['login'] = $value->login_usuario;
                    $dados['usuarios'][$key]['email'] = $value->email;
                    $dados['usuarios'][$key]['id_usuario'] = $value->id_usuario;
                    $dados['usuarios'][$key]['url'] = base_url();
                }
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_usuarios/historico_presenca', $dados, TRUE);
                $this->parser->parse('restrita/layout_restrita', $dados);
            } else {
                $this->historico_presenca_usuario($this->session->userdata('id_usuario'));
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
    }

    public function historico_presenca_usuario($id_usuario) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de usuários';
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
            $url = base_url() . 'api/Usuario_api/usuario_presenca';
            $res = $this->client->request('GET', $url, ["query" => ['id_usuario_fk' => $id_usuario], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                $dados['usuarios'] = array();
                foreach ($result->message as $key => $value) {
                    $dados['usuarios'][$key]['nome_participante'] = $value->nome_participante;
                    $dados['usuarios'][$key]['cpf_matricula'] = $value->cpf_matricula;
                    $dados['usuarios'][$key]['nome_ocorrencia'] = $value->nome_ocorrencia;
                    $dados['usuarios'][$key]['nome_evento'] = $value->nome_evento;
                    $horario = explode(' ', $value->horario);
                    $data = date("d/m/Y", strtotime($horario[0]));
                    $hora = explode(':', $horario[1]);
                    $ohorario = $data . ' - ' . $hora[0] . ':' . $hora[1];
                    $dados['usuarios'][$key]['horario'] = $ohorario;
                    $dados['usuarios'][$key]['url'] = base_url();
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_usuarios/historico_presenca_participantes', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

}
