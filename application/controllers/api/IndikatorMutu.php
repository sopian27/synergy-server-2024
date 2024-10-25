<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class IndikatorMutu extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('indikatorMutu_model');
        $this->load->model('settings_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $indikatorMutu = array();
        if($id != null) {
            $indikatorMutu =  $this->indikatorMutu_model->get($id);
        }else{
            $indikatorMutu = $this->indikatorMutu_model->all();
        }
        $this->set_response($indikatorMutu, REST_Controller::HTTP_OK);
    }

    public function reportHarianByQuery_get() {
        $noRawat = $this->get('noRawat');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $indikatorMutu = array();
        $indikatorMutu =  $this->indikatorMutu_model->reportHarianByQuery($noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai);
        $this->set_response($indikatorMutu, REST_Controller::HTTP_OK);
    }

    public function allPetugas_get() {
        $petugas = $this->indikatorMutu_model->allPetugas();
        $this->set_response($petugas, REST_Controller::HTTP_OK);
    }

    public function allPetugasByQuery_get() {
        $searchStr = $this->get('searchstr');
        $petugas = $this->indikatorMutu_model->allPetugasByQuery($searchStr);
        $this->set_response($petugas, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $isRanap = $this->get('isRanap');
        $noRawat = $this->get('noRawat');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $indikatorMutu = array();
        $indikatorMutu =  $this->indikatorMutu_model->getByQuery($isRanap, $noRawat, $noRekamMedis, $namaPasien, $namaDokter,$tanggalDari,$tanggalSampai);
        $this->set_response($indikatorMutu, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->indikatorMutu_model->create($dataPost);
        if($id !== FALSE) {
            $indikatorMutu = $this->indikatorMutu_model->get($id);
            $settings =  $this->settings_model->get();
            $data = json_decode(json_encode($indikatorMutu), true);
            $data['to_email'] = $settings->notif_email_ikp;
            $data['to_subject'] = 'Notifikasi Pelaporan IKP';
            $data['template'] = 'ikp_create';
            Util::curlAsync("https://rsudsawahbesar.jakarta.go.id/synergy-server-2024/email", $data);
            $this->set_response($indikatorMutu, REST_Controller::HTTP_OK);
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
            $result = $this->indikatorMutu_model->update($dataPut, $id);
            if($result) {
                $indikatorMutu = $this->indikatorMutu_model->get($id);
                $this->set_response($indikatorMutu, REST_Controller::HTTP_OK);
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
            $result = $this->indikatorMutu_model->delete($id);
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


