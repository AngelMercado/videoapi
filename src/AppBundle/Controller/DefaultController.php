<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
//        return $this->render('default/index.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
//        ]);
    }
    
     /**
     * @Route("/pruebas", name="pruebas")
     */
    public function testAction(Request $request)
    {
        //get entity manager
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("BackBundle:User")->findAll();
        dump($users);
        
        die();
        
    }
    
}
