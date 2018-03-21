<?php

namespace Bundles\FrameworkBundle\Generate;

use Core\Singleton\Singleton;

class Generate extends Singleton {

    protected $path = ROOT . "/Bundles";

    protected function createFolders( $folders, $bundleName ){
        foreach ( $folders as $k => $v ){
            $p = $this->path . "/" . $bundleName . $v;
            @mkdir( $p );
        }
    }

    protected function getTemplateFile($name){
        return $this->getFileContent(  $this->path . "/FrameworkBundle/Template/" . $name );
    }

    protected function getFileContent($path){
        return file_get_contents($path);
    }

    protected function insertInfoInClass( $find, $replace, $content ){
        return str_replace($find, $replace, $content);
    }

    protected function createFile($fileName, $data = ''){
        return file_put_contents($fileName, $data);
    }

    protected function createOtherFiles( $files, $bundleName ){
        $filePath = $this->path . "/" . $bundleName;
        foreach ( $files as $fileName => $model ){
            $content = $this->getTemplateFile($model);
            $this->createFile($filePath . $fileName, $content);
        }
    }

    protected function InsertContentInFileBefore( $line, $delimiter, $file ){
        $content = $this->getFileContent( $file );
        $array = explode(PHP_EOL, $content);
        foreach ($array as $k => $v){
            if(trim($v) == $delimiter and !array_search($line, $array) ){
                array_splice( $array, $k-1, 0, [$line] );
            }
        }
        $content = implode(PHP_EOL, $array);
        $this->createFile( $file, $content);
    }

    protected function InsertContentInFileAfter( $line, $delimiter, $file ){
        $content = $this->getFileContent( $file );
        $array = explode(PHP_EOL, $content);
        $array = array_reverse($array);
        foreach ($array as $k => $v){
            if(trim($v) == $delimiter and !array_search($line, $array) ){
                array_splice( $array, $k+1, 0, [$line] );
            }
        }
        $array = array_reverse($array);
        $content = implode(PHP_EOL, $array);
        $this->createFile( $file, $content);
    }


}