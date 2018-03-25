<?php 

namespace Core\Router;

use App;
use Core\Query\Query;
use Core\Singleton\Singleton;
use Core\Utils\Utils;
use SimpleXMLElement;

class Router {

    const TYPE_INT = '([0-9]+)';
    const TYPE_SLUG = '(.+)';
    const TYPE_ACTION = '([a-z]+)';

    protected $url;
    protected $routes = [];
    protected $hasMatched;
    protected $matchedRoute;
    protected $data;

    protected static $_instance;
    public static function getInstance(){
        if (!(static::$_instance instanceof Router)) {
            static::$_instance = new Router();
        }
        return static::$_instance;
    }

	public function __construct($url = false){

	    if( !$url ){
            $url = $_GET['url'];
        }
        $this->url = trim($url, '/');

        if( App::$routeInstance == null){
            App::$routeInstance = $this;
        }

        self::$_instance = $this;
	}

	public static function getRoutes(){
	    $inst = self::getInstance();
	    return $inst->routes;
    }

    public function addXMLRoutingFile($filePath){
        $XMLContent = Utils::XMLToArray($filePath);
        $jsonContentEncoded = json_encode($XMLContent);
        $this->addRoutingFile($jsonContentEncoded, $filePath);
    }

    public function addRoutingAnnotations($controllers){
        if( !empty($controllers) ){
            foreach ($controllers as $controllerName => $controllersAnnotations){
                if(!empty($controllersAnnotations)){
                    $constructor = "<routes>";
                    foreach ($controllersAnnotations as $methodName => $methodAnnotation){
                        $controller = str_replace(["\\",'Bundles/', 'Controller/'], ['/', '', ''], $controllerName);

                        $constructor .= "<route name='{$methodAnnotation['routename']}'>";
                        $constructor .= "<url>{$methodAnnotation['routeurl']}</url>";
                        $constructor .= "<controller>{$controller}</controller>";
                        $constructor .= "<action>{$methodName}</action>";
                        $constructor .= "<params>";

                        if($methodAnnotation['routeparam']){
                            if( !is_array($methodAnnotation['routeparam'])){
                                $methodAnnotation['routeparam'] = [ $methodAnnotation['routeparam'] ];
                            }
                            foreach ($methodAnnotation['routeparam'] as $param){
                                list($paramName, $paramValue) = explode(' ', $param);
                                $paramName = str_replace(':', '', $paramName);
                                $constructor .= "<$paramName>$paramValue</$paramName>";
                            }
                        }

                        $constructor .= "</params>";
                        $constructor .= "</route>";
                    }
                    $constructor .= "</routes>";

                    $element = new SimpleXMLElement($constructor);
                    $this->addRoutingFile( json_encode($element) , $controllerName);
                }
            }
        }
    }

    public function addRoutingFile($jsonContentEncoded, $filePath){
	    $XMLFullArray = json_decode($jsonContentEncoded, true);

	    if($jsonContentEncoded == '{"0":"\n\n\n\n"}' OR $jsonContentEncoded == null){
	        return;
        }

	    if( !isset($XMLFullArray['route'][0]) ){
	        $values = $XMLFullArray['route'];
            unset( $XMLFullArray['route'] );
            $XMLFullArray['route'][0] = $values;
        }

	    if( count($XMLFullArray['route']) > 0){
            foreach ($XMLFullArray['route'] as $k => $v){

                $XMLArray = $v;
                $routeName = $XMLArray['@attributes']['name'];
                $route = $XMLArray['url'];
                $controller = $XMLArray['controller'];
                $action = $XMLArray['action'];
                unset($XMLArray['params']['comment']);
                $params = $XMLArray['params'];

                $controller = str_replace('/', '\\', $controller);
                $routeArray = [
                    'name' => $routeName,
                    'route' => $route,
                    'controller'=> $controller,
                    'action'=> $action,
                    'params' => $params,
                    'file' => str_replace(ROOT . '/Bundles/', "", $filePath)
                ];
                if($params == null){ $routeArray['params'] = array(); }
                $this->routes[$routeName] = $routeArray;
            }
        }
    }

    protected function add($route, $callable, $routeName, $params = []){
        list($controller, $action) = explode('@', $callable);
        $controller = str_replace('/', '\\', $controller);
        $routeArray = [
            'name' => $routeName,
            'route' => $route,
            'controller'=> $controller,
            'action'=> $action,
            'params' => $params
        ];

        $this->routes[$routeName] = $routeArray;
    }

