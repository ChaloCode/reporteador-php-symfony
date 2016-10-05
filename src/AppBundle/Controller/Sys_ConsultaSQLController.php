<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sys_ConsultaSQL;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use  AppBundle\Regla\ConsultasRegla;




class Sys_ConsultaSQLController extends Controller
{   
    function __construct()
    {       
       $this->regla = new ConsultasRegla();
    }  

    private function getSys_consulta_sql($id_usuario)
    {
        $query = $this->get('service_query');  
        $filasx=$query->getConsulta($id_usuario); 
        $tablaCRUD=$this->regla->newTablaNoSQL($filasx);      
        return $tablaCRUD['infoTabla'];
    }
    
    /**
     * @Route("/consulta/", name="consultaSQL")
     */
    public function indexAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $id_usuario=$usuario->getid();
        $query = $this->get('service_query');  
        $dataConexion=$query->getConexion($id_usuario);  
        $lisConexiones=array();
        foreach ($dataConexion as $key => $value) {            
           $lisConexiones[$value['Nombre_Conexion']]=$value['id'];
        }
     
        //Se crea el formulario
        $form = $this->createFormBuilder()   
                    ->add('idConexion', ChoiceType::class, array(
                                                 'choices'  =>$lisConexiones,    
                                                 'label' => 'Conexión a la BD externa *',                                                
                                                  'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                  'attr' => array('class' => 'height25px col-md-7 col-xs-12') 
                                                 ))               
                      
