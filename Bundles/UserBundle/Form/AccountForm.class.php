<?php

namespace Bundles\UserBundle\Form;
use Core\Form\Form;
use Core\Form\Validation;

class AccountForm extends Form
{
    /*
     *
     * Changement de mot de passe - formulaire & validation
     *
     */

    public function changePasswordForm()
    {
        $this->setAction('#')->setMethod('POST')->setFormId('changePasswordForm');
        $this->password('oldPassword', 'Ancien Mot de passe :');
        $this->password('newPassword', 'Nouveau Mot de passe :');
        $this->password('repeatPassword', 'Retapez votre nouveau Mot de passe :');
        $this->submit('Modifier mon mot de passe');

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

    public function changeEmailForm()
    {
        $this->setAction('#')->setMethod('POST')->setFormId('changeEmailForm');
        $this->email('oldEmail', 'Ancienne adresse email :', true, null, ['disabled' => 'disabled', 'class' => 'form-control']);
        $this->email('newEmail', 'Nouvelle adresse email :');
        $this->submit('Modifier mon adresse email');

        $this->changeEmailFormValidation();
        return $this;
    }

    public function changeEmailFormValidation()
    {
        $validation = new Validation( $this );

        $validation->isEmail('newEmail');

        $this->validation = $validation;
    }




}