<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Usuario_api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function usuario_get() {
        $dados = $this->get();
        if (!empty($dados)) {
            $data = $this->Usuario_model->Usuario_select($dados);
        } else {
            $data = $this->Usuario_model->Usuario_select(null);
        }

        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function usuario_post() {
        $dados = $this->post();
        if ($this->Usuario_model->Usuario_insere($dados)) {
            $this->response([
                "status" => true,
                "message" => "Inserido com sucesso!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => FALSE,
                "message" => "Inserido sem sucesso!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function usuario_put() {
        $dados = $this->put();
        $id['id_usuario'] = $dados['id_usuario'];
        if ($this->Usuario_model->Usuario_update($id, $dados)) {
            $this->response([
                "status" => true,
                "message" => "Atualizado com sucesso!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Atualiazdo sem sucesso!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function usuario_delete() {
        $dados = $this->input->get();
        if ($this->Usuario_model->Usuario_delete($dados)) {
            $this->response([
                "status" => true,
                "message" => "Deletado com sucesso!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Deletado sem sucesso!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function usuario_set_evento_null_put() {
        $dados = $this->put();
        if ($this->Usuario_model->set_evento_null($dados)) {
            $this->response([
                "status" => true,
                "message" => "Atualizado com sucesso!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Atualiazdo sem sucesso!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function usuario_presenca_get() {
        $dados = $this->get();
        $data = $this->Usuario_model->Select_usuario_presenca($dados);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

}
