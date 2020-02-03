<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {

    private $nav = array();
    private $client;
    private $auth = array();
    private $hash_id;
    private $pdf;

    public function __construct() {
        parent::__construct();
        $this->client = new GuzzleHttp\Client();
        $this->auth = array('admin', '1234', 'digest');
        $this->hash_id = new Hashids\Hashids("kVh=b2W<u%X_^r5U7LA:", 10);
        $this->pdf = new Mpdf\Mpdf();
        if (!$this->session->userdata('login_usuario')) {
            $this->nav = $this->menu->get_menu_inicio(true);
        } else {
            $this->nav = $this->menu->get_menu_inicio(false);
        }
    }

    public function index($id = null, $msg = null) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['display'] = 'none';
        $dados['linha_central'] = '<hr/>';
        $oid = array();
        if ($id) {
            $oid['id_tipo'] = $id;
        }
        try {
            $url = base_url() . "api/Evento_api/tipo";
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['tipo_evento'][$key]['id_tipo'] = $this->hash_id->encode($value->id_tipo);
                    $dados['tipo_evento'][$key]['tipo'] = $value->tipo;
                    $dados['tipo_evento'][$key]['url'] = base_url();
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['ultimo_evento'] = array();
        try {
            $url = base_url() . "api/Evento_api/evento";
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1 && $value->estado == 0 && $value->sub_id_evento == null) {
                        $dados['ultimo_evento'][$key]['id_evento'] = $this->hash_id->encode($value->id_evento);
                        $dados['ultimo_evento'][$key]['nome'] = $value->nome;
                        $dados['ultimo_evento'][$key]['data'] = date('d/m/Y', strtotime($value->data));
                        $horaInicial = explode(":", $value->hora_inicial);
                        $dados['ultimo_evento'][$key]['hora_inicial'] = $horaInicial[0] . ":" . $horaInicial[1];
                        $dados['ultimo_evento'][$key]['url'] = base_url();
                        $dados['ultimo_evento'][$key]['descricao'] = substr($value->descricao, 0, 120) . ' [...]';
                        if (count($dados['ultimo_evento']) == 6) {
                            break;
                        }
                    }
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }

        if ($msg) {

            $msg_decrypt = $this->hash_id->decode($msg);
            switch ($msg_decrypt[0]) {
                case 1:
                    $dados['msg'] = 'Usúario ou senha incorretos!';
                    $dados['color'] = 'danger';
                    break;
                case 2:
                    $dados['msg'] = 'Logado com sucesso, agora você pode entrar no menu de gerenciamento!';
                    $dados['color'] = 'success';
                    break;
                case 3:
                    $dados['color'] = 'danger';
                    $dados['msg'] = 'Digite corretamente o campo.';
                    break;
                case 4:
                    $dados['color'] = 'danger';
                    $dados['msg'] = 'Participante não encontrado.';
                    break;
            }
            $dados['display'] = 'block';
        }

        if (count($dados['ultimo_evento']) == 0) {
            $dados['conteudo2'] = '<div class="col-sm-12"> <h5> Não há nenhum evento cadastrado! </h5> </div>';
        } else {
            $dados['conteudo2'] = $this->parser->parse('layout_principal/ultimos_eventos', $dados, TRUE);
        }

        $dados['conteudo1'] = $this->parser->parse('layout_principal/carrossel', $dados, TRUE);
        $dados['msg_layout_central'] = "<i class='fa fa-calendar-alt'></i> Próximos eventos";

        $this->parser->parse('layout', $dados);
    }

    public function logar() {
        $this->form_validation->set_rules('login_usuario', 'Login Usuario', 'required');
        $this->form_validation->set_rules('senha_usuario', 'Senha Usuario', 'required');
        if ($this->form_validation->run()) {
            $form = $this->input->post();
            $form['senha_usuario'] = sha1($form['senha_usuario']);
            $url = base_url() . 'api/Usuario_api/usuario';
            try {
                $res = $this->client->request('GET', $url, ['query' => $form, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    if (count($result->message) == 1) {
                        foreach ($result->message as $key => $value) {
                            $user['login_usuario'] = $value->login_usuario;
                            $user['id_evento_fk'] = $value->id_evento_fk;
                            $user['tipo_usuario'] = $value->tipo_usuario;
                            $user['id_usuario'] = $value->id_usuario;
                        }
                        $this->session->set_userdata($user);
                        $msg = $this->hash_id->encode(2);
                        redirect('Inicio/index/null/' . $msg);
                    } else {
                        $msg = $this->hash_id->encode(1);
                        redirect('Inicio/index/null/' . $msg);
                    }
                } else {
                    echo 'erro';
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
            }
        } else {
            redirect('Inicio/index/null/1');
        }
    }

    public function mostrar_evento($id = null) {
        if (!$id) {
            redirect('Inicio');
        }
        $id = $this->hash_id->decode($id)[0];
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['msg_layout_central'] = "";
        $dados['conteudo2'] = "";
        $dados['display'] = 'none';
        $dados['linha_central'] = '';
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                $eventos_relacionados = [];
                foreach ($result->message as $key => $value) {
                    $dados['mostrar_evento'][$key]['id_evento'] = $this->hash_id->encode($value->id_evento);
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

                    try {
                        $url = base_url() . 'api/Evento_api/evento';
                        $res = $this->client->request('GET', $url, ['query' => ['sub_id_evento' => $id], 'auth' => $this->auth]);
                        $resultx = json_decode($res->getBody());
                        if (count($resultx->message) == 0) {
                            $eventos_relacionados[0]['id_evento'] = $this->hash_id->encode($id);
                            $eventos_relacionados[0]['nome_relacionado'] = 'Não há nenhum evento secundário.';
                            $eventos_relacionados[0]['url'] = base_url();
                        } else {
                            foreach ($resultx->message as $kx => $vx) {
                                $eventos_relacionados[$kx]['id_evento'] = $this->hash_id->encode($vx->id_evento);
                                $eventos_relacionados[$kx]['nome_relacionado'] = $vx->nome;
                                $eventos_relacionados[$kx]['url'] = base_url();
                            }
                        }
                    } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                        $response = $ex->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        $dados = $responseBodyAsString;
                    }
                    $dados['mostrar_evento'][$key]['eventos_secundarios'] = $eventos_relacionados;
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }

        try {
            $url = base_url() . "api/Evento_api/tipo";
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['tipo_evento'][$key]['id_tipo'] = $value->id_tipo;
                    $dados['tipo_evento'][$key]['tipo'] = $value->tipo;
                    $dados['tipo_evento'][$key]['url'] = base_url();
                }
            } else {
                echo "Erro";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['conteudo1'] = $this->parser->parse('layout_principal/mostrar_evento', $dados, TRUE);
        $this->parser->parse('layout', $dados);
    }

    public function eventos() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['msg_layout_central'] = "";
        $dados['conteudo2'] = "";
        $dados['display'] = 'none';
        $dados['linha_central'] = '';
        try {
            $url = base_url() . "api/Evento_api/tipo";
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['tipo_evento'][$key]['id_tipo'] = $this->hash_id->encode($value->id_tipo);
                    $dados['tipo_evento'][$key]['tipo'] = $value->tipo;
                    $dados['tipo_evento'][$key]['url'] = base_url();
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

        try {
            $inicio = ($this->uri->segment('3')) ? $this->uri->segment('3') : 0;
            $maximo = 7;
            $url = base_url() . "api/Evento_api/evento_pagination";
            $res = $this->client->request('GET', $url, ['query' => ['maximo' => $maximo, 'inicio' => $inicio], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1) {
                        $dados['eventos'][$key]['nome'] = $value->nome;
                        $dados['eventos'][$key]['id_evento'] = $this->hash_id->encode($value->id_evento);
                        $horario = explode(" ", $value->data);
                        $dados['eventos'][$key]['data'] = date('d/m/Y', strtotime($horario[0]));
                        $dados['eventos'][$key]['descricao'] = substr($value->descricao, 0, 150) . ' [...]';
                        $dados['eventos'][$key]['url'] = base_url();
                    }
                }
            } else {
                echo "Erro";
            }
            $config['base_url'] = base_url() . 'Inicio/eventos';
            $config['per_page'] = $maximo;
            $config['total_rows'] = $result->message2;
            $this->pagination->initialize($config);
            $dados['paginacao'] = $this->pagination->create_links();
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['evento_paginacao'] = $this->parser->parse('layout_principal/evento_paginacao', $dados, TRUE);
        $dados['conteudo1'] = $this->parser->parse('layout_principal/mais_eventos', $dados, TRUE);
        $this->parser->parse('layout', $dados);
    }

    public function mostrar_evento_categoria($id = null) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['display'] = 'none';
        $dados['linha_central'] = '<hr/>';
        $idx = $this->hash_id->decode($id);
        $idc['id_tipo_fk'] = $idx[0];
        try {
            $url = base_url() . "api/Evento_api/tipo";
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['tipo_evento'][$key]['id_tipo'] = $this->hash_id->encode($value->id_tipo);
                    $dados['tipo_evento'][$key]['tipo'] = $value->tipo;
                    $dados['tipo_evento'][$key]['url'] = base_url();
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

        try {
            $url = base_url() . "api/Evento_api/tipo_as_evento";
            $res = $this->client->request('GET', $url, ['query' => $idc, 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['ultimo_evento'] = array();
            $dados['msg_layout_central'] = "";
            foreach ($result->message as $key => $value) {
                if ($value->ativo == 1 && $value->estado == 0) {
                    $dados['msg_layout_central'] = $value->tipo;
                    $dados['ultimo_evento'][$key]['id_evento'] = $this->hash_id->encode($value->id_evento);
                    $dados['ultimo_evento'][$key]['nome'] = $value->nome;
                    $dados['ultimo_evento'][$key]['data'] = date('d/m/Y', strtotime($value->data));
                    $horaInicial = explode(":", $value->hora_inicial);
                    $dados['ultimo_evento'][$key]['hora_inicial'] = $horaInicial[0] . ":" . $horaInicial[1];
                    $dados['ultimo_evento'][$key]['url'] = base_url();
                    $dados['ultimo_evento'][$key]['descricao'] = substr($value->descricao, 0, 120) . ' [...]';
                }
            }

            if (count($dados['ultimo_evento']) == 0) {
                $dados['conteudo2'] = '<div class="col-sm-12"><h5>Não há nenhum evento nessa categoria.</h5></div>';
            } else {
                $dados['conteudo2'] = $this->parser->parse('layout_principal/ultimos_eventos', $dados, TRUE);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        $dados['conteudo1'] = $this->parser->parse('layout_principal/carrossel', $dados, TRUE);
        $this->parser->parse('layout', $dados);
    }

    /*     * ***Folha de presenças do participante***** */

    public function search_presenca_participante() {
        $dados = $this->nav;
        $form = $this->input->post();
        $participante = array();
        if (count($form) == 1) {
            if (strlen($form['flag_id']) < 11) {
                $msg = $this->hash_id->encode(3);
                redirect('Inicio/index/null/' . $msg);
            }
            $matricula['cpf_matricula'] = $this->security->xss_clean($form['flag_id']);
            try {
                $url = base_url() . "api/Participante_api/participante_matricula";
                $res = $this->client->request('GET', $url, ['query' => $matricula, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    if (count($result->message) == 0) {
                        $msg = $this->hash_id->encode(4);
                        redirect('Inicio/index/null/' . $msg);
                    } else {
                        foreach ($result->message as $key => $value) {
                            $participante = $value->id_participante;
                        }
                        $this->show_eventos_participante($participante);
                    }
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
                echo $dados;
            }
        }
    }

    public function show_eventos_participante($id) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        try {
            $url = base_url() . "api/Participante_api/evento_as_participante";
            $res = $this->client->request('GET', $url, ['query' => ['id_participante_fk' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['eventos'][$key]['nome'] = $value->nome;
                    $dados['eventos'][$key]['id_evento'] = $this->hash_id->encode($value->id_evento);
                    $dados['eventos'][$key]['id_participante'] = $this->hash_id->encode($id);
                    $dados['eventos'][$key]['url'] = base_url();
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('participante_busca_presenca', $dados);
    }

    public function gerar_folha_presenca($id_evento, $id_participante) {
        $id_evento = $this->hash_id->decode($id_evento);
        $id_participante = $this->hash_id->decode($id_participante);
        $ocorrencias = array();
        $eventos_filhos = array();
        $presencas = array();
        $participante = array();
        //ocorrências
        try {
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento[0]], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $ocorrencias[$key]['id_ocorrencia'] = $value->id_ocorrencia;
                    $ocorrencias[$key]['nome_ocorrencia'] = $value->nome;
                    $ohora = explode(' ', $value->horario);
                    $horas = explode(':', $ohora[1]);
                    $ocorrencias[$key]['horario'] = $horas[0] . ':' . $horas[1];
                    $ocorrencias[$key]['data'] = date('d/m/Y', strtotime($ohora[0]));
                    $ocorrencias[$key]['url'] = base_url();
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        //Eventos filhos.
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['sub_id_evento' => $id_evento[0]], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $eventos_filhos[$key]['nome_evento_filho'] = $value->nome;
                    $eventos_filhos[$key]['id_evento'] = $value->id_evento;
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        //Participante
        try {
            $url = base_url() . "api/Participante_api/participante";
            $res = $this->client->request('GET', $url, ['query' => ['id_participante' => $id_participante[0]], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $participante[$key]['nome'] = $value->nome;
                    $participante[$key]['cpf_matricula'] = $value->cpf_matricula;
                    $participante[$key]['id_participante'] = $id_participante[0];
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        foreach ($eventos_filhos as $value) {
            $ocorrencias = array_merge($this->busca_ocorrencias_evento($value['id_evento']), $ocorrencias);
        }
        $presencas = $this->busca_presenca_evento($ocorrencias) != false ? $this->busca_presenca_evento($ocorrencias) : array();

        $dados['presencas'] = $presencas;
        $dados['participante'] = $participante;
        $dados['ocorrencias'] = $ocorrencias;
        $this->gerar_presenca($dados);
    }

    private function gerar_presenca($dados) {
        $presenca_r['presenca_r'] = array();
        $presencas = array();
        $participante_ocorrencias = 0;
        foreach ($dados['presencas'] as $kp => $vp) {
            if ($vp['id_participante_fk'] == $dados['participante'][0]['id_participante']) {
                array_push($presencas, $vp['id_ocorrencia_fk']);
            }
        }
        foreach ($dados['ocorrencias'] as $ko => $vo) {
            $find = false;
            foreach ($presencas as $presenca) {
                if ($presenca == $vo['id_ocorrencia']) {
                    $find = true;
                    ++$participante_ocorrencias;
                }
            }
            if ($find) {
                array_push($presenca_r['presenca_r'], $dados['ocorrencias'][$ko]);
            }
        }
        if (count($presenca_r['presenca_r']) == 0) {
            $dados['presenca_registrada'] = "Não possui nenhuma presença registrada.";
        } else {
            $dados['presenca_registrada'] = $this->parser->parse('layout_principal/relatorio_presenca_registrada', $presenca_r, TRUE);
        }
        $dados['nome'] = $dados['participante'][0]['nome'];
        $dados['cpf_matricula'] = $dados['participante'][0]['cpf_matricula'];
        $dados['qtd_presenca'] = $participante_ocorrencias;
        $dados['qtd_total'] = count($dados['ocorrencias']);
        $dados['porcentagem'] = number_format(($participante_ocorrencias / count($dados['ocorrencias'])) * 100, 1);
        $html = $this->parser->parse('layout_principal/relatorio', $dados, TRUE);

        $this->pdf->WriteHTML($html);
        $this->pdf->SetTitle("Presenças");
        $this->pdf->Output();
    }

    private function busca_ocorrencias_evento($id_evento) {
        try {
            $ocorrencias = array();
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento_fk' => $id_evento], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                return array();
            } else {
                foreach ($result->message as $key => $value) {
                    $ocorrencias[$key]['id_ocorrencia'] = $value->id_ocorrencia;
                    $ocorrencias[$key]['nome_ocorrencia'] = $value->nome;
                    $horario = explode(" ", $value->horario);
                    $hora = explode(":", $horario[1]);
                    $ocorrencias[$key]['data'] = date("d/m/Y", strtotime($horario[0]));
                    $ocorrencias[$key]['horario'] = $hora[0] . ":" . $hora[1];
                    $ocorrencias[$key]['url'] = base_url();
                }
                return $ocorrencias;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
    }

    private function busca_presenca_evento($ocorrencias) {
        try {
            $ocorrencias = array();
            foreach ($ocorrencias as $key => $value) {
                $ocorrencias[$key]['id_ocorrencia_fk'] = $value['id_ocorrencia'];
            }
            $url = base_url() . 'api/Ocorrencia_api/presenca_ocorrencia';
            $res = $this->client->request('GET', $url, ["query" => $ocorrencias, "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                if (count($result->message) == 0) {
                    return false;
                } else {
                    foreach ($result->message as $key => $value) {
                        $presenca[$key]['id_participante_fk'] = $value->id_participante_fk;
                        $presenca[$key]['id_ocorrencia_fk'] = $value->id_ocorrencia_fk;
                    }
                    return $presenca;
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
    }

    /*     * ***Folha de presença pro bobão que perder***** */

    public function search_folha_participante() {
        $dados = $this->nav;
        $form = $this->input->post();
        $participante = array();
        if (count($form) == 1) {
            if (strlen($form['flag_id']) < 11) {
                $msg = $this->hash_id->encode(3);
                redirect('Inicio/index/null/' . $msg);
            }
            $matricula['cpf_matricula'] = $this->security->xss_clean($form['flag_id']);
            try {
                $url = base_url() . "api/Participante_api/participante_matricula";
                $res = $this->client->request('GET', $url, ['query' => $matricula, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    if (count($result->message) == 0) {
                        $msg = $this->hash_id->encode(4);
                        redirect('Inicio/index/null/' . $msg);
                    } else {
                        foreach ($result->message as $key => $value) {
                            $participante = $value->id_participante;
                        }
                        $this->show_eventos_folha_participante($participante);
                    }
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
                echo $dados;
            }
        }
    }

    public function show_eventos_folha_participante($id) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        try {
            $url = base_url() . "api/Participante_api/evento_as_participante";
            $res = $this->client->request('GET', $url, ['query' => ['id_participante_fk' => $id], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $dados['eventos'][$key]['nome'] = $value->nome;
                    $dados['eventos'][$key]['id_evento'] = $this->hash_id->encode($value->id_evento);
                    $dados['eventos'][$key]['id_participante'] = $this->hash_id->encode($id);
                    $dados['eventos'][$key]['url'] = base_url();
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $this->parser->parse('participante_gera_folha_presenca', $dados);
    }

    public function gerar_folha_qrcode($id_evento, $id_participante) {
        $id_evento = $this->hash_id->decode($id_evento);
        $id_participante = $this->hash_id->decode($id_participante);
        //Participantes.
        $participante = array();
        $ocorrencias = array();
        $eventos_filhos = array();
        try {
            $url = base_url() . "api/Participante_api/participante";
            $res = $this->client->request('GET', $url, ['query' => ['id_participante' => $id_participante[0]], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $participante[$key]['nome_participante'] = $value->nome;
                    $participante[$key]['cpf_matricula'] = $value->cpf_matricula;
                    $participante[$key]['id_participante'] = $id_participante[0];
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        //Ocorrencias.
        try {
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento[0]], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $ocorrencias[$key]['id_ocorrencia'] = $value->id_ocorrencia;
                    $ocorrencias[$key]['nome_ocorrencia'] = $value->nome;
                    $ohora = explode(' ', $value->horario);
                    $horas = explode(':', $ohora[1]);
                    $ocorrencias[$key]['hora'] = $horas[0] . ':' . $horas[1];
                    $ocorrencias[$key]['data'] = date('d/m/Y', strtotime($ohora[0]));
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        //Eventos filhos.
        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['sub_id_evento' => $id_evento[0]], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $eventos_filhos[$key]['nome_evento_filho'] = $value->nome;
                    $eventos_filhos[$key]['id_evento'] = $value->id_evento;
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        foreach ($eventos_filhos as $value) {
            $data_tmp = $this->return_dados_eventos_filho($value['id_evento']);
            $ocorrencias = array_merge($ocorrencias, $data_tmp['ocorrencias']);
            $data_tmp = array();
        }

        if (count($ocorrencias) == 0) {
            exit('Este evento não possui nenhuma ocorrência para gerar uma folha de presença');
        }

        $contador = 0;
        foreach ($participante as $p) {
            $dados['nome_participante'] = $p['nome_participante'];
            $dados['matricula_cpf'] = $p['cpf_matricula'];
            $dados['id_participante'] = $p['id_participante'];
            foreach ($ocorrencias as $key => $o) {
                if (!is_dir('assets/imagens/qrcode_tmp/' . $dados['id_participante'])) {
                    mkdir('assets/imagens/qrcode_tmp/' . $dados['id_participante']);
                }
                $caminho = 'assets/imagens/qrcode_tmp/' . $dados['id_participante'] . '/' . $key . '.png';
                $msg = $dados['id_participante'] . '.' . $o['id_ocorrencia'] . '.' . $o['hora'];
                $msg_encrypt = $this->encryption->encrypt($msg);
                QRcode::png($msg_encrypt, $caminho);
            }
            for ($y = 0; $y < count($ocorrencias); $y++) {
                $dados['qrcodes'][$y]['nome_ocorrencia'] = $ocorrencias[$y]['nome_ocorrencia'];
                $dados['qrcodes'][$y]['horario'] = $ocorrencias[$y]['hora'];
                $dados['qrcodes'][$y]['data'] = $ocorrencias[$y]['data'];
                $dados['qrcodes'][$y]['id_participante'] = $dados['id_participante'];
                $dados['qrcodes'][$y]['id_img'] = $y;
                $dados['qrcodes'][$y]['url'] = base_url();
            }
            $this->pdf->WriteHTML($this->parser->parse('adm/gerenciar_presencas/presenca', $dados, TRUE));
            ++$contador;
            if (($contador) < count($participante)) {
                $this->pdf->AddPage();
            }
        }

        foreach ($participante as $p) {
            for ($i = 0; $i < count($ocorrencias); $i++) {
                if (file_exists('assets/imagens/qrcode_tmp/' . $p['id_participante'] . '/' . $i . '.png')) {
                    unlink('assets/imagens/qrcode_tmp/' . $p['id_participante'] . '/' . $i . '.png');
                }
            }
            if (is_dir('assets/imagens/qrcode_tmp/' . $p['id_participante'])) {
                rmdir('assets/imagens/qrcode_tmp/' . $p['id_participante']);
            }
        }
        $this->pdf->SetTitle('Folha de presença');
        $this->pdf->Output();
    }

    public function return_dados_eventos_filho($id_evento = null) {
        $data['ocorrencias'] = array();
        //Ocorrencias
        try {
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $data['ocorrencias'][$key]['id_ocorrencia'] = $value->id_ocorrencia;
                    $data['ocorrencias'][$key]['nome_ocorrencia'] = $value->nome;
                    $ohora = explode(' ', $value->horario);
                    $horas = explode(':', $ohora[1]);
                    $data['ocorrencias'][$key]['hora'] = $horas[0] . ':' . $horas[1];
                    $data['ocorrencias'][$key]['data'] = date('d/m/Y', strtotime($ohora[0]));
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        return $data;
    }

}
