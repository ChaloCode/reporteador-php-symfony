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
use Doctrine\DBAL\DriverManager;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        
        $this->regresionLineal();
        die;
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

    private function selectDataExterna()
    {
        //Con esto se va probar las conexiones de las BD de los usuarios
        $conn = DriverManager::getConnection(array(
            'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
            'driver' => 'pdo_mysql',
            'master' => array('user' => 'root', 'port'=>'','password' => '', 'host' => '127.0.0.1', 'dbname' => 'pruebacolibri'),
            'slaves' => array(
                array('user' => 'slave1', 'password', 'host' => '', 'dbname' => '')
            )
        ));
        
        $conn->connect('master');
        $sql = "SELECT Nombre FROM prueba";
        $stmt = $conn->prepare($sql);
        //$stmt->bindValue(1, $id);
        $stmt->execute();
        $filasx = $stmt->fetchAll(); 
        
        var_dump( $filasx);
    }

}
