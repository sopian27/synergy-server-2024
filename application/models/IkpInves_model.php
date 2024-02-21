<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IkpInves_Model extends CI_Model
{
    public function create(array $obj)
    {
        $id = $obj['tgl_kejadian'] . ';' . $obj['no_rawat'];
        $tglWaktuArr = explode(" ", $obj['tgl_kejadian']);
        $data_ikp_inves = Util::copyIfNotEmpty(['no_rawat', 'penyebab_langsung','latar_belakang', 'rekomendasi','tindakan_akan',
            'tgl_mulai', 'tgl_selesai','penanggung_jawab', 'lengkap', 'inves_lanjut', 'grading_risiko'], $obj);
        $data_ikp_inves['tgl_kejadian'] = $tglWaktuArr[0];
        $data_ikp_inves['jam_kejadian'] = $tglWaktuArr[1];
        $this->db->insert('sikat_ikp_inves', $data_ikp_inves);
        return $id;
    }

    public function delete($id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_ikp_inves = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rawat' => $ids[1]])->delete('sikat_ikp_inves');
        return $data_ikp_inves;
    }

    public function update(array $obj, $id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_ikp_inves = Util::copyIfNotEmpty(['penyebab_langsung','latar_belakang', 'rekomendasi','tindakan_akan',
            'tgl_mulai', 'tgl_selesai','penanggung_jawab', 'lengkap', 'inves_lanjut', 'grading_risiko'], $obj);
        $data_ikp_inves['tgl_kejadian'] = $tglWaktuArr[0];
        $data_ikp_inves['jam_kejadian'] = $tglWaktuArr[1];
        $data_ikp_inves['no_rawat'] = $ids[1];
        $data_ikp_inves = $this->db->replace('sikat_ikp_inves', $data_ikp_inves);
        return $data_ikp_inves;
    }

    public function get($id) {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        return $this->db->get_where('sikat_ikp_inves', ['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rawat' => $ids[1]])->row();
    }

    public function all()
    {
        return $this->db->get('sikat_ikp_inves')->result();
    }

    public function getByQuery($isRanap, $noRawat, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('reg.no_rkm_medis, reg.tgl_registrasi, ikp.no_rawat, ikp.kode_insiden, insi.nama as nama_insiden, kamar.kd_kamar, bangsal.nm_bangsal, ikp.nip, petugas.nama as nama_petugas, reg.status_lanjut, pasien.nm_pasien, reg.kd_dokter, dokter.nm_dokter, pasien.umur, pasien.jk as jenis_kelamin, penjab.png_jawab as cara_bayar, CONCAT(ikp.tgl_kejadian, " ", ikp.jam_kejadian) as tgl_kejadian, inves.no_rawat as inves_no', false)
        ->from('insiden_keselamatan_pasien as ikp')
        ->join('reg_periksa as reg', 'reg.no_rawat=ikp.no_rawat', 'left')
        ->join('penjab as penjab', 'reg.kd_pj=penjab.kd_pj', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('petugas as petugas', 'ikp.nip=petugas.nip', 'left')
        ->join('sikat_ikp as sikatikp', 'sikatikp.tgl_kejadian=ikp.tgl_kejadian and sikatikp.jam_kejadian=ikp.jam_kejadian and sikatikp.no_rawat=ikp.no_rawat', 'left')
        ->join('sikat_nama_insiden as insi', 'sikatikp.nama_insiden=insi.id', 'left')
        ->join('kamar_inap as kamar', "reg.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->join('sikat_ikp_inves as inves', 'ikp.tgl_kejadian=inves.tgl_kejadian and ikp.jam_kejadian=inves.jam_kejadian and ikp.no_rawat=inves.no_rawat', 'left')
        ->order_by('ikp.tgl_kejadian', 'DESC')
        ->order_by('ikp.jam_kejadian', 'DESC');
        if (!isset($noRawat) && !isset($namaInsiden) && !isset($namaPetugas) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($isRanap) && $isRanap == true) {
            if($isRanap == 'true') $this->db->where('kamar.kd_kamar is NOT NULL', NULL, FALSE);
            else $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
        }
        if(isset($noRawat)) $this->db->like('reg.no_rawat',$noRawat);
        if(isset($namaInsiden)) $this->db->like('insi.nama',$namaInsiden);
        if(isset($namaPetugas)) $this->db->like('petugas.nama',$namaPetugas);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($tanggalDari)) $this->db->where('ikp.tgl_kejadian >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('ikp.tgl_kejadian <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

    public function reportHarianByQuery($noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('reg.no_rkm_medis, inves.*, pasien.nm_pasien, reg.kd_dokter, dokter.nm_dokter, kamar.kd_kamar, bangsal.nm_bangsal, penjab.png_jawab as cara_bayar', false)
        ->from('sikat_ikp_inves as inves')
        ->join('reg_periksa as reg', 'inves.no_rawat=reg.no_rawat', 'left')
        ->join('penjab as penjab', 'reg.kd_pj=penjab.kd_pj', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "reg.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->group_by(array("inves.no_rawat", "inves.tgl_kejadian", "inves.jam_kejadian"))
        ->order_by('inves.tgl_kejadian', 'DESC');
        if (!isset($noRawat) && !isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($noRawat)) $this->db->like('inves.no_rawat',$noRawat);
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($tanggalDari)) $this->db->where('inves.tgl_kejadian >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('inves.tgl_kejadian <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

}




