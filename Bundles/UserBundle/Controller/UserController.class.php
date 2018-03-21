<?php

namespace Bundles\UserBundle\Controller;

use Core\Controller\Controller;

Class UserController extends Controller {



    /**
     * @RouteName test_annotation_route
     * @RouteUrl /bonjour/{:nom}/{:age}
     * @RouteParam :nom ([a-zA-Z])+
     * @RouteParam :age ([0-9])+
     */
    public function showAction($params){

    }

}