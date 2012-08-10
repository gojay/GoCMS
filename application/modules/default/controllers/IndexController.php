<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    	$this->option = $this->_helper->hooks->getOption('option_general');
    }

    public function indexAction()
    {
    	$title = $this->_request->getParam( 'title' );
        $page = new Model_Content( $this->db );
		if( !$page->load( $title, 'content_slug' ) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		// jika front page, forward action index
		$the_page = $page->content_slug;
		if( $the_page == $this->option['front_page'] )
		{
			$this->_forward( $the_page );
		}
		// jika tipe post, forward action posts
		else if( $the_page == $this->option['news_page'] )
		{
			$this->_forward( $the_page );
		}
		// jika tipe portfolios, forward action portfolios
		else if( $the_page == $this->option['portfolios_page'] )
		{
			$this->_forward( $the_page );
		}
		
		$this->view->page = $page;
		
		// Page Childs
        $pages = Model_Content::GetContents( $this->db, array(
		        'type' => 'page', 
		        'parent_in' => $page->getId(),
				'order' => 'content_order'
			) 
		);
		$this->view->pages = $pages;
		
		// AJAX
		if($this->_request->isXmlHttpRequest()){
			$this->view->isXmlHttpRequest = true;
			$this->_helper->layout->disableLayout();
		}
		
		$this->view->headTitle( $page->content_name );
    }
	
	public function viewAction()
	{
		$title = $this->_request->getParam( 'title' );
		
		$page = new Model_Content( $this->db );
		if( !$page->load( $title, 'content_slug' ) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		$this->view->page = $page;
		
		$this->_helper->layout->disableLayout();
	}
	
	public function detailAction()
	{
		$act = $this->_request->getParam( 'act' );
		$detail = $this->_request->getParam( 'detail' );
		
		$this->view->headTitle( $act );
		
		Zend_Debug::dump(array($title, $detail));
		
		$this->_helper->viewRenderer->setNorender();
	}
	
	public function peopleAction()
	{
		$name = $this->_request->getParam( 'name' );
		
		$page = new Model_Content( $this->db );
		if( !$page->load( $name, 'content_slug' ) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		$this->view->page = $page;
		// peoples
		$options = array(
			'type'  => 'people',
		);
        $this->view->peoples = Model_Content::GetContents( $this->db, $options );
		
		// AJAX
		if($this->_request->isXmlHttpRequest()){
			$this->view->isXmlHttpRequest = true;
			$this->_helper->layout->disableLayout();
		}
		
		$this->view->headTitle( 'Our People' );
	}
	
	/**
	 * =========== COSTUMIZE PAGES ====================
	 */

    public function homeAction()
    {
    	$slides = $this->_helper->hooks->getOption( 'gallery_slide', 'value', 'gallery' );
    	$this->view->slides = $slides;
		
    	// portfolios
        $this->view->portfolios = Model_Content::GetContents( $this->db, array('type' => Model_Content::CONTENT_GALLERY) );
		// offices
		$this->view->offices = $this->_helper->hooks->getOption( 'option_office' );
		// news
		$options = array(
			'type'  => Model_Content::CONTENT_POST,
			'limit' => 3,
		);
        $this->view->news = Model_Content::GetContents( $this->db, $options );
    }
	
	public function newsAction()
	{
		$page = $this->view->navigation()->findOneBy('label', 'News');
		if ( $page ) {
		  $page->setActive();
		}
		
		$title = $this->_request->getParam( 'title' );
		
        $news = new Model_Content( $this->db );
		if( !$news->load( $title, 'content_slug' ) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		$isSingle = false;
		if( $title !== $this->option['news_page'] )
		{
			$isSingle = true;
			$page = new Zend_Navigation_Page_Mvc(array(  
				'label' => $news->content_name
			));
			$page->setActive(true);
			$this->view->navigation()->findOneBy('label','News')->addPage($page);
		}
		
		$this->view->isSingle = $isSingle;
		
		$this->view->page = $news;
		
		$options = array(
			'type'   => Model_Content::CONTENT_POST,
			'page' 	 => $this->_request->getParam('page'),
			'show' 	 => $this->option['show_news'],
		);
		$this->view->paginator = Model_Content::GetPagination( $this->db, $options );
		
		$this->view->headTitle( $page->content_name );
	}
	
	public function portfolioAction()
	{
		$page = $this->view->navigation()->findOneBy('label', 'Portfolio');
		if ( $page ) {
		  $page->setActive();
		}
		
		$title = $this->_request->getParam( 'title' );
		
        $portfolio = new Model_Content( $this->db );
		if( !$portfolio->load( $title, 'content_slug' ) )
			throw new Zend_Controller_Action_Exception('This page dont exist',404);
		
		$isSingle = false;
		if( $title !== 'portfolio' )
		{
			$isSingle = true;
			$page = new Zend_Navigation_Page_Mvc(array(  
				'label' => $portfolio->content_name
			));
			$page->setActive(true);
			$this->view->navigation()->findOneBy('label','Portfolio')->addPage($page);
		}
		
		$this->view->isSingle = $isSingle;
		
		$this->view->page = $portfolio;
		
		$options = array(
			'type'   => Model_Content::CONTENT_GALLERY,
			'page' 	 => $this->_request->getParam('page'),
			'show' 	 => $this->option['show_portfolios'],
		);
	    $this->view->paginator = Model_Content::GetPagination( $this->db, $options );
		
		$this->view->headTitle( $page->content_name );
	}

	public function testAction()
	{
		$page = Model_Content::GetContentsByParent($this->db, array(
				'type' => Model_Content::CONTENT_PAGE, 
				'parent_in' => 0,
				'order' => 'content_id'
			)
		);
		foreach ($page AS $index => $p) 
		{
			$navigation[$p['id']] = array(
				'label' => $p['name'],
				'controller' => 'index',
				'params' => array(
					'title' => $p['slug']
				),
				'route' => 'page'
			);
			
			if( $p['child'] ){
				foreach($p['child'] as $child )
			 	{
			 		if($p['name'] == 'About Us')
						$route = 'about';
					elseif($p['name'] == 'Services')
						$route = 'service';
					
			 		$navigation[$p['id']]['pages'][] = array(
						'label' => $child['content_name'],
						'controller' => 'index',
						'params' => array(
							'title' => $child['content_slug']
						),
						'route' => $route
					);
			 	}
			}
			
			if( $p['name'] == 'News' )
			{
				$news = Model_Content::GetContents($this->db, array('type' => Model_Content::CONTENT_POST));
				foreach($news as $n )
			 	{
			 		$navigation[$p['id']]['pages'][] = array(
						'label' => $n->content_name,
						'controller' => 'index',
						'params' => array(
							'title' => $n->content_slug
						),
						'route' => 'news'
					);
			 	}
			}
			
			if( $p['name'] == 'Portfolio' )
			{
				$portfolios = Model_Content::GetContents($this->db, array('type' => Model_Content::CONTENT_GALLERY));
				foreach($portfolios as $prt )
			 	{
			 		$navigation[$p['id']]['pages'][] = array(
						'label' => $prt->content_name,
						'controller' => 'index',
						'params' => array(
							'title' => $prt->content_slug
						),
						'route' => 'portfolio'
					);
			 	}
			}
			
			if( $p['name'] == 'People' )
			{
				$peoples = Model_Content::GetContents($this->db, array('type' => 'people'));
				foreach($peoples as $pp )
			 	{
			 		$navigation[$p['id']]['pages'][] = array(
						'label' => $pp->content_name,
						'controller' => 'index',
						'params' => array(
							'title' => $pp->content_slug
						),
						'route' => 'people'
					);
			 	}
			}
		 	
			
        }
		
		Zend_Debug::dump($navigation);
		
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

}

