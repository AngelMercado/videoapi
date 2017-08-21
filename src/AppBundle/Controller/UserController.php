<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackBundle\Entity\User;
/**
 * Description of UserController
 *
 * @author root
 */
class UserController extends Controller{
    
    public function createUserAction(Request $request) {
        $helper = $this->get("app.helper");
        
        $json = $request->get("json",null);
        $params = json_decode($json);       
        $data = array();
        
        if ($json!=null){
            $createdAt = new \DateTime("now");
            $image = null;
            $role= "user";
            $email= (isset($params->email)) ? $params->email:null; 
            $name= (isset($params->name) && ctype_alpha($params->name)) ? $params->name:null; 
            $surname= (isset($params->surname)&& ctype_alpha($params->surname)) ? $params->surname:null; 
            $password= (isset($params->password)) ? $params->password:null; 
            
            $emailContraint = new Assert\Email();
            $emailContraint->message = "invalid email please enter a correct email";
            $validate_email = $this->get("validator")->validate($email, $emailContraint);
            
            if ($email!=null && count($validate_email)== 0 && $password != null && $name != null && $surname != null){
                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setEmail($email);
                $user->setImage($image);
                $user->setName($name);
                $user->setPassword($password);
                $user->setRole($role);
                $user->setSurname($surname);
                $user->setUpdatedAt(null);
                
                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository("BackBundle:User")->findBy(
                        array(
                            "email" => $email
                        ));
            
                if (count($isset_user)==0){
                    $em->persist($user);
                    $em->flush();
                    
                    $data["status"]="success";
                    $data["code"]="202";
                    $data["msg"]="User is created";
                    
                }else{
                    $data["status"]="error";
                    $data["code"]="400";
                    $data["msg"]="User already exists";
                }
            }else{
                $data["status"]="error";
                $data["code"]="400";
                $data["msg"]="user is not created invalid arguments";
            }
            
            
        }else{
            $data["status"]="error";
            $data["code"]="400";
            $data["msg"]="user is not created";
        }
        return $helper->json($data);
    }
}
