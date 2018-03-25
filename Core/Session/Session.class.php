<?php

namespace Core\Session;

use Core\Singleton\Singleton;

class Session extends Singleton {

    public function __construct(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    // Method Flash

    public static function addFlash($key, $message){
        $_SESSION['flash'][$key] = $message;
    }

    public static function hasFlashes($key = ''){
        if(empty($key)){
            return isset($_SESSION['flash']);
        }
        return isset($_SESSION['flash'][$key]);
    }

    public static function getFlashes(){
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    public static function getFlash($key){
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }

    public static function success($message){
        self::addFlash('success', "<div class='alert alert-success'>$message</div>");
    }

    public static function error($message){
        self::addFlash('error', "<div class='alert alert-danger'>$message</div>");
    }

    public static function getSuccess(){
        return self::getFlash('success');
    }

    public static function getError(){
        return self::getFlash('error');
    }

    // Method Session

    public static function all(){
        return $_SESSION;
    }

    public static function write($key, $value){
        $_SESSION[$key] = $value;
    }

    public static function get($key){
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function has($key){
        return isset($_SESSION[$key]);
    }

    public static function delete($key){
        unset($_SESSION[$key]);
    }

    public static function clear($except = false){
        if($except){ $exceptValue = $_SESSION[$except]; }
        session_unset();
        if($except){ $_SESSION[$except] = $exceptValue; }
    }

    public static function isStarted(){
        if(session_status() === PHP_SESSION_NONE){
           return false;
        }
        return true;
    }

    public static function end(){
        session_destroy();
    }
}