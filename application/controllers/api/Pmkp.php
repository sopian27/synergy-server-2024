<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Pmkp extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('pmkp_model');
    }

    public function index_get() {
        $id = $this->get('id');
        $pmkp = array();
        if($id != null) {
            $pmkp =  $this->pmkp_model->get($id);
        }else{
            $pmkp = $this->pmkp_model->all();
        }
        $this->set_response($pmkp, REST_Controller::HTTP_OK);
    }

    public function getByYearAndMonthAndType_get() {
        $type = $this->get('type');
        $year = $this->get('year');
        $month = $this->get('month');
        $pmkp = array();
        if($year != null && $month != null) {
            $pmkp =  $this->pmkp_model->getByYearAndMonthAndType($year, $month, $type);
        }else{
            $pmkp = $this->pmkp_model->all();
        }
        $this->set_response($pmkp, REST_Controller::HTTP_OK);
    }

    public function getByYearAndType_get() {
        $year = $this->get('year');
        $type = $this->get('type');
        $pmkp = array();
        if($year != null) {
            $pmkp =  $this->pmkp_model->getByYearAndType($year, $type);
        }else{
            $pmkp = $this->pmkp_model->all();
        }
        $this->set_response($pmkp, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->pmkp_model->create($dataPost);
        if($id !== FALSE) {
            $pmkp = $this->pmkp_model->get($id);
            
            // insert to SISMADAK
            /*if ($pmkp->year > 2019 || ($pmkp->year == 2019 && $pmkp->month >= 9)) {
                $dayInMonth = cal_days_in_month(CAL_GREGORIAN, $pmkp->month, $pmkp->year); // because some data is not having form A so we directly put only tgl 1 of each month
                // array of array(<indicator_id>,<pmkp_type>,<formB_idx_which_start_from_1>, 0=normal,1=only form B, 2=only hasil,<formA_idx_which_start_from_1> )
                $mappingList = array(
                    array(90, 'timKPRS', 1, 0, 1),
                    array(91, 'igd', 5, 0, 3),
                    array(92, 'rawatJalan', 4, 0, 3),
                    array(93, 'kamarOperasi', 12, 0, 23),
                    array(94, 'rawatInap', 4, 0, 1),
                    array(95, 'laboratorium', 7, 0, 19),
                    array(96, 'farmasi', 5, 1, 0),
                    // array(97, 'timKPRS', 1),
                    array(98, 'ppi', 12, 0, 19),
                    array(99, 'timKPRS', 8, 1, 0),
                    array(100, 'komiteMedis', 1, 2, 0),
                    array(101, 'timKomplain', 1, 1, 0),
                    array(102, 'timKomplain', 2, 1, 0)
                );
                $departmentId = 51;
                $rowCount = 0;
                $query = array();
                foreach ($mappingList as $mapping) {
                    $dataForType =  $this->pmkp_model->getByYearAndMonthAndType((int)$pmkp->year, (int)$pmkp->month, $mapping[1]);
                    if ($dataForType != NULL) {
                        $monthlyData = json_decode($dataForType->monthlyData, true);
                        $dailyData = json_decode($dataForType->dailyData, true);
                        if ((int)$mapping[2] < count($monthlyData)) {
                            $numerator = $monthlyData[(int)$mapping[2]-1]['numerator'];
                            $denumerator = $monthlyData[(int)$mapping[2]-1]['denumerator'];
                            $hasil = $monthlyData[(int)$mapping[2]-1]['hasil'];
                            if (count($mapping) >= 6) {
                                $processedNumAndDenum = $mapping[5]($numerator, $denumerator, $hasil);
                                $numerator = $processedNumAndDenum[0];
                                $denumerator = $processedNumAndDenum[1];
                            }
                            $rowCount++;
                            if ($mapping[3] == 2) {
                                $numerator = $hasil;
                                $denumerator = $hasil;
                            }
                            array_push($query, "UPDATE `hospital_survey_indicator_result_recapitulation` SET `result_numerator_value` = '" . $numerator . "', `result_denumerator_value` = '" . $denumerator . "' WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-01'; ");
                            array_push($query, 
                            "INSERT INTO `hospital_survey_indicator_result_recapitulation` (`result_indicator_id`, `result_hospital_id`, `result_numerator_value`, `result_denumerator_value`, `result_period`, `result_post_date`, `result_record_status`) " . 
                            "SELECT * FROM (SELECT " . $mapping[0] . " as val1, 2277 as val2, '" . $numerator . "' as val4, '" . $denumerator . "' as val5, '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-01' as val7, '2019-09-27 00:00:00' as val8, 'A' as val9" . ") AS tmp " .
                            "WHERE NOT EXISTS (" .
                            "SELECT `result_indicator_id` FROM `hospital_survey_indicator_result_recapitulation` WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-01' " .
                            ") LIMIT 1; " );
                            if ($mapping[3] == 0) {
                                // fill daily data
                                for ($day = 1; $day <= $dayInMonth; $day++) {
                                    if ($mapping[4] < count($dailyData)) {
                                        $dailyNumerator = $dailyData[(int)$mapping[4]-1][$day - 1];
                                        if (!isset($dailyNumerator) || trim($dailyNumerator) === '') $dailyNumerator = 0;
                                        $dailyDenumerator = $dailyData[(int)$mapping[4]][$day - 1];
                                        if (!isset($dailyDenumerator) || trim($dailyDenumerator) === '') $dailyDenumerator = 0;
                                        array_push($query, "UPDATE `hospital_survey_indicator_result` SET `result_numerator_value` = '" . $dailyNumerator . "', `result_denumerator_value` = '" . $dailyDenumerator . "' WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_department_id` = '" . $departmentId . "' AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-" . sprintf('%02d', $day) . "'; ");
                                        array_push($query, 
                                        "INSERT INTO `hospital_survey_indicator_result` (`result_indicator_id`, `result_hospital_id`, `result_department_id`, `result_numerator_value`, `result_denumerator_value`, `result_document_amount`, `result_period`, `result_post_date`, `result_record_status`) " . 
                                        "SELECT * FROM (SELECT " . $mapping[0] . " as val1, 2277 as val2, '" . $departmentId . "' as val3, '" . $dailyNumerator . "' as val4, '" . $dailyDenumerator . "' as val5, NULL as val6, '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-" . sprintf('%02d', $day) . "' as val7, '2019-09-27 00:00:00' as val8, 'A' as val9" . ") AS tmp " .
                                        "WHERE NOT EXISTS (" .
                                        "SELECT `result_indicator_id` FROM `hospital_survey_indicator_result` WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_department_id` = '" . $departmentId . "' AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-" . sprintf('%02d', $day) . "' " .
                                        ") LIMIT 1; " );
                                    }
                                }
                            }
                            
                        }
                    }
                }
                $queryStr = join('  ', $query);
                $data = array();
                $data['api_key'] = 'synergy1234';
                $data['query'] = $queryStr;
              $queryUrl = Util::curlAsync("http://122.50.7.149/sismadak_v5/proxy.php", $data, 'POST');
            }*/

            $this->set_response($pmkp, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create pmkp failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put($id = null) {
        $dataPut = $this->put();
        if($id) {
            $result = $this->pmkp_model->update($dataPut, $id);
            if($result) {
                $pmkp = $this->pmkp_model->get($id);

                // insert to SISMADAK
                /*if ($pmkp->year > 2019 || ($pmkp->year == 2019 && $pmkp->month >= 9)) {
                    $dayInMonth = cal_days_in_month(CAL_GREGORIAN, $pmkp->month, $pmkp->year); // because some data is not having form A so we directly put only tgl 1 of each month
                    // array of array(<indicator_id>,<pmkp_type>,<formB_idx_which_start_from_1>, 0=normal,1=only form B, 2=only hasil,<formA_idx_which_start_from_1> )
                    $mappingList = array(
                        array(90, 'timKPRS', 1, 0, 1),
                        array(91, 'igd', 5, 0, 3),
                        array(92, 'rawatJalan', 4, 0, 3),
                        array(93, 'kamarOperasi', 12, 0, 23),
                        array(94, 'rawatInap', 4, 0, 1),
                        array(95, 'laboratorium', 7, 0, 19),
                        array(96, 'farmasi', 5, 1, 0),
                        // array(97, 'timKPRS', 1),
                        array(98, 'ppi', 12, 0, 19),
                        array(99, 'timKPRS', 8, 1, 0),
                        array(100, 'komiteMedis', 1, 2, 0),
                        array(101, 'timKomplain', 1, 1, 0),
                        array(102, 'timKomplain', 2, 1, 0)
                    );
                    $departmentId = 51;
                    $rowCount = 0;
                    $query = array();
                    foreach ($mappingList as $mapping) {
                        $dataForType =  $this->pmkp_model->getByYearAndMonthAndType((int)$pmkp->year, (int)$pmkp->month, $mapping[1]);
                        if ($dataForType != NULL) {
                            $monthlyData = json_decode($dataForType->monthlyData, true);
                            $dailyData = json_decode($dataForType->dailyData, true);
                            if ((int)$mapping[2] < count($monthlyData)) {
                                $numerator = $monthlyData[(int)$mapping[2]-1]['numerator'];
                                $denumerator = $monthlyData[(int)$mapping[2]-1]['denumerator'];
                                $hasil = $monthlyData[(int)$mapping[2]-1]['hasil'];
                                if (count($mapping) >= 6) {
                                    $processedNumAndDenum = $mapping[5]($numerator, $denumerator, $hasil);
                                    $numerator = $processedNumAndDenum[0];
                                    $denumerator = $processedNumAndDenum[1];
                                }
                                $rowCount++;
                                if ($mapping[3] == 2) {
                                    $numerator = $hasil;
                                    $denumerator = $hasil;
                                }
                                array_push($query, "UPDATE `hospital_survey_indicator_result_recapitulation` SET `result_numerator_value` = '" . $numerator . "', `result_denumerator_value` = '" . $denumerator . "' WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-01'; ");
                                array_push($query, 
                                "INSERT INTO `hospital_survey_indicator_result_recapitulation` (`result_indicator_id`, `result_hospital_id`, `result_numerator_value`, `result_denumerator_value`, `result_period`, `result_post_date`, `result_record_status`) " . 
                                "SELECT * FROM (SELECT " . $mapping[0] . " as val1, 2277 as val2, '" . $numerator . "' as val4, '" . $denumerator . "' as val5, '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-01' as val7, '2019-09-27 00:00:00' as val8, 'A' as val9" . ") AS tmp " .
                                "WHERE NOT EXISTS (" .
                                "SELECT `result_indicator_id` FROM `hospital_survey_indicator_result_recapitulation` WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-01' " .
                                ") LIMIT 1; " );
                                if ($mapping[3] == 0) {
                                    // fill daily data
                                    for ($day = 1; $day <= $dayInMonth; $day++) {
                                        if ($mapping[4] < count($dailyData)) {
                                            $dailyNumerator = $dailyData[(int)$mapping[4]-1][$day - 1];
                                            if (!isset($dailyNumerator) || trim($dailyNumerator) === '') $dailyNumerator = 0;
                                            $dailyDenumerator = $dailyData[(int)$mapping[4]][$day - 1];
                                            if (!isset($dailyDenumerator) || trim($dailyDenumerator) === '') $dailyDenumerator = 0;
                                            array_push($query, "UPDATE `hospital_survey_indicator_result` SET `result_numerator_value` = '" . $dailyNumerator . "', `result_denumerator_value` = '" . $dailyDenumerator . "' WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_department_id` = '" . $departmentId . "' AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-" . sprintf('%02d', $day) . "'; ");
                                            array_push($query, 
                                            "INSERT INTO `hospital_survey_indicator_result` (`result_indicator_id`, `result_hospital_id`, `result_department_id`, `result_numerator_value`, `result_denumerator_value`, `result_document_amount`, `result_period`, `result_post_date`, `result_record_status`) " . 
                                            "SELECT * FROM (SELECT " . $mapping[0] . " as val1, 2277 as val2, '" . $departmentId . "' as val3, '" . $dailyNumerator . "' as val4, '" . $dailyDenumerator . "' as val5, NULL as val6, '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-" . sprintf('%02d', $day) . "' as val7, '2019-09-27 00:00:00' as val8, 'A' as val9" . ") AS tmp " .
                                            "WHERE NOT EXISTS (" .
                                            "SELECT `result_indicator_id` FROM `hospital_survey_indicator_result` WHERE `result_indicator_id` = " . $mapping[0] . " AND `result_department_id` = '" . $departmentId . "' AND `result_period` = '" . $pmkp->year . "-" . str_pad($pmkp->month, 2, "0", STR_PAD_LEFT) . "-" . sprintf('%02d', $day) . "' " .
                                            ") LIMIT 1; " );
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                    $queryStr = join('  ', $query);
                    $data = array();
                    $data['api_key'] = 'synergy1234';
                    $data['query'] = $queryStr;
                 $queryUrl = Util::curlAsync("http://122.50.7.149/sismadak_v5/proxy.php", $data, 'POST');
                }*/
                
                $this->set_response($pmkp, REST_Controller::HTTP_OK);
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
            $result = $this->pmkp_model->delete($id);
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


