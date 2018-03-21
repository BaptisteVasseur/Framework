<?php

namespace Core\Controller;
use \App;
use Core\Singleton\Singleton;

//class Controller extends Singleton {
class Controller {

    protected $app;

    public function __construct()
    {
        $app = App::getInstance();

        $this->cookie = $app->getCookie();
        $this->session = $app->getSession();

        $this->request = $app->getRequest();
        $this->server = $app->getServer();
        $this->query = $app->getQuery();
        $this->config = $app->getConfig();
    }

    public function format_date($date, $format = 1){
        $jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
        $mois_fr = Array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août",
            "Septembre", "Octobre", "Novembre", "Décembre");

        $date = date("w/d/n/Y/G/i", strtotime($date));
        list($nom_jour, $jour, $mois, $annee, $hours, $minutes) = explode('/', $date);

        if($format == 1){
            return $jour_fr[$nom_jour] . ' ' . $jour . ' ' . $mois_fr[$mois] . ' ' . $annee . ' à ' . $hours . 'h' . $minutes;
        }elseif($format == 2){
            return $jour . ' ' . $mois_fr[$mois] . ' ' . $annee;
        }elseif($format == 3){
            return $jour_fr[$nom_jour] . ' ' . $jour . ' ' . $mois_fr[$mois] . ' ' . $annee;
        }elseif($format == 4){
            return $annee;
        }else{
            return $date;
        }
    }

    public static function renderController($callable, $datas = [])
    {
        list($controller, $action) = explode('@', $callable);
        list($bundle, $controllerName) = explode(':', $controller);

        $classToCall = "bundles/" . $bundle . "/Controller/" . $controllerName;
        $classToCall = str_replace("/", "\\", $classToCall);

        $class = new $classToCall();
        return $class->$action($datas);
    }

    public function render($template, $templateVars = []){

        if(!isset($GLOBALS['templateVars']) or empty($GLOBALS['templateVars'])){
            $GLOBALS['templateVars'] = [];
        }

        if(isset($GLOBALS['templateVars']) and !empty($GLOBALS['templateVars'])){
            $templateVars = array_merge($templateVars, $GLOBALS['templateVars']);
        }

        $GLOBALS['templateVars'] = $templateVars;

        if(substr_count($template, ":") == 1){
            list($bundle, $ressourceName) = explode(':', $template);
        }else{
            list($bundle, $folder ,$ressourceName) = explode(':', $template);
            $folder .= "/";
        }

        $filePath = ROOT . "/Bundles/" . $bundle . "/Views/" . $folder . $ressourceName . '.php';

        $content = ob_get_contents();

        extract($templateVars);
        if ( !file_exists($filePath) ){
            die('Template <b>' . $template . '.php' . '</b> introuvable !');
        }else{
            require($filePath);
        }
        return '';
    }

    public function getRessource($ressource, $justURL = false){
        list($bundle, $typeRessource, $ressourceName) = explode(':', $ressource);

        $path = BASE_URL . '/Bundles/' . $bundle . "/Assets/" . $typeRessource . "/" . $ressourceName;
        $absolutePath = ROOT . '/Bundles/' . $bundle . "/Assets/" . $typeRessource . "/" . $ressourceName;

        if( !file_exists($absolutePath) ){
            return "Error ! File : <b>" . $path . "</b> unavailable.";
        }

        if($typeRessource == 'js' and !$justURL){
            $return = "<script src='$path'  type='text/javascript'></script>";
        }elseif($typeRessource == 'css' and !$justURL){
            $return = "<link href='$path' rel='stylesheet'/>";
        }else{
            $return = $path;
        }

        return $return;
    }

    public function getForm( $formPath, $formName, $datas ){
        list($type, $bundle, $formFileName) = explode(':', $formPath);
        $formClass = implode('\\', [$type, $bundle, 'Form', $formFileName]);
        $method = $formName . "Form";
        $class = new $formClass('', $datas);

        return $class->$method();
    }

}