<?php

namespace Core\Entity;

use Core\Collection\OneToManyCollection;
use Core\Collection\OneToOneCollection;
use Core\ORM\getRelationEntity;

class Entity{

    private $originalDataHash;
    private $newDataHash;

    public function __construct() {
        //$this->originalDataHash = $this->dataHash();
    }

    protected function dataHash(){
        $vars = $this->getEntityVars();
        if( !empty($vars) ) {

            foreach ($vars as $k => $v){
                if($v instanceof \DateTime){
                    $vars[$k] = $vars[$k]->format('Y-m-d H:i:s');
                }elseif($v instanceof Entity){
                    $vars[$k] = $vars[$k]->getId();
                }elseif($v instanceof OneToOneCollection){
                    $vars[$k] = $vars[$k]->getElementId();
                }elseif($v instanceof OneToManyCollection){
                    $vars[$k] = $vars[$k]->getElementsId();
                }
            }

            $values = implode(', ', array_values( $vars ));
            $hash = sha1($values);

            return $hash;
        }
        return null;
    }

    public function getEntityVars(){
        $systemVars = get_class_vars( get_class() );
        $allVars = get_object_vars( $this );

        $parameters = array_diff_key($allVars, $systemVars);

        return $parameters;
    }

    public function getEntityVarsList(){
        $vars = $this->getEntityVars();
        $return = array();
        if( !empty($vars) ) {
            foreach ($vars as $k => $v){
                $return[] = $k;
            }
        }
        return $return;
    }

    public function hasBeenChanged(){
        $this->newDataHash = $this->dataHash();
        if($this->originalDataHash == $this->newDataHash){
            return false;
        }else{
            return true;
        }
    }

    public function save(){ $this->newDataHash = $this->dataHash(); return $this; }

    public function initialize(){
        $this->originalDataHash = $this->dataHash();
        return $this;
    }

    public function getDataHash(){ return $this->dataHash(); }
    public function unsetVariable($variable){ unset($this->$variable); }
    public function getPropertyValue($property){ return $this->$property; }
    public function setPropertyValue($property, $value){ $this->$property = $value; return $this; }

    public function __debugInfo()
    {
        $systemVars = get_class_vars( get_class() );
        $allVars = get_object_vars( $this );
        $parameters = array_diff_key($allVars, $systemVars);
        return $parameters;
    }
}