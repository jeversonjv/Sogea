<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Presenca_pai extends CI_Controller {

    private $pdf;
    private $nav = array();
    private $client;
    private $auth = array();

    public function __construct() {
        parent::__construct();
        $this->pdf = new Mpdf\Mpdf();
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

    public function gerenciar_presenca_pai() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de presença';
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
            $url = base_url() . 'api/Evento_api/evento_pai';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento pai validado ou registrado. </h5></div>";
            } else {
                $dados['eventos'] = array();
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1 && $value->estado == 0) {
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
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento pai validado ou registrado. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_presencas/tabela_presenca_pai', $dados, TRUE);
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $dados['msg_erro'] = 'Gerar a folha de presença pode ser um processo um pouco demorado, basta clicar uma ÚNICA vez no botão e aguardar o resultado.';
        $dados['cor_alert'] = 'warning';
        $dados['display'] = 'block';
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerar_folha($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/presencas/pai');
        }
        //Participantes.
        $participantes = array();
        $ocorrencias = array();
        $eventos_filhos = array();
        try {
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $participantes[$key]['nome_participante'] = $value->nome;
                    $participantes[$key]['cpf_matricula'] = $value->cpf_matricula;
                    $participantes[$key]['id_participante'] = $value->id_participante;
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
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
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
            $res = $this->client->request('GET', $url, ['query' => ['sub_id_evento' => $id_evento], 'auth' => $this->auth]);
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
            $data_tmp = $this->return_dados_eventos_filho($value['id_evento'], $participantes);
            $participantes = array_merge($participantes, $data_tmp['participantes']);
            $ocorrencias = array_merge($ocorrencias, $data_tmp['ocorrencias']);
            $data_tmp = array();
        }
        if (count($participantes) == 0) {
            exit('Este evento não possui nenhum participante para gerar uma folha de presença');
        }

        if (count($ocorrencias) == 0) {
            exit('Este evento não possui nenhuma ocorrência para gerar uma folha de presença');
        }

        $contador = 0;
        foreach ($participantes as $p) {
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
            if (($contador) < count($participantes)) {
                $this->pdf->AddPage();
            }
        }

        foreach ($participantes as $p) {
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

    public function return_dados_eventos_filho($id_evento = null, $participantes) {
        $data['participantes'] = array();
        $data['ocorrencias'] = array();
        //participantes
        try {
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $achou = false;
                    foreach ($participantes as $v) {
                        if ($value->cpf_matricula == $v['cpf_matricula']) {
                            $achou = true;
                        }
                    }
                    if (!$achou) {
                        $data['participantes'][$key]['nome_participante'] = $value->nome;
                        $data['participantes'][$key]['cpf_matricula'] = $value->cpf_matricula;
                        $data['participantes'][$key]['id_participante'] = $value->id_participante;
                    }
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
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
