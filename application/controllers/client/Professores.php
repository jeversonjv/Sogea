<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Professores extends CI_Controller {

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
            if ($this->session->userdata('tipo_usuario') <= 1) {
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

    public function gerenciar_professores() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de professores';
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
            $url = base_url() . 'api/Professor_api/professor';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                if ($result->status) {
                    foreach ($result->message as $key => $value) {
                        $dados['tabela_professores'][$key]['id_professor'] = $value->id_professor;
                        $dados['tabela_professores'][$key]['nome'] = $value->nome;
                        $dados['tabela_professores'][$key]['siape'] = $value->siape;
                        $dados['tabela_professores'][$key]['url'] = base_url();
                    }
                    $dados['conteudo_professor'] = $this->parser->parse('adm/gerenciar_professores/tabela_professores', $dados, TRUE);
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_professores/visualizar_professor', $dados, TRUE);
                } else {
                    echo 'Erro';
                }
            } else {
                $dados['conteudo_professor'] = '<div class="col-sm-12"><h5>Não há nenhum professor registrado no sistema.</h5></div>';
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_professores/visualizar_professor', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }

        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerenciar_horario_professor($id_professor = null, $id_dia = null) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de professores';
        $dados['display'] = 'none';
        $dados['conteudo_visualizar'] = '';
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
            $url = base_url() . 'api/Professor_api/Select_dia_semana';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['semana'][$key]['id_dia_semana'] = $value->id_dia_semana;
                    $dados['semana'][$key]['dia_semana'] = $value->dia_semana;
                    $dados['semana'][$key]['id_professor'] = $id_professor;
                    $dados['semana'][$key]['url'] = base_url();
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        try {
            $url = base_url() . 'api/Professor_api/professor';
            $res = $this->client->request('GET', $url, ['query' => ['id_professor' => $id_professor], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                $dados['nome_professor'] = $result->message[0]->nome;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        if ($id_dia) {
            $data['id_professor'] = $id_professor;
            $data['dia_semana_fk'] = $id_dia;
            try {
                $url = base_url() . 'api/Professor_api/professor_as_horario';
                $res = $this->client->request('GET', $url, ['query' => $data, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if (count($result->message) > 0) {
                    $dados['dia_semana'] = $result->message[0]->dia_semana;
                    foreach ($result->message as $key => $value) {
                        $dados['horario_professor'][$key]['id_professor_fk'] = $value->id_professor_fk;
                        $dados['horario_professor'][$key]['id_horario_fk'] = $value->id_horario_fk;
                        $hora_inicio = explode(':', $value->hora_inicio);
                        $hora_final = explode(':', $value->hora_final);
                        $dados['horario_professor'][$key]['hora_inicio'] = $hora_inicio[0] . ":" . $hora_inicio[1];
                        $dados['horario_professor'][$key]['hora_final'] = $hora_final[0] . ":" . $hora_final[1];
                        $dados['horario_professor'][$key]['id_dia'] = $id_dia;
                        $dados['horario_professor'][$key]['btn'] = $value->trabalha == 1 ? 'btn-success' : 'btn-danger';
                        $dados['horario_professor'][$key]['img_btn'] = $value->trabalha == 1 ? 'fa fa-check-square' : 'fa fa-window-close';
                        $dados['horario_professor'][$key]['trabalha'] = $value->trabalha;
                        $dados['horario_professor'][$key]['url'] = base_url();
                    }
                    $dados['conteudo_visualizar'] = $this->parser->parse('adm/gerenciar_professores/tabela_horario_professor', $dados, TRUE);
                } else {
                    $dados['conteudo_visualizar'] = 'Não possui horário';
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_professores/visualizar_horario', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function editar_professor($id = null) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de professores';
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
        if (!$id) {
            redirect('gerenciar/professores');
        }
        try {
            $url = base_url() . 'api/Professor_api/professor';
            $res = $this->client->request('GET', $url, ['query' => ['id_professor' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['editar_professor'][$key]['id_professor'] = $value->id_professor;
                    $dados['editar_professor'][$key]['nome'] = $value->nome;
                    $dados['editar_professor'][$key]['siape'] = $value->siape;
                    $dados['editar_professor'][$key]['url'] = base_url();
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_professores/editar_professor', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function salvar_dados_professor() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de professores';
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
        if (count($form) > 1) {
            $data['id_professor'] = $form['id_professor'];
            if (isset($form['nome']))
                $data['nome'] = $form['nome'];
            if (isset($form['siape']))
                $data['siape'] = $form['siape'];
            try {
                $url = base_url() . 'api/Professor_api/professor';
                $res = $this->client->request('PUT', $url, ['form_params' => $data, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    redirect('gerenciar/professores');
                } else {
                    echo $result->message;
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_professor', $dados, TRUE);
            }
        } else {
            $dados['display'] = 'block';
            $dados['cor_alert'] = 'danger';
            $dados['msg_erro'] = 'Nenhum dado para atualizar';
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_professores/adicionar_professor', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function toggle_horario($id_professor = null, $id_horario = null, $id_dia = null, $trabalha = null) {
        if ($id_professor == null || $id_horario == null || $id_dia == null || $trabalha == null) {
            redirect('gerenciar/professores');
        }
        try {
            $data['trabalha'] = $trabalha == 1 ? 0 : 1;
            $data['id_professor_fk'] = $id_professor;
            $data['id_horario_fk'] = $id_horario;
            $url = base_url() . 'api/Professor_api/professor_horario';
            $res = $this->client->request('PUT', $url, ['form_params' => $data, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/professores/horario/' . $id_professor . '/' . $id_dia);
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

    public function excluir_professor($id = null) {
        if (!$id) {
            redirect('gerenciar/professores');
        }
        try {
            $url = base_url() . 'api/Professor_api/professor';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_professor' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/professores');
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

    public function adicionar_professor() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de professores';
        $dados['display'] = 'none';
        $ohorario = array();
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
        if (isset($form['nome']) && isset($form['siape'])) {
            $this->form_validation->set_rules('nome', 'Nome', 'required');
            $this->form_validation->set_rules('siape', 'Siape', 'required');
            if ($this->form_validation->run()) {
                try {
                    $form = $this->security->xss_clean($form);
                    $url = base_url() . 'api/Professor_api/professor';
                    $res = $this->client->request('POST', $url, ['form_params' => $form, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        $horario['id_professor'] = $result->message;
                        try {
                            $url = base_url() . 'api/Professor_api/horario';
                            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
                            $result = json_decode($res->getBody());
                            foreach ($result->message as $key => $value) {
                                $ohorario[$key]['id_professor_fk'] = $horario['id_professor'];
                                $ohorario[$key]['id_horario_fk'] = $value->id_horario;
                                $ohorario[$key]['trabalha'] = 0;
                            }
                        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                            $response = $ex->getResponse();
                            $responseBodyAsString = $response->getBody()->getContents();
                            $dados = $responseBodyAsString;
                            echo $dados;
                        }
                        try {
                            $url = base_url() . 'api/Professor_api/professor_horario';
                            $res = $this->client->request('POST', $url, ['form_params' => $ohorario, 'auth' => $this->auth]);
                            $result = json_decode($res->getBody());
                            if (!$result->status) {
                                echo $result->message;
                            } else {
                                redirect('gerenciar/professores');
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
                    $dados['display'] = 'block';
                    $dados['cor_alert'] = 'danger';
                    $dados['msg_erro'] = $this->parser->parse('adm/possiveis_erros/erro_inserir_professor', $dados, TRUE);
                }
            } else {
                echo 'Erro ao tentar cadastrar professor!';
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_professores/adicionar_professor', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

}
