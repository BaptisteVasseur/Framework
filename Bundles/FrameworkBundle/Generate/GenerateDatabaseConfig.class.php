<?php

namespace Bundles\FrameworkBundle\Generate;

class GenerateDatabaseConfig extends Generate
{
     public static function generate($username, $password, $host, $dbName)
     {
         $inst = self::getInstance();
         $find = ["%%DATABASE_USERNAME%%", "%%DATABASE_PASSWORD%%", "%%DATABASE_HOST%%", "%%DATABASE_NAME%%"];
         $replace = [$username, $password, $host, $dbName];
         $content = $inst->insertInfoInClass( $find, $replace, $content = $inst->getTemplateFile('DatabaseXML.txt') );
         $inst->createFile(ROOT . "/Config/Database.xml", $content);
     }


}