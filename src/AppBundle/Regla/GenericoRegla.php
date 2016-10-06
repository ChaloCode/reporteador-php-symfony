<?php

namespace AppBundle\Regla;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;


class GenericoRegla extends Controller
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
   //carga la informacion y la muestra en un array asociativo
    public function newTabla($sql,$msm=true)
    {
        //Data de la consulta
        //Select filas
        try {           
            $connection = $this->em->getConnection();        
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

     
    public function newTablaToExterna($sql,$idConexion,$id_usuario)
    {           
        //Data de la consulta
        //Select filas
        try {  
            $connection = $this->em->getConnection();  
            $statement = $connection->prepare("SELECT
                                                sys_conexion_bd.`Host`,
                                                sys_conexion_bd.`Port`,
                                                sys_conexion_bd.Nombre_BD,
                                                sys_conexion_bd.Usuario,
                                                sys_conexion_bd.`Password` ,
                                                sys_tipo_conexion.Driver AS Driver
                                                FROM `sys_conexion_bd`
                                                INNER JOIN sys_tipo_conexion ON sys_tipo_conexion.id=sys_conexion_bd.id_Tipo_Conexion
                                                WHERE sys_conexion_bd.id_Fos_user=:id
                                                AND sys_conexion_bd.id=:id_conexion");  
            $statement->bindValue('id', $id_usuario);
            $statement->bindValue('id_conexion', $idConexion);
            $statement->execute();
            $dataConexion = $statement->fetchAll();  
            $driver=$dataConexion['0']['Driver'];
            $user=$dataConexion['0']['Usuario'];
            $port=$dataConexion['0']['Port'];
            $password=$dataConexion['0']['Password'];
            $host=$dataConexion['0']['Host'];
            $dbname=$dataConexion['0']['Nombre_BD'];            
             
            $filasx = $this->selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname);
        } catch (\Exception $e) {            
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
        return array(                                                                      
                    'infoTabla'=>$infoTabla ,      
                    'control'=>5              
                    );
    }

}
