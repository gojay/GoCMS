<?php
class Form_Contact extends My_Form
{
	public $decorators_name = array(
		'ViewHelper',
		array('Errors', array('class' => 'warnings')),
		array('Label', array()),
		array('HtmlTag',array('tag'=>'div', 'class' => 'name_form'))
	);
	public $decorators_subject = array(
		'ViewHelper',
		array('Errors', array('class' => 'warnings')),
		array('HtmlTag',array('tag'=>'div', 'class' => 'subject_form'))
	);
	
	public function init()
	{
		$this->setAttrib('class', 'contacts');
		// name : text
		$name = $this->createElement('text', 'name');
		$name->setRequired(true);
		$name->setAttribs(array('class' => 'box', 'placeholder' => 'Name'));
		$name->setDecorators($this->decorators_name);
		$this->addElement($name);
		// email : text
		$email = $this->createElement('text', 'email');
		$email->setRequired(true);
		$email->setAttribs(array('class' => 'box', 'placeholder' => 'E-mail'));
		$email->addValidator('EmailAddress');
		$email->setDecorators($this->decorators_name);
		$this->addElement($email);
		// subject : text
		$subject = $this->createElement('text', 'subject');
		$subject->setRequired(true);
		$subject->setAttribs(array('class' => 'box', 'placeholder' => 'Subject'));
		$subject->setDecorators($this->decorators_name);
		$this->addElement($subject);// create new element
        // message : textarea
		$message = $this->createElement('textarea', 'message');
		$message->setRequired(true);
		$message->setAttribs(array('rows' => '', 'cols' => '', 'class' => 'box'));
		$message->setDecorators($this->decorators_subject);
		$this->addElement($message);
		// submit : submit button
		$submit = $this->createElement('submit', 'submit');
		$submit->setLabel('Submit →');
		$submit->setAttrib('class', 'medium button send');
		$submit->setDecorators($this->decorators_subject);
		$this->addElement($submit);
		
		// add group contact
		$this->addDisplayGroup(
			array(
				'name',
				'email',
				'subject'
			), 
			'contact', 
			array( 
			 	'decorators' => array(
					'FormElements',
					array('HtmlTag',array('tag'=>'div','class'=>'six columns'))
				)
			)
		);
		// add group text n submit
		$this->addDisplayGroup(
			array(
				'message',
				'submit',
			), 
			'contact2', 
			array( 
			 	'decorators' => array(
					'FormElements',
					array('HtmlTag',array('tag'=>'div','class'=>'twelve columns'))
				)
			)
		);
	}
}
