<?php

class Default_SitemapController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$this->getResponse()->setHeader('Content-Type', 'text/xml');	
        $this->_helper->layout->disableLayout();
    }
	
	public function redirectAction()
	{
		$this->_helper->redirector->gotoSimple( 'index', 'sitemap', 'default' );
	}

}

