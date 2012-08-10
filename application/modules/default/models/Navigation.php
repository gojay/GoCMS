<?php
class Model_Navigation
{
	protected $_menus;
	
	public function __construct( $menus )
	{
		$this->_menus = $menus;
	}
	
	
	/**
	 * Get navigation
	 * 
	 * @return Zend_Navigation
	 */
	public function getNavigation()
	{
		if( is_object($this->_menus) )
			return new Zend_Navigation((array)$this->_menus);
		
		$container = $this->_buildNavigation( $this->_menus );
		$navigation = new Zend_Navigation($container);
		return $navigation;
	}
	
	/**
	 * Get navigation from cache
	 * 
	 * @param string
	 * @return Zend_Navigation
	 */
	public function getCachedNavigation( $cacheId = 'menu_navigation' )
	{
		$cache = Zend_Registry::get('cache');  
        if ( !$navigation = $cache->load($cacheId) ) {  
            $navigation = $this->getNavigation();  
            $cache->save($navigation, $cacheId);  
        }  
        return $navigation;  
	}
	
	/**
	 * build navigation
	 * 
	 * @param array/string
	 * @return array
	 */
	protected function _buildNavigation( $menus )
	{
		foreach ($menus as $page) 
		{
			// parent route adalah "page"
			// child route adalah slug parent
			$route = ( $page->content_parent == 0) ? 'page' : $page->getParent()->content_slug ;
			
			$container[$page->getId()] = array(
				'label' 	 => $page->content_name,
				'controller' => 'index',
				'params' => array(
					'title' => $page->content_slug
				),
				'route' => 'page',
				'title' => $page->content_name,
				'order' => (int) $page->content_order
			);
			
			// children
			if( $page->hasChild() ){
				$container[$page->getId()]['pages'] = $this->_buildNavigation( $page->getChild() );
			}
		}
			
		// sort by order
		$this->_arraySort($container,"order");
		
		return $container;
	}

	/**
	 * Sort array by order
	 * 
	 * @param array
	 * @param string
	 */
	protected function _arraySort(&$array, $key) {
	    $sorter = array();
	    $ret = array();
	    reset($array);
	    foreach ($array as $ii => $va) {
	        $sorter[$ii] = $va[$key];
	    }
	    asort($sorter);
	    foreach ($sorter as $ii => $va) {
	        $ret[$ii] = $array[$ii];
	    }
	    $array = $ret;
	}
	
	/**
	 * ambil menu
	 * 
	 * @return array
	 */
	public function getMenu( $parent = 'root' )
	{
		$menu = $this->_buildMenu($this->_menus, $parent);
		return $menu;
	}
	
	/**
	 * build tree menus, sesuai parent child
	 * 
	 * @param array
	 * @param string
	 * @return arrray
	 * 
	 * sample
	 * from :
	 * array(7) {
		  ["home"] => array(4) {
		    ["parent"] => string(4) "root"
		    ["label"] => string(4) "Home"
		    ["title"] => string(4) "Home"
		    ["uri"] => string(15) "http://home.com"
		  }
		  ["about"] => array(4) {
		    ["parent"] => string(4) "root"
		    ["label"] => string(5) "About"
		    ["title"] => string(5) "About"
		    ["uri"] => string(16) "http://about.com"
		  }
		  ["test"] => array(4) {
		    ["parent"] => string(5) "about"
		    ["label"] => string(4) "test"
		    ["title"] => string(4) "test"
		    ["uri"] => string(15) "http://test.com"
		  }
		  [7] => array(5) {
		    ["parent"] => string(4) "root"
		    ["label"] => string(6) "Page 1"
		    ["title"] => string(6) "Page 1"
		    ["controller"] => string(5) "index"
		    ["params"] => array(1) {
		      ["title"] => string(6) "page-1"
		    }
		  }
		  [8] => array(5) {
		    ["parent"] => string(7) "contact"
		    ["label"] => string(6) "Page 2"
		    ["title"] => string(6) "Page 2"
		    ["controller"] => string(5) "index"
		    ["params"] => array(1) {
		      ["title"] => string(6) "page-2"
		    }
		  }
		  ["contact"] => array(4) {
		    ["parent"] => string(4) "root"
		    ["label"] => string(7) "contact"
		    ["title"] => string(7) "contact"
		    ["uri"] => string(18) "http://contact.com"
		  }
		  [9] => array(5) {
		    ["parent"] => string(1) "8"
		    ["label"] => string(6) "Page 3"
		    ["title"] => string(6) "Page 3"
		    ["controller"] => string(5) "index"
		    ["params"] => array(1) {
		      ["title"] => string(6) "page-3"
		    }
		  }
		}
	 * 
	 * to :
	 * 
	 * array(4) {
		  ["home"] => array(3) {
		    ["label"] => string(4) "Home"
		    ["title"] => string(4) "Home"
		    ["uri"] => string(15) "http://home.com"
		  }
		  ["about"] => array(4) {
		    ["label"] => string(5) "About"
		    ["title"] => string(5) "About"
		    ["uri"] => string(16) "http://about.com"
		    ["pages"] => array(1) {
		      ["test"] => array(3) {
		        ["label"] => string(4) "test"
		        ["title"] => string(4) "test"
		        ["uri"] => string(15) "http://test.com"
		      }
		    }
		  }
		  [7] => array(5) {
		    ["label"] => string(6) "Page 1"
		    ["title"] => string(6) "Page 1"
		    ["controller"] => string(5) "index"
		    ["params"] => array(1) {
		      ["title"] => string(6) "page-1"
		    }
		    ["route"] => string(4) "page"
		  }
		  ["contact"] => array(4) {
		    ["label"] => string(7) "contact"
		    ["title"] => string(7) "contact"
		    ["uri"] => string(18) "http://contact.com"
		    ["pages"] => array(1) {
		      [8] => array(6) {
		        ["label"] => string(6) "Page 2"
		        ["title"] => string(6) "Page 2"
		        ["controller"] => string(5) "index"
		        ["params"] => array(1) {
		          ["title"] => string(6) "page-2"
		        }
		        ["route"] => string(4) "page"
		        ["pages"] => array(1) {
		          [9] => array(5) {
		            ["label"] => string(6) "Page 3"
		            ["title"] => string(6) "Page 3"
		            ["controller"] => string(5) "index"
		            ["params"] => array(1) {
		              ["title"] => string(6) "page-3"
		            }
		            ["route"] => string(4) "page"
		          }
		        }
		      }
		    }
		  }
		}
	 * 
	 */
	protected function _buildMenu( $pages, $parent )
	{
		$menus = array();
		foreach($pages as $key => $page)
		{
			if($page['parent'] == $parent)
			{
				$menu = $page;
				// page link ? hapus route
				if( is_string($key) )
					unset($menu['route']);
				// sub menu	
				$menu['pages'] = $this->_buildMenu($pages, $key);
				
				// tidak ada submenu ? hapus pages
				if( !$menu['pages'] )
					unset($menu['pages']);
				// hapus parent
				unset($menu['parent']);
				// set
				$menus[$key] = $menu;
			}
		}
		return $menus;
	}
}
