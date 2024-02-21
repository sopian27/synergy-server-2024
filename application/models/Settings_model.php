<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_Model extends CI_Model
{
    public function update(array $obj)
    {
        $id = $this->db->from('sikat_settings')->get()->row()->id;
        $settings = Util::copyIfNotEmpty(['nama_direktur', 'nip_direktur','nama_rumah_sakit', 'waktu_kunci_pmkp', 'waktu_sembunyi_ikp', 'notif_email_ikp', 'notif_email_k3rs'], $obj);
        $settings['id'] = $id;
        $settings = $this->db->replace('sikat_settings', $settings);
        return $settings;
    }

    public function get() {
        return $this->db->from('sikat_settings')->get()->row();
    }

}




