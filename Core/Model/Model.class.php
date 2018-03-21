<?php

namespace Core\Model;

use App;
use Core\Collection\OneToOneCollection;
use Core\Database\Database;
use Core\ORM\saveRelationEntity;
use Core\ORM\updateRelationEntity;
use Error;
use MongoDB\BSON\ObjectID;
use \PDO;

class Model{

    protected $db;
    protected $newEntities = [];
    protected $databaseEntities = [];

    public function __construct(Database $db){
        $this->db = $db;
    }

    protected function getEntityName(){
        $class = get_called_class();
        $entityName = str_replace('Model', 'Entity', $class);
        return $entityName;
    }

    protected function getEntityPathName(){
        $entityName = $this->getEntityName();
        $entityPath = str_replace('\\', '/', $entityName);
        return $entityPath;
    }

    protected function getTableName(){
        $class = explode('\\', get_called_class() );
        $modelName = array_pop($class);
        $tableName = str_replace('Model', '', $modelName);
        $tableName = strtolower($tableName);
        return $tableName;
    }


    /*
     *
     * Method pour ajouter ou modifier des elements de la bdd
     *
     */

    public function new(){
        $entityPath = $this->getEntityName();
        $entity = new $entityPath();
        $this->newEntities[] = $entity;
        return $entity->initialize();
    }

    public function addNewEntity($entity){
        $this->newEntities[] = $entity;
        return $this;
    }

    public function persist($entity){
        return $this->addNewEntity($entity);
    }

    public function addUpdateEntity($entity){
        $this->databaseEntities[] = $entity;
        return $this;
    }

    protected function getAllEntityToUpdate(){
        $allDatabaseEntities = $this->databaseEntities;
        $result = [];

        foreach ($allDatabaseEntities as $entity){
            if( $entity->hasBeenChanged() ){
                $result[] = $entity;
            }
        }

        return $result;
    }

    protected function getAllEntityToRemove(){
        $allDatabaseEntities = $this->databaseEntities;
        $result = [];

        foreach ($allDatabaseEntities as $entity){
            if( $entity->hasBeenChanged() ){
                $result[] = $entity;
            }
        }

        return $result;
    }

    protected function getAllEntityToAdd(){
        return  $this->newEntities;
    }


    /*
     * Fait la jonction entre l'enregistrement et la modification d'un element
     */

    public function save($debug = false){
        $elementsIdList = [];

        $entityToAdd = $this->getAllEntityToAdd(); // only Add
        $entityToUpdate = $this->getAllEntityToUpdate(); // Update and Remove

        foreach ($entityToAdd as $k => $entity){
            $saveEntity = new saveRelationEntity( $this->getEntityPathName() );
            $return = $saveEntity->saveRelationData($entity, $debug);
            $id = $this->DatabaseAddEntity($entity, $debug);

            if($id){
                $saveEntity->saveOneToManyRelation($id);
                $elementsIdList[] = $id;
            }
        }

        foreach ($entityToUpdate as $k => $entity){
            $updateEntity = new updateRelationEntity( $this->getEntityPathName() );
            $result = $updateEntity->updateRelationData($entity, $debug);
            $id = $this->DatabaseUpdateEntity($entity, $debug);

            if($id){
                $updateEntity->saveOneToManyRelation($id);
            }
        }


//        return $this->refresh( $elementsIdList , $this->databaseEntities );

    }

    public function refresh($addEntitiesIdList , $databaseEntitiesList){
        $this->newEntities = [];
        $this->databaseEntities = [];


        foreach ($databaseEntitiesList as $entity){
            $entity = $this->findById( $entity->getId() );
            if( !in_array($entity, $this->databaseEntities) ){
                $this->databaseEntities[] = $entity;
            }
        }

        foreach ($addEntitiesIdList as $id){
            $entity = $this->findById($id);
            if( !in_array($entity, $this->databaseEntities) ){
                $this->databaseEntities[] = $entity;
            }
        }

        return $this->databaseEntities;
    }

    public function delete($element, $debug = false){

    }


    /*
     * Ajoute un element
     */

