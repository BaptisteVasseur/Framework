<?php

namespace Core\ORM;

use Bundles\BundlesRegistration;
use Core\ClassReader\ClassReader;
use Core\ClassReader\SaveEntityReader;
use App;
use Core\Installation\Installation;
use Core\Logger\Logger;

class databaseInteraction {

    public $queryContainer;
    public $database;


    public function __construct($action, $setDatabaseConnection = true, $DBConnection = false)
    {
        $this->createEntitiesQuery();

        if($setDatabaseConnection){
            $this->database = App::getInstance()->getDb();
        }
        if($DBConnection){
            $this->database = $DBConnection;
        }
        if($action !== ''){
            $action .= 'Action';
            $this->$action($this->queryContainer);
        }
    }

    /*
     * Affiche un dump des requetes
     */
    public function dumpAction($q){
        var_dump($q['INITIAL_QUERIES']);
        var_dump($q['TRIGGER_QUERIES']);
    }

    /*
     * Affiche le script SQL sans formatage
     */
    public function showSQLAction($q){
        echo implode('', $q['INITIAL_QUERIES']) . " " . implode('', $q['TRIGGER_QUERIES']);
    }

    /*
     * Action pour créer la BDD et installer l'architecture
     */
    public function createAction($q){
        $array = array_merge($q['INITIAL_QUERIES'], $q['TRIGGER_QUERIES']);
        $log = new Logger('database.log');
        foreach ($array as $k => $q){
            $this->database->query($q);
            $log->write($q, "INSTALLATION BDD - ");
        }
        $this->saveCurrentDatabaseScriptAction();
    }

    /*
     * Action pour mettre à jour la BDD
     */
    public function updateSchemaAction(){
        $array = $this->getDatabaseScriptUpdate();
        $log = new Logger('database.log');
        foreach ($array as $k => $q){
            $this->database->query($q);
            $log->write($q, "UPDATE BDD - ");
        }
        $this->saveCurrentDatabaseScriptAction();
    }

    /*
     * Action pour réinitialiser la BDD
     */
    public function resetDatabaseAction(){
        $log = new Logger('database.log');

        $dbName = Installation::getInstance()->getDBName();
        $array = ["DROP DATABASE $dbName", "CREATE DATABASE $dbName"];
        foreach ($array as $k => $q) {
            $this->database->query($q);
            $log->write($q, "RESET - ");
        }

        $path = ROOT . "/Core/Database/structure.txt";
        file_put_contents($path, '');
    }

    /*
     * Core method
     */

    /*
     * Renvoie toutes les annotations d'une entité
     */
    public function getAllEntitiesProperties(){
        $allBundleEntities = BundlesRegistration::getEntities();

        foreach ($allBundleEntities as $bundleName => $bundleEntitites){
            foreach ($bundleEntitites as $key2 => $entity){
                $entityName = "Bundles\\" . $bundleName . "\\Entity\\" . $entity;
                $obj = new ClassReader($entityName);
                $return[$entityName] = $obj->getPropertiesAnnotation();
            }
        }

        return $return;
    }

    /*
     * Créer les requetes (pour créer les tables) associés aux annotations des entités
     */
    protected function createEntitiesQuery(){
        $entities = $this->getAllEntitiesProperties();

        foreach ($entities as $entityName => $entity){
            if( !is_null($entity) ){

                $tableName = $this->getEntityName($entityName);

                $propertiesQuery = array();
                foreach ($entity as $propertyName => $propertyOpt) {
                    $propertyQuery = $this->getQuery($propertyName, $propertyOpt, $tableName);

                    if($propertyQuery != ''){
                        $propertiesQuery[] = $propertyQuery;
                    }
                }

                $query = implode(', ', $propertiesQuery);
                $req = "CREATE TABLE $tableName ( " . $query . " );";
                $this->queryContainer["INITIAL_QUERIES"][] = $req;
            }
        }
    }

