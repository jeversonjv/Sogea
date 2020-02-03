<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Professor_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function Select_professor($where = null, $order = null) {
        $this->db->select('*');
        $this->db->from('professores');
        if ($where) {
            $this->db->where($where);
        }
        if ($order) {
            $this->db->order_by('nome', $order);
        }
        $result = $this->db->get();
        return $result->result_array();
    }

    public function Insere_professor($dados) {
        return $this->db->insert('professores', $dados);
    }

    public function Update_professor($dados, $where) {
        $this->db->where($where);
        return $this->db->update('professores', $dados);
    }

    public function Delete_professor($where) {
        $this->db->where($where);
        return $this->db->delete('professores');
    }

    public function Select_horario($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->from('horario');
        $this->db->select('*');
        $result = $this->db->get();
        return $result->result_array();
    }

    public function Insere_professores_horario($dados) {
        try {
            foreach ($dados as $value) {
                $this->db->insert('professores_horario', $value);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function update_professores_horario($dados, $where) {
        $this->db->where($where);
        return $this->db->update('professores_horario', $dados);
    }

    public function professor_as_horario($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->select('h.id_horario, he.hora_inicio, he.hora_final,'
                . 'ph.trabalha, ph.id_professor_fk, ph.id_horario_fk,'
                . 'p.id_professor, p.nome,'
                . 'ds.dia_semana');
        $this->db->from('horario as h');
        $this->db->join('professores_horario as ph', 'h.id_horario = ph.id_horario_fk');
        $this->db->join('professores as p', 'ph,id_professor_fk = p.id_professor');
        $this->db->join('dia_semana as ds', 'h.dia_semana_fk = id_dia_semana');
        $this->db->join('horario_expediente as he', 'h.id_horario_expediente_fk = he.id_horario_expediente');
        $this->db->order_by('he.hora_inicio', 'asc');

        $result = $this->db->get();
        return $result->result_array();
    }

    public function Select_dia_semana() {
        $this->db->from('dia_semana');
        $this->db->select('*');
        $result = $this->db->get();
        return $result->result_array();
    }

    public function professor_as_evento_as_horario($where) {
        $query = 'SELECT nome, id_professor FROM professores inner join professores_horario on id_professor_fk = id_professor '
                . 'inner join horario on id_horario_fk = id_horario inner join horario_expediente on id_horario_expediente_fk = id_horario_expediente'
                . ' WHERE trabalha = 1 and hora_inicio BETWEEN '
                . $where['hora_inicial'] . ' and ' . $where['hora_termino']
                . ' and hora_final BETWEEN '
                . $where['hora_inicial'] . ' and ' . $where['hora_termino'];
        return $this->db->query($query)->result_array();
    }

    public function Select_horario_expediente() {
        $this->db->from('horario_expediente');
        $this->db->select('*');
        return $this->db->get()->result_array();
    }

    public function Delete_horario_expediente($where) {
        $this->db->where($where);
        return $this->db->delete('horario_expediente');
    }

    public function Insert_horario_expediente($dados) {
        return $this->db->insert("horario_expediente", $dados);
    }

    public function Insert_horario_after_expediente($dados) {
        $data['id_horario'] = array();
        try {
            foreach ($dados as $value) {
                $this->db->insert('horario', $value);
                array_push($data['id_horario'], $this->db->insert_id());
            }
            return $data;
        } catch (Exception $ex) {
            return false;
        }
    }

}
