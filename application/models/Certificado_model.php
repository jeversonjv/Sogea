<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Certificado_model extends CI_Model {

    public function Select_certificado_info($where) {
        $this->db->where($where);
        return $this->db->get('certificado_info')
                        ->result_array();
    }

    public function Insere_certificado_info($dados) {
        return $this->db->insert('certificado_info', $dados);
    }

    public function Update_certificado_info($dados, $where) {
        $this->db->where($where);
        return $this->db->update("certificado_info", $dados);
    }

    public function Delete_certificado_info($where) {
        $this->db->where($where);
        return $this->db->delete('certificado_info');
    }

    public function Select_certificado($where) {
        $this->db->where($where);
        return $this->db->get('certificado')
                        ->result_array();
    }

    public function Delete_certificado($where) {
        return $this->db->delete('certificado', $where);
    }

    public function Insert_certificado($dados) {
        try {
            foreach ($dados as $value) {
                $this->db->insert('certificado', $value);
            }
            return true;
        } catch (exception $ex) {
            return false;
        }
    }

    public function Select_certificado_gerar($where) {
        $this->db->where($where);
        $this->db->select('
			ci.conteudo,
			ci.data,
			ci.assinatura1,
			ci.assinatura2,
			ci.ext_imagem,
			p.nome
		');
        $this->db->from('certificado_info as ci');
        $this->db->join('certificado as c', 'ci.id_certificado_info = c.id_certificado_info_fk');
        $this->db->join('participante as p', 'p.id_participante = c.id_participante_fk');
        return $this->db->get()->result_array();
    }

}
