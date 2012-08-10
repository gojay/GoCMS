<?php
include_once('Zend/Config/Xml.php');

class Zend_View_Helper_LoadStyle extends Zend_View_Helper_Abstract
{
	public function loadStyle($style)
	{
		$style_xml = (defined('RUNNING_FROM_ROOT')) ? 'public/assets/'.$style.'.xml' : 'assets/'.$style.'.xml';
		$styleData = new Zend_Config_XML( $style_xml );
		$stylesheets = $styleData->stylesheets->stylesheet->toArray();
		if(is_array($stylesheets)){
			foreach($stylesheets as $stylesheet){
				$this->view->headLink()->appendStylesheet( $this->view->baseUrl() . '/assets/css/'.$stylesheet);
			}
		}
	}
}
?>