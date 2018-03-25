<?php

namespace Core\Form;

use App;

class Form{

	public $typeForm;
	public $data = [];
    protected $validation;

	protected $formId = 1;
	protected $method = 'POST';
	protected $action = '#';
	protected $enctype = '';

	public $elemClass = 'form-control';
	public $beforeSurround = "<div class='form-group'>";
	public $afterSurround = "</div>";

	protected $success = array();
	protected $error = array();
	protected $message = array();
	protected $htmlFormConstructor = array();
	protected $formScript = array();

    const REQUIRED = true;
    const NOT_REQUIRED = false;

    public function __construct($data = []){
		$this->data = $data;
		if (session_status() == PHP_SESSION_NONE) {
    		session_start();
		}
		$this->addFormScript($this->loadJqueryCdnFile);
	}
	protected function surround($html){
		$return = $this->beforeSurround . $html . $this->afterSurround;
		return $return;
	}
	protected function getDefaultData($index, $default = ''){
		$value = $this->data;
		if(isset($value[$index]) && !empty($value[$index])){
			return $value[$index];
		}
		return $default;
	}
	public function unsetData($index){
		$value = $this->data;
		if(isset($value[$index]) && !empty($value[$index])){
			$this->data[$index] = '';
		}
		return $this;
	}
	public function render($return = false){
		$enctypeElem = ($this->enctype != '') ? "enctype='{$this->enctype}'": '' ;

		$htmlFormRendered = '';
		if($this->error != null){ $htmlFormRendered = $this->getError(); }
		if($this->success != null){ $htmlFormRendered = $this->getSuccess(); }
		if($this->message != null){ $htmlFormRendered = $this->getMessage(); }

		foreach ($this->htmlFormConstructor as $k => $elem){
		    $value = $this->getDefaultData($k, $this->data[$k]);
            $elem = str_replace('data-value="__'.strtoupper($k).'__"', 'value="'.$value.'"', $elem);
            $this->htmlFormConstructor[$k] = $elem;
        }

		$htmlFormRendered .= "<form action='{$this->action}' method='{$this->method}' id='{$this->formId}' $enctypeElem>";
		$htmlFormRendered .= implode('', $this->htmlFormConstructor);
		$htmlFormRendered .= '</form>';

		if($return){
		    return $htmlFormRendered;
        }else{
            echo $htmlFormRendered;
		    $this->loadFormScript();
        }
	}
    
	public function addFormScript($script){
		$this->formScript[] = $script;
	}
	public function loadFormScript(){
        echo implode('', $this->formScript);
	}
	public function setAction($action){ 
		$this->action = $action; 
		return $this; 
	}
	public function setMethod($method){ 
		$this->method = $method; 
		return $this; 
	}
	public function setFormId($formId){ 
		$this->formId = $formId; 
		return $this; 
	}
	public function setFormEnctype($enctype){ 
		$this->enctype = $enctype; 
		return $this; 
	}
	public function success($value){ 
		$this->success[] = $value;
		return $this;
	}
    public function error($value){
        $this->error[] = $value;
        return $this;
    }
//    public function message($value){
//        $this->message[] = $value;
//        return $this;
//    }
	public function getError(){
		$return = '<div class="alert alert-danger">' . implode('<br>', $this->error) . '</div>';
		return $return; 
	}
    public function getSuccess(){
        $return = '<div class="alert alert-success">' . implode('<br>', $this->success) . '</div>';
        return $return;
    }
//    public function getMessage(){
//        $return = implode('<br>', $this->message);
//        return $return;
//    }

    public function clear(){
	    $data = $this->data;
	    foreach ($data as $k => $v){
            $this->unsetData($k);
        }
    }

    public function setData($name, $value){
        $this->data[$name] = $value;
        return $this;
    }


	protected function addTextElement($name, $label, $isRequired, $value, $options, $typeElem ){

        $elemOption = "";
        foreach ($options as $k => $v){
            $elemOption .= "$k='$v'";
        }

		$requiredElem = ($isRequired) ? 'required' : '' ;
		$asterisk = ($isRequired) ? '<sup style="color:red">*</sup>' : '' ;

//		$value = $this->getDefaultData($name, $value);
		$valueElem = ($value != '') ? "value='$value'" : 'data-value="__'.strtoupper($name).'__"' ;
		$labelElem = ($label != '') ? "<label for='$name'>$label $asterisk</label>" : '' ;

		if($typeElem == 'textarea'){
			$returnElem = $labelElem . "<textarea $elemOption name='$name' id='$name' $requiredElem $options>$value</textarea>";
		}else{
			$returnElem = $labelElem . "<input $elemOption type='$typeElem' name='$name' id='$name' $valueElem $requiredElem $options>";
		}
		return $this->surround($returnElem);
	}

	protected function addBtnElement($name, $value, $class, $inline){
		$classElem = ($class != '') ? "class='$class'" : "" ;
        $value = htmlentities($value);
        $returnElem = '<input type="submit" id="'.$name.'-btn" name="'.$name.'" '.$classElem.' value="'.$value.'">';
		if($inline){
			return $returnElem;
		}
		return $this->surround($returnElem);
	}

