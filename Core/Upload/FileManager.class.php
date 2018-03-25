<?php

namespace Core\Upload;

use App;

class FileManager{


    public function newUpload(){
        return new Upload();
    }


    public function find($fileName, $filePath, $absoluteLink = false){
        $absoluePath = ROOT . $filePath;
        $urlPath = BASE_URL . $filePath;

        if( $this->exist($fileName, $absoluePath) ){
            $absoluteFilePath = glob($absoluePath . $fileName . '.*')[0];
            $extension = explode('.', $absoluteFilePath)[1];
            $extension = ($extension == '') ? '' : '.' . $extension ;

            if($absoluteLink){
                return $absoluePath  . $fileName . $extension;
            }else{
                return $urlPath . $fileName . $extension;
            }
        }
        return App::translate('app:noFileFound') . ' <b>' . $filePath . $fileName . '</b>';
    }

    public function exist($fileName, $filePath){
        if( file_exists($filePath . $fileName) ){
            return true;
        }

        $file = glob($filePath . $fileName . '.*')[0];
        if(file_exists($file) ){
            return true;
        }

        return false;
    }


    /*
     * dans App
     *
     * copy
     * move
     * delete
     * get
     * add
     *
     */

}