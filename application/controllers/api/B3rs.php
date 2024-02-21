<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class B3rs extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('b3rs_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $b3rs = array();
        if($id != null) {
            $b3rs =  $this->b3rs_model->get($id);
        }else{
            $b3rs = $this->b3rs_model->all();
        }
        $this->set_response($b3rs, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $jenisBahan = $this->get('jenisBahan');
        $fasa = $this->get('fasa');
        $lokasi = $this->get('lokasi');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $b3rs = array();
        $b3rs =  $this->b3rs_model->getByQuery($jenisBahan, $fasa, $lokasi, $tanggalDari,$tanggalSampai);
        $this->set_response($b3rs, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->b3rs_model->create($dataPost);
        if($id !== FALSE) {
            $b3rs = $this->b3rs_model->get($id);
            $this->set_response($b3rs, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create b3rs failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put() {
        $dataPut = $this->put();
        $id = $dataPut['id'];
        if($id) {
            $result = $this->b3rs_model->update($dataPut, $id);
            if($result) {
                $b3rs = $this->b3rs_model->get($id);
                $this->set_response($b3rs, REST_Controller::HTTP_OK);
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
            $result = $this->b3rs_model->delete($id);
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


