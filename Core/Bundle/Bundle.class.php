<?php

namespace Core\Bundle;

use Core\ClassReader\ClassReader;

class Bundle{

    public function __construct() {}

    public function getBundleName(){
        $class = get_called_class();
        $moduleName = end( explode('\\', $class) );
        return $moduleName;
    }

    public function getNamespace(){
        $class = get_called_class();
        $namespace = preg_replace('/' . $this->getBundleName() . '$/', '', $class, -1);
        return $namespace;
    }

    public function getRootDir(){
        $file = $this->getNamespace();
        $path = str_replace('\\', '/', $file);
        $url = ROOT . '/' . $path;
        return $url;
    }

    public function getConfigFile(){
        $path = $this->getRootDir();
        $configFile = $path . 'Config/Config.xml';
        return $configFile;
    }

    public function getRoutingFile(){
        $path = $this->getRootDir();
        $configFile = $path . 'Config/Routing.xml';
        return $configFile;
    }

    public function getTranslationFile(){
        $path = $this->getRootDir();
        $configFile = $path . 'Config/Translations.xml';
        return $configFile;
    }

    public function getBundleEntities(){
        $path = $this->getRootDir() . 'Entity';
        if(is_dir($path)) {
            $entities = array_diff(scandir($path), array('..', '.'));

            foreach ($entities as $key => $entity) {
                if (!strpos($entity, 'Entity')) {
                    unset($entities[$key]);
                } else {
                    $entities[$key] = str_replace('.class.php', '', $entity);;
                }
            }
            return array_values($entities);
        }
        return [];
    }

    public function getBundleControllers(){
        $path = $this->getRootDir() . 'Controller';
        if(is_dir($path)){
            $controllers = array_diff(scandir($path), array('..', '.'));

            foreach ($controllers as $key => $controller){
                if(!strpos($controller, 'Controller')){
                    unset($controllers[$key]);
                }else{
                    $controllers[$key] =  str_replace('.class.php', '', $controller);
                }
            }
            return array_values($controllers);
        }
        return [];
    }

    public function getBundleActions(){
        $controllers = $this->getBundleControllers();

        $actions = [];
        foreach ($controllers as $controller){
            $classToOpen = str_replace('\\', "/", $this->getNamespace() . 'Controller\\' . $controller . ".class.php");
            $content = file_get_contents(ROOT . "/" . $classToOpen);
            if( preg_match_all('#function ([a-zA-Z0-9_]+Action)#m', $content, $matches) ){
                $actions[$controller] = $matches[1];
            }
        }
        return $actions;
    }

    public function getRoutingAnnotations(){
        $controllers = $this->getBundleControllers();

        $return = [];
        foreach ($controllers as $controller){
            $class = $this->getNamespace() . 'Controller\\' . $controller;
            $cr = new ClassReader($class);
            $return[$class] = $cr->getMethodsAnnotation();
        }
        return $return;
    }


}