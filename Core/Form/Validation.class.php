<?php  

namespace Core\Form;

use App;

class Validation{

	public $errors = array();
    protected $typeForm;
    protected $data;

    const GOOGLE_CAPTCHA_NAME = 'g-recaptcha-response';

    const REQUIRED = true;
    const NOT_REQUIRED = false;
    const EMAIL_REGEX = '/^[a-z0-9_\-.]{3,64}@[a-z0-9-_]{3,64}\.([a-z]{2,5})$/' ;
    const PASSWORD_REGEX = '/^[a-zA-Z0-9]{5,20}$/';
    const URL_REGEX = '/^((https?:\/\/)?(www.)?)?[a-zA-Z0-9.]+\.[a-zA-Z]{2,5}(\/?(.+))?$/';
    const PHONE_REGEX = '/^(0|\+33)[0-9]([ .-]?[0-9]{2}){4}$/';
    const DATE_REGEX = '/^\d{2}[-|\/]\d{2}[-|\/]\d{4}$/';
    const NUMBER_REGEX = '/^[0-9]+$/';
    const COLOR_REGEX = '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/';
    const NO_REGEX = '';

    public function __construct(Form $form){

        $this->typeForm = $form->typeForm;
        $this->data = $form->data;
        $typeForm = $this->typeForm;

        if($typeForm != '' && $typeForm != 'normalForm') { $this->$typeForm(); }
    }


    /* Text input in form */


    public function isText($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        return $this;
    }

    public function isEmail($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::EMAIL_REGEX, $options);
        }
        return $this;
    }

    public function isPassword($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::PASSWORD_REGEX, $options);
        }
        return $this;
    }
    public function isUrl($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::URL_REGEX, $options);
        }
        return $this;
    }

    public function isPhone($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::PHONE_REGEX, $options);
        }
        return $this;
    }

    public function isDate($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELf::DATE_REGEX, $options);
        }
        return $this;
    }

    public function isNumber($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::NUMBER_REGEX, $options);
        }
        return $this;
    }

    public function isRange($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::NUMBER_REGEX, $options);
        }
        return $this;
    }

    public function isColor($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);
        if($result){
            $result = $this->regexCompare($nomChamp, SELF::COLOR_REGEX, $options);
        }
        return $this;
    }



    /* Other input in form */


    public function isTextarea($nomChamp, $isRequired = true, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);

        return $this;
    }

    public function isChecked($nomChamp, $isRequired = false, $options = []){
        $result = $this->isRequired($nomChamp, $isRequired, $options);

        return $this;
    }

    public function isValidCaptcha($nomChamp, $isRequired = true, $options = []){
        $value = $this->data[$nomChamp];
        $GooglePrivateKey = App::getConfig()->get('form_Google_Private_Key');
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $GooglePrivateKey . "&response=" . $value . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        $responseKeys = json_decode($response,true);
        if(intval($responseKeys["success"]) !== 1) {
            $this->errors[] = "Le <b>captcha</b> est invalide !";
            return false;
        }
        return $this;
    }

    public function isEqual($champs1, $champs2, $error = 'default' ){

        $error = ($error == 'default') ? App::trans('core:validation:IsEqualError') : $error ;

        if($champs1 === $champs2){
            return true;
        }
       
        $this->errors[] = $error;
        return false;

    }

    public function databaseInteraction($result, $error){
        if(!$result){
            $this->errors[] = $error;
            return false;
        }
    }




    public function isRequired($nomChamp, $isRequired, $options){
        if(isset($this->data[$nomChamp])){
            $value = $this->data[$nomChamp];
        }else{
            $this->errors[] = "Le champs <b>'$nomChamp'</b> n'est pas défini !";
            return false;
        }
        if($isRequired){
            if( $value == '' || $value == null){
                 if(isset($options['emptyMsg']) && !empty($options['emptyMsg'])) {
                     $this->errors[] = $options['emptyMsg'];
                 }else{
                     $this->errors[] = "Veuillez remplir le champs<b> '$nomChamp' </b>!";
                 }
                return false;
            }
        }
        return true;
    }

    public function regexCompare($nomChamp, $RegexCompare, $options){
        $value = $this->data[$nomChamp];
        if(preg_match( $RegexCompare, $value) == false){
            if(isset($options['regexErrorMsg']) && !empty($options['regexErrorMsg'])) {
                $this->errors[] = $options['regexErrorMsg'];
            }else{
                $this->errors[] = "Le champs <b><u> '$nomChamp' </u></b> n'est pas valide !";
            }
            return false;
        }
        return true;
    }

    /*
     *
     * isUploaded
     * */





    public function isValid(){
        return empty($this->errors);
    }

    public function getErrors(){
		return implode('<br>', $this->errors);
	}

    public function getData($selectedValue = array() ){
        $values = $this->data;
        foreach ($values as $k => $v) {
            if(in_array($k ,$selectedValue) || empty($selectedValue)){
                $return[$k] = $v;
            }
        }
        return $return;
    }


//    public function contactForm(){
//        $this->isText('nomComplet');
//        $this->isEmail('email');
//        $this->isText('sujetMsg');
//        $this->isTextarea('contentMsg');
//        $this->isValidCaptcha(self::GOOGLE_CAPTCHA_NAME);
//    }
//
//
//    public function changeEmailForm(){
//        $this->isEmail('oldEmail');
//        $this->isEmail('newEmail');
//        $this->isEmail('repeatEmail');
//        $this->isPassword('password');
//        $this->isValidCaptcha(self::GOOGLE_CAPTCHA_NAME);
//    }





}