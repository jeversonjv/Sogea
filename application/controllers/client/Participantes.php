<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Participantes extends CI_Controller {

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

    public function index() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de participantes';
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
        if ($this->session->userdata('tipo_usuario') == 1) {
            $dados['link_participantes'] = "";
            $dados['link_evento'] = $this->parser->parse('adm/gerenciar_participantes/link_eventos', $dados, TRUE);
            $dados['link_evento_pai'] = $this->parser->parse('adm/gerenciar_participantes/link_eventos_pai', $dados, TRUE);
        }
        if ($this->session->userdata('tipo_usuario') == 2) {
            $dados['link_participantes'] = $this->parser->parse('adm/gerenciar_participantes/link_participantes', $dados, TRUE);
            $dados['link_evento'] = $this->parser->parse('adm/gerenciar_participantes/link_eventos', $dados, TRUE);
            $dados['link_evento_pai'] = $this->parser->parse('adm/gerenciar_participantes/link_eventos_pai', $dados, TRUE);
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/caminho_participantes', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerenciar_participantes() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de participantes';
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
            $url = base_url() . 'api/Participante_api/participante';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['tabela_participantes'][$key]['id_participante'] = $value->id_participante;
                    $dados['tabela_participantes'][$key]['nome'] = $value->nome;
                    $dados['tabela_participantes'][$key]['cpf_matricula'] = $value->cpf_matricula;
                    $dados['tabela_participantes'][$key]['url'] = base_url();
                }
                $dados['conteudo_participantes_sistema'] = $this->parser->parse('adm/gerenciar_participantes/sistema/tabela_participantes', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/sistema/visualizar_participantes_sistema', $dados, TRUE);
            } else {
                $dados['conteudo_participantes_sistema'] = '<div class="col-sm-12"><h5>Não há nenhum participante registrado.</h5></div>';
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/sistema/visualizar_participantes_sistema', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo $responseBodyAsString;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function adicionar_participante() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de participantes';
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
        if (count($form) >= 2) {
            $this->form_validation->set_rules('nome', 'Nome', 'required');
            $this->form_validation->set_rules('cpf_matricula', 'CPF/Matricula', 'required|min_length[11]|max_length[12]');
            $oCpf = explode(".", $form['cpf_matricula']);
            if (count($oCpf) > 1) {
                $dados['msg_erro'] = 'Digite o CPF/Matricula sem pontuação.';
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
            } else {
                if ($this->form_validation->run()) {
                    try {
                        $form = $this->security->xss_clean($form);
                        $url = base_url() . 'api/Participante_api/participante';
                        $res = $this->client->request('POST', $url, ['form_params' => $form, 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status) {
                            redirect('gerenciar/participantes/sistema');
                        } else {
                            echo 'Erro';
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $dados['msg_erro'] = 'CPF/Matricula já cadastrada no sistema.';
                        $dados['display'] = 'block';
                        $dados['cor_alert'] = 'danger';
                    }
                } else {
                    $dados['msg_erro'] = 'Digite o formulário corretamente.';
                    $dados['display'] = 'block';
                    $dados['cor_alert'] = 'danger';
                }
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/sistema/adicionar_participante', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function editar_participante($id = null) {
        if (!$id) {
            redirect('gerenciar/participantes/sistema');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de participantes';
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
            $url = base_url() . 'api/Participante_api/participante';
            $res = $this->client->request('GET', $url, ['query' => ['id_participante' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['editar_participante'][$key]['id_participante'] = $value->id_participante;
                    $dados['editar_participante'][$key]['nome'] = $value->nome;
                    $dados['editar_participante'][$key]['cpf_matricula'] = $value->cpf_matricula;
                    $dados['editar_participante'][$key]['url'] = base_url();
                }
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/sistema/tabela_participantes', $dados, TRUE);
            } else {
                $dados['conteudo'] = "Nenhum participante registrado.";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo $responseBodyAsString;
        }

        $form = $this->input->post();
        if (count($form) >= 2) {
            $this->form_validation->set_rules('nome', 'Nome', 'required');
            $this->form_validation->set_rules('cpf_matricula', 'CPF/Matricula', 'required|min_length[11]|max_length[12]');
            $oCpf = explode(".", $form['cpf_matricula']);
            if (count($oCpf) > 1) {
                $dados['msg_erro'] = 'Digite o CPF/Matricula sem pontuação.';
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
            } else {
                if ($this->form_validation->run()) {
                    try {
                        $form['id_participante'] = $id;
                        $form = $this->security->xss_clean($form);
                        $url = base_url() . 'api/Participante_api/participante';
                        $res = $this->client->request('PUT', $url, ['form_params' => $form, 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status) {
                            redirect('gerenciar/participantes/sistema');
                        } else {
                            echo 'Erro';
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $dados['msg_erro'] = 'CPF/Matricula já cadastrada no sistema.';
                        $dados['display'] = 'block';
                        $dados['cor_alert'] = 'danger';
                    }
                } else {
                    $dados['msg_erro'] = 'Digite o formulário corretamente.';
                    $dados['display'] = 'block';
                    $dados['cor_alert'] = 'danger';
                }
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_participantes/sistema/editar_participante', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function excluir_participante($id = null) {
        if (!$id) {
            redirect('gerenciar/participantes/sistema');
        }
        try {
            $url = base_url() . 'api/Participante_api/participante';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_participante' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/participantes/sistema');
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo $responseBodyAsString;
        }
    }

}
