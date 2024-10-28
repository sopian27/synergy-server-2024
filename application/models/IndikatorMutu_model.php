<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IndikatorMutu_Model extends CI_Model
{
    public function create(array $obj)
    {
        $id = $obj['tgl_kejadian'] . ';' . $obj['no_rawat'];
        $tglWaktuArr = explode(" ", $obj['tgl_kejadian']);
        $tglWaktuLaporArr = explode(" ", $obj['tgl_lapor']);
        $data_ikp = Util::copyIfNotEmpty(['no_rawat', 'kode_insiden', 'nip', 'lokasi', 
            'unit_terkait', 'akibat', 'tindakan_insiden', 'identifikasi_masalah', 'rtl'], $obj);
        $data_ikp['tgl_kejadian'] = $tglWaktuArr[0];
        $data_ikp['jam_kejadian'] = $tglWaktuArr[1];
        $data_ikp['tgl_lapor'] = $tglWaktuLaporArr[0];
        $data_ikp['jam_lapor'] = $tglWaktuLaporArr[1];
        $data_sikat = Util::copyIfNotEmpty(['no_rawat','pertama_melaporkan','unit_penyebab','kejadian_sebelumnya','pencegahan_terulang','nm_pembuat',
            'tgl_terima','nm_penerima', 'grading_risiko', 'jenis_insiden', 'nama_insiden', 'skor_dampak', 'tipe_insiden', 'subtipe_insiden', 'frekuensi_kejadian', 'tindakan_oleh'], $obj);
        $data_sikat['tgl_kejadian'] = $tglWaktuArr[0];
        $data_sikat['jam_kejadian'] = $tglWaktuArr[1];
        $this->db->insert('insiden_keselamatan_pasien', $data_ikp);
        $this->db->set('created_date', 'NOW()', FALSE);
        $this->db->insert('sikat_ikp', $data_sikat);
        return $id;
    }

    public function save($data){
        $this->db->insert('sikat_profile_indikator', $data);
        $this->db->set('create_date', 'NOW()', FALSE);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $data_ikp = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rawat' => $ids[1]])->delete('insiden_keselamatan_pasien');
        $data_sikat = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rawat' => $ids[1]])->delete('sikat_ikp');
        return $data_ikp;
    }

    public function update(array $obj, $id)
    {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $tglWaktuLaporArr = explode(" ", $obj['tgl_lapor']);
        $data_ikp = Util::copyIfNotEmpty(['no_rawat', 'kode_insiden', 'nip', 'lokasi', 
            'unit_terkait', 'akibat', 'tindakan_insiden', 'identifikasi_masalah', 'rtl'], $obj);
        $data_ikp['tgl_kejadian'] = $tglWaktuArr[0];
        $data_ikp['jam_kejadian'] = $tglWaktuArr[1];
        $data_ikp['tgl_lapor'] = $tglWaktuLaporArr[0];
        $data_ikp['jam_lapor'] = $tglWaktuLaporArr[1];
        $data_sikat = Util::copyIfNotEmpty(['no_rawat','pertama_melaporkan','unit_penyebab','kejadian_sebelumnya','pencegahan_terulang','nm_pembuat',
            'tgl_terima','nm_penerima', 'grading_risiko', 'jenis_insiden', 'nama_insiden', 'skor_dampak', 'tipe_insiden', 'subtipe_insiden', 'frekuensi_kejadian', 'tindakan_oleh'], $obj);
        $data_sikat['tgl_kejadian'] = $tglWaktuArr[0];
        $data_sikat['jam_kejadian'] = $tglWaktuArr[1];
        $data_ikp = $this->db->where(['tgl_kejadian' => $tglWaktuArr[0], 'jam_kejadian' => $tglWaktuArr[1], 'no_rawat' => $data_ikp['no_rawat']])->update('insiden_keselamatan_pasien', $data_ikp);
        $data_sikat = $this->db->replace('sikat_ikp', $data_sikat);
        return $data_ikp;
    }

    public function get($id) {
        $ids = explode(";",$id);
        $tglWaktuArr = explode(" ", $ids[0]);
        $this->db
        ->select('insiden.*, ikp.*, petugas.nama')
        ->from('insiden_keselamatan_pasien as insiden')
        ->join('sikat_ikp as ikp', 'ikp.tgl_kejadian=insiden.tgl_kejadian and ikp.jam_kejadian=insiden.jam_kejadian and ikp.no_rawat=insiden.no_rawat', 'left')
        ->join('petugas as petugas', 'petugas.nip=insiden.nip', 'left')
        ->where(['insiden.tgl_kejadian' => $tglWaktuArr[0], 'insiden.jam_kejadian' => $tglWaktuArr[1], 'insiden.no_rawat' => $ids[1]]);
        return $this->db->get()->row();
    }

    public function allPetugas()
    {
        return $this->db->get('petugas')->result();
    }

    public function allPetugasByQuery($searchStr)
    {
        $this->db
        ->select('petugas.nip, petugas.nama', false)
        ->from('petugas as petugas');
        if(isset($searchStr)) {
            $this->db->like('petugas.nip',$searchStr); 
            $this->db->or_like('petugas.nama',$searchStr);
        }
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }

    public function getByQuery($isRanap, $noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $settings = $this->db->from('sikat_settings')->get()->row();
        $this->db
        ->select('reg.no_rkm_medis, reg.no_rawat, reg.tgl_registrasi, reg.status_lanjut, pasien.nm_pasien, reg.kd_dokter, dokter.nm_dokter, kamar.kd_kamar, bangsal.nm_bangsal, pasien.umur, pasien.jk as jenis_kelamin, penjab.png_jawab as cara_bayar, GROUP_CONCAT(CONCAT(ikp.tgl_kejadian, " ", ikp.jam_kejadian)) as ikp_tanggal_list, count(ikp.tgl_kejadian) as jumlah_ikp', false)
        ->from('reg_periksa as reg')
        ->join('penjab as penjab', 'reg.kd_pj=penjab.kd_pj', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "reg.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->join('insiden_keselamatan_pasien as ikp', 'ikp.no_rawat=reg.no_rawat', 'left')
        ->join('sikat_ikp as sikatikp', 'sikatikp.tgl_kejadian=ikp.tgl_kejadian and sikatikp.jam_kejadian=ikp.jam_kejadian and sikatikp.no_rawat=ikp.no_rawat', 'left')
        ->group_by("reg.no_rawat")
        ->order_by('reg.tgl_registrasi', 'DESC');
        if (!isset($noRawat) && !isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($isRanap) && $isRanap == true) {
            if($isRanap == 'true') $this->db->where('kamar.kd_kamar is NOT NULL', NULL, FALSE);
            else $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
        }
        $this->db->where('(sikatikp.created_date > CURDATE()-INTERVAL ' . $settings->waktu_sembunyi_ikp . ' DAY or sikatikp.created_date is null)');
        if(isset($tanggalDari)) $this->db->where('reg.tgl_registrasi >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('reg.tgl_registrasi <=',$tanggalSampai);
        if(isset($noRawat)) $this->db->like('reg.no_rawat',$noRawat);
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        
        return $this->db->get()->result_array();
    }

    public function reportHarianByQuery($noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('CONCAT(sikat_jenis_insiden.nama, " - ", sikat_jenis_insiden.deskripsi) as jenis_insiden_d, sikat_nama_insiden.nama as nama_insiden_d, sikat_skor_dampak.nama as skor_dampak_d, sikat_frekuensi_kejadian.nama as frekuensi_kejadian_d, sikat_tindakan_oleh.nama as tindakan_oleh_d, reg.no_rkm_medis, reg.tgl_registrasi, CONCAT(petugas.nip, " - ", petugas.nama) as petugas, ikp.*, sikatikp.*, pasien.nm_pasien, reg.kd_dokter, dokter.nm_dokter, kamar.kd_kamar, bangsal.nm_bangsal, pasien.umur, pasien.jk as jenis_kelamin, penjab.png_jawab as cara_bayar ', false)
        ->from('insiden_keselamatan_pasien as ikp')
        ->join('sikat_ikp as sikatikp', 'sikatikp.tgl_kejadian=ikp.tgl_kejadian and sikatikp.jam_kejadian=ikp.jam_kejadian and sikatikp.no_rawat=ikp.no_rawat', 'left')
        ->join('reg_periksa as reg', 'ikp.no_rawat=reg.no_rawat', 'left')
        ->join('penjab as penjab', 'reg.kd_pj=penjab.kd_pj', 'left')
        ->join('petugas as petugas', 'ikp.nip=petugas.nip', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "reg.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->join('sikat_jenis_insiden', 'sikatikp.jenis_insiden=sikat_jenis_insiden.id', 'left')
        ->join('sikat_nama_insiden', 'sikatikp.nama_insiden=sikat_nama_insiden.id', 'left')
        ->join('sikat_skor_dampak', 'sikatikp.skor_dampak=sikat_skor_dampak.id', 'left')
        ->join('sikat_frekuensi_kejadian', 'sikatikp.frekuensi_kejadian=sikat_frekuensi_kejadian.id', 'left')
        ->join('sikat_tindakan_oleh', 'sikatikp.tindakan_oleh=sikat_tindakan_oleh.id', 'left')
        ->group_by(array("ikp.no_rawat", "ikp.tgl_kejadian", "ikp.jam_kejadian"))
        ->order_by('ikp.tgl_kejadian', 'DESC');
        if (!isset($noRawat) && !isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($noRawat)) $this->db->like('ikp.no_rawat',$noRawat);
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($tanggalDari)) $this->db->where('ikp.tgl_kejadian >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('ikp.tgl_kejadian <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

}




