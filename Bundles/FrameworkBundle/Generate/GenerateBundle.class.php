<?php

namespace Bundles\FrameworkBundle\Generate;

class GenerateBundle extends Generate {

    protected $folderList = [
        '',
        '/Assets',
        '/Assets/js',
        '/Assets/css',
        '/Assets/images',
        '/Assets/fonts',
        '/Config',
        '/Controller',
        '/Entity',
        '/Form',
        '/Model',
        '/Views',
    ];

    protected $filesList = [
        "/Config/Config.xml" => "ConfigXML.txt",
        "/Config/Routing.xml" => "RoutingXML.txt",
        "/Config/Translations.xml" => "TranslationsXML.txt",
    ];

    public static function generate($nom, $auteur, $link, $date) {
        $inst = self::getInstance();
        $bundleName = ucfirst($nom) . "Bundle";

        $inst->createFolders( $inst->folderList, $bundleName );
        $inst->createBundleClass( $bundleName , [$bundleName, $auteur, $link, $date] );
        $inst->createOtherFiles( $inst->filesList, $bundleName );
        $inst->InsertContentInFileBefore( $inst->getLineToAdd($bundleName), '];', $inst->path . "/BundlesRegistration.class.php" );
    }

    protected function createBundleClass( $bundleName , $replace ){
        $fileName = $this->path . "/" . $bundleName . "/" . $bundleName . ".class.php";
        $find = ["%%BUNDLE_NAME%%", "%%BUNDLE_AUTHOR%%", "%%BUNDLE_LINK%%", "%%BUNDLE_DATE%%"];
        $content = $this->insertInfoInClass( $find, $replace, $this->getTemplateFile("BundleClass.txt") );
        return $this->createFile($fileName, $content);
    }

    public function getLineToAdd($bundleName){
        return "            new $bundleName\\$bundleName(),";
    }

    public static function delete($bundleName){
        $inst = self::getInstance();

        $inst->deleteBundle( $bundleName );
        $inst->RemoveBundleInBundleRegistration( $bundleName );
    }

    private function deleteBundle( $bundleName ){
        $folderPath = $this->path . "/" . $bundleName . "/";
        $this->rrmdir($folderPath);
    }

    function rrmdir($directory) {
        if (is_dir($directory)) {
            $objects = scandir($directory);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($directory . "/" . $object)) {
                        $this->rrmdir($directory . "/" . $object);
                    }else{
                        unlink($directory . "/" . $object);
                    }
                }
            }
            rmdir($directory);
        }
    }

    private function RemoveBundleInBundleRegistration( $bundleName ){
        $content = $this->getFileContent( $this->path . "/BundlesRegistration.class.php" );
        $lineToAdd = PHP_EOL . "            new $bundleName\\$bundleName(),"; // Indentation + saut de ligne
        $content = str_replace($lineToAdd, "", $content);
        $this->createFile( $this->path . "/BundlesRegistration.class.php", $content);
    }


}