<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Horario_professor extends CI_Controller {

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

    public function gerenciar_horario() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de horários';
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
            $url = base_url() . 'api/Professor_api/horario_expediente';
            $res = $this->client->request('GET', $url, ["auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['horarios'][$key]['id_horario_expediente'] = $value->id_horario_expediente;
                    $hora_inicio = explode(':', $value->hora_inicio);
                    $hora_final = explode(':', $value->hora_final);
                    $dados['horarios'][$key]['hora_inicio'] = $hora_inicio[0] . ":" . $hora_inicio[1];
                    $dados['horarios'][$key]['hora_final'] = $hora_final[0] . ":" . $hora_final[1];
                    $dados['horarios'][$key]['url'] = base_url();
                }
                $dados['conteudo_expediente'] = $this->parser->parse('adm/gerenciar_horarios/tabela_horarios', $dados, TRUE);
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_horarios/visualizar_horario', $dados, TRUE);
            } else {
                $dados['conteudo_expediente'] = "<div class='col-sm-12'><h5> Não há nenhum horário cadastrado. </h5></div>";
                $dados['conteudo'] = $this->parser->parse('adm/gerenciar_horarios/visualizar_horario', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['conteudo'] = $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function excluir_horario($id_horario = null) {
        if (!$id_horario) {
            redirect('gerenciar/horario');
        }
        try {
            $url = base_url() . 'api/Professor_api/horario_expediente';
            $id['id_horario_expediente'] = $id_horario;

            $res = $this->client->request('DELETE', $url, ["query" => $id, "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                redirect('gerenciar/horario');
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

    public function adicionar_horario() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de horários';
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
        if (count($form) == 2) {
            $this->form_validation->set_rules('hora_inicio', "Hora inicial", "required");
            $this->form_validation->set_rules('hora_final', "Hora final", "required");
            if ($this->form_validation->run()) {
                try {
                    $url = base_url() . 'api/Professor_api/horario_expediente';
                    $res = $this->client->request('POST', $url, ["form_params" => $form, "auth" => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        $id_horario_inserido = $result->message;
                        try {
                            $url = base_url() . 'api/Professor_api/Select_dia_semana';
                            $res = $this->client->request('GET', $url, ["auth" => $this->auth]);
                            $result = json_decode($res->getBody());
                            if ($result->status) {
                                foreach ($result->message as $key => $value) {
                                    $horarios[$key]['id_horario_expediente_fk'] = $id_horario_inserido;
                                    $horarios[$key]['dia_semana_fk'] = $value->id_dia_semana;
                                }
                            }
                            try {
                                $url = base_url() . 'api/Professor_api/horario_after_expediente';
                                $res = $this->client->request('POST', $url, ["form_params" => $horarios, "auth" => $this->auth]);
                                $result = json_decode($res->getBody());
                                $ids_horarios_inseridos = $result->message->id_horario;
                                try {
                                    $url = base_url() . 'api/Professor_api/professor';
                                    $res = $this->client->request('GET', $url, ["auth" => $this->auth]);
                                    $result = json_decode($res->getBody());
                                    if ($result->message) {
                                        foreach ($result->message as $key => $value) {
                                            $professor_ids[$key]['id_professor'] = $value->id_professor;
                                        }
                                        $contador = 0;
                                        foreach ($professor_ids as $kp => $vp) {
                                            foreach ($ids_horarios_inseridos as $kids => $vids) {
                                                $horario_professor[$contador]['id_professor_fk'] = $vp['id_professor'];
                                                $horario_professor[$contador]['id_horario_fk'] = $vids;
                                                ++$contador;
                                            }
                                        }
                                        try {
                                            $url = base_url() . 'api/Professor_api/professor_horario';
                                            $res = $this->client->request('POST', $url, ["form_params" => $horario_professor, "auth" => $this->auth]);
                                            $result = json_decode($res->getBody());
                                            if ($result->status) {
                                                $dados['display'] = 'block';
                                                $dados['cor_alert'] = 'success';
                                                $dados['msg_erro'] = 'Sucesso ao inserir os dados.';
                                            } else {
                                                $dados['display'] = 'block';
                                                $dados['cor_alert'] = 'danger';
                                                $dados['msg_erro'] = 'Erro ao inserir no banco de dados.';
                                            }
                                        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                            $response = $ex->getResponse();
                                            $responseBodyAsString = $response->getBody()->getContents();
                                            $dados = $responseBodyAsString;
                                            echo $dados;
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
                            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                                $response = $ex->getResponse();
                                $responseBodyAsString = $response->getBody()->getContents();
                                $dados = $responseBodyAsString;
                                echo $dados;
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
                        $dados['msg_erro'] = 'Erro ao inserir no banco de dados.';
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
                $dados['msg_erro'] = 'Preencha corretamente o formulário.';
            }
        }

        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_horarios/adicionar_horario', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

}
