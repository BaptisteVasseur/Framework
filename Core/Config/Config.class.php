<?php

namespace Core\Config;

use Core\Singleton\Singleton;
use Core\Utils\Utils;

class Config extends Singleton {

    protected static $parameters;

    public function __construct() {
        $parameters = (array) Utils::XMLToArray( ROOT . '/Config/Config.xml' );
        unset($parameters['comment']);

        foreach ($parameters as $paramName => $paramValue){
            $name = strtolower('app' . ":" . $paramName);
            self::$parameters[$name] = $paramValue;
        }
    }

    public function addXMLConfigFile($filePath) {
        $parameters = (array) Utils::XMLToArray( $filePath );
        unset($parameters['comment'], $parameters[0]);

        list($racine, $path) = explode('Bundles/', $filePath);
        $bundleName = str_replace("/Config/Config.xml", '', $path);

        foreach ($parameters as $paramName => $paramValue){
            $name = strtolower($bundleName . ":" . $paramName);
            self::$parameters[$name] = $paramValue;
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
        $key = strtolower($key);
        self::$parameters[$key] = $value;
    }

    public static function has($key) {
        $key = strtolower($key);
        return array_key_exists($key, self::$parameters);
    }

    public static function remove($key) {
        $key = strtolower($key);
        unset(self::$parameters[$key]);
    }

    public static function count() {
        return count(self::$parameters);
    }

    public static function get($key){
        $key = strtolower($key);
        return self::$parameters[$key];
    }


}