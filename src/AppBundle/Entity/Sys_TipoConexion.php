<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sys_TipoConexion
 *
 * @ORM\Table(name="sys_tipo_conexion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Sys_TipoConexionRepository")
 */
class Sys_TipoConexion
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
     * @ORM\OneToMany(targetEntity="Sys_ConexionBD", mappedBy="consulta")
     */
    private $conexiones;

    public function __construct()
    {
        $this->conexiones = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="Driver", type="string", length=255)
     */
    private $driver;

    /**
     * @var string
     *
     * @ORM\Column(name="Nombre", type="string", length=255, unique=true)
     */
    private $nombre;


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
     * Set driver
     *
     * @param string $driver
     *
     * @return Sys_TipoConexion
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    
        return $this;
    }

    /**
     * Get driver
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Sys_TipoConexion
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
     * Add conexione
     *
     * @param \AppBundle\Entity\Sys_ConexionBD $conexione
     *
     * @return Sys_TipoConexion
     */
    public function addConexione(\AppBundle\Entity\Sys_ConexionBD $conexione)
    {
        $this->conexiones[] = $conexione;
    
        return $this;
    }

    /**
     * Remove conexione
     *
     * @param \AppBundle\Entity\Sys_ConexionBD $conexione
     */
    public function removeConexione(\AppBundle\Entity\Sys_ConexionBD $conexione)
    {
        $this->conexiones->removeElement($conexione);
    }

    /**
     * Get conexiones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConexiones()
    {
        return $this->conexiones;
    }
}
