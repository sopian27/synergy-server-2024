<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ppi_Model extends CI_Model
{
    public function create(array $obj)
    {
        $id = $obj['tanggal'] . ';' . $obj['no_rawat'];
        $data_hais = Util::copyIfNotEmpty(['tanggal','no_rawat', 'ett;ETT', 'cvl;CVL', 'ivl;IVL', 'uc;UC', 'vap;VAP', 
         'iad;IAD', 'pleb;PLEB', 'isk;ISK', 'ilo;ILO', 'hap;HAP', 'tinea;Tinea', 'scabies;Scabies', 'deku;DEKU', 
         'sputum;SPUTUM', 'darah;DARAH', 'urine;URINE', 'antibiotik;ANTIBIOTIK', 'kd_kamar'], $obj);
        $data_ppi = Util::copyIfNotEmpty(['tanggal','no_rawat','tgl_sampel','tgl_kirim','tgl_hasil','mdr',
            'difteri;DIFTERI','konsentrat;KONSENTRAT'], $obj);
        $this->db->insert('data_HAIs', $data_hais);
        $this->db->insert('sikat_ppi', $data_ppi);
        return $id;
    }

    public function delete($id)
    {
        $ids = explode(";",$id);
        $data_hais = $this->db->where(['tanggal' => $ids[0], 'no_rawat' => $ids[1]])->delete('data_HAIs');
        $data_ppi = $this->db->where(['tanggal' => $ids[0], 'no_rawat' => $ids[1]])->delete('sikat_ppi');
        return $data_hais;
    }

    public function update(array $obj, $id)
    {
        $ids = explode(";",$id);
        $searchArr = ['tanggal' => $ids[0], 'no_rawat' => $ids[1]];
        $data_hais = Util::copyIfNotEmpty(['ett;ETT', 'cvl;CVL', 'ivl;IVL', 'uc;UC', 'vap;VAP', 
        'iad;IAD', 'pleb;PLEB', 'isk;ISK', 'ilo;ILO', 'hap;HAP', 'tinea;Tinea', 'scabies;Scabies', 'deku;DEKU', 
        'sputum;SPUTUM', 'darah;DARAH', 'urine;URINE', 'antibiotik;ANTIBIOTIK', 'kd_kamar'], $obj);
        $data_hais['tanggal'] = $ids[0];
        $data_hais['no_rawat'] = $ids[1];
        $data_ppi = Util::copyIfNotEmpty(['tgl_sampel','tgl_kirim','tgl_hasil','mdr','difteri;DIFTERI','konsentrat;KONSENTRAT'], $obj);
        $data_ppi['tanggal'] = $ids[0];
        $data_ppi['no_rawat'] = $ids[1];
        $data_hais = $this->db->replace('data_HAIs', $data_hais);
        $data_ppi = $this->db->replace('sikat_ppi', $data_ppi);
        return $data_hais;
    }

    public function get($id) {
        $ids = explode(";",$id);
        $this->db
        ->select('*')
        ->from('data_HAIs as hais')
        ->join('sikat_ppi as ppi', 'ppi.tanggal=hais.tanggal and ppi.no_rawat=hais.no_rawat', 'left')
        ->where(['hais.tanggal' => $ids[0], 'hais.no_rawat' => $ids[1]]);
        return $this->db->get()->row();
    }

    public function all()
    {
        $this->db
        ->select('*')
        ->from('data_HAIs as hais')
        ->join('sikat_ppi as ppi', 'ppi.tanggal=hais.tanggal and ppi.no_rawat=hais.no_rawat', 'left');
        return $this->db->get()->result();
    }

    public function getByQuery($isRanap, $noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('reg.no_rkm_medis, reg.no_rawat, reg.tgl_registrasi, reg.status_lanjut, pasien.nm_pasien, reg.kd_dokter, dokter.nm_dokter, kamar.kd_kamar, GROUP_CONCAT(ppi.tanggal) as ppi_tanggal_list, count(ppi.tanggal) as jumlah_ppi', false)
        ->from('reg_periksa as reg')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "reg.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('data_HAIs as ppi', 'ppi.no_rawat=reg.no_rawat', 'left')
        ->group_by("reg.no_rawat")
        ->order_by('reg.tgl_registrasi', 'DESC');
        if (!isset($noRawat) && !isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($isRanap) && $isRanap == true) {
            if($isRanap == 'true') 
                $this->db
                ->where('kamar.kd_kamar is NOT NULL', NULL, TRUE); 
                // ->where('kamar.kd_kamar = ppi.kd_kamar');
            else $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
        }
        if(isset($noRawat)) $this->db->like('reg.no_rawat',$noRawat);
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($tanggalDari)) $this->db->where('reg.tgl_registrasi >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('reg.tgl_registrasi <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

    public function reportHarianByQuery($isRanap, $kodeKamar, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('reg.no_rkm_medis, hais.*, ppi.MDR, ppi.DIFTERI, ppi.KONSENTRAT, pasien.nm_pasien, reg.kd_dokter, dokter.nm_dokter, kamar.kd_kamar, bangsal.nm_bangsal', false)
        ->from('data_HAIs as hais')
        ->join('sikat_ppi as ppi', 'ppi.tanggal=hais.tanggal and ppi.no_rawat=hais.no_rawat', 'left')
        ->join('reg_periksa as reg', 'hais.no_rawat=reg.no_rawat', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "ppi.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->group_by("hais.no_rawat")
        ->order_by('hais.tanggal', 'DESC');
        if (!isset($kodeKamar) && !isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($isRanap) && $isRanap == true) {
            if($isRanap == 'true') $this->db->where('kamar.kd_kamar is NOT NULL', NULL, FALSE);
            else $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
            if(isset($kodeKamar)) $this->db->like('hais.kd_kamar',$kodeKamar);
        } else {
            $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
        }
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($tanggalDari)) $this->db->where('hais.tanggal >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('hais.tanggal <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

    public function reportBulananByQuery($isRanap, $kodeKamar, $noRekamMedis, $namaPasien, $namaDokter, $bulan, $tahun) {
        $this->db
        ->select('hais.tanggal, count(hais.no_rawat) as jml_pasien, sum(hais.ETT) as jml_ETT, sum(hais.CVL) as jml_CVL, sum(hais.IVL) as jml_IVL, sum(hais.UC) as jml_UC, sum(hais.VAP) as jml_VAP, sum(hais.IAD) as jml_IAD, sum(hais.PLEB) as jml_PLEB, sum(hais.ISK) as jml_ISK, sum(hais.ILO) as jml_ILO, sum(hais.HAP) as jml_HAP, sum(hais.Tinea) as jml_Tinea, sum(hais.Scabies) as jml_Scabies, sum(case hais.DEKU when \'YA\' then 1 when \'TIDAK\' then 0 end) as jml_DEKU, count(hais.SPUTUM) as jml_SPUTUM, count(hais.DARAH) as jml_DARAH, count(hais.URINE) as jml_URINE, count(hais.ANTIBIOTIK) as jml_ANTIBIOTIK, sum(ppi.MDR) as jml_MDR, sum(ppi.DIFTERI) as jml_DIFTERI, count(ppi.KONSENTRAT) as jml_KONSENTRAT', false)
        ->from('data_HAIs as hais')
        ->join('sikat_ppi as ppi', 'ppi.tanggal=hais.tanggal and ppi.no_rawat=hais.no_rawat', 'left')
        ->join('reg_periksa as reg', 'hais.no_rawat=reg.no_rawat', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "ppi.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->group_by("hais.tanggal")
        ->order_by('hais.tanggal', 'ASC');
        if (!isset($kodeKamar) && !isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($bulan) && !isset($tahun)) {
            $this->db->limit(250);
        }
        if(isset($isRanap) && $isRanap == true) {
            if($isRanap == 'true') $this->db->where('kamar.kd_kamar is NOT NULL', NULL, FALSE);
            else $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
            if(isset($kodeKamar)) $this->db->like('hais.kd_kamar',$kodeKamar);
        } else {
            $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
        }
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($bulan) && isset($tahun)) {
            $this->db->where("hais.tanggal >= '" . $tahun . "-" . str_pad($bulan, 2, "0", STR_PAD_LEFT) . "-01'", NULL, FALSE);
            $this->db->where("hais.tanggal <= LAST_DAY('" . $tahun . "-" . str_pad($bulan, 2, "0", STR_PAD_LEFT) . "-01')" , NULL, FALSE);
        }
        return $this->db->get()->result_array();
    }

    public function reportKamarByQuery($isRanap, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai) {
        $this->db
        ->select('kamar.kd_kamar, bangsal.nm_bangsal, count(hais.no_rawat) as jml_pasien, sum(hais.ETT) as jml_ETT, sum(hais.CVL) as jml_CVL, sum(hais.IVL) as jml_IVL, sum(hais.UC) as jml_UC, sum(hais.VAP) as jml_VAP, sum(hais.IAD) as jml_IAD, sum(hais.PLEB) as jml_PLEB, sum(hais.ISK) as jml_ISK, sum(hais.ILO) as jml_ILO, sum(hais.HAP) as jml_HAP, sum(hais.Tinea) as jml_Tinea, sum(hais.Scabies) as jml_Scabies, sum(case hais.DEKU when \'YA\' then 1 when \'TIDAK\' then 0 end) as jml_DEKU, count(hais.SPUTUM) as jml_SPUTUM, count(hais.DARAH) as jml_DARAH, count(hais.URINE) as jml_URINE, count(hais.ANTIBIOTIK) as jml_ANTIBIOTIK, sum(ppi.MDR) as jml_MDR, sum(ppi.DIFTERI) as jml_DIFTERI, count(ppi.KONSENTRAT) as jml_KONSENTRAT', false)
        ->from('data_HAIs as hais')
        ->join('sikat_ppi as ppi', 'ppi.tanggal=hais.tanggal and ppi.no_rawat=hais.no_rawat', 'left')
        ->join('reg_periksa as reg', 'hais.no_rawat=reg.no_rawat', 'left')
        ->join('pasien as pasien', 'reg.no_rkm_medis=pasien.no_rkm_medis', 'left')
        ->join('dokter as dokter', 'reg.kd_dokter=dokter.kd_dokter', 'left')
        ->join('kamar_inap as kamar', "hais.no_rawat=kamar.no_rawat and kamar.stts_pulang <> 'Pindah Kamar'", 'left')
        ->join('kamar as kamar_unit', 'kamar.kd_kamar=kamar_unit.kd_kamar', 'left')
        ->join('bangsal as bangsal', 'kamar_unit.kd_bangsal=bangsal.kd_bangsal', 'left')
        ->group_by("kamar.kd_kamar")
        ->order_by('kamar.kd_kamar', 'DESC');
        if (!isset($noRekamMedis) && !isset($namaPasien) && !isset($namaDokter) && !isset($tanggalDari) && !isset($tanggalSampai)) {
            $this->db->limit(250);
        }
        if(isset($isRanap) && $isRanap == true) {
            if($isRanap == 'true') $this->db->where('kamar.kd_kamar is NOT NULL', NULL, FALSE);
            else $this->db->where('kamar.kd_kamar is NULL', NULL, FALSE);
        }
        if(isset($noRekamMedis)) $this->db->like('reg.no_rkm_medis',$noRekamMedis);
        if(isset($namaPasien)) $this->db->like('pasien.nm_pasien',$namaPasien);
        if(isset($namaDokter)) $this->db->like('dokter.nm_dokter',$namaDokter);
        if(isset($tanggalDari)) $this->db->where('hais.tanggal >=',$tanggalDari);
        if(isset($tanggalSampai)) $this->db->where('hais.tanggal <=',$tanggalSampai);
        return $this->db->get()->result_array();
    }

}




