<?php

namespace Core\ORM;

use App;
use Core\ClassReader\ClassReader;
use Core\ClassReader\EntityRelationReader;
use Core\Collection\OneToOneCollection;

class updateRelationEntity extends ClassReader {

    private $allOneToManyRelationQuery;

    public function __construct($className)
    {
        parent::__construct($className);
        $this->db = App::getDb();
    }

    public function updateRelationData($entity, $debug){
        $relations = $this->getEntityRelation();
        $this->debug = $debug;

        if(isset($relations['OneToOne'])){
            foreach ($relations['OneToOne'] as $variable => $entityRelation) {
                $this->oneToOneMethod( $variable, $entityRelation, $entity);
            }
        }

        if(isset($relations['OneToMany'])){
            foreach ($relations['OneToMany'] as $variable => $entityRelation) {
                $this->oneToManyMethod($variable, $entityRelation, $entity);
            }
        }
    }

    public function oneToOneMethod($variable, $entityRelation, $entity){

        $relationEntityModel = str_replace('Entity', '', $entityRelation);
        $method = "get" . ucfirst( $variable );
        $relationEntity = $entity->$method();

        $variableNameInDatabase = $variable . "_id";
        $entity->unsetVariable($variable);

        if($relationEntity != null){
            $modelpath = $this->getModelName($entityRelation);

            if ($relationEntity->getId() == null) {
//                var_dump('Entity has been added');
                $result = App::getTable($modelpath)->addNewEntity($relationEntity)->save($this->debug);
                $lastId = $this->db->lastInsertId();
            } else {
                if( $relationEntity->hasBeenChanged() ){
//                    var_dump('Entity has been modified');
                    $result = App::getTable($modelpath)->addUpdateEntity($relationEntity)->save($this->debug);
                }
                $lastId = $relationEntity->getId();
            }

            $entity->$variableNameInDatabase = $lastId;
        }else{
            $entity->$variableNameInDatabase = null;
        }
    }

    public function oneToManyMethod($variable, $entityRelation, $entity){

        $tableList = $this->db->getTableList();

        $currentEntityName = str_replace('Entity', '', end( explode('/', $this->className) ) );
        $relationEntityName = str_replace('Entity', '', end( explode(':', $entityRelation) ) );

        $tableName_1 = strtolower($currentEntityName) . '_' . strtolower( $relationEntityName );
        $tableName_2 = strtolower($relationEntityName) . '_' . strtolower( $currentEntityName );

        $tableName = (in_array($tableName_1, $tableList)) ? $tableName_1 : $tableName_2 ;
        $colonne = ( $tableName == $tableName_1 ) ? strtolower($currentEntityName) . '_id' : strtolower($relationEntityName) . '_id' ;
        $relationColonne = ( $tableName != $tableName_1 ) ? strtolower($currentEntityName) . '_id' : strtolower($relationEntityName) . '_id' ;

        $relationEntities = $entity->getPropertyValue($variable);

        if($relationEntities !== null) {
            $modelpath = $this->getModelName($entityRelation);

            if(count($relationEntities->all()) > 0){
                foreach ($relationEntities->all() as $relationEntity) { // à Ajouter + à modifier

                    if ($relationEntity->getId() == null) {
    //                    var_dump('Entity has been added');
                        $result = App::getTable($modelpath)->addNewEntity($relationEntity)->save($this->debug);
                        $lastId = $this->db->lastInsertId();
                    } else {
                        if( $relationEntity->hasBeenChanged() ){
    //                        var_dump('Entity has been modified');
                            $result = App::getTable($modelpath)->addUpdateEntity($relationEntity)->save($this->debug);
                        }else{
    //                        var_dump('Entity has been set');
                        }
                        $lastId = $relationEntity->getId();
                    }

                    if( !in_array($lastId, $relationEntities->getDatabaseElementsId() ) ){
                        $this->allOneToManyRelationQuery[] = "INSERT INTO $tableName ($colonne, $relationColonne) VALUES (/-id-/, $lastId)";
                    }
                }
            }

            if(count($relationEntities->getElementToRemove()) > 0){
                foreach ($relationEntities->getElementToRemove() as $relationEntity) { // à supprimer
                    if ($relationEntity->getId() != null) {
                        $query = "DELETE FROM $tableName WHERE $colonne = {$entity->getId()} AND $relationColonne = {$relationEntity->getId()}";
                        $this->db->getPDO()->query($query);
                    }
                }
            }
        }

        $entity->unsetVariable($variable);
    }

    public function saveOneToManyRelation($savedEntityId = 0){
        if(empty($this->allOneToManyRelationQuery)){ return true; }

        foreach ($this->allOneToManyRelationQuery as $query){
            $query = str_replace('/-id-/', $savedEntityId, $query);
            if($savedEntityId){
                $this->db->getPDO()->query($query);
            }
//            var_dump($query);
        }
    }




}