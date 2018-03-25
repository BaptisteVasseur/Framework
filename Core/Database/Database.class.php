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
			$pdo = new PDO("mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8", $this->db_user, $this->db_pass);
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

    public function add($statement, $attributes){
        return $this->noFetchPrepareQuery($statement, $attributes);
    }

    public function update($tableName, $toUpdate, $where){
        $toUpdateQuery = "";
        $toUpdateValues = [];
        $whereQuery = "";
        $whereValues = [];

        foreach ($toUpdate as $name => $value){
            if($toUpdateQuery != ''){ $toUpdateQuery .= ', '; }
            if($toUpdateQuery == ''){ $toUpdateQuery .= 'SET'; }
            $toUpdateQuery .= " $name = ? ";
            $toUpdateValues[] = $value;
        }

        foreach ($where as $name => $value){
            if($whereQuery != ''){ $whereQuery .= 'AND'; }
            if($whereQuery == ''){ $whereQuery .= 'WHERE'; }
            $whereQuery .= " $name = ? ";
            $whereValues[] = $value;
        }

        return $this->noFetchPrepareQuery("UPDATE $tableName $toUpdateQuery $whereQuery", array_merge($toUpdateValues, $whereValues));
    }

    public function remove($tableName, $where){
        $whereQuery = "";
        $whereValues = [];

        foreach ($where as $name => $value){
            if($whereQuery != ''){ $whereQuery .= 'AND'; }
            if($whereQuery == ''){ $whereQuery .= 'WHERE'; }
            $whereQuery .= " $name = ? ";
            $whereValues[] = $value;
        }

       return $this->noFetchPrepareQuery("DELETE FROM $tableName $whereQuery", $whereValues);
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
        $allTables = $this->getPDO()->query("SHOW TABLES");
        $tableList = [];
        while ($row = $allTables->fetch(PDO::FETCH_NUM)) {
            $tableList[] = $row[0];
        }
        return $this->tableList = $tableList;
    }

    public function count($params, $tableName){
        $paramsQueryConstructor = "";
        $paramValues = [];
        foreach ($params as $name => $value){
            if($paramsQueryConstructor != ''){ $paramsQueryConstructor .= 'AND'; }
            if($paramsQueryConstructor == ''){ $paramsQueryConstructor .= 'WHERE'; }
            $paramsQueryConstructor .= " $name = ? ";
            $paramValues[] = $value;
        }
        $result = $this->prepare("SELECT COUNT(*) as nb FROM $tableName $paramsQueryConstructor", $paramValues, true, PDO::FETCH_ASSOC);
        $result = ($result['nb'] && $result['nb'] > 0) ? $result['nb'] : 0;
        return $result;
    }

    public function has($params, $tableName){
        return ( $this->count($params, $tableName) > 0 ) ? true : false ;
    }


}