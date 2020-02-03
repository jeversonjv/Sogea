<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Evento_api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function evento_get() {
        $dados = $this->get();
        if (!empty($dados['id_evento']) || (!empty($dados['ativo'])) || (!empty($dados['estado'])) || (!empty($dados['sub_id_evento']))) {
            $data = $this->Evento_model->Select_evento($dados, null, null);
        } else if (!empty($dados['limit'])) {
            $limite = $dados['limit'];
            $data = $this->Evento_model->Select_evento(null, $limite, 'asc');
        } else {
            $data = $this->Evento_model->Select_evento(null, null, 'asc');
        }
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function evento_relatorio_get() {
        $dados = $this->get();
        $id['id_evento'] = $dados['id_evento'];
        $order['order'] = $dados['order'];
        $data = $this->Evento_model->select_relatorio_evento($id, $order);
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function evento_pai_get() {
        $dados = $this->get();
        $data = $this->Evento_model->Select_evento_pai();
        $this->response([
            "status" => true,
            "message" => $data
                ], REST_Controller::HTTP_OK);
    }

    public function evento_post() {
        $dados = $this->post();
        if ($this->Evento_model->Insert_evento($dados)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao inserir no banco!",
                'ultimo_id' => $this->db->insert_id()
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao inserir no banco!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function evento_put() {
        $dados = $this->put();
        if (!empty($dados['id_evento'])) {
            $id['id_evento'] = $dados['id_evento'];
        }
        if (!empty($dados['sub_id_evento'])) {
            if (isset($id['id_evento'])) {
                unset($id['id_evento']);
            }
            $id['sub_id_evento'] = $dados['sub_id_evento'];
        }

        if (!empty($dados['id_evento']) && !empty($dados['sub_id_evento'])) {
            unset($id['sub_id_evento']);
            $id['id_evento'] = $dados['id_evento'];
        }

        if ($this->Evento_model->Update_evento($dados, $id)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao atualizaro banco!",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao inserir no banco!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function evento_delete() {
        $dados = $this->input->get();
        if ($this->Evento_model->Delete_evento($dados)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao apagar o evento do banco!",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao apagar do banco!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function set_evento_pai_null_put() {
        $dados = $this->put();
        $id['id_evento'] = $dados['id_evento'];
        if ($this->Evento_model->set_evento_pai_null($id)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao atualizar o evento pai do banco!",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao atualizar do banco!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function tipo_get() {
        $id = $this->get();
        if (count($id) > 0) {
            $dados = $this->Evento_model->Select_tipo($id);
        } else {
            $dados = $this->Evento_model->Select_tipo(null);
        }
        $this->response([
            'status' => true,
            'message' => $dados
                ], REST_Controller::HTTP_OK);
    }

    public function tipo_distinct_get() {
        $dados = $this->get();
        $data = $this->Evento_model->Select_distinct_tipo($dados);
        $this->response([
            'status' => true,
            'message' => $data,
            'message2' => $this->db->last_query()
                ], REST_Controller::HTTP_OK);
    }

    public function tipo_post() {
        $dados = $this->post();
        if ($this->Evento_model->Insert_tipo($dados)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao inserir no banco!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao inserir no banco!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function tipo_put() {
        $dados = $this->put();
        $id['id_tipo'] = $dados['id_tipo'];
        if ($this->Evento_model->Update_tipo($dados, $id)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao atualizar!"
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao atualizar o banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function tipo_delete() {
        $dados = $this->input->get();
        $id['id_tipo'] = $dados['id_tipo'];
        if (!$this->Evento_model->Delete_tipo($id)) {
            $this->response([
                'status' => false,
                'message' => "Erro ao excluir do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao excluir do banco!"
                    ], REST_Controller::HTTP_OK);
        }
    }

    public function tipo_as_evento_get() {
        $dados = $this->get();
        $data = $this->Evento_model->tipo_as_evento($dados);
        $this->response([
            'status' => true,
            'message' => $data,
                ], REST_Controller::HTTP_OK);
    }

    public function eventoAsTipo_get() {
        $dados = $this->get();
        $data = $this->Evento_model->Evento_as_tipo($dados);
        $this->response([
            'status' => true,
            'message' => $data,
                ], REST_Controller::HTTP_OK);
    }

    public function eventoAstipo_post() {
        $dados = $this->post();
        if ($this->Evento_model->Insert_evento_as_tipo($dados)) {
            $this->response([
                'status' => true,
                'message' => "Erro ao inserir no banco",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao inserir do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function eventoAstipo_delete() {
        $dados = $this->input->get();
        $id['id_evento_fk'] = $dados['id_evento_fk'];
        if ($this->Evento_model->Delete_evento_as_tipo($id)) {
            $this->response([
                'status' => true,
                'message' => "Erro ao deletar no banco",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao deletar do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function eventoAsprofessor_get() {
        $dados = $this->get();
        $data = $this->Evento_model->Evento_as_professor($dados);
        $this->response([
            'status' => true,
            'message' => $data,
                ], REST_Controller::HTTP_OK);
    }

    public function eventoAsprofessor_post() {
        $dados = $this->post();
        if ($this->Evento_model->Insert_Evento_as_professor($dados)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao inserir no banco de dados.",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao inserir do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function eventoAsprofessor_delete() {
        $dados = $this->input->get();
        if ($this->Evento_model->Delete_Evento_as_professor($dados)) {
            $this->response([
                'status' => true,
                'message' => "Sucesso ao inserir no banco de dados.",
                    ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Erro ao deletar do banco de dados!"
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function evento_pagination_get() {
        $dados = $this->get();
        $maximo = $dados['maximo'];
        $inicio = $dados['inicio'];
        $data = $this->Evento_model->Select_evento_pagination($maximo, $inicio);
        $data2 = $this->Evento_model->Select_quantidade();
        $this->response([
            'status' => true,
            'message' => $data,
            'message2' => $data2
                ], REST_Controller::HTTP_OK);
    }

    public function professor_as_evento_as_horario_get() {
        $dados = $this->get();
        $data = $this->Professor_model->professor_as_evento_as_horario($dados);
        $this->response([
            'status' => true,
            'message' => $data,
                ], REST_Controller::HTTP_OK);
    }

}
