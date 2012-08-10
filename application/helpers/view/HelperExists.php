<?php
class Zend_View_Helper_HelperExists extends Zend_VIew_Helper_Abstract
{
	public function helperExists($name) 
	{
        return (bool)$this->view->getPluginLoader('helper')->load($name, false);
    }
}
