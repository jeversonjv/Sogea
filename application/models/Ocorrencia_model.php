<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ocorrencia_model extends CI_Model {

    public function Select_ocorrencia($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->order_by('horario', 'ASC');
        return $this->db->get('ocorrencia_evento')->result_array();
        
    }

    public function Insert_ocorrencia($dados) {
        return $this->db->insert('ocorrencia_evento', $dados);
    }

    public function Update_ocorrencia($dados, $where) {
        $this->db->where($where);
        return $this->db->update('ocorrencia_evento', $dados);
    }

    public function Delete_ocorrencia($where) {
        $this->db->where($where);
        return $this->db->delete('ocorrencia_evento');
    }

    public function Select_ocorrencia_as_evento($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->select('o.id_ocorrencia, o.nome as nome_ocorrencia, o.horario, e.nome as nome_evento, e.id_evento');
        $this->db->from('evento as e');
        $this->db->join('ocorrencia_evento as o', 'e.id_evento = o.id_evento_fk');

        return $this->db->get()->result_array();
    }

    public function Select_presenca($where) {
        $this->db->where($where);
        return $this->db->get('presenca')->result_array();
    }

    public function Insert_presenca($dados) {
        return $this->db->insert('presenca', $dados);
    }

    public function Select_presenca_ocorrencia($where) {
        try {
            foreach ($where as $value) {
                $this->db->where("id_ocorrencia_fk = ", $value['id_ocorrencia']);
            }
            return $this->db->get('presenca')->result_array();
        } catch (Exception $ex) {
            return false;
        }
    }

}
