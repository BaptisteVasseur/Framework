<?php

namespace Core\Installation;

use App;
use Core\Database\Database;
use Core\Utils\Utils;
use PDO;
use PDOException;

class Installation {

    public static $_instance;
    private $settings = array();
    private $path = ROOT . '/Config/Database.xml';

    public static function getInstance(){
        if(self::$_instance === null){
            self::$_instance = new Installation();
            self::$_instance->initializeConnection();
        }
        return self::$_instance;
    }

    public function initializeConnection(){
        if( !$this->databaseConfigurationIsSet() ){
            App::redirectToRoute('framework_admin_database_showModal_route');
        }
    }

    public function databaseConfigurationIsSet(){
        if ( !file_exists($this->path) ) { // Return false if file doesn't exist
            return false;
        }else{
            $config = Utils::XMLToArray( $this->path ); // Return false if file is empty
            if ( !$config OR !isset($config->db_user) OR !isset($config->db_pass) OR !isset($config->db_host) OR !isset($config->db_name) ) {
                return false;
            }else{
                try{ // Return an error if has wrong connection information in file
                    new PDO("mysql:host={$config->db_host};dbname={$config->db_name}", $config->db_user, $config->db_pass);
                }catch (PDOException $e){
                    return false;
                }
            }
        }
        $this->settings = (array) $config;
        return true;
    }

    public function get($key){
        if(!isset( $this->settings[$key] )){
            return null;
        }
        return $this->settings[$key];
    }

    public function getDBName(){
        return $this->get('db_name');
    }

}