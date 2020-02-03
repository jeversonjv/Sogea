<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function Usuario_select($where = null) {
        $this->db->select('*');
        $this->db->from('usuario');
        if ($where) {
            $this->db->where($where);
        }
        $result = $this->db->get();
        return $result->result_array();
    }

    public function Usuario_insere($dados) {
        return $this->db->insert('usuario', $dados);
    }

    public function Usuario_update($where, $dados) {
        $this->db->where($where);
        return $this->db->update('usuario', $dados);
    }

    public function Usuario_delete($where) {
        $this->db->where($where);
        return $this->db->delete('usuario');
    }

    public function set_evento_null($where) {
        $this->db->where($where);
        return $this->db->query('UPDATE usuario SET id_evento_fk = NULL');
    }

    public function Select_usuario_presenca($where) {
        $this->db->where('pe.id_usuario_fk', $where['id_usuario_fk']);
        $this->db->select('p.nome as nome_participante, '
                . 'p.cpf_matricula,'
                . 'o.nome as nome_ocorrencia,'
                . 'e.nome as nome_evento,'
                . 'pe.horario');
        $this->db->from('presenca as pe');
        $this->db->join('ocorrencia_evento as o', 'pe.id_ocorrencia_fk = o.id_ocorrencia', "inner");
        $this->db->join('evento as e', 'o.id_evento_fk = e.id_evento', "inner");
        $this->db->join('participante as p', 'pe.id_participante_fk = p.id_participante', "inner");
        $this->db->order_by('pe.horario', 'ASC');
        return $this->db->get()->result_array();
    }

}
