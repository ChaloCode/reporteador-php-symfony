<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sys_ConsultaSQL
 *
 * @ORM\Table(name="sys_consulta_sql")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Sys_ConsultaSQLRepository")
 */
class Sys_ConsultaSQL
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    
    /**
     * @var int
     *
     * @ORM\Column(name="id_conexion", type="integer")
     */
    private $idConexion;

    /**
     * @var int
     *
     * @ORM\Column(name="id_usuario", type="integer")
     */
    private $idUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="string_query", type="text")
     */
    private $stringQuery;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUsuario
     *
     * @param integer $idUsuario
     *
     * @return Consulta_SQL
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    
        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return integer
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Consulta_SQL
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set stringQuery
     *
     * @param string $stringQuery
     *
     * @return Consulta_SQL
     */
    public function setStringQuery($stringQuery)
    {
        $this->stringQuery = $stringQuery;
    
        return $this;
    }

    /**
     * Get stringQuery
     *
     * @return string
     */
    public function getStringQuery()
    {
        return $this->stringQuery;
    }

      /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Consulta_SQL
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Consulta_SQL
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set idConexion
     *
     * @param integer $idConexion
     *
     * @return Consulta_SQL
     */
    public function setIdConexion($idConexion)
    {
        $this->idConexion = $idConexion;
    
        return $this;
    }

    /**
     * Get idConexion
     *
     * @return integer
     */
    public function getIdConexion()
    {
        return $this->idConexion;
    }
}

