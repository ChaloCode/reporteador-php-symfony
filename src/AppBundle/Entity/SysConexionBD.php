<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SysConexionBD
 *
 * @ORM\Table(name="sys_conexion_bd")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SysConexionBDRepository")
 */
class SysConexionBD
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
     * @var string
     *
     * @ORM\Column(name="Nombre_Conexion", type="string", length=255)
     */
    private $nombreConexion;    

    /**
     * @var string
     *
     * @ORM\Column(name="Driver", type="string", length=255)
     */
    private $driver;

    /**
     * @var string
     *
     * @ORM\Column(name="Host", type="string", length=255)
     */
    private $host;

    /**
     * @var string
     *
     * @ORM\Column(name="Port", type="string", length=255, nullable=true)
     */
    private $port;

    /**
     * @var string
     *
     * @ORM\Column(name="Nombre_BD", type="string", length=255)
     */
    private $nameBD;

    /**
     * @var string
     *
     * @ORM\Column(name="Usuario", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="Password", type="string", length=255, nullable=true)
     */
    private $password;


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
     * @return SysConexionBD
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
     * Set host
     *
     * @param string $host
     *
     * @return SysConexionBD
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param string $port
     *
     * @return SysConexionBD
     */
    public function setPort($port)
    {
        $this->port = $port;
    
        return $this;
    }

    /**
     * Get port
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }


    /**
     * Set nameBD
     *
     * @param string $nombreConexion
     *
     * @return SysConexionBD
     */
    public function setNombreConexion($nombreConexion)
    {
        $this->nombreConexion = $nombreConexion;
    
        return $this;
    }

    /**
     * Get nombreConexion
     *
     * @return string
     */
    public function getNombreConexion()
    {
        return $this->nombreConexion;
    }
    
    /**
     * Set nameBD
     *
     * @param string $nameBD
     *
     * @return SysConexionBD
     */
    public function setNameBD($nameBD)
    {
        $this->nameBD = $nameBD;
    
        return $this;
    }

    /**
     * Get nameBD
     *
     * @return string
     */
    public function getNameBD()
    {
        return $this->nameBD;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return SysConexionBD
     */
    public function setUser($user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return SysConexionBD
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}