    /*
     * Renvoie la requete en fonction du type de la propriété de l'entité
     */
    protected function getQuery($propertyName, $propertyOpt, $tableName){

        if(array_key_exists('relation', $propertyOpt) AND $propertyOpt['relation'] == 'OneToOne'){
            return $this->relationOneToOneTypeQuery($propertyName, $propertyOpt, $tableName);

        }elseif(array_key_exists('relation', $propertyOpt) AND $propertyOpt['relation'] == 'OneToMany'){
            $this->relationOneToManyTypeQuery($propertyName, $propertyOpt, $tableName);

        }elseif(array_key_exists('type', $propertyOpt)){
            return $this->simpleTypeQuery($propertyName, $propertyOpt);

        }

    }

    /*
     * Renvoie le nom de l'entité en fonction de son chemin (namespace)
     */
    protected function getEntityName($entityName){
        list($bundle, $bundleName, $entity, $bundleEntity) = explode('\\', $entityName);
        $entityName = str_replace("Entity", "", $bundleEntity);
        $entityName = strtolower($entityName);
        return $entityName;
    }


    /*
     * Renvoie la requete d'un paramétre d'une entité : "simple"
     */
    protected function simpleTypeQuery($propertyName, $propertyOpt){

        if($propertyOpt['type'] == 'string' and ( !isset($propertyOpt['length']) OR empty($propertyOpt['length']) OR ($propertyOpt['length'] > 255) )){
            die("Erreur ! le paramètre <b>$propertyName</b> est de type 'string (varchar) ' il doit donc avoir un parametre 'length <= 255' ");
        }

        $nullable = (empty($propertyOpt['nullable']) OR $propertyOpt['nullable'] == 'false') ? ' NOT NULL' : '' ;
        $primary = (empty($propertyOpt['primary']) OR $propertyOpt['primary'] == 'false') ? '' : ' PRIMARY KEY' ;
        $increment = (empty($propertyOpt['increment']) OR $propertyOpt['increment'] == 'false') ? '' : ' AUTO_INCREMENT' ;
        $default = (!empty($propertyOpt['default'])) ? " DEFAULT '" . $propertyOpt['default'] . "'" : '' ;

        $normal = ['smallint', 'bigint', 'longtext', 'text', 'datetime', 'timestamp', 'date', 'time', 'int'];

        if( !in_array($propertyOpt['type'], $normal)){

            switch ($propertyOpt['type']) {
                case 'identifier' :
                    $type = 'INT';
                    $primary = ' PRIMARY KEY';
                    $increment = ' AUTO_INCREMENT';
                    break;
                case 'integer' :
                    $type = 'INT';
                    break;
                case 'string' :
                    $type = 'VARCHAR(' . $propertyOpt['length'] . ')';
                break;
                case 'boolean' :
                    $type = 'TINYINT(1)';
                break;
                case 'array' :
                    $type = 'LONGTEXT COMMENT "json_array"';
                    $default = "";
                break;
            }

        }else{
            $type = strtoupper($propertyOpt['type']);
        }

        return $propertyName . ' ' . $type . $nullable . $default . $increment . $primary;
    }

    /*
     * Renvoie la requete d'un paramétre d'une entité : "oneToOne"
     */
    protected function relationOneToOneTypeQuery($propertyName, $propertyOpt, $tableName){

        $nullable = (empty($propertyOpt['nullable']) OR $propertyOpt['nullable'] == 'false') ? ' NOT NULL' : '' ;

        list($bundleName, $bundleEntity) = explode(':', $propertyOpt['target']);
        $propertyName .= "_id";
        $foreignKeyTable = strtolower(str_replace("Entity", "", $bundleEntity));
        $this->queryContainer["TRIGGER_QUERIES"][] = "ALTER TABLE $tableName ADD CONSTRAINT FK_$foreignKeyTable FOREIGN KEY ($propertyName) REFERENCES $foreignKeyTable(id);";

        return $propertyName . " INT " . $nullable;
    }

