<?php

class Default_RssController extends Zend_Controller_Action
{

    public function init()
    {
        $this->db = Zend_Registry::get( 'db' );
    }

    public function indexAction()
    {
    	$option = $this->_helper->hooks->getOption( 'option_general' );
		
    	$page = new Model_Content( $this->db );
		$about = $page->load( 'about-us', 'content_slug' );
		
		$entries = array();
		
		$rss = $option['rss'];
		$pages = Model_Content::GetContents( $this->db, array(
			'type' => $rss, 
			'order' => 'content_date DESC') 
		);
		if( $pages )
		{
			foreach( $pages as $p )
			{
				$entries[$p->getId()] = array(
					'title' => ucwords( $p->content_name ),
                    'description' => strip_tags($this->_helper->hooks->getExcerptContent($p->content_description, '[...]')),
				);
				
				if( $p->content_type == 'post' )
				{
					$entries[$p->getId()]['link'] = $this->_helper->url->url(array('title' => $p->content_slug), 'news');
				} 
				elseif( $p->content_type == 'portfolio' ) {
					$entries[$p->getId()]['link'] = $this->_helper->url->url(array('title' => $p->content_slug), 'portfolio');
				}
				elseif( $p->content_type == 'people' ) {
					$entries[$p->getId()]['link'] = $this->_helper->url->url(array('name' => $p->content_slug), 'people');
				}
				else {
					if( $p->isChild() )
					{
						$parent = $p->getParent();
						$route = ( $parent->content_name == 'About Us' ) ? 'about' : 'service';
					}
					else
						$route = 'page';
					
					$entries[$p->getId()]['link'] = $this->_helper->url->url(array('title' => $p->content_slug), $route);
				}
			}
		}
		
		//Zend_Debug::dump($entries);
		
		$feedData = array(
            'title' => $option['rss_title'],
            'description' => strip_tags( $about->content_description ),
            'link' => $this->view->baseUrl(),
            'charset' => 'utf8',
            'entries' => $entries
        );
        // create our feed object and import the data
        $feed = Zend_Feed::importArray ( $feedData, 'rss' );
		
        // set the Content Type of the document
        header ( 'Content-type: text/xml' );
 
        // contents of the RSS xml document
        echo $feed->send();
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
    }


}

