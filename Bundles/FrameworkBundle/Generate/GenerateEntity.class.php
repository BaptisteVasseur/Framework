<?php

namespace Bundles\FrameworkBundle\Generate;

class GenerateEntity extends Generate {

    public static function generate($nom, $bundleName, $properties) {
        $inst = self::getInstance();
        $entityName = ucfirst($nom) . "Entity";

        $properties = $inst->convertArray($properties);
        unset($properties[0]);
        $annotations = $inst->generateEntitesAnnotation($properties);
        $constructor = $inst->generateEntitesConstructor($properties);
        $methods = $inst->generateEntitesMethods($properties);

        $inst->createEntityClass($entityName, $bundleName, $annotations, $constructor, $methods);
    }

    public function generateEntitesMethods($properties){

        $template = '';

        foreach ($properties as $k => $property){
            $nom = $property['propertyName'];
            $nameForMethod = ucfirst(strtolower($nom));
            if($property['propertyType'] == 'OneToOne'){

                $getter = '    public function get' . $nameForMethod . '() { return $this->' . $nom . '->get(); }' . PHP_EOL;
                $setter = '    public function set' . $nameForMethod . '($' . $nom . ') { return $this->' . $nom . '->set($' . $nom . '); }' . PHP_EOL;

                $template .= $getter . $setter . PHP_EOL;
            }else if($property['propertyType'] == 'OneToMany'){

                $add = '    public function add' . $nameForMethod . '($' . $nom . ') { return $this->' . $nom . '->add($' . $nom . '); }' . PHP_EOL;
                $remove = '    public function remove' . $nameForMethod . '($' . $nom . ') { return $this->' . $nom . '->remove($' . $nom . '); }' . PHP_EOL;
                $getter = '    public function get' . $nameForMethod . '() { return $this->' . $nom . '->instance(); }' . PHP_EOL;

                $template .= $add . $remove . $getter . PHP_EOL;
            }else{
                $nom = $property['propertyName'];
                $getter = '    public function get' . $nameForMethod . '() { return $this->' . $nom . '; }' . PHP_EOL;
                $setter = '    public function set' . $nameForMethod . '($' . $nom . ') { $this->' . $nom . ' = $' . $nom . '; return $this; }' . PHP_EOL;

                $template .= $getter . $setter . PHP_EOL;
            }
        }

        var_dump($template);
        return $template;
    }


    public function generateEntitesConstructor($properties){

        $template = '';

        foreach ($properties as $k => $property){
            if($property['propertyType'] == 'OneToOne'){
                $template .= '        $this->' . $property['propertyName'] . ' = new OneToOneCollection(\'' . $property['propertyName'] . '\', $this);' . PHP_EOL;
            }else if($property['propertyType'] == 'OneToMany'){
                $template .= '        $this->' . $property['propertyName'] . ' = new OneToManyCollection(\'' . $property['propertyName'] . '\', $this);' . PHP_EOL;
            }
        }

        return $template;
    }

    public function generateEntitesAnnotation($properties){
        $template = "";

        foreach ($properties as $k => $property){
            $typeAnnotation = ""; $defaultAnnotation = ""; $nullableAnnotation = ""; $lengthAnnotation = ""; $targetAnnotation = "";
            $startAnnotation = "    /**" . PHP_EOL;

            if($property['propertyType'] != 'OneToOne' AND $property['propertyType'] != 'OneToMany'){
                $typeAnnotation = (isset($property['propertyType']) AND $property['propertyType'] !== '') ? "     * @Type " . $property['propertyType'] . PHP_EOL : "     * @Type TYPE_NOT_SET" . PHP_EOL;
                $defaultAnnotation = (isset($property['propertyDefaultValue']) AND $property['propertyDefaultValue'] !== '') ?  "     * @Default " . $property['propertyDefaultValue'] . PHP_EOL : '' ;
                $nullableAnnotation = (isset($property['propertyIsNullable']) AND $property['propertyIsNullable'] !== '0') ?  "     * @Nullable true" . PHP_EOL : '' ;
                $lengthAnnotation = (isset($property['propertyLength']) AND $property['propertyLength'] !== '') ? "     * @Length " . $property['propertyLength'] . PHP_EOL : '' ;
            }else{
                $typeAnnotation = "     * @Relation " . $property['propertyType'] . PHP_EOL;
                $targetAnnotation = "     * @Target " . $property['bundleRelationTarget'] . ':' . $property['entityRelationTarget'] . PHP_EOL;
            }

            $endAnnotation = "     */" . PHP_EOL;
            $nameAnnotation = "    protected $" . $property['propertyName'] . ";" . PHP_EOL;

            $template .= $startAnnotation . $typeAnnotation . $targetAnnotation . $defaultAnnotation . $nullableAnnotation . $lengthAnnotation . $endAnnotation . $nameAnnotation . PHP_EOL;
        }

        return $template;
    }

    protected function convertArray($array){
        $result = array();
        foreach($array as $key1 => $value1){
            foreach($value1 as $key2 => $value2){
                $result[$key2][$key1] = $value2;
            }
        }
        return $result;
    }

    protected function createEntityClass( $entityName, $bundleName, $annotations, $constructor, $methods ){
        $folder = $this->path . "/" . $bundleName . "/Entity";
        if( ! is_dir($folder) ){ mkdir($folder); }
        $fileName = $folder . "/" . $entityName . ".class.php";
        $find = ["%%BUNDLE_NAME%%", "%%ENTITY_NAME%%", "    %%INSERT_PROPERTY_HERE%%" . PHP_EOL . PHP_EOL , "        %%INSERT_CONSTRUCTOR_HERE%%" . PHP_EOL, '    %%INSERT_METHOD_HERE%%' . PHP_EOL];
        $replace = [$bundleName, $entityName, $annotations, $constructor, $methods];
        $content = $this->insertInfoInClass( $find, $replace, $this->getTemplateFile("Entity.txt") );
        return $this->createFile($fileName, $content);
    }

    public static function delete($bundleName, $entityName){
        $link = ROOT . "/Bundles/" . $bundleName . "/Entity/" . $entityName . ".class.php";
        if(file_exists($link)){
            unlink($link);
        }
    }

}