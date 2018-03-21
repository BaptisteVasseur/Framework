<?php

namespace Bundles\FrameworkBundle\Generate;

    class GenerateController extends Generate {



    public static function generate($controllerName, $bundleName) {
        $inst = self::getInstance();
        $controllerName = ucfirst($controllerName) . "Controller";

        $inst->createControllerClass($controllerName, $bundleName);
    }

    protected function createControllerClass( $controllerName, $bundleName ){
        $folder = $this->path . "/" . $bundleName . "/Controller";
        if( ! is_dir($folder) ){ mkdir($folder); }
        $fileName = $folder . "/" . $controllerName . ".class.php";
        $find = ["%%BUNDLE_NAME%%", "%%CONTROLLER_NAME%%"];
        $replace = [$bundleName, $controllerName];
        $content = $this->insertInfoInClass( $find, $replace, $this->getTemplateFile("Controller.txt") );
        return $this->createFile($fileName, $content);
    }

    public static function delete($bundleName, $controllerName){
        $link = ROOT . "/Bundles/" . $bundleName . "/Controller/" . $controllerName . ".class.php";
        if(file_exists($link)){
            unlink($link);
        }
    }

}