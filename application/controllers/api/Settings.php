<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Settings extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('settings_model');
    }

    public function index_get() {
        $settings = array();
        $settings =  $this->settings_model->get();
        $this->set_response($settings, REST_Controller::HTTP_OK);
    }

    public function index_put() {
        $dataPut = $this->put();
        $result = $this->settings_model->update($dataPut);
        if($result) {
            $settings = $this->settings_model->get();
            $this->set_response($settings, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'database error',
            ];
            $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

}


