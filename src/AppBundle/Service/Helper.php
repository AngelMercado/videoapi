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
     public function json($data){
        //convert json to object
        $normalizer = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
    
        //convert object to json
        $encoder= array("json"=> new \Symfony\Component\Serializer\Encoder\JsonEncoder());
        
        //object serializer
        $serilizer = new \Symfony\Component\Serializer\Serializer($normalizer,$encoder);
        
        $json = $serilizer->serialize($data, 'json');
        
        //Http object
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($json);
        $response->headers->set("Content-Type", "application/json");
        
        return $response;
        
    }
}
