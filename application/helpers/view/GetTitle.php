<?php
class Zend_View_Helper_GetTitle extends Zend_View_Helper_Abstract
{
    public function getTitle()
    {
       	$view = new Zend_View();
		$headTitle = $view->placeholder( 'Zend_View_Helper_HeadTitle' );
		
		list($name, $title) = explode(' | ', $headTitle);
		return $title;
	}

}
