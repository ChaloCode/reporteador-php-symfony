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
       return $this->render('generico/formulario.html.twig', array(
            'form' => $form->createView(),
             'formularioTitulo' => 'Hola index'
                 ));
    }
}
