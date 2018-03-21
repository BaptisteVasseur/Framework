<?php

namespace Core\Database;
use Core\Installation\Installation;
use Core\Singleton\Singleton;
use \PDO;

class Database extends Singleton {

	private $db_name;
	private $db_user;
	private $db_pass;
	private $db_host;
	private $pdo;

	private $tableList;

	public function __construct(){
        $databaseConfig = Installation::getInstance();
		$this->db_name = $databaseConfig->get('db_name');
		$this->db_user = $databaseConfig->get('db_user');
		$this->db_pass = $databaseConfig->get('db_pass');
		$this->db_host = $databaseConfig->get('db_host');
	}

	public function getPDO(){
		if($this->pdo === null){
			$pdo = new PDO("mysql:host={$this->db_host};dbname={$this->db_name}", $this->db_user, $this->db_pass);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo = $pdo;

		}
		return $this->pdo;
	}

	public function query($statement, $one = false, $fetchMode = PDO::FETCH_OBJ, $className = null){
	    try{
            $req = $this->getPDO()->query($statement);

            if($fetchMode == PDO::FETCH_CLASS){ $req->setFetchMode($fetchMode, $className); }else{ $req->setFetchMode($fetchMode); }
            if($one){
                $datas = $req->fetch();
            }else{
                $datas = $req->fetchAll();
            }
            return $datas;
        }catch (\Exception $e){
	        return $e;
        }
	}

    public function prepare($statement, $attributes, $one = false, $fetchMode = PDO::FETCH_OBJ, $className = null){
        try{
            $req = $this->getPDO()->prepare($statement);
            $req->execute($attributes);

            if($fetchMode == PDO::FETCH_CLASS){ $req->setFetchMode($fetchMode, $className); }else{ $req->setFetchMode($fetchMode); }
            if($one){
                $datas = $req->fetch();
            }else{
                $datas = $req->fetchAll();
            }
            return $datas;
        }catch (\Exception $e){
            return $e;
        }
    }

    public function update($statement, $attributes){
      return  $this->noFetchPrepareQuery($statement, $attributes);
    }

    public function add($statement, $attributes){
       return $this->noFetchPrepareQuery($statement, $attributes);
    }

    public function remove($statement, $attributes){
       return $this->noFetchPrepareQuery($statement, $attributes);
    }

    public function noFetchPrepareQuery($statement, $attributes){
        $req = $this->getPDO()->prepare($statement);
        $req->execute( (array) $attributes);

        if($req->rowCount() && $req->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function lastInsertId(){
        return $this->getPDO()->lastInsertId();
    }

    public function getTableList(){
        $alltables = $this->getPDO()->query("SHOW TABLES");
        while ($row = $alltables->fetch(PDO::FETCH_NUM)) {
            $tableList[] = $row[0];
        }
        return $this->tableList = $tableList;
    }

    public function count($params = [], $values = [], $tableName){
        foreach ($params as $k => $paramName){
            if($paramsQueryConstructor == ''){ $paramsQueryConstructor .= 'WHERE'; }
            if($paramsQueryConstructor != ''){ $paramsQueryConstructor .= 'AND'; }
            $paramsQueryConstructor .= " $paramName = ? ";
        }
        $query = "SELECT COUNT(*) as nb FROM $tableName WHERE $paramsQueryConstructor";
        $result = $this->prepare($query, $values, true, PDO::FETCH_ASSOC);
        $result = ($result['nb'] && $result['nb'] > 0) ? $result['nb'] : 0;
        return $result;
    }

    public function has($params = [], $values = [], $tableName){
        return ( $this->count($params, $values, $tableName) > 0 ) ? true : false ;
    }


}