<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sikat_Profile_Indikator_Model extends CI_Model
{

    public function get_where($where) {
        return $this->db->get_where('sikat_profile_indikator', $where)->result();
    }

    public function getByQuery($tahun,$unit) {
        $this->db
        ->select('*', false)
        ->from('sikat_profile_indikator as pro')
        ->join('sikat_profile_type as tp', 'pro.process_type=tp.type', 'left')
        ->order_by('pro.create_date', 'DESC');
        if (!isset($tahun)) {
            $this->db->limit(250);
        }

        if(isset($tahun)) $this->db->where('pro.tahun =',$tahun);
        if(isset($unit)) $this->db->where('pro.process_type =',$unit);
        return $this->db->get()->result_array();
    }

    public function getLevel($unit,$tahun){
        return $this->db->query("SELECT max(level)+1 as result FROM sikat_profile_indikator WHERE tahun='".$tahun."' and PROCESS_TYPE='".$unit."'")->result();
    }
}




