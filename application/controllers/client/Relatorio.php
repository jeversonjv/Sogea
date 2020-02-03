<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Relatorio extends CI_Controller {

    private $nav = array();
    private $client;
    private $auth = array();
    private $pdf;

    public function __construct() {
        parent::__construct();
        $this->client = new GuzzleHttp\Client();
        $this->pdf = new Mpdf\Mpdf();
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

    public function gerenciar_relatorio() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de relatórios';
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
            $res = $this->client->request('GET', $url, ["auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento encerrado ou registrado. </h5></div>";
            } else {
                $dados['eventos'] = array();
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1) {
                        if ($this->session->userdata('tipo_usuario') == 2) {
                            $dados['eventos'][$key]['nome'] = $value->nome;
                            $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                            $dados['eventos'][$key]['url'] = base_url();
                        } else {
                            if ($this->session->userdata('tipo_usuario') == 1 && $this->session->userdata('id_usuario') == $value->id_usuario_fk) {
                                $dados['eventos'][$key]['nome'] = $value->nome;
                                $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                                $dados['eventos'][$key]['url'] = base_url();
                            }
                        }
                    }
                }
                if (count($dados['eventos']) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento encerrado ou registrado. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_relatorios/tabela_relatorio_geral', $dados, TRUE);
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerar_relatorio($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/relatorio/geral');
        }
        $dados['participantes'] = $this->busca_participantes_evento($id_evento) != false ? $this->busca_participantes_evento($id_evento) : exit("Não há participantes cadastrados");
        $dados['ocorrencias'] = $this->busca_ocorrencias_evento($id_evento) != false ? $this->busca_ocorrencias_evento($id_evento) : exit("Não há ocorrencias neste evento");
        $dados['presencas'] = $this->busca_presenca_evento($dados['ocorrencias']) != false ? $this->busca_presenca_evento($dados['ocorrencias']) : array();
        foreach ($dados['participantes'] as $key => $value) {
            $id_participante = $value['id_participante'];
            $presenca_r['presenca_r'] = array();
            $presenca_nao_r['presenca_nao_r'] = array();
            $presencas = array();
            $participante_ocorrencias = 0;
            foreach ($dados['presencas'] as $kp => $vp) {
                if ($vp['id_participante_fk'] == $id_participante) {
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
                if (!$find) {
                    array_push($presenca_nao_r['presenca_nao_r'], $dados['ocorrencias'][$ko]);
                } else {
                    array_push($presenca_r['presenca_r'], $dados['ocorrencias'][$ko]);
                }
            }

            if (count($presenca_r['presenca_r']) == 0) {
                $dados['presenca_registrada'] = "Não possui nenhuma presença registrada.";
            } else {
                $dados['presenca_registrada'] = $this->parser->parse('adm/gerenciar_relatorios/relatorio_presenca_registrada', $presenca_r, TRUE);
            }

            if (count($presenca_nao_r['presenca_nao_r']) == 0) {
                $dados['presenca_nao_registrada'] = "Se registrou em todas as ocorrências.";
            } else {
                $dados['presenca_nao_registrada'] = $this->parser->parse('adm/gerenciar_relatorios/presenca_nao_registrada', $presenca_nao_r, TRUE);
            }
            $dados['nome'] = $value['nome_participante'];
            $dados['cpf_matricula'] = $value['cpf_matricula'];
            $dados['qtd_presenca'] = $participante_ocorrencias;
            $dados['qtd_total'] = count($dados['ocorrencias']);
            $dados['porcentagem'] = number_format(($participante_ocorrencias / count($dados['ocorrencias'])) * 100, 1);
            $html = $this->parser->parse('adm/gerenciar_relatorios/relatorio', $dados, TRUE);
            $this->pdf->WriteHTML($html);
            if ($key + 1 < count($dados['participantes'])) {
                $this->pdf->AddPage();
            }
        }
        $this->pdf->SetTitle("Relatório de presença");
        $this->pdf->Output();
    }

    private function busca_participantes_evento($id_evento) {
        try {
            $participantes = array();
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento_fk' => $id_evento], "auth" => $this->auth]);
            $result = json_decode($res->getBody());

            if (count($result->message) == 0) {
                return false;
            } else {
                foreach ($result->message as $key => $value) {
                    $participantes[$key]['nome_participante'] = $value->nome;
                    $participantes[$key]['cpf_matricula'] = $value->cpf_matricula;
                    $participantes[$key]['id_participante'] = $value->id_participante;
                }
                return $participantes;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
    }

    private function busca_ocorrencias_evento($id_evento) {
        try {
            $ocorrencias = array();
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento_fk' => $id_evento], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                return false;
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

}
