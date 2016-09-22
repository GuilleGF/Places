<?php

/**
 * Created by PhpStorm.
 * User: guille
 * Date: 29/04/16
 * Time: 19:26
 */
class GenericApi
{
    const DEBUG = false;
    
    /**
     * @param string $url
     * @return string
     */
    protected static function execute($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (!$response) {
            $response = '{"error":"'.curl_error($ch).'"}';
        }
        unset($ch);

        return $response;
    }

    /**
     * @param string $response
     * @return array|mixed|object
     */
    protected static function jsonDecode($response)
    {
        for ($i = 0; $i <= 31; ++$i) {
            $response = str_replace(chr($i), "", $response);
        }
        $response = str_replace(chr(127), "", $response);
        $response = str_replace("â€“","-",$response);

        // This is the most common part
        // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
        // here we detect it and we remove it, basically it's the first 3 characters 
        if (0 === strpos(bin2hex($response), 'efbbbf')) {
            $response = substr($response, 3);
        }

        return json_decode($response, true);
    }

}