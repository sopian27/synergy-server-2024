<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Email extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->config('email');
        $this->load->model('settings_model');
    }

    public function index_get() {
        $settings =  $this->settings_model->get();
        $param = $this->get();
        $table = $this->get();
        unset($table['to_email']);
        unset($table['to_subject']);
        unset($table['template']);
        $data = array();
        $data['param'] = $param;
        $data['table'] = $table;
        $data['from_email'] = 'admin@synergy';
        $data['from_name'] = 'RSUD SAWAH BESAR';
        $data['settings'] = $settings;
        $this->load->library('email');
        $this->email->from($data['from_email'], $data['from_name'])
            ->to($param['to_email'])
            ->subject($param['to_subject'])
            ->message($this->load->view('email_'.$param['template'], $data, true));
        $this->email->send(); 
        $arr = array('msg' => 'Something went wrong try again lator', 'success' =>false);
        if($this->email->send()){
            $arr = array('msg' => 'Mail has been sent successfully', 'success' =>true);
        }
        $this->set_response($data, REST_Controller::HTTP_OK);
    }
}


