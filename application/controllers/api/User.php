<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class User extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $table = array();
        if($id != null) {
            $table =  $this->user_model->get($id);
        }else{
            $table = $this->user_model->all();
        }
        $this->set_response($table, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->user_model->create($dataPost);
        if($id !== FALSE) {
            $table = $this->user_model->get($id);
            $this->set_response($table, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create user failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put($id = null) {
        $dataPut = $this->put();
        if($id) {
            $result = $this->user_model->update($dataPut, $id);
            if($result) {
                $table = $this->user_model->get($id);
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
            $result = $this->user_model->delete($id);
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


