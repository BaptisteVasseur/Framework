<?php

namespace Core\Query;

use App;
use Core\Parameters\Parameters;
use Core\Router\Router;
use Core\Singleton\Singleton;

class Query extends Singleton {

    protected static $parameters;

    public function __construct()
    {
        self::$parameters = Router::getUrlParams();
    }

    public static function all() {
        return self::$parameters;
    }

    public static function keys() {
        return array_keys(self::$parameters);
    }

    public static function replace($parameters = array()) {
        self::$parameters = $parameters;
    }

    public static function add($parameters = array()) {
        self::$parameters = array_replace(self::$parameters, $parameters);
    }

    public static function set($key, $value) {
        self::$parameters[$key] = $value;
    }

    public static function has($key) {
        return array_key_exists($key, self::$parameters);
    }

    public static function remove($key) {
        unset(self::$parameters[$key]);
    }

    public static function count() {
        return count(self::$parameters);
    }

    public static function get($key){
        return self::$parameters[$key];
    }

}