<?php

namespace Core\Upload;

use App;

class Upload{

    protected $maxSize;
    protected $validFileExtension = ['jpg', 'png', 'jpeg', 'giff', 'pdf', 'php'];
    protected $erreur = [];
    protected $filePath;

    public function __construct() {

    }

    public function addFiles($name, $newName = null, $forcedExtension = null){

        foreach ($_FILES[$name]['name'] as $indice => $value){
            if( $this->isValid($name, $indice) ){

                $extension = ($forcedExtension == null) ? $this->getFileExtension($name, $indice) : $forcedExtension ;
                $fileName = ($newName == null) ? explode('.',  $this->getFileName($name, $indice))[0] : str_replace('{:id}', $indice+1, $newName) ;
                $this->setValidFile( $name, $indice, $fileName . '.' . $extension );
            }
        }
        return false;
    }

    public function isValid($name, $indice){

        if ($_FILES[$name]['error'][$indice] > 0) $this->erreur[] = App::trans('core:upload:ErrorDuringTransfert');
        if ($_FILES[$name]['size'][$indice] > 3000000000) $this->erreur[] = App::trans('core:upload:ErrorTooBig');

        $uploadExtension = $this->getFileExtension($name, $indice);
        if(!in_array($uploadExtension, $this->validFileExtension) )  $this->erreur[] = App::trans('core:upload:ErrorExtension');

        if(empty($this->erreur)){
            return true;
        }
        return false;
    }


    public function setValidFile( $name, $indice, $newName ){
        $tmpName = $_FILES[$name]['tmp_name'][$indice];
        $validFile = ['tmp_name' => $tmpName, 'newName' => $newName];
        $this->validFile[] = $validFile;
    }

    public function getFileExtension($name, $indice){
        return strtolower(  substr(  strrchr($_FILES[$name]['name'][$indice],'.'),1)  );
    }

    public function getFileName($name, $indice){
        return $_FILES[$name]['name'][$indice];
    }



    public function save(){
        $filePath = $this->filePath;
        $files = $this->validFile;

        if(!is_dir($filePath)){ mkdir($filePath); }

        foreach ($files as $k => $v){
            $v['filePath'] = $filePath . $v['newName'];
            $result = move_uploaded_file($v['tmp_name'], $v['filePath']);
            if(!$result){ echo '<div class="alert alert-success"><b>' . App::trans('core:upload:ErrorFileUpload') . '</b></div>'; }
        }

        return '<div class="alert alert-success"><b>' . App::trans('core:upload:FileSaved') . '</b></div>';
    }



    public function setMaxSize($maxSize) {
        $this->maxSize = $maxSize;
    }

    public function setValidFileExtension($validFileExtension) {
        $this->validFileExtension = $validFileExtension;
    }

    public function getError(){
        return $this->erreur;
    }

    public function setFilePath($filePath) {
        $this->filePath = $filePath;
    }


    /*
     * save
     * destination_exist
     * create_destination
     *
     */

}