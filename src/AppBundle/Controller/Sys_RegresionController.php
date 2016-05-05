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
           if($retorno['control']>0)
           {
               return $this->render('regresion/regresion.html.twig', array(
                                                            'form' => $form->createView(),
                                                            'info'=> $info,
                                                            'infoTabla'=>$retorno['infoTabla'], 
                                                            'control'=>$retorno['control']                                                          
                                                            ));
           }
        } 

        return $this->render('regresion/regresion.html.twig', array(
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
    
     private function regresionCuadratica()
    {
        $x_array=array(35,50,65,80,95,110);	//Dias
        $y_array=array(16,26,41,62,88,119); //Porcentaje de ejecucion
        $pm=100; //Valor futuro
        
        $x=0;
        $y=0;
        $x2=0;
        $x3=0;
        $x4=0;
        $xy=0;
        $x2y=0;
        $y2=0;
        
        $n=count($y_array);        
       
        
        for($i=0;$i<$n;$i++){
            //Tabla de datos
            print ($x_array[$i]."   ".$y_array[$i]."<br>");
            //Calculo de terminos
            $x+=$x_array[$i];
            $y+=$y_array[$i];
            $x2+=pow($x_array[$i],2);
            $x3+=pow($x_array[$i],3);
            $x4+=pow($x_array[$i],4);
            $xy+=$x_array[$i]*$y_array[$i];
            $x2y+=pow($x_array[$i],2)*$y_array[$i];
            $y2+=pow($y_array[$i],2);
                
        }
        
        $b1=($xy-($x*$y/$n))*($x4-(pow($x2,2)/$n))-($x2y-($x2*$y/$n))*($x3-($x2*$x/$n));
        $b2=$x3-($x2*$x/$n);
        $b3=($x2-(pow($x,2)/$n))*($x4-(pow($x2,2)/$n))-pow($b2,2); 
        $b=$b1/$b3; 
        
        $c1=($x2-(pow($x,2)/$n))*($x2y-($x2*$y/$n))-($x3-($x2*$x/$n))*($xy-($x*$y/$n));
        $c=$c1/$b3;
        
        $a1=$y-$b*$x-$c*$x2;
        $a=$a1/$n;
       
        //Parabola tendencial
        //y=a+bx+c(x2)
        print('y='.round($a,4).'+'.round($b,4).'x+'.round($c,4).'x2');
        //$dp=round($resultado,2);
      
     }
    
    private function regresionLineal()
    {
        $xarray=array(1, 2, 3, 4, 5 );	//Dias
        $yarray=array(5, 5, 5, 6.8, 9); //Porcentaje de ejecucion
        $pm=100; //Valor futuro
        $x2=0;
        $y=0;
        $x=0;
        $xy=0;
        $cantidad=count($xarray);
        for($i=0;$i<$cantidad;$i++){
            //Tabla de datos
            print ($xarray[$i]."    ".$yarray[$i]."<br>");
            //Calculo de terminos
            $x2 += $xarray[$i]*$xarray[$i];
            $y  += $yarray[$i];
            $x  += $xarray[$i];
            $xy += $xarray[$i]*$yarray[$i];
        }
        //Coeficiente parcial de regresion
        $b=($cantidad*$xy-$x*$y)/($cantidad*$x2-$x*$x);
        //Calculo del intercepto
        $a=($y-$b*$x)/$cantidad;
        //Recta tendencial
        //y=a+bx
        print('y='.round($a,4).'+'.round($b,4).'x');
        //Proyeccion en dias para un 100% de la ejecucion:
        if ($b!=0){
             $dias_proyectados=($pm-$a)/$b;
        }
        else{
            $dias_proyectados=999999; //Infinitos
       }
       // $dp=round($dias_proyectados,0);
       
     }

  
}
