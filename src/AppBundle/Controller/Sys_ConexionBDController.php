<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sys_ConexionBD;
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




class Sys_ConexionBDController extends Controller
{   
    //Este metodo se volverar generico y se llamara cargarInfo
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
    
    /**
     * @Route("/conexion/", name="Crear_Conexion_BD")
     */
    public function addAction(Request $request)
    {  
        
        $sysConexionBD=new Sys_ConexionBD();         
        $form = $this->createFormBuilder($sysConexionBD) 
             ->add('nombreConexion', TextType::class,array('label' => 'Nombre de la conexión *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-7 col-xs-10')))
                                                       
            ->add('host', TextType::class,array('label' => 'Host *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('title'=>'Ejemplo: 127.0.0.1 o https://hostname','class' => 'col-md-7 col-xs-10','placeholder'=>'127.0.0.1')))
                                                
            ->add('port', IntegerType::class,array('label' => 'Port', 
                                                    'required'=>false,
                                                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                    'attr' => array('class' => 'col-md-7 col-xs-10','placeholder'=>'3306')))
           
            ->add('nameBD', TextType::class,array('label' => 'Nombre de la BD *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-7 col-xs-10')))
      
            ->add('user', TextType::class,array('label' => 'Usuario de la BD *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('placeholder'=>'root','class' => 'col-md-7 col-xs-10')))
      
            ->add('password', PasswordType::class,array('label' => 'Contraseña de la BD',
                                                        'required'=>false, 
                                                        'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                        'attr' => array('class' => 'col-md-7 col-xs-10')))  
       
             ->add('idTipoConexion', EntityType::class, array( 
                    'label'=>'Tipo conexión de la BD *',                  
                    'class' => 'AppBundle:Sys_TipoConexion',
                    'choice_label' => 'Nombre',  
                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                    'attr' => array('class' => 'height25px col-md-7 col-xs-10')                 
                ))             
            ->getForm();  
         $form->handleRequest($request);         
         if ($form->isSubmitted() && $form->isValid()) 
         {             
             $idTipoConexion=$request->get('form')['idTipoConexion'];
             
             $driver=$this->getDoctrine()
                             ->getRepository('AppBundle:Sys_TipoConexion')
                             ->find($idTipoConexion);  
             
             $user=$request->get('form')['user'];
             $port=$request->get('form')['port'];
             $password=$request->get('form')['password'];
             $host=$request->get('form')['host'];
             $dbname=$request->get('form')['nameBD'];
             $valConexion=$this->validarConexion($driver->getdriver(),$user,$port,$password,$host,$dbname);
             if( $valConexion)
             {
                //Seteamos
                $sysConexionBD->setIdTipoConexion($idTipoConexion);
                $usuario = $this->get('security.token_storage')->getToken()->getUser();                
                $sysConexionBD->setIdFosUser($usuario->getId());
                //Insert 
                $em=$this->getDoctrine()->getManager();
                $em->persist($sysConexionBD);
                $em->flush();               
                
                $this->addFlash(
                                'info',
                                'Conexión de la BD externa, creada correctamente.'  
                               ); 
                
                return $this->redirectToRoute('Crear_Conexion_BD');   
             }
             else {
                   $this->addFlash(
                                'error',
                                'No se pudo establecer conexión, con la base de datos externa.\nRevise los datos de conexión y vuelva a intentarlo.'  
                                ); 
                
             }
                             
          
        }   
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Conexiones BD Externa',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Nueva Fuente de Datos', 
                      'subtitulo' =>'Base de datos externa'
                    ),
                      'tabla_crud'=>array(
                      'titulo' => 'Conexiones', 
                      'subtitulo' =>'Base de datos externas',
                      'descripcion'=>'Generado: '.$fecha                      
                    ),
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
        ); 
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $id_usuario=$usuario->getId();  
        $sql="SELECT
                sys_conexion_bd.id,
                sys_conexion_bd.Nombre_Conexion  AS 'Nombre de la conexión',
                sys_conexion_bd.`Host`,
                sys_conexion_bd.`Port`,
                sys_conexion_bd.Nombre_BD AS 'Nombre de la BD',
                sys_conexion_bd.Usuario,
                sys_conexion_bd.`Password` AS 'Contraseña', 
                sys_tipo_conexion.Nombre AS 'Tipo conexión'
                FROM sys_conexion_bd
                INNER JOIN sys_tipo_conexion ON sys_conexion_bd.id_Tipo_Conexion=sys_tipo_conexion.id
                WHERE sys_conexion_bd.id_Fos_user=$id_usuario";  
        $retorno=$this->newTabla($sql,false);   
        $infoTabla=$retorno['infoTabla'];   
         
        if(empty($infoTabla['filas'])){
            $control=0;
        }
        else{           
            $control=$retorno['control'];
        }
        
        return $this->render('sys_conexionBD/index.html.twig', array(
                                                                    'form' => $form->createView(),
                                                                    'info'=>$info,
                                                                    'infoTabla_crud'=>$infoTabla, 
                                                                    'control'=> $control                                                          
                                                        
                                                                ));
    }
    
    private function validarConexion($driver,$user,$port,$password,$host,$dbname)
    {       
         try {
                $conn = DriverManager::getConnection(array(
                    'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
                    'driver' => $driver,
                    'master' => array(
                                        'user' => $user, 
                                        'port'=>$port,
                                        'password' => $password,
                                        'host' => $host,
                                        'dbname' => $dbname),
                    'slaves' => array(
                                        array('user' => 'slave1', 'password', 'host' => '', 'dbname' => '')
                                     )
                ));

                $conn->connect('master');
        } catch (\Exception $e) {
                return false;
        }
        return true;  
    }
    
    
    /**
     * @Route("/conexion/update/{id}", name="Actualizar_Conexion")
     */
    public function updateAction($id,Request $request)
    {
        $sysConexionBD=$this->getDoctrine()
              ->getRepository('AppBundle:Sys_ConexionBD')
              ->find($id);            
         
         $form = $this->createFormBuilder($sysConexionBD) 
             ->add('nombreConexion', TextType::class,array('label' => 'Nombre de la conexión *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))
                                                       
            ->add('host', TextType::class,array('label' => 'Host *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('title'=>'Ejemplo: 127.0.0.1 o https://hostname','class' => 'col-md-8 col-xs-12','placeholder'=>'127.0.0.1')))
                                                
            ->add('port', IntegerType::class,array('label' => 'Port', 
                                                    'required'=>false,
                                                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                    'attr' => array('class' => 'col-md-8 col-xs-12','placeholder'=>'3306')))
           
            ->add('nameBD', TextType::class,array('label' => 'Nombre de la BD *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))
      
            ->add('user', TextType::class,array('label' => 'Usuario de la BD *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('placeholder'=>'root','class' => 'col-md-8 col-xs-12')))
      
            ->add('password', PasswordType::class,array('label' => 'Contraseña de la BD',
                                                        'required'=>false, 
                                                        'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                        'attr' => array('class' => 'col-md-8 col-xs-12')))  
       
             ->add('idTipoConexion', EntityType::class, array( 
                    'label'=>'Tipo conexión de la BD *',                  
                    'class' => 'AppBundle:Sys_TipoConexion',
                    'choice_label' => 'Nombre',  
                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                    'attr' => array('class' => 'height25px col-md-8 col-xs-12')                 
                ))             
            ->getForm();  
            
         $form->handleRequest($request);
         
         if ($form->isSubmitted() && $form->isValid()) 
         {    
             $idTipoConexion=$request->get('form')['idTipoConexion'];
             
             $driver=$this->getDoctrine()
                             ->getRepository('AppBundle:Sys_TipoConexion')
                             ->find($idTipoConexion);              
                             
             $user=$request->get('form')['user'];
             $port=$request->get('form')['port'];
             $password=$request->get('form')['password'];
             $host=$request->get('form')['host'];
             $dbname=$request->get('form')['nameBD'];
             $valConexion=$this->validarConexion($driver->getdriver(),$user,$port,$password,$host,$dbname);
             if( $valConexion)
             { 
                 //Seteamos
                $sysConexionBD->setIdTipoConexion($idTipoConexion);
                //Update 
                $em=$this->getDoctrine()->getManager();             
                $em->flush();              
                
                $this->addFlash(
                                    'info',
                                    'Conexión de la BD externa, actualizada correctamente.'  
                                ); 
                            
                return $this->redirectToRoute('Crear_Conexion_BD');   
             }
             else {
                   $this->addFlash(
                                'error',
                                'No se pudo establecer conexión, con la base de datos externa.\nRevise los datos de conexión y vuelva a intentarlo.'  
                                ); 
                
             }
        } 
        
        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Conexiones BD Externa',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Actualizar Fuente de Datos', 
                      'subtitulo' =>'Base de datos externa'
                    ),
                      'tabla'=>array(
                      'titulo' => '', 
                      'subtitulo' =>'',
                      'descripcion'=>'Generado: '.$fecha                      
                    ),
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
        );       
        return $this->render('sys_conexionBD/update.html.twig', array(
                                                                    'form' => $form->createView(),
                                                                    'info'=>$info
                                                                   ));
           
    }
    
     /**
     * @Route("/conexion/delete/{id}", name="Borrar_Conexion")
     */
    public function deleteAction($id)
    {
        try {
                $em = $this->getDoctrine()->getManager();
                $conexion = $em->getRepository('AppBundle:Sys_ConexionBD')->find($id);

                if (!$conexion) {
                    $this->addFlash(
                                    'advertencia',
                                    'Esta conexión ya habia sido borrada.'  
                                   );
                }

                $em->remove($conexion);
                $em->flush();
        } catch (\Exception $e) {
                $this->addFlash(
                        'error',
                        'No se pudo borrar la conexión.\nRecargue la pagina y vuelva a intentarlo.'  
                        );
        }
          
         $this->addFlash(
                        'info',
                        'Se ha borrado con éxito la conexión.'  
                        );

        return $this->redirectToRoute('Crear_Conexion_BD');   
    }
    
  
}
