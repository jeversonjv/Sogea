<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Eventos_tipos extends CI_Controller {

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

    public function gerenciar_tipo() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de tipos';
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
            $url = base_url() . 'api/Evento_api/tipo';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                if ($result->status) {
                    foreach ($result->message as $key => $value) {
                        $dados['tabela_tipo'][$key]['id_tipo'] = $value->id_tipo;
                        $dados['tabela_tipo'][$key]['tipo'] = $value->tipo;
                        $dados['tabela_tipo'][$key]['url'] = base_url();
                    }
                    $dados['visualizar_tipo_eventos'] = $this->parser->parse('adm/gerenciar_tipo_eventos/tabela_tipos', $dados, TRUE);
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_tipo_eventos/visualizar_tipo_eventos', $dados, TRUE);
                } else {
                    echo "Erro";
                }
            } else {
                $dados['visualizar_tipo_eventos'] = '<div class="col-sm-12"><h5>Não há nenhum tipo registrado no sistema.</h5></div>';
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_tipo_eventos/visualizar_tipo_eventos', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function editar_tipo($idTipo = NULL) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de tipos';
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
        if (isset($form['tipo']) && isset($form['id_tipo'])) {
            try {
                $url = base_url() . 'api/Evento_api/tipo';
                $res = $this->client->request('PUT', $url, ['form_params' => $form, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    redirect('gerenciar/tipo');
                } else {
                    echo $result->message;
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
            }
        }
        try {
            $url = base_url() . 'api/Evento_api/tipo';
            $res = $this->client->request('GET', $url, ['query' => ['id_tipo' => $idTipo], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            foreach ($result->message as $key => $value) {
                $dados['editar_tipo'][$key]['id_tipo'] = $value->id_tipo;
                $dados['editar_tipo'][$key]['tipo'] = $value->tipo;
                $dados['editar_tipo'][$key]['url'] = base_url();
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_tipo_eventos/editar_tipo', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function adicionar_tipo() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de tipos';
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
        if (isset($form['tipo'])) {
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');
            if ($this->form_validation->run()) {
                try {
                    $url = base_url() . 'api/Evento_api/tipo';
                    $res = $this->client->request('POST', $url, ['form_params' => $form, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        redirect('gerenciar/tipo');
                    } else {
                        $result->message;
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $dados['display'] = 'block';
                    $dados['cor_alert'] = 'danger';
                    $dados['msg_erro'] = $this->parser->parse('possiveis_erros/erro_inserir_tipo_evento', $dados, TRUE);
                }
            } else {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'danger';
                $dados['msg_erro'] = $this->parser->parse('possiveis_erros/erro_inserir_tipo_evento', $dados, TRUE);
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_tipo_eventos/adicionar_tipo', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function excluir_tipo($id = null) {
        if (!$id) {
            redirect('gerenciar/tipo');
        }
        try {
            $url = base_url() . 'api/Evento_api/tipo';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_tipo' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            print_r($result);
            if ($result->status) {
                redirect('gerenciar/tipo');
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

}