    /*
     * Renvoie la requete d'un paramétre d'une entité : "OneToMany"
     */
    protected function relationOneToManyTypeQuery($propertyName, $propertyOpt, $tableName){

        if($propertyOpt['relation'] == "OneToMany"){

            list($bundleName, $bundleEntity) = explode(':', $propertyOpt['target']);
            $foreignKeyTableName = strtolower(str_replace("Entity", "", $bundleEntity));
            $relationTableName = $tableName . '_' . $foreignKeyTableName;

            $foreignKeyColName = $propertyName . "_id";
            $entityColName = $tableName . "_id";

            $this->queryContainer["TRIGGER_QUERIES"][] = "ALTER TABLE $relationTableName ADD CONSTRAINT FK_$foreignKeyTableName FOREIGN KEY ($foreignKeyColName) REFERENCES $foreignKeyTableName(id);";
            $this->queryContainer["TRIGGER_QUERIES"][] = "ALTER TABLE $relationTableName ADD CONSTRAINT FK_$tableName FOREIGN KEY ($entityColName) REFERENCES $tableName(id);";

            $req = "CREATE TABLE $relationTableName ( $entityColName INT , $foreignKeyColName INT );";

            $this->queryContainer["INITIAL_QUERIES"][] = $req;
        }
    }

    /*
     * Génére le schema de la base de données
     */
    public function generateSchemaAction(){
        $entities = $this->getAllEntitiesProperties();

        foreach ($entities as $entityName => $entity) {
            if (!is_null($entity)) {

                $entityName = str_replace("Bundles\\", '', str_replace('\\Entity\\', ':', $entityName));
                $id = str_replace(':', '_', $entityName);
                $template .= "<div class='card schema' id='$id' style='margin: 10px; width: 30%; padding: 10px;'>";
                $template .= "<h5 style='font-weight:bolder; text-align: center; border-bottom: 1px solid #d1d1d1; padding: 10px;'>$entityName</h5>";
                $template .= "<ul>";

                foreach ($entity as $propertyName => $propertyOpt) {
                    $propertyName = (isset($propertyOpt['type']) AND $propertyOpt['type'] == 'identifier') ? "<u>$propertyName</u>" : $propertyName ;
                    if(isset($propertyOpt['type']) ){
                        if($propertyOpt['type'] == "identifier"){
                            $propertyType = "<u>{$propertyOpt['type']}</u>";
                        }else{
                            $propertyType = $propertyOpt['type'];
                        }
                    }else{
                        if(isset($propertyOpt['relation'])){
                            $target = str_replace(':', '_', $propertyOpt['target']);
                            $propertyType = "<u>{$propertyOpt['relation']}</u> <small>(<u style='cursor: pointer;' data-target='#$target'>{$propertyOpt['target']}</u>)</small>";
                        }else{
                            $propertyType = 'unknow';
                        }
                    }

                    $template .= "<li>$propertyName : $propertyType</li>";
                }

                $template .= "</ul>";
                $template .= "</div>";
            }
        }
        return $template;
    }


    /*
     * Renvoie le dump des réquetes créer en fonction des entités
     */
    public function getDump(){
        $q = $this->queryContainer;
        return [$q['INITIAL_QUERIES'], $q['TRIGGER_QUERIES']];
    }

    /*
     * Enregistre le nouveau schéma dans le fichier qui contient la version actuelle de la BDD
     */
    public function saveCurrentDatabaseScriptAction(){
        $path = ROOT . "/Core/Database/structure.txt";
        $q = $this->queryContainer;
        $content = implode(PHP_EOL, $q['INITIAL_QUERIES']) . PHP_EOL . implode(PHP_EOL, $q['TRIGGER_QUERIES']);
        file_put_contents($path, $content);
    }


    /*
     * Compare le script de la base de données (enregister dans un fichier) avec le script générer par rapport aux entités et renvoie false si les versions ne correspondent pas.
     */
    public function compareDatabaseScript() {
        $fileContent = file_get_contents(ROOT . "/Core/Database/structure.txt");
        $currentBDDScriptInDB = explode(PHP_EOL, $fileContent);

        $q = $this->queryContainer;
        $generatedDBScript = array_merge($q['INITIAL_QUERIES'], $q['TRIGGER_QUERIES']);
        foreach ($generatedDBScript as $k => $line){
            if($line == ''){ unset($generatedDBScript[$k]); }
            if( !in_array($line, $currentBDDScriptInDB) ){
                return false;
            }
        }
        foreach ($currentBDDScriptInDB as $k => $line){
            if($line == ''){ unset($currentBDDScriptInDB[$k]); }
            if( !in_array($line, $generatedDBScript) ){
                return false;
            }
        }
        return true;
    }

