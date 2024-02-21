<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Table extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('table_model');
    }

    public function allDokter_get() {
        $dokter = $this->table_model->allDokter();
        $this->set_response($dokter, REST_Controller::HTTP_OK);
    }

    public function allUnit_get() {
        $unit = $this->table_model->allUnit();
        $this->set_response($unit, REST_Controller::HTTP_OK);
    }

    public function allRuang_get() {
        $ruang = $this->table_model->allRuang();
        $this->set_response($ruang, REST_Controller::HTTP_OK);
    }

    public function index_get() {
        $id = $this->get('id');
        $table = array();
        if($id != null) {
            $table =  $this->table_model->get($id);
        }else{
            $table = $this->table_model->all();
        }
        $this->set_response($table, REST_Controller::HTTP_OK);
    }

    public function show_get() {
        $id = $this->get('id');
        $minggu = $this->get('minggu');
        $bulan = $this->get('bulan');
        $tahun = $this->get('tahun');
        $unit = $this->get('unit');
        $dokter = $this->get('dokter');
        $ruang = $this->get('ruang');
        $umumBpjs = $this->get('umumBpjs');
        $startDate = $this->get('startDate');
        $endDate = $this->get('endDate');
        $table = array();
        if($id != null) {
            $table =  $this->table_model->show($id, $minggu, $bulan, $tahun, $unit, $dokter, $ruang, $umumBpjs, $startDate, $endDate);
        }
        $this->set_response($table, REST_Controller::HTTP_OK);
    }

    public function view_get() {
        $id = $this->get('id');
        $startDate = $this->get('tglStart');
        $endDate = $this->get('tglEnd');
        $table = array();
        if($id != null) {
            $table =  $this->table_model->view($id, $startDate, $endDate);
        }
        $this->set_response($table, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->table_model->create($dataPost);
        if($id !== FALSE) {
            $table = $this->table_model->get($id);
            $this->set_response($table, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create table failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put($id = null) {
        $dataPut = $this->put();
        if($id) {
            $result = $this->table_model->update($dataPut, $id);
            if($result) {
                $table = $this->table_model->get($id);
                $this->set_response($table, REST_Controller::HTTP_OK);
            }else{
                $response = [
                    'status' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'database error',
                ];
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
            
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'param ID can\'t be null',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        
    }

    public function index_delete($id = null) {
        if($id) {
            $result = $this->table_model->delete($id);
            if($result) {
                $this->set_response('deleted', REST_Controller::HTTP_OK);
            }else{
                $response = [
                    'status' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'database error',
                ];
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'param ID can\'t be null',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}


