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

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
         $form = $this->createFormBuilder()
        
            ->add('Texto', TextType::class,array('label' => 'Texto *', 
                                                'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                'attr' => array('class' => 'col-md-7 col-xs-12')))
            ->add('Numero', IntegerType::class,array('label' => 'Numero *', 
                                                    'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                    'attr' => array('class' => 'col-md-7 col-xs-12')))
           
            ->add('TextoArea', TextareaType::class,array('label' => 'Text Area *', 
                                                         'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                         'attr' => array('class' => 'col-md-7 col-xs-12')))
            
       
            ->add('isAttending', ChoiceType::class, array(
                                                    'choices'  => array(
                                                                        'Maybe' => null,
                                                                        'Yes' => true,
                                                                        'No' => false,
                                                                        ),    
                                                 'label' => 'Check List *',                                                
                                                 'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                 'attr' => array('class' => 'col-md-7 col-xs-12')
                                                 )) 
             ->add('isAttending33', ChoiceType::class, array(
                                                    'choices'  => array(
                                                                        'Maybe' => null,
                                                                        'Yes' => true,
                                                                        'No' => false,
                                                                        ),    
                                                 'label' => 'Check List *',                                                
                                                 'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                 'attr' => array('class' => 'select2_single col-md-7 col-xs-12')
                                                 ))                                                                                    
            ->add('public', CheckboxType::class, array(                                                
                                                'required' => false,
                                                 'label' => 'Single Check *',                                                 
                                                 'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                 'attr' => array('class' => 'flat')
                                                 )) 
                                                 
         
            ->add('attending2', ChoiceType::class, array(
                                                    'choices' => array(
                                                        'Yes' => true,
                                                        'No' => false,
                                                        'Maybe' => null,
                                                    ),
                                                    'choices_as_values' => true,
                                                    'expanded' => true,
                                                    'multiple' => false,
                                                    'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                                    'choice_attr' => function($val, $key, $index) {
                                                        // adds a class like attending_yes, attending_no, etc
                                                        return ['class' => 'flat'];
                                                    },
                                                ))                 
                             
                                                              
            ->add('Fecha', TextType::class,array('label' => 'Fecha *',
                                            'label_attr' => array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'),
                                            'attr' => array('class' => 'date-picker-control col-md-7 col-xs-12')
                                            ))   
                                                 
                    // ->add('cancelar', ResetType::class, array('label' => 'Cancelar','attr' => array('class' => 'btn btn-success')))
            
            ->getForm(); 
        // replace this example code with whatever you need
         $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("
                        SELECT                        
                        prueba.Nombre AS '0',
                        prueba.Sexo AS '1',
                        prueba.Cargo AS '2',
                        prueba.Edad AS '3',
                        prueba.Salario AS '4'
                        FROM
                        prueba
                        ");  
        $statement->execute();
        $constantes = $statement->fetchAll();  
        $columnas = array('Nombre',
                        'Sexo',
                        'Cargo',
                        'Edad',
                        'Salario' ); 
       $info = array('paginaTitulo' => 'Tabla generica', 
                      'tablaTitulo' =>'Tabla generica',
                      'tablaSubTitulo' => 'Data por SQL',
                      'tablaInfo' =>'Esta tabla puede ser cargada dinamicamente por cualquier sentecia SQL (o un array asociativo) siempre cuando los nombres del SELECT SQL esten enumerodados de cero en adelante (0,1,2,3..)' 
                     );    
                     
       $grafica= array('year' => array(2001,
                                        2002,
                                        2003,
                                        2004,
                                        2005,
                                        2006,
                                        2007,
                                        2008,
                                        2009,
                                        2010,
                                        2011,
                                        2012,
                                        2013,
                                        2014,
                                        2015,
                                        2016,
                                        2017,
                                        2018,
                                        2019,
                                        2020,
                                        2021,
                                        2022
                                        ) ,
                      'value' => array(20,
                                        21,
                                        22,
                                        23,
                                        24,
                                        25,
                                        26,
                                        27,
                                        28,
                                        29,
                                        30,
                                        31,
                                        32,
                                        33,
                                        34,
                                        35,
                                        36,
                                        37,
                                        38,
                                        39,
                                        40,
                                        41

                                        ) ,
                                        
                                        );
       return $this->render('default/index.html.twig', array(
            'form' => $form->createView(),
             'formularioTitulo' => 'Hola index',
             'columnas'=>$columnas,
                                                               'filas' =>$constantes,
                                                               'numfila'=>count($constantes)-1,
                                                               'numcolumna'=>count($columnas)-1,
                                                               'info'=>$info,
                                                               'grafica'=>$grafica
                 ));
    }
}
