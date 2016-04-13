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

class ReporteController extends Controller
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
                                                         'attr' => array('class' => 'col-md-7 col-xs-12'))) 
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
             return $this->reporte($info, $sql );
           
        } 
          
       return $this->render('reporte/index.html.twig', array(
                                                            'form' => $form->createView(),
                                                            'info'=>$info                                                 
                                                        ));
    }
    
    
   
    private function reporte($info, $sql )
    {   
        //Se crea el formulario
         $form = $this->createFormBuilder()
            ->add('TextAreaSQL', TextareaType::class,array('label' => 'Consulta SQL *', 
                                                         'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                         'attr' => array('class' => 'col-md-7 col-xs-12'))) 
            ->getForm(); 
            
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
             return $this->redirectToRoute('Reporte');
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
       //Informacion de las paginas 
           
       $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
       $info = array('pagina'=>array(
                        'titulo' => 'Reporte SQL',
                        ),                    
                     'formulario'=>array(
                        'titulo' => 'Diseñador del Reporte', 
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
       //Data de la grafica              
       $grafica= array('x' => array(
                                        2001,
                                        2002,
                                        2003,
                                        2004,
                                        2005,
                                        2006,
                                        2007,
                                        2008,
                                        2009,
                                        2010,
                                        2011,
                                        2012,
                                        2013,
                                        2014,
                                        2015,
                                        2016,
                                        2017,
                                        2018,
                                        2019,
                                        2020,
                                        2021,
                                        2022
                                        ) ,
                      'y' => array(
                                        10,
                                        31,
                                        22,
                                        23,
                                        44,
                                        25,
                                        56,
                                        77,
                                        58,
                                        29,
                                        30,
                                        41,
                                        32,
                                        53,
                                        34,
                                        65,
                                        76,
                                        57,
                                        18,
                                        69,
                                        40,
                                        41
                                        ) ,                                        
                                        );
       
      $this->addFlash(
               'success',
               'Reporte creado correctamente.'  
             );   
       return $this->render('reporte/reporte.html.twig', array(
                                                            'form' => $form->createView(),
                                                            'info'=>$info,
                                                            'infoTabla'=>$infoTabla,               
                                                            'grafica'=>$grafica
                                                        ));
    }
}
