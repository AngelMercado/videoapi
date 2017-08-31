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
class UserController extends Controller {

    public function createUserAction(Request $request) {
        $helper = $this->get("app.helper");
        $json = $request->get("json", null);
        $params = json_decode($json);
        $data = array();

        if ($json != null) {
            $createdAt = new \DateTime("now");
            $image = null;
            $role = "user";
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
            $password = (isset($params->password)) ? $params->password : null;

            $emailContraint = new Assert\Email();
            $emailContraint->message = "invalid email please enter a correct email";
            $validate_email = $this->get("validator")->validate($email, $emailContraint);

            if ($email != null && count($validate_email) == 0 && $password != null && $name != null && $surname != null) {
                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setEmail($email);
                $user->setImage($image);
                $user->setName($name);
                $user->setRole($role);
                $user->setSurname($surname);
                $user->setUpdatedAt(null);

                // encode password
                $psw = hash('sha256', $password);
                $user->setPassword($psw);
                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository("BackBundle:User")->findBy(
                        array(
                            "email" => $email
                ));

                if (count($isset_user) == 0) {
                    $em->persist($user);
                    $em->flush();

                    $data["status"] = "success";
                    $data["code"] = "202";
                    $data["msg"] = "User is created";
                } else {
                    $data["status"] = "error";
                    $data["code"] = "400";
                    $data["msg"] = "User already exists";
                }
            } else {
                $data["status"] = "error";
                $data["code"] = "400";
                $data["msg"] = "user is not created invalid arguments";
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "400";
            $data["msg"] = "user is not created";
        }
        return $helper->json($data);
    }

    public function updateUserAction(Request $request) {
        $helper = $this->get("app.helper");
        $hash = $request->get("authorization", null);
        $authCheck = $helper->authCheck($hash);

        if ($authCheck == true) {

            $identity = $helper->authCheck($hash, true);
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository("BackBundle:User")->findOneBy(
                    array(
                        "userid" => $identity->sub
            ));

            $json = $request->get("json", null);
            $params = json_decode($json);
            $data = array();

            if ($json != null) {
                $updatedAt = new \DateTime("now");
                $image = null;
                $role = "user";
                $email = (isset($params->email)) ? $params->email : null;
                $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
                $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
                $password = (isset($params->password)) ? $params->password : null;

                $emailContraint = new Assert\Email();
                $emailContraint->message = "invalid email please enter a correct email";
                $validate_email = $this->get("validator")->validate($email, $emailContraint);

                if ($email != null && count($validate_email) == 0 && $name != null && $surname != null) {
                    $user->setEmail($email);
                    $user->setImage($image);
                    $user->setName($name);
                    $user->setRole($role);
                    $user->setSurname($surname);
                    $user->setUpdatedAt($updatedAt);

                    if ($password != null) {
                        // encode password
                        $psw = hash('sha256', $password);
                        $user->setPassword($psw);
                    }
                    $em = $this->getDoctrine()->getManager();
                    $isset_user = $em->getRepository("BackBundle:User")->findBy(
                            array(
                                "email" => $email
                    ));

                    if (count($isset_user) == 0 || $identity->email == $email) {
                        $em->persist($user);
                        $em->flush();

                        $data["status"] = "success";
                        $data["code"] = "202";
                        $data["msg"] = "User is updated";
                    } else {
                        $data["status"] = "error";
                        $data["code"] = "400";
                        $data["msg"] = "User dont updated";
                    }
                } else {
                    $data["status"] = "error";
                    $data["code"] = "400";
                    $data["msg"] = "user is not updated invalid arguments";
                }
            } else {
                $data["status"] = "error";
                $data["code"] = "400";
                $data["msg"] = "user is not updated bad request";
            }
            return $helper->json($data);
        } else {
            $data["status"] = "error";
            $data["code"] = "403";
            $data["msg"] = "Not authenticated";
            return $helper->json($data);
        }
    }

    public function updateImageAction(Request $request) {
        $helpers = $this->get("app.helper");
        $data = array();

        $hash = $request->get("authorization", null);

        if ($hash != null) {
            $authCheck = $helpers->authCheck($hash);

            if ($authCheck) {
                $identity = $helpers->authCheck($hash, true);
                $em = $this->getDoctrine()->getEntityManager();
                $user = $em->getRepository("BackBundle:User")->findOneBy(
                        array(
                            "userid" => $identity->sub
                ));
                //ulpload file
                $file = $request->files->get("image");
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();
                    if ($ext == "jpeg" || $ext == "png" || $ext == "gif") {
                        $file_name = time() . "." + $ext;
                        $file->move("uploads/user", $file_name);

                        $user->setImage($file_name);
                        $em->persist($user);
                        $em->flush();

                        $data["status"] = "sucess";
                        $data["code"] = "200";
                        $data["msg"] = "uploaded image";
                        return $helpers->json($data);
                    } else {
                        $data["status"] = "error";
                        $data["code"] = "200";
                        $data["msg"] = "invalid image format";
                    }
                } else {
                    $data["status"] = "error";
                    $data["code"] = "400";
                    $data["msg"] = "invalid Image";
                    return $helpers->json($data);
                }
            } else {
                $data["status"] = "error";
                $data["code"] = "403";
                $data["msg"] = "Ivalid Credentials";
                return $helpers->json($data);
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "403";
            $data["msg"] = "Not authenticated";
            return $helpers->json($data);
        }
    }

    //this method is public any user can see the videos
    public function channelAction(Request $request, $userid = null) {
        $helper = $this->get("app.helper");
        #get param for get request
        $page = $request->query->getInt("page", 1);
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository("BackBundle:User")->findOneBy(
                array(
                    "userid" => $userid
        ));

        #create a dql query
        $dql = "SELECT v FROM BackBundle:Video v WHERE v.user = $userid  ORDER BY v.videoid DESC";
        $query = $em->createQuery($dql);
        $data = array();

        #load paginator
        $paginator = $this->get("knp_paginator");
        $items_per_page = 6;
        $pagination = $paginator->paginate($query, $page, $items_per_page);                
        $total_items_count = $pagination->getTotalItemCount();

        if ($user->getUserid() != null) {
            $data = array(
                "status" => "success",
                "code" => 202,
                "totalItemsCount" => $total_items_count,
                "actualPage" => $page,
                "itemsPerPage" => $items_per_page,
                "totalPages" => ceil($total_items_count / $items_per_page),
            );
            $data["data"]["videos"] = $pagination;
            $data["data"]["user"] = $user;
        } else {
            $data = array(
                "status" => "error",
                "code" => 404,
                "msg" => "user do not exits"
            );
        }

        return $helper->json($data);
    }

}
