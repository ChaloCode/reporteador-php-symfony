<?php
namespace AppBundle\Regla;

use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ConsultasRegla 
{

    
    //Ejecuta consulta conectadonse a una BD externa.   
    public function selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname)
    {         
        $conn = DriverManager::getConnection(array(
            'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
            'driver' => $driver,
            'master' => array('user' => $user, 'port'=>$port,'password' => $password, 'host' => $host, 'dbname' => $dbname),
            'slaves' => array(
                array('user' => 'slave1', 'password', 'host' => '', 'dbname' => '')
            )
        ));        
        $conn->connect('master');        
        $stmt = $conn->prepare($sql);     
        $stmt->execute();
        $filasx = $stmt->fetchAll();         
        return $filasx;
    }   

      //carga la informacion y la muestra en un array asociativo
    public function newTablaNoSQL($filasx)
    {             
        $filas=array();
        $columnas=array();
        //Renombra las filas y columnas
        for($i=0;$i<count($filasx);$i++)
        {
            $j=0;
            foreach ($filasx[$i] as $clave => $valor) {    
                    $valor=strtolower($valor);                 
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
        return array(                                                                      
                    'infoTabla'=>$infoTabla ,      
                    'control'=>5              
                    );
    
    }  



   

}
?>