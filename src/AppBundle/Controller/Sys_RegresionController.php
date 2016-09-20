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
use Symfony\Component\HttpFoundation\JsonResponse;

use  AppBundle\Regla\ConsultasRegla;

class Sys_RegresionController extends Controller
{   

    function __construct()
    {       
       $this->regla = new ConsultasRegla();
    }  
    
   /**
     * @Route("/regresion/", name="Regresion")
     */
    public function indexAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $id_usuario=$usuario->getid();  
     
        //Se crea el formulario
        $form = $this->createFormBuilder()   
                ->add('idConsulta', EntityType::class, array( 
                    'label'=>'*Utilice consultas que mustre el comportamiento de un unico producto/servicio',                  
                    'class' => 'AppBundle:Sys_ConsultaSQL',
                    'query_builder' => function (\AppBundle\Repository\Sys_ConsultaSQLRepository $er) use($id_usuario) {
                                            return $er->createQueryBuilder('p') 
                                                    ->where('p.idUsuario = :id')
                                                    ->setParameter('id', $id_usuario) ;
                                        },
                    'choice_label' => 'nombre',  
                    'label_attr' => array('class' => ''),
                    'attr' => array('class' => 'height25px col-md-6 col-xs-12')  
                ))   
                ->getForm();          

        //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Regresión',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Predicciones', 
                      'subtitulo' =>'Mercado'
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
           $em = $this->getDoctrine()->getManager();
           $consulta = $em->getRepository('AppBundle:Sys_ConsultaSQL')->find($request->get('form')['idConsulta']);


           $idConexion=$consulta->getIdConexion();
           $sql=$consulta->getStringQuery();             
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
  
  
   //Este metodo se volvera generico y se llamara cargarInfo
  private function newTabla($sql,$idConexion,$msm=true)
    {      
         //Data de la consulta
        //Select filas
        try {  
            $usuario = $this->get('security.token_storage')->getToken()->getUser();
            $id_usuario=$usuario->getId();
            
            $em = $this->getDoctrine()->getManager();
            $dataConexion = $em->getRepository('AppBundle:Sys_ConexionBD')->getConexionBD($id_usuario,$idConexion);   

            $driver=$dataConexion['0']['driver'];
            $user=$dataConexion['0']['user'];
            $port=$dataConexion['0']['port'];
            $password=$dataConexion['0']['password'];
            $host=$dataConexion['0']['host'];
            $dbname=$dataConexion['0']['nameBD'];
            
             
            $filasx = $this->regla->selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname);
        
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
     *@Route("/regresion/getvaluetable", name="getValueTableAction")
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
     *@Route("/regresion/getvaluerow", name="getvaluerow")
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
        $sql="SELECT *  FROM $table LIMIT 1";
        foreach ($tables as $key => $table) { 
           $list  = $this->regla->selectDataExterna($sql,$driver,$user,$port,$password,$host,$dbname);             
           $list_new[$table]=$list[0];
        }      
     
        return new JsonResponse($list_new);
    }
   
}
