<?php

namespace Core\Singleton;

class Singleton{

    protected static $_instance;

    public static function getInstance(){

        if (!(static::$_instance instanceof static)) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

}