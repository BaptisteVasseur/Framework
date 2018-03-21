<?php

namespace Bundles\FrameworkBundle\Generate;

class GenerateRoute extends Generate
{

    public static function generate($inputSaveBundleName, $inputNom, $inputUrl, $inputBundle, $inputController, $inputAction, $params)
    {
        $inst = self::getInstance();
        $content = $inst->generateTemplate($inputNom, $inputUrl, $inputBundle, $inputController, $inputAction);
        $content = $inst->setParamsValue($content, $params);
        $inst->InsertContentInFileBefore($content, '</routes>', $inst->getRouteFile($inputSaveBundleName));
    }

    protected function generateTemplate($inputNom, $inputUrl, $inputBundle, $inputController, $inputAction)
    {
        $controller = $inputBundle . '/' . $inputController;
        $find = ["__ROUTE_NAME__", "__ROUTE_URL__", "__ROUTE_CONTROLLER__", "__ROUTE_ACTION__"];
        $replace = [$inputNom, $inputUrl, $controller, $inputAction];
        $content = $this->insertInfoInClass($find, $replace, $this->getTemplateFile("Route.txt"));

        return $content;
    }

    protected function setParamsValue($content, $params)
    {
        $template = "<__PARAM_NAME__>__PARAM_VALUE__</__PARAM_NAME__>";

        if(!empty($params)){
            $paramsConstructor = "";
            $lastElement = end($params);
            foreach ($params as $paramName => $paramValue) {
                $paramsConstructor .= str_replace(["__PARAM_NAME__", "__PARAM_VALUE__"], [$paramName, $paramValue], $template);
                if ($lastElement !== $paramValue) {
                    $paramsConstructor .= PHP_EOL . "            "; // Espace nécesaire pour l'indetation
                }
            }
        }else{
            $paramsConstructor = "<!-- NO PARAM -->";
        }

        return str_replace($template, $paramsConstructor, $content);
    }

    protected function getRouteFile($bundleName)
    {
        return $this->path . "/" . $bundleName . "/Config/Routing.xml";
    }


}