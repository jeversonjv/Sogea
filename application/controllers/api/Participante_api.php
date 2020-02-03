<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Participante_api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function participante_get() {
        $dados = $this->get();
        if (!empty($dados['id_participante'])) {
            $id['id_participante'] = $dados['id_participante'];
            $data = $this->Participante_model->Select_participante($id);
        } else {
            $data = $this->Participante_model->Select_participante();
        }
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function participante_matricula_get() {
        $dados = $this->get();
        $data = $this->Participante_model->Select_participante($dados);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function participante_post() {
        $dados = $this->post();
        if ($this->Participante_model->Insert_participante($dados)) {
            $this->response([
                "status" => true,
                "message" => 'Sucesso ao inserir no banco',
                "message2" => $this->db->insert_id()
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao inserir no banco"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function participante_put() {
        $dados = $this->put();
        $id['id_participante'] = $dados['id_participante'];
        if ($this->Participante_model->Update_participante($dados, $id)) {
            $this->response([
                "status" => true,
                "message" => 'Sucesso ao atualizar no banco'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao atualizar no banco"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function participante_delete() {
        $dados = $this->input->get();
        if ($this->Participante_model->Delete_participante($dados)) {
            $this->response([
                "status" => true,
                "message" => 'Sucesso ao deletar no banco'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao deletar no banco"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function participante_as_evento_get() {
        $dados = $this->get();
        if (!empty($dados['id_evento_fk'])) {
            $data = $this->Participante_model->Select_participante_as_evento($dados);
        } else {
            $data = $this->Participante_model->Select_participante_as_evento();
        }
        $this->response([
            "status" => true,
            "message" => $data,
            "message2" => $this->Participante_model->quantidade_participante()
                ], REST_Controller::HTTP_OK);
    }

    public function participante_as_evento_post() {
        $dados = $this->post();
        if ($this->Participante_model->Insert_participante_as_evento($dados)) {
            $this->response([
                "status" => true,
                "message" => 'Sucesso ao inserir no banco'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao inserir no banco"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function participante_as_evento_delete() {
        $dados = $this->input->get();
        if ($this->Participante_model->Delete_participante_as_evento($dados)) {
            $this->response([
                "status" => true,
                "message" => 'Sucesso ao deletar no banco'
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao deletar no banco"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function participante_distinct_get() {
        $dados = $this->get();
        if (count($dados) > 0) {
            $data = $this->Participante_model->Select_participante_distinct($dados);
        } else {
            $data = $this->Participante_model->Select_participante_distinct();
        }
        if (!$data) {
            $this->response([
                "status" => false,
                "message" => "Erro ao pegar os valores no banco"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                "status" => true,
                "message" => $data
                    ], REST_Controller::HTTP_OK);
        }
    }

    public function evento_as_participante_get() {
        $dados = $this->get();
        $id['id_participante_fk'] = $dados['id_participante_fk'];
        $data = $this->Participante_model->select_evento_as_participante($id);
        $this->response([
            'status' => true,
            'message' => $data,
                ], REST_Controller::HTTP_OK);
    }

}
