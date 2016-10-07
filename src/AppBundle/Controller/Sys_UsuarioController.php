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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class Sys_UsuarioController extends Controller
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
         return $this->render('sys_usuario/perfil.html.twig',  array(
                                                                'info'=>$info,
                                                                 ));
    }
    
    
  
    
    /**
     * @Route("/usuario/perfil/edit/", name="Usuario_Editar")
     */
    public function editAction(Request $request)
    {
        $perfil = $this->get('security.token_storage')->getToken()->getUser();
        $old_password=$perfil->getPassword();
              
        //Se crea el formulario
        $form = $this->createFormBuilder($perfil)   
                      ->add('username', TextType::class,array('label' => 'Usario: ', 
                                                'label_attr' => array('class' => 'control-label col-md-2 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))
                      ->add('email', EmailType::class,array('label' => 'Email: ', 
                                                'label_attr' => array('class' => 'control-label col-md-2 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))                           
                      ->add('password', PasswordType::class,array('label' => 'Contraseña: ', 'required'=>false,
                                                'label_attr' => array('class' => 'control-label col-md-2 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12','title'=>'* Utilice este campo para cambiar su clave actual.')))
                   
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
            $new_password=$request->get('form')['password'];
            if(empty($new_password ))
            {
              $perfil->setPassword($old_password);
            }
            else{
              $perfil->setPlainPassword($new_password);
            }
           
            $em=$this->getDoctrine()->getManager();             
            $em->flush();  
            $this->addFlash(
                                'info',
                                'Peril, actualizado correctamente.'  
                            ); 
                        
            return $this->redirectToRoute('Usuario_Perfil');   
         
        }
        else{
           $this->addFlash(
                                'advertencia',
                                'Contraseña\nUtilice este campo para cambiar su clave actual.'  
                            ); 
        } 

        return $this->render('sys_usuario/edit.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info                                                                                                                      
                                                               ));
    }

     /**
     * @Route("/admin/usuario/", name="GestionarUsuario")
     */
    public function adminAction(Request $request)
    {           
        //Se crea el formulario
        $form = $this->createFormBuilder()   
                       ->add('idUsuario', EntityType::class, array( 
                              'label'=>'Usuarios *',                  
                              'class' => 'AppBundle:User',
                              'choice_label' => 'username',  
                              'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                              'attr' => array('class' => 'height25px col-md-8 col-xs-12') 
                              )) 
                      ->getForm();       

        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Gestionar Usuario',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Gestionar', 
                      'subtitulo' =>'Usuario'
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
          $idUsuario=$request->get('form')['idUsuario'];
          $em = $this->getDoctrine()->getManager();
          $usuario = $em->getRepository('AppBundle:User')->find($idUsuario); 
          return $this->render('sys_usuario/admin.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info,  
                                                                'control'=>true ,
                                                                'usuario'=>$usuario                                                                                                                                                                                
                                                               ));
        }      

        return $this->render('sys_usuario/admin.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info,  
                                                                'control'=>false                                                                                                                    
                                                               ));
    }

      /**
     *@Route("/admin/usuario/update/", name="updateUsuarioAdmin")
     */
    public function adminUpdateAction(Request $request)
    {             
      
         $idUsuario=$request->get('idUsuario_post');
         $nombreUsuario_post=$request->get('nombreUsuario_post');
         $emailUsuario_post=$request->get('emailUsuario_post');
         $idPassword_post=$request->get('idPassword_post');
         $idBloqueado_post=$request->get('idBloqueado_post');
         $idExpirado_post=$request->get('idExpirado_post');
         $idFechaUsuario_post=$request->get('idFechaUsuario_post');

         $em = $this->getDoctrine()->getManager();
         $usuario = $em->getRepository('AppBundle:User')->find($idUsuario);
         $usuario->setUsername($nombreUsuario_post);
         $usuario->setEmail($emailUsuario_post);
         if($idPassword_post!='vacio')
         {
            $usuario->setPlainPassword($idPassword_post);
         }
         $usuario->setLocked($idBloqueado_post);
         $usuario->setExpired($idExpirado_post);
         if($idFechaUsuario_post!='vacio')
         {
            $usuario->setExpiresAt(new \DateTime($idFechaUsuario_post));
         }         
         $em=$this->getDoctrine()->getManager();             
         $em->flush(); 
         
         return new JsonResponse('ok');
    }
}
