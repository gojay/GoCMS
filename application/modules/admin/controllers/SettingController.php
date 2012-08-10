<?php

class Admin_SettingController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    }

    public function indexAction()
    {
    	$this->view->headTitle('Setting');
		
		$option = new Model_Option( $this->db );
		
		$action = $this->view->baseUrl(false) . '/admin/ajax/option';
		
		// GENERAL
       	$formGeneral = new Admin_Form_OptionGeneral();
		$formGeneral->setAttrib( 'id', 'option_general');
		$formGeneral->setAttrib( 'ajax-validate', $ajax_validate);
		$formGeneral->setAction( $action );
		$formGeneral->populate( $option->populate() );
		//$formGeneral->getElement( 'g_password' )->setValue( 'aaaaa' );
		$this->view->formGeneral = $formGeneral;
		
		/*
		// CONTACT
		$formContact = new Admin_Form_OptionContact();
		$formContact->setAttrib( 'id', 'option_contact');
		$formContact->setAttrib( 'ajax-validate', $ajax_validate);
		$formContact->setAction( $this->view->baseUrl(false) . '/admin/ajax/contact' );
		$formContact->populate( $option->populate() );
		$this->view->formContact = $formContact;
		
		// SOCIAL
		$formSocial = new Admin_Form_OptionSocial();
		$formSocial->setAttrib( 'id', 'option_social');
		$formSocial->setAttrib( 'ajax-validate', $ajax_validate);
		$formSocial->setAction( $action );
		$formSocial->populate( $option->populate() );
		$this->view->formSocial = $formSocial;
		
		// PROFILE
		$user = new Model_User( $this->db );
		$user->load(1); //admin
		$formProfile = new Admin_Form_OptionProfile();
		$formProfile->setAttrib( 'id', 'option_profile');
		$formProfile->setAttrib( 'ajax-validate', $ajax_validate );
		$formProfile->setAction( $action );
		$formProfile->populate( $user->toArray() );
		$this->view->formProfile = $formProfile;
		*/
    }

	public function optionAction()
	{
		
	}

}

