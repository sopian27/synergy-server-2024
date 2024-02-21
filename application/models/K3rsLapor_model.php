<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class K3rsLapor_Model extends CI_Model
{
    public function create(array $obj)
    {
        $id = $obj['tgl_kejadian'] . ';' . $obj['no_rkm_medis'];
        $tglWaktuArr = explode(" ", $obj['tgl_kejadian']);
        $data_k3rs = Util::copyIfNotEmpty(['no_rkm_medis', 'lokasi','pekerjaan', 'kronologi','kerusakan_aset',
            'cidera', 'nm_saksi','penanganan', 'nm_pelapor', 'penanggung_jawab'], $obj);
        $data_k3rs['tgl_kejadian'] = $tglWaktuArr[0];
        $data_k3rs['jam_kejadian'] = $tglWaktuArr[1];
        $this->db->insert('sikat_k3rs', $data_k3rs);
        return $id;
    }

    public function delete($id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_k3rs = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rkm_medis' => $ids[1]])->delete('sikat_k3rs');
        return $data_k3rs;
    }

    public function update(array $obj, $id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_k3rs = Util::copyIfNotEmpty(['lokasi','pekerjaan', 'kronologi','kerusakan_aset',
            'cidera', 'nm_saksi','penanganan', 'nm_pelapor', 'penanggung_jawab'], $obj);
        $data_k3rs['tgl_kejadian'] = $tglWaktuArr[0];
        $data_k3rs['jam_kejadian'] = $tglWaktuArr[1];
        $data_k3rs['no_rkm_medis'] = $ids[1];
        $data_k3rs = $this->db->replace('sikat_k3rs', $data_k3rs);
        return $data_k3rs;
    }

    public function get($id) {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $this->db
        ->select('sikat_k3rs.*, pasien.nm_pasien')
        ->from('sikat_k3rs as sikat_k3rs')
        ->join('pasien as pasien', 'pasien.no_rkm_medis=sikat_k3rs.no_rkm_medis', 'left')
        ->where(['sikat_k3rs.tgl_kejadian' => $tglWaktuArr[0], 'sikat_k3rs.jam_kejadian' => $tglWaktuArr[1], 'sikat_k3rs.no_rkm_medis' => $ids[1]]);
        return $this->db->get()->row();
    }

    public function allPasienByQuery($searchStr)
    {
        $this->db
        ->select('pasien.no_rkm_medis, pasien.nm_pasien', false)
        ->from('pasien as pasien');
        if(isset($searchStr)) {
            $this->db->like('pasien.no_rkm_medis',$searchStr); 
            $this->db->or_like('pasien.nm_pasien',$searchStr);
        }
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }

    public function all()
    {
        return $this->db->get('sikat_k3rs')->result();
    }

    public function getByQuery($noRekamMedis, $lokasi, $pekerjaan, $namaPasien, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('k3rs.no_rkm_medis, k3rs.lokasi, k3rs.pekerjaan, pasien.nm_pasien, CONCAT(k3rs.tgl_kejadian, " ", k3rs.jam_kejadian) as tgl_kejadian', false)
        ->from('sikat_k3rs as k3rs')
        ->join('pasien as pasien', 'k3rs.no_rkm_medis=pasien.no_rkm_medis', 'left')
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




