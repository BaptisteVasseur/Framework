<?php

namespace Bundles\UserBundle\Model;

use \Core\Model\Model;

class UserModel extends Model {

    public function findByUsername(string $username){
        return $this->findOneBy(['nom' => $username]);
    }

    public function findByEmail(string $email){
        return $this->findOneBy(['email' => $email]);
    }

}