<?php

namespace Bundles\AppBundle\Entity;

use \Core\Entity\Entity;

class ExtensionEntity extends Entity {

    /**
     * @Type identifier
     */
    protected $id;

    /**
     * @Type integer
     */
    protected $age;

    /**
     * @Type string
     * @Length 255
     */
    protected $telephone;

    /**
     * @Type string
     * @Length 255
     */
    protected $adresse;

    public function getId(){ return $this->id; }

    public function getAge(){ return $this->age; }
    public function setAge($age){ $this->age = $age; return $this; }

    public function getTelephone(){ return $this->telephone; }
    public function setTelephone($telephone){ $this->telephone = $telephone; return $this; }

    public function getAdresse(){ return $this->adresse; }
    public function setAdresse($adresse){ $this->adresse = $adresse; return $this; }
}