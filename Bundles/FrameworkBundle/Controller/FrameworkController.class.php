<?php

namespace Bundles\FrameworkBundle\Controller;

use App;
use Bundles\BundlesRegistration;
use Core\Bundle\Registration;
use Core\Collection\OneToManyCollection;
use Core\Collection\OneToOneCollection;
use Core\Config\Config;
use Core\Controller\Controller;
use Core\Installation\Installation;
use Core\Query\Query;
use Core\Router\Router;
use Core\Utils\Utils;

class FrameworkController extends Controller {

    /**
     * @RouteName index_test
     * @RouteUrl /aby/{:id}
     * @RouteParam id ([0-9])+
     */
    public function indexAction($params){
        return $this->render('frameworkBundle:dashboard');
    }

    public function sidebar(){

        $databaseNotUpToDate = ! App::database('', false)->compareDatabaseScript();

        return $this->render('frameworkBundle:Includes:sidebar', [
            'databaseNotUpToDate' => $databaseNotUpToDate
        ]);
    }

    public function bundleListAction($params){
        $bundlesList = BundlesRegistration::getBundles();
        foreach ($bundlesList as $index => $bundle){
            $bundles[$index]['name'] = $bundle->getBundleName();
            $bundles[$index]['author'] = $bundle->getAuthor();
            $bundles[$index]['link'] = $bundle->getLink();
            $bundles[$index]['date'] = $bundle->getCreationDate();
        }

        return $this->render('frameworkBundle:bundle', ["bundles" => $bundles]);
    }

    public function entityListAction($params){
        $bundlesList = BundlesRegistration::getBundles();
        $allBundleEntities = BundlesRegistration::getEntities();

        foreach ($allBundleEntities as $bundleName => $bundleEntities){

            $nbOneToMany = 0;
            $nbOneToOne = 0;
            foreach ($bundleEntities as $k => $entity){
                $class = "Bundles\\" . $bundleName . "\\Entity\\" . $entity;
                $entityProperties = (new $class())->getEntityVars();

                foreach ($entityProperties as $propertyName => $propertyValue){
                    if($propertyValue instanceof OneToManyCollection){
                        $nbOneToMany += 1;
                    }elseif($propertyValue instanceof OneToOneCollection){
                        $nbOneToOne += 1;
                    }
                }

                $allBundleEntities[$bundleName][$k] = ['name' => $entity, 'properties' => $entityProperties, 'nbOneToMany' => $nbOneToMany, 'nbOneToOne' => $nbOneToOne];
            }
        }

        foreach ($bundlesList as $bundle){ $bundles[] = $bundle->getBundleName(); }
        return $this->render('frameworkBundle:entity', ['bundles' => $bundles, "allBundleEntities" => $allBundleEntities]);
    }

    public function controllerListAction($params){
        $bundlesList = BundlesRegistration::getBundles();
        $controllerList = BundlesRegistration::getControllers();

        foreach ($bundlesList as $bundle){ $bundles[] = $bundle->getBundleName(); }
        return $this->render('frameworkBundle:controller', [
            "bundles" => $bundles,
            "bundleControllers" => $controllerList
        ]);
    }

    public function formListAction($params){
        return $this->render('frameworkBundle:form');
    }

    public function configlistAction($params){
        $bundlesList = BundlesRegistration::getBundles();
        foreach ($bundlesList as $bundle){ $bundles[] = $bundle->getBundleName(); }

        $config = Config::all();
        $count = (count($config) == 1 OR count($config) == 0) ? 1 : count($config) / 2 ;
        list($config1, $config2) = array_chunk( $config, $count, true);
        return $this->render('frameworkBundle:config', ['params1' => $config1, 'params2' => $config2, "bundles" => $bundles]);
    }

    public function routeListAction($params){
        $bundlesList = BundlesRegistration::getBundles();
        $routes = Router::getRoutes();
        unset($routes['']);

        usort($routes, function($a, $b){
            return strcmp($a['route'], $b['route']);
        });

        foreach ($bundlesList as $bundle){ $bundles[] = $bundle->getBundleName(); }
        return $this->render('frameworkBundle:route', [
            "bundles" => $bundles,
            "routes" => $routes,
        ]);
    }



