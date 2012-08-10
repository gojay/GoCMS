<?php

class Default_WidgetController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
		$this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        // action body
    }
	
	public function menuAction()
	{
		$menus = Model_Content::GetContents( $this->db, array('type' => 'page', 'parent_in' => 0, 'order' => 'content_id') );
		$this->view->menus = $menus;
	}

    public function sliderAction()
    {
        $slides = $this->_helper->hooks->getOption( 'gallery_slide', 'value', 'gallery' );
    	$this->view->slides = $slides;
    }

    public function footerAction()
    {
        $position = $this->_request->getParam( 'position' );
		
		$page = Model_Content::GetContentsByParent( $this->db, array(
			'type' => 'page', 
			'parent_in' => 0, 
			'order' => 'content_id'
			) 
		);
		$this->view->page = $page;
		$this->view->offices = $this->_helper->hooks->getOption( 'option_office' );
		$this->view->social = $this->_helper->hooks->getOption( 'option_social' );
		$this->view->position = $position;
		
		//Zend_Debug::dump($page);
    }

}





