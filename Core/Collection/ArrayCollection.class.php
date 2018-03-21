<?php

namespace Core\Collection;

use Core\ORM\getRelationEntity;

class ArrayCollection{

    private $elements;


    public function __construct($items = "") {
        if(!empty($item)){
            $this->set($items);
        }
    }

    public function add($element) {
        if(is_array($element)){
            foreach ($element as $k => $e){
                $this->elements[$k] = $e;
            }
        }else{
            $this->elements[] = $element;
        }

        return $this;
    }

    public function remove($element) {
        if(is_array($element)){
            foreach ($element as $e){
                $key = $this->search($e, $this->elements);
                if($key !== false){
                    unset($this->elements[$key]);
                }
            }
        }else{
            $key = $this->search($element, $this->elements);
            if($key !== false){
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

    public function set($element) {
        $this->elements = [];
        if(is_array($element)){
            foreach ($element as $e){
                $this->elements[] = $e;
            }
        }else{
            $this->elements[] = $element;
        }
    }

    public function containsKey($key) {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

//    public function removeKey($key) {
//        if ( ! isset($this->elements[$key]) && ! array_key_exists($key, $this->elements)) {
//            return null;
//        }
//
//        $removed = $this->elements[$key];
//        unset($this->elements[$key]);
//
//        return $removed;
//    }

    public function getElementsId(){
        if($this->elements != null){
            foreach ($this->elements as $e){
                $return[] = $e->getId();
            }
            return $return;
        }

        return null;
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
        return $this;
    }

    public function __debugInfo() {
        return ['elements' => $this->elements] ?? ['elements' => 'null'];
    }

    public function serialize(){
        return serialize( $this->all() );
    }

}