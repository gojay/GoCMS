<?php
class Admin_Form_Login extends My_Form
{
	public function init()
	{
		// nama event : input text => class medium
		$username = $this->createElement('text', 'username')
					 ->setLabel('Username')
			 		 ->setAttrib('class', 'large')
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// nama event : input text => class medium
		$password = $this->createElement('password', 'password')
					 ->setLabel('Password')
			 		 ->setAttrib('class', 'large')
					 ->setRequired(TRUE)
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// submit : button
		$submit = $this->createElement('submit', 'submit')
					   ->setAttrib('class', 'button')
					   ->setDecorators(array('ViewHelper'));
		
		// add Elements
		$this->addElements(array(
			$username,
			$password,
			$submit
		));
					   
		// add group contact
		$this->addDisplayGroup(
			array(
				'username',
				'password',
			), 
			'login', 
			array( 
			 	'decorators' => array(
					'FormElements',
					'Fieldset'
				)
			)
		);
		
		// add group
		$this->addDisplayGroup(array(
			'submit',
		), 'button')
		->getDisplayGroup('button')
		->setDecorators(array('FormElements'));		
					 
	}
}
