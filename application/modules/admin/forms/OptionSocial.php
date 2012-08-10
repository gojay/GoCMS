<?php
class Admin_Form_OptionSocial extends My_Form
{
	public function init()
	{
		 /**
		  * Social
		  * facebook : text small
		  * twitter : text small
		  * flicker : text small
		  * 
		  */
		 // Facebook : input text => class small
		$facebook = $this->createElement('text', 'facebook')
					 ->setLabel('Facebook')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter your Facebook URL e.g. http://www.facebook.com/snap')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		 // Twitter : input text => class medium
		$twitter = $this->createElement('text', 'twitter')
					 ->setLabel('Twitter')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter your Twitter URL e.g. http://www.facebook.com/snap')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		 // Linkedin : input text => class medium
		$linkedin = $this->createElement('text', 'linkedin')
					 ->setLabel('Linkedin')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter your LinkedIn URL e.g. http://www.linkedin.com/in/snap')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
					 
		$option_social_id = $this->createElement('hidden', 'option_social_id');
		
		// submit
		$submit = $this->createElement('submit', 'submit')
					   ->setAttrib('class', 'button')
					   ->setDecorators(array('ViewHelper'));
		
		// add Elements
		$this->addElements(array(
			// social
			$facebook,
			$twitter,
			$linkedin,
			$option_social_id,
			$submit
		));
		
		// add group social
		$this->addDisplayGroup(array(
			'facebook',
			'twitter',
			'linkedin',
			'option_social_id'
		), 
		'social', 
		array( 
			'legend' => 'Social',
		 	'decorators' => array(
				'FormElements',
				'Fieldset'
			)
		));
		
		/**
		 * ============= TWITTER TIMELINE =============
		 
		// twitter_timeline_information => html
		$twitter_timeline_information = new My_Form_Element_Html('twitter_timeline_information');
		$twitter_timeline_information->setValue('In order to authenticate with Twitter and sign requests with your own Twitter account 
			you need to register a new Twitter application on <a href="https://dev.twitter.com/apps">https://dev.twitter.com/apps</a> site and 
			obtain "access token" and "access token secret".<br/><br/>
			Use the access token string as your "oauth_token" and the access token secret as your "oauth_token_secret" 
			to sign requests with your own Twitter account. Do not share your oauth_token_secret with anyone.')
			->setAttrib('class', 'notification information medium');
		 // twitter_access_token : input text => class medium
		$twitter_access_token = $this->createElement('text', 'twitter_access_token')
					 ->setLabel('Access Token')
			 		 ->setAttrib('class', 'medium')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDescription('Use the access token string as your "oauth_token"')
					 ->setDecorators($this->decorators);
		 // twitter_access_token_secret : input text => class medium
		$twitter_access_token_secret = $this->createElement('text', 'twitter_access_token_secret')
					 ->setLabel('Access Token Secret')
			 		 ->setAttrib('class', 'medium')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDescription('Use the access token secret as your "oauth_token_secret" ')
					 ->setDecorators($this->decorators);
		
		 // twitter_count : input selectx
		$twitter_timeline = $this->createElement('select', 'twitter_count')
					 ->setLabel('Count Timeline')
					 ->setDescription('Number of status displayed');
		for ($i=1; $i <= 5; $i++) { 
			$twitter_timeline->addMultiOption($i, $i);
		}
		// hidden id twitter timeline					
		$option_twitter_timeline_id = $this->createElement('hidden', 'option_twitter_timeline_id');
		  
					   
		// add Elements
		$this->addElements(array(
			// twitter timeline
			$twitter_timeline_information,
			$twitter_access_token,
			$twitter_access_token_secret,
			$twitter_timeline,
			$option_twitter_timeline_id,
			$submit
		));
		
		// add group social
		$this->addDisplayGroup(array(
			'twitter_timeline_information',
			'twitter_access_token',
			'twitter_access_token_secret',
			'twitter_count',
			'option_twitter_timeline_id'
		), 
		'sc_twitter', 
		array( 
			'legend' => 'Twitter Timeline',
		 	'decorators' => array(
				'FormElements',
				'Fieldset'
			)
		));*/
						
		// add group submit
		$this->addDisplayGroup(array(
			'submit',
		), 'button')
		->getDisplayGroup('button')
		->setDecorators(array('FormElements'));		 
	}
}
