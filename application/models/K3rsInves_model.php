<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class K3rsInves_Model extends CI_Model
{
    public function create(array $obj)
    {
        $id = $obj['tgl_kejadian'] . ';' . $obj['no_rkm_medis'];
        $tglWaktuArr = explode(" ", $obj['tgl_kejadian']);
        $data_k3rs_inves = Util::copyIfNotEmpty([
            'no_rkm_medis', 'kondisi','tindakan', 'pribadi','kurang_prosedur','kurang_sarana', 'kurang_taat',
            'rencana_tindakan1', 'rencana_tindakan2', 'rencana_tindakan3', 'rencana_tindakan4', 'rencana_tindakan5', 'rencana_tindakan6', 'rencana_tindakan7', 
            'target1', 'target2', 'target3', 'target4', 'target5', 'target6', 'target7', 
            'wewenang1', 'wewenang2', 'wewenang3', 'wewenang4', 'wewenang5', 'wewenang6', 'wewenang7', 
            'nm_penanggung', 'nm_kasir','tgl_paraf_saksi', 'tgl_paraf_penanggung', 'tgl_paraf_kasir'], $obj);
        $data_k3rs_inves['tgl_kejadian'] = $tglWaktuArr[0];
        $data_k3rs_inves['jam_kejadian'] = $tglWaktuArr[1];
        $this->db->insert('sikat_k3rs_inves', $data_k3rs_inves);
        return $id;
    }

    public function delete($id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_k3rs_inves = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rkm_medis' => $ids[1]])->delete('sikat_k3rs_inves');
        return $data_k3rs_inves;
    }

    public function update(array $obj, $id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_k3rs_inves = Util::copyIfNotEmpty([
            'kondisi','tindakan', 'pribadi','kurang_prosedur','kurang_sarana', 'kurang_taat',
            'rencana_tindakan1', 'rencana_tindakan2', 'rencana_tindakan3', 'rencana_tindakan4', 'rencana_tindakan5', 'rencana_tindakan6', 'rencana_tindakan7', 
            'target1', 'target2', 'target3', 'target4', 'target5', 'target6', 'target7', 
            'wewenang1', 'wewenang2', 'wewenang3', 'wewenang4', 'wewenang5', 'wewenang6', 'wewenang7', 
            'nm_penanggung', 'nm_kasir','tgl_paraf_saksi', 'tgl_paraf_penanggung', 'tgl_paraf_kasir'], $obj);
        $data_k3rs_inves['tgl_kejadian'] = $tglWaktuArr[0];
        $data_k3rs_inves['jam_kejadian'] = $tglWaktuArr[1];
        $data_k3rs_inves['no_rkm_medis'] = $ids[1];
        $data_k3rs_inves = $this->db->replace('sikat_k3rs_inves', $data_k3rs_inves);
        return $data_k3rs_inves;
    }

    public function get($id) {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        return $this->db->get_where('sikat_k3rs_inves', ['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rkm_medis' => $ids[1]])->row();
    }

    public function all()
    {
        return $this->db->get('sikat_k3rs_inves')->result();
    }

    public function getByQuery($noRekamMedis, $lokasi, $pekerjaan, $namaPasien, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('k3rs.no_rkm_medis, k3rs.lokasi, k3rs.pekerjaan, k3rs.nm_saksi, pasien.nm_pasien, CONCAT(k3rs.tgl_kejadian, " ", k3rs.jam_kejadian) as tgl_kejadian, inves.no_rkm_medis as inves_no', false)
        ->from('sikat_k3rs as k3rs')
        ->join('pasien as pasien', 'k3rs.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('sikat_k3rs_inves as inves', 'k3rs.tgl_kejadian=inves.tgl_kejadian and k3rs.jam_kejadian=inves.jam_kejadian and k3rs.no_rkm_medis=inves.no_rkm_medis', 'left')
        ->order_by('k3rs.tgl_kejadian', 'DESC')
        ->order_by('k3rs.jam_kejadian', 'DESC');
        if (!isset($noRekamMedis) && !isset($lokasi) && !isset($pekerjaan) && !isset($namaPasien) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($noRekamMedis)) $this->db->like('k3rs.no_rkm_medis',$noRekamMedis);
        if(isset($lokasi)) $this->db->like('k3rs.lokasi',$lokasi);
        if(isset($pekerjaan)) $this->db->like('k3rs.pekerjaan',$pekerjaan);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($tanggalDari)) $this->db->where('k3rs.tgl_kejadian >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('k3rs.tgl_kejadian <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

}




