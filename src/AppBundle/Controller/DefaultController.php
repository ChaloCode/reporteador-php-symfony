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
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
      
        
        //Con esto se va probar las conexiones de las BD de los usuarios
        $conn = DriverManager::getConnection(array(
     'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
     'driver' => 'pdo_mysql',
     'master' => array('user' => 'root', 'password' => '', 'host' => '127.0.0.1', 'dbname' => 'pruebacolibri'),
     'slaves' => array(
         array('user' => 'slave1', 'password', 'host' => '', 'dbname' => '')
        
     )
  ));
  $conn->connect('master');
$sql = "SELECT Nombre FROM prueba";
$stmt = $conn->prepare($sql);
//$stmt->bindValue(1, $id);
$stmt->execute();
   $filasx = $stmt->fetchAll(); 
        
     
      var_dump( $filasx);
    }
}
