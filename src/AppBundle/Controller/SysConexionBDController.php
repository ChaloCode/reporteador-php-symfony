<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\SysConexionBD;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;






class SysConexionBDController extends Controller
{
    /**
     * @Route("/conexion/", name="ConexionBD")
     */
    public function tablaAction(Request $request)
    { 
          //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Conexiones BD Externa',
                    ),                    
                      'tabla'=>array(
                      'titulo' => 'Conexiones', 
                      'subtitulo' =>'Base de datos externas',
                      'descripcion'=>'Generado: '.$fecha
                    )
        );  
        
          $sql="SELECT * FROM  sys_conexion_bd";  
          $retorno=$this->newTabla($sql,false);
          return $this->render('sysconexionbd/index.html.twig', array(                                                            
                                                            'info'=> $info,
                                                            'infoTabla'=>$retorno['infoTabla'], 
                                                            'control'=>$retorno['control']                                                          
                                                            ));
        
    
       
        
    }
    
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
                'success',
                'Reporte creado correctamente.'  
                );
        }    
        return array(                                                                      
                    'infoTabla'=>$infoTabla ,      
                    'control'=>5              
                    );
    
    }
            
      /**
     * @Route("/constantes/list/{id}", name="Detalle_Constantes")
     */
    public function listAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT
                        constantes.id,
                        constantes.Nombre AS nombre,
                        constantes.Utilidad AS utilidad,
                        constantes.Plazo AS plazo,
                        constantes.Dias_Clavo AS diasClavo,
                        constantes.id_Oficina AS idOficina,                        
                        oficina.Nombre_Oficina AS nombreOficina
                        FROM
                        constantes 
                        INNER JOIN oficina ON constantes.id_Oficina=oficina.id
                        WHERE constantes.id=:id");            
        $statement->bindValue('id', $id);
        $statement->execute();
        $constante = $statement->fetchAll();        
        return $this->render('constantes/list.html.twig', array('constante' =>$constante['0']));
    }
    
    /**
     * @Route("/conexion/add/", name="Crear_Conexion_BD")
     */
    public function addAction(Request $request)
    {  
        $sysConexionBD=new SysConexionBD();         
        $form = $this->createFormBuilder($sysConexionBD)        
            ->add('host', TextType::class,array('label' => 'Host *', 
                                                'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                'attr' => array('title'=>'Ejemplo: 127.0.0.1 o https://hostname','class' => 'col-md-7 col-xs-12','placeholder'=>'127.0.0.1','pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}')))
                                                
            ->add('port', IntegerType::class,array('label' => 'Port *', 
                                                    'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                    'attr' => array('class' => 'col-md-7 col-xs-12','placeholder'=>'3306')))
           
            ->add('nameBD', TextType::class,array('label' => 'Nombre de la BD *', 
                                                'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-7 col-xs-12')))
      
            ->add('user', TextType::class,array('label' => 'Usuario de la BD *', 
                                                'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                'attr' => array('placeholder'=>'root','class' => 'col-md-7 col-xs-12')))
      
            ->add('password', PasswordType::class,array('label' => 'Contraseña de la BD *', 
                                                        'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                        'attr' => array('class' => 'col-md-7 col-xs-12')))  
       
            ->add('driver', ChoiceType::class, array(
                                                    'choices'  => array(
                                                                        'MySQL' => 'pdo_mysql',
                                                                        'SQLite' => 'pdo_sqlite',
                                                                        'PostgreSQL' => 'pdo_pgsql',
                                                                        'Oracle' => 'pdo_oci'                                                                       
                                                                        ),    
                                                 'label' => 'Tipo conexión de BD *',                                                
                                                 'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                 'attr' => array('class' => 'col-md-7 col-xs-12')
                                                 )) 
            
            ->getForm();  
         $form->handleRequest($request);         
         if ($form->isSubmitted() && $form->isValid()) 
         {             
             
            //Insert 
            $em=$this->getDoctrine()->getManager();
            $em->persist($sysConexionBD);
            $em->flush();               
            //return $this->redirectToRoute('Constantes');
             $this->addFlash(
                'success',
                'Conexión de la BD externa, creada correctamente.'  
                );                     
          
        }   
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Nueva Conexion',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Nueva Fuente de Datos', 
                      'subtitulo' =>'Base de datos externa'
                    ),
                      'tabla'=>array(
                      'titulo' => '', 
                      'subtitulo' =>'',
                      'descripcion'=>': '.$fecha
                    ),
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
        );           
        return $this->render('sysconexionbd/add.html.twig', array(
                                                                    'form' => $form->createView(),
                                                                    'info'=>$info
                                                                ));
    }
    
    
    /**
     * @Route("/constantes/update/{id}/{id_ofi}", name="Actualizar_Constantes")
     */
    public function updateAction($id,$id_ofi,Request $request)
    {
        $constante=$this->getDoctrine()
              ->getRepository('AppBundle:Constantes')
              ->find($id);            
         
        $form = $this->createFormBuilder($constante)
            ->add('nombre', TextType::class)  
            ->add('utilidad', IntegerType::class)
            ->add('plazo', IntegerType::class)
            ->add('diasClavo', IntegerType::class)                   
            ->add('idOficina', EntityType::class, array( 
                    'label'=>'Oficina',                  
                    'class' => 'AppBundle:Oficina',
                    'choice_label' => 'nombreOficina',                   
                ))        
            ->add('save', SubmitType::class, array('label' => 'Editar Constante','attr' => array('class' => 'ui-widget-header ui-corner-all editar')))
            ->getForm();
            
         $form->handleRequest($request);
         
         if ($form->isSubmitted() && $form->isValid()) 
         {  
             $constante->setIdOficina((int)$_POST['form']['idOficina']);             
             $em=$this->getDoctrine()->getManager();             
             $em->flush();              
             
             $this->addFlash(
               'notice',
               'Actualizado.'  
             );         
            return $this->redirectToRoute('Constantes');
        } 
             
        return $this->render('constantes/update.html.twig', array(
            'form' => $form->createView(),'oficina'=>$id_ofi
        ));
           
    }
    
     /**
     * @Route("/constantes/delete/{id}", name="Borrar_Constantes")
     */
    public function deleteOficinaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $oficina = $em->getRepository('AppBundle:Constantes')->find($id);

        if (!$oficina) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $em->remove($oficina);
        $em->flush();

        return $this->redirectToRoute('Constantes');
    }
    
      /**
     * @Route("/capas/consulta/{id}", name="Query_Capa")
     */
    public function consultaCapaAction($id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT
            c
            FROM AppBundle:Capa c
            WHERE c.colorCapa=:price'  
        )->setParameter('price', $id);

        $capas = $query->getResult();
        //var_dump($products);
        //die('fin query');
        return $this->render('capa/index.html.twig', array('capas' =>$capas));
    }
    
  
}
