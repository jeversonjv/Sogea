<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Restrita extends CI_Controller {

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
            $this->nav = $this->menu->get_menu_adm($this->session->userdata('tipo_usuario'));
        }
    }

    public function verify_pass() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['display'] = 'none';

        $id_usuario['id_usuario'] = $this->session->userdata('id_usuario');
        try {
            $url = base_url() . 'api/Usuario_api/usuario';
            $res = $this->client->request('GET', $url, ['query' => $id_usuario, "auth" => $this->auth]);
            $result = json_decode($res->getBody());

            if (count($result->message) > 0) {
                if ($result->message[0]->senha_usuario == sha1("123")) {
                    $form = $this->input->post();
                    if (count($form) == 2) {
                        if ($form['senha'] == $form['rep_senha']) {
                            if ($form['senha'] != '123') {
                                if (strlen($form['senha']) >= 8) {
                                    $senha['senha_usuario'] = sha1($form['senha']);
                                    $senha['id_usuario'] = $id_usuario['id_usuario'];
                                    try {
                                        $url = base_url() . 'api/Usuario_api/usuario';
                                        $res = $this->client->request('PUT', $url, ['form_params' => $senha, "auth" => $this->auth]);
                                        $result = json_decode($res->getBody());
                                        if ($result->status) {
                                            redirect('Restrita');
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
                                    $dados['color'] = 'danger';
                                    $dados['msg'] = 'Senha muito curta, é necessário pelo menos 8 digitos.';
                                }
                            } else {
                                $dados['display'] = 'blcok';
                                $dados['color'] = 'danger';
                                $dados['msg'] = 'Está senha não é permitida.';
                            }
                        } else {
                            $dados['display'] = 'blcok';
                            $dados['color'] = 'danger';
                            $dados['msg'] = 'As senhas não se coincidem.';
                        }
                    }
                    $this->parser->parse('verify_pass', $dados);
                } else {
                    redirect('Restrita');
                }
            } else {
                redirect('Inicio');
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
    }

    public function index() {
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

        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = '';
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
        $dados['conteudo'] = $this->parser->parse('restrita/msg_inicial', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function deslogar() {
        $this->session->sess_destroy();
        redirect('Restrita');
    }

}
