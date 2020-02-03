<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Evento_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function Select_evento($where = NULL, $limite = NULL, $order = NULL) {
        $this->db->select('*');
        $this->db->from('evento');
        if ($where) {
            $this->db->where($where);
        }
        if ($limite) {
            $this->db->limit($limite);
        }
        if ($order) {
            $this->db->order_by('data', $order);
        }
        $result = $this->db->get();
        return $result->result_array();
    }

    public function Insert_evento($dados = NULL) {
        return $this->db->insert('evento', $dados);
    }

    public function Update_evento($dados = null, $where = null) {
        $this->db->where($where);
        return $this->db->update('evento', $dados);
    }

    public function Delete_evento($where = null) {
        $this->db->where($where);
        return $this->db->delete('evento');
    }

    public function Select_tipo($where = NULL) {
        $this->db->select('id_tipo, tipo');
        $this->db->from('tipo');
        if ($where) {
            $this->db->where($where);
        }
        $result = $this->db->get();
        return $result->result_array();
    }

    public function Insert_tipo($dados = null) {
        return $this->db->insert('tipo', $dados);
    }

    public function Update_tipo($dados = null, $where = null) {
        $this->db->where($where);
        return $this->db->update('tipo', $dados);
    }

    public function Delete_tipo($where) {
        $this->db->where($where);
        return $this->db->delete('tipo');
    }

    public function Select_distinct_tipo($dados = null) {
        try {
            foreach ($dados as $value) {
                $this->db->where("tipo !=", $value['tipo']);
            }
            return $this->db->get('tipo')->result_array();
        } catch (Exception $ex) {
            return false;
        }
    }

    public function Select_evento_pagination($maximo, $inicio) {
        $this->db->order_by('data', 'desc');
        return $this->db->get('evento', $maximo, $inicio)->result_array();
    }

    public function Select_quantidade() {
        return $this->db->count_all_results('evento');
    }

    public function Insert_evento_as_tipo($dados = null) {
        try {
            foreach ($dados as $value) {
                $this->db->insert('tipo_evento', $value);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function Delete_evento_as_tipo($where = null) {
        $this->db->where($where);
        return $this->db->delete('tipo_evento');
    }

    public function tipo_as_evento($where) {
        $this->db->where($where);
        $this->db->from('tipo_evento as te');
        $this->db->select('e.nome, e.data, e.descricao, e.hora_inicial, t.tipo, e.ativo, e.estado, e.id_evento');
        $this->db->join('tipo as t', 't.id_tipo = te.id_tipo_fk');
        $this->db->join('evento as e', 'e.id_evento = te.id_evento_fk');
        $this->db->order_by('e.hora_inicial', 'DESC');
        return $this->db->get()->result_array();
    }

    public function Evento_as_tipo($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->select('t.tipo, t.id_tipo');
        $this->db->from('tipo_evento as te');
        $this->db->join('tipo as t', 'te.id_tipo_fk = t.id_tipo');
        return $this->db->get()->result_array();
    }

    public function Evento_as_professor($where = null) {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->select('p.nome as nome_prof, p.id_professor');
        $this->db->from('professores_evento as pe');
        $this->db->join('professores as p', 'pe.id_professores_fk = p.id_professor');
        return $this->db->get()->result_array();
    }

    public function Delete_Evento_as_professor($where = null) {
        $this->db->where($where);
        return $this->db->delete('professores_evento');
    }

    public function Insert_Evento_as_professor($dados) {
        return $this->db->insert('professores_evento', $dados);
    }

    public function set_evento_pai_null($where) {
        $this->db->where($where);
        return $this->db->query('UPDATE evento SET sub_id_evento = NULL');
    }

    public function Select_evento_pai() {
        return $this->db->query('SELECT id_evento, nome, estado, ativo, id_usuario_fk FROM evento WHERE sub_id_evento IS NULL')->result_array();
    }

    public function select_relatorio_evento($where, $order) {
        $query = "SELECT ee.nome, e.id_evento, e.data, oe.nome as nome_ocorrencia, oe.horario, ee.id_evento as id_evento_sec, oe.id_evento_fk from evento as e"
                . " inner join evento as ee on e.id_evento = ee.sub_id_evento inner join "
                . "ocorrencia_evento as oe on oe.id_evento_fk = ee.id_evento WHERE e.id_evento = " . $where['id_evento'] . " ORDER BY " . $order['order'][0] . ", " . $order['order'][1];
        return $this->db->query($query)->result_array();
    }

}