    public function execute(){
        if( $this->hasMatched ){
            $matchedRoute = $this->matchedRoute;
            $data = $this->data;

            $controller = $matchedRoute['controller'];
            $action = $matchedRoute['action'];

            if($controller == 'FILE:'){
                require $action;
            }elseif($controller == 'STRING:'){
                die( $action );
            }else{
                list($bundle, $ctrler) = explode('\\', $controller);
                if(strpos($bundle, 'Bundle') !== false){
                    $controllerToCall = "Bundles\\" . $bundle . "\\Controller\\" . $ctrler;
                }else{
                    $controllerToCall = $bundle . "\\Controller\\" . $ctrler;
                }
                $class = new $controllerToCall();
                $class->$action($data);
            }
        }else{
            die( App::trans('core:router:noRoutefound') );
        }
    }

    protected function toRegexFromRoute($routeName, $routeArray){
        $parsedRoute = $routeArray['route'];
        foreach ($routeArray['params'] as $paramName => $paramValue){
            $paramName = "{:$paramName}";
            $parsedRoute = str_replace($paramName, $paramValue, $parsedRoute);
        }
        $parsedRoute = trim($parsedRoute,'/');
        $routeRegex = str_replace('/', '\/',$parsedRoute);
        $routeRegex = '#^' . $routeRegex . '$#i';
        $routeArray['parsedRoute'] = $routeRegex;
        return $routeArray;
    }

    public function match(){
        foreach ($this->routes as $routeName => $route){
            $routeArray = $this->toRegexFromRoute($routeName, $route);
            if(preg_match($routeArray['parsedRoute'], $this->url, $params)){
                array_shift($params);
                $i = 0;
                foreach ($routeArray['params'] as $paramName => $paramValue){
                    $formatedArray[$paramName] = $params[$i];
                    $i++;
                }
                $this->hasMatched = true;
                $this->matchedRoute = $routeArray;
                $this->data = $formatedArray;
                return true;
            }
        }
        return false;
    }

    public static function generateUrl($routeName, $params = [], $absoluthPath = true){
        $inst = self::getInstance();
        if(!isset($inst->routes[$routeName])){
            echo '<b>Aucune route associé à ce nom ne correspond !</b>';
            return false;
        }
        $route = $inst->routes[$routeName];
        $routeParams = $route['params'];
        $url = $route['route'];
        $nbRouteParams = count($routeParams);
        $nbFuncParams = count($params);

        if( $nbRouteParams !== $nbFuncParams ){
            echo $nbRouteParams . ' paramètres d\'url sont nécessaires : <b>' . implode(', ', array_keys($routeParams)) . '</b> &nbsp; &nbsp; --> &nbsp; &nbsp; (' . ($nbRouteParams-$nbFuncParams) . ') manquant(s) !</b>';
            return false;
        }
        if(array_keys( $routeParams ) !== array_keys( $params )){
            echo 'Les paramètres attendus sont : <b>' . implode(', ', array_keys($routeParams) ) . '</b><br>';
            echo 'Les paramètres envoyés sont : <b>' . implode(', ', array_keys($params) ) . '</b><br>';
            return false;
        }
        foreach ($route['params'] as $paramRegexName => $paramRegexValue){
            $paramValue = $params[$paramRegexName];
            $paramName = "{:$paramRegexName}";
            $url = str_replace($paramName, $paramValue, $url);
        }

        if($absoluthPath){
            return BASE_URL . $url;
        }else{
            return $url;
        }
    }

    public static function getUrlParams(){
        $requestUri = $_SERVER['REQUEST_URI'];
        $array = explode('?', $requestUri);
        $getParams = end( $array );
        if($getParams){
            $params = explode('&', $getParams);
            foreach ($params as $param){
                list($key, $value) = explode('=', $param);
                $formatedParrameter[ $key ] = $value;
            }
            return $formatedParrameter;
        }
        return false;
    }






    /*
     *
     * Generate and redirect route method
     *
     */

    public static function redirectToRoute($routeName, $params = [], $absoluthPath = true){
        $url = self::generateUrl($routeName, $params, $absoluthPath);
        self::redirect($url);
    }

    public static function redirect($url){
        header('Location: ' . $url);
        exit;
    }

    public static function getCurrentUrl($absoluthPath = true){
        $inst = self::getInstance();
        if($absoluthPath){
            return BASE_URL . $inst->matchedRoute['route'];
        }
        return $inst->matchedRoute['route'];
    }

    public static function getCurrentRoute() {
        $inst = self::getInstance();
        return $inst->matchedRoute;
    }

    public static function getCurrentRouteName() {
        return self::getCurrentRoute()['name'];
    }

    public static function redirectToPreviousRoute(){
        // TODO :
    }







    public function exception($exception){}
}