<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sys_Conexion_BD
 *
 * @ORM\Table(name="sys__conexion__b_d")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Sys_Conexion_BDRepository")
 */
class Sys_Conexion_BD
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
     * @ORM\Column(name="Id_Usuario", type="integer")
     */
    private $idUsuario;

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
     * @ORM\Column(name="Port", type="string", length=255)
     */
    private $port;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_Name", type="string", length=255)
     */
    private $dBName;

    /**
     * @var string
     *
     * @ORM\Column(name="User", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="Password", type="string", length=255)
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
     * Set idUsuario
     *
     * @param integer $idUsuario
     *
     * @return Sys_Conexion_BD
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
     * Set driver
     *
     * @param string $driver
     *
     * @return Sys_Conexion_BD
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
     * @return Sys_Conexion_BD
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
     * @return Sys_Conexion_BD
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
     * Set dBName
     *
     * @param string $dBName
     *
     * @return Sys_Conexion_BD
     */
    public function setDBName($dBName)
    {
        $this->dBName = $dBName;
    
        return $this;
    }

    /**
     * Get dBName
     *
     * @return string
     */
    public function getDBName()
    {
        return $this->dBName;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return Sys_Conexion_BD
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
     * @return Sys_Conexion_BD
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