	protected function addCheckboxElement($name, $label, $isRequired, $value, $options){

		$requiredElem = ($isRequired) ? 'required' : '' ;
		$asterisk = ($isRequired) ? '<sup style="color:red">*</sup>' : '' ;

		$value = $this->getDefaultData($name, $value);

		$valueElem = ($value != '' && $value == true) ? "checked" : '' ;

		$inputElem = "<input type='checkbox' name='$name' id='$name' $valueElem $requiredElem>";
		$returnElem = "<label for='$name'>$inputElem $label $asterisk</label>";
		
		return $this->surround($returnElem); 
	}

	protected function addRadioElement($name, $label, $values, $isRequired, $options){

		$requiredElem = ($isRequired) ? 'required' : '' ;
		$asterisk = ($isRequired) ? '<sup style="color:red">*</sup>' : '' ;

		$returnElem = ($label != '') ? "<label for='$name'>$label $asterisk</label><br>" : '' ;
		$value = $this->getDefaultData($name);

		foreach ($values as $k => $v) {
			$checkedElem = ($value != '' && $value == $k) ? 'checked'  : '' ;
			$returnElem .= "<label style='font-weight:normal;'><input type='radio' name='$name' id='$name' value='$k' $checkedElem $requiredElem> $v</label><br>";
		}
		return $this->surround($returnElem);
	}

	protected function addSelectElement($name, $label, $values, $isRequired, $options){

        $elemOption = '';
        foreach ($options as $k => $v){
            $elemOption .= "$k='$v'";
        }

		$requiredElem = ($isRequired) ? 'required' : '' ;
		$asterisk = ($isRequired) ? '<sup style="color:red">*</sup>' : '' ;

		$returnElem = ($label != '') ? "<label for='$name'>$label $asterisk</label> " : '' ;
		$value = $this->getDefaultData($name);

		$returnElem .= "<select $elemOption name='$name' id='$name' class='custom-select'>";
		foreach ($values as $k => $v) {
			$selectedElem = ($value != '' && $value == $k) ? 'selected'  : '' ;
			$returnElem .= "<option value='$k' $requiredElem $selectedElem>$v</option>";
		}
		$returnElem .= '</select>';
		
		return $this->surround($returnElem);
	}





// Text input Form

	public function text($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'text');
		return $this;
	}

	public function password($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'password');
		return $this;
	} 

	public function email($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'email');
		return $this;
	}

	public function url($name, $label = '', $isRequired = true, $value = 'http://', $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'url');
		return $this;
	}

	public function phone($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'tel');
		return $this;
	}

	public function date($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
		$options .= ' pattern="\d{4}-\d{2}-\d{2}" placeholder="YYYY-MM-DD"';
		$this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'date');
		return $this;
	}

	public function number($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'number');
		return $this;
	}

	public function range($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'range');
		return $this;
	}

	public function color($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
		$options .= ' pattern="#[a-g0-9]{6}"';
		$this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'color');
		return $this;
	}

	public function textarea($name, $label = '', $isRequired = true, $value = null, $options = ['class' => 'form-control'] ){
	    $this->htmlFormConstructor[$name] = $this->addTextElement($name, $label, $isRequired, $value, $options, 'textarea');
		return $this;
	}






 	public function checkbox($name, $label = '', $isRequired = false, $value = null, $options = null ){
 		$this->htmlFormConstructor[$name] = $this->addCheckboxElement($name, $label, $isRequired, $value, $options);
 		return $this;
 	}

 	public function radio($name, $label = '', $values, $isRequired = true, $options = null){
 		$this->htmlFormConstructor[$name] = $this->addRadioElement($name, $label, $values, $isRequired, $options);
 		return $this;
 	}

 	public function select($name, $label = '', $values, $isRequired = true, $options = null){
 		$this->htmlFormConstructor[$name] = $this->addSelectElement($name, $label, $values, $isRequired, $options);
 		return $this;
 	}

 	public function YesNo($name, $label = '', $isRequired = true, $options = null){
 		$this->radio($name, $label, ['Y' => "Oui", 'N' => "Non"], $isRequired, $options);
 		return $this;
 	}

	public function submit($value = 'Envoyer', $name = 'submit', $class = 'btn-primary', $inline = true){
		$this->htmlFormConstructor[$name] = $this->addBtnElement($name, $value, 'btn '.$class, $inline);
		return $this;
	}

	public function cancel($value = 'Annuler', $name = 'cancel', $class = 'btn-danger', $inline = true){ 
		$this->htmlFormConstructor[$name] = $this->addBtnElement($name, $value, 'btn '.$class, $inline);
		return $this;
	}


