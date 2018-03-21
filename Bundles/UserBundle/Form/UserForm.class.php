<?php

namespace Bundles\UserBundle\Form;
use Core\Form\Form;
use Core\Form\Validation;

class UserForm extends Form
{

    /*
     *
     * Connexion - formulaire & validation
     *
     */

    public function loginForm()
    {
        $this->setAction('#')->setMethod('POST')->setFormId('loginForm');
        $this->email('email', 'Email :');
        $this->password('password', 'Mot de passe :');
        $this->checkbox('remember', 'Se souvenir de moi');
        $this->submit('Se connecter');

        $this->loginFormValidation();

        return $this;
    }

    public function loginFormValidation()
    {
        $validation = new Validation( $this );
        $validation->isEmail('email');
        $validation->isPassword('password');

        $this->validation = $validation;
    }

    /*
     *
     * Mot de passe oublié - formulaire & validation
     *
     */

    public function forgotPasswordForm()
    {
        $this->setAction('#')->setMethod('POST')->setFormId('forgotPasswordForm');
        $this->email('email', 'Saisir votre Adresse Email :');
        $this->captcha();
        $this->submit('Réintialiser mon MDP');

        $this->forgotPasswordFormValidation();
        return $this;
    }

    public function forgotPasswordFormValidation()
    {
        $validation = new Validation( $this );
        $validation->isEmail('email');
        $validation->isValidCaptcha( $validation::GOOGLE_CAPTCHA_NAME );

        $this->validation = $validation;
    }

    /*
     *
     * Mot de passe oublié - formulaire & validation
     *
     */

    public function registerForm()
    {
        $this->text('nom', 'Nom :');
        $this->email('email', 'Email :');
        $this->password('password', 'Mot de passe :');
        $this->password('repeatPassword', 'Retapez votre mot de passe :');
        $this->captcha();
        $this->submit("Inscription");

        $this->registerFormValidation();
        return $this;
    }

    public function registerFormValidation()
    {
        $validation = new Validation( $this );

        $validation->isText('nom');
        $validation->isEmail('email');
        $validation->isPassword('password');
        $validation->isPassword('repeatPassword');
        $validation->isValidCaptcha( $validation::GOOGLE_CAPTCHA_NAME);

        $this->validation = $validation;
    }

    /*
     *
     * Changement de mot de passe - formulaire & validation
     *
     */

    public function changePasswordForm()
    {
        $this->setAction('#')->setMethod('POST')->setFormId('contactForm');
        $this->password('oldPassword', 'Ancien Mot de passe :');
        $this->password('newPassword', 'Nouveau Mot de passe :');
        $this->password('repeatPassword', 'Retapez votre nouveau Mot de passe :');
        $this->submit('Modifier mon MDP');

        $this->changePasswordFormValidation();
        return $this;
    }

    public function changePasswordFormValidation()
    {
        $validation = new Validation( $this );

        $validation->isPassword('oldPassword');
        $validation->isPassword('newPassword');
        $validation->isPassword('repeatPassword');

        $this->validation = $validation;
    }




}