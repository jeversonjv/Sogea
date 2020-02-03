<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Certificado_api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function certificado_get() {
        $dados = $this->get();
        $data = $this->Certificado_model->Select_certificado_info($dados);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function certificado_post() {
        $dados = $this->post();
        if ($this->Certificado_model->Insere_certificado_info($dados)) {
            $this->response([
                "status" => true,
                "message" => "Ok"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function certificado_put() {
        $dados = $this->put();
        $where['id_evento_fk'] = $dados['id_evento_fk'];
        if ($this->Certificado_model->Update_certificado_info($dados, $where)) {
            $this->response([
                "status" => true,
                "message" => "Ok"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function certificado_delete() {
        $dados = $this->input->get();
        if ($this->Certificado_model->Delete_certificado_info($dados)) {
            $this->response([
                "status" => true,
                "message" => "Ok"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function certificado_real_get() {
        $dados = $this->get();
        $data = $this->Certificado_model->Select_certificado($dados);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function certificado_real_post() {
        $dados = $this->input->post();
        if ($this->Certificado_model->Insert_certificado($dados)) {
            $this->response([
                "status" => true,
                "message" => "Ok"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function certificado_real_delete() {
        $dados = $this->input->get();
        $where['id_certificado_info_fk'] = $dados['id_certificado_info_fk'];
        if ($this->Certificado_model->Delete_certificado($where)) {
            $this->response([
                "status" => true,
                "message" => "Ok"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function Select_certificado_gerar_get() {
        $dados = $this->get();
        $data = $this->Certificado_model->Select_certificado_gerar($dados);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

}
