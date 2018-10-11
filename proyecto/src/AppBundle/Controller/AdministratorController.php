<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\User;

class AdministratorController extends Controller {

    //put your code here
    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function usersAction(Request $request) {
        //para trabajar con la bd hacemos una instancia del entitimanager
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM BackendBundle:User u ORDER BY u.id ASC";
        $query = $em->createQuery($dql);
        //ahora usamos el paginador para obtener los registros y poder paginar
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('AppBundle:Administrator:users.html.twig', array(
                    'pagination' => $pagination
        ));
    }

}
