<?php
class Default_ContactController extends Zend_Controller_Action
{
	public function init()
	{
		$this->db = Zend_Registry::get( 'db' );
	}
	
	public function indexAction()
	{
    	$this->view->headTitle('Contact Us');
		
		$contact = new Model_Content( $this->db );
		if( !$contact->load( 'contact', 'content_slug' ) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		$this->view->page = $contact;
		
		$option = $this->_helper->hooks->getOption( 'option_general' );
		
		$form = new Form_Contact();
		$form->setAction( $this->_helper->url->simple( 'index' ) );
		$form->setMethod('post');
		if( $this->_request->isPost() && 
			$form->isValid( $this->_request->getPost() ) )
		{
			$data = $form->getValues();
			$sender = $data['name'];
			$email = $data['email'];
			$subject = $data['subject'];
			$message = $data['message'];
			$htmlMessage = $this->view->partial('contact/templates/email.phtml', $data);
			
			$mail = new Zend_Mail();
			$mail->setSubject($subject);
			$mail->setFrom($email, $sender);
			$mail->addTo( $option['email'] , 'admin');
			$mail->setBodyHtml($htmlMessage);
			$mail->setBodyText($message);
			$result = $mail->send();
			
			$this->view->messageProcessed = true;
			if($result){
				$this->view->sendError = false;
			}
			else {
				$this->view->sendError = true;
			}
		}
		$this->view->form = $form;
		
		/*$google = Zend_Registry::get( 'google' );
		$this->view->googleMapsKey = $google['map_api_key'];
		
		$contact = $this->_helper->hooks->getOption( 'option_contact' );
		$this->view->contact = $contact;
		
		$offices = $this->_helper->hooks->getOption( 'option_office' );
		$this->view->offices = $offices;*/
	}
}
