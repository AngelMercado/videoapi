<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

use Firebase\JWT\JWT;

class JwtAuth {
    public $manager;
    //get the argument from service.yml
    public function __construct($manager){
        $this->manager = $manager;
    }
    public function singup($email,$password,$getHash=null) {
        $key = "clave-secreta";
        $singup = false;
        $user = $this->manager->getRepository("BackBundle:User")->findOneBy(
                    array(
                        "email" => $email,
                        "password" => $password                
                    )
                );
        
        if(is_object($user)){
            $singup= true;
        }
        if ($singup==true){
            //generate commont jwt sub, iat, exp
            $token = array(
                "sub" => $user->getUserId(),
                "rol" => $user->getRole(),
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "surname" => $user->getSurname(),
                "password" => $user->getPassword(),
                "image" => $user->getImage(),
                "iat" => time(),
                "exp" => time() + (7*24*60*60)
            );
            $jwt = JWT::encode($token, $key,'HS256');
            $decoded  = JWT::decode($jwt, $key,array('HS256'));
            
            if ($getHash == true){
                return $jwt;
            }
            else{
                return $decoded;
            }
//            return array("status"=>"success","message"=>"Login success");
        }else{
            return array("status"=>"error","message"=>"Login fail");
        }
    }
    
     public function checkToken($jwt,$getIdentity=false) {
        $key = "clave-secreta";
        $auth =false;
        
        try {
            $decoded = JWT::decode(trim($jwt,'"'), $key, array('HS256'));
            
         } catch (\DomainException $exc) {
            $auth = false;            
         } catch (\UnexpectedValueException $exc){
            $auth = false;             
         }
        
        if (isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        
        if ($getIdentity == true){
            return $decoded;
        }else{
            return $auth;
        }
     }
}
