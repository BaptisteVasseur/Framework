<?php

namespace Bundles\UserBundle;

use Core\Bundle\Bundle;

Class UserBundle extends Bundle {

    public function getBundleName(){
        return "UserBundle";
    }

    public function getAuthor(){
        return "oZix_Baptiste";
    }

    public function getLink(){
        return "http://baptiste-vasseur.fr";
    }

    public function getCreationDate(){
        return "02/19/2018";
    }

}