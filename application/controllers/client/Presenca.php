<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Presenca extends CI_Controller {

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

    public function gerenciar_presenca_evento() {
        if ($this->session->userdata('tipo_usuario') == 0) {
            redirect('Inicio');
        }
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
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if (count($result->message) > 0) {
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
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento validado ou registrado. </h5></div>";
                } else {
                    $dados['conteudo'] = $this->parser->parse('adm/gerenciar_presencas/tabela_presenca', $dados, TRUE);
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
        $dados['msg_erro'] = 'Gerar a folha de presença pode ser um processo um pouco demorado, basta clicar uma ÚNICA vez no botão e aguardar o resultado.';
        $dados['cor_alert'] = 'warning';
        $dados['display'] = 'block';

        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerar_folha($id_evento = null) {
        if ($this->session->userdata('tipo_usuario') == 0) {
            redirect('Inicio');
        }
        if (!$id_evento) {
            redirect('gerenciar/presencas');
        }
        $referenciaQrCodesImg = array();
        $referenciaParticipante = array();

        try {
            $url = base_url() . 'api/Ocorrencia_api/ocorrencia_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                exit('Este evento não possui nenhuma ocorrência para gerar uma folha de presença');
            }
            foreach ($result->message as $key => $value) {
                $referenciaQrCodesImg[$key]['url'] = base_url();
                $referenciaQrCodesImg[$key]['id_ocorrencia'] = $value->id_ocorrencia;
                $referenciaQrCodesImg[$key]['nome_ocorrencia'] = $value->nome_ocorrencia;
                $aHora = explode(' ', $value->horario);
                $oHora = explode(':', $aHora[1]);
                $referenciaQrCodesImg[$key]['data'] = date('d/m/Y', strtotime($aHora[0]));
                $referenciaQrCodesImg[$key]['horario'] = $oHora[0] . ':' . $oHora[1];
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }

        try {
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (count($result->message) == 0) {
                exit('Este evento não possui nenhum participante para gerar uma folha de presença');
            }
            foreach ($result->message as $k => $v) {
                $referenciaParticipante[$k]['nome_participante'] = $v->nome;
                $referenciaParticipante[$k]['matricula_cpf'] = $v->cpf_matricula;
                $referenciaParticipante[$k]['id_participante'] = $v->id_participante;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        $idImg = 0;
        $qtdParticipante = count($referenciaParticipante);
        $qtdQrCode = count($referenciaQrCodesImg);
        for ($i = 0; $i < $qtdParticipante; ++$i) {
            $idImg = 0;
            $dados['nome_participante'] = $referenciaParticipante[$i]['nome_participante'];
            $dados['matricula_cpf'] = $referenciaParticipante[$i]['matricula_cpf'];
            $dados['id_participante'] = $referenciaParticipante[$i]['id_participante'];
            for ($y = 0; $y < $qtdQrCode; ++$y) {
                if (!is_dir('assets/imagens/qrcode_tmp/' . $dados['id_participante'])) {
                    mkdir('assets/imagens/qrcode_tmp/' . $dados['id_participante']);
                }
                $caminho = 'assets/imagens/qrcode_tmp/' . $dados['id_participante'] . '/' . $idImg . '.png';
                $msg = $referenciaParticipante[$i]['id_participante'] . '.' . $referenciaQrCodesImg[$y]['id_ocorrencia'] . '.' . $referenciaQrCodesImg[$y]['horario'];
                ++$idImg;
                $msg_encrypt = $this->encryption->encrypt($msg);
                QRcode::png($msg_encrypt, $caminho);
            }
            for ($k = 0; $k < $idImg; ++$k) {
                $dados['qrcodes'][$k]['nome_ocorrencia'] = $referenciaQrCodesImg[$k]['nome_ocorrencia'];
                $dados['qrcodes'][$k]['id_img'] = $k;
                $dados['qrcodes'][$k]['data'] = $referenciaQrCodesImg[$k]['data'];
                $dados['qrcodes'][$k]['horario'] = $referenciaQrCodesImg[$k]['horario'];
                $dados['qrcodes'][$k]['id_participante'] = $dados['id_participante'];
                $dados['qrcodes'][$k]['url'] = base_url();
            }
            $this->pdf->WriteHTML($this->parser->parse('adm/gerenciar_presencas/presenca', $dados, TRUE));
            if ($i + 1 < $qtdParticipante) {
                $this->pdf->AddPage();
            }
        }
        foreach ($referenciaParticipante as $value) {
            for ($i = 0; $i < $qtdQrCode; $i++) {
                if (file_exists('assets/imagens/qrcode_tmp/' . $value['id_participante'] . '/' . $i . '.png')) {
                    unlink('assets/imagens/qrcode_tmp/' . $value['id_participante'] . '/' . $i . '.png');
                }
            }
            if (is_dir('assets/imagens/qrcode_tmp/' . $value['id_participante'])) {
                rmdir('assets/imagens/qrcode_tmp/' . $value['id_participante']);
            }
        }
        $this->pdf->Output();
    }

    public function abrir_camera() {
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
        $erros = false;
        $type_erro = -1;
        $form = $this->input->post();
        if (!empty($form['id_qrCode'])) {
            $type_erro = -2;
            date_default_timezone_set('America/Sao_Paulo');
            $hora_gravada = date('H:i');
            $oForm = explode('.', $this->encryption->decrypt($form['id_qrCode']));
            if (count($oForm) == 3) {
                $data['id_participante'] = $oForm[0];
                $data['id_ocorrencia'] = $oForm[1];
                $data['horario'] = $oForm[2];
            } else {
                $erros = true;
                $type_erro = 5;
            }

            if (!$erros) {
                try {
                    $url = base_url() . 'api/Participante_api/participante';
                    $res = $this->client->request('GET', $url, ['query' => ['id_participante' => $data['id_participante']], 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (count($result->message) == 0) {
                        $erros = true;
                        $type_erro = 0;
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
            if (!$erros) {
                try {
                    $url = base_url() . 'api/Ocorrencia_api/ocorrencia';
                    $res = $this->client->request('GET', $url, ['query' => ['id_ocorrencia' => $data['id_ocorrencia']], 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (count($result->message) == 0) {
                        $erros = true;
                        $type_erro = 1;
                    } else {
                        $hora_o = explode(':', $data['horario']);
                        $hora_ocorrencia = (int) $hora_o[0];
                        $minuto_ocorrencia = (int) $hora_o[1];

                        $minuto_ocorrencia_before = $minuto_ocorrencia - 20;
                        if ($minuto_ocorrencia_before < 0) {
                            $minuto_ocorrencia_before = 60 + $minuto_ocorrencia_before;
                            if ($hora_ocorrencia == 0) {
                                $hora_ocorrencia_before = 23;
                            } else {
                                $hora_ocorrencia_before = $hora_ocorrencia - 1;
                            }
                        } else if ($minuto_ocorrencia_before == 0) {
                            $minuto_ocorrencia_before = 0;
                        } else {
                            $hora_ocorrencia_before = $hora_ocorrencia;
                        }

                        $minuto_ocorrencia_after = $minuto_ocorrencia + 20;
                        if ($minuto_ocorrencia_after > 60) {
                            $minuto_ocorrencia_after = $minuto_ocorrencia_after - 60;
                            if ($hora_ocorrencia == 23) {
                                $hora_ocorrencia_after = 0;
                            } else {
                                $hora_ocorrencia_after = $hora_ocorrencia + 1;
                            }
                        } else if ($minuto_ocorrencia_after == 60) {
                            $minuto_ocorrencia_after = 0;
                            if ($hora_ocorrencia == 23) {
                                $hora_ocorrencia_after = 0;
                            } else {
                                $hora_ocorrencia_after = $hora_ocorrencia + 1;
                            }
                        } else {
                            $hora_ocorrencia_after = $hora_ocorrencia;
                        }

                        $hora_r = explode(':', $hora_gravada);
                        $hora_registro = (int) $hora_r[0];
                        $minuto_registro = (int) $hora_r[1];

                        if (!$erros) {
                            if ($hora_registro != $hora_ocorrencia_before && $hora_registro != $hora_ocorrencia_after) {
                                $erros = true;
                                $type_erro = 4;
                            }
                        }

                        if (!$erros) {
                            if ($hora_registro > $hora_ocorrencia_before && $hora_registro == $hora_ocorrencia_after) {
                                if ($minuto_registro < $minuto_ocorrencia_before && $minuto_registro <= $minuto_ocorrencia_after) {
                                    $erros = false;
                                } else {
                                    $erros = true;
                                    $type_erro = 4;
                                }
                            }
                        }

                        if (!$erros) {
                            if ($hora_registro < $hora_ocorrencia_after && $hora_registro == $hora_ocorrencia_before) {
                                if ($minuto_registro >= $minuto_ocorrencia_before && $minuto_registro > $minuto_ocorrencia_after) {
                                    $erros = false;
                                } else {
                                    $erros = true;
                                    $type_erro = 4;
                                }
                            }
                        }

                        if (!$erros) {
                            if ($hora_registro == $hora_ocorrencia_before && $hora_registro == $hora_ocorrencia_after) {
                                if ($minuto_registro >= $minuto_ocorrencia_before && $minuto_registro <= $minuto_ocorrencia_after) {
                                    $erros = false;
                                } else {
                                    $erros = true;
                                    $type_erro = 4;
                                }
                            }
                        }
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
            if (!$erros) {
                try {
                    $registros['id_participante_fk'] = $data['id_participante'];
                    $registros['id_ocorrencia_fk'] = $data['id_ocorrencia'];

                    $url = base_url() . 'api/Ocorrencia_api/presenca';
                    $res = $this->client->request('GET', $url, ['query' => $registros, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (count($result->message) > 0) {
                        $erros = true;
                        $type_erro = 2;
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }

            if (!$erros) {
                try {
                    $registros['id_participante_fk'] = $data['id_participante'];
                    $registros['id_ocorrencia_fk'] = $data['id_ocorrencia'];
                    $registros['horario'] = date('Y-m-d H:i:s');
                    $registros['id_usuario_fk'] = $this->session->userdata('id_usuario');

                    $url = base_url() . 'api/Ocorrencia_api/presenca';
                    $res = $this->client->request('POST', $url, ['form_params' => $registros, 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if (!$result->status) {
                        $erros = true;
                        $type_erro = 3;
                    } else {
                        $erros = false;
                        unset($form);
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                    echo $dados;
                }
            }
        }
        if (!$erros) {
            if ($type_erro == -1) {
                $dados['display'] = 'none';
            } else {
                $dados['display'] = 'block';
                $dados['cor_alert'] = 'success';
                $dados['msg_erro'] = 'Presença registrada com sucesso!';
            }
        } else {
            $dados['display'] = 'block';
            $dados['cor_alert'] = 'danger';
            switch ($type_erro) {
                case 0:
                    $dados['msg_erro'] = 'Participante não existente no sistema.';
                    break;
                case 1:
                    $dados['msg_erro'] = 'Ocorrência não existente no sistema.';
                    break;
                case 2:
                    $dados['msg_erro'] = 'Presença já registrada!';
                    break;
                case 3:
                    $dados['msg_erro'] = 'Erro ao registrar a presença, contate o administrador!';
                    break;
                case 4:
                    $dados['msg_erro'] = 'Horário fora do permitido para a leitura!';
                    break;
                case 5:
                    $dados['msg_erro'] = 'Formato do QRCode incorreto!';
                    break;
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/leitura_presenca/camera', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

}
