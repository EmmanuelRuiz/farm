<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\EditUserType;

class UserController extends Controller {

    //las sessiones las uso para los mensajes flash
    //para eso arriba usamos el componente session
    //en register action hay un ejemplo
    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils) {
        //si el if nos devuelve un objeto que redirija a home
        // pues el usuario esta logueado
        if (is_object($this->getUser())) {
            return $this->redirect('/admin/');
        }

        //en security yml configuramos el provider, 
        //en donde indicamos que entidad funcionara como provider
        //en firewalls menu indicamos las rutas
        //en backendbundle/entity/user.php se configura userinterface
        //cargar servicio de autenticacion de symfony
        //$authenticationUtils = $this->get('security.authentication_utils');

        //guardar si hubo un error
        $error = $authenticationUtils->getLastAuthenticationError();
        //saccar el ultimo usuario que intento loggearse y no pudo
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('AppBundle:User:login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error
        ));
    }

    public function registerAction(Request $request) {
        //si el if nos devuelve un objeto que redirija a home
        // pues el usuario esta logueado        
        if (is_object($this->getUser())) {
            return $this->redirect('/admin/');
        }


        //Usamos el objeto user y el formulario, hay que incluirlos en el namespace
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        //recoger request de formulario con handleRequest
        //bindea/vincula  los datos que llegan con el formulario con $user
        //ya no toca vincular los datos que llegan individualmente con ->set...
        $form->handleRequest($request);
        //validar si llega el form
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //entity manager
                $em = $this->getDoctrine()->getManager();
                //obtener repositorio del usuario
                //$user_repo = $em->getRepository("BackendBundle:User");
                //query para comprobar que el que se quiere registrar no esté ya registrado en DQL
                //con la siguiente linea conseguimos el dato que llega del formulario por post
                //->setParameter('email', $form->get("email")->getData());

                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email')
                        ->setParameter('email', $form->get("email")->getData());
                //obtener el resultado de la query
                $user_isset = $query->getResult();
                //si no hay resultados, es que no hay registro 
                if (count($user_isset) == 0) {
                    //si el resultado es 0, creamos el usuario
                    //codificar la contraseña con los encoders
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);
                    // el metodo getSalt lo creamos en la entidad user 
                    //BackendBundle/entity/User.php
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());
                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage(null);
                    //persistir objeto user en doctrine
                    $em->persist($user);

                    $flush = $em->flush();
                    if ($flush == null) {
                        $status = "Te has registrado correctamente.";
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");
                    } else {
                        $status = "Hubo un error, intenta de nuevo.";
                    }
                } else {
                    $status = "El usuario ya existe.";
                }
            } else {
                $status = "No te has registrado correctamente. Prueba de nuevo";
            }
            $this->session->getFlashBag()->add("status", $status);
        }


        return $this->render('AppBundle:User:register.html.twig', array(
                    "form" => $form->createView()
        ));
    }

    public function emailTestAction(Request $request) {
        //recojemos en la variable lo que nos llega por post
        //para usar la respuesta http hay que incluirla en el namespace
        $email = $request->get("email");

        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");
        $user_isset = $user_repo->findOneBy(array("email" => $email));

        $result = "used";
        if (count($user_isset) >= 1 && is_object($user_isset)) {
            $result = "used";
        } else {
            $result = "unused";
        }

        return new Response($result);
    }

    public function editUserAction(Request $request) {
        //cargar en user todos los datos del usuario logueado
        $user = $this->getUser();
        $user_image = $user->getImage();
        //pasar los datos del usuario logueado al formulario
        $form = $this->createForm(EditUserType::class, $user);


        //recoger request de formulario con handleRequest
        //bindea/vincula  los datos que llegan con el formulario con $user
        //ya no toca vincular los datos que llegan individualmente con ->set...
        $form->handleRequest($request);
        //validar si llega el form
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //entity manager
                $em = $this->getDoctrine()->getManager();
                //obtener repositorio del usuario
                //$user_repo = $em->getRepository("BackendBundle:User");
                //query para comprobar que el que se quiere registrar no esté ya registrado en DQL
                //con la siguiente linea conseguimos el dato que llega del formulario por post
                //->setParameter('email', $form->get("email")->getData());

                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email')
                        ->setParameter('email', $form->get("email")->getData());
                //obtener el resultado de la query
                $user_isset = $query->getResult();
                //si user isset = 0 que deje editar el usuario 
                if ( count($user_isset) == 0 || ($user->getEmail() == $user_isset[0]->getEmail()) ) {
                    //si el resultado es 0, modificamos el usuario
                    //subir archivo

                    $file = $form["image"]->getData();
                    if (!empty($file) && $file != null) {
                        $ext = $file->guessExtension();
                        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                            $file_name = $user->getName() . time() . '.' . $ext;
                            //guardar fichero en directorio
                            $file->move("uploads/users", $file_name);
                            $user->setImage($file_name);
                        }
                    } else {
                        $user->setImage($user_image);
                    }

                    //persistir objeto user en doctrine
                    $em->persist($user);
                    //guardar en bd
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Los datos se guardaron con éxito.";
                    } else {
                        $status = "Hubo un error, intenta de nuevo.";
                    }
                } else {
                    $status = "El usuario ya existe.";
                }
            } else {
                $status = "Los datos no se pudieron guardar. Prueba de nuevo";
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirect('my-data');
        }

        return $this->render('AppBundle:User:edit_user.html.twig', array(
                    "form" => $form->createView()
        ));
    }

    /*public function usersAction(Request $request) {
        //para trabajar con la bd hacemos una instancia del entitimanager
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT u FROM BackendBundle:User u";
        $query = $em->createQuery($dql);
        //ahora usamos el paginador para obtener los registros y poder paginar
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1, 5)
        );

        return $this->render('AppBundle:Administrator:users.html.twig', array(
            'pagination' => $pagination
        ));
    }*/

}
