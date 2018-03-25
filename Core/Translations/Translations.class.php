<?php

namespace Core\Translations;

use Core\Singleton\Singleton;
use Core\Utils\Utils;

class Translations extends Singleton {

    protected static $translations;

    public function __construct() {
        $translations = (array) Utils::XMLToArray( ROOT . '/Config/Translations.xml' );
        unset($translations['comment']);

        foreach ($translations as $translationName => $translationValue){
            $name = strtolower('app' . ":" . $translationName);
            self::$translations[$name] = $translationValue;
        }
    }

    public function addXMLTranslationFile($filePath) {
        $translations = (array) Utils::XMLToArray( $filePath );
        unset($translations['comment'], $translations[0]);

        list($racine, $path) = explode('Bundles/', $filePath);
        $bundleName = str_replace("/Config/Translations.xml", '', $path);

        foreach ($translations as $transName => $transValue){
            $name = strtolower($bundleName . ":" . $transName);
            self::$translations[$name] = $transValue;
        }
    }

    public static function get($key, $params){
        $translation = self::$translations[ strtolower($key) ];
        return isset( $translation ) ?  vsprintf($translation, $params) : null;
    }

    public static function all() {
        return self::$translations;
    }
}