<?php

class Admin_SlideController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    }

    public function indexAction()
    {
        // get option gallery_home
		$option = new Model_Option( $this->db );
		$sliders = $option->getOption( 'gallery', 'gallery_slide' );
		$images = $sliders['value'];
		$this->view->slideId = count( $images ) + 1;
    }

    public function modalAction()
    {
        // get option gallery_home
		$option = new Model_Option( $this->db );
		$sliders = $option->getOption( 'gallery', 'gallery_slide' );
		$images = $sliders['value'];
		$this->view->slideId = count( $images ) + 1;
		
		$this->_helper->layout->disableLayout();
    }


}

