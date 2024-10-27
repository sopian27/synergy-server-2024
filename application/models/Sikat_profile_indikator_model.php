<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sikat_Profile_Indikator_Model extends CI_Model
{

    public function get_where($where) {
        return $this->db->get_where('sikat_profile_indikator', $where)->result();
    }
}




