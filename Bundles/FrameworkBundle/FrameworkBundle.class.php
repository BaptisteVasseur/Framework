<?php

namespace Bundles\FrameworkBundle;

use Core\Bundle\Bundle;

Class FrameworkBundle extends Bundle {

    public function getBundleName(){
        return "FrameworkBundle";
    }

    public function getAuthor(){
        return "oZix_Baptiste";
    }

    public function getLink(){
        return "http://baptiste-vasseur.fr";
    }

    public function getCreationDate(){
        return "12/22/2018";
    }

}