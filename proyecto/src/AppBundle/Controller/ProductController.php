<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\Product;

class ProductController extends Controller {

    public function indexAction(Request $request) {

        return $this->render('AppBundle:Product:home.html.twig');
    }

    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        //recoger un parametro get de la url
        //en caso de que no llegue nada, que sea null
        $search = $request->query->get("search", null);

        //si search es igual a null nos redirige a home
        if ($search == null) {
            return $this->redirect($this->generateURL('home_products'));
        }
        $dql = "SELECT u from BackendBundle:Product u "
                . "WHERE u.productName LIKE :search ORDER BY u.productName ASC";
        //limpiar los string con setParameter
        $query = $em->createQuery($dql)->setParameter('search', "%$search%");
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );
        return $this->render('AppBundle:Product:search.html.twig', array(
                    'pagination' => $pagination
        ));
    }

}
