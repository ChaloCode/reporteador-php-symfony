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

use Doctrine\DBAL\DriverManager;




class Sys_RegresionController extends Controller
{   
    
   /**
     * @Route("/regresion/", name="Regresion")
     */
    public function indexAction(Request $request)
    {
        //Se crea el formulario
        $form = $this->createFormBuilder()
                     ->add('TextAreaSQL', TextareaType::class,array('label' => 'Consulta SQL *', 
                                                                    'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                                    'attr' => array('class' => 'col-md-12 col-xs-12')))  
                      ->getForm();       

        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Regresión',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Regresión', 
                      'subtitulo' =>'Calculo de predicciones'
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
           $sql=$request->get('form')['TextAreaSQL'];             
           $retorno=$this->newTabla($sql,false);
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
  
   
   //Este metodo se volvera generico y se llamara cargarInfo
  private function newTabla($sql,$msm=true)
    {
        //Data de la consulta
        //Select filas
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();         
            $statement = $connection->prepare($sql);  
            $statement->execute();
            $filasx = $statement->fetchAll(); 
        } catch (\Exception $e) {
                if($msm){
                        $this->addFlash(
                        'error',
                        'Su sentencia SQL,no es correcta. Revísela y vuelva a intentarlo.'  
                        ); 
                }
                return array(  
                            'control'=>0              
                           );
        }        
        $filas=array();
        $columnas=array();
        //Renombra las filas y columnas
        for($i=0;$i<count($filasx);$i++)
        {
            $j=0;
            foreach ($filasx[$i] as $clave => $valor) {                     
                    $filas[$i][$j]=$valor;                    
                    //Renombra las columnas
                    if($i==0)
                    {
                        $columnas[$j]=$clave;
                    }
                    $j++;
                
            } 
        }      

        //informacion de la data de la tabla
        $infoTabla=array('filas'=>$filas,
                            'columnas'=>$columnas,
                            'lengthColumnas'=>count($columnas)-1,   
                            'lengthFilas'=>count($filas)-1           
        );      

        if($msm){
            $this->addFlash(
                'info',
                'Reporte creado correctamente.'  
                );
        }    
        return array(                                                                      
                    'infoTabla'=>$infoTabla ,      
                    'control'=>5              
                    );
    
    }
   
}
