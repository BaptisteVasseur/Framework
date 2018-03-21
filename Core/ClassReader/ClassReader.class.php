<?php

namespace Core\ClassReader;

class ClassReader{

    protected $className;
    protected $classReaderRegex = '';

    public function __construct($className) {
        $this->className = $className;
        $this->setRegex('#(@[a-zA-Z-_]+) ?(.+)?#');
    }

    protected function setRegex($regex) { $this->classReaderRegex = $regex; }

    protected function getClassPath(){
        $classPath = str_replace('\\', '/', $this->className);
        $classPath .= '.class.php';
        return $classPath;
    }

    protected function getClassName(){
        $classPath = str_replace('/', '\\', $this->className);
        return $classPath;
    }

    protected function getModelName($entityRelation){
        return str_replace('Entity', '', $entityRelation);
    }

    protected function convertArray($array) {
        for ($z = 0;$z < count($array);$z++) {
            for ($x = 0;$x < count($array[$z]);$x++) {
                $return[$x][$z] = trim($array[$z][$x]);
            }
        }

        foreach ($return as $k => $r){
            $paramName = strtolower(str_replace('@', '', $r[0]));
            $paramValue = $r[1];
            if(array_key_exists($paramName, $return)){
                if(is_string($return[ $paramName ])){
                    $return[ $paramName ] = [ $return[ $paramName ] ];
                }
                array_push($return[ $paramName ], $paramValue);
            }else{
                $return[ $paramName ] = $paramValue;
            }
            unset($return[ $k ]);
        }
        return $return;
    }


    /*
     * Class Reader for properties
     */

    protected function propertiesReader(){
        $classPath = $this->getClassName();
        $obj = new \ReflectionClass( $classPath );
        $properties = $obj->getProperties();

        foreach ($properties as $key => $property){
            unset($property->class);
            unset($properties[$key]);
            $properties[$property->name] = $obj->getProperty($property->name)->getDocComment();

        }
        return $properties;
    }

    public function getPropertiesAnnotation(){
        $propertiesComment = $this->propertiesReader();
        $regex = $this->classReaderRegex;

        foreach ($propertiesComment as $propertyName => $propertyDoc) {
            if(preg_match_all($regex, $propertyDoc, $docElements)) {
                unset($docElements[0]);
                $docElements = array_values($docElements);
                $return[$propertyName] = $this->convertArray($docElements);
            }
        }
        return $return;
    }

    /*
     * Class Reader for class
     */

    protected function classReader(){
        $classPath = $this->getClassName();
        $obj = new \ReflectionClass( $classPath );
        $comments = $obj->getDocComment();

        return $comments;
    }

    public function getClassAnnotation(){
        $classContent = $this->classReader();
        $regex = $this->classReaderRegex;
        if(preg_match_all($regex, $classContent, $annotations)) {
            unset($annotations[0]);
            $annotations = array_values($annotations);
            $return = $this->convertArray($annotations);
        }
        return $return;
    }

    /*
     * Class Reader for methods
     */

    protected function methodsReader(){
        $classPath = $this->getClassName();
        $obj = new \ReflectionClass( $classPath );
        $methods = $obj->getMethods();

        foreach ($methods as $key => $method){
            unset($method->class);
            unset($methods[$key]);
            $methods[$method->name] = $obj->getMethod($method->name)->getDocComment();
        }
        return $methods;
    }

    public function getMethodsAnnotation(){
        $methodsComment = $this->methodsReader();
        $regex = $this->classReaderRegex;

        $return = [];
        foreach ($methodsComment as $methodName => $methodDoc) {
            if(preg_match_all($regex, $methodDoc, $docElements)) {
                unset($docElements[0]);
                $docElements = array_values($docElements);
                $return[$methodName] = $this->convertArray($docElements);
            }
        }
        return $return;
    }





    public function getEntityRelation(){
        $properties = $this->getPropertiesAnnotation();

        $return = [];
        foreach ($properties as $name => $property){
            if(key_exists('relation', $property)){
                $return[ $property['relation'] ][$name] = $property['target'];
            }
        }

        return $return;
    }

}