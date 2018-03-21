<?php

use Bundles\BundlesRegistration;
use Bundles\UserBundle\Entity\UserEntity;
use Core\Authentification\Authentification;
use Core\ClassReader\ClassReader;
use Core\Database\Database;
use Core\Form\Form;
use Core\Query\Query;
use Core\Router\Router;
use Core\Security\Security;

//$inst = Security::getInstance();

//$auth = new Authentification();
//$auth->forceLogin('bvasseur77@gmail.com', true);
//var_dump($auth->logged());

////$auth->logout();
//$user = $auth->refresh();


//var_dump( App::getConfig()->get('form_Google_Public_Key') );

//$cr = new ClassReader('Bundles\\UserBundle\\Controller\\UserController');
//var_dump($cr->getMethodsAnnotation());

$content = ob_get_clean();
require 'template.php';

