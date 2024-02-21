<?php

class Util
{
    public static function copyIfNotEmpty(array $keyList, array $obj) {
        $result = array();
        foreach ($keyList as $key => $val) {
            $sourceName = $val;
            $targetName = $val;
            if (strpos($val, ';') !== false) {
                $valToken = explode(";", $val);
                $sourceName = $valToken[0];
                $targetName = $valToken[1];
            }
            if(!empty($obj[$sourceName]) && trim($obj[$sourceName])) {
                $result[$targetName] = $obj[$sourceName];
            }
        }
        return $result;
    }
    // $type must equal 'GET' or 'POST'
    public static function curlAsync($url, $params, $type='GET')
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.rawurlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);

        // Data goes in the path for a GET request
        if('GET' == $type) $parts['path'] .= '?'.$post_string;

        $out = "$type ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        // Data goes in the request body for a POST request
        if ('POST' == $type && isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
        return $out;
    }
}



