<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class IkpLapor extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ikpLapor_model');
        $this->load->model('settings_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $ikpLapor = array();
        if($id != null) {
            $ikpLapor =  $this->ikpLapor_model->get($id);
        }else{
            $ikpLapor = $this->ikpLapor_model->all();
        }
        $this->set_response($ikpLapor, REST_Controller::HTTP_OK);
    }

    public function reportHarianByQuery_get() {
        $noRawat = $this->get('noRawat');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ikpLapor = array();
        $ikpLapor =  $this->ikpLapor_model->reportHarianByQuery($noRawat, $noRekamMedis, $namaPasien, $namaDokter, $tanggalDari, $tanggalSampai);
        $this->set_response($ikpLapor, REST_Controller::HTTP_OK);
    }

    public function allJenisInsiden_get() {
        $jenisInsiden = $this->ikpLapor_model->allJenisInsiden();
        $this->set_response($jenisInsiden, REST_Controller::HTTP_OK);
    }

    public function allNamaInsiden_get() {
        $namaInsiden = $this->ikpLapor_model->allNamaInsiden();
        $this->set_response($namaInsiden, REST_Controller::HTTP_OK);
    }

    public function allSkorDampak_get() {
        $skorDampak = $this->ikpLapor_model->allSkorDampak();
        $this->set_response($skorDampak, REST_Controller::HTTP_OK);
    }

    public function allTipeInsiden_get() {
        $tipeInsiden = $this->ikpLapor_model->allTipeInsiden();
        $this->set_response($tipeInsiden, REST_Controller::HTTP_OK);
    }

    public function allSubtipeInsiden_get() {
        $subtipeInsiden = $this->ikpLapor_model->allSubtipeInsiden();
        $this->set_response($subtipeInsiden, REST_Controller::HTTP_OK);
    }

    public function allFrekuensiKejadian_get() {
        $frekuensiKejadian = $this->ikpLapor_model->allFrekuensiKejadian();
        $this->set_response($frekuensiKejadian, REST_Controller::HTTP_OK);
    }

    public function allTindakanOleh_get() {
        $tindakanOleh = $this->ikpLapor_model->allTindakanOleh();
        $this->set_response($tindakanOleh, REST_Controller::HTTP_OK);
    }

    public function allInsiden_get() {
        $insiden = $this->ikpLapor_model->allInsiden();
        $this->set_response($insiden, REST_Controller::HTTP_OK);
    }

    public function allPetugas_get() {
        $petugas = $this->ikpLapor_model->allPetugas();
        $this->set_response($petugas, REST_Controller::HTTP_OK);
    }

    public function allPetugasByQuery_get() {
        $searchStr = $this->get('searchstr');
        $petugas = $this->ikpLapor_model->allPetugasByQuery($searchStr);
        $this->set_response($petugas, REST_Controller::HTTP_OK);
    }

    public function allInsidenByQuery_get() {
        $searchStr = $this->get('searchstr');
        $insiden = $this->ikpLapor_model->allInsidenByQuery($searchStr);
        $this->set_response($insiden, REST_Controller::HTTP_OK);
    }

    public function getByQuery_get() {
        $isRanap = $this->get('isRanap');
        $noRawat = $this->get('noRawat');
        $noRekamMedis = $this->get('noRekamMedis');
        $namaPasien = $this->get('namaPasien');
        $namaDokter = $this->get('namaDokter');
        $tanggalDari = $this->get('tanggalDari');
        $tanggalSampai = $this->get('tanggalSampai');
        $ikpLapor = array();
        $ikpLapor =  $this->ikpLapor_model->getByQuery($isRanap, $noRawat, $noRekamMedis, $namaPasien, $namaDokter,$tanggalDari,$tanggalSampai);
        $this->set_response($ikpLapor, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->ikpLapor_model->create($dataPost);
        if($id !== FALSE) {
            $ikpLapor = $this->ikpLapor_model->get($id);
            $settings =  $this->settings_model->get();
            $data = json_decode(json_encode($ikpLapor), true);
            $data['to_email'] = $settings->notif_email_ikp;
            $data['to_subject'] = 'Notifikasi Pelaporan IKP';
            $data['template'] = 'ikp_create';
            Util::curlAsync("https://rsudsawahbesar.jakarta.go.id/synergy-server-2022/email", $data);
            $this->set_response($ikpLapor, REST_Controller::HTTP_OK);
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
            $result = $this->ikpLapor_model->update($dataPut, $id);
            if($result) {
                $ikpLapor = $this->ikpLapor_model->get($id);
                $this->set_response($ikpLapor, REST_Controller::HTTP_OK);
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
            $result = $this->ikpLapor_model->delete($id);
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


