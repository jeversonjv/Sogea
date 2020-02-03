<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Participante_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function Select_participante($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->order_by('nome', 'ASC');
        return $this->db->get('participante')->result_array();
    }

    public function Insert_participante($dados) {
        return $this->db->insert('participante', $dados);
    }

    public function Update_participante($dados, $where) {
        $this->db->where($where);
        return $this->db->update('participante', $dados);
    }

    public function Delete_participante($where) {
        $this->db->where($where);
        return $this->db->delete('participante');
    }

    public function Select_participante_as_evento($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->select('e.nome as nome_evento, e.id_evento, p.nome, p.id_participante, p.cpf_matricula');
        $this->db->from('evento as e');
        $this->db->join('participante_evento as pe', 'e.id_evento = pe.id_evento_fk');
        $this->db->join('participante as p', 'p.id_participante = pe.id_participante_fk');
        $this->db->order_by('p.cpf_matricula', 'ASC');
        $this->db->distinct();
        return $this->db->get()->result_array();
    }

    public function Select_participante_distinct($dados = null) {
        try {
            if ($dados) {
                foreach ($dados as $value) {
                    $this->db->where("id_participante != ", $value['id_participante']);
                }
            }
            $this->db->distinct();
            return $this->db->get('participante')->result_array();
        } catch (Exception $ex) {
            return false;
        }
    }

    public function Delete_participante_as_evento($where) {
        $this->db->where($where);
        return $this->db->delete('participante_evento');
    }

    public function Insert_participante_as_evento($dados) {
        return $this->db->insert('participante_evento', $dados);
    }

    public function quantidade_participante() {
        return $this->db->count_all_results('participante');
    }

    public function select_evento_as_participante($where) {
        $this->db->select('e.nome, e.id_evento');
        $this->db->from('participante_evento as pe');
        $this->db->join('participante as p', 'pe.id_participante_fk = p.id_participante', 'inner');
        $this->db->join('evento as e', 'e.id_evento = pe.id_evento_fk', 'inner');
        $this->db->where('e.sub_id_evento is null');
        $this->db->where($where);
        return $this->db->get()->result_array();
    }

}
