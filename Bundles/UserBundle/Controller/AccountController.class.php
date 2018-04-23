<?php

namespace Bundles\UserBundle\Controller;

use App;
use Bundles\UserBundle\Entity\UserEntity;
use Core\Authentification\Authentification;
use Core\Config\Config;
use Core\Controller\Controller;
use Core\Database\Database;
use Core\Email\Email;
use Core\Email\Exception;
use Core\Email\PHPMailer;
use Core\Request\Request;
use Core\Server\Server;
use Core\Session\Session;
use Core\Translations\Translations;

Class AccountController extends Controller {

    public function __construct()
    {
        if( !App::getUser() ){
            App::redirectToRoute('login');
        }
        parent::__construct();
    }

    public function changePasswordAction(){

        $form = $this->getForm('userBundle:AccountForm', 'changePassword', $_POST);

        if( $this->request->is('post') ){
            $auth = App::getAuthentification();
            $userManager = App::getTable('userBundle:user');
            $currentUser = $auth->getUser();
            $currentUserPassword = $currentUser->getPassword();

            $data = $form->getData();
            $form->isEqual($currentUserPassword, $auth->encryptPassword($data['oldPassword']), App::translate('userBundle:error_invalidOldPassword'));
            $form->isEqual($data['newPassword'], $data['repeatPassword'], App::translate('userBundle:error_passwordDoesntMatch'));

            if( $form->isValid() ){

                $mail = new Email();
                $mail->setSubject( App::translate('userBundle:email_subject_passwordChanged') );
                $mail->setContent( $this->render('userBundle:emails:changePassword', [
                    'nom' => $currentUser->getNom(),
                    'ip' => Server::getClientIp(),
                    'password' => $data['newPassword']
                ], true) );
                $mail->addAddress($currentUser->getEmail());
                $mail->send();

                $userManager->update($currentUser, ['password' => $auth->encryptPassword($data['newPassword'])]);
                $form->success( App::translate('userBundle:success_passwordChanged') );

            }else{
                $form->error( $form->getErrors() );
            }
        }

        return $this->render( Config::get('userBundle:template_changePassword') , [
            'form' => $form->render(true)
        ]);
    }

    public function changeEmailAction(){

        $form = $this->getForm('userBundle:AccountForm', 'changeEmail', $_POST);
        $auth = App::getAuthentification();
        $currentUser = $auth->getUser();
        $currentUserEmail = $currentUser->getEmail();

        $form->setData('oldEmail', $currentUserEmail);

        if( $this->request->is('post') ){
            $data = $form->getData();
            $userManager = App::getTable('userBundle:user');
            $hasUser = $userManager->has(['email' => $data['newEmail'] ]);
            $form->databaseInteraction( !$hasUser , App::translate('userBundle:error_emailAlreadyInDatabase') );

            if( $form->isValid() ){

            }else{
                $form->error( $form->getErrors() );
            }
        }

        return $this->render( Config::get('userBundle:template_changeEmail') , [
            'form' => $form->render(true)
        ]);
    }

    public function changePseudoAction(){

    }

}