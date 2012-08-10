<?php

class Admin_AuthController extends Zend_Controller_Action
{

    public function init()
    {
        $db = Zend_Registry::get( 'db' );
        $this->option = new Model_Option( $db );
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction()
	{
    	$this->view->headTitle('Login');
		
		$loginForm = new Admin_Form_Login();
		$loginForm->setAction( $this->_helper->url->simple( 'login' ) );
		if( $this->_request->isPost() && 
			$loginForm->isValid($_POST) )
		{
			$authAdapter = new Zend_Auth_Adapter_DbTable($this->db, 
													'snap_users', 
													'username', 
													'password', 
													'md5(?)');
			$authAdapter->setIdentity( $loginForm->getValue('username') );
			$authAdapter->setCredential( $loginForm->getValue('password') );
			
			$result = $authAdapter->authenticate();
			if($result->isValid())
			{
				$auth = Zend_Auth::getInstance();
				$auth->getStorage()->write($authAdapter->getResultRowObject(array(
																			'id', 
																			'username', 
																			'firstname', 
																			'lastname',  
																			'email', 
																			'role')
				));
				
				// redirect
				$this->_helper->redirector->gotoSimple( 'index', 'dashboard', 'admin' );
			} else {
				$this->view->loginMessage = 'Sorry your username or password was incorect';	
			}
		}
		$this->view->form = $loginForm;
		
		$this->_helper->layout->disableLayout();		
	}
	
	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
	}

}





