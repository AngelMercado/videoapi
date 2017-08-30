<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

/**
 * Description of Helpers
 *
 * @author root
 */
class Helper {
    public $jwt_auth;

    public function __construct($jwt_auth) {
        $this->jwt_auth = $jwt_auth;
    }

    public function authCheck($hash, $getIdentity = false) {
        $jwt_auth = $this->jwt_auth;
        $auth = false;

        if ($hash != null) {
            if ($getIdentity == false) {
                $checked_token = $jwt_auth->checkToken($hash);
                if ($checked_token == true) {
                    $auth = true;
                }
            } else {
                $checked_token = $jwt_auth->checkToken($hash,true);
                if (is_object($checked_token)) {
                    $auth = $checked_token;
                }
            }
        }
        return $auth;
    }

    public function json($data) {
        //convert object to array
        $normalizer = new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('transitions'));
        
        $normalizers = array($normalizer);

        //convert array to json or xml
        $encoders = array("json" => new \Symfony\Component\Serializer\Encoder\JsonEncoder());        
        //object serializer
        $serilizer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
        
        $json = $serilizer->serialize($data, 'json');

        //Http object

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($json);
        $response->headers->set("Content-Type", "application/json");

        return $response;
    }
}