                     ->add('TextAreaSQL', TextareaType::class,array('label' => 'Query SQL (generado) *', 
                                                                     'label_attr' => array('class' => 'control-label col-md-5  col-xs-12'),
                                                                      'attr' => array('class' => 'col-xs-12')   
                                                    ))    
                     ->getForm();            

        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Generador SQL',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Generador Consulta', 
                      'subtitulo' =>'Query SQL'
                    ),
                      'tabla'=>array(
                      'titulo' => 'Detalle', 
                      'subtitulo' =>'Consulta Generada',
                      'descripcion'=>'Generado: '.$fecha
                    ),
                     'tabla_crud'=>array(
                      'titulo' => 'Lista Consulta', 
                      'subtitulo' =>'Query SQL',
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
           $retorno=$this->reporte($sql,$idConexion );
           if(empty($retorno['infoTabla']['filas']))
           {              
                $this->addFlash(
                    'advertencia',
                    'La consulta no ha arrojado datos. Recomendaciones:* Revise su consulta.* Asegurese que su base de datos tenga datos.'  
                    ); 
           } 
           else 
           {
                //get all post
                $is_activo=$request->get('is_activo');
                $name_consulta=$request->get('name_consulta');
                $descripcion=$request->get('descripcion');
               
                //Instanciamos 
                $sysConsultaSQL=new Sys_ConsultaSQL(); 

                //Setiamos
                $sysConsultaSQL->setIdConexion($idConexion);
                $sysConsultaSQL->setIdUsuario($id_usuario);  
                $sysConsultaSQL->setIsActive($is_activo);          
                $sysConsultaSQL->setStringQuery($sql);
                $sysConsultaSQL->setNombre($name_consulta);
                $sysConsultaSQL->setDescripcion($descripcion);

                //Insert   
                try {             
                    $em=$this->getDoctrine()->getManager();
                    $em->persist($sysConsultaSQL);
                    $em->flush();   

                    $this->addFlash(
                        'advertencia',
                        'Consulta generada correctamente.'  
                    ); 

                } catch (\Exception $e) {
                    $this->addFlash(
                       'error',
                       'No se pudo generar la consulta.\nRecargue la pagina y vuelva a intentarlo.'  
                     );
                }

            
               return $this->render('sys_consultaSQL/index.html.twig', array(
                                                            'form' => $form->createView(),
                                                            'info'=> $info,
                                                            'infoTabla'=>$retorno['infoTabla'], 
                                                            'control'=>$retorno['control'],
                                                            'infoTabla_crud'=>$this->getSys_consulta_sql($id_usuario)                                                        
                                                            ));
                                                            
           }
        } 

        return $this->render('sys_consultaSQL/index.html.twig', array(
                                                                'form' => $form->createView(),
                                                                'info'=>$info,
                                                                'infoTabla'=>null, 
                                                                'control'=>0 ,
                                                                'infoTabla_crud'=>$this->getSys_consulta_sql($id_usuario)                                                        
                                                               ));
    }   
 
   //Este metodo se volverar generico y se llamara cargarInfo
    private function reporte($sql,$idConexion)
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
            
             
            $filasx = $this->regla->selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname);
        } catch (\Exception $e) {
                $this->addFlash(
                'error',
                'Su sentencia SQL,no es correcta. Revísela y vuelva a intentarlo.'  
                ); 
                return array(  
                            'control'=>0              
                           );
        }      
       
        return $this->regla->newTablaNoSQL($filasx);
    }


    /**
     * @Route("/consulta/update/{id}/", name="Actualizar_Consulta")
     */
    public function updateAction($id,Request $request)
    {
        $sysConsultaSQL=$this->getDoctrine()
              ->getRepository('AppBundle:Sys_ConsultaSQL')
              ->find($id);            
         
         $form = $this->createFormBuilder($sysConsultaSQL) 
             ->add('nombre', TextType::class,array('label' => 'Nombre de la conexión *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))

             ->add('descripcion', TextareaType::class,array('label' => 'Descripción *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))
                                                       
            ->add('stringQuery', TextareaType::class,array('label' => 'Query *', 
                                                'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                                                'attr' => array('class' => 'col-md-8 col-xs-12')))
            
            ->add('isActive', ChoiceType::class, array(
                    'choices'  => array(                        
                        'SI' => 1,
                        'NO' => 0,
                    ),
                    'label' => 'Activo *', 
                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                    'attr' => array('class' => 'height25px col-md-8 col-xs-12')
                ))
            
            ->add('idConexion', EntityType::class, array( 
                    'label'=>'Nombre Conexión *',                  
                    'class' => 'AppBundle:Sys_ConexionBD',
                    'choice_label' => 'nombreConexion',  
                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                    'attr' => array('class' => 'height25px col-md-8 col-xs-12')  
                     
                                                
                ))   

            ->getForm();  
            
         $form->handleRequest($request);
         
         if ($form->isSubmitted() && $form->isValid()) 
         {  
             try {
                    //Update 
                    $sysConsultaSQL->setIdConexion((int)$request->get('form')['idConexion']);
                    $em=$this->getDoctrine()->getManager();             
                    $em->flush();              
                    
                    $this->addFlash(
                                    'info',
                                    'Consulta, actualizada correctamente.'  
                                    ); 

                } catch (\Exception $e) {
                    $this->addFlash(
                       'error',
                        'No se pudo actualizar la consulta.\nRecargue la pagina y vuelva a intentarlo.'  
                    );
                }
                            
                return $this->redirectToRoute('consultaSQL');   
        } 
        
        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Generador Consulta',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Actualizar Consulta', 
                      'subtitulo' =>'SQL generado'
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
        return $this->render('sys_consultaSQL/update.html.twig', array(
                                                                    'form' => $form->createView(),
                                                                    'info'=>$info,
                                                                    'idConexion'=>$sysConsultaSQL->getIdConexion()
                                                                   ));
           
    }


    
     /**
     * @Route("/consulta/delete/{id}/", name="Borrar_Consulta")
     */
    public function deleteAction($id)
    {
        try {
                $em = $this->getDoctrine()->getManager();
                $conexion = $em->getRepository('AppBundle:Sys_ConsultaSQL')->find($id);

                if (!$conexion) {
                    $this->addFlash(
                                    'advertencia',
                                    'Esta consulta ya habia sido borrada.'  
                                   );
                }

                $em->remove($conexion);
                $em->flush();
        } catch (\Exception $e) {
                $this->addFlash(
                        'error',
                        'No se pudo borrar la consulta.\nRecargue la pagina y vuelva a intentarlo.'  
                        );
        }
          
         $this->addFlash(
                        'info',
                        'La consulta, se ha borrado con éxito.'  
                        );

        return $this->redirectToRoute('consultaSQL');   
    }

    /**
     *@Route("/consulta/getvaluetable", name="getValueTableAction")
     */
    public function getValueTableAction(Request $request)
    {

        $id=$request->get('id');   
        $em = $this->getDoctrine()->getManager();
        $dataConexion = $em->getRepository('AppBundle:Sys_ConexionBD')->getConexionBD_id($id);   
 
        $driver=$dataConexion['0']['driver'];
        $user=$dataConexion['0']['user'];
        $port=$dataConexion['0']['port'];
        $password=$dataConexion['0']['password'];
        $host=$dataConexion['0']['host'];
        $dbname=$dataConexion['0']['nameBD'];
        

        //SQL es una sentencia que se debe pasar dql, para que aplique todos los motores
        //Mysql, oracle , sql server
        //Trae los nombres de las tablas
        $sql="SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA ='$dbname'";
        $list  =  $this->regla->selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname); 
     
        return new JsonResponse($list);
    }
    
      /**
     *@Route("/consulta/getvaluerow", name="getvaluerow")
     */
    public function getvaluerowAction(Request $request)
    {
        $tables=$request->get('tables'); 
        $id=$request->get('id'); 

        $list_new=array();
        $em = $this->getDoctrine()->getEntityManager();
        $dataConexion = $em->getRepository('AppBundle:Sys_ConexionBD')->getConexionBD_id($id);   

        $driver=$dataConexion['0']['driver'];
        $user=$dataConexion['0']['user'];
        $port=$dataConexion['0']['port'];
        $password=$dataConexion['0']['password'];
        $host=$dataConexion['0']['host'];
        $dbname=$dataConexion['0']['nameBD'];

        //Se requiere pasar a sentecia dql para que se puede aplicar a cualquier motor de bd
       //Trae los nombres de las columnas.
      
        foreach ($tables as $key => $table) { 
           $sql="SELECT *  FROM $table LIMIT 1";
           $list  = $this->regla->selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname);             
           $list_new[$table]=$list[0];
        }      
     
        return new JsonResponse($list_new);
    }
  
}
