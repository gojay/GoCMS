<?php
class Zend_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
    public function baseUrl( $public = true )
    {
    	$frontController = Zend_Controller_Front::getInstance();
       	$baseUrl = $frontController->getBaseUrl(); 
    	if ( $public && defined('RUNNING_FROM_ROOT' )) 
    	{
            $baseUrl .= '/public'; 
        } 
		
		return $this->_getFullUrl() . $baseUrl;
    }

    private function _getFullUrl() 
	{
      	return	(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        		(isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
        		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
        		(isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] === 443 ||
        		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT'])));
    }
}
