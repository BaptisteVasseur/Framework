<?php

namespace Core\Cookie;


use Core\Singleton\Singleton;

class Cookie extends Singleton {

    public function __construct(){}

    public function write($key, $value, $time){
        setcookie($key, $value, $time);
    }

    public function get($key){
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    public function has($key){
        return isset($_COOKIE[$key]);
    }

    public function delete($key){
//        $inst = self::getInstance();
//        $inst->write($key, null, time()-1);
        $this->write($key, null, time()-1);
    }



}