    protected function DatabaseAddEntity($element, $debug = false){

        $table = $this->getTableName();
        $tableInputs = $element->getEntityVars();

        foreach ($tableInputs as $inputName => $v){
            $getInput = "get" . ucfirst($inputName);

            if(method_exists($element, $getInput)){
                if($v instanceof \DateTime){
                    $values[] = $element->$getInput()->format('Y-m-d H:i:s');
                }else{
                    $values[] = $element->$getInput();
                }
            }else{
                $values[] = $element->$inputName;
            }

            $queryConstructor[] = "$inputName";
            $prepareConstructor[] = "?";
        }

        $QueConstructor = implode(', ', $queryConstructor);
        $ArgConstructor = implode(', ', $prepareConstructor);

        $query = "INSERT INTO $table ($QueConstructor) VALUES ($ArgConstructor) ";

        if($debug){ var_dump($query, $values); }

        $result = $this->db->add($query, $values);

        if($result){
            if($debug){ echo App::translate('core:model:entitySaved'); }
            return $this->db->lastInsertId();
        }else{
            if($debug){ echo App::translate('core:model:entitySavedError'); }
            return false;
        }
    }

    /*
     * Met à jour un element
     */

    protected function DatabaseUpdateEntity($element, $debug = false){

        $table = $this->getTableName();
        $tableInputs = $element->getEntityVars();

        foreach ($tableInputs as $inputName => $v){
            $queryConstructor[] = "$inputName = ?";

            $getInput = "get" . ucfirst($inputName);

            if(method_exists($element, $getInput)){
                if($v instanceof \DateTime){
                    $values[] = $element->$getInput()->format('Y-m-d H:i:s');
                }else{
                    $values[] = $element->$getInput();
                }
            }else{
                $values[] = $element->$inputName;
            }
        }

        $QueConstructor = implode(', ', $queryConstructor);

        $query = "UPDATE $table SET $QueConstructor WHERE id = " . $element->getId();

        if($debug){ var_dump($query, $values); }
        $result = $this->db->add($query, $values);

        if($result){
            if($debug){ echo App::translate('core:model:entityUpdated'); }
        }else{
            if($debug){ echo App::translate('core:model:entityUpdatedError'); }
        }

        return $element->getId();
    }


    /*
     *
     * Retrouve des elements dans la base de donnée.
     *
     */

    protected function getQuery($params, $order){
        $tableName = $this->getTableName();

        $orderByQueryConstructor = '';
        $queryConstructor = '';
        $values = [];

        foreach ($params as $param => $value){
            if($paramsQueryConstructor != ''){ $paramsQueryConstructor .= 'AND'; }
            if($paramsQueryConstructor == ''){ $paramsQueryConstructor .= 'WHERE'; }
            $paramsQueryConstructor .= " $param = ? ";
            $paramsQueryValues[] = $value;
        }

        foreach ($order as $param => $value){
            if($orderByQueryConstructor != ''){ $orderByQueryConstructor .= ','; }
            if($orderByQueryConstructor == ''){ $orderByQueryConstructor .= 'ORDER BY'; }
            $orderByQueryConstructor .= " $param $value ";
        }

        $query = "SELECT * FROM $tableName " . $paramsQueryConstructor . $orderByQueryConstructor;

        return [$query, $paramsQueryValues];
    }



    /*
     *
     * Method pour récuperer les entités
     *
     */

    public function findOneBy($params = [], $order = []){
        $entityName = $this->getEntityName();
        list($query, $paramsQueryValues) = $this->getQuery($params, $order);
        $result = $this->db->prepare($query, $paramsQueryValues , true, PDO::FETCH_CLASS, $entityName);

        if($result){
            $this->databaseEntities[] = $result;
            return $result->initialize();
        }
        return false;
    }

    public function findBy($params = [], $order = []){
        $entityName = $this->getEntityName();
        list($query, $paramsQueryValues) = $this->getQuery($params, $order);
        $result = $this->db->prepare($query, $paramsQueryValues , false, PDO::FETCH_CLASS, $entityName);

        if($result){
            foreach ($result as $r){
                $this->databaseEntities[] = $r;
                $r->initialize();
            }
            return $result;
        }
        return false;
    }

    public function findById($id){
        return $this->findOneBy(['id' => $id]);
    }

    public function findAll(){
        return $this->findBy();
    }

    public function has($element){
        $id = $element->getId();
        $table = $this->getTableName();
        return $this->db->has(['id'], [$id], $table);
    }

}

