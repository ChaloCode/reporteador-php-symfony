<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use AppBundle\Entity\User;


class UsuarioController extends Controller
{
    /**
     * @Route("/usuario/perfil/", name="Usuario_Perfil")
     */
    public function perfilAction(Request $request)
    {
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Perfil Usuario',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Usuario', 
                      'subtitulo' =>'Perfil'
                    ),
                      'tabla'=>array(
                      'titulo' => '', 
                      'subtitulo' =>'',
                      'descripcion'=>'Generado: '.$fecha
                    ),
                      'grafica'=>array(
                      'titulo' => 'Gráfica', 
                      'subtitulo' =>'Genereda: '.$fecha
                    )
        ); 
         return $this->render('usuario/perfil.html.twig',  array(
                                                                'info'=>$info,
                                                                 ));
    }
    
    
  
    
    /**
     * @Route("/usuario/perfil/edit/", name="Usuario_Editar")
     */
    public function editAction(Request $request)
    {
        $perfil = $this->get('security.token_storage')->getToken()->getUser();
      //var_dump($user->getId());
      //die('fin');
     // $perfil=$this->getDoctrine()
            //  ->getRepository('AppBundle:User')
            //  ->find($id); 
         
        
        //Se crea el formulario
        $form = $this->createFormBuilder($perfil)   
                      ->add('username', TextType::class,array('label' => 'Usario: ', 
                                                'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-12 col-xs-12')))
                      ->add('email', EmailType::class,array('label' => 'Email: ', 
                                                'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-12 col-xs-12')))                           
                      ->getForm();       

        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Editar Perfil',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Perfil', 
                      'subtitulo' =>'Editar'
                    ),
                      'tabla'=>array(
                      'titulo' => '', 
                      'subtitulo' =>'',
                      'descripcion'=>'Generado: '.$fecha
                    ),
                      'grafica'=>array(
                      'titulo' => 'Gráfica', 
                      'subtitulo' =>'Genereda: '.$fecha
                    )
        ); 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        { 
          
           $perfil->setPlainPassword('123');
            $em=$this->getDoctrine()->getManager();             
            $em->flush();  
            $this->addFlash(
                                'info',
                                'Peril, actualizado correctamente.'  
                            ); 
                        
            return $this->redirectToRoute('Usuario_Perfil');   
         
        } 

        return $this->render('usuario/edit.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info                                                                                                                      
                                                               ));
    }
}