// // Other inpur Form

 	public function captcha($label = '', $isRequired = true){

 		$labelElem = ($label != '') ? "<label>$label</label>" : '' ;
 		$this->htmlFormConstructor['captcha'] = $labelElem . '<div class="g-recaptcha" data-sitekey="' . App::getConfig()->get('form_Google_Public_Key') . '"></div><br>';
 	}

 	public function file($name, $label, $isRequired = false, $options = null, $class = 'btn btn-info', $multiple = false){
 		$classElem = ($class != '') ? "class=' $class'" : '' ;
 		$labelElem =  "<label $classElem id='inputLabel-$name' for='$name'>$label</label>" ;

 		if($multiple){
 			$nameElem = $name . '[]';
 			$labelExemple =  "<label $classElem id='inputLabel-exempleNameLabel' for='exempleNameFor'>$label</label>" ;
 			$exempleInput = $labelExemple . "<input id='exempleNameId' name='$nameElem' data-label='$label' class='inputTrigger' type='file'>";
 			$exempleElem = "<div id='exempleInputFiles' data-name='$name' class='form-group' style='display:none;'>" . $exempleInput . "</div>";
 			$this->htmlFormConstructor[$name . 'Example'] = $exempleElem ;
 		}else{
 			$nameElem = $name;
 		}

 		$element = $this->surround( $labelElem . "<input id='$name' name='$nameElem' data-label='$label' class='inputTrigger' type='file' style='display:none'>" );

 		$this->htmlFormConstructor[$name] = $element ;
 		$this->addFormScript( $this->singleFileScript );
 		$this->enctype = 'multipart/form-data';

 		return $this;
 	}

 	public function files($name, $label, $isRequired = false, $options = null, $class = 'btn btn-info'){

 		$this->file($name, $label . ' <u>#1</u>', $isRequired, $options, $class, true);
 		$this->addFormScript( $this->multipleFileScript );
 		$this->addFormScript( $this->DeleteEmptyFileInputOnSubmitFormScript );
 	}


	private $loadJqueryCdnFile = '<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>';
	private $singleFileScript = "<script> $('html').on('change', '.inputTrigger', function(e) { th = $(this);name = th.attr('id');label = th.attr('data-label');val = th.val().split('fakepath\\\').pop(); if (val == '') { newVal = label;} else { newVal = 'Fichier : <b>' + val + '</b>'; } $('#inputLabel-' + name).html(newVal); });</script>";
	private $multipleFileScript = "<script>$('html').on('change', '.inputTrigger', function(e) {val = $(this).val().split('fakepath\\\').pop();if (val != '') { number = $('#inputLabel-exempleNameLabel u').html().replace('#', '') * 1 + 1;$('#inputLabel-exempleNameLabel u').text('#' + number );newName = $('#exempleInputFiles').attr('data-name') + '_' + number;exempleElem = $('#exempleInputFiles');newElem = exempleElem[0].outerHTML;newElem = newElem.replace('exempleNameLabel', newName);newElem = newElem.replace('exempleNameFor', newName);newElem = newElem.replace('exempleNameId', newName);newElem = newElem.replace('#1', '#'+ number);newElem = newElem.replace('exempleInputFiles', 'div-' + newName);$(this).parent().after( newElem );$('#' + newName).hide();$('#div-' + newName).show();}});</script>";
	private $DeleteEmptyFileInputOnSubmitFormScript = "<script>$('#submit-btn').click(function(e){ $('.inputTrigger').each(function(e){ val = $(this).val(); if( val == null || val == '' ){ $(this).remove(); }}); });</script>";




	public function setValidation(Validation $validation){
	    $this->validation = $validation;
	    return $this;
    }

    public function isValid(){ return $this->validation->isValid(); }
    public function getData(){ return $this->validation->getData(); }
    public function getErrors(){ return $this->validation->getErrors(); }
    public function databaseInteraction($result, $error){ return $this->validation->databaseInteraction($result, $error); }
    public function isEqual($champs1, $champs2, $error = 'default'){ return $this->validation->isEqual($champs1, $champs2, $error); }



//    public function contactForm(){
//        $this->setAction('#')->setMethod('POST')->setFormId('contactForm');
//        $this->text('nomComplet', 'Nom Complet :');
//        $this->email('email', 'Email :');
//        $this->text('sujetMsg', 'Sujet du message :');
//        $this->textarea('contentMsg', 'Votre message :', [self::REQUIRED], NULL, ['class' => 'form-control', 'rows' => '15']);
//        $this->captcha();
//        $this->submit('Envoyer');
//    }
//
//
//    public function changeEmailForm(){
//        $this->setAction('#')->setMethod('POST')->setFormId('contactForm');
//        $this->email('oldEmail', 'Adresse email actuelle :', [self::REQUIRED], null, ['class' => 'form-control', 'readonly' => 'readonly']);
//        $this->email('newEmail', 'Nouvelle adresse Email :');
//        $this->email('repeatEmail', 'Retapez votre nouvelle adresse Email :');
//        $this->password('password', 'Mot de passe :');
//        $this->captcha();
//        $this->submit('Modifier mon adresse Email');
//    }


}
