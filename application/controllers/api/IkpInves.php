<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class IkpInves extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ikpInves_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $ikpInves = array();
        if($id != null) {
            $ikpInves =  $this->ikpInves_model->get($id);
        }else{
            $ikpInves = $this->ikpInves_model->all();
        }
        $this->set_response($ikpInves, REST_Controller::HTTP_OK);
    }

    public function reportHarianByQuery_get() {
        $noRawat = $this->get('noRawat');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ikpInves = array();
        $ikpInves =  $this->ikpInves_model->reportHarianByQuery($noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai);
        $this->set_response($ikpInves, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $isRanap = $this->get('isRanap');
        $noRawat = $this->get('noRawat');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ikpInves = array();
        $ikpInves =  $this->ikpInves_model->getByQuery($isRanap, $noRawat, $namaPasien, $namaDokter,$tanggalDari,$tanggalSampai);
        $this->set_response($ikpInves, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->ikpInves_model->create($dataPost);
        if($id !== FALSE) {
            $ikpInves = $this->ikpInves_model->get($id);
            $this->set_response($ikpInves, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create ikp failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put() {
        $dataPut = $this->put();
        $id = $dataPut['id'];
        if($id) {
            $result = $this->ikpInves_model->update($dataPut, $id);
            if($result) {
                $ikpInves = $this->ikpInves_model->get($id);
                $this->set_response($ikpInves, REST_Controller::HTTP_OK);
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
            $result = $this->ikpInves_model->delete($id);
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


