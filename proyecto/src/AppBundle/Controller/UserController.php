<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use BackendBundle\Entity\User;
use AppBundle\Form\UserType;

class UserController extends Controller {

    public function loginAction(Request $request) {
        
        return $this->render('AppBundle:User:login.html.twig', array(
            "titulo" => "Login"
        ));
    }
    
    public function registerAction(Request $request){
        //Usamos el objeto user y el formulario, hay que incluirlos en el namespace
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        
        //recoger request de formulario con handleRequest
        //bindea/vincula  los datos que llegan con el formulario con $user
        //ya no toca vincular los datos que llegan individualmente con ->set...
        $form->handleRequest($request);
        //validar si llega el form
        if($form->isSubmitted()){
            if($form->isValid()){
                //entity manager
                $em = $this->getDoctrine()->getManager();
                //obtener repositorio del usuario
                $user_repo =  $em->getRepository("BackendBundle:User");
                
                //query para comprobar que el que se quiere registrar no esté ya registrado en DQL
                //con la siguiente linea conseguimos el dato que llega del formulario por post
                //->setParameter('email', $form->get("email")->getData());
                
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email')
                        ->setParameter('email', $form->get("email")->getData());
                //obtener el resultado de la query
                $user_isset = $query->getResult();
                //si no hay resultados, es que no hay registro 
                if(count($user_isset) == 0){
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
                    if($flush == null){
                        $status = "Te has registrado correctamente.";
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
           
        }
        
        
        return $this->render('AppBundle:User:register.html.twig', array(
            "form" => $form->createView()
        ));
    }
}
