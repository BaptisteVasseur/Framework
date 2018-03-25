<?php

namespace Bundles\UserBundle\Controller;

use App;
use Bundles\UserBundle\Entity\UserEntity;
use Core\Authentification\Authentification;
use Core\Config\Config;
use Core\Controller\Controller;
use Core\Email\Email;
use Core\Email\Exception;
use Core\Router\Router;
use Core\Server\Server;
use Core\Session\Session;

Class LoginController extends Controller {

    public function loginAction($params){

        $form = $this->getForm('UserBundle:LoginForm', 'login', $_POST);
        $auth = App::getAuthentification();

        if( $this->request->is('post') ){
            if( $form->isValid() ){
                $data = $form->getData();
                if( ! $auth->login($data['email'], $data['password'], $data['remember'] ) ){
                    $auth->logout(); $form->error( $auth->getError() );
                }
            }else{  $form->error( $form->getErrors() ); }
        }

        if($auth->logged()){
            App::redirect( BASE_URL );
        }

        return $this->render( Config::get('userBundle:template_login') , [
            'form' => $form->render(true)
        ]);
    }

    public function logoutAction(){
        Authentification::getInstance()->logout();
        App::redirectToRoute('login');
    }

    public function registerAction()
    {
        if(App::getUser()){
            App::redirect( BASE_URL );
        }

        $form = $this->getForm('UserBundle:LoginForm', 'register', $_POST);
        $userManager = App::getManager('UserBundle:user');
        $auth = App::getAuthentification();

        if ($this->request->is('post')) {

            $data = $form->getData();
            $plainPassword = $data['password'];
            $newPassword = $auth->encryptPassword($data['password']);
            $repeatPassword = $auth->encryptPassword($data['repeatPassword']);
            $email = $data['email'];
            $nom = $data['nom'];

            $hasUserWithThisEmail = $userManager->has(['email' => $email]);
            $form->isEqual($newPassword, $repeatPassword, '<b>Les deux mots de passe ne correspondent pas !</b>');
            $form->databaseInteraction(!$hasUserWithThisEmail, '<b>Un utilisateur avec cette adresse email existe déjà !</b>');

            if ($form->isValid()) {
                $user = new UserEntity();
                $user->setNom($nom);
                $user->setEmail($email);
                $user->setPlainPassword($plainPassword);
                $user->addRole('ROLE_USER');

                $userManager->persist($user);
                $userManager->save();

                $createdUser = $userManager->findByEmail($email);

                $mail = new Email(true);
                $mail->addAddress($email);
                if( Config::get('userBundle:accountConfirmationRequired') == 'true' ){
                    try {
                        list($token, $userId) = explode('-/-\-', $auth->generateAuthToken($createdUser));
                        $lien = App::generateUrl('validAccount', ['userId' => $userId, 'token' => $token]);
                        $mail->setContent( $this->render('userBundle:emails:validAccount', [
                            'nom' => $nom,
                            'email' => $email,
                            'password' => $plainPassword,
                            'lien' => $lien
                        ], true) );
                        $mail->setSubject("Vérification de votre compte.");
                        $mail->send();

                        $createdUser->setValidationDate("0000-00-00 00:00:00");
                        $userManager->save();
                       Session::success("<b>Une email contenant un lien pour vérifier votre compte vous à été envoyé.</b>");
                       App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error("Échec de l'inscription. <b>" . $mail->ErrorInfo . "</b>");
                    }
                }else{
                    try {
                        $mail->setContent( $this->render('userBundle:emails:register', [
                            'nom' => $nom,
                            'email' => $email,
                            'password' => $plainPassword
                        ], true) );
                        $mail->setSubject("Bienvenue sur " . Config::get('app:Email_SiteName'));
                        $mail->send();

                        $createdUser->setValidationDate( date("Y-m-d H:i:s") );
                        $userManager->save();
                       Session::success('<b>Votre compte a bien été créer. Vous pouvez dés maintenant vous connecter à votre compte.</b>');
                       App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error("Échec de l'inscription ! Veuillez réessayer plus tard.");
                    }
                }
            } else {
                $form->error( $form->getErrors() );
            }
        }

        return $this->render( Config::get('userBundle:template_register') , [
            'form' => $form->render(true)
        ]);
    }

    public function validAccountAction($data){

        if(App::getUser()){
            App::redirect( BASE_URL );
        }

        $urlToken = $data['token'];
        $userId = $data['userId'];

        $userManager = App::getTable('UserBundle:user');
        if( $userManager->has(['id' => $userId, 'validationDate' => '0000-00-00 00:00:00']) ) {

            $user = $userManager->findById($userId);
            $auth = App::getAuthentification();
            list($token, $id) = explode('-/-\-', $auth->generateAuthToken($user) );

            if ($token == $urlToken) {
                $validationDate = date("Y-m-d H:i:s");
                $user->setValidationDate( $validationDate );
                $userManager->save();

                if ( $userManager->has(['id' => $userId, 'validationDate' => $validationDate]) ) {
                    Session::success('Votre compte à bien été confirmé. Vous pouvez maintenant vous connecter avec vos identifiants de connexion.');
                    App::redirectToRoute('login');
                } else {
                    App::forbidden('Un bug est survenu lors de la confirmation de votre compte ! Réessayer plus tard !');
                }
            }else{
                App::forbidden('La clé de validation de votre compte est incorrect !');
            }
        }else{
            App::forbidden('Votre compte à déjà été validé !');
        }
    }

    public function forgotPasswordAction(){

        if(App::getUser()){
            App::redirect( BASE_URL );
        }

        $form = $this->getForm('UserBundle:LoginForm', 'forgotPassword', $_POST);

        $auth = App::getAuthentification();
        $userManager = App::getTable('UserBundle:user');

        if( $auth->logged() ){ Router::redirectToPreviousRoute(); }

        if( $this->request->is('post') ){

            $generatedPassword = $auth->generatePassword();
            $datas = $form->getData();
            $emailData = $datas['email'];

            $hasUserWithThisEmail = $userManager->has(['email' => $emailData]);
            $form->databaseInteraction( $hasUserWithThisEmail , '<b>Aucun compte avec cette adresse email n\'à été trouvé !</b>' );

            if( $form->isValid() ){
                $user = $userManager->findByEmail($emailData);
                $form->unsetData('email');

                if( Config::get('userBundle:generateNewPasswordWhenForgot') == 'true' ){
                    $emailContent = $this->render('userBundle:emails:forgot', ['generatedPassword' => $generatedPassword], true);
                    $mail = new Email(true);
                    try {
                        $mail->addAddress($datas['email']);
                        $mail->setContent($emailContent);
                        $mail->setSubject("Réinitialisation de votre mot de passe.");
                        $mail->send();

                        $user->setPlainPassword($generatedPassword);
                        $userManager->save();

                        Session::success('<b>Un email contenant un code provisoire vous a été envoyé !</b>');
                        App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error( "L'email n'à pas pu être envoyé ! <b>" . $mail->ErrorInfo . "</b>");
                    }
                }else{
                    list($token, $userId) = explode('-/-\-', $auth->generateAuthToken($user));
                    $lien = App::generateUrl('resetPassword', ['userId' => $userId, 'token' => $token]);
                    $emailContent = $this->render('userBundle:emails:reset', ['lien' => $lien], true);
                    $mail = new Email(true);
                    try {
                        $mail->addAddress($datas['email']);
                        $mail->setContent($emailContent);
                        $mail->setSubject("Réinitialisation de votre mot de passe.");
                        $mail->send();

                        Session::success('<b>Un email contenant un lien pour réinitialiser votre mot de passe vous a été envoyé !</b>');
                        App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error( "L'email n'à pas pu être envoyé ! <b>" . $mail->ErrorInfo . "</b>");
                    }
                }
            }else{ $form->error( $form->getErrors() ); }
        }

        return $this->render( Config::get('userBundle:template_forgot') , [
            'form' => $form->render(true)
        ]);
    }

    public function resetPasswordAction($params){

        if(App::getUser()){
            App::redirect( BASE_URL );
        }

        $form = $this->getForm('UserBundle:LoginForm', 'resetPassword', $_POST);

        $auth = App::getAuthentification();
        $userManager = App::getTable('UserBundle:user');
        $user = $userManager->findById($params['userId']);
        list($token, $userId) = explode('-/-\-', $auth->generateAuthToken($user));

        if($token == $params['token']){
            if( $this->request->is('post') ){
                $data = $form->getData();
                $form->isEqual($data['newPassword'], $data['repeatPassword'], "Les deux mots de passe ne correspondent pas !");

                if( $form->isValid() ){
                    $user->setPlainPassword($data['newPassword']);
                    $userManager->save();

                    $mail = new Email();
                    $mail->setSubject('Changement de votre mot de passe.');
                    $mail->setContent( $this->render('userBundle:emails:changePassword', [
                        'nom' => $user->getNom(),
                        'ip' => Server::getClientIp(),
                        'password' => $data['newPassword']
                    ], true) );
                    $mail->addAddress($user->getEmail());
                    $mail->send();

                    Session::success("<b>Votre mot de passe à bien été modifié. Veuillez maintenant vous connecter.</b>");
                    App::redirectToRoute('login');

                }else{
                    $form->error( $form->getErrors() );
                }
            }
        }else{
            App::forbidden("Erreur ! La clé de réinitialisation de votre mot de passe est incorrect.");
        }

        return $this->render( Config::get('userBundle:template_reset') , [
            'form' => $form->render(true)
        ]);
    }

}