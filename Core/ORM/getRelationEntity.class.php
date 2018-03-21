<?php

namespace Core\ORM;

use App;
use Core\ClassReader\ClassReader;
use Core\ClassReader\EntityRelationReader;

class getRelationEntity extends ClassReader {

    private $relations;

    public function __construct($className, $currentEntity)
    {
        parent::__construct($className);
        $this->relations = $this->getEntityRelation();
        $this->currentEntity = $currentEntity;
        $this->db = App::getDb();
    }

    public function getData($relationName){

        $OneToMany = $this->relations['OneToMany'][$relationName] ?? false;
        $OneToOne = $this->relations['OneToOne'][$relationName] ?? false;

        if($OneToMany){
            return $this->oneToManyMethod($relationName, $OneToMany);

        }else if($OneToOne){
            return $this->oneToOneMethod($relationName, $OneToOne);

        }else{
            return "Aucune relation associé au champs : <b>" . $relationName . "</b> n'est trouvé !";
        }
    }

    public function oneToOneMethod($relationName, $relations){

        $modelpath = $this->getModelName($relations);
        $variableNameInDatabase = $relationName . "_id";

        $variableValue = $this->currentEntity->$variableNameInDatabase;

        if($variableValue != null){
            $data = App::getTable($modelpath)->findById( $variableValue );

            unset($this->currentEntity->$variableNameInDatabase);
            return $data->initialize();
        }
    }

    public function oneToManyMethod($relationName, $relations){

        if($this->currentEntity->getId() != null){

            $tableList = $this->db->getTableList();

            $currentEntityName = str_replace('Entity', '', end( explode('\\', get_class($this->currentEntity)) ) );
            $relationEntityName = str_replace('Entity', '', end( explode(':', $relations) ) );

            $tableName_1 = strtolower($currentEntityName) . '_' . strtolower( $relationEntityName );
            $tableName_2 = strtolower($relationEntityName) . '_' . strtolower( $currentEntityName );

            $tableName = (in_array($tableName_1, $tableList)) ? $tableName_1 : $tableName_2 ;
            $colonne = ( $tableName == $tableName_1 ) ? strtolower($currentEntityName) . '_id' : strtolower($relationEntityName) . '_id' ;
            $relationColonne = ( $tableName != $tableName_1 ) ? strtolower($currentEntityName) . '_id' : strtolower($relationEntityName) . '_id' ;

            $query = "SELECT * FROM $tableName WHERE $colonne = " . $this->currentEntity->getId();

            $results = $this->db->query($query, false, \PDO::FETCH_ASSOC );

            $return = [];
            foreach ($results as $key => $value){
                $relationEntityModel = str_replace('Entity', '', $relations);
                $entity = App::getTable($relationEntityModel)->findById($value[$relationColonne]);
                $entity->initialize();
                $return[] = $entity;
            }

            return $return;
        }
    }
}