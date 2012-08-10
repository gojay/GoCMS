<?php
class Admin_Form_OptionAnalytics extends My_Form
{
	public function init()
	{
		// Google email : input text => class medium
		$email = $this->createElement('text', 'g_mail')
					 ->setLabel('Email')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter account google email')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// Google password : input text => class medium
		$password = $this->createElement('password', 'g_password')
					 ->setLabel('Password')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter account google passwrod')
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// Analytics ID : input text => class medium
		$profile = $this->createElement('text', 'ga_profile_id')
					 ->setLabel('Profile ID')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter analytic profile ID')
					 ->addFilters(array('StringTrim', 'Int'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
					 
		$option_analytics_id = $this->createElement('hidden', 'option_analytics_id');
		// submit : button
		$submit = $this->createElement('submit', 'submit')
					   ->setAttrib('class', 'button')
					   ->setDecorators(array('ViewHelper'));
		
		// add Elements
		$this->addElements(array(
			$email,
			$password,
			$profile,
			$option_analytics_id,
			$submit
		));
					   
		// add group contact
		$this->addDisplayGroup(
			array(
				'g_mail',
				'g_password',
				'ga_profile_id',
				'option_analytics_id'
			), 
			'contact', 
			array( 
				'legend' => 'Account Google Analytics',
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
