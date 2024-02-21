<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Table_Model extends CI_Model
{

    public function allDokter()
    {
        return $this->db->get('dokter')->result();
    }

    public function allUnit()
    {
        return $this->db->get('poliklinik')->result();
    }

    public function allRuang()
    {
        return $this->db->get('bangsal')->result();
    }

    public function create(array $obj)
    {
        $data = Util::copyIfNotEmpty(['nm_menu','nm_table',
        'is_tahun',
        'is_bulan',
        'is_minggu',
        'is_unit',
        'is_dokter',
        'is_ruang',
        'is_umum_bpjs',
        'is_start_date',
        'is_end_date',
        'span_header1',
        'span_header2',
        'column_title',
        'column_key',
        'query'], $obj);
        $this->db->insert('sikat_table', $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('sikat_table');
    }

    public function update(array $obj, $id)
    {
        $data = Util::copyIfNotEmpty(['nm_menu','nm_table',
        'is_tahun',
        'is_bulan',
        'is_minggu',
        'is_unit',
        'is_dokter',
        'is_ruang',
        'is_umum_bpjs',
        'is_start_date',
        'is_end_date',
        'span_header1',
        'span_header2',
        'column_title',
        'column_key',
        'query'], $obj);
        $data['id'] = $id;
        return $this->db->replace('sikat_table', $data);
    }

    public function get($id) {
        return $this->db->get_where('sikat_table', ['id' => $id])->row();
    }

    public function show($id, $minggu, $bulan, $tahun, $unit, $dokter, $ruang, $umumBpjs, $startDate, $endDate) {
        $tableMeta = $this->db->get_where('sikat_table', ['id' => $id])->row();
        $queryStr = $tableMeta->query;
        if(isset($minggu)) {
            $queryStr = str_replace('#MINGGU#', $minggu, $queryStr);
        } else {
            $queryStr = str_replace('#MINGGU#', "", $queryStr);
        }
        if(isset($bulan)) {
            $queryStr = str_replace('#BULAN#', $bulan, $queryStr);
        } else {
            $queryStr = str_replace('#BULAN#', "", $queryStr);
        }
        if(isset($tahun)) {
            $queryStr = str_replace('#TAHUN#', $tahun, $queryStr);
        } else {
            $queryStr = str_replace('#TAHUN#', "", $queryStr);
        }
        if(isset($unit)) {
            $queryStr = str_replace('#UNIT#', $unit, $queryStr);
        } else {
            $queryStr = str_replace('#UNIT#', "", $queryStr);
        }
        if(isset($dokter)) {
            $queryStr = str_replace('#DOKTER#', $dokter, $queryStr);
        } else {
            $queryStr = str_replace('#DOKTER#', "", $queryStr);
        }
        if(isset($ruang)) {
            $queryStr = str_replace('#RUANG#', $ruang, $queryStr);
        } else {
            $queryStr = str_replace('#RUANG#', "", $queryStr);
        }
        if(isset($umumBpjs)) {
            $queryStr = str_replace('#UMUM_BPJS#', $umumBpjs, $queryStr);
        } else {
            $queryStr = str_replace('#UMUM_BPJS#', "", $queryStr);
        }
        if(isset($startDate)) {
            $queryStr = str_replace('#START_DATE#', $startDate ,$queryStr);
        } else {
            $queryStr = str_replace('#START_DATE#',"CURDATE()",$queryStr);
        }
        if(isset($endDate)) {
            $queryStr = str_replace('#END_DATE#', $endDate ,$queryStr);
        } else {
            $queryStr = str_replace('#END_DATE#',"CURDATE()",$queryStr);
        }

        $query = $this->db->query($queryStr);
        return $query->result_array();
    }

    public function view($id, $startDate, $endDate) {
        $tableMeta = $this->db->get_where('sikat_table', ['id' => $id])->row();
        $queryStr = $tableMeta->query;
        if(isset($startDate)) {
            $queryStr = str_replace('#START_DATE#',"'" . $startDate . "'",$queryStr);
        } else {
            $queryStr = str_replace('#START_DATE#',"CURDATE()",$queryStr);
        }
        if(isset($endDate)) {
            $queryStr = str_replace('#END_DATE#',"'" . $endDate . "'",$queryStr);
        } else {
            $queryStr = str_replace('#END_DATE#',"CURDATE()",$queryStr);
        }
        $query = $this->db->query($queryStr);
        return $query->result_array();
    }

    public function all()
    {
        // return $this->db->get('sikat_table')->result();
        $query = $this->db
            ->select('*, SUBSTRING_INDEX(nm_menu, ":", -1) as menuidx', FALSE)
            ->from('sikat_table')
            ->order_by('menuidx')
            ->get();
        return $query->result();   
    }

}




