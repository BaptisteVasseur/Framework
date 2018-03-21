<?php

namespace Core\Config;

use Core\Singleton\Singleton;
use Core\Utils\Utils;

class Config extends Singleton {

    protected static $parameters;

    public function __construct() {
        $parameters = (array) Utils::XMLToArray( ROOT . '/Config/Config.xml' );
        unset($parameters['comment']);
        self::$parameters = $parameters;
    }

    public function addXMLConfigFile($filePath) {
        $parameters = (array) Utils::XMLToArray( $filePath );
        unset($parameters['comment'], $parameters[0]);
        if( !empty($parameters) ){
            self::$parameters = array_merge(self::$parameters, $parameters);
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