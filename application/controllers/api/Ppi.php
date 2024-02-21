<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Ppi extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('ppi_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $ppi = array();
        if($id != null) {
            $ppi =  $this->ppi_model->get($id);
        }else{
            $ppi = $this->ppi_model->all();
        }
        $this->set_response($ppi, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $isRanap = $this->get('isRanap');
        $noRawat = $this->get('noRawat');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ppi = array();
        $ppi =  $this->ppi_model->getByQuery($isRanap, $noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai);
        $this->set_response($ppi, REST_Controller::HTTP_OK);
    }

    public function reportHarianByQuery_get() {
        $isRanap = $this->get('isRanap');
        $kodeKamar = $this->get('kodeKamar');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ppi = array();
        $ppi =  $this->ppi_model->reportHarianByQuery($isRanap, $kodeKamar, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai);
        $this->set_response($ppi, REST_Controller::HTTP_OK);
    }

    public function reportBulananByQuery_get() {
        $isRanap = $this->get('isRanap');
        $kodeKamar = $this->get('kodeKamar');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $bulan = $this->get('bulan');
        $tahun = $this->get('tahun');
        $ppi = array();
        $ppi =  $this->ppi_model->reportBulananByQuery($isRanap, $kodeKamar, $noRekamMedis, $namaPasien, $namaDokter, $bulan, $tahun);
        $this->set_response($ppi, REST_Controller::HTTP_OK);
    }

    public function reportKamarByQuery_get() {
        $isRanap = $this->get('isRanap');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ppi = array();
        $ppi =  $this->ppi_model->reportKamarByQuery($isRanap, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai);
        $this->set_response($ppi, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->ppi_model->create($dataPost);
        if($id !== FALSE) {
            $ppi = $this->ppi_model->get($id);
            $this->set_response($ppi, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create ppi failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put() {
        $dataPut = $this->put();
        $id = $dataPut['id'];
        if($id) {
            $result = $this->ppi_model->update($dataPut, $id);
            if($result) {
                $ppi = $this->ppi_model->get($id);
                $this->set_response($ppi, REST_Controller::HTTP_OK);
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

    public function delete_get() {
        $id = $this->get('id');
        if($id) {
            $result = $this->ppi_model->delete($id);
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


