<?php

namespace Core\Singleton;

abstract class Singleton{

    protected static $_instance;

    public static function getInstance() : self {

        if (!(static::$_instance instanceof static)) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

}