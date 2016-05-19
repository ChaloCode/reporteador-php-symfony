<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;






class Info_Controller extends Controller
{   
      
    /**
     * @Route("/info/quienes_somos", name="Info")
     */
    public function quienSomosAction(Request $request)
    {          
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => '¿Quienes somos?',
                    ),                    
                      'formulario'=>array(
                      'titulo' => '', 
                      'subtitulo' =>''
                    ),
                      'tabla'=>array(
                      'titulo' => '¿Quienes somos?', 
                      'subtitulo' =>'Colibrí Report',
                      'descripcion'=>'Generado: '.$fecha                      
                    ),
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
                    ); 
        return $this->render('info_/quienSomos.html.twig', array(                                                                   
                                                                    'info'=>$info
                                                                   
                                                                ));
    }
    
     /**
     * @Route("/info/version", name="Info_Version")
     */
    public function versionAction(Request $request)
    {          
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Version',
                    ),                    
                      'formulario'=>array(
                      'titulo' => '', 
                      'subtitulo' =>''
                    ),
                      'tabla'=>array(
                      'titulo' => 'Nota de versión', 
                      'subtitulo' =>'Colibrí Report',
                      'descripcion'=>'Generado: '.$fecha                      
                    ),
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
                    ); 
        return $this->render('info_/version.html.twig', array(                                                                   
                                                                    'info'=>$info
                                                                   
                                                                ));
    }
    
    
       /**
     * @Route("/info/contactar", name="Info_Contactar")
     */
    public function contactarAction(Request $request)
    {          
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Contactar',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Enviar', 
                      'subtitulo' =>'Mensaje'
                    ),
                      'tabla'=>array(
                      'titulo' => 'Contactar', 
                      'subtitulo' =>'Colibrí Report',
                      'descripcion'=>'Generado: '.$fecha                      
                    ),
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
                    ); 

    //Se crea el formulario
        $form = $this->createFormBuilder()   
                      ->add('asunto', TextType::class,array('label' => ' ', 
                                                'label_attr' => array('class' => 'fa fa-paper-plane-o control-label col-md-3 col-sm-3 col-xs-12'),                                                
                                                'attr' => array('class' => 'col-md-12 col-xs-12','placeholder'=>'Asunto')))
                      ->add('msm', TextareaType::class,array('label' => '  ', 'required'=>false,
                                                'label_attr' => array('class' => 'fa fa-envelope control-label col-md-3 col-sm-3 col-xs-12'),                                                
                                                'attr' => array('class' => 'col-md-12 col-xs-12','title'=>'* Utilice este campo para cambiar su clave actual.','placeholder'=>'Mensaje...')))
                   
                      ->getForm();    
        return $this->render('info_/contactar.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info                                                                                                                      
                                                               ));
    }
    private function sendEmail()
    {
         $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('send@example.com')
        ->setTo('gonzaloperezbarrios@hotmail.com')
        ->setBody('hola')
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'Emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    ;
    $this->get('mailer')->send($message);
    }
}
