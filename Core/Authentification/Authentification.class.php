<?php

namespace Core\Authentification;

use App;
use Bundles\UserBundle\Entity\UserEntity;
use Core\Cookie\Cookie;
use Core\Database\Database;
use Core\Session\Session;
use Core\Singleton\Singleton;

class Authentification extends Singleton {

	private $db;
	private $loginError;
	private $connectedUser;

	public function __construct(){
	    $app = App::getInstance();
		$this->db = Database::getInstance();
        $this->session = Session::getInstance();
        $this->cookie = Cookie::getInstance();
        $this->userManager = $app->getManager('UserBundle:User');

		if( !$this->session->has('auth') && $this->cookie->has('authToken') ){
		    $this->cookieReconnect();
        }
	}

	public function cookieReconnect(){
        $cookieValue = $this->cookie->get('authToken');

        $authentification = explode("-/-\-", $cookieValue);
        $userId = $authentification[1];

        $user = $this->userManager->findById($userId);
        if($user){
            $userToken = $this->generateAuthToken($user);
            if($userToken === $cookieValue){
                $this->saveConnection($user);
            }else{
                $this->fatalError('Erreur dans le cookie d\'authentification ! Clés de sécurité invalide');
            }
        }else{
            $this->fatalError('Erreur dans le cookie d\'authentification ! Utilisateur introuvable');
        }
    }

    public function login($email, $password, $remember){
        $user = $this->userManager->findByEmail($email);
        if($user){
            if( $user->getPassword() === $this->encryptPassword($password) ){
                if($remember) {
                    $this->setPermanentConnection($user);
                }

                $this->saveConnection($user);
                return true;
            }
        }
        return false;
    }

    public function forceLogin($email, $remember = false){
        $user = $this->userManager->findByEmail($email);
        if($user){
            if($remember) {
                $this->setPermanentConnection($user);
            }

            $this->saveConnection($user);
            return $user;
        }
        $this->loginError = '<b>No user with this email found!</b>';
        return false;
    }

	public function saveConnection($user){
		$token = $this->generateAuthToken($user);
        $this->session->write('auth', ['token' => $token, 'user' => serialize($user)]);
	}

	public function generateAuthToken($user){
        $userId = $user->getId();
        $userEmail = $user->getEmail();
        $userPassword = $user->getPassword();

		$pass_token = sha1(md5(sha1(md5($userPassword))));
		$email_token = md5(sha1(md5(sha1($userEmail))));
		$separateur = "/@/!\\@\\";
		  
		$token = $pass_token . $email_token . $separateur . $userEmail . "-" . $email_token;
		$token = sha1(sha1(sha1($token))) . "-/-\-" . $userId;

		return $token;
	}

	public function setPermanentConnection($user){
		$token = $this->generateAuthToken($user);

        $this->cookie->write('authToken', $token, time() + 60*60*24*31*3);
	}

	public function encryptPassword($password){
		return sha1($password);
	}

	public function getError(){
		return $this->loginError;
	}

	public function logout(){
		if( $this->cookie->has('authToken') ){
			$this->cookie->delete('authToken');
		}
        return $this->session->delete('auth');
	}

	public function logged(){
		return $this->session->has('auth');
	}

	public function getUserId(){
		if( $this->logged() ){
			return (int) $this->getUser()->getId();
		}else{
			return false;
		}
	}

	public function getUser() {
	    if( $this->logged() ){
            return unserialize( $this->session->get('auth')['user'] );
        }
        return false;
    }

    public function refresh(){
        if( $this->logged() ) {
            $user = $this->userManager->findById($this->getUser()->getId());
            $this->saveConnection($user);
            return $user;
        }
        return false;
    }

    public function fatalError($msg){
        $this->cookie->delete('authToken');
        $this->session->delete('auth');
        die($msg . ' <b>Veuillez rafraichir la page</b> !');
        exit;
    }

    public function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }



}