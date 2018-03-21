<?php

namespace Core\Server;

use Core\Singleton\Singleton;

class Server extends Singleton {

    protected static $parameters;

    public function __construct()
    {
        self::$parameters = $_SERVER;
    }

    public static function getStatus(){ return $_SERVER['REDIRECT_STATUS']; }
    public static function getHost(){ return $_SERVER['HTTP_HOST']; }
    public static function getServerIp(){ return $_SERVER['SERVER_ADDR']; }
    public static function getClientIp(){ return $_SERVER['REMOTE_ADDR']; }
    public static function getDocumentPath(){ return $_SERVER['DOCUMENT_ROOT']; }
    public static function getMethod(){ return $_SERVER['REQUEST_METHOD']; }
    public static function getURI(){ return $_SERVER['REQUEST_URI']; }
    public static function getScript(){ return $_SERVER['SCRIPT_NAME']; }

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