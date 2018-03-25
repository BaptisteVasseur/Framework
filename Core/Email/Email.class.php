<?php

namespace Core\Email;

use Core\Config\Config;

class Email extends PHPMailer {


    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);

        $this->senderName = Config::get('App:Email_SenderName');
        $this->senderEmail = Config::get('App:Email_SenderEmail');

        $this->isSMTP();
        $this->CharSet = "UTF-8";
        $this->Host = Config::get('App:Email_SenderHost');
        $this->SMTPAuth = true;
        $this->Username = Config::get('App:Email_SenderEmail');
        $this->Password = Config::get('App:Email_SenderPassword');
        $this->SMTPSecure = 'tls';

        $this->isHTML(true);

        $this->setFrom( Config::get('App:Email_SenderEmail'), Config::get('App:Email_SenderName'));
    }

    public function setContent($content){
        $this->Body = $content;
    }

    public function setSubject($subject){
        $this->Subject = $subject;
    }





}