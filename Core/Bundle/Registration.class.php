<?php

namespace Core\Bundle;

use App;
use Core\Singleton\Singleton;

class Registration extends Singleton {

    protected $bundles;

    public function initialize($bundles){
        $app = App::getInstance();
        $this->bundles = $bundles;
        foreach ($this->bundles as $bundle ){
            $app->getTranslations()->addXMLTranslationFile( $bundle->getTranslationFile() );
            $app->getConfig()->addXMLConfigFile( $bundle->getConfigFile() );
            $app->getRouter()->addXMLRoutingFile( $bundle->getRoutingFile() );
            $app->getRouter()->addRoutingAnnotations( $bundle->getRoutingAnnotations() );
        }
    }

}