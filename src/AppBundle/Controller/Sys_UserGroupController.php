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
use AppBundle\Entity\User;


class Sys_UserGroupController extends Controller
{ 
     
     /**
     * @Route("/usergrupo/", name="adminroluser")
     */
      public function indexction(Request $request)
    {          
         
        $form = $this->createFormBuilder() 
            ->add('idUsuario', EntityType::class, array( 
                    'label'=>'Usuario *',                                 
                    'class' => 'AppBundle:User',
                    'choice_label' => 'username',  
                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                    'attr' => array('class' => 'height25px col-md-7 col-xs-10')                 
                ))  
            
            ->add('idGroup', EntityType::class, array( 
                    'label'=>'Rol *',                                 
                    'class' => 'AppBundle:Group',
                    'choice_label' => 'name',  
                    'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                    'attr' => array('class' => 'height25px col-md-7 col-xs-10')                 
                ))  
                         
            ->getForm();  
         $form->handleRequest($request);         
         if ($form->isSubmitted() && $form->isValid()) 
         {      
            $control=true;                   
            try { 
                $idUsuario=$request->get('form')['idUsuario'];
                $idGroup=$request->get('form')['idGroup'];  
                $sql="INSERT INTO fos_user_user_group (user_id, group_id) VALUES ('$idUsuario', '$idGroup')";
                $em = $this->getDoctrine()->getEntityManager();
                $connection = $em->getConnection();         
                $statement = $connection->prepare($sql);  
                $statement->execute();                
            } catch (\Exception $e) {               
                if($e->getErrorCode()==1062)
                {
                    $control=false;  
                    $msmError='No se pudo guardar.\nEl usuario ya tiene asignado este rol.';
                }
            }   
                            
            if($control){
                $this->addFlash(
                            'info',
                            'Usuario relacionado con éxito.'  
                            ); 
            }  
            else{
                 $this->addFlash('error',$msmError); 
            }  
            
            
            return $this->redirectToRoute('adminroluser');   
           
            
                             
          
        }   
         //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Asignar Rol',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Relacionar', 
                      'subtitulo' =>'Usuario y Rol'
                    ),
                      'tabla_crud'=>array(
                      'titulo' => 'Usuarios y Roles', 
                      'subtitulo' =>'Autorización sistema',
                      'descripcion'=>''                     
                    ),  
                    'tabla'=>array(
                      'titulo' => 'Detalle', 
                      'subtitulo' =>'Roles',
                      'descripcion'=>''
                    ),                 
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
        ); 
       
        $sql="SELECT  name AS 'Rol', roles AS 'Permiso' FROM fos_group";  
        $getTabla = $this->get('service_generico');  
        $retorno=$getTabla->newTabla($sql,false);          
        $infoTabla2=$retorno['infoTabla']; 
        foreach ($infoTabla2['filas'] as $key => $value) {  
           $a=unserialize($infoTabla2['filas'][$key][1]);
           $text='registrado';
           if(!empty($a)){
               foreach ($a as $key2 => $value2) {
                   $value2=explode("_", $value2);
                   $text.=' '.$value2[1];
               }             
           }
           $infoTabla2['filas'][$key][1]=$text;
        } 
        $sql="SELECT
                CONCAT(mae_grupo.group_id,'-', mae_grupo.user_id) AS id,
                usuario.username AS 'Usuario',
                grupo.name AS 'Rol'
            FROM
                fos_user_user_group AS mae_grupo
            INNER JOIN fos_user AS usuario ON usuario.id = mae_grupo.user_id
            INNER JOIN fos_group AS grupo ON grupo.id = mae_grupo.group_id";  
        $r=$getTabla->newTabla($sql,false);        
        $infoTabla=$r['infoTabla'];  
     
         
        if(empty($infoTabla['filas'])){
            $control=0;
        }
        else{           
            $control=$retorno['control'];
        }
        
        return $this->render('sys_usergroup/index.html.twig', array(
                                                                    'form' => $form->createView(),
                                                                    'info'=>$info,
                                                                    'infoTabla_crud'=>$infoTabla,                                                                     
                                                                    'infoTabla'=>$infoTabla2,
                                                                    'control'=> $control                                                          
                                                        
                                                                ));
    }


    /**
     * @Route("/usergrupo/delete/{id}/", name="BorrarUserGrupo")
     */
    public function deleteAction($id)
    {
        $control=true;
        $msmError='No se pudo borrar relacion usurio y rol.\nRecargue la pagina y vuelva a intentarlo.' ;
        try {
                $array=explode("-", $id);
                $idUsuario=$array[1];
                $idGroup=$array[0];  
                $sql="DELETE FROM fos_user_user_group WHERE user_id=$idUsuario AND group_id=$idGroup";
                $em = $this->getDoctrine()->getEntityManager();
                $connection = $em->getConnection();         
                $statement = $connection->prepare($sql);  
                $a= $statement->execute();  
                if (!$a) {
                    $this->addFlash(
                                    'advertencia',
                                    'Esta relacion usuario y rol, ya habia sido borrada.'  
                                    );
                }  
        } catch (\Exception $e) {  
            $control=false;
        }

        if($control){
        $this->addFlash(
                        'info',
                        'Se ha borrado con éxito el rol.'  
                        );
        }
        else{
            $this->addFlash('error',$msmError);

        }   

        return $this->redirectToRoute('adminroluser');   
      
    }


     /**
     * @Route("/usergrupo/update/{id}", name="ActualizarUserGrupo")
     */
    public function updateAction($id,Request $request)
    {      
        $array=explode("-", $id);
        $idUsuario2=$array[1];
        $idGroup2=$array[0];     
       

        $em = $this->getDoctrine()->getManager();
        $grupo = $em->getRepository('AppBundle:Group')->find($idGroup2);   
        $form = $this->createFormBuilder() 
                     ->add('idUsuario', EntityType::class, array( 
                            'label'=>'Usuario *',                                 
                            'class' => 'AppBundle:User',
                            'choice_label' => 'username',  
                            'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                            'attr' => array('class' => 'height25px col-md-7 col-xs-10')                 
                         )) 
                     ->add('idGroup', EntityType::class, array( 
                            'label'=>'Rol *',                                 
                            'class' => 'AppBundle:Group',
                            'choice_label' => 'name',  
                            'label_attr' => array('class' => 'control-label col-md-4 col-sm-4 col-xs-12'),
                            'attr' => array('class' => 'height25px col-md-7 col-xs-10')                 
                         ))       
                     ->getForm();             
         $form->handleRequest($request);         
         if ($form->isSubmitted() && $form->isValid()) 
         {                                      
            $control=true;                   
            try {         
                $idUsuario=$request->get('form')['idUsuario'];
                $idGroup=$request->get('form')['idGroup'];          
                $sql="UPDATE fos_user_user_group SET user_id='$idUsuario', group_id='$idGroup' WHERE user_id='$idUsuario2' AND group_id='$idGroup2'";
                $em = $this->getDoctrine()->getEntityManager();
                $connection = $em->getConnection();         
                $statement = $connection->prepare($sql);  
                $statement->execute();                
            } catch (\Exception $e) { 
                    $control=false;  
                    $msmError='No se pudo actualizar la relación usuario y rol.';
            }   
                            
            if($control){
                $this->addFlash(
                            'info',
                            'Se actualizado usuario y rol con éxito.'  
                            ); 
            }  
            else{
                 $this->addFlash('error',$msmError); 
            }  
            
            
            return $this->redirectToRoute('adminroluser');    
            
        } 
        
           //Informacion de las paginas            
        $fecha=strftime("El día, %d del mes %m del %Y %H:%M");		
        $info = array('pagina'=>array(
                      'titulo' => 'Asignar Rol',
                    ),                    
                      'formulario'=>array(
                      'titulo' => 'Actualizar', 
                      'subtitulo' =>'Usuario y Rol'
                    ),
                      'tabla_crud'=>array(
                      'titulo' => 'Usuarios y Roles', 
                      'subtitulo' =>'Autorización sistema',
                      'descripcion'=>''                     
                    ),  
                    'tabla'=>array(
                      'titulo' => 'Detalle', 
                      'subtitulo' =>'Roles',
                      'descripcion'=>''
                    ),                 
                      'grafica'=>array(
                      'titulo' => '', 
                      'subtitulo' =>': '.$fecha
                    )
        ); 
       
        $sql="SELECT  name AS 'Rol', roles AS 'Permiso' FROM fos_group"; 
        $getTabla = $this->get('service_generico');  
        $retorno=$getTabla->newTabla($sql,false);   
        $infoTabla2=$retorno['infoTabla']; 

        foreach ($infoTabla2['filas'] as $key => $value) {  
           $a=unserialize($infoTabla2['filas'][$key][1]);
           $text='registrado';
           if(!empty($a)){
               foreach ($a as $key2 => $value2) {
                   $value2=explode("_", $value2);
                   $text.=' '.$value2[1];
               }             
           }
           $infoTabla2['filas'][$key][1]=$text;
        } 
     
           
           
                    
        return $this->render('sys_usergroup/update.html.twig', array(
                                                                    'form' => $form->createView(),
                                                                    'info'=>$info,
                                                                    'infoTabla'=>$infoTabla2, 
                                                                    'userOld'=>$idUsuario2,
                                                                    'rolOld'=>$idGroup2
                                                                   ));
           
    }

}
