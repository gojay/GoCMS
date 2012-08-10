<?php
class Admin_ContentController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
		
		$t = $this->_getParam('type');
		$type = (isset($t)) ? $t : 'post' ;
		$this->content_type = $type;
		
		$this->form = new Admin_Form_Content();
    }

    public function indexAction()
    {
    	$this->view->headTitle( ucfirst($this->content_type) );
		
		$options = array(
			'show' 	=> 10,
			'range' => 3,
			'page' 	=> $this->_request->getParam('page'),
			'type'	=> $this->content_type
		);
        $paginator = Model_Content::GetPagination( $this->db, $options );
		$this->view->paginator = $paginator;
		
		$this->view->content_type = $this->content_type;
    }

    public function addAction()
    {
    	$this->view->headTitle( sprintf('Add %s', ucfirst($this->content_type)) );
		
		// set content form
		$this->form->setContent( $this->content_type, true );
		// set form action
		$this->form->setAction( $this->_helper->url->simple( 'add' ) );
		
		// proses
		if( $this->_request->isPost() && 
			$this->form->isValid($this->_request->getPost()))
		{
			$data = $this->form->getValues();
			$content = new Model_Content( $this->db );
			$isSaved = $content->saveContent( $data );
			if( $isSaved ){
				$this->_helper->FlashMessenger( array('success' => sprintf('%s added', ucwords($this->content_type))) );
			} else {
				$this->_helper->FlashMessenger( array('error' => 'Error occured. Please try again later') );
			}
			
			// redirect
			if( $data['redirect'] == 'index' )
				$this->_helper->redirector->gotoRoute( array('type' => $data['content_type']), 'contents' );
			else
				$this->_helper->redirector->gotoRoute( array('action' => $data['redirect'], 'type' => $data['content_type']), 'content' );
		
		}
		
		$this->view->form = $this->form;
    }

    public function editAction()
    {
		// ambil id
		$id = $this->_request->getParam('id');
		$content = new Model_Content( $this->db );
		// cek konten. jika tidak ada redirect index dan kirim pesan error
		if( !$content->load( $id ) )
		{
			//throw new Zend_Controller_Action_Exception('This page dont exist',404);
			$this->_helper->FlashMessenger( array( 'error' => 'Page not found' ) );
			$this->_helper->redirector->gotoRoute( array('type' => $this->content_type), 'contents' );
		}
		// set content form
		$this->form->setContent( $content->content_type );
		// set form action
		$this->form->setAction( $this->_helper->url->simple( 'edit' ) . '/id/' . $id );
		// populate data
		$this->form->populate( $content->toPopulate() );
		// set permalink
		$this->form->setPermalink( $content->content_slug, $content->getId() );
		// set field id untuk custom field
		$this->form->setFieldId( $id );
		// set featured image
		$this->form->setFeaturedImage( $content->content_image );
		// set edit
		$this->form->setEdit();
		
		// proses
		if( $this->_request->isPost() && 
			$this->form->isValid($this->_request->getPost()))
		{
			// data
			$data = $this->form->getValues();
			// save
			$isSaved = $content->saveContent( $data );
			if($isSaved){
				$this->_helper->FlashMessenger(array('success' => sprintf('"%s %s" successfully updated', 
																	ucfirst($this->content_type), $data['content_name'])));
			} else {
				$this->_helper->FlashMessenger(array('error' => 'Error occured. Please try again later'));
			}
			
			// redirect
			if( $data['redirect'] == 'edit' )
				$this->_helper->redirector->gotoRoute( array('action' => 'edit', 'type' => $data['content_type'], 'id' => $data['content_id']), 'content' );
			elseif( $data['redirect'] == 'add' )
				$this->_helper->redirector->gotoRoute( array('action' => 'add', 'type' => $data['content_type']), 'content' );
			else
				$this->_helper->redirector->gotoRoute( array('type' => $data['content_type']), 'contents' );
			
		}
		
		$this->view->form = $this->form;
		
    	$this->view->headTitle( sprintf('Edit %s', ucfirst($content->content_name)) );
    }

    public function deleteAction()
    {
		$id = $this->_request->getParam('id');
		
		$content = new Model_Content( $this->db );
		if( !$content->load( $id ) ){
			$message = array('error' => 'Content not found');
		} else {
			if( $content->delete() )
				$message = array('success' => 'Content deleted');
			else
				$message = array('error' => 'Error occured. Please try again');
		}
		
		// redirect
		$this->_helper->FlashMessenger( $message );
		$this->_helper->redirector->gotoRoute( array('type' => $this->content_type), 'contents' );
    }
	
	public function termAction()
	{
		$taxonomy = $this->_getParam('type');
		
		$form = new Admin_Form_Term();
		$form->setTaxonomy( $taxonomy );
		$this->view->form = $form;
		
		$options = array(
			'show' 	=> 5,
			'range' => 3,
			'page' 	=> $this->_request->getParam('page'),
			'type'	=> $this->content_type,
			'taxonomy' => $taxonomy 
		);
		$pagination = Model_Term::GetPagination( $this->db, $options);
		$this->view->paginator = $pagination;
		
		$title = ( $taxonomy == 'category' ) ? 'Categories' : 'Tags';
    	$this->view->headTitle( $title );
	}

	public function testAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$_cats1 = 'PHP';
		$_cats2 = 'PHP,jQuery';
		$_cats3 = 'Zend';
		$_cats4 = 'jQuery';
		
		$_terms1 = 'PHP,pdo';
		$_terms2 = 'PHP,jQuery';
		$_terms3 = 'Zend,framework';
		$_terms4 = 'Ajax,jQuery,framework';
		
		$id = 32;
		
		$term = new Model_Term( $this->db );
		/*
		$term->addTerms( $id, array(
			'category' => $_cats2,
			'tag' => ''
		));*/
		
		$terms1 = Model_Term::GetTerms( $this->db, array('relation' => 1) );
		echo '<h1>Term 1</h1>';
		Zend_Debug::dump( $terms1 );
		$terms2 = Model_Term::GetTerms( $this->db, array('relation' => 2) );
		echo '<h1>Term 2</h1>';
		Zend_Debug::dump( $terms2 );
		$terms3 = Model_Term::GetTerms( $this->db, array('relation' => 3) );
		echo '<h1>Term 3</h1>';
		Zend_Debug::dump( $terms3 );
		$terms10 = Model_Term::GetTerms( $this->db, array('relation' => 10) );
		echo '<h1>Term 10</h1>';
		Zend_Debug::dump( $terms10 );
		$terms = Model_Term::GetTerms( $this->db, array('relation' => array(1,2,3)) );
		echo '<h1>Terms</h1>';
		Zend_Debug::dump( $terms );
		
		/*
		$cache = Zend_Registry::get('cache');
		//$cache->remove('rs');
		$id = 'rs';

		$start_time = microtime(true);
		
		if(!($_terms = $cache->load($id)))
		{
		    echo "Not found in Cache<br />";
		
			$_terms = Model_Term::GetTermsParentChild( $this->db, array(
														'taxonomy' => 'category', 
														'order' => 't.term_id',
													));
		
		    $cache->save($_terms);
		}
		else
		{
		    echo "Running from Cache<br />";
		}
		
		Zend_Debug::dump( $_terms );
		$cpu_time = microtime(true) - $start_time;
		echo sprintf('%01.4f', $cpu_time);
		 */
		/* 
		$array = array(
			'product' => array(
				'singular' => 'Product',
				'plural' => 'Products'
			),
			'portfolio' => array(
				'singular' => 'Portfolio',
				'plural' => 'Portfolios'
			)
		);
		echo serialize($array);
		 */
		 
		$menu = array(
			array(
				'beforeText' => '<i class="icon-home"></i>',
		        //'label' => '<h1>Dashboard</h1>', 
		        //'labelIsHtml' => true, 
		        'label' => 'DASHBOARD', 
				'route' => 'admin',
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'order' => 5
			),
			array(
				'beforeText' => '<i class="icon-file"></i>',
				'label' => 'POSTS',
				'uri' => '#sub-posts',
		        'ulAttrbs' => array( 
		            'id' => 'sub-posts',
		            'class' => 'collapse',
		        ),
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'data' => array(
					'data-toggle' => 'collapse'
				),
		        'order' => 2,
				'pages' => array(
					array(
						'label' => 'All Posts',
						'params' => array(
							'type' => 'post'
						),
						'route' => 'contents',
					),
					array(
						'label' => 'Add Post',
						'action' => 'add',
						'params' => array(
							'type' => 'post'
						),
						'route' => 'content',
					),
					array(
						'label' => 'Categories',
						'action' => 'term',
						'params' => array(
							'type' => 'category'
						),
						'route' => 'content',
					),
					array(
						'label' => 'Tags',
						'action' => 'term',
						'params' => array(
							'type' => 'tag'
						),
						'route' => 'content',
					)
				)
			),
			array(
				'beforeText' => '<i class="icon-book"></i>',
				'label' => 'PAGES',
				'uri' => '#sub-pages',
		        'ulAttrbs' => array( 
		            'id' => 'sub-pages',
		            'class' => 'collapse',
		        ),
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'data' => array(
					'data-toggle' => 'collapse'
				),
		        'order' => 3,
				'pages' => array(
					array(
						'label' => 'All Pages',
						'params' => array(
							'type' => 'page'
						),
						'route' => 'contents',
					),
					array(
						'label' => 'Add Page',
						'action' => 'add',
						'params' => array(
							'type' => 'page'
						),
						'route' => 'content',
					)
				)
			),
			// CUSTOM CONTENTS
			array(
				'beforeText' => '<i class="icon-file"></i>',
				'label' => 'PRODUCTS',
				'uri' => '#sub-products',
		        'ulAttrbs' => array( 
		            'id' => 'sub-products',
		            'class' => 'collapse',
		        ),
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'data' => array(
					'data-toggle' => 'collapse'
				),
		        'order' => 4,
				'pages' => array(
					array(
						'label' => 'All Products',
						'params' => array(
							'type' => 'product'
						),
						'route' => 'contents',
					),
					array(
						'label' => 'Add Post',
						'action' => 'add',
						'params' => array(
							'type' => 'post'
						),
						'route' => 'content',
					),
					array(
						'label' => 'Categories',
						'action' => 'term',
						'params' => array(
							'type' => 'product'
						),
						'route' => 'content',
					)
				)
			),
			array(
				'beforeText' => '<i class="icon-file"></i>',
				'label' => 'PORTFOLIOS',
				'uri' => '#sub-portfolios',
		        'ulAttrbs' => array( 
		            'id' => 'sub-portfolios',
		            'class' => 'collapse',
		        ),
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'data' => array(
					'data-toggle' => 'collapse'
				),
		        'order' => 1,
				'pages' => array(
					array(
						'label' => 'All Portfolios',
						'params' => array(
							'type' => 'portfolio'
						),
						'route' => 'contents',
					),
					array(
						'label' => 'Add Portfolio',
						'action' => 'add',
						'params' => array(
							'type' => 'portfolio'
						),
						'route' => 'content',
					),
					array(
						'label' => 'Categories',
						'action' => 'term',
						'params' => array(
							'type' => 'portfolio'
						),
						'route' => 'content',
					)
				)
			),
			// END CUSTOM CONTENTS
			array(
				'beforeText' => '<i class="icon-list-alt"></i>',
				'label' => 'APPEARANCE',
				'uri' => '#sub-appearance',
		        'ulAttrbs' => array( 
		            'id' => 'sub-appearance',
		            'class' => 'collapse',
		        ),
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'data' => array(
					'data-toggle' => 'collapse'
				),
		        'order' => 6,
				'pages' => array(
					array(
						'label' => 'Widgets',
						'controller' => 'appearance',
						'action' => 'widgets',
						'module' => 'admin',
						'route' => 'default',
					),
					array(
						'label' => 'Menus',
						'controller' => 'appearance',
						'action' => 'menus',
						'module' => 'admin',
						'route' => 'default',
					),
				)
			),
			array(
				'beforeText' => '<i class="icon-cog"></i>',
				'label' => 'SETTINGS',
				'uri' => '#sub-settings',
		        'ulAttrbs' => array( 
		            'id' => 'sub-settings',
		            'class' => 'collapse',
		        ),
		        'liAttrbs' => array( 
		            'class' => 'nav-header'
		        ),
		        'data' => array(
					'data-toggle' => 'collapse'
				),
		        'order' => 7,
				'pages' => array(
					array(
						'label' => 'General',
						'controller' => 'settings',
						'module' => 'admin',
						'route' => 'default',
					),
					array(
						'label' => 'Options',
						'controller' => 'settings',
						'action' => 'option',
						'module' => 'admin',
						'route' => 'default',
					),
				)
			)
		);
		$container = new Zend_Navigation($menu);
		$navigation = $this->view->navigationBootstrap($container)->setUlId('menu-navigation')->setUlClass('nav nav-list');
		//$navigation = $this->view->navigation($container);
		
		if ($found = $navigation->findBy('label', 'POSTS')) {
		    $found->setActive();
		}
		
		echo $navigation->render();
		
		$page = $navigation->findOneBy('label', 'POSTS');
		echo $navigation->render($page);
	}
}
