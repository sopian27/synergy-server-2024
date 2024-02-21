<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class K3rsInves extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('k3rsInves_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $k3rsInves = array();
        if($id != null) {
            $k3rsInves =  $this->k3rsInves_model->get($id);
        }else{
            $k3rsInves = $this->k3rsInves_model->all();
        }
        $this->set_response($k3rsInves, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $noRkmMedis = $this->get('noRkmMedis');
        $lokasi = $this->get('lokasi');
        $pekerjaan = $this->get('pekerjaan');
        $namaPasien = $this->get('namaPasien');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $k3rsInves = array();
        $k3rsInves =  $this->k3rsInves_model->getByQuery($noRkmMedis, $lokasi, $pekerjaan, $namaPasien,$tanggalDari,$tanggalSampai);
        $this->set_response($k3rsInves, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->k3rsInves_model->create($dataPost);
        if($id !== FALSE) {
            $k3rsInves = $this->k3rsInves_model->get($id);
            $this->set_response($k3rsInves, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create k3rs failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put() {
        $dataPut = $this->put();
        $id = $dataPut['id'];
        if($id) {
            $result = $this->k3rsInves_model->update($dataPut, $id);
            if($result) {
                $k3rsInves = $this->k3rsInves_model->get($id);
                $this->set_response($k3rsInves, REST_Controller::HTTP_OK);
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
            $result = $this->k3rsInves_model->delete($id);
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


