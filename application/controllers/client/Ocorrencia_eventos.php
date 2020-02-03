<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ocorrencia_eventos extends CI_Controller {

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

    public function gerenciar_ocorrencias() {
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
            if (count($result->message) > 0) {
                $dados['eventos'] = array();
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1 && $value->estado == 0) {
                        $dados['eventos'][$key]['nome'] = $value->nome;
                        $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                        $dados['eventos'][$key]['url'] = base_url();
                    }
                }
                if (count($dados['eventos']) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento validado ou registrado. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_ocorrencias/tabela_eventos', $dados, TRUE);
                }
            } else {
                $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento validado ou registrado. </h5></div>";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function ocorrencia_evento($id_evento = null) {
        if ($id_evento == null) {
            redirect('gerenciar/ocorrencias');
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
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['id_evento_fk'] = $id_evento;
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['ocorrencia'][$key]['nome'] = $value->nome;
                    $dados['ocorrencia'][$key]['id_ocorrencia'] = $value->id_ocorrencia;
                    $dados['ocorrencia'][$key]['id_evento_fk'] = $value->id_evento_fk;
                    $hora = explode(' ', $value->horario);
                    $ahora = explode(':', $hora[1]);
                    $dados['ocorrencia'][$key]['data'] = date('d/m/Y', strtotime($hora[0]));
                    $dados['ocorrencia'][$key]['horario'] = $ahora[0] . ":" . $ahora[1];
                    $dados['ocorrencia'][$key]['url'] = base_url();
                }

                $dados['ocorrencia_conteudo'] = $this->parser->parse('adm/gerenciar_ocorrencias/tabela_ocorrencia', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_ocorrencias/visualizar_tabela_ocorrencia', $dados, TRUE);
            } else {
                $dados['ocorrencia_conteudo'] = "<div class='col-sm-12'><h5> Não há nenhuma ocorrência resgistrada para este evento. </h5></div>";
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_ocorrencias/visualizar_tabela_ocorrencia', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function adicionar_ocorrencia($id_evento = null) {
        if ($id_evento == null) {
            redirect('gerenciar/ocorrencias');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de eventos';
        $dados['display'] = 'none';
        $dados['id_evento_fk'] = $id_evento;
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
            $this->form_validation->set_rules('nome', 'Nome', 'Required|max_length[20]');
            $this->form_validation->set_rules('data', 'Data', 'Required');
            $this->form_validation->set_rules('horario', 'Horario', 'Required');
            if ($this->form_validation->run()) {
                if ($form['nome'] == "" || $form['horario'] == "" || $form['data'] == "") {
                    $dados['cor_alert'] = 'danger';
                    $dados['display'] = 'block';
                    $dados['msg_erro'] = 'Preencha o formulário corretamente.';
                } else {
                    $data['nome'] = ucfirst(strtolower($form['nome']));
                    $data['horario'] = $form['data'] . ' ' . $form['horario'];
                    $data['id_evento_fk'] = $id_evento;
                    try {
                        $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
                        $res = $this->client->request('POST', $url, ['form_params' => $data, 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if (!$result->status) {
                            $dados['cor_alert'] = 'danger';
                            $dados['display'] = 'block';
                            $dados['msg_erro'] = 'Preencha o formulário corretamente.';
                            unset($form);
                        } else {
                            $dados['cor_alert'] = 'success';
                            $dados['display'] = 'block';
                            $dados['msg_erro'] = 'Inserido com sucesso.';
                            unset($form);
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                        echo $dados;
                    }
                }
            } else {
                $dados['cor_alert'] = 'danger';
                $dados['display'] = 'block';
                $dados['msg_erro'] = 'Preencha o formulário corretamente.';
            }
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_ocorrencias/adicionar_ocorrencias', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function editar_ocorrencia($id_ocorrencia = null) {
        if ($id_ocorrencia == null) {
            redirect('gerenciar/ocorrencias');
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

        $form = $this->input->post();
        if (count($form) == 3) {
            if ($form['nome'] == "" || $form['horario'] == "" || $form['data'] == "") {
                $dados['cor_alert'] = 'danger';
                $dados['display'] = 'block';
                $dados['msg_erro'] = 'Caso não for editar o campo não apague o valor, deixe preenchido.';
            } else {
                $data['id_ocorrencia'] = $id_ocorrencia;
                $data['nome'] = ucfirst(strtolower($form['nome']));
                $data['horario'] = $form['data'] . ' ' . $form['horario'];

                try {
                    $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
                    $res = $this->client->request('PUT', $url, ['form_params' => $data, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());

                    if (!$result->status) {
                        echo "Erro";
                    } else {
                        $dados['cor_alert'] = 'success';
                        $dados['display'] = 'block';
                        $dados['msg_erro'] = 'Atualizado com sucesso.';
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
        } else {
            $dados['cor_alert'] = 'danger';
            $dados['display'] = 'block';
            $dados['msg_erro'] = 'Caso não for editar o campo não apague o valor, deixe preenchido.';
        }

        try {
            $id['id_ocorrencia'] = $id_ocorrencia;
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ['query' => $id, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            foreach ($result->message as $key => $value) {
                $dados['ocorrencia'][$key]['nome'] = $value->nome;
                $dados['ocorrencia'][$key]['id_ocorrencia'] = $value->id_ocorrencia;
                $hora = explode(" ", $value->horario);
                $dados['ocorrencia'][$key]['data'] = $hora[0];
                $dados['ocorrencia'][$key]['horario'] = $hora[1];
                $dados['ocorrencia'][$key]['id_evento_fk'] = $value->id_evento_fk;
                $dados['ocorrencia'][$key]['url'] = base_url();
                $dados['id_evento_fk'] = $value->id_evento_fk;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_ocorrencias/editar_ocorrencia', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function delete_ocorrencia($id_ocorrencia = null, $id_evento_fk = null) {
        if ($id_evento_fk == null || $id_ocorrencia == null) {
            redirect('gerenciar/ocorrencias');
        }
        try {
            $id['id_ocorrencia'] = $id_ocorrencia;
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('DELETE', $url, ['query' => $id, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/ocorrencias/evento/' . $id_evento_fk);
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

}
