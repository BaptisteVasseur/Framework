<?php

namespace Core\Email;

use Core\Config\Config;

class Email extends PHPMailer {


    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);

        $this->senderName = Config::get('App:email_SenderName');
        $this->senderEmail = Config::get('App:email_SenderEmail');

        $this->isSMTP();
        $this->CharSet = "UTF-8";
        $this->Host = Config::get('App:email_SenderHost');
        $this->SMTPAuth = true;
        $this->Username = Config::get('App:email_SenderEmail');
        $this->Password = Config::get('App:email_SenderPassword');
        $this->SMTPSecure = 'tls';

        $this->isHTML(true);

        $this->setFrom( Config::get('App:email_SenderEmail'), Config::get('App:email_SenderName'));
    }

    public function setContent($content){
        $this->Body = $content;
    }

    public function setSubject($subject){
        $this->Subject = $subject;
    }





}