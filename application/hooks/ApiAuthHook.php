<?php

class ApiAuthHook {
    private $CI;
    private $route;

    public function __construct() {
        $this->CI = &get_instance();
        $this->route = '/^api/i';
    }

    public function index() {
        $this->CI->load->helper('url');
        if (preg_match($this->route, uri_string())) {
            $headers = $this->CI->input->request_headers();
            if ($this->tokenIsExist($headers)) {
                $jwt = $this->jwtIsExist($headers);
                $token = $this->validateToken($jwt);
            } else {
                $this->httpBadResponse(
                    'The request lacks the authorization token'
                );
            }
        }
    }

    public function tokenIsExist($headers = array()) {
        return (
                array_key_exists('Authorization', $headers) &&
                !empty($headers['Authorization'])
                );
    }

    public function jwtIsExist($headers) {
        list($jwt) = sscanf($headers['Authorization'], '%s');
        return $jwt;
    }

    public function validateToken($jwt) {
        if ($jwt) {
            try {
                $token = Authorization::validateToken($jwt);
                return $token;
            } catch (Exception $ex) {
                $this->httpUnauthorizedResponse($ex->getMessage());
            }
        } else {
            $this->httpBadResponse(
                    'the token is unauthorized'
            );
        }
    }

    /**
     * http code 400 response
     * 
     * @param type $msg
     */
    public function httpBadResponse($msg = NULL) {
        set_status_header(400, $msg);
        exit(1);
    }

    /**
     * http code 401 response
     * 
     * @param type $msg
     */
    public function httpUnauthorizedResponse($msg = NULL) {
        set_status_header(401, $msg);
        exit(1);
    }

}
