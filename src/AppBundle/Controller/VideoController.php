<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

/**
 * Description of VideoController
 *
 * @author root
 */
use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackBundle\Entity\User;
use BackBundle\Entity\Video;

class VideoController extends Controller {

    public function createVideoAction(Request $request) {
        $helper = $this->get("app.helper");
        $hash = $request->get("authorization", null);

        //verifing user have a valid token
        $authCheck = $helper->authCheck($hash);
        $data = array();

        if ($authCheck == true) {
            $identity = $helper->authCheck($hash, true);
            $json = $request->get("json", null);
            $params = json_decode($json);

            //defined default properties
            $createdAt = new \DateTime('now');
            $updatedtedAt = new \DateTime('now');
            $image = null;
            $video_path = null;

            $user_id = ($identity->sub != null) ? $identity->sub : null;

            //setting client params values

            $title = (isset($params->title)) ? $params->title : null;
            $description = (isset($params->description)) ? $params->description : null;
            $status = (isset($params->status)) ? $params->status : null;

            //verify token corresponses to a valid user            
            if ($user_id != null && $title != null) {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository("BackBundle:User")->findOneBy(
                        array(
                            "userid" => $user_id
                ));

                $video = new Video();

                $video->setUser($user);
                $video->setTitle($title);
                $video->setDescription($description);
                $video->setStatus($status);
                $video->setCreatedAt($createdAt);
                $video->setUpdatedAt($updatedtedAt);
                $video->setImage($image);
                $video->setVideopath($video_path);

                $em->persist($video);
                $em->flush();

                $videoq = $em->getRepository("BackBundle:Video")->findOneBy(
                        array(
                            "user" => $user,
                            "title" => $title,
                            "status" => $status,
                            "createdAt" => $createdAt
                ));

                $data["status"] = "sucess";
                $data["code"] = 202;
                $data["data"] = $video;
                return $helper->json($data);
            } else {
                $data["status"] = "error";
                $data["code"] = "404";
                $data["msg"] = "Invalid Params video not created";

                return $helper->json($data);
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "404";
            $data["msg"] = "Authorization not valid";

            return $helper->json($data);
        }
    }

    public function updateVideoAction(Request $request, $videoid = null) {
        $helper = $this->get("app.helper");
        $hash = $request->get("authorization", null);

        //verifing user have a valid token
        $authCheck = $helper->authCheck($hash);
        $data = array();

        if ($authCheck == true) {
            $identity = $helper->authCheck($hash, true);
            $json = $request->get("json", null);
            $params = json_decode($json);

            //defined default properties            
            $updatedtedAt = new \DateTime('now');
            $video_path = null;

            $user_id = ($identity->sub != null) ? $identity->sub : null;

            //setting client params values

            $title = (isset($params->title)) ? $params->title : null;
            $description = (isset($params->description)) ? $params->description : null;
            $status = (isset($params->status)) ? $params->status : null;

            //verify token corresponses to a valid user            
            if ($user_id != null && $title != null) {
                $em = $this->getDoctrine()->getManager();
                $video = $em->getRepository("BackBundle:Video")->findOneBy(
                        array(
                            "videoid" => $videoid
                ));
                //valids user is the owner of the video
                if (isset($identity->sub) && $identity->sub == $video->getUser()->getUserid()) {
                    $video->setTitle($title);
                    $video->setDescription($description);
                    $video->setStatus($status);
                    $video->setUpdatedAt($updatedtedAt);


                    $em->persist($video);
                    $em->flush();



                    $data["status"] = "sucess";
                    $data["code"] = 202;
                    $data["data"] = "videoUpdated";
                    return $helper->json($data);
                } else {
                    $data["status"] = "error";
                    $data["code"] = "401";
                    $data["msg"] = "Unauthorized user to complete this action";

                    return $helper->json($data);
                }
            } else {
                $data["status"] = "error";
                $data["code"] = "404";
                $data["msg"] = "invalid Params";

                return $helper->json($data);
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "403";
            $data["msg"] = "Authorization not valid";

            return $helper->json($data);
        }
    }

    public function uploadFilesAction(Request $request, $videoid) {
        $helper = $this->get("app.helper");
        $hash = $request->get("authorization", null);

        //verifing user have a valid token
        $authCheck = $helper->authCheck($hash);
        $data = array();

        if ($authCheck == true) {
            $identity = $helper->authCheck($hash, true);

            $em = $this->getDoctrine()->getEntityManager();
            $video = $em->getRepository("BackBundle:Video")->findOneBy(
                    array(
                        "videoid" => $videoid
            ));
            //valid the user is the owner of the video
            if ($videoid != null && isset($identity->sub) && $identity->sub == $video->getUser()->getUserid()) {
                $fileImage = $request->files->get("image", null);
                $fileVideo = $request->files->get("video", null);
                //switch between image and video
                if ($fileImage != null && !empty($fileImage)) {
                    $ext = $fileImage->guessExtension();
                    $file_name = time() . "." . $ext;

                    if ($ext == "jpeg" || $ext == "png") {
                        $filename = time() . "." . $ext;
                        $path_of_file = "uploads/video_image/video_" . $video->getVideoid();
                        $fileImage->move($path_of_file);
                        $video->setImage($filename);
                        $video->setVideopath($path_of_file);
                        $em->persist($video);
                        $em->flush();
                        $data["status"] = "success";
                        $data["code"] = "200";
                        $data["msg"] = "image file uploaded";
                        return $helper->json($data);
                    } else {
                        $data["status"] = "error";
                        $data["code"] = "400";
                        $data["msg"] = "format for image invalid";
                        return $helper->json($data);
                    }
                } else {
                    if ($fileVideo != null && !empty($fileVideo)) {
                        $ext = $fileVideo->guessExtension();
                        if ($ext == 'mp4' || $ext == "avi") {
                            $path_of_file = "uploads/video_files/video_" . $video->getVideoid();
                            $fileVideo->move($path_of_file);
                            $video->setVideopath($path_of_file);
                            $em->persist($video);
                            $em->flush();
                            $data["status"] = "success";
                            $data["code"] = "200";
                            $data["msg"] = "video file uploaded";
                            return $helper->json($data);
                        } else {
                            $data["status"] = "error";
                            $data["code"] = "400";
                            $data["msg"] = "Format for video invalid";
                            return $helper->json($data);
                        }
                    }
                }
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "403";
            $data["msg"] = "Authorization not valid";

            return $helper->json($data);
        }
    }

    //this method is public any user can see the videos
    public function videoListAction(Request $request) {
        $helper = $this->get("app.helper");
        #get param for get request
        $page = $request->query->getInt("page", 1);
        $em = $this->getDoctrine()->getEntityManager();
        #create a dql query
        $dql = "SELECT v FROM BackBundle:Video v ORDER BY v.videoid DESC";
        $query = $em->createQuery($dql);

        #load paginator
        $paginator = $this->get("knp_paginator");
        $items_per_page = 6;
        $pagination = $paginator->paginate($query, $page, $items_per_page);

        $total_items_count = $pagination->getTotalItemCount();

        $data = array(
            "status" => "success",
            "code" => 202,
            "totalItemsCount" => $total_items_count,
            "actualPage" => $page,
            "itemsPerPage" => $items_per_page,
            "totalPages" => ceil($total_items_count / $items_per_page),
            "data" => $pagination
        );
        
        return $helper->json($data);
    }

}
