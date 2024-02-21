<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class K3rsLapor extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('k3rsLapor_model');
        $this->load->model('settings_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $k3rsLapor = array();
        if($id != null) {
            $k3rsLapor =  $this->k3rsLapor_model->get($id);
        }else{
            $k3rsLapor = $this->k3rsLapor_model->all();
        }
        $this->set_response($k3rsLapor, REST_Controller::HTTP_OK);
    }

    public function allPasienByQuery_get() {
        $searchStr = $this->get('searchstr');
        $pasien = $this->k3rsLapor_model->allPasienByQuery($searchStr);
        $this->set_response($pasien, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $noRkmMedis = $this->get('noRkmMedis');
        $lokasi = $this->get('lokasi');
        $pekerjaan = $this->get('pekerjaan');
        $namaPasien = $this->get('namaPasien');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $k3rsLapor = array();
        $k3rsLapor =  $this->k3rsLapor_model->getByQuery($noRkmMedis, $lokasi, $pekerjaan, $namaPasien,$tanggalDari,$tanggalSampai);
        $this->set_response($k3rsLapor, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->k3rsLapor_model->create($dataPost);
        if($id !== FALSE) {
            $k3rsLapor = $this->k3rsLapor_model->get($id);
            $settings =  $this->settings_model->get();
            $data = json_decode(json_encode($k3rsLapor), true);
            $data['to_email'] = $settings->notif_email_k3rs;
            $data['to_subject'] = 'Notifikasi Pelaporan K3RS';
            $data['template'] = 'k3rs_create';
            Util::curlAsync("https://rsudsawahbesar.jakarta.go.id/synergy-server-2022/email", $data);
            $this->set_response($k3rsLapor, REST_Controller::HTTP_OK);
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
            $result = $this->k3rsLapor_model->update($dataPut, $id);
            if($result) {
                $k3rsLapor = $this->k3rsLapor_model->get($id);
                $this->set_response($k3rsLapor, REST_Controller::HTTP_OK);
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
            $result = $this->k3rsLapor_model->delete($id);
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


