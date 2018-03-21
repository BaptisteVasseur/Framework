<?php

namespace Core\Request;

use Core\Parameters\Parameters;
use Core\Singleton\Singleton;

class Request extends Singleton {

    protected static $parameters;

    public function __construct()
    {
        self::$parameters = $_POST;
    }

    public static function is($requestType){
        if($requestType == 'XmlHttpRequest'){

            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                return true;
            }
            return false;

        }elseif($requestType == 'get'){

            if(!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
                return true;
            }
            return false;

        }elseif($requestType == 'post'){

            if(!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
                return true;
            }
            return false;

        }
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