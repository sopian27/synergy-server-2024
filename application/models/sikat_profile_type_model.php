<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sikat_Profile_Type_Model extends CI_Model
{

    public function get($id) {
        return $this->db->get_where('sikat_profile_type', ['type' => $id])->row();
    }

    public function all()
    {
        return $this->db->get('sikat_profile_type')->result();
    }
}




