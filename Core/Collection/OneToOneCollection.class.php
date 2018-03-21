<?php

namespace Core\Collection;

use Core\ORM\getRelationEntity;

class OneToOneCollection{

    private $className;
    private $classObj;
    private $element;
    private $propertyName;

    private $elementToUpdate;

    public function __construct($propertyName, $parentElement) {
        $this->className = get_class($parentElement);
        $this->classObj = $parentElement;
        $this->propertyName = $propertyName;
    }

    private function loadElementFromDatabase(){
        if( $this->element != null ){ return false; }

        $getEntity = new getRelationEntity($this->className, $this->classObj);
        $this->element = $getEntity->getData($this->propertyName);
    }

    public function get() {
        $this->loadElementFromDatabase();
        return $this->element;
    }

    public function set($element) {
        $this->loadElementFromDatabase();
        $this->element = $element;
    }

    public function getElementId(){
        return ($this->element == null) ? null : $this->element->getId();
    }

    public function __debugInfo() {
        return ['element' => $this->element] ?? ['element' => 'null'];
    }

    public function __toString() {
        return $this->element->getId();
    }

}