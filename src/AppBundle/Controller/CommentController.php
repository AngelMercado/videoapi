<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

/**
 * Description of CommentController
 *
 * @author root
 */
use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackBundle\Entity\User;
use BackBundle\Entity\Video;
use BackBundle\Entity\Comment;

class CommentController extends Controller {

    //put your code here
    public function createCommentAction(Request $request) {

        $helper = $this->get("app.helper");
        $hash = $request->get("authorization", null);
        $authCheck = $helper->authCheck($hash);
        $data = array();

        if ($authCheck == true) {
            $identity = $helper->authCheck($hash, true);
            $json = $request->get("json", null);

            if ($json != null) {

                $params = json_decode($json);

                $created_at = new \DateTime('now');
                $user_id = (isset($identity->sub)) ? $identity->sub : null;
                $video_id = (isset($params->videoId)) ? $params->videoId : null;
                $body = (isset($params->body)) ? $params->body : null;

                if ($user_id != null && $video_id != null) {
                    $em = $this->getDoctrine()->getManager();

                    $user = $em->getRepository("BackBundle:User")->findOneBy(
                            array(
                                "userid" => $user_id
                    ));

                    $video = $em->getRepository("BackBundle:Video")->findOneBy(
                            array(
                                "videoid" => $user_id
                    ));

                    //intance comment
                    $comment = new Comment();

                    $comment->setUserid($user);
                    $comment->setVideoid($video);
                    $comment->setBody($body);
                    $comment->setCreatedAt($created_at);

                    $em->persist($comment);
                    $em->flush();
                    $data["status"] = "success";
                    $data["code"] = "202";
                    $data["msg"] = "createad Comment";

                    return $helper->json($data);
                } else {
                    $data["status"] = "error";
                    $data["code"] = "404";
                    $data["msg"] = "Params not valid";

                    return $helper->json($data);
                }
            } else {
                $data["status"] = "error";
                $data["code"] = "404";
                $data["msg"] = "Params not valid";

                return $helper->json($data);
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "501";
            $data["msg"] = "Authentication not valid";

            return $helper->json($data);
        }
    }

    public function deleteCommentAction(Request $request, $commentId) {
        $helper = $this->get("app.helper");
        $hash = $request->get("authorization", null);
        $authCheck = $helper->authCheck($hash);
        $data = array();

        if ($authCheck == true) {
            $identity = $helper->authCheck($hash, true);

            $user_id = ($identity->sub != null) ? $identity->sub : null;

            $em = $this->getDoctrine()->getManager();
            $comment = $em->getRepository("BackBundle:Comment")->findOneBy(
                    array(
                        "commentid" => $commentId
            ));


            if ($user_id != null && is_object($comment)) {
                //valid user is the owner of the video
                if (isset($identity->sub) && $identity->sub == $comment->getUserid()->getUserId()) {
                    $em->remove($comment);
                    $em->flush();
                    $data["status"] = "success";
                    $data["code"] = "200";
                    $data["msg"] = "deleted comment";
                    return $helper->json($data);
                } else {
                    $data["status"] = "error";
                    $data["code"] = "501";
                    $data["msg"] = "Unauthorized user to complete this action";

                    return $helper->json($data);
                }
            } else {
                $data["status"] = "error";
                $data["code"] = "404";
                $data["msg"] = "Params Invalid";

                return $helper->json($data);
            }
        } else {
            $data["status"] = "error";
            $data["code"] = "501";
            $data["msg"] = "Authentication not valid";

            return $helper->json($data);
        }
    }

    public function listCommentAction(Request $request, $videoId = null) {
        $helper = $this->get("app.helper");
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("BackBundle:Video")->findOneBy(
                array(
                    "videoid" => $videoId
        ));
        $comments = $em->getRepository("BackBundle:Comment")->findBy(array(
            "videoid" => $video
                ), array('commentid' => 'desc'));

        if (count($comments) > 0) {
            $data = array(
                "status" => "success",
                "code" => 200,
                "data" => $comments
            );
            return $helper->json($data);
        } else {
            $data = array(
                "status" => "error",
                "code" => 404,
                "data" => "Don't exists comments in this video"
            );
            return $helper->json($data);
        }
    }

}
