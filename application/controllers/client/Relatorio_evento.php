<?php

class Relatorio_evento extends CI_Controller {

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
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
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

    public function gerar_relatorio_eventos() {
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
            $res = $this->client->request('GET', $url, ["auth" => $this->auth]);
            $result = json_decode($res->getBody());
            $dados['eventos'] = array();
            if ($result->status) {
                foreach ($result->message as $key => $value) {
                    if ($value->ativo == 1 && $value->sub_id_evento == null) {
                        if ($this->session->userdata('tipo_usuario') == 2) {
                            $dados['eventos'][$key]['nome'] = $value->nome;
                            $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                            $dados['eventos'][$key]['url'] = base_url();
                        } else {
                            if ($value->id_usuario_fk == $this->session->userdata('id_usuario') && $this->session->userdata('tipo_usuario') == 1) {
                                $dados['eventos'][$key]['nome'] = $value->nome;
                                $dados['eventos'][$key]['id_evento'] = $value->id_evento;
                                $dados['eventos'][$key]['url'] = base_url();
                            }
                        }
                    }
                }
            }
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
        }
        $dados['conteudo'] = $this->parser->parse('adm/gerenciar_eventos/visualizar_eventos_relatorio', $dados, TRUE);
        $this->parser->parse('restrita/layout_restrita', $dados);
    }

    public function gerar_eventos($id_evento) {
        try {
            $url = base_url() . 'api/Evento_api/evento_relatorio';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento' => $id_evento, 'order' => ['ee.nome', 'oe.horario']], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            $eventos_ocorrencias = [];
            $id_ev = -1;
            $ocorrencias = [];
            $ev = -1;
            $oc = 0;
            foreach ($result->message as $key => $value) {
                $evento = $value->nome;
                if ($id_ev != $value->id_evento_sec) {
                    ++$ev;
                    $eventos_ocorrencias['eventos'][$ev]['nome'] = $evento;
                    $oc = 0;
                }
                $eventos_ocorrencias['eventos'][$ev]['ocorrencias'][$oc]['nome_ocorrencia'] = $value->nome_ocorrencia;
                $horario = explode(' ', $value->horario);
                $data = date("d/m/Y", strtotime($horario[0]));
                $hora = explode(':', $horario[1]);
                $ohorario = $data . ' - ' . $hora[0] . ':' . $hora[1];
                $eventos_ocorrencias['eventos'][$ev]['ocorrencias'][$oc]['horario'] = $ohorario;
                $eventos_ocorrencias['eventos'][$ev]['qtd_rows'] = $oc + 1;
                $id_ev = $value->id_evento_sec;
                $oc++;
            }

            $html = $this->load->view('adm/gerenciar_eventos/relatorio_eventos', $eventos_ocorrencias, TRUE);
            $this->pdf->WriteHTML($html);
            $this->pdf->SetTitle("Horários");
            $this->pdf->Output();
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

    public function gerar_horario($id_evento) {
        try {
            $url = base_url() . 'api/Evento_api/evento_relatorio';
            $res = $this->client->request('GET', $url, ["query" => ['id_evento' => $id_evento, 'order' => ['oe.horario', 'oe.nome']], "auth" => $this->auth]);
            $result = json_decode($res->getBody());
            if ($result->message) {
                foreach ($result->message as $key => $value) {
                    $eventos_ocorrencias['eventos'][$key]['nome'] = $value->nome;
                    $horario = explode(' ', $value->horario);
                    $data = date("d/m/Y", strtotime($horario[0]));
                    $hora = explode(':', $horario[1]);
                    $ohorario = $data . ' - ' . $hora[0] . ':' . $hora[1];
                    $eventos_ocorrencias['eventos'][$key]['nome_ocorrencia'] = $value->nome_ocorrencia;
                    $eventos_ocorrencias['eventos'][$key]['horario'] = $ohorario;
                }
            }
            if (count($eventos_ocorrencias) == 0) {
                exit('Não há registros para gerar o relatório');
            }
            $this->pdf->WriteHTML($this->parser->parse('adm/gerenciar_eventos/relatorios_horarios', $eventos_ocorrencias, TRUE));
            $this->pdf->SetTitle("Horários");
            $this->pdf->Output();
        } catch (GuzzleHttp\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $dados = $responseBodyAsString;
            echo $dados;
        }
    }

}
