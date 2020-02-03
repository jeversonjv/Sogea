<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Ocorrencia_api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function ocorrencia_get() {
        $dados = $this->get();

        if (!empty($dados['id_evento_fk'])) {
            $id['id_evento_fk'] = $dados['id_evento_fk'];
        }

        if (!empty($dados['id_ocorrencia'])) {
            if (isset($id['id_evento_fk'])) {
                unset($id['id_evento_fk']);
            }
            $id['id_ocorrencia'] = $dados['id_ocorrencia'];
        }

        if (!empty($dados['id_evento_fk']) && !empty($dados['id_ocorrencia'])) {
            $id['id_evento_fk'] = $dados['id_evento_fk'];
        }

        $data = $this->Ocorrencia_model->Select_ocorrencia($id);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function ocorrencia_post() {
        $dados = $this->post();
        if ($this->Ocorrencia_model->Insert_ocorrencia($dados)) {
            $this->response([
                "status" => TRUE,
                "message" => "Sucesso ao inserir"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Erro ao inserir"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function ocorrencia_put() {
        $dados = $this->put();
        $id['id_ocorrencia'] = $dados['id_ocorrencia'];
        unset($dados['id_ocorrencia']);
        if ($this->Ocorrencia_model->Update_ocorrencia($dados, $id)) {
            $this->response([
                "status" => TRUE,
                "message" => "Sucesso ao atualizar"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Erro ao atualizar"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function ocorrencia_delete() {
        $dados = $this->input->get();
        if ($this->Ocorrencia_model->Delete_ocorrencia($dados)) {
            $this->response([
                "status" => TRUE,
                "message" => "Sucesso ao deletar"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Erro ao deletar"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function ocorrencia_as_evento_get() {
        $dados = $this->get();
        $id['id_evento'] = $dados['id_evento'];
        $data = $this->Ocorrencia_model->Select_ocorrencia_as_evento($id);
        $this->response([
            "status" => TRUE,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function presenca_get() {
        $dados = $this->get();
        $id['id_participante_fk'] = $dados['id_participante_fk'];
        $id['id_ocorrencia_fk'] = $dados['id_ocorrencia_fk'];
        $data = $this->Ocorrencia_model->Select_presenca($id);
        $this->response([
            "status" => TRUE,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function presenca_post() {
        $dados = $this->post();
        if ($this->Ocorrencia_model->Insert_presenca($dados)) {
            $this->response([
                "status" => TRUE,
                "message" => "Sucesso ao registrar a presença"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Erro ao registrar a presença"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function presenca_ocorrencia_get() {
        $dados = $this->get();
        if ($this->Ocorrencia_model->Select_presenca_ocorrencia($dados)) {
            $data = $this->Ocorrencia_model->Select_presenca_ocorrencia($dados);
            $this->response([
                "status" => TRUE,
                "message" => $data
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Ocorreu um erro"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

}
