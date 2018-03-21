<?php

namespace Core\Email;

class Email{

    public $separator = "\\r\\n";
    protected $email;
    protected $sujet;
    protected $message;

    protected $senderName = 'MBA Park';
    protected $senderEmail = 'no-reply@mbaPark.fr';

    public function addEmail($email){
        $this->email = $email;
        return $this;
    }

    public function setContent($content){
        $this->message = $content;
        return $this;
    }

    public function setSubject($sujet){
        $this->sujet = $sujet;
        return $this;
    }


    public function send(){

        $mail = $this->email;
        $sujet = $this->sujet;
        $message_html = $this->message;

        $senderName = $this->senderName;
        $senderEmail = $this->senderEmail;


        if ( !preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail) )
        {
            $ln = "\r\n";
        }else{
            $ln = "\n";
        }

        $boundary = "-----=".md5(rand());

        $header = "From: '$senderName'<$senderEmail>" . $ln;
        $header.= "Reply-to: '$senderName' <$senderEmail>" . $ln;
        $header.= "MIME-Version: 1.0" . $ln;
        $header.= "Content-Type: multipart/alternative;" . $ln . " boundary=\"$boundary\"" . $ln;

        $message = $ln . "--" . $boundary . $ln;
        $message.= "Content-Type: text/html; charset='ISO-8859-1'" . $ln;
        $message.= "Content-Transfer-Encoding: 8bit" . $ln;
        $message.= $ln . $message_html . $ln;

        $message.= $ln . "--" . $boundary . "--" . $ln;
        $message.= $ln . "--" . $boundary . "--" . $ln;

        return mail($mail,$sujet,$message,$header);
    }




}