    // Retrive list of element for display

    public function getBundleListAction(){
        $bundles = BundlesRegistration::getBundles();

        foreach ($bundles as $bundle){
            echo "<option value='{$bundles->getBundleName()}'>{$controller->getBundleName()}</option>";
        }
    }

    public function getControllerListAction(){
        $bundleName = Query::get('bundleName');
        $controllerList = BundlesRegistration::getControllers();

        $controllers = $controllerList[$bundleName];
        if(empty($controllers)){ echo "<option>Aucun controller !</option>"; }else{ echo "<option>Choisir un Controller...</option>"; }
        foreach ($controllers as $controller){
            echo "<option value='$controller'>$controller</option>";
        }
    }

    public function getEntityListAction(){
        $bundleName = Query::get('bundleName');
        $entitiesList = BundlesRegistration::getEntities();

        $entities = $entitiesList[$bundleName];
        if(empty($entities)){ echo "<option>Aucune entitée !</option>"; }else{ echo "<option>Choisir une Entitée...</option>"; }
        foreach ($entities as $entity){
            echo "<option value='$entity'>$entity</option>";
        }
    }

    public function getActionListAction(){
        $bundleName = Query::get('bundleName');
        $controllerName = Query::get('controllerName');
        $actionList = BundlesRegistration::getActions();

        $actions = $actionList[$bundleName][$controllerName];
        if(empty($actions)){ echo "<option>Aucune action !</option>"; }else{ echo "<option>Choisir une Action...</option>"; }
        foreach ($actions as $action){
            echo "<option value='$action'>$action</option>";
        }
    }

    public function databaseAction($params){
        $databaseNotUpToDate = ! App::database('', false)->compareDatabaseScript();
        if( (new Installation())->databaseConfigurationIsSet() ){
            $config = Installation::getInstance();
            $dbUsername = $config->get('db_user');
            $dbPassword = $config->get('db_pass');
            list($dbHost, $dbPort) = explode(':', $config->get('db_host'));
            $dbName = $config->get('db_name');
        }else{
            $dbUsername = "";
            //$dbPassword = (Utils::getOS() == "Mac OS") ? 'root' : '' ;
            $dbPassword = "";
            $dbHost = "";
            $dbPort = "";
            $dbName = "";
        }
        $schema = App::database('', false)->generateSchemaAction();

        App::database('', false)->getDatabaseScriptUpdate();

        $forceModalDisplay = Query::has('show');

        return $this->render('frameworkBundle:database', [
            'dbUsername' => $dbUsername,
            'dbPassword' => $dbPassword,
            'dbHost' => $dbHost,
            'dbPort' => $dbPort,
            'dbName' => $dbName,
            'schema' => $schema,
            'forceModalDisplay' => $forceModalDisplay,
            'databaseNotUpToDate' => $databaseNotUpToDate
        ]);
    }

    public function getDatabaseUpdateQueriesAction(){
        $fullArray = App::database('', false)->getDatabaseScriptUpdate();
        if(!empty($fullArray)){
            foreach ($fullArray as $line){
                echo $line . "<br>";
            }
        }else{
            echo "La base de données est à jour.";
        }
    }

    public function databaseDumpAction($params){
        list($queries, $modifiers) = (App::database('', false))->getDump();
        header("Content-type: application/force-download" );
        header("Content-Disposition: attachment; filename=database-export.sql" );
        print( implode(PHP_EOL, $queries) . PHP_EOL . implode(PHP_EOL, $modifiers) );
    }

    public function databaseUpdateAction(){
        App::database('updateSchema');
        App::redirectToRoute("framework_admin_database_route");
    }

    public function databaseResetAction(){
        App::database('resetDatabase');
        App::redirectToRoute("framework_admin_database_route");
    }


}