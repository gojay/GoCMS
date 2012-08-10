<?php
class Admin_Form_OptionGeneral extends My_Form
{
	public function init()
	{
		/**
		 * General
		 * email : text
		 * show page : select
		 */
		 
		/** ========================== General ========================== **/
		 
		 // Email : input text => class medium
		$email = $this->createElement('text', 'email')
					 ->setLabel('Email')
			 		 ->setAttrib('class', 'small')
					 ->setRequired(TRUE)
					 ->addFilters(array('StringTrim'))
					 ->addValidators(array('NotEmpty', 'EmailAddress'))
					 ->setDescription('Enter your e-mail address to use on the Contact Form')
					 ->setDecorators($this->decorators);
		 // Show Page : select
		$show_news = $this->createElement('text', 'show_news')
			 		 ->setAttrib('style', 'width:20px')
					 ->setLabel('Show News')
					 ->setDecorators($this->decorators);
		 // Show Portfolios : select
		$show_portfolios = $this->createElement('text', 'show_portfolios')
			 		 ->setAttrib('style', 'width:20px')
					 ->setLabel('Show Portfolios')
					 ->setDecorators($this->decorators);
		
		 // Front Page : select
		$front_page = $this->createElement('select', 'front_page')
					 ->setLabel('Front Page')
					 //->setDescription('Front Page')
					 ->setDecorators($this->decorators);
		 // News Page : select
		$news_page = $this->createElement('select', 'news_page')
					 ->setLabel('News Page')
					 ->setDecorators($this->decorators);
		 // Portfolios Page : select
		$portfolios_page = $this->createElement('select', 'portfolios_page')
					 ->setLabel('Portfolios Page')
					 ->setDecorators($this->decorators);
		// get pages
		$db = Zend_Registry::get( 'db' );
		$pages = Model_Content::GetContents( $db, array(
			'type' => 'page', 
			'parent_in' => 0, 
			'order' => 'content_id'
			) 
		);
		foreach ($pages as $page) {
			$front_page->addMultiOption($page->content_slug, $page->content_name);
			$news_page->addMultiOption($page->content_slug, $page->content_name);
			$portfolios_page->addMultiOption($page->content_slug, $page->content_name);
		}
		
		// option general id
		$option_general_id = $this->createElement('hidden', 'option_general_id');
					   
		// add General Elements
		$this->addElements(array(
			$email,
			$show_news,
			$show_portfolios,
			$front_page,
			$news_page,
			$portfolios_page,
			$option_general_id
		));
		
		// add group general
		$this->addDisplayGroup(array(
			'email',
			'show_news',
			'show_portfolios',
			'front_page',
			'news_page',
			'portfolios_page',
			'option_general_id',
		), 'general', array( 'legend' => 'General' ));
		// set decoration Fieldset group event
		$general = $this->getDisplayGroup('general')
					  ->setDecorators(array(        
			          	'FormElements',
			          	'Fieldset'
			          ));
		
		/** ========================== GOOGLE ANALYTICS ========================== **/
		
		// Google email : input text => class medium
		$g_email = $this->createElement('text', 'g_mail')
					 ->setLabel('Email')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter account google email')
					 ->addFilters(array('StringTrim'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// Google password : input text => class medium
		$g_password = $this->createElement('text', 'g_pass')
					 ->setLabel('Password')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter account google password')
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// Analytics ID : input text => class medium
		$g_profile = $this->createElement('text', 'ga_profile_id')
					 ->setLabel('Profile ID')
			 		 ->setAttrib('class', 'medium')
					 ->setDescription('Enter analytic profile ID')
					 ->addFilters(array('StringTrim', 'Int'))
					 ->addValidator('NotEmpty')
					 ->setDecorators($this->decorators);
		// submit				
		$submit = $this->createElement('submit', 'submit')
					   ->setAttrib('class', 'button')
					   ->setDecorators(array('ViewHelper'));
		
		// add Google Elements
		$this->addElements(array(
			$g_email,
			$g_password,
			$g_profile,
		));
		
		// add group contact
		$this->addDisplayGroup(
			array(
				'g_mail',
				'g_pass',
				'ga_profile_id',
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
		
		/** ========================== RSS ========================== **/
		
		$rss_title = $this->createElement('text', 'rss_title')
					 ->setLabel('Title')
			 		 ->setAttrib('class', 'small')
					 ->addFilters(array('StringTrim'))
					 ->setDecorators($this->decorators);
					 
		$rss = new Zend_Form_Element_MultiCheckbox('rss', array(
	        	'multiOptions' => array(
		            'page' => ' Page',
		            'post' => ' News',
		            'portfolio' => ' Portfolio',
		            'people' => ' People',
		        )
	    ));
     
    	$rss->setLabel('Choose a content to be displayed');
		
		// add Google Elements
		$this->addElements(array(
			$rss_title,
			$rss
		));
		
		// add group contact
		$this->addDisplayGroup(
			array(
				'rss_title',
				'rss',
			), 
			'group_rss', 
			array( 
				'legend' => 'RSS',
			 	'decorators' => array(
					'FormElements',
					'Fieldset'
				)
			)
		);
		
		// submit
		$this->addElement( $submit );
		// add group submit
		$this->addDisplayGroup(array(
			'submit',
		), 'button')
		->getDisplayGroup('button')
		->setDecorators(array('FormElements'));	
	}
}
