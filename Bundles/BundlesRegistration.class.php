<?php

namespace Bundles;

use Core\Bundle\Registration;

Class BundlesRegistration extends Registration {

    public static function registerBundles(){
        $inst = self::getInstance();
        $inst->initialize( $inst->loadBundles() );
    }

    public function loadBundles(){
        return [

            new UserBundle\UserBundle(),
            new AppBundle\AppBundle(),
            new FrameworkBundle\FrameworkBundle(),
            
        ];
    }

    public static function getBundles(){
        return self::getInstance()->loadBundles();
    }

    public static function getControllers(){
        $bundles = self::getInstance()->loadBundles();
        $controllers = [];
        foreach ($bundles as $bundle){
            $controllers[$bundle->getBundleName()] = $bundle->getBundleControllers();
        }
        return $controllers;
    }

    public static function getActions(){
        $bundles = self::getInstance()->loadBundles();
        $actions = [];
        foreach ($bundles as $bundle){
            $actions[$bundle->getBundleName()] = $bundle->getBundleActions();
        }
        return $actions;
    }

    public static function getEntities(){
        $bundles = self::getInstance()->loadBundles();
        $allBundleEntities = [];
        foreach ($bundles as $bundle){
            $allBundleEntities[$bundle->getBundleName()] = $bundle->getBundleEntities();
        }

        return $allBundleEntities;
    }

}