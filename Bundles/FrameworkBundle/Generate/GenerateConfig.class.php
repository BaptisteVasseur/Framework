<?php

namespace Bundles\FrameworkBundle\Generate;

class GenerateConfig extends Generate
{

    public static function generate($inputNomParam, $inputValueParam, $inputBundleName)
    {
        $inst = self::getInstance();
        $content = $inst->getTemplate($inputNomParam, $inputValueParam);
        $inst->InsertContentInFileBefore($content, '</parameters>', $inst->getRouteFile($inputBundleName) );
    }

    protected function getTemplate($paramName, $paramValue)
    {
        return "    <$paramName>$paramValue</$paramName>" . PHP_EOL;
    }

    protected function getRouteFile($bundleName)
    {
        return $this->path . "/" . $bundleName . "/Config/Config.xml";
    }


}