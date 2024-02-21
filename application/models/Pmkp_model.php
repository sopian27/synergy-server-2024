<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pmkp_Model extends CI_Model
{
    public function create(array $obj)
    {
        $data = Util::copyIfNotEmpty(['type','year','month','dailyData','monthlyData'], $obj);
        $data_str = implode(",", $data);
        log_message('debug',"data: ".$data_str);
        $this->db->insert('sikat_pmkp', $data);
        log_message('debug',"query: ".$this->db->last_query());
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('sikat_pmkp');
    }

    public function update(array $obj, $id)
    {
        $data = Util::copyIfNotEmpty(['type','year','month','dailyData','monthlyData'], $obj);
        $data['id'] = $id;
        $data_str = implode(",", $data);
        log_message('debug',"data: ".$data_str);
        return $this->db->replace('sikat_pmkp', $data);
    }

    public function get($id) {
        return $this->db->get_where('sikat_pmkp', ['id' => $id])->row();
    }

    public function all()
    {
        return $this->db->get('sikat_pmkp')->result();
    }

    public function getByYearAndMonthAndType($year, $month, $type) {
        $sql = "SELECT * FROM sikat_pmkp WHERE year='".$year."' AND month='".$month."' AND type='".$type."' ORDER BY id DESC";
        $query = $this->db->query($sql);
        return $query->row();
        // return$this->db->get_where('sikat_pmkp', ['year' => $year, 'month' => $month, 'type' => $type])->row();
    }

    public function getByYearAndType($year, $type) {
        // return $this->db->get_where('sikat_pmkp', ['year' => $year, 'type' => $type])->result();
        $sql = "SELECT * FROM sikat_pmkp WHERE year='".$year."' AND type='".$type."' ORDER BY id DESC";
        $query = $this->db->query($sql);
        return $query->result();
    }

}




