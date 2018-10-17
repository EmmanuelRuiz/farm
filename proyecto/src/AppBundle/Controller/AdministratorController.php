<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Product;
use AppBundle\Form\ProductType;

class AdministratorController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        //var_dump($user);
        //d//ie();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

                
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //subir imagen
                $file = $form['image']->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();
                    if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                        $file_name = $user->getId() . time() . "." . $ext;
                        $file->move("uploads/products/images", $file_name);
                        $product->setImage($file_name);
                    } else {
                        $product->setImage(null);
                    }
                } else {
                    $product->setImage(null);
                }

                $product->setUser($user);
                $product->setCategory(1);
                $product->setAvailable("1");
                $product->setCreatedAt(new \DateTime("now"));
                $em->persist($product);
                $flush = $em->flush();
                
                if ($flush == null) {
                    $status = "El producto se guardó correctamente.";
                } else {
                    $status = "Error al guardar el producto.";
                }

                $status = "La información se guardó.";
            } else {
                $status = "El formulario no se envió.";
            }

            $this->session->getFlashBag()->add("status", $status);
            //$this->redirectToRoute('login');
        }

        return $this->render("AppBundle:Administrator:admin_home.html.twig", array(
                    'form' => $form->createView()
        ));
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