    /*
    * Renvoie toutes les requetes à effectuer (les UPDATE, les REMOVE, les ADD, les DROP ...)
    */
    public function getDatabaseScriptUpdate(){
        list($lineToRemove, $lineToAdd) = $this->getDatabaseScriptDiff();
        list($toAdd, $toRemove, $toUpdate) = $this->getLineToUpdate($lineToRemove, $lineToAdd);

        $toRemove = $this->convertAddQueriesToRemove($toRemove);
        $toUpdate = $this->convertAddQueriesToUpdate($toUpdate);

        $queries = array_merge($toAdd, $toRemove, $toUpdate);
        $return = $this->orderQueries($queries);
        return $return;
    }

    /*
     * Renvoie les requetes "d'ajout" qui ne correspondent plus (Modifié, Supprimé ou Ajouté)
     */
    protected function getDatabaseScriptDiff() {
        $lineToRemove = []; $lineToAdd = [];
        $fileContent = file_get_contents(ROOT . "/Core/Database/structure.txt");
        $currentScript = explode(PHP_EOL, $fileContent);

        $q = $this->queryContainer;
        $newScript = array_merge($q['INITIAL_QUERIES'], $q['TRIGGER_QUERIES']);

        foreach ($currentScript as $k => $line){
            if($line == ''){ unset($currentScript[$k]); }
            if( !in_array($line, $newScript) ){
                $lineToRemove[] = $line;
            }
        }

        foreach ($newScript as $k => $line){
            if($line == ''){ unset($newScript[$k]); }
            if( !in_array($line, $currentScript) ){
                $lineToAdd[] = $line;
            }
        }

        return [$lineToRemove, $lineToAdd];
    }

    /*
     * Récupére les requetes / tables à modifier
     */
    protected function getLineToUpdate($removeArray, $addArray){

        $updateArray1 = []; $updateArray2 = []; $formatedUpdateArray = [];
        $addArrayCompare = []; $removeArrayCompare = [];

        foreach ($addArray as $k => $line){
            $words = explode(' ', $line);
            $keyValue = uniqid();
            unset($addArray[$k]);
            $addArray[$keyValue] = $line;

            if($words[0] === "CREATE"){
                $addArrayCompare[$keyValue] = "$words[0] $words[1] $words[2]";
            }elseif($words[0] === "ALTER"){
                $addArrayCompare[$keyValue] = "$words[0] $words[1] $words[2] $words[3] $words[4] $words[5]";
            }
        }

        foreach ($removeArray as $k => $line){
            $words = explode(' ', $line);
            $keyValue = uniqid();
            unset($removeArray[$k]);
            $removeArray[$keyValue] = $line;

            if($words[0] === "CREATE"){
                $removeArrayCompare[$keyValue] = "$words[0] $words[1] $words[2]";
            }elseif($words[0] === "ALTER"){
                $removeArrayCompare[$keyValue] = "$words[0] $words[1] $words[2] $words[3] $words[4] $words[5]";
            }
        }

        $lineToUpdate1 = array_intersect($addArrayCompare, $removeArrayCompare);
        foreach ($lineToUpdate1 as $keyValue => $line){
            $updateArray1[] = $addArray[$keyValue];
            unset($addArray[$keyValue]);
        }

        $lineToUpdate2 = array_intersect($removeArrayCompare, $addArrayCompare);
        foreach ($lineToUpdate2 as $keyValue => $line){
            $updateArray2[] = $removeArray[$keyValue];
            unset($removeArray[$keyValue]);
        }

        for ($i = 0; $i < count($updateArray1); $i++){
            $formatedUpdateArray[] = [ 'from' => $updateArray2[$i] , "to" => $updateArray1[$i] ];
        }

        return [$addArray, $removeArray, $formatedUpdateArray];
    }

