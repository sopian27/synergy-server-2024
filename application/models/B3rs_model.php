<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class B3rs_Model extends CI_Model
{
    public function create(array $obj)
    {
        $id = $obj['tgl_kejadian'] . ';' . $obj['jenis_bahan'];
        $tglWaktuArr = explode(" ", $obj['tgl_kejadian']);
        $data_b3rs = Util::copyIfNotEmpty(['jenis_bahan', 'fasa', 'lokasi', 'jumlah', 'liter',
            'penanganan', 'kondisi_setelah','nm_pelapor'], $obj);
        $data_b3rs['tgl_kejadian'] = $tglWaktuArr[0];
        $data_b3rs['jam_kejadian'] = $tglWaktuArr[1];
        $this->db->insert('sikat_b3rs', $data_b3rs);
        return $id;
    }

    public function delete($id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_b3rs = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'jenis_bahan' => $ids[1]])->delete('sikat_b3rs');
        return $data_b3rs;
    }

    public function update(array $obj, $id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_b3rs = Util::copyIfNotEmpty(['fasa', 'lokasi', 'jumlah', 'liter',
        'penanganan', 'kondisi_setelah','nm_pelapor'], $obj);
        $data_b3rs['tgl_kejadian'] = $tglWaktuArr[0];
        $data_b3rs['jam_kejadian'] = $tglWaktuArr[1];
        $data_b3rs['jenis_bahan'] = $ids[1];
        $data_b3rs = $this->db->replace('sikat_b3rs', $data_b3rs);
        return $data_b3rs;
    }

    public function get($id) {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        return $this->db->get_where('sikat_b3rs', ['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'jenis_bahan' => $ids[1]])->row();
    }

    public function all()
    {
        return $this->db->get('sikat_b3rs')->result();
    }

    public function getByQuery($jenisBahan, $fasa, $lokasi, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('b3rs.jenis_bahan, b3rs.fasa, b3rs.lokasi, b3rs.jumlah, b3rs.liter, CONCAT(b3rs.tgl_kejadian, " ", b3rs.jam_kejadian) as tgl_kejadian', false)
        ->from('sikat_b3rs as b3rs')
        ->order_by('b3rs.tgl_kejadian', 'DESC')
        ->order_by('b3rs.jam_kejadian', 'DESC');
        if (!isset($jenisBahan) && !isset($fasa) && !isset($lokasi) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($jenisBahan)) $this->db->like('b3rs.jenis_bahan',$jenisBahan);
        if(isset($fasa)) $this->db->like('b3rs.fasa',$fasa);
        if(isset($lokasi)) $this->db->like('b3rs.lokasi',$lokasi);
        if(isset($tanggalDari)) $this->db->where('b3rs.tgl_kejadian >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('b3rs.tgl_kejadian <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

}




