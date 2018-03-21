<?php

namespace Core\Logger;

class Logger{

    protected $path;
    protected $file;
    protected $filePath;

    const SUCCESS = 'SUCCESS';
    const ERROR = "ERROR";
    const WARNNING = "WARNNING";
    const DEBUG = 'DEBUG';

    public function __construct($fileName = '', $path = ROOT . '/Logs'){
        $this->path = $path;
        $this->createFolder();

        if($fileName != ''){
            $this->getFile($fileName);
        }
    }

//    public function __destruct(){
//        if($this->fileExist($fileName)) {
//            return $this->close();
//        }
//    }

    protected function createFolder(){
        if( !file_exists($this->path) ) {
            mkdir($this->path, 0777, true);
            echo "<br>Le chemin <b>" . $this->path . "</b> n'existe pas ! Il à donc été créer. <br>";
        }
    }

    protected function newFile($fileName){
        //echo "<br>Le fichier <b>" . $fileName . "</b> n'existe pas ! Il à donc été créer. <br>";
        $this->file = fopen($this->filePath, 'a+');
        return $this->writeStart();
    }

    public function getFile($fileName){

        if($this->file){ $this->writeEnd();  }

        if( $this->fileExist($fileName) ) {
            $this->file = fopen($this->filePath, 'a+');
            return $this->writeStart();
        }else{
            $this->newFile($fileName);
            return false;
        }
    }

    public function write($message, $typeMessage = self::DEBUG){
        $header = $this->getCurrentDate() . " | " . strtoupper($typeMessage);

        $message = sprintf("%s -- %s\n",  $header, $message);
        fwrite($this->file, $message);
        return $this;
    }

    public function close(){
        $this->writeEnd();
        fclose($this->file);
        return $this;
    }

    public function deleteFile(){
        unlink($this->filePath);
    }

    public function fileExist($fileName){
        $filePath = $this->path . "/" . $fileName;
        $this->filePath = $filePath;

        return file_exists($filePath);
    }

    protected function getCurrentDate(){
        $date = new \DateTime();
        return $date->format('d/m/Y H:i:s');
    }

    protected function writeStart(){
        //$this->write("===================== STARTING =====================", '');
        return $this;
    }

    protected function writeEnd(){
        //$this->write("===================== ENDING =====================", '');
        return $this;
    }

    public function readFile($key = ''){
        $content = file($this->filePath);

        $return = [];
        foreach ($content as $k => $row){
            list($date, $fullMessage) = explode(' | ', $row);
            list($nom, $message) = explode(' -- ', $fullMessage);
            $return[ strtolower($nom) ][] = $message;
        }

        if($key != ''){
            $key = strtolower($key);
            return (count($return[$key]) == 1) ? $return[$key][0] : $return[$key] ;
        }
        return $return;
    }


}