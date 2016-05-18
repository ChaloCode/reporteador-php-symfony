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
         $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $id_usuario=$usuario->getid();
        
        $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();         
            $statement = $connection->prepare("SELECT
                                            sys_conexion_bd.id,
                                            sys_conexion_bd.Nombre_Conexion
                                            FROM `sys_conexion_bd`
                                            WHERE sys_conexion_bd.id_Fos_user=:id");  
            $statement->bindValue('id', $id_usuario);
            $statement->execute();
            $dataConexion = $statement->fetchAll(); 

       
        $lisConexiones=array();
        foreach ($dataConexion as $key => $value) {            
           $lisConexiones[$value['Nombre_Conexion']]=$value['id'];
        }
        //Se crea el formulario
        $form = $this->createFormBuilder()
                     ->add('idConexion', ChoiceType::class, array(
                                                 'choices'  =>$lisConexiones,    
                                                 'label' => 'Conexión a la BD externa *',                                                
                                                 'label_attr' => array('class' => 'control-label col-md-4 col-sm-3 col-xs-12'),
                                                 'attr' => array('class' => 'col-md-12 col-xs-12')
                                                 ))               
                      
                     ->add('TextAreaSQL', TextareaType::class,array('label' => 'Consulta SQL *', 
                                                                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-3 col-xs-12'),
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
           $idConexion=$request->get('form')['idConexion'];
           $sql=$request->get('form')['TextAreaSQL'];             
           $retorno=$this->newTabla($sql,$idConexion,false);
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
  
    private function selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname)
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
   //Este metodo se volvera generico y se llamara cargarInfo
  private function newTabla($sql,$idConexion,$msm=true)
    {      
         //Data de la consulta
        //Select filas
        try {  
            $usuario = $this->get('security.token_storage')->getToken()->getUser();
            $id_usuario=$usuario->getId();
            
            $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();         
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
