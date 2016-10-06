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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class Sys_RegresionController extends Controller
{  
   
   /**
     * @Route("/regresion/", name="Regresion")
     */
    public function indexAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $id_usuario=$usuario->getid();  
     
        //Se crea el formulario
        $form = $this->createFormBuilder()   
                ->add('idConsulta', EntityType::class, array( 
                    'label'=>'*Utilice consultas que mustre el comportamiento de un unico producto/servicio',                  
                    'class' => 'AppBundle:Sys_ConsultaSQL',
                    'query_builder' => function (\AppBundle\Repository\Sys_ConsultaSQLRepository $er) use($id_usuario) {
                                            return $er->createQueryBuilder('p') 
                                                    ->where('p.idUsuario = :id')
                                                    ->setParameter('id', $id_usuario) ;
                                        },
                    'choice_label' => 'nombre',  
                    'label_attr' => array('class' => ''),
                    'attr' => array('class' => 'height25px col-md-6 col-xs-12')  
                ))   
                ->getForm();          

        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Regresión',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Predicciones', 
                      'subtitulo' =>'Mercado'
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
           $em = $this->getDoctrine()->getManager();
           $consulta = $em->getRepository('AppBundle:Sys_ConsultaSQL')->find($request->get('form')['idConsulta']);


           $idConexion=$consulta->getIdConexion();
           $sql=$consulta->getStringQuery(); 
           $retorno=$this->reporte($sql,$idConexion ); 
           if(empty($retorno['infoTabla']['filas']))
           {
                  $this->addFlash(
                        'advertencia',
                        'La consulta no ha arrojado datos. Recomendaciones:* Revise su consulta.* Asegurese que su base de datos tenga datos.'  
                        ); 
           }        
           else if($retorno['control']>0)
           {
               return $this->render('sys_regresion/regresion.html.twig', array(
                                                            'form' => $form->createView(),
                                                            'info'=> $info,
                                                            'infoTabla'=>$retorno['infoTabla'], 
                                                            'control'=>$retorno['control']                                                          
                                                            ));
           }
        } 

        return $this->render('sys_regresion/regresion.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info,
                                                                'infoTabla'=>null, 
                                                                'control'=>0                                                          
                                                               ));
    }   
  
  
    private function reporte($sql,$idConexion)
    { 
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $id_usuario=$usuario->getId();          
        $generico = $this->get('service_generico');  
        $filasx=$generico->newTablaToExterna($sql,$idConexion,$id_usuario); 
        if ($filasx['control']>0)
        {
            $this->addFlash('info','Reporte creado correctamente.');
        }
        else{
            $this->addFlash('error', 'Su sentencia SQL,no es correcta. Revísela y vuelva a intentarlo.');
        }       
        return $filasx;
        
    }
   
}
