<?php

namespace Bundles\AppBundle\Entity;

use \Core\Entity\Entity;

class ProjectEntity extends Entity {

    /**
     * @Type identifier
     */
    protected $id;

    /**
     * @Type string
     * @Length 255
     */
    protected $nom;

    /**
     * @Type date
     */
    protected $date;

    public function getId(){ return $this->id;}

    public function getNom(){ return $this->nom;}
    public function setNom($nom){ $this->nom = $nom;}

    public function getDate(){ return $this->date;}
    public function setDate($date){ $this->date = $date;}
}