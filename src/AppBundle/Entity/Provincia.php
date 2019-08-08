<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provincia
 *
 * @ORM\Table(name="provincia", indexes={@ORM\Index(name="pais_id", columns={"pais_id"})})
 * @ORM\Entity
 */
class Provincia
{
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=100, nullable=false)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="abrev", type="string", length=10, nullable=false)
     */
    private $abrev;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Pais
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pais_id", referencedColumnName="id")
     * })
     */
    private $pais;

 /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Provincia
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }


     /**
     * Set abrev
     *
     * @param string $abrev
     *
     * @return Provincia
     */
    public function setAbrev($abrev) {
        $this->abrev = $abrev;

        return $this;
    }

    /**
     * Get abrev
     *
     * @return string
     */
    public function getAbrev() {
        return $this->abrev;
    }
   

     /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Provincia
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo() {
        return $this->activo;
    }

     /**
     * Set pais
     *
     * @param integer $pais
     *
     * @return Provincia
     */
    public function setPais($pais) {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais
     *
     * @return integer
     */
    public function getPais() {
        return $this->pais;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
   
}

