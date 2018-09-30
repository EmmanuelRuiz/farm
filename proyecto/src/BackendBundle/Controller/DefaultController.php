<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //acceso a la bd
        $em = $this->getDoctrine()->getManager();
        //obtener identidad user
        $user_repo = $em->getRepository("BackendBundle:User");
        $user = $user_repo->find(1);
        echo "Bienvenido " . $user->getName();
        var_dump($user);
        die();
        return $this->render('BackendBundle:Default:index.html.twig');
    }
}
