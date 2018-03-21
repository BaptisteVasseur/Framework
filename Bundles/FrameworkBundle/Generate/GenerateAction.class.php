<?php

namespace Bundles\FrameworkBundle\Generate;

class GenerateAction extends Generate {



    public static function generate($actionName, $controllerName, $bundleName) {
        $inst = self::getInstance();
        $actionName = ucfirst($actionName) . "Action";
        $filePath = ROOT . "/bundles/" . $bundleName . "/Controller/" . $controllerName . ".class.php";

        $content = $inst->generateTemplate($actionName);
        $inst->InsertContentInFileAfter($content, '}', $filePath);
    }

    protected function generateTemplate($inputAction)
    {
        $find = ["%%ACTION_NAME%%"];
        $replace = [$inputAction];
        $content = $this->insertInfoInClass($find, $replace, $this->getTemplateFile("Action.txt"));

        return $content;
    }


}