    /*
     * Convertir les requetes "d'ajout" des elements à supprimer en requetes de suppresion
     */
    protected function convertAddQueriesToRemove($removeArray){
        foreach ($removeArray as $k => $line){
            $words = explode(' ', $line);
            if($words[0] === "CREATE"){
                $removeArray[$k] = "DROP TABLE $words[2];";
            }elseif($words[0] === "ALTER"){
                $removeArray[$k] = "ALTER TABLE $words[2] DROP FOREIGN KEY $words[5];";
            }
        }
        return $removeArray;
    }

    /*
     * Convertit les requetes "d'ajout" des elements modifiés en requetes de modification
     */
    protected function convertAddQueriesToUpdate($updateArray){
        $updateArrayQueries = [];
        foreach ($updateArray as $k => $line){
            $wordsTo = explode(' ', $line['to']);
            if($wordsTo[0] === "CREATE"){
                $updateArrayQueries = array_merge($updateArrayQueries, $this->getNewEntityStructure($wordsTo[2], $line) );
            }elseif($wordsTo[0] === "ALTER"){
                $updateArrayQueries[] = "ALTER TABLE $wordsTo[2] DROP FOREIGN KEY $wordsTo[5];";
                $updateArrayQueries[] = $line['to'];
            }
        }

        return $updateArrayQueries;
    }

    /*
     * Renvoie les requetes pour modifier la structure d'une entitée
     */
    protected function getNewEntityStructure($tableName, $line){
        $oldStrucure = str_replace("CREATE TABLE $tableName ( ", '', $line['from']);
        $newStructure = str_replace("CREATE TABLE $tableName ( ", '', $line['to']);
        $oldStrucure = str_replace(" );", '', $oldStrucure);
        $newStructure = str_replace(" );", '', $newStructure);
        $oldStrucure = explode(', ', $oldStrucure);
        $newStructure = explode(', ', $newStructure);

        $toRemove = array_diff($oldStrucure, $newStructure);
        $toAdd = array_diff($newStructure, $oldStrucure);

        array_splice($toRemove, 0, 0); // Ré-index les array
        array_splice($toAdd, 0, 0); // Ré-index les array
        $return = [];

        if(count($toRemove) == 1 and count($toAdd) == 1){
                $line = explode(' ', $toRemove[0]);
                $return[] = "ALTER TABLE $tableName CHANGE $line[0] $toAdd[0];";
        }else{
            foreach ($toAdd as $k => $addLine){
                $return[] = "ALTER TABLE $tableName ADD $addLine;";
            }
            foreach ($toRemove as $k => $removeLine){
                $removeLine = explode(' ', $removeLine);
                $return[] = "ALTER TABLE $tableName DROP COLUMN $removeLine[0];";
            }
        }
        return $return;
    }

    /*
     * Ordonne l'ordre d'execution des requetes pour pas qu'il y est de conflit
     */
    protected function orderQueries($queries){
        $dropConstraint = [];
        $dropTable = [];
        $dropColumn = [];
        $addColumn = [];
        $addConstraint = [];
        $addTable = [];
        $changeTable = [];

        foreach ($queries as $query){
            if(strpos($query, "DROP FOREIGN KEY ") !== false){
                $dropConstraint[] = $query;
            }else if(strpos($query, "DROP TABLE ") !== false){
                $dropTable[] = $query;
            }else if(strpos($query, "DROP COLUMN ") !== false){
                $dropColumn[] = $query;
            }else if(strpos($query, "ADD CONSTRAINT ") !== false){
                $addConstraint[] = $query;
            }else if(strpos($query, "CREATE TABLE ") !== false){
                $addTable[] = $query;
            }else if(strpos($query, "ADD ") !== false){
                $addColumn[] = $query;
            }else if(strpos($query, "CHANGE ") !== false){
                $changeTable[] = $query;
            }
        }

        return array_merge($dropConstraint, $dropColumn, $changeTable, $addTable, $addColumn, $addConstraint, $dropTable);
    }

}