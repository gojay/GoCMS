<?php
class Zend_View_Helper_FormLink extends Zend_View_Helper_FormElement
{
    public function formLink($name, $value = null, $attribs = null)
    {
    	if( isset($attribs['icon']) ){
    		$icon = '<i class="'. $attribs['icon'] . '"></i>&nbsp;';
			unset($attribs['icon']);
    	} 
		if( isset($attribs['spin']) && $attribs['spin'] ){
    		$spin = '&nbsp;<img src="'.$this->view->baseUrl().'/images/ajax_spin.gif" id="ajax-loader" class="hide" />';
			unset($attribs['spin']);
    	} 
			
        $info = $this->_getInfo($name, $value, $attribs, $options);
        extract($info); // name, value, attribs
        
        // Render the button.
        $xhtml = '<a id="' . $this->view->escape($id) . '" '
            .  $this->_htmlAttribs($attribs) . '>'
            . $icon . $value . '</a>' . $spin ;
			
        return $xhtml;
    }
}
