<?php

namespace Bundles\AppBundle;

use Core\Bundle\Bundle;

Class AppBundle extends Bundle {

    public function getBundleName(){
        return "AppBundle";
    }

    public function getAuthor(){
        return "oZix_Baptiste";
    }

    public function getLink(){
        return "http://baptiste-vasseur.fr";
    }

    public function getCreationDate(){
        return "03/12/2018";
    }

}