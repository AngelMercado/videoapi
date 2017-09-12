<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        //replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    public function testAction(Request $request) {
        $helper = $this->get("app.helper");
        
        $hash = $request->get("authorization", null);

        $check = $helper->authCheck($hash,true);
        var_dump($check);
        die();
//get entity manager
//        $em = $this->getDoctrine()->getManager();
//        $users = $em->getRepository("BackBundle:User")->findAll();
//        return $helper->json($users);
    }

    public function loginAction(Request $request) {
        //initiallize services
        $helper = $this->get("app.helper");
        $jwt_auth = $this->get("app.jwt_auth");
        //get request params
        $json = $request->get("json", null);

        if ($json != null) {
            $params = json_decode($json);
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            // encode password
            $psw = hash('sha256', $password);
            $getHash = (isset($params->gethash)) ? $params->gethash : null;
            //validate email
            $emailContraint = new Assert\Email();
            $emailContraint->message = "invalid email please enter a correct email";
            $validate_email = $this->get("validator")->validate($email, $emailContraint);

            if (count($validate_email) == 0 && $password != null) {
                //sing up return a json or hash
                if ($getHash == null || $getHash == false) {
                    $singup = $jwt_auth->singup($email, $psw);
                } else {
                    $singup = $jwt_auth->singup($email, $psw,true);
                }
                return new \Symfony\Component\HttpFoundation\JsonResponse($singup);
            } else {
                return $helper->json(
                                array(
                                    "status" => "error",
                                    "data" => "invalid user or password"
                                )
                );
            }
        } else {
            return $helper->json(
                            array(
                                "status" => "error",
                                "data" => "Send json with post"
                            )
            );
        }
    }

}
