<?php

namespace Core\Collection;

use Core\ORM\getRelationEntity;

class OneToManyCollection{

    private $className;
    private $classObj;
    private $elements;
    private $propertyName;

    private $elementToAdd;
    private $elementToRemove;

    public function __construct($propertyName, $parentElement) {
        $this->className = get_class($parentElement);
        $this->classObj = $parentElement;
        $this->propertyName = $propertyName;
    }

    public function getElementToAdd(){ return $this->elementToAdd; }
    public function getElementToRemove(){ return $this->elementToRemove; }

    private function loadElementFromDatabase(){
        if( $this->elements != null ){ return false; }

        $getEntity = new getRelationEntity($this->className, $this->classObj);
        $data = $getEntity->getData($this->propertyName);;
        $this->elements = $data;
        $this->dbElements = $data;
    }





    public function add($element) {
        $this->loadElementFromDatabase();
        if(is_array($element)){
            foreach ($element as $e){
                $this->elementToAdd[] = $e;
                $this->elements[] = $e;
            }
        }else{
            $this->elementToAdd[] = $element;
            $this->elements[] = $element;
        }

        return $this;
    }

    public function remove($element) {
        $this->loadElementFromDatabase();
        if(is_array($element)){
            foreach ($element as $e){
                $key = $this->search($e, $this->elements);
                if($key !== false){
                    $this->elementToRemove[] = $e;
                    unset($this->elements[$key]);
                }
            }
        }else{
            $key = $this->search($element, $this->elements);
            if($key !== false){
                $this->elementToRemove[] = $element;
                unset($this->elements[$key]);
            }
        }

        return $this;
    }

    public function search($element, $list){
        if(count($list) > 0){
            foreach ($list as $k => $e){
                if($e == $element){
                    return $k;
                }
            }
        }
        return false;
    }

    public function contains($element) {
        return in_array($element, $this->elements, true);
    }

    public function get($key) {
        return isset($this->elements[$key]) ? $this->elements[$key] : null;
    }

    public function containsKey($key) {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    public function getElementsId(){
        if($this->elements != null){
            foreach ($this->elements as $e){
                $return[] = $e->getId();
            }
            return $return;
        }

        return null;
    }

    public function getDatabaseElementsId(){
        if($this->dbElements != null){
            foreach ($this->dbElements as $e){
                $return[] = $e->getId();
            }
            return $return;
        }

        return [];
    }





    public function all() {
        return $this->elements;
    }

    public function toArray() {
        return $this->elements;
    }

    public function first() {
        return reset($this->elements);
    }

    public function last() {
        return end($this->elements);
    }

    public function isFirst($element) {
        return reset($this->elements) === $element;
    }

    public function isLast($element) {
        return end($this->elements) === $element;
    }

    public function key() {
        return key($this->elements);
    }

    public function next() {
        return next($this->elements);
    }

    public function current() {
        return current($this->elements);
    }

//    public function clear() {
//        $this->elements = array();
//    }

    public function getKeys() {
        return array_keys($this->elements);
    }

    public function getValues() {
        return array_values($this->elements);
    }

    public function isEmpty() {
        return empty($this->elements);
    }

    public function count() {
        return count($this->elements);
    }

    public function __toString() {
        return '';
    }

    public function instance() {
        $this->loadElementFromDatabase();
        return $this;
    }

    public function __debugInfo() {
        return ['elements' => $this->elements] ?? ['elements' => 'null'];
    }

}