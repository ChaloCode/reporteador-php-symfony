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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class Sys_ReporteController extends Controller
{
    /**
     * @Route("/reporte/", name="Reporte")
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
                      'titulo' => 'Reporte SQL',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Diseñador de Informes', 
                      'subtitulo' =>'Consulta SQL'
                    ),
                      'tabla'=>array(
                      'titulo' => 'Detalle', 
                      'subtitulo' =>'Reporte',
                      'descripcion'=>'Generado: '.$fecha
                    ),
                      'grafica'=>array(
                      'titulo' => 'Grafica', 
                      'subtitulo' =>'Genereda: '.$fecha
                    )
        );    

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        { 
           $sql=$request->get('form')['TextAreaSQL'];             
           $retorno=$this->reporte($sql );
           if(empty($retorno['infoTabla']['filas']))
           {
                  $this->addFlash(
                        'advertencia',
                        'La consulta no ha arrojado datos. Recomendaciones:* Revise su consulta.* Asegurese que su base de datos tenga datos.'  
                        ); 
           } 
           else if($retorno['control']>0)
           {
               return $this->render('sys_reporte/reporte.html.twig', array(
                                                            'form' => $form->createView(),
                                                            'info'=> $info,
                                                            'infoTabla'=>$retorno['infoTabla'], 
                                                            'control'=>$retorno['control']                                                          
                                                            ));
           }
        } 

        return $this->render('sys_reporte/reporte.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info,
                                                                'infoTabla'=>null, 
                                                                'control'=>0                                                          
                                                               ));
    }   
  
   //Este metodo se volverar generico y se llamara cargarInfo
    private function reporte($sql)
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
                $this->addFlash(
                'error',
                'Su sentencia SQL,no es correcta. Revísela y vuelva a intentarlo.'  
                ); 
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


        $this->addFlash(
                'info',
                'Reporte creado correctamente.'  
                );
                
        return array(                                                                      
                    'infoTabla'=>$infoTabla ,      
                    'control'=>5              
                    );
    }
}
