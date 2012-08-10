<?php
class Zend_View_Helper_GetMenus extends Zend_View_Helper_Abstract
{
    public function getMenus()
    {
    	$db = Zend_Registry::get('db');
		// menus
		$menus = Model_Option::GetMenus( $db );
		return $menus;
	}

}
