<?php

namespace AppBundle\Regla;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;


class QuerysRegla extends Controller
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getConexion($id_usuario)
    {
        $connection = $this->em->getConnection();         
        $statement = $connection->prepare("SELECT
                                        sys_conexion_bd.id,
                                        sys_conexion_bd.Nombre_Conexion
                                        FROM `sys_conexion_bd`
                                        WHERE sys_conexion_bd.id_Fos_user=:id");  
        $statement->bindValue('id', $id_usuario);
        $statement->execute();
        return $dataConexion = $statement->fetchAll();
    }

    public function getConsulta($id_usuario)
    {        
        $connection = $this->em->getConnection();         
        $statement = $connection->prepare("SELECT
                                                sys_consulta_sql.id,
                                                sys_consulta_sql.nombre AS 'Nombre',
                                                sys_consulta_sql.string_query AS 'Query',
                                                sys_consulta_sql.descripcion AS 'Descripción',
                                                sys_conexion_bd.Nombre_Conexion AS 'Nombre Conexión',
                                                CASE sys_consulta_sql.is_active 
                                                    WHEN 1 THEN 'SI'
                                                    WHEN 0 THEN 'NO'
                                                    ELSE 0
                                                    END AS 'Activo'
                                        FROM sys_consulta_sql
                                        INNER JOIN sys_conexion_bd ON sys_conexion_bd.id= sys_consulta_sql.id_conexion
                                        WHERE sys_consulta_sql.id_usuario=:id");  
        $statement->bindValue('id', $id_usuario);
        $statement->execute();
        return $filasx = $statement->fetchAll(); 
    }

    public function getConexionExterna($id_usuario,$idConexion)
    {         
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
        return $dataConexion = $statement->fetchAll();  
    }

}
