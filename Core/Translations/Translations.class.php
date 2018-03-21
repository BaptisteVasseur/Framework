<?php

namespace Core\Translations;

use Core\Singleton\Singleton;
use Core\Utils\Utils;

class Translations extends Singleton {

    protected $translations;

    public function __construct() {
        $translations = (array) Utils::XMLToArray( ROOT . '/Config/Translations.xml' );
        unset($translations['comment']);
        $this->translations = $translations;
    }

    public static function addXMLTranslationFile($filePath) {
        $inst = self::getInstance();
        $translations = (array) Utils::XMLToArray( $filePath );
        unset($translations['comment'], $translations[0]);
        if( !empty($translations) ){
            $inst->translations = array_merge($inst->translations, $translations);
        }
    }

    public static function transform($key){
        $inst = self::getInstance();
        list($folder, $class, $name) = explode(':', $key);
        $translation = ( (array) $inst->translations[$folder]->$class->$name )[0];
        return $translation;
    }

    public static function get($key, $params){
        if($key == 'all' || $key == '*'){
            return self::all();
        }else{
            $translation = self::transform($key);
            return isset( $translation ) ?  vsprintf($translation, $params) : null;
        }
    }

    public static function has($key){
        $inst = self::getInstance();
        $translation = $inst->transform($key);
        return isset( $translation );
    }

    public static function all(){
        $inst = self::getInstance();
        return $inst->translations;
    }

}