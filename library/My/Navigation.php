<?php
class My_Navigation
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

}
