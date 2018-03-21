<?php

namespace Bundles\FrameworkBundle\Controller;

use Bundles\FrameworkBundle\Generate\GenerateAction;
use Bundles\FrameworkBundle\Generate\GenerateBundle;
use Bundles\FrameworkBundle\Generate\GenerateConfig;
use Bundles\FrameworkBundle\Generate\GenerateController;
use Bundles\FrameworkBundle\Generate\GenerateDatabaseConfig;
use Bundles\FrameworkBundle\Generate\GeneratedRoute;
use Bundles\FrameworkBundle\Generate\GenerateEntity;
use Bundles\FrameworkBundle\Generate\GenerateRoute;
use Core\Controller\Controller;
use App;
use Core\Query\Query;
use Core\Request\Request;
use Exception;
use PDO;

class FrameworkFormController extends Controller {

    // Création Bundle
    public function bundleFormAction($params){
        $nom = Request::get('inputNom');
        $auteur = Request::get('inputAuteur');
        $link = Request::get('inputLink');
        $date = (new \DateTime())->format('m/d/y');

        GenerateBundle::generate($nom, $auteur, $link, $date);
        App::redirectToRoute("framework_admin_bundle_route");
    }

    // Suppresion Bundle
    public function bundleRemoveAction($params){
        $bundleName = $params["bundleName"];

        GenerateBundle::delete($bundleName);
        App::redirectToRoute("framework_admin_bundle_route");
    }

    // Création Controller
    public function controllerFormAction($params){
        $nom = Request::get('inputNom');
        $bundle = Request::get('inputBundle');

        GenerateController::generate($nom, $bundle);
        App::redirectToRoute("framework_admin_controller_route");
    }

    // Suppresion Controller
    public function controllerRemoveAction($params){
        $bundleName = $params["bundleName"];
        $controllerName = $params["controllerName"];

        GenerateController::delete($bundleName, $controllerName);
        App::redirectToRoute("framework_admin_controller_route");
    }

    // Création Entité
    public function entityFormAction($params){

        $inputNom = Request::get('inputNom');
        $inputBundle = Request::get('inputBundle');
        $properties = Request::get('inputProperties');

        GenerateEntity::generate($inputNom, $inputBundle, $properties);
        App::redirectToRoute("framework_admin_entity_route");
    }

    // Suppresion Entité
    public function entityRemoveAction($params){
        $bundleName = $params["bundleName"];
        $entityName = $params["entityName"];

        GenerateEntity::delete($bundleName, $entityName);
        App::redirectToRoute("framework_admin_entity_route");
    }

    public function formFormAction($params){
//        $inputNom = Request::get('inputNom');
//        $inputBundle = Request::get('inputBundle');
//        $properties = Request::get('inputProperties');
//
//        GenerateEntity::generate($inputNom, $inputBundle, $properties);
//        App::redirectToRoute("framework_admin_entity_route");
    }

    public function configFormAction($params){
        $inputNom = Query::get('inputNom');
        $inputValue = Query::get('inputValue');
        $inputBundle = Query::get('inputBundle');

        GenerateConfig::generate($inputNom, $inputValue, $inputBundle);
        App::redirectToRoute("framework_admin_config_route");
    }

    public function routeFormAction(){
        $inputNom = Request::get('inputNom');
        $inputSaveBundleName = Request::get('inputSaveBundleName');
        $inputUrl = Request::get('inputUrl');
        $inputBundle = Request::get('inputBundle');
        $inputController = Request::get('inputController');
        $inputAction = Request::get('inputAction');
        $params = Request::get('inputParams'); unset($params['__NAME__']);

        GenerateRoute::generate($inputSaveBundleName, $inputNom, $inputUrl, $inputBundle, $inputController, $inputAction, $params);
        App::redirectToRoute("framework_admin_route_route");
    }

    public function actionFormAction(){
        $bundleName = Query::get('inputBundle');
        $controllerName = Query::get('inputController');
        $actionToCreate = Query::get('inputAction');

        GenerateAction::generate($actionToCreate, $controllerName, $bundleName);
    }

    public function databaseTestConnectionAction(){
        $inputUsername = Query::get('inputUsername');
        $inputPassword = Query::get('inputPassword');
        $inputHost = Query::get('inputHost');
        $inputDBName = Query::get('inputDBName');
        $inputPort = Query::get('inputPort');
        try{
            new PDO("mysql:host=$inputHost:$inputPort;dbname=$inputDBName", $inputUsername , $inputPassword);
            echo "success-with-database";
        }catch (Exception $e){
            try{
                new PDO("mysql:host=$inputHost:$inputPort", $inputUsername , $inputPassword);
                echo "success-without-database";
            }catch (Exception $e){
                echo $e;
            }
        }
    }

    public function databaseCreateDatabaseAction(){
        $inputUsername = Query::get('inputUsername');
        $inputPassword = Query::get('inputPassword');
        $inputHost = Query::get('inputHost');
        $inputDBName = Query::get('inputDBName');
        $inputPort = Query::get('inputPort');

        $pdo = new PDO("mysql:host=$inputHost:$inputPort", $inputUsername , $inputPassword);
        $pdo->query('CREATE DATABASE ' . $inputDBName);

        try{
            $pdo = new PDO("mysql:host=$inputHost:$inputPort;dbname=$inputDBName", $inputUsername , $inputPassword);
            App::database('create', false, $pdo);
        }catch (Exception $e){
            echo $e;
        }
        echo "created";
    }

    public function databaseSaveConfigDatabaseAction(){
        $inputUsername = Query::get('inputUsername');
        $inputPassword = Query::get('inputPassword');
        $inputHost = Query::get('inputHost');
        $inputDBName = Query::get('inputDBName');
        $inputPort = Query::get('inputPort');

        GenerateDatabaseConfig::generate($inputUsername, $inputPassword, $inputHost.':'.$inputPort, $inputDBName);
        echo "saved";
    }


}