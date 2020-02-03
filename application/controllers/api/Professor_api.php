<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Professor_api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function professor_get() {
        $dados = $this->get();
        if (isset($dados['id_professor'])) {
            $id['id_professor'] = $dados['id_professor'];
            $data = $this->Professor_model->Select_professor($id, 'asc');
        } else {
            $data = $this->Professor_model->Select_professor(null, 'asc');
        }
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function professor_post() {
        $dados = $this->post();
        if (!$this->Professor_model->Insere_professor($dados)) {
            $this->response([
                "status" => false,
                "message" => "Erro ao inserir no banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                "status" => true,
                "message" => $this->db->insert_id()
                    ], REST_Controller::HTTP_OK);
        }
    }

    public function professor_put() {
        $dados = $this->put();
        $id['id_professor'] = $dados['id_professor'];
        if ($this->Professor_model->Update_professor($dados, $id)) {
            $this->response([
                "status" => true,
                "message" => "Inserido com sucesso!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao atualizar o banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function professor_delete() {
        $dados = $this->input->get();
        if ($this->Professor_model->Delete_professor($dados)) {
            $this->response([
                "status" => true,
                "message" => "Deletado com sucesso!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao delete do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function horario_get() {
        $dados = $this->get();
        if (isset($dados['id_horario'])) {
            $data = $this->Professor_model->Select_horario($dados);
        } else {
            $data = $this->Professor_model->Select_horario(null);
        }
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function professor_as_horario_get() {
        $dados = $this->get();
        if (isset($dados['id_professor']) && isset($dados['dia_semana_fk'])) {
            $data = $this->Professor_model->professor_as_horario($dados);
        } else {
            $data = $this->Professor_model->professor_as_horario(NULL);
        }
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function professor_horario_post() {
        $dados = $this->post();
        if (!$this->Professor_model->Insere_professores_horario($dados)) {
            $this->response([
                "status" => false,
                "message" => "Erro ao inserir no banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                "status" => true,
                "message" => "Sucesso ao inserir horario"
                    ], REST_Controller::HTTP_OK);
        }
    }

    public function professor_horario_put() {
        $dados = $this->put();
        $trabalha['trabalha'] = $dados['trabalha'];
        $where['id_professor_fk'] = $dados['id_professor_fk'];
        $where['id_horario_fk'] = $dados['id_horario_fk'];
        if (!$this->Professor_model->update_professores_horario($trabalha, $where)) {
            $this->response([
                "status" => false,
                "message" => "Erro ao atualizar no banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                "status" => true,
                "message" => "Sucesso ao atualizar horario"
                    ], REST_Controller::HTTP_OK);
        }
    }

    public function Select_dia_semana_get() {
        $dados = $this->Professor_model->Select_dia_semana();
        $this->response([
            "status" => true,
            "message" => $dados
                ], REST_Controller::HTTP_OK);
    }

    public function horario_expediente_get() {
        $dados = $this->get();
        $data = $this->Professor_model->Select_horario_expediente($dados);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function horario_expediente_delete() {
        $dados = $this->input->get();
        $id['id_horario_expediente'] = $dados['id_horario_expediente'];
        if ($this->Professor_model->Delete_horario_expediente($id)) {
            $this->response([
                "status" => true,
                "message" => "Sucesso ao apagar horario"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao apagar do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function horario_expediente_post() {
        $dados = $this->post();
        if ($this->Professor_model->Insert_horario_expediente($dados)) {
            $this->response([
                "status" => true,
                "message" => $this->db->insert_id()
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                "status" => false,
                "message" => "Erro ao inserir no banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function horario_after_expediente_post() {
        $dados = $this->post();
        $data = $this->Professor_model->Insert_horario_after_expediente($dados);
        if (!$data) {
            $this->response([
                "status" => false,
                "message" => "Erro ao inserir no banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                "status" => true,
                "message" => $data
                    ], REST_Controller::HTTP_OK);
        }
    }

}
