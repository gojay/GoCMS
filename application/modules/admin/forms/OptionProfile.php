<?php
class Admin_Form_OptionProfile extends My_Form
{
	public function init()
	{
		  
		 /**
		  * Profile
		  * username : text medium
		  * password : password medium
		  * re-password : password medium
		  */
		$firstname = $this->createElement('text', 'firstname')
					 ->setLabel('First Name')
			 		 ->setAttrib('class', 'medium')
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		$lastname = $this->createElement('text', 'lastname')
					 ->setLabel('LastName')
			 		 ->setAttrib('class', 'medium')
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		 // Email : input text => class medium
		$email = $this->createElement('text', 'email')
					 ->setLabel('Email')
			 		 ->setAttrib('class', 'medium')
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidators(array('NotEmpty', 'EmailAddress'))
					 ->setDecorators($this->decorators);
		 // Username : input text => class medium
		$username = $this->createElement('text', 'username')
					 ->setLabel('Username')
			 		 ->setAttrib('class', 'medium')
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		 // Password : input text => class medium
		$password = $this->createElement('password', 'password')
					 ->setLabel('Password')
			 		 ->setAttrib('class', 'medium')
					 ->addFilters(array('StringTrim'))
					 ->setDecorators($this->decorators);
		 // Retype your password : input password => class medium
		$password_confirm = $this->createElement('password', 'password_confirm')
						->setLabel('Retype your password')
						->setAttrib('class', 'medium')
						->setDescription('Retype your password')
						->addFilter('StringTrim')
						->addValidators(array(
							array('NotEmpty', true),
							array('Identical', true, array('token' => 'password'))
						))
					    ->setDecorators($this->decorators);	
							
		$option_profile_id = $this->createElement('hidden', 'option_profile_id');
						
		$submit = $this->createElement('submit', 'submit')
					   ->setAttrib('class', 'button')
					   ->setDecorators(array('ViewHelper'));
					   
		// add Elements
		$this->addElements(array(
			// account
			$username,
			$password,
			$password_confirm,
			// profile
			$firstname,
			$lastname,
			$email,
			$option_profile_id,
			$submit
		));
		
		// add group profile
		$this->addDisplayGroup(array(
			'username',
			'password',
			'password_confirm'
		), 'account', array( 'legend' => 'Account' ));
		// set decoration Fieldset group action
		$account = $this->getDisplayGroup('account')
					    ->setDecorators(array(        
				          	'FormElements',
				          	'Fieldset'
			            ));
		
		// add group account
		$this->addDisplayGroup(array(
			'firstname',
			'lastname',
			'email',
			'option_profile_id',
		), 'profile', array( 'legend' => 'Profile' ));
		// set decoration Fieldset group action
		$profile = $this->getDisplayGroup('profile')
					    ->setDecorators(array(        
				          	'FormElements',
				          	'Fieldset'
			            ));
		
		// add group
		$this->addDisplayGroup(array(
			'submit',
		), 'button')
		->getDisplayGroup('button')
		->setDecorators(array('FormElements'));		 
	}
}
