<?php

namespace Bundles\UserBundle\Entity;

use Core\Collection\ArrayCollection;
use Core\Collection\OneToManyCollection;
use Core\Collection\OneToOneCollection;
use \Core\Entity\Entity;
use Core\Security\Security;

/**
 * @Entity lol
 */
class UserEntity extends Entity {


    /**
     * @Type identifier
     */
    protected $id;

    /**
     * @Type string
     * @Length 255
     */
    protected $nom;

    /**
     * @Type string
     * @Length 255
     */
    protected $email;

    /**
     * @Type string
     * @Length 255
     */
    protected $password;

    /**
     * @Type array
     * @Default {"ROLE_ADMIN"}
     */
    protected $roles;

    /**
     * @Type datetime
     * @Nullable true
     */
    protected $validationDate;

    /**
     * @Relation OneToOne
     * @Target AppBundle:ExtensionEntity
     * @Nullable true
     */
    protected $extension;

    /**
     * @Relation OneToMany
     * @Target AppBundle:ProjectEntity
     */
    protected $project;

    /**
     * @Type text
     * @Nullable true
     */
    protected $description;



    public function __construct()
    {
        $this->project = new OneToManyCollection('project', $this);
        $this->extension = new OneToOneCollection('extension', $this);
    }

    /*
     * Getters and Setters
     */

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; return $this; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; return $this; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; return $this; }

    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; return $this; }
    public function setPlainPassword($password) { $this->password = sha1($password); return $this; }

    public function getValidationDate() { return $this->validationDate; }
    public function setValidationDate($validationDate) { $this->validationDate = $validationDate; return $this; }

    public function getExtension() { return $this->extension->get(); }
    public function setExtension($extension) { return $this->extension->set($extension); }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; return $this; }

    public function addProject($project) { return $this->project->add($project); }
    public function removeProject($project) { return $this->project->remove($project); }
    public function getProjects() { return $this->project->instance(); }

    public function getRoles() {
        return $this->roles;
    }

    public function addRole($roles) {
        if( is_array($roles) ){
            $res = Security::convertRolesToArray($this->roles);
            foreach ($roles as $role){
                $res[$role] = $role;
            }
        }else{
            $res = Security::convertRolesToArray($this->roles);
            $res[$roles] = $roles;
        }

        $this->roles = '{ ' . implode(', ', $res) . ' }';
        return $this;
    }

    public function removeRole($roles) {
        if( is_array($roles) ){
            $res = Security::convertRolesToArray($this->roles);
            foreach ($roles as $role){
                unset($res[$role]);
            }
        }else{
            $res = Security::convertRolesToArray($this->roles);
            unset($res[$roles]);
        }
        $this->roles = '{ ' . implode(', ', $res) . ' }';
        return $this;
    }

    public function hasRole($role) {
        $rolesList = Security::convertRolesToArray($this->roles);
        foreach ($rolesList as $value){
            $childRoles = Security::getInstance()->getChildRoles( trim($value) );
            if($childRoles){
                foreach ($childRoles as $childRole){
                    $childRole = trim($childRole);
                    $rolesList[ $childRole ] = $childRole;
                }
            }
        }
        return (isset($rolesList[ $role ]) AND !empty($rolesList[ $role ]));
    }
}