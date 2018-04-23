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
                    $auth->logout(); $form->error( App::translate('userBundle:error_invalidEmailOrPassword') );
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
            $form->isEqual($newPassword, $repeatPassword, App::translate("userBundle:error_passwordDoesntMatch"));
            $form->databaseInteraction(!$hasUserWithThisEmail, App::translate("userBundle:error_emailAlreadyInDatabase"));

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
                        $mail->setSubject( App::translate('userBundle:email_subject_verification') );
                        $mail->send();

                        $createdUser->setValidationDate("0000-00-00 00:00:00");
                        $userManager->save();
                       Session::success( App::translate('userBundle:success_verificationEmailSend') );
                       App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error( App::translate('userBundle:error_registrationFailed', [$mail->ErrorInfo]) );
                    }
                }else{
                    try {
                        $mail->setContent( $this->render('userBundle:emails:register', [
                            'nom' => $nom,
                            'email' => $email,
                            'password' => $plainPassword
                        ], true) );
                        $mail->setSubject( App::translate('userBundle:email_subject_welcome', [ Config::get('app:email_SiteName') ]));
                        $mail->send();

                        $createdUser->setValidationDate( date("Y-m-d H:i:s") );
                        $userManager->save();
                       Session::success( App::translate('userBundle:success_registerSucceed') );
                       App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error(App::translate('userBundle:error_registrationFailed', [$mail->ErrorInfo]));
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
                    Session::success( App::translate('userBundle:success_accountConfirmed') );
                    App::redirectToRoute('login');
                } else {
                    App::forbidden( App::translate('userBundle:error_confirmationFailed') );
                }
            }else{
                App::forbidden( App::translate('userBundle:error_invalidToken') );
            }
        }else{
            App::forbidden( App::translate('userBundle:error_accountAlreadyConfirmed') );
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
            $form->databaseInteraction( $hasUserWithThisEmail ,  App::translate('userBundle:error_noEmailFound')  );

            if( $form->isValid() ){
                $user = $userManager->findByEmail($emailData);
                $form->unsetData('email');

                if( Config::get('userBundle:generateNewPasswordWhenForgot') == 'true' ){
                    $emailContent = $this->render('userBundle:emails:forgot', ['generatedPassword' => $generatedPassword], true);
                    $mail = new Email(true);
                    try {
                        $mail->addAddress($datas['email']);
                        $mail->setContent($emailContent);
                        $mail->setSubject( App::translate('userBundle:email_subject_forgotPassword') );
                        $mail->send();

                        $user->setPlainPassword($generatedPassword);
                        $userManager->save();

                        Session::success( App::translate('userBundle:success_newPasswordSend') );
                        App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error( App::translate('userBundle:error_emailSendingFailed', [$mail->ErrorInfo]) );
                    }
                }else{
                    list($token, $userId) = explode('-/-\-', $auth->generateAuthToken($user));
                    $lien = App::generateUrl('resetPassword', ['userId' => $userId, 'token' => $token]);
                    $emailContent = $this->render('userBundle:emails:reset', ['lien' => $lien], true);
                    $mail = new Email(true);
                    try {
                        $mail->addAddress($datas['email']);
                        $mail->setContent($emailContent);
                        $mail->setSubject( App::translate('userBundle:email_subject_forgotPassword') );
                        $mail->send();

                        Session::success( App::translate('userBundle:success_resetLinkSend') );
                        App::redirectToRoute('login');
                    }catch (Exception $e){
                        $form->error(  App::translate('userBundle:error_emailSendingFailed', [$mail->ErrorInfo]) );
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
                $form->isEqual($data['newPassword'], $data['repeatPassword'],  App::translate('userBundle:error_passwordDoesntMatch') );

                if( $form->isValid() ){
                    $user->setPlainPassword($data['newPassword']);
                    $userManager->save();

                    $mail = new Email();
                    $mail->setSubject( App::translate('userBundle:email_subject_passwordChanged') );
                    $mail->setContent( $this->render('userBundle:emails:changePassword', [
                        'nom' => $user->getNom(),
                        'ip' => Server::getClientIp(),
                        'password' => $data['newPassword']
                    ], true) );
                    $mail->addAddress($user->getEmail());
                    $mail->send();

                    Session::success( App::translate('userBundle:success_passwordReset') );
                    App::redirectToRoute('login');

                }else{
                    $form->error( $form->getErrors() );
                }
            }
        }else{
            App::forbidden( App::translate('userBundle:error_invalidResetToken') );
        }

        return $this->render( Config::get('userBundle:template_reset') , [
            'form' => $form->render(true)
        ]);
    }

}