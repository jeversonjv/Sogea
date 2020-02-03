<?php

defined("BASEPATH") or exit('Sem acesso por script direto');

class Certificado extends CI_Controller {

    private $nav = array();
    private $client;
    private $auth = array();
    private $pdf;

    public function __construct() {
        parent::__construct();
        $this->client = new GuzzleHttp\Client();
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 0,
            'default_font' => '',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'L',
        ];
        $this->pdf = new Mpdf\Mpdf($config);
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

    public function gerenciar_certificado() {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de certificados';
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
            if ($result->status) {
                if (count($result->message) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum evento encerrado ou registrado. </h5></div>";
                } else {
                    $dados['eventos'] = [];
                    foreach ($result->message as $key => $value) {
                        if ($value->ativo == 1 && $value->estado == 1 && $value->certificado == 1) {
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
                        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_certificado/tabela_gerenciar_certificado', $dados, TRUE);
                    }
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function configurar_certificado($id_evento = null) {
        if (!$id_evento) {
            redirect('gerenciar/certificado');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de certificados';
        $dados['display'] = 'none';
        $dados['id_evento'] = $id_evento;
        $erros = false;
        $allowed_types = array("jpg", "png", "jpeg", "JPG", "PNG", "JPEG");
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
        $dados['certificado_info'] = $this->return_data_certificado($id_evento);
        if (count($form) >= 2) {
            $data['conteudo'] = $form['conteudo'];
            $data['data'] = $form['data'];
            if ($data['data'] == "" || $data['conteudo'] == "") {
                $erros = true;
            }
            $data['assinatura1'] = $form['assinatura1'];
            $data['assinatura2'] = $form['assinatura2'];
            $data['id_evento_fk'] = $id_evento;
            $data = $this->security->xss_clean($data);
            try {
                if (($_FILES['fundo_certificado']['name']) != "") {
                    $ext_img = explode('.', $_FILES['fundo_certificado']['name'])[1];
                    if (in_array($ext_img, $allowed_types)) {
                        $data['ext_imagem'] = $ext_img;
                    } else {
                        $erros = true;
                    }
                }
                if ($dados['certificado_info'][0]['conteudo'] == "" && $dados['certificado_info'][0]['data'] == "") {
                    $request = 'POST';
                } else {
                    $request = 'PUT';
                }
                $url = base_url() . 'api/Certificado_api/certificado';
                $res = $this->client->request($request, $url, ['form_params' => $data, 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    if (!$erros) {
                        if (($_FILES['fundo_certificado']['name']) != "") {
                            $tmp_nome = explode('.', $_FILES['fundo_certificado']['name']);
                            $nome_arquivo = $id_evento . '.' . $tmp_nome[1];
                            $caminho = 'assets/imagens/certificado';
                            if (!in_array($tmp_nome[1], $allowed_types)) {
                                $erros = true;
                            } else {
                                foreach ($dados['certificado_info'] as $key => $value) {
                                    $ext_arquivo = $value['ext_imagem'];
                                }
                                if (is_file($caminho . '/' . $id_evento . '.' . $ext_arquivo)) {
                                    unlink($caminho . '/' . $id_evento . '.' . $ext_arquivo);
                                }
                            }
                            $config = [
                                'file_name' => $nome_arquivo,
                                'allowed_types' => "jpg|png|jpeg",
                                'upload_path' => $caminho
                            ];
                            $this->upload->initialize($config);
                            if (!$this->upload->do_upload('fundo_certificado')) {
                                $erros = true;
                            }
                        }
                    }
                    if (!$erros) {
                        $dados['display'] = 'block';
                        $dados['msg_erro'] = 'Salvo com sucesso!';
                        $dados['cor_alert'] = 'success';
                        $dados['certificado_info'] = $this->return_data_certificado($id_evento);
                    } else {
                        $dados['display'] = 'block';
                        $dados['msg_erro'] = 'Erro ao salvar, verifique se as imagens estão no formato correto: "jpg|jpeg|png". Ou preencha o formulário corretamente.';
                        $dados['cor_alert'] = 'danger';
                    }
                } else {
                    $dados['display'] = 'block';
                    $dados['msg_erro'] = 'Erro ao salvar!';
                    $dados['cor_alert'] = 'danger';
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
                echo $dados;
            }
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_certificado/configurar_certificado', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    private function return_data_certificado($id_evento) {
        try {
            $data = array();
            $url = base_url() . 'api/Certificado_api/certificado';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status && count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $data[$key]['conteudo'] = $value->conteudo;
                    $data[$key]['assinatura1'] = $value->assinatura1;
                    $data[$key]['assinatura2'] = $value->assinatura2;
                    $data[$key]['ext_imagem'] = $value->ext_imagem;
                    $data[$key]['data'] = $value->data;
                    $data[$key]['url'] = base_url();
                    $data[$key]['id_evento'] = $id_evento;
                }
                return $data;
            } else {
                return array(
                    0 => [
                        'conteudo' => "",
                        'assinatura1' => "",
                        'assinatura2' => "",
                        'ext_imagem' => "",
                        'data' => "",
                        'url' => base_url(),
                        'id_evento' => $id_evento
                    ]
                );
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    private function delete_certificado_evento($id_evento) {
        try {
            $data = array();
            $url = base_url() . 'api/Certificado_api/certificado';
            $res = $this->client->request('DELETE', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if (!$result->status) {
                echo $result->message;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function gerenciar_participantes_certificado($id_evento = null, $id_msgs = null) {
        if (!$id_evento) {
            redirect('gerenciar/certificado');
        }
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de certificados';
        $dados['display'] = 'none';
        $dados['id_evento'] = $id_evento;
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
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                if (count($result->message) == 0) {
                    $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum participante cadastrado nesse evento. </h5></div>";
                } else {
                    $dados['participantes'] = array();
                    foreach ($result->message as $key => $value) {
                        $dados['participantes'][$key]['nome'] = $value->nome;
                        $dados['participantes'][$key]['id_participante'] = $value->id_participante;
                        $dados['participantes'][$key]['cpf_matricula'] = $value->cpf_matricula;
                        $dados['participantes'][$key]['url'] = base_url();
                    }
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }

        try {
            $url = base_url() . 'api/Evento_api/evento';
            $res = $this->client->request('GET', $url, ['query' => ['sub_id_evento' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            $ids_evento = array();
            $participantes_sub_evento = array();
            if (count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $ids_evento[$key] = $value->id_evento;
                }
                foreach ($ids_evento as $value) {
                    $participantes_sub_evento = $this->busca_participantes_evento($value);
                    foreach ($participantes_sub_evento as $kpse => $vpse) {
                        $find = false;
                        foreach ($dados['participantes'] as $kdp => $vdp) {
                            if ($vpse['cpf_matricula'] == $vdp['cpf_matricula']) {
                                $find = true;
                            }
                        }
                        if (!$find) {
                            array_push($dados['participantes'], $participantes_sub_evento[$kpse]);
                        }
                    }
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        if (count($dados['participantes']) == 0) {
            $dados['conteudo'] = "<div class='col-sm-12'><h5> Não há nenhum participante cadastrado nesse evento. </h5></div>";
        } else {
            //pega id -> certificado_info
            $id_certificado_info = -1;
            try {
                $url = base_url() . 'api/Certificado_api/certificado';
                $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
                $result = json_decode($res->getBody());
                if ($result->status) {
                    foreach ($result->message as $key => $value) {
                        $id_certificado_info = $value->id_certificado_info;
                    }
                }
            } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                $response = $ex->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $dados = $responseBodyAsString;
            }
            //pega participantes -> certificado
            if ($id_certificado_info == -1) {
                $dados['display'] = 'block';
                $dados['msg_erro'] = 'Antes de configurar os participantes, é necesário, primeiramente, configurar o certificado.';
                $dados['cor_alert'] = 'danger';
            } else {
                $participantes_have_certificado = array();
                try {
                    $url = base_url() . 'api/Certificado_api/certificado_real';
                    $res = $this->client->request('GET', $url, ['query' => ['id_certificado_info_fk' => $id_certificado_info], 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        foreach ($result->message as $key => $value) {
                            $participantes_have_certificado[$key]['id_participante_fk'] = $value->id_participante_fk;
                        }
                    }
                } catch (GuzzleHttp\Exception\BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    $dados = $responseBodyAsString;
                }
                //verifica quem já possui certificado e checked o input
                foreach ($dados['participantes'] as $kdp => $vdp) {
                    $have = false;
                    foreach ($participantes_have_certificado as $kphc => $vphc) {
                        if ($vdp['id_participante'] == $vphc['id_participante_fk']) {
                            $have = true;
                        }
                    }
                    if ($have) {
                        $dados['participantes'][$kdp]['checked'] = 'checked';
                    } else {
                        $dados['participantes'][$kdp]['checked'] = '';
                    }
                }
            }

            if (!empty($id_msgs) && $id_msgs !== 0) {
                $dados['display'] = 'block';
                $msgs = $this->return_msgs($id_msgs);
                $dados['cor_alert'] = $msgs['cor_alert'];
                $dados['msg_erro'] = $msgs['msg_erro'];
            }

            $dados['conteudo'] = $this->parser->parse('adm/gerenciar_certificado/tabela_participantes', $dados, TRUE);
        }
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    private function busca_participantes_evento($id_evento) {
        try {
            $participantes = array();
            $url = base_url() . 'api/Participante_api/participante_as_evento';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento_fk' => $id_evento], "auth" => $this->auth]);
            $result = json_decode($res->getBody());

            if (count($result->message) == 0) {
                return array();
            } else {
                foreach ($result->message as $key => $value) {
                    $participantes[$key]['nome'] = $value->nome;
                    $participantes[$key]['id_participante'] = $value->id_participante;
                    $participantes[$key]['cpf_matricula'] = $value->cpf_matricula;
                    $participantes[$key]['url'] = base_url();
                }
                return $participantes;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
    }

    public function gerenciar_participantes_salvar($id_evento) {
        $dados = $this->nav;
        $dados['url'] = base_url();
        $dados['gerenciamento_area'] = 'Gerenciamento de certificados';
        $dados['display'] = 'none';
        $dados['id_evento'] = $id_evento;
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
        //pega o id_certificado_info
        $id_certificado_info = -1;
        try {
            $url = base_url() . 'api/Certificado_api/certificado';
            $res = $this->client->request('GET', $url, ['query' => ['id_evento_fk' => $id_evento], 'auth' => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    $id_certificado_info = $value->id_certificado_info;
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
        if ($id_certificado_info == -1) {
            redirect('gerenciar/certificado/participantes/' . $id_evento . '/1');
        } else {
            if (!isset($form['id_participante'])) {
                redirect('gerenciar/certificado/participantes/' . $id_evento . '/2');
            } else {
                $participantes_win_certificado = array();
                foreach ($form['id_participante'] as $key => $value) {
                    $participantes_win_certificado[$key]['id_participante_fk'] = $value;
                    $participantes_win_certificado[$key]['id_certificado_info_fk'] = $id_certificado_info;
                }
                try {
                    $url = base_url() . 'api/Certificado_api/certificado_real';
                    $res = $this->client->request('DELETE', $url, ['query' => ['id_certificado_info_fk' => $id_certificado_info], 'auth' => $this->auth]);
                    $result = json_decode($res->getBody());
                    if ($result->status) {
                        $url = base_url() . 'api/Certificado_api/certificado_real';
                        $res = $this->client->request('POST', $url, ['form_params' => $participantes_win_certificado, 'auth' => $this->auth]);
                        $result = json_decode($res->getBody());
                        if ($result->status) {
                            redirect('gerenciar/certificado/participantes/' . $id_evento . '/3');
                        } else {
                            redirect('gerenciar/certificado/participantes/' . $id_evento . '/4');
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
    }

    public function pre_visualiazr($id_evento) {
        $dados['url'] = base_url();
        $dados['id_evento'] = $id_evento;
        try {
            $url = base_url() . 'api/Certificado_api/certificado';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento_fk' => $id_evento], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status && count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $dados['certificado'][$key]['conteudo'] = $value->conteudo;
                    $dados['certificado'][$key]['data'] = $value->data;
                    $dados['certificado'][$key]['assinatura1'] = $value->assinatura1;
                    $dados['certificado'][$key]['assinatura2'] = $value->assinatura2;
                    $dados['certificado'][$key]['ext_imagem'] = $value->ext_imagem;
                    $dados['certificado'][$key]['nome_participante'] = "Nome do participante aqui.";
                    $dados['certificado'][$key]['url'] = base_url();
                    $dados['certificado'][$key]['id_evento'] = $id_evento;
                }
            } else {
                $dados['certificado'][0]['conteudo'] = "conteúdo aqui.";
                $dados['certificado'][0]['data'] = "data aqui.";
                $dados['certificado'][0]['assinatura1'] = "assinatura aqui.";
                $dados['certificado'][0]['assinatura2'] = "assinatura aqui.";
                $dados['certificado'][0]['nome_participante'] = "Nome do participante aqui.";
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }

        $html = $this->parser->parse('adm/gerenciar_certificado/certificado', $dados, TRUE);
        $this->pdf->WriteHTML($html);
        $this->pdf->SetTitle("Pré-visualizar");
        $this->pdf->Output();
    }

    public function gerar_certificado($id_evento) {

        try {
            $url = base_url() . 'api/Certificado_api/Select_certificado_gerar';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento_fk' => $id_evento], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->status && count($result->message) > 0) {
                foreach ($result->message as $key => $value) {
                    $data['certificado'][$key]['nome_participante'] = $value->nome;
                    $data['certificado'][$key]['id_evento'] = $id_evento;
                    $data['certificado'][$key]['ext_imagem'] = $value->ext_imagem;
                    $data['certificado'][$key]['conteudo'] = $value->conteudo;
                    $data['certificado'][$key]['data'] = $value->data;
                    $data['certificado'][$key]['assinatura1'] = $value->assinatura1;
                    $data['certificado'][$key]['assinatura2'] = $value->assinatura2;
                    $data['certificado'][$key]['url'] = base_url();
                }
            } else {
                exit('É necessário configurar os participantes antes de gerar o certificado.');
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $html = $this->parser->parse('adm/gerenciar_certificado/certificado', $data, TRUE);
        $this->pdf->WriteHTML($html);
        $this->pdf->SetTitle("Certificados");
        $this->pdf->Output();
    }

    public function return_msgs($id) {
        switch ($id) {
            case 1:
                $dados['msg_erro'] = 'Antes de configurar os participantes, é necesário, primeiramente, configurar o certificado.';
                $dados['cor_alert'] = 'danger';
                break;
            case 2:
                $dados['msg_erro'] = 'É necessário escolher um participante para salvar.';
                $dados['cor_alert'] = 'danger';
                break;
            case 3:
                $dados['msg_erro'] = 'Sucesso ao configurar os participantes.';
                $dados['cor_alert'] = 'success';
                break;
            case 4:
                $dados['msg_erro'] = 'Ocorreu um erro, tente novamente!';
                $dados['cor_alert'] = 'danger';
                break;
        }
        return $dados;
    }

}
