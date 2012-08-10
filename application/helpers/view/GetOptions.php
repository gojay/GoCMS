<?php
class Zend_View_Helper_GetOptions extends Zend_View_Helper_Abstract
{
	public function getOptions( $option_name )
	{
		$db = Zend_Registry::get('db');
		
		$option = new Model_Option( $db );
		$option->load( $option_name, 'option_name' );
		$value = $option->option_value;
		
		// cek is unserialized
		$data = @unserialize($value);
		if ($value === 'b:0;' || $data !== false) {
		    return $data;
		} 
		
		return $value;
	}
}
