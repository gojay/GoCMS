<?php
class My_Form extends Zend_Form
{
	public static $default_decorator = array(
		'ViewHelper', 
		array( 'Label', array('class' => 'control-label alignleft quarter') ),
		array( 'HtmlTag', array('tag' => 'div', 'class' => 'control-group') )
	);
	
	public function __construct($options = null) 
	{
		parent::__construct($options);
		
        $this->setAttrib('class', 'form-horizontal');
		$this->setDecorators(array('FormElements', 'Form'));
	}

	public function loadMessageConfig() 
	{
		$messages = new Zend_Config_ini(APPLICATION_PATH . '/configs/form/messages.ini', APPLICATION_ENV);
		return $messages -> toArray();
	}

	public function initValidationMessages() 
	{
		$locale = Zend_Registry::get('Zend_Locale');
		if ($locale == 'id') {
			$messages = $this -> loadMessageConfig();
			if (!empty($messages)) {
				foreach ($messages as $field => $fieldMessages) {
					foreach ($fieldMessages as $validator => $validatorMessages) {
						$element = $this -> getElement($field);
						$validate = null;

						if ($element) {
							$validate = $element -> getValidator($validator);
						}

						if ($validate) {
							$validate -> setMessages($validatorMessages);
						}
					}
				}
			}
		}

		return $this;
	}

	public function isValid($data) 
	{
		$isValid = parent::isValid($data);

		if (!$isValid) {
			$arrErrors = parent::getErrors();

			if (is_array($arrErrors) && count($arrErrors) > 0) {
				foreach ($arrErrors as $key => $value) {
					if (is_array($value) && count($value) > 0) {
						$objElement = parent::getElement($key);
						if ($objElement)
							$objElement -> setAttrib('class', $objElement -> getAttrib('class') . ' invalid');
					} else {
						$objElement = parent::getElement($key);
						if ($objElement)
							$objElement -> setAttrib('class', $objElement -> getAttrib('class') . ' valid');
					}
				}
			}

			return false;
		}

		return true;
	}